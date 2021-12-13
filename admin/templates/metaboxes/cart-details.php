<?php
$post_id      = filter_input( INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT );
$cart_items   = get_post_meta( $post_id, 'cart_items', true );
$coupon_items = get_post_meta( $post_id, 'coupon_items', true );
$cart_totals  = get_post_meta( $post_id, 'cart_totals', true );

// Render the cart items.
if ( empty( $cart_items ) || ! is_array( $cart_items ) ) {
	?><p><?php esc_html_e( 'There are no cart items.', 'import-from-hawthorne' ); ?></p><?php
} else {
	?>
	<h3><?php esc_html_e( 'Cart Items', 'import-from-hawthorne' ); ?></h3>
	<table class="form-table hawthorne-cart-log-product-data">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Image', 'import-from-hawthorne' ); ?></th>
				<th><?php esc_html_e( 'Title', 'import-from-hawthorne' ); ?></th>
				<th><?php esc_html_e( 'Quantity', 'import-from-hawthorne' ); ?></th>
				<th><?php esc_html_e( 'Subtotal', 'import-from-hawthorne' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $cart_items as $cart_item ) {?>
				<!-- CART DATA -->
				<tr>
					<td>
						<img width="25%" alt="<?php echo ( ! empty( $cart_item['name'] ) ) ? $cart_item['name'] : ''; ?>" src="<?php echo esc_url( ( ! empty( $cart_item['image'] ) ) ? $cart_item['image'] : '' ); ?>" />
					</td>
					<td>
						<a title="<?php echo ( ! empty( $cart_item['name'] ) ) ? $cart_item['name'] : ''; ?>" href="<?php echo esc_url( ( ! empty( $cart_item['link'] ) ) ? $cart_item['link'] : '' ); ?>">
							<?php echo ( ! empty( $cart_item['name'] ) ) ? $cart_item['name'] : ''; ?>
						</a>
					</td>
					<td><?php echo ( ! empty( $cart_item['quantity'] ) ) ? $cart_item['quantity'] : ''; ?></td>
					<td><?php echo ( ! empty( $cart_item['subtotal'] ) ) ? wc_price( $cart_item['subtotal'] ) : ''; ?></td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
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
	<?php
}