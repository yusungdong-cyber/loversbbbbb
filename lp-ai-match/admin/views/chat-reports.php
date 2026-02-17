<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="wrap lp-admin">
	<h1><?php esc_html_e( '通報管理', 'lp-ai-match' ); ?></h1>

	<?php if ( empty( $reports ) ) : ?>
		<p><?php esc_html_e( '通報はありません。', 'lp-ai-match' ); ?></p>
	<?php else : ?>
		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'メッセージID', 'lp-ai-match' ); ?></th>
					<th><?php esc_html_e( '通報者', 'lp-ai-match' ); ?></th>
					<th><?php esc_html_e( '理由', 'lp-ai-match' ); ?></th>
					<th><?php esc_html_e( '日時', 'lp-ai-match' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( array_reverse( $reports ) as $report ) :
					$reporter = get_user_by( 'id', $report['reporter_id'] );
				?>
				<tr>
					<td><?php echo esc_html( $report['message_id'] ); ?></td>
					<td><?php echo esc_html( $reporter ? $reporter->display_name : 'ID:' . $report['reporter_id'] ); ?></td>
					<td><?php echo esc_html( $report['reason'] ); ?></td>
					<td><?php echo esc_html( $report['reported_at'] ); ?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>
</div>
