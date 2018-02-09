/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("VanlineManager_Edit_Js",{},{
	getQueryVariable : function(variable) {
		var query = window.location.search.substring(1);
		var vars = query.split("&");
		for (var i=0; i<vars.length; i++) {
			var pair = vars[i].split("=");
			if(pair[0] == variable) {return pair[1];}
		}
		return(false);
	},

	registerEvents: function(){
		this._super();
		this.initializeAddressAutofill('VanlineManager');
		if(this.getQueryVariable('record') == false) {
			jQuery('form').submit(function() {
				jQuery('form').append('<input type="hidden" name="newRecord" value=1 />');
			});
		}
	}
});
