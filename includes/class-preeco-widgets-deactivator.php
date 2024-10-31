<?php

/**
 * @since      1.0.0
 * @package    Preeco_Widgets
 * @subpackage Preeco_Widgets/includes
 * @author     preeco GmbH <info@preeco.de>
 */
class Preeco_Widgets_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		wp_clear_scheduled_hook( 'preeco_widgets_cache_all' );
	}

}
