/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/


Vtiger_List_Js("Calendar_List_Js",{

	triggerMassEdit : function(massEditUrl) {
		Vtiger_List_Js.triggerMassAction(massEditUrl, function(container){
			var massEditForm = container.find('#massEdit');
			massEditForm.validationEngine(app.validationEngineOptions);
			var listInstance = Vtiger_List_Js.getInstance();
			var editInstance = Vtiger_Edit_Js.getInstance();
			listInstance.registerRecordAccessCheckEvent(massEditForm);
			editInstance.registerBasicEvents(jQuery(container));
			listInstance.postMassEdit(container);
		});
	},

	triggerImportAction : function (importUrl) {
		var progressIndicatorElement = jQuery.progressIndicator();
		AppConnector.request(importUrl).then(
			function(data) {
				progressIndicatorElement.progressIndicator({'mode' : 'hide'});
				if(data) {
					app.showModalWindow(data, function(data){
						jQuery('#ical_import').validationEngine(app.validationEngineOptions);
					});
				}
			}
		);
	},

	triggerExportAction : function (importUrl) {
		var progressIndicatorElement = jQuery.progressIndicator();
		AppConnector.request(importUrl).then(
			function(data) {
				progressIndicatorElement.progressIndicator({'mode' : 'hide'});
				if(data) {
					app.showModalWindow(data, function(data){
					});
				}
			}
		);
	}

},{

    registerHoldFollowupOnEvent : function(){
        var thisInstance = this;
		var listViewContentDiv = this.getListViewContentContainer();
		listViewContentDiv.on('click','.holdFollowupOn',function(e){
			var elem = jQuery(e.currentTarget);
			var recordId = elem.closest('tr').data('id');

            var url = 'index.php?module=Calendar&view=QuickCreateFollowupAjax&record='+recordId;
            var progressIndicatorInstance = jQuery.progressIndicator({});
            AppConnector.request(url).then(
				function(data){
					if(data){
                        progressIndicatorInstance.hide();
                        app.showModalWindow(data, function(data){
                         var createFollowupForm = data.find('form.followupCreateView');
                         createFollowupForm.validationEngine(app.validationEngineOptions);
                         app.registerEventForTimeFields(createFollowupForm);
                         //Form submit
                         createFollowupForm.submit(function(event){
                             var createButton = jQuery(this).find('button.btn-success');
                             createButton.attr('disabled','disabled');
                             progressIndicatorInstance = jQuery.progressIndicator({});
                             event.preventDefault();
                             var result = createFollowupForm.validationEngine('validate');
                             if(!result){
                                 createButton.removeAttr('disabled');
                                 progressIndicatorInstance.hide();
                                 return false;
                             }
                             var moduleName = jQuery(this).find("[name='module']").val();
                             var recordId = jQuery(this).find("[name='record']").val();
                             var followupStartDate = jQuery(this).find("[name='followup_date_start']").val();
                             var followupStartTime = jQuery(this).find("[name='followup_time_start']").val();
                             var action = jQuery(this).find("[name='action']").val();
                             var mode = jQuery(this).find("[name='mode']").val();
                             var defaultCallDuration = jQuery(this).find("[name='defaultCallDuration']").val();
                             var defaultOtherEventDuration = jQuery(this).find("[name='defaultOtherEventDuration']").val();
                             var params = {
                                            module : moduleName,
                                            action : action,
                                            mode : mode,
                                            record : recordId,
                                            followup_date_start : followupStartDate,
                                            followup_time_start : followupStartTime,
                                            defaultOtherEventDuration : defaultOtherEventDuration,
                                            defaultCallDuration : defaultCallDuration
                                        }
                                        AppConnector.request(params).then(function(data){
                                            app.hideModalWindow();
                                            progressIndicatorInstance.hide();
                                            if(data['result'] && data['result'].created){
                                                //Update listview and pagination
                                                  var orderBy = jQuery('#orderBy').val();
                                                  var sortOrder = jQuery("#sortOrder").val();
                                                  var urlParams = {
                                                    "orderby": orderBy,
                                                    "sortorder": sortOrder
                                                }
                                                jQuery('#recordsCount').val('');
                                                jQuery('#totalPageCount').text('');
                                                thisInstance.getListViewRecords(urlParams).then(function(){
                                                    thisInstance.updatePagination();
                                                });
                                            }
                                        });
                         });
                    });
                    }
                    else{
                        progressIndicatorInstance.hide();
                        Vtiger_Helper_Js.showPnotify(app.vtranslate('JS_NO_EDIT_PERMISSION'));
                    }
				});
			e.stopPropagation();
		});
    },

    registerMarkAsHeldEvent : function(){
        var thisInstance = this;
		var listViewContentDiv = this.getListViewContentContainer();
		listViewContentDiv.on('click','.markAsHeld',function(e){
            var elem = jQuery(e.currentTarget);
			var recordId = elem.closest('tr').data('id');
            var activityType = elem.closest('tr').data('activity-type');
            if (activityType && activityType == 'Task'){
                var message = app.vtranslate('JS_CONFIRM_MARK_COMPLETED')
            } else {
                var message = app.vtranslate('JS_CONFIRM_MARK_AS_HELD');
            }
            Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
			function(e) {
                var params = {
                                module : "Calendar",
                                action : "SaveFollowupAjax",
                                mode : "markAsHeldCompleted",
                                record : recordId
                            }
                            AppConnector.request(params).then(function(data){
                                if(data['error']){
                                    var param = {text:app.vtranslate('JS_PERMISSION_DENIED')};
                                    Vtiger_Helper_Js.showPnotify(param);
                                }
                                else if(data['result'].valid && data['result'].markedascompleted){
                                    //Update listview and pagination
                                    var orderBy = jQuery('#orderBy').val();
                                    var sortOrder = jQuery("#sortOrder").val();
                                    var urlParams = {
                                        "orderby": orderBy,
                                        "sortorder": sortOrder
                                    }
                                    jQuery('#recordsCount').val('');
                                    jQuery('#totalPageCount').text('');
                                    thisInstance.getListViewRecords(urlParams).then(function(){
                                        thisInstance.updatePagination();
                                    });
                                    if(data['result'].activitytype == 'Task')
                                        var param = {text:app.vtranslate('JS_TODO_MARKED_AS_COMPLETED')};
                                    else
                                        var param = {text:app.vtranslate('JS_EVENT_MARKED_AS_HELD')};
                                    Vtiger_Helper_Js.showMessage(param);
                                }
                                else{
                                    var param = {text:app.vtranslate('JS_FUTURE_EVENT_CANNOT_BE_MARKED_AS_HELD')};
                                    Vtiger_Helper_Js.showPnotify(param);
                                }
                            });
            },
            function(error, err){
                return false;
			});
            e.stopPropagation();
        });
    },
	registerRowClickEvent: function(){
		var thisInstance = this;
		var listViewContentDiv = this.getListViewContentContainer();
		listViewContentDiv.on('click','.listViewEntries',function(e){
			if(jQuery(e.target, jQuery(e.currentTarget)).is('td:first-child')) return;
            if(jQuery(e.target).is('input[type="checkbox"]')) return;
            if(jQuery(e.target).closest('td').hasClass('listViewEntryNotLink')) return;
			var elem = jQuery(e.currentTarget);
			var recordUrl = elem.data('recordurl');
            if(typeof recordUrl == 'undefined') {
                return;
            }
			window.location.href = recordUrl;
		});
	},
    
    registerStatusPicklistChange: function() {
        var thisInstance = this;
        var listViewContentDiv = this.getListViewContentContainer();
        listViewContentDiv.on('change','.taskstatus',function(e){
            var progressIndicatorInstance = jQuery.progressIndicator({});
            var recordId = jQuery(this).data('recordid');
            var fieldName = jQuery(this).data('fieldname');
            var newStatus = jQuery(this).find('option:selected').html();
            var params = {
                    module : 'Calendar',
                    action : 'CalendarUserActions',
                    mode : 'saveStatusForCalendar',
                    recordId : recordId,
                    fieldName : fieldName,
                    newStatus : newStatus
                };
                AppConnector.request(params).then(function(data){
                    progressIndicatorInstance.hide();
                    if(data.success){
                        var msg = "Status updated.";
                        var type = "success";
                        var title = "JS_OK";
                    }else{
                        var msg = "ERROR: status could not be updated.";
                        var type = "error"; 
                        var title = "JS_ERROR";
                    }
                    var params = {
                            title: app.vtranslate(title),
                            text: app.vtranslate(msg),
                            animation: 'show',
                            type: type
                        };
                    Vtiger_Helper_Js.showPnotify(params);
                });
        });
    },

    registerEvents : function(){
        this._super();
        this.registerHoldFollowupOnEvent();
        this.registerMarkAsHeldEvent();
        this.registerRowClickEvent();
        this.registerStatusPicklistChange();
    }

});
