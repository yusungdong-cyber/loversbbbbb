<?php
/**
 * LPC_Partners — Custom Post Type for Byeolgram Road partner listings.
 *
 * Registers the `lpc_partner` CPT with taxonomies and meta boxes.
 * Admin can add/edit partners through standard WP post editor.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class LPC_Partners {

    public static function init() {
        add_action( 'init', array( __CLASS__, 'register_post_type' ) );
        add_action( 'init', array( __CLASS__, 'register_taxonomies' ) );
        add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
        add_action( 'save_post_lpc_partner', array( __CLASS__, 'save_meta' ), 10, 2 );
        add_filter( 'manage_lpc_partner_posts_columns', array( __CLASS__, 'admin_columns' ) );
        add_action( 'manage_lpc_partner_posts_custom_column', array( __CLASS__, 'admin_column_content' ), 10, 2 );
    }

    /* ── CPT Registration ───────────────────────────────────── */

    public static function register_post_type() {
        $labels = array(
            'name'               => __( 'Partners', 'loverspick' ),
            'singular_name'      => __( 'Partner', 'loverspick' ),
            'add_new'            => __( 'Add Partner', 'loverspick' ),
            'add_new_item'       => __( 'Add New Partner', 'loverspick' ),
            'edit_item'          => __( 'Edit Partner', 'loverspick' ),
            'new_item'           => __( 'New Partner', 'loverspick' ),
            'view_item'          => __( 'View Partner', 'loverspick' ),
            'search_items'       => __( 'Search Partners', 'loverspick' ),
            'not_found'          => __( 'No partners found', 'loverspick' ),
            'not_found_in_trash' => __( 'No partners found in trash', 'loverspick' ),
            'all_items'          => __( 'All Partners', 'loverspick' ),
            'menu_name'          => __( 'Partners', 'loverspick' ),
        );

        register_post_type( 'lpc_partner', array(
            'labels'       => $labels,
            'public'       => false,
            'show_ui'      => true,
            'show_in_menu' => true,
            'menu_icon'    => 'dashicons-store',
            'menu_position' => 31,
            'supports'     => array( 'title', 'thumbnail' ),
            'has_archive'  => false,
            'rewrite'      => false,
            'show_in_rest' => false,
        ) );
    }

    /* ── Taxonomies ─────────────────────────────────────────── */

    public static function register_taxonomies() {
        // Category: Stay / Restaurant.
        register_taxonomy( 'lpc_partner_cat', 'lpc_partner', array(
            'labels'       => array(
                'name'          => __( 'Category', 'loverspick' ),
                'singular_name' => __( 'Category', 'loverspick' ),
                'add_new_item'  => __( 'Add Category', 'loverspick' ),
            ),
            'hierarchical' => true,
            'public'       => false,
            'show_ui'      => true,
            'show_admin_column' => true,
            'show_in_rest' => false,
        ) );

        // Area: Hongdae, Myeongdong, etc.
        register_taxonomy( 'lpc_partner_area', 'lpc_partner', array(
            'labels'       => array(
                'name'          => __( 'Area', 'loverspick' ),
                'singular_name' => __( 'Area', 'loverspick' ),
                'add_new_item'  => __( 'Add Area', 'loverspick' ),
            ),
            'hierarchical' => true,
            'public'       => false,
            'show_ui'      => true,
            'show_admin_column' => true,
            'show_in_rest' => false,
        ) );

        // Seed default terms on first run.
        if ( ! get_option( 'lpc_terms_seeded' ) ) {
            self::seed_terms();
            update_option( 'lpc_terms_seeded', true );
        }
    }

    private static function seed_terms() {
        $categories = array( 'Stay', 'Restaurant' );
        foreach ( $categories as $cat ) {
            if ( ! term_exists( $cat, 'lpc_partner_cat' ) ) {
                wp_insert_term( $cat, 'lpc_partner_cat' );
            }
        }

        $areas = array( 'Hongdae', 'Myeongdong', 'Gangnam', 'Itaewon', 'Seongsu' );
        foreach ( $areas as $area ) {
            if ( ! term_exists( $area, 'lpc_partner_area' ) ) {
                wp_insert_term( $area, 'lpc_partner_area' );
            }
        }
    }

    /* ── Meta Boxes ─────────────────────────────────────────── */

    public static function add_meta_boxes() {
        add_meta_box(
            'lpc_partner_details',
            __( 'Partner Details', 'loverspick' ),
            array( __CLASS__, 'render_meta_box' ),
            'lpc_partner',
            'normal',
            'high'
        );
    }

    public static function render_meta_box( $post ) {
        wp_nonce_field( 'lpc_partner_meta', 'lpc_partner_nonce' );

        $fields = array(
            '_lpc_short_desc'  => array( 'label' => __( 'Short Description (why it\'s good for foreigners)', 'loverspick' ), 'type' => 'textarea' ),
            '_lpc_benefits'    => array( 'label' => __( 'Exclusive Benefits (free extras / upgrades — NO discount language)', 'loverspick' ), 'type' => 'textarea' ),
            '_lpc_languages'   => array( 'label' => __( 'Languages Supported', 'loverspick' ), 'type' => 'checkboxes', 'options' => array( 'EN' => 'English', 'JP' => 'Japanese', 'KR' => 'Korean', 'CN' => 'Chinese' ) ),
            '_lpc_map_link'    => array( 'label' => __( 'Google Maps Link', 'loverspick' ), 'type' => 'url' ),
            '_lpc_contact_link' => array( 'label' => __( 'Reserve / Contact Link (URL, WhatsApp, or Telegram)', 'loverspick' ), 'type' => 'url' ),
            '_lpc_featured'    => array( 'label' => __( 'Featured Partner', 'loverspick' ), 'type' => 'checkbox' ),
        );

        echo '<table class="form-table lpc-meta-table">';

        foreach ( $fields as $key => $field ) {
            $value = get_post_meta( $post->ID, $key, true );
            echo '<tr><th><label for="' . esc_attr( $key ) . '">' . esc_html( $field['label'] ) . '</label></th><td>';

            switch ( $field['type'] ) {
                case 'textarea':
                    printf(
                        '<textarea id="%1$s" name="%1$s" rows="3" class="large-text">%2$s</textarea>',
                        esc_attr( $key ),
                        esc_textarea( $value )
                    );
                    break;

                case 'url':
                    printf(
                        '<input type="url" id="%1$s" name="%1$s" value="%2$s" class="regular-text" />',
                        esc_attr( $key ),
                        esc_url( $value )
                    );
                    break;

                case 'checkbox':
                    printf(
                        '<input type="checkbox" id="%1$s" name="%1$s" value="1" %2$s />',
                        esc_attr( $key ),
                        checked( $value, '1', false )
                    );
                    break;

                case 'checkboxes':
                    $saved = is_array( $value ) ? $value : array();
                    foreach ( $field['options'] as $opt_val => $opt_label ) {
                        printf(
                            '<label style="margin-right:16px;"><input type="checkbox" name="%1$s[]" value="%2$s" %3$s /> %4$s</label>',
                            esc_attr( $key ),
                            esc_attr( $opt_val ),
                            checked( in_array( $opt_val, $saved, true ), true, false ),
                            esc_html( $opt_label )
                        );
                    }
                    break;
            }

            echo '</td></tr>';
        }

        echo '</table>';
    }

    /* ── Save Meta ──────────────────────────────────────────── */

    public static function save_meta( $post_id, $post ) {
        if ( ! isset( $_POST['lpc_partner_nonce'] ) || ! wp_verify_nonce( $_POST['lpc_partner_nonce'], 'lpc_partner_meta' ) ) {
            return;
        }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Text / textarea fields.
        $text_fields = array( '_lpc_short_desc', '_lpc_benefits' );
        foreach ( $text_fields as $key ) {
            if ( isset( $_POST[ $key ] ) ) {
                update_post_meta( $post_id, $key, sanitize_textarea_field( wp_unslash( $_POST[ $key ] ) ) );
            }
        }

        // URL fields.
        $url_fields = array( '_lpc_map_link', '_lpc_contact_link' );
        foreach ( $url_fields as $key ) {
            if ( isset( $_POST[ $key ] ) ) {
                update_post_meta( $post_id, $key, esc_url_raw( wp_unslash( $_POST[ $key ] ) ) );
            }
        }

        // Languages (checkbox array).
        $languages = isset( $_POST['_lpc_languages'] ) && is_array( $_POST['_lpc_languages'] )
            ? array_map( 'sanitize_text_field', wp_unslash( $_POST['_lpc_languages'] ) )
            : array();
        update_post_meta( $post_id, '_lpc_languages', $languages );

        // Featured toggle.
        $featured = isset( $_POST['_lpc_featured'] ) ? '1' : '0';
        update_post_meta( $post_id, '_lpc_featured', $featured );
    }

    /* ── Admin Columns ──────────────────────────────────────── */

    public static function admin_columns( $columns ) {
        $new = array();
        foreach ( $columns as $key => $label ) {
            $new[ $key ] = $label;
            if ( 'title' === $key ) {
                $new['lpc_area']     = __( 'Area', 'loverspick' );
                $new['lpc_featured'] = __( 'Featured', 'loverspick' );
                $new['lpc_langs']    = __( 'Languages', 'loverspick' );
            }
        }
        return $new;
    }

    public static function admin_column_content( $column, $post_id ) {
        switch ( $column ) {
            case 'lpc_area':
                $terms = get_the_terms( $post_id, 'lpc_partner_area' );
                echo $terms && ! is_wp_error( $terms )
                    ? esc_html( implode( ', ', wp_list_pluck( $terms, 'name' ) ) )
                    : '—';
                break;

            case 'lpc_featured':
                echo get_post_meta( $post_id, '_lpc_featured', true ) === '1' ? '&#9733;' : '—';
                break;

            case 'lpc_langs':
                $langs = get_post_meta( $post_id, '_lpc_languages', true );
                echo is_array( $langs ) && $langs ? esc_html( implode( ', ', $langs ) ) : '—';
                break;
        }
    }
}
