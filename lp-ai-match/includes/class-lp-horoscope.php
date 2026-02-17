<?php
/**
 * Horoscope / Zodiac sign calculation and fortune generation.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LP_Horoscope {

	/**
	 * Zodiac signs with date ranges.
	 */
	private static $signs = array(
		'おひつじ座'  => array( 'start' => '03-21', 'end' => '04-19', 'symbol' => '♈', 'en' => 'aries' ),
		'おうし座'    => array( 'start' => '04-20', 'end' => '05-20', 'symbol' => '♉', 'en' => 'taurus' ),
		'ふたご座'    => array( 'start' => '05-21', 'end' => '06-21', 'symbol' => '♊', 'en' => 'gemini' ),
		'かに座'      => array( 'start' => '06-22', 'end' => '07-22', 'symbol' => '♋', 'en' => 'cancer' ),
		'しし座'      => array( 'start' => '07-23', 'end' => '08-22', 'symbol' => '♌', 'en' => 'leo' ),
		'おとめ座'    => array( 'start' => '08-23', 'end' => '09-22', 'symbol' => '♍', 'en' => 'virgo' ),
		'てんびん座'  => array( 'start' => '09-23', 'end' => '10-23', 'symbol' => '♎', 'en' => 'libra' ),
		'さそり座'    => array( 'start' => '10-24', 'end' => '11-22', 'symbol' => '♏', 'en' => 'scorpio' ),
		'いて座'      => array( 'start' => '11-23', 'end' => '12-21', 'symbol' => '♐', 'en' => 'sagittarius' ),
		'やぎ座'      => array( 'start' => '12-22', 'end' => '01-19', 'symbol' => '♑', 'en' => 'capricorn' ),
		'みずがめ座'  => array( 'start' => '01-20', 'end' => '02-18', 'symbol' => '♒', 'en' => 'aquarius' ),
		'うお座'      => array( 'start' => '02-19', 'end' => '03-20', 'symbol' => '♓', 'en' => 'pisces' ),
	);

	/**
	 * Default fortune texts per sign (admin-editable via options).
	 */
	private static $default_fortunes = array(
		'aries'       => array(
			'今日は新しい出会いの予感。積極的に行動すると良い結果が待っています。',
			'片思いの人は今日がチャンス。思い切って声をかけてみましょう。',
			'恋愛運は上昇中。大切な人との絆が深まる一日です。',
		),
		'taurus'      => array(
			'安定した関係を求めるあなたに、素敵な出会いが訪れそうです。',
			'パートナーとのコミュニケーションが鍵。素直な気持ちを伝えましょう。',
			'自分磨きの努力が実を結ぶ時。魅力が増しています。',
		),
		'gemini'      => array(
			'知的な会話がきっかけで恋が生まれるかも。好奇心を大切に。',
			'複数の選択肢がある時は直感を信じて。心が示す方向へ。',
			'軽やかな気持ちでいることで、素敵な縁が舞い込みます。',
		),
		'cancer'      => array(
			'優しさが伝わる日。あなたの温かさに惹かれる人がいます。',
			'家庭的な一面を見せると好感度アップ。自然体でいましょう。',
			'過去の恋愛から学んだことが、次の幸せにつながります。',
		),
		'leo'         => array(
			'輝くあなたの魅力が最大限に発揮される日。自信を持って。',
			'大胆なアプローチが功を奏す。恐れずに気持ちを表現しましょう。',
			'注目を集める一日。でも謙虚さも忘れずに。',
		),
		'virgo'       => array(
			'細やかな気配りが相手の心を動かします。さりげない優しさが鍵。',
			'完璧を求めすぎず、自然体の自分を見せましょう。',
			'実直な性格が評価される日。信頼が恋につながります。',
		),
		'libra'       => array(
			'バランスの取れた関係が幸運を呼びます。調和を大切に。',
			'美しいものに触れることで恋愛運がアップ。おしゃれを楽しんで。',
			'迷いがあるなら、心が軽くなる方を選びましょう。',
		),
		'scorpio'     => array(
			'深い感情が魅力となる日。本気の想いが相手に届きます。',
			'秘密にしていた気持ちを打ち明けるタイミングかも。',
			'直感が冴える日。第六感を信じて行動しましょう。',
		),
		'sagittarius' => array(
			'冒険心が恋を呼ぶ日。いつもと違う場所に出かけてみて。',
			'自由を愛するあなたに、同じ価値観の人が現れそう。',
			'楽観的な気持ちが幸運を引き寄せます。笑顔を忘れずに。',
		),
		'capricorn'   => array(
			'真面目な姿勢が認められる日。努力が恋愛面でも実を結びます。',
			'将来を見据えた出会いがありそう。長期的な視点で考えて。',
			'控えめだけど確実なアプローチが効果的。焦らずに。',
		),
		'aquarius'    => array(
			'ユニークな発想が相手を惹きつけます。自分らしさを大切に。',
			'友人関係から恋に発展する可能性。周りの人を大切に。',
			'既成概念にとらわれない新しい恋の形が見つかるかも。',
		),
		'pisces'      => array(
			'ロマンチックな雰囲気が漂う日。夢見る心が恋を引き寄せます。',
			'直感を信じて。心が示す方向に運命の人がいます。',
			'芸術的な感性が魅力に。自分の感性を表現してみましょう。',
		),
	);

	/**
	 * Get zodiac sign from birthdate.
	 *
	 * @param string $birthdate Format: Y-m-d.
	 * @return array Sign data with name, symbol, and English key.
	 */
	public static function get_sign( $birthdate ) {
		$date  = new DateTime( $birthdate );
		$md    = $date->format( 'm-d' );

		foreach ( self::$signs as $name => $data ) {
			if ( 'やぎ座' === $name ) {
				// Capricorn spans year boundary.
				if ( $md >= '12-22' || $md <= '01-19' ) {
					return array(
						'name'   => $name,
						'symbol' => $data['symbol'],
						'en'     => $data['en'],
					);
				}
			} elseif ( $md >= $data['start'] && $md <= $data['end'] ) {
				return array(
					'name'   => $name,
					'symbol' => $data['symbol'],
					'en'     => $data['en'],
				);
			}
		}

		// Fallback.
		return array(
			'name'   => 'やぎ座',
			'symbol' => '♑',
			'en'     => 'capricorn',
		);
	}

	/**
	 * Generate today's love horoscope for a given birthdate.
	 *
	 * @param string $birthdate Format: Y-m-d.
	 * @return array Horoscope data.
	 */
	public static function generate_daily_horoscope( $birthdate ) {
		$sign = self::get_sign( $birthdate );

		// Check for admin-customized fortunes.
		$custom = get_option( 'lp_ai_match_horoscope_' . $sign['en'], array() );
		if ( ! empty( $custom ) && is_array( $custom ) ) {
			$fortunes = $custom;
		} else {
			$fortunes = isset( self::$default_fortunes[ $sign['en'] ] )
				? self::$default_fortunes[ $sign['en'] ]
				: array( '今日も素敵な一日になりますように。' );
		}

		// Select fortune based on date (deterministic per day).
		$day_index = (int) gmdate( 'z' ); // Day of year 0-365.
		$fortune_index = $day_index % count( $fortunes );

		// Generate luck score deterministically.
		$seed = crc32( $sign['en'] . gmdate( 'Y-m-d' ) );
		$luck_score = abs( $seed ) % 101; // 0-100.

		return array(
			'sign'        => $sign,
			'fortune'     => $fortunes[ $fortune_index ],
			'luck_score'  => $luck_score,
			'date'        => gmdate( 'Y-m-d' ),
			'lucky_color' => self::get_lucky_color( $seed ),
			'lucky_time'  => self::get_lucky_time( $seed ),
		);
	}

	/**
	 * Get a lucky color based on seed.
	 *
	 * @param int $seed Random seed.
	 * @return string Color name in Japanese.
	 */
	private static function get_lucky_color( $seed ) {
		$colors = array(
			'ピンク', 'ラベンダー', 'ホワイト', 'ゴールド',
			'スカイブルー', 'コーラル', 'ミント', 'シャンパン',
		);
		return $colors[ abs( $seed ) % count( $colors ) ];
	}

	/**
	 * Get a lucky time based on seed.
	 *
	 * @param int $seed Random seed.
	 * @return string Time range.
	 */
	private static function get_lucky_time( $seed ) {
		$times = array(
			'10:00〜12:00', '13:00〜15:00', '15:00〜17:00',
			'17:00〜19:00', '19:00〜21:00', '21:00〜23:00',
		);
		return $times[ abs( $seed >> 4 ) % count( $times ) ];
	}

	/**
	 * Get all zodiac signs.
	 *
	 * @return array All sign data.
	 */
	public static function get_all_signs() {
		$result = array();
		foreach ( self::$signs as $name => $data ) {
			$result[] = array(
				'name'   => $name,
				'symbol' => $data['symbol'],
				'en'     => $data['en'],
			);
		}
		return $result;
	}
}
