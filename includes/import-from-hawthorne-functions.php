<?php
/**
 * This file is used for writing all the re-usable custom functions.
 *
 * @since 1.0.0
 * @package Import_From_Hawthorne
 * @subpackage Import_From_Hawthorne/includes
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

// Remove cart page actions to replace the button, "Proceed to Checkout".
remove_action( 'woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20 );
remove_action( 'woocommerce_widget_shopping_cart_buttons', 'woocommerce_widget_shopping_cart_proceed_to_checkout', 20 );

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

		return strtoupper( hash_hmac( 'sha256', add_query_arg( $signature_args, $api_base_url ), $api_secret_key ) );
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

			case 'open_send_cart_modal_button_text':
				$data = ( ! empty( $plugin_settings['open_send_cart_modal_button_text'] ) ) ? $plugin_settings['open_send_cart_modal_button_text'] : __( 'Send Cart to Greenlight', 'import-from-hawthorne' );
				break;

			case 'sent_cart_success_message':
				$data = ( ! empty( $plugin_settings['sent_cart_success_message'] ) ) ? $plugin_settings['sent_cart_success_message'] : __( 'Cart is sent successfully !!', 'import-from-hawthorne' );
				break;

			case 'clear_cart':
				$data = ( ! empty( $plugin_settings['clear_cart'] ) ) ? $plugin_settings['clear_cart'] : '';
				break;

			case 'single_product_add_to_cart_button_text':
				$data = ( ! empty( $plugin_settings['single_product_add_to_cart_button_text'] ) ) ? $plugin_settings['single_product_add_to_cart_button_text'] : __( 'Add to Wishlist', 'import-from-hawthorne' );
				break;

			case 'archive_product_pages_add_to_cart_button_text':
				$data = ( ! empty( $plugin_settings['archive_product_pages_add_to_cart_button_text'] ) ) ? $plugin_settings['archive_product_pages_add_to_cart_button_text'] : __( 'Add to Wishlist', 'import-from-hawthorne' );
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
			'headers'   => array_merge(
				array(
					'Content-Type' => 'application/json',
				)
			),
			'sslverify' => false,
			'timeout'   => 600,
			'body'      => array(
				'format'    => 'json',
				'X-ApiKey'  => $api_key,
				'time'      => $current_time,
				'signature' => hawthorne_get_authentication_signature( $api_key, $api_secret_key, $products_endpoint, $current_time ),
			),
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
	 * @param array $product_title New product title.
	 * @return int
	 */
	function hawthorne_create_product( $product_title ) {
		// Save the product post object in the database and return the product ID.
		return wp_insert_post(
			array(
				'post_title'  => $product_title,
				'post_status' => 'publish',
				'post_author' => 1,
				'post_date'   => gmdate( 'Y-m-d H:i:s' ),
				'post_type'   => 'product',
			)
		);
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
	 * @return void
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
		}

		// Brand data.
		$hawthorne_brand_id     = ( ! empty( $part['BrandId'] ) ) ? $part['BrandId'] : '';
		$hawthorne_brand_web_id = ( ! empty( $part['BrandWebId'] ) ) ? $part['BrandWebId'] : '';
		$hawthorne_brand_name   = ( ! empty( $part['BrandName'] ) ) ? $part['BrandName'] : '';

		// If the brand data is available, update the product for the same.
		if ( ! empty( $hawthorne_brand_id ) && ! empty( $hawthorne_brand_web_id ) && ! empty( $hawthorne_brand_name ) ) {
			hawthorne_update_product_brand_data( $existing_product_id, $hawthorne_brand_id, $hawthorne_brand_web_id, $hawthorne_brand_name );
		}

		// Product image.
		$product_image_url     = ( ! empty( $part['ImageMedium'] ) ) ? $part['ImageMedium'] : '';
		$product_image_url     = ( ! empty( $product_image_url ) ) ? $product_image_url : ( ( ! empty( $part['ImageFamilyMedium'] ) ) ? $part['ImageFamilyMedium'] : '' );
		$product_has_thumbnail = has_post_thumbnail( $existing_product_id );

		// If the product doesn't have featured image set, we need to set one.
		if ( ! empty( $product_image_url ) && false === $product_has_thumbnail ) {
			hawthorne_update_product_featured_image( $product_image_url, $existing_product_id );
		}

		// Published date.
		$published_date = ( ! empty( $part['NewDate'] ) ) ? $part['NewDate'] : '';
		$published_date = ( ! empty( $published_date ) ) ? gmdate( 'Y-m-d H:i:s', strtotime( $published_date ) ) : gmdate( 'Y-m-d H:i:s' );

		// Product dimentional details.
		$product_upc        = ( ! empty( $part['EachUpc'] ) ) ? $part['EachUpc'] : '';
		$product_volume     = ( ! empty( $part['EachVolume'] ) ) ? $part['EachVolume'] : '';
		$product_dim_weight = ( ! empty( $part['EachDimWeight'] ) ) ? $part['EachDimWeight'] : '';
		$product_weight     = ( ! empty( $part['EachWeight'] ) ) ? $part['EachWeight'] : '';
		$product_length     = ( ! empty( $part['EachLength'] ) ) ? $part['EachLength'] : '';
		$product_width      = ( ! empty( $part['EachWidth'] ) ) ? $part['EachWidth'] : '';
		$product_height     = ( ! empty( $part['EachHeight'] ) ) ? $part['EachHeight'] : '';

		// Update the data now.
		update_post_meta( $existing_product_id, '_sku', $sku );
		update_post_meta( $existing_product_id, '_regular_price', $msrp );
		update_post_meta( $existing_product_id, '_price', $msrp );
		update_post_meta( $existing_product_id, '_height', $product_height );
		update_post_meta( $existing_product_id, '_width', $product_width );
		update_post_meta( $existing_product_id, '_length', $product_length );
		update_post_meta( $existing_product_id, '_weight', $product_weight );
		update_post_meta( $existing_product_id, '_dim_weight', $product_dim_weight );
		update_post_meta( $existing_product_id, '_volume', $product_volume );
		update_post_meta( $existing_product_id, '_upc', $product_upc );

		// Update the post content now.
		$wpdb->update(
			$wpdb->posts,
			array(
				'post_content' => $content,
				'post_date'    => $published_date,
			),
			array(
				'ID' => $existing_product_id,
			),
			array(
				'%s',
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
	 * @param int    $product_id Product ID from the WordPress database.
	 * @param string $hawthorne_category_id Category ID fetched from Hawthorne.
	 * @param string $hawthorne_category_web_id Category slug fetched from Hawthorne.
	 * @param string $hawthorne_category_name Category name fetched from Hawthorne.
	 * @return void
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
			if ( htmlspecialchars_decode( $product_cat->name ) === $hawthorne_category_name ) {
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
		$category_term_exists = term_exists( $hawthorne_category_web_id, 'product_cat' );
		$category_term_id     = ( is_null( $category_term_exists ) ) ? hawthorne_create_product_category_term( $hawthorne_category_id, $hawthorne_category_web_id, $hawthorne_category_name ) : ( ( ! empty( $category_term_exists['term_id'] ) ) ? (int) $category_term_exists['term_id'] : 0 );

		// Now that the category term is created, let's assign this term to the product.
		wp_set_object_terms( $product_id, $category_term_id, 'product_cat' );
	}
}

/**
 * Check, if the function exists.
 */
if ( ! function_exists( 'hawthorne_update_product_brand_data' ) ) {
	/**
	 * Update the brand data of the product.
	 *
	 * @param int    $product_id Product ID from the WordPress database.
	 * @param string $hawthorne_brand_id Brand ID fetched from Hawthorne.
	 * @param string $hawthorne_brand_web_id Brand slug fetched from Hawthorne.
	 * @param string $hawthorne_brand_name Brand name fetched from Hawthorne.
	 * @return void
	 */
	function hawthorne_update_product_brand_data( $product_id, $hawthorne_brand_id, $hawthorne_brand_web_id, $hawthorne_brand_name ) {
		// Get the brand terms already assigned to the product.
		$product_brands         = wp_get_object_terms( $product_id, 'product_brand' );
		$brand_already_assigned = false;

		// If there is no brand assigned, we need to assign one.
		if ( empty( $product_brands ) || ! is_array( $product_brands ) ) {
			$brand_already_assigned = false;
		}

		// Check if the brand received from the API is one of the assigned.
		foreach ( $product_brands as $product_brand ) {
			// If the brand name matches, break the loop.
			if ( htmlspecialchars_decode( $product_brand->name ) === $hawthorne_brand_name ) {
				$brand_already_assigned = true;
				break;
			}
		}

		// Return, if the brand is already assigned.
		if ( true === $brand_already_assigned ) {
			return;
		}

		/**
		 * Just in case the brand is not assigned, let's assign it.
		 * Before that, we need to check if that brand exists or not.
		 */
		$brand_term_exists = term_exists( $hawthorne_brand_web_id, 'product_brand' );
		$brand_term_id     = ( is_null( $brand_term_exists ) ) ? hawthorne_create_product_brand_term( $hawthorne_brand_id, $hawthorne_brand_web_id, $hawthorne_brand_name ) : ( ( ! empty( $brand_term_exists['term_id'] ) ) ? (int) $brand_term_exists['term_id'] : 0 );

		// Now that the brand term is created, let's assign this term to the product.
		wp_set_object_terms( $product_id, $brand_term_id, 'product_brand' );
	}
}

/**
 * Check, if the function exists.
 */
if ( ! function_exists( 'hawthorne_create_product_category_term' ) ) {
	/**
	 * Create a category term and save in the database.
	 *
	 * @param string $hawthorne_category_id Category ID fetched from Hawthorne.
	 * @param string $hawthorne_category_web_id Category slug fetched from Hawthorne.
	 * @param string $hawthorne_category_name Category name fetched from Hawthorne.
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

/**
 * Check, if the function exists.
 */
if ( ! function_exists( 'hawthorne_create_product_brand_term' ) ) {
	/**
	 * Create a brand term and save in the database.
	 *
	 * @param string $hawthorne_brand_id Brand ID fetched from Hawthorne.
	 * @param string $hawthorne_brand_web_id Brand slug fetched from Hawthorne.
	 * @param string $hawthorne_brand_name Brand name fetched from Hawthorne.
	 */
	function hawthorne_create_product_brand_term( $hawthorne_brand_id, $hawthorne_brand_web_id, $hawthorne_brand_name ) {
		// Insert the term now.
		$term_details = wp_insert_term(
			$hawthorne_brand_name,
			'product_brand',
			array(
				'description' => '',
				'slug'        => $hawthorne_brand_web_id,
				'parent'      => 0,
			)
		);

		// Update the meta details.
		update_term_meta( $term_details['term_id'], 'hawthorne_id', $hawthorne_brand_id );

		return $term_details['term_id'];
	}
}

/**
 * Check if the function exists.
 */
if ( ! function_exists( 'hawthorne_register_product_brand_taxonomy' ) ) {
	/**
	 * This function registers product brand taxonomy.
	 */
	function hawthorne_register_product_brand_taxonomy() {
		$labels = array(
			'name'              => _x( 'Brands', 'taxonomy general name', 'import-from-hawthorne' ),
			'singular_name'     => _x( 'Brand', 'taxonomy singular name', 'import-from-hawthorne' ),
			'search_items'      => __( 'Search Brand', 'import-from-hawthorne' ),
			'all_items'         => __( 'All Brands', 'import-from-hawthorne' ),
			'parent_item'       => __( 'Parent Brand', 'import-from-hawthorne' ),
			'parent_item_colon' => __( 'Parent Brand:', 'import-from-hawthorne' ),
			'edit_item'         => __( 'Edit Brand', 'import-from-hawthorne' ),
			'update_item'       => __( 'Update Brand', 'import-from-hawthorne' ),
			'add_new_item'      => __( 'Add New Brand', 'import-from-hawthorne' ),
			'new_item_name'     => __( 'New Brand Name', 'import-from-hawthorne' ),
			'menu_name'         => __( 'Brands', 'import-from-hawthorne' ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_in_menu'      => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'product_brand' ),
		);

		register_taxonomy( 'product_brand', array( 'product' ), $args );
	}
}

/**
 * Check if the function exists.
 */
if ( ! function_exists( 'hawthorne_update_product_featured_image' ) ) {
	/**
	 * Update the featured image of the product.
	 *
	 * @param string $image_url Image URL.
	 * @param int    $product_id Existing product ID.
	 *
	 * @since 1.0.0
	 */
	function hawthorne_update_product_featured_image( $image_url, $product_id ) {
		$image_basename = pathinfo( $image_url );
		$image_name     = $image_basename['basename'];
		$upload_dir     = wp_upload_dir();
		$image_data     = hawthorne_get_image_data( $image_url );

		// Return, if image data is not properly received.
		if ( is_null( $image_data ) ) {
			return;
		}

		$unique_file_name = wp_unique_filename( $upload_dir['path'], $image_name );
		$filename         = basename( $unique_file_name );

		// Check folder permission and define file location.
		if ( wp_mkdir_p( $upload_dir['path'] ) ) {
			$file = $upload_dir['path'] . '/' . $filename;
		} else {
			$file = $upload_dir['basedir'] . '/' . $filename;
		}

		// Create the image  file on the server.
		file_put_contents( $file, $image_data );

		// Check image file type.
		$wp_filetype = wp_check_filetype( $filename, null );

		// Set attachment data.
		$attachment = array(
			'post_mime_type' => $wp_filetype['type'],
			'post_title'     => sanitize_file_name( $filename ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		);

		// Create the attachment.
		$attach_id = wp_insert_attachment( $attachment, $file, 0 );

		// Include image.php file.
		require_once ABSPATH . 'wp-admin/includes/image.php';

		// Define attachment metadata.
		$attach_data = wp_generate_attachment_metadata( $attach_id, $file );

		// Assign metadata to attachment.
		wp_update_attachment_metadata( $attach_id, $attach_data );

		// Assign the attachment ID to the product.
		set_post_thumbnail( $product_id, $attach_id );
	}
}

/**
 * Check if the function exists.
 */
if ( ! function_exists( 'hawthorne_get_image_data' ) ) {
	/**
	 * Get image data from image URL.
	 *
	 * @param string $image_url Holds the image URL.
	 * @return string
	 */
	function hawthorne_get_image_data( $image_url ) {

		if ( '' === $image_url ) {
			return;
		}

		$response      = wp_remote_get( $image_url );
		$response_code = wp_remote_retrieve_response_code( $response );

		if ( 200 !== $response_code ) {
			return;
		}

		return wp_remote_retrieve_body( $response );
	}
}

/**
 * Check if the function exists.
 */
if ( ! function_exists( 'hawthorne_get_attachment_url_from_attachment_id' ) ) {
	/**
	 * Returns the image URL by attachment ID.
	 *
	 * @param int $image_id Holds the attachment ID.
	 * @return string
	 */
	function hawthorne_get_attachment_url_from_attachment_id( $image_id ) {

		return ( empty( $image_id ) ) ? wc_placeholder_img_src() : wp_get_attachment_url( $image_id );
	}
}
