/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Inventory_Detail_Js("Surveys_Detail_Js",{},{
	/**
	 * Function to handle the ajax edit for detailview and summary view fields
	 * which will expects the currentTdElement
	 */
	ajaxEditHandling : function(currentTdElement) {
			//console.dir('In This Function');
			var thisInstance = this;
			var detailViewValue = jQuery('.value',currentTdElement);
			var editElement = jQuery('.edit',currentTdElement);
			var actionElement = jQuery('.summaryViewEdit', currentTdElement);
			var fieldnameElement = jQuery('.fieldname', editElement);
			var fieldName = fieldnameElement.val();
			var fieldElement = jQuery('[name="'+ fieldName +'"]', editElement);
			//console.dir(detailViewValue);
			//console.dir(fieldName);
			
			if(detailViewValue.hasClass('hide')){
				//console.dir('its hidden?');
				return;
			}
			if(fieldElement.attr('disabled') == 'disabled'){
				return;
			}
			
			if(editElement.length <= 0) {
				return;
			}

			if(editElement.is(':visible')){
				return;
			}

			detailViewValue.addClass('hide');
			editElement.removeClass('hide').show().children().filter('input[type!="hidden"]input[type!="image"],select').filter(':first').focus();

			var saveTriggred = false;
			var preventDefault = false;

			var saveHandler = function(e) {
				var element = jQuery(e.target);
				if((element.closest('td').is(currentTdElement))){
					return;
				}

				currentTdElement.removeAttr('tabindex');

				var previousValue = fieldnameElement.data('prevValue');
				var formElement = thisInstance.getForm();
				var formData = formElement.serializeFormData();
				var ajaxEditNewValue = formData[fieldName];
				//value that need to send to the server
				var fieldValue = ajaxEditNewValue;
                var fieldInfo = Vtiger_Field_Js.getInstance(fieldElement.data('fieldinfo'));

                // Since checkbox will be sending only on and off and not 1 or 0 as currrent value
				if(fieldElement.is('input:checkbox')) {
					if(fieldElement.is(':checked')) {
						ajaxEditNewValue = '1';
					} else {
						ajaxEditNewValue = '0';
					}
					fieldElement = fieldElement.filter('[type="checkbox"]');
				}
				var errorExists = fieldElement.validationEngine('validate');
				//If validation fails
				if(errorExists) {
					return;
				}




                fieldElement.validationEngine('hide');
                //Before saving ajax edit values we need to check if the value is changed then only we have to save
                if(previousValue == ajaxEditNewValue) {
                    editElement.addClass('hide');
                    detailViewValue.removeClass('hide');
					actionElement.show();
					jQuery(document).off('click', '*', saveHandler);
                } else {
					var preFieldSaveEvent = jQuery.Event(thisInstance.fieldPreSave);
					fieldElement.trigger(preFieldSaveEvent, {'fieldValue' : fieldValue,  'recordId' : thisInstance.getRecordId()});
					if(preFieldSaveEvent.isDefaultPrevented()) {
						//Stop the save
						saveTriggred = false;
						preventDefault = true;
						return
					}
					preventDefault = false;

					jQuery(document).off('click', '*', saveHandler);

					if(!saveTriggred && !preventDefault) {
						saveTriggred = true;
					}else{
						return;
					}

                    currentTdElement.progressIndicator();
					editElement.addClass('hide');
                    var fieldNameValueMap = {};
                    if(fieldInfo.getType() == 'multipicklist' || fieldInfo.getType() == 'multiagent') {
                        var multiPicklistFieldName = fieldName.split('[]');
                        fieldName = multiPicklistFieldName[0];
                    }
                    fieldNameValueMap["value"] = fieldValue;
					fieldNameValueMap["field"] = fieldName;
					fieldNameValueMap = thisInstance.getCustomFieldNameValueMap(fieldNameValueMap);
                    thisInstance.saveFieldValues(fieldNameValueMap).then(function(response) {
						var postSaveRecordDetails = response.result;
						currentTdElement.progressIndicator({'mode':'hide'});
                        detailViewValue.removeClass('hide');
						actionElement.show();
                        detailViewValue.html(postSaveRecordDetails[fieldName].display_value);
                        fieldElement.trigger(thisInstance.fieldUpdatedEvent,{'old':previousValue,'new':fieldValue});
                        fieldnameElement.data('prevValue', ajaxEditNewValue);
                        fieldElement.data('selectedValue', ajaxEditNewValue); 
                        //After saving source field value, If Target field value need to change by user, show the edit view of target field. 
                        if(thisInstance.targetPicklistChange) { 
                                if(jQuery('.summaryView', thisInstance.getForm()).length > 0) { 
                                        thisInstance.targetPicklist.find('.summaryViewEdit').trigger('click'); 
                                } else { 
                                        thisInstance.targetPicklist.trigger('click'); 
                                } 
                                thisInstance.targetPicklistChange = false; 
                                thisInstance.targetPicklist = false; 
                        } 
                        },
                        function(error){
                            //TODO : Handle error
                            currentTdElement.progressIndicator({'mode':'hide'});
                        }
                    )
                }
			}

			jQuery(document).on('click','*', saveHandler);
	},

	registerEvents: function(){
		console.dir('Making it into Surveys Detail.js');
		this._super();
		this.initializeAddressAutofill('Surveys');
	}
});