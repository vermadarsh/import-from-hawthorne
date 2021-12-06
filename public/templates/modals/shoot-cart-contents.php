<?php
/**
 * This file is used for templating the send cart template.
 *
 * @since 1.0.0
 * @package Import_From_Hawthorne
 * @subpackage Import_From_Hawthorne/public/templates/modals
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

// Get the current customer ID.
$customer_id         = get_current_user_id();
$customer            = ( 0 !== $customer_id ) ? new WC_Customer( $customer_id ) : false;
$customer_first_name = ( false !== $customer ) ? $customer->get_billing_first_name() : '';
$customer_last_name  = ( false !== $customer ) ? $customer->get_billing_last_name() : '';
$customer_phone      = ( false !== $customer ) ? $customer->get_billing_phone() : '';
$customer_email      = ( false !== $customer ) ? $customer->get_billing_email() : '';
$customer_full_name  = '';

// Prepare the customer full name.
if ( ! empty( $customer_first_name ) && ! empty( $customer_last_name ) ) {
	$customer_full_name = "{$customer_first_name} {$customer_last_name}";
} elseif ( ! empty( $customer_first_name ) ) {
	$customer_full_name = $customer_first_name;
} elseif ( ! empty( $customer_last_name ) ) {
	$customer_full_name = $customer_last_name;
}
?>
<div class="modal fade" id="hawthorne-shoot-cart-contents-modal" tabindex="-1" aria-labelledby="hawthorne-shoot-cart-contents-modal-label" aria-hidden="false">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="hawthorne-shoot-cart-contents-modal-label"><?php esc_html_e( 'Send Cart', 'import-from-hawthorne' ); ?></h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post">
					<div class="form-row">
						<div class="col-12 col-md-6">
							<div class="form-group">
								<input type="text" id="customer-name" class="form-control" placeholder="<?php esc_html_e( 'Name*', 'import-from-hawthorne' ); ?>" value="<?php echo esc_html( $customer_full_name ); ?>">
								<span class="hawthorne-send-cart-error customer-name"></span>
							</div>
						</div>
						<div class="col-12 col-md-6">
							<div class="form-group">
								<input type="text" id="customer-phone" class="form-control" placeholder="<?php esc_html_e( 'Phone Number*', 'import-from-hawthorne' ); ?>" value="<?php echo esc_html( $customer_phone ); ?>">
								<span class="hawthorne-send-cart-error customer-phone"></span>
							</div>
						</div>
						<div class="col-12 col-md-12">
							<div class="form-group">
								<input type="email" id="customer-email" class="form-control" placeholder="<?php esc_html_e( 'Email*', 'import-from-hawthorne' ); ?>" value="<?php echo esc_html( $customer_email ); ?>">
								<span class="hawthorne-send-cart-error customer-email"></span>
							</div>
						</div>
						<div class="col-12">
							<div class="form-group">
								<textarea id="customer-message" class="form-control" placeholder="<?php esc_html_e( 'Message', 'import-from-hawthorne' ); ?>" style="width: 100%; height: 100px;" spellcheck="false"></textarea>
							</div>
							<div class="form-group text-right">
								<button class="hawthorne-send-cart-to-greenlight btn btn-accent" type="button"><?php esc_html_e( 'Send Cart to Greenlight', 'import-from-hawthorne' ); ?></button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
