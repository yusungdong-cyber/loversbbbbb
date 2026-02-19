<?php
/**
 * LoversPick — Admin Partner Applications Viewer
 *
 * Displays partner applications from the lpc_partner_apps table.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $wpdb;

$table = $wpdb->prefix . 'lpc_partner_apps';
$total = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $table" );
$apps = $wpdb->get_results( "SELECT * FROM $table ORDER BY created_at DESC LIMIT 100" );
?>

<div class="wrap lpc-admin">
    <h1><?php esc_html_e( 'Partner Applications', 'loverspick' ); ?></h1>

    <div class="lpc-admin__stats">
        <div class="lpc-admin__stat">
            <div class="lpc-admin__stat-number"><?php echo esc_html( $total ); ?></div>
            <div class="lpc-admin__stat-label"><?php esc_html_e( 'Total Applications', 'loverspick' ); ?></div>
        </div>
    </div>

    <?php if ( $apps ) : ?>
    <table class="lpc-admin__table">
        <thead>
            <tr>
                <th><?php esc_html_e( 'Date', 'loverspick' ); ?></th>
                <th><?php esc_html_e( 'Business', 'loverspick' ); ?></th>
                <th><?php esc_html_e( 'Contact', 'loverspick' ); ?></th>
                <th><?php esc_html_e( 'Category', 'loverspick' ); ?></th>
                <th><?php esc_html_e( 'Area', 'loverspick' ); ?></th>
                <th><?php esc_html_e( 'URL', 'loverspick' ); ?></th>
                <th><?php esc_html_e( 'Message', 'loverspick' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ( $apps as $app ) : ?>
            <tr>
                <td><?php echo esc_html( wp_date( 'M j, Y', strtotime( $app->created_at ) ) ); ?></td>
                <td>
                    <strong><?php echo esc_html( $app->business_name ); ?></strong>
                    <?php if ( $app->contact_name ) : ?>
                        <br><small><?php echo esc_html( $app->contact_name ); ?></small>
                    <?php endif; ?>
                </td>
                <td><?php echo esc_html( $app->contact_info ); ?></td>
                <td><?php echo esc_html( ucfirst( $app->category ) ?: '—' ); ?></td>
                <td><?php echo esc_html( ucfirst( $app->area ) ?: '—' ); ?></td>
                <td>
                    <?php if ( $app->business_url ) : ?>
                        <a href="<?php echo esc_url( $app->business_url ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Visit', 'loverspick' ); ?></a>
                    <?php else : ?>
                        —
                    <?php endif; ?>
                </td>
                <td><?php echo esc_html( $app->message ? wp_trim_words( $app->message, 15 ) : '—' ); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else : ?>
        <p><?php esc_html_e( 'No partner applications yet.', 'loverspick' ); ?></p>
    <?php endif; ?>
</div>
