<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="wrap lp-admin">
	<h1><?php esc_html_e( 'タロットカード管理', 'lp-ai-match' ); ?></h1>
	<p><?php esc_html_e( 'カードの解釈テキストを編集できます。', 'lp-ai-match' ); ?></p>

	<table class="wp-list-table widefat fixed striped">
		<thead>
			<tr>
				<th style="width:60px"><?php esc_html_e( 'ID', 'lp-ai-match' ); ?></th>
				<th style="width:120px"><?php esc_html_e( 'カード名', 'lp-ai-match' ); ?></th>
				<th><?php esc_html_e( '正位置の解釈', 'lp-ai-match' ); ?></th>
				<th><?php esc_html_e( '逆位置の解釈', 'lp-ai-match' ); ?></th>
				<th style="width:80px"><?php esc_html_e( '操作', 'lp-ai-match' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $cards as $id => $card ) : ?>
			<tr id="card-row-<?php echo esc_attr( $id ); ?>">
				<td><?php echo esc_html( $id ); ?></td>
				<td>
					<strong><?php echo esc_html( $card['name'] ); ?></strong><br>
					<small><?php echo esc_html( $card['name_en'] ); ?></small>
				</td>
				<td>
					<form method="post" style="display:inline">
						<?php wp_nonce_field( 'lp_save_tarot', 'lp_tarot_nonce' ); ?>
						<input type="hidden" name="card_id" value="<?php echo esc_attr( $id ); ?>">
						<textarea name="upright" rows="3" style="width:100%"><?php echo esc_textarea( $card['upright'] ); ?></textarea>
				</td>
				<td>
						<textarea name="reverse" rows="3" style="width:100%"><?php echo esc_textarea( $card['reverse'] ); ?></textarea>
				</td>
				<td>
						<button type="submit" class="button button-primary button-small"><?php esc_html_e( '保存', 'lp-ai-match' ); ?></button>
					</form>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
