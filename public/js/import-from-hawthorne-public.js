jQuery( document ).ready( function( $ ) {
	'use strict';

	/**
	 * Open the cart contents modal.
	 */
	$( document ).on( 'click', '.hawthorne-open-cart-contents-modal', function() {
		$( '#hawthorne-shoot-cart-contents-modal' ).css( 'display', 'block' );
		setTimeout( function() {
			$( '#hawthorne-shoot-cart-contents-modal' ).addClass( 'show' );
		}, 300 );
	} );

	/**
	 * Close the modal.
	 */
	$( document ).on( 'click', '#hawthorne-shoot-cart-contents-modal .close', function() {
		$( '#hawthorne-shoot-cart-contents-modal' ).css( 'display', 'none' );
		setTimeout( function() {
			$( '#hawthorne-shoot-cart-contents-modal' ).removeClass( 'show' );
		}, 300 );
	} );
} );
