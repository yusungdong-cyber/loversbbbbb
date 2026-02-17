<?php
/**
 * Stripe payment integration.
 *
 * Handles checkout sessions and webhook events.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LP_Stripe {

	/**
	 * Initialize Stripe hooks.
	 */
	public static function init() {
		add_action( 'rest_api_init', array( __CLASS__, 'register_webhook_route' ) );
	}

	/**
	 * Register webhook REST route.
	 */
	public static function register_webhook_route() {
		register_rest_route(
			'lp-ai-match/v1',
			'/payment/webhook',
			array(
				'methods'             => 'POST',
				'callback'            => array( __CLASS__, 'handle_webhook' ),
				'permission_callback' => '__return_true', // Verified via Stripe signature.
			)
		);
	}

	/**
	 * Get Stripe API key.
	 *
	 * @return string Secret key.
	 */
	private static function get_secret_key() {
		$mode = get_option( 'lp_ai_match_stripe_mode', 'test' );
		if ( 'live' === $mode ) {
			return get_option( 'lp_ai_match_stripe_live_sk', '' );
		}
		return get_option( 'lp_ai_match_stripe_test_sk', '' );
	}

	/**
	 * Get Stripe publishable key.
	 *
	 * @return string Publishable key.
	 */
	public static function get_publishable_key() {
		$mode = get_option( 'lp_ai_match_stripe_mode', 'test' );
		if ( 'live' === $mode ) {
			return get_option( 'lp_ai_match_stripe_live_pk', '' );
		}
		return get_option( 'lp_ai_match_stripe_test_pk', '' );
	}

	/**
	 * Create a Stripe Checkout session.
	 *
	 * @param string $tier    Product tier (basic, standard, premium).
	 * @param int    $user_id WordPress user ID.
	 * @return array|WP_Error Session data or error.
	 */
	public static function create_checkout_session( $tier, $user_id ) {
		$secret_key = self::get_secret_key();
		if ( empty( $secret_key ) ) {
			return new WP_Error( 'no_stripe_key', __( 'Stripe APIキーが設定されていません。', 'lp-ai-match' ) );
		}

		$prices = array(
			'basic'    => (int) get_option( 'lp_ai_match_price_basic', 1000 ),
			'standard' => (int) get_option( 'lp_ai_match_price_standard', 3000 ),
			'premium'  => (int) get_option( 'lp_ai_match_price_premium', 6900 ),
		);

		if ( ! isset( $prices[ $tier ] ) ) {
			return new WP_Error( 'invalid_tier', __( '無効なプランです。', 'lp-ai-match' ) );
		}

		$product_names = array(
			'basic'    => '今日の運命の相手 - ベーシック',
			'standard' => '運命マッチング - スタンダード',
			'premium'  => '韓国式四柱結婚相性 - プレミアム',
		);

		$currency = get_option( 'lp_ai_match_currency', 'jpy' );
		$amount   = $prices[ $tier ];

		// JPY doesn't use decimal places.
		if ( 'jpy' !== strtolower( $currency ) ) {
			$amount = $amount * 100;
		}

		$success_url = add_query_arg(
			array(
				'lp_payment' => 'success',
				'session_id' => '{CHECKOUT_SESSION_ID}',
			),
			home_url( '/matching-result/' )
		);
		$cancel_url = home_url( '/matching/' );

		$response = wp_remote_post(
			'https://api.stripe.com/v1/checkout/sessions',
			array(
				'timeout' => 30,
				'headers' => array(
					'Authorization' => 'Bearer ' . $secret_key,
					'Content-Type'  => 'application/x-www-form-urlencoded',
				),
				'body'    => http_build_query(
					array(
						'payment_method_types[]' => 'card',
						'line_items[0][price_data][currency]'     => $currency,
						'line_items[0][price_data][product_data][name]' => $product_names[ $tier ],
						'line_items[0][price_data][unit_amount]'  => $amount,
						'line_items[0][quantity]'                 => 1,
						'mode'        => 'payment',
						'success_url' => $success_url,
						'cancel_url'  => $cancel_url,
						'metadata[user_id]' => $user_id,
						'metadata[tier]'    => $tier,
					)
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( isset( $body['error'] ) ) {
			return new WP_Error( 'stripe_error', $body['error']['message'] );
		}

		// Store session reference.
		if ( isset( $body['id'] ) ) {
			update_user_meta( $user_id, '_lp_stripe_session_' . $body['id'], array(
				'tier'       => $tier,
				'amount'     => $prices[ $tier ],
				'created_at' => current_time( 'mysql' ),
			) );
		}

		return array(
			'session_id'  => $body['id'],
			'checkout_url' => isset( $body['url'] ) ? $body['url'] : '',
		);
	}

	/**
	 * Handle Stripe webhook.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response Response.
	 */
	public static function handle_webhook( $request ) {
		$payload   = $request->get_body();
		$sig_header = isset( $_SERVER['HTTP_STRIPE_SIGNATURE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_STRIPE_SIGNATURE'] ) ) : '';
		$webhook_secret = get_option( 'lp_ai_match_stripe_webhook_secret', '' );

		// Verify webhook signature.
		if ( ! empty( $webhook_secret ) ) {
			$verified = self::verify_webhook_signature( $payload, $sig_header, $webhook_secret );
			if ( ! $verified ) {
				return new WP_REST_Response( array( 'error' => 'Invalid signature' ), 400 );
			}
		}

		$event = json_decode( $payload, true );

		if ( ! $event || ! isset( $event['type'] ) ) {
			return new WP_REST_Response( array( 'error' => 'Invalid payload' ), 400 );
		}

		if ( 'checkout.session.completed' === $event['type'] ) {
			self::handle_checkout_completed( $event['data']['object'] );
		}

		return new WP_REST_Response( array( 'received' => true ), 200 );
	}

	/**
	 * Handle completed checkout session.
	 *
	 * @param array $session Stripe session data.
	 */
	private static function handle_checkout_completed( $session ) {
		$user_id = isset( $session['metadata']['user_id'] ) ? (int) $session['metadata']['user_id'] : 0;
		$tier    = isset( $session['metadata']['tier'] ) ? sanitize_text_field( $session['metadata']['tier'] ) : 'basic';

		if ( ! $user_id ) {
			return;
		}

		// Get user's profile.
		$profiles = get_posts(
			array(
				'post_type'   => 'lp_profile',
				'author'      => $user_id,
				'numberposts' => 1,
			)
		);

		if ( empty( $profiles ) ) {
			return;
		}

		$profile_id = $profiles[0]->ID;

		// Determine number of matches.
		$match_counts = array(
			'basic'    => (int) get_option( 'lp_ai_match_matches_basic', 1 ),
			'standard' => (int) get_option( 'lp_ai_match_matches_standard', 3 ),
			'premium'  => (int) get_option( 'lp_ai_match_matches_premium', 5 ),
		);
		$limit = isset( $match_counts[ $tier ] ) ? $match_counts[ $tier ] : 1;

		// Find matches.
		$matches = LP_Matching::find_matches( $profile_id, $limit );

		foreach ( $matches as $match_data ) {
			// Create match record.
			$match_id = wp_insert_post(
				array(
					'post_type'   => 'lp_match',
					'post_title'  => sprintf( 'Match: %d × %d', $profile_id, $match_data['profile_id'] ),
					'post_status' => 'publish',
					'post_author' => $user_id,
				)
			);

			if ( is_wp_error( $match_id ) ) {
				continue;
			}

			update_post_meta( $match_id, '_lp_profile_a', $profile_id );
			update_post_meta( $match_id, '_lp_profile_b', $match_data['profile_id'] );
			update_post_meta( $match_id, '_lp_tier', $tier );
			update_post_meta( $match_id, '_lp_stripe_session', $session['id'] );

			// Generate report.
			LP_Report::generate( $match_id, $tier );

			// Activate chat.
			LP_Chat::activate_chat( $match_id, $tier );
		}

		// Store payment record.
		update_user_meta(
			$user_id,
			'_lp_last_payment',
			array(
				'session_id' => $session['id'],
				'tier'       => $tier,
				'amount'     => $session['amount_total'],
				'paid_at'    => current_time( 'mysql' ),
			)
		);
	}

	/**
	 * Verify Stripe webhook signature.
	 *
	 * @param string $payload   Raw request body.
	 * @param string $sig_header Stripe-Signature header.
	 * @param string $secret    Webhook endpoint secret.
	 * @return bool Valid signature.
	 */
	private static function verify_webhook_signature( $payload, $sig_header, $secret ) {
		if ( empty( $sig_header ) ) {
			return false;
		}

		$parts = explode( ',', $sig_header );
		$timestamp = null;
		$signature = null;

		foreach ( $parts as $part ) {
			$kv = explode( '=', trim( $part ), 2 );
			if ( count( $kv ) !== 2 ) {
				continue;
			}
			if ( 't' === $kv[0] ) {
				$timestamp = $kv[1];
			} elseif ( 'v1' === $kv[0] ) {
				$signature = $kv[1];
			}
		}

		if ( ! $timestamp || ! $signature ) {
			return false;
		}

		// Tolerance: 5 minutes.
		if ( abs( time() - (int) $timestamp ) > 300 ) {
			return false;
		}

		$signed_payload = $timestamp . '.' . $payload;
		$expected       = hash_hmac( 'sha256', $signed_payload, $secret );

		return hash_equals( $expected, $signature );
	}
}
