<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="lp-tarot" id="lp-tarot">
	<div class="lp-tarot__header">
		<h2 class="lp-tarot__title"><?php esc_html_e( 'タロット恋愛占い', 'lp-ai-match' ); ?></h2>
		<p class="lp-tarot__subtitle"><?php esc_html_e( '3枚のカードがあなたの恋愛の過去・現在・未来を教えてくれます', 'lp-ai-match' ); ?></p>
	</div>

	<div class="lp-tarot__draw-area" id="lp-tarot-draw-area">
		<div class="lp-tarot__deck">
			<div class="lp-tarot__card-back" id="lp-tarot-card-1"></div>
			<div class="lp-tarot__card-back" id="lp-tarot-card-2"></div>
			<div class="lp-tarot__card-back" id="lp-tarot-card-3"></div>
		</div>
		<button type="button" id="lp-tarot-draw" class="lp-btn lp-btn--primary">
			<?php esc_html_e( 'カードを引く', 'lp-ai-match' ); ?>
		</button>
	</div>

	<div class="lp-tarot__result" id="lp-tarot-result" style="display:none">
		<div class="lp-tarot__cards">
			<!-- Cards will be populated by JS -->
		</div>

		<div class="lp-tarot__cta">
			<p class="lp-tarot__cta-text"><?php esc_html_e( 'カードが示す運命の相手を見つけませんか？', 'lp-ai-match' ); ?></p>
			<a href="#lp-pricing" class="lp-btn lp-btn--accent">
				<?php esc_html_e( 'AI運命マッチングを試す', 'lp-ai-match' ); ?>
			</a>
		</div>
	</div>
</div>
