<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$questions = LP_Matching::get_personality_questions();
$values    = LP_Matching::get_value_keywords();
?>
<div class="lp-profile-form" id="lp-profile-form">
	<h2 class="lp-profile-form__title"><?php esc_html_e( 'プロフィール作成', 'lp-ai-match' ); ?></h2>
	<p class="lp-profile-form__subtitle"><?php esc_html_e( 'あなたのことを教えてください。AIが最適な相手を見つけます。', 'lp-ai-match' ); ?></p>

	<form id="lp-profile-form-el" class="lp-form">
		<!-- Step 1: Basic Info -->
		<div class="lp-form__step" data-step="1">
			<h3 class="lp-form__step-title"><?php esc_html_e( '基本情報', 'lp-ai-match' ); ?></h3>

			<div class="lp-form-group">
				<label for="lp-nickname" class="lp-label"><?php esc_html_e( 'ニックネーム', 'lp-ai-match' ); ?></label>
				<input type="text" id="lp-nickname" name="nickname" class="lp-input" required maxlength="20">
			</div>

			<div class="lp-form-group">
				<label for="lp-birthdate" class="lp-label"><?php esc_html_e( '生年月日', 'lp-ai-match' ); ?></label>
				<input type="date" id="lp-birthdate" name="birthdate" class="lp-input" required>
			</div>

			<div class="lp-form-group">
				<label class="lp-label"><?php esc_html_e( '性別', 'lp-ai-match' ); ?></label>
				<div class="lp-radio-group">
					<label class="lp-radio">
						<input type="radio" name="gender" value="female" required>
						<span><?php esc_html_e( '女性', 'lp-ai-match' ); ?></span>
					</label>
					<label class="lp-radio">
						<input type="radio" name="gender" value="male">
						<span><?php esc_html_e( '男性', 'lp-ai-match' ); ?></span>
					</label>
				</div>
			</div>
		</div>

		<!-- Step 2: Personality Questions -->
		<div class="lp-form__step" data-step="2" style="display:none">
			<h3 class="lp-form__step-title"><?php esc_html_e( '性格診断', 'lp-ai-match' ); ?></h3>

			<?php foreach ( $questions as $q ) : ?>
			<div class="lp-form-group">
				<label class="lp-label"><?php echo esc_html( $q['text'] ); ?></label>
				<div class="lp-option-group">
					<?php foreach ( $q['options'] as $key => $text ) : ?>
					<label class="lp-option">
						<input type="radio" name="q_<?php echo esc_attr( $q['id'] ); ?>" value="<?php echo esc_attr( $key ); ?>" required>
						<span class="lp-option__text"><?php echo esc_html( $text ); ?></span>
					</label>
					<?php endforeach; ?>
				</div>
			</div>
			<?php endforeach; ?>
		</div>

		<!-- Step 3: Values -->
		<div class="lp-form__step" data-step="3" style="display:none">
			<h3 class="lp-form__step-title"><?php esc_html_e( '大切にしている価値観', 'lp-ai-match' ); ?></h3>
			<p class="lp-form__hint"><?php esc_html_e( '3つまで選んでください', 'lp-ai-match' ); ?></p>

			<div class="lp-value-tags">
				<?php foreach ( $values as $key => $label ) : ?>
				<label class="lp-value-tag">
					<input type="checkbox" name="values[]" value="<?php echo esc_attr( $key ); ?>">
					<span class="lp-value-tag__text"><?php echo esc_html( $label ); ?></span>
				</label>
				<?php endforeach; ?>
			</div>
		</div>

		<!-- Step 4: Confirmation -->
		<div class="lp-form__step" data-step="4" style="display:none">
			<h3 class="lp-form__step-title"><?php esc_html_e( '確認', 'lp-ai-match' ); ?></h3>

			<div class="lp-form-group">
				<label class="lp-checkbox">
					<input type="checkbox" id="lp-age-verify" name="age_verified" value="1" required>
					<span><?php esc_html_e( '18歳以上であることを確認します', 'lp-ai-match' ); ?></span>
				</label>
			</div>

			<div class="lp-form-group">
				<label class="lp-checkbox">
					<input type="checkbox" id="lp-terms" required>
					<span>
						<a href="/terms/" target="_blank"><?php esc_html_e( '利用規約', 'lp-ai-match' ); ?></a>
						<?php esc_html_e( 'と', 'lp-ai-match' ); ?>
						<a href="/privacy/" target="_blank"><?php esc_html_e( 'プライバシーポリシー', 'lp-ai-match' ); ?></a>
						<?php esc_html_e( 'に同意します', 'lp-ai-match' ); ?>
					</span>
				</label>
			</div>
		</div>

		<!-- Navigation -->
		<div class="lp-form__nav">
			<button type="button" id="lp-form-prev" class="lp-btn lp-btn--secondary" style="display:none">
				<?php esc_html_e( '戻る', 'lp-ai-match' ); ?>
			</button>
			<button type="button" id="lp-form-next" class="lp-btn lp-btn--primary">
				<?php esc_html_e( '次へ', 'lp-ai-match' ); ?>
			</button>
			<button type="submit" id="lp-form-submit" class="lp-btn lp-btn--accent" style="display:none">
				<?php esc_html_e( 'プロフィールを保存', 'lp-ai-match' ); ?>
			</button>
		</div>

		<!-- Progress -->
		<div class="lp-form__progress">
			<div class="lp-form__progress-bar" id="lp-form-progress" style="width:25%"></div>
		</div>
	</form>
</div>
