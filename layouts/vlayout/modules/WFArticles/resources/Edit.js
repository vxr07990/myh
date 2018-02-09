/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("WFArticles_Edit_Js",{

},{
    //This will store the editview form
    editViewForm : false,

    /**
     * This function will return the current form
     */
    getForm : function(){
        if(this.editViewForm == false) {
            this.editViewForm = jQuery('#EditView');
        }
        return this.editViewForm;
    },

    /**
     * This function will register before saving any record
     */
    // registerRecordPreSaveEvent : function(form) {
    //     var thisInstance = this;
    //     if(typeof form == 'undefined') {
    //         form = this.getForm();
    //     }
    //
    //     form.on(Vtiger_Edit_Js.recordPreSave, function(e, data) {
    //         var params = {
    //             'module' : 'WFArticles',
    //             'action' : "CheckDuplicate",
    //             'currentAccount' : jQuery('input[name="wfaccount"]').val(),
    //             'articleNumber' : jQuery('input[name="article_num"]').val(),
    //         };
    //         AppConnector.request(params).then(
    //             function(data) {
    //                 //If this is a duplicate
    //                 if(data.success && data.result) {
    //
    //                     bootbox.alert(app.vtranslate('JS_DUPLICATE_CREATION'));
    //                     return false;
    //                 }
    //                 window.onbeforeunload = null;
    //                 form.submit();
    //             },
    //             function(error,err){
    //                 //
    //             }
    //         );
    //         e.preventDefault();
    //     })
    // },
});
