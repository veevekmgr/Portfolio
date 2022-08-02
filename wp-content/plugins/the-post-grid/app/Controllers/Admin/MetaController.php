<?php


namespace RT\ThePostGrid\Controllers\Admin;


use RT\ThePostGrid\Helpers\Fns;
use RT\ThePostGrid\Helpers\Options;

class MetaController {
	function __construct() {
		// actions
		add_action( 'admin_head', array( $this, 'admin_head' ) );
		add_action( 'edit_form_after_title', array( $this, 'tpg_sc_after_title' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );
		add_filter( 'manage_edit-rttpg_columns', array( $this, 'arrange_rttpg_columns' ) );
		add_action( 'manage_rttpg_posts_custom_column', array( $this, 'manage_rttpg_columns' ), 10, 2 );
	}

	public function manage_rttpg_columns( $column ) {
		switch ( $column ) {
			case 'shortcode':
				echo '<input type="text" onfocus="this.select();" readonly="readonly" value="[the-post-grid id=&quot;' . get_the_ID() . '&quot; title=&quot;' . get_the_title() . '&quot;]" class="large-text code rt-code-sc">';
				break;
			default:
				break;
		}
	}

	function arrange_rttpg_columns( $columns ) {
		$shortcode = array( 'shortcode' => __( 'Shortcode', 'the-post-grid' ) );

		return array_slice( $columns, 0, 2, true ) + $shortcode + array_slice( $columns, 1, null, true );
	}

	function admin_enqueue_scripts() {

		global $pagenow, $typenow;

		if ( $typenow == 'tpg_builder' ) {
			wp_enqueue_style( 'rt-tpg-admin' );
		}

		// validate page
		if ( ! in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {
			return;
		}

		if ( $typenow != rtTPG()->post_type ) {
			return;
		}

		wp_dequeue_script( 'autosave' );
		wp_enqueue_media();

		$select2Id = 'rt-select2';
		if ( class_exists( 'WPSEO_Admin_Asset_Manager' ) && class_exists( 'Avada' ) ) {
			$select2Id = 'yoast-seo-select2';
		} elseif ( class_exists( 'WPSEO_Admin_Asset_Manager' ) ) {
			$select2Id = 'yoast-seo-select2';
		} elseif ( class_exists( 'Avada' ) ) {
			$select2Id = 'select2-avada-js';
		}

		// scripts
		wp_enqueue_script( array(
			'jquery',
			'jquery-ui-datepicker',
			'wp-color-picker',
			$select2Id,
			'imagesloaded',
			'rt-isotope-js',
			'rt-tpg-admin',
			'rt-tpg-admin-preview',
		) );

		// styles
		wp_enqueue_style( array(
			'wp-color-picker',
			'rt-select2',
			'rt-fontawsome',
			'rt-tpg-admin',
			'rt-tpg-admin-preview',
		) );

		wp_localize_script( 'rt-tpg-admin', 'rttpg',
			array(
				'nonceID' => rtTPG()->nonceId(),
				'nonce'   => wp_create_nonce( rtTPG()->nonceText() ),
				'ajaxurl' => admin_url( 'admin-ajax.php' )
			) );

	}

	function admin_head() {

		add_meta_box(
			'rttpg_meta',
			__( 'Short Code Generator', 'the-post-grid' ),
			array( $this, 'rttpg_meta_settings_selection' ),
			rtTPG()->post_type,
			'normal',
			'high' );

		add_meta_box(
			rtTPG()->post_type . '_sc_preview_meta',
			__( 'Layout Preview', 'the-post-grid' ),
			array( $this, 'tpg_sc_preview_selection' ),
			rtTPG()->post_type,
			'normal',
			'high' );

		add_meta_box(
			'rt_plugin_sc_pro_information',
			__( 'Documentation', 'the-post-grid' ),
			array( $this, 'rt_plugin_sc_pro_information' ),
			rtTPG()->post_type,
			'side',
			'low'
		);

	}

	function rt_plugin_sc_pro_information( $post ) {
		$html = '';
		if ( $post === 'settings' ) {
			$html .= '<div class="rt-document-box rt-update-pro-btn-wrap">
                <a href="https://www.radiustheme.com/the-post-grid-pro-for-wordpress/" target="_blank" class="rt-update-pro-btn">' . __( "Update Pro To Get More Features", "the-post-grid" ) . '</a>
            </div>';
		} else {
			if ( ! rtTPG()->hasPro() ) {
				$html .= sprintf( '<div class="rt-document-box"><div class="rt-box-icon"><i class="dashicons dashicons-megaphone"></i></div><div class="rt-box-content"><h3 class="rt-box-title">Pro Features</h3>%s</div></div>', Options::get_pro_feature_list() );
			}
		}
		$html .= sprintf( '<div class="rt-document-box">
							<div class="rt-box-icon"><i class="dashicons dashicons-media-document"></i></div>
							<div class="rt-box-content">
                    			<h3 class="rt-box-title">%1$s</h3>
                    				<p>%2$s</p>
                        			<a href="https://www.radiustheme.com/docs/the-post-grid/" target="_blank" class="rt-admin-btn">%1$s</a>
                			</div>
						</div>',
			__( "Documentation", 'the-post-grid' ),
			__( "Get started by spending some time with the documentation we included step by step process with screenshots with video.", 'the-post-grid' )
		);

		$html .= '<div class="rt-document-box">
							<div class="rt-box-icon"><i class="dashicons dashicons-sos"></i></div>
							<div class="rt-box-content">
                    			<h3 class="rt-box-title">Need Help?</h3>
                    				<p>Stuck with something? Please create a 
                        <a href="https://www.radiustheme.com/contact/">ticket here</a> or post on <a href="https://www.facebook.com/groups/234799147426640/">facebook group</a>. For emergency case join our <a href="https://www.radiustheme.com/">live chat</a>.</p>
                        			<a href="https://www.radiustheme.com/contact/" target="_blank" class="rt-admin-btn">' . __( "Get Support", "the-post-grid" ) . '</a>
                			</div>
						</div>';

		echo $html;
	}

	/**
	 *  Preview section
	 */
	function tpg_sc_preview_selection() {
		$html = null;
		$html .= "<div class='rt-response'></div>";
		$html .= "<div id='tpg-preview-container'></div>";
		echo $html;

	}


	function tpg_sc_after_title( $post ) {

		if ( rtTPG()->post_type !== $post->post_type ) {
			return;
		}
		$html = null;
		$html .= '<div class="postbox rt-after-title" style="margin-bottom: 0;"><div class="inside">';
		$html .= '<p><input type="text" onfocus="this.select();" readonly="readonly" value="[the-post-grid id=&quot;' . $post->ID . '&quot; title=&quot;' . $post->post_title . '&quot;]" class="large-text code rt-code-sc">
            <input type="text" onfocus="this.select();" readonly="readonly" value="&#60;&#63;php echo do_shortcode( &#39;[the-post-grid id=&quot;' . $post->ID . '&quot; title=&quot;' . $post->post_title . '&quot;]&#39; ); &#63;&#62;" class="large-text code rt-code-sc">
            </p>';
		$html .= '</div></div>';

		echo $html;
	}

	function rttpg_meta_settings_selection( $post ) {

		$last_tab = trim( get_post_meta( $post->ID, '_tpg_last_active_tab', true ) );
		$last_tab = $last_tab ? $last_tab : 'sc-post-post-source';
		$post     = array(
			'post' => $post
		);
		wp_nonce_field( rtTPG()->nonceText(), rtTPG()->nonceId() );
		$html = null;
		$html .= '<div id="sc-tabs" class="rttpg-wrapper rt-tab-container rt-setting-holder">';
		$html .= sprintf( '<ul class="rt-tab-nav">
                                <li%s><a href="#sc-post-post-source">%s</a></li>
                                <li%s><a href="#sc-post-layout-settings">%s</a></li>
                                <li%s><a href="#sc-settings">%s</a></li>
                                <li%s><a href="#sc-field-selection">%s</a></li>
                                <li%s><a href="#sc-style">%s</a></li>
                              </ul>',
			$last_tab == "sc-post-post-source" ? ' class="active"' : '',
			__( 'Query Build', 'the-post-grid' ),
			$last_tab == "sc-post-layout-settings" ? ' class="active"' : '',
			__( 'Layout Settings', 'the-post-grid' ),
			$last_tab == "sc-settings" ? ' class="active"' : '',
			__( 'Settings', 'the-post-grid' ),
			$last_tab == "sc-field-selection" ? ' class="active"' : '',
			__( 'Field Selection', 'the-post-grid' ),
			$last_tab == "sc-style" ? ' class="active"' : '',
			__( 'Style', 'the-post-grid' )
		);

		$html .= sprintf( '<div id="sc-post-post-source" class="rt-tab-content"%s>', $last_tab == "sc-post-post-source" ? ' style="display:block"' : '' );
		$html .= Fns::view( 'settings.post-source', $post, true );
		$html .= '</div>';

		$html .= sprintf( '<div id="sc-post-layout-settings" class="rt-tab-content"%s>', $last_tab == "sc-post-layout-settings" ? ' style="display:block"' : '' );
		$html .= Fns::view( 'settings.layout-settings', $post, true );
		$html .= '</div>';

		$html .= sprintf( '<div id="sc-settings" class="rt-tab-content"%s>', $last_tab == "sc-settings" ? ' style="display:block"' : '' );
		$html .= Fns::view( 'settings.sc-settings', $post, true );
		$html .= '</div>';

		$html .= sprintf( '<div id="sc-field-selection" class="rt-tab-content"%s>', $last_tab == "sc-field-selection" ? ' style="display:block"' : '' );
		$html .= Fns::view( 'settings.item-fields', $post, true );
		$html .= '</div>';

		$html .= sprintf( '<div id="sc-style" class="rt-tab-content"%s>', $last_tab == "sc-style" ? ' style="display:block"' : '' );
		$html .= Fns::view( 'settings.style', $post, true );
		$html .= '</div>';
		$html .= sprintf( '<input type="hidden" id="_tpg_last_active_tab" name="_tpg_last_active_tab"  value="%s"/>', $last_tab );
		$html .= '</div>';
		echo $html;
	}

	function save_post( $post_id, $post ) {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		if ( ! Fns::verifyNonce() ) {
			return $post_id;
		}

		if ( rtTPG()->post_type != $post->post_type ) {
			return $post_id;
		}

		$mates = Fns::rtAllOptionFields();
		foreach ( $mates as $metaKey => $field ) {
			$rValue = ! empty( $_REQUEST[ $metaKey ] ) ? $_REQUEST[ $metaKey ] : null;
			$value  = Fns::sanitize( $field, $rValue );
			if ( empty( $field['multiple'] ) ) {
				update_post_meta( $post_id, $metaKey, $value );
			} else {
				delete_post_meta( $post_id, $metaKey );
				if ( is_array( $value ) && ! empty( $value ) ) {
					foreach ( $value as $item ) {
						add_post_meta( $post_id, $metaKey, $item );
					}
				}
			}
		}

		$post_filter = ( isset( $_REQUEST['post_filter'] ) ? $_REQUEST['post_filter'] : array() );
		$advFilter   = Options::rtTPAdvanceFilters();
		foreach ( $advFilter['post_filter']['options'] as $filter => $fValue ) {
			if ( $filter == 'tpg_taxonomy' ) {
				delete_post_meta( $post_id, $filter );
				if ( ! empty( $_REQUEST[ $filter ] ) && is_array( $_REQUEST[ $filter ] ) ) {
					foreach ( $_REQUEST[ $filter ] as $tax ) {
						if ( in_array( $filter, $post_filter ) ) {
							add_post_meta( $post_id, $filter, trim( $tax ) );
						}
						delete_post_meta( $post_id, 'term_' . $tax );
						$tt = isset( $_REQUEST[ 'term_' . $tax ] ) ? $_REQUEST[ 'term_' . $tax ] : array();
						if ( is_array( $tt ) && ! empty( $tt ) && in_array( $filter, $post_filter ) ) {
							foreach ( $tt as $termID ) {
								add_post_meta( $post_id, 'term_' . $tax, trim( $termID ) );
							}
						}
						$tto = isset( $_REQUEST[ 'term_operator_' . $tax ] ) ? $_REQUEST[ 'term_operator_' . $tax ] : null;
						if ( $tto ) {
							update_post_meta( $post_id, 'term_operator_' . $tax, trim( $tto ) );
						}
					}
					$filterCount = isset( $_REQUEST[ $filter ] ) ? $_REQUEST[ $filter ] : array();
					$tr          = isset( $_REQUEST['taxonomy_relation'] ) ? $_REQUEST['taxonomy_relation'] : null;
					if ( count( $filterCount ) > 1 && $tr ) {
						update_post_meta( $post_id, 'taxonomy_relation', trim( $tr ) );
					} else {
						delete_post_meta( $post_id, 'taxonomy_relation' );
					}

				}
			} else if ( $filter == 'author' ) {
				delete_post_meta( $post_id, 'author' );
				$authors = isset( $_REQUEST['author'] ) ? $_REQUEST['author'] : array();
				if ( is_array( $authors ) && ! empty( $authors ) && in_array( 'author', $post_filter ) ) {
					foreach ( $authors as $authorID ) {
						add_post_meta( $post_id, 'author', trim( $authorID ) );
					}
				}
			} else if ( $filter == 'tpg_post_status' ) {
				delete_post_meta( $post_id, $filter );
				$statuses = isset( $_REQUEST[ $filter ] ) ? $_REQUEST[ $filter ] : array();
				if ( is_array( $statuses ) && ! empty( $statuses ) && in_array( $filter, $post_filter ) ) {
					foreach ( $statuses as $post_status ) {
						add_post_meta( $post_id, $filter, trim( $post_status ) );
					}
				}
			} else if ( $filter == 's' ) {
				delete_post_meta( $post_id, 's' );
				$s = isset( $_REQUEST['s'] ) ? $_REQUEST['s'] : null;
				if ( $s && in_array( 's', $post_filter ) ) {
					update_post_meta( $post_id, 's', sanitize_text_field( trim( $s ) ) );
				}
			} else if ( $filter == 'order' ) {
				if ( in_array( 'order', $post_filter ) ) {
					$order = isset( $_REQUEST['order'] ) ? $_REQUEST['order'] : null;
					if ( $order && in_array( 'order', $post_filter ) ) {
						update_post_meta( $post_id, 'order', sanitize_text_field( trim( $order ) ) );
					}
					$order_by = isset( $_REQUEST['order_by'] ) ? $_REQUEST['order_by'] : null;
					if ( $order_by && in_array( 'order', $post_filter ) ) {
						update_post_meta( $post_id, 'order_by', sanitize_text_field( trim( $order_by ) ) );
					}
					$tpg_meta_key = isset( $_REQUEST['tpg_meta_key'] ) ? $_REQUEST['tpg_meta_key'] : null;
					if ( in_array( $order_by, array_keys( Options::rtMetaKeyType() ) ) && $tpg_meta_key && in_array( 'order', $post_filter ) ) {
						update_post_meta( $post_id, 'tpg_meta_key', sanitize_text_field( trim( $tpg_meta_key ) ) );
					} else {
						delete_post_meta( $post_id, 'tpg_meta_key' );
					}
				} else {
					delete_post_meta( $post_id, 'order' );
					delete_post_meta( $post_id, 'tpg_meta_key' );
					delete_post_meta( $post_id, 'order_by' );
				}
			} else if ( $filter == 'date_range' ) {
				if ( in_array( 'date_range', $post_filter ) ) {
					$start = ! empty( $_REQUEST[ $filter . '_start' ] ) ? $_REQUEST[ $filter . '_start' ] : null;
					$end   = ! empty( $_REQUEST[ $filter . '_end' ] ) ? $_REQUEST[ $filter . '_end' ] : null;
					update_post_meta( $post_id, $filter . '_start', trim( $start ) );
					update_post_meta( $post_id, $filter . '_end', trim( $end ) );
				} else {
					delete_post_meta( $post_id, $filter . '_start' );
					delete_post_meta( $post_id, $filter . '_end' );
				}
			}
		}

		// Extra css

		$extraFields = Options::extraStyle();
		$extraTypes  = array( 'color', 'size', 'weight', 'alignment' );

		foreach ( $extraFields as $key => $title ) {
			foreach ( $extraTypes as $type ) {
				$newKew = $key . "_{$type}";
				if ( isset( $_REQUEST[ $newKew ] ) ) {
					$value = trim( $_REQUEST[ $newKew ] );
					update_post_meta( $post_id, $newKew, $value );
				}
			}
		}


		if ( isset( $_POST['_tpg_last_active_tab'] ) && $active_tab = trim( $_POST['_tpg_last_active_tab'] ) ) {
			update_post_meta( $post_id, '_tpg_last_active_tab', $_POST['_tpg_last_active_tab'] );
		}

	} // end function
}