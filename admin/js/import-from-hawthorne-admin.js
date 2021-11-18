/**
 * Admin script file.
 */
jQuery( document ).ready( function( $ ) {
	'use strict';

	// Localized variables.
	var product_import_button_text = Hawthorne_Admin_Script_Vars.product_import_button_text;
	var product_import_admin_url   = Hawthorne_Admin_Script_Vars.product_import_admin_url;

	// Add the import button besides the main title.
	// $( '<a href="' + product_import_admin_url + '" class="page-title-action">' + product_import_button_text + '</a>' ).insertAfter( 'body.post-type-product .wrap a.page-title-action:last' );

	$( '<div class="dropdown"><a href="' + product_import_admin_url + '" class="page-title-action import-sub-dropdown">' + product_import_button_text + '</a></div>' ).insertAfter( 'body.post-type-product .wrap a.page-title-action:nth-child(3)' );

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

	$( "body.post-type-product .wrap a.page-title-action:nth-child(3)" ).addClass("dropdown-btn");
	$( '<a href="#" class="page-title-action import-sub-dropdown-btn"><span class="dashicons dashicons-arrow-down"></span></a>' ).insertAfter( '.dropdown-btn' );

	$('.import-sub-dropdown-btn').on('click',function () {
		$('.dropdown').toggleClass('show');
    });

	// progress bar js 
	var i = 0;
	function move() {
	if (i == 0) {
		i = 1;
		var elem = document.querySelector(".importer-progress");
		var value = document.querySelector(" .value");
		console.log(elem);
		var width = 0;
		var id = setInterval(frame, 0);
		function frame() {
		if (width >= 100) {
			clearInterval(id);
			i = 0;
		} else {
			width++;
			elem.setAttribute("value", width);
			value.innerHTML = width + "%";
			value.style.width = width + "%";
		}
		}
	}
	}
	move();


} );
