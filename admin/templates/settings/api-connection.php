<?php
/**
 * This file is used for templating the plugin API connection settings.
 *
 * @since 1.0.0
 * @package Import_From_Hawthorne
 * @subpackage Import_From_Hawthorne/admin/settings
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

// Check if the submit button is clicked.
$test_api_connection = filter_input( INPUT_POST, 'hawthorne_test_api_connection', FILTER_SANITIZE_STRING );
if ( ! is_null( $test_api_connection ) ) {
	$api_base_url   = filter_input( INPUT_POST, 'api-base-url', FILTER_SANITIZE_STRING );
	$custom_headers = filter_input( INPUT_POST, 'custom-headers' );
	$custom_headers = ( ! empty( $custom_headers ) ) ? json_decode( $custom_headers, true ) : array();

	// Testing the API connection now.
	$api_key        = hawthorne_get_plugin_settings( 'api_key' );
	$api_secret_key = hawthorne_get_plugin_settings( 'api_secret_key' );
	$current_time   = gmdate( 'Y-m-d\TH:i:s\Z' );
	$api_args       = array(
		'headers'   => array_merge(
			$custom_headers,
			array(
				'Content-Type' => 'application/json',
			)
		),
		'body'      => array(
			'format'    => 'json',
			'X-ApiKey'  => $api_key,
			'time'      => $current_time,
			'signature' => hawthorne_get_authentication_signature( $api_key, $api_secret_key, $api_base_url, $current_time ),
		),
		'sslverify' => false,
		'timeout'   => 600,
	);

	$api_response      = wp_remote_get( $api_base_url, $api_args ); // Shoot the API.
	$api_response_code = wp_remote_retrieve_response_code( $api_response ); // Get the response code.

	// If the response is OK.
	if ( 200 === $api_response_code ) {
		$api_response_body = wp_remote_retrieve_body( $api_response ); // Get the response body.
		$api_response_body = substr( $api_response_body, 0, 1000 ) . '...';
		$success_message   = __( 'Success!! API is working.', 'import-from-hawthorne' ) . '<br />' . $api_response_body;

		// Print the success message.
		?>
		<div class="notice updated is-dismissible" id="message"><p><?php echo wp_kses_post( $success_message ); ?></p></div>
		<?php
	} else {
		$api_response_body    = wp_remote_retrieve_body( $api_response ); // Get the response body.
		$api_response_body    = json_decode( $api_response_body, true );
		$api_response_message = ( ! empty( $api_response_body['Message'] ) ) ? $api_response_body['Message'] : '';
		$api_response_message = ( empty( $api_response_body['Message'] ) ) ? ( ( ! empty( $api_response['response']['message'] ) ) ? $api_response['response']['message'] : '' ) : $api_response_message;

		// If there is some error message to be displayed.
		?>
		<div class="error">
			<p>
				<?php
				/* translators: 1: %s: error message */
				echo wp_kses_post( sprintf( __( 'Error: %1$s', 'import-from-hawthorne' ), $api_response_message ) );
				?>
			</p>
		</div>
		<?php
	}
}
?>
<table class="form-table">
	<tbody>
		<!-- API BASE URL -->
		<tr>
			<th scope="row"><label for="api-base-url"><?php esc_html_e( 'API Base URL', 'import-from-hawthorne' ); ?></label></th>
			<td>
				<input type="url" required id="api-base-url" name="api-base-url" placeholder="https://example.com" class="regular-text">
				<p class="description"><?php esc_html_e( 'Hawthorne API base URL.', 'import-from-hawthorne' ); ?></p>
			</td>
		</tr>

		<!-- CUSTOM HEADERS -->
		<tr>
			<th scope="row"><label for="custom-headers"><?php esc_html_e( 'Custom Headers', 'import-from-hawthorne' ); ?></label></th>
			<td>
				<textarea class="regular-text" rows="6" id="custom-headers" name="custom-headers" placeholder='{"key1":"value1","key2":"value2"}'></textarea>
				<p class="description"><?php esc_html_e( 'Custom headers. These header values will be merged with default headers.', 'import-from-hawthorne' ); ?></p>
			</td>
		</tr>

	</tbody>
</table>
<?php submit_button( __( 'Test API Connection', 'import-from-hawthorne' ), 'primary', 'hawthorne_test_api_connection' ); ?>
