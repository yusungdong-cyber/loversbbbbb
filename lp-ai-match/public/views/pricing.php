<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$price_basic    = number_format( (int) get_option( 'lp_ai_match_price_basic', 1000 ) );
$price_standard = number_format( (int) get_option( 'lp_ai_match_price_standard', 3000 ) );
$price_premium  = number_format( (int) get_option( 'lp_ai_match_price_premium', 6900 ) );
?>
<div class="lp-pricing" id="lp-pricing">
	<h2 class="lp-pricing__title"><?php esc_html_e( '韓国式AI運命マッチング', 'lp-ai-match' ); ?></h2>
	<p class="lp-pricing__subtitle"><?php esc_html_e( '四柱推命×AI分析で、運命の相手を見つけましょう', 'lp-ai-match' ); ?></p>

	<div class="lp-pricing__cards">
		<!-- Basic -->
		<div class="lp-pricing__card">
			<div class="lp-pricing__card-header">
				<h3><?php esc_html_e( 'ベーシック', 'lp-ai-match' ); ?></h3>
			</div>
			<div class="lp-pricing__price">
				<span class="lp-pricing__currency">¥</span>
				<span class="lp-pricing__amount"><?php echo esc_html( $price_basic ); ?></span>
			</div>
			<ul class="lp-pricing__features">
				<li><?php esc_html_e( '今日の運命の相手 1名', 'lp-ai-match' ); ?></li>
				<li><?php esc_html_e( '簡単AI相性レポート', 'lp-ai-match' ); ?></li>
				<li><?php esc_html_e( '24時間チャット', 'lp-ai-match' ); ?></li>
			</ul>
			<button type="button" class="lp-btn lp-btn--primary lp-pricing__btn" data-tier="basic">
				<?php esc_html_e( '購入する', 'lp-ai-match' ); ?>
			</button>
		</div>

		<!-- Standard -->
		<div class="lp-pricing__card lp-pricing__card--popular">
			<div class="lp-pricing__badge"><?php esc_html_e( '人気No.1', 'lp-ai-match' ); ?></div>
			<div class="lp-pricing__card-header">
				<h3><?php esc_html_e( 'スタンダード', 'lp-ai-match' ); ?></h3>
			</div>
			<div class="lp-pricing__price">
				<span class="lp-pricing__currency">¥</span>
				<span class="lp-pricing__amount"><?php echo esc_html( $price_standard ); ?></span>
			</div>
			<ul class="lp-pricing__features">
				<li><?php esc_html_e( '運命の相手 3名マッチング', 'lp-ai-match' ); ?></li>
				<li><?php esc_html_e( '詳細AI相性レポート', 'lp-ai-match' ); ?></li>
				<li><?php esc_html_e( '7日間チャット', 'lp-ai-match' ); ?></li>
				<li><?php esc_html_e( '葛藤分析・タイミング予測', 'lp-ai-match' ); ?></li>
			</ul>
			<button type="button" class="lp-btn lp-btn--accent lp-pricing__btn" data-tier="standard">
				<?php esc_html_e( '購入する', 'lp-ai-match' ); ?>
			</button>
		</div>

		<!-- Premium -->
		<div class="lp-pricing__card">
			<div class="lp-pricing__card-header">
				<h3><?php esc_html_e( 'プレミアム', 'lp-ai-match' ); ?></h3>
			</div>
			<div class="lp-pricing__price">
				<span class="lp-pricing__currency">¥</span>
				<span class="lp-pricing__amount"><?php echo esc_html( $price_premium ); ?></span>
			</div>
			<ul class="lp-pricing__features">
				<li><?php esc_html_e( '韓国式四柱結婚相性分析', 'lp-ai-match' ); ?></li>
				<li><?php esc_html_e( '年度別結婚運グラフ', 'lp-ai-match' ); ?></li>
				<li><?php esc_html_e( '運命の相手 5名マッチング', 'lp-ai-match' ); ?></li>
				<li><?php esc_html_e( '7日間チャット', 'lp-ai-match' ); ?></li>
				<li><?php esc_html_e( 'PDF詳細レポート', 'lp-ai-match' ); ?></li>
			</ul>
			<button type="button" class="lp-btn lp-btn--primary lp-pricing__btn" data-tier="premium">
				<?php esc_html_e( '購入する', 'lp-ai-match' ); ?>
			</button>
		</div>
	</div>
</div>
