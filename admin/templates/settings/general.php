<?php
/**
 * This file is used for templating the plugin general settings.
 *
 * @since 1.0.0
 * @package Import_From_Hawthorne
 * @subpackage Import_From_Hawthorne/admin/settings
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

// Check if the submit button is clicked.
$update_plugin_settings = filter_input( INPUT_POST, 'hawthorne_update_plugin_settings', FILTER_SANITIZE_STRING );
if ( ! is_null( $update_plugin_settings ) ) {
	// Get the settings.
	$plugin_settings = get_option( 'hawthorne_integration_plugin_settings' );
	$plugin_settings = ( empty( $plugin_settings ) ) ? array() : $plugin_settings;

	// Add send cart settings to the settings array.
	$plugin_settings['api_key']                                       = filter_input( INPUT_POST, 'api-key', FILTER_SANITIZE_STRING );
	$plugin_settings['api_secret_key']                                = filter_input( INPUT_POST, 'api-secret-key', FILTER_SANITIZE_STRING );
	$plugin_settings['products_endpoint']                             = filter_input( INPUT_POST, 'products-endpoint', FILTER_SANITIZE_STRING );
	$plugin_settings['single_product_add_to_cart_button_text']        = filter_input( INPUT_POST, 'single-product-add-to-cart-button-text', FILTER_SANITIZE_STRING );
	$plugin_settings['archive_product_pages_add_to_cart_button_text'] = filter_input( INPUT_POST, 'archive-product-pages-add-to-cart-button-text', FILTER_SANITIZE_STRING );

	// Array for the settings.
	update_option( 'hawthorne_integration_plugin_settings', $plugin_settings, false );

	// Print the success message.
	?>
	<div class="notice updated is-dismissible" id="message"><p><?php esc_html_e( 'Plugin settings updated.', 'import-from-hawthorne' ); ?></p></div>
	<?php
}

// Get the plugin settings.
$api_key                                       = hawthorne_get_plugin_settings( 'api_key' );
$api_secret_key                                = hawthorne_get_plugin_settings( 'api_secret_key' );
$products_endpoint                             = hawthorne_get_plugin_settings( 'products_endpoint' );
$single_product_add_to_cart_button_text        = hawthorne_get_plugin_settings( 'single_product_add_to_cart_button_text' );
$archive_product_pages_add_to_cart_button_text = hawthorne_get_plugin_settings( 'archive_product_pages_add_to_cart_button_text' );
?>
<table class="form-table hack-import-subscribers-table">
	<tbody>
		<!-- API KEY -->
		<tr>
			<th scope="row"><label for="api-key"><?php esc_html_e( 'API Key', 'import-from-hawthorne' ); ?></label></th>
			<td>
				<input type="text" required id="api-key" name="api-key" placeholder="Ht**************" class="regular-text" value="<?php echo esc_html( $api_key ); ?>">
				<p class="description"><?php esc_html_e( 'Hawthorne API key.', 'import-from-hawthorne' ); ?></p>
			</td>
		</tr>

		<!-- API SECRET KEY -->
		<tr>
			<th scope="row"><label for="api-secret-key"><?php esc_html_e( 'API Secret Key', 'import-from-hawthorne' ); ?></label></th>
			<td>
				<div class="relative">
					<input type="password" required id="api-secret-key" name="api-secret-key" placeholder="****************" class="regular-text" value="<?php echo esc_html( $api_secret_key ); ?>">
					<span id="togglePassword" class="eye-btn dashicons dashicons-visibility"></span>
				</div>
				<p class="description"><?php esc_html_e( 'Hawthorne secret API key.', 'import-from-hawthorne' ); ?></p>
			</td>
		</tr>

		<!-- PRODUCTS ENDPOINT -->
		<tr>
			<th scope="row"><label for="products-endpoint"><?php esc_html_e( 'Products Endpoint', 'import-from-hawthorne' ); ?></label></th>
			<td>
				<input type="url" required id="products-endpoint" name="products-endpoint" placeholder="https://example.com" class="regular-text" value="<?php echo esc_url( $products_endpoint ); ?>">
				<p class="description"><?php esc_html_e( 'Hawthorne products import endpoint.', 'import-from-hawthorne' ); ?></p>
			</td>
		</tr>

		<!-- SINGLE PRODUCT - ADD TO CART BUTTON TEXT -->
		<tr>
			<th scope="row"><label for="single-product-add-to-cart-button-text"><?php esc_html_e( 'Single Product - Add to Cart Button Text', 'import-from-hawthorne' ); ?></label></th>
			<td>
				<input type="text" required id="single-product-add-to-cart-button-text" name="single-product-add-to-cart-button-text" placeholder="<?php esc_html_e( 'Default: Add to Wishlist', 'import-from-hawthorne' ); ?>" class="regular-text" value="<?php echo esc_html( $single_product_add_to_cart_button_text ); ?>">
				<p class="description"><?php esc_html_e( 'Add to cart button text on single product page.', 'import-from-hawthorne' ); ?></p>
			</td>
		</tr>

		<!-- ARCHIVE PRODUCT PAGES - ADD TO CART BUTTON TEXT -->
		<tr>
			<th scope="row"><label for="archive-product-pages-add-to-cart-button-text"><?php esc_html_e( 'Archive Product Pages - Add to Cart Button Text', 'import-from-hawthorne' ); ?></label></th>
			<td>
				<input type="text" required id="archive-product-pages-add-to-cart-button-text" name="archive-product-pages-add-to-cart-button-text" placeholder="<?php esc_html_e( 'Default: Add to Wishlist', 'import-from-hawthorne' ); ?>" class="regular-text" value="<?php echo esc_html( $archive_product_pages_add_to_cart_button_text ); ?>">
				<p class="description"><?php esc_html_e( 'Add to cart button text on archive product pages.', 'import-from-hawthorne' ); ?></p>
			</td>
		</tr>
	</tbody>
</table>
<?php submit_button( __( 'Update', 'import-from-hawthorne' ), 'primary', 'hawthorne_update_plugin_settings' ); ?>
