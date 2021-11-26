<?php
/**
 * Reservation reminder email.
 *
 * @package Easy_Reservations
 * @subpackage Easy_Reservations/admin/templates/emails
 */

defined( 'ABSPATH' ) || exit;

/**
 * This hook runs on the custom email headers.
 *
 * This hook helps in customizing email header text.
 *
 * @param string $email_heading Email heading.
 * @since 1.0.0
 */
do_action( 'woocommerce_email_header', $email_heading );
?>
<p><?php esc_html_e( 'Congratulations !! There is a cart request from a customer.', 'import-from-hawthorne' ); ?></p>
<h3><?php esc_html_e( 'Customer details:', 'import-from-hawthorne' ); ?></h3>
<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" bordercolor="#eee">
	<tbody>
		<!-- CUSTOMER NAME -->
		<tr>
			<th scope="row" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e( 'Name', 'import-from-hawthorne' ); ?></th>
			<td style="text-align:left; border: 1px solid #eee;">
				<?php echo esc_html( ( ! empty( $email_data->customer['name'] ) ? $email_data->customer['name'] : '' ) ); ?>
			</td>
		</tr>
		<!-- CUSTOMER EMAIL -->
		<tr>
			<th scope="row" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e( 'Email', 'import-from-hawthorne' ); ?></th>
			<td style="text-align:left; border: 1px solid #eee;">
				<?php echo esc_html( ( ! empty( $email_data->customer['email'] ) ? $email_data->customer['email'] : '' ) ); ?>
			</td>
		</tr>
		<!-- CUSTOMER PHONE -->
		<tr>
			<th scope="row" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e( 'Phone', 'import-from-hawthorne' ); ?></th>
			<td style="text-align:left; border: 1px solid #eee;">
				<?php echo esc_html( ( ! empty( $email_data->customer['phone'] ) ? $email_data->customer['phone'] : '' ) ); ?>
			</td>
		</tr>
		<!-- CUSTOMER MESSAGE -->
		<tr>
			<th scope="row" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e( 'Message', 'import-from-hawthorne' ); ?></th>
			<td style="text-align:left; border: 1px solid #eee;">
				<?php echo esc_html( ( ! empty( $email_data->customer['message'] ) ? $email_data->customer['message'] : '--' ) ); ?>
			</td>
		</tr>
	</tbody>
</table>
<h3><?php esc_html_e( 'Cart items:', 'import-from-hawthorne' ); ?></h3>
<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" bordercolor="#eee">
	<tbody>
		<!-- ITERATE THROUGH THE CART ITEMS -->
		<?php
		if ( ! empty( $email_data->cart ) && is_array( $email_data->cart ) ) {
			foreach ( $email_data->cart as $cart_item ) {
				?>
				<tr>
					<th scope="row" style="text-align:left; border: 1px solid #eee;">
						<img width="20%" src="<?php echo esc_url( ( ! empty( $cart_item['image'] ) ? $cart_item['image'] : '' ) ); ?>" />
						<a title="<?php echo esc_html( ( ! empty( $cart_item['name'] ) ? $cart_item['name'] : '' ) ); ?>" href="<?php echo esc_url( ( ! empty( $cart_item['link'] ) ? $cart_item['link'] : '' ) ); ?>" target="_blank">
							<?php echo esc_html( ( ! empty( $cart_item['name'] ) ? $cart_item['name'] : '' ) ); ?>
						</a>
					</th>
					<td style="text-align:left; border: 1px solid #eee;">
						<?php
						// Print the product ID.
						if ( ! empty( $cart_item['id'] ) ) {
							/* translators: 1: %s: product ID */
							echo wp_kses_post( '<p>' . sprintf( __( 'ID: %1$s', 'import-from-hawthorne' ), ( ! empty( $cart_item['id'] ) ? $cart_item['id'] : '' ) ) . '</p>' );
						}

						// Print the product quantity.
						if ( ! empty( $cart_item['quantity'] ) ) {
							/* translators: 1: %s: product quantity */
							echo wp_kses_post( '<p>' . sprintf( __( 'Quantity: %1$s', 'import-from-hawthorne' ), ( ! empty( $cart_item['quantity'] ) ? $cart_item['quantity'] : '' ) ) . '</p>' );
						}

						// Print the product subtotal.
						if ( ! empty( $cart_item['subtotal'] ) ) {
							/* translators: 1: %s: product subtotal */
							echo wp_kses_post( '<p>' . sprintf( __( 'Subtotal: %1$s', 'import-from-hawthorne' ), wc_price( ( ! empty( $cart_item['subtotal'] ) ? $cart_item['subtotal'] : 0 ) ) ) . '</p>' );
						}
						?>
					</td>
				</tr>
				<?php
			}
		}
		?>
	</tbody>
</table>
<p><?php esc_html_e( 'This is a system generated email. Please DO NOT respond to it.', 'easy-reservations' ); ?></p>
<?php
/**
 * This hook runs on the custom email footers.
 *
 * This hook helps in customizing email footer text.
 *
 * @since 1.0.0
 */
do_action( 'woocommerce_email_footer' );
