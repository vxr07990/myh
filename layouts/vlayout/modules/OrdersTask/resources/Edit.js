Vtiger_Edit_Js("OrdersTask_Edit_Js", {
}, {
    //This will store the editview form
    editViewForm: false,
   // operativeNameBlock: 'Operative Task Information',
    disabledDispatchBlocks: ['Dispatch Services'],
    //This will store Checkbox dispatch status
    checkboxDispatchStatus:false,
    resourcesCheckCache: {},
    /**
     * This function will return the current form
     */
    getForm: function () {
        if (this.editViewForm == false) {
            this.editViewForm = jQuery('#EditView');
        }
        return this.editViewForm;
    },
    /**
     * This function will return the account name
     */
    getStartDate: function (container) {
        return jQuery('input[name="startdate"]', container).val();
    },
    /**
     * This function will return the account name
     */
    getEndDate: function (container) {
        return jQuery('input[name="enddate"]', container).val();
    },
    /**
     * This function will return the current RecordId
     */
    getRecordId: function (container) {
        return jQuery('input[name="record"]', container).val();
    },

    setDispatchFieldsReadOnly: function() {
        var thisInstance = this;
        jQuery.each(thisInstance.disabledDispatchBlocks, function (index, block_name) {
            jQuery(document).find('.blockHeader:contains("' + block_name + '")').closest('table').find('input').prop('disabled',true).prop('readonly','readonly');
            jQuery('input[name="disp_assignedstart"]').prop('disabled',false).prop('readonly','readonly');
            jQuery('input[name="disp_actualend"]').prop('disabled',false).prop('readonly','readonly');
        });
        jQuery('[name="dispatch_status"]').prop('disabled', true).trigger("liszt:updated");
        jQuery('[name="check_call"]').prop('disabled',true).trigger("liszt:updated");
        if(jQuery('#OrdersTask_editView_fieldName_cancel_task').prop('checked')){
            var allDispatchBlocks = ['Dispatch Updates','Long Distance Dispatch Information','Local Operations Task Details','Custom Information','Description Details','Operative Task Information','Dispatch Services'];
            jQuery.each(allDispatchBlocks, function (index, block_name) {
                    jQuery(document).find('.blockHeader:contains("' + block_name + '")').closest('table').find('input').prop('disabled',true).prop('readonly','readonly');
                    jQuery(document).find('.blockHeader:contains("' + block_name + '")').closest('table').find('select').prop('disabled',true).prop('readonly','readonly').trigger("liszt:updated");
            });
            jQuery('.btn.btn-success').prop('disabled',true);
        }else{
            jQuery('[name="reason_cancelled"]').prop('disabled',true).trigger("liszt:updated");
        }
    },
    registerCancelTaskCheckbox: function() {
        var thisInstance = this;
        var disStatusCurrentVal = jQuery('[name="dispatch_status"]').val();
        jQuery(document).on("change","#OrdersTask_editView_fieldName_cancel_task",function(){
            var select = jQuery('[name=dispatch_status]');
            if(jQuery(this).prop("checked")){
                if(thisInstance.checkboxDispatchStatus==false){
                    thisInstance.checkboxDispatchStatus = select.val();
                }
                select.val('Cancelled').trigger("liszt:updated");
                jQuery('[name="reason_cancelled"]').prop('disabled',false).trigger("liszt:updated");
                jQuery('[name="dispatch_status"]').val('Cancelled').trigger("liszt:updated");
            }else{
                if(thisInstance.checkboxDispatchStatus!=false){
                    select.val(thisInstance.checkboxDispatchStatus).trigger("liszt:updated");
                }
                jQuery('[name="reason_cancelled"]').prop('disabled',true).trigger("liszt:updated");
                jQuery('[name="dispatch_status"]').val(disStatusCurrentVal).trigger("liszt:updated");
            }
        });
    },

    registerAgentTypeChange: function(){
        jQuery(document).on("change","select[name='agent_type']",function(){
            var refenceSelector = jQuery(this).closest('tr').find('.clearReferenceSelection');
            refenceSelector.trigger('click');

        });
    },
    getUrlParameter: function(sParam) {
	var sPageURL = decodeURIComponent(window.location.search.substring(1)),
	    sURLVariables = sPageURL.split('&'),
	    sParameterName,
	    i;

	for (i = 0; i < sURLVariables.length; i++) {
	    sParameterName = sURLVariables[i].split('=');

	    if (sParameterName[0] === sParam) {
		return sParameterName[1] === undefined ? true : sParameterName[1];
	    }
	}
    },
    setParamsFromCapacityCalendar: function(){

	if(this.getUrlParameter("fromcapacity") == "true"){
            jQuery(".cancelLink").attr("onclick","window.open('index.php?module=OrdersTask&view=List','_self')");

            var seldate = this.getUrlParameter("seldate");
            if(seldate) {
                jQuery('[name="service_date_from"]').val(seldate);
                app.registerEventForDatePickerFields();

            }
	}
    },
    registerRecordPreSaveEvent : function(form) {
	var thisInstance = this;
	if(typeof form == 'undefined') {
	    form = this.getForm();
	}

	form.on(Vtiger_Edit_Js.recordPreSave, function(e, data) {
	    var ok = true;
	    jQuery('[name="LBL_EMPLOYEES_AVAILABILITY"] tbody .timepicker-default').each(function(){
		if(jQuery(this).val() == "" || jQuery(this).data("wrong-range") == "true"){
		    ok = false;
		    return false;
		}
	    });
	    if(!ok){
		var params = {
		    title: app.vtranslate('JS_MESSAGE'),
		    text: app.vtranslate('One of the selected time is not in the Agent Manager range time.'),
		    animation: 'show',
		    type: 'error',
		};
		Vtiger_Helper_Js.showPnotify(params);
		e.preventDefault();
	    }
	});
    },

    registerCustomTooltipEvents: function() {
	    var references = jQuery('td.customToolTip');
	    var lastPopovers = [];

	    // Fetching reference fields often is not a good idea on a given page.
	    // The caching is done based on the URL so we can reuse.
	    var CACHE_ENABLED = true; // TODO - add cache timeout support.

	    function prepareAndShowTooltipView() {
		hideAllTooltipViews();

		var el = jQuery(this);
		var field = (jQuery(this).hasClass('total_estimated_personnel')) ? 'personnel' : 'vehicles';
		var id = jQuery(this).closest("tr.listViewEntries").data("id");

		var url = '?module=OrdersTask&view=TooltipAjax&record='+id+'&customTooltip='+field;
		var cachedView = CACHE_ENABLED ? jQuery('[data-url-cached="'+url+'"]') : null;
		if (cachedView && cachedView.length) {
			showTooltip(el, cachedView.html());
		} else {
		    AppConnector.request(url).then(function(data){
			cachedView = jQuery('<div>').css({display:'none'}).attr('data-url-cached', url);
			cachedView.html(data);
			jQuery('body').append(cachedView);
			showTooltip(el, data);
		    });
		}
	    }

	    function showTooltip(el, data) {
		var title = (jQuery(el).hasClass('total_estimated_personnel')) ? 'Estimated Personnel' : 'Estimated Vehicles';
		el.popover({
		    title: title,
		    trigger: 'manual',
		    content: data,
		    animation: false,
		    placement:  'left',
		    template: '<div class="popover popover-tooltip"><div class="arrow"></div><div class="popover-inner"><button name="vtTooltipClose" class="close" style="color:white;opacity:1;font-weight:lighter;position:relative;top:3px;right:3px;">x</button><h3 class="popover-title"></h3><div class="popover-content"><div></div></div></div></div>'
		});
		lastPopovers.push(el.popover('show'));
		registerToolTipDestroy();
	    }

	    function hideAllTooltipViews() {// Hide all previous popover
		var lastPopover = null;
		while (lastPopover = lastPopovers.pop()) {
		    lastPopover.popover('hide');
		}
	    }

	    references.each(function(index, el){
		jQuery(el).hoverIntent({
		    interval: 100,
		    sensitivity: 7,
		    timeout: 10,
		    over: prepareAndShowTooltipView,
		    out: hideAllTooltipViews
		});
	    });

	    function registerToolTipDestroy() {
		jQuery('button[name="vtTooltipClose"]').on('click', function(e){
		    var lastPopover = lastPopovers.pop();
		    lastPopover.popover('hide');
		});
	    }
    },

    registerBasicEvents: function (container) {
        this._super(container);
        this.setDispatchFieldsReadOnly();
        this.registerCancelTaskCheckbox();
        this.registerAgentTypeChange();
        this.registerEventForOwnerField();
        this.checkAndHideField();
        //this.checkQuantittyOfCPU();
        this.setParamsFromCapacityCalendar();
        this.registerCheckTaskForBlockedDate();
        this.resetTimes();
        this.registerRecordPreSaveEvent();
        this.hideDateSpread();
        this.registerDateSpreadChange();

    //Hiding Total Estimated Personnel y Total Estimated Vehicles
	jQuery('[name="total_estimated_personnel"]').closest('td').hide().prev("td").hide();
	jQuery('[name="total_estimated_vehicles"]').closest('td').hide().prev("td").hide();
    },
    checkQuantittyOfCPU:function () {
        var form=this.getForm();
        form.submit(function (e) {
            var cpuBlock = jQuery('[name="LBL_CPU"]');
            jQuery('tr.itemRow',cpuBlock).each(function () {
                var cartonqty = jQuery('[name^="cartonqty_"]', jQuery(this)).val();
                var packingqty = jQuery('[name^="packingqty_"]', jQuery(this)).val();
                var unpackingqty = jQuery('[name^="unpackingqty_"]',jQuery(this)).val();
                if((cartonqty  == null || cartonqty=='' || cartonqty==0)
                    && (packingqty == null || packingqty== '' || packingqty==0)
                    && (unpackingqty == null || unpackingqty == '' || unpackingqty==0)){
                    jQuery('.removeItem', this).trigger('click');
                }
            });
        });

    },
    showLoadingMessage: function (message) {
        var loadingMessage = app.vtranslate(message);
        var progressIndicatorElement = jQuery.progressIndicator({
            'message': loadingMessage,
            'position': 'html',
            'blockInfo': {
                'enabled': true
            }
        });

        return progressIndicatorElement;
    },
    hideLoadingMessage: function (progressIndicatorElement) {
        progressIndicatorElement.progressIndicator({
            'mode': 'hide'
        });
    },
    getParticipantAgentsData: function(orderID){
        var thisInstance = this;
        var params = {
            'mode': 'getParticipantAgentsData',
            'module':'OrdersTask',
            'view':'NewLoadLocalDispatch',
            'orderID': orderID,
        };
	var progressIndicatorElement = thisInstance.showLoadingMessage('Searching Participant Agents for the order selected...');
        AppConnector.request(params).then(function (data) {
            var data = JSON.parse(data);
	    if(data.success){
		var jsonArray = data.result;
		for(var i=0;i<jsonArray.length;i++){
		    var option = new Option(jsonArray[i].label, jsonArray[i].value);
		    jQuery('[name="participating_agent"]').append(jQuery(option));
		}
		jQuery('[name="participating_agent"]').trigger("liszt:updated");
	    }
	    thisInstance.hideLoadingMessage(progressIndicatorElement);
        });
    },
    openPopUp: function(e) {
	var thisInstance = this;
	var parentElem = jQuery(e.target).closest('td');

	var params = this.getPopUpParams(parentElem);

	var isMultiple = false;
	if (params.multi_select) {
	    isMultiple = true;
	}
	var sourceFieldElement = jQuery('input[class="sourceField"]', parentElem);
	var prePopupOpenEvent = jQuery.Event(Vtiger_Edit_Js.preReferencePopUpOpenEvent);
	sourceFieldElement.trigger(prePopupOpenEvent);

	if (prePopupOpenEvent.isDefaultPrevented()) {
	    return;
	}
        var popupInstance = Vtiger_Popup_Js.getInstance();
        popupInstance.show(params, function(data) {
            var responseData = JSON.parse(data);
            var responseData = JSON.parse(data);
            var dataList = [];
            for (var id in responseData) {
                var data = {
                    'name': responseData[id].name,
                    'id': id
                };
                dataList.push(data);
                if (!isMultiple) {
                    thisInstance.setReferenceFieldValue(parentElem, data);
                    if(jQuery(sourceFieldElement).attr("name") == "ordersid"){
                        thisInstance.getParticipantAgentsData(id);
                    }
                }
            }

            if (isMultiple) {
                sourceFieldElement.trigger(Vtiger_Edit_Js.refrenceMultiSelectionEvent, { 'data': dataList });
            }
            sourceFieldElement.trigger(Vtiger_Edit_Js.postReferenceSelectionEvent, { 'data': responseData });
        });
    },
    getReferencedModuleName : function(parenElement){
        return jQuery('input[name^="popupReferenceModule"]',parenElement).val();
    },
    registerClearReferenceSelectionEvent : function(container) {
        container.find('.clearReferenceSelection').on('click', function(e){
            var element = jQuery(e.currentTarget);
            var parentTdElement = element.closest('td');
            var fieldNameElement = parentTdElement.find('.sourceField');
            var fieldInfo = fieldNameElement.data('fieldinfo');
            if(fieldInfo != undefined){
                if(typeof fieldInfo != 'object'){
                    fieldInfo = JSON.parse(fieldInfo);
                }
                var fieldName = fieldInfo.name;
            }else {
                var fieldName = fieldNameElement.attr('name');
            }
            fieldNameElement.val('');
            parentTdElement.find('[name^="'+fieldName+'_display"]').removeAttr('readonly').val('');
            element.trigger(Vtiger_Edit_Js.referenceDeSelectionEvent);
            fieldNameElement.trigger(Vtiger_Edit_Js.referenceDeSelectionEvent);
            e.preventDefault();
        })
    },
    //test
    getPopUpParams : function(container) {
        var params = {};
        var sourceModule = app.getModuleName();
        var popupReferenceModule = jQuery('input[name^="popupReferenceModule"]',container).val();
        var sourceFieldElement = jQuery('input[class="sourceField"]',container);
        var fieldInfo = sourceFieldElement.data('fieldinfo');
        var sourceField = fieldInfo.name;
        var sourceRecordElement = jQuery('input[name="record"]');
        var sourceRecordId = '';
        var search_key = '';
        var search_value = '';
        if(sourceField == 'stopnumber') {
            search_key = 'extrastops_relcrmid';
            search_value = jQuery('input.sourceField[name="ordersid"]').data('displayvalue');
        }

        if(sourceRecordElement.length > 0) {
            sourceRecordId = sourceRecordElement.val();
        }

        var isMultiple = false;
        if(sourceFieldElement.data('multiple') == true){
            isMultiple = true;
        }

        var params = {
            'module' : popupReferenceModule,
            'src_module' : sourceModule,
            'src_field' : sourceField,
            'src_record' : sourceRecordId,
            'search_key' : search_key,
            'search_value' : search_value
        };

        if(sourceField == 'orderstask_agent'){
           params.agent_type = jQuery('select[name="agent_type"]').val();
        }
        if(sourceField == 'calendarcode'){
           params.participating_agent = jQuery('select[name="participating_agent"]').val();
        }

        if(isMultiple) {
            params.multi_select = true ;
        }
        return params;
    },
    searchModuleNames : function(params) {
            var aDeferred = jQuery.Deferred();

            if(typeof params.module == 'undefined') {
                    params.module = app.getModuleName();
            }

            if(typeof params.action == 'undefined') {
                    params.action = 'BasicAjax';
            }

            // check agentid select exists
            if (jQuery('select[name="agentid"]').length > 0) {
                    params.agentId = jQuery('select[name="agentid"]').val();
            }
            // check participating Agent select exists
            if (jQuery('select[name="participating_agent"]').length > 0) {
                    params.participatingAgent = jQuery('select[name="participating_agent"]').val();
            }

            AppConnector.request(params).then(
                    function(data){
                            aDeferred.resolve(data);
                    },
                    function(error){
                            //TODO : Handle error
                            aDeferred.reject();
                    }
                    );
            return aDeferred.promise();
    },
    registerAutoCompleteFields : function(container) {
        var thisInstance = this;
        container.find('input.autoComplete').autocomplete({
            'minLength' : '3',
            'source' : function(request, response){
                //element will be array of dom elements
                //here this refers to auto complete instance
                var inputElement = jQuery(this.element[0]);
                var searchValue = request.term;
                var params = thisInstance.getReferenceSearchParams(inputElement);
                params.search_value = searchValue;
                if(params.search_module != 'Equipment'){
                    params.parent_id = jQuery('input[name="ordersid"]').val();
                    params.parent_module = "Orders";
                }
                thisInstance.searchModuleNames(params).then(function(data){
                    var reponseDataList = [];
                    var serverDataFormat = data.result;
                    if(serverDataFormat.length <= 0) {
                        jQuery(inputElement).val('');
                        serverDataFormat = new Array({
                            'label' : app.vtranslate('JS_NO_RESULTS_FOUND'),
                            'type'  : 'no results'
                        });
                    }
                    for(var id in serverDataFormat){
                        var responseData = serverDataFormat[id];
                        reponseDataList.push(responseData);
                    }
                    response(reponseDataList);
                });
            },
            'select' : function(event, ui ){
                var selectedItemData = ui.item;
                //To stop selection if no results is selected
                if(typeof selectedItemData.type != 'undefined' && selectedItemData.type=="no results"){
                    return false;
                }
                selectedItemData.name = selectedItemData.value;
                var element = jQuery(this);
                var tdElement = element.closest('td');
                thisInstance.setReferenceFieldValue(tdElement, selectedItemData);

                var sourceField = tdElement.find('input[class="sourceField"]').attr('name');
                var fieldElement = tdElement.find('input[name="'+sourceField+'"]');

                fieldElement.trigger(Vtiger_Edit_Js.postReferenceSelectionEvent,{'data':selectedItemData});
            },
            'change' : function(event, ui) {
                var element = jQuery(this);
                //if you dont have readonly attribute means the user didnt select the item
                if(element.attr('readonly')== undefined) {
                    element.closest('td').find('.clearReferenceSelection').trigger('click');
                }
            },
            'open' : function(event,ui) {
                //To Make the menu come up in the case of quick create
                jQuery(this).data('autocomplete').menu.element.css('z-index','100001');

            }

        });
    },

    registerEventForOwnerField : function () {
        var thisInstance = this;
        jQuery('select[name=agentid]').on('change',function () {
            var formElement = thisInstance.getForm();
            var ordersIdDisplay = formElement.find('input[name="ordersid_display"]').val();
            if (ordersIdDisplay == ''){
                var participating_agent = formElement.find('[name="participating_agent"]').data('selected-value');
                var recordId = formElement.find('input[name="record"]').val();

                var relationOperation = jQuery('[name="relationOperation"]');
                var exist = (relationOperation.length > 0) ? true : false;
                var optionChange = '<option>Select an Option</option>';
                if (exist == false) {
                    var ordersTaskParams = [];
                    ordersTaskParams['module'] = 'OrdersTask';
                    ordersTaskParams['action'] = 'ActionAjax';
                    ordersTaskParams['mode'] = 'getParticipatingAgentByOwner';
                    ordersTaskParams['agentId'] = jQuery(this).val();
                    ordersTaskParams['record'] = recordId;
                    ordersTaskParams['participating_agent'] = participating_agent;

                    AppConnector.request(ordersTaskParams).then(
                        function (data) {
                            if (data.success) {
                                jQuery.each(data.result, function (key, item) {
                                    if (item.selected == true){
                                        optionChange = optionChange + '<option value="' + item.agentsid + '" selected>' + item.agentname + '</option>';
                                    }
                                    else{
                                        optionChange = optionChange + '<option value="' + item.agentsid + '">' + item.agentname + '</option>';
                                    }
                                });
                                jQuery('select[name=participating_agent]').html(optionChange).trigger('liszt:updated');
    }
                        }
                    );
                }
            }
        });

        jQuery('select[name=agentid]').trigger('change');
    },

    checkAndHideField: function () {
        var thisInstance = this;
        jQuery('[name="date_spread"][type="checkbox"],[name="multiservice_date"][type="checkbox"]').on('change',function () {
            var name = jQuery(this).attr('name');
            var val1 = jQuery('[name="date_spread"][type="checkbox"]').prop('checked');
            var val2 = jQuery('[name="multiservice_date"][type="checkbox"]').prop('checked');
            var service_date_to = jQuery('[name="service_date_to"]');
            var pref_date_service = jQuery('[name="pref_date_service"]');
            if(val1 || val2){
                if(name == 'date_spread'){
                    if(val1) {
                        jQuery('[name="multiservice_date"]').prop('checked', false);
                        //pref_date_service.closest('span.span10').removeClass('hide');
                        //pref_date_service.closest('td').prev().find('label').removeClass('hide');
                    }
                }else if (val2) {
                    //pref_date_service.closest('span.span10').addClass('hide');
                    //pref_date_service.closest('td').prev().find('label').addClass('hide');
                }
                if(name=='multiservice_date' && val2){
                    jQuery('[name="date_spread"]').prop('checked',false);
                }
                //service_date_to.closest('span.span10').removeClass('hide');
                //service_date_to.closest('td').prev().find('label').removeClass('hide');
            }else{
                //service_date_to.closest('span.span10').addClass('hide');
                //service_date_to.closest('td').prev().find('label').addClass('hide');
                //pref_date_service.closest('span.span10').addClass('hide');
                //pref_date_service.closest('td').prev().find('label').addClass('hide');
            }
        });
        jQuery('[name="date_spread"],[name="multiservice_date"]').trigger('change');
    },

    setReferenceFieldValue : function(container, params) {
        var fieldInfo = container.find('input[class="sourceField"]').data('fieldinfo');
        if(fieldInfo != undefined){
            if(typeof fieldInfo != 'object'){
                fieldInfo = JSON.parse(fieldInfo);
            }
            var sourceField = fieldInfo.name;
        }else {
            var sourceField = container.find('input[class="sourceField"]').attr('name');
        }
        var fieldElement = container.find('input[name^="'+sourceField+'"]');
        var sourceFieldDisplay = sourceField+"_display";
        var fieldDisplayElement = container.find('input[name^="'+sourceFieldDisplay+'"]');
        var popupReferenceModule = container.find('input[name^="popupReferenceModule"]').val();

        var selectedName = params.name;
        var id = params.id;

        fieldElement.val(id);
        fieldDisplayElement.val(selectedName).attr('readonly',true);
        if(sourceField == 'ordersid'){
            this.updateEquipmentItem(id);
        }

        fieldElement.trigger(Vtiger_Edit_Js.referenceSelectionEvent, {'source_module' : popupReferenceModule, 'record' : id, 'selectedName' : selectedName});
        fieldDisplayElement.validationEngine('closePrompt',fieldDisplayElement);
    },
    updateEquipmentItem: function (orderid) {
        var params = {
            module: app.getModuleName(),
            view:'GetEquipmentAndPackingItems',
            orderId:orderid
        };
        AppConnector.request(params).then(
            function (data) {
                var equipmentBlock = jQuery('[name="LBL_EQUIPMENT"]');
                var cpuBlock = jQuery('[name="LBL_CPU"]');
                var response = JSON.parse(data);
                var results = response.result;
                if(results['equipment_items'][1] != undefined){
                    jQuery('tr.itemRow .removeItem',equipmentBlock).trigger('click');
                    jQuery.each(results['equipment_items'],function (index,values) {
                        jQuery('button.addItem',equipmentBlock).first().trigger('click');
                        jQuery('[name^="equipment_name_"].sourceField',equipmentBlock).last().val(values.equipment_name);
                        jQuery('[name^="equipment_name_display_"]',equipmentBlock).last().val(values.equipment_name_display).prop('readonly',true);
                        jQuery('[name^="equipmentqty_"]',equipmentBlock).last().val(values.equipmentqty);
                    });
                }
                if(results['packing_items'][1] != undefined){
                    jQuery('tr.itemRow .removeItem',cpuBlock).trigger('click');
                    jQuery.each(results['packing_items'],function (index,values) {
                        jQuery('button.addItem',cpuBlock).first().trigger('click');
                        jQuery('[name^="carton_name_"]',cpuBlock).last().val(values.carton_name);
                        jQuery('[name^="cartonqty_"]',cpuBlock).last().val(values.cartonqty);
                        jQuery('[name^="packingqty_"]',cpuBlock).last().val(values.packingqty);
                        jQuery('[name^="unpackingqty_"]',cpuBlock).last().val(values.unpackingqty);
                    });
                }
            }
        );
    },
    resetTimes: function(){
        if(this.getRecordId() == ''){
            jQuery('input[name="disp_assignedstart"]').val('');
            jQuery('input[name="disp_actualend"]').val('');
        }
    },
    registerCheckTaskForBlockedDate: function(){
        var thisInstance = this;
        jQuery('input[name="service_date_from"],select[name="business_line"],select[name="participating_agent"]').change(function(){
            var date = jQuery('[name="service_date_from"]').val();
            var businessLine = jQuery('select[name="business_line"]').val();
            var participatingAgent = jQuery('select[name="participating_agent"]').val();
            if(date && businessLine && participatingAgent){
                var progressIndicatorElement = thisInstance.showLoadingMessage('Checking if date is blocked...');
                var params = {
                    module: 'OrdersTask',
                    action: 'ActionAjax',
                    mode: 'isDateBlockedForTask',
                    date: date,
                    businessLine: businessLine,
                    participatingAgent: participatingAgent
                };
                AppConnector.request(params).then(function (data) {
                    thisInstance.hideLoadingMessage(progressIndicatorElement);
                    if(data.success){
                        if(data.result.isBlocked){
                            var params = {
                                title: app.vtranslate('JS_MESSAGE'),
                                text: app.vtranslate('The selected date within the Service Date From field is blocked, please select a different date'),
                                animation: 'show',
                                type: 'info',
                                delay: '6000'
                            };
                            Vtiger_Helper_Js.showPnotify(params);
                            jQuery('[name="service_date_from"]').val('').focus();
                        }
                    }
                },
                function (error) {
                    //To send empty events if error occurs
                    callback([]);
                });
            }
        });
    },
    hideDateSpread: function () {
        var thisInstance = this;

        if(jQuery('#OrdersTask_editView_fieldName_date_spread').is(':checked')){
            jQuery('#OrdersTask_editView_fieldName_service_date_to').closest('tr').show();            
        }else{
            jQuery('#OrdersTask_editView_fieldName_service_date_to').closest('tr').hide();
        }
    },
    registerDateSpreadChange:function(){
        var thisInstance = this;
        jQuery('#OrdersTask_editView_fieldName_date_spread').change(function(){
            thisInstance.hideDateSpread();
        });
    }

});

jQuery(document).ready(function () {
    var e = new OrdersTask_Edit_Js;
    e.registerCustomTooltipEvents();
});
