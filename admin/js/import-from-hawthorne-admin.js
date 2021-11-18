/**
 * Admin script file.
 */
jQuery( document ).ready( function( $ ) {
	'use strict';

	// Localized variables.
	var product_import_button_text = Hawthorne_Admin_Script_Vars.product_import_button_text;
	var product_import_admin_url   = Hawthorne_Admin_Script_Vars.product_import_admin_url;

	// Add the import button besides the main title.
	$( '<a href="' + product_import_admin_url + '" class="page-title-action">' + product_import_button_text + '</a>' ).insertAfter( 'body.post-type-product .wrap a.page-title-action:last' );

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
