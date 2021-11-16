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

})( jQuery );
