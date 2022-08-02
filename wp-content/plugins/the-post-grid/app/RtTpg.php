<?php

use RT\ThePostGrid\Controllers\Admin\AdminAjaxController;
use RT\ThePostGrid\Controllers\Admin\MetaController;
use RT\ThePostGrid\Controllers\Admin\NoticeController;
use RT\ThePostGrid\Controllers\Admin\PostTypeController;
use RT\ThePostGrid\Controllers\Admin\SettingsController;
use RT\ThePostGrid\Controllers\AjaxController;
use RT\ThePostGrid\Controllers\ElementorController;
use RT\ThePostGrid\Controllers\GutenBergController;
use RT\ThePostGrid\Controllers\ScriptController;
use RT\ThePostGrid\Controllers\ShortcodeController;
use RT\ThePostGrid\Controllers\Hooks\FilterHooks;
use RT\ThePostGrid\Controllers\Hooks\ActionHooks;
use RT\ThePostGrid\Controllers\WidgetController;
use RT\ThePostGrid\Helpers\Install;
use RT\ThePostGrid\Controllers\Admin\UpgradeController;

require_once __DIR__ . './../vendor/autoload.php';

if ( ! class_exists( RtTpg::class ) ) {
	final class RtTpg {

		public $post_type = "rttpg";
		public $options = [
			'settings'          => 'rt_the_post_grid_settings',
			'version'           => RT_THE_POST_GRID_VERSION,
			'installed_version' => 'rt_the_post_grid_current_version',
			'slug'              => RT_THE_POST_GRID_PLUGIN_SLUG,
		];
		public $defaultSettings = [
			'tpg_block_type'     => 'default',
			'popup_fields'       => [
				'title',
				'feature_img',
				'content',
				'post_date',
				'author',
				'categories',
				'tags',
				'social_share',
			],
			'social_share_items' => [
				'facebook',
				'twitter',
				'linkedin',
			],
		];

		protected static $_instance;

		/**
		 * Store the singleton object.
		 */
		private static $singleton = false;

		/**
		 * Create an inaccessible constructor.
		 */
		private function __construct() {
			$this->__init();
		}

		/**
		 * Fetch an instance of the class.
		 */
		final public static function getInstance() {
			if ( self::$singleton === false ) {
				self::$singleton = new self();
			}

			return self::$singleton;
		}


		protected function __init() {

			$settings = get_option( $this->options['settings'] );

			new UpgradeController();
			new PostTypeController();
			new AjaxController();
			new ScriptController();
			new WidgetController();

			if ( is_admin() ) {
				new AdminAjaxController();
				new NoticeController();
				new MetaController();
			}

			if ( ! isset( $settings['tpg_block_type'] ) || in_array( $settings['tpg_block_type'], [ 'default',
					'shortcode'
				] ) ) {
				new ShortcodeController();
				new GutenBergController();
			}

			FilterHooks::init();
			ActionHooks::init();

			( new SettingsController() )->init();

			if ( ! isset( $settings['tpg_block_type'] ) || in_array( $settings['tpg_block_type'], [ 'default',
					'elementor'
				] ) ) {
				new ElementorController();
			}


			$this->load_hooks();
		}

		private function load_hooks() {
			register_activation_hook( RT_THE_POST_GRID_PLUGIN_FILE, [ Install::class, 'activate' ] );
			register_deactivation_hook( RT_THE_POST_GRID_PLUGIN_FILE, [ Install::class, 'deactivate' ] );

			add_action( 'plugins_loaded', [ $this, 'on_plugins_loaded' ], - 1 );
			add_action( 'init', [ &$this, 'init_hooks' ], 0 );
			//add_action( 'init', [ ShortcodeController::class, 'init' ] ); // Init ShortCode.
			add_filter( 'wp_calculate_image_srcset', '__return_false' );
		}

		public function init_hooks() {
			do_action( 'rttpg_before_init', $this );

			$this->load_language();
		}

		public function load_language() {
			do_action( 'rttpg_set_local', null );
			$locale = determine_locale();
			$locale = apply_filters( 'plugin_locale', $locale, 'the-post-grid' );
			unload_textdomain( 'the-post-grid' );
			load_textdomain( 'the-post-grid', WP_LANG_DIR . '/the-post-grid/the-post-grid-' . $locale . '.mo' );
			load_plugin_textdomain( 'the-post-grid', false, plugin_basename( dirname( RT_THE_POST_GRID_PLUGIN_FILE ) ) . '/languages' );
		}

		public function on_plugins_loaded() {
			do_action( 'rttpg_loaded', $this );
		}

		/**
		 * Get the plugin path.
		 *
		 * @return string
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( RT_THE_POST_GRID_PLUGIN_FILE ) );
		}

		public function plugin_template_path() {
			$plugin_template = $this->plugin_path() . '/templates/';

			return apply_filters( 'tlp_tpg_template_path', $plugin_template );
		}

		public function default_template_path() {
			return apply_filters( 'rttpg_default_template_path', untrailingslashit( plugin_dir_path( RT_THE_POST_GRID_PLUGIN_FILE ) ) );
		}

		public static function nonceText() {
			return "rttpg_nonce_secret";
		}

		public static function nonceId() {
			return "rttpg_nonce";
		}

		/**
		 * @param $file
		 *
		 * @return string
		 */
		public function get_assets_uri( $file ) {
			$file = ltrim( $file, '/' );

			return trailingslashit( RT_THE_POST_GRID_PLUGIN_URL . '/assets' ) . $file;
		}

		/**
		 * @param $file
		 *
		 * @return string
		 */
		public function tpg_can_be_rtl( $file ) {
			$file = ltrim( str_replace( '.css', '', $file ), '/' );

			if ( is_rtl() ) {
				$file .= '.rtl';
			}

			return trailingslashit( RT_THE_POST_GRID_PLUGIN_URL . '/assets' ) . $file . '.min.css';
		}

		/**
		 * Get the template path.
		 *
		 * @return string
		 */
		public function get_template_path() {
			return apply_filters( 'rttpg_template_path', 'the-post-grid/' );
		}


		public function hasPro() {
			return class_exists( 'RtTpgPro' ) || class_exists( 'rtTPGP' );
		}

	}

	function rtTPG() {
		return rtTPG::getInstance();
	}

	rtTPG();
}


