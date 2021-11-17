<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/vermadarsh/
 * @since      1.0.0
 *
 * @package    Import_From_Hawthorne
 * @subpackage Import_From_Hawthorne/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Import_From_Hawthorne
 * @subpackage Import_From_Hawthorne/admin
 * @author     Adarsh Verma <adarsh@cmsminds.com>
 */
class Import_From_Hawthorne_Admin {
	/**
	 * The ID of this plugin.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * The array of plugin settings tabs.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var array $plugin_settings_tabs Plugin settings tabs.
	 */
	private $plugin_settings_tabs;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name          = $plugin_name;
		$this->version              = $version;
		$this->plugin_settings_tabs = array();
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function hawthorne_admin_enqueue_scripts_callback() {
		// Custom admin style.
		wp_enqueue_style(
			$this->plugin_name,
			HAWTHORNE_PLUGIN_URL . 'admin/css/import-from-hawthorne-admin.css',
			array(),
			filemtime( HAWTHORNE_PLUGIN_PATH . 'admin/css/import-from-hawthorne-admin.css' ),
			'all'
		);

		// Custom admin script.
		wp_enqueue_script(
			$this->plugin_name,
			HAWTHORNE_PLUGIN_URL . 'admin/js/import-from-hawthorne-admin.js',
			array( 'jquery' ),
			filemtime( HAWTHORNE_PLUGIN_PATH . 'admin/js/import-from-hawthorne-admin.js' ),
			true
		);

		// Localize custom admin script.
		wp_localize_script(
			$this->plugin_name,
			'Hawthorne_Admin_Script_Vars',
			array(
				'ajaxurl'                    => admin_url( 'admin-ajax.php' ),
				'product_import_button_text' => __( 'Import From Hawthorne', 'import-from-hawthorne' ),
			)
		);
	}

	/**
	 * Add custom settings page for the plugin.
	 *
	 * @version 1.0.0
	 */
	public function hawthorne_admin_menu_callback() {
		// Add submenu to the WooCommerce menu.
		add_submenu_page(
			'woocommerce',
			__( 'Hawthorne Integration', 'import-from-hawthorne' ),
			__( 'Hawthorne Integration', 'import-from-hawthorne' ),
			'manage_options',
			'import-from-hawthorne',
			array( $this, 'hawthorne_admin_settings_callback' )
		);
	}

	/**
	 * Template for the custom admin settings page.
	 *
	 * @param 1.0.0
	 */
	public function hawthorne_admin_settings_callback() {
		$tab = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_STRING );
		$tab = ( ! is_null( $tab ) ) ? $tab : 'hawthorne-general';
		?>
		<div class="wrap">
			<div class="hawthorne-plugin-settings-header">
				<h1><?php esc_html_e( 'Import from Hawthorne', 'import-from-hawthorne' ); ?></h1>
				<p><?php esc_html_e( 'This plugin lets you import ecommerce content from Hawthorne.', 'import-from-hawthorne' ); ?></p>
			</div>
			<div class="hawthorne-plugin-settings-content">
				<div class="hawthorne-plugin-settings-tabs"><?php $this->hawthorne_generate_plugin_settings_tabs(); ?></div>
				<div class="hawthorne-plugin-settings-content">
					<form action="" method="POST" id="<?php echo esc_attr( $tab ); ?>-settings-form" enctype="multipart/form-data"><?php do_settings_sections( $tab ); ?></form>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Plugin settings tabs.
	 *
	 * @version 1.0.0
	 */
	public function hawthorne_generate_plugin_settings_tabs() {
		$tab = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_STRING );
		$tab = ( ! is_null( $tab ) ) ? $tab : 'hawthorne-general';
		echo '<h2 class="nav-tab-wrapper">';

		// Iterate through the tabs.
		foreach ( $this->plugin_settings_tabs as $tab_key => $tab_caption ) {
			$active = ( $tab === $tab_key ) ? 'nav-tab-active' : '';
			echo '<a class="nav-tab ' . $active . '" href="?page=import-from-hawthorne&tab=' . $tab_key . '">' . $tab_caption . '</a>';
		}

		echo '</h2>';
	}

	/**
	 * Plugin settings tabs and settings templates.
	 *
	 * @version 1.0.0
	 */
	public function hawthorne_plugin_settings_templates_callback() {
		// General settings.
		$this->plugin_settings_tabs['hawthorne-general'] = __( 'General', 'import-from-hawthorne' );
		register_setting( 'hawthorne-general', 'hawthorne-general' );
		add_settings_section( 'tab-general', ' ', array( &$this, 'hawthorne_plugin_general_settings_callback' ), 'hawthorne-general' );

		// Test API settings.
		$this->plugin_settings_tabs['hawthorne-api-connection'] = __( 'API Connection', 'import-from-hawthorne' );
		register_setting( 'hawthorne-api-connection', 'hawthorne-api-connection' );
		add_settings_section( 'tab-api-connection', ' ', array( &$this, 'hawthorne_plugin_api_connection_settings_callback' ), 'hawthorne-api-connection' );
	}

	/**
	 * Plugin general settings template.
	 *
	 * @param 1.0.0
	 */
	public function hawthorne_plugin_general_settings_callback() {
		include_once 'templates/settings/general.php'; // Include the general settings template.
	}

	/**
	 * Plugin general settings template.
	 *
	 * @param 1.0.0
	 */
	public function hawthorne_plugin_api_connection_settings_callback() {
		include_once 'templates/settings/api-connection.php'; // Include the API connection settings template.
	}
	/**
	 * AJAX to import products from Hawthorne.
	 *
	 * @since 1.0.0
	 */
	public function hawthorne_import_products_callback() {
		$action = filter_input( INPUT_POST, 'action', FILTER_SANITIZE_STRING );

		// Exit, if the action mismatches.
		if ( empty( $action ) || 'import_products' !== $action ) {
			echo 0;
			wp_die();
		}

		// Fetch products.
		$products = hawthorne_fetch_products();
		debug( $products );
		die;
		
		$response = array(
			'code' => $api_response_message,
			'html' => ersrv_get_amenity_html( array() ),
		);
		wp_send_json_success( $response );
		wp_die();
	}
}
