/**
 * Admin script file.
 */
jQuery( document ).ready( function( $ ) {
	'use strict';

	// Localized variables.
	var ajaxurl                    = Hawthorne_Admin_Script_Vars.ajaxurl;
	var product_import_button_text = Hawthorne_Admin_Script_Vars.product_import_button_text;
	var product_import_admin_url   = Hawthorne_Admin_Script_Vars.product_import_admin_url;

	// Insert the import button.
	$( '<div class="dropdown"><a href="' + product_import_admin_url + '" class="page-title-action import-sub-dropdown">' + product_import_button_text + '</a></div>' ).insertAfter( 'body.post-type-product .wrap a.page-title-action:nth-child(3)' );
	$( 'body.post-type-product .wrap a.page-title-action:nth-child(3)' ).addClass( 'dropdown-btn' );
	$( '<a href="javascript:void(0);" class="page-title-action import-sub-dropdown-btn"><span class="dashicons dashicons-arrow-down"></span></a>' ).insertAfter( '.dropdown-btn' );
	$( document ).on( 'click', '.import-sub-dropdown-btn', function () {
		$( '.dropdown' ).toggleClass( 'show' );
	} );

	// Remove the notice from elementor.
	$( '.e-notice--extended' ).remove();

	// When the window is completely loaded, make the AJAX call to start importing the products.
	$( window ).load( function() {
		var new_products_added     = 0;
		var old_products_updated   = 0;
		var products_import_failed = 0;
		kickoff_product_import( 258, new_products_added, old_products_updated, products_import_failed );
	} );

	/**
	 * Kickoff products import.
	 *
	 * @param {*} page
	 * @param {*} new_products_added
	 * @param {*} old_products_updated
	 * @param {*} products_import_failed
	 */
	function kickoff_product_import( page, new_products_added, old_products_updated, products_import_failed ) {
		$.ajax( {
			dataType: 'JSON',
			url: ajaxurl,
			type: 'POST',
			data: {
				action: 'kickoff_products_import',
				page: page,
				new_products_added: new_products_added,
				old_products_updated: old_products_updated,
				products_import_failed: products_import_failed,
			},
			cache: true,
			success: function ( response ) {
				// Exit, if there is an invalid response.
				if ( 0 === response ) {
					console.log( 'grow with greenlight: invalid ajax call for products import' );
					return false;
				}

				// If all the products are imported.
				var code = response.data.code;
				if ( 'products-imported' === code ) {
					$( '.import-from-hawthorne-wrapper .finish-card' ).show(); // Show the card details for imported products.
					$( '.import-from-hawthorne-wrapper .importing-card' ).hide(); // Hide the the progress bar.
					console.log( 'all the products imported.' );
					return false;
				}

				// If the import is in process.
				if( 'products-import-in-progress' === code ) {
					// Set the progress bar.
					make_the_bar_progress( response.data.percent );

					// Update the import notice.
					var imported_products = parseInt( response.data.imported );
					var total_products    = parseInt( response.data.total );
					imported_products     = ( imported_products >= total_products ) ? total_products : imported_products;
					$( '.importing-notice span.imported-count' ).text( imported_products );
					$( '.importing-notice span.total-products-count' ).text( total_products );
					/**
					 * Call self to import next set of products.
					 * This wait of 500ms is just to allow the script to set the progress bar.
					 */
					setTimeout( function() {
						page++;
						kickoff_product_import( page, response.data.new_products_added, response.data.old_products_updated, response.data.products_import_failed );
					}, 500 );
				}
			},
		} );
	}

	/**
	 * Make progress to the progress bar.
	 *
	 * @param {*} percent
	 */
	function make_the_bar_progress( percent ) {
		percent = percent.toFixed( 2 ); // Upto 2 decimal places.
		percent = parseFloat( percent ); // Convert the percent to float.
		percent = ( 100 <= percent ) ? 100 : percent;

		// Set the progress bar.
		$( '.importer-progress' ).val( percent );
		$( '.importer-progress' ).next( '.value' ).html( percent + '%' );
		$( '.importer-progress' ).next( '.value' ).css( 'width', percent + '%' );
	}

	/**
	 * Block element.
	 *
	 * @param {string} element
	 */
	function block_element( element ) {
		element.addClass( 'non-clickable' );
	}

	/**
	 * Unblock element.
	 *
	 * @param {string} element
	 */
	function unblock_element( element ) {
		element.removeClass( 'non-clickable' );
	}
} );
