<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="wrap lp-admin">
	<h1><?php esc_html_e( 'マッチングログ', 'lp-ai-match' ); ?></h1>

	<?php if ( empty( $matches ) ) : ?>
		<p><?php esc_html_e( 'まだマッチングがありません。', 'lp-ai-match' ); ?></p>
	<?php else : ?>
		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'ID', 'lp-ai-match' ); ?></th>
					<th><?php esc_html_e( 'ユーザーA', 'lp-ai-match' ); ?></th>
					<th><?php esc_html_e( 'ユーザーB', 'lp-ai-match' ); ?></th>
					<th><?php esc_html_e( 'プラン', 'lp-ai-match' ); ?></th>
					<th><?php esc_html_e( 'チャット状態', 'lp-ai-match' ); ?></th>
					<th><?php esc_html_e( '作成日', 'lp-ai-match' ); ?></th>
					<th><?php esc_html_e( '操作', 'lp-ai-match' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $matches as $match ) :
					$profile_a = get_post_meta( $match->ID, '_lp_profile_a', true );
					$profile_b = get_post_meta( $match->ID, '_lp_profile_b', true );
					$tier      = get_post_meta( $match->ID, '_lp_tier', true );
					$chat_status = get_post_meta( $match->ID, '_lp_chat_status', true );

					// Find report.
					$report_posts = get_posts( array(
						'post_type'   => 'lp_report',
						'meta_key'    => '_lp_match_id',
						'meta_value'  => $match->ID,
						'numberposts' => 1,
					) );
					$report_id = ! empty( $report_posts ) ? $report_posts[0]->ID : 0;
				?>
				<tr>
					<td><?php echo esc_html( $match->ID ); ?></td>
					<td><?php echo esc_html( get_the_title( $profile_a ) ); ?></td>
					<td><?php echo esc_html( get_the_title( $profile_b ) ); ?></td>
					<td><?php echo esc_html( $tier ); ?></td>
					<td><?php echo esc_html( $chat_status ? $chat_status : '-' ); ?></td>
					<td><?php echo esc_html( $match->post_date ); ?></td>
					<td>
						<?php if ( $report_id ) : ?>
							<a href="<?php echo esc_url( admin_url( 'post.php?post=' . $report_id . '&action=edit' ) ); ?>" class="button button-small">
								<?php esc_html_e( 'レポート閲覧', 'lp-ai-match' ); ?>
							</a>
						<?php endif; ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>
</div>
