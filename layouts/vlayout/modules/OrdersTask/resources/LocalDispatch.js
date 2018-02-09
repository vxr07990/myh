Vtiger_List_Js("OrdersTask_LocalDispatch_Js", {
    triggerEditFilter: function() {
	var localDispatchJS = new  OrdersTask_LocalDispatch_Js();
	
	var selectedFilterElement = jQuery('#customFilter').find(':selected');
	var cvid = selectedFilterElement.val();
	var lockedViews = JSON.parse(jQuery('input[name="lockedViews"]').val());
	if(lockedViews.indexOf(cvid) != -1) {
	    var message = app.vtranslate('JS_LBL_NO_EDIT_FILTER_PERMISSIONS') + ' ' + app.vtranslate('JS_LBL_CREATE_NEW_FILTER_CONFIRMATION');
	    Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
		function(e) {
		    triggerCreateFilter();
		}
	    );
	} else {
	    var editURL = selectedFilterElement.data('editurl');
	    console.log(editURL);
	    localDispatchJS.loadFilterView(editURL, null);
	    //Vtiger_CustomView_Js.loadFilterView(selectedFilterElement.data('editurl'));
	}
    },
    loadFilterViewStatic: function(){
        //let localDispatchJS = new  OrdersTask_LocalDispatch_Js();
        var localDispatchJS = new  OrdersTask_LocalDispatch_Js();
        localDispatchJS.loadFilterView(null, null);
    },
    ldFilterCreate: function(e){
	var createUrl = jQuery(e).data("createurl");
	var aux = jQuery(e).data("rigthtable");
	var className = (aux == "Crew") ? "resources_crew" : (aux == "Equipment") ? "resources_equipment" : "resources_vendors";
	
	jQuery("."+className+" .ldFilterSelect").data('select2').close();
	
	var OrderTaskInstance = new OrdersTask_LocalDispatch_Js();
	OrderTaskInstance.loadFilterView(createUrl,aux);
    },   
    getPopupParams : function(id){
        var parameters = {};
        var parameters = {
            'module' : "OrdersTask",
            'src_module' : "OrdersTask",
            'src_record' : id,
            'multi_select' : false,
            'popup_type': 'local_dispatch_related',
        }
        return parameters;
    },
    openPopUp : function(id){
        var thisInstance = this;
        var idTo = jQuery('.select_task:checkbox:checked').val();
        var OrderTaskInstance = new OrdersTask_LocalDispatch_Js();
        if (!OrderTaskInstance.isTaskAccepted(idTo)) { //dispatch_status
            return false;
        }


            
        var params = thisInstance.getPopupParams(idTo);

        // check agentid select exists
        if(jQuery('select[name="agentid"]').length>0){
            params['agentId'] = jQuery('select[name="agentid"]').val();
        }
        
        var popupInstance = Vtiger_Popup_Js.getInstance();
        popupInstance.show(params,function(data){
            var data = JSON.parse(data);
            for (first in data){
                thisInstance.copyResources(data[first].info.orderstaskid, idTo);
                break;
            }
        });
    },
    copyResources: function(idFrom,idsTo){
        var auxInstance = new OrdersTask_LocalDispatch_Js;
        var params = {
            'mode': 'copyResources',
            'module':'OrdersTask',
            'view':'NewLoadLocalDispatch',
            'task_id': idFrom,
            'task_ids_to': idsTo
        };
        var progressIndicatorElement = auxInstance.showLoadingMessage('Copying Resources...');
        AppConnector.request(params).then(
            function (responseData) {
                var responseData = JSON.parse(responseData);
                if(responseData.result.result !== "OK"){
                    alert(responseData.result.msg);
                }else{
                    auxInstance.hideLoadingMessage(progressIndicatorElement);
                    auxInstance.refreshView();
                }
            });
    },
    showCopyModal: function(){
        var thisInstance = this;
        if(jQuery('.select_task:checkbox:checked').length === 1){
            thisInstance.openPopUp();
        }
    },
    triggerCreateLocalTask: function(){
        var win = window.open('index.php?module=OrdersTask&view=Edit', '_blank');
        win.focus();
    },
    customActionButtons: function(resourceType){
            if(jQuery('.select_task:checkbox:checked').length > 0){
                var auxArr = new Array();
                jQuery('.select_task:checkbox:checked').each(function(){
                    auxArr.push(jQuery(this).data("id"));
                });
                var task_id = auxArr.join();
                var params = {
                    'module': app.getModuleName(),
                    'mode': 'massTaskResourceHandler',
                    'view': 'NewLoadLocalDispatch',
                    'task_id': task_id,
                    'resource_type': resourceType,
                }
                var progressIndicatorElement = instance.showLoadingMessage('Updating Resources...');
                AppConnector.request(params).then(function (data) {
                    var data = JSON.parse(data);
                    instance.hideLoadingMessage(progressIndicatorElement);
                    if (data.success && data.result.result == "OK") {
                        instance.refreshView();
                    }else{
                        console.log(data.result.msg);
                    }
                });
            }else{
                var params = {
                    title: app.vtranslate('JS_ERROR'),
                    text: app.vtranslate('Select one or more tasks first.'),
                    animation: 'show',
                    type: 'error'
                };
                Vtiger_Helper_Js.showPnotify(params);
            }
        
    },
    exportData: function () {
        var exportActionUrl = 'index.php?module=OrdersTask&view=Export';
        var listInstance = Vtiger_List_Js.getInstance();
        var cvId = listInstance.getCurrentCvId();

        var auxArr = new Array();
        jQuery('.listViewEntriesTable tbody tr.listViewEntries').each(function () {
            auxArr.push(jQuery(this).data("id"));
        });

        exportActionUrl += '&selected_ids=' + auxArr.join() + '&excluded_ids=&viewname=' + cvId + '&qty=' + auxArr.length;

        window.location.href = exportActionUrl;

    },
}, {
    is_running: false,
    tableHeight: '200px',
    changeFilter: false,
    splitter_obj : false,
    registerChangeCustomFilterEvent : function(){
	var thisInstance = this;
	var filterSelectElement = this.getFilterSelectElement();
	filterSelectElement.change(function(e){
	    jQuery('#pageNumber').val("1");
	    jQuery('#pageToJump').val('1');
	    jQuery('#orderBy').val('');
	    jQuery("#sortOrder").val('');
	    var cvId = thisInstance.getCurrentCvId();
	    selectedIds = new Array();
	    excludedIds = new Array();

	    var urlParams ={
		"viewname" : cvId,
		//to make alphabetic search empty
		"search_key" : thisInstance.getAlphabetSearchField(),
		"search_value" : "",
		"search_params" : ""
	    }
	    //Make the select all count as empty
	    jQuery('#recordsCount').val('');
	    //Make total number of pages as empty
	    jQuery('#totalPageCount').text("");
	    thisInstance.getListViewRecords(urlParams).then (function(){
		thisInstance.ListViewPostOperation();
		thisInstance.updatePagination();
		var e = new OrdersTask_List_Js;
		e.registerCustomTooltipEvents();
	    });
	});
    },
    registerManageResources: function(){ //
        var thisInstance = this;
        jQuery(document).on("click","#assign_vendor, #assign_vehicle,#assign_employee,#remove_vehicle,#remove_employee",function(){
	    var id = jQuery(this).prop("id");
	    var resourceClass = (id == "assign_vehicle") ? "resource_vehicle" : (id == "assign_vendor") ? "resource_vendor" : "assigned_resource";
            var resourceType = (id == "assign_vehicle") ? "Vehicle" : (id == "assign_vendor") ? "Vendor" : "Employee";
            
                var resource_ids = new Array();
                var proles = new Array();
                jQuery('.'+resourceClass+':checkbox:checked').each(function(){
                    proles.push(jQuery(this).closest("tr").find(".chznprole option:selected").val()); //get role selected
                    resource_ids.push(jQuery(this).data("id")); //get employee id checked
                });
                var task_id =[];
                jQuery('.select_task:checkbox:checked').each(function(){
                    task_id.push(jQuery(this).data("id"));
                });
                var lead_employee_id = jQuery('.lead_resource:radio:checked').data("id");
                if(!thisInstance.isTaskAccepted(task_id)){ //dispatch_status
                    return false;
                }
                var params = {
                    'module': app.getModuleName(),
                    'mode': 'taskResourceHandler',
                    'view': 'NewLoadLocalDispatch',
                    'task_id': task_id,
                    'lead_employee_id': lead_employee_id,
                    'resource_type': resourceType,
                    'resource_ids':resource_ids,
                    'proles': proles,
                }
                var progressIndicatorElement = instance.showLoadingMessage('Updating Resources...');
                AppConnector.request(params).then(function (data) {
                    var data = JSON.parse(data);
                    instance.hideLoadingMessage(progressIndicatorElement);
                    if (data.success && data.result.result == "OK") {
			thisInstance.changeFilter = false;
                        for(i = 0; i < task_id.length; i++){
                            if(resourceType == 'Employee'){
                                var resourceSelect = jQuery('.employees_chzn[data-orderstaskid="' + task_id[i] + '"]');
                                thisInstance.loadEmployees(task_id);
                            }else if(resourceType == "Vehicle"){
                                var resourceSelect = jQuery('.vehicles_chzn[data-orderstaskid="' + task_id[i] + '"]');
                                thisInstance.loadVehicles(task_id);
                            }else{//Vendor
				var resourceSelect = jQuery('.vendorchzn[data-orderstaskid="' + task_id[i] + '"]');
			    }
                            
                            resourceSelect.find('option').remove().end();
                            var newOptions = data.result.resources;
                            for (var resourceId in newOptions) {
                                var resourceName = newOptions[resourceId];
                                var lead_employee = (resourceId == lead_employee_id ? ' lead_employee':'');
                                var option = '<option selected value="' + resourceId+ '" class="employee_' + resourceId + lead_employee + '">' + resourceName + '</option>';
                                resourceSelect.append(option);
                            }
                            resourceSelect.trigger('liszt:updated');
                            thisInstance.registerMakeBoldLeadCrew();
                            instance.initChznDragDrop('.employees_chzn');
                            instance.initChznDragDrop('.vehicles_chzn');
			    instance.initChznDragDrop('.vendorchzn');
                        }
                    }else{
                        console.log(data.msg); // Print on console the exception error
                    }
                });
            
        });
    },
    addResourceDropped: function(e,t,resourceType){
        var thisInstance = this;
            if(!this.isTaskAccepted(t)){ //dispatch_status
		return false;
            }
            
            if(!jQuery('#assigned_'+e).is(':checked')){
                jQuery('#assigned_'+e).attr("checked", "checked");
                jQuery('#assigned_'+e).checked = true;
            }
	    //resourceType = module
            if(resourceType == "Employee"){
                var resourceClass = "assigned_resource";
            }else if(resourceType == "Vehicle"){
                var resourceClass = "resource_vehicle";
            }else{
                var resourceClass = "resource_vendor";
	    }
            if(jQuery('.'+resourceClass+':checkbox:checked').length > 0){
                var resource_ids = new Array();
                jQuery('.'+resourceClass+':checkbox:checked').each(function(){
                    resource_ids.push(jQuery(this).data("id"));
                });
                var task_id = t;
                var lead_employee_id = jQuery('.lead_resource:radio:checked').data("id");
                var params = {
                    'module': app.getModuleName(),
                    'mode': 'taskResourceHandler',
                    'view': 'NewLoadLocalDispatch',
                    'task_id': task_id,
                    'lead_employee_id': lead_employee_id,
                    'resource_type': resourceType,
                    'resource_ids':resource_ids,
                }
                var progressIndicatorElement = instance.showLoadingMessage('Updating Resources...');
                AppConnector.request(params).then(function (data) {
                    var data = JSON.parse(data);
                    instance.hideLoadingMessage(progressIndicatorElement);
                    if (data.success && data.result.result == "OK") {
                        thisInstance.changeFilter = false;
                       for(i = 0; i < task_id.length; i++){
                            if(resourceType == 'Employee'){
                                var resourceSelect = jQuery('.employees_chzn[data-orderstaskid="' + task_id[i] + '"]');
                                thisInstance.loadEmployees(task_id);
                            }else if(resourceType == "Vehicle"){
                                var resourceSelect = jQuery('.vehicles_chzn[data-orderstaskid="' + task_id[i] + '"]');
                                thisInstance.loadVehicles(task_id);
                            }else{//Vendor
				var resourceSelect = jQuery('.vendorchzn[data-orderstaskid="' + task_id[i] + '"]');
			    }
                            
                            resourceSelect.find('option').remove().end();
                            var newOptions = data.result.resources;
                            for (var resourceId in newOptions) {
                                var resourceName = newOptions[resourceId];
                                var lead_employee = (resourceId == lead_employee_id ? ' lead_employee':'');
                                var option = '<option selected value="' + resourceId+ '" class="employee_' + resourceId + lead_employee + '">' + resourceName + '</option>';
                                resourceSelect.append(option);
                            }
                            resourceSelect.trigger('liszt:updated');
                            thisInstance.registerMakeBoldLeadCrew();
                            instance.initChznDragDrop('.employees_chzn');
                            instance.initChznDragDrop('.vehicles_chzn');
			    instance.initChznDragDrop('.vendorchzn');
                        }
                       
                    }else{
                        console.log(data.msg); // Print on console the exception error
                    }
                });
            }
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
    isTaskAccepted: function (task_id) {
        var allAccepted = true;

        if(!Array.isArray(task_id)){
            task_id = [task_id];
        }

        for(i = 0; i < task_id.length; i++){
            if (jQuery(".dispatch_status[data-orderstaskid='" + task_id[i] + "'] option:selected").text().trim() !== "Accepted") { //dispatch_status
                allAccepted = false;
            }
        }
        if(!allAccepted){
            var params = {
                title: app.vtranslate('JS_ERROR'),
                text: app.vtranslate('Order task dispatch status must be accepted first.'),
                animation: 'show',
                type: 'error'
            };
            Vtiger_Helper_Js.showPnotify(params);
            return false;
        } else {
            return true;
        }
    },
    hideLoadingMessage: function (progressIndicatorElement) {
        progressIndicatorElement.progressIndicator({
            'mode': 'hide'
        });
    },
    getResourceParams: function(task_id,mode){
        thisInstance = this;
        var cvid = 0;
        
        if(jQuery('input[name="instance"]') !== 'graebel'){
            if(mode == 'getVehiclesTable'){
                var cvid = jQuery('.resources_equipment').find('select.ldFilterSelect').val();
            }else if(mode == 'getEmployeeTable'){
                var cvid = jQuery('.resources_crew').find('select.ldFilterSelect').val();
            }else if(mode == 'getVendorsTable'){
                var cvid = jQuery('.resources_vendors').find('select.ldFilterSelect').val();
            }
        }
        
        var params = {
            'module': app.getModuleName(),
            'parent': app.getParentModuleName(),
            'mode': mode,
            'view': 'NewLoadLocalDispatch',
            'task_id': task_id,
            'customview':cvid,
	    'isChange': thisInstance.changeFilter,
        }
        return params;
    },
    loadEmployees: function(task_id){
        instance = this;
        var params = this.getResourceParams(task_id, 'getEmployeeTable');
        var progressIndicatorElementEmployees = instance.showLoadingMessage('LBL_LOADING_RESOURCES');
        window.progressIndicatorElementEmployees = progressIndicatorElementEmployees;
        AppConnector.request(params).then(function (data) {
            var data = JSON.parse(data);
            if (data.success){
                jQuery('.resources_crew').find(".panel").html(data.result);
                //jQuery('.employees-tables').find('.chzn-select ').chosen();
                instance.initResourcesDragDrop(task_id);
                instance.registerFilterOptionsHoverEvent("resources_crew");
                instance.registerLDDeleteFilterClickEvent("resources_crew");
                instance.registerLDEditFilterClickEvent("resources_crew");
                var progressIndicatorElementEmployees = window.progressIndicatorElementEmployees;
                instance.hideLoadingMessage(progressIndicatorElementEmployees);
                instance.updateAccordionsHeight();
            } else {
                var progressIndicatorElementEmployees = window.progressIndicatorElementEmployees;
                instance.hideLoadingMessage(progressIndicatorElementEmployees);
            }

        });
    },
    loadVehicles: function(task_id){
        instance = this;
        var params = this.getResourceParams(task_id, 'getVehiclesTable');
        var progressIndicatorElementVs = instance.showLoadingMessage('LBL_LOADING_RESOURCES');
        window.progressIndicatorElementVs = progressIndicatorElementVs;
        AppConnector.request(params).then(function (data) {
            var data = JSON.parse(data);
            if (data.success) {
                jQuery('.resources_equipment').find(".panel").html(data.result);
                instance.initResourcesDragDrop(task_id);
                instance.registerFilterOptionsHoverEvent("resources_equipment");
                instance.registerLDDeleteFilterClickEvent("resources_equipment");
                instance.registerLDEditFilterClickEvent("resources_equipment");
            }
            instance.initResourcesDragDrop(task_id);
            var progressIndicatorElementVs = window.progressIndicatorElementVs;
            instance.hideLoadingMessage(progressIndicatorElementVs);
            instance.updateAccordionsHeight();
        });
    },
    loadVendors: function(task_id){ //TODO add on drag&drop
        instance = this;
        var params = this.getResourceParams(task_id, 'getVendorsTable');
        var progressIndicatorElementVen = instance.showLoadingMessage('LBL_LOADING_RESOURCES');
        window.progressIndicatorElementVen = progressIndicatorElementVen;
        AppConnector.request(params).then(function (data) {
            var data = JSON.parse(data);
            if (data.success && data.result != '') {
                jQuery('.resources_vendors').removeClass('hide');
                jQuery('.resources_vendors').find(".panel").html(data.result);
                instance.initResourcesDragDrop(task_id);
                instance.registerFilterOptionsHoverEvent("resources_vendors");
                instance.registerLDDeleteFilterClickEvent("resources_vendors");
                instance.registerLDEditFilterClickEvent("resources_vendors");
                
            } else if ( data.success && data.result == '' ){
                jQuery('.resources_vendors').addClass('hide');
            }
            var progressIndicatorElementVen = window.progressIndicatorElementVen;
            instance.hideLoadingMessage(progressIndicatorElementVen);
            instance.initResourcesDragDrop(task_id);
            instance.updateAccordionsHeight();
        });
    },
    initResourcesDragDrop: function(task_ids){
	//drag and drop inicialization after tables are created
	//this will hold reference to the tr we have dragged and its helper
	var e = {};
	jQuery(".employees-tables tr,.vehicles-tables tr,.vendors-tables tr").draggable({
	    helper: function(){ //A function that will return a DOM Element to use while dragging.
		var text = 'Resource';
		if($(this).hasClass('employees')){
		    text = $(this).find(" td a").html();
		}else{ //vehicle & vendor
		    text = $(this).find(" td a").html();
		}
            return $("<div>").html(text).css({'border': '1px dotted #000','background': '#fff','font-size': '1.2em'})},
	    scroll: false,
	    cursor: "move", cursorAt: { top: 0, left: 0 },
	    appendTo: 'body',
	    start: function(event, ui) {
		e.tr = this;
		e.module = ($(this).hasClass('employees')) ? "Employee" : ($(this).hasClass('vehicle')) ? "Vehicle" : "Vendor";
	    },
	});
	var t = {}
	jQuery('.listViewEntries').removeClass('ui-droppable');
	if(!(task_ids.constructor === Array)){
	    var aux = task_ids;
            if(aux.constructor === String && aux.indexOf(',') !== -1){
                task_ids = aux.split(',');
            }else{
                task_ids = new Array();
                task_ids.push(aux);
            }
	}
	for(task_id in task_ids){
	    jQuery('.listViewEntries[data-id="' + task_ids[task_id] + '"]').droppable({
		drop: function( event, ui ) {
                    var task_id =[];
                    jQuery('.select_task:checkbox:checked').each(function(){
                        task_id.push(jQuery(this).data("id"));
                    });
		    t.checked = jQuery(this).find('.listViewEntriesCheckBox').prop('checked');
		    if(t.checked){
			instance.addResourceDropped(e.tr.id,task_id,e.module);   
		    }
		}
	    });
	    jQuery('.listViewEntries[data-id="' + task_id + '"]').addClass('highlightBackgroundColor');
	}
    },
    initChznDragDrop: function (resource_class) {
        jQuery('.listViewEntries').each(function () {
            var select_id = jQuery(this).find(resource_class).attr('id');
            var chzn_id = '#' + select_id + '_chzn';
            jQuery(chzn_id).find('.search-choice').draggable({
                helper: function () {
                    var text = $(this).html();
                    return $("<div>").html(text).css({'padding': '2px', 'border': '1px dotted #000', 'background': '#fff', 'font-size': '1.2em'})
                },
                scroll: false,
                cursor: "move",
                appendTo: 'body',
            });

            if (resource_class == '.employees_chzn') {
                jQuery(chzn_id).find('.search-choice').addClass('employee_drag');
            } else if(resource_class == '.vehicles_chzn'){
                jQuery(chzn_id).find('.search-choice').addClass('vehicle_drag');
            }else if(resource_class == '.vendorchzn'){
		jQuery(chzn_id).find('.search-choice').addClass('vendor_drag');
	    }


            jQuery(chzn_id).droppable({
                accept: function (d) {
                    if (resource_class == '.employees_chzn' && d.hasClass('employee_drag')) {
                        return true;
                    } else if (resource_class == '.vehicles_chzn' && d.hasClass('vehicle_drag')) {
                        return true;
                    } else if (resource_class == '.vendorchzn' && d.hasClass('vendor_drag')) {
			return true;
		    }

                    return false;
                },
                drop: function (e, ui) {
                    var movedresource_id = '#' + ui.draggable.attr('id');
                    var resource_name = jQuery(movedresource_id).text().trim();
                    var select_id = movedresource_id.split('_')[0];
                    var remove_taskid = jQuery(select_id).data('orderstaskid');
                    var target_id = '#' + jQuery(this).attr('id').split('_')[0];
                    var target_taskid = jQuery(target_id).data('orderstaskid');
                    var resource_type = (ui.draggable.hasClass('employee_drag')?'Employee': (ui.draggable.hasClass('vehicle_drag')) ? 'Vehicle' : 'Vendor');

                     if(!thisInstance.isTaskAccepted(target_taskid)){ //dispatch_status
                        return false;
                    }

                    jQuery(select_id).find('option').each(function () {
                        if (jQuery(this).text().trim() == resource_name) {
                            //Remove option from select
                            var res_id = jQuery(this).val();
                            jQuery(this).remove();
                            jQuery(select_id).trigger('liszt:updated');


                            //Update Target select with the new option
                            resourceSelect = jQuery(target_id);
                            var option = '<option selected value="' + res_id + '">' + resource_name + '</option>';
                            resourceSelect.append(option);
                            resourceSelect.trigger('liszt:updated');
                            instance.initChznDragDrop('.employees_chzn');
                            instance.initChznDragDrop('.vehicles_chzn');

//                            console.log('Resource: ' + res_id + ' type: '+ resource_type + ' Was remove from task_id: ' + remove_taskid + ' And added to task_id: ' + target_taskid);
                            
                            var params = {
                                'module': app.getModuleName(),
                                'mode': 'dragDropUpdate',
                                'view': 'NewLoadLocalDispatch',
                                'res_id': res_id,
                                'resource_type': resource_type,
                                'remove_taskid':remove_taskid,
                                'target_taskid': target_taskid,
                            }
                            var progressIndicatorElement = instance.showLoadingMessage('Updating Resources...');
                            AppConnector.request(params).then(function (data) {
                                var data = JSON.parse(data);
                                instance.hideLoadingMessage(progressIndicatorElement);
                                if (data.success && data.result.result == "OK") {
//                                    console.log('updated...');
                                }else{
                                    console.log(data.msg); 
                                }
                                //hack to re load resources tables
                                if(jQuery('.listViewEntriesTable input.select_task[type="checkbox"]:checked').length > 0){
                                    jQuery('.listViewEntriesTable input.select_task[type="checkbox"]:checked:first').change();
                                }
                                instance.initChznDragDrop('.employees_chzn');
                                instance.initChznDragDrop('.vehicles_chzn');
				instance.initChznDragDrop('.vendorchzn');
                            });
                        }
                    });
                }
            });
        });
    },
    requestDirections: function(start, end, mapObject) { 
        instance = this;
        var directionsService = new google.maps.DirectionsService();
        directionsService.route({ 
            origin: start, 
            destination: end, 
            travelMode: google.maps.DirectionsTravelMode.DRIVING 
        }, function(result) { 
            new google.maps.DirectionsRenderer({
                map: window.mapObject,
                directions: result
            });
        }); 
    },
    loadMap: function(task_id,map){
        instance = this;
        var params = this.getResourceParams(task_id, 'getDirectionsData');
        var progressIndicatorElementMap = instance.showLoadingMessage('LBL_LOADING_RESOURCES');
        window.progressIndicatorElementMap = progressIndicatorElementMap;
        AppConnector.request(params).then(function (data) {
            var data = JSON.parse(data);
            var myArr = data.result;
            if (data.success && myArr.length > 0) {
                for(var i = 0; i < myArr.length; i++){
                    instance.requestDirections(myArr[i].from, myArr[i].to);
                }
                google.maps.event.trigger(window.mapObject, "resize");
            }
            var progressIndicatorElementMap = window.progressIndicatorElementMap;
            instance.hideLoadingMessage(progressIndicatorElementMap);
        });
    },
    registerCheckboxSelection: function(){
        var instance = this;
        jQuery(document).on("change",".select_task",function(){
            instance.registerDisableEnableDateAndTime();
            instance.resetResourceTable();
            if(jQuery('.select_task:checkbox:checked').length > 0){
                var task_id = 0;
                var myOptions = {
                    zoom: 2,
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    center: new google.maps.LatLng(35.306808813403926,-98.17305),
                    mapTypeControl: false,
                    scaleControl: false,
                    scrollwheel: false
                };
                var mapObject = new google.maps.Map(document.getElementById("panel-map"), myOptions);
                window.mapObject = mapObject;
                
            
                if(jQuery('.select_task:checkbox:checked').length == 1){
                    jQuery(".resources_crew,.resources_equipment").show();
                    task_id = jQuery('.select_task:checkbox:checked').data("id");
			instance.changeFilter = false;
                        instance.loadEmployees(task_id);
                        instance.loadVehicles(task_id);
                        instance.loadVendors(task_id);
                        if(jQuery('.resources_map').find('.accordion').hasClass('active')){
                            instance.loadMap(task_id);
                        }
                            
                        instance.registerDisableEnableDateAndTime(task_id);
                }else if(jQuery('.select_task:checkbox:checked').length > 1){
                        jQuery(".resources_crew,.resources_equipment").show();
                        var auxArr = new Array();
                        jQuery('.select_task:checkbox:checked').each(function(){
                            auxArr.push(jQuery(this).data("id"));
                        });
                        task_id = auxArr.join();
			instance.changeFilter = false;
                        instance.loadEmployees(task_id);
                        instance.loadVehicles(task_id);
                        instance.loadVendors(task_id);
                }else{
                    jQuery(".resources_crew,.resources_equipment").hide();
                    var auxArr = new Array();
                    jQuery('.select_task:checkbox:checked').each(function(){
                        auxArr.push(jQuery(this).data("id"));
                    });
                    task_id = auxArr.join();
                }
            }else{
                jQuery('.panel').html('');
                instance.updateAccordionsHeight();
            }
        });
    },
    refreshView: function (doRefreshResourceTables = true) {
        thisInstance = this;
        if (!thisInstance.is_running) {
            thisInstance.is_running = true;
            thisInstance.getListViewRecords({'page': '1'}).then(
                    function (data) {
                        //To Set the page number as first page
                        jQuery('#pageNumber').val('1');
                        jQuery('#pageToJump').val('1');
                        jQuery('#totalPageCount').text("");
                        thisInstance.calculatePages().then(function () {
                            thisInstance.updatePagination();
                        });

                        //OT5799 OT5804
                        if(doRefreshResourceTables){
                            //hack to re load resources tables
                            if(jQuery('.listViewEntriesTable input.select_task[type="checkbox"]:checked').length > 0){
                                jQuery('.listViewEntriesTable input.select_task[type="checkbox"]:checked:first').change();
                            }
                        }
                        thisInstance.is_running = false;
                    },
                    function (textStatus, errorThrown) {
                    });
        } 
    },
    resetResourceTable: function () {
        var empty_crew_table = '<table class="table table-bordered listViewEntriesTable"><tbody><tr><td style="padding: 4%;">'+ app.vtranslate('JS_CHOOSE_TASK') + app.vtranslate('LBL_AVAILABLE_CREW') +'</td></tr></tbody></table>';
        jQuery('.resources_crew').find(".panel").html(empty_crew_table);
        var empty_equipment_table = '<table class="table table-bordered listViewEntriesTable"><tbody><tr><td style="padding: 4%;">'+ app.vtranslate('JS_CHOOSE_TASK') + app.vtranslate('LBL_AVAILABLE_VEHICLES') +'</td></tr></tbody></table>';
        jQuery('.resources_equipment').find(".panel").html(empty_equipment_table);
        var empty_vendor_table = '<table class="table table-bordered listViewEntriesTable"><tbody><tr><td style="padding: 4%;">'+ app.vtranslate('JS_CHOOSE_TASK') + app.vtranslate('LBL_AVAILABLE_VENDORS') +'</td></tr></tbody></table>';
        jQuery('.resources_vendors').find(".panel").html(empty_vendor_table);
         window.mapObject = 0;
            var empty_map = '<table class="table table-bordered listViewEntriesTable" id="dummytable"><tbody><tr><td > <div id="map_label" style="padding: 4%;">'+ app.vtranslate('JS_CHOOSE_TASK') + app.vtranslate('LBL_AVAILABLE_MAP') +'</div><div id="map" style="display:none;height:200px;"></div></td></tr></tbody></table>';
            jQuery('.resources_map').find(".panel").html(empty_map);
    },
    registerDateFilterChange: function () {
        thisInstance = this;
       
        jQuery('#filter_date_from').on('change', function(ev){
            var d1 = new Date(jQuery('#filter_date_from').val().replace(/-/g, "/")); //safari
            var d2 = new Date(jQuery('#filter_date_to').val().replace(/-/g, "/")); //safari
            if(d1 > d2){
                jQuery('#filter_date_to').val(jQuery('#filter_date_from').val());
                thisInstance.initDatePicker();
            }
            thisInstance.refreshView();
        }); 
        
        jQuery('#filter_date_to').on('change', function(ev){
            var d1 = new Date(jQuery('#filter_date_from').val().replace(/-/g, "/")); //safari
            var d2 = new Date(jQuery('#filter_date_to').val().replace(/-/g, "/")); //safari
            if(d2 < d1){
                jQuery('#filter_date_from').val(jQuery('#filter_date_to').val());
                thisInstance.initDatePicker();
            }
            thisInstance.refreshView();
        });
    },
    /*
    * Function which will give you all the list view params
    */
    getListViewRecords : function(urlParams) {
        var aDeferred = jQuery.Deferred();
        if(typeof urlParams == 'undefined') {
            urlParams = {};
        }

        urlParams.customViewUpdate = true;

        var thisInstance = this;
        var loadingMessage = jQuery('.listViewLoadingMsg').text();
        var progressIndicatorElement = jQuery.progressIndicator({
            'message' : loadingMessage,
            'position' : 'html',
            'blockInfo' : {
                'enabled' : true
            }
        });

        var defaultParams = this.getDefaultParams();
        var urlParams = jQuery.extend(defaultParams, urlParams);
        AppConnector.request(urlParams).then(
            function(data){
                progressIndicatorElement.progressIndicator({
                    'mode' : 'hide'
                });
                var data2 = jQuery(data);
                var listViewContents = jQuery(data2["0"]);//div.root-div
                var listViewContentsContainer = jQuery('#listViewContents');
                listViewContentsContainer.html(listViewContents);
                jQuery('.listViewEntriesDiv ').height(jQuery('.bodyContents').height() - jQuery(".listViewTopMenuDiv").height()); //Fix window height
                thisInstance.hideShowResourceTab();
               
                app.showSelect2ElementView(listViewContentsContainer.find('select.select2'));
                app.showReferenceMultiSelectView(listViewContentsContainer.find('input.select2'));
                app.changeSelectElementView(listViewContentsContainer);
                thisInstance.registerTimeListSearch(listViewContentsContainer);

                thisInstance.registerDateListSearch(listViewContentsContainer);
                thisInstance.calculatePages().then(function(data){
                    //thisInstance.triggerDisplayTypeEvent();
                    Vtiger_Helper_Js.showHorizontalTopScrollBar();
                    thisInstance.fixHeights(false);

                    var selectedIds = thisInstance.readSelectedIds();
                    if(selectedIds != ''){
                        if(selectedIds == 'all'){
                            jQuery('.listViewEntriesCheckBox').each( function(index,element) {
                                jQuery(this).attr('checked', true).closest('tr').addClass('highlightBackgroundColor');
                            });
                            jQuery('#deSelectAllMsgDiv').show();
                            var excludedIds = thisInstance.readExcludedIds();
                            if(excludedIds != ''){
                                jQuery('#listViewEntriesMainCheckBox').attr('checked',false);
                                jQuery('.listViewEntriesCheckBox').each( function(index,element) {
                                    if(jQuery.inArray(jQuery(element).val(),excludedIds) != -1){
                                        jQuery(element).attr('checked', false).closest('tr').removeClass('highlightBackgroundColor');
                                    }
                                });
                            }
                        } else {
                            var updatedSelectedId = [];
                            jQuery('.listViewEntriesCheckBox').each( function(index,element) {
                                if(jQuery.inArray(jQuery(element).val(),selectedIds) != -1){
                                    updatedSelectedId.push(jQuery(element).val());
                                    jQuery(this).attr('checked', true).closest('tr').addClass('highlightBackgroundColor');
                                }

                                thisInstance.writeSelectedIds(updatedSelectedId);

                            });
                        }
                        thisInstance.checkSelectAll();
                    }
                    aDeferred.resolve(data);

                    // Let listeners know about page state change.
                    app.notifyPostAjaxReady();
                });
            },

            function(textStatus, errorThrown){
                aDeferred.reject(textStatus, errorThrown);
            }
        );
        return aDeferred.promise();
    },
    getDefaultParams: function () {
        var pageNumber = jQuery('#pageNumber').val();
        var module = app.getModuleName();
        var parent = app.getParentModuleName();
        var orderBy = jQuery('#orderBy').val();
        var sortOrder = jQuery("#sortOrder").val();
        var cvid = this.getCurrentCvId();

        var params = {
            'module': module,
            'parent': parent,
            'page': pageNumber,
            'view': "NewLocalDispatch",
            'viewname':cvid,
            'orderby': orderBy,
            'sortorder': sortOrder,
            'from_date': this.getDBformattedDate(new Date(Date.parse(jQuery("#filter_date_from").val()))),
            'to_date': this.getDBformattedDate(new Date(Date.parse(jQuery("#filter_date_to").val()))),
            //'filtro': jQuery('#associated_filter option:selected').val(),
        }
        
        params.search_params = JSON.stringify(this.getListSearchParams());

        return params;
    },
    getDBformattedDate: function (date) {
        var mm = date.getMonth() + 1;
        mm = (mm < 10) ? '0' + mm : mm;
        var dd = date.getDate();
        dd = (dd < 10) ? '0' + dd : dd;
        return date.getFullYear() + '-' + mm + '-' + dd;
    },
    initDatePicker: function () {
        app.registerEventForDatePickerFields();
    },
    removeResources: function (){
        var thisInstance = this;
        jQuery(".employees_chzn,.vehicles_chzn,.vendorchzn").chosen().change(function(evt){
            var resources = $(this).val();
            var resourceType = jQuery(this).data("resource_type");
            var task_id = [ jQuery(this).data("orderstaskid") ];
            var lead_employee_id = $(this).find('.lead_employee').val();
            var params = {
                'module': app.getModuleName(),
                'mode': 'taskResourceHandler',
                'view': 'NewLoadLocalDispatch',
                'task_id': task_id,
                'resource_type': resourceType,
                'resource_ids':resources,
                'lead_employee_id':lead_employee_id
            }
            var progressIndicatorElement = instance.showLoadingMessage('Updating Resources...');
            AppConnector.request(params).then(function (data) {
                var data = JSON.parse(data);
                instance.hideLoadingMessage(progressIndicatorElement);
                if (data.success && data.result.result == "OK") {
                    //instance.refreshView(); //NO need to refresh the view for this one.
                    thisInstance.changeFilter = false;
		    if(jQuery("input.select_task[data-id='"+task_id+"']").attr("checked")){
                        thisInstance.loadEmployees(task_id);
                        thisInstance.loadVehicles(task_id);
                        thisInstance.loadVendors(task_id);
                        thisInstance.initChznDragDrop('.employees_chzn');
                        thisInstance.initChznDragDrop('.vehicles_chzn');
                        thisInstance.initChznDragDrop('.vendorchzn');
                    }
                }else{
                    console.log(data.msg); 
                }
            });
        });
    },
    registerDispatchStatusChange: function(){
        jQuery(document).on("change",".dispatch_status",function(){
	    var dispatchstatushtmlelement = jQuery(this);
	    var prevvalue = jQuery(dispatchstatushtmlelement).data("prevvalue");
            var thisInstance = this;
            if(jQuery(thisInstance).closest('tr').find('.disp_assigneddate').length == 1){
                if(jQuery(thisInstance).closest('tr').find('.disp_assigneddate').val() == ''){
                    var params = {
                    title: app.vtranslate("JS_ERROR"),
                    text: app.vtranslate('JS_ERROR_ASSIGNED_DATE_NOT_PRESENT'),
                    animation: 'show',
                    type: "error"
                    };
                    Vtiger_Helper_Js.showPnotify(params);
                    jQuery(dispatchstatushtmlelement).val(prevvalue).trigger('liszt:updated');
                    return false;
                }
            }
            var dispatch_status = jQuery(this).find("option:selected").text();
            var task_id = jQuery(this).closest("tr").data("id");
            var params = {
                'module': app.getModuleName(),
                'mode': 'updateDispatchStatus',
                'view': 'NewLoadLocalDispatch',
                'task_id': task_id,
                'dispatch_status': dispatch_status,
                'dispatchAssignedDatePresent': (jQuery(thisInstance).closest('tr').find('.disp_assigneddate').length == 1 ? 1 : 0)
            }
            var progressIndicatorElement = instance.showLoadingMessage('Updating Dispatch Status...');
            AppConnector.request(params).then(function (data) {
                var data = JSON.parse(data); 
                instance.hideLoadingMessage(progressIndicatorElement);
                if (data.success && data.result.result == "OK") {
		    jQuery(dispatchstatushtmlelement).data("prevvalue", jQuery(dispatchstatushtmlelement).val());
                    var assignedDate = data.result.assignedDate;
                    if(assignedDate != ''){
                        jQuery(thisInstance).closest('tr').find('.disp_assigneddate').val(assignedDate);
                    }
                    var msg = "Dispatch status updated.";
                    var type = "info";
                    var title = "JS_OK";
                }else{
                    var msg = "ERROR: dispatch status could not be updated. Try again.";
                    var type = "error"; 
                    var title = "JS_ERROR";
		    
		    jQuery(dispatchstatushtmlelement).val(prevvalue).trigger('liszt:updated');
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
    auxSetHours: function(current_hour,estimated_hours){
        var d = new Date(),
            s = current_hour,
            parts = s.match(/(\d+)\.(\d+) (\w+)/),
            hours = /am/i.test(parts[3]) ? parseInt(parts[1], 10) : parseInt(parts[1], 10) + 12,
            minutes = parseInt(parts[2], 10);

        d.setHours(hours+estimated_hours);
        d.setMinutes(minutes);
        
        return d;
    },
    registerHoursChange: function(){
        var thisInstance = this;
        jQuery(document).on("change",".disp_actualend, .disp_assignedstart",function(){
            var thisCheckbox = jQuery(this).closest('tr').find('.select_task');
            var task_id = jQuery(this).closest("tr").data("id");
            var endDate = 0; var startDate = 0;
            if(jQuery(this).closest("tr").find(".disp_actualend").length){
                var actual_end = jQuery(this).closest("tr").find(".disp_actualend").val().replace(":",".");
                endDate = instance.auxSetHours(actual_end,0);
            }else{
                actual_end = 'NaN';
            }

            if(jQuery(this).closest("tr").find(".disp_assignedstart").length){
                var actual_start = jQuery(this).closest("tr").find(".disp_assignedstart").val().replace(":",".");
                startDate = instance.auxSetHours(actual_start,0);
            }
            
            
            if(endDate !=0 && startDate !=0 && endDate < startDate){ //if this happens then i notify and calculate end date by start date + estimated hours
                var params = {
                    title: app.vtranslate("JS_ERROR"),
                    text: app.vtranslate("End date cannot be greater than Start date. End date will be automatically calculated."),
                    animation: 'show',
                    type: "error",
                };
                Vtiger_Helper_Js.showPnotify(params);
                actual_end = 'NaN';
                var datesError = true;
            }

            var actualEndInput = jQuery(this).closest("tr").find(".disp_actualend");


            var params = {
                'module': app.getModuleName(),
                'mode': 'updateTimes',
                'view': 'NewLoadLocalDispatch',
                'task_id': task_id,
                'disp_actualstart': actual_start,
                'disp_actualend': actual_end,
            }
            var progressIndicatorElement = instance.showLoadingMessage('Updating Actual Times...');
            AppConnector.request(params).then(function (data) {
                var data = JSON.parse(data); 
                instance.hideLoadingMessage(progressIndicatorElement);
                if (data.success && data.result.result !== "OK") {
                    var msg = "ERROR: start/end hours could not be updated.";
                    var type = "error";
                    var title = "JS_ERROR";
                    
                    var params = {
                        title: app.vtranslate(title),
                        text: app.vtranslate(msg),
                        animation: 'show',
                        type: type
                    };
                    
                    Vtiger_Helper_Js.showPnotify(params);
                } else {
                    if(datesError){
                        var actual_end = instance.formatAMPM(instance.auxSetHours(data.result.end_date,0));
                        actualEndInput.val(actual_end);
                        
                    }

                    thisCheckbox.trigger('change');
                }
            });
        });
    },
    formatAMPM: function(date) {
        var hours = date.getHours();
        var minutes = date.getMinutes();
        var ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12; // the hour '0' should be '12'
        minutes = minutes < 10 ? '0'+minutes : minutes;
        var strTime = hours + ':' + minutes + ' ' + ampm;
        
        return strTime;
    },
    registerRowClickEvent: function () {
        var listViewContentDiv = this.getListViewContentContainer();
        listViewContentDiv.on('click', '.listViewEntries', function (e) {
            return;
        });
    },
    loadFilterView : function(url,aux) {
        if(url == null){
            var url = jQuery('#createFilter').data('createurl') + '&sourceView=LocalDispatch';
        }

        var thisInstance = this;
		var progressIndicatorElement = jQuery.progressIndicator();
		AppConnector.request(url).then(
			function(data){
				app.hideModalWindow();
				var contents = jQuery(".contentsDiv").html(data);
				progressIndicatorElement.progressIndicator({'mode' : 'hide'});
				Vtiger_CustomView_Js.registerEvents();
				Vtiger_CustomView_Js.advanceFilterInstance = Vtiger_AdvanceFilter_Js.getInstance(jQuery('.filterContainer',contents));
                                
                                jQuery('#CustomView').append('<input type="hidden" name="source_module_view" value="NewLocalDispatch"/>');
				var view = "NewLocalDispatch";
				if(aux){
				    jQuery('#CustomView').append('<input type="hidden" name="rightTable" value="'+aux+'"/>');
				    view += aux;
				    jQuery(".conditionsFromHere").hide();
				}
                jQuery('#CustomView').append('<input type="hidden" name="sourceModuleView" value="'+view+'"/>');
                if(view == 'NewLocalDispatch'){
                    jQuery('#div-slider').removeClass('hide');
                    jQuery('#div-collapsed').removeClass('hide');
                }
			},
			function(error,err){

			}
		);
    },
    hideShowResourceTab: function(){
        if(jQuery('#customViewResourceHidden').val() == 'yes' && !jQuery('#ctButtonImage').hasClass('icon-chevron-left')){
            jQuery('#ctButtonImage').trigger('click');
        }else if(jQuery('#customViewResourceHidden').val() == 'no' && jQuery('#ctButtonImage').hasClass('icon-chevron-left')){
            jQuery('#ctButtonImage').trigger('click');
        }
    },
    addSplitter: function(){
        var thisInstance = this;
        if(!jQuery('#ctButtonImage').hasClass('icon-chevron-left')){
            var position = thisInstance.getSplitterPosition();
            
            if(!position || position == ''){
               position = '75.00%';
            }
        }else{
            var position = '99%';
        }
        
        if(localStorage.getItem('hiddenSplitterLimit') === null){
            var limitWidth = window.innerWidth * .1;
            localStorage.setItem('hiddenSplitterLimit', limitWidth);
        }else{
            var limitWidth = localStorage.getItem('hiddenSplitterLimit');
        }
        
        if(!jQuery('#ctButtonImage').hasClass('icon-chevron-left') && (parseFloat(position) * window.innerWidth / 100) > (window.innerWidth - limitWidth)){
            position = "90%";
        }
		
        var params = {
            orientation: 'vertical',
            limit: limitWidth,
            position: position, // if there is no percentage it interpret it as pixels
            onDrag: function(event) {
                thisInstance.fixHeights();
                localStorage.setItem('hiddenSplitterPosition', ((thisInstance.splitter_obj.position() / jQuery(window).width()) * 100)+"%");
            }
        }

        if(jQuery('#ctButtonImage').hasClass('icon-chevron-left')){
            params.invisible = true;
        }

        thisInstance.splitter_obj = jQuery('.parasplitter').height(jQuery(window).height() - jQuery(".navbar-fixed-top").height() -22).split(params);
        
    },
    getSplitterPosition: function(){

            filterSplitterPos = jQuery('#customViewSplitterPosition').val();

            if(!filterSplitterPos || filterSplitterPos == ''){
                filterSplitterPos = localStorage.getItem('hiddenSplitterPosition');
            }else{
                filterSplitterPos = parseFloat(Math.round((100 - filterSplitterPos) * 100) / 100).toFixed(2) + "%";
            }

            return filterSplitterPos;
    },
    registerOpenCloseSplitter: function(){
        var thisInstance = this;
        var new_position = "";
        jQuery(document).on("click","#ctButtonImage",function(){
            var action = (jQuery(this).hasClass("icon-chevron-right")) ? "close" : "open";
            if(action == "close"){
                jQuery(".accordion_ld.borderless, .resourceTableTitleDiv").hide();
                jQuery(this).removeClass("icon-chevron-right").addClass("icon-chevron-left");
                thisInstance.splitter_obj.position('98.00%');
                jQuery('#rightPane').width('1px;');
                thisInstance.splitter_obj.destroy();
                
            }else{
                thisInstance.splitter_obj.destroy();
                jQuery(this).removeClass("icon-chevron-left").addClass("icon-chevron-right");
                thisInstance.addSplitter();
                thisInstance.fixHeights();
                jQuery(".accordion_ld.borderless, .resourceTableTitleDiv").show();
                
            }
        });
    },
    registerLeadRoleChange: function(){
        var thisInstance = this;
        jQuery(document).on("change",".chznprole,.lead_resource",function(){
            var primaryRole = jQuery(this).closest("tr").find(".chznprole option:selected").val();
            var employeeId = jQuery(this).closest("tr").prop("id");
            var task_id = jQuery('.select_task:checkbox:checked').data("id");
            var selectedLead = (jQuery(this).closest("tr").find(".lead_resource").prop("checked")) ? 1 : 0;
                    
            var params = {
                'module': app.getModuleName(),
                'mode': 'primaryRoleNLeadUpdate',
                'view': 'NewLoadLocalDispatch',
                'task_id': task_id,
                'primaryRole': primaryRole,
                'employeeId':employeeId,
                'selectedLead':selectedLead,
            }
            var progressIndicatorElement = instance.showLoadingMessage('Updating Primary Role...');
            AppConnector.request(params).then(function (data) {
                var data = JSON.parse(data);
                instance.hideLoadingMessage(progressIndicatorElement);
                if (data.success && data.result.result == "OK") {
                    //instance.refreshView(); //NO need to refresh the view for this one.
                    if(selectedLead){
                        var oldLeadEmployeeId = jQuery('select.employees_chzn[data-orderstaskid="' + task_id + '"]').next().find('li.lead_employee').removeClass('lead_employee').prop('id');
                        if(oldLeadEmployeeId){
                            var newId = oldLeadEmployeeId.replace("_o_", "_c_");
                            jQuery('#' + newId).css('font-weight', 'normal');
                        }
                        jQuery('select.employees_chzn[data-orderstaskid="' + task_id + '"]').next().find('li.employee_' + employeeId).addClass('lead_employee');
                        thisInstance.registerMakeBoldLeadCrew();
                    }
                }else{
                    console.log(data.msg); 
                }
            });
        });   
    },
    registerModalEvent: function(){
        jQuery(document).on("click",".calldata",function(){
            var task_id = jQuery(this).data("id");
            var otherParams = {'module': 'OrdersTask','view': 'NewLoadLocalDispatch','mode': 'checkForShowCallModal','task_id': task_id};
            AppConnector.request(otherParams).then(
                function (data) {
                    var data = JSON.parse(data);
                    if (data.success && data.result == "OK") {
                        params = {
                            'module': 'OrdersTask',
                            'view': 'NewLoadLocalDispatch',
                            'mode': 'showCallModal',
                            'task_id': task_id,
                        }

                        AppConnector.request(params).then(
                            function (data) {
                                app.showModalWindow(data, function (data) {
                                });
                        });
                    }else{
                        var params = {
                            title: app.vtranslate("Error!"),
                            text: app.vtranslate(data.result),
                            animation: 'show',
                            type: "error"
                        };
                        Vtiger_Helper_Js.showPnotify(params);
                    }
            });
        });
        jQuery(document).on("click","#saveCheckCall",function(){
            var task_id = jQuery("#check_call").data("orderstaskid");
            var check_call = jQuery("#check_call option:selected").text();
            
            var params = {
                'module': app.getModuleName(),
                'mode': 'updateCheckCallField',
                'view': 'NewLoadLocalDispatch',
                'task_id': task_id,
                'check_call': check_call,
            }
            var progressIndicatorElement = instance.showLoadingMessage('Updating Check Call Field...');
            AppConnector.request(params).then(function (data) {
                var data = JSON.parse(data);
                instance.hideLoadingMessage(progressIndicatorElement);
                if (data.success && data.result.result == "OK") {
                    app.hideModalWindow();
                }else{
                    console.log(data.msg); 
                }
            });
        });
    },
    createJobTasks: function(){
        jQuery(document).on('click', '#quickCreateTasks', function (e) {
            var postQuickCreateSave = function (data) {
                instance.refreshView();
            };
            var quickCreateParams = {};
            quickCreateParams['noCache'] = true;
            quickCreateParams['callbackFunction'] = postQuickCreateSave;
            var progress = jQuery.progressIndicator();
            var headerInstance = new Vtiger_Header_Js();
            headerInstance.getQuickCreateForm('index.php?module=OrdersTask&view=QuickCreateAjax', 'OrdersTask', quickCreateParams).then(function (data) {
                progress.progressIndicator({'mode': 'hide'});
                headerInstance.handleQuickCreateData(data, quickCreateParams);
                jQuery('[name="orderstasktype"] option[value="operative"]').prop("selected",true).trigger('liszt:updated');
            });
        });
    },
    registerDateListSearch: function (container) {
        container.find('.dateField').each(function (index, element) {
            var dateElement = jQuery(element);

            if (dateElement.hasClass('notMultipleCalendar')) {
                var customParams = {}
            } else {
                var customParams = {
                    calendars: 3,
                    mode: 'range',
                    className: 'rangeCalendar',
                    onChange: function (formated) {
                        dateElement.val(formated.join(','));
                    }
                }
            }
            app.registerEventForDatePickerFields(dateElement, false, customParams);
        });

    },
    //Changes in assigned Date
    registerNoMultipleCalendarChange: function(){
        var thisInstance = this;
        jQuery(document).on('click','.notMultipleCalendar',function(){
            var val = jQuery(this).val();
            jQuery(this).attr('data-prevdate',val);
        });
        jQuery(document).on('change','.notMultipleCalendar',function(){
            var element = jQuery(this);
            var thisCheckbox = jQuery(this).closest('tr').find('.select_task');
            var task_id = jQuery(this).closest('tr').data('id');
            var assigned_date = jQuery(this).val();
            //OT5799 OT5804
            var isChecked = jQuery(thisCheckbox).is(':checked');
            var doUpdateAssignedDate = false;
            var doRefreshTaskTable = false;
            var doRefreshResourceTables = false;
            var isOutsideFilterRange = thisInstance.isAssignedDateOutsideFilterRange(assigned_date);
            var hasResourcesAssigned = thisInstance.hasResourcesAssigned(task_id);
            
            if(hasResourcesAssigned){
                //check if resources assigned are available on the new date.
                //if available do nothing and update the date
                //if not availabe show which resources are not available and show prompt
                var params = {
                    'module': app.getModuleName(),
                    'mode': 'checkResourcesAvailabilityForDate',
                    'action': 'ActionAjax',
                    'task_id': task_id,
                    'assigned_date': assigned_date
                };

                var string = 'false';
                var progressIndicatorElement = thisInstance.showLoadingMessage('Checking Resouces Availability for New Date...');
                AppConnector.request(params).then(function (data) {
                    thisInstance.hideLoadingMessage(progressIndicatorElement);
                    if(data.success) {
                        if(data.result.allAvailable === true){//all resources are available for the new date, update
                            doRefreshResourceTables = true;
                            if(isChecked && isOutsideFilterRange){
                                //@TODO: Need to un select the task and reload the resources table.
                                thisCheckbox.prop('checked', false);
                                doUpdateAssignedDate = doRefreshTaskTable = doRefreshResourceTables = true;
                            }
                            
                            thisInstance.updateAssignedDate(task_id, assigned_date, doRefreshTaskTable, doRefreshResourceTables);
                        }else{//if != true, has the msg to show
                            //show confirm alert
                            var message = data.result.msg;
                            Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
                                function(e) {
                                    //update date and remove resources
                                    doRefreshTaskTable = doRefreshResourceTables = true;
                                    thisInstance.removeUnavailableTaskResources(task_id, data.result.ids, assigned_date, doRefreshTaskTable, doRefreshResourceTables);
                                    
                                },
                                function(error, err) {
                                    //do not update date and keep resources
                                    thisInstance.returnToPrevValue(element);
                                }
                            );
                        }
                    }else{
                        console.log(data.msg); 
                    }
                });
            }else if(!isChecked && isOutsideFilterRange){
                doUpdateAssignedDate = doRefreshTaskTable = true;
                doRefreshResourceTables = false;
            }else if(!isChecked && !isOutsideFilterRange){
                doUpdateAssignedDate = true;
                doRefreshResourceTables = false;
            }else if(isChecked && !isOutsideFilterRange && !hasResourcesAssigned){
                doUpdateAssignedDate = doRefreshResourceTables = true;
            }else if(isChecked && isOutsideFilterRange){
                //@TODO: Need to un select the task and reload the resources table.
                thisCheckbox.prop('checked', false);
                doUpdateAssignedDate = doRefreshTaskTable = doRefreshResourceTables = true;
            }
            if(!doUpdateAssignedDate){
                return false;
            }else{
                thisInstance.updateAssignedDate(task_id, assigned_date, doRefreshTaskTable, doRefreshResourceTables);
            }
        });
    },
    updateAssignedDate:function(task_id, assigned_date, doRefreshTaskTable, doRefreshResourceTables){
        var thisInstance = this;
        var params = {
            'module': app.getModuleName(),
            'mode': 'updateAssignedDate',
            'view': 'NewLoadLocalDispatch',
            'task_id': task_id
        };

        if(assigned_date == ''){
            params.assigned_date = '';
        }else{
            params.assigned_date = thisInstance.getDBformattedDate(new Date(Date.parse(assigned_date)));
        }


        var progressIndicatorElement = thisInstance.showLoadingMessage('Updating Task Assigned Date...');
        AppConnector.request(params).then(function (data) {
            var data = JSON.parse(data);
            thisInstance.hideLoadingMessage(progressIndicatorElement);
            if (data.success && data.result.result === "OK") {
                //OT5799 OT5804
                if(doRefreshTaskTable){  
                    thisInstance.refreshView(doRefreshResourceTables);
                }else if(doRefreshResourceTables){
                    //hack to re load resources tables
                    if(jQuery('.listViewEntriesTable input.select_task[type="checkbox"]:checked').length > 0){
                        jQuery('.listViewEntriesTable input.select_task[type="checkbox"]:checked:first').change();
                    }
                }
            }else{
                console.log(data.msg); 
            }
        });
    },
    registerEmployeeSearch: function(){
        instance = this;
        jQuery(document).on("click","#searchEmployee",function(){
            //reset all table rows to visible before the new search
            jQuery(".employees-tables table tbody tr.ajaxSearched").remove();
            jQuery.each(jQuery(".employees-tables table tbody tr"), function(){
                jQuery(this).removeClass("hide");
            });
            jQuery(".employees-tables table tbody tr.erase-me").remove();
            if(jQuery('[name="employee-pagging"]').val() == "no"){
                jQuery.each(jQuery(".employees-tables table tbody tr"), function() {
		    var row = $(this);
		    var flag = true;
		    var index = 3; //The first 3 columns are not searchable (assigned)
		    if(jQuery("#ahiddensmownerid").length > 0) //it works with +1, i think because ahiddensmownerid hidden field is counting as child
			index++;
		    var qty = jQuery(".employees-tables .listSearchContributor").length;
		    var auxQty = 0;
		    jQuery(".employees-tables .listSearchContributor").each(function(){
			index++;
			var valor = $(this).val().toLowerCase();
			if(valor == ""){
			    auxQty++;
			}else if(row.find("td:nth-child("+index+")").text().trim().toLowerCase().indexOf(valor) < 0){
			    auxQty = 0;
			    flag = false;
			}
		    });
		    if(flag || (qty == auxQty)) //If a filter worked well or if they are all empty then show
			jQuery(this).show();
                    else
			jQuery(this).hide();
                });
            }else{
		var arrPar = {};
		jQuery(".employees-tables .listSearchContributor").each(function(){
		    if($(this).data("fieldname") == "smownerid"){
			var valor = $(this).parent("table").find("#ahiddensmownerid").val();
		    }else{
			var valor = $(this).val();
		    }
		    var fieldName = $(this).data("fieldname");
		    arrPar[fieldName] = valor;
		});
                var progressIndicatorElementEmployees = instance.showLoadingMessage('LBL_LOADING_RESOURCES');
                window.progressIndicatorElementEmployees = progressIndicatorElementEmployees;

                var params = {
                    'module': "OrdersTask",
                    'mode': 'getEmployeeTable',
                    'view': 'NewLoadLocalDispatch',
                    'arrPar': JSON.stringify(arrPar),
                    'task_id': jQuery(".select_task:checked").val(),
                }

                AppConnector.request(params).then(function (data) {
                    var data = JSON.parse(data);
                    jQuery(".employees-tables table tbody tr.firstpagging").addClass("hide");
                    if(data.success && data.result !== ""){
                        jQuery(".employees-tables table tbody").append(data.result);
                        //jQuery(".chznprole").chosen();
                        var progressIndicatorElementEmployees = window.progressIndicatorElementEmployees;
                        instance.hideLoadingMessage(progressIndicatorElementEmployees);
                    }else{
                        var params = { title: app.vtranslate("Error!"), text: "No record match the search criteria", animation: 'show', type: "error" };
                        Vtiger_Helper_Js.showPnotify(params);
			jQuery(".employees-tables .listSearchContributor").each(function(){
			    $(this).val("");
			});
                        jQuery(".employees-tables table tbody tr.firstpagging").removeClass("hide");

                         var progressIndicatorElementEmployees = window.progressIndicatorElementEmployees;
                        instance.hideLoadingMessage(progressIndicatorElementEmployees);
                    }
                });
            }
            if(jQuery(".employees-tables table tbody tr:visible").length < 1){
		var numcols = jQuery(".employees-tables table thead tr.listViewHeaders th").length;
                jQuery(".employees-tables table tbody tr.employees:last").after('<tr class="erase-me"><td colspan="'+numcols+'" style="font-size: 1.15em;">No drivers found, try another search.</td></tr>');
            }

        });
    },
    registerVehicleSearch: function(){
        jQuery(document).on("click","#SearchVehicle",function(){
            var progressIndicatorElement = instance.showLoadingMessage('Searching vehicles...');
            //reset all table rows to visible before the new search
            jQuery.each(jQuery(".vehicles-tables table tbody tr"), function(){
                jQuery(this).show();
            });
            jQuery(".vehicles-tables table tbody tr.erase-me").remove();
            jQuery.each(jQuery(".vehicles-tables table tbody tr"), function() {
		var row = $(this);
		var flag = true;
		var index = 1; //The first column is not searchable (assigned)
		jQuery(".vehicles-tables .listSearchContributor").each(function(){
		    index++;
		    var valor = $(this).val().toLowerCase();
		    if(valor !== "" && row.find("td:nth-child("+index+")").text().trim().toLowerCase().indexOf(valor) == -1)
			flag = false;
		});
		if(flag)
                    jQuery(this).show();
                else
                   jQuery(this).hide();                
            });
            if(jQuery(".vehicles-tables table tbody tr:visible").length < 1){
		var numcols = jQuery(".vehicles-tables table thead tr.listViewHeaders th").length;
                jQuery(".vehicles-tables table tbody tr.vehicle:last").after('<tr class="erase-me"><td colspan="'+numcols+'" style="font-size: 1.15em;">No vehicles found, try another search.</td></tr>');
            }
            instance.hideLoadingMessage(progressIndicatorElement);
        });  
    },
    registerVendorSearch: function(){
        jQuery(document).on("click","#SearchVendor",function(){
            var progressIndicatorElement = instance.showLoadingMessage('Searching vendors...');
            //reset all table rows to visible before the new search
            jQuery.each(jQuery(".vendors-tables table tbody tr"), function(){
                jQuery(this).show();
            });
            jQuery(".vendors-tables table tbody tr.erase-me").remove();
            jQuery.each(jQuery(".vendors-tables table tbody tr"), function() {
		var row = $(this);
		var flag = true;
		var index = 1; //The first column is not searchable (assigned)
		jQuery(".vendors-tables .listSearchContributor").each(function(){
		    index++;
		    var valor = $(this).val().toLowerCase();
		    if(valor !== "" && row.find("td:nth-child("+index+")").text().trim().toLowerCase().indexOf(valor) == -1)
			flag = false;
		});
		if(flag)
                    jQuery(this).show();
                else
                   jQuery(this).hide();                
            });
            if(jQuery(".vendors-tables table tbody tr:visible").length < 1){
		var numcols = jQuery(".vendors-tables table thead tr.listViewHeaders th").length;
                jQuery(".vendors-tables table tbody tr.vendor:last").after('<tr class="erase-me"><td colspan="'+numcols+'" style="font-size: 1.15em;">No vendors found, try another search.</td></tr>');
            }
            instance.hideLoadingMessage(progressIndicatorElement);
        });  
    },
    registerFilterSelect: function(){
        var thisInstance = this;
        $(document).on("change", ".ldFilterSelect", function(){
            var aux = $(this).closest(".accordion-head").find("button.accordion").text().trim();
            var relModule = (aux == "Crew") ? "Employees" : (aux == "Vehicles") ? "Vehicles" : "Vendors";  

            thisInstance.changeFilter = true;
            var task_id = jQuery('.select_task:checkbox:checked').data("id");
            if(relModule== "Employees"){
            thisInstance.loadEmployees(task_id);
            thisInstance.initChznDragDrop('.employees_chzn');
            }else if(relModule == "Vehicles"){
            thisInstance.loadVehicles(task_id);
            thisInstance.initChznDragDrop('.vehicles_chzn');
            }else if(relModule == "Vendors"){
            thisInstance.loadVendors(task_id);
            thisInstance.initChznDragDrop('.vendorchzn');
            }
            console.log(relModule);
        });
    },
    registerAccordionEvents : function(){
    thisInstance = this;
	jQuery(document).on("click",".accordion-toggle",function(e){
	    if(jQuery(this).data("firstclick") !== "true"){
                var open = (jQuery(this).data("open")) ? false : true;
                jQuery(this).data("open", open);

                
	    }
	});
    },
    registerLDEditFilterClickEvent: function (currClass) {
        var thisInstance = this;

        var filterSelect = jQuery("." + currClass + " .chzn-select-ld.ldFilterSelect").data('select2');

        if (filterSelect != undefined && filterSelect != null) {
            var block = filterSelect.dropdown;

            jQuery(block).on('mouseup', 'li i.editFilter', function (event) {
                var toEdit = jQuery("." + currClass).find("select.ldFilterSelect option").filter(function () {
                    return jQuery(this).html() == jQuery(event.target).closest('li').text();
                }).val();
                jQuery("." + currClass + " .chzn-select-ld.ldFilterSelect").data('select2').close();//to close the dropdown
                event.stopPropagation();

                var aux = (currClass == "resources_crew") ? "Crew" : ((currClass == "resources_equipment") ? "Equipment" : "Vendors");

                var editUrl = "index.php?module=CustomView&view=EditAjax&source_module=OrdersTask&sourceView=LocalDispatch&record=" + toEdit;
                thisInstance.loadFilterView(editUrl, aux);
            });
        }

    },
    registerLDDeleteFilterClickEvent: function (currClass) {

        var filterSelect = jQuery("." + currClass + " .chzn-select-ld.ldFilterSelect").data('select2');

        if (filterSelect != undefined && filterSelect != null) {
            var block = filterSelect.dropdown;
            jQuery(block).on('mouseup', 'li i.deleteFilter', function (event) {
                var toDelete = jQuery("." + currClass).find("select.ldFilterSelect option").filter(function () {
                    return jQuery(this).html() == jQuery(event.target).closest('li').text();
                }).val();
                jQuery("." + currClass + " .chzn-select-ld.ldFilterSelect").data('select2').close();//to close the dropdown
                event.stopPropagation();

                var params = {
                    'module': "OrdersTask",
                    'mode': 'deleteCustomFilterByID',
                    'view': 'NewLoadLocalDispatch',
                    'toDelete': toDelete,
                }

                AppConnector.request(params).then(function (data) {
                    var data = JSON.parse(data);
                    if (data.success && data.result == "Ok") {
                        var msgparams = {
                            title: app.vtranslate('Info'),
                            text: app.vtranslate('Filter deleted!'),
                            animation: 'show',
                            type: 'info'
                        };
                        Vtiger_Helper_Js.showPnotify(msgparams);
                        jQuery(".select_task").change();
                    } else {
                        var msgparams = {
                            title: app.vtranslate('Info'),
                            text: app.vtranslate('An error occurred trying to delete the filter!'),
                            animation: 'show',
                            type: 'error'
                        };
                        Vtiger_Helper_Js.showPnotify(msgparams);
                    }

                });
            });
        }
    },
    registerFilterOptionsHoverEvent: function (currClass) {

        var filterSelect = jQuery("." + currClass + " .chzn-select-ld.ldFilterSelect").data('select2');

        if (filterSelect != undefined && filterSelect != null) {
            var block = filterSelect.dropdown;
            jQuery(block).on('hover', 'li.select2-result-selectable', function (event) {
                var liElement = jQuery(event.currentTarget);
                var liFilterImages = liElement.find(".ldfilterActionImgs");

                if (event.type === 'mouseenter' && jQuery(liElement).text() != "Create Filter") {
                    if (liFilterImages.length > 0) {
                        liFilterImages.show();
                    } else {
                        var table = (currClass == "resources_crew") ? "employees-tables" : ((currClass == "resources_equipment") ? "vehicles-tables" : "vendors-tables");

                        jQuery(document).find(".ldfilterActionImages").first().clone(true, true).removeClass('ldfilterActionImages').addClass('ldfilterActionImgs').appendTo(liElement.find('.select2-result-label')).show();
                        if (jQuery.trim(jQuery(liElement).text()) == jQuery.trim(jQuery("." + table + " select.ldFilterSelect option[data-isdefault='true']").text())) {
                            liElement.find('.deleteFilter').remove();
                        }
                    }
                } else {
                    liFilterImages.hide();
                }
            });
        }
    },
    registerMakeBoldLeadCrew : function(){
        jQuery('li.lead_employee').each(function(){
            var id = jQuery(this).prop("id");
            var newId = id.replace("_o_", "_c_");
           
            jQuery('#' + newId).parent().find('.lead_employee').hide();

            if(jQuery('#' + newId + '.lead_employee').length == 0){
                jQuery('#' + newId).find('span').append(' <i style="margin-left:2%;" title="lead_employee" class="lead_employee icon-user alignMiddle"></i> ');
            }

            
        });
    },
    registerDisableEnableDateAndTime : function(taskId){    
        jQuery('input.disp_assignedstart:enabled').prop('disabled', true);
        jQuery('input.disp_actualend:enabled').prop('disabled', true);
        if(taskId != null && taskId != ''){
            var tr = jQuery('tr[data-id="' + taskId + '"]');
            tr.find('input.disp_assignedstart').prop('disabled', false);
            tr.find('input.disp_actualend').prop('disabled', false);
        }

    },
    registerAddCreateFilterEvent: function(){
    //Crew
        jQuery(".resources_crew").find('span.filterActionsDivCrewAppended').remove();
	var auxFilterActionsDivCrew = jQuery('span.filterActionsDivCrew').clone();
        jQuery(auxFilterActionsDivCrew).appendTo(jQuery(".resources_crew select.ldFilterSelect").select2().data('select2').dropdown).removeClass('hide').removeClass("filterActionsDivCrew").addClass("filterActionsDivCrewAppended");
    //Vehicles
	jQuery(".resources_equipment").find('span.filterActionsDivEquipmentAppended').remove();
	var auxFilterActionsDivEquipment = jQuery('span.filterActionsDivEquipment').clone();
        jQuery(auxFilterActionsDivEquipment).appendTo(jQuery(".resources_equipment select.ldFilterSelect").select2().data('select2').dropdown).removeClass('hide').removeClass("filterActionsDivEquipment").addClass("filterActionsDivEquipmentAppended");
    //Vendors  
	jQuery(".resources_vendors").find('span.filterActionsDivVendorsAppended').remove();
        if(jQuery("#hide_vendors").val() != "yes"){
	    var auxFilterActionsDivVendors = jQuery('span.filterActionsDivVendors').clone();
            jQuery(auxFilterActionsDivVendors).appendTo(jQuery(".resources_vendors select.ldFilterSelect").select2().data('select2').dropdown).removeClass('hide').removeClass("filterActionsDivVendors").addClass("filterActionsDivVendorsAppended");
	}
    },
    registerRightPannelDynamicHeight: function(){
        var instance = this;
        var acc = document.getElementsByClassName("accordion");
        var i;

        for (i = 0; i < acc.length; i++) {
            acc[i].onclick = function () {
                
                var btnId = this.id;
                jQuery('#' + btnId).toggleClass('active');

                var panel = jQuery('#' + btnId).parent().parent().find('.panel')[0];
                if (panel.style.display === "block") {
                    panel.style.display = "none";
                } else {
                    panel.style.display = "block";
                    panel.style.height = '100px';
                }

                instance.updateAccordionsHeight();
                
            };
        }
    },
    updateAccordionsHeight: function(){
        var openTabs = document.querySelectorAll('.accordion.active').length;
        var containerHeight = jQuery('.accordion_ld').height();
        var headersHeight = jQuery('.accordion-head').outerHeight(true) * jQuery('.accordion-head').length;
        var splitHeight = ((containerHeight - headersHeight) / openTabs) - 3;

        var accGroups = document.getElementsByClassName("accordion-group");

        var accordionInfo = {};
        var extraSpace = 0;
        var extraSpaceCount = 0;

        jQuery('.accordion-group').each(function () {
            jQuery(this).find('.panel').outerHeight(splitHeight);
            if (jQuery(this).find('.accordion').hasClass('active')) {

                if (typeof(Storage) !== "undefined") {
                    localStorage.setItem(jQuery(this).attr('id'), "active");
                }

                if(jQuery(this).hasClass('resources_map')){
                    var calculateHeight = 360;
                }else{
                    var calculateHeight = (jQuery(this).find('tr').outerHeight(true) * jQuery(this).find('tr').length) + 15;
                }

                accordionInfo[jQuery(this).attr('id')] = {};
                accordionInfo[jQuery(this).attr('id')]['calculateHeight'] = calculateHeight;
                if (calculateHeight > splitHeight) {
                    accordionInfo[jQuery(this).attr('id')]['needSpace'] = calculateHeight - splitHeight;
                    extraSpaceCount = extraSpaceCount + 1
                } else {
                    accordionInfo[jQuery(this).attr('id')]['needSpace'] = 0;
                    extraSpace = extraSpace + (splitHeight - calculateHeight);
                }
            }else{
                if (typeof(Storage) !== "undefined") {
                    localStorage.setItem(jQuery(this).attr('id'), "inactive");
                }
            }

        });

        if (extraSpace) {
            if(extraSpaceCount == 0){
                var toSplit = extraSpace / openTabs;
            }else{
                var toSplit = extraSpace / extraSpaceCount;
            }

            extraSpace = 0;
        

            jQuery.each(accordionInfo, function (accordionId, accordionData) {

                if(extraSpaceCount == 0){
                    accordionInfo[accordionId]['splitHeight'] = splitHeight;
                }else{
                    if (accordionData.needSpace) {
                        if (accordionData.calculateHeight > splitHeight + toSplit) {
                            accordionInfo[accordionId]['splitHeight'] = splitHeight + toSplit;
                            accordionInfo[accordionId]['needSpace'] = accordionData['calculateHeight'] - accordionInfo[accordionId]['splitHeight'];
                        } else {
                            extraSpace = extraSpace + (splitHeight - accordionInfo[accordionId]['calculateHeight']);
                            accordionInfo[accordionId]['splitHeight'] = accordionInfo[accordionId]['calculateHeight'];
                        }
                    } else {
                        accordionInfo[accordionId]['splitHeight'] = accordionInfo[accordionId]['calculateHeight'];
                    }
                } 
            });
        }

        if (extraSpace) {
            jQuery.each(accordionInfo, function (accordionId, accordionData) {
                if (accordionData.needSpace) {
                    if (accordionData.calculateHeight > splitHeight + toSplit) {
                        accordionInfo[accordionId]['splitHeight'] = splitHeight + toSplit;
                        accordionInfo[accordionId]['needSpace'] = accordionData['calculateHeight'] - accordionInfo[accordionId]['splitHeight'];
                    } else {
                        extraSpace = extraSpace + (splitHeight - accordionInfo[accordionId]['calculateHeight']);
                        accordionInfo[accordionId]['splitHeight'] = accordionInfo[accordionId]['calculateHeight'];
                    }
                } else {
                    accordionInfo[accordionId]['splitHeight'] = accordionInfo[accordionId]['calculateHeight'];
                }
            });
        }


        jQuery('.accordion-group').each(function () {
            if (jQuery(this).find('.accordion').hasClass('active')) {
                jQuery(this).find('.panel').outerHeight(accordionInfo[jQuery(this).attr('id')]['splitHeight']);
                if(jQuery(this).hasClass('resources_map')){
                    task_id = jQuery('.select_task:checkbox:checked').data("id");
                    instance.loadMap(task_id);
                }else{
                    var panelId = jQuery(this).find('.panel').attr('id');
                    document.getElementById(panelId).addEventListener("scroll",function(){
                        var translate = "translate(0,"+this.scrollTop+"px)";
                        this.querySelector("thead").style.transform = translate;
                     });
                }
            }
        });
    },
    registerAccordionStatus: function() {
        var instance = this;
        if (typeof(Storage) !== "undefined") {
            jQuery('.accordion-group').each(function () {
                if(localStorage.getItem(jQuery(this).attr('id')) ==  "active"){
                    jQuery(this).find('.accordion').toggleClass('active');
                    jQuery(this).find('.panel').show();                    
                }
            });
            instance.updateAccordionsHeight();
        }
    },
    registerExtraLeftMenuAction: function(){
        var thisInstance = this;
        jQuery(document).on("click","#toggleButton",function(){
            thisInstance.splitter_obj.destroy(); 
            thisInstance.addSplitter();         
            thisInstance.fixHeights();
           
        });
    },    
    fixHeights: function(addMargin = true){
        if(addMargin){
            jQuery('#rightPane').width(jQuery('#rightPane').width() + 35);
        }else{
            jQuery('#rightPane').width(jQuery('#rightPane').width());
            
        }
        jQuery('.accordion_ld').height(jQuery(window).height() - jQuery(".navbar-fixed-top").height()-jQuery(".resourceTableTitleDiv").outerHeight(true) - 40);
        jQuery('.listViewEntriesDiv ').height(jQuery('.bodyContents').height() - jQuery(".listViewTopMenuDiv").height());
    },
    updateUIonResize: function(){
        var thisInstance = this;
        jQuery(window).on('resize', function(){
	    var limitWidth = window.innerWidth * .1;
	    localStorage.setItem('hiddenSplitterLimit', limitWidth);
	    
            thisInstance.splitter_obj.destroy();
            thisInstance.addSplitter();
            thisInstance.fixHeights();
        });
    },
    //OT5799 OT5804
    isAssignedDateOutsideFilterRange: function(assigned_date){
        var d1 = new Date(jQuery('#filter_date_from').val().replace(/-/g, "/")); //safari
        var d2 = new Date(jQuery('#filter_date_to').val().replace(/-/g, "/")); //safari
        var newDate = new Date(assigned_date.replace(/-/g, "/")); //safari
        if(newDate < d1 || newDate > d2){
            return true;
        }else{
            return false;
        }
    },
    //OT5799 OT5804
    hasResourcesAssigned:function(task_id){
        var tr = jQuery('tr.listViewEntries:[data-id="' + task_id + '"]');
        if(tr.find('select.employees_chzn option:selected').length > 0){
            return true;
        }else if(tr.find('select.vehicles_chzn option:selected').length > 0){
            return true;
        }else if(tr.find('select.vendorchzn option:selected').length > 0){
            return true;
        }
        return false;
    },
    //OT5799 OT5804
    returnToPrevValue:function(elem){
        var prevval = jQuery(elem).data('prevdate');
        jQuery(elem).val(prevval);
    },
    //OT5799 OT5804
    removeUnavailableTaskResources:function(task_id,resourcesToRemove,assigned_date,doRefreshTaskTable, doRefreshResourceTables){
        var thisInstance = this;
        var params = {
            'module': app.getModuleName(),
            'mode': 'removeUnavailableTaskResources',
            'action': 'ActionAjax',
            'task_id': task_id,
            'resources': resourcesToRemove
        };

        var progressIndicatorElement = thisInstance.showLoadingMessage('Removing unavailable resources...');
        AppConnector.request(params).then(function (data) {
            thisInstance.hideLoadingMessage(progressIndicatorElement);
            if (data.success && data.result.result === "OK") {
                var msgparams = {
                    title: app.vtranslate('Info'),
                    text: app.vtranslate('Resources Updated!'),
                    animation: 'show',
                    type: 'info'
                };
                Vtiger_Helper_Js.showPnotify(msgparams);
            } else {
                var msgparams = {
                    title: app.vtranslate('Info'),
                    text: app.vtranslate('An error occurred trying to update the resources!'),
                    animation: 'show',
                    type: 'error'
                };
                Vtiger_Helper_Js.showPnotify(msgparams);
            }
            thisInstance.updateAssignedDate(task_id, assigned_date, doRefreshTaskTable, doRefreshResourceTables);
        });
    },
    /*
    * Function to register the click event for list view main check box.
    */
    registerMainCheckBoxClickEvent : function(){
            var listViewPageDiv = jQuery('div.listViewPageDiv');
            var thisInstance = this;
            listViewPageDiv.on('click','#listViewEntriesMainCheckBox',function(){
                    var selectedIds = thisInstance.readSelectedIds();
                    var excludedIds = thisInstance.readExcludedIds();
                    if(jQuery('#listViewEntriesMainCheckBox').is(":checked")){
//                            var recordCountObj = thisInstance.getRecordsCount();
//                            recordCountObj.then(function(data){
//                                    jQuery('#totalRecordsCount').text(data);
//                                    if(jQuery("#deSelectAllMsgDiv").css('display') == 'none'){
//                                            jQuery("#selectAllMsgDiv").show();
//                                    }
//                            });

                            jQuery('.listViewEntriesCheckBox').each( function(index,element) {
                                    jQuery(this).attr('checked', true).closest('tr').addClass('highlightBackgroundColor');
                                    if(selectedIds == 'all'){
                                            if((jQuery.inArray(jQuery(element).val(), excludedIds))!= -1){
                                                    excludedIds.splice(jQuery.inArray(jQuery(element).val(),excludedIds),1);
                                            }
                                    } else if((jQuery.inArray(jQuery(element).val(), selectedIds)) == -1){
                                            selectedIds.push(jQuery(element).val());
                                    }
                            });
                    }else{
//                            jQuery("#selectAllMsgDiv").hide();
                            jQuery('.listViewEntriesCheckBox').each( function(index,element) {
                                    jQuery(this).attr('checked', false).closest('tr').removeClass('highlightBackgroundColor');
                            if(selectedIds == 'all'){
                                    excludedIds.push(jQuery(element).val());
                                    selectedIds = 'all';
                            } else {
                                    selectedIds.splice( jQuery.inArray(jQuery(element).val(), selectedIds), 1 );
                            }
                            });
                    }
                    thisInstance.writeSelectedIds(selectedIds);
                    thisInstance.writeExcludedIds(excludedIds);
                    //hack to re load resources tables
                    jQuery('.listViewEntriesTable input.select_task[type="checkbox"]:first').change();
                    
               });
       },
    registerEvents: function () {
        this.addSplitter();
        this.fixHeights();
        this.registerOpenCloseSplitter();
        this.hideShowResourceTab(); 
        this._super();  
        this.registerVendorSearch();
        this.registerVehicleSearch();
        this.registerEmployeeSearch();
        this.registerDateFilterChange();
        this.initDatePicker();
        this.registerCheckboxSelection();
        this.registerManageResources();
        this.resetResourceTable();
        this.removeResources();
        this.registerDispatchStatusChange();
        this.registerHoursChange();
        this.registerLeadRoleChange();
        this.initChznDragDrop('.employees_chzn');
        this.initChznDragDrop('.vehicles_chzn');
        this.initChznDragDrop('.vendorchzn');
        this.registerModalEvent();
        this.createJobTasks();
        this.registerNoMultipleCalendarChange();
	    this.registerFilterSelect();
	    this.registerAccordionEvents();
	    this.registerMakeBoldLeadCrew();
        this.registerDisableEnableDateAndTime();
	    this.registerAddCreateFilterEvent();
        this.registerRightPannelDynamicHeight();
        this.registerAccordionStatus();
	    this.registerExtraLeftMenuAction();
        this.updateUIonResize();        
    }
});

jQuery(document).ready(function () {
    var instance = new OrdersTask_LocalDispatch_Js;
    instance.registerEvents();

    app.listenPostAjaxReady(function() {
	    var instance = new OrdersTask_LocalDispatch_Js;
        instance.registerDateFilterChange();
        instance.registerCheckboxSelection();
        instance.registerManageResources();
        instance.removeResources();
        instance.initChznDragDrop('.employees_chzn');
        instance.initChznDragDrop('.vehicles_chzn');
	    instance.initChznDragDrop('.vendorchzn');
        instance.registerLeadRoleChange();
        instance.registerMakeBoldLeadCrew();
        instance.registerDisableEnableDateAndTime();
        instance.registerAddCreateFilterEvent();
        instance.registerRightPannelDynamicHeight();
        instance.registerAccordionStatus();
        instance.registerHoursChange();
        if(!jQuery("#leftPanel").hasClass("hide"))
            jQuery(".toggleButton").click();
        });
});

