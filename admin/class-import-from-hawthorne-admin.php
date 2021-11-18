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
				'product_import_admin_url'   => admin_url( 'admin.php?page=import-products-from-hawthorne' ),
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

		/**
		 * Add submenu to the WooCommerce menu.
		 * This is for showing the template for the products import.
		 */
		add_submenu_page(
			null,
			__( 'Import Products', 'import-from-hawthorne' ),
			__( 'Import Products from Hawthorne', 'import-from-hawthorne' ),
			'manage_options',
			'import-products-from-hawthorne',
			array( $this, 'hawthorne_import_products_from_hawthorne_callback' )
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
	 * Products import with progressbar template.
	 *
	 * @param 1.0.0
	 */
	public function hawthorne_import_products_from_hawthorne_callback() {
		include 'templates/pages/import-products.php'; // Include the template for importing products - progressbar.
	}

	/**
	 * AJAX to kickoff the products import.
	 *
	 * @since 1.0.0
	 */
	public function hawthorne_kickoff_products_import_callback() {
		$action = filter_input( INPUT_POST, 'action', FILTER_SANITIZE_STRING );

		// Exit, if the action mismatches.
		if ( empty( $action ) || 'kickoff_products_import' !== $action ) {
			echo 0;
			wp_die();
		}

		// Posted data.
		$page                   = (int) filter_input( INPUT_POST, 'page', FILTER_SANITIZE_NUMBER_INT );
		$new_products_added     = (int) filter_input( INPUT_POST, 'new_products_added', FILTER_SANITIZE_NUMBER_INT );
		$old_products_updated   = (int) filter_input( INPUT_POST, 'old_products_updated', FILTER_SANITIZE_NUMBER_INT );
		$products_import_failed = (int) filter_input( INPUT_POST, 'products_import_failed', FILTER_SANITIZE_NUMBER_INT );

		// Fetch products.
		$products       = get_transient( 'hawthorne_product_items' );
		$products       = json_decode( $products, true ); // Decode the JSON.
		$products_count = count( $products );
		$products       = array_chunk( $products, 10, true ); // Divide the complete data into 10 products.
		$chunk_index    = $page - 1;
		$chunk          = ( array_key_exists( $chunk_index, $products ) ) ? $products[ $chunk_index ] : array();

		// Return, if the chunk is empty, means all the products are imported.
		if ( empty( $chunk ) || ! is_array( $chunk ) ) {
			delete_transient( 'hawthorne_product_items' ); // Delete the transient now.

			// Sent the final response.
			$response = array(
				'code'                   => 'products-imported',
				'products_import_failed' => $products_import_failed, // Count of the products that failed to import.
				'new_products_added'     => $new_products_added, // Count of the products that failed to import.
				'old_products_updated'   => $old_products_updated, // Count of the products that failed to import.
			);
			wp_send_json_success( $response );
			wp_die();
		}

		// Iterate through the loop to import the products.
		foreach ( $chunk as $part ) {
			$product_title = ( ! empty( $part['Name'] ) ) ? $part['Name'] : '';

			// Skip the update if the product title is missing.
			if ( empty( $product_title ) ) {
				$products_import_failed++; // Increase the count of products import.
				continue;
			}

			// Check if the product exists with the name.
			$product_exists = get_page_by_title( $product_title, OBJECT, 'product' );

			// If the product doesn't exist.
			if ( is_null( $product_exists ) ) {
				hawthorne_create_product( $part ); // Create product.
				$new_products_added++; // Increase the counter of new product created.
			} else {
				hawthorne_update_product( $product_exists->ID, $part ); // Update product.
				$old_products_updated++; // Increase the counter of old product updated.
			}
		}

		// Send the AJAX response now.
		$response = array(
			'code'                   => 'products-import-in-progress',
			'percent'                => ( ( $page * 10 ) / $products_count ) * 100, // Percent of the products imported.
			'total'                  => $products_count, // Count of the total products.
			'imported'               => ( $page * 10 ), // These are the count of products that are imported.
			'products_import_failed' => $products_import_failed, // Count of the products that failed to import.
			'new_products_added'     => $new_products_added, // Count of the products that failed to import.
			'old_products_updated'   => $old_products_updated, // Count of the products that failed to import.
		);
		wp_send_json_success( $response );
		wp_die();
	}

	/**
	 * Highlight a menu in the admin menubar for the import products page.
	 *
	 * @param string $submenu_file Submenu file.
	 * @return string
	 * @since 1.0.0
	 */
	public function hawthorne_submenu_file_callback( $submenu_file ) {
		$screen = get_current_screen(); // Get the current screen.

		// If the current screen is of the import products.
		if (  ! empty( $screen->id ) && 'admin_page_import-products-from-hawthorne' === $screen->id ) {
			$submenu_file = 'woocommerce_page_wc-admin'; // Make "WooCommerce" menu as the parent menu.
			// $submenu_file = 'edit-product'; // Make "Products" menu as the parent menu.

			$submenu_file = 'import-from-hawthorne';
		}

		return $submenu_file;
	}
}
