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

			default:
				$data = -1;
		}

		return $data;
	}
}
