<?php
/**
 * This file is used for templating the customer details.
 *
 * @since 1.0.0
 * @package Import_From_Hawthorne
 * @subpackage Import_From_Hawthorne/admin/templates/metaboxes
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

$postid           = filter_input( INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT );
$customer_details = get_post_meta( $postid, 'customer_details', true );
?>
<table class="form-table hawthorne-cart-log-customer-data">
	<tbody>
		<!-- CUSTOMER FULL NAME -->
		<tr>
			<th scope="row"><label for="customer-name"><?php esc_html_e( 'Name', 'import-from-hawthorne' ); ?></label></th>
			<td><?php echo esc_html( ( ! empty( $customer_details['name'] ) ) ? $customer_details['name'] : '--' ); ?></td>
		</tr>

		<!-- CUSTOMER EMAIL -->
		<tr>
			<th scope="row"><label for="customer-email"><?php esc_html_e( 'Email', 'import-from-hawthorne' ); ?></label></th>
			<td><?php echo wp_kses_post( ( ! empty( $customer_details['email'] ) ) ? $customer_details['email'] : '--' ); ?></td>
		</tr>

		<!-- CUSTOMER PHONE -->
		<tr>
			<th scope="row"><label for="customer-phone"><?php esc_html_e( 'Phone', 'import-from-hawthorne' ); ?></label></th>
			<td><?php echo wp_kses_post( ( ! empty( $customer_details['phone'] ) ) ? $customer_details['phone'] : '--' ); ?></td>
		</tr>

		<!-- CUSTOMER MESSAGE -->
		<tr>
			<th scope="row"><label for="customer-message"><?php esc_html_e( 'Message', 'import-from-hawthorne' ); ?></label></th>
			<td><?php echo esc_html( ( ! empty( $customer_details['message'] ) ) ? $customer_details['message'] : '' ); ?></td>
		</tr>
	</tbody>
</table>
