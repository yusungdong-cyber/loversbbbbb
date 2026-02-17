<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$user_id  = get_current_user_id();
$profiles = get_posts( array(
	'post_type'   => 'lp_profile',
	'author'      => $user_id,
	'numberposts' => 1,
) );
$has_profile = ! empty( $profiles );
?>
<div class="lp-matching" id="lp-matching">
	<h2 class="lp-matching__title"><?php esc_html_e( 'AI運命マッチング', 'lp-ai-match' ); ?></h2>

	<?php if ( ! $has_profile ) : ?>
		<div class="lp-notice lp-notice--warning">
			<p><?php esc_html_e( 'まずプロフィールを作成してください。', 'lp-ai-match' ); ?></p>
			<a href="/profile/" class="lp-btn lp-btn--primary"><?php esc_html_e( 'プロフィールを作成', 'lp-ai-match' ); ?></a>
		</div>
	<?php else : ?>
		<p class="lp-matching__desc"><?php esc_html_e( 'プランを選んで、運命の相手を見つけましょう。', 'lp-ai-match' ); ?></p>

		<!-- Show existing matches -->
		<?php
		$matches = get_posts( array(
			'post_type'   => 'lp_match',
			'author'      => $user_id,
			'numberposts' => 20,
			'orderby'     => 'date',
			'order'       => 'DESC',
		) );
		?>

		<?php if ( ! empty( $matches ) ) : ?>
		<h3><?php esc_html_e( 'あなたのマッチング', 'lp-ai-match' ); ?></h3>
		<div class="lp-matching__list">
			<?php foreach ( $matches as $match ) :
				$profile_b = get_post_meta( $match->ID, '_lp_profile_b', true );
				$tier      = get_post_meta( $match->ID, '_lp_tier', true );
				$chat_active = LP_Chat::is_chat_active( $match->ID );
				$report_posts = get_posts( array(
					'post_type'   => 'lp_report',
					'meta_key'    => '_lp_match_id',
					'meta_value'  => $match->ID,
					'numberposts' => 1,
				) );
			?>
			<div class="lp-matching__item">
				<div class="lp-matching__item-info">
					<span class="lp-matching__item-name"><?php echo esc_html( get_the_title( $profile_b ) ); ?></span>
					<span class="lp-matching__item-tier"><?php echo esc_html( ucfirst( $tier ) ); ?></span>
				</div>
				<div class="lp-matching__item-actions">
					<?php if ( ! empty( $report_posts ) ) : ?>
						<a href="/matching-result/?report_id=<?php echo esc_attr( $report_posts[0]->ID ); ?>" class="lp-btn lp-btn--small">
							<?php esc_html_e( 'レポート', 'lp-ai-match' ); ?>
						</a>
					<?php endif; ?>
					<?php if ( $chat_active ) : ?>
						<a href="/chat/?match_id=<?php echo esc_attr( $match->ID ); ?>" class="lp-btn lp-btn--small lp-btn--accent">
							<?php esc_html_e( 'チャット', 'lp-ai-match' ); ?>
						</a>
					<?php endif; ?>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>

	<?php endif; ?>
</div>
