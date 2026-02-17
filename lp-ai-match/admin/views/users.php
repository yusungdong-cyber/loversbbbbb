<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="wrap lp-admin">
	<h1><?php esc_html_e( 'ユーザー管理', 'lp-ai-match' ); ?></h1>

	<?php if ( empty( $profiles ) ) : ?>
		<p><?php esc_html_e( 'まだプロフィールがありません。', 'lp-ai-match' ); ?></p>
	<?php else : ?>
		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'ニックネーム', 'lp-ai-match' ); ?></th>
					<th><?php esc_html_e( 'WPユーザー', 'lp-ai-match' ); ?></th>
					<th><?php esc_html_e( '性別', 'lp-ai-match' ); ?></th>
					<th><?php esc_html_e( '生年月日', 'lp-ai-match' ); ?></th>
					<th><?php esc_html_e( 'ステータス', 'lp-ai-match' ); ?></th>
					<th><?php esc_html_e( '操作', 'lp-ai-match' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $profiles as $profile ) :
					$user_id    = $profile->post_author;
					$user       = get_user_by( 'id', $user_id );
					$gender     = get_post_meta( $profile->ID, '_lp_gender', true );
					$birthdate  = get_post_meta( $profile->ID, '_lp_birthdate', true );
					$is_blocked = LP_Chat::is_user_blocked( $user_id );
				?>
				<tr>
					<td><?php echo esc_html( $profile->post_title ); ?></td>
					<td><?php echo esc_html( $user ? $user->user_email : 'N/A' ); ?></td>
					<td><?php echo esc_html( 'male' === $gender ? '男性' : '女性' ); ?></td>
					<td><?php echo esc_html( $birthdate ); ?></td>
					<td>
						<?php if ( $is_blocked ) : ?>
							<span style="color:red;font-weight:bold"><?php esc_html_e( 'ブロック中', 'lp-ai-match' ); ?></span>
						<?php else : ?>
							<span style="color:green"><?php esc_html_e( 'アクティブ', 'lp-ai-match' ); ?></span>
						<?php endif; ?>
					</td>
					<td>
						<form method="post" style="display:inline">
							<?php wp_nonce_field( 'lp_user_action', 'lp_user_action_nonce' ); ?>
							<input type="hidden" name="user_id" value="<?php echo esc_attr( $user_id ); ?>">
							<?php if ( $is_blocked ) : ?>
								<input type="hidden" name="action_type" value="unblock">
								<button type="submit" class="button button-small"><?php esc_html_e( 'ブロック解除', 'lp-ai-match' ); ?></button>
							<?php else : ?>
								<input type="hidden" name="action_type" value="block">
								<button type="submit" class="button button-small" style="color:red"><?php esc_html_e( 'ブロック', 'lp-ai-match' ); ?></button>
							<?php endif; ?>
						</form>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>
</div>
