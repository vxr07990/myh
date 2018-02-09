/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("Contracts_Edit_Js",{},{
	miscSequence : jQuery('input[name="numMisc"]').val(),
	fuelSequence : jQuery('input[name="numFuel"]').val(),
	flatRateAutoSequence : jQuery('input[name="numFlatRateAuto"]').val(),

	//Will have the mapping of address fields based on the modules
	addressFieldsMapping : {
		'Contacts': {
            'billing_address1': 'mailingstreet',
            'billing_pobox': 'mailingpobox',
            'billing_state': 'mailingstate',
            'billing_city': 'mailingcity',
            'billing_zip': 'mailingzip',
            'billing_country': 'mailingcountry',
		},
		'Accounts': {
			'billing_address1': 'bill_street',
			'billing_pobox': 'bill_pobox',
			'billing_state': 'bill_state',
			'billing_city': 'bill_city',
			'billing_zip': 'bill_zip',
			'billing_country': 'bill_country',
		}
	},

	registerRateTypeRadioButtons: function() {
		var radios = jQuery('input[name^="MiscFlatChargeOrQtyRate"]');
		radios.change(function(){
			//this.closest("tr").find()
			if(jQuery(this).val() == 0){
				jQuery(this).closest('tr').find('input[name^="MiscQty"]').attr('disabled', true);
			}
			else {
				jQuery(this).closest('tr').find('input[name^="MiscQty"]').attr('disabled', false);
			}
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

	registerDeleteMiscItemClickEvent : function() {
		var thisInstance = this;
		jQuery('.deleteMiscChargeButton').on('click', function(e) {
			var currentRow = jQuery(e.currentTarget).closest('tr');

			var lineItemId = currentRow.find("input[name^='MiscId'").val();
			if(lineItemId !='none' && lineItemId) {
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
			} else if(currentRow.find('input[name^="FuelId-"').val() != 'none' && currentRow.find('input[name^="FuelId-"').val()){
				lineItemId = currentRow.find('input[name^="FuelId-"').val();
				var dataURL = 'index.php?module=Contracts&action=DeleteFuelTable&lineItemId='+lineItemId;
				AppConnector.request(dataURL).then(
					function(data) {
						if(data.success) {
							currentRow.remove();
						}
					},
					function(error) {
					}
				);
			} else{
				currentRow.remove();
			}
		});
	},

	registerOwnershipChangeEvent : function() {
		var thisInstance = this;
		var selectTag = jQuery('select[name="assigned_user_id"]');
		selectTag.on('change', function() {
			var groupType = selectTag.find('option:selected').parent().attr('label');
			if(groupType == app.vtranslate('LBL_VANLINE_GROUPS')){
				if(jQuery('#assignedVanlinesTable').closest('table').hasClass('hide')){
					jQuery('#assignedVanlinesTable').closest('table').removeClass('hide');
				}
				var vanlineName = selectTag.find('option:selected').html();
				var vanlineTd = jQuery('.selectVanline').closest('td').next('td');
				vanlineTd.each(function(){
					if(jQuery(this).html() == vanlineName && jQuery('#assignedVanlinesTable tr').length <= 1){
						if(jQuery(this).prev('td').find('.selectVanline').prop('checked') == false){
							jQuery(this).prev('td').find('.selectVanline').prop('checked', true);
							jQuery('.assignVanlineSubmit').first().trigger('click');
						}
					}
				});
			} else{
				if(!jQuery('#assignedVanlinesTable').closest('table').hasClass('hide')){
					jQuery('#assignedVanlinesTable').closest('table').addClass('hide');
					thisInstance.removeVanline();
				}
			}
		});
		selectTag.trigger('change');
	},

	registerToggleFieldByCheckbox: function() {
		jQuery('input[name="free_fvp_allowed"]').on('change', function() {
			var tdElement = jQuery(this).closest('tr').find('td').last();
			var labelElement = jQuery(this).closest('tr').find('td.fieldLabel').last();
			if(jQuery(this).is(':checked') && tdElement.find('div').hasClass('hide')) {
				tdElement.find('div').removeClass('hide');
				labelElement.find('label').removeClass('hide');
			} else if(jQuery(this).is(':checked')) {
				//Do nothing
			} else if(tdElement.find('div').hasClass('hide')) {
				//Do nothing
			} else {
				tdElement.find('div').addClass('hide');
				labelElement.find('label').addClass('hide');
			}
		});
		//this one trigger makes the above run 731 times. on page load.
		//jQuery('input[type="checkbox"]').trigger('change');
	},

	searchModuleNames : function(params) {
		var aDeferred = jQuery.Deferred();

		if(typeof params.module == 'undefined') {
			params.module = app.getModuleName();
		}

		if(typeof params.action == 'undefined') {
			params.action = 'BasicAjax';
		}
		params.assignedTo = jQuery('select[name="assigned_user_id"]').val();
		AppConnector.request(params).then(
			function(data){
				aDeferred.resolve(data);
			},
			function(error){
				//TODO : Handle error
				aDeferred.reject();
			}
			)
		return aDeferred.promise();
	},

	registerViewAllButtons: function() {
		jQuery('.viewAllAgents').off('click').on('click', function() {
			var elementId = jQuery(this).attr('id');
			jQuery.colorbox({inline:true, width:'500px', height:'90%', left:'15%', top:'-5%', href:'#'+elementId+'Div', onClosed:function(){jQuery(document.body).css({overflow:'auto'});}, onComplete:function(){jQuery(document.body).css({overflow:'hidden'});}});
		});
	},

	registerApplyToAllAgents: function() {
		jQuery('.assignAllAgents').off('change').on('change', function() {
			var elementId = jQuery(this).attr('id');
			var divId = '#view'+elementId.substr(6)+'Div';
			var isChecked = jQuery(this).prop('checked');
			if(isChecked) {
				//console.dir('isChecked');
				jQuery(divId).find('input[type="checkbox"]').each(function() {
					jQuery(this).prop('checked', false);
					jQuery(this).trigger('click');
					jQuery(this).on('click', function() {return false;});
				});
			} else {
				//console.dir('notIsChecked');
				jQuery('input[id^="assignAgent"]').each(function(){
					jQuery(this).prop('checked', false);
					jQuery(this).siblings('input[type="hidden"]').val(0);
				});
				jQuery(divId).find('input[type="checkbox"]').each(function() {
					jQuery(this).off('click');
				});
			}
		});
	},

	registerAssignVanline: function() {
		jQuery('#assignVanline').on('click', function() {
			jQuery.colorbox({inline:true, width:'500px', height:'90%', left:'15%', top:'-5%', href:'#assignVanlinesDiv', onClosed:function(){jQuery(document.body).css({overflow:'auto'});}, onComplete:function(){jQuery(document.body).css({overflow:'hidden'});}});
		});
	},

	registerAssignVanlineSubmit: function() {
		var thisInstance = this;
		jQuery('.assignVanlineSubmit').on('click', function() {
			jQuery('#assignVanlinesDiv').find('.selectVanline').each(function() {
				var isChecked = jQuery(this).prop('checked');
				if(isChecked) {
					var id = jQuery(this).attr('id').substr(6);
					var row = jQuery(this).closest('tr');
					var html = "<td style='width:35%' class='vanline"+id.substr(7)+"'>"+row.find('.vanlineName').html()+"</td>";
					html += "<td style='width:8%;text-align:center' class='vanline"+id.substr(7)+"'><button type='button' class='viewAllAgents' id='view"+id+"Agents'>View All</button></td>"
					html += "<td style='width:5%;text-align:center' class='vanline"+id.substr(7)+"'><input type='hidden' name='assign"+id+"Agents' value='0' /><input type='checkbox' class='assignAllAgents' name='assign"+id+"Agents' id='assign"+id+"Agents' /></td>";

					var stateInput = jQuery('input[name="'+id+'State"]');
					if(stateInput.length) {
						stateInput.val('assigned');
						stateInput.attr('value', 'assigned');
					} else {
						jQuery('#assignedVanlinesTable').append("<input type='hidden' name='"+id+"State' value='assigned' />");
					}
					var newTable = jQuery('#assignedVanlinesTable').html() + html;
					jQuery('#assignedVanlinesTable').html(newTable);
					jQuery('input[id^="assignAgent"]').prop('checked', false);
				}
			});
			thisInstance.registerViewAllButtons();
			thisInstance.registerApplyToAllAgents();
			thisInstance.registerRemoveVanline();
			jQuery.colorbox.close();
		});
	},

	registerAgentAssignCheckbox: function(){
		//console.dir(jQuery('input[id^="assignAgent"]'));
		jQuery('input[id^="assignAgent"]').on('change', function(){
			//console.dir('clicked');
			if(jQuery(this).prop('checked')){
				jQuery(this).siblings('input[type="hidden"]').val('on');
			} else{
				jQuery(this).siblings('input[type="hidden"]').val(0);
			}
		});
	},

	registerRemoveVanline: function() {
		var thisInstance = this;
		jQuery('.deleteVanlineButton').off('click').on('click', function() {
			var vanlineId = jQuery(this).attr('id').substr(6);
			/*jQuery('.vanline'+vanlineId).empty();
			jQuery('.vanline'+vanlineId).removeClass().addClass('emptyRecord');*/
			jQuery('#Vanline'+vanlineId).removeClass('hide');
			jQuery('#assignVanline'+vanlineId).prop('checked', false);
			jQuery('input[name="Vanline'+vanlineId+'State"]').val('unassigned').attr('value', 'unassigned');
			//console.dir(jQuery('input[name="Vanline'+vanlineId+'State"]').val());
			jQuery(this).closest('tr').remove();
		});
	},

	removeVanline: function() {
		var thisInstance = this;
		var vanlineObject = jQuery('button[id^="viewVanline"]');
		var vanlineObjectId = vanlineObject.attr('id');
		var vanlineId = vanlineObjectId.substr(11);
		vanlineId = vanlineId.replace('Agents', '');
		jQuery('#Vanline'+vanlineId).removeClass('hide');
		jQuery('#assignVanline'+vanlineId).prop('checked', false);
		jQuery('input[name="Vanline'+vanlineId+'State"]').val('unassigned').attr('value', 'unassigned');
		jQuery('input[id^="assignAgent"]').prop('checked', false).siblings('input[type="hidden"]').val(0);
		vanlineObject.closest('tr').remove();
	},

	registerAnnualRateEvents : function(){
		thisInstance = this;
		//console.dir('initialize AnnRate');
		thisInstance.annualRatesIncrease = Contracts_AnnualRateIncrease_Js.getInstance();
		thisInstance.annualRatesIncrease.registerEvents();
	},

	registerBaseSpecificEvents : function() {
		thisInstance = this;
		try {
			thisInstance.baseSirva = Contracts_BaseSirva_Js.getInstance();
			thisInstance.baseSirva.registerEvents();
		} catch (errBS) {
			//do nothing.
			//It is acceptable that this did not load
		}
	},

    getPopUpParams : function(container) {
        params = Vtiger_Edit_Js.prototype.getPopUpParams.call(this, container);

        if(jQuery('select[name="assigned_user_id"]').length>0) {
            params['assignedTo'] = jQuery('select[name="assigned_user_id"]').val();
		}

		// check agentid select exists
		if(jQuery('select[name="agentid"]').length>0){
			params['agentId'] = jQuery('select[name="agentid"]').val();
		}

        // For SIRVA because trying to inherit it doesn't work.
        if($('[name="instance"]').val() == 'sirva') {
            params['move_type'] = $('[name="move_type"]').val();
		}

        if (jQuery('input[name="account_id"]').val() != 'undefined') {
            params['accountId'] = params['account_id'] = jQuery('input[name="account_id"]').val();
        }

        return params;
    },

	//pulled from vtiger's edit.js to override the return of the account's APN if it exists.
	openPopUp : function(e){
		var thisInstance = this;
		var parentElem = jQuery(e.target).closest('td');

		var params = this.getPopUpParams(parentElem);

		var isMultiple = false;
		if(params.multi_select) {
			isMultiple = true;
		}
		var sourceFieldElement = jQuery('input[class="sourceField"]',parentElem);

		var prePopupOpenEvent = jQuery.Event(Vtiger_Edit_Js.preReferencePopUpOpenEvent);
		sourceFieldElement.trigger(prePopupOpenEvent);

		if(prePopupOpenEvent.isDefaultPrevented()) {
			return ;
		}

		var popupInstance =Vtiger_Popup_Js.getInstance();
		popupInstance.show(params,function(data){
			var responseData = JSON.parse(data);
			var dataList = new Array();
			for(var id in responseData){
				var data = {
					'name' : responseData[id].name,
					'id' : id
				}
				//if (typeof responseData[id]['recordAPN'] !== 'undefined') {
				if (typeof responseData[id]['recordAPN'] !== 'undefined' && responseData[id]['recordAPN']) {
					data['apn'] = responseData[id]['recordAPN'];
				}
				dataList.push(data);
				if(!isMultiple) {
					thisInstance.setReferenceFieldValue(parentElem, data);
				}
			}

			if(isMultiple) {
				sourceFieldElement.trigger(Vtiger_Edit_Js.refrenceMultiSelectionEvent,{'data':dataList});
			}
			sourceFieldElement.trigger(Vtiger_Edit_Js.postReferenceSelectionEvent,{'data':responseData});
		});
	},

	setReferenceFieldValue : function(container, params) {
		var sourceField = container.find('input[class="sourceField"]').attr('name');
		var fieldElement = container.find('input[name="'+sourceField+'"]');
		var sourceFieldDisplay = sourceField+"_display";
		var fieldDisplayElement = container.find('input[name="'+sourceFieldDisplay+'"]');
		var popupReferenceModule = container.find('input[name="popupReferenceModule"]').val();

		var selectedName = params.name;
		var id = params.id;

		fieldElement.val(id)
		fieldDisplayElement.val(selectedName).attr('readonly',true);
		if(!params.suppress) {
            var data = {'source_module' : popupReferenceModule, 'record' : id, 'selectedName' : selectedName};
            if (typeof params.apn !== 'undefined') {
			if (sourceField == 'nat_account_no') {
					data['apn'] = params.apn;
					data['selectedName'] = params.apn;
				} else {
                    jQuery('input[name="nat_account_no"]').val(params.apn);
                }
            } else {
					//@TODO: figure out some way to indicate this is set but empty string.
					//data['apn'] = '<APN not set in this related account>';
					//data['selectedName'] = '<APN not set in this related account>';
				}
			fieldElement.trigger(Vtiger_Edit_Js.referenceSelectionEvent, data);
		}

		fieldDisplayElement.validationEngine('closePrompt',fieldDisplayElement);
		fieldElement.trigger('change');
	},

	registerAddFuelSurchargeButtons : function() {
		var thisInstance = this;
		var table = jQuery('table[name="FuelSurchargeTable"]').find('tbody');

		var buttons = jQuery('[id^="addFuelRow"]');

		var sequenceItem = thisInstance.fuelSequence;

		var defaultRowClass = 'defaultFuelSurchargeRow';
		var rowId = 'FuelSurchargeRow';
		var names = ['FuelTableId','FuelTableFromCost','FuelTableToCost','FuelTableRate','FuelTablePercent'];

		var addHandler = function() {
			var localContainer = jQuery(this).closest('tbody');
			var calledField = jQuery(this).attr('name');
			//var regExp = /\d+/g;
			//var serviceid = calledField.match(regExp);

			var newRow = localContainer.find('.'+defaultRowClass).clone(true,true);
			var sequenceNode = localContainer.find("input[name='numFuel']");
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

	registerChangeFuelSurchargeType : function() {
		var thisInstance = this;
		var selectTag = jQuery('select[name^="fuel_surcharge_type"]');
		selectTag.siblings('.chzn-container').find('.chzn-results').on('mouseup', function() {
			var fsValue = jQuery(this).closest('td').find('select').find('option:selected').val();
			if(fsValue == 'DOE - Fuel Percentage' || fsValue == 'DOE - Rate/Mile or Percentage') {
				jQuery('input[name^="FuelTablePercent"]').each(function() {
					jQuery(this).closest('td').removeClass('hide');
				});
				jQuery('input[name^="FuelTableRate"]').each(function() {
					jQuery(this).closest('td').css('width', '23.75%');
				});
				jQuery('input[name^="FuelTableToCost"]').each(function() {
					jQuery(this).closest('td').css('width', '23.75%');
				});
				jQuery('input[name^="FuelTableFromCost"]').each(function() {
					jQuery(this).closest('td').css('width', '23.75%');
				});
				jQuery('#fparentPercentage').removeClass('hide');
				jQuery('[id^="fparent"').css('width', '23.75%');
				jQuery('#fuelButtonRow').attr('colspan', 5);
				jQuery('table[name="FuelSurchargeTable"]').removeClass('hide');
			} else if(fsValue == 'DOE - Rate/CWT/Mile' || fsValue == 'DOE - Rate/Mile') {
				jQuery('input[name^="FuelTablePercent"]').each(function() {
					jQuery(this).closest('td').addClass('hide');
				});
				jQuery('input[name^="FuelTableRate"]').each(function() {
					jQuery(this).closest('td').css('width', '35%');
				});
				jQuery('input[name^="FuelTableToCost"]').each(function() {
					jQuery(this).closest('td').css('width', '30%');
				});
				jQuery('input[name^="FuelTableFromCost"]').each(function() {
					jQuery(this).closest('td').css('width', '30%');
				});
				jQuery('#fparentPercentage').addClass('hide');
				jQuery('#fparentRate').css('width', '35%');
				jQuery('#fparentToCost, #fparentFromCost').css('width', '30%');
				jQuery('#fuelButtonRow').attr('colspan', 5);
				jQuery('table[name="FuelSurchargeTable"]').removeClass('hide');
			} else if(fsValue == 'Static Fuel Percentage') {
				//jQuery('input[name="fuel_charge"]').closest('td').find('div').removeClass('hide');
				//jQuery('#fuelChargeLabel').removeClass('hide');
				jQuery('table[name="FuelSurchargeTable"]').addClass('hide');
			} else {
				//jQuery('input[name="fuel_charge"]').closest('td').find('div').addClass('hide');
				//jQuery('#fuelChargeLabel').addClass('hide');
				jQuery('table[name="FuelSurchargeTable"]').addClass('hide');
			}
		});
		selectTag.siblings('.chzn-container').find('.chzn-results').trigger('mouseup');
	},

	/**
	 * Function which will register event for Reference Fields Selection
	 */
	registerReferenceSelectionEvent : function(container) {
		var thisInstance = this;
		jQuery('input[name="billing_contact"]', container).on(Vtiger_Edit_Js.referenceSelectionEvent, function(e, data){
			thisInstance.referenceSelectionEventHandler(data, container);
		});
	},

	/**
	 * Reference Fields Selection Event Handler
	 * On Confirmation It will copy the address details
	 */
	referenceSelectionEventHandler :  function(data, container) {
		var thisInstance = this;
		if(data['source_module']=='Accounts'&&app.vtranslate('OVERWRITE_EXISTING_MSG1_V2')!=''){
			var message = app.vtranslate('OVERWRITE_EXISTING_MSG1_V2')+
				app.vtranslate('SINGLE_'+data['source_module'])+
				' ('+data['selectedName']+') '+app.vtranslate('OVERWRITE_EXISTING_MSG2_V2');
		}
		else{
			var message = app.vtranslate('OVERWRITE_EXISTING_MSG1')+app.vtranslate('SINGLE_'+data['source_module'])+' ('+data['selectedName']+') '+app.vtranslate('OVERWRITE_EXISTING_MSG2');
		}
		Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
			function(e) {
				thisInstance.copyAddressDetails(data, container);
			},
			function(error, err){
			});
	},

	/**
	 * Function which will copy the address details - without Confirmation
	 */
	copyAddressDetails : function(data, container) {
		var thisInstance = this;
		var sourceModule = data['source_module'];
		thisInstance.getRecordDetails(data).then(
			function(data){
				var response = data['result'];
				thisInstance.mapAddressDetails(thisInstance.addressFieldsMapping[sourceModule], response['data'], container);
			},
			function(error, err){

			});
	},

	/**
	 * Function which will map the address details of the selected record
	 */
	mapAddressDetails : function(addressDetails, result, container) {
		for(var key in addressDetails) {
			if(container.find('[name="'+key+'"]').length == 0) {
				var create = container.append("<input type='hidden' name='"+key+"'>");
			}
			container.find('[name="'+key+'"]').val(result[addressDetails[key]]);
			container.find('[name="'+key+'"]').trigger('change');
		}
	},

	registerToggleFieldForBaseTariff: function() {
		jQuery('input[name="local_tariff"]').on('change', function() {
			var popupRef = jQuery('input[name="related_tariff"]').closest('td').find('input[name="popupReferenceModule"]');
			if (jQuery(this).is(':checked')) {
				popupRef.val('Tariffs');
			} else {
				popupRef.val('TariffManager');
			}
		});
		jQuery('[name="related_tariff"]').on(Vtiger_Edit_Js.referenceDeSelectionEvent, function () {
			Vtiger_Edit_Js.setReadonly('move_type',false);
		});
	},

	registerToggleFieldForFVP: function() {
		hideFields = [
			'free_fvp_amount',
			'rate_per_100'
		];

		jQuery('input[name="free_fvp_allowed"]').on('change', function() {
			if(jQuery(this).is(':checked')) {
				//unhide the boxes
				for (var index in hideFields) {
					var name = hideFields[index];
					var relatedContractField = jQuery('input[name="' + name + '"]');
					relatedContractField.closest('td').children().each(function () {
						jQuery(this).removeClass('hide');
					});
					relatedContractField.closest('td').prev('td').children().each(function () {
						jQuery(this).removeClass('hide');
					});
					relatedContractField.closest('tr').removeClass('hide');
				}
			} else {
				//hide the boxes
				for (var index in hideFields) {
					var name = hideFields[index];
					var relatedContractField = jQuery('input[name="' + name + '"]');

					relatedContractField.closest('td').children().each(function () {
						jQuery(this).addClass('hide');
					});

					relatedContractField.closest('td').prev('td').children().each(function () {
						jQuery(this).addClass('hide');
					});

					var hideRow = 1;
					relatedContractField.closest('tr').children().each(function() {
						//@TODO: this may need checking if I am searching too generally or not enough.
						if (!jQuery(this).find('label, span, div').hasClass('hide') && jQuery(this).html()) {
                            hideRow = 0;
						}
					});

					if (hideRow == 1) {
						relatedContractField.closest('tr').addClass('hide');
					}
				}
			}
		});
	},

	/*
	fieldNames should be an array of field names you want to modify their readonly property
	lock is an boolean value true will lock them false will unlock them
	 */
	lockInputFieldsByName: function(fieldNames, lock) {
		jQuery.each(fieldNames, function(index, value){
			if(jQuery('[name="'+value+'"]').is('select')) {
				jQuery('[name="'+value+'"]').prop('disabled', lock).trigger('liszt:updated');
			} else {
				jQuery('[name="' + value + '"]').prop('readonly', lock);
			}
		});
	},

	toggleInternationalInformationBlock: function() {
		var thisInstance = this;
		var internationalInputs = ['international_origin_services', 'international_destination_services', 'estimate_approval_required', 'booking_commission_air', 'booking_commission_sea', 'us_flag_carrier_required'];

		if(jQuery('select[name="contract_status"]').val() != 'New') {
			thisInstance.lockInputFieldsByName(internationalInputs, true);
		}

		jQuery('select[name="contract_status"]').change(function() {
			if(jQuery(this).val() == 'New') {
				thisInstance.lockInputFieldsByName(internationalInputs, false);
			} else {
				thisInstance.lockInputFieldsByName(internationalInputs, true);
			}
		});
	},

	setStatusFieldDefault: function() {
		var record = jQuery('input[name="record"]').val();
		if(!record) {
			jQuery('select[name="contract_status"]').val('New').trigger('liszt:updated');
		}
	},

	/*
		Tariff information logic that hides and shows inputs based on the users actions
		If you want to add logic to this function, add the name of the field to index and set the value to the box you want to hide. Then add the name of the field to the onchange listener and your all set.
	 */
	fixedEffectiveDateLogic: function() {

		var effectiveInputs = {
			'fixed_eff_date':'effective_date',
			'fixed_irr':'irr_charge',
			'fuel_surcharge_type':'fuel_charge',
			'fixed_eac':'fixed_eac_percent',
			'discount_type':'waive_peak_rates',
		};

		// Check if any of these fields have changed
		jQuery('[name="fixed_eff_date"], [name="fixed_irr"], [name="fuel_surcharge_type"], [name="fixed_eac"], [name="discount_type"]').change(function() {
			var inputName = jQuery(this).attr('name');
			if(jQuery(this).is('select')) {
				if(inputName == 'fuel_surcharge_type') {
					if(jQuery(this).val() == 'Static Fuel Percentage') {
						jQuery('input[name="fuel_charge"]').closest('td').find('div').removeClass('hide');
						jQuery('#fuelChargeLabel').removeClass('hide');
					} else {
						jQuery('input[name="fuel_charge"]').closest('td').find('div').addClass('hide');
						jQuery('#fuelChargeLabel').addClass('hide');
					}
				}
				if(inputName == 'discount_type') {
					if(jQuery(this).val() != 'Peak/Non­Peak Discount') {
						jQuery('input[name="'+effectiveInputs['discount_type']+'"]').closest('td').find('div').removeClass('hide').closest('td').prev().find('label').removeClass('hide');
					} else {
						jQuery('input[name="'+effectiveInputs['discount_type']+'"]').closest('td').find('div').addClass('hide').closest('td').prev().find('label').addClass('hide');
					}
				}
			} else if(jQuery(this).is(':checked')) {
					jQuery('input[name="'+effectiveInputs[inputName]+'"]').closest('td').find('div').removeClass('hide').closest('td').prev().find('label').removeClass('hide');
                    Vtiger_Edit_Js.makeFieldMandatory(jQuery('input[name="'+effectiveInputs[inputName]+'"]'));
				} else {
					jQuery('input[name="'+effectiveInputs[inputName]+'"]').closest('td').find('div').addClass('hide').closest('td').prev().find('label').addClass('hide');
                Vtiger_Edit_Js.makeFieldNotMandatory(jQuery('input[name="'+effectiveInputs[inputName]+'"]'));
			}
		});

		jQuery.each(effectiveInputs, function(index, value){
			if(jQuery('[name="'+index+'"]').is('select')) {
				if(jQuery('[name="'+index+'"]').val() == 'Static Fuel Percentage' || index == 'discount_type') {
					//jQuery('input[name="'+value+'"]').closest('td').find('div').removeClass('hide');
					jQuery('input[name="'+value+'"]').closest('td').find('div').removeClass('hide').closest('td').prev().find('label').removeClass('hide');
					jQuery('#fuelChargeLabel').removeClass('hide');
				} else {
					//jQuery('input[name="'+value+'"]').closest('td').find('div').addClass('hide');
					jQuery('input[name="'+value+'"]').closest('td').find('div').addClass('hide').closest('td').prev().find('label').addClass('hide');
					jQuery('#fuelChargeLabel').addClass('hide');
				}
			} else if(jQuery('[name="'+index+'"]').is(':checked')) {
					jQuery('input[name="'+value+'"]').closest('td').find('div').removeClass('hide').closest('td').prev().find('label').removeClass('hide');
                Vtiger_Edit_Js.makeFieldMandatory(jQuery('input[name="'+value+'"]'));
				} else {
					jQuery('input[name="'+value+'"]').closest('td').find('div').addClass('hide').closest('td').prev().find('label').addClass('hide');
                Vtiger_Edit_Js.makeFieldNotMandatory(jQuery('input[name="'+value+'"]'));
			}
		});
	},

	toggleFlatRateAutoBlock: function() {
		//OT16162 don't consider contract_status.
	    /*
		var status = jQuery('select[name="contract_status"]').val();
		if(status == 'Requested' && jQuery('#Contracts_editView_fieldName_flat_rate_auto').is(':checked')) {
			jQuery('table[name="LBL_CONTRACTS_FLAT_RATE_AUTO"]').removeClass('hide');
			jQuery('table[name="LBL_CONTRACTS_ADDITIONAL_FLAT_RATE_AUTO"]').removeClass('hide');
		} else {
			jQuery('table[name="LBL_CONTRACTS_FLAT_RATE_AUTO"]').addClass('hide');
			jQuery('table[name="LBL_CONTRACTS_ADDITIONAL_FLAT_RATE_AUTO"]').addClass('hide');
		}
		*/

		//jQuery('[name="contract_status"], [name="flat_rate_auto"]').change(function() {
		jQuery('[name="flat_rate_auto"]').change(function() {
			//var status = jQuery('select[name="contract_status"]').val();
			//if(status == 'Requested' && jQuery('[name="flat_rate_auto"]').is(':checked')) {
			if(jQuery('[name="flat_rate_auto"]').is(':checked')) {
				jQuery('table[name="LBL_CONTRACTS_FLAT_RATE_AUTO"]').removeClass('hide');
				jQuery('table[name="LBL_CONTRACTS_ADDITIONAL_FLAT_RATE_AUTO"]').removeClass('hide');
			} else {
				jQuery('table[name="LBL_CONTRACTS_FLAT_RATE_AUTO"]').addClass('hide');
				jQuery('table[name="LBL_CONTRACTS_ADDITIONAL_FLAT_RATE_AUTO"]').addClass('hide');
			}
		}).trigger('change');
	},

	removeLineItemDiscountType: function() {
		jQuery('select[name="discount_type"] option').each(function() {
			if(jQuery(this).html() == 'Line Item Discount') {
				jQuery(this).remove();
			}
		});
		jQuery('select[name="discount_type"]').trigger('liszt:updated');
	},

	registerAddFlatRateAutoButtons : function() {
		var thisInstance = this;
		var table = jQuery('table[name="LBL_CONTRACTS_FLAT_RATE_AUTO"]').find('tbody');

		var buttons = jQuery('[id^="addFlatRateAutoRow"]');

		var defaultRowClass = 'defaultFlatRateAutoRow';
		var rowId = 'FlatRateAutoTableId';
		var names = ['FlatRateAutoTableId', 'FlatRateAutoTableFromMileage', 'FlatRateAutoTableToMileage', 'FlatRateAutoTableRate', 'FlatRateAutoTableDiscount'];

		var addHandler = function() {
			var localContainer = jQuery(this).closest('tbody');

			var newRow = localContainer.find('.'+defaultRowClass).clone(true,true);
			var sequenceNode = localContainer.find("input[name='numFlatRateAuto']");
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

	registerDeleteFlatRateAutoClickEvent : function() {
		jQuery('.deleteFlatRateAutoButton').on('click', function(e) {
			var currentRow = jQuery(e.currentTarget).closest('tr');

			var lineItemId = currentRow.find("input[name^='FlatRateAutoTableId'").val();
			if(lineItemId !='none' && lineItemId) {
				var dataURL = 'index.php?module=Contracts&action=DeleteFlatRateAutoItem&lineItemId='+lineItemId;
				AppConnector.request(dataURL).then(
					function(data) {
						if(data.success) {
							currentRow.remove();
						}
					},
					function(error) {
					}
				);
			} else{
				currentRow.remove();
			}
		});
	},

	registerValuationChange : function () {
		//Updating this list for Contracts without doing the table.
		if($('[name="instance"]').val() != 'sirva') {
			var selectValues = ['Select an Option',
							'Replacement Value Protection',
								'Carrier Based Liability'];
		jQuery('[name="valuation_deductible"]').find('option').each(function(){
			if (jQuery.inArray(jQuery(this).text(), selectValues) == -1){
			 	jQuery(this).remove();
			}
		})
		}

		jQuery('[name="valuation_deductible"] option[value=""]').text('Tariff Modification');
		jQuery('[name="valuation_deductible"]').trigger('liszt:updated');

		jQuery('[name="valuation_deductible"]').on('change', function () {
			var selectedValue = jQuery(this).find('option:selected').val();
			if(selectedValue == '' || $('[name="instance"]').val() == 'sirva')
			{
				jQuery('input[name="min_val_per_lb"]').closest('td').children().each(function () {
					jQuery(this).removeClass('hide');
				})
				jQuery('input[name="min_val_per_lb"]').closest('td').prev('td').children().each(function () {
					jQuery(this).removeClass('hide');
				})
				jQuery('input[name="free_frv"]').closest('tr').children().removeClass('hide');
				jQuery('input[name="maximum_rvp"]').closest('tr').children().addClass('hide');
				jQuery('input[name="rvp_per_1000"]').closest('tr').children().addClass('hide');
			}
			else if (selectedValue == 'Replacement Value Protection') {
				jQuery('input[name="min_val_per_lb"]').closest('td').children().each(function () {
					jQuery(this).removeClass('hide');
				})
				jQuery('input[name="min_val_per_lb"]').closest('td').prev('td').children().each(function () {
					jQuery(this).removeClass('hide');
				})
				jQuery('input[name="free_frv"]').closest('tr').children().addClass('hide');
				jQuery('input[name="free_frv"]').prop('checked', false);
				jQuery('input[name="free_frv_amount"]').val('');
				jQuery('input[name="maximum_rvp"]').closest('tr').children().removeClass('hide');
				jQuery('input[name="rvp_per_1000"]').closest('tr').children().removeClass('hide');
			}
			else {
				jQuery('input[name="min_val_per_lb"]').closest('td').children().each(function () {
					jQuery(this).addClass('hide');
				});
				jQuery('input[name="min_val_per_lb"]').closest('td').prev('td').children().each(function () {
					jQuery(this).addClass('hide');
				});
				jQuery('input[name="min_val_per_lb"]').val('');
				jQuery('input[name="free_frv"]').closest('tr').children().addClass('hide');
				jQuery('input[name="free_frv"]').prop('checked', false);
				jQuery('input[name="free_frv_amount"]').val('');
				jQuery('input[name="maximum_rvp"]').closest('tr').children().addClass('hide');
				jQuery('input[name="rvp_per_1000"]').closest('tr').children().addClass('hide');
			}
		});

		// jQuery('input:checkbox[name="valuation_discounted"]').on('click', function(){
		// 	if(jQuery(this).prop('checked') == false){
		// 		jQuery('input[name="valuation_discount_amount"]').closest('td').children().each(function(){
		// 			jQuery(this).addClass('hide');
		// 		});
		// 		jQuery('input[name="valuation_discount_amount"]').closest('td').prev('td').children().each(function(){
		// 			jQuery(this).addClass('hide');
		// 		});
		// 	} else{
		// 		jQuery('input[name="valuation_discount_amount"]').closest('td').children().each(function(){
		// 			jQuery(this).removeClass('hide');
		// 		});
		// 		jQuery('input[name="valuation_discount_amount"]').closest('td').prev('td').children().each(function(){
		// 			jQuery(this).removeClass('hide');
		// 		});
		// 	}
		// });
		// if(jQuery('input:checkbox[name="valuation_discounted"]').prop('checked') == false){
		// 	jQuery('input[name="valuation_discount_amount"]').closest('td').children().each(function(){
		// 		jQuery(this).addClass('hide');
		// 	})
		// 	jQuery('input[name="valuation_discount_amount"]').closest('td').prev('td').children().each(function(){
		// 		jQuery(this).addClass('hide');
		// 	});
		// }

		jQuery('[name="valuation_deductible"]').trigger('change');
	},

	registerEvents: function(){
		$('[name="use_current_rates"]').prop('readonly', true);
	},

    //set special validation for APN
    setAPN : function() {
        var apn = jQuery('input[name="billing_apn"]');
        apn.attr('data-validation-engine', 'validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation],custom[integer],minSize[8],maxSize[8]]');
    },

	registerEvents: function(){
		this._super();
        this.setAPN();
		this.initializeAddressAutofill('Contracts');
		this.registerToggleFieldByCheckbox();
		this.registerAddMiscItemButtons();
		this.registerDeleteMiscItemClickEvent();
		this.registerViewAllButtons();
		this.registerApplyToAllAgents();
		this.registerAssignVanline();
		this.registerAssignVanlineSubmit();
		this.registerRemoveVanline();
		this.registerOwnershipChangeEvent();
		this.registerRateTypeRadioButtons();
		this.registerAgentAssignCheckbox();
		this.registerAnnualRateEvents();
		this.registerAddFuelSurchargeButtons();
		this.registerChangeFuelSurchargeType();
		this.registerBaseSpecificEvents();
		this.registerReferenceSelectionEvent(jQuery('#EditView'));
		this.registerToggleFieldForBaseTariff();
		this.setStatusFieldDefault();
		this.toggleInternationalInformationBlock();
		this.fixedEffectiveDateLogic();
		this.toggleFlatRateAutoBlock();
		this.removeLineItemDiscountType();
		this.registerAddFlatRateAutoButtons();
        this.registerDeleteFlatRateAutoClickEvent();
		this.registerValuationChange();
		// this.setCurrentTariffCheckboxReadonly();
		jQuery('input[name="local_tariff"]').trigger('change');
		jQuery('.assignAllAgents').trigger('blur');
		try {
			sirvaContracts = Contracts_BaseSirva_Js.getInstance();
			sirvaContracts.registerReferenceSelectionEvent(jQuery('#EditView'));
		} catch (errMT) {
			//do nothing this is fine
		}
		this.registerToggleFieldForFVP();
        jQuery('input[name="free_fvp_allowed"]').trigger('change');
		var common = new Contracts_Common_Js();
		common.applyAllVisibilityRules(true);
		jQuery('[name="business_line[]"]').on('change',
		function() {
			var v = jQuery(this).val();
			if(!v) {
				v = [];
			}
			jQuery('[name="business_line"]').val(v.join(',')).trigger('change');
		});
	}
});
