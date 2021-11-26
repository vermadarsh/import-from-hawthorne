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

	/**
	 * Close the modal on Escape key press.
	 */
	$( document ).on( 'keyup', function( evt ) {
		if ( 27 === evt.keyCode ) {
			$( '#hawthorne-shoot-cart-contents-modal .close' ).click();
		}
	} );

	/**
	 * Send cart to greenlight.
	 */
	$( document ).on( 'click', '.hawthorne-send-cart-to-greenlight', function() {
		var this_button      = $( this );
		var this_button_text = this_button.text();
		var customer_name    = $( '#customer-name' ).val();
		var customer_email   = $( '#customer-email' ).val();
		var customer_phone   = $( '#customer-phone' ).val();
		var customer_message = $( '#customer-message' ).val();
		var send_cart        = true;

		$( '.hawthorne-send-cart-error' ).text( '' ); // Vacate the errors.

		// Validate the customer name.
		if ( -1 === is_valid_string( customer_name ) ) {
			$( '.hawthorne-send-cart-error.customer-name' ).text( 'Name is required.' );
			send_cart = false;
		}

		// Validate email.
		if ( -1 === is_valid_string( customer_email ) ) {
			$( '.hawthorne-send-cart-error.customer-email' ).text( 'Email is required.' );
			send_cart = false;
		} else if ( -1 === is_valid_email( customer_email ) ) {
			$( '.hawthorne-send-cart-error.customer-email' ).text( 'Email is invalid.' );
			send_cart = false;
		}

		// Validate the phone.
		if ( '' === customer_phone ) {
			$( '.hawthorne-send-cart-error.customer-phone' ).text( 'Phone is required.' );
			send_cart = false;
		}

		// Exit, if user registration is set to false.
		if ( false === send_cart ) {
			// ersrv_show_toast( 'bg-danger', 'fa-skull-crossbones', toast_error_heading, reservation_item_contact_owner_error_message );
			return false;
		}

		// Block the button.
		block_element( this_button );

		// Activate loader.
		this_button.html( '<span class="ajax-request-in-process"><i class="fa fa-refresh fa-spin"></i></span> Please wait...' );

		// Send the AJAX now.
		var data = {
			action: 'send_cart',
			customer_name: customer_name,
			customer_email: customer_email,
			customer_phone: customer_phone,
			customer_message: customer_message,
		};

		$.ajax( {
			dataType: 'JSON',
			url: ajaxurl,
			type: 'POST',
			data: data,
			success: function ( response ) {
				// In case of invalid AJAX call.
				if ( 0 === response ) {
					console.warn( 'easy reservations: invalid AJAX call' );
					return false;
				}

				// If user already exists.
				if ( 'request-saved' === response.data.code ) {
					// Unblock the button.
					unblock_element( this_button );

					// Activate loader.
					this_button.html( this_button_text );

					// Show the success toast.
					ersrv_show_toast( 'bg-success', 'fa-check-circle', toast_success_heading, response.data.toast_message );

					// Vacate all the values in the modal.
					$( '#customer-name' ).val( '' );
					$( '#customer-email' ).val( '' );
					$( '#customer-phone' ).val( '' );
					$( '#customer-query-subject' ).val( '' );
					$( '#customer-message' ).val( '' );

					// Close the modal.
					setTimeout( function() {
						$( '#ersrv-modal' ).hide();
					}, 2000 );
				}
			}
		} );
	} );

	/**
	 * Check if a string is valid.
	 *
	 * @param {string} data
	 * @returns {number}
	 */
	function is_valid_string( data ) {

		return ( '' === data || undefined === data || ! isNaN( data ) || 0 === data ) ? -1 : 1;
	}

	/**
	 * Check if a email is valid.
	 *
	 * @param {string} email
	 * @returns {number}
	 */
	function is_valid_email( email ) {
		var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

		return ( ! regex.test( email ) ) ? -1 : 1;
	}

	/**
	 * Block element.
	 *
	 * @param {string} element
	 */
	function block_element(element) {
		element.addClass('non-clickable');
	}

	/**
	 * Unblock element.
	 *
	 * @param {string} element
	 */
	function unblock_element(element) {
		element.removeClass('non-clickable');
	}
} );
