<?php
/**
 * LoversPick — Admin Settings Page
 *
 * Tabbed interface: General / Pricing / Analytics.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'general';
$tabs = array(
    'general'   => __( 'General', 'loverspick' ),
    'pricing'   => __( 'Pricing', 'loverspick' ),
    'analytics' => __( 'Analytics', 'loverspick' ),
);

$settings_groups = array(
    'general'   => 'lpc_settings_general',
    'pricing'   => 'lpc_settings_pricing',
    'analytics' => 'lpc_settings_analytics',
);
?>

<div class="wrap lpc-admin">
    <h1><?php esc_html_e( 'LoversPick Settings', 'loverspick' ); ?></h1>

    <div class="lpc-admin__tabs">
        <?php foreach ( $tabs as $tab_key => $tab_label ) : ?>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=loverspick&tab=' . $tab_key ) ); ?>"
               class="lpc-admin__tab <?php echo $active_tab === $tab_key ? 'lpc-admin__tab--active' : ''; ?>">
                <?php echo esc_html( $tab_label ); ?>
            </a>
        <?php endforeach; ?>
    </div>

    <form method="post" action="options.php">
        <?php settings_fields( $settings_groups[ $active_tab ] ); ?>

        <?php if ( 'general' === $active_tab ) : ?>

            <div class="lpc-admin__section">
                <h2><?php esc_html_e( 'Messenger Links', 'loverspick' ); ?></h2>
                <p class="description"><?php esc_html_e( 'These links are used in CTA buttons throughout the site.', 'loverspick' ); ?></p>
                <table class="form-table">
                    <tr>
                        <th><label for="lpc_telegram_link"><?php esc_html_e( 'Telegram Link', 'loverspick' ); ?></label></th>
                        <td>
                            <input type="url" id="lpc_telegram_link" name="lpc_telegram_link"
                                   value="<?php echo esc_url( get_option( 'lpc_telegram_link' ) ); ?>"
                                   class="regular-text" placeholder="https://t.me/yourbotname" />
                            <p class="lpc-help"><?php esc_html_e( 'Used for "Start on Telegram" buttons and floating mobile CTA.', 'loverspick' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="lpc_whatsapp_link"><?php esc_html_e( 'WhatsApp Link', 'loverspick' ); ?></label></th>
                        <td>
                            <input type="url" id="lpc_whatsapp_link" name="lpc_whatsapp_link"
                                   value="<?php echo esc_url( get_option( 'lpc_whatsapp_link' ) ); ?>"
                                   class="regular-text" placeholder="https://wa.me/yourphonenumber" />
                        </td>
                    </tr>
                </table>
            </div>

            <div class="lpc-admin__section">
                <h2><?php esc_html_e( 'Notifications', 'loverspick' ); ?></h2>
                <table class="form-table">
                    <tr>
                        <th><label for="lpc_admin_email"><?php esc_html_e( 'Notification Email', 'loverspick' ); ?></label></th>
                        <td>
                            <input type="email" id="lpc_admin_email" name="lpc_admin_email"
                                   value="<?php echo esc_attr( get_option( 'lpc_admin_email', get_option( 'admin_email' ) ) ); ?>"
                                   class="regular-text" />
                            <p class="lpc-help"><?php esc_html_e( 'Receives email alerts when leads or partner applications are submitted.', 'loverspick' ); ?></p>
                        </td>
                    </tr>
                </table>
            </div>

        <?php elseif ( 'pricing' === $active_tab ) : ?>

            <div class="lpc-admin__section">
                <h2><?php esc_html_e( 'Pass Prices', 'loverspick' ); ?></h2>
                <p class="description"><?php esc_html_e( 'These values appear on pricing cards throughout the site.', 'loverspick' ); ?></p>
                <table class="form-table">
                    <tr>
                        <th><label for="lpc_currency"><?php esc_html_e( 'Currency Symbol', 'loverspick' ); ?></label></th>
                        <td>
                            <input type="text" id="lpc_currency" name="lpc_currency"
                                   value="<?php echo esc_attr( get_option( 'lpc_currency', '$' ) ); ?>"
                                   class="small-text" />
                        </td>
                    </tr>
                    <tr>
                        <th><label for="lpc_price_3day"><?php esc_html_e( '3-Day Pass Price', 'loverspick' ); ?></label></th>
                        <td><input type="text" id="lpc_price_3day" name="lpc_price_3day" value="<?php echo esc_attr( get_option( 'lpc_price_3day', '29' ) ); ?>" class="small-text" /></td>
                    </tr>
                    <tr>
                        <th><label for="lpc_price_5day"><?php esc_html_e( '5-Day Pass Price', 'loverspick' ); ?></label></th>
                        <td><input type="text" id="lpc_price_5day" name="lpc_price_5day" value="<?php echo esc_attr( get_option( 'lpc_price_5day', '39' ) ); ?>" class="small-text" /></td>
                    </tr>
                    <tr>
                        <th><label for="lpc_price_7day"><?php esc_html_e( '7-Day Pass Price', 'loverspick' ); ?></label></th>
                        <td><input type="text" id="lpc_price_7day" name="lpc_price_7day" value="<?php echo esc_attr( get_option( 'lpc_price_7day', '49' ) ); ?>" class="small-text" /></td>
                    </tr>
                    <tr>
                        <th><label for="lpc_price_premium"><?php esc_html_e( 'Premium Add-on Price', 'loverspick' ); ?></label></th>
                        <td><input type="text" id="lpc_price_premium" name="lpc_price_premium" value="<?php echo esc_attr( get_option( 'lpc_price_premium', '15' ) ); ?>" class="small-text" /></td>
                    </tr>
                </table>
            </div>

            <div class="lpc-admin__section">
                <h2><?php esc_html_e( 'Checkout Links', 'loverspick' ); ?></h2>
                <p class="description"><?php esc_html_e( 'External checkout URLs (Stripe, Paddle, LemonSqueezy). Leave blank to fall back to lead capture form.', 'loverspick' ); ?></p>
                <table class="form-table">
                    <tr>
                        <th><label for="lpc_checkout_3day"><?php esc_html_e( '3-Day Checkout URL', 'loverspick' ); ?></label></th>
                        <td><input type="url" id="lpc_checkout_3day" name="lpc_checkout_3day" value="<?php echo esc_url( get_option( 'lpc_checkout_3day' ) ); ?>" class="regular-text" placeholder="https://buy.stripe.com/..." /></td>
                    </tr>
                    <tr>
                        <th><label for="lpc_checkout_5day"><?php esc_html_e( '5-Day Checkout URL', 'loverspick' ); ?></label></th>
                        <td><input type="url" id="lpc_checkout_5day" name="lpc_checkout_5day" value="<?php echo esc_url( get_option( 'lpc_checkout_5day' ) ); ?>" class="regular-text" placeholder="https://buy.stripe.com/..." /></td>
                    </tr>
                    <tr>
                        <th><label for="lpc_checkout_7day"><?php esc_html_e( '7-Day Checkout URL', 'loverspick' ); ?></label></th>
                        <td><input type="url" id="lpc_checkout_7day" name="lpc_checkout_7day" value="<?php echo esc_url( get_option( 'lpc_checkout_7day' ) ); ?>" class="regular-text" placeholder="https://buy.stripe.com/..." /></td>
                    </tr>
                </table>
            </div>

        <?php elseif ( 'analytics' === $active_tab ) : ?>

            <div class="lpc-admin__section">
                <h2><?php esc_html_e( 'Google Analytics 4', 'loverspick' ); ?></h2>
                <table class="form-table">
                    <tr>
                        <th><label for="lpc_ga4_id"><?php esc_html_e( 'Measurement ID', 'loverspick' ); ?></label></th>
                        <td>
                            <input type="text" id="lpc_ga4_id" name="lpc_ga4_id"
                                   value="<?php echo esc_attr( get_option( 'lpc_ga4_id' ) ); ?>"
                                   class="regular-text" placeholder="G-XXXXXXXXXX" />
                            <p class="lpc-help"><?php esc_html_e( 'Find this in GA4 > Admin > Data Streams > your stream.', 'loverspick' ); ?></p>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="lpc-admin__section">
                <h2><?php esc_html_e( 'Meta (Facebook) Pixel', 'loverspick' ); ?></h2>
                <table class="form-table">
                    <tr>
                        <th><label for="lpc_meta_pixel_id"><?php esc_html_e( 'Pixel ID', 'loverspick' ); ?></label></th>
                        <td>
                            <input type="text" id="lpc_meta_pixel_id" name="lpc_meta_pixel_id"
                                   value="<?php echo esc_attr( get_option( 'lpc_meta_pixel_id' ) ); ?>"
                                   class="regular-text" placeholder="123456789012345" />
                            <p class="lpc-help"><?php esc_html_e( 'Find this in Meta Events Manager > your Pixel > Settings.', 'loverspick' ); ?></p>
                        </td>
                    </tr>
                </table>
            </div>

        <?php endif; ?>

        <?php submit_button(); ?>
    </form>
</div>
