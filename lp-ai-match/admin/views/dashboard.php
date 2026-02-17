<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="wrap lp-admin">
	<h1><?php esc_html_e( 'LP AI Match ダッシュボード', 'lp-ai-match' ); ?></h1>

	<div class="lp-admin__stats">
		<div class="lp-admin__stat-card">
			<h3><?php esc_html_e( 'プロフィール数', 'lp-ai-match' ); ?></h3>
			<span class="lp-admin__stat-number"><?php echo esc_html( isset( $total_profiles->publish ) ? $total_profiles->publish : 0 ); ?></span>
		</div>
		<div class="lp-admin__stat-card">
			<h3><?php esc_html_e( 'マッチング数', 'lp-ai-match' ); ?></h3>
			<span class="lp-admin__stat-number"><?php echo esc_html( isset( $total_matches->publish ) ? $total_matches->publish : 0 ); ?></span>
		</div>
		<div class="lp-admin__stat-card">
			<h3><?php esc_html_e( 'レポート数', 'lp-ai-match' ); ?></h3>
			<span class="lp-admin__stat-number"><?php echo esc_html( isset( $total_reports->publish ) ? $total_reports->publish : 0 ); ?></span>
		</div>
	</div>

	<h2><?php esc_html_e( '最近のマッチング', 'lp-ai-match' ); ?></h2>
	<?php
	$recent = get_posts( array(
		'post_type'      => 'lp_match',
		'posts_per_page' => 10,
		'orderby'        => 'date',
		'order'          => 'DESC',
	) );

	if ( empty( $recent ) ) :
	?>
		<p><?php esc_html_e( 'まだマッチングがありません。', 'lp-ai-match' ); ?></p>
	<?php else : ?>
		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'ID', 'lp-ai-match' ); ?></th>
					<th><?php esc_html_e( 'プロフィールA', 'lp-ai-match' ); ?></th>
					<th><?php esc_html_e( 'プロフィールB', 'lp-ai-match' ); ?></th>
					<th><?php esc_html_e( 'プラン', 'lp-ai-match' ); ?></th>
					<th><?php esc_html_e( '日時', 'lp-ai-match' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $recent as $match ) : ?>
				<tr>
					<td><?php echo esc_html( $match->ID ); ?></td>
					<td><?php echo esc_html( get_the_title( get_post_meta( $match->ID, '_lp_profile_a', true ) ) ); ?></td>
					<td><?php echo esc_html( get_the_title( get_post_meta( $match->ID, '_lp_profile_b', true ) ) ); ?></td>
					<td><?php echo esc_html( get_post_meta( $match->ID, '_lp_tier', true ) ); ?></td>
					<td><?php echo esc_html( $match->post_date ); ?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>
</div>
