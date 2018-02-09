/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Popup_Js("Leads_Popup_Js",{
	getInstance: function() {
		return new Leads_Popup_Js();
	}
},{
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
				urlOrParams['assignedTo'] = jQuery('select[name="assigned_user_id"]').val();
				if(urlOrParams['src_field'] == 'source_name') {
					urlOrParams['search_value'] = jQuery('select[name="agentid"]').val();
					urlOrParams['search_key'] = 'agentid';
				}
				} else {
				urlOrParams += '&triggerEventName=' + eventName;
				urlOrParams += '&assignedTo=' + jQuery('select[name="assigned_user_id"]').val();
					urlOrParams += '&search_value=' + jQuery('select[name="agentid"]').val();
					urlOrParams += '&search_key=agentid';
			}

			var urlString = (typeof urlOrParams == 'string')? urlOrParams : jQuery.param(urlOrParams);
			var url = 'index.php?'+urlString;	
			/*console.dir(url);*/
			
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
});
