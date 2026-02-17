<?php
/**
 * Saju (四柱) calculation engine.
 *
 * Korean-style Four Pillars of Destiny compatibility calculation.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LP_Saju {

	/**
	 * Heavenly Stems (天干).
	 */
	private static $heavenly_stems = array(
		'甲', '乙', '丙', '丁', '戊', '己', '庚', '辛', '壬', '癸',
	);

	/**
	 * Earthly Branches (地支).
	 */
	private static $earthly_branches = array(
		'子', '丑', '寅', '卯', '辰', '巳', '午', '未', '申', '酉', '戌', '亥',
	);

	/**
	 * Five Elements (五行).
	 */
	private static $elements = array(
		'木' => 'wood',
		'火' => 'fire',
		'土' => 'earth',
		'金' => 'metal',
		'水' => 'water',
	);

	/**
	 * Stem-to-Element mapping.
	 */
	private static $stem_element = array(
		0 => '木', 1 => '木',  // 甲, 乙
		2 => '火', 3 => '火',  // 丙, 丁
		4 => '土', 5 => '土',  // 戊, 己
		6 => '金', 7 => '金',  // 庚, 辛
		8 => '水', 9 => '水',  // 壬, 癸
	);

	/**
	 * Branch-to-Element mapping.
	 */
	private static $branch_element = array(
		0  => '水', // 子
		1  => '土', // 丑
		2  => '木', // 寅
		3  => '木', // 卯
		4  => '土', // 辰
		5  => '火', // 巳
		6  => '火', // 午
		7  => '土', // 未
		8  => '金', // 申
		9  => '金', // 酉
		10 => '土', // 戌
		11 => '水', // 亥
	);

	/**
	 * Element compatibility matrix.
	 * Positive = generating cycle, negative = overcoming cycle.
	 */
	private static $element_compat = array(
		'木木' => 50, '木火' => 80, '木土' => 30, '木金' => 20, '木水' => 90,
		'火木' => 80, '火火' => 50, '火土' => 85, '火金' => 25, '火水' => 15,
		'土木' => 30, '土火' => 85, '土土' => 50, '土金' => 80, '土水' => 25,
		'金木' => 20, '金火' => 25, '金土' => 80, '金金' => 50, '金水' => 85,
		'水木' => 90, '水火' => 15, '水土' => 25, '水金' => 85, '水水' => 50,
	);

	/**
	 * Calculate the Four Pillars from a birth date.
	 *
	 * @param string $birthdate Format: Y-m-d.
	 * @return array Pillar data with stems, branches, elements.
	 */
	public static function calculate_pillars( $birthdate ) {
		$date  = new DateTime( $birthdate );
		$year  = (int) $date->format( 'Y' );
		$month = (int) $date->format( 'n' );
		$day   = (int) $date->format( 'j' );

		// Year pillar.
		$year_stem_idx   = ( $year - 4 ) % 10;
		$year_branch_idx = ( $year - 4 ) % 12;

		// Month pillar (simplified — based on solar month).
		$month_branch_idx = ( $month + 1 ) % 12;
		$month_stem_idx   = ( ( $year_stem_idx % 5 ) * 2 + $month ) % 10;

		// Day pillar (simplified calculation).
		$base_date   = new DateTime( '1900-01-01' );
		$diff        = $base_date->diff( $date )->days;
		$day_stem_idx   = ( $diff + 10 ) % 10;
		$day_branch_idx = ( $diff + 12 ) % 12;

		return array(
			'year'  => array(
				'stem'    => self::$heavenly_stems[ $year_stem_idx ],
				'branch'  => self::$earthly_branches[ $year_branch_idx ],
				'element' => self::$stem_element[ $year_stem_idx ],
			),
			'month' => array(
				'stem'    => self::$heavenly_stems[ $month_stem_idx ],
				'branch'  => self::$earthly_branches[ $month_branch_idx ],
				'element' => self::$stem_element[ $month_stem_idx ],
			),
			'day'   => array(
				'stem'    => self::$heavenly_stems[ $day_stem_idx ],
				'branch'  => self::$earthly_branches[ $day_branch_idx ],
				'element' => self::$stem_element[ $day_stem_idx ],
			),
		);
	}

	/**
	 * Calculate saju compatibility score between two people.
	 *
	 * @param string $birthdate_a Format: Y-m-d.
	 * @param string $birthdate_b Format: Y-m-d.
	 * @return array Score and analysis details.
	 */
	public static function calculate_compatibility( $birthdate_a, $birthdate_b ) {
		$pillars_a = self::calculate_pillars( $birthdate_a );
		$pillars_b = self::calculate_pillars( $birthdate_b );

		$total  = 0;
		$count  = 0;
		$details = array();

		foreach ( array( 'year', 'month', 'day' ) as $pillar ) {
			$elem_a = $pillars_a[ $pillar ]['element'];
			$elem_b = $pillars_b[ $pillar ]['element'];
			$key    = $elem_a . $elem_b;

			$score = isset( self::$element_compat[ $key ] ) ? self::$element_compat[ $key ] : 50;
			$total += $score;
			$count++;

			$details[ $pillar ] = array(
				'element_a' => $elem_a,
				'element_b' => $elem_b,
				'score'     => $score,
			);
		}

		$final_score = $count > 0 ? round( $total / $count, 2 ) : 50;

		return array(
			'score'     => $final_score,
			'pillars_a' => $pillars_a,
			'pillars_b' => $pillars_b,
			'details'   => $details,
		);
	}

	/**
	 * Calculate yearly marriage luck graph.
	 *
	 * @param string $birthdate Format: Y-m-d.
	 * @param int    $start_year Start year.
	 * @param int    $end_year   End year.
	 * @return array Year => luck score.
	 */
	public static function calculate_marriage_luck( $birthdate, $start_year = 0, $end_year = 0 ) {
		if ( 0 === $start_year ) {
			$start_year = (int) gmdate( 'Y' );
		}
		if ( 0 === $end_year ) {
			$end_year = $start_year + 5;
		}

		$pillars = self::calculate_pillars( $birthdate );
		$day_element = $pillars['day']['element'];
		$luck = array();

		for ( $year = $start_year; $year <= $end_year; $year++ ) {
			$year_stem_idx = ( $year - 4 ) % 10;
			$year_element  = self::$stem_element[ $year_stem_idx ];
			$key = $day_element . $year_element;

			$base_score = isset( self::$element_compat[ $key ] ) ? self::$element_compat[ $key ] : 50;
			// Add some variance based on year.
			$variance = ( ( $year * 7 + 13 ) % 20 ) - 10;
			$luck[ $year ] = max( 0, min( 100, $base_score + $variance ) );
		}

		return $luck;
	}

	/**
	 * Get element name in Japanese.
	 *
	 * @param string $element Chinese character element.
	 * @return string Japanese element description.
	 */
	public static function get_element_name_ja( $element ) {
		$names = array(
			'木' => '木（もく）',
			'火' => '火（か）',
			'土' => '土（ど）',
			'金' => '金（きん）',
			'水' => '水（すい）',
		);
		return isset( $names[ $element ] ) ? $names[ $element ] : $element;
	}
}
