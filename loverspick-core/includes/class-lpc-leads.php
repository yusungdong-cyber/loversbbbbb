<?php
/**
 * LPC_Leads — Lead capture and partner application form handling.
 *
 * REST API endpoints for AJAX form submissions.
 * Stores leads in custom DB table + sends email notification.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class LPC_Leads {

    public static function init() {
        add_action( 'rest_api_init', array( __CLASS__, 'register_routes' ) );
    }

    /* ── REST Routes ────────────────────────────────────────── */

    public static function register_routes() {
        $ns = 'loverspick/v1';

        register_rest_route( $ns, '/lead', array(
            'methods'             => 'POST',
            'callback'            => array( __CLASS__, 'handle_lead' ),
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( $ns, '/partner-apply', array(
            'methods'             => 'POST',
            'callback'            => array( __CLASS__, 'handle_partner_app' ),
            'permission_callback' => '__return_true',
        ) );
    }

    /* ── Lead Submission ────────────────────────────────────── */

    public static function handle_lead( $request ) {
        $params = $request->get_json_params();

        $email = isset( $params['email'] ) ? sanitize_email( $params['email'] ) : '';
        if ( ! is_email( $email ) ) {
            return new WP_Error( 'invalid_email', __( 'Please enter a valid email address.', 'loverspick' ), array( 'status' => 400 ) );
        }

        $consent = ! empty( $params['consent'] ) ? 1 : 0;
        if ( ! $consent ) {
            return new WP_Error( 'no_consent', __( 'Please agree to receive communications.', 'loverspick' ), array( 'status' => 400 ) );
        }

        global $wpdb;

        $data = array(
            'name'             => isset( $params['name'] ) ? sanitize_text_field( $params['name'] ) : '',
            'email'            => $email,
            'country'          => isset( $params['country'] ) ? sanitize_text_field( $params['country'] ) : '',
            'travel_dates'     => isset( $params['travel_dates'] ) ? sanitize_text_field( $params['travel_dates'] ) : '',
            'messenger_handle' => isset( $params['messenger_handle'] ) ? sanitize_text_field( $params['messenger_handle'] ) : '',
            'messenger_type'   => isset( $params['messenger_type'] ) ? sanitize_text_field( $params['messenger_type'] ) : '',
            'consent'          => $consent,
            'utm_source'       => isset( $params['utm_source'] ) ? sanitize_text_field( $params['utm_source'] ) : '',
            'utm_medium'       => isset( $params['utm_medium'] ) ? sanitize_text_field( $params['utm_medium'] ) : '',
            'utm_campaign'     => isset( $params['utm_campaign'] ) ? sanitize_text_field( $params['utm_campaign'] ) : '',
            'utm_term'         => isset( $params['utm_term'] ) ? sanitize_text_field( $params['utm_term'] ) : '',
            'utm_content'      => isset( $params['utm_content'] ) ? sanitize_text_field( $params['utm_content'] ) : '',
            'source_page'      => isset( $params['source_page'] ) ? esc_url_raw( $params['source_page'] ) : '',
        );

        $result = $wpdb->insert(
            $wpdb->prefix . 'lpc_leads',
            $data,
            array( '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s' )
        );

        if ( false === $result ) {
            return new WP_Error( 'db_error', __( 'Something went wrong. Please try again.', 'loverspick' ), array( 'status' => 500 ) );
        }

        // Email notification.
        self::notify_admin_lead( $data );

        return rest_ensure_response( array(
            'success' => true,
            'message' => __( 'Thanks! We\'ll be in touch soon.', 'loverspick' ),
        ) );
    }

    /* ── Partner Application ────────────────────────────────── */

    public static function handle_partner_app( $request ) {
        $params = $request->get_json_params();

        $business = isset( $params['business_name'] ) ? sanitize_text_field( $params['business_name'] ) : '';
        $contact  = isset( $params['contact_info'] ) ? sanitize_text_field( $params['contact_info'] ) : '';

        if ( ! $business || ! $contact ) {
            return new WP_Error( 'missing_fields', __( 'Business name and contact info are required.', 'loverspick' ), array( 'status' => 400 ) );
        }

        global $wpdb;

        $data = array(
            'business_name' => $business,
            'contact_name'  => isset( $params['contact_name'] ) ? sanitize_text_field( $params['contact_name'] ) : '',
            'business_url'  => isset( $params['business_url'] ) ? esc_url_raw( $params['business_url'] ) : '',
            'contact_info'  => $contact,
            'category'      => isset( $params['category'] ) ? sanitize_text_field( $params['category'] ) : '',
            'area'          => isset( $params['area'] ) ? sanitize_text_field( $params['area'] ) : '',
            'message'       => isset( $params['message'] ) ? sanitize_textarea_field( $params['message'] ) : '',
        );

        $result = $wpdb->insert(
            $wpdb->prefix . 'lpc_partner_apps',
            $data,
            array( '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
        );

        if ( false === $result ) {
            return new WP_Error( 'db_error', __( 'Something went wrong. Please try again.', 'loverspick' ), array( 'status' => 500 ) );
        }

        // Notify admin.
        self::notify_admin_partner_app( $data );

        return rest_ensure_response( array(
            'success' => true,
            'message' => __( 'Application received! We\'ll review it shortly.', 'loverspick' ),
        ) );
    }

    /* ── Email Notifications ────────────────────────────────── */

    private static function notify_admin_lead( $data ) {
        $to      = get_option( 'lpc_admin_email', get_option( 'admin_email' ) );
        $subject = sprintf( '[LoversPick] New Lead: %s', $data['email'] );
        $body    = "New lead captured:\n\n";
        foreach ( $data as $key => $val ) {
            if ( $val ) {
                $body .= ucfirst( str_replace( '_', ' ', $key ) ) . ': ' . $val . "\n";
            }
        }
        $body .= "\nView all leads in WP Admin > LoversPick > Leads";

        wp_mail( $to, $subject, $body );
    }

    private static function notify_admin_partner_app( $data ) {
        $to      = get_option( 'lpc_admin_email', get_option( 'admin_email' ) );
        $subject = sprintf( '[LoversPick] Partner Application: %s', $data['business_name'] );
        $body    = "New partner application:\n\n";
        foreach ( $data as $key => $val ) {
            if ( $val ) {
                $body .= ucfirst( str_replace( '_', ' ', $key ) ) . ': ' . $val . "\n";
            }
        }
        $body .= "\nView applications in WP Admin > LoversPick > Partner Apps";

        wp_mail( $to, $subject, $body );
    }
}
