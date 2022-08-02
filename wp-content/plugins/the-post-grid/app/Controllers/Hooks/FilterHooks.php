<?php

namespace RT\ThePostGrid\Controllers\Hooks;

use Cassandra\Varint;
use RT\ThePostGrid\Helpers\Fns;

class FilterHooks {

	public static function init() {
		add_filter( 'tpg_author_arg', [ __CLASS__, 'filter_author_args' ], 10 );
		add_filter( 'plugin_row_meta', [ __CLASS__, 'plugin_row_meta' ], 10, 2 );

		$settings = get_option( 'rt_the_post_grid_settings' );
		if ( isset( $settings['show_acf_details'] ) && $settings['show_acf_details'] ) {
			add_filter( 'the_content', [ __CLASS__, 'tpg_acf_content_filter' ] );
		}
		add_filter( 'wp_head', [ __CLASS__, 'set_post_view_count' ], 9999 );
		add_filter( 'admin_body_class', [ __CLASS__, 'admin_body_class' ] );
	}


	public static function admin_body_class($clsses){
		$settings = get_option('rt_the_post_grid_settings');

		if( isset($settings['tpg_block_type']) && in_array($settings['tpg_block_type'], ['elementor', 'shortcode']) ) {
			$clsses .= 'tpg-block-type-elementor-or-shortcode';
		}

		return $clsses;
	}


	public static function set_post_view_count( $content ) {
		if ( is_single() ) {
			$pId = get_the_ID();
			Fns::update_post_views_count( $pId );
		}

		return $content;
	}

	public static function filter_author_args( $args ) {
		$defaults = [ 'role__in' => [ 'administrator', 'editor', 'author' ] ];

		return wp_parse_args( $args, $defaults );
	}

	public static function plugin_row_meta( $links, $file ) {
		if ( $file == RT_THE_POST_GRID_PLUGIN_ACTIVE_FILE_NAME ) {
			$report_url         = 'https://www.radiustheme.com/contact/';
			$row_meta['issues'] = sprintf( '%2$s <a target="_blank" href="%1$s">%3$s</a>',
				esc_url( $report_url ),
				esc_html__( 'Facing issue?', 'the-post-grid' ),
				'<span style="color: red">' . esc_html__( 'Please open a support ticket.', 'the-post-grid' ) . '</span>' );

			return array_merge( $links, $row_meta );
		}

		return (array) $links;
	}


	public static function tpg_acf_content_filter( $content ) {
		// Check if we're inside the main loop in a post or page.
		if ( is_single() && in_the_loop() && is_main_query() && rtTPG()->hasPro() ) {
			$settings = get_option( rtTPG()->options['settings'] );

			$data = [
				'show_acf'            => isset( $settings['show_acf_details'] ) && $settings['show_acf_details'] ? 'show' : false,
				'cf_group'            => isset( $settings['cf_group_details'] ) ? $settings['cf_group_details'] : [],
				'cf_hide_empty_value' => isset( $settings['cf_hide_empty_value_details'] ) ? $settings['cf_hide_empty_value_details'] : false,
				'cf_show_only_value'  => isset( $settings['cf_show_only_value_details'] ) ? $settings['cf_show_only_value_details'] : false,
				'cf_hide_group_title' => isset( $settings['cf_hide_group_title_details'] ) ? $settings['cf_hide_group_title_details'] : false,
			];

			return $content . Fns::tpg_get_acf_data_elementor( $data, null, false );
		}

		return $content;
	}

}
