/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("Agents_Detail_Js",{
    deleteAgentRecord: function(deleteRecordActionUrl) {
        var message = app.vtranslate('LBL_DELETE_CONFIRMATION');
        Vtiger_Helper_Js.showConfirmationBox({
            'message': message
        }).then(function(data) {
        var urlParams = {
            module: 'Agents',
            action: 'AgentsActions',
            mode: 'checkToDelete',
            agentid: $('#recordId').val(),
        }
        AppConnector.requestPjax(urlParams).then(
            function (data) {
                if (data.result == 'OK'){
                    AppConnector.request(deleteRecordActionUrl + '&ajaxDelete=true').then(function(data) {
                        if (data.success == true) {
                            window.location.href = data.result;
                        } else {
                            Vtiger_Helper_Js.showPnotify(data.error.message);
                        }
                    });
                }else{
                    alert("The record can't be deleted because is being used by other module in the system.");
                }
            }
        );            
        }, function(error, err) {});
    }
}, {
	//Google Address Autofill
	registerEvents : function() {
		this._super();
		this.initializeAddressAutofill('Agents');
	}
	
});
jQuery(document).ready(function(){
    var href = jQuery('#Agents_detailView_moreAction_Delete_Agents').find('a').attr('href').replace('Vtiger_Detail_Js','Agents_Detail_Js').replace('deleteRecord','deleteContactRecord');
    jQuery('#Agents_detailView_moreAction_Delete_Agents').find('a').attr('href',href);
});