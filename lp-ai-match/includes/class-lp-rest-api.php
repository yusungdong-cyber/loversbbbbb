<?php
/**
 * REST API endpoints.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LP_Rest_API {

	/**
	 * API namespace.
	 */
	const NAMESPACE = 'lp-ai-match/v1';

	/**
	 * Initialize API routes.
	 */
	public static function init() {
		add_action( 'rest_api_init', array( __CLASS__, 'register_routes' ) );
	}

	/**
	 * Register all REST routes.
	 */
	public static function register_routes() {
		// Horoscope.
		register_rest_route(
			self::NAMESPACE,
			'/horoscope',
			array(
				'methods'             => 'POST',
				'callback'            => array( __CLASS__, 'horoscope' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'birthdate' => array(
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
						'validate_callback' => array( __CLASS__, 'validate_date' ),
					),
				),
			)
		);

		// Tarot draw.
		register_rest_route(
			self::NAMESPACE,
			'/tarot/draw',
			array(
				'methods'             => 'POST',
				'callback'            => array( __CLASS__, 'tarot_draw' ),
				'permission_callback' => '__return_true',
			)
		);

		// Profile CRUD.
		register_rest_route(
			self::NAMESPACE,
			'/profile',
			array(
				array(
					'methods'             => 'POST',
					'callback'            => array( __CLASS__, 'create_update_profile' ),
					'permission_callback' => array( __CLASS__, 'check_logged_in' ),
				),
				array(
					'methods'             => 'GET',
					'callback'            => array( __CLASS__, 'get_profile' ),
					'permission_callback' => array( __CLASS__, 'check_logged_in' ),
				),
			)
		);

		// Match request.
		register_rest_route(
			self::NAMESPACE,
			'/match/request',
			array(
				'methods'             => 'POST',
				'callback'            => array( __CLASS__, 'request_match' ),
				'permission_callback' => array( __CLASS__, 'check_logged_in' ),
				'args'                => array(
					'tier' => array(
						'required' => true,
						'type'     => 'string',
						'enum'     => array( 'basic', 'standard', 'premium' ),
					),
				),
			)
		);

		// Get match details.
		register_rest_route(
			self::NAMESPACE,
			'/match/(?P<id>\d+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'get_match' ),
				'permission_callback' => array( __CLASS__, 'check_logged_in' ),
			)
		);

		// Get report.
		register_rest_route(
			self::NAMESPACE,
			'/report/(?P<id>\d+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'get_report' ),
				'permission_callback' => array( __CLASS__, 'check_logged_in' ),
			)
		);

		// Download report PDF.
		register_rest_route(
			self::NAMESPACE,
			'/report/(?P<id>\d+)/pdf',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'download_report_pdf' ),
				'permission_callback' => array( __CLASS__, 'check_logged_in' ),
			)
		);

		// Chat: send message.
		register_rest_route(
			self::NAMESPACE,
			'/chat/send',
			array(
				'methods'             => 'POST',
				'callback'            => array( __CLASS__, 'chat_send' ),
				'permission_callback' => array( __CLASS__, 'check_logged_in' ),
				'args'                => array(
					'match_id' => array( 'required' => true, 'type' => 'integer' ),
					'message'  => array(
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_textarea_field',
					),
				),
			)
		);

		// Chat: get messages.
		register_rest_route(
			self::NAMESPACE,
			'/chat/(?P<match_id>\d+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'chat_get' ),
				'permission_callback' => array( __CLASS__, 'check_logged_in' ),
				'args'                => array(
					'after' => array( 'type' => 'integer', 'default' => 0 ),
				),
			)
		);

		// Chat: report message.
		register_rest_route(
			self::NAMESPACE,
			'/chat/report',
			array(
				'methods'             => 'POST',
				'callback'            => array( __CLASS__, 'chat_report' ),
				'permission_callback' => array( __CLASS__, 'check_logged_in' ),
			)
		);

		// Chat: consent contact share.
		register_rest_route(
			self::NAMESPACE,
			'/chat/consent-contact',
			array(
				'methods'             => 'POST',
				'callback'            => array( __CLASS__, 'chat_consent_contact' ),
				'permission_callback' => array( __CLASS__, 'check_logged_in' ),
			)
		);

		// Payment: create session.
		register_rest_route(
			self::NAMESPACE,
			'/payment/create-session',
			array(
				'methods'             => 'POST',
				'callback'            => array( __CLASS__, 'create_payment_session' ),
				'permission_callback' => array( __CLASS__, 'check_logged_in' ),
				'args'                => array(
					'tier' => array(
						'required' => true,
						'type'     => 'string',
						'enum'     => array( 'basic', 'standard', 'premium' ),
					),
				),
			)
		);
	}

	/* ------------------------------------------------------------------
	 * Permission callbacks
	 * ---------------------------------------------------------------- */

	/**
	 * Check if user is logged in.
	 *
	 * @return bool|WP_Error
	 */
	public static function check_logged_in() {
		if ( ! is_user_logged_in() ) {
			return new WP_Error(
				'rest_not_logged_in',
				__( 'ログインが必要です。', 'lp-ai-match' ),
				array( 'status' => 401 )
			);
		}
		return true;
	}

	/**
	 * Validate date format.
	 *
	 * @param string $value Date string.
	 * @return bool Valid.
	 */
	public static function validate_date( $value ) {
		$date = DateTime::createFromFormat( 'Y-m-d', $value );
		return $date && $date->format( 'Y-m-d' ) === $value;
	}

	/* ------------------------------------------------------------------
	 * Endpoint callbacks
	 * ---------------------------------------------------------------- */

	/**
	 * Generate horoscope.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response Response.
	 */
	public static function horoscope( $request ) {
		$birthdate = $request->get_param( 'birthdate' );
		$result    = LP_Horoscope::generate_daily_horoscope( $birthdate );

		return new WP_REST_Response(
			array(
				'success' => true,
				'data'    => $result,
			),
			200
		);
	}

	/**
	 * Draw tarot cards.
	 *
	 * @return WP_REST_Response Response.
	 */
	public static function tarot_draw() {
		$cards = LP_Tarot::draw_three_cards();

		return new WP_REST_Response(
			array(
				'success' => true,
				'data'    => $cards,
			),
			200
		);
	}

	/**
	 * Create or update user profile.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response Response.
	 */
	public static function create_update_profile( $request ) {
		$user_id = get_current_user_id();
		$params  = $request->get_json_params();

		// Check age verification.
		if ( empty( $params['age_verified'] ) ) {
			return new WP_REST_Response(
				array( 'success' => false, 'message' => '年齢確認が必要です。' ),
				400
			);
		}

		// Find existing profile.
		$existing = get_posts(
			array(
				'post_type'   => 'lp_profile',
				'author'      => $user_id,
				'numberposts' => 1,
			)
		);

		$profile_data = array(
			'post_type'   => 'lp_profile',
			'post_title'  => isset( $params['nickname'] ) ? sanitize_text_field( $params['nickname'] ) : 'User ' . $user_id,
			'post_status' => 'publish',
			'post_author' => $user_id,
		);

		if ( ! empty( $existing ) ) {
			$profile_data['ID'] = $existing[0]->ID;
			$profile_id = wp_update_post( $profile_data );
		} else {
			$profile_id = wp_insert_post( $profile_data );
		}

		if ( is_wp_error( $profile_id ) ) {
			return new WP_REST_Response(
				array( 'success' => false, 'message' => $profile_id->get_error_message() ),
				500
			);
		}

		// Save meta fields.
		$meta_fields = array(
			'birthdate'           => '_lp_birthdate',
			'gender'              => '_lp_gender',
			'personality_answers' => '_lp_personality_answers',
			'values'              => '_lp_values',
			'romance_style'       => '_lp_romance_style',
			'age_verified'        => '_lp_age_verified',
		);

		foreach ( $meta_fields as $param => $meta_key ) {
			if ( isset( $params[ $param ] ) ) {
				$value = $params[ $param ];
				if ( is_string( $value ) ) {
					$value = sanitize_text_field( $value );
				}
				update_post_meta( $profile_id, $meta_key, $value );
			}
		}

		return new WP_REST_Response(
			array(
				'success'    => true,
				'profile_id' => $profile_id,
			),
			200
		);
	}

	/**
	 * Get current user's profile.
	 *
	 * @return WP_REST_Response Response.
	 */
	public static function get_profile() {
		$user_id  = get_current_user_id();
		$profiles = get_posts(
			array(
				'post_type'   => 'lp_profile',
				'author'      => $user_id,
				'numberposts' => 1,
			)
		);

		if ( empty( $profiles ) ) {
			return new WP_REST_Response(
				array( 'success' => true, 'data' => null ),
				200
			);
		}

		$profile = $profiles[0];
		return new WP_REST_Response(
			array(
				'success' => true,
				'data'    => array(
					'id'        => $profile->ID,
					'nickname'  => $profile->post_title,
					'birthdate' => get_post_meta( $profile->ID, '_lp_birthdate', true ),
					'gender'    => get_post_meta( $profile->ID, '_lp_gender', true ),
					'answers'   => get_post_meta( $profile->ID, '_lp_personality_answers', true ),
					'values'    => get_post_meta( $profile->ID, '_lp_values', true ),
				),
			),
			200
		);
	}

	/**
	 * Request matching (redirects to payment).
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response Response.
	 */
	public static function request_match( $request ) {
		$tier    = sanitize_text_field( $request->get_param( 'tier' ) );
		$user_id = get_current_user_id();

		// Check profile exists.
		$profiles = get_posts(
			array(
				'post_type'   => 'lp_profile',
				'author'      => $user_id,
				'numberposts' => 1,
			)
		);

		if ( empty( $profiles ) ) {
			return new WP_REST_Response(
				array( 'success' => false, 'message' => 'プロフィールを作成してください。' ),
				400
			);
		}

		// Create Stripe checkout session.
		$session = LP_Stripe::create_checkout_session( $tier, $user_id );

		if ( is_wp_error( $session ) ) {
			return new WP_REST_Response(
				array( 'success' => false, 'message' => $session->get_error_message() ),
				500
			);
		}

		return new WP_REST_Response(
			array(
				'success' => true,
				'data'    => $session,
			),
			200
		);
	}

	/**
	 * Get match details.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response Response.
	 */
	public static function get_match( $request ) {
		$match_id = (int) $request->get_param( 'id' );
		$user_id  = get_current_user_id();
		$match    = get_post( $match_id );

		if ( ! $match || 'lp_match' !== $match->post_type ) {
			return new WP_REST_Response(
				array( 'success' => false, 'message' => 'マッチングが見つかりません。' ),
				404
			);
		}

		// Verify ownership.
		$profile_a = get_post_meta( $match_id, '_lp_profile_a', true );
		$profile_b = get_post_meta( $match_id, '_lp_profile_b', true );
		$user_a    = get_post_field( 'post_author', $profile_a );
		$user_b    = get_post_field( 'post_author', $profile_b );

		if ( (int) $user_id !== (int) $user_a && (int) $user_id !== (int) $user_b ) {
			return new WP_REST_Response(
				array( 'success' => false, 'message' => 'アクセス権がありません。' ),
				403
			);
		}

		// Get report.
		$reports = get_posts(
			array(
				'post_type'  => 'lp_report',
				'meta_key'   => '_lp_match_id',
				'meta_value' => $match_id,
				'numberposts' => 1,
			)
		);

		$report_id = ! empty( $reports ) ? $reports[0]->ID : null;

		return new WP_REST_Response(
			array(
				'success' => true,
				'data'    => array(
					'id'           => $match_id,
					'profile_a'    => $profile_a,
					'profile_b'    => $profile_b,
					'tier'         => get_post_meta( $match_id, '_lp_tier', true ),
					'chat_active'  => LP_Chat::is_chat_active( $match_id ),
					'report_id'    => $report_id,
					'created_at'   => $match->post_date,
				),
			),
			200
		);
	}

	/**
	 * Get report.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response Response.
	 */
	public static function get_report( $request ) {
		$report_id = (int) $request->get_param( 'id' );
		$user_id   = get_current_user_id();
		$report    = get_post( $report_id );

		if ( ! $report || 'lp_report' !== $report->post_type ) {
			return new WP_REST_Response(
				array( 'success' => false, 'message' => 'レポートが見つかりません。' ),
				404
			);
		}

		// Verify ownership.
		if ( (int) $user_id !== (int) $report->post_author ) {
			return new WP_REST_Response(
				array( 'success' => false, 'message' => 'アクセス権がありません。' ),
				403
			);
		}

		return new WP_REST_Response(
			array(
				'success' => true,
				'data'    => array(
					'id'      => $report_id,
					'title'   => $report->post_title,
					'content' => $report->post_content,
					'scores'  => get_post_meta( $report_id, '_lp_scores', true ),
					'tier'    => get_post_meta( $report_id, '_lp_tier', true ),
				),
			),
			200
		);
	}

	/**
	 * Download report as PDF.
	 *
	 * @param WP_REST_Request $request Request.
	 */
	public static function download_report_pdf( $request ) {
		$report_id = (int) $request->get_param( 'id' );
		$user_id   = get_current_user_id();
		$report    = get_post( $report_id );

		if ( ! $report || 'lp_report' !== $report->post_type || (int) $user_id !== (int) $report->post_author ) {
			return new WP_REST_Response(
				array( 'success' => false, 'message' => 'アクセス権がありません。' ),
				403
			);
		}

		LP_PDF::serve_download( $report_id );
	}

	/**
	 * Send chat message.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response Response.
	 */
	public static function chat_send( $request ) {
		$match_id = (int) $request->get_param( 'match_id' );
		$message  = $request->get_param( 'message' );
		$user_id  = get_current_user_id();

		$result = LP_Chat::send_message( $match_id, $user_id, $message );

		if ( is_wp_error( $result ) ) {
			return new WP_REST_Response(
				array( 'success' => false, 'message' => $result->get_error_message() ),
				400
			);
		}

		return new WP_REST_Response(
			array( 'success' => true, 'message_id' => $result ),
			200
		);
	}

	/**
	 * Get chat messages (polling).
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response Response.
	 */
	public static function chat_get( $request ) {
		$match_id = (int) $request->get_param( 'match_id' );
		$after    = (int) $request->get_param( 'after' );
		$user_id  = get_current_user_id();

		$messages = LP_Chat::get_messages( $match_id, $user_id, $after );

		return new WP_REST_Response(
			array(
				'success'      => true,
				'data'         => $messages,
				'chat_active'  => LP_Chat::is_chat_active( $match_id ),
			),
			200
		);
	}

	/**
	 * Report a chat message.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response Response.
	 */
	public static function chat_report( $request ) {
		$params     = $request->get_json_params();
		$message_id = isset( $params['message_id'] ) ? (int) $params['message_id'] : 0;
		$reason     = isset( $params['reason'] ) ? sanitize_text_field( $params['reason'] ) : '';
		$user_id    = get_current_user_id();

		if ( ! $message_id ) {
			return new WP_REST_Response(
				array( 'success' => false, 'message' => 'メッセージIDが必要です。' ),
				400
			);
		}

		LP_Chat::report_message( $message_id, $user_id, $reason );

		return new WP_REST_Response(
			array( 'success' => true, 'message' => '報告が送信されました。' ),
			200
		);
	}

	/**
	 * Consent to share contact info.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response Response.
	 */
	public static function chat_consent_contact( $request ) {
		$params   = $request->get_json_params();
		$match_id = isset( $params['match_id'] ) ? (int) $params['match_id'] : 0;
		$user_id  = get_current_user_id();

		if ( ! $match_id ) {
			return new WP_REST_Response(
				array( 'success' => false, 'message' => 'マッチングIDが必要です。' ),
				400
			);
		}

		$result = LP_Chat::consent_share_contact( $match_id, $user_id );

		return new WP_REST_Response(
			array( 'success' => true, 'data' => $result ),
			200
		);
	}

	/**
	 * Create Stripe payment session.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response Response.
	 */
	public static function create_payment_session( $request ) {
		$tier    = sanitize_text_field( $request->get_param( 'tier' ) );
		$user_id = get_current_user_id();

		$session = LP_Stripe::create_checkout_session( $tier, $user_id );

		if ( is_wp_error( $session ) ) {
			return new WP_REST_Response(
				array( 'success' => false, 'message' => $session->get_error_message() ),
				500
			);
		}

		return new WP_REST_Response(
			array( 'success' => true, 'data' => $session ),
			200
		);
	}
}
