<?php
/**
 * This file is used for templating the cart items and totals.
 *
 * @since 1.0.0
 * @package Import_From_Hawthorne
 * @subpackage Import_From_Hawthorne/admin/templates/metaboxes
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

$postid       = filter_input( INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT );
$cart_items   = get_post_meta( $postid, 'cart_items', true );
$coupon_items = get_post_meta( $postid, 'coupon_items', true );
$cart_totals  = get_post_meta( $postid, 'cart_totals', true );

// Render the cart items.
if ( empty( $cart_items ) || ! is_array( $cart_items ) ) {
	?>
	<p><?php esc_html_e( 'There are no cart items.', 'import-from-hawthorne' ); ?></p>
	<?php
} else {
	?>
	<div class="hawthorne-cart-log-product-data-wrap">
		<table class="form-table hawthorne-cart-log-product-data">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Image', 'import-from-hawthorne' ); ?></th>
					<th><?php esc_html_e( 'Title', 'import-from-hawthorne' ); ?></th>
					<th><?php esc_html_e( 'Subtotal', 'import-from-hawthorne' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				// Iterate through the cart items.
				foreach ( $cart_items as $cart_item ) {
					$product_quantity = ( ! empty( $cart_item['quantity'] ) ) ? $cart_item['quantity'] : '';
					?>
					<!-- CART DATA -->
					<tr>
						<td>
							<img width="25%" alt="<?php echo esc_attr( ( ! empty( $cart_item['name'] ) ) ? $cart_item['name'] : '' ); ?>" src="<?php echo esc_url( ( ! empty( $cart_item['image'] ) ) ? $cart_item['image'] : '' ); ?>" />
						</td>
						<td>
							<a title="<?php echo esc_attr( ( ! empty( $cart_item['name'] ) ) ? $cart_item['name'] : '' ); ?>" href="<?php echo esc_url( ( ! empty( $cart_item['link'] ) ) ? $cart_item['link'] : '' ); ?>">
								<?php echo esc_html( ( ! empty( $cart_item['name'] ) ) ? $cart_item['name'] : '' ); ?>
							</a>
							<p><?php echo esc_html( sprintf( __( 'Quantity: %1$s', 'import-from-hawthorne' ), $product_quantity ) ); ?></p>
						</td>
						<td><?php echo wp_kses_post( ( ! empty( $cart_item['subtotal'] ) ) ? wc_price( $cart_item['subtotal'] ) : '' ); ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
	<div class="hawthorne-cart-total-data">
		<h3><?php esc_html_e( 'Cart Totals', 'import-from-hawthorne' ); ?></h3>
		<table class="form-table hawthorne-cart-log-cart-total-data">
			<tbody>
				<!-- SUBTOTAL -->
				<tr>
					<th scope="row"><?php esc_html_e( 'Subtotal', 'import-from-hawthorne' ); ?></th>
					<td><?php echo ( ! empty( $cart_totals['subtotal'] ) ) ? wc_price( $cart_totals['subtotal'] ) : ''; ?></td>
				</tr>
				<?php foreach ( $coupon_items as $coupon_code => $coupon_discount ) {?>
					<!-- COUPON ITEMS -->
					<tr>
						<th scope="row"><?php echo sprintf( __( 'Coupon: %1$s', 'import-from-hawthorne' ), $coupon_code ); ?></th>
						<td><?php echo ( ! empty( $coupon_discount ) ) ? '-' . wc_price( $coupon_discount ) : 0; ?></td>
					</tr>
				<?php } ?>
				<!-- TOTAL -->
				<tr>
					<th scope="row"><?php esc_html_e( 'Total', 'import-from-hawthorne' ); ?></th>
					<td><?php echo ( ! empty( $cart_totals['cart_contents_total'] ) ) ? wc_price( $cart_totals['cart_contents_total'] ) : ''; ?></td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php
}