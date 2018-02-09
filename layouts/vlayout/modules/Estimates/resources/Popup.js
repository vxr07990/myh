/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
Vtiger_Popup_Js("Estimates_Popup_Js", {}, {
	moduleName: 'Estimates',
},{

	//holds the event name that child window need to trigger
	eventName : '',
	popupPageContentsContainer : false,
	sourceModule : false,
	sourceRecord : false,
	sourceField : false,
	multiSelect : false,
	relatedParentModule : false,
	relatedParentRecord : false,
	searchedModule : false,
	assigenedTo : false,
	loadDate : false,

	/**
	 * Function to get source module
	 */
	getSourceModule : function(){
		if(this.sourceModule == false){
			this.sourceModule = jQuery('#parentModule').val();
		}
		return this.sourceModule;
	},

	/**
	 * Function to get the AssignedTo
	 */
	getAssignedTo : function(){
		return jQuery('select[name="assigned_user_id"]').val();
	},
	getLoadDate : function() {
		return jQuery('#' + Estimates_Popup_Js.I().moduleName + '_editView_fieldName_load_date').val();
	},

	show : function(urlOrParams, cb, windowName, eventName, onLoadCb){
		//Only open a new popup if one is not currently in the process of being opened
		var containerApp;
		if(this.getPopupStatus()){
			//clear out the extra things that break the popup on the second loading
			if(typeof urlOrParams == 'object'){
				if(urlOrParams['src_field'] != 'contract'){
					var removeThese = ['related_parent_id','related_parent_module','loadDate'];
					for(var key in removeThese){
						delete urlOrParams[removeThese[key]];
					}
				}
			}
			//Let the application know we are currenlty opening a popup
			jQuery('#popupStatus').val(0);
			
			if(typeof urlOrParams == 'undefined'){
				urlOrParams = {};
			}
			if (typeof urlOrParams == 'object' && (typeof urlOrParams['view'] == "undefined")) {
				urlOrParams['view'] = 'Popup';
			}
			
			// Target eventName to be trigger post data selection.
			if(typeof eventName == 'undefined') {
				eventName = 'postSelection'+ Math.floor(Math.random() * 10000);
			}
			if(typeof windowName == 'undefined' ){
				windowName = 'test';
			}
			if (typeof urlOrParams == 'object') {
				urlOrParams['triggerEventName'] = eventName;
			} else {
				urlOrParams += '&triggerEventName=' + eventName;
			}
			//console.dir(jQuery('select[name="assigned_user_id"]').val());
			if (typeof urlOrParams == 'object') {
				urlOrParams['assignedTo'] = jQuery('select[name="assigned_user_id"]').val();
			} else {
				urlOrParams += '&assignedTo=' + jQuery('select[name="assigned_user_id"]').val();
			}
			//added account ID so that contracts can be limited
			if (typeof urlOrParams == 'object') {
				urlOrParams['accountId'] = jQuery('input:hidden[name="account_id"]').val();
			} else {
				urlOrParams += '&accountId=' + jQuery('input:hidden[name="account_id"]').val();
			}
			//added business line so that contracts can be limited
			if (typeof urlOrParams == 'object') {
				urlOrParams['businessLine'] = jQuery('select[name="business_line_est"] > option:selected').val();
			} else {
				urlOrParams += '&businessLine=' + jQuery('select[name="business_line_est"] > option:selected').val();
			}
			//added agentID so that local carriers can be limited.
			if (typeof(urlOrParams) == 'object' && urlOrParams['src_field'] == 'local_carrier') {
				if (typeof urlOrParams == 'object') {
					urlOrParams['search_value'] = jQuery('select[name="agentid"]').val();
					urlOrParams['search_key'] = 'agentid';
				} else {
					urlOrParams += '&search_value=' + jQuery('select[name="agentid"]').val();
					urlOrParams += '&search_key=agentid';
				}
			}
			var loadDateUserFormat = jQuery('#' + Estimates_Popup_Js.I().moduleName + '_editView_fieldName_load_date').val();
			if(loadDateUserFormat != ''){
				var dateFormat = jQuery('#' + Estimates_Popup_Js.I().moduleName + '_editView_fieldName_load_date').data('date-format');
				var y = ''; 
				var m = '';
				var d = '';
				for(var i = 0; i<10; i++){
					if(dateFormat[i] == 'y'){
						y += loadDateUserFormat[i];
					} else if(dateFormat[i] == 'm'){
						m += loadDateUserFormat[i];
					} else if(dateFormat[i] == 'd'){
						d += loadDateUserFormat[i];
					}
				}
				loadDateDBFormat = y+'-'+m+'-'+d;
				if (typeof urlOrParams == 'object') {
					urlOrParams['loadDate'] = loadDateDBFormat;
				} else {
					urlOrParams += '&loadDate=' + loadDateDBFormat;
				}
			}
			var urlString = (typeof urlOrParams == 'string')? urlOrParams : jQuery.param(urlOrParams);
			var url = 'index.php?'+urlString;	
			console.dir(url);
			
			//Creates the blockUI element (lightbox)
			var progressIndicatorElement = jQuery.progressIndicator();
			containerApp = AppConnector.request(url).then(
				function(data) {
					progressIndicatorElement.progressIndicator({'mode' : 'hide'});
					
					app.showModalWindow(data);
					var popupInstance = Vtiger_Popup_Js.getInstance();
					var triggerEventName = jQuery('.triggerEventName').val();
					popupInstance.setEventName(triggerEventName);
					popupInstance.registerEvents();
				}
			).then(
				function() {
					//Let the application know we are finished opening a popup
					jQuery('#popupStatus').val(1);	
				}
			)
			
			if (typeof this.destroy == 'function') {
				// To remove form elements that have created earlier
				this.destroy();
			}
			jQuery.initWindowMsg();

			if(typeof cb != 'undefined') {
				this.retrieveSelectedRecords(cb, eventName);
			}

			 if(typeof onLoadCb == 'function') {
				jQuery.windowMsg('Vtiger.OnPopupWindowLoad.Event', function(data){
					onLoadCb(data);
				})
			}
		} 
		return containerApp;
	},

	/**
	 * Function to get complete params
	 */
    getCompleteParams: function () {
        var params = {};
        params['view'] = this.getView();
        params['src_module'] = this.getSourceModule();
        params['src_record'] = this.getSourceRecord();
        params['src_field'] = this.getSourceField();
        params['search_key'] = this.getSearchKey();
        params['search_value'] = this.getSearchValue();
        params['orderby'] = this.getOrderBy();
        params['sortorder'] = this.getSortOrder();
        params['page'] = this.getPageNumber();
        //if(params['src_field'] == 'contract'){
        params['related_parent_module'] = this.getRelatedParentModule();
        params['related_parent_id'] = this.getRelatedParentRecord();
        params['module'] = this.getSearchedModule();
        params['assignedTo'] = this.getAssignedTo();
        //}
        if (jQuery('#accountId') != undefined) {
            params['accountId'] = jQuery('#accountId').val();
            params['businessLine'] = jQuery('#businessLine').val();

        }
        if (this.isMultiSelectMode()) {
            params['multi_select'] = true;
        }
        var filterSelectElement = jQuery('#popupRecordFilter');
        if(filterSelectElement.length > 0){
            params['cvid'] = filterSelectElement.val();
        }
        return params;
    },
});
