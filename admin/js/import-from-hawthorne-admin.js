/**
 * Admin script file.
 */
jQuery( document ).ready( function( $ ) {
	'use strict';

	// Localized variables.
	var ajaxurl                    = Hawthorne_Admin_Script_Vars.ajaxurl;
	var product_import_button_text = Hawthorne_Admin_Script_Vars.product_import_button_text;

	// Add the import button besides the main title.
	$( '<a href="javascript:void(0);" class="hawthorne_products_import page-title-action">' + product_import_button_text + '</a>' ).insertAfter( 'body.post-type-product .wrap a.page-title-action:last' );

	/**
	 * AJAX to import products from Hawthorne.
	 */
	$( document ).on( 'click', '.hawthorne_products_import', function() {
		var this_btn = $( this );
		// block_element( this_btn );
		$.ajax( {
			dataType: 'JSON',
			url: ajaxurl,
			type: 'POST',
			data: {
				action: 'import_products',
			},
			cache: true,
			success: function ( response ) {
				// unblock_element( $('body.post-type-product .wrap') );
				if( '' === response.data.code ) {

				}
			},
		} );
	} );
	/* ========================================= Functions ==================================== */
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
