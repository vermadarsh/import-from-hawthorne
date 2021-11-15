<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/vermadarsh/
 * @since             1.0.0
 * @package           Import_From_Hawthorne
 *
 * @wordpress-plugin
 * Plugin Name:       Import from Hawthorne
 * Plugin URI:        https://github.com/vermadarsh/import-from-hawthorne/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Adarsh Verma
 * Author URI:        https://github.com/vermadarsh/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       import-from-hawthorne
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
define( 'IMPORT_FROM_HAWTHORNE_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-import-from-hawthorne-activator.php
 */
function activate_import_from_hawthorne() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-import-from-hawthorne-activator.php';
	Import_From_Hawthorne_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-import-from-hawthorne-deactivator.php
 */
function deactivate_import_from_hawthorne() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-import-from-hawthorne-deactivator.php';
	Import_From_Hawthorne_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_import_from_hawthorne' );
register_deactivation_hook( __FILE__, 'deactivate_import_from_hawthorne' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-import-from-hawthorne.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_import_from_hawthorne() {

	$plugin = new Import_From_Hawthorne();
	$plugin->run();

}
run_import_from_hawthorne();
