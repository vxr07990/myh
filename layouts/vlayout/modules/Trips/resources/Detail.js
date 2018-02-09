/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("Trips_Detail_Js", {
    reloadRelatedList: function () {
        var pageNumber = jQuery('[name="currentPageNum"]').val();
        var detailInstance = Vtiger_Detail_Js.getInstance();
        detailInstance.loadRelatedList(pageNumber);
    }

}, {
    loadRelatedList: function (pageNumber) {
        var relatedListInstance = new Vtiger_RelatedList_Js(this.getRecordId(), app.getModuleName(), this.getSelectedTab(), this.getRelatedModuleName());
        var params = {'page': pageNumber};
        relatedListInstance.loadRelatedList(params);
    },
    registerEventForRelatedList: function () {
        var thisInstance = this;
        var detailContentsHolder = this.getContentHolder();
        detailContentsHolder.on('click', '.relatedListHeaderValues', function (e) {
            var element = jQuery(e.currentTarget);
            var selectedTabElement = thisInstance.getSelectedTab();
            var relatedModuleName = thisInstance.getRelatedModuleName();
            var relatedController = new Vtiger_RelatedList_Js(thisInstance.getRecordId(), app.getModuleName(), selectedTabElement, relatedModuleName);
            relatedController.sortHandler(element);
        });

        detailContentsHolder.on('click', 'button.selectRelation', function (e) {

            var selectedTabElement = thisInstance.getSelectedTab();
            var relatedModuleName = thisInstance.getRelatedModuleName();
            var relatedController = new Trips_RelatedList_Js(thisInstance.getRecordId(), app.getModuleName(), selectedTabElement, relatedModuleName);
            relatedController.showSelectRelationPopup().then(function (data) {
                var emailEnabledModule = jQuery(data).find('[name="emailEnabledModules"]').val();
                if (emailEnabledModule) {
                    thisInstance.registerEventToEditRelatedStatus();
                }
            });
        });

        detailContentsHolder.on('click', 'a.relationDelete', function (e) {
            e.stopImmediatePropagation();
            var element = jQuery(e.currentTarget);
            var instance = Vtiger_Detail_Js.getInstance();

            if(element.attr('data-relatedmodule') && element.data('relatedmodule') == 'Orders'){
                var message = app.vtranslate('JS_REMOVE_ORDER_TRIP');
            }else{
                var key = instance.getDeleteMessageKey();
                var message = app.vtranslate(key);
            }

            Vtiger_Helper_Js.showConfirmationBox({'message': message}).then(
                    function (e) {
                        var row = element.closest('tr');
                        var relatedRecordid = row.data('id');
                        var selectedTabElement = thisInstance.getSelectedTab();
                        var relatedModuleName = thisInstance.getRelatedModuleName();
                        var relatedController = new Vtiger_RelatedList_Js(thisInstance.getRecordId(), app.getModuleName(), selectedTabElement, relatedModuleName);
                        relatedController.deleteRelation([relatedRecordid]).then(function (response) {
                            relatedController.loadRelatedList();
                        });
                    },
                    function (error, err) {
                    }
            );
        });
    },
});