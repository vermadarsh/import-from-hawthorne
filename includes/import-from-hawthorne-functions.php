<?php
/**
 * This file is used for writing all the re-usable custom functions.
 *
 * @since 1.0.0
 * @package Import_From_Hawthorne
 * @subpackage Import_From_Hawthorne/includes
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Check, if the function exists.
 */
if ( ! function_exists( 'debug' ) ) {
	/**
	 * Debug function definition.
	 * Debugger function which shall be removed in production.
	 *
	 * @since 1.0.0
	 */
	function debug( $params ) {
		echo '<pre>';
		print_r( $params );
		echo '</pre>';
	}
}

/**
 * Check, if the function exists.
 */
if ( ! function_exists( 'hawthorne_get_authentication_signature' ) ) {
	/**
	 * Get the Hawthorne API authentication signature.
	 *
	 * @param string $api_key Hawthorne API key.
	 * @param string $api_secret_key Hawthorne api secret key.
	 * @param string $api_base_url API base URL.
	 * @param string $current_time Current server time.
	 * @return string
	 * @since 1.0.0
	 */
	function hawthorne_get_authentication_signature( $api_key, $api_secret_key, $api_base_url, $current_time ) {
		$signature_args = array(
			'format'   => 'json',
			'X-ApiKey' => $api_key,
			'time'     => $current_time,
		);

		return strtoupper( hash_hmac( "sha256", add_query_arg( $signature_args, $api_base_url ), $api_secret_key ) );
	}
}

/**
 * Check, if the function exists.
 */
if ( ! function_exists( 'hawthorne_get_plugin_settings' ) ) {
	/**
	 * Get plugin setting by setting index.
	 *
	 * @param string $setting Holds the setting index.
	 * @return boolean|string|array|int
	 * @since 1.0.0
	 */
	function hawthorne_get_plugin_settings( $setting ) {
		$plugin_settings = get_option( 'hawthorne_integration_plugin_settings' ); // Get the settings from the database.

		// Swutch control to get the actual setting data.
		switch ( $setting ) {
			case 'api_key':
				$data = ( ! empty( $plugin_settings['api_key'] ) ) ? $plugin_settings['api_key'] : '';
				break;

			case 'api_secret_key':
				$data = ( ! empty( $plugin_settings['api_secret_key'] ) ) ? $plugin_settings['api_secret_key'] : '';
				break;

			case 'products_endpoint':
				$data = ( ! empty( $plugin_settings['products_endpoint'] ) ) ? $plugin_settings['products_endpoint'] : '';
				break;

			default:
				$data = -1;
		}

		return $data;
	}
}

/**
 * Check, if the function exists.
 */
if ( ! function_exists( 'hawthorne_fetch_products' ) ) {
	/**
	 * Get the products imported from Hawthorne.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	function hawthorne_fetch_products() {
		hawthorne_write_import_log( 'NOTICE: Starting to import products.' ); // Write the log.
		$products_endpoint = hawthorne_get_plugin_settings( 'products_endpoint' );
		$api_key           = hawthorne_get_plugin_settings( 'api_key' );
		$api_secret_key    = hawthorne_get_plugin_settings( 'api_secret_key' );
		$current_time      = gmdate( 'Y-m-d\TH:i:s\Z' );
		$api_args          = array(
			'headers'      => array_merge(
				array(
					'Content-Type' => 'application/json',
				)
			),
			'body'         => array(
				'format'    => 'json',
				'X-ApiKey'  => $api_key,
				'time'      => $current_time,
				'signature' => hawthorne_get_authentication_signature( $api_key, $api_secret_key, $products_endpoint, $current_time ),
			),
			'sslverify'    => false,
			'timeout'      => 600,
		);

		$api_response      = wp_remote_get( $products_endpoint, $api_args ); // Shoot the API.

		debug( $api_response ); die;

		$api_response_code = wp_remote_retrieve_response_code( $api_response ); // Get the response code.
		if ( 200 === $api_response_code ) {
			$api_response_message .= 'hawthorne_product_import_api_call_success';
			$api_response_body     = wp_remote_retrieve_body( $api_response ); // Get the response body.
			

		} else {
			$api_response_body     = wp_remote_retrieve_body( $api_response ); // Get the response body.
			$api_response_body     = json_decode( $api_response_body, true );
			$api_response_message .= ( ! empty( $api_response_body['Message'] ) ) ? $api_response_body['Message'] : '';
			$api_response_message .= ( empty( $api_response_body['Message'] ) ) ? ( ( ! empty( $api_response['response']['message'] ) ) ? $api_response['response']['message'] : '' ) : $api_response_message;
		}
	}
}

/**
 * Check, if the function exists.
 */
if ( ! function_exists( 'hawthorne_write_import_log' ) ) {
	/**
	 * Write log to the log file.
	 *
	 * @param string $message Holds the log message.
	 * @return void
	 */
	function hawthorne_write_import_log( $message = '' ) {
		global $wp_filesystem;

		if ( empty( $message ) ) {
			return;
		}

		require_once ABSPATH . '/wp-admin/includes/file.php';
		WP_Filesystem();

		$local_file = HAWTHORNE_LOG_DIR_PATH . 'import-log.log';

		// Fetch the old content.
		if ( $wp_filesystem->exists( $local_file ) ) {
			$content  = $wp_filesystem->get_contents( $local_file );
			$content .= "\n" . rothco_get_current_datetime( 'Y-m-d H:i:s' ) . ' :: ' . $message;
		}

		$wp_filesystem->put_contents(
			$local_file,
			$content,
			FS_CHMOD_FILE // predefined mode settings for WP files.
		);
	}
}
