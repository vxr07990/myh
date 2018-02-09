/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
Vtiger_Popup_Js("OrdersTask_Popup_Js",{
    moduleName: 'OrdersTask',
},{

	show : function(urlOrParams, cb, windowName, eventName, onLoadCb){
		//Only open a new popup if one is not currently in the process of being opened
		var containerApp;
		if(this.getPopupStatus()){

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
			var value = jQuery('input[name="sourceRecord"]').val()?jQuery('input[name="sourceRecord"]').val():(jQuery('input[name="ordersid"]').val()) ? jQuery('input[name="ordersid"]').val() : jQuery('.select_task:checkbox:checked').data("id");
			console.dir(value);
			if (typeof urlOrParams == 'object') {
				urlOrParams['src_record'] = value;
			} else {
				urlOrParams += '&src_record=' + value;
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
	getCompleteParams : function(){
		var params = {};
		params['view'] = this.getView();
		params['src_module'] = this.getSourceModule();
		params['src_record'] = this.getSourceRecord();
		params['src_field'] = this.getSourceField();
		params['search_key'] =  this.getSearchKey();
		params['search_value'] =  this.getSearchValue();
		params['orderby'] =  this.getOrderBy();
		params['sortorder'] =  this.getSortOrder();
		params['page'] = this.getPageNumber();
		params['related_parent_module'] = this.getRelatedParentModule();
		params['related_parent_id'] = this.getRelatedParentRecord();
		params['module'] = this.getSearchedModule();

		if(this.isMultiSelectMode()) {
			params['multi_select'] = true;
		}

        var filterSelectElement = jQuery('#popupRecordFilter');
        if(filterSelectElement.length > 0){
            params['cvid'] = filterSelectElement.val();
        }

		return params;
	},
});
