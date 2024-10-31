<?php

/**
 * @link              https://www.preeco.de
 * @since             1.0.0
 * @package           Preeco_Widgets
 *
 * @wordpress-plugin
 * Plugin Name:       preeco Widgets
 * Plugin URI:        https://preeco.de
 * Description:       Easy way to include preeco widgets
 * Version:           1.1.0
 * Author:            preeco GmbH
 * Author URI:        https://www.preeco.de
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       preeco_widgets
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PREECO_WIDGETS_VERSION', '1.1.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-preeco-widgets-activator.php
 */
function activate_preeco_widgets() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-preeco-widgets-activator.php';
	Preeco_Widgets_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-preeco-widgets-deactivator.php
 */
function deactivate_preeco_widgets() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-preeco-widgets-deactivator.php';
	Preeco_Widgets_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_preeco_widgets' );
register_deactivation_hook( __FILE__, 'deactivate_preeco_widgets' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-preeco-widgets.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_preeco_widgets() {

	$plugin = new Preeco_Widgets();
	$plugin->run();

}
run_preeco_widgets();
