/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("OrdersTask_Detail_Js", {}, {
    detailViewRecentTicketsTabLabel: 'Trouble Tickets',
    detailViewRecentTasksTabLabel: 'Local Operations Tasks',
    detailViewRecentMileStonesLabel: 'Orders Milestones',
    operativeNameBlock: 'Operative Task Information',
    disabledDispatchFields: ['disp_assigneddate', 'disp_assignedstart', 'disp_assignedcrew', 'disp_actualdate', 'disp_actualstart', 'disp_actualcrew', 'disp_actualhours','orderstask_ldate','orderstask_plandeldate','orderstask_pldate','orderstask_actualdeldate','orderstask_onhold'],
    /**
     * Function to register event for create related record
     * in summary view widgets
     */
    registerSummaryViewContainerEvents: function (summaryViewContainer) {
        this._super(summaryViewContainer);
        this.registerEventForAddingModuleRelatedRecordFromSummaryWidget();
        //this.registerEventForAddingResourceFromSummaryWidget();
        //this.hideShowOperativeBlocks();
    },
    /*
     * Function to add module related record from summary widget
     */
    registerEventForAddingModuleRelatedRecordFromSummaryWidget: function () {
        var thisInstance = this;
        jQuery('#createProjectMileStone,#createProjectTask').on('click', function (e) {
            var currentElement = jQuery(e.currentTarget);
            var summaryWidgetContainer = currentElement.closest('.summaryWidgetContainer');
            var widgetDataContainer = summaryWidgetContainer.find('.widget_contents');
            var referenceModuleName = widgetDataContainer.find('[name="relatedModule"]').val();
            var quickcreateUrl = currentElement.data('url');
            var parentId = thisInstance.getRecordId();
            var quickCreateParams = {};
            var relatedField = currentElement.data('parentRelatedField');
            var moduleName = currentElement.closest('.widget_header').find('[name="relatedModule"]').val();
            var relatedParams = {};
            relatedParams[relatedField] = parentId;

            var postQuickCreateSave = function (data) {
                thisInstance.postSummaryWidgetAddRecord(data, currentElement);
                if (referenceModuleName == "OrdersTask") {
                    thisInstance.loadModuleSummary();
                }
            }

            if (typeof relatedField != "undefined") {
                quickCreateParams['data'] = relatedParams;
            }
            quickCreateParams['noCache'] = true;
            quickCreateParams['callbackFunction'] = postQuickCreateSave;
            var progress = jQuery.progressIndicator();
            var headerInstance = new Vtiger_Header_Js();
            headerInstance.getQuickCreateForm(quickcreateUrl, moduleName, quickCreateParams).then(function (data) {
                headerInstance.handleQuickCreateData(data, quickCreateParams);
                progress.progressIndicator({'mode': 'hide'});
            });
        })
    },
    hideShowOperativeBlocks: function () {
        var thisInstance = this;

        var field_id = 'OrdersTask_detailView_fieldValue_orderstasktype';
        if (jQuery.trim(jQuery('#' + field_id + ' .value').text()) !== 'Operations') {
            var blockHeader = jQuery(document).find('.blockHeader:contains("' + thisInstance.operativeNameBlock + '")');
            var table = blockHeader.closest('table');
            table.hide();

        }

    },
    disableDispatchFields: function () {
        thisInstance = this;
        jQuery.each(thisInstance.disabledDispatchFields, function (index, field_name) {
            jQuery('input[name="' + field_name + '"]').prop('disabled', true);
        });
        jQuery('[name="dispatch_status"]').prop('disabled',true).trigger("liszt:updated");
        jQuery('[name="participating_agent"]').prop('disabled',true).trigger("liszt:updated");
        if(jQuery('#OrdersTask_detailView_fieldValue_cancel_task').length > 0){
            if(jQuery('#OrdersTask_detailView_fieldValue_cancel_task span.value').html().trim() == 'Yes'){
                jQuery('span.value').each(function () {
                    $(this).prop('disabled',true).prop('readonly','readonly');
                });
                jQuery('td.fieldValue').each(function () {
                    $(this).prop('disabled',true).prop('readonly','readonly');
                });
            }
        }
        
    },
    hideDateSpread: function () {
        var thisInstance = this;

        if(jQuery('#OrdersTask_detailView_fieldValue_date_spread span.value').html().trim() == 'Yes'){
            jQuery('#OrdersTask_detailView_fieldValue_service_date_to').closest('tr').show();            
        }else{
            jQuery('#OrdersTask_detailView_fieldValue_service_date_to').closest('tr').hide();
        }
    },
    registerEvents: function () {
        this._super();
        this.disableDispatchFields();
        this.hideDateSpread();
    }

});

jQuery(document).ready(function () {
    var e = new OrdersTask_Detail_Js;
    app.listenPostAjaxReady(function () {
        e.disableDispatchFields()
    })
});
