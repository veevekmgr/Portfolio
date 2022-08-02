<?php

namespace RT\ThePostGrid\Controllers\Admin;

class UpgradeController {

	public static $compare_version;

	public function __construct() {
		self::$compare_version = '5.1.0';
		if ( ! self::check_plugin_version() ) {
			add_filter( 'plugin_row_meta', [ $this, 'show_update_notification' ], 10, 2 );
			$this->version_notice();
		}
	}

	public static function check_plugin_version() {
		$tpg_version = self::get_pro_plugin_info( 'Version' );
		if ( version_compare( $tpg_version, self::$compare_version, '<' ) ) {
			return false;
		}

		return true;
	}

	public function version_notice() {
		if ( class_exists( 'RtTpgPro' ) || class_exists( 'rtTPGP' ) ) {
			add_action( 'admin_notices',
				function () {
					$class    = 'notice notice-error';
					$text     = esc_html__( 'The Post Grid Pro', 'the-post-grid' );
					$link_pro = 'https://www.radiustheme.com/downloads/the-post-grid-pro-for-wordpress/';

					printf( '<div class="%1$s"><p><a target="_blank" href="%2$s"><strong>The Post Grid Pro</strong></a> is not working properly, You need to update <strong>%3$s</strong> version to 5.1.0 or more to get the pro features.</p></div>',
						$class,
						$link_pro,
						$text );
				} );
		}
	}

	/**
	 * Get TPG Pro Plugin Info
	 *
	 * @param $parameter
	 *
	 * @return string
	 */
	public static function get_pro_plugin_info( $parameter ): string {
		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		$tpg_free_path = WP_PLUGIN_DIR . '/the-post-grid-pro/the-post-grid-pro.php';

		if ( file_exists( $tpg_free_path ) ) {
			$plugin_path = get_plugin_data( $tpg_free_path );

			if ( isset( $plugin_path[ $parameter ] ) ) {
				return $plugin_path[ $parameter ];
			}
		}

		return '';
	}

	public function show_update_notification( $links, $file ) {
		if ( $file == 'the-post-grid-pro/the-post-grid-pro.php' ) {
			$row_meta['tpg_update'] = '<span style="color: red">The Plugin is not compatible with the post grid free. Please update the plugin to ' . self::$compare_version . ' or more otherwise it will not activate.</span>';

			return array_merge( $links, $row_meta );
		}

		return (array) $links;
	}
}
