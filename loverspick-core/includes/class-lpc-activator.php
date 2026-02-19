<?php
/**
 * LPC_Activator — Plugin activation handler.
 *
 * Creates DB tables, sets default options, generates pages.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class LPC_Activator {

    /**
     * Run on plugin activation.
     */
    public static function activate() {
        self::create_tables();
        self::set_defaults();
        LPC_Pages::create_all();
        flush_rewrite_rules();
    }

    /* ── Database Tables ────────────────────────────────────── */

    private static function create_tables() {
        global $wpdb;

        $charset = $wpdb->get_charset_collate();

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        // Leads table.
        $sql_leads = "CREATE TABLE {$wpdb->prefix}lpc_leads (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) DEFAULT '',
            email VARCHAR(255) NOT NULL,
            country VARCHAR(100) DEFAULT '',
            travel_dates VARCHAR(255) DEFAULT '',
            messenger_handle VARCHAR(255) DEFAULT '',
            messenger_type VARCHAR(50) DEFAULT '',
            consent TINYINT(1) NOT NULL DEFAULT 0,
            utm_source VARCHAR(255) DEFAULT '',
            utm_medium VARCHAR(255) DEFAULT '',
            utm_campaign VARCHAR(255) DEFAULT '',
            utm_term VARCHAR(255) DEFAULT '',
            utm_content VARCHAR(255) DEFAULT '',
            source_page VARCHAR(255) DEFAULT '',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_email (email),
            KEY idx_created (created_at)
        ) $charset;";

        dbDelta( $sql_leads );

        // Partner applications table.
        $sql_apps = "CREATE TABLE {$wpdb->prefix}lpc_partner_apps (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            business_name VARCHAR(255) NOT NULL,
            contact_name VARCHAR(255) DEFAULT '',
            business_url VARCHAR(500) DEFAULT '',
            contact_info VARCHAR(255) DEFAULT '',
            category VARCHAR(50) DEFAULT '',
            area VARCHAR(100) DEFAULT '',
            message TEXT,
            status VARCHAR(20) NOT NULL DEFAULT 'pending',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset;";

        dbDelta( $sql_apps );
    }

    /* ── Default Options ────────────────────────────────────── */

    private static function set_defaults() {
        $defaults = array(
            // General.
            'lpc_telegram_link'  => '',
            'lpc_whatsapp_link'  => '',
            'lpc_admin_email'    => get_option( 'admin_email' ),

            // Pricing.
            'lpc_currency'       => '$',
            'lpc_price_3day'     => '29',
            'lpc_price_5day'     => '39',
            'lpc_price_7day'     => '49',
            'lpc_price_premium'  => '15',

            // Checkout links (empty = lead capture fallback).
            'lpc_checkout_3day'  => '',
            'lpc_checkout_5day'  => '',
            'lpc_checkout_7day'  => '',

            // Analytics.
            'lpc_ga4_id'         => '',
            'lpc_meta_pixel_id'  => '',
        );

        foreach ( $defaults as $key => $value ) {
            if ( false === get_option( $key ) ) {
                add_option( $key, $value );
            }
        }
    }
}
