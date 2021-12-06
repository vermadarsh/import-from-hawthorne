<?php
/**
 * Fired during plugin deactivation
 *
 * @link       https://github.com/vermadarsh/
 * @since      1.0.0
 *
 * @package    Import_From_Hawthorne
 * @subpackage Import_From_Hawthorne/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Import_From_Hawthorne
 * @subpackage Import_From_Hawthorne/includes
 * @author     Adarsh Verma <adarsh@cmsminds.com>
 */
class Import_From_Hawthorne_Deactivator {
	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		// Clear the scheduled crons now.
		if ( wp_next_scheduled( 'hawthorne_import_products_cron' ) ) {
			wp_clear_scheduled_hook( 'hawthorne_import_products_cron' );
		}
	}
}
