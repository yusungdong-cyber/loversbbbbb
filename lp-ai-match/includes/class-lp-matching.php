<?php
/**
 * Matching algorithm.
 *
 * Final Score = (Saju 40%) + (Personality 40%) + (Values 20%)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LP_Matching {

	/**
	 * Personality question definitions.
	 */
	private static $personality_questions = array(
		array(
			'id'      => 'introvert_extrovert',
			'text'    => '休日はどう過ごしたいですか？',
			'options' => array(
				'a' => '家でゆっくり過ごしたい',
				'b' => '友達と外出したい',
				'c' => '二人でカフェに行きたい',
				'd' => '新しい場所を探検したい',
			),
		),
		array(
			'id'      => 'communication_style',
			'text'    => 'コミュニケーションスタイルは？',
			'options' => array(
				'a' => 'こまめに連絡したい',
				'b' => '必要な時だけ連絡',
				'c' => '電話派',
				'd' => 'LINEスタンプ派',
			),
		),
		array(
			'id'      => 'conflict_style',
			'text'    => 'ケンカした時どうしますか？',
			'options' => array(
				'a' => 'すぐ話し合う',
				'b' => '少し時間をおく',
				'c' => '手紙やメッセージで伝える',
				'd' => '相手が落ち着くまで待つ',
			),
		),
		array(
			'id'      => 'love_language',
			'text'    => '愛情表現で一番大切なのは？',
			'options' => array(
				'a' => '言葉で伝える',
				'b' => '一緒に時間を過ごす',
				'c' => 'プレゼントやサプライズ',
				'd' => 'スキンシップ',
			),
		),
		array(
			'id'      => 'future_vision',
			'text'    => '理想の将来は？',
			'options' => array(
				'a' => '二人で静かに暮らしたい',
				'b' => '子供がいる賑やかな家庭',
				'c' => 'お互いの夢を追いかける',
				'd' => '旅行しながら生活したい',
			),
		),
		array(
			'id'      => 'money_attitude',
			'text'    => 'お金の使い方は？',
			'options' => array(
				'a' => '計画的に貯金',
				'b' => '体験にお金を使いたい',
				'c' => '半分貯金、半分趣味',
				'd' => '今を楽しむ派',
			),
		),
		array(
			'id'      => 'jealousy_level',
			'text'    => 'パートナーが異性の友達と食事。どう思う？',
			'options' => array(
				'a' => '全く気にならない',
				'b' => '少し気になるけど信頼',
				'c' => '事前に教えてほしい',
				'd' => '正直嫌だ',
			),
		),
		array(
			'id'      => 'romance_style',
			'text'    => '理想のデートは？',
			'options' => array(
				'a' => 'おしゃれなレストラン',
				'b' => '自然の中でピクニック',
				'c' => '映画やアート鑑賞',
				'd' => 'おうちでまったり',
			),
		),
	);

	/**
	 * Value keywords for matching.
	 */
	private static $value_keywords = array(
		'family'     => '家族を大切に',
		'career'     => 'キャリア重視',
		'adventure'  => '冒険好き',
		'stability'  => '安定志向',
		'creativity' => '創造性',
		'health'     => '健康第一',
		'education'  => '学び続ける',
		'kindness'   => '思いやり',
		'humor'      => 'ユーモア',
		'honesty'    => '正直さ',
		'ambition'   => '向上心',
		'loyalty'    => '忠誠心',
	);

	/**
	 * Get personality questions.
	 *
	 * @return array Questions.
	 */
	public static function get_personality_questions() {
		return self::$personality_questions;
	}

	/**
	 * Get value keywords.
	 *
	 * @return array Keywords.
	 */
	public static function get_value_keywords() {
		return self::$value_keywords;
	}

	/**
	 * Calculate personality match score between two profiles.
	 *
	 * @param array $answers_a User A's answers (question_id => answer).
	 * @param array $answers_b User B's answers.
	 * @return float Score 0-100.
	 */
	public static function calculate_personality_score( $answers_a, $answers_b ) {
		if ( empty( $answers_a ) || empty( $answers_b ) ) {
			return 50.0;
		}

		// Compatibility matrix: same answers can be good or indicate need for complement.
		$complement_questions = array( 'introvert_extrovert', 'communication_style', 'conflict_style' );
		$match_questions      = array( 'love_language', 'future_vision', 'money_attitude', 'romance_style' );

		$total = 0;
		$count = 0;

		foreach ( self::$personality_questions as $q ) {
			$qid = $q['id'];
			if ( ! isset( $answers_a[ $qid ] ) || ! isset( $answers_b[ $qid ] ) ) {
				continue;
			}

			$a = $answers_a[ $qid ];
			$b = $answers_b[ $qid ];
			$count++;

			if ( in_array( $qid, $complement_questions, true ) ) {
				// Complementary: different answers score higher.
				$total += ( $a !== $b ) ? 85 : 60;
			} elseif ( in_array( $qid, $match_questions, true ) ) {
				// Matching: same answers score higher.
				$total += ( $a === $b ) ? 90 : 55;
			} else {
				// Neutral.
				$total += ( $a === $b ) ? 75 : 65;
			}
		}

		return $count > 0 ? round( $total / $count, 2 ) : 50.0;
	}

	/**
	 * Calculate values compatibility score.
	 *
	 * @param array $values_a User A's selected value keywords.
	 * @param array $values_b User B's selected value keywords.
	 * @return float Score 0-100.
	 */
	public static function calculate_values_score( $values_a, $values_b ) {
		if ( empty( $values_a ) || empty( $values_b ) ) {
			return 50.0;
		}

		$common = array_intersect( $values_a, $values_b );
		$total  = count( array_unique( array_merge( $values_a, $values_b ) ) );

		if ( 0 === $total ) {
			return 50.0;
		}

		// Jaccard similarity * 100.
		return round( ( count( $common ) / $total ) * 100, 2 );
	}

	/**
	 * Calculate total matching score.
	 *
	 * @param int $profile_a_id Profile A post ID.
	 * @param int $profile_b_id Profile B post ID.
	 * @return array Detailed scores.
	 */
	public static function calculate_total_score( $profile_a_id, $profile_b_id ) {
		// Get profile data.
		$dob_a       = get_post_meta( $profile_a_id, '_lp_birthdate', true );
		$dob_b       = get_post_meta( $profile_b_id, '_lp_birthdate', true );
		$answers_a   = get_post_meta( $profile_a_id, '_lp_personality_answers', true );
		$answers_b   = get_post_meta( $profile_b_id, '_lp_personality_answers', true );
		$values_a    = get_post_meta( $profile_a_id, '_lp_values', true );
		$values_b    = get_post_meta( $profile_b_id, '_lp_values', true );

		// Get weights from settings.
		$saju_weight       = (int) get_option( 'lp_ai_match_saju_weight', 40 );
		$personality_weight = (int) get_option( 'lp_ai_match_personality_weight', 40 );
		$values_weight     = (int) get_option( 'lp_ai_match_values_weight', 20 );

		// Calculate individual scores.
		$saju_result      = LP_Saju::calculate_compatibility( $dob_a, $dob_b );
		$saju_score       = $saju_result['score'];
		$personality_score = self::calculate_personality_score(
			is_array( $answers_a ) ? $answers_a : array(),
			is_array( $answers_b ) ? $answers_b : array()
		);
		$values_score = self::calculate_values_score(
			is_array( $values_a ) ? $values_a : array(),
			is_array( $values_b ) ? $values_b : array()
		);

		// Weighted total.
		$total_weight = $saju_weight + $personality_weight + $values_weight;
		if ( $total_weight <= 0 ) {
			$total_weight = 100;
		}
		$total_score = round(
			( $saju_score * $saju_weight + $personality_score * $personality_weight + $values_score * $values_weight ) / $total_weight,
			2
		);

		// Save to DB.
		self::save_score( $profile_a_id, $profile_b_id, $saju_score, $personality_score, $values_score, $total_score );

		return array(
			'saju_score'       => $saju_score,
			'saju_details'     => $saju_result,
			'personality_score' => $personality_score,
			'values_score'     => $values_score,
			'total_score'      => $total_score,
			'weights'          => array(
				'saju'        => $saju_weight,
				'personality' => $personality_weight,
				'values'      => $values_weight,
			),
		);
	}

	/**
	 * Find best matches for a profile.
	 *
	 * @param int $profile_id Profile post ID.
	 * @param int $limit      Number of matches to return.
	 * @return array Sorted matches with scores.
	 */
	public static function find_matches( $profile_id, $limit = 3 ) {
		$user_gender = get_post_meta( $profile_id, '_lp_gender', true );
		$target_gender = ( 'male' === $user_gender ) ? 'female' : 'male';

		// Get opposite gender profiles.
		$candidates = get_posts(
			array(
				'post_type'      => 'lp_profile',
				'posts_per_page' => 100,
				'post_status'    => 'publish',
				'exclude'        => array( $profile_id ),
				'meta_query'     => array(
					array(
						'key'   => '_lp_gender',
						'value' => $target_gender,
					),
				),
			)
		);

		$matches = array();
		foreach ( $candidates as $candidate ) {
			$score_data = self::calculate_total_score( $profile_id, $candidate->ID );
			$matches[] = array(
				'profile_id' => $candidate->ID,
				'scores'     => $score_data,
			);
		}

		// Sort by total score descending.
		usort(
			$matches,
			function ( $a, $b ) {
				return $b['scores']['total_score'] <=> $a['scores']['total_score'];
			}
		);

		return array_slice( $matches, 0, $limit );
	}

	/**
	 * Save matching score to DB.
	 *
	 * @param int   $profile_a_id   Profile A ID.
	 * @param int   $profile_b_id   Profile B ID.
	 * @param float $saju_score     Saju score.
	 * @param float $personality    Personality score.
	 * @param float $values         Values score.
	 * @param float $total          Total score.
	 */
	private static function save_score( $profile_a_id, $profile_b_id, $saju_score, $personality, $values, $total ) {
		global $wpdb;
		$table = $wpdb->prefix . 'lp_matching_scores';

		// Ensure consistent ordering.
		$a = min( $profile_a_id, $profile_b_id );
		$b = max( $profile_a_id, $profile_b_id );

		$wpdb->replace(
			$table,
			array(
				'profile_a_id'     => $a,
				'profile_b_id'     => $b,
				'saju_score'       => $saju_score,
				'personality_score' => $personality,
				'values_score'     => $values,
				'total_score'      => $total,
				'calculated_at'    => current_time( 'mysql', true ),
			),
			array( '%d', '%d', '%f', '%f', '%f', '%f', '%s' )
		);
	}
}
