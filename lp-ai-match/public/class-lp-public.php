<?php
/**
 * Frontend controller — shortcodes and asset loading.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LP_Public {

	/**
	 * Initialize frontend hooks.
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );

		// Register shortcodes.
		add_shortcode( 'lp_horoscope', array( __CLASS__, 'shortcode_horoscope' ) );
		add_shortcode( 'lp_tarot', array( __CLASS__, 'shortcode_tarot' ) );
		add_shortcode( 'lp_profile_form', array( __CLASS__, 'shortcode_profile_form' ) );
		add_shortcode( 'lp_matching', array( __CLASS__, 'shortcode_matching' ) );
		add_shortcode( 'lp_matching_result', array( __CLASS__, 'shortcode_matching_result' ) );
		add_shortcode( 'lp_chat', array( __CLASS__, 'shortcode_chat' ) );
		add_shortcode( 'lp_pricing', array( __CLASS__, 'shortcode_pricing' ) );
	}

	/**
	 * Enqueue frontend assets.
	 */
	public static function enqueue_assets() {
		wp_enqueue_style(
			'lp-public-css',
			LP_AI_MATCH_PLUGIN_URL . 'public/css/public.css',
			array(),
			LP_AI_MATCH_VERSION
		);

		wp_enqueue_script(
			'lp-public-js',
			LP_AI_MATCH_PLUGIN_URL . 'public/js/public.js',
			array( 'jquery' ),
			LP_AI_MATCH_VERSION,
			true
		);

		wp_localize_script(
			'lp-public-js',
			'lpAiMatch',
			array(
				'apiUrl'    => esc_url_raw( rest_url( 'lp-ai-match/v1' ) ),
				'nonce'     => wp_create_nonce( 'wp_rest' ),
				'stripeKey' => LP_Stripe::get_publishable_key(),
				'i18n'      => array(
					'loading'      => __( '読み込み中...', 'lp-ai-match' ),
					'error'        => __( 'エラーが発生しました。', 'lp-ai-match' ),
					'send'         => __( '送信', 'lp-ai-match' ),
					'chatExpired'  => __( 'チャットの有効期限が切れました。', 'lp-ai-match' ),
					'reportSent'   => __( '通報しました。', 'lp-ai-match' ),
				),
			)
		);
	}

	/**
	 * Horoscope shortcode.
	 *
	 * @return string HTML output.
	 */
	public static function shortcode_horoscope() {
		ob_start();
		include LP_AI_MATCH_PLUGIN_DIR . 'public/views/horoscope.php';
		return ob_get_clean();
	}

	/**
	 * Tarot shortcode.
	 *
	 * @return string HTML output.
	 */
	public static function shortcode_tarot() {
		ob_start();
		include LP_AI_MATCH_PLUGIN_DIR . 'public/views/tarot.php';
		return ob_get_clean();
	}

	/**
	 * Profile form shortcode.
	 *
	 * @return string HTML output.
	 */
	public static function shortcode_profile_form() {
		if ( ! is_user_logged_in() ) {
			return '<div class="lp-notice">' . esc_html__( 'プロフィールを作成するにはログインしてください。', 'lp-ai-match' ) . '</div>';
		}
		ob_start();
		include LP_AI_MATCH_PLUGIN_DIR . 'public/views/profile-form.php';
		return ob_get_clean();
	}

	/**
	 * Matching page shortcode.
	 *
	 * @return string HTML output.
	 */
	public static function shortcode_matching() {
		if ( ! is_user_logged_in() ) {
			return '<div class="lp-notice">' . esc_html__( 'マッチングにはログインが必要です。', 'lp-ai-match' ) . '</div>';
		}
		ob_start();
		include LP_AI_MATCH_PLUGIN_DIR . 'public/views/matching.php';
		return ob_get_clean();
	}

	/**
	 * Matching result shortcode.
	 *
	 * @return string HTML output.
	 */
	public static function shortcode_matching_result() {
		if ( ! is_user_logged_in() ) {
			return '<div class="lp-notice">' . esc_html__( 'ログインしてください。', 'lp-ai-match' ) . '</div>';
		}
		ob_start();
		include LP_AI_MATCH_PLUGIN_DIR . 'public/views/matching-result.php';
		return ob_get_clean();
	}

	/**
	 * Chat shortcode.
	 *
	 * @return string HTML output.
	 */
	public static function shortcode_chat() {
		if ( ! is_user_logged_in() ) {
			return '<div class="lp-notice">' . esc_html__( 'チャットにはログインが必要です。', 'lp-ai-match' ) . '</div>';
		}
		ob_start();
		include LP_AI_MATCH_PLUGIN_DIR . 'public/views/chat.php';
		return ob_get_clean();
	}

	/**
	 * Pricing shortcode.
	 *
	 * @return string HTML output.
	 */
	public static function shortcode_pricing() {
		ob_start();
		include LP_AI_MATCH_PLUGIN_DIR . 'public/views/pricing.php';
		return ob_get_clean();
	}
}
