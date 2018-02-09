/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("PushNotifications_Edit_Js",{},{
	text_max : 200,

	getQueryVariable : function(variable) {
		var query = window.location.search.substring(1);
		var vars = query.split("&");
		for (var i=0; i<vars.length; i++) {
			var pair = vars[i].split("=");
			if(pair[0] == variable) {return pair[1];}
		}
		return(false);
	},

	setOwnerField : function() {
		var ownerId = this.getQueryVariable('sourceRecord');
		var selectElement = jQuery('select[name="agentid"]');
		selectElement.find('option').each(function() {
			jQuery(this).prop('selected', false);
		});
		selectElement.find('option[value="'+ownerId+'"]').prop('selected', true);
		selectElement.prop('disabled', true);
		selectElement.trigger('liszt:updated');
		selectElement.prop('disabled', false);
	},

	disableAssignedUser : function() {
		var selectElement = jQuery('select[name="assigned_user_id"]');
		selectElement.prop('disabled', true);
		selectElement.trigger('liszt:updated');
		selectElement.prop('disabled', false);
	},

	registerRemainingCharCounter : function() {
		var thisInstance = this;
		jQuery('textarea[name="message"]').on('keyup', function() {
			var text_length = jQuery(this).val().length;
			var text_remaining = thisInstance.text_max - text_length;

			if(text_remaining < 0) {
				jQuery(this).val(jQuery(this).val().substring(0,thisInstance.text_max));
				text_remaining = 0;
			}

			jQuery('#text_remaining').html(text_remaining + ' Characters Remaining');
		});
	},

	registerSubmit : function() {
		var thisInstance = this;
		jQuery('form').on('submit', function(e) {
			e.preventDefault();
			var userValidated = confirm(app.vtranslate('LBL_CONFIRM_SUBMIT'));

			if(userValidated === true) {
				jQuery('form').off('submit');
				jQuery('form').submit();
			}
		})
	},

	registerEvents : function(){
		this._super();
		this.setOwnerField();
		this.disableAssignedUser();
		this.registerRemainingCharCounter();
		this.registerSubmit();
	}
});
