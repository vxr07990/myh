/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("AgentManager_Detail_Js",{},{
	
	registerAddUserButton : function() {
		var thisInstance = this;
		
		if(jQuery('#addUserButton').length) {
			jQuery('#addUserButton').on('click', function() {
				var url = jQuery(this).data('url');
				console.dir(url);
				window.open(url, "Add Agency User", "height=650,width=850");
			});
		}
	},
	
	registerSelectUserButton : function() {
		var thisInstance = this;
		
		if(jQuery('#selectUserButton').length) {
			jQuery('#selectUserButton').on('click', function() {
				var url = jQuery(this).data('url');
				console.dir(url);
				window.open(url, "Select Agency Users", "height=650,width=850");
				jQuery(window).bind('postSelection', function(event) {console.dir(event);});
			});
		}
	},
	
	registerEventForRelatedList : function(){
		var thisInstance = this;
		var detailContentsHolder = this.getContentHolder();
		/*detailContentsHolder.on('click','.relatedListHeaderValues',function(e){
			var element = jQuery(e.currentTarget);
			var selectedTabElement = thisInstance.getSelectedTab();
			var relatedModuleName = thisInstance.getRelatedModuleName();
			var relatedController = new Vtiger_RelatedList_Js(thisInstance.getRecordId(), app.getModuleName(), selectedTabElement, relatedModuleName);
			relatedController.sortHandler(element);
		});*/
		
		detailContentsHolder.on('click', 'button.selectRelation', function(e){
			var selectedTabElement = thisInstance.getSelectedTab();
			var relatedModuleName = thisInstance.getRelatedModuleName();
			var relatedController = new Vtiger_RelatedList_Js(thisInstance.getRecordId(), app.getModuleName(), selectedTabElement, relatedModuleName);
			relatedController.showSelectRelationPopup().then(function(data){
				var emailEnabledModule = jQuery(data).find('[name="emailEnabledModules"]').val();
				if(emailEnabledModule){
					thisInstance.registerEventToEditRelatedStatus();
				}
			thisInstance.registerAddUserButton();
			});
		});

		/*detailContentsHolder.on('click', 'a.relationDelete', function(e){
			e.stopImmediatePropagation();
			var element = jQuery(e.currentTarget);
			var instance = Vtiger_Detail_Js.getInstance();
			var key = instance.getDeleteMessageKey();
			var message = app.vtranslate(key);
			Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
				function(e) {
					var row = element.closest('tr');
					var relatedRecordid = row.data('id');
					var selectedTabElement = thisInstance.getSelectedTab();
					var relatedModuleName = thisInstance.getRelatedModuleName();
					var relatedController = new Vtiger_RelatedList_Js(thisInstance.getRecordId(), app.getModuleName(), selectedTabElement, relatedModuleName);
					relatedController.deleteRelation([relatedRecordid]).then(function(response){
						relatedController.loadRelatedList();
					});
				},
				function(error, err){
				}
			);
		});*/
	},
	
	loadContents : function(url,data) {
		var thisInstance = this;
		var aDeferred = jQuery.Deferred();

		var detailContentsHolder = this.getContentHolder();
		var params = url;
		if(typeof data != 'undefined'){
			params = {};
			params.url = url;
			params.data = data;
		}
		AppConnector.requestPjax(params).then(
			function(responseData){
				detailContentsHolder.html(responseData);
				responseData = detailContentsHolder.html();
				//thisInstance.triggerDisplayTypeEvent();
				thisInstance.registerBlockStatusCheckOnLoad();
				//Make select box more usability
				app.changeSelectElementView(detailContentsHolder);
				//Attach date picker event to date fields
				app.registerEventForDatePickerFields(detailContentsHolder);
				app.registerEventForTextAreaFields(jQuery(".commentcontent"));
				jQuery('.commentcontent').autosize();
				thisInstance.getForm().validationEngine();
				aDeferred.resolve(responseData);
				if(thisInstance.addressAutofill) {thisInstance.initializeAddressAutofill(thisInstance.autofillModuleName);}
				thisInstance.registerAddUserButton();
				thisInstance.registerSelectUserButton();
			},
			function(){

			}
		);

		return aDeferred.promise();
	},


	registerEvents: function(){
		this._super();
		this.initializeAddressAutofill('AgentManager');
		this.registerAddUserButton();
		//this.registerSelectUserButton();
		//this.registerEventForRelatedList();
	}
});