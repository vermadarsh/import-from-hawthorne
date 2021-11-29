<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/vermadarsh/
 * @since      1.0.0
 *
 * @package    Import_From_Hawthorne
 * @subpackage Import_From_Hawthorne/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Import_From_Hawthorne
 * @subpackage Import_From_Hawthorne/includes
 * @author     Adarsh Verma <adarsh@cmsminds.com>
 */
class Import_From_Hawthorne {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power the plugin.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var Import_From_Hawthorne_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->version     = ( defined( 'HAWTHORNE_PLUGIN_VERSION' ) ) ? HAWTHORNE_PLUGIN_VERSION : '1.0.0';
		$this->plugin_name = 'import-from-hawthorne';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Import_From_Hawthorne_Loader. Orchestrates the hooks of the plugin.
	 * - Import_From_Hawthorne_i18n. Defines internationalization functionality.
	 * - Import_From_Hawthorne_Admin. Defines all hooks for the admin area.
	 * - Import_From_Hawthorne_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		// The class responsible for orchestrating the actions and filters of the core plugin.
		require_once 'class-import-from-hawthorne-loader.php';

		// The class responsible for defining internationalization functionality of the plugin.
		require_once 'class-import-from-hawthorne-i18n.php';

		// The file is responsible for defining all custom functions.
		require_once HAWTHORNE_PLUGIN_PATH . 'includes/import-from-hawthorne-functions.php';

		// The file is responsible for defining custom email notifications.
		require_once HAWTHORNE_PLUGIN_PATH . 'includes/emails/class-import-from-hawthorne-emails-manager.php';

		// The class responsible for defining all actions that occur in the admin area.
		require_once HAWTHORNE_PLUGIN_PATH . 'admin/class-import-from-hawthorne-admin.php';

		// The class responsible for defining all actions that occur in the public-facing side of the site.
		require_once HAWTHORNE_PLUGIN_PATH . 'public/class-import-from-hawthorne-public.php';

		$this->loader = new Import_From_Hawthorne_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Import_From_Hawthorne_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Import_From_Hawthorne_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Import_From_Hawthorne_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'hawthorne_admin_enqueue_scripts_callback' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'hawthorne_admin_menu_callback' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'hawthorne_admin_init_callback' );
		// $this->loader->add_action( 'admin_init', $plugin_admin, 'hawthorne_hawthorne_import_products_cron_callback' );
		$this->loader->add_action( 'wp_ajax_kickoff_products_import', $plugin_admin, 'hawthorne_kickoff_products_import_callback' );
		$this->loader->add_filter( 'parent_file', $plugin_admin, 'hawthorne_parent_file_callback' );
		$this->loader->add_action( 'woocommerce_product_options_dimensions', $plugin_admin, 'hawthorne_woocommerce_product_options_dimensions_callback' );
		$this->loader->add_action( 'woocommerce_product_options_sku', $plugin_admin, 'hawthorne_woocommerce_product_options_sku_callback' );
		$this->loader->add_action( 'woocommerce_process_product_meta', $plugin_admin, 'hawthorne_woocommerce_process_product_meta_callback' );
		$this->loader->add_action( 'hawthorne_import_products_cron', $plugin_admin, 'hawthorne_hawthorne_import_products_cron_callback' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_public = new Import_From_Hawthorne_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'hawthorne_wp_enqueue_scripts_callback' );
		$this->loader->add_action( 'init', $plugin_public, 'hawthorne_init_callback' );
		$this->loader->add_action( 'woocommerce_proceed_to_checkout', $plugin_public, 'hawthorne_woocommerce_proceed_to_checkout_callback', 30 );
		$this->loader->add_action( 'wp_footer', $plugin_public, 'hawthorne_wp_footer_callback' );
		$this->loader->add_action( 'wp_ajax_send_cart', $plugin_public, 'hawthorne_send_cart_callback' );
		$this->loader->add_action( 'wp_ajax_nopriv_send_cart', $plugin_public, 'hawthorne_send_cart_callback' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Import_From_Hawthorne_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
