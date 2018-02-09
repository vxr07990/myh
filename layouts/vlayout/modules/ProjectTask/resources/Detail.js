/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("ProjectTask_Detail_Js", {}, {
    detailViewRecentTicketsTabLabel: 'Trouble Tickets',
    detailViewRecentTasksTabLabel: 'Project Tasks',
    detailViewRecentMileStonesLabel: 'Project Milestones',
    /**
     * Function to register event for create related record
     * in summary view widgets
     */
    registerSummaryViewContainerEvents: function(summaryViewContainer) {
        this._super(summaryViewContainer);
        this.registerEventForAddingModuleRelatedRecordFromSummaryWidget();
        this.registerEventForAddingResourceFromSummaryWidget();
    },
    /*
     * Function to add module related record from summary widget
     */
    registerEventForAddingModuleRelatedRecordFromSummaryWidget: function() {
        var thisInstance = this;
        jQuery('#createProjectMileStone,#createProjectTask').on('click', function(e) {
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

            var postQuickCreateSave = function(data) {
                thisInstance.postSummaryWidgetAddRecord(data, currentElement);
                if (referenceModuleName == "ProjectTask") {
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
            headerInstance.getQuickCreateForm(quickcreateUrl, moduleName, quickCreateParams).then(function(data) {
                headerInstance.handleQuickCreateData(data, quickCreateParams);
                progress.progressIndicator({'mode': 'hide'});
            });
        })
    },
    /**
     * Function redirect to resource assigment page from tasks widget
     * @author VGS Global - Conrado Maggi
     */

    registerEventForAddingResourceFromSummaryWidget: function() {
        var thisInstance = this;
        jQuery('.addResource').on('click', function(e) {
            var currentElement = jQuery(e.currentTarget);
            var summaryWidgetContainer = currentElement.closest('.summaryWidgetContainer');
            var widgetDataContainer = summaryWidgetContainer.find('.widget_contents');
            var parentId = thisInstance.getRecordId();

            var resourcesSearchURL = 'index.php?module=ResourceDashboard&view=ResourceSelect&refferer=ProjectTask&taskid=' + parentId;
            window.location.href = resourcesSearchURL;




        })
    },
    /**
     * Function to load module summary of Projects
     */
    loadModuleSummary: function() {
        var summaryParams = {};
        summaryParams['module'] = app.getModuleName();
        summaryParams['view'] = "Detail";
        summaryParams['mode'] = "showModuleSummaryView";
        summaryParams['record'] = jQuery('#recordId').val();

        AppConnector.request(summaryParams).then(
                function(data) {
                    jQuery('.summaryView').html(data);
                }
        );
    },
    saveFieldValues: function(fieldDetailList) {
        var thisInstance = this;
        var targetFn = this._super;

        var fieldName = fieldDetailList.field;
        if (fieldName != 'startdate' && fieldName != 'enddate') {
            return targetFn.call(thisInstance, fieldDetailList);
        }

        if (fieldName == 'startdate') {
            var startDate = fieldDetailList.value;
            var endDate = jQuery('input[name="enddate"]').val();
            var recordId = this.getRecordId();
        }

        if (fieldName == 'enddate') {
            var endDate = fieldDetailList.value;
            var startDate = jQuery('input[name="startdate"]').val();
            var recordId = this.getRecordId();
        }

        var aDeferred = jQuery.Deferred();

        var params = {
            'module': 'ResourceDashboard',
            'action': 'checkProjectTasksResources',
            'startdate': startDate,
            'enddate': endDate,
            'recordid': recordId
        }

        AppConnector.request(params).then(
                function(data) {
                    var response = data['result'];
                    var result = response['success'];
                    if (result == true) {
                        var form = thisInstance.getForm();
                        var params = {
                            title: app.vtranslate('RESOURCE_CONFLICT'),
                            text: app.vtranslate('RESOURCE_CONFLICT_CANT_SAVE'),
                            width: '35%'
                        };
                        Vtiger_Helper_Js.showPnotify(params);
                        var fieldresponse=[];
                        fieldresponse['display_value']= jQuery('input[name="'+fieldDetailList.field+'"]').prop( 'defaultValue' );
                        fieldresponse[fieldName] = fieldresponse;
                        data['result'] = fieldresponse;
                        aDeferred.resolve(data);

                    } else {
                         var fieldresponse=[];
                        fieldresponse['display_value']= fieldDetailList.value;
                        fieldresponse[fieldName] = fieldresponse;
                        data['result'] = fieldresponse;
                        aDeferred.resolve(data);
                    }
                }
        );

        return aDeferred.promise();
    },
    registerEvents: function() {
        var detailContentsHolder = this.getContentHolder();
        var thisInstance = this;
        this._super();

        detailContentsHolder.on('click', '.moreRecentMilestones', function() {
            var recentMilestonesTab = thisInstance.getTabByLabel(thisInstance.detailViewRecentMileStonesLabel);
            recentMilestonesTab.trigger('click');
        });

        detailContentsHolder.on('click', '.moreRecentTickets', function() {
            var recentTicketsTab = thisInstance.getTabByLabel(thisInstance.detailViewRecentTicketsTabLabel);
            recentTicketsTab.trigger('click');
        });

        detailContentsHolder.on('click', '.moreRecentTasks', function() {
            var recentTasksTab = thisInstance.getTabByLabel(thisInstance.detailViewRecentTasksTabLabel);
            recentTasksTab.trigger('click');
        });
    }
})

function deleteAssoc(crmid, taskid, taskName) {

    var summaryParams = {};
    summaryParams['module'] = 'ProjectTask';
    summaryParams['view'] = "Detail";
    summaryParams['mode'] = "deleteRelatedResource";
    summaryParams['resourceid'] = crmid;
    summaryParams['taskid'] = taskid;
    summaryParams['project_tasks'] = taskName;
    summaryParams['record'] = jQuery('#recordId').val();

    AppConnector.request(summaryParams).then(
            function(data) {

                var div = jQuery('#task_' + taskName).parent().html(data);
            }
    );
}

function editAssoc(crmid, taskid, taskName) {

    var summaryParams = {};
    summaryParams['module'] = 'Project';
    summaryParams['view'] = "Detail";
    summaryParams['mode'] = "editRelatedResource";
    summaryParams['resourceid'] = crmid;
    summaryParams['taskid'] = taskid;
    summaryParams['project_tasks'] = taskName;
    summaryParams['record'] = jQuery('#recordId').val();

    AppConnector.request(summaryParams).then(
            function(data) {

                window.location.href = 'index.php?module=ResourceDashboard&view=ResourceSelect&refferer=ProjectTask&taskid=' + jQuery('#recordId').val();
            }
    );
}