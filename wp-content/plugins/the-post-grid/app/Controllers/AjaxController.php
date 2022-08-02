<?php

namespace RT\ThePostGrid\Controllers;

use RT\ThePostGrid\Helpers\Fns;
use RT\ThePostGrid\Helpers\Options;

class AjaxController {
	function __construct() {
		add_action( 'wp_ajax_rtTPGSettings', array( $this, 'rtTPGSaveSettings' ) );
		add_action( 'wp_ajax_rtTPGShortCodeList', array( $this, 'shortCodeList' ) );
		add_action( 'wp_ajax_rtTPGTaxonomyListByPostType', array( $this, 'rtTPGTaxonomyListByPostType' ) );
		add_action( 'wp_ajax_rtTPGIsotopeFilter', array( $this, 'rtTPGIsotopeFilter' ) );
		add_action( 'wp_ajax_rtTPGTermListByTaxonomy', array( $this, 'rtTPGTermListByTaxonomy' ) );
		add_action( 'wp_ajax_defaultFilterItem', array( $this, 'defaultFilterItem' ) );
		add_action( 'wp_ajax_getCfGroupListAsField', array( $this, 'getCfGroupListAsField' ) );
	}

	function getCfGroupListAsField() {

		$error = true;
		$data  = $msg = null;
		if ( Fns::verifyNonce() ) {
			$fields    = array();
			$post_type = ! empty( $_REQUEST['post_type'] ) ? $_REQUEST['post_type'] : null;
			if ( $cf = Fns::is_acf() && $post_type ) {
				$fields['cf_group'] = array(
					"type"        => "checkbox",
					"name"        => "cf_group",
					"holderClass" => "tpg-hidden cf-fields cf-group",
					"label"       => "Custom Field group",
					"multiple"    => true,
					"alignment"   => "vertical",
					"id"          => "cf_group",
					"options"     => Fns::get_groups_by_post_type( $post_type, $cf )
				);
				$error              = false;
				$data               = Fns::rtFieldGenerator( $fields );
			}
		} else {
			$msg = __( 'Server Error !!', 'the-post-grid' );
		}
		$response = array(
			'error' => $error,
			'msg'   => $msg,
			'data'  => $data
		);
		wp_send_json( $response );
		die();
	}

	function defaultFilterItem() {

		$error = true;
		$data  = $msg = null;
		if ( Fns::verifyNonce() ) {
			if ( $filter = $_REQUEST['filter'] ) {
				$include = [];
				if ( isset( $_REQUEST['include'] ) && $term = $_REQUEST['include'] ) {
					$include = explode( ',', $term );
				}
				$error = false;
				$msg   = __( 'Success', 'the-post-grid' );
				$data  .= "<option value=''>" . __( 'Show All', 'the-post-grid' ) . "</option>";
				$items = Fns::rt_get_selected_term_by_taxonomy( $filter, $include, '', 0 );
				if ( ! empty( $items ) ) {
					foreach ( $items as $id => $item ) {
						$data .= "<option value='{$id}'>{$item}</option>";
					}
				}
			}
		} else {
			$msg = __( 'Session Error !!', 'the-post-grid' );
		}
		$response = array(
			'error' => $error,
			'msg'   => $msg,
			'data'  => $data
		);
		wp_send_json( $response );
		die();
	}

	function rtTPGSaveSettings() {

		$error = true;
		if ( Fns::verifyNonce() ) {
			unset( $_REQUEST['action'] );
			unset( $_REQUEST[ rtTPG()->nonceId() ] );
			unset( $_REQUEST['_wp_http_referer'] );

			update_option( rtTPG()->options['settings'], $_REQUEST );
			$response = array(
				'error' => false,
				'msg'   => __( 'Settings successfully updated', 'the-post-grid' )
			);
		} else {
			$response = array(
				'error' => $error,
				'msg'   => __( 'Session Error !!', 'the-post-grid' )
			);
		}
		wp_send_json( $response );
		die();
	}

	function rtTPGTaxonomyListByPostType() {

		$error = true;
		$msg   = $data = null;
		if ( Fns::verifyNonce() ) {
			$error      = false;
			$taxonomies = Fns::rt_get_all_taxonomy_by_post_type( isset( $_REQUEST['post_type'] ) ? $_REQUEST['post_type'] : null );
			if ( is_array( $taxonomies ) && ! empty( $taxonomies ) ) {
				$data .= Fns::rtFieldGenerator(
					array(
						'tpg_taxonomy' => array(
							'type'     => 'checkbox',
							'label'    => 'Taxonomy',
							'id'       => 'post-taxonomy',
							"multiple" => true,
							"value"    => isset( $_REQUEST['taxonomy'] ) ? $_REQUEST['taxonomy'] : [],
							'options'  => $taxonomies
						)
					)
				);
			} else {
				$data = __( '<div class="field-holder">No Taxonomy found</div>', 'the-post-grid' );
			}

		} else {
			$msg = __( 'Security error', 'the-post-grid' );
		}
		wp_send_json( array( 'error' => $error, 'msg' => $msg, 'data' => $data ) );
		die();
	}

	function rtTPGIsotopeFilter() {

		$error = true;
		$msg   = $data = null;
		if ( Fns::verifyNonce() ) {
			$error      = false;
			$taxonomies = Fns::rt_get_taxonomy_for_filter( isset( $_REQUEST['post_type'] ) ? $_REQUEST['post_type'] : null );
			if ( is_array( $taxonomies ) && ! empty( $taxonomies ) ) {
				foreach ( $taxonomies as $tKey => $tax ) {
					$data .= "<option value='{$tKey}'>{$tax}</option>";
				}
			}
		} else {
			$msg = __( 'Security error', 'the-post-grid' );
		}
		wp_send_json( array( 'error' => $error, 'msg' => $msg, 'data' => $data ) );
		die();
	}

	function rtTPGTermListByTaxonomy() {

		$error = true;
		$msg   = $data = null;
		if ( Fns::verifyNonce() ) {
			$error    = false;
			$taxonomy = isset( $_REQUEST['taxonomy'] ) ? $_REQUEST['taxonomy'] : null;
			$data     .= "<div class='term-filter-item-container {$taxonomy}'>";
			$data     .= Fns::rtFieldGenerator(
				array(
					'term_' . $taxonomy => array(
						'type'        => 'select',
						'label'       => ucfirst( str_replace( '_', ' ', $taxonomy ) ),
						'class'       => 'rt-select2 full',
						'id'          => 'term-' . mt_rand(),
						'holderClass' => "term-filter-item {$taxonomy}",
						'value'       => null,
						"multiple"    => true,
						'options'     => Fns::rt_get_all_term_by_taxonomy( $taxonomy )
					)
				)
			);
			$data     .= Fns::rtFieldGenerator(
				array(
					'term_operator_' . $taxonomy => array(
						'type'        => 'select',
						'label'       => 'Operator',
						'class'       => 'rt-select2 full',
						'holderClass' => "term-filter-item-operator {$taxonomy}",
						'options'     => Options::rtTermOperators()
					)
				)
			);
			$data     .= "</div>";
		} else {
			$msg = __( 'Security error', 'the-post-grid' );
		}
		wp_send_json( array( 'error' => $error, 'msg' => $msg, 'data' => $data ) );
		die();
	}

	function shortCodeList() {
		$html = null;
		$scQ  = new \WP_Query( apply_filters( 'tpg_sc_list_query_args',
			array( 'post_type'      => rtTPG()->post_type,
			       'order_by'       => 'title',
			       'order'          => 'DESC',
			       'post_status'    => 'publish',
			       'posts_per_page' => - 1
			)
		) );
		if ( $scQ->have_posts() ) {

			$html .= "<div class='mce-container mce-form'>";
			$html .= "<div class='mce-container-body'>";
			$html .= '<label class="mce-widget mce-label" style="padding: 20px;font-weight: bold;" for="scid">' . __( 'Select Short code', 'the-post-grid' ) . '</label>';
			$html .= "<select name='id' id='scid' style='width: 150px;margin: 15px;'>";
			$html .= "<option value=''>" . __( 'Default', 'the-post-grid' ) . "</option>";
			while ( $scQ->have_posts() ) {
				$scQ->the_post();
				$html .= "<option value='" . get_the_ID() . "'>" . get_the_title() . "</option>";
			}
			$html .= "</select>";
			$html .= "</div>";
			$html .= "</div>";
		} else {
			$html .= "<div>" . __( 'No shortCode found.', 'the-post-grid' ) . "</div>";
		}
		echo $html;
		die();
	}
}