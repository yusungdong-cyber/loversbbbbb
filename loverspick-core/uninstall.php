<?php
/**
 * LoversPick Core — Uninstall
 *
 * Runs when the plugin is deleted via WP Admin.
 * Removes custom DB tables, options, and generated pages.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

global $wpdb;

// Drop custom tables.
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}lpc_leads" );
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}lpc_partner_apps" );

// Delete all partner posts.
$partners = get_posts( array(
    'post_type'   => 'lpc_partner',
    'numberposts' => -1,
    'post_status' => 'any',
    'fields'      => 'ids',
) );
foreach ( $partners as $id ) {
    wp_delete_post( $id, true );
}

// Delete pages created by the plugin.
$page_slugs = array(
    'loverspick-home',
    'pricing',
    'how-it-works',
    'faq',
    'contact',
    'terms',
    'privacy-policy-loverspick',
    'byeolgram-road',
);
foreach ( $page_slugs as $slug ) {
    $page = get_page_by_path( $slug );
    if ( $page ) {
        wp_delete_post( $page->ID, true );
    }
}

// Delete all plugin options.
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE 'lpc\_%'" );
