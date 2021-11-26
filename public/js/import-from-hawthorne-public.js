jQuery( document ).ready( function( $ ) {
	'use strict';

	/**
	 * Open the cart contents modal.
	 */
	$( document ).on( 'click', '.hawthorne-open-cart-contents-modal', function() {
		$( '#hawthorne-shoot-cart-contents-modal' ).addClass( 'show' ).css( 'display', 'block' );
	} );
} );
