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
		$enqueue_scripts = false;

		// Enqueue scripts only on selected pages.
		if ( is_cart() || is_tax( 'product_cat' ) || is_tax( 'product_brand' ) || is_product() ) {
			$enqueue_scripts = true;
		}

		// Return, if scripts are not to be enqueued.
		if ( false === $enqueue_scripts ) {
			return;
		}

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
				'ajaxurl'                           => admin_url( 'admin-ajax.php' ),
				'notification_error_heading'        => __( 'Error', 'import-from-hawthorne' ),
				'notification_success_heading'      => __( 'Success', 'import-from-hawthorne' ),
				'notification_notice_heading'       => __( 'Notice', 'import-from-hawthorne' ),
				'send_cart_customer_name_required'  => __( 'Name is required.', 'import-from-hawthorne' ),
				'send_cart_customer_email_required' => __( 'Email is required.', 'import-from-hawthorne' ),
				'send_cart_customer_email_invalid'  => __( 'Email is invalid.', 'import-from-hawthorne' ),
				'send_cart_customer_phone_required' => __( 'Phone is required.', 'import-from-hawthorne' ),
				'send_cart_error_message'           => __( 'There is some issue sending the cart. Please see the errors above and try again.', 'import-from-hawthorne' ),
			)
		);
	}

	/**
	 * Register product brand taxonomy.
	 *
	 * @since 1.0.0
	 */
	public function hawthorne_init_callback() {
		// Register product brand taxonomy.
		hawthorne_register_product_brand_taxonomy();

		// Register cart logs custom post type.
		hawthorne_register_cart_logs_custom_post_type();

		// Get media files for the part.
		// $api_url = 'https://services.hawthornegc.com/v1/Media/HGC00006';
		// $api_key           = hawthorne_get_plugin_settings( 'api_key' );
		// $api_secret_key    = hawthorne_get_plugin_settings( 'api_secret_key' );
		// $current_time      = gmdate( 'Y-m-d\TH:i:s\Z' );
		// $api_args          = array(
		// 	'headers'   => array_merge(
		// 		array(
		// 			'Content-Type' => 'application/json',
		// 		)
		// 	),
		// 	'sslverify' => false,
		// 	'timeout'   => 600,
		// 	'body'      => array(
		// 		'format'    => 'json',
		// 		'X-ApiKey'  => $api_key,
		// 		'time'      => $current_time,
		// 		'signature' => hawthorne_get_authentication_signature( $api_key, $api_secret_key, $api_url, $current_time ),
		// 	),
		// );

		// $api_response      = wp_remote_get( $api_url, $api_args ); // Shoot the API.
		// $api_response_code = wp_remote_retrieve_response_code( $api_response ); // Get the response code.
		// $api_response_body = wp_remote_retrieve_body( $api_response ); // Get the response body.

		// var_dump( $api_url, $api_response_code, $api_response_body );
		// die;
	}

	/**
	 * Add button on the cart page to help shoot the email to greenlight.
	 *
	 * @since 1.0.0
	 */
	public function hawthorne_woocommerce_proceed_to_checkout_callback() {
		$open_send_cart_modal_button_text = hawthorne_get_plugin_settings( 'open_send_cart_modal_button_text' );
		?>
		<a href="javascript:void(0);" class="checkout-button button alt wc-forward hawthorne-open-cart-contents-modal"><?php echo wp_kses_post( $open_send_cart_modal_button_text ); ?></a>
		<?php
	}

	/**
	 * Add custom assets in the footer section of the page.
	 *
	 * @since 1.0.0
	 */
	public function hawthorne_wp_footer_callback() {
		// Check if it the cart page.
		if ( is_cart() ) {
			// Include the cart contents email modal.
			require_once HAWTHORNE_PLUGIN_PATH . 'public/templates/modals/shoot-cart-contents.php';

			// Include the notification html.
			require_once HAWTHORNE_PLUGIN_PATH . 'public/templates/modals/notification.php';
		}
	}

	/**
	 * AJAX to send cart details to Greenlight.
	 *
	 * @since 1.0.0
	 */
	public function hawthorne_send_cart_callback() {
		$action = filter_input( INPUT_POST, 'action', FILTER_SANITIZE_STRING );

		// Exit, if the action mismatches.
		if ( empty( $action ) || 'send_cart' !== $action ) {
			echo esc_html( 0 );
			wp_die();
		}

		// Gather the customer details.
		$customer_details = array(
			'name'    => filter_input( INPUT_POST, 'customer_name', FILTER_SANITIZE_STRING ),
			'email'   => filter_input( INPUT_POST, 'customer_email', FILTER_SANITIZE_STRING ),
			'phone'   => filter_input( INPUT_POST, 'customer_phone', FILTER_SANITIZE_STRING ),
			'message' => filter_input( INPUT_POST, 'customer_message', FILTER_SANITIZE_STRING ),
		);

		// Get the cart contents now.
		$cart_contents = WC()->cart->get_cart_contents();
		$cart_items    = array();

		// If the cart content is available, iterate the items to prepare the array.
		if ( ! empty( $cart_contents ) && is_array( $cart_contents ) ) {
			foreach ( $cart_contents as $cart_item ) {
				$cart_item_product_id   = ( ! empty( $cart_item['product_id'] ) && 0 !== $cart_item['product_id'] ) ? $cart_item['product_id'] : 0;
				$cart_item_variation_id = ( ! empty( $cart_item['variation_id'] ) && 0 !== $cart_item['variation_id'] ) ? $cart_item['variation_id'] : 0;
				$product_id             = ( 0 === $cart_item_variation_id ) ? $cart_item_product_id : $cart_item_variation_id;
				$product_image_id       = get_post_thumbnail_id( $product_id );
				$cart_items[]           = array(
					'id'       => $product_id,
					'name'     => get_the_title( $product_id ),
					'image'    => hawthorne_get_attachment_url_from_attachment_id( $product_image_id ),
					'quantity' => ( ! empty( $cart_item['quantity'] ) && 0 !== $cart_item['quantity'] ) ? $cart_item['quantity'] : 0,
					'link'     => get_permalink( $product_id ),
					'subtotal' => ( ! empty( $cart_item['line_subtotal'] ) && 0 !== $cart_item['line_subtotal'] ) ? $cart_item['line_subtotal'] : 0,
				);
			}
		}

		// If there is a coupon applied.
		$coupon_items = ( ! empty( WC()->cart->coupon_discount_totals ) && is_array( WC()->cart->coupon_discount_totals ) ) ? WC()->cart->coupon_discount_totals : array();

		// Get the cart totals.
		$cart_totals = WC()->cart->get_totals();

		/**
		 * Send the email now.
		 *
		 * This hook is declared for sending the cart items to greenlight.
		 *
		 * @param array $customer_details Customer details array.
		 * @param array $cart_items Cart items array.
		 * @param array $coupon_items Applied coupon items.
		 * @param array $cart_totals Cart totals.
		 * @since 1.0.0
		 */
		do_action( 'shoot_cart_to_greenlight_email', $customer_details, $cart_items, $coupon_items, $cart_totals );

		// Clear cart if required.
		$clear_cart = hawthorne_get_plugin_settings( 'clear_cart' );
		if ( ! empty( $clear_cart ) && 'yes' === $clear_cart ) {
			WC()->cart->empty_cart();
		}

		// Send the response.
		wp_send_json_success(
			array(
				'code'          => 'cart-sent',
				'toast_message' => hawthorne_get_plugin_settings( 'sent_cart_success_message' ),
			)
		);
		wp_die();
	}

	/**
	 * Change the button text - add to cart - product single page.
	 *
	 * @param string $button_text Add to cart button text.
	 * @return string
	 * @since 1.0.0
	 */
	public function hawthorne_woocommerce_product_single_add_to_cart_text_callback( $button_text ) {
		$button_text = hawthorne_get_plugin_settings( 'single_product_add_to_cart_button_text' );

		return $button_text;
	}

	/**
	 * Change the button text - add to cart - archive pages.
	 *
	 * @param string $button_text Add to cart button text.
	 * @return string
	 * @since 1.0.0
	 */
	public function hawthorne_woocommerce_product_add_to_cart_text_callback( $button_text ) {
		$button_text = hawthorne_get_plugin_settings( 'archive_product_pages_add_to_cart_button_text' );

		return $button_text;
	}

	/**
	 * Add custom data received from Hawthorne to the product pages.
	 *
	 * @param array      $product_attributes Product attributes array.
	 * @param WC_Product $product WooCommerce product data object.
	 * @return array
	 * @since 1.0.0
	 */
	public function hawthorne_woocommerce_display_product_attributes_callback( $product_attributes, $product ) {
		$product_id = $product->get_id(); // Get the product ID.
		$dim_weight = get_post_meta( $product_id, '_dim_weight', true );
		$volume     = get_post_meta( $product_id, '_volume', true );
		$upc        = get_post_meta( $product_id, '_upc', true );

		// Add dim weight to the product attributes displayed array.
		if ( ! empty( $dim_weight ) ) {
			$product_attributes['dim_weight'] = array(
				'label' => __( 'Dim. Weight', 'import-from-hawthorne' ),
				'value' => $dim_weight,
			);
		}

		// Add volume to the product attributes displayed array.
		if ( ! empty( $volume ) ) {
			$product_attributes['volume'] = array(
				'label' => __( 'Volume', 'import-from-hawthorne' ),
				'value' => $volume,
			);
		}

		// Add UPC to the product attributes displayed array.
		if ( ! empty( $upc ) ) {
			$product_attributes['upc'] = array(
				'label' => __( 'UPC', 'import-from-hawthorne' ),
				'value' => $upc,
			);
		}

		return $product_attributes;
	}

	/**
	 * Create a log in the admin about the cart request submitted.
	 *
	 * @param array $customer_details Customer details.
	 * @param array $cart_items Cart items.
	 * @param array $coupon_items Applied coupon items.
	 * @param array $cart_totals Cart totals.
	 * @since 1.0.0
	 */
	public function hawthorne_shoot_cart_to_greenlight_email_callback( $customer_details, $cart_items, $coupon_items, $cart_totals ) {
		// Add the cart log into the database.
		wp_insert_post(
			array(
				/* translators: 1: %s: sha conversion of time */
				'post_title'    => sprintf( __( 'Cart Log %s', 'import-from-hawthorne' ), sha1( time() ) ),
				'post_status'   => 'publish',
				'post_author'   => 1,
				'post_date'     => gmdate( 'Y-m-d H:i:s' ),
				'post_modified' => gmdate( 'Y-m-d H:i:s' ),
				'post_type'     => 'greenlight_cart',
				'meta_input'    => array(
					'customer_details' => $customer_details,
					'cart_items'       => $cart_items,
					'coupon_items'     => $coupon_items,
					'cart_totals'      => $cart_totals,
				),
			)
		);
	}

	/**
	 * Manage the product structure on the WooCommerce archive pages.
	 *
	 * @param array $product_structure Product structure.
	 * @return array
	 * @since 1.0.0
	 */
	public function hawthorne_astra_woo_shop_product_structure_callback( $product_structure ) {
		// Check if we can find the price in the structure.
		$price_index = array_search( 'price', $product_structure, true );

		// Return, if the index is not there.
		if ( false === $price_index ) {
			return $product_structure;
		}

		// Remove the price from the structure.
		unset( $product_structure[ $price_index ] );

		return $product_structure;
	}

	/**
	 * Show the MSRP and the unit price on the archive pages.
	 *
	 * @since 1.0.0
	 */
	public function hawthorne_astra_woo_shop_rating_after_callback() {
		$product_id       = get_the_ID();
		$is_quest_branded = has_term( 'quest', 'product_brand', $product_id );
		$msrp             = get_post_meta( $product_id, '_price', true );
		$unit_price       = get_post_meta( $product_id, '_unit_price', true );
		$unit_price       = ( $is_quest_branded ) ? __( 'Price too low to advertise. Contact us for the Greenlight price.', 'import-from-hawthorne' ) : wc_price( $unit_price );

		// Show the prices now.
		?>
		<h6 class="hawthorne-product-msrp"><?php echo sprintf( __( 'MSRP: %1$s', 'import-from-hawthorne' ), wc_price( $msrp ) ); ?></h6>
		<h6 class="hawthorne-product-unit-price"><?php echo sprintf( __( 'Greenlight Price: %1$s', 'import-from-hawthorne' ), $unit_price ); ?></h6>
		<?php
	}

	/**
	 * Show the MSRP and the unit price on the archive pages.
	 *
	 * @since 1.0.0
	 */
	public function hawthorne_woocommerce_single_product_summary_callback() {
		$product_id       = get_the_ID();
		$is_quest_branded = has_term( 'quest', 'product_brand', $product_id );
		$msrp             = get_post_meta( $product_id, '_price', true );
		$unit_price       = get_post_meta( $product_id, '_unit_price', true );
		$unit_price       = ( $is_quest_branded ) ? __( 'Price too low to advertise. Contact us for the Greenlight price.', 'import-from-hawthorne' ) : wc_price( $unit_price );

		// Show the prices now.
		?>
		<h6 class="hawthorne-product-msrp"><?php echo sprintf( __( 'MSRP: %1$s', 'import-from-hawthorne' ), wc_price( $msrp ) ); ?></h6>
		<h6 class="hawthorne-product-unit-price"><?php echo sprintf( __( 'Greenlight Price: %1$s', 'import-from-hawthorne' ), $unit_price ); ?></h6>
		<?php
	}

	/**
	 * Setup the cron to delete the log files from the uploads folder.
	 *
	 * @since 1.0.0
	 */
	public function hawthorne_hawthorne_import_products_comparison_log_deletion_cron_callback() {
		$log_files = glob( HAWTHORNE_LOG_DIR_PATH . 'import-log-*.log' );

		// Return, if there are no log files to delete.
		if ( empty( $log_files ) ) {
			return;
		}

		// Loop in through the files to unlink each of them.
		foreach ( $log_files as $log_file ) {
			unlink( $log_file );
		}
	}
}
