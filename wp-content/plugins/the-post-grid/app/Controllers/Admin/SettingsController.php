<?php


namespace RT\ThePostGrid\Controllers\Admin;


use RT\ThePostGrid\Helpers\Fns;

class SettingsController {

	private $sc_tag = 'rt_tpg_scg';

	public function init() {
		add_action( 'admin_menu', [ &$this, 'register' ] );
		add_filter( 'plugin_action_links_' . RT_THE_POST_GRID_PLUGIN_ACTIVE_FILE_NAME, [ &$this, 'marketing' ] );
		add_action( 'admin_enqueue_scripts', [ &$this, 'settings_admin_enqueue_scripts' ] );
		add_action( 'wp_print_styles', [ &$this, 'tpg_dequeue_unnecessary_styles' ], 99 );
		add_action( 'admin_footer', [ $this, 'pro_alert_html' ] );
		add_action( 'admin_head', [ $this, 'admin_head' ] );
	}

	/**
	 * admin_head
	 * calls your functions into the correct filters
	 *
	 * @return void
	 */
	function admin_head() {
		// check user permissions
		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}
		// check if WYSIWYG is enabled
		if ( 'true' == get_user_option( 'rich_editing' ) ) {
			add_filter( 'mce_external_plugins', [ $this, 'mce_external_plugins' ] );
			add_filter( 'mce_buttons', [ $this, 'mce_buttons' ] );
			echo "<style>";
			echo "i.mce-i-rt_tpg_scg{";
			echo "background: url('" . rtTPG()->get_assets_uri( 'images/icon-20x20.png' ) . "');";
			echo "}";
			echo "</style>";
		}
	}

	/**
	 * mce_external_plugins
	 * Adds our tinymce plugin
	 *
	 * @param  array  $plugin_array
	 *
	 * @return array
	 */
	function mce_external_plugins( $plugin_array ) {
		$plugin_array[ $this->sc_tag ] = rtTPG()->get_assets_uri( 'js/mce-button.js' );

		return $plugin_array;
	}

	/**
	 * mce_buttons
	 * Adds our tinymce button
	 *
	 * @param  array  $buttons
	 *
	 * @return array
	 */
	function mce_buttons( $buttons ) {
		array_push( $buttons, $this->sc_tag );

		return $buttons;
	}

	public function pro_alert_html() {
		global $typenow;

		if ( rtTPG()->hasPro() ) {
			return;
		}

		if ( ( isset( $_GET['page'] ) && $_GET['page'] != 'rttpg_settings' ) || $typenow != rtTPG()->post_type ) {
			return;
		}

		$html = '';
		$html .= '<div class="rt-document-box rt-alert rt-pro-alert">
                <div class="rt-box-icon"><i class="dashicons dashicons-lock"></i></div>
                <div class="rt-box-content">
                    <h3 class="rt-box-title">' . esc_html__( 'Pro field alert!', 'the-post-grid' ) . '</h3>
                    <p><span></span>' . esc_html__( 'Sorry! this is a pro field. To use this field, you need to use pro plugin.', 'the-post-grid' ) . '</p>
                    <a href="https://www.radiustheme.com/downloads/the-post-grid-pro-for-wordpress/" target="_blank" class="rt-admin-btn">' . esc_html__( "Upgrade to pro",
				"the-post-grid" ) . '</a>
                    <a href="#" target="_blank" class="rt-alert-close rt-pro-alert-close">x</a>
                </div>
            </div>';
		echo $html;
	}

	public function tpg_dequeue_unnecessary_styles() {
		$settings = get_option( rtTPG()->options['settings'] );

		if ( isset( $settings['tpg_skip_fa'] ) ) {
			wp_dequeue_style( 'rt-fontawsome' );
			wp_deregister_style( 'rt-fontawsome' );
		}
	}

	public function settings_admin_enqueue_scripts() {
		global $pagenow, $typenow;

		// validate page
		if ( ! in_array( $pagenow, [ 'edit.php' ] ) ) {
			return;
		}
		if ( $typenow != rtTPG()->post_type ) {
			return;
		}

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'rt-tpg-admin' );

		// styles
		wp_enqueue_style( 'rt-tpg-admin' );

		$nonce = wp_create_nonce( rtTPG()->nonceText() );
		wp_localize_script( 'rt-tpg-admin',
			'rttpg',
			[
				'nonceID' => rtTPG()->nonceId(),
				'nonce'   => $nonce,
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
			] );
	}

	function marketing( $links ) {
		$links[] = '<a target="_blank" href="' . esc_url( 'https://www.radiustheme.com/demo/plugins/the-post-grid/' ) . '">Demo</a>';
		$links[] = '<a target="_blank" href="' . esc_url( 'https://www.radiustheme.com/docs/the-post-grid/' ) . '">Documentation</a>';
		if ( ! rtTPG()->hasPro() ) {
			$links[] = '<a target="_blank" style="color: #39b54a;font-weight: 700;" href="' . esc_url( 'https://www.radiustheme.com/downloads/the-post-grid-pro-for-wordpress/' )
			           . '">Get Pro</a>';
		}

		return $links;
	}

	public function register() {
		add_submenu_page(
			'edit.php?post_type=' . rtTPG()->post_type,
			__( 'Settings', 'the-post-grid' ),
			__( 'Settings', "the-post-grid" ),
			'administrator',
			'rttpg_settings',
			[ &$this, 'settings' ] );

		add_submenu_page( 'edit.php?post_type=' . rtTPG()->post_type,
			__( 'Get Help' ),
			__( 'Get Help', "the-post-grid" ),
			'administrator',
			'rttpg_get_help',
			[
				$this,
				'get_help',
			] );
	}

	function get_help() {
		Fns::view('page.help');
	}

	public function settings() {
		Fns::view( 'settings.settings' );
	}

}