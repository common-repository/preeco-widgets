<?php

/**
 * @since      1.0.0
 * @package    Preeco_Widgets
 * @subpackage Preeco_Widgets/includes
 * @author     preeco GmbH <info@preeco.de>
 */
class Preeco_Widgets_Activator {

	/**
	 * @since    1.0.0
	 */
	public static function activate() {
		$plugin_admin = new Preeco_Widgets_Admin( 'preeco-widgets', PREECO_WIDGETS_VERSION );
		$plugin_admin->update_cron_scheduler();
	}

}
