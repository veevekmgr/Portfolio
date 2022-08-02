<?php


namespace RT\ThePostGrid\Controllers\Admin;

class PostTypeController {

	public function __construct() {
		add_action( 'init', [ &$this, 'register_post_types' ], 1 );
		add_action( 'admin_init', [ &$this, 'the_post_grid_remove_all_meta_box' ], 9999 );
	}

	public function the_post_grid_remove_all_meta_box() {
		// if ( is_admin() && apply_filters( 'rttpg_remove_all_extra_metabox_from_shordcode', true ) ) {
		// 	add_filter( "get_user_option_meta-box-order_" . rtTPG()->post_type, [ &$this, 'remove_all_meta_boxes_tgp_sc', ] );
		// }

		if ( get_option( 'rttpg_activation_redirect', false ) ) {
			delete_option( 'rttpg_activation_redirect' );
			wp_redirect( admin_url( 'edit.php?post_type=rttpg&page=rttpg_settings&section=common-settings' ) );
		}
	}

	public function register_post_types() {

		// Create the post grid post type
		$labels = [
			'name'               => __( 'The Post Grid', 'the-post-grid' ),
			'singular_name'      => __( 'The Post Grid', 'the-post-grid' ),
			'add_new'            => __( 'Add New Grid', 'the-post-grid' ),
			'all_items'          => __( 'All Grids', 'the-post-grid' ),
			'add_new_item'       => __( 'Add New Post Grid', 'the-post-grid' ),
			'edit_item'          => __( 'Edit Post Grid', 'the-post-grid' ),
			'new_item'           => __( 'New Post Grid', 'the-post-grid' ),
			'view_item'          => __( 'View Post Grid', 'the-post-grid' ),
			'search_items'       => __( 'Search Post Grids', 'the-post-grid' ),
			'not_found'          => __( 'No Post Grids found', 'the-post-grid' ),
			'not_found_in_trash' => __( 'No Post Grids found in Trash', 'the-post-grid' ),
		];

		register_post_type( rtTPG()->post_type,
			[
				'labels'          => $labels,
				'public'          => false,
				'show_ui'         => true,
				'_builtin'        => false,
				'capability_type' => 'page',
				'hierarchical'    => true,
				'menu_icon'       => rtTPG()->get_assets_uri( 'images/icon-16x16.png' ),
				'rewrite'         => false,
				'query_var'       => rtTPG()->post_type,
				'supports'        => [
					'title',
				],
				'show_in_menu'    => true,
				'menu_position'   => 20,
			] );


	}


	/**
	 * @return void|array
	 */
	public function remove_all_meta_boxes_tgp_sc() {
		global $wp_meta_boxes;
		if ( isset( $wp_meta_boxes[ rtTPG()->post_type ]['normal']['high']['rttpg_meta'] )
		     && $wp_meta_boxes[ rtTPG()->post_type ]['normal']['high']['rttpg_sc_preview_meta']
		     && $wp_meta_boxes[ rtTPG()->post_type ]['side']['low']['rt_plugin_sc_pro_information']
		) {

			$publishBox   = $wp_meta_boxes[ rtTPG()->post_type ]['side']['core']['submitdiv'];
			$scBox        = $wp_meta_boxes[ rtTPG()->post_type ]['normal']['high']['rttpg_meta'];
			$scBoxPreview = $wp_meta_boxes[ rtTPG()->post_type ]['normal']['high']['rttpg_sc_preview_meta'];
			$docBox       = $wp_meta_boxes[ rtTPG()->post_type ]['side']['low']['rt_plugin_sc_pro_information'];

			$wp_meta_boxes[ rtTPG()->post_type ] = [
				'side'     => [
					'core'    => [ 'submitdiv' => $publishBox ],
					'default' => [
						'rt_plugin_sc_pro_information' => $docBox,
					],
				],
				'normal'   => [ 'high' => [ 'submitdiv' => $scBox ] ],
				'advanced' => [ 'high' => [ 'postexcerpt' => $scBoxPreview ] ],
			];

			return [];
		}
	}

}