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
	 * @return array|string|boolean
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
		$api_response_code = wp_remote_retrieve_response_code( $api_response ); // Get the response code.

		// If everything is OK.
		if ( 200 === $api_response_code ) {
			$api_response_body = wp_remote_retrieve_body( $api_response ); // Get the response body.
			return ( ! empty( $api_response_body ) ) ? json_decode( $api_response_body ) : array();
		} else {
			return false;
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

		$local_file = HAWTHORNE_LOG_DIR_PATH . 'import-log.log'; // Log file path.

		// Fetch the old content.
		if ( $wp_filesystem->exists( $local_file ) ) {
			$content  = $wp_filesystem->get_contents( $local_file );
			$content .= "\n" . hawthorne_get_current_datetime( 'Y-m-d H:i:s' ) . ' :: ' . $message;
		}

		$wp_filesystem->put_contents(
			$local_file,
			$content,
			FS_CHMOD_FILE // predefined mode settings for WP files.
		);
	}
}

/**
 * Check, if the function exists.
 */
if ( ! function_exists( 'hawthorne_get_current_datetime' ) ) {
	/**
	 * Return the current date according to local time.
	 *
	 * @param string $format Holds the format string.
	 * @return string
	 */
	function hawthorne_get_current_datetime( $format = 'Y-m-d' ) {
		$timezone_format = _x( $format, 'timezone date format' );

		return date_i18n( $timezone_format );
	}
}

/**
 * Check, if the function exists.
 */
if ( ! function_exists( 'hawthorne_create_product' ) ) {
	/**
	 * Create new product and insert that into the database.
	 *
	 * @param array $part New product data.
	 * @return int
	 */
	function hawthorne_create_product( $part ) {
	}
}

/**
 * Check, if the function exists.
 */
if ( ! function_exists( 'hawthorne_update_product' ) ) {
	/**
	 * Update the old product into the database.
	 *
	 * @param int   $existing_product_id Existing product data.
	 * @param array $part API product data.
	 * @return int
	 */
	function hawthorne_update_product( $existing_product_id, $part ) {
		global $wpdb;
		$sku     = ( ! empty( $part['Id'] ) ) ? $part['Id'] : '';
		$msrp    = ( ! empty( $part['EachMsrp'] ) ) ? $part['EachMsrp'] : '';
		$content = ( ! empty( $part['Description'] ) ) ? $part['Description'] : '';

		// Category data.
		$hawthorne_category_id     = ( ! empty( $part['CategoryId'] ) ) ? $part['CategoryId'] : '';
		$hawthorne_category_web_id = ( ! empty( $part['CategoryWebId'] ) ) ? $part['CategoryWebId'] : '';
		$hawthorne_category_name   = ( ! empty( $part['CategoryName'] ) ) ? $part['CategoryName'] : '';

		// If the category data is available, update the product for the same.
		if ( ! empty( $hawthorne_category_id ) && ! empty( $hawthorne_category_web_id ) && ! empty( $hawthorne_category_name ) ) {
			hawthorne_update_product_category_data( $existing_product_id, $hawthorne_category_id, $hawthorne_category_web_id, $hawthorne_category_name );
			die("pool");
		}

		die("before update");
		// Update the data now.
		update_post_meta( $existing_product_id, '_sku', $sku );
		update_post_meta( $existing_product_id, '_regular_price', $msrp );
		update_post_meta( $existing_product_id, '_price', $msrp );

		// Update the post content now.
		$wpdb->update(
			$wpdb->posts,
			array(
				'post_content' => $content,
			),
			array(
				'ID' => $existing_product_id,
			),
			array(
				'%s',
			),
			array(
				'%d',
			)
		);
	}
}

/**
 * Check, if the function exists.
 */
if ( ! function_exists( 'hawthorne_update_product_category_data' ) ) {
	/**
	 * Update the category data of the product.
	 *
	 * @param int $product_id Product ID from the WordPress database.
	 * @param string 
	 */
	function hawthorne_update_product_category_data( $product_id, $hawthorne_category_id, $hawthorne_category_web_id, $hawthorne_category_name ) {
		// Get the category terms already assigned to the product.
		$product_cats              = wp_get_object_terms( $product_id, 'product_cat' );
		$category_already_assigned = false;

		// If there is no category assigned, we need to assign category.
		if ( empty( $product_cats ) || ! is_array( $product_cats ) ) {
			$category_already_assigned = false;
		}

		// Check if the category received from the API is one of the assigned.
		foreach ( $product_cats as $product_cat ) {
			// If the category name matches, break the loop.
			if ( $hawthorne_category_name === htmlspecialchars_decode( $product_cat->name ) ) {
				$category_already_assigned = true;
				break;
			}
		}

		// Return, if the category is already assigned.
		if ( true === $category_already_assigned ) {
			return;
		}

		/**
		 * Just in case the category is not assigned, let's assign it.
		 * Before that, we need to check if that category exists or not.
		 */
		$category_term_exists = term_exists( $hawthorne_category_name, 'product_cat' );
		var_dump( $hawthorne_category_name );
		var_dump( $category_term_exists );

		// If the category term doesn't exist, create one.
		if ( is_null( $category_term_exists ) ) {
			echo 'hello';
			die;
			$category_term_exists = hawthorne_create_product_category_term( $hawthorne_category_id, $hawthorne_category_web_id, $hawthorne_category_name );
		}
		var_dump( $category_term_exists );
		var_dump( $hawthorne_category_name );
		die;
	}
}

/**
 * Check, if the function exists.
 */
if ( ! function_exists( 'hawthorne_create_product_category_term' ) ) {
	/**
	 * Create a category term and save in the database.
	 *
	 * @param int $product_id Product ID from the WordPress database.
	 * @param string 
	 */
	function hawthorne_create_product_category_term( $hawthorne_category_id, $hawthorne_category_web_id, $hawthorne_category_name ) {
		// Insert the term now.
		$term_details = wp_insert_term(
			$hawthorne_category_name,
			'product_cat',
			array(
				'description' => '',
				'slug'        => $hawthorne_category_web_id,
				'parent'      => 0,
			)
		);

		// Update the meta details.
		update_term_meta( $term_details['term_id'], 'hawthorne_id', $hawthorne_category_id );

		return $term_details['term_id'];
	}
}
