    /*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
Vtiger_List_Js("Contacts_List_Js",{
    deleteRecord : function(recordId) {
        var listInstance = Vtiger_List_Js.getInstance();
        var message = app.vtranslate('LBL_DELETE_CONFIRMATION');
        Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
            function(e) {
                var urlParams = {
                    module: 'Contacts',
                    action: 'ContactsActions',
                    mode: 'checkToDelete',
                    contactid: recordId,
                }
                AppConnector.requestPjax(urlParams).then(
                    function (data) {
                        if (data.result == 'OK'){
                            var postData = {
                                "module": 'Contacts',
                                "action": "DeleteAjax",
                                "record": recordId,
                                "parent": app.getParentModuleName()
                            }
                            var deleteMessage = app.vtranslate('JS_RECORD_GETTING_DELETED');
                            var progressIndicatorElement = jQuery.progressIndicator({
                                'message' : deleteMessage,
                                'position' : 'html',
                                'blockInfo' : {
                                    'enabled' : true
                                }
                            });
                            AppConnector.request(postData).then(
                                function(data){
                                    progressIndicatorElement.progressIndicator({
                                        'mode' : 'hide'
                                    })
                                    if(data.success) {
                                        var orderBy = jQuery('#orderBy').val();
                                        var sortOrder = jQuery("#sortOrder").val();
                                        var urlParams = {
                                            "viewname": data.result.viewname,
                                            "orderby": orderBy,
                                            "sortorder": sortOrder
                                        }
                                        jQuery('#recordsCount').val('');
                                        jQuery('#totalPageCount').text('');
                                        listInstance.getListViewRecords(urlParams).then(function(){
                                            listInstance.updatePagination();
                                        });
                                    } else {
                                        var  params = {
                                            text : app.vtranslate(data.error.message),
                                            title : app.vtranslate('JS_LBL_PERMISSION')
                                        }
                                        Vtiger_Helper_Js.showPnotify(params);
                                    }
                                },
                                function(error,err){
                                }
                            );
                        }else{
                            alert("The record can't be deleted because is being used by other module in the system.");
                        }
                    }
                ); 
            },
            function(error, err){
            }
        );
    },
},{
    registerDeleteContactRecordClickEvent: function(){
        var listViewContentDiv = this.getListViewContentContainer();
        listViewContentDiv.on('click','.deleteContactRecordButton',function(e){
            var elem = jQuery(e.currentTarget);
            var recordId = elem.closest('tr').data('id');
            Contacts_List_Js.deleteRecord(recordId);
            e.stopPropagation();
        });
    },
    registerContactEvents : function(){
        var instance = new Contacts_List_Js();
        instance.registerDeleteContactRecordClickEvent();
    },
});
jQuery(document).ready(function(){
    var instance = new Contacts_List_Js();
    instance.registerContactEvents();
});