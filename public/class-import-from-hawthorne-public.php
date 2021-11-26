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
		// Return if it's not the cart page.
		if ( ! is_cart() ) {
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
				'secd_cart_customer_name_required'  => __( 'Name is required.', 'import-from-hawthorne' ),
				'secd_cart_customer_email_required' => __( 'Email is required.', 'import-from-hawthorne' ),
				'secd_cart_customer_email_invalid'  => __( 'Email is invalid.', 'import-from-hawthorne' ),
				'secd_cart_customer_phone_required' => __( 'Phone is required.', 'import-from-hawthorne' ),
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

		// do_action( 'shoot_cart_to_greenlight_email', array() );
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

		debug( $cart_contents ); die;

		// Send the response.
		wp_send_json_success(
			array(
				'code'          => 'cart-sent',
				'toast_message' => hawthorne_get_plugin_settings( 'sent_cart_success_message' ),
			)
		);
		wp_die();
	}
}
