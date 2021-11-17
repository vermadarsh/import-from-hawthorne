<?php

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/vermadarsh/
 * @since      1.0.0
 *
 * @package    Import_From_Hawthorne
 * @subpackage Import_From_Hawthorne/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Import_From_Hawthorne
 * @subpackage Import_From_Hawthorne/includes
 * @author     Adarsh Verma <adarsh@cmsminds.com>
 */
class Import_From_Hawthorne_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		// Create a log directory within the WordPress uploads directory.
		$uploads     = wp_upload_dir();
		$uploads_dir = $uploads['basedir'];
		$uploads_dir = "{$uploads_dir}/hawthorne-import-log/";

		if ( ! file_exists( $uploads_dir ) ) {
			mkdir( $uploads_dir, 0755, true );
		}
	}
}
