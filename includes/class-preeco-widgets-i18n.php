<?php

/**
 * @since      1.0.0
 * @package    Preeco_Widgets
 * @subpackage Preeco_Widgets/includes
 * @author     preeco GmbH <info@preeco.de>
 */
class Preeco_Widgets_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'preeco_widgets',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}


}
