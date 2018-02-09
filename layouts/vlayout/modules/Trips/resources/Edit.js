/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("Trips_Edit_Js", {}, {

    getPopUpParams: function (container) {
        var params = this._super(container);
        var sourceFieldElement = jQuery('input[class="sourceField"]', container);

        if (sourceFieldElement.attr('name') == 'driver_id') {
            params['employee_type'] = 'Contractor';
            params['popup_type'] = 'get_drivers';
        }else if(sourceFieldElement.attr('name') == 'trips_vehicle'){
            params['popup_type'] = 'get_tripsvehicles';
        }else if(sourceFieldElement.attr('name') == 'trips_trailer'){
            params['popup_type'] = 'get_trailer';
        }
        return params;
    },
    /**
    * Function which will handle the reference auto complete event registrations
    * @params - container <jQuery> - element in which auto complete fields needs to be searched
    */
    registerAutoCompleteFields : function(container) {
            var thisInstance = this;
    var autoCompleteOptions = {
        'minLength' : '3',
        'source' : function(request, response){
            //element will be array of dom elements
            //here this refers to auto complete instance
            var inputElement = jQuery(this.element[0]);
            var searchValue = request.term;
            var params = thisInstance.getReferenceSearchParams(inputElement);
            params.search_value = searchValue;
            params.field_name = inputElement.attr('name');
            var doSearch = true;
            if (params.field_name == 'driver_id_display' || params.field_name == 'trips_vehicle_display' || params.field_name == 'trips_trailer_display'){
                var date = jQuery('input[name="trips_firstload"]').val();
                if(date !== ''){
                    params.date = date;
                }else{
                    Vtiger_Helper_Js.showPnotify({
                        title: 'Please, complete First Load Date',
                        text: 'To enable search for Drivers',
                        type: 'info',
                        hide: true
                    });
                    doSearch = false;
                }
            }
            if(doSearch){
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
            }else{
                response([]);
            }
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
                            if(app.getModuleName() == 'Workflows') {
                                tdElement = element.closest('.conditionRow');
            }
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
    };
            $(document).on('keydown.autoComplete', '.autoComplete', function() {
                    $(this).autocomplete(autoCompleteOptions);
            });
            container.find('input.autoComplete').autocomplete(autoCompleteOptions);
    },
    setReferenceFieldValue: function(container, params, id) {
        var sourceField = container.find('input[class="sourceField"]').attr('name');
        var fieldElement = container.find('input[name="' + sourceField + '"]');
        var sourceFieldDisplay = sourceField + "_display";
        var fieldDisplayElement = container.find('input[name="' + sourceFieldDisplay + '"]');
        var popupReferenceModule = container.find('input[name="popupReferenceModule"]').val();
        var selectedName = params.name;

        fieldElement.val(params.id);
        fieldDisplayElement.val(selectedName).attr('readonly', true);
        fieldElement.trigger(Vtiger_Edit_Js.referenceSelectionEvent, { 'source_module': popupReferenceModule, 'record': id, 'selectedName': selectedName });

        fieldDisplayElement.validationEngine('closePrompt', fieldDisplayElement);
        fieldElement.trigger('change');
    },
    openPopUp: function(e) {
            var thisInstance = this;
            var parentElem = jQuery(e.target).closest('td');

            var params = this.getPopUpParams(parentElem);

            var isMultiple = false;
            if (params.multi_select) {
                isMultiple = true;
            }
            
            // check agentid select exists
            if(jQuery('select[name="agentid"]').length>0){
                params['agentId'] = jQuery('select[name="agentid"]').val();
            }

            var sourceFieldElement = jQuery('input[class="sourceField"]', parentElem);
            var prePopupOpenEvent = jQuery.Event(Vtiger_Edit_Js.preReferencePopUpOpenEvent);
            sourceFieldElement.trigger(prePopupOpenEvent);
            if (sourceFieldElement.attr('name') == 'driver_id' || sourceFieldElement.attr('name') == 'trips_vehicle' || sourceFieldElement.attr('name') == 'trips_trailer'){
                var date = jQuery('input[name="trips_firstload"]').val();
                if(date !== ''){
                    params['date'] = date;
                }else{
                    prePopupOpenEvent.preventDefault();
                    Vtiger_Helper_Js.showPnotify({
                    title: 'Please, complete First Load Date',
                    text: 'To enable search for Drivers or Vehicles',
                    type: 'info',
                    hide: true
                } );
                }
            }

            if (prePopupOpenEvent.isDefaultPrevented()) {
                return;
            }
        var popupInstance = Vtiger_Popup_Js.getInstance();
        popupInstance.show(params, function(data) {
            var responseData = JSON.parse(data);
//            var responseData = JSON.parse(data);
            var dataList = new Array();
            for (var id in responseData) {
                var data = {
                    'name': responseData[id].name,
                    'id': id
                }
                dataList.push(data);
                if (!isMultiple) {
                    thisInstance.setReferenceFieldValue(parentElem, data);
                    if(jQuery(sourceFieldElement).attr("name") == "driver_id"){
                        thisInstance.getDriverData(id);
                    }else if(jQuery(sourceFieldElement).attr("name") == "trips_vehicle"){
                        thisInstance.getVehicleData(id,"trips_vehicle");
                    }else if(jQuery(sourceFieldElement).attr("name") == "trips_trailer"){
                        thisInstance.getVehicleData(id,"trips_trailer");
                    }
                }
            }

            if (isMultiple) {
                sourceFieldElement.trigger(Vtiger_Edit_Js.refrenceMultiSelectionEvent, { 'data': dataList });
            }
            sourceFieldElement.trigger(Vtiger_Edit_Js.postReferenceSelectionEvent, { 'data': responseData });
        });
    },
    getVehicleData: function(vehicleId, type){
        var thisInstance = this;
        var popupType = type;
        var urlParams = {
            module: 'Trips',
            action: 'TripsActions',
            mode: 'getVehicleInfo',
            vehicle_id: vehicleId,
            popuptype: type,
        };
        AppConnector.request(urlParams).then(
        function (data) {
            if(popupType === "trips_vehicle"){
                jQuery('[name="trips_vehi_length"]').val(data.result['vehicle_length']);
                jQuery('[name="trips_vehi_cube"]').val(data.result['vehicle_cubec']);
                jQuery('[name="agent_id"]').val(data.result['agent_id']);
                if(data.result['agent_id']>0) {
                    jQuery('[name="agent_id_display"]').val(data.result['agentname']).prop('readonly', true);
                }
            }else{
                jQuery('[name="trips_trailer_length"]').val(data.result['vehicle_length']);
                jQuery('[name="trips_trailer_cube"]').val(data.result['vehicle_cubec']);
            }
        });
    },
    getDriverData: function(driverId){
        var thisInstance = this;
        var urlParams = {
            module: 'Trips',
            action: 'TripsActions',
            mode: 'getDriverInfo',
            driverId: driverId,
        };
        var actualUrl = window.location.href;
        //Todo: this is wrong way of doing this
        AppConnector.requestPjax(urlParams).then(
        function (data) {
            var data = JSON.parse(data.result);
            jQuery('[name="trips_driverlastname"]').val(data.DriverLastName);
            jQuery('[name="trips_driverno"]').val(data.DriverNo);
            jQuery('[name="trips_driverfirstname"]').val(data.DriverFirstName);
            jQuery('[name="trips_drivercellphone"]').val(data.DriverCellPhone);
            jQuery('[name="trips_driversemail"]').val(data.DriverEmail);
            jQuery('[name="agent_unit"]').val(data.OwnerId);
            jQuery('[name="agent_unit_display"]').val(data.OwnerName).attr('readonly','readonly');
            jQuery('[name="trips_performancerating"]').val(data.PerformanceRating);
            jQuery('[name="trips_pqcrating"]').val(data.PcqRating);
            jQuery('[name="trips_driverclaimratio"]').val(data.DriverClaimRatio);
			thisInstance.lockDriverFields();
            window.history.replaceState("", "", actualUrl);
            if(data.OnNotice){
                Vtiger_Helper_Js.showPnotify({
                    title: 'Please be advised',
                    text: 'Driver : ' + data.DriverFirstName + ' ' + data.DriverLastName + ' is On Notice.',
                    type: 'info',
                    hide: true
                } );
            }
        });
    },
    populateOriginEmptyState: function(){
        var urlParams = {
            module: 'Trips',
            action: 'TripsActions',
            mode: 'getStateInfo',
            record: jQuery('[name=record]').val()
        };
        if(urlParams.record != ''){
            var actualUrl = window.location.href;
            //Todo: this is wrong way of doing this
            AppConnector.requestPjax(urlParams).then(
            function (data) {
                var data = JSON.parse(data.result);
                if(data.originState != ''){
                    jQuery('[name=origin_state] option:contains("' + data.originState + '")').attr('selected','true').trigger("liszt:updated");
                }
                if(data.emptyState != ''){
                    jQuery('[name=empty_state] option:contains("' + data.emptyState + '")').attr('selected','true').trigger("liszt:updated");
                }
                window.history.replaceState("", "", actualUrl);
            });
        }
    },
    /*
    *  Once the driver data is loaded into the view hook onto this function and lock the fields
    * */
    lockDriverFields: function() {
        if(jQuery('input[name="driver_id_display"]').val()) {
          var lockFields = ['trips_driverlastname', 'trips_driverfirstname', 'trips_driverno', 'trips_drivercellphone', 'trips_driversemail'];

          for(var i=0;i<lockFields.length;i++) {
             jQuery('input[name="'+lockFields[i]+'"]').prop('readonly', true);
          }
        }
    },
    clearDriverFields: function(){
        jQuery(document).on("click",".Trips_editView_fieldName_driver_id_clear",function(){
            jQuery('[name="trips_driverlastname"]').val("");
            jQuery('[name="trips_driverno"]').val("");
            jQuery('[name="trips_driverfirstname"]').val("");
            jQuery('[name="trips_drivercellphone"]').val("");
            jQuery('[name="trips_driversemail"]').val("");
            jQuery('[name="trips_performancerating"]').val("");
            jQuery('[name="trips_pqcrating"]').val("");
            jQuery('[name="trips_driverclaimratio"]').val("");

            Vtiger_Helper_Js.showPnotify({'text' : 'Driver Unassigned!','type' : 'info'} );
        });
    },

    getParameterByName: function(name, url) {
        if (!url) url = window.location.href;
        name = name.replace(/[\[\]]/g, "\\$&");
        var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, " "));
    },
    	getReferenceSearchParams : function(element){
		var tdElement = jQuery(element).closest('td');
		var params = {};
		var searchModule = this.getReferencedModuleName(tdElement);
                var sourceField = jQuery('.sourceField',tdElement).attr('name');
		params.search_module = searchModule;
                params.source_field = sourceField;
		return params;
	},

    registerEvents: function () {
        this._super();
        this.lockDriverFields();
        this.clearDriverFields();
        this.populateOriginEmptyState();
        // this.initializeAddressAutofill('Vanlines');

        if(this.getParameterByName("calledby") === "ldd" && this.getParameterByName("related_module") == "Orders"){
            jQuery("form.recordEditView").append('<input type="hidden" name="related_module" value="'+this.getParameterByName("related_module")+'">');
            jQuery("form.recordEditView").append('<input type="hidden" name="calledby" value="'+this.getParameterByName("calledby")+'">');
            jQuery("form.recordEditView").append('<input type="hidden" name="ispackage" value="'+this.getParameterByName("ispackage")+'">');
            jQuery("form.recordEditView").append('<input type="hidden" name="related_record_list" value="'+this.getParameterByName("related_record_list")+'">');

        }
    },
});

