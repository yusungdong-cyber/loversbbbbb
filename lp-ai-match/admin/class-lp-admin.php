<?php
/**
 * Admin dashboard pages and settings.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LP_Admin {

	/**
	 * Initialize admin hooks.
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_menu_pages' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
	}

	/**
	 * Add admin menu pages.
	 */
	public static function add_menu_pages() {
		add_menu_page(
			__( 'LP AI Match', 'lp-ai-match' ),
			__( 'LP AI Match', 'lp-ai-match' ),
			'manage_options',
			'lp-ai-match',
			array( __CLASS__, 'render_dashboard' ),
			'dashicons-heart',
			30
		);

		add_submenu_page(
			'lp-ai-match',
			__( 'ダッシュボード', 'lp-ai-match' ),
			__( 'ダッシュボード', 'lp-ai-match' ),
			'manage_options',
			'lp-ai-match',
			array( __CLASS__, 'render_dashboard' )
		);

		add_submenu_page(
			'lp-ai-match',
			__( '設定', 'lp-ai-match' ),
			__( '設定', 'lp-ai-match' ),
			'manage_options',
			'lp-ai-match-settings',
			array( __CLASS__, 'render_settings' )
		);

		add_submenu_page(
			'lp-ai-match',
			__( 'タロットカード管理', 'lp-ai-match' ),
			__( 'タロットカード', 'lp-ai-match' ),
			'manage_options',
			'lp-ai-match-tarot',
			array( __CLASS__, 'render_tarot_management' )
		);

		add_submenu_page(
			'lp-ai-match',
			__( '星座運勢管理', 'lp-ai-match' ),
			__( '星座運勢', 'lp-ai-match' ),
			'manage_options',
			'lp-ai-match-horoscope',
			array( __CLASS__, 'render_horoscope_management' )
		);

		add_submenu_page(
			'lp-ai-match',
			__( 'マッチングログ', 'lp-ai-match' ),
			__( 'マッチングログ', 'lp-ai-match' ),
			'manage_options',
			'lp-ai-match-logs',
			array( __CLASS__, 'render_match_logs' )
		);

		add_submenu_page(
			'lp-ai-match',
			__( 'ユーザー管理', 'lp-ai-match' ),
			__( 'ユーザー管理', 'lp-ai-match' ),
			'manage_options',
			'lp-ai-match-users',
			array( __CLASS__, 'render_user_management' )
		);

		add_submenu_page(
			'lp-ai-match',
			__( '通報管理', 'lp-ai-match' ),
			__( '通報管理', 'lp-ai-match' ),
			'manage_options',
			'lp-ai-match-reports',
			array( __CLASS__, 'render_chat_reports' )
		);
	}

	/**
	 * Register plugin settings.
	 */
	public static function register_settings() {
		// Price settings.
		register_setting( 'lp_ai_match_settings', 'lp_ai_match_price_basic', 'absint' );
		register_setting( 'lp_ai_match_settings', 'lp_ai_match_price_standard', 'absint' );
		register_setting( 'lp_ai_match_settings', 'lp_ai_match_price_premium', 'absint' );
		register_setting( 'lp_ai_match_settings', 'lp_ai_match_currency', 'sanitize_text_field' );

		// Stripe settings.
		register_setting( 'lp_ai_match_settings', 'lp_ai_match_stripe_mode', 'sanitize_text_field' );
		register_setting( 'lp_ai_match_settings', 'lp_ai_match_stripe_test_pk', 'sanitize_text_field' );
		register_setting( 'lp_ai_match_settings', 'lp_ai_match_stripe_test_sk', 'sanitize_text_field' );
		register_setting( 'lp_ai_match_settings', 'lp_ai_match_stripe_live_pk', 'sanitize_text_field' );
		register_setting( 'lp_ai_match_settings', 'lp_ai_match_stripe_live_sk', 'sanitize_text_field' );
		register_setting( 'lp_ai_match_settings', 'lp_ai_match_stripe_webhook_secret', 'sanitize_text_field' );

		// OpenAI settings.
		register_setting( 'lp_ai_match_settings', 'lp_ai_match_openai_api_key', 'sanitize_text_field' );

		// Matching weights.
		register_setting( 'lp_ai_match_settings', 'lp_ai_match_saju_weight', 'absint' );
		register_setting( 'lp_ai_match_settings', 'lp_ai_match_personality_weight', 'absint' );
		register_setting( 'lp_ai_match_settings', 'lp_ai_match_values_weight', 'absint' );

		// Chat settings.
		register_setting( 'lp_ai_match_settings', 'lp_ai_match_chat_basic_hours', 'absint' );
		register_setting( 'lp_ai_match_settings', 'lp_ai_match_chat_standard_days', 'absint' );
		register_setting( 'lp_ai_match_settings', 'lp_ai_match_chat_premium_days', 'absint' );

		// Match counts.
		register_setting( 'lp_ai_match_settings', 'lp_ai_match_matches_basic', 'absint' );
		register_setting( 'lp_ai_match_settings', 'lp_ai_match_matches_standard', 'absint' );
		register_setting( 'lp_ai_match_settings', 'lp_ai_match_matches_premium', 'absint' );
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @param string $hook Current admin page hook.
	 */
	public static function enqueue_assets( $hook ) {
		if ( strpos( $hook, 'lp-ai-match' ) === false ) {
			return;
		}

		wp_enqueue_style(
			'lp-admin-css',
			LP_AI_MATCH_PLUGIN_URL . 'admin/css/admin.css',
			array(),
			LP_AI_MATCH_VERSION
		);

		wp_enqueue_script(
			'lp-admin-js',
			LP_AI_MATCH_PLUGIN_URL . 'admin/js/admin.js',
			array( 'jquery' ),
			LP_AI_MATCH_VERSION,
			true
		);
	}

	/**
	 * Render dashboard page.
	 */
	public static function render_dashboard() {
		$total_profiles = wp_count_posts( 'lp_profile' );
		$total_matches  = wp_count_posts( 'lp_match' );
		$total_reports  = wp_count_posts( 'lp_report' );

		include LP_AI_MATCH_PLUGIN_DIR . 'admin/views/dashboard.php';
	}

	/**
	 * Render settings page.
	 */
	public static function render_settings() {
		include LP_AI_MATCH_PLUGIN_DIR . 'admin/views/settings.php';
	}

	/**
	 * Render tarot management page.
	 */
	public static function render_tarot_management() {
		// Handle form submission.
		if ( isset( $_POST['lp_tarot_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['lp_tarot_nonce'] ) ), 'lp_save_tarot' ) ) {
			$card_id = isset( $_POST['card_id'] ) ? absint( $_POST['card_id'] ) : -1;
			if ( $card_id >= 0 ) {
				LP_Tarot::update_card(
					$card_id,
					array(
						'upright' => isset( $_POST['upright'] ) ? sanitize_textarea_field( wp_unslash( $_POST['upright'] ) ) : '',
						'reverse' => isset( $_POST['reverse'] ) ? sanitize_textarea_field( wp_unslash( $_POST['reverse'] ) ) : '',
					)
				);
				echo '<div class="notice notice-success"><p>' . esc_html__( '保存しました。', 'lp-ai-match' ) . '</p></div>';
			}
		}

		$cards = LP_Tarot::get_cards();
		include LP_AI_MATCH_PLUGIN_DIR . 'admin/views/tarot.php';
	}

	/**
	 * Render horoscope management page.
	 */
	public static function render_horoscope_management() {
		// Handle form submission.
		if ( isset( $_POST['lp_horoscope_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['lp_horoscope_nonce'] ) ), 'lp_save_horoscope' ) ) {
			$sign = isset( $_POST['sign'] ) ? sanitize_text_field( wp_unslash( $_POST['sign'] ) ) : '';
			if ( $sign ) {
				$fortunes = isset( $_POST['fortunes'] ) ? array_map( 'sanitize_textarea_field', wp_unslash( $_POST['fortunes'] ) ) : array();
				update_option( 'lp_ai_match_horoscope_' . $sign, array_filter( $fortunes ) );
				echo '<div class="notice notice-success"><p>' . esc_html__( '保存しました。', 'lp-ai-match' ) . '</p></div>';
			}
		}

		$signs = LP_Horoscope::get_all_signs();
		include LP_AI_MATCH_PLUGIN_DIR . 'admin/views/horoscope.php';
	}

	/**
	 * Render match logs page.
	 */
	public static function render_match_logs() {
		$matches = get_posts(
			array(
				'post_type'      => 'lp_match',
				'posts_per_page' => 50,
				'orderby'        => 'date',
				'order'          => 'DESC',
			)
		);

		include LP_AI_MATCH_PLUGIN_DIR . 'admin/views/match-logs.php';
	}

	/**
	 * Render user management page.
	 */
	public static function render_user_management() {
		// Handle block/unblock.
		if ( isset( $_POST['lp_user_action_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['lp_user_action_nonce'] ) ), 'lp_user_action' ) ) {
			$target_user = isset( $_POST['user_id'] ) ? absint( $_POST['user_id'] ) : 0;
			$action      = isset( $_POST['action_type'] ) ? sanitize_text_field( wp_unslash( $_POST['action_type'] ) ) : '';

			if ( $target_user ) {
				if ( 'block' === $action ) {
					LP_Chat::block_user( $target_user );
				} elseif ( 'unblock' === $action ) {
					LP_Chat::unblock_user( $target_user );
				}
			}
		}

		// Get users with profiles.
		$profiles = get_posts(
			array(
				'post_type'      => 'lp_profile',
				'posts_per_page' => 100,
				'post_status'    => 'publish',
			)
		);

		include LP_AI_MATCH_PLUGIN_DIR . 'admin/views/users.php';
	}

	/**
	 * Render chat reports page.
	 */
	public static function render_chat_reports() {
		$reports = get_option( 'lp_ai_match_chat_reports', array() );
		include LP_AI_MATCH_PLUGIN_DIR . 'admin/views/chat-reports.php';
	}
}
