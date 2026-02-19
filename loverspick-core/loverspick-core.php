<?php
/**
 * Plugin Name: LoversPick Core
 * Plugin URI:  https://loverspick.com
 * Description: LoversPick Seoul — anti-ripoff travel concierge with real local chat support. Core plugin for landing pages, pricing, lead capture, and Byeolgram Road partner directory.
 * Version:     1.0.0
 * Author:      LoversPick
 * Author URI:  https://loverspick.com
 * Text Domain: loverspick
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * License:     GPL-2.0-or-later
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* ── Constants ──────────────────────────────────────────────── */
define( 'LPC_VERSION', '1.0.0' );
define( 'LPC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'LPC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'LPC_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/* ── Includes ───────────────────────────────────────────────── */
require_once LPC_PLUGIN_DIR . 'includes/class-lpc-activator.php';
require_once LPC_PLUGIN_DIR . 'includes/class-lpc-partners.php';
require_once LPC_PLUGIN_DIR . 'includes/class-lpc-settings.php';
require_once LPC_PLUGIN_DIR . 'includes/class-lpc-shortcodes.php';
require_once LPC_PLUGIN_DIR . 'includes/class-lpc-leads.php';
require_once LPC_PLUGIN_DIR . 'includes/class-lpc-pages.php';

/* ── Activation / Deactivation ──────────────────────────────── */
register_activation_hook( __FILE__, array( 'LPC_Activator', 'activate' ) );
register_deactivation_hook( __FILE__, function () {
    flush_rewrite_rules();
} );

/* ── Init ───────────────────────────────────────────────────── */
add_action( 'plugins_loaded', 'lpc_init' );

function lpc_init() {
    load_plugin_textdomain( 'loverspick', false, dirname( LPC_PLUGIN_BASENAME ) . '/languages' );

    LPC_Partners::init();
    LPC_Settings::init();
    LPC_Shortcodes::init();
    LPC_Leads::init();
}

/* ── Analytics (GA4 + Meta Pixel) ───────────────────────────── */
add_action( 'wp_head', 'lpc_inject_analytics', 1 );

function lpc_inject_analytics() {
    $ga4 = get_option( 'lpc_ga4_id', '' );
    if ( $ga4 ) {
        printf(
            '<script async src="https://www.googletagmanager.com/gtag/js?id=%1$s"></script>' .
            '<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}' .
            'gtag("js",new Date());gtag("config","%1$s");</script>' . "\n",
            esc_attr( $ga4 )
        );
    }

    $pixel = get_option( 'lpc_meta_pixel_id', '' );
    if ( $pixel ) {
        printf(
            "<script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?" .
            "n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;" .
            "n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];" .
            "s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');" .
            "fbq('init','%s');fbq('track','PageView');</script>\n",
            esc_js( $pixel )
        );
    }
}

/* ── Frontend Assets ────────────────────────────────────────── */
add_action( 'wp_enqueue_scripts', 'lpc_enqueue_assets' );

function lpc_enqueue_assets() {
    wp_enqueue_style(
        'loverspick',
        LPC_PLUGIN_URL . 'public/css/loverspick.css',
        array(),
        LPC_VERSION
    );

    wp_enqueue_script(
        'loverspick',
        LPC_PLUGIN_URL . 'public/js/loverspick.js',
        array( 'jquery' ),
        LPC_VERSION,
        true
    );

    wp_localize_script( 'loverspick', 'lpcData', array(
        'apiUrl'       => esc_url_raw( rest_url( 'loverspick/v1' ) ),
        'nonce'        => wp_create_nonce( 'wp_rest' ),
        'telegramLink' => esc_url( get_option( 'lpc_telegram_link', '' ) ),
    ) );
}

/* ── Admin Assets ───────────────────────────────────────────── */
add_action( 'admin_enqueue_scripts', 'lpc_admin_assets' );

function lpc_admin_assets( $hook ) {
    if ( strpos( $hook, 'loverspick' ) === false ) {
        return;
    }
    wp_enqueue_style(
        'loverspick-admin',
        LPC_PLUGIN_URL . 'admin/css/admin.css',
        array(),
        LPC_VERSION
    );
}
