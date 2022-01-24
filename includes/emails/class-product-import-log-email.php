<?php
/**
 * Shoot product import log email class.
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
 * Shoot product import log email class.
 *
 * @package    Import_From_Hawthorne
 * @subpackage Import_From_Hawthorne/includes/emails
 * @author     Adarsh Verma <adarsh@cmsminds.com>
 * @since      1.0.0
 * @extends \WC_Email
 */
class Product_Import_Log_Email extends WC_Email {
	/**
	 * Set email defaults.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// Email slug we can use to filter other data.
		$this->id          = 'product_import_log';
		$this->title       = __( 'Hawthorne: Products Import Compare Log', 'import-from-hawthorne' );
		$this->description = __( 'An email sent to the admin user when the hawthorne import is completed.', 'import-from-hawthorne' );

		// For admin area to let the user know we are sending this email to the store owner.
		$this->customer_email = false;
		$this->heading        = __( 'Products Import Comparison Log', 'import-from-hawthorne' );

		// translators: placeholder is {blogname}, a variable that will be substituted when email is sent out.
		$this->subject = sprintf( _x( '[%s] Products Import Comparison Log', 'default email subject for rental agreement being sent to the admin', 'import-from-hawthorne' ), '{blogname}' );

		// Template paths.
		$this->template_html  = 'product-import-log-html.php';
		$this->template_plain = 'plain/product-import-log-plain.php';

		add_action( 'hawthorne_product_import_complete_callback_notification', array( $this, 'hawthorne_hawthorne_product_import_complete_notification_callback' ), 20, 4 );

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
	 * @since 1.0.0
	 */
	public function hawthorne_hawthorne_product_import_complete_notification_callback() {
		// Email data object.
		$this->object = $this->create_object();

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
	 * @return stdClass
	 * @since 1.0.0
	 */
	public static function create_object() {
		$cart_object = new stdClass();

		/**
		 * This filter is fired when sending import log to the store admins.
		 *
		 * This filter helps managing the data in the cart contents email template.
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
				'email_data'    => $this->object,
				'email_heading' => $this->get_heading(),
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
				'email_heading' => $this->get_heading(),
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

		return apply_filters( 'woocommerce_email_recipient_' . $this->id, 'sbiggs@growwithgreenlight.com' );
	}

	/**
	 * Get the email headers.
	 *
	 * @return string
	 */
	public function get_headers() {

		return apply_filters( 'woocommerce_email_headers_' . $this->id, 'Content-Type: text/html' );
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
		$attachments = array();

		// Get the log filename.
		$sku_list_log_file = ( ! empty( $_COOKIE['hawthorne_import_log_sku_list_filename'] ) ) ? $_COOKIE['hawthorne_import_log_sku_list_filename'] : '';

		// Set the sku list log file attachment.
		if ( ! empty( $sku_list_log_file ) ) {
			$attachments[] = HAWTHORNE_LOG_DIR_PATH . $sku_list_log_file;
		}

		return $attachments;
	}

	/**
	 * Define the email settings.
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled'    => array(
				'title'   => __( 'Enable/Disable', 'import-from-hawthorne' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable this email notification', 'import-from-hawthorne' ),
				'default' => 'yes',
			),
			'subject'    => array(
				'title'       => __( 'Subject', 'import-from-hawthorne' ),
				'type'        => 'text',
				/* translators: 1: %s: email subject */
				'description' => sprintf( __( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'import-from-hawthorne' ), $this->subject ),
				'placeholder' => '',
				'default'     => '',
			),
			'heading'    => array(
				'title'       => __( 'Email Heading', 'import-from-hawthorne' ),
				'type'        => 'text',
				/* translators: 1: %s: email heading */
				'description' => sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.', 'import-from-hawthorne' ), $this->heading ),
				'placeholder' => '',
				'default'     => '',
			),
			'email_type' => array(
				'title'       => __( 'Email type', 'import-from-hawthorne' ),
				'type'        => 'select',
				'description' => __( 'Choose which format of email to send.', 'import-from-hawthorne' ),
				'default'     => 'html',
				'class'       => 'email_type',
				'options'     => array(
					'html' => __( 'HTML', 'import-from-hawthorne' ),
				),
			),
		);
	}
} // end \Reservation_Reminder_Email class
