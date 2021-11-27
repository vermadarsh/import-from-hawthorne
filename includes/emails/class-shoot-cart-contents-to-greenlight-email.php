<?php
/**
 * Shoot cart contents email class.
 *
 * @link       https://github.com/vermadarsh/
 * @since      1.0.0
 *
 * @package    Import_From_Hawthorne
 * @subpackage Import_From_Hawthorne/includes/emails
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Shoot cart contents email class.
 *
 * @package    Import_From_Hawthorne
 * @subpackage Import_From_Hawthorne/includes/emails
 * @author     Adarsh Verma <adarsh@cmsminds.com>
 * @since      1.0.0
 * @extends \WC_Email
 */
class Shoot_Cart_Contents_To_Greenlight_Email extends WC_Email {
	/**
	 * Set email defaults.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// Email slug we can use to filter other data.
		$this->id          = 'shoot_cart_contents';
		$this->title       = __( 'Hawthorne: Cart Details', 'import-from-hawthorne' );
		$this->description = __( 'An email sent to the admin user when they place a cart order.', 'import-from-hawthorne' );

		// For admin area to let the user know we are sending this email to the store owner.
		$this->customer_email = false;
		$this->heading        = __( 'Cart Details', 'import-from-hawthorne' );

		// translators: placeholder is {blogname}, a variable that will be substituted when email is sent out.
		$this->subject = sprintf( _x( '[%s] Cart Contents', 'default email subject for rental agreement being sent to the admin', 'import-from-hawthorne' ), '{blogname}' );

		// Template paths.
		$this->template_html  = 'shoot-cart-contents-html.php';
		$this->template_plain = 'plain/shoot-cart-contents-plain.php';

		add_action( 'hawthorne_shoot_cart_to_greenlight_email_callback_notification', array( $this, 'hawthorne_hawthorne_shoot_cart_to_greenlight_email_callback_notification_callback' ), 20, 2 );

		// Call parent constructor.
		parent::__construct();

		// Template base path.
		$this->template_base = HAWTHORNE_EMAIL_TEMPLATE_PATH;

		// Recipient.
		$this->recipient = $this->get_option( 'recipient' );
	}

	/**
	 * This callback helps fire the email notification.
	 *
	 * @param array $customer_details Customer details.
	 * @param array $cart_items Cart items.
	 * @since 1.0.0
	 */
	public function hawthorne_hawthorne_shoot_cart_to_greenlight_email_callback_notification_callback( $customer_details, $cart_items ) {
		// Email data object.
		$this->object = $this->create_object( $customer_details, $cart_items );

		// Fire the notification now.
		$this->send(
			$this->get_recipient(),
			$this->get_subject(),
			$this->get_content(),
			$this->get_headers(),
			$this->get_attachments()
		);
	}

	/**
	 * Create the data object that will be used in the template.
	 *
	 * @param array $customer_details Customer details.
	 * @param array $cart_items Cart items.
	 * @return stdClass
	 * @since 1.0.0
	 */
	public static function create_object( $customer_details, $cart_items ) {
		global $wpdb;
		$cart_object = new stdClass();

		// Set the cart object.
		$cart_object->cart     = $cart_items; // WooCommerce cart contents.
		$cart_object->customer = $customer_details; // Requesting customer details.

		/**
		 * This filter is fired when sending cart contents email to the store owner.
		 *
		 * This filter helps managing the cart data in the cart contents email template.
		 *
		 * @param stdClass $cart_object Data object.
		 * @return stdClass
		 * @since 1.0.0
		 */
		return apply_filters( 'hawthorne_cart_object', $cart_object );
	}

	/**
	 * Get the html content of the email.
	 *
	 * @return string
	 */
	public function get_content_html() {
		ob_start();

		wc_get_template(
			$this->template_html,
			array(
				'email_data'     => $this->object,
				'email_heading' => $this->get_heading()
			),
			'',
			$this->template_base
		);

		return ob_get_clean();
	}

	/**
	 * Get the plain text content of the email.
	 *
	 * @return string
	 */
	public function get_content_plain() {
		ob_start();

		wc_get_template(
			$this->template_plain,
			array(
				'item_data'     => $this->object,
				'email_heading' => $this->get_heading()
			),
			'',
			$this->template_base
		);

		return ob_get_clean();
	}

	/**
	 * Get the email subject line.
	 *
	 * @return string
	 */
	public function get_subject() {
		return apply_filters( 'woocommerce_email_subject_' . $this->id, $this->format_string( $this->subject ), $this->object );
	}

	/**
	 * Get the email recipient.
	 *
	 * @return string
	 */
	public function get_recipient() {

		return apply_filters( 'woocommerce_email_recipient_' . $this->id, get_option( 'admin_email' ) );
	}

	/**
	 * Get the email main heading line.
	 *
	 * @return string
	 */
	public function get_heading() {

		return apply_filters( 'woocommerce_email_heading_' . $this->id, $this->format_string( $this->heading ), $this->object );
	}
	
	/**
	 * Get the email attachments.
	 *
	 * @return array
	 */
	public function get_attachments() {

		return array();
	}

	/**
	 * Get the email settings.
	 *
	 * @return string
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title'   => __( 'Enable/Disable', 'import-from-hawthorne' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable this email notification', 'import-from-hawthorne' ),
				'default' => 'yes'
			),
			'subject' => array(
				'title'       => __( 'Subject', 'import-from-hawthorne' ),
				'type'        => 'text',
				'description' => sprintf( __( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'import-from-hawthorne' ), $this->subject ),
				'placeholder' => '',
				'default'     => ''
			),
			'heading' => array(
				'title'       => __( 'Email Heading', 'import-from-hawthorne' ),
				'type'        => 'text',
				'description' => sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.', 'import-from-hawthorne' ), $this->heading ),
				'placeholder' => '',
				'default'     => ''
			),
			'email_type' => array(
				'title'       => __( 'Email type', 'import-from-hawthorne' ),
				'type'        => 'select',
				'description' => __( 'Choose which format of email to send.', 'import-from-hawthorne' ),
				'default'     => 'html',
				'class'       => 'email_type',
				'options'		=> array(
					'html' => __( 'HTML', 'import-from-hawthorne' ),
				)
			)
		);
	}
} // end \Reservation_Reminder_Email class
