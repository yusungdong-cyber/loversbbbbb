<?php
/**
 * AI Report generation.
 *
 * Generates compatibility reports based on matching scores.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LP_Report {

	/**
	 * Generate a full compatibility report.
	 *
	 * @param int    $match_id Match post ID.
	 * @param string $tier     Product tier: basic, standard, premium.
	 * @return int|WP_Error Report post ID or error.
	 */
	public static function generate( $match_id, $tier = 'basic' ) {
		$profile_a_id = get_post_meta( $match_id, '_lp_profile_a', true );
		$profile_b_id = get_post_meta( $match_id, '_lp_profile_b', true );

		if ( ! $profile_a_id || ! $profile_b_id ) {
			return new WP_Error( 'invalid_match', __( 'マッチングデータが見つかりません。', 'lp-ai-match' ) );
		}

		$scores = LP_Matching::calculate_total_score( $profile_a_id, $profile_b_id );

		// Build report content.
		$content = self::build_report_content( $profile_a_id, $profile_b_id, $scores, $tier );

		// Check if report already exists for this match.
		$existing = get_posts(
			array(
				'post_type'  => 'lp_report',
				'meta_key'   => '_lp_match_id',
				'meta_value' => $match_id,
				'numberposts' => 1,
			)
		);

		if ( ! empty( $existing ) ) {
			// Update existing report.
			$report_id = $existing[0]->ID;
			wp_update_post(
				array(
					'ID'           => $report_id,
					'post_content' => $content,
				)
			);
		} else {
			// Create new report.
			$name_a = get_the_title( $profile_a_id );
			$name_b = get_the_title( $profile_b_id );

			$report_id = wp_insert_post(
				array(
					'post_type'    => 'lp_report',
					'post_title'   => sprintf( '%s × %s 相性レポート', $name_a, $name_b ),
					'post_content' => $content,
					'post_status'  => 'publish',
					'post_author'  => get_post_field( 'post_author', $profile_a_id ),
				)
			);
		}

		if ( is_wp_error( $report_id ) ) {
			return $report_id;
		}

		// Save meta.
		update_post_meta( $report_id, '_lp_match_id', $match_id );
		update_post_meta( $report_id, '_lp_profile_a', $profile_a_id );
		update_post_meta( $report_id, '_lp_profile_b', $profile_b_id );
		update_post_meta( $report_id, '_lp_scores', $scores );
		update_post_meta( $report_id, '_lp_tier', $tier );
		update_post_meta( $report_id, '_lp_generated_at', current_time( 'mysql' ) );

		return $report_id;
	}

	/**
	 * Build report HTML content.
	 *
	 * @param int    $profile_a_id Profile A ID.
	 * @param int    $profile_b_id Profile B ID.
	 * @param array  $scores       Matching scores.
	 * @param string $tier         Product tier.
	 * @return string HTML content.
	 */
	private static function build_report_content( $profile_a_id, $profile_b_id, $scores, $tier ) {
		$dob_a = get_post_meta( $profile_a_id, '_lp_birthdate', true );
		$dob_b = get_post_meta( $profile_b_id, '_lp_birthdate', true );
		$name_a = get_the_title( $profile_a_id );
		$name_b = get_the_title( $profile_b_id );

		$total = $scores['total_score'];
		$saju  = $scores['saju_score'];
		$personality = $scores['personality_score'];
		$values = $scores['values_score'];

		// Section 1: Total Score.
		$html = '<div class="lp-report">';
		$html .= '<h2 class="lp-report__title">相性総合スコア</h2>';
		$html .= '<div class="lp-report__total-score">';
		$html .= '<span class="lp-report__score-number">' . esc_html( round( $total ) ) . '</span>';
		$html .= '<span class="lp-report__score-unit">点</span>';
		$html .= '</div>';
		$html .= '<p class="lp-report__score-label">' . esc_html( self::get_score_label( $total ) ) . '</p>';

		// Section 2: Saju Analysis.
		$html .= '<h3 class="lp-report__section-title">四柱推命による分析</h3>';
		$html .= '<div class="lp-report__score-bar">';
		$html .= '<span class="lp-report__bar-label">四柱相性</span>';
		$html .= '<div class="lp-report__bar" style="width:' . esc_attr( $saju ) . '%"></div>';
		$html .= '<span class="lp-report__bar-value">' . esc_html( round( $saju ) ) . '点</span>';
		$html .= '</div>';
		$html .= '<p>' . esc_html( self::get_saju_analysis( $saju, $scores['saju_details'] ) ) . '</p>';

		// Section 3: Personality complement.
		$html .= '<h3 class="lp-report__section-title">性格の補完関係</h3>';
		$html .= '<div class="lp-report__score-bar">';
		$html .= '<span class="lp-report__bar-label">性格マッチ</span>';
		$html .= '<div class="lp-report__bar" style="width:' . esc_attr( $personality ) . '%"></div>';
		$html .= '<span class="lp-report__bar-value">' . esc_html( round( $personality ) ) . '点</span>';
		$html .= '</div>';
		$html .= '<p>' . esc_html( self::get_personality_analysis( $personality ) ) . '</p>';

		// Section 4: Conflict potential (standard and premium only).
		if ( in_array( $tier, array( 'standard', 'premium' ), true ) ) {
			$html .= '<h3 class="lp-report__section-title">葛藤の可能性</h3>';
			$html .= '<p>' . esc_html( self::get_conflict_analysis( $total, $personality, $values ) ) . '</p>';
		}

		// Section 5: Relationship timing (standard and premium only).
		if ( in_array( $tier, array( 'standard', 'premium' ), true ) ) {
			$html .= '<h3 class="lp-report__section-title">関係発展のタイミング</h3>';
			$html .= '<p>' . esc_html( self::get_timing_analysis( $total, $saju ) ) . '</p>';
		}

		// Premium: Marriage luck graph.
		if ( 'premium' === $tier ) {
			$html .= '<h3 class="lp-report__section-title">結婚運グラフ</h3>';
			$marriage_luck = LP_Saju::calculate_marriage_luck( $dob_a );
			$html .= '<div class="lp-report__marriage-graph">';
			foreach ( $marriage_luck as $year => $luck ) {
				$html .= '<div class="lp-report__graph-bar">';
				$html .= '<span class="lp-report__graph-year">' . esc_html( $year ) . '</span>';
				$html .= '<div class="lp-report__graph-fill" style="height:' . esc_attr( $luck ) . '%"></div>';
				$html .= '<span class="lp-report__graph-value">' . esc_html( $luck ) . '</span>';
				$html .= '</div>';
			}
			$html .= '</div>';
		}

		// Values compatibility.
		$html .= '<h3 class="lp-report__section-title">価値観の適合度</h3>';
		$html .= '<div class="lp-report__score-bar">';
		$html .= '<span class="lp-report__bar-label">価値観</span>';
		$html .= '<div class="lp-report__bar" style="width:' . esc_attr( $values ) . '%"></div>';
		$html .= '<span class="lp-report__bar-value">' . esc_html( round( $values ) ) . '点</span>';
		$html .= '</div>';
		$html .= '<p>' . esc_html( self::get_values_analysis( $values ) ) . '</p>';

		$html .= '</div>';

		// Try AI enhancement if API key is configured.
		$api_key = get_option( 'lp_ai_match_openai_api_key', '' );
		if ( ! empty( $api_key ) && in_array( $tier, array( 'standard', 'premium' ), true ) ) {
			$ai_content = self::enhance_with_ai( $name_a, $name_b, $scores, $tier, $api_key );
			if ( $ai_content ) {
				$html .= '<div class="lp-report__ai-insight">';
				$html .= '<h3 class="lp-report__section-title">AI詳細分析</h3>';
				$html .= wp_kses_post( $ai_content );
				$html .= '</div>';
			}
		}

		return $html;
	}

	/**
	 * Get score label text.
	 *
	 * @param float $score Total score.
	 * @return string Label.
	 */
	private static function get_score_label( $score ) {
		if ( $score >= 90 ) {
			return '運命の相手！奇跡的な相性です。';
		} elseif ( $score >= 75 ) {
			return '素晴らしい相性！お互いを高め合える関係です。';
		} elseif ( $score >= 60 ) {
			return '良い相性です。努力次第で素敵な関係になれます。';
		} elseif ( $score >= 40 ) {
			return 'まずまずの相性。お互いを理解する努力が大切です。';
		} else {
			return '課題はありますが、乗り越えることで深い絆が生まれます。';
		}
	}

	/**
	 * Generate saju-based analysis text.
	 *
	 * @param float $score   Saju score.
	 * @param array $details Saju calculation details.
	 * @return string Analysis text.
	 */
	private static function get_saju_analysis( $score, $details ) {
		$year_detail = isset( $details['details']['year'] ) ? $details['details']['year'] : null;

		if ( $score >= 80 ) {
			$text = '四柱推命から見て、お二人は非常に高い相性を持っています。';
		} elseif ( $score >= 60 ) {
			$text = '四柱推命による分析では、お二人の間には良好な気の流れがあります。';
		} elseif ( $score >= 40 ) {
			$text = '四柱推命では、お二人にはいくつかの課題が見られますが、お互いを補い合える要素もあります。';
		} else {
			$text = '四柱推命的には挑戦的な組み合わせですが、これは成長の機会でもあります。';
		}

		if ( $year_detail ) {
			$elem_a = LP_Saju::get_element_name_ja( $year_detail['element_a'] );
			$elem_b = LP_Saju::get_element_name_ja( $year_detail['element_b'] );
			$text .= sprintf( ' 年柱の五行では%sと%sの関係にあり、', $elem_a, $elem_b );

			if ( $year_detail['score'] >= 80 ) {
				$text .= '自然と支え合える組み合わせです。';
			} elseif ( $year_detail['score'] >= 60 ) {
				$text .= '調和のとれた関係を築けます。';
			} else {
				$text .= 'お互いの違いを受け入れることが大切です。';
			}
		}

		return $text;
	}

	/**
	 * Generate personality analysis text.
	 *
	 * @param float $score Personality score.
	 * @return string Analysis text.
	 */
	private static function get_personality_analysis( $score ) {
		if ( $score >= 80 ) {
			return 'お二人の性格は理想的な補完関係にあります。一方の弱みをもう一方がカバーし、一緒にいることで最高のパフォーマンスを発揮できるでしょう。コミュニケーションも自然で、お互いの考えを理解しやすい関係です。';
		} elseif ( $score >= 60 ) {
			return 'お二人の性格は比較的相性が良く、共通点と補完関係のバランスが取れています。時に意見の相違があっても、お互いを尊重することで乗り越えられるでしょう。';
		} elseif ( $score >= 40 ) {
			return 'お二人は異なるタイプですが、それが魅力にもなり得ます。お互いの違いを認め、歩み寄る姿勢が大切です。新しい視点を与え合える関係です。';
		} else {
			return '性格面ではチャレンジングな組み合わせですが、だからこそ成長できる関係です。コミュニケーションを丁寧に行うことが鍵となります。';
		}
	}

	/**
	 * Generate conflict analysis text.
	 *
	 * @param float $total       Total score.
	 * @param float $personality Personality score.
	 * @param float $values      Values score.
	 * @return string Analysis text.
	 */
	private static function get_conflict_analysis( $total, $personality, $values ) {
		$risk = 100 - ( ( $personality + $values ) / 2 );

		if ( $risk < 25 ) {
			return '葛藤のリスクは非常に低いです。価値観や性格面で高い一致が見られ、大きな衝突は起こりにくいでしょう。ただし、穏やかすぎる関係にマンネリを感じることもあるかもしれません。時には新しい挑戦を一緒にすることをお勧めします。';
		} elseif ( $risk < 50 ) {
			return '日常的な小さな意見の相違はあるかもしれませんが、それは健全な関係の証でもあります。お互いの意見を尊重し、話し合いで解決していく姿勢が大切です。定期的なコミュニケーションが関係を強くします。';
		} elseif ( $risk < 75 ) {
			return '価値観や性格の違いから、時に葛藤が生じる可能性があります。特にお金の使い方や将来のビジョンについて話し合っておくことが重要です。お互いの違いを「個性」として受け入れることで、より深い関係が築けます。';
		} else {
			return '性格や価値観の違いが大きく、葛藤のリスクは高めです。しかし、違いがあるからこそお互いから学べることも多いです。忍耐力とオープンなコミュニケーションが必要不可欠です。専門家のカウンセリングも視野に入れてみてください。';
		}
	}

	/**
	 * Generate timing analysis text.
	 *
	 * @param float $total Total score.
	 * @param float $saju  Saju score.
	 * @return string Analysis text.
	 */
	private static function get_timing_analysis( $total, $saju ) {
		if ( $total >= 75 ) {
			return '今がまさに関係を深めるベストタイミングです。お互いの気の流れが合致しており、自然な形で関係が進展するでしょう。積極的なアプローチが吉。3ヶ月以内に大きな進展が期待できます。';
		} elseif ( $total >= 50 ) {
			return '関係はゆっくりと、しかし確実に進展していくでしょう。焦らず、まずは友人として信頼関係を築くことをお勧めします。半年後には大きな変化が訪れる可能性があります。';
		} else {
			return '現時点では関係の発展に少し時間がかかるかもしれません。しかし、お互いを深く理解する期間と捉えてください。1年後には驚くほど関係が変わっている可能性があります。自分磨きの時間として活用しましょう。';
		}
	}

	/**
	 * Generate values analysis text.
	 *
	 * @param float $score Values score.
	 * @return string Analysis text.
	 */
	private static function get_values_analysis( $score ) {
		if ( $score >= 80 ) {
			return '価値観の一致度が非常に高いです。人生で大切にしているものが共通しており、同じ方向を向いて歩んでいけるでしょう。';
		} elseif ( $score >= 60 ) {
			return '基本的な価値観は共有できており、お互いを理解しやすい関係です。一部の違いは良い刺激となるでしょう。';
		} elseif ( $score >= 40 ) {
			return '価値観にいくつかの違いがあります。しかし、多様な視点は関係を豊かにします。大切な点については話し合いを重ねましょう。';
		} else {
			return '価値観の違いは大きめですが、お互いから新しい世界を学べるチャンスでもあります。オープンマインドで向き合いましょう。';
		}
	}

	/**
	 * Enhance report with AI-generated content.
	 *
	 * @param string $name_a  Name A.
	 * @param string $name_b  Name B.
	 * @param array  $scores  Matching scores.
	 * @param string $tier    Product tier.
	 * @param string $api_key OpenAI API key.
	 * @return string|false AI-generated HTML or false on failure.
	 */
	private static function enhance_with_ai( $name_a, $name_b, $scores, $tier, $api_key ) {
		$prompt = sprintf(
			"あなたは恋愛相性の専門家です。以下のスコアデータに基づいて、%sさんと%sさんの相性について詳しい分析を日本語で書いてください。\n\n"
			. "総合スコア: %s点\n四柱相性: %s点\n性格マッチ: %s点\n価値観適合: %s点\n\n"
			. "以下の点について分析してください：\n"
			. "1. お二人の関係の特徴\n"
			. "2. うまくいくためのアドバイス\n"
			. "3. 注意すべきポイント\n\n"
			. "重要：ランダムな内容ではなく、スコアに基づいた具体的な分析を書いてください。HTML形式で返してください。",
			$name_a,
			$name_b,
			round( $scores['total_score'] ),
			round( $scores['saju_score'] ),
			round( $scores['personality_score'] ),
			round( $scores['values_score'] )
		);

		$response = wp_remote_post(
			'https://api.openai.com/v1/chat/completions',
			array(
				'timeout' => 30,
				'headers' => array(
					'Authorization' => 'Bearer ' . $api_key,
					'Content-Type'  => 'application/json',
				),
				'body'    => wp_json_encode(
					array(
						'model'    => 'gpt-4o-mini',
						'messages' => array(
							array(
								'role'    => 'user',
								'content' => $prompt,
							),
						),
						'max_tokens'  => 1000,
						'temperature' => 0.7,
					)
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( isset( $body['choices'][0]['message']['content'] ) ) {
			return $body['choices'][0]['message']['content'];
		}

		return false;
	}

	/**
	 * Get report HTML for display.
	 *
	 * @param int $report_id Report post ID.
	 * @return string HTML content.
	 */
	public static function get_report_html( $report_id ) {
		$post = get_post( $report_id );
		if ( ! $post || 'lp_report' !== $post->post_type ) {
			return '';
		}
		return $post->post_content;
	}
}
