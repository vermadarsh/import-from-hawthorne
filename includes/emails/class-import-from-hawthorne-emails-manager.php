<?php
/**
 * Custom email templates manager class.
 *
 * @link       https://github.com/vermadarsh/
 * @since      1.0.0
 *
 * @package    Import_From_Hawthorne
 * @subpackage Import_From_Hawthorne/includes/emails
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Custom email templates manager class.
 *
 * Defines the custom email templates and notifications.
 *
 * @package    Import_From_Hawthorne
 * @subpackage Import_From_Hawthorne/includes/emails
 * @author     Adarsh Verma <adarsh@cmsminds.com>
 */
class Import_From_Hawthorne_Emails_Manager {
	/**
	 * Constructor to help define actions.
	 */
	public function __construct() {
		define( 'HAWTHORNE_EMAIL_TEMPLATE_PATH', HAWTHORNE_PLUGIN_PATH . 'admin/templates/emails/' );
		add_action( 'shoot_cart_to_greenlight_email', array( &$this, 'hawthorne_shoot_cart_to_greenlight_email_callback' ), 10, 4 );
		add_action( 'product_import_complete', array( &$this, 'hawthorne_product_import_complete_callback' ) );
		add_filter( 'woocommerce_email_classes', array( &$this, 'hawthorne_woocommerce_email_classes_callback' ) );
	}

	/**
	 * Send notification to the greenlight admin user for the cart contents.
	 *
	 * @param array $customer_details Customer details.
	 * @param array $cart_items Cart items.
	 * @param array $coupon_items Applied coupon items.
	 * @param array $cart_totals Cart totals.
	 * @since 1.0.0
	 */
	public function hawthorne_shoot_cart_to_greenlight_email_callback( $customer_details, $cart_items, $coupon_items, $cart_totals ) {
		new WC_Emails();
		/**
		 * This action fires when the customer send the cart to greenlight.
		 *
		 * @param array $customer_details Customer details.
		 * @param array $cart_items Cart items.
		 * @param array $coupon_items Applied coupon items.
		 * @param array $cart_totals Cart totals.
		 * @since 1.0.0
		 */
		do_action( 'hawthorne_shoot_cart_to_greenlight_email_callback_notification', $customer_details, $cart_items, $coupon_items, $cart_totals );
	}

	/**
	 * Send notification to the greenlight admin user about the import log.
	 *
	 * @since 1.0.0
	 */
	public function hawthorne_product_import_complete_callback() {
		new WC_Emails();
		/**
		 * This action fires when the import is complete.
		 *
		 * @since 1.0.0
		 */
		do_action( 'hawthorne_product_import_complete_callback_notification' );
	}

	/**
	 * Add custom class to send reservation emails.
	 *
	 * @param array $email_classes Email classes array.
	 * @return array
	 * @since 1.0.0
	 */
	public function hawthorne_woocommerce_email_classes_callback( $email_classes ) {
		// Shoot the cart contents to the greenlight admin.
		require_once 'class-shoot-cart-contents-to-greenlight-email.php'; // Require the class file.
		$email_classes['Shoot_Cart_Contents_To_Greenlight_Email'] = new Shoot_Cart_Contents_To_Greenlight_Email(); // Put in the classes into existing classes.

		// Shoot the email to the admin users about the import log.
		require_once 'class-product-import-log-email.php'; // Require the class file.
		$email_classes['Product_Import_Log_Email'] = new Product_Import_Log_Email(); // Put in the classes into existing classes.

		return $email_classes;
	}

	
}

new Import_From_Hawthorne_Emails_Manager();
