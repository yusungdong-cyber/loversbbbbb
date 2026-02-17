<?php
/**
 * Custom Post Types registration.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LP_Post_Types {

	/**
	 * Initialize CPT registration.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_types' ) );
	}

	/**
	 * Register all custom post types.
	 */
	public static function register_post_types() {
		// Profile CPT.
		register_post_type(
			'lp_profile',
			array(
				'labels'       => array(
					'name'          => __( 'プロフィール', 'lp-ai-match' ),
					'singular_name' => __( 'プロフィール', 'lp-ai-match' ),
					'add_new'       => __( '新規追加', 'lp-ai-match' ),
					'add_new_item'  => __( 'プロフィールを追加', 'lp-ai-match' ),
					'edit_item'     => __( 'プロフィールを編集', 'lp-ai-match' ),
					'all_items'     => __( 'すべてのプロフィール', 'lp-ai-match' ),
				),
				'public'       => false,
				'show_ui'      => true,
				'show_in_menu' => false,
				'supports'     => array( 'title', 'author' ),
				'has_archive'  => false,
				'rewrite'      => false,
			)
		);

		// Match CPT.
		register_post_type(
			'lp_match',
			array(
				'labels'       => array(
					'name'          => __( 'マッチング', 'lp-ai-match' ),
					'singular_name' => __( 'マッチング', 'lp-ai-match' ),
					'all_items'     => __( 'すべてのマッチング', 'lp-ai-match' ),
				),
				'public'       => false,
				'show_ui'      => true,
				'show_in_menu' => false,
				'supports'     => array( 'title', 'author' ),
				'has_archive'  => false,
				'rewrite'      => false,
			)
		);

		// Report CPT.
		register_post_type(
			'lp_report',
			array(
				'labels'       => array(
					'name'          => __( '相性レポート', 'lp-ai-match' ),
					'singular_name' => __( '相性レポート', 'lp-ai-match' ),
					'all_items'     => __( 'すべてのレポート', 'lp-ai-match' ),
				),
				'public'       => false,
				'show_ui'      => true,
				'show_in_menu' => false,
				'supports'     => array( 'title', 'author', 'editor' ),
				'has_archive'  => false,
				'rewrite'      => false,
			)
		);

		// Reading CPT (horoscope/tarot).
		register_post_type(
			'lp_reading',
			array(
				'labels'       => array(
					'name'          => __( '占い結果', 'lp-ai-match' ),
					'singular_name' => __( '占い結果', 'lp-ai-match' ),
					'all_items'     => __( 'すべての占い結果', 'lp-ai-match' ),
				),
				'public'       => false,
				'show_ui'      => true,
				'show_in_menu' => false,
				'supports'     => array( 'title', 'author', 'editor' ),
				'has_archive'  => false,
				'rewrite'      => false,
			)
		);
	}
}
