(function( $ ) {
	'use strict';

	// Localized variables.
	var ajaxurl                           = Hawthorne_Admin_Script_Vars.ajaxurl;
	var import_from_hawthorne_button_text = Hawthorne_Admin_Script_Vars.import_from_hawthorne_button_text;
	var is_administrator                  = Hawthorne_Admin_Script_Vars.is_administrator;
	

	// Add the export button besides the new log button.
	// Enable the exporting feature only for admin users.
	if ( 'yes' === is_administrator ) {
		$( '<a href="javascript:void(0);" class="hawthorne_import_button page-title-action">' + import_from_hawthorne_button_text + '</a>' ).insertAfter( 'body.post-type-product .wrap a.page-title-action' );
	}
	/*
	 Ajax to serve Import data from hawthorne API call.
	 */
	$( document ).on( 'click', '.hawthorne_import_button', function( evt ) {
		evt.preventDefault();
		var this_btn = $( this );
		var data = {
			action: 'hawthorne_product_import_api',
		}
		// block_element( $('body.post-type-product .wrap') );
		$.ajax( {
			dataType: 'json',
			url: ajaxurl,
			type: 'POST',
			data: data,
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
})( jQuery );
