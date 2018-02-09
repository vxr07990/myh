/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

 Vtiger_Edit_Js("Tariffs_Edit_Js",{},{

	registerAdminAccess : function(){
		var adminAccess = jQuery('input:checkbox[name="admin_access"]');
		adminAccess.on('change', function(){
			if(adminAccess.prop('checked') == true && !jQuery('select[name="assigned_user_id"]').parents('span').hasClass('hide')){
				jQuery('select[name="assigned_user_id"]').parents('span').addClass('hide');
				jQuery('select[name="assigned_user_id"]').parents('td').prev('td').find('label').addClass('hide');
			} else if(!adminAccess.prop('checked') && jQuery('select[name="assigned_user_id"]').parents('span').hasClass('hide')){
				jQuery('select[name="assigned_user_id"]').parents('span').removeClass('hide');
				jQuery('select[name="assigned_user_id"]').parents('td').prev('td').find('label').removeClass('hide');
			}
		});
	},

	registerEvents: function() {
		this._super();
		this.registerAdminAccess();
		jQuery('input:checkbox[name="admin_access"]').trigger('change');
	},

 });
