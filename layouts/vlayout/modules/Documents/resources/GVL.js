/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("Documents_GVL_Js", {
    getInstance : function() {
        return new Documents_GVL_Js();
    }
},{
    registerEventForToggleIncludedClick : function(){
        var thisInstance = this;
        var detailContentsHolder = this.getContentHolder();
        detailContentsHolder.on('click','.toggleIncluded',function(e){
            e.stopPropagation();
            thisInstance.toggleInvoicePacketFlag(e);
        });
    },

    toggleInvoicePacketFlag : function(e){
        var elem = jQuery(e.currentTarget);
        var recordId = elem.closest('tr').data('id');
        var message = app.vtranslate('JS_CONFIRM_MARK_AS_INCLUDED');
        Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
            function(e) {
                var params = {
                    module : "Documents",
                    action : "SaveFollowupAjax",
                    mode : "toggleIncluded",
                    record : recordId
                }
                AppConnector.request(params).then(function(data){
                    if(data['error']){
                        var param = {text:app.vtranslate('JS_PERMISSION_DENIED')};
                        Vtiger_Helper_Js.showPnotify(param);
                    } else if(data['result'].valid) {
                        if (data['result']['invoice_packet_include']) {
                            //set true (yes)
                        } else {
                            //set false (no).
                        }
                        //OH MY GOD.  Really?  Can't we just change that Yes to no? HOW? because there's no identifier!
                        //Update related listview and pagination
                        Vtiger_Detail_Js.reloadRelatedList();
                    } else {
                        var param = {text:app.vtranslate('JS_FUTURE_EVENT_CANNOT_BE_MARKED_AS_HELD')};
                        Vtiger_Helper_Js.showPnotify(param);
                    }
                });
            },
            function(error, err){
                return false;
            });
    },
    registerEvents : function(isEditView, moduleName) {
        this.registerEventForToggleIncludedClick();
    }
}
);
$(document).ready(function(){
    Documents_GVL_Js.getInstance().registerEvents();
});
