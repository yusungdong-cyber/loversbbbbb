<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="wrap lp-admin">
	<h1><?php esc_html_e( '星座運勢管理', 'lp-ai-match' ); ?></h1>
	<p><?php esc_html_e( '各星座の恋愛運メッセージを編集できます。複数行で入力すると日替わりで表示されます。', 'lp-ai-match' ); ?></p>

	<?php foreach ( $signs as $sign ) : ?>
	<div class="lp-admin__horoscope-card">
		<h3><?php echo esc_html( $sign['symbol'] . ' ' . $sign['name'] ); ?></h3>
		<form method="post">
			<?php wp_nonce_field( 'lp_save_horoscope', 'lp_horoscope_nonce' ); ?>
			<input type="hidden" name="sign" value="<?php echo esc_attr( $sign['en'] ); ?>">
			<?php
			$custom = get_option( 'lp_ai_match_horoscope_' . $sign['en'], array() );
			for ( $i = 0; $i < 3; $i++ ) :
				$value = isset( $custom[ $i ] ) ? $custom[ $i ] : '';
			?>
				<p>
					<label><?php printf( esc_html__( 'メッセージ %d:', 'lp-ai-match' ), $i + 1 ); ?></label><br>
					<textarea name="fortunes[]" rows="2" style="width:100%"><?php echo esc_textarea( $value ); ?></textarea>
				</p>
			<?php endfor; ?>
			<button type="submit" class="button button-primary"><?php esc_html_e( '保存', 'lp-ai-match' ); ?></button>
		</form>
	</div>
	<?php endforeach; ?>
</div>
