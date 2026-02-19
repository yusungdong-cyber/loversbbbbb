<?php
/**
 * LoversPick — Admin Leads Viewer
 *
 * Displays captured leads from the lpc_leads table.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $wpdb;

$table = $wpdb->prefix . 'lpc_leads';
$page_num = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
$per_page = 50;
$offset = ( $page_num - 1 ) * $per_page;

$total = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $table" );
$leads = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM $table ORDER BY created_at DESC LIMIT %d OFFSET %d",
        $per_page,
        $offset
    )
);

$total_pages = ceil( $total / $per_page );
?>

<div class="wrap lpc-admin">
    <h1><?php esc_html_e( 'Leads', 'loverspick' ); ?></h1>

    <div class="lpc-admin__stats">
        <div class="lpc-admin__stat">
            <div class="lpc-admin__stat-number"><?php echo esc_html( $total ); ?></div>
            <div class="lpc-admin__stat-label"><?php esc_html_e( 'Total Leads', 'loverspick' ); ?></div>
        </div>
        <?php
        $today_count = (int) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM $table WHERE DATE(created_at) = %s",
                current_time( 'Y-m-d' )
            )
        );
        ?>
        <div class="lpc-admin__stat">
            <div class="lpc-admin__stat-number"><?php echo esc_html( $today_count ); ?></div>
            <div class="lpc-admin__stat-label"><?php esc_html_e( 'Today', 'loverspick' ); ?></div>
        </div>
    </div>

    <?php if ( $leads ) : ?>
    <table class="lpc-admin__table">
        <thead>
            <tr>
                <th><?php esc_html_e( 'Date', 'loverspick' ); ?></th>
                <th><?php esc_html_e( 'Email', 'loverspick' ); ?></th>
                <th><?php esc_html_e( 'Name', 'loverspick' ); ?></th>
                <th><?php esc_html_e( 'Country', 'loverspick' ); ?></th>
                <th><?php esc_html_e( 'Messenger', 'loverspick' ); ?></th>
                <th><?php esc_html_e( 'Travel Dates', 'loverspick' ); ?></th>
                <th><?php esc_html_e( 'UTM Source', 'loverspick' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ( $leads as $lead ) : ?>
            <tr>
                <td><?php echo esc_html( wp_date( 'M j, Y H:i', strtotime( $lead->created_at ) ) ); ?></td>
                <td><strong><?php echo esc_html( $lead->email ); ?></strong></td>
                <td><?php echo esc_html( $lead->name ?: '—' ); ?></td>
                <td><?php echo esc_html( $lead->country ?: '—' ); ?></td>
                <td>
                    <?php if ( $lead->messenger_handle ) : ?>
                        <?php echo esc_html( $lead->messenger_type . ': ' . $lead->messenger_handle ); ?>
                    <?php else : ?>
                        —
                    <?php endif; ?>
                </td>
                <td><?php echo esc_html( $lead->travel_dates ?: '—' ); ?></td>
                <td><?php echo esc_html( $lead->utm_source ?: '—' ); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if ( $total_pages > 1 ) : ?>
        <div class="tablenav bottom">
            <div class="tablenav-pages">
                <?php
                echo paginate_links( array(
                    'base'    => add_query_arg( 'paged', '%#%' ),
                    'format'  => '',
                    'current' => $page_num,
                    'total'   => $total_pages,
                ) );
                ?>
            </div>
        </div>
    <?php endif; ?>

    <?php else : ?>
        <p><?php esc_html_e( 'No leads captured yet. They will appear here when visitors submit the lead form.', 'loverspick' ); ?></p>
    <?php endif; ?>
</div>
