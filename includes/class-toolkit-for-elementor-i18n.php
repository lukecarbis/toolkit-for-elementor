<?php

/**
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 * @link       https://toolkitforelementor.com
 * @since      1.0.0
 * @package    Toolkit_For_Elementor
 * @subpackage Toolkit_For_Elementor/includes
 * @author     ToolKit For Elementor <support@toolkitforelementor.com>
 */
class Toolkit_For_Elementor_i18n {

	/**
	 * Load the plugin text domain for translation.
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'toolkit-for-elementor',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}
}
