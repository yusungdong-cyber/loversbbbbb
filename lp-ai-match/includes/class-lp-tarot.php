<?php
/**
 * Tarot card system.
 *
 * 22 Major Arcana cards with love interpretations.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LP_Tarot {

	/**
	 * Default Major Arcana cards with love interpretations.
	 */
	private static $default_cards = array(
		0  => array(
			'name'    => '愚者',
			'name_en' => 'The Fool',
			'image'   => 'fool.png',
			'upright' => '新しい恋の始まり。自由な心で出会いを受け入れましょう。型にはまらない素敵な関係が生まれるかもしれません。',
			'reverse' => '恋愛に対して無計画になりがち。少し立ち止まって考えてみましょう。',
		),
		1  => array(
			'name'    => '魔術師',
			'name_en' => 'The Magician',
			'image'   => 'magician.png',
			'upright' => 'あなたの魅力が最大限に発揮される時。自信を持ってアプローチしましょう。',
			'reverse' => '見栄を張りすぎていませんか？ありのままの自分を見せることが大切です。',
		),
		2  => array(
			'name'    => '女教皇',
			'name_en' => 'The High Priestess',
			'image'   => 'high_priestess.png',
			'upright' => '直感を信じて。心の声に従えば、運命の相手に出会えるでしょう。',
			'reverse' => '感情を抑えすぎないで。素直な気持ちを大切にしましょう。',
		),
		3  => array(
			'name'    => '女帝',
			'name_en' => 'The Empress',
			'image'   => 'empress.png',
			'upright' => '愛に満ちた関係が築けます。母性的な優しさが相手の心を包みます。',
			'reverse' => '相手に尽くしすぎていませんか？自分自身も大切にしましょう。',
		),
		4  => array(
			'name'    => '皇帝',
			'name_en' => 'The Emperor',
			'image'   => 'emperor.png',
			'upright' => '安定した関係を築くチャンス。リーダーシップを発揮して。',
			'reverse' => '支配的にならないように注意。対等な関係を意識しましょう。',
		),
		5  => array(
			'name'    => '教皇',
			'name_en' => 'The Hierophant',
			'image'   => 'hierophant.png',
			'upright' => '伝統的な恋愛の形が幸運をもたらします。誠実さが鍵です。',
			'reverse' => '形式にとらわれすぎず、二人だけの関係を築きましょう。',
		),
		6  => array(
			'name'    => '恋人',
			'name_en' => 'The Lovers',
			'image'   => 'lovers.png',
			'upright' => '運命的な出会いの予感。深い愛と絆で結ばれるでしょう。',
			'reverse' => '選択を迫られるかも。本当に大切な人を見極めて。',
		),
		7  => array(
			'name'    => '戦車',
			'name_en' => 'The Chariot',
			'image'   => 'chariot.png',
			'upright' => '恋愛に積極的になれる時。勇気を持って前に進みましょう。',
			'reverse' => '焦りは禁物。相手のペースも尊重しましょう。',
		),
		8  => array(
			'name'    => '力',
			'name_en' => 'Strength',
			'image'   => 'strength.png',
			'upright' => '内面の強さが恋を引き寄せます。忍耐と優しさで愛を育てて。',
			'reverse' => '自信を失わないで。あなたには十分な魅力があります。',
		),
		9  => array(
			'name'    => '隠者',
			'name_en' => 'The Hermit',
			'image'   => 'hermit.png',
			'upright' => '一人の時間が恋愛観を深めます。自分と向き合うことで理想の相手が見えてきます。',
			'reverse' => '孤独を恐れないで。でも殻に閉じこもりすぎないように。',
		),
		10 => array(
			'name'    => '運命の輪',
			'name_en' => 'Wheel of Fortune',
			'image'   => 'wheel_of_fortune.png',
			'upright' => '恋愛運が大きく変わる転機。チャンスを逃さないで。',
			'reverse' => '思い通りにいかなくても焦らずに。流れは必ず変わります。',
		),
		11 => array(
			'name'    => '正義',
			'name_en' => 'Justice',
			'image'   => 'justice.png',
			'upright' => '公平で誠実な関係が築けます。お互いを尊重する姿勢が大切。',
			'reverse' => '恋愛で不公平を感じていませんか？バランスを見直しましょう。',
		),
		12 => array(
			'name'    => '吊された男',
			'name_en' => 'The Hanged Man',
			'image'   => 'hanged_man.png',
			'upright' => '視点を変えることで新しい恋が見えてきます。待つことも愛の形。',
			'reverse' => '犠牲的になりすぎないで。自分の幸せも大切にしましょう。',
		),
		13 => array(
			'name'    => '死神',
			'name_en' => 'Death',
			'image'   => 'death.png',
			'upright' => '古い恋を手放す時。新しい関係への扉が開かれます。変化を恐れないで。',
			'reverse' => '過去の恋に執着していませんか？前を向くことで幸せが訪れます。',
		),
		14 => array(
			'name'    => '節制',
			'name_en' => 'Temperance',
			'image'   => 'temperance.png',
			'upright' => 'バランスの取れた穏やかな恋愛。ゆっくりと関係を深めていきましょう。',
			'reverse' => '極端な行動は控えて。中庸を心がけましょう。',
		),
		15 => array(
			'name'    => '悪魔',
			'name_en' => 'The Devil',
			'image'   => 'devil.png',
			'upright' => '情熱的な恋。しかし依存にならないように注意が必要です。',
			'reverse' => '束縛から解放される時。自由な恋愛を楽しみましょう。',
		),
		16 => array(
			'name'    => '塔',
			'name_en' => 'The Tower',
			'image'   => 'tower.png',
			'upright' => '予想外の展開。しかしそれは本当の愛へ向かうための変化です。',
			'reverse' => '変化を恐れすぎないで。壊れることで新しいものが生まれます。',
		),
		17 => array(
			'name'    => '星',
			'name_en' => 'The Star',
			'image'   => 'star.png',
			'upright' => '希望に満ちた恋愛運。あなたの願いは必ず叶います。星に導かれて。',
			'reverse' => '希望を失わないで。明けない夜はありません。',
		),
		18 => array(
			'name'    => '月',
			'name_en' => 'The Moon',
			'image'   => 'moon.png',
			'upright' => '神秘的な魅力が増す時。ミステリアスなあなたに惹かれる人がいます。',
			'reverse' => '不安や迷いに惑わされないで。真実を見極めましょう。',
		),
		19 => array(
			'name'    => '太陽',
			'name_en' => 'The Sun',
			'image'   => 'sun.png',
			'upright' => '最高の恋愛運！明るく輝くあなたに幸せな出会いが訪れます。',
			'reverse' => '楽観的すぎないように。現実もしっかり見つめましょう。',
		),
		20 => array(
			'name'    => '審判',
			'name_en' => 'Judgement',
			'image'   => 'judgement.png',
			'upright' => '過去の恋から学び、新しいステージへ。復縁の可能性も。',
			'reverse' => '過去を振り返りすぎず、前を向きましょう。',
		),
		21 => array(
			'name'    => '世界',
			'name_en' => 'The World',
			'image'   => 'world.png',
			'upright' => '恋愛の完成。すべてが調和し、理想的な関係が築けます。',
			'reverse' => 'もう少しで完成です。最後まで諦めないで。',
		),
	);

	/**
	 * Draw 3 random tarot cards.
	 *
	 * @return array Three drawn cards with position meanings.
	 */
	public static function draw_three_cards() {
		$cards    = self::get_cards();
		$card_ids = array_keys( $cards );
		shuffle( $card_ids );

		$drawn    = array_slice( $card_ids, 0, 3 );
		$positions = array(
			__( '過去', 'lp-ai-match' ),
			__( '現在', 'lp-ai-match' ),
			__( '未来', 'lp-ai-match' ),
		);

		$result = array();
		foreach ( $drawn as $i => $card_id ) {
			$card = $cards[ $card_id ];
			// 50% chance reversed.
			$is_reversed = ( wp_rand( 0, 1 ) === 1 );

			$result[] = array(
				'id'          => $card_id,
				'name'        => $card['name'],
				'name_en'     => $card['name_en'],
				'image'       => $card['image'],
				'position'    => $positions[ $i ],
				'is_reversed' => $is_reversed,
				'meaning'     => $is_reversed ? $card['reverse'] : $card['upright'],
			);
		}

		return $result;
	}

	/**
	 * Get all cards (merged with admin customizations).
	 *
	 * @return array All tarot cards.
	 */
	public static function get_cards() {
		$custom_cards = get_option( 'lp_ai_match_tarot_cards', array() );
		if ( ! empty( $custom_cards ) && is_array( $custom_cards ) ) {
			return wp_parse_args( $custom_cards, self::$default_cards );
		}
		return self::$default_cards;
	}

	/**
	 * Get a single card by ID.
	 *
	 * @param int $card_id Card index.
	 * @return array|null Card data or null.
	 */
	public static function get_card( $card_id ) {
		$cards = self::get_cards();
		return isset( $cards[ $card_id ] ) ? $cards[ $card_id ] : null;
	}

	/**
	 * Update a card's data (admin use).
	 *
	 * @param int   $card_id Card index.
	 * @param array $data    Updated card data.
	 * @return bool Success.
	 */
	public static function update_card( $card_id, $data ) {
		$cards = self::get_cards();
		if ( ! isset( $cards[ $card_id ] ) ) {
			return false;
		}

		$cards[ $card_id ] = wp_parse_args( $data, $cards[ $card_id ] );
		return update_option( 'lp_ai_match_tarot_cards', $cards );
	}
}
