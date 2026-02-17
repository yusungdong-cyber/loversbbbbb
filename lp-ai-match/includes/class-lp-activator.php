<?php
/**
 * Plugin activation handler.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LP_Activator {

	/**
	 * Run on plugin activation.
	 */
	public static function activate() {
		self::create_tables();
		self::set_default_options();
		flush_rewrite_rules();
	}

	/**
	 * Create custom database tables.
	 */
	private static function create_tables() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		$sql_chat = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}lp_chat_messages (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			match_id BIGINT(20) UNSIGNED NOT NULL,
			sender_id BIGINT(20) UNSIGNED NOT NULL,
			receiver_id BIGINT(20) UNSIGNED NOT NULL,
			message TEXT NOT NULL,
			is_read TINYINT(1) DEFAULT 0,
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY match_id (match_id),
			KEY sender_id (sender_id),
			KEY receiver_id (receiver_id)
		) $charset_collate;";

		$sql_scores = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}lp_matching_scores (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			profile_a_id BIGINT(20) UNSIGNED NOT NULL,
			profile_b_id BIGINT(20) UNSIGNED NOT NULL,
			saju_score DECIMAL(5,2) DEFAULT 0,
			personality_score DECIMAL(5,2) DEFAULT 0,
			values_score DECIMAL(5,2) DEFAULT 0,
			total_score DECIMAL(5,2) DEFAULT 0,
			calculated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY profile_a_id (profile_a_id),
			KEY profile_b_id (profile_b_id),
			UNIQUE KEY pair (profile_a_id, profile_b_id)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql_chat );
		dbDelta( $sql_scores );
	}

	/**
	 * Set default plugin options.
	 */
	private static function set_default_options() {
		$defaults = array(
			'lp_ai_match_price_basic'       => 1000,
			'lp_ai_match_price_standard'    => 3000,
			'lp_ai_match_price_premium'     => 6900,
			'lp_ai_match_currency'          => 'jpy',
			'lp_ai_match_stripe_mode'       => 'test',
			'lp_ai_match_stripe_test_pk'    => '',
			'lp_ai_match_stripe_test_sk'    => '',
			'lp_ai_match_stripe_live_pk'    => '',
			'lp_ai_match_stripe_live_sk'    => '',
			'lp_ai_match_stripe_webhook_secret' => '',
			'lp_ai_match_openai_api_key'    => '',
			'lp_ai_match_saju_weight'       => 40,
			'lp_ai_match_personality_weight' => 40,
			'lp_ai_match_values_weight'     => 20,
			'lp_ai_match_chat_basic_hours'  => 24,
			'lp_ai_match_chat_standard_days' => 7,
			'lp_ai_match_chat_premium_days' => 7,
			'lp_ai_match_matches_basic'     => 1,
			'lp_ai_match_matches_standard'  => 3,
			'lp_ai_match_matches_premium'   => 5,
		);

		foreach ( $defaults as $key => $value ) {
			if ( false === get_option( $key ) ) {
				add_option( $key, $value );
			}
		}
	}
}
