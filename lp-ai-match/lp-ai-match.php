<?php
/**
 * Plugin Name: LP AI Match
 * Plugin URI:  https://github.com/yusungdong-cyber/loversbbbbb
 * Description: 韓国式AI運命マッチング — Korean-style saju + AI compatibility matching service
 * Version:     1.0.0
 * Author:      LP AI Match Team
 * Author URI:  https://github.com/yusungdong-cyber
 * Text Domain: lp-ai-match
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * License:     GPL-2.0+
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'LP_AI_MATCH_VERSION', '1.0.0' );
define( 'LP_AI_MATCH_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'LP_AI_MATCH_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'LP_AI_MATCH_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

// Autoload includes.
require_once LP_AI_MATCH_PLUGIN_DIR . 'includes/class-lp-activator.php';
require_once LP_AI_MATCH_PLUGIN_DIR . 'includes/class-lp-deactivator.php';
require_once LP_AI_MATCH_PLUGIN_DIR . 'includes/class-lp-post-types.php';
require_once LP_AI_MATCH_PLUGIN_DIR . 'includes/class-lp-saju.php';
require_once LP_AI_MATCH_PLUGIN_DIR . 'includes/class-lp-horoscope.php';
require_once LP_AI_MATCH_PLUGIN_DIR . 'includes/class-lp-tarot.php';
require_once LP_AI_MATCH_PLUGIN_DIR . 'includes/class-lp-matching.php';
require_once LP_AI_MATCH_PLUGIN_DIR . 'includes/class-lp-report.php';
require_once LP_AI_MATCH_PLUGIN_DIR . 'includes/class-lp-pdf.php';
require_once LP_AI_MATCH_PLUGIN_DIR . 'includes/class-lp-chat.php';
require_once LP_AI_MATCH_PLUGIN_DIR . 'includes/class-lp-stripe.php';
require_once LP_AI_MATCH_PLUGIN_DIR . 'includes/class-lp-rest-api.php';
require_once LP_AI_MATCH_PLUGIN_DIR . 'admin/class-lp-admin.php';
require_once LP_AI_MATCH_PLUGIN_DIR . 'public/class-lp-public.php';

// Activation / Deactivation hooks.
register_activation_hook( __FILE__, array( 'LP_Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'LP_Deactivator', 'deactivate' ) );

/**
 * Initialize the plugin.
 */
function lp_ai_match_init() {
	// Load text domain.
	load_plugin_textdomain( 'lp-ai-match', false, dirname( LP_AI_MATCH_PLUGIN_BASENAME ) . '/languages' );

	// Initialize components.
	LP_Post_Types::init();
	LP_Rest_API::init();
	LP_Chat::init();
	LP_Stripe::init();

	if ( is_admin() ) {
		LP_Admin::init();
	}

	LP_Public::init();
}
add_action( 'plugins_loaded', 'lp_ai_match_init' );
