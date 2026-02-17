<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="lp-horoscope" id="lp-horoscope">
	<div class="lp-horoscope__header">
		<h2 class="lp-horoscope__title"><?php esc_html_e( '今日の恋愛運', 'lp-ai-match' ); ?></h2>
		<p class="lp-horoscope__subtitle"><?php esc_html_e( '生年月日を入力して、今日の恋愛運をチェック', 'lp-ai-match' ); ?></p>
	</div>

	<div class="lp-horoscope__form">
		<div class="lp-form-group">
			<label for="lp-horoscope-date" class="lp-label">
				<?php esc_html_e( '生年月日', 'lp-ai-match' ); ?>
			</label>
			<input type="date" id="lp-horoscope-date" class="lp-input" required>
		</div>
		<button type="button" id="lp-horoscope-submit" class="lp-btn lp-btn--primary">
			<?php esc_html_e( '占う', 'lp-ai-match' ); ?>
		</button>
	</div>

	<div class="lp-horoscope__result" id="lp-horoscope-result" style="display:none">
		<div class="lp-horoscope__sign">
			<span class="lp-horoscope__symbol" id="lp-horoscope-symbol"></span>
			<span class="lp-horoscope__sign-name" id="lp-horoscope-sign"></span>
		</div>

		<div class="lp-horoscope__score-circle">
			<span class="lp-horoscope__score-number" id="lp-horoscope-score"></span>
			<span class="lp-horoscope__score-label"><?php esc_html_e( '恋愛運', 'lp-ai-match' ); ?></span>
		</div>

		<p class="lp-horoscope__fortune" id="lp-horoscope-fortune"></p>

		<div class="lp-horoscope__extras">
			<div class="lp-horoscope__extra">
				<span class="lp-horoscope__extra-label"><?php esc_html_e( 'ラッキーカラー', 'lp-ai-match' ); ?></span>
				<span class="lp-horoscope__extra-value" id="lp-horoscope-color"></span>
			</div>
			<div class="lp-horoscope__extra">
				<span class="lp-horoscope__extra-label"><?php esc_html_e( 'ラッキータイム', 'lp-ai-match' ); ?></span>
				<span class="lp-horoscope__extra-value" id="lp-horoscope-time"></span>
			</div>
		</div>

		<div class="lp-horoscope__cta">
			<p class="lp-horoscope__cta-text"><?php esc_html_e( '今日あなたと相性が高い人がいます', 'lp-ai-match' ); ?></p>
			<a href="#lp-pricing" class="lp-btn lp-btn--accent">
				<?php esc_html_e( '運命の相手を見つける（1,000円〜）', 'lp-ai-match' ); ?>
			</a>
		</div>
	</div>
</div>
