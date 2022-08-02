<?php


namespace RT\ThePostGrid\Helpers;


class Install {

	public static function activate() {
		self::insertDefaultData();
		add_option( 'rttpg_activation_redirect', true );
	}

	public static function deactivate() {
		update_option( 'tpg_flush_rewrite_rules', 0 );
	}

	public static function insertDefaultData() {
		update_option( rtTPG()->options['installed_version'], RT_THE_POST_GRID_VERSION );
		if ( ! get_option( rtTPG()->options['settings'] ) ) {
			update_option( rtTPG()->options['settings'], rtTPG()->defaultSettings );
		}

		if ( get_option( 'elementor_experiment-e_optimized_assets_loading' ) ) {
			update_option( 'elementor_experiment-e_optimized_assets_loading', 'default' );
		}
		if ( get_option( 'elementor_experiment-e_optimized_css_loading' ) ) {
			update_option( 'elementor_experiment-e_optimized_css_loading', 'default' );
		}
	}

}