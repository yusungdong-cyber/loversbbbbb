<?php
/**
 * Plugin deactivation handler.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LP_Deactivator {

	/**
	 * Run on plugin deactivation.
	 */
	public static function deactivate() {
		// Clear scheduled events.
		wp_clear_scheduled_hook( 'lp_chat_expiry_check' );
		flush_rewrite_rules();
	}
}
