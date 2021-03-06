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
		$post_type          = filter_input( INPUT_GET, 'post_type', FILTER_SANITIZE_STRING );
		$show_import_button = 'yes';

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

		// Check if there are settings saved and we need to display the import button.
		if ( ! is_null( $post_type ) && 'product' === $post_type ) {
			// Fetch the settings.
			$api_key            = hawthorne_get_plugin_settings( 'api_key' );
			$api_secret_key     = hawthorne_get_plugin_settings( 'api_secret_key' );
			$products_endpoint  = hawthorne_get_plugin_settings( 'products_endpoint' );
			$show_import_button = ( empty( $api_key ) || empty( $api_secret_key ) || empty( $products_endpoint ) ) ? 'no' : $show_import_button;
		}


		// Localize custom admin script.
		wp_localize_script(
			$this->plugin_name,
			'Hawthorne_Admin_Script_Vars',
			array(
				'ajaxurl'                    => admin_url( 'admin-ajax.php' ),
				'product_import_button_text' => __( 'Import From Hawthorne', 'import-from-hawthorne' ),
				'product_import_admin_url'   => admin_url( 'admin.php?page=import-products-from-hawthorne' ),
				'show_import_button'         => $show_import_button,
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
			'hawthorne',
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
		$tab = ( ! is_null( $tab ) ) ? $tab : 'settings';
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
		$tab = ( ! is_null( $tab ) ) ? $tab : 'settings';
		echo '<h2 class="nav-tab-wrapper">';

		// Iterate through the tabs.
		foreach ( $this->plugin_settings_tabs as $tab_key => $tab_caption ) {
			$active = ( $tab === $tab_key ) ? 'nav-tab-active' : '';
			echo '<a class="nav-tab ' . $active . '" href="?page=hawthorne&tab=' . $tab_key . '">' . $tab_caption . '</a>';
		}

		echo '</h2>';
	}

	/**
	 * Plugin settings tabs and settings templates.
	 *
	 * @version 1.0.0
	 */
	public function hawthorne_admin_init_callback() {
		// General settings.
		$this->plugin_settings_tabs['settings'] = __( 'Settings', 'import-from-hawthorne' );
		register_setting( 'settings', 'settings' );
		add_settings_section( 'tab-general', ' ', array( &$this, 'hawthorne_plugin_general_settings_callback' ), 'settings' );

		// Test API settings.
		$this->plugin_settings_tabs['test-api'] = __( 'Test API', 'import-from-hawthorne' );
		register_setting( 'test-api', 'test-api' );
		add_settings_section( 'tab-test-api', ' ', array( &$this, 'hawthorne_plugin_api_connection_settings_callback' ), 'test-api' );

		// Send cart settings.
		$this->plugin_settings_tabs['send-cart'] = __( 'Send Cart', 'import-from-hawthorne' );
		register_setting( 'send-cart', 'send-cart' );
		add_settings_section( 'tab-send-cart', ' ', array( &$this, 'hawthorne_plugin_send_cart_settings_callback' ), 'send-cart' );

		// Redirect to the plugin settings page just as it is activated.
		if ( get_option( 'hawthorne_do_plugin_activation_redirect' ) ) {
			delete_option( 'hawthorne_do_plugin_activation_redirect' );
			wp_safe_redirect( admin_url( 'admin.php?page=hawthorne' ) );
			exit;
		}
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
	 * Plugin send cart settings template.
	 *
	 * @param 1.0.0
	 */
	public function hawthorne_plugin_send_cart_settings_callback() {
		include_once 'templates/settings/send-cart.php'; // Include the API connection settings template.
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

		// Start the import log if the request is for the first time.
		if ( 1 === $page ) {
			// Get the current product count.
			$products_count = array_filter( (array) wp_count_posts( 'product' ) );
			$count_log_arr  = array();

			// Prepare a string from the array.
			if ( ! empty( $products_count ) && is_array( $products_count ) ) {
				// Iterate through the counts.
				foreach ( $products_count as $status => $count ) {
					$status = ucfirst( $status );
					$count_log_arr[] = "{$status}: {$count}";
				}
			}

			$message  = sprintf( __( 'Previous product count: %1$s', 'import-from-hawthorne' ), implode( ', ', $count_log_arr ) );
			$filename = 'import-log-' . gmdate( 'Y-m-d' ) . '-' . md5( time() ) . '.log';
			$log_file = HAWTHORNE_LOG_DIR_PATH . $filename;

			// Set the filename in the cookie to use it during the email.
			setcookie( 'hawthorne_import_log_filename', $filename, time() + (10 * 365 * 24 * 60 * 60), '/' );

			// Write the log.
			hawthorne_write_import_log( $message, $log_file );

			// Set the filename that holds the updated/uploaded SKUs, in the cookie to use it during the email.
			$sku_list_filename = 'import-log-sku-list-' . gmdate( 'Y-m-d' ) . '-' . md5( time() ) . '.log';
			setcookie( 'hawthorne_import_log_sku_list_filename', $sku_list_filename, time() + (10 * 365 * 24 * 60 * 60), '/' );
		}

		// Fetch products.
		$products       = get_transient( 'hawthorne_product_items' );
		$products       = json_decode( $products, true );
		$products_count = count( $products );
		$products       = array_chunk( $products, 10, true ); // Divide the complete data into 10 products.
		$chunk_index    = $page - 1;
		$chunk          = ( array_key_exists( $chunk_index, $products ) ) ? $products[ $chunk_index ] : array();

		// Return, if the chunk is empty, means all the products are imported.
		if ( empty( $chunk ) || ! is_array( $chunk ) ) {
			// Get the filename.
			$filename = ( ! empty( $_COOKIE['hawthorne_import_log_filename'] ) ) ? $_COOKIE['hawthorne_import_log_filename'] : '';
			$log_file = HAWTHORNE_LOG_DIR_PATH . $filename;

			// Update the log for updated products.
			$message  = sprintf( __( 'Updated products: %1$s', 'import-from-hawthorne' ), $old_products_updated );
			hawthorne_write_import_log( $message, $log_file );

			// Update the log for newly uploaded products.
			$message  = sprintf( __( 'Uploaded products: %1$s', 'import-from-hawthorne' ), $new_products_added );
			hawthorne_write_import_log( $message, $log_file );

			/**
			 * This hook fires on the admin portal.
			 *
			 * This actions fires when the import process from hawthorne is complete.
			 *
			 * @since 1.0.0
			 */
			do_action( 'product_import_complete' );

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
			$product_title        = ( ! empty( $part['Name'] ) ) ? $part['Name'] : '';
			$product_id_hawthorne = ( ! empty( $part['Id'] ) ) ? $part['Id'] : '';

			// Skip the update if the product title or part id is missing.
			if ( empty( $product_title ) || empty( $product_id_hawthorne ) ) {
				$products_import_failed++; // Increase the count of products import.
				continue;
			}

			// Check if the product exists with the name.
			$product_exists = hawthorne_product_exists( $product_id_hawthorne );

			// If the product doesn't exist.
			if ( false === $product_exists ) {
				$product_id = hawthorne_create_product( $product_title ); // Create product with title.
				$new_products_added++; // Increase the counter of new product created.

				// Add the import log.
				$filename = ( ! empty( $_COOKIE['hawthorne_import_log_sku_list_filename'] ) ) ? $_COOKIE['hawthorne_import_log_sku_list_filename'] : '';
				$message  = sprintf( __( 'Uploaded SKU: %1$s', 'import-from-hawthorne' ), $product_id_hawthorne );
				hawthorne_write_import_log( $message, HAWTHORNE_LOG_DIR_PATH . $filename );
			} else {
				$product_id = $product_exists;
				$old_products_updated++; // Increase the counter of old product updated.

				// Add the import log.
				$filename = ( ! empty( $_COOKIE['hawthorne_import_log_sku_list_filename'] ) ) ? $_COOKIE['hawthorne_import_log_sku_list_filename'] : '';
				$message  = sprintf( __( 'Updated SKU: %1$s', 'import-from-hawthorne' ), $product_id_hawthorne );
				hawthorne_write_import_log( $message, HAWTHORNE_LOG_DIR_PATH . $filename );
			}

			// Update product.
			hawthorne_update_product( $product_id, $part );
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
	 * Highlight a parent menu in the admin menubar for the import products page.
	 *
	 * @param string $parent_file Parent file.
	 * @return string
	 * @since 1.0.0
	 */
	public function hawthorne_parent_file_callback( $parent_file ) {
		global $plugin_page; // Get the current page ID.

		// If the current page matches the import products.
		if (  ! empty( $plugin_page ) && 'import-products-from-hawthorne' === $plugin_page ) {
			$plugin_page = 'wc-admin'; // Make "WooCommerce" menu as the parent menu.
		}

		return $parent_file;
	}

	/**
	 * Add custom fields in the shipping section, the data of which is provided by Hawthorne.
	 *
	 * @since 1.0.0
	 */
	public function hawthorne_woocommerce_product_options_dimensions_callback() {
		$product_id = get_the_ID();

		// Dim. weight field.
		woocommerce_wp_text_input(
			array(
				'id'          => '_dim_weight',
				'value'       => get_post_meta( $product_id, '_dim_weight', true ),
				'label'       => __( 'Dim. Weight', 'import-from-hawthorne' ) . ' (' . get_option( 'woocommerce_weight_unit' ) . ')',
				'placeholder' => wc_format_localized_decimal( 0 ),
				'desc_tip'    => true,
				'description' => __( 'Dim. weight of the product. This data is provided by Hawthorne.', 'import-from-hawthorne' ),
				'type'        => 'text',
				'data_type'   => 'decimal',
			)
		);

		// Volume field.
		woocommerce_wp_text_input(
			array(
				'id'          => '_volume',
				'value'       => get_post_meta( $product_id, '_volume', true ),
				'label'       => __( 'Volume', 'import-from-hawthorne' ) . ' (' . get_option( 'woocommerce_weight_unit' ) . ')',
				'placeholder' => wc_format_localized_decimal( 0 ),
				'desc_tip'    => true,
				'description' => __( 'Volume of the product. This data is provided by Hawthorne.', 'import-from-hawthorne' ),
				'type'        => 'text',
				'data_type'   => 'decimal',
			)
		);
	}

	/**
	 * Add custom fields in the shipping section, the data of which is provided by Hawthorne.
	 *
	 * @since 1.0.0
	 */
	public function hawthorne_woocommerce_product_options_sku_callback() {
		$product_id = get_the_ID();

		// UPC field.
		woocommerce_wp_text_input(
			array(
				'id'          => '_upc',
				'value'       => get_post_meta( $product_id, '_upc', true ),
				'label'       => __( 'UPC', 'import-from-hawthorne' ),
				'placeholder' => '',
				'desc_tip'    => true,
				'description' => __( 'Product\'s UPC. This data is provided by Hawthorne.', 'import-from-hawthorne' ),
				'type'        => 'text',
			)
		);
	}

	/**
	 * Add custom fields in the pricing section, the data of which is provided by Hawthorne.
	 *
	 * @since 1.0.0
	 */
	public function hawthorne_woocommerce_product_options_pricing_callback() {
		$product_id = get_the_ID();

		// Unit price field.
		woocommerce_wp_text_input(
			array(
				'id'          => '_unit_price',
				'value'       => get_post_meta( $product_id, '_unit_price', true ),
				'label'       => __( 'Greenlight price', 'import-from-hawthorne' ) . ' (' . get_woocommerce_currency_symbol() . ')',
				'placeholder' => '',
				'desc_tip'    => true,
				'description' => __( 'Product\'s unit price. This data is provided by Hawthorne.', 'import-from-hawthorne' ),
				'type'        => 'text',
				'data_type'   => 'decimal',
			)
		);
	}

	/**
	 * Update product custom meta details.
	 *
	 * @param int $product_id Holds the product ID.
	 *
	 * @since 1.0.0
	 */
	public function hawthorne_woocommerce_process_product_meta_callback( $product_id ) {
		$dim_weight = filter_input( INPUT_POST, '_dim_weight', FILTER_SANITIZE_STRING );
		$volume     = filter_input( INPUT_POST, '_volume', FILTER_SANITIZE_STRING );
		$upc        = filter_input( INPUT_POST, '_upc', FILTER_SANITIZE_STRING );
		$unit_price = filter_input( INPUT_POST, '_unit_price', FILTER_SANITIZE_STRING );

		// If product dim. weight is available.
		if ( ! empty( $dim_weight ) ) {
			update_post_meta( $product_id, '_dim_weight', $dim_weight );
		} else {
			delete_post_meta( $product_id, '_dim_weight' );
		}

		// If product volume is available.
		if ( ! empty( $volume ) ) {
			update_post_meta( $product_id, '_volume', $volume );
		} else {
			delete_post_meta( $product_id, '_volume' );
		}

		// If product UPC is available.
		if ( ! empty( $upc ) ) {
			update_post_meta( $product_id, '_upc', $upc );
		} else {
			delete_post_meta( $product_id, '_upc' );
		}

		// If product unit price is available.
		if ( ! empty( $unit_price ) ) {
			update_post_meta( $product_id, '_unit_price', $unit_price );
		} else {
			delete_post_meta( $product_id, '_unit_price' );
		}
	}

	/**
	 * Cron job to import products from Hawthorne and update the database.
	 */
	public function hawthorne_hawthorne_import_products_cron_callback() {
		$products = hawthorne_fetch_products(); // Shoot the API to get products.
		$products = array_chunk( $products, 50, true ); // Divide the complete data into 50 products.
		self::hawthorne_import_products( $products, 1 );
	}

	/**
	 * Import products when the cron is executed.
	 *
	 * @param array $products Products fetched from Hawthorne.
	 * @param int   $page Page.
	 * @since 1.0.0
	 */
	public static function hawthorne_import_products( $products, $page ) {
		$chunk_index = $page - 1;
		$chunk       = ( array_key_exists( $chunk_index, $products ) ) ? $products[ $chunk_index ] : array();

		// Import the chunk, if there are products.
		if ( ! empty( $chunk ) && is_array( $chunk ) ) {
			self::hawthorne_import_products_chunk( $products, $chunk, $page );
		}

		/**
		 * This hook fires on the admin portal.
		 *
		 * This actions fires when the import process from hawthorne is complete.
		 *
		 * @since 1.0.0
		 */
		do_action( 'product_import_complete' );
	}

	/**
	 * Import products chunk.
	 *
	 * @param array $products Products fetched from Hawthorne.
	 * @param array $chunk Products chunk.
	 * @param int   $page Page.
	 * @since 1.0.0
	 */
	public static function hawthorne_import_products_chunk( $products, $chunk, $page ) {
		// Return, if the chunk is empty or invalid.
		if ( empty( $chunk ) || ! is_array( $chunk ) ) {
			return;
		}

		// Iterate through the loop to import the products.
		foreach ( $chunk as $part ) {
			$part                 = (array) $part; // Convert the data type from std class to array.
			$product_title        = ( ! empty( $part['Name'] ) ) ? $part['Name'] : '';
			$product_id_hawthorne = ( ! empty( $part['Id'] ) ) ? $part['Id'] : '';

			// Skip the update if the product title or part id is missing.
			if ( empty( $product_title ) || empty( $product_id_hawthorne ) ) {
				continue;
			}

			// Check if the product exists with the name.
			$product_exists = hawthorne_product_exists( $product_id_hawthorne );
			$product_id     = ( false === $product_exists ) ? hawthorne_create_product( $product_title ) : $product_exists;
			hawthorne_update_product( $product_id, $part ); // Update product.
		}

		// Increase the page value.
		$page += 1;

		// Call the function again to continue import.
		self::hawthorne_import_products( $products, $page );
	}

	/**
	 * Register custom metaboxes for showing cart logs details.
	 *
	 * @since 1.0.0
	 */
	public function hawthorne_add_meta_boxes_callback() {
		$post_id = filter_input( INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT );

		/**
		 * Add metaboxes to the greenlight cart logs.
		 * Add metabox for showing the customer details.
		 */
		add_meta_box(
			'hawthorne-greenlight-cart-customer-details',
			__( 'Customer Details', 'import-from-hawthorne' ),
			array( $this, 'hawthorne_greenlight_cart_customer_details_callback' ),
			'greenlight_cart',
			'normal',
			'high'
		);

		// Metabox for showing cart items.
		add_meta_box(
			'hawthorne-greenlight-cart-products-details',
			__( 'Cart Items', 'import-from-hawthorne' ),
			array( $this, 'hawthorne_greenlight_cart_products_details_callback' ),
			'greenlight_cart',
			'normal',
			'high'
		);
	}

	/**
	 * Callback function to show the customer details.
	 *
	 * @since 1.0.0
	 */
	public function hawthorne_greenlight_cart_customer_details_callback() {
		include 'templates/metaboxes/customer-details.php'; // Customer details template.
	}

	/**
	 * Callback function to show the cart items details.
	 *
	 * @since 1.0.0
	 */
	public function hawthorne_greenlight_cart_products_details_callback() {
		include 'templates/metaboxes/cart-details.php'; // Cart details template.
	}

	/**
	 * Add custom columns to the cart logs admin list.
	 *
	 * @param array $columns Columns array.
	 * @return array
	 * @since 1.0.0
	 */
	public function hawthorne_manage_greenlight_cart_posts_columns_callback( $columns ) {
		$columns['customer']       = __( 'Customer', 'import-from-hawthorne' );
		$columns['cart_items']     = __( 'Cart Items', 'import-from-hawthorne' );
		$columns['cart_totals']    = __( 'Cart Totals', 'import-from-hawthorne' );

		return $columns;
	}

	/**
	 * Add custom data to the custom columns.
	 *
	 * @param string $column Columns key.
	 * @param int    $post_id Post ID.
	 * @since 1.0.0
	 */
	public function hawthorne_manage_greenlight_cart_posts_custom_column_callback( $column, $post_id ) {
		$customer_details = get_post_meta( $post_id, 'customer_details', true );
		$cart_items       = get_post_meta( $post_id, 'cart_items', true );
		$coupon_items     = get_post_meta( $post_id, 'coupon_items', true );
		$cart_totals      = get_post_meta( $post_id, 'cart_totals', true );

		// If it's the customer's information column.
		if ( 'customer' === $column ) {
			echo wp_kses_post(
				sprintf(
					__(
						'%1$s%3$sName: %4$s%5$s%2$s%1$s%3$sEmail: %4$s%6$s%2$s%1$s%3$sPhone: %4$s%7$s%2$s%1$s%3$sMessage: %4$s%8$s%2$s',
						'import-from-hawthorne'
					),
					'<p>',
					'</p>',
					'<strong>',
					'</strong>',
					( ! empty( $customer_details['name'] ) ) ? $customer_details['name'] : '--',
					( ! empty( $customer_details['email'] ) ) ? $customer_details['email'] : '--',
					( ! empty( $customer_details['phone'] ) ) ? $customer_details['phone'] : '--',
					( ! empty( $customer_details['message'] ) ) ? $customer_details['message'] : '--',
				)
			);
		} elseif ( 'cart_items' === $column ) {
			// Check if there are cart items present.
			if ( ! empty( $cart_items ) && is_array( $cart_items ) ) {
				// Iterate through the cart items to display them.
				foreach ( $cart_items as $cart_item ) {
					$product_id = ( ! empty( $cart_item['id'] ) ) ? $cart_item['id'] : '';

					// Skip, if the ID is not available.
					if ( empty( $product_id ) ) {
						continue;
					}

					// Get the other details.
					$product_name      = ( ! empty( $cart_item['name'] ) ) ? $cart_item['name'] : '';
					$product_quantity  = ( ! empty( $cart_item['quantity'] ) ) ? $cart_item['quantity'] : '';
					$product_subtotal  = ( ! empty( $cart_item['subtotal'] ) ) ? $cart_item['subtotal'] : '';
					$product_edit_link = get_edit_post_link( $product_id, '&' );

					?>
					<p>
						<strong>
							<a href="<?php echo esc_url( $product_edit_link ); ?>" title="<?php echo $product_name; ?>">
								<?php echo $product_name; ?>
							</a>
						</strong> * 
						<strong><?php echo esc_html( $product_quantity ); ?></strong> = 
						<strong><?php echo wp_kses_post( wc_price( $product_subtotal ) ); ?></strong>
					</p>
					<?php
				}
			}
		} elseif ( 'cart_totals' === $column ) {
			?>
			<!-- CART SUBTOTAL -->
			<p>
				<?php
				echo wp_kses_post(
					sprintf(
						__(
							'%1$sSubtotal: %3$s%2$s',
							'import-from-hawthorne'
						),
						'<strong>',
						'</strong>',
						( ! empty( $cart_totals['subtotal'] ) ) ? wc_price( $cart_totals['subtotal'] ) : ''
					)
				);
				?>
			</p>
			<!-- DISCOUNTS -->
			<?php
			// If the discounts are available.
			if ( ! empty( $coupon_items ) && is_array( $coupon_items ) ) {
				// Iterate through the coupon items.
				foreach ( $coupon_items as $coupon_code => $coupon_discount ) {
					echo wp_kses_post(
						sprintf(
							__(
								'%1$s%3$sCoupon (%5$s): -%6$s%2$s',
								'import-from-hawthorne'
							),
							'<p>',
							'</p>',
							'<strong>',
							'</strong>',
							$coupon_code,
							wc_price( $coupon_discount )
						)
					);
				}
			}
			?>
			<!-- CART TOTAL -->
			<p>
				<?php
				echo wp_kses_post(
					sprintf(
						__(
							'%1$sTotal: %3$s%2$s',
							'import-from-hawthorne'
						),
						'<strong>',
						'</strong>',
						( ! empty( $cart_totals['cart_contents_total'] ) ) ? wc_price( $cart_totals['cart_contents_total'] ) : ''
					)
				);
				?>
			</p>
			<?php
		}
	}

	/**
	 * This callback is triggered when all the items from hawthorne are imported.
	 *
	 * @since 1.0.0
	 */
	public function hawthorne_product_import_complete_callback() {
		// Delete the import transient.
		delete_transient( 'hawthorne_product_items' );

		// Nullify the cookies.
		setcookie( 'hawthorne_import_log_filename', null, -1, '/' );
		setcookie( 'hawthorne_import_log_sku_list_filename', null, -1, '/' );
	}
}
