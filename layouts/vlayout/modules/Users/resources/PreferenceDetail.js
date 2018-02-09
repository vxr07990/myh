/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

 $("#Users_detailView_fieldValue_user_smtp_password").html('********************');

Users_Detail_Js("Users_PreferenceDetail_Js",{},{
    
	/**
	 * register Events for my preference
	 */
	registerEvents : function(){
		this._super();
		Users_PreferenceEdit_Js.registerChangeEventForCurrencySeperator();
	}
        
       
});

jQuery(document).ready(function () {
	/**
	 * Obscure the Exchange password value in the page.
	 *
	 * @todo Fix this terrible hack.
	 * orly?
	 */
	var exchangePassword = document.querySelector('#Users_detailView_fieldValue_user_exchange_password > span');

	if (exchangePassword) {
		if (exchangePassword.textContent.trim() != '') {
			exchangePassword.textContent = '********************';
		}
	}
});
