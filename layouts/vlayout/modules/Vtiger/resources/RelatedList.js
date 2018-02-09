/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

jQuery.Class("Vtiger_RelatedList_Js",{
	registerDuplicateButton : function() {
		//console.log('registerDuplicateButton called');
		jQuery('.duplicateSpan').on('click', function(e) {
			//console.dir('duplicate triggered');
			e.stopImmediatePropagation();
			var url = jQuery(this).data('url');
            var progInd = $.progressIndicator({
                message: 'Duplicating record...',
                position: 'html',
                blockInfo: {
                    enabled: true
                }
            });

			AppConnector.request(url).then(
				function(data) {
                    progInd.progressIndicator({mode: 'hide'});

                    // What is happening???
                    data = JSON.parse(data);

					if(data.result.has_message) {
						bootbox.alert({
                            size: "small",
                            title: "Duplication Log",
                            message: data.result.consolidated,
                            callback: function() {
                                window.location.reload();
                            }
                        });
                    }else {
                        window.location.reload();
                    }
				}
			);
		});
	},

    triggerEditFilter : function() {
        var selectedFilterElement = jQuery('#recordsFilter').find(':selected');
        var cvid = selectedFilterElement.val();
        var lockedViews = JSON.parse(jQuery('input[name="lockedViews"]').val());
        if(lockedViews.indexOf(cvid) != -1) {
            //bootbox.alert("You do not have adequate permissions to edit the current filter.");
            var message = app.vtranslate('JS_LBL_NO_EDIT_FILTER_PERMISSIONS') + ' ' + app.vtranslate('JS_LBL_CREATE_NEW_FILTER_CONFIRMATION');
            Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
                function(e) {
                    triggerCreateFilter();
                }
            )
        } else {
            Vtiger_CustomView_Js.loadFilterView(selectedFilterElement.data('editurl'));
        }
    },
},{

	selectedRelatedTabElement : false,
	parentRecordId : false,
	parentModuleName : false,
	relatedModulename : false,
	relatedTabsContainer : false,
	detailViewContainer : false,
	relatedContentContainer : false,

	setSelectedTabElement : function(tabElement) {
		this.selectedRelatedTabElement = tabElement;
	},

	getSelectedTabElement : function(){
		return this.selectedRelatedTabElement;
	},

	getParentId : function(){
		return this.parentRecordId;
	},

	loadRelatedList : function(params){
		var aDeferred = jQuery.Deferred();
		var thisInstance = this;
		if(typeof this.relatedModulename== "undefined" || this.relatedModulename.length <= 0 ) {
			return;
		}
		var progressIndicatorElement = jQuery.progressIndicator({
			'position' : 'html',
			'blockInfo' : {
				'enabled' : true
			}
		});
		var completeParams = this.getCompleteParams();
		jQuery.extend(completeParams,params);
		AppConnector.request(completeParams).then(
			function(responseData){
				progressIndicatorElement.progressIndicator({
					'mode' : 'hide'
				})
				thisInstance.relatedTabsContainer.find('li').removeClass('active');
				thisInstance.selectedRelatedTabElement.addClass('active');
				thisInstance.relatedContentContainer.html(responseData);
				responseData = thisInstance.relatedContentContainer.html();
				//thisInstance.triggerDisplayTypeEvent();
				Vtiger_Helper_Js.showHorizontalTopScrollBar();
				jQuery('.pageNumbers',thisInstance.relatedContentContainer).tooltip();
				aDeferred.resolve(responseData);
				jQuery('.relatedContents table tbody .select2').select2();
				jQuery('input[name="currentPageNum"]', thisInstance.relatedContentContainer).val(completeParams.page);
				// Let listeners know about page state change.
				app.notifyPostAjaxReady();
			},

			function(textStatus, errorThrown){
				aDeferred.reject(textStatus, errorThrown);
			}
		);
		return aDeferred.promise();
	},

	triggerDisplayTypeEvent : function() {
		var widthType = app.cacheGet('widthType', 'narrowWidthType');
		if(widthType) {
			var elements = jQuery('.listViewEntriesTable').find('td,th');
			elements.attr('class', widthType);
		}
	},

	showSelectRelationPopup : function(){
		var aDeferred = jQuery.Deferred();
		var thisInstance = this;
		var popupInstance = Vtiger_Popup_Js.getInstance();
		popupInstance.show(this.getPopupParams(), function(responseString){
				var responseData = JSON.parse(responseString);
				var relatedIdList = Object.keys(responseData);
				thisInstance.addRelations(relatedIdList).then(
					function(data){
						var relatedCurrentPage = thisInstance.getCurrentPageNum();
						var params = {'page':relatedCurrentPage};
						thisInstance.loadRelatedList(params).then(function(data){
							aDeferred.resolve(data);
						});
					}
				);
			}
		);
		return aDeferred.promise();
	},

	addRelations : function(idList){
		var aDeferred = jQuery.Deferred();
		var sourceRecordId = this.parentRecordId;
		var sourceModuleName = this.parentModuleName;
		var relatedModuleName = this.relatedModulename;

		var params = {};
		params['mode'] = "addRelation";
		params['module'] = sourceModuleName;
		params['action'] = 'RelationAjax';

		params['related_module'] = relatedModuleName;
		params['src_record'] = sourceRecordId;
		params['related_record_list'] = JSON.stringify(idList);

		AppConnector.request(params).then(
			function(responseData){
				aDeferred.resolve(responseData);
			},

			function(textStatus, errorThrown){
				aDeferred.reject(textStatus, errorThrown);
			}
		);
		return aDeferred.promise();
	},

	getPopupParams : function(){
		var parameters = {};
		var parameters = {
			'module' : this.relatedModulename,
			'src_module' : this.parentModuleName,
			'src_record' : this.parentRecordId,
			'multi_select' : true
		}
		return parameters;
	},

	deleteRelation : function(relatedIdList) {
		var aDeferred = jQuery.Deferred();
		var params = {};
		params['mode'] = "deleteRelation";
		params['module'] = this.parentModuleName;
		params['action'] = 'RelationAjax';

		params['related_module'] = this.relatedModulename;
		params['src_record'] = this.parentRecordId;
		params['related_record_list'] = JSON.stringify(relatedIdList);

		AppConnector.request(params).then(
			function(responseData){
				aDeferred.resolve(responseData);
			},

			function(textStatus, errorThrown){
				aDeferred.reject(textStatus, errorThrown);
			}
		);
		return aDeferred.promise();
	},

	getCurrentPageNum : function() {
		return jQuery('input[name="currentPageNum"]',this.relatedContentContainer).val();
	},

	setCurrentPageNumber : function(pageNumber){
		jQuery('input[name="currentPageNum"]').val(pageNumber);
	},

	/**
	 * Function to get Order by
	 */
	getOrderBy : function(){
		return jQuery('#orderBy').val();
	},

	/**
	 * Function to get Sort Order
	 */
	getSortOrder : function(){
			return jQuery("#sortOrder").val();
	},

	getCompleteParams : function(){
		var params = {};
		params['view'] = "Detail";
		params['module'] = this.parentModuleName;
		params['record'] = this.getParentId(),
		params['relatedModule'] = this.relatedModulename,
		params['sortorder'] =  this.getSortOrder(),
		params['orderby'] =  this.getOrderBy(),
		params['page'] = this.getCurrentPageNum();
		params['mode'] = "showRelatedList"
        params.search_params = JSON.stringify(this.getListSearchParams());
		return params;
	},

	/**
	 * Function to handle Sort
	 */
	sortHandler : function(headerElement){
		var aDeferred = jQuery.Deferred();
		var fieldName = headerElement.data('fieldname');
		var sortOrderVal = headerElement.data('nextsortorderval');
		var sortingParams = {
			"orderby" : fieldName,
			"sortorder" : sortOrderVal,
			"tab_label" : this.selectedRelatedTabElement.data('label-key')
		}
		this.loadRelatedList(sortingParams).then(
				function(data){
					aDeferred.resolve(data);
				},

				function(textStatus, errorThrown){
					aDeferred.reject(textStatus, errorThrown);
				}
			);
		return aDeferred.promise();
	},

	/**
	 * Function to handle next page navigation
	 */
	nextPageHandler : function(){
		var aDeferred = jQuery.Deferred();
		var thisInstance = this;
		var pageLimit = jQuery('#pageLimit').val();
		var noOfEntries = jQuery('#noOfEntries').val();
		if(noOfEntries == pageLimit){
			var pageNumber = this.getCurrentPageNum();
			var nextPage = parseInt(pageNumber) + 1;
			var nextPageParams = {
				'page' : nextPage
			}
			this.loadRelatedList(nextPageParams).then(
				function(data){
					thisInstance.setCurrentPageNumber(nextPage);
					aDeferred.resolve(data);
				},

				function(textStatus, errorThrown){
					aDeferred.reject(textStatus, errorThrown);
				}
			);
		}
		return aDeferred.promise();
	},

	/**
	 * Function to handle next page navigation
	 */
	previousPageHandler : function(){
		var aDeferred = jQuery.Deferred();
		var thisInstance = this;
		var aDeferred = jQuery.Deferred();
		var pageNumber = this.getCurrentPageNum();
		if(pageNumber > 1){
			var previousPage = parseInt(pageNumber) - 1;
			var previousPageParams = {
				'page' : previousPage
			}
			this.loadRelatedList(previousPageParams).then(
				function(data){
					thisInstance.setCurrentPageNumber(previousPage);
					aDeferred.resolve(data);
				},

				function(textStatus, errorThrown){
					aDeferred.reject(textStatus, errorThrown);
				}
			);
		}
		return aDeferred.promise();
	},

	/**
	 * Function to handle page jump in related list
	 */
	pageJumpHandler : function(e){
		var aDeferred = jQuery.Deferred();
		var thisInstance = this;
		if(e.which == 13){
			var element = jQuery(e.currentTarget);
			var response = Vtiger_WholeNumberGreaterThanZero_Validator_Js.invokeValidation(element);
			if(typeof response != "undefined"){
				element.validationEngine('showPrompt',response,'',"topLeft",true);
				e.preventDefault();
			} else {
				element.validationEngine('hideAll');
				var jumpToPage = parseInt(element.val());
				var totalPages = parseInt(jQuery('#totalPageCount').text());
				if(jumpToPage > totalPages){
					var error = app.vtranslate('JS_PAGE_NOT_EXIST');
					element.validationEngine('showPrompt',error,'',"topLeft",true);
				}
				var invalidFields = element.parent().find('.formError');
				if(invalidFields.length < 1){
					var currentPage = jQuery('input[name="currentPageNum"]').val();
					if(jumpToPage == currentPage){
						var message = app.vtranslate('JS_YOU_ARE_IN_PAGE_NUMBER')+" "+jumpToPage;
						var params = {
							text: message,
							type: 'info'
						};
						Vtiger_Helper_Js.showMessage(params);
						e.preventDefault();
						return false;
					}
					var jumptoPageParams = {
						'page' : jumpToPage
					}
					this.loadRelatedList(jumptoPageParams).then(
						function(data){
							thisInstance.setCurrentPageNumber(jumpToPage);
							aDeferred.resolve(data);
						},

						function(textStatus, errorThrown){
							aDeferred.reject(textStatus, errorThrown);
						}
					);
				} else {
					e.preventDefault();
				}
			}
		}
		return aDeferred.promise();
	},
	/**
	 * Function to add related record for the module
	 */
	addRelatedRecord : function(element , callback){
		var aDeferred = jQuery.Deferred();
		var thisInstance = this;
		var	referenceModuleName = this.relatedModulename;
		var parentId = this.getParentId();
		var parentModule = this.parentModuleName;
		var quickCreateParams = {};
		var relatedParams = {};
		var relatedField = element.data('name');
		var fullFormUrl = element.data('url');
		relatedParams[relatedField] = parentId;
		var eliminatedKeys = new Array('view', 'module', 'mode', 'action');

		var preQuickCreateSave = function(data){

			var index,queryParam,queryParamComponents;

			//To handle switch to task tab when click on add task from related list of activities
			//As this is leading to events tab intially even clicked on add task
			if(typeof fullFormUrl != 'undefined' && fullFormUrl.indexOf('?')!== -1) {
				var urlSplit = fullFormUrl.split('?');
				var queryString = urlSplit[1];
				var queryParameters = queryString.split('&');
				for(index=0; index<queryParameters.length; index++) {
					queryParam = queryParameters[index];
					queryParamComponents = queryParam.split('=');
					if(queryParamComponents[0] == 'mode' && queryParamComponents[1] == 'Calendar'){
						data.find('a[data-tab-name="Task"]').trigger('click');
					}
				}
			}
			jQuery('<input type="hidden" name="sourceModule" value="'+parentModule+'" />').appendTo(data);
			jQuery('<input type="hidden" name="sourceRecord" value="'+parentId+'" />').appendTo(data);
			jQuery('<input type="hidden" name="relationOperation" value="true" />').appendTo(data);

			if(typeof relatedField != "undefined"){
				var field = data.find('[name="'+relatedField+'"]');
				//If their is no element with the relatedField name,we are adding hidden element with
				//name as relatedField name,for saving of record with relation to parent record
				if(field.length == 0){
					jQuery('<input type="hidden" name="'+relatedField+'" value="'+parentId+'" />').appendTo(data);
				}
			}
			for(index=0; index<queryParameters.length; index++) {
				queryParam = queryParameters[index];
				queryParamComponents = queryParam.split('=');
				if(jQuery.inArray(queryParamComponents[0], eliminatedKeys) == '-1' && data.find('[name="'+queryParamComponents[0]+'"]').length == 0) {
					jQuery('<input type="hidden" name="'+queryParamComponents[0]+'" value="'+queryParamComponents[1]+'" />').appendTo(data);
				}
			}
                        if(typeof callback !== 'undefined') {
                            callback();
                        }
		}
		var postQuickCreateSave  = function(data) {
			thisInstance.loadRelatedList().then(
				function(data){
					aDeferred.resolve(data);
				})
		}

		//If url contains params then seperate them and make them as relatedParams
		if(typeof fullFormUrl != 'undefined' && fullFormUrl.indexOf('?')!== -1) {
			var urlSplit = fullFormUrl.split('?');
			var queryString = urlSplit[1];
			var queryParameters = queryString.split('&');
			for(var index=0; index<queryParameters.length; index++) {
				var queryParam = queryParameters[index];
				var queryParamComponents = queryParam.split('=');
				if(jQuery.inArray(queryParamComponents[0], eliminatedKeys) == '-1') {
					relatedParams[queryParamComponents[0]] = queryParamComponents[1];
				}
			}
		}

		quickCreateParams['data'] = relatedParams;
		quickCreateParams['callbackFunction'] = postQuickCreateSave;
		quickCreateParams['callbackPostShown'] = preQuickCreateSave;
		quickCreateParams['noCache'] = true;
		var quickCreateNode = jQuery('#quickCreateModules').find('[data-name="'+ referenceModuleName +'"]');
		if(quickCreateNode.length <= 0) {
			Vtiger_Helper_Js.showPnotify(app.vtranslate('JS_NO_CREATE_OR_NOT_QUICK_CREATE_ENABLED'))
		}
		quickCreateNode.trigger('click',quickCreateParams);
		return aDeferred.promise();
	},

	getRelatedPageCount : function(){
		var aDeferred = jQuery.Deferred();
		var params = {};
		params['action'] = "RelationAjax";
		params['module'] = this.parentModuleName;
		params['record'] = this.getParentId(),
		params['relatedModule'] = this.relatedModulename,
		params['tab_label'] = this.selectedRelatedTabElement.data('label-key');
		params['mode'] = "getRelatedListPageCount"

		var element = jQuery('#totalPageCount');
		var totalCountElem = jQuery('#totalCount');
		var totalPageNumber = element.text();
		if(totalPageNumber == ""){
			element.progressIndicator({});
			AppConnector.request(params).then(
				function(data) {
					var pageCount = data['result']['page'];
					var numberOfRecords = data['result']['numberOfRecords'];
					totalCountElem.val(numberOfRecords);
					element.text(pageCount);
					element.progressIndicator({'mode': 'hide'});
					aDeferred.resolve();
				},
				function(error,err){

				}
			);
		}else{
			aDeferred.resolve();
		}
		return aDeferred.promise();
	},

    addFollowupEvent : function(e){
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
                                            defaultCallDuration : defaultCallDuration,
                                            defaultOtherEventDuration : defaultOtherEventDuration
                                        }
                                        AppConnector.request(params).then(function(data){
                                            app.hideModalWindow();
                                            progressIndicatorInstance.hide();
                                            if(data['result'].created){
                                                //Update related listview and pagination
                                                Vtiger_Detail_Js.reloadRelatedList();
                                            }
                                        });
                         });
                    });
                    }
                    else{
                        progressIndicatorInstance.hide();
                        Vtiger_Helper_Js.showPnotify(app.vtranslate('JS_NO_EDIT_PERMISSION',"Calendar"));
                    }
				});
    },

    markAsCompleted : function(e){
        var elem = jQuery(e.currentTarget);
        var recordId = elem.closest('tr').data('id');
        var activityType = elem.closest('tr').data('activity-type');
        if (activityType && activityType == 'Task'){
            var message = app.vtranslate('JS_CONFIRM_MARK_COMPLETED');
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
                                    //Update related listview and pagination
                                    Vtiger_Detail_Js.reloadRelatedList();
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
    },

	init : function(parentId, parentModule, selectedRelatedTabElement, relatedModuleName){
		this.selectedRelatedTabElement = selectedRelatedTabElement,
		this.parentRecordId = parentId;
		this.parentModuleName = parentModule;
		this.relatedModulename = relatedModuleName;
		this.relatedTabsContainer = selectedRelatedTabElement.closest('div.related');
		this.detailViewContainer = this.relatedTabsContainer.closest('div.detailViewContainer');
		this.relatedContentContainer = jQuery('div.contents',this.detailViewContainer);
		Vtiger_Helper_Js.showHorizontalTopScrollBar();
	},
        
    registerStatusPicklistChange: function() {
        jQuery('.listViewEntriesTable .taskstatus').on('change',function(e){
            var recordId = jQuery(this).data('recordid');
            var fieldName = jQuery(this).data('fieldname');
            var newStatus = jQuery(this).find('option:selected').html();
            var progressIndicatorInstance = jQuery.progressIndicator({});
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
        this.changeCustomFilterElementView();
        this.registerChangeCustomFilterEvent();
        this.registerRelatedListSearch();
        var listViewPageDiv = this.relatedContentContainer;
        this.registerDateRelatedListSearch(listViewPageDiv);
        this.registerTimeRelatedListSearch(listViewPageDiv);
        this.registerStatusPicklistChange();
    },

    changeCustomFilterElementView : function() {
        var filterSelectElement = jQuery('#recordsFilter');
        if(filterSelectElement.length > 0){
            app.showSelect2ElementView(filterSelectElement,{
                formatSelection : function(data){
                    var resultContainer = jQuery('<span></span>');
                    resultContainer.append(jQuery(jQuery('.filterImage').clone().get(0)).show());
                    resultContainer.append(data.text);
                    return resultContainer;
                }
            });

            var select2Instance = filterSelectElement.data('select2');
            select2Instance.dropdown.append(jQuery('span.filterActionsDiv'));
        }
    },

    registerChangeCustomFilterEvent : function(){
        var thisInstance = this;
        var filterSelectElement = jQuery('#recordsFilter');
        filterSelectElement.change(function(e){
            var element = jQuery(e.currentTarget);
            var cvId = element.find('option:selected').data('id');
            var relatedModuleName = jQuery('.relatedModuleName').val();
            var relatedTabsContainer = thisInstance.relatedTabsContainer;
            var selectedTabElement = relatedTabsContainer.find('li.active');
            var url = selectedTabElement.data('url');
            var relatedUrl = url+'&viewname='+cvId;
            if(relatedUrl.indexOf('?') === -1) {
                relatedUrl = "index.php?"+relatedUrl;
            }
            window.location.href=relatedUrl;
        });

    },

    registerRelatedListSearch : function() {
        var thisInstance = this;
        var listViewPageDiv = thisInstance.relatedContentContainer;
        listViewPageDiv.on('click','[data-trigger="listSearch"]',function(e){
            var reloadUrl='index.php?'
            reloadUrl +='view=Detail&module='+thisInstance.parentModuleName+'&record='+thisInstance.getParentId()+'&relatedModule='+thisInstance.relatedModulename;
            reloadUrl +='&mode=showRelatedList&sortorder='+thisInstance.getSortOrder()+'&orderby='+thisInstance.getOrderBy()+'&page=1';
            reloadUrl +='&viewname='+jQuery('#recordsFilter').val();
            var search_params = JSON.stringify(thisInstance.getListSearchParams());
            reloadUrl += '&search_params='+search_params;
            reloadUrl += '&tab_label='+thisInstance.selectedRelatedTabElement.data('label-key');
            window.location.href = reloadUrl;
        });

        listViewPageDiv.on('keydown','input.listSearchContributor',function(e){
            if(e.which == 13){
                var element = jQuery(e.currentTarget);
                var parentElement = element.closest('tr');
                var searchTriggerElement = parentElement.find('[data-trigger="listSearch"]');
                searchTriggerElement.trigger('click');
            }
        });
    },

    registerDateRelatedListSearch : function(container) {
			var type = 'range';
			var numCalendars = 3;
			var isOpList = getQueryVariable('relatedModule') == 'OPList';
			var fn = function(f) {
				return f.join(',');
			};
			if(isOpList) {
				type = 'single';
				numCalendars = 1;
				fn = function(f) {
					return f;
				};
			}
        container.find('.dateField').each(function(index,element){
            var dateElement = jQuery(element);
            var customParams = {
                calendars: numCalendars,
                mode: type,
                className : 'rangeCalendar',
                onChange: function(formated) {
                    dateElement.val(fn(formated));
                }
            };
            app.registerEventForDatePickerFields(dateElement,true,customParams);
        });

    },

    registerTimeRelatedListSearch : function(container) {
        app.registerEventForTimeFields(container,true);
    },

    getListSearchParams : function(){
        var thisInstance = this;
        var listViewPageDiv = thisInstance.relatedContentContainer;
        var listViewTable = listViewPageDiv.find('.listViewEntriesTable');
        var searchParams = new Array();
        listViewTable.find('.listSearchContributor').each(function(index,domElement){
            var searchInfo = new Array();
            var searchContributorElement = jQuery(domElement);
            var fieldInfo = searchContributorElement.data('fieldinfo');
            var fieldName = searchContributorElement.attr('name');

            var searchValue = searchContributorElement.val();

            if(typeof searchValue == "object") {
                if(searchValue == null) {
                    searchValue = "";
                }else{
                    searchValue = searchValue.join(',');
                }
            }
            searchValue = searchValue.trim();
            if(searchValue.length <=0 ) {
                //continue
                return true;
            }
            var searchOperator = 'c';
            if(fieldInfo.type == "date" || fieldInfo.type == "datetime") {
                searchOperator = 'bw';
            }else if (  fieldInfo.type == "boolean" || fieldInfo.type == "picklist") {
                searchOperator = 'e';
            }else if( fieldInfo.type == 'currency'  || fieldInfo.type == "double" ||
                fieldInfo.type == 'percentage' || fieldInfo.type == "integer"  ||
                fieldInfo.type == "number"){
                if(searchValue.substring(0,2) == '>=' ) {
                    searchOperator = 'h';
                } else if ( searchValue.substring(0,2)== '<=') {
                    searchOperator = 'm';
                } else   if(searchValue.substring(0,1) == '>' ) {
                    searchOperator = 'g';
                } else if ( searchValue.substring(0,1)== '<') {
                    searchOperator = 'l';
                } else {
                    searchOperator = 'e';
                }
            }
            searchInfo.push(fieldName);
            searchInfo.push(searchOperator);
            searchInfo.push(searchValue);
            searchParams.push(searchInfo);
        });
        return new Array(searchParams);
    },
})
