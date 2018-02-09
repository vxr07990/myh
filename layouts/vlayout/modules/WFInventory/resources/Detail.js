/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("WFInventory_Detail_Js",{

},{
	/**
	 * Function to register Event for Sorting
	 */
	registerEventForRelatedList : function(){
		var thisInstance = this;
		var detailContentsHolder = this.getContentHolder();
		detailContentsHolder.on('click','.relatedListHeaderValues',function(e){
			var element = jQuery(e.currentTarget);
			var selectedTabElement = thisInstance.getSelectedTab();
			var relatedModuleName = thisInstance.getRelatedModuleName();
			var relatedController = new Vtiger_RelatedList_Js(thisInstance.getRecordId(), app.getModuleName(), selectedTabElement, relatedModuleName);
			relatedController.sortHandler(element);
		});

		detailContentsHolder.on('click', 'button.selectRelation', function(e){
			var selectedTabElement = thisInstance.getSelectedTab();
			var relatedModuleName = thisInstance.getRelatedModuleName();
			var relatedController = new Vtiger_RelatedList_Js(thisInstance.getRecordId(), app.getModuleName(), selectedTabElement, relatedModuleName);
			relatedController.showSelectRelationPopup().then(function(data){
				var emailEnabledModule = jQuery(data).find('[name="emailEnabledModules"]').val();
				if(emailEnabledModule){
					thisInstance.registerEventToEditRelatedStatus();
				}
			});
		});

		// Fix upload form
		var uploadForm = jQuery('#InventoryImagesUploadForm');
		var enctypeSetting = uploadForm.attr('enctype');

		var parentForm = uploadForm.closest('form');
		if (parentForm.length > 0) {
			parentForm.attr('enctype', enctypeSetting);
		}

		detailContentsHolder.on('click', 'a.relationDelete', function(e){
			e.stopImmediatePropagation();
			var element = jQuery(e.currentTarget);
			var instance = Vtiger_Detail_Js.getInstance();
			var key = instance.getDeleteMessageKey();
			var message = app.vtranslate(key);
			Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
				function(e) {
					var row = element.closest('.listViewEntries');
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
		});

		// Register preview image popup
		thisInstance.showPreviewImage();
	},

	showPreviewImage: function() {
		var relatedContents = jQuery('.relatedContents');
		var listViewEntries = relatedContents.find('.listViewEntries');
		listViewEntries.find('img').on('click', function () {
			var focus = jQuery(this);
			var src = focus.attr('src');
			var data = '<div class="modelContainer">' +
				'<div class="modal-header contentsBackground">' +
				'<button class="close" aria-hidden="true" data-dismiss="modal" type="button" title="Close">×</button>' +
				'<h3>&nbsp;</h3></div>' +
				'<form class="form-horizontal recordEditView" method="post" action="index.php">' +
				'<img src="' + src + '" style="width: 100%;" />' +
				'</form>' +
				'</div>';

			app.showModalWindow(data, function(data){

			}, {
				'width': '400px',
				'text-align': 'center'
			});
		});
	},
});
