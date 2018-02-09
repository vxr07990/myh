Vtiger_Detail_Js("Contracts_Detail_Js",{},{
	registerViewAllButtons: function() {
		jQuery('.viewAllAgents').off('click').on('click', function() {
			var elementId = jQuery(this).attr('id');
			jQuery.colorbox({inline:true, width:'500px', height:'90%', left:'15%', top:'-5%', href:'#'+elementId+'Div', onClosed:function(){jQuery(document.body).css({overflow:'auto'});}, onComplete:function(){jQuery(document.body).css({overflow:'hidden'});}});
		});
	},
	
	registerOwnershipChangeEvent : function() {
		var thisInstance = this;
		var selectTag = jQuery('select[name="assigned_user_id"]');
		var groupType = selectTag.find('option:selected').parent().attr('label');
		/*if(groupType == app.vtranslate('LBL_VANLINE_GROUPS')){
			if(jQuery('#assignedVanlinesTable').closest('table').hasClass('hide')){
				jQuery('#assignedVanlinesTable').closest('table').removeClass('hide');
			}
		} else{
			if(!jQuery('#assignedVanlinesTable').closest('table').hasClass('hide')){
				jQuery('#assignedVanlinesTable').closest('table').addClass('hide');
			}
		}*/
		selectTag.on('change', function() {
			groupType = selectTag.find('option:selected').parent().attr('label');
			if(groupType == app.vtranslate('LBL_VANLINE_GROUPS')){
				if(jQuery('#assignedVanlinesTable').closest('table').hasClass('hide')){
					jQuery('#assignedVanlinesTable').closest('table').removeClass('hide');
				}
			} else{
				if(!jQuery('#assignedVanlinesTable').closest('table').hasClass('hide')){
					jQuery('#assignedVanlinesTable').closest('table').addClass('hide');
				}
			}
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
                    //console.dir(fieldNameValueMap);
					if(fieldNameValueMap["field"].substr(0, 4) != "Misc") {
						thisInstance.saveFieldValues(fieldNameValueMap).then(function(response) {
							var postSaveRecordDetails = response.result;
							currentTdElement.progressIndicator({'mode':'hide'});
							detailViewValue.removeClass('hide');
							actionElement.show();
							detailViewValue.html(postSaveRecordDetails[fieldName].display_value);
							fieldElement.trigger(thisInstance.fieldUpdatedEvent,{'old':previousValue,'new':fieldValue});
							fieldnameElement.data('prevValue', ajaxEditNewValue);
							fieldElement.data('selectedValue', ajaxEditNewValue); 
							
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
					else {
						var miscItemId = fieldElement.closest('tr').find('input[name^="MiscId"]').val();
						if(miscItemId != ''){	
							var url = "index.php?module=Contracts&action=SaveAjaxMisc&id="+miscItemId+"&name="+fieldName+'&value='+fieldValue;
							AppConnector.request(url).then(
								function(data) {
									if(data.success) {
										currentTdElement.progressIndicator({'mode':'hide'});
										detailViewValue.removeClass('hide');
										if(fieldName.substr(0, 23) == "MiscFlatChargeOrQtyRate"){
											radioBtns = jQuery('[name="'+fieldName+'"]');
											radioBtns.each(function(){
												if(jQuery(this).is(':checked')){
													jQuery(this).closest('td').find('.value').html('Yes');	
												}
												else{
													jQuery(this).closest('td').find('.value').html('No');
												}
											});
										}
										else if(fieldName.substr(0, 14) == "MiscDiscounted"){
											if(jQuery('[name="'+fieldName+'"]').is(':checked')){
												jQuery('[name="'+fieldName+'"]').closest('td').find('.value').html('Yes');	
											}
											else{
												jQuery('[name="'+fieldName+'"]').closest('td').find('.value').html('No');	
											}
										}
										else{
											detailViewValue.html(fieldValue);
										}
										actionElement.show();
									}
								},
								function(error) {
									console.dir('error');
								}
							);
						}
						else if(fieldElement.closest('tr').find('input[name^="MiscDescription"]').val() != '' && fieldElement.closest('tr').find('input[name^="MiscRate"]').val() != '' && fieldElement.closest('tr').find('input[name^="MiscDescription"]').val() != '' && fieldElement.closest('tr').find('input[name^="MiscRate"]').val() != '' && fieldElement.closest('tr').find('input[name^="MiscQty"]').val() != ''){
							var url = "index.php?module=Contracts&action=SaveAjaxNewMisc" +
								"&Record="                  + jQuery("#recordId").val() +
								"&MiscFlatChargeOrQtyRate=" + jQuery(fieldElement).closest('tr').find('input[name^="MiscFlatChargeOrQtyRate"]:checked').val() +
								"&MiscDescription="         + jQuery(fieldElement).closest('tr').find('input[name^="MiscDescription"]').val() +
								"&MiscRate="                + jQuery(fieldElement).closest('tr').find('input[name^="MiscRate"]').val() +
								"&MiscQty="                 + jQuery(fieldElement).closest('tr').find('input[name^="MiscQty"]').val() +
								"&MiscDiscounted="          + jQuery(fieldElement).closest('tr').find('input[name^="MiscDiscounted"]').val() +
								"&MiscDiscount="            + jQuery(fieldElement).closest('tr').find('input[name^="MiscDiscount-"]').val();
							AppConnector.request(url).then(
								function(data) {
									jQuery(fieldElement).closest('tr').find('input[name^="MiscDescription"]').val(data.result);
								},
								function(error) {
									console.dir('error');
								}
							);
						}
						detailViewValue.html(fieldValue);
						actionElement.show();
						currentTdElement.progressIndicator({'mode':'hide'});
						detailViewValue.removeClass('hide');
					}
                }
			}

			jQuery(document).on('click','*', saveHandler);
	},
	
	registerDeleteMiscItemClickEvent : function() {
		var thisInstance = this;
		jQuery('.icon-trash').on('click', function(e) {
			var currentRow = jQuery(e.currentTarget).closest('tr');
			
			var lineItemId = currentRow.find("input[name^='MiscId'").val();
			if(lineItemId !='none') {
				var dataURL = 'index.php?module=Contracts&action=DeleteMiscItem&lineItemId='+lineItemId;

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
	
	registerAddMiscItemButtons : function() {
		var thisInstance = this;
		var table = jQuery('table[name="MiscItemsTable"]').find('tbody');
		
		var buttons = jQuery('[id^="addMiscItem"]');

		var sequenceItem = thisInstance.miscSequence;
		
		var defaultRowClass = 'defaultMiscItem';
		var rowId = 'MiscItemRow';
		var names = ['MiscId', 'MiscFlatChargeOrQtyRate','MiscDescription','MiscRate','MiscQty','MiscDiscounted','MiscDiscount'];

		var addHandler = function() {			
			
			var localContainer = jQuery(this).closest('tbody');
			var calledField = jQuery(this).attr('name');
			//var regExp = /\d+/g;
			//var serviceid = calledField.match(regExp);

			var newRow = localContainer.find('.'+defaultRowClass).clone(true,true);
			var sequenceNode = localContainer.find("input[name='numMisc']");
			var sequence = sequenceNode.val();
			sequence++;
			sequenceNode.val(sequence);

			newRow.removeClass('hide '+defaultRowClass);
			newRow.attr('id', rowId+sequence);
			for(var i=0; i<names.length; i++) {
				var name = names[i];
				newRow.find('input[name="'+name+'"]').prop('disabled', false);
				newRow.find('input[name="'+name+'"]').attr('name', name+'-'+sequence);
				newRow.find('input[name="'+name+'"]').closest('td').find('.fieldname').data('prevValue', '0');
				newRow.find('input[name="'+name+'-'+sequence+'"]').closest('td').find('.fieldname').val(name+'-'+sequence);
			}
			newRow = newRow.appendTo(localContainer.closest('table'));
		};
		
		buttons.on('click', addHandler);
	},
	
	registerEvents: function() {
		this._super();
		this.registerViewAllButtons();
		this.registerOwnershipChangeEvent();
		this.registerDeleteMiscItemClickEvent();
		this.registerAddMiscItemButtons();

		var common = new Contracts_Common_Js();
		common.applyAllVisibilityRules(false);

		if(jQuery('[name="instance"]').val() == 'graebel') {
			var val_ded = jQuery('[id$="_fieldValue_valuation_deductible"] span.value:first');
			if(val_ded.text().trim() == '')
			{
				val_ded.text('Tariff Modification');
			}
		}
	}
});