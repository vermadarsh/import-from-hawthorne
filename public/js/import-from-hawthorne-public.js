jQuery( document ).ready( function( $ ) {
	'use strict';

	// Localized variables.
	var ajaxurl                          = Hawthorne_Public_Script_Vars.ajaxurl;
	var notification_error_heading        = Hawthorne_Public_Script_Vars.notification_error_heading;
	var notification_success_heading      = Hawthorne_Public_Script_Vars.notification_success_heading;
	var notification_notice_heading       = Hawthorne_Public_Script_Vars.notification_notice_heading;
	var send_cart_customer_name_required  = Hawthorne_Public_Script_Vars.send_cart_customer_name_required;
	var send_cart_customer_email_required = Hawthorne_Public_Script_Vars.send_cart_customer_email_required;
	var send_cart_customer_email_invalid  = Hawthorne_Public_Script_Vars.send_cart_customer_email_invalid;
	var send_cart_customer_phone_required = Hawthorne_Public_Script_Vars.send_cart_customer_phone_required;
	var send_cart_error_message           = Hawthorne_Public_Script_Vars.send_cart_error_message;

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
			$( '.hawthorne-send-cart-error.customer-name' ).text( send_cart_customer_name_required );
			send_cart = false;
		}

		// Validate email.
		if ( -1 === is_valid_string( customer_email ) ) {
			$( '.hawthorne-send-cart-error.customer-email' ).text( send_cart_customer_email_required );
			send_cart = false;
		} else if ( -1 === is_valid_email( customer_email ) ) {
			$( '.hawthorne-send-cart-error.customer-email' ).text( send_cart_customer_email_invalid );
			send_cart = false;
		}

		// Validate the phone.
		if ( '' === customer_phone ) {
			$( '.hawthorne-send-cart-error.customer-phone' ).text( send_cart_customer_phone_required );
			send_cart = false;
		}

		// Exit, if user registration is set to false.
		if ( false === send_cart ) {
			hawthorne_show_notification( 'bg-danger', 'fa-skull-crossbones', notification_error_heading, send_cart_error_message );
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
					console.warn( 'import from hawthorne: invalid AJAX call' );
					return false;
				}

				// If user already exists.
				if ( 'cart-sent' === response.data.code ) {
					// Unblock the button.
					unblock_element( this_button );

					// Activate loader.
					this_button.html( this_button_text );

					// Show the success toast.
					hawthorne_show_notification( 'bg-success', 'fa-check-circle', notification_success_heading, response.data.toast_message );

					// Vacate all the values in the modal.
					$( '#customer-name' ).val( '' );
					$( '#customer-email' ).val( '' );
					$( '#customer-phone' ).val( '' );
					$( '#customer-message' ).val( '' );

					// Close the modal.
					setTimeout( function() {
						$( '#hawthorne-shoot-cart-contents-modal .close' ).click();
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

	/**
	 * Show the notification text.
	 *
	 * @param {string} bg_color Holds the toast background color.
	 * @param {string} icon Holds the toast icon.
	 * @param {string} heading Holds the toast heading.
	 * @param {string} message Holds the toast body message.
	 */
	function hawthorne_show_notification( bg_color, icon, heading, message ) {
		$( '.hawthorne-notification-wrapper .toast' ).removeClass( 'bg-success bg-warning bg-danger' );
		$( '.hawthorne-notification-wrapper .toast' ).addClass( bg_color );
		$( '.hawthorne-notification-wrapper .toast .hawthorne-notification-icon' ).removeClass( 'fa-skull-crossbones fa-check-circle fa-exclamation-circle' );
		$( '.hawthorne-notification-wrapper .toast .hawthorne-notification-icon' ).addClass( icon );
		$( '.hawthorne-notification-wrapper .toast .hawthorne-notification-heading' ).text( heading );
		$( '.hawthorne-notification-wrapper .toast .hawthorne-notification-message' ).html( message );
		$( '.hawthorne-notification-wrapper .toast' ).removeClass( 'hide' ).addClass( 'show' );

		setTimeout( function() {
			$( '.hawthorne-notification-wrapper .toast' ).removeClass( 'show' ).addClass( 'hide' );
		}, 5000 );
	}
} );
