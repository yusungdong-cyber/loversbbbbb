<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$report_id = isset( $_GET['report_id'] ) ? absint( $_GET['report_id'] ) : 0;
$user_id   = get_current_user_id();
?>
<div class="lp-result" id="lp-result">
	<?php if ( $report_id ) :
		$report = get_post( $report_id );
		if ( $report && 'lp_report' === $report->post_type && (int) $report->post_author === $user_id ) :
			$scores = get_post_meta( $report_id, '_lp_scores', true );
			$tier   = get_post_meta( $report_id, '_lp_tier', true );
			$match_id = get_post_meta( $report_id, '_lp_match_id', true );
	?>
		<h2 class="lp-result__title"><?php echo esc_html( $report->post_title ); ?></h2>

		<div class="lp-result__content">
			<?php echo wp_kses_post( $report->post_content ); ?>
		</div>

		<div class="lp-result__actions">
			<?php if ( 'premium' === $tier ) : ?>
			<a href="<?php echo esc_url( rest_url( 'lp-ai-match/v1/report/' . $report_id . '/pdf' ) ); ?>" class="lp-btn lp-btn--secondary" target="_blank">
				<?php esc_html_e( 'PDFをダウンロード', 'lp-ai-match' ); ?>
			</a>
			<?php endif; ?>

			<?php if ( $match_id && LP_Chat::is_chat_active( $match_id ) ) : ?>
			<a href="/chat/?match_id=<?php echo esc_attr( $match_id ); ?>" class="lp-btn lp-btn--accent">
				<?php esc_html_e( 'チャットを開始', 'lp-ai-match' ); ?>
			</a>
			<?php endif; ?>
		</div>

	<?php else : ?>
		<div class="lp-notice"><?php esc_html_e( 'レポートが見つからないか、アクセス権がありません。', 'lp-ai-match' ); ?></div>
	<?php endif; ?>

	<?php else : ?>
		<!-- Payment success landing -->
		<div class="lp-result__success" id="lp-result-success">
			<h2><?php esc_html_e( 'お支払い完了！', 'lp-ai-match' ); ?></h2>
			<p><?php esc_html_e( 'マッチングとレポートの生成が完了しました。', 'lp-ai-match' ); ?></p>
			<a href="/matching/" class="lp-btn lp-btn--accent"><?php esc_html_e( 'マッチング結果を見る', 'lp-ai-match' ); ?></a>
		</div>
	<?php endif; ?>
</div>
