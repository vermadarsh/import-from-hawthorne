<?php
/**
 * This file is used for templating the plugin send cart settings.
 *
 * @since 1.0.0
 * @package Import_From_Hawthorne
 * @subpackage Import_From_Hawthorne/admin/settings
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

// Check if the submit button is clicked.
$update_plugin_settings = filter_input( INPUT_POST, 'hawthorne_update_send_cart_plugin_settings', FILTER_SANITIZE_STRING );
if ( ! is_null( $update_plugin_settings ) ) {
	$open_send_cart_modal_button_text = filter_input( INPUT_POST, 'open-send-cart-modal-button-text', FILTER_SANITIZE_STRING );
	$sent_cart_success_message        = filter_input( INPUT_POST, 'sent-cart-success-message', FILTER_SANITIZE_STRING );
	$clear_cart                       = filter_input( INPUT_POST, 'clear-cart', FILTER_SANITIZE_STRING );
	$clear_cart                       = ( is_null( $clear_cart ) ) ? 'no' : 'yes';

	// Get the settings.
	$plugin_settings = get_option( 'hawthorne_integration_plugin_settings' );
	$plugin_settings = ( empty( $plugin_settings ) ) ? array() : $plugin_settings;

	// Add send cart settings to the settings array.
	$plugin_settings['open_send_cart_modal_button_text'] = $open_send_cart_modal_button_text;
	$plugin_settings['sent_cart_success_message']        = $sent_cart_success_message;
	$plugin_settings['clear_cart']                       = $clear_cart;

	// Array for the settings.
	update_option( 'hawthorne_integration_plugin_settings', $plugin_settings, false );

	// Print the success message.
	?>
	<div class="notice updated is-dismissible" id="message"><p><?php esc_html_e( 'Plugin settings updated.', 'import-from-hawthorne' ); ?></p></div>
	<?php
}

// Get the plugin settings.
$open_send_cart_modal_button_text = hawthorne_get_plugin_settings( 'open_send_cart_modal_button_text' );
$sent_cart_success_message        = hawthorne_get_plugin_settings( 'sent_cart_success_message' );
$clear_cart                       = hawthorne_get_plugin_settings( 'clear_cart' );
?>
<table class="form-table hack-import-subscribers-table">
	<tbody>
		<!-- OPEN SEND CART MODAL BUTTON TEXT -->
		<tr>
			<th scope="row"><label for="open-send-cart-modal-button-text"><?php esc_html_e( 'Open "Send Cart" Modal Button Text', 'import-from-hawthorne' ); ?></label></th>
			<td>
				<input type="text" id="open-send-cart-modal-button-text" name="open-send-cart-modal-button-text" placeholder="<?php esc_html_e( 'Default: Send Cart to Greenlight', 'import-from-hawthorne' ); ?>" class="regular-text" value="<?php echo esc_html( $open_send_cart_modal_button_text ); ?>">
				<p class="description"><?php esc_html_e( 'The button that appears on cart page that initiates the cart sending process.', 'import-from-hawthorne' ); ?></p>
			</td>
		</tr>

		<!-- SENT CART SUCCESS MESSAGE -->
		<tr>
			<th scope="row"><label for="sent-cart-success-message"><?php esc_html_e( 'Success Message after Cart is Sent', 'import-from-hawthorne' ); ?></label></th>
			<td>
				<input type="text" id="sent-cart-success-message" name="sent-cart-success-message" placeholder="<?php esc_html_e( 'Default: Cart is sent successfully !!', 'import-from-hawthorne' ); ?>" class="regular-text" value="<?php echo esc_html( $sent_cart_success_message ); ?>">
				<p class="description"><?php esc_html_e( 'Success message that is shown when the cart is successfully sent.', 'import-from-hawthorne' ); ?></p>
			</td>
		</tr>

		<!-- CLEAR CART -->
		<tr>
			<th scope="row"><label for="clear-cart"><?php esc_html_e( 'Clear Cart after Cart is Sent', 'import-from-hawthorne' ); ?></label></th>
			<td>
				<input type="checkbox" id="clear-cart" name="clear-cart" value="on" <?php echo esc_attr( ( ! empty( $clear_cart ) && 'yes' === $clear_cart ) ? 'checked' : '' ); ?>>
				<p class="description"><?php esc_html_e( 'Check this checkbox if the cart should be cleared it is sent to Greenlight.', 'import-from-hawthorne' ); ?></p>
			</td>
		</tr>
	</tbody>
</table>
<?php submit_button( __( 'Update', 'import-from-hawthorne' ), 'primary', 'hawthorne_update_send_cart_plugin_settings' ); ?>
