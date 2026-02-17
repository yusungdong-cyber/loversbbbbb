<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$match_id = isset( $_GET['match_id'] ) ? absint( $_GET['match_id'] ) : 0;
$user_id  = get_current_user_id();
?>
<div class="lp-chat" id="lp-chat" data-match-id="<?php echo esc_attr( $match_id ); ?>" data-user-id="<?php echo esc_attr( $user_id ); ?>">
	<?php if ( ! $match_id ) : ?>
		<div class="lp-notice"><?php esc_html_e( 'チャット対象が指定されていません。', 'lp-ai-match' ); ?></div>
	<?php else :
		$match = get_post( $match_id );
		$profile_a = get_post_meta( $match_id, '_lp_profile_a', true );
		$profile_b = get_post_meta( $match_id, '_lp_profile_b', true );
		$user_a    = get_post_field( 'post_author', $profile_a );
		$user_b    = get_post_field( 'post_author', $profile_b );

		// Determine partner name.
		$partner_profile = ( (int) $user_id === (int) $user_a ) ? $profile_b : $profile_a;
		$partner_name    = get_the_title( $partner_profile );
		$chat_active     = LP_Chat::is_chat_active( $match_id );
	?>
		<div class="lp-chat__header">
			<h2 class="lp-chat__title">
				<?php echo esc_html( $partner_name ); ?>
				<?php esc_html_e( 'さんとのチャット', 'lp-ai-match' ); ?>
			</h2>
			<div class="lp-chat__actions">
				<button type="button" class="lp-btn lp-btn--small lp-btn--secondary" id="lp-chat-consent">
					<?php esc_html_e( '連絡先を共有', 'lp-ai-match' ); ?>
				</button>
				<button type="button" class="lp-btn lp-btn--small lp-btn--danger" id="lp-chat-report-btn">
					<?php esc_html_e( '通報', 'lp-ai-match' ); ?>
				</button>
			</div>
		</div>

		<?php if ( ! $chat_active ) : ?>
			<div class="lp-notice"><?php esc_html_e( 'チャットの有効期限が切れました。', 'lp-ai-match' ); ?></div>
		<?php else : ?>
			<div class="lp-chat__messages" id="lp-chat-messages">
				<!-- Messages loaded via AJAX -->
				<div class="lp-chat__loading"><?php esc_html_e( '読み込み中...', 'lp-ai-match' ); ?></div>
			</div>

			<div class="lp-chat__input-area">
				<textarea id="lp-chat-input" class="lp-chat__input" placeholder="<?php esc_attr_e( 'メッセージを入力...', 'lp-ai-match' ); ?>" rows="2"></textarea>
				<button type="button" id="lp-chat-send" class="lp-btn lp-btn--accent">
					<?php esc_html_e( '送信', 'lp-ai-match' ); ?>
				</button>
			</div>
		<?php endif; ?>

		<!-- Report modal -->
		<div class="lp-modal" id="lp-report-modal" style="display:none">
			<div class="lp-modal__content">
				<h3><?php esc_html_e( 'メッセージを通報', 'lp-ai-match' ); ?></h3>
				<textarea id="lp-report-reason" class="lp-input" placeholder="<?php esc_attr_e( '通報理由を入力してください', 'lp-ai-match' ); ?>" rows="3"></textarea>
				<div class="lp-modal__actions">
					<button type="button" class="lp-btn lp-btn--secondary" id="lp-report-cancel"><?php esc_html_e( 'キャンセル', 'lp-ai-match' ); ?></button>
					<button type="button" class="lp-btn lp-btn--danger" id="lp-report-submit"><?php esc_html_e( '通報する', 'lp-ai-match' ); ?></button>
				</div>
			</div>
		</div>
	<?php endif; ?>
</div>
