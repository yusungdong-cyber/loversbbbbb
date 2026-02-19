<?php
/**
 * LPC_Settings — Admin settings page for LoversPick.
 *
 * Tabbed interface: General, Pricing, Analytics.
 * All pricing, checkout links, and analytics IDs are configurable here.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class LPC_Settings {

    public static function init() {
        add_action( 'admin_menu', array( __CLASS__, 'add_menu' ) );
        add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
    }

    /* ── Menu ───────────────────────────────────────────────── */

    public static function add_menu() {
        add_menu_page(
            __( 'LoversPick', 'loverspick' ),
            __( 'LoversPick', 'loverspick' ),
            'manage_options',
            'loverspick',
            array( __CLASS__, 'render_settings_page' ),
            'dashicons-heart',
            30
        );

        add_submenu_page(
            'loverspick',
            __( 'Settings', 'loverspick' ),
            __( 'Settings', 'loverspick' ),
            'manage_options',
            'loverspick',
            array( __CLASS__, 'render_settings_page' )
        );

        add_submenu_page(
            'loverspick',
            __( 'Leads', 'loverspick' ),
            __( 'Leads', 'loverspick' ),
            'manage_options',
            'loverspick-leads',
            array( __CLASS__, 'render_leads_page' )
        );

        add_submenu_page(
            'loverspick',
            __( 'Partner Applications', 'loverspick' ),
            __( 'Partner Apps', 'loverspick' ),
            'manage_options',
            'loverspick-partner-apps',
            array( __CLASS__, 'render_partner_apps_page' )
        );
    }

    /* ── Settings Registration ──────────────────────────────── */

    public static function register_settings() {
        // General settings.
        register_setting( 'lpc_settings_general', 'lpc_telegram_link', array( 'sanitize_callback' => 'esc_url_raw' ) );
        register_setting( 'lpc_settings_general', 'lpc_whatsapp_link', array( 'sanitize_callback' => 'esc_url_raw' ) );
        register_setting( 'lpc_settings_general', 'lpc_admin_email', array( 'sanitize_callback' => 'sanitize_email' ) );

        // Pricing settings.
        $pricing = array(
            'lpc_currency',
            'lpc_price_3day',
            'lpc_price_5day',
            'lpc_price_7day',
            'lpc_price_premium',
            'lpc_checkout_3day',
            'lpc_checkout_5day',
            'lpc_checkout_7day',
        );
        foreach ( $pricing as $opt ) {
            register_setting( 'lpc_settings_pricing', $opt, array( 'sanitize_callback' => 'sanitize_text_field' ) );
        }

        // Analytics settings.
        $analytics = array(
            'lpc_ga4_id',
            'lpc_meta_pixel_id',
        );
        foreach ( $analytics as $opt ) {
            register_setting( 'lpc_settings_analytics', $opt, array( 'sanitize_callback' => 'sanitize_text_field' ) );
        }
    }

    /* ── Render Pages ───────────────────────────────────────── */

    public static function render_settings_page() {
        require_once LPC_PLUGIN_DIR . 'admin/views/settings.php';
    }

    public static function render_leads_page() {
        require_once LPC_PLUGIN_DIR . 'admin/views/leads.php';
    }

    public static function render_partner_apps_page() {
        require_once LPC_PLUGIN_DIR . 'admin/views/partner-apps.php';
    }
}
