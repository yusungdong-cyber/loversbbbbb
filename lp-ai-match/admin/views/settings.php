<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="wrap lp-admin">
	<h1><?php esc_html_e( 'LP AI Match 設定', 'lp-ai-match' ); ?></h1>

	<form method="post" action="options.php">
		<?php settings_fields( 'lp_ai_match_settings' ); ?>

		<!-- Pricing -->
		<h2><?php esc_html_e( '料金設定', 'lp-ai-match' ); ?></h2>
		<table class="form-table">
			<tr>
				<th><?php esc_html_e( '通貨', 'lp-ai-match' ); ?></th>
				<td>
					<select name="lp_ai_match_currency">
						<option value="jpy" <?php selected( get_option( 'lp_ai_match_currency' ), 'jpy' ); ?>>JPY (日本円)</option>
						<option value="usd" <?php selected( get_option( 'lp_ai_match_currency' ), 'usd' ); ?>>USD</option>
					</select>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'ベーシック価格', 'lp-ai-match' ); ?></th>
				<td><input type="number" name="lp_ai_match_price_basic" value="<?php echo esc_attr( get_option( 'lp_ai_match_price_basic', 1000 ) ); ?>" min="0" /> <span class="description">¥</span></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'スタンダード価格', 'lp-ai-match' ); ?></th>
				<td><input type="number" name="lp_ai_match_price_standard" value="<?php echo esc_attr( get_option( 'lp_ai_match_price_standard', 3000 ) ); ?>" min="0" /> <span class="description">¥</span></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'プレミアム価格', 'lp-ai-match' ); ?></th>
				<td><input type="number" name="lp_ai_match_price_premium" value="<?php echo esc_attr( get_option( 'lp_ai_match_price_premium', 6900 ) ); ?>" min="0" /> <span class="description">¥</span></td>
			</tr>
		</table>

		<!-- Matching Weights -->
		<h2><?php esc_html_e( 'マッチング加重値', 'lp-ai-match' ); ?></h2>
		<table class="form-table">
			<tr>
				<th><?php esc_html_e( '四柱相性 (%)', 'lp-ai-match' ); ?></th>
				<td><input type="number" name="lp_ai_match_saju_weight" value="<?php echo esc_attr( get_option( 'lp_ai_match_saju_weight', 40 ) ); ?>" min="0" max="100" /></td>
			</tr>
			<tr>
				<th><?php esc_html_e( '性格マッチ (%)', 'lp-ai-match' ); ?></th>
				<td><input type="number" name="lp_ai_match_personality_weight" value="<?php echo esc_attr( get_option( 'lp_ai_match_personality_weight', 40 ) ); ?>" min="0" max="100" /></td>
			</tr>
			<tr>
				<th><?php esc_html_e( '価値観適合 (%)', 'lp-ai-match' ); ?></th>
				<td><input type="number" name="lp_ai_match_values_weight" value="<?php echo esc_attr( get_option( 'lp_ai_match_values_weight', 20 ) ); ?>" min="0" max="100" /></td>
			</tr>
		</table>

		<!-- Match Counts -->
		<h2><?php esc_html_e( 'マッチング人数', 'lp-ai-match' ); ?></h2>
		<table class="form-table">
			<tr>
				<th><?php esc_html_e( 'ベーシック (人)', 'lp-ai-match' ); ?></th>
				<td><input type="number" name="lp_ai_match_matches_basic" value="<?php echo esc_attr( get_option( 'lp_ai_match_matches_basic', 1 ) ); ?>" min="1" /></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'スタンダード (人)', 'lp-ai-match' ); ?></th>
				<td><input type="number" name="lp_ai_match_matches_standard" value="<?php echo esc_attr( get_option( 'lp_ai_match_matches_standard', 3 ) ); ?>" min="1" /></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'プレミアム (人)', 'lp-ai-match' ); ?></th>
				<td><input type="number" name="lp_ai_match_matches_premium" value="<?php echo esc_attr( get_option( 'lp_ai_match_matches_premium', 5 ) ); ?>" min="1" /></td>
			</tr>
		</table>

		<!-- Chat Duration -->
		<h2><?php esc_html_e( 'チャット期間', 'lp-ai-match' ); ?></h2>
		<table class="form-table">
			<tr>
				<th><?php esc_html_e( 'ベーシック (時間)', 'lp-ai-match' ); ?></th>
				<td><input type="number" name="lp_ai_match_chat_basic_hours" value="<?php echo esc_attr( get_option( 'lp_ai_match_chat_basic_hours', 24 ) ); ?>" min="1" /></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'スタンダード (日)', 'lp-ai-match' ); ?></th>
				<td><input type="number" name="lp_ai_match_chat_standard_days" value="<?php echo esc_attr( get_option( 'lp_ai_match_chat_standard_days', 7 ) ); ?>" min="1" /></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'プレミアム (日)', 'lp-ai-match' ); ?></th>
				<td><input type="number" name="lp_ai_match_chat_premium_days" value="<?php echo esc_attr( get_option( 'lp_ai_match_chat_premium_days', 7 ) ); ?>" min="1" /></td>
			</tr>
		</table>

		<!-- Stripe -->
		<h2><?php esc_html_e( 'Stripe決済設定', 'lp-ai-match' ); ?></h2>
		<table class="form-table">
			<tr>
				<th><?php esc_html_e( 'モード', 'lp-ai-match' ); ?></th>
				<td>
					<select name="lp_ai_match_stripe_mode">
						<option value="test" <?php selected( get_option( 'lp_ai_match_stripe_mode' ), 'test' ); ?>>テスト</option>
						<option value="live" <?php selected( get_option( 'lp_ai_match_stripe_mode' ), 'live' ); ?>>本番</option>
					</select>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'テスト公開キー', 'lp-ai-match' ); ?></th>
				<td><input type="text" name="lp_ai_match_stripe_test_pk" value="<?php echo esc_attr( get_option( 'lp_ai_match_stripe_test_pk' ) ); ?>" class="regular-text" /></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'テストシークレットキー', 'lp-ai-match' ); ?></th>
				<td><input type="password" name="lp_ai_match_stripe_test_sk" value="<?php echo esc_attr( get_option( 'lp_ai_match_stripe_test_sk' ) ); ?>" class="regular-text" /></td>
			</tr>
			<tr>
				<th><?php esc_html_e( '本番公開キー', 'lp-ai-match' ); ?></th>
				<td><input type="text" name="lp_ai_match_stripe_live_pk" value="<?php echo esc_attr( get_option( 'lp_ai_match_stripe_live_pk' ) ); ?>" class="regular-text" /></td>
			</tr>
			<tr>
				<th><?php esc_html_e( '本番シークレットキー', 'lp-ai-match' ); ?></th>
				<td><input type="password" name="lp_ai_match_stripe_live_sk" value="<?php echo esc_attr( get_option( 'lp_ai_match_stripe_live_sk' ) ); ?>" class="regular-text" /></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Webhookシークレット', 'lp-ai-match' ); ?></th>
				<td><input type="password" name="lp_ai_match_stripe_webhook_secret" value="<?php echo esc_attr( get_option( 'lp_ai_match_stripe_webhook_secret' ) ); ?>" class="regular-text" /></td>
			</tr>
		</table>

		<!-- OpenAI -->
		<h2><?php esc_html_e( 'AI設定', 'lp-ai-match' ); ?></h2>
		<table class="form-table">
			<tr>
				<th><?php esc_html_e( 'OpenAI APIキー', 'lp-ai-match' ); ?></th>
				<td><input type="password" name="lp_ai_match_openai_api_key" value="<?php echo esc_attr( get_option( 'lp_ai_match_openai_api_key' ) ); ?>" class="regular-text" /></td>
			</tr>
		</table>

		<?php submit_button( __( '設定を保存', 'lp-ai-match' ) ); ?>
	</form>
</div>
