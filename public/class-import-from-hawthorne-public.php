<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/vermadarsh/
 * @since      1.0.0
 *
 * @package    Import_From_Hawthorne
 * @subpackage Import_From_Hawthorne/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Import_From_Hawthorne
 * @subpackage Import_From_Hawthorne/public
 * @author     Adarsh Verma <adarsh@cmsminds.com>
 */
class Import_From_Hawthorne_Public {
	/**
	 * The version of this plugin.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function hawthorne_wp_enqueue_scripts_callback() {
		// Custom public style.
		wp_enqueue_style(
			$this->plugin_name,
			HAWTHORNE_PLUGIN_URL . 'public/css/import-from-hawthorne-public.css',
			array(),
			filemtime( HAWTHORNE_PLUGIN_PATH . 'public/css/import-from-hawthorne-public.css' ),
			'all'
		);

		// Custom public script.
		wp_enqueue_script(
			$this->plugin_name,
			HAWTHORNE_PLUGIN_URL . 'public/js/import-from-hawthorne-public.js',
			array( 'jquery' ),
			filemtime( HAWTHORNE_PLUGIN_PATH . 'public/js/import-from-hawthorne-public.js' ),
			true
		);

		// Localize custom public script.
		wp_localize_script(
			$this->plugin_name,
			'Hawthorne_Public_Script_Vars',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
			)
		);
	}
}
