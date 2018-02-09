/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Inventory_Detail_Js("Quotes_Detail_Js",{},{/*
	flatItemsSequence : jQuery('.flatItemRow').length,
	
	qtyRateItemsSequence : jQuery('.qtyRateItemRow').length,
	
	crateSequence : jQuery('.crateRow').length,
	
	registerWeightChangeEvent : function() {
		var thisInstance = this;
		jQuery('input[name="weight"]').on('change', function() {
			var weightValue = jQuery('input[name="weight"]').val();
			var valuation = parseInt(weightValue)*6;
			jQuery('input[name="valuation_amount"]').val(valuation);
			
			var currentTdElement = jQuery('input[name="valuation_amount"]').closest('td');
			
			var detailViewValue = jQuery('.value',currentTdElement);
			var editElement = jQuery('.edit',currentTdElement);
			var actionElement = jQuery('.summaryViewEdit', currentTdElement);
			var fieldnameElement = jQuery('.fieldname', editElement);
			var fieldName = fieldnameElement.val();
			var fieldElement = jQuery('[name="'+ fieldName +'"]', editElement);
			var previousValue = fieldnameElement.data('prevValue');
			
			currentTdElement.progressIndicator();
			detailViewValue.addClass('hide');
			
			var fieldNameValueMap = {};
			fieldNameValueMap["value"] = valuation;
			fieldNameValueMap["field"] = fieldName;
			fieldNameValueMap = thisInstance.getCustomFieldNameValueMap(fieldNameValueMap);
			thisInstance.saveFieldValues(fieldNameValueMap).then(function(response) {
				var postSaveRecordDetails = response.result;
				currentTdElement.progressIndicator({'mode':'hide'});
				detailViewValue.removeClass('hide');
				jQuery('#interstateRateQuick').removeClass('hide');
				actionElement.show();
				detailViewValue.html(postSaveRecordDetails[fieldName].display_value);
				fieldElement.trigger(thisInstance.fieldUpdatedEvent,{'old':previousValue,'new':valuation});
				fieldnameElement.data('prevValue', valuation);
				fieldElement.data('selectedValue', valuation); 
			});
		});
	},
	
	registerReportsButton : function() {
		var thisInstance = this;
		jQuery('#getReportSelectButton').on('click', function() {
			jQuery('#getReportSelectButton').closest('td').progressIndicator();
			jQuery('#getReportSelectButton').addClass('hide');
			var dataURL = 'index.php?module=Quotes&action=GetReport&record='+getQueryVariable('record')+'&requestType=GetAvailableReports';
			AppConnector.request(dataURL).then(
				function(data) {
					if(data.success) {
						jQuery('#reportContent').html(data.result);
						jQuery.colorbox({inline:true, width:'300px', height:'40%', left:'25%', top:'25%', href:'#reportContent', onClosed:function(){jQuery(document.body).css({overflow:'auto'});}, onComplete:function(){jQuery(document.body).css({overflow:'hidden'});}});
						jQuery('#reportContent').find('button').each(function() {
							jQuery(this).on('click', function() {
								jQuery('#reportContent').find('.contents').addClass('hide');
								jQuery('#reportContent').progressIndicator();
								var reportURL = 'index.php?module=Quotes&action=GetReport&record='+getQueryVariable('record')+'&reportId='+jQuery(this).attr('name')+'&reportName='+encodeURIComponent(jQuery(this).html());
								AppConnector.request(reportURL).then(
									function(data) {
										if(data.success) {
											window.location.href = 'index.php?module=Documents&view=Detail&record='+data.result;
										}
									},
									function(error) {
									}
								);
							});
						});
						jQuery('#getReportSelectButton').closest('td').progressIndicator({'mode':'hide'});
						jQuery('#getReportSelectButton').removeClass('hide');
					}
				},
				function(error) {
				}
			);
		});
	},
	
	registerInterstateRateEstimate : function() {
		var thisInstance = this;
		jQuery('#interstateRateQuick').on('click', function() {
			thisInstance.advancedRateEstimate();
		});
		if(jQuery('#Quotes_detailView_fieldValue_potential_id').find('a').length && jQuery('#Quotes_detailView_fieldValue_contact_id').find('a').length) {
			jQuery('#interstateRateDetail').on('click', function() {
				var errorExists = false;
				var errorString = '';
				if(jQuery('input[name="origin_zip"]').val().length < 5) {errorExists = true; errorString += 'Origin Zip must be valid\n';}
				if(jQuery('input[name="destination_zip"]').val().length < 5) {errorExists = true; errorString += 'Destination Zip must be valid\n';}
				if(jQuery('input[name="weight"]').val().length < 1) {errorExists = true; errorString += 'Weight must be set\n';}
				if(jQuery('input[name="valuation_amount"]').val.length < 1) {errorExists = true; errorString += 'Valuation amount must be set\n';}
				if(errorExists) {alert(errorString); return;}
				jQuery.colorbox({inline:true, width:'515px', height:'90%', left:'15%', top:'-5%', href:'#inline_content', onClosed:function(){jQuery(document.body).css({overflow:'auto'});thisInstance.getDetailedRate();}, onComplete:function(){jQuery(document.body).css({overflow:'hidden'});}});
				jQuery('#cboxLoadedContent').on('click','table.detailview-table td.fieldValue', function(e) {
					var currentTdElement = jQuery(e.currentTarget);
					thisInstance.ajaxEditHandling(currentTdElement);
				});
			});
		} else {
			jQuery('#interstateRateDetail').addClass('hide');
		}
		jQuery(document.head).append("<style>.cbxblockhead td {text-align:center; font-weight:bold; min-width:200px;} #inline_content td {padding:0 5px 0 0;} .packing .fieldValue {text-align:center;} .sit input {max-width:50px; padding:0;} .packing .edit .input-large {max-width:30px; padding:0;} .bulky .edit .input-large {max-width:20px; padding:0;}</style>");
	},
	
	getDetailedRate : function() {
		//console.dir('Function to generate XML and retrieve rate');
		var dataURL = 'index.php?module=Quotes&action=GetDetailedRate&record='+getQueryVariable('record');
		jQuery('th:contains("Item Details")').closest('table').find('tbody').addClass('hide');
		jQuery('td:contains("Grand Total")').closest('table').addClass('hide');
		jQuery('th:contains("Item Details")').closest('table').progressIndicator();
		jQuery('#interstateRateQuick').addClass('hide');
		jQuery('#interstateRateDetail').addClass('hide');
		jQuery('#interstateRateDetail').closest('td').progressIndicator();
		AppConnector.request(dataURL).then(
			function(data) {
				if(data.success) {
					var currentTable = jQuery('th:contains("Item Details")').closest('table');
					for(var key in data.result.lineitems) {
						currentTable.find('tr:contains("'+key+'")').find('span').html(parseFloat(data.result.lineitems[key]).toFixed(2));
						if(parseFloat(data.result.lineitems[key]) == 0) {
							currentTable.find('tr:contains("'+key+'")').addClass('hide');
						}
						else {
							currentTable.find('tr:contains("'+key+'")').removeClass('hide');
						}
					}
					jQuery('td:contains("Grand Total")').siblings().find('span').html(parseFloat(data.result.rateEstimate).toFixed(2));
					jQuery('#interstateRateQuick').removeClass('hide');
					jQuery('#interstateRateDetail').closest('td').progressIndicator({'mode':'hide'});
					jQuery('#interstateRateDetail').removeClass('hide');
					jQuery('th:contains("Item Details")').closest('table').find('tbody').removeClass('hide');
					jQuery('th:contains("Item Details")').closest('table').progressIndicator({'mode':'hide'});
					jQuery('td:contains("Grand Total")').closest('table').removeClass('hide');
				}
			},
			function(error) {
			}
		);
	},
	
	checkAccessorialFieldDisplay: function() {
		var thisInstance = this;
		var formElement = thisInstance.getForm();
		var formData = formElement.serializeFormData();
		
		var fieldName = 'acc_shuttle_origin_applied';
		thisInstance.toggleAccessorialFields(fieldName, formData[fieldName]);
		
		fieldName = 'acc_shuttle_origin_over25';
		thisInstance.toggleAccessorialFields(fieldName, formData[fieldName]);
		
		fieldName = 'acc_shuttle_dest_applied';
		thisInstance.toggleAccessorialFields(fieldName, formData[fieldName]);
		
		fieldName = 'acc_shuttle_dest_over25';
		thisInstance.toggleAccessorialFields(fieldName, formData[fieldName]);
		
		fieldName = 'acc_selfstg_origin_applied';
		thisInstance.toggleAccessorialFields(fieldName, formData[fieldName]);
		
		fieldName = 'acc_selfstg_dest_applied';
		thisInstance.toggleAccessorialFields(fieldName, formData[fieldName]);
	},
	
	advancedRateEstimate : function() {
		var currentTd = jQuery('#interstateRateQuick').closest('td');
		currentTd.progressIndicator();
		jQuery('#interstateRateQuick').addClass('hide');
		
		var dataURL = 'index.php?module=Quotes&action=QuickEstimate&record='+getQueryVariable('record');
		AppConnector.request(dataURL).then(
			function(data) {
				if(data.success) {
					var currentTable = jQuery('th:contains("Item Details")').closest('table');
					for(var key in data.result.lineitems) {
						currentTable.find('tr:contains("'+key+'")').find('span').html(parseFloat(data.result.lineitems[key]).toFixed(2));
						if(parseFloat(data.result.lineitems[key]) == 0) {
							currentTable.find('tr:contains("'+key+'")').addClass('hide');
						}
						else {
							currentTable.find('tr:contains("'+key+'")').removeClass('hide');
						}
					}
					jQuery('td:contains("Grand Total")').siblings().find('span').html(parseFloat(data.result.rateEstimate).toFixed(2));
					currentTd.progressIndicator({'mode':'hide'});
					jQuery('#interstateRateQuick').removeClass('hide');
				}
			},
			function(error, err) {
				alert(error + ': ' + err);
				currentTd.progressIndicator({'mode':'hide'});
			}
		);
	},

	registerBlockAnimationEvent : function(){
		var detailContentsHolder = this.getContentHolder();
		detailContentsHolder.on('click','.blockToggle',function(e){
			var currentTarget =  jQuery(e.currentTarget);
			var blockId = currentTarget.data('id');
			var closestBlock = currentTarget.closest('.detailview-table');
			var bodyContents = closestBlock.find('tbody');
			var data = currentTarget.data();
			var module = app.getModuleName();
			var hideHandler = function() {
				bodyContents.hide('slow');
				app.cacheSet(module+'.'+blockId, 0);
			}
			var showHandler = function() {
				bodyContents.show();
				app.cacheSet(module+'.'+blockId, 1);
				if(currentTarget.closest('div').parent().attr('id') == 'inline_content') {
					closestBlock.siblings().find('tbody').hide('slow');
				}
			}
			if(data.mode == 'show'){
				hideHandler();
				currentTarget.hide();
				closestBlock.find("[data-mode='hide']").show();
			}else{
				showHandler();
				currentTarget.hide();
				closestBlock.find("[data-mode='show']").show();
				if(currentTarget.closest('div').parent().attr('id') == 'inline_content') {
					closestBlock.siblings().each(function() {
						jQuery(this).find("[data-mode='hide']").show();
						jQuery(this).find("[data-mode='show']").hide();
						app.cacheSet(module+'.'+jQuery(this).find("[data-mode='show']").data('id'), 0);
					});
				}
			}
		});

	},
	
	registerDeleteMiscItemClickEvent : function() {
		var thisInstance = this;
		jQuery('.icon-trash').on('click', function(e) {
			var currentRow = jQuery(e.currentTarget).closest('tr');
			var lineItemId = currentRow.find('.lineItemId').val();
			if(lineItemId) {
				var dataURL = 'index.php?module=Quotes&action=DeleteMiscItem&rowType='+currentRow.attr('class')+'&lineItemId='+lineItemId;
				
				AppConnector.request(dataURL).then(
					function(data) {
						if(data.success) {
							currentRow.remove();
						}
					},
					function(error) {
					}
				);
			} else {currentRow.remove();}
		});
	},
	
	ajaxEditHandling : function(currentTdElement) {
		var thisInstance = this;
		var detailViewValue = jQuery('.value',currentTdElement);
		var editElement = jQuery('.edit',currentTdElement);
		var actionElement = jQuery('.summaryViewEdit', currentTdElement);
		var fieldnameElement = jQuery('.fieldname', editElement);
		var fieldName = fieldnameElement.val();
		var fieldElement = jQuery('[name="'+ fieldName +'"]', editElement);

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
		jQuery('#interstateRateQuick').addClass('hide');

		var saveTriggred = false;
		var preventDefault = false;
		
		var miscItem = false;
		var crateItem = false;
		var bulkyPackItem = false;
		
		if(currentTdElement.closest('tr').hasClass('qtyRateItemRow') || currentTdElement.closest('tr').hasClass('flatItemRow')) {
			miscItem = true;
		} else if(currentTdElement.closest('tr').hasClass('crateRow')) {
			crateItem = true;
		} else if(currentTdElement.closest('table').hasClass('bulky') || currentTdElement.closest('table').hasClass('packing')) {
			bulkyPackItem = true;
		}
		
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

			// Since checkbox will be sending only on and off and not 1 or 0 as currrent value
			if(fieldElement.is('input:checkbox')) {
				if(fieldElement.is(':checked')) {
					ajaxEditNewValue = '1';
				} else {
					ajaxEditNewValue = '0';
				}
				fieldElement = fieldElement.filter('[type="checkbox"]');
			}
			
			if(!miscItem && !crateItem && !bulkyPackItem) {
				var fieldInfo = Vtiger_Field_Js.getInstance(fieldElement.data('fieldinfo'));
				var errorExists = fieldElement.validationEngine('validate');
				//If validation fails
				if(errorExists) {
					return;
				}

				fieldElement.validationEngine('hide');
			}
			
			if(previousValue == ajaxEditNewValue) {
				editElement.addClass('hide');
				detailViewValue.removeClass('hide');
				actionElement.show();
				jQuery('#interstateRateQuick').removeClass('hide');
				jQuery(document).off('click', '*', saveHandler);
				return;
			}
			
			if(!miscItem && !crateItem && !bulkyPackItem) {
				var preFieldSaveEvent = jQuery.Event(thisInstance.fieldPreSave);
				fieldElement.trigger(preFieldSaveEvent, {'fieldValue' : fieldValue,  'recordId' : thisInstance.getRecordId()});
				if(preFieldSaveEvent.isDefaultPrevented()) {
					//Stop the save
					saveTriggred = false;
					preventDefault = true;
					return
				}
				preventDefault = false;
			}
			
			jQuery(document).off('click', '*', saveHandler);
			
			if(!saveTriggred && !preventDefault) {
				saveTriggred = true;
			}else{
				return;
			}
			
			if(miscItem) {
				currentTdElement.progressIndicator();
				editElement.addClass('hide');
				var currentRow = currentTdElement.closest('tr');
				var description = currentRow.find('input[name*="Description"]').val();
				var charge = currentRow.find('input[name*="Charge"]').val();
				var qty = '1';
				if(currentRow.find('input[name*="Qty"]').length) {
					qty = currentRow.find('input[name*="Qty"]').val();
				}
				var discounted = (currentRow.find('input:checkbox[name*="Discounted"]')[0].checked) ? '1':'0';
				var discount = currentRow.find('input[name*="DiscountPercent"]').val();
				var chargeType = (currentRow.hasClass('flatItemRow')) ? 'flat':'qty';
				var line_item_id = (currentRow.find('input.lineItemId').length) ? currentRow.find('input.lineItemId').val():'';
				var dataURL = "index.php?module=Quotes&action=SaveMiscCharge&record="+getQueryVariable('record')+"&description="+description+"&charge="+charge+"&qty="+qty+"&discounted="+discounted+"&discount="+discount+"&type="+chargeType+"&line_item_id="+line_item_id;
				
				if(currentRow.hasClass('newItemRow')) {
					
					if(description === '') {
						//Update value but do not save new item without description
						if(fieldElement.is('input:checkbox'))
						{
							if(fieldValue === 'on') {
								detailViewValue.html('Yes');
							}
							else {
								detailViewValue.html('No');
							}
						}
						else {
							detailViewValue.html(fieldValue);
						}
						currentTdElement.progressIndicator({'mode':'hide'});
						detailViewValue.removeClass('hide');
						actionElement.show();
						jQuery('#interstateRateQuick').removeClass('hide');
					}
					else {
						//Save the new item
						AppConnector.request(dataURL).then(
							function(data) {
								if(data.success) {
									if(fieldElement.is('input:checkbox'))
									{
										if(fieldValue === 'on') {
											detailViewValue.html('Yes');
										}
										else {
											detailViewValue.html('No');
										}
									}
									else {
										detailViewValue.html(fieldValue);
									}
									currentTdElement.progressIndicator({'mode':'hide'});
									detailViewValue.removeClass('hide');
									actionElement.show();
									jQuery('#interstateRateQuick').removeClass('hide');
									currentRow.removeClass('newItemRow');
									currentRow.append("<input type='hidden' class='lineItemId' value='"+data.result+"' />");
								}
							},
							function(error) {
							}
						);
					}
				}
				else {
					//Check if description has been deleted
					if(description === '') {
						currentTdElement.progressIndicator({'mode':'hide'});
						detailViewValue.removeClass('hide');
						actionElement.show();
						jQuery('#interstateRateQuick').removeClass('hide');
						jQuery(document).off('click', '*', saveHandler);
						alert('Description cannot be blank - changes to this record will not be saved');
						return;
					}
					//Save updated field and display new value
					AppConnector.request(dataURL).then(
						function(data) {
							if(data.success) {
								if(fieldElement.is('input:checkbox'))
								{
									if(fieldValue === 'on') {
										detailViewValue.html('Yes');
									}
									else {
										detailViewValue.html('No');
									}
								}
								else {
									detailViewValue.html(fieldValue);
								}
								currentTdElement.progressIndicator({'mode':'hide'});
								detailViewValue.removeClass('hide');
								actionElement.show();
								jQuery('#interstateRateQuick').removeClass('hide');
							}
						},
						function(error) {
						}
					);
				}
			} else if(crateItem) {
				currentTdElement.progressIndicator();
				editElement.addClass('hide');
				var currentRow = currentTdElement.closest('tr');
				var description = currentRow.find('input[name*="Description"]').val();
				var crateId = currentRow.find('input[name*="crateId"]').val();
				var length = currentRow.find('input[name*="Length"]').val();
				var width = currentRow.find('input[name*="Width"]').val();
				var height = currentRow.find('input[name*="Height"]').val();
				var pack = (currentRow.find('input:checkbox[name*="cratePack"]')[0].checked) ? '1':'0';
				var unpack = (currentRow.find('input:checkbox[name*="crateUnpack"]')[0].checked) ? '1':'0';
				var otPack = (currentRow.find('input:checkbox[name*="crateOTPack"]')[0].checked) ? '1':'0';
				var otUnpack = (currentRow.find('input:checkbox[name*="crateOTUnpack"]')[0].checked) ? '1':'0';
				var discount = currentRow.find('input[name*="crateDiscountPercent"]').val();
				var line_item_id = (currentRow.find('input.lineItemId').length) ? currentRow.find('input.lineItemId').val():'';
				var dataURL = "index.php?module=Quotes&action=SaveCrate&record="+getQueryVariable('record')+"&crateid="+crateId+"&description="+description+"&length="+length+"&width="+width+"&height="+height+"&pack="+pack+"&unpack="+unpack+"&otpack="+otPack+"&otunpack="+otUnpack+"&discount="+discount+"&line_item_id="+line_item_id;
				
				if(currentRow.hasClass('newItemRow')) {
					if(description == '' || length == '' || length == '0' || width == '' || width == '0' || height == '' || height == '0') {
						//Update value but do not save new item without description, length, width, and height
						if(fieldElement.is('input:checkbox'))
						{
							if(fieldValue === 'on') {
								detailViewValue.html('Yes');
							}
							else {
								detailViewValue.html('No');
							}
						}
						else {
							detailViewValue.html(fieldValue);
						}
						currentTdElement.progressIndicator({'mode':'hide'});
						detailViewValue.removeClass('hide');
						actionElement.show();
						jQuery('#interstateRateQuick').removeClass('hide');
					}
					else {
						//Save the new item
						AppConnector.request(dataURL).then(
							function(data) {
								if(data.success) {
									if(fieldElement.is('input:checkbox'))
									{
										if(fieldValue === 'on') {
											detailViewValue.html('Yes');
										}
										else {
											detailViewValue.html('No');
										}
									}
									else {
										detailViewValue.html(fieldValue);
									}
									currentTdElement.progressIndicator({'mode':'hide'});
									detailViewValue.removeClass('hide');
									actionElement.show();
									jQuery('#interstateRateQuick').removeClass('hide');
									currentRow.removeClass('newItemRow');
									currentRow.append("<input type='hidden' class='lineItemId' value='"+data.result+"' />");
								}
							},
							function(error) {
							}
						);
					}
				}
				else {
					//Check if description, length, width, or height have been deleted
					var errorExists = false;
					var errorString = '';
					if(description == '') {
						errorExists = true;
						errorString += "Description cannot be blank.\n";
					}
					if(length == '' || width == '0') {
						errorExists = true;
						errorString += "Length cannot be blank and must be greater than 0.\n";
					}
					if(width == '' || width == '0') {
						errorExists = true;
						errorString += "Width cannot be blank and must be greater than 0.\n";
					}
					if(height == '' || height == '0') {
						errorExists = true;
						errorString += "Height cannot be blank and must be greater than 0.\n";
					}
					//If any required fields are blank, display error and do not save to database
					if(errorExists) {
						currentTdElement.progressIndicator({'mode':'hide'});
						detailViewValue.removeClass('hide');
						actionElement.show();
						jQuery('#interstateRateQuick').removeClass('hide');
						jQuery(document).off('click', '*', saveHandler);
						alert(errorString);
						return;
					}
					//Save updated field and display new value
					AppConnector.request(dataURL).then(
						function(data) {
							if(data.success) {
								if(fieldElement.is('input:checkbox'))
								{
									if(fieldValue === 'on') {
										detailViewValue.html('Yes');
									}
									else {
										detailViewValue.html('No');
									}
								}
								else {
									detailViewValue.html(fieldValue);
								}
								currentTdElement.progressIndicator({'mode':'hide'});
								detailViewValue.removeClass('hide');
								actionElement.show();
								jQuery('#interstateRateQuick').removeClass('hide');
							}
						},
						function(error) {
						}
					);
				}
			} else if(bulkyPackItem) {
				if(fieldValue == '') {
					fieldValue = '0';
				}
				var itemType = (currentTdElement.closest('table').hasClass('bulky')) ? 'Bulky':'Packing';
				
				currentTdElement.progressIndicator();
				editElement.addClass('hide');
				var dataURL = 'index.php?module=Quotes&action=Save'+itemType+'Item&record='+getQueryVariable('record')+'&name='+fieldName+'&qty='+fieldValue;
				
				AppConnector.request(dataURL).then(
					function(data) {
						if(data.success) {
							detailViewValue.html(fieldValue);
							fieldnameElement.data('prevValue', fieldValue);
							currentTdElement.progressIndicator({'mode':'hide'});
							detailViewValue.removeClass('hide');
							jQuery('#interstateRateQuick').removeClass('hide');
						}
					},
					function(error) {
					}
				);
			} else {
				currentTdElement.progressIndicator();
				editElement.addClass('hide');
				var fieldNameValueMap = {};
				if(fieldInfo.getType() == 'multipicklist') {
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
					jQuery('#interstateRateQuick').removeClass('hide');
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
					if(currentTdElement.closest('div').parent().attr('id') == 'inline_content') {
						thisInstance.toggleAccessorialFields(fieldName, ajaxEditNewValue);
					}
					},
					function(error){
						//TODO : Handle error
						currentTdElement.progressIndicator({'mode':'hide'});
					}
				)
			}
		};

		jQuery(document).on('click','*', saveHandler);
		
	},
	
	toggleAccessorialFields: function(fieldName, fieldValue) {
		var thisInstance = this;
		if(fieldName == 'acc_shuttle_origin_applied') {
			if(fieldValue == '0') {
				jQuery('.shuttleOriginOT').addClass('hide');
				jQuery('.shuttleOriginOTHide').removeClass('hide');
				thisInstance.zeroField(jQuery('.shuttleOriginOT'));
				
				jQuery('.shuttleOriginOver25').addClass('hide');
				jQuery('.shuttleOriginOver25Hide').removeClass('hide');
				thisInstance.zeroField(jQuery('.shuttleOriginOver25'));
				
				jQuery('.shuttleOriginMiles').addClass('hide');
				jQuery('.shuttleOriginMilesHide').removeClass('hide');
				thisInstance.zeroField(jQuery('.shuttleOriginMiles'));
			}
			else {
				jQuery('.shuttleOriginOT').removeClass('hide');
				jQuery('.shuttleOriginOTHide').addClass('hide');
				
				jQuery('.shuttleOriginOver25').removeClass('hide');
				jQuery('.shuttleOriginOver25Hide').addClass('hide');
			}
		}
		else if(fieldName == 'acc_shuttle_dest_applied') {
			if(fieldValue == '0') {
				jQuery('.shuttleDestOT').addClass('hide');
				jQuery('.shuttleDestOTHide').removeClass('hide');
				thisInstance.zeroField(jQuery('.shuttleDestOT'));
				
				jQuery('.shuttleDestOver25').addClass('hide');
				jQuery('.shuttleDestOver25Hide').removeClass('hide');
				thisInstance.zeroField(jQuery('.shuttleDestOver25'));
				
				jQuery('.shuttleDestMiles').addClass('hide');
				jQuery('.shuttleDestMilesHide').removeClass('hide');
				thisInstance.zeroField(jQuery('.shuttleDestMiles'));
			}
			else {
				jQuery('.shuttleDestOT').removeClass('hide');
				jQuery('.shuttleDestOTHide').addClass('hide');
				
				jQuery('.shuttleDestOver25').removeClass('hide');
				jQuery('.shuttleDestOver25Hide').addClass('hide');
			}
		}
		else if(fieldName == 'acc_shuttle_origin_over25') {
			if(fieldValue == '0') {
				jQuery('.shuttleOriginMiles').addClass('hide');
				jQuery('.shuttleOriginMilesHide').removeClass('hide');
				thisInstance.zeroField(jQuery('.shuttleOriginMiles'));
			}
			else {
				jQuery('.shuttleOriginMiles').removeClass('hide');
				jQuery('.shuttleOriginMilesHide').addClass('hide');
			}
		}
		else if(fieldName == 'acc_shuttle_dest_over25') {
			if(fieldValue == '0') {
				jQuery('.shuttleDestMiles').addClass('hide');
				jQuery('.shuttleDestMilesHide').removeClass('hide');
				thisInstance.zeroField(jQuery('.shuttleDestMiles'));
			}
			else {
				jQuery('.shuttleDestMiles').removeClass('hide');
				jQuery('.shuttleDestMilesHide').addClass('hide');
			}
		}
		else if(fieldName == 'acc_selfstg_origin_applied') {
			if(fieldValue == '0') {
				jQuery('.selfstgOriginOT').addClass('hide');
				jQuery('.selfstgOriginOTHide').removeClass('hide');
				thisInstance.zeroField(jQuery('.selfstgOriginOT'));
			}
			else {
				jQuery('.selfstgOriginOT').removeClass('hide');
				jQuery('.selfstgOriginOTHide').addClass('hide');
			}
		}
		else if(fieldName == 'acc_selfstg_dest_applied') {
			if(fieldValue == '0') {
				jQuery('.selfstgDestOT').addClass('hide');
				jQuery('.selfstgDestOTHide').removeClass('hide');
				thisInstance.zeroField(jQuery('.selfstgDestOT'));
			}
			else {
				jQuery('.selfstgDestOT').removeClass('hide');
				jQuery('.selfstgDestOTHide').addClass('hide');
			}
		}
	},
	
	zeroField: function(currentTdElement) {
		var thisInstance = this;
		var detailViewValue = jQuery('.value',currentTdElement);
		var editElement = jQuery('.edit',currentTdElement);
		var actionElement = jQuery('.summaryViewEdit', currentTdElement);
		var fieldnameElement = jQuery('.fieldname', editElement);
		var fieldName = fieldnameElement.val();
		var fieldElement = jQuery('[name="'+ fieldName +'"]', editElement);
		var saveTriggred = false;
		var preventDefault = false;
		
		var previousValue = fieldnameElement.data('prevValue');
		var formElement = thisInstance.getForm();
		var formData = formElement.serializeFormData();
		var ajaxEditNewValue = '0';
		//value that need to send to the server
		var fieldValue = ajaxEditNewValue;
		var fieldInfo = Vtiger_Field_Js.getInstance(fieldElement.data('fieldinfo'));
		
		if(fieldElement.is('input:checkbox')) {
			if(previousValue == 'No') {
				previousValue = '0';
			} else if(previousValue == 'Yes') {
				previousValue = '1';
			}
		}

		var errorExists = fieldElement.validationEngine('validate');
		//If validation fails
		if(errorExists) {
			return;
		}
		
		if(previousValue == ajaxEditNewValue) {
			return;
		}
		
		var preFieldSaveEvent = jQuery.Event(thisInstance.fieldPreSave);
		fieldElement.trigger(preFieldSaveEvent, {'fieldValue' : fieldValue,  'recordId' : thisInstance.getRecordId()});
		if(preFieldSaveEvent.isDefaultPrevented()) {
			//Stop the save
			saveTriggred = false;
			preventDefault = true;
			return
		}
		preventDefault = false;

		if(!saveTriggred && !preventDefault) {
			saveTriggred = true;
		}else{
			return;
		}
		
		var fieldNameValueMap = {};
		fieldNameValueMap['field'] = fieldName;
		fieldNameValueMap['value'] = fieldValue;
		fieldNameValueMap = this.getCustomFieldNameValueMap(fieldNameValueMap);
		thisInstance.saveFieldValues(fieldNameValueMap).then(function(response) {
			var postSaveRecordDetails = response.result;
			detailViewValue.html(postSaveRecordDetails[fieldName].display_value);
			fieldElement.trigger(thisInstance.fieldUpdatedEvent,{'old':previousValue,'new':fieldValue});
			fieldnameElement.data('prevValue', ajaxEditNewValue);
			fieldElement.data('selectedValue', ajaxEditNewValue);
		},
		function(error) {
		});
	},
	
	registerAddMiscItems: function() {
		var thisInstance = this;
		var flatItemsTable = jQuery('#flatItemsTab');
		var qtyRateItemsTable = jQuery('#qtyRateItemsTab');
		var cratesTable = jQuery('#cratesTab');
		jQuery('#addFlatChargeItem').on('click', function() {
			var newRow = jQuery('.defaultFlatItem').clone(true, true);
			var sequence = thisInstance.flatItemsSequence++;
			newRow.removeClass('hide defaultFlatItem');
			newRow.attr('id', 'flatItemRow'+sequence);
			newRow.find('input.rowNumber').val(sequence);
			var name = "flatDescription";
			newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
			newRow.find('.fieldname[value="'+name+'"]').val(name+sequence);
			name = "flatCharge";
			newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
			newRow.find('.fieldname[value="'+name+'"]').val(name+sequence);
			name = "flatDiscounted";
			newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
			newRow.find('.fieldname[value="'+name+'"]').val(name+sequence);
			name = "flatDiscountPercent";
			newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
			newRow.find('.fieldname[value="'+name+'"]').val(name+sequence);
			newRow = newRow.appendTo(flatItemsTable);
		});
		jQuery('#addQtyRateChargeItem').on('click', function() {
			var newRow = jQuery('.defaultQtyRateItem').clone(true, true);
			var sequence = thisInstance.qtyRateItemsSequence++;
			newRow.removeClass('hide defaultQtyRateItem');
			newRow.attr('id', 'qtyRateItem'+sequence);
			newRow.find('input.rowNumber').val(sequence);
			var name = "qtyRateDescription";
			newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
			newRow.find('.fieldname[value="'+name+'"]').val(name+sequence);
			name = "qtyRateCharge";
			newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
			newRow.find('.fieldname[value="'+name+'"]').val(name+sequence);
			name = "qtyRateQty";
			newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
			newRow.find('.fieldname[value="'+name+'"]').val(name+sequence);
			name = "qtyRateDiscounted";
			newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
			newRow.find('.fieldname[value="'+name+'"]').val(name+sequence);
			name = "qtyRateDiscountPercent";
			newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
			newRow.find('.fieldname[value="'+name+'"]').val(name+sequence);
			newRow = newRow.appendTo(qtyRateItemsTable);
		});
		jQuery('#addCrate').on('click', function() {
			var newRow = jQuery('.defaultCrate').clone(true, true);
			var sequence = thisInstance.crateSequence++;
			newRow.removeClass('hide defaultCrate');
			newRow.attr('id', 'crate'+sequence);
			newRow.find('input.rowNumber').val(sequence);
			var name = "crateId";
			var idInt = 1;
			while(true) {
				var matchFound = false;
				jQuery('input[name*="'+name+'"]').each(function() {
					if(jQuery(this).closest('td').find('.value').html() == 'C-'+idInt) {
						matchFound = true;
						idInt++;
					}
				});
				if(!matchFound) {break;}
			}
			newRow.find('input[name="'+name+'"]').val('C-'+idInt);
			newRow.find('input[name="'+name+'"]').closest('td').find('.value').html('C-'+idInt);
			newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
			newRow.find('.fieldname[value="'+name+'"]').val(name+sequence);
			name = "crateDescription";
			newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
			newRow.find('.fieldname[value="'+name+'"]').val(name+sequence);
			name = "crateLength";
			newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
			newRow.find('.fieldname[value="'+name+'"]').val(name+sequence);
			name = "crateWidth";
			newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
			newRow.find('.fieldname[value="'+name+'"]').val(name+sequence);
			name = "crateHeight";
			newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
			newRow.find('.fieldname[value="'+name+'"]').val(name+sequence);
			name = "cratePack";
			newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
			newRow.find('.fieldname[value="'+name+'"]').val(name+sequence);
			name = "crateUnpack";
			newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
			newRow.find('.fieldname[value="'+name+'"]').val(name+sequence);
			name = "crateOTPack";
			newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
			newRow.find('.fieldname[value="'+name+'"]').val(name+sequence);
			name = "crateOTUnpack";
			newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
			newRow.find('.fieldname[value="'+name+'"]').val(name+sequence);
			name = "crateDiscountPercent";
			newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
			newRow.find('.fieldname[value="'+name+'"]').val(name+sequence);
			newRow = newRow.appendTo(cratesTable);
		});
	},
	
	hideZeroValServices : function() {
		var itemTable = jQuery('th:contains("Item Details")').closest('table').find('tbody');
		itemTable.find('tr').each(function() {
			if(parseFloat(jQuery(this).find('span.pull-right').html()) == 0) {
				jQuery(this).addClass('hide');
			}
			else {
				jQuery(this).removeClass('hide');
			}
		});
	},
	
	registerEvents: function(){
		this._super();
		this.registerInterstateRateEstimate();
		this.checkAccessorialFieldDisplay();
		this.registerAddMiscItems();
		this.registerDeleteMiscItemClickEvent();
		this.hideZeroValServices();
		this.registerWeightChangeEvent();
		this.initializeAddressAutofill('Quotes');
		this.registerReportsButton();
	}*/
});