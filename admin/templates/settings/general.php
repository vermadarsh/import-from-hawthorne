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
	$api_key        = filter_input( INPUT_POST, 'api-key', FILTER_SANITIZE_STRING );
	$api_secret_key = filter_input( INPUT_POST, 'api-secret-key', FILTER_SANITIZE_STRING );

	// Array for the settings.
	update_option(
		'hawthorne_integration_plugin_settings',
		array(
			'api_key'        => $api_key,
			'api_secret_key' => $api_secret_key,
		),
		false
	);

	// Print the success message.
	?>
	<div class="notice updated is-dismissible" id="message"><p><?php esc_html_e( 'Plugin settings updated.', 'import-from-hawthorne' ); ?></p></div>
	<?php
}

// Get the plugin settings.
$api_key        = hawthorne_get_plugin_settings( 'api_key' );
$api_secret_key = hawthorne_get_plugin_settings( 'api_secret_key' );
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
				<input type="password" required id="api-secret-key" name="api-secret-key" placeholder="****************" class="regular-text" value="<?php echo esc_html( $api_secret_key ); ?>">
				<p class="description"><?php esc_html_e( 'Hawthorne secret API key.', 'import-from-hawthorne' ); ?></p>
			</td>
		</tr>
	</tbody>
</table>
<?php submit_button( __( 'Update', 'import-from-hawthorne' ), 'primary', 'hawthorne_update_plugin_settings' ); ?>
