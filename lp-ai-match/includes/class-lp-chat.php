<?php
/**
 * Internal chat system.
 *
 * 1:1 messaging with AJAX polling, auto-expiry, and moderation.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LP_Chat {

	/**
	 * Initialize chat hooks.
	 */
	public static function init() {
		add_action( 'lp_chat_expiry_check', array( __CLASS__, 'check_expired_chats' ) );

		// Schedule expiry check if not already scheduled.
		if ( ! wp_next_scheduled( 'lp_chat_expiry_check' ) ) {
			wp_schedule_event( time(), 'hourly', 'lp_chat_expiry_check' );
		}
	}

	/**
	 * Send a message.
	 *
	 * @param int    $match_id   Match post ID.
	 * @param int    $sender_id  WP User ID of sender.
	 * @param string $message    Message text.
	 * @return int|WP_Error Message ID or error.
	 */
	public static function send_message( $match_id, $sender_id, $message ) {
		// Verify chat is active.
		if ( ! self::is_chat_active( $match_id ) ) {
			return new WP_Error( 'chat_expired', __( 'チャットの有効期限が切れました。', 'lp-ai-match' ) );
		}

		// Verify sender is part of this match.
		$profile_a = get_post_meta( $match_id, '_lp_profile_a', true );
		$profile_b = get_post_meta( $match_id, '_lp_profile_b', true );
		$user_a    = get_post_field( 'post_author', $profile_a );
		$user_b    = get_post_field( 'post_author', $profile_b );

		if ( (int) $sender_id !== (int) $user_a && (int) $sender_id !== (int) $user_b ) {
			return new WP_Error( 'unauthorized', __( 'このチャットへのアクセス権がありません。', 'lp-ai-match' ) );
		}

		// Check if sender is blocked.
		if ( self::is_user_blocked( $sender_id ) ) {
			return new WP_Error( 'blocked', __( 'アカウントがブロックされています。', 'lp-ai-match' ) );
		}

		$receiver_id = ( (int) $sender_id === (int) $user_a ) ? $user_b : $user_a;

		global $wpdb;
		$table = $wpdb->prefix . 'lp_chat_messages';

		$result = $wpdb->insert(
			$table,
			array(
				'match_id'    => $match_id,
				'sender_id'   => $sender_id,
				'receiver_id' => $receiver_id,
				'message'     => sanitize_textarea_field( $message ),
				'is_read'     => 0,
				'created_at'  => current_time( 'mysql', true ),
			),
			array( '%d', '%d', '%d', '%s', '%d', '%s' )
		);

		if ( false === $result ) {
			return new WP_Error( 'db_error', __( 'メッセージの送信に失敗しました。', 'lp-ai-match' ) );
		}

		return $wpdb->insert_id;
	}

	/**
	 * Get messages for a match (polling).
	 *
	 * @param int $match_id Match post ID.
	 * @param int $user_id  Current user ID.
	 * @param int $after_id Only get messages after this ID.
	 * @return array Messages.
	 */
	public static function get_messages( $match_id, $user_id, $after_id = 0 ) {
		global $wpdb;
		$table = $wpdb->prefix . 'lp_chat_messages';

		$messages = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT id, sender_id, receiver_id, message, is_read, created_at
				 FROM $table
				 WHERE match_id = %d AND id > %d
				 ORDER BY id ASC
				 LIMIT 100",
				$match_id,
				$after_id
			),
			ARRAY_A
		);

		// Mark messages as read.
		if ( ! empty( $messages ) ) {
			$wpdb->query(
				$wpdb->prepare(
					"UPDATE $table SET is_read = 1
					 WHERE match_id = %d AND receiver_id = %d AND is_read = 0",
					$match_id,
					$user_id
				)
			);
		}

		return $messages ? $messages : array();
	}

	/**
	 * Check if chat is still active for a match.
	 *
	 * @param int $match_id Match post ID.
	 * @return bool Active status.
	 */
	public static function is_chat_active( $match_id ) {
		$status = get_post_meta( $match_id, '_lp_chat_status', true );
		if ( 'expired' === $status || 'blocked' === $status ) {
			return false;
		}

		$tier       = get_post_meta( $match_id, '_lp_tier', true );
		$created_at = get_post_meta( $match_id, '_lp_chat_started', true );

		if ( ! $created_at ) {
			return false;
		}

		$expiry = self::get_expiry_time( $tier, $created_at );
		if ( time() > $expiry ) {
			update_post_meta( $match_id, '_lp_chat_status', 'expired' );
			return false;
		}

		return true;
	}

	/**
	 * Get chat expiry timestamp.
	 *
	 * @param string $tier       Product tier.
	 * @param string $created_at Chat start datetime.
	 * @return int Unix timestamp of expiry.
	 */
	private static function get_expiry_time( $tier, $created_at ) {
		$start = strtotime( $created_at );

		switch ( $tier ) {
			case 'basic':
				$hours = (int) get_option( 'lp_ai_match_chat_basic_hours', 24 );
				return $start + ( $hours * HOUR_IN_SECONDS );
			case 'standard':
			case 'premium':
				$days = (int) get_option( 'lp_ai_match_chat_standard_days', 7 );
				return $start + ( $days * DAY_IN_SECONDS );
			default:
				return $start + DAY_IN_SECONDS;
		}
	}

	/**
	 * Activate chat for a match.
	 *
	 * @param int    $match_id Match post ID.
	 * @param string $tier     Product tier.
	 */
	public static function activate_chat( $match_id, $tier ) {
		update_post_meta( $match_id, '_lp_chat_status', 'active' );
		update_post_meta( $match_id, '_lp_chat_started', current_time( 'mysql', true ) );
		update_post_meta( $match_id, '_lp_tier', $tier );
	}

	/**
	 * Check and expire old chats (cron job).
	 */
	public static function check_expired_chats() {
		$active_matches = get_posts(
			array(
				'post_type'      => 'lp_match',
				'posts_per_page' => -1,
				'meta_query'     => array(
					array(
						'key'   => '_lp_chat_status',
						'value' => 'active',
					),
				),
				'fields' => 'ids',
			)
		);

		foreach ( $active_matches as $match_id ) {
			self::is_chat_active( $match_id ); // This will auto-expire if needed.
		}
	}

	/**
	 * Report a message.
	 *
	 * @param int    $message_id Message ID.
	 * @param int    $reporter_id User reporting.
	 * @param string $reason     Report reason.
	 * @return bool Success.
	 */
	public static function report_message( $message_id, $reporter_id, $reason ) {
		$reports = get_option( 'lp_ai_match_chat_reports', array() );
		$reports[] = array(
			'message_id'  => $message_id,
			'reporter_id' => $reporter_id,
			'reason'      => sanitize_text_field( $reason ),
			'reported_at' => current_time( 'mysql' ),
		);
		return update_option( 'lp_ai_match_chat_reports', $reports );
	}

	/**
	 * Block a user (admin action).
	 *
	 * @param int $user_id User ID to block.
	 * @return bool Success.
	 */
	public static function block_user( $user_id ) {
		return update_user_meta( $user_id, '_lp_blocked', true );
	}

	/**
	 * Unblock a user.
	 *
	 * @param int $user_id User ID.
	 * @return bool Success.
	 */
	public static function unblock_user( $user_id ) {
		return delete_user_meta( $user_id, '_lp_blocked' );
	}

	/**
	 * Check if user is blocked.
	 *
	 * @param int $user_id User ID.
	 * @return bool Blocked status.
	 */
	public static function is_user_blocked( $user_id ) {
		return (bool) get_user_meta( $user_id, '_lp_blocked', true );
	}

	/**
	 * Share contact info (mutual consent).
	 *
	 * @param int $match_id Match post ID.
	 * @param int $user_id  User consenting.
	 * @return array|false Both users' consent status, or false.
	 */
	public static function consent_share_contact( $match_id, $user_id ) {
		$consents = get_post_meta( $match_id, '_lp_contact_consents', true );
		if ( ! is_array( $consents ) ) {
			$consents = array();
		}

		$consents[ $user_id ] = true;
		update_post_meta( $match_id, '_lp_contact_consents', $consents );

		// Check if both users have consented.
		$profile_a = get_post_meta( $match_id, '_lp_profile_a', true );
		$profile_b = get_post_meta( $match_id, '_lp_profile_b', true );
		$user_a    = get_post_field( 'post_author', $profile_a );
		$user_b    = get_post_field( 'post_author', $profile_b );

		$both = ! empty( $consents[ $user_a ] ) && ! empty( $consents[ $user_b ] );

		return array(
			'both_consented' => $both,
			'your_consent'   => ! empty( $consents[ $user_id ] ),
		);
	}

	/**
	 * Get unread message count for a user.
	 *
	 * @param int $user_id User ID.
	 * @return int Unread count.
	 */
	public static function get_unread_count( $user_id ) {
		global $wpdb;
		$table = $wpdb->prefix . 'lp_chat_messages';

		return (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM $table WHERE receiver_id = %d AND is_read = 0",
				$user_id
			)
		);
	}
}
