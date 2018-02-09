/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("Orders_Edit_Js", {

        }, {
	disabledDispatchBlocks: ['Long Distance Dispatch Information'],
	ValuationJS: {},
	effectiveTariffData: [],

    customRequirementsPassed: false,

    disabledDispatchBlocks: ['Long Distance Dispatch Information'],
	setDispatchFieldsReadOnly: function() {
		jQuery(document).find('.blockHeader:contains("Long Distance Dispatch Information")').closest('table').find('input').prop('disabled',true).prop('readonly','readonly');
		jQuery(document).find('.blockHeader:contains("Long Distance Dispatch Information")').closest('table').find('.clearReferenceSelection').hide();
		jQuery(document).find('.blockHeader:contains("Long Distance Dispatch Information")').closest('table').find('.relatedPopup').hide();
		jQuery(document).find('.blockHeader:contains("Long Distance Dispatch Information")').closest('table').find('.createReferenceRecord').hide();

	},
	updateTabIndexValues: function() {
		var tabindex = 1;
		jQuery('table').each(function() {
			if (!jQuery(this).hasClass('hide')) {
				if(jQuery(this).attr('name') == 'LBL_ORDERS_ORIGINADDRESS'){
					var row = 1;
					jQuery(this).find('input,select, textarea').each(function() {
						if (this.type != "hidden" && !jQuery(this).closest('td').hasClass('hide')) {
							if(row == 1){
								var $input = jQuery(this);
								$input.attr("tabindex", tabindex);
								tabindex++;
								row = 2;
                            } else {
								row = 1;
							}
						}
					});
					var row = 1;
					jQuery(this).find('input,select, textarea').each(function() {
						if (this.type != "hidden" && !jQuery(this).closest('td').hasClass('hide')) {
							if(row == 2){
								var $input = jQuery(this);
								$input.attr("tabindex", tabindex);
								tabindex++;
								row = 1;
                            } else {
								row = 2;
							}
						}
					});
                } else {
					jQuery(this).find('input,select, textarea').each(function() {
						if (this.type != "hidden" && !jQuery(this).closest('td').hasClass('hide')) {
							var $input = jQuery(this);
							$input.attr("tabindex", tabindex);
							tabindex++;
						}
					});
				}
			}
		});
	},

    registerRules: function(isEditView)
    {
        var rules = {
            setPORequired: {
                conditions: [
                    {
                        operator: 'is',
                        value: '1',
                        targetFields: [
                            {
                                name: 'orders_ponumber',
                                mandatory: true
                            }
                        ]
                    }
                ]
            }
        }
        this.applyVisibilityRules(rules, isEditView);
    },

    setPORequired: function(val) {
        var f = jQuery('input[name="setPORequired"]');
        if(f.length == 0)
        {
            jQuery('[name="instance"]').after('<input type="hidden" name="setPORequired">');
            f = jQuery('input[name="setPORequired"]');
        }
        f.val(val ? '1' : '0').trigger('change');
    },

	/**
	 * @description populate Account data when the ref selection is triggered on orders_account
	 */
	registerPopulateAccountDataOnChange : function() {
		var thisInstance = this;
        jQuery('input:hidden[name="orders_account"]').on(Vtiger_Edit_Js.referenceSelectionEvent + ' change ' + Vtiger_Edit_Js.referenceDeSelectionEvent, function() {
			if(jQuery('input[name="movehq"]').val()) {
				thisInstance.populateAccountData();
			} else {
				thisInstance.autoFillAccountDetail();
			}
		});
				},
	autoFillAccountDetail: function () {
		var accountid = jQuery('input[name="orders_account"]').val();
		if(accountid != undefined && accountid != ''){
			var params = {
				module: 'Orders',
				action: 'GetAccountDetail',
				accountid: accountid
			};
			AppConnector.request(params).then(
				function (data) {
					if(data){
						var response = data.result;
						jQuery('[name="national_account_number"]').val(response.national_account_number);
						jQuery('[name="account_address1"]').val(response.account_address1);
						jQuery('[name="account_address2"]').val(response.account_address2);
						jQuery('[name="account_city"]').val(response.account_city);
						jQuery('[name="account_state"]').val(response.account_state);
						jQuery('[name="account_zip_code"]').val(response.account_zip_code);
						jQuery('[name="account_country"]').val(response.account_country);
						if(response.account_contract != undefined){
							jQuery('[name="account_contract"]').val(response.account_contract);
							jQuery('[name="account_contract_display"]').val(response.account_contract_display);
						}
					}
				}
			);
			}
	},
	/**
	 * @description populate orders page with account's address data based on ajax call.
	 */
	populateAccountData : function(sourceRecord, onlyBusinessLineUpdate) {
		var thisInstance = this;

		if(typeof sourceRecord != 'undefined')
		{
			var accountid = sourceRecord;
        } else
		{
			var accountid = jQuery('input[name="orders_account"]').val();
		}
		if(!accountid || accountid <= 0) {
			Vtiger_Edit_Js.setPicklistOptions('business_line');
            this.setPORequired(false);
			return;
		}
		var lineField = jQuery('select[name="business_line2"]');
		if(lineField.length == 0)
		{
			lineField = jQuery('select[name="business_line"]');
		}
		var business_line = lineField.val();
		//console.log(business_line);
		if(typeof accountid == 'undefined')
		{
			accountid = '';
		}
		if(typeof business_line == 'undefined')
		{
			business_line = '';
		}
		var url = 'index.php?module=Orders&action=PopulateAccountData&accountid=' + accountid + '&business_line=' + business_line;

		AppConnector.request(url).then(
			function(data) {
				if (data.success) {
                        thisInstance.setPORequired(data.result.po_required > 0);
					addresses = data.result.addresses;
						var instance = jQuery('input[name="instance"]').val();
						if(instance == 'graebel') {
							// update business lines
							if (Object.keys(data.result.available_business_lines).length > 0) {
								Vtiger_Edit_Js.setPicklistOptions('business_line', data.result.available_business_lines);
							}
							else {
								Vtiger_Edit_Js.setPicklistOptions('business_line');
							}
						}
						if(Object.keys(data.result.avail_addr_desc).length > 0){
							Vtiger_Edit_Js.setPicklistOptions('bill_addrdesc', data.result.avail_addr_desc);
						}
						else {
							Vtiger_Edit_Js.setPicklistOptions('bill_addrdesc');
						}
							if(typeof onlyBusinessLineUpdate != 'undefined')
							{
								return;
							}
					//clean fields
						jQuery('[name="commodity"]').val("").prop('readonly', false).trigger('liszt:updated');
						jQuery('[name="invoice_format"]').val("").prop('readonly', false).trigger('liszt:updated');
						jQuery('[name="invoice_pkg_format"]').val("").prop('readonly', false).trigger('liszt:updated');
						jQuery('[name="invoice_document_format"]').val("").prop('readonly', false).trigger('liszt:updated');
						jQuery('[name="invoice_delivery_format"]').val("").prop('readonly', false).trigger('liszt:updated');
						jQuery('[name="invoice_finance_charge"]').val("").prop('readonly', false);
						jQuery('[name="payment_terms"]').val("").prop('readonly', false);
						jQuery('input[name="bill_city"]').val("").prop('readonly', false);
						jQuery('input[name="bill_street"]').val("").prop('readonly', false);
						jQuery('input[name="bill_country"]').val("").prop('readonly', false);
						jQuery('input[name="bill_state"]').val("").prop('readonly', false);
						jQuery('input[name="bill_code"]').val("").prop('readonly', false);
                        jQuery('select[name="bill_addrdesc"]').val('').prop('readonly', false).trigger('liszt:updated');
						jQuery('input[name="bill_pobox"]').val('');
						jQuery('input[name="bill_company"]').val('');

						if(instance == 'graebel') {
							var total = jQuery('input[name="orders_etotal"]').val();
							var hold = data.result.credit['credit_hold'];
							var overRide = data.result.credit['credit_hold_override'];

							if(hold == '1' && overRide == '0') {
								bootbox.alert('This account has a credit hold placed on it');
								jQuery('button[type="submit"]').prop('disabled', true);
								return false;
							} else {
								jQuery('button[type="submit"]').prop('disabled', false);
							}

							var a = thisInstance.populateInvoiceSettings(data.result);
							a.done(function() {
								thisInstance.populateAddressDetails(data.result);
							});
						} else { //If not graebel
						jQuery('input[name="bill_city"]').val(data.result.billing['city']);
						jQuery('input[name="bill_street"]').val(data.result.billing['street']);
						jQuery('input[name="bill_country"]').val(data.result.billing['country']);
						jQuery('input[name="bill_state"]').val(data.result.billing['state']);
						jQuery('input[name="bill_code"]').val(data.result.billing['zip']);
						jQuery('input[name="bill_pobox"]').val(data.result.billing['pobox']);
					}
				}
			},
			function(err) {

			}
		);
	},
	//MM HERE OT 1599, hijacking populating contract data from Estimates

	populateContractData : function() {
		var thisInstance = this;

		if (jQuery('input:hidden[name="account_contract"]').length) {
			jQuery('input:hidden[name="account_contract"]').on(Vtiger_Edit_Js.referenceSelectionEvent, function() {
				var message = app.vtranslate('JS_MSG_POPULATE_CONTRACT');
				Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
					function(e) {
						//they pressed yes
						var id = jQuery('input[name="account_contract"]').val();
						var url = 'index.php?module=Orders&action=PopulateContractData&contract_id=' + id;
						AppConnector.request(url).then(
							function(data) {
								if (data.success) {
									//clear any existing contract items in order to use this contract's items
									thisInstance.unbindContractItems(false);
									//set the billing address
									jQuery('input[name="bill_city"]').val(data.result['city']).attr('readonly','readonly');
									jQuery('input[name="bill_street"]').val(data.result['address1']).attr('readonly','readonly');
									jQuery('input[name="bill_country"]').val(data.result['country']).attr('readonly','readonly');
									jQuery('input[name="bill_state"]').val(data.result['state']).attr('readonly','readonly');
									jQuery('input[name="bill_code"]').val(data.result['zip']).attr('readonly','readonly');
									jQuery('input[name="bill_pobox"]').val(data.result['pobox']).attr('readonly','readonly');
									if (typeof data.result['additional_valuation'] !== 'undefined') { //added check to stop setting these to nothing.
										var contractAdditionalValuation = parseFloat(data.result['additional_valuation']).toFixed(2);
										if (!isNaN(contractAdditionalValuation)) {
											jQuery('input[name="additional_valuation"]').val(contractAdditionalValuation).prop('readonly', true);
										}
									}
									if(data.result['sit_distribution_discount']>0) {
										jQuery('input[name="sit_distribution_discount"]').val(data.result['sit_distribution_discount']).attr('readonly', 'readonly');
									}
									if(data.result['bottom_line_distribution_discount']>0) {
										jQuery('input[name="bottom_line_distribution_discount"]').val(data.result['bottom_line_distribution_discount']).attr('readonly', 'readonly');
									}

									if(data.result['bottom_line_disc']>0) {
										jQuery('input[name="orders_discount"]').val(data.result['bottom_line_disc']).attr('readonly', 'readonly');
									}

									if(data.result['min_val_per_lb'] > 0)
									{
										contract_MinValPerLb = Number(data.result['min_val_per_lb']);
									}

//									if(data.result['business_line']) {
//										jQuery('[name="business_line"]').val(data.result['business_line']).trigger('liszt:updated').trigger('change');
//									}

									if (data.result['contract_no'] !== 'undefined' && data.result['contract_no']) {
										jQuery('input[name="account_contract_display"]').val(data.result['contract_no']).attr('readonly', 'readonly');
									}
									console.dir('Contract Info Populating');
									 // console.dir('min weight: ' + data.result['min_weight']);
									 if(data.result['min_weight'] || jQuery('[name="weight"]').val()<data.result['min_weight']) {
									 jQuery('input[name="orders_minweight"]').val(data.result['min_weight']);
									 }

									 //Tariff needs to be set first as valuation_deductible depends on it.
									if(typeof data.result['effective_tariff'] != 'undefined' && data.result['effective_tariff'] != null){

										var contract_tariff = parseInt(data.result['effective_tariff_id']);
										jQuery('select[name="tariff_id"]').val(contract_tariff);
										jQuery('select[name="tariff_id"]').trigger('liszt:updated').trigger('change');
									}


									if(typeof data.result['valuation_deductible'] != 'undefined' && data.result['valuation_deductible'] != null && data.result['valuation_deductible'].length > 0){
										//data.result['valuation_deductible'] = app.vtranslate(data.result['valuation_deductible']);
										var valDedField = jQuery('select[name="valuation_deductible"]');
										var contractValuationType = data.result['valuation_deductible'];
										valDedField.val(contractValuationType);
										valDedField.trigger('liszt:updated').trigger('change');
										Orders_Edit_Js.setReadonly("valuation_deductible", true);
										// workingDiv.find('li.result-selected').removeClass('result-selected');
										// workingDiv.find('li:contains("'+data.result['valuation_deductible']+'")').addClass('result-selected');
										// workingDiv.find('span').html(data.result['valuation_deductible']).attr('readonly','readonly');
										// workingDiv.find('li:contains("'+app.vtranslate(data.result['valuation_deductible'])+'")').addClass('result-selected');
										// workingDiv.find('span').html(app.vtranslate(data.result['valuation_deductible'])).attr('readonly','readonly');
										// jQuery('select[name="valuation_deductible"]').data('selectedValue', data.result['valuation_deductible']).attr('readonly','readonly');
										// jQuery('select[name="valuation_deductible"]').find('option').prop('selected', false).attr('readonly','readonly');
										// jQuery('select[name="valuation_deductible"]').find('option[value="'+data.result['valuation_deductible']+'"]').prop('selected', true).attr('readonly','readonly');
										// jQuery('select[name="valuation_deductible"]').trigger('liszt:updated').trigger('change');
									}


								} else {
									console.dir('error getting contract');
								}
							},
							function(err) {
								console.dir('overall error getting contract');
							}
						);
					},
					function(error, err) {
						//they pressed no, clear the contract selection
						//thisInstance.removeContractRow();
						thisInstance.unbindContractItems(false);
					}
				);

			})
		}
	},
	removeAllContractMiscItems : function () {
		var thisInstance = this;
		//dump the enforced line items
		jQuery('.enforced').each(function() {
			var xval = jQuery(this).val();
			if (xval == 1) {
				var currentRow = jQuery(this).closest('tr');
				rowId = currentRow.attr('id');
				var regExp = /\d+/g;
				var rowNumbers = rowId.match(regExp);
				var serviceid = rowNumbers[0];
				var rowNum = rowNumbers[1];
				thisInstance.DeleteMiscItem(currentRow, serviceid, rowNum);
			}
		});

		// reset the item sequences now those rows are removed,
		// thisInstance.flatItemsSequence = jQuery('.flatItemRow').length;
		// thisInstance.qtyRateItemsSequence = jQuery('.qtyRateItemRow').length;

		// don't need to hide the tables, because the trigger on effective_tariff will correct the tables.
		/*
		 //hide the tables if needed
		 if (thisInstance.flatItemsSequence == 1) {
		 var flatItemsTable = jQuery('#flatItemsTab');
		 flatItemsTable.addClass('hide');
		 flatItemsTable.closest('table').addClass('hide');
		 }

		 if (thisInstance.qtyRateItemsSequence == 1) {
		 var qtyRateItemsTable = jQuery('#qtyRateItemsTab');
		 qtyRateItemsTable.addClass('hide');
		 qtyRateItemsTable.closest('table').addClass('hide');
		 }
		 */
	},
	DeleteMiscItem : function(currentRow, serviceid, rowNum) {
		var newInput = jQuery('<input>').attr({
			type: 'hidden',
			class: 'hide',
			name: 'deleteRow' + serviceid + '-' + rowNum
		});
		currentRow.closest('table').append(newInput);

		var lineItemId = currentRow.find('.lineItemId').val();
        if (!lineItemId)
            lineItemId = currentRow.find('input[name^="vehicleID"]').val();//Second check for vehicles
		//Third check for locals
		var extraParams = '';
		if (currentRow.attr('class') == 'localCrateRow') {
			lineItemId = currentRow.find('input[name^="crateID"]').prop('name');
			record = jQuery('input[name="record"]').val();
			extraParams = '&estimateid='+record;
		}

		if (lineItemId) {
			var dataURL = 'index.php?module=Estimates&action=DeleteMiscItem&rowType=' + currentRow.attr('class') + '&lineItemId=' + lineItemId+extraParams;

			AppConnector.request(dataURL).then(
				function (data) {
					if (data.success) {
						currentRow.remove();
					}
				},
				function (error) {
				}
			);
		} else {
			currentRow.remove();
		}
	},



	// showContractRow : function() {
	// 	jQuery('#contract_row').removeClass('hide');
	// 	jQuery('input[name="parent_contract"]').closest('td').children().removeClass('hide');
	// 	jQuery('input[name="parent_contract"]').closest('td').prev('td').children().removeClass('hide');
	// 	jQuery('input[name="nat_account_no"]').closest('td').children().removeClass('hide');
	// 	jQuery('input[name="nat_account_no"]').closest('td').prev('td').children().removeClass('hide');
	// },

	// removeContractRow : function() {
	// 	//console.dir("Remove Contract information")
	// 	//hide the contract row and remove the values
	// 	jQuery('#contract_row').addClass('hide');
	// 	jQuery('input[name="parent_contract"]').val('');
	// 	jQuery('input[name="parent_contract"]').closest('td').children().addClass('hide');
	// 	jQuery('input[name="parent_contract"]').closest('td').prev('td').children().addClass('hide');
	// 	jQuery('input[name="nat_account_no"]').val('');
	// 	jQuery('input[name="nat_account_no"]').closest('td').children().addClass('hide');
	// 	jQuery('input[name="nat_account_no"]').closest('td').prev('td').children().addClass('hide');
	// },

	unbindContractItems : function(nullFields) {
		//console.dir("start remove contract row");
		this.removeAllContractMiscItems();

		//remove lockdowns on all the other fields.
		var fieldsToChange = [
			'bill_city',
			'bill_street',
			'bill_country',
			'bill_state',
			'bill_code',
			'bill_pobox',
			'parent_contract',
			'nat_account_no',
			'interstate_effective_date',
			'sit_origin_fuel_percent',
			'sit_dest_fuel_percent',
			'irr_charge',
			'linehaul_disc',
			'accessorial_disc',
			'packing_disc',
			'sit_disc',
			'bottom_line_discount',
			'additional_valuation',
			'valuation_amount'

		];
		for (var i=0; i < fieldsToChange.length; i++) {
			if (nullFields) {
				jQuery('input[name="' + fieldsToChange[i] + '"]').val('').attr('readonly', false).removeAttr('disabled');
			} else {
				jQuery('input[name="' + fieldsToChange[i] + '"]').attr('readonly', false).removeAttr('disabled');
			}
		}
		//jQuery('input[name="irr_charge"]').attr('readonly', true).val('4');

		//Alf found that this is probably an error.
		//clear the account and contact fields using it's click event! THANK YOU Ryan!
		//jQuery('.Estimates_editView_fieldName_account_id_clear').trigger('click');
		//jQuery('.Orders_editView_fieldName_account_contract_clear').trigger('click');

		//only remove the valuation and effective tariff IF we are just removing the contract,
		//otherwise leave them alone.
		if (nullFields) {
			contract_MinValPerLb = undefined;
			//Reset the Valuation to "Select an Option"
			//I'm not hardcoding a default value here...
			//Reset the Effective Tariff to "Select an Option"
			Orders_Edit_Js.setReadonly("valuation_deductible", false);
			jQuery('select[name="valuation_deductible"]').find('option').each(function () {
				jQuery(this).prop('selected', false);
			});

			var workingDiv = jQuery('select[name="valuation_deductible"]').siblings().first();
			workingDiv.find('li.result-selected').removeClass('result-selected');
			workingDiv.find('li:contains("Select an Option")').addClass('result-selected');
			jQuery('select[name="valuation_deductible"]').data('selectedValue', 'Select an Option');
			jQuery('select[name="valuation_deductible"]').trigger('liszt:updated');
			jQuery('select[name="valuation_deductible"]').trigger('change');

			//Reset the Effective Tariff to "Select an Option"
			jQuery('select[name="tariff_id"]').find('option').each(function () {
				jQuery(this).prop('selected', false);
			});

			var workingDiv = jQuery('select[name="tariff_id"]').siblings().first();
			workingDiv.find('li.result-selected').removeClass('result-selected');
			workingDiv.find('li:contains("Select an Option")').addClass('result-selected');
			jQuery('select[name="tariff_id"]').data('selectedValue', 'Select an Option');
			jQuery('select[name="tariff_id"]').trigger('liszt:updated');
			jQuery('select[name="tariff_id"]').trigger('change');

			//reset the Local Tariff effective to "Select an option"
			jQuery('select[name="local_tariff"]').find('option').each(function () {
				jQuery(this).prop('selected', false);
			});

			var workingDiv = jQuery('select[name="local_tariff"]').siblings().first();
			workingDiv.find('li.result-selected').removeClass('result-selected');
			workingDiv.find('li:contains("Select an Option")').addClass('result-selected');
			jQuery('select[name="local_tariff"]').data('selectedValue', 'Select an Option');
			jQuery('select[name="local_tariff"]').trigger('liszt:updated');
			jQuery('select[name="local_tariff"]').trigger('change');
		}
	},
	//MM HERE end OT1599 populate contract data

	populateAddressDetails: function(data) {
		// addresses = data.addresses;
		if(addresses && addresses.length > 0) {
			if (addresses.length > 1) {
				var table = '<table id="address-options" class="table table-bordered table-hover">';
				table += '<thead><tr>';
				table += '<td></td>';
				table += '<td><b>Business Line</b></td>';
				table += '<td><b>Description</b></td>';
				table += '<td><b>Company</b></td>';
				table += '<td><b>Address</b></td>';
				table += '<td><b>City</b></td>';
				table +=  '<td><b>State</b></td>';
				table +=  '<td><b>Zip</b></td>';
				table +=  '<td><b>Country</b></td>';
				table += '</tr></thead><tbody>';
				var count = 0;
				$.each(addresses, function (index, value) {
					table += '<tr>';
					if(count==0) {
						table += '<td class="text-center"><input type="radio" style="display: block; margin: 0 auto;" value="'+index+'" name="address_option" checked></td>';
					} else {
						table += '<td class="text-center"><input type="radio" style="display: block; margin: 0 auto;" name="address_option" value="'+index+'"></td>';
					}
					table += '<td>'+value.commodity+'</td>';
					table += '<td>'+value.address_desc+'</td>';
					table += '<td>'+value.company+'</td>';
					table += '<td>'+value.address1+'</td>';
					table += '<td>'+value.city+'</td>';
					table +=  '<td>'+value.state+'</td>';
					table +=  '<td>'+value.zip+'</td>';
					table +=  '<td>'+value.country+'</td>';
					table += '<tr>';
					count++;
				});
				table += '</tbody></table>';

				bootbox.confirm(
					{
						message: table,
						title: 'Multiple Billing Options for this Account Select One',
						closeButton: false,
						className: 'bootbox-invoice-settings',
						buttons: {
							confirm: {
								label: 'Select'
							}
						},
						callback: function() {
							var index = jQuery('input[name="address_option"]:checked').val();
							Vtiger_Edit_Js.setPicklistOptions('bill_addrdesc', data.avail_addr_desc);
							jQuery('select[name="bill_addrdesc"]').val(addresses[index]['address_desc']).trigger('liszt:updated');
							jQuery('input[name="bill_company"]').val(addresses[index]['company']);
							jQuery('input[name="bill_city"]').val(addresses[index]['city']);
							jQuery('input[name="bill_street"]').val(addresses[index]['address1']);
							jQuery('input[name="bill_pobox"]').val(addresses[index]['address2']);
							jQuery('input[name="bill_country"]').val(addresses[index]['country']);
							jQuery('input[name="bill_state"]').val(addresses[index]['state']);
							jQuery('input[name="bill_code"]').val(addresses[index]['zip']);

							//commodity - invoice_format - invoice_pkg_format - invoice_document_format - invoice_delivery_format - invoice_finance_charge - payment_terms

						}
					});

			} else { //if only one address use it and don't ask
                //@NOTE: Unsure what will happen to the array assignment if this is undefined...
                if (typeof data.avail_addr_desc != 'undefined') {
                    var address_desc_picklist_array = data.avail_addr_desc;
					Vtiger_Edit_Js.setPicklistOptions('bill_addrdesc', address_desc_picklist_array);
                }
				jQuery('select[name="bill_addrdesc"]').val(addresses[0]['address_desc']).trigger('liszt:updated');
				jQuery('input[name="bill_company"]').val(addresses[0]['company']);
				jQuery('input[name="bill_city"]').val(addresses[0]['city']);
				jQuery('input[name="bill_street"]').val(addresses[0]['address1']);
				jQuery('input[name="bill_pobox"]').val(addresses[0]['address2']);
				jQuery('input[name="bill_country"]').val(addresses[0]['country']);
				jQuery('input[name="bill_state"]').val(addresses[0]['state']);
				jQuery('input[name="bill_code"]').val(addresses[0]['zip']);
			}
		}
	},
	populateInvoiceSettings: function(data) {
		var a = jQuery.Deferred();
		if(data.invoice && data.invoice.length > 0) {
			var thisInstance = this;
			if(data.invoice.length>1) {
				var table = '<table id="invoice-options" class="table table-bordered table-hover">';
				table += '<thead><tr>';
				table += '<td></td>';
				table += '<td><b>Business Line</b></td>';
				table += '<td><b>Invoice Template</b></td>';
				table +=  '<td><b>Invoice Packet</b></td>';
				table +=  '<td><b>Document Format</b></td>';
				table +=  '<td><b>Invoice Delivery</b></td>';
				table +=  '<td><b>Finance Charge</b></td>';
				table +=  '<td><b>Payment Terms</b></td>';
				table += '</tr></thead><tbody>';
				var count = 0;
				$.each(data.invoice, function (index, value) {
					table += '<tr>';
					if(count==0) {
						table += '<td class="text-center"><input type="radio" value="'+index+'" name="invoice" checked></td>';
					} else {
						table += '<td class="text-center"><input type="radio" name="invoice" value="'+index+'"></td>';
					}
					table += '<td>'+value.commodity+'</td>';
					table += '<td>'+value.invoice_template+'</td>';
					table +=  '<td>'+value.invoice_packet+'</td>';
					table +=  '<td>'+value.document_format+'</td>';
					table +=  '<td>'+value.invoice_delivery+'</td>';
					table +=  '<td>'+value.finance_charge+'</td>';
					table +=  '<td>'+value.payment_terms+'</td>';
					table += '<tr>';
					count++;
				});
				table += '</tbody></table>';

				bootbox.confirm(
					{
						message: table,
						title: 'Multiple Invoice Settings Options for this Account Select One',
						closeButton: false,
						className: 'bootbox-invoice-settings',
						buttons: {
							confirm: {
								label: 'Select'
							}
						},
						callback: function() {
							var index = jQuery('input[name="invoice"]:checked').val();
							jQuery('[name="commodity"]').val(data.invoice[index].commodity).trigger('liszt:updated');
							jQuery('[name="invoice_format"]').val(data.invoice[index].invoice_template).trigger('liszt:updated');
							jQuery('[name="invoice_pkg_format"]').val(data.invoice[index].invoice_packet).trigger('liszt:updated');
							jQuery('[name="invoice_document_format"]').val(data.invoice[index].document_format).trigger('liszt:updated');
							jQuery('[name="invoice_delivery_format"]').val(data.invoice[index].invoice_delivery).trigger('liszt:updated');
							jQuery('[name="invoice_finance_charge"]').val(data.invoice[index].finance_charge);
							jQuery('[name="payment_terms"]').val(data.invoice[index].payment_terms);
							a.resolve();
						}
					}
				);
			} else if(data.invoice.length>0) {
				jQuery('[name="commodity"]').val(data.invoice[0].commodity).trigger('liszt:updated');
				jQuery('[name="invoice_format"]').val(data.invoice[0].invoice_template).trigger('liszt:updated');
				jQuery('[name="invoice_pkg_format"]').val(data.invoice[0].invoice_packet).trigger('liszt:updated');
				jQuery('[name="invoice_document_format"]').val(data.invoice[0].document_format).trigger('liszt:updated');
				jQuery('[name="invoice_delivery_format"]').val(data.invoice[0].invoice_delivery).trigger('liszt:updated');
				jQuery('[name="invoice_finance_charge"]').val(data.invoice[0].finance_charge);
				jQuery('[name="payment_terms"]').val(data.invoice[0].payment_terms);
				a.resolve();
			} else {
				a.resolve();
			}
		} else {
			a.resolve();
		}
		return a;
	},
	registerBusinessLineChangeEvent : function() {
        participantInstance = ParticipatingAgents_Edit_Js.getInstance();
		var thisInstance = this;
		var bl = jQuery('select[name="business_line"]');
		bl.data('prev-value', bl.val());
		bl.on('change', function (){
			var instance = jQuery('input[name="instance"]').val();
			if(jQuery('[name="movehq"]').val() && jQuery(this).val() != jQuery(this).data('prev-value')) {
				thisInstance.populateAccountData();
			}
			jQuery(this).data('prev-value', jQuery(this).val());
            thisInstance.performLastCreditCheck();
            if(instance == 'graebel') {
                var carrierRow = participantInstance.findParticipantRow('Carrier');
                if (carrierRow && carrierRow.attr('data-state') == 'auto-set' && jQuery('input:hidden[name="instance"]').val() == 'graebel') {
                    businessLineValue = jQuery('select[name="business_line"]').find('option:selected').html();
                    participantInstance.setDefaultCarrier(businessLineValue);
				}
			}
			thisInstance.retrieveTariffList();
		});
	},

	registerBillingAddressDescChangeEvent : function() {
		var thisInstance = this;
		var ad = jQuery('select[name="bill_addrdesc"]');
		ad.on('change', function() {
			if(typeof addresses == 'undefined'){
				return;
			}
			var currentVal = ad.val();
			for(i = 0; i < addresses.length; i++){
				addrId = addresses[i].id;
				if(addrId == currentVal){
					jQuery('select[name="bill_addrdesc"]').val(addresses[i]['id']).trigger('liszt:updated');
					jQuery('input[name="bill_company"]').val(addresses[i]['company']);
					jQuery('input[name="bill_city"]').val(addresses[i]['city']);
					jQuery('input[name="bill_street"]').val(addresses[i]['address1']);
					jQuery('input[name="bill_pobox"]').val(addresses[i]['address2']);
					jQuery('input[name="bill_country"]').val(addresses[i]['country']);
					jQuery('input[name="bill_state"]').val(addresses[i]['state']);
					jQuery('input[name="bill_code"]').val(addresses[i]['zip']);
					return;
				}
			}
		});
	},
    //@NOTE: Returns false is the check passes
    performLastCreditCheck: function (silent) {
        if (typeof silent == 'undefined') {
            silent = false;
        }
        var aDeferred = jQuery.Deferred();

        //@TODO: fix this nonsense after making sure it'll be ok moving here.
        var businessLineValue = jQuery('select[name="business_line"]').find('option:selected').html();
        if ((businessLineValue.indexOf('HHG') >= 0) || (businessLineValue.indexOf('Work Space') >= 0)) {
        } else {
            //default is allow so return "false"
            aDeferred.resolve(false);
        }

        var accountId = jQuery('input[name="orders_account"]').val();
        if (accountId != '' && accountId != '0') {
        } else {
            //default is allow so return "false"
            aDeferred.resolve(false);
        }

        var dataURL = 'index.php?module=Accounts&action=LastCreditCheck&accountId=' + accountId;
        AppConnector.request(dataURL).then(
            function (data) {
                if (data.success) {
                    if(!data.result.creditCheckDone){
                            if (jQuery('#last-credit-check').length > 0) {
                                jQuery('#last-credit-check').val("true");
                            } else {
                                jQuery('#EditView').append('<input type="hidden" id="last-credit-check" value="true" />');
                            }

                            if (!silent) {
                        Vtiger_Helper_Js.showPnotify({
                            title: 'Last Account Credit Check outdated',
                            text: 'New Move cannot be created until a new Credit Check is done.',
                            type: 'error',
                            hide: false
                        });
                            }
                                aDeferred.resolve(true);
                        } else {
                            if(jQuery('#last-credit-check').length > 0){
                                jQuery('#last-credit-check').val("false");
                            }else{
                                jQuery('#EditView').append('<input type="hidden" id="last-credit-check" value="false" />');
                            }
                                aDeferred.resolve(false);
                    }
                }
            },
            function (error) {
                    //default is allow so return "false"
                    aDeferred.resolve(false);
            }
        );
        return aDeferred.promise();
    },
	registerInvoiceDeliveryFormatRequiredFields : function() {
        //@TODO Make this compatible with standardized mandatory field conditionalization
		var thisInstance = this;
		jQuery('tr:not(.hide) select[name="invoice_delivery_format"]').off('change').on('change', function() {
            //console.log('change triggered');
			var hasEmail        = false;
			var hasPortal       = false;
            var hasMail         = false;
            var invoiceEmail = jQuery('input[name="invoice_email"]');
            var invoicePhone = jQuery('input[name="invoice_phone"]');
            var billStreet = jQuery('input[name="bill_street"]');
            var billCity = jQuery('input[name="bill_city"]');
            var billState = jQuery('input[name="bill_state"]');
            var billZip = jQuery('input[name="bill_code"]');
            var billCountry = jQuery('input[name="bill_country"]');
            var addressArray = [billStreet, billCity, billState, billZip, billCountry];

			thisInstance.mailCommodities = [];
			jQuery('tr:not(.hide) select[name="invoice_delivery_format"]').each(function() {
				switch(jQuery(this).find('option:selected').val()) {
					case 'E-mail':
						hasEmail = true;
						break;
					case 'Customer Portal':
						hasPortal = true;
						break;
                    case 'Mail':
                        hasMail = true;
                        break;
					default:
						break;
				}
			});

			if(hasEmail) {
                thisInstance.makeFieldMandatory(invoiceEmail);
			} else {
                thisInstance.makeFieldNotMandatory(invoiceEmail);
			}

			if(hasPortal) {
                thisInstance.makeFieldMandatory(invoicePhone);
			} else {
                thisInstance.makeFieldNotMandatory(invoicePhone);
			}

            if(hasMail) {
                jQuery.each(addressArray, function () {
                    thisInstance.makeFieldMandatory(this);
        });
            } else {
                jQuery.each(addressArray, function() {
                    thisInstance.makeFieldNotMandatory(this);
                });
            }
		});
	},
    //@TODO: Remove below function when it exists in the vtiger edit.js
    makeFieldMandatory : function(changingField) {
        var fieldName = changingField.attr("name");
        //clear existing red star to keep them from stacking up
        jQuery('#required' + fieldName + 'Label').remove();
        changingField.data('validationEngine', 'validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]');
        changingField.attr('data-validation-engine', 'validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]');
        //Add red star to indicate required field
        var fieldLabel = changingField.closest('td').prev('.fieldLabel').find('label');
        fieldLabel.prepend('<span id="required' + fieldName + 'Label" class="redColor">*</span>');
    },
    //@TODO: Remove below function when it exists in the vtiger edit.js
    makeFieldNotMandatory : function(changingField) {
        var fieldName = changingField.attr("name");
        changingField.data('validationEngine', 'validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]');
        changingField.attr('data-validation-engine', 'validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]');
        jQuery('#required' + fieldName + 'Label').remove();
    },
    registerAssignedToChangeEvent : function () {
		var thisInstance = this;
		jQuery('select[name="assigned_user_id"],select[name="agentid"]').on('change', function () {
			thisInstance.retrieveTariffList();
        });
		thisInstance.retrieveTariffList();
    },
    registerCommodityChangeEvent : function () {
		var thisInstance = this;
		jQuery('select[name="commodities"]').on('change', function () {
			thisInstance.retrieveTariffList();
		});
    },
    registerBusinessLine2ChangeEvent : function () {
		var thisInstance = this;
		jQuery('select[name="business_line"]').on('change', function () {
			thisInstance.retrieveTariffList();
		});
    },
	retrieveTariffList: function () {
        var selectedTariff = jQuery('select[name="tariff_id"]').find('option:selected').val();


            //colect the assigned agency and the business_line.
	    var assigned_to = jQuery('select[name="assigned_user_id"]').find('option:selected').val();
        var businessLine = jQuery('select[name="business_line"]').val();//jQuery('select[name="business_line"]').find('option:selected').val();
	    var commodity = encodeURIComponent(jQuery('select[name="commodities"]').val());
	    var agentid = jQuery('select[name="agentid"]').val();
            var dataUrl = "index.php?module=Orders&action=RetrieveTariffList&assigned_to=" + assigned_to + '&business_line=' + businessLine + '&commodity=' + commodity + '&agentid='+agentid;
	    
	    document.body.style.cursor='wait';
            AppConnector.request(dataUrl).then(
                function (data) {
                    if (data.success) {
                        var select = jQuery('select[name="tariff_id"]');
                        select.find('option[value!=""]').remove();
                        var hiddenTariffFieldHTML = '';
					jQuery.each(data.result.tariffs, function (key,val) {
						if (val && key == selectedTariff) {
							select.append('<option value="' + key + '" selected>' + val + '</option>');
						} else if (val) {
							select.append('<option value="' + key + '">' + val + '</option>');
                            }
                            if (data.result.tariffTypes[key]) {
                                hiddenTariffFieldHTML += '<input type="hidden" id="tariffType_' + key + '" value="' + data.result.tariffTypes[key] + '">';
                            }
                            if (data.result.tariffScripts[key]) {
                                hiddenTariffFieldHTML += '<input type="hidden" id="customjs_' + key + '" value="' + data.result.tariffScripts[key] + '">';
                            }
					});
                        jQuery('#hiddenTariffFields').html(hiddenTariffFieldHTML);
						select.val(selectedTariff);
                        select.trigger('liszt:updated');
                    }
		    document.body.style.cursor='default';
                },
                function (error) {
                    console.error('Error: ' + error);
		    document.body.style.cursor='default';
                }
            );
    },
	registerCommissionChangeEvent: function(){
    	return;
    	// just going to do validation for now, but keeping this stuff here in case we want to use it here or somewhere else
		var moveRoleCommissions = jQuery('input[name^="sales_commission_"]');
		moveRoleCommissions.off('change').on('change', function() {
			var commissions = jQuery('input[name^="sales_commission_"]');
			var totalValue = 0;
			for(var i=0; i < commissions.length; i++)
			{
				if(commissions[i] == this)
				{
					continue;
				}
				totalValue += Number(commissions[i].value);
			}
			var thisValue = Number(this.value);
			if(totalValue + thisValue > 100)
			{
				var exceeds = totalValue + thisValue - 100;
				for(var i=0; i < commissions.length; i++)
				{
					if(commissions[i] == this)
					{
						continue;
					}
					var v = Number(commissions[i].value);
					commissions[i].value = (v - (exceeds * (v / totalValue)));
				}
			}
		});
	},
	updateMoveRoleFieldsVisibility :function (){
		var moveRoles = jQuery('select[name^="moveroles_role_"]');
		moveRoles.each(function(){
			var salesCommission = jQuery(this).closest('tbody').find('input[name^="sales_commission_"]');
			var serviceProvider = jQuery(this).closest('tbody').find('input[name^="service_provider_"]');
			if(jQuery(this).val() == "Salesperson")
			{
				salesCommission.closest('td').children().each(function(){
					jQuery(this).removeClass('hide');
				});
				salesCommission.closest('td').prev('td').children().each(function(){
					jQuery(this).removeClass('hide');
				});
			} else {
				salesCommission.val('');
				salesCommission.closest('td').children().each(function(){
					jQuery(this).addClass('hide');
				});
				salesCommission.closest('td').prev('td').children().each(function(){
					jQuery(this).addClass('hide');
				});
			}
			if(jQuery(this).val() == "Billing Clerk")
			{
				serviceProvider.val('');
				jQuery(this).closest('tbody').find('input[name^="service_provider_display"]').val('').attr('readonly', false);
				serviceProvider.closest('td').children().each(function(){
					jQuery(this).addClass('hide');
				});
				serviceProvider.closest('td').prev('td').children().each(function(){
					jQuery(this).addClass('hide');
				});
			} else {
				serviceProvider.closest('td').children().each(function(){
					jQuery(this).removeClass('hide');
				});
				serviceProvider.closest('td').prev('td').children().each(function(){
					jQuery(this).removeClass('hide');
				});
			}
		});
	},
	registerMoveRoleChangeEvent : function (){
		var thisInstance = this;
		var moveRoles = jQuery('select[name^="moveroles_role_"]');
		moveRoles.off('change').on('change', function() {
			thisInstance.updateMoveRoleFieldsVisibility();
		});
	},
	registerWeightChangeEvent : function() {
		var thisInstance = this;
		jQuery('input[name="orders_eweight"]').off('change').on('change', function() {
			thisInstance.ValuationJS.enforceMinimumValuation();
		});
	},

	registerTariffChangeEvent : function() {
		var thisInstance = this;
		jQuery('[name="tariff_id"]').on('value_change', function() {
			var newTariff = jQuery(this).val();
			if (!newTariff) {
				jQuery('#effective_tariff_custom_type').val('').trigger('change');
				return;
			}
			var tariffData = thisInstance.effectiveTariffData[newTariff];
			jQuery('#effective_tariff_custom_type').val(tariffData['custom_tariff_type']).trigger('change');
		});
	},

	registerCustomerNumberChange : function()
	{
		jQuery('.contentsDiv').on(Vtiger_Edit_Js.postReferenceSelectionEvent, '[name="orders_custnum"]', function(e,data){
			data = data['data'];
			var message = 'Would you like to load the remote data from the Customer?';
			Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
				function(){
					data = data[Object.keys(data)[0]];
					if(typeof data['info'] == 'object')
					{
						data = data['info'];
					}
					var map = {
						'bill_street': 'Address 1',
						'bill_pobox': 'Address 2',
						'bill_city': 'City',
						'bill_state': 'State',
						'bill_code': 'Zip',
						'bill_country': 'Country',
						'invoice_phone': 'Primary Phone',
						'invoice_email': 'Primary Email',
					};
					Vtiger_Edit_Js.populateData(data, map);
				},
				function(error, err) {
					//they pressed no don't populate the data.
				}
			);
		});
	},

	registerEvents: function () {
        this._super();
        this.initializeAddressAutofill('Orders');
		this.initializeReverseZipAutoFill('Orders');
		this.checkWeight();
		this.checkRWeight();
		this.registerWeightChangeEvent();
		this.checkForSharedServices();
		//this.registerAddParticipantButtons();
		//this.registerParticipantCheck();
		//this.registerAddStopEvent();
		//this.registerStopsAnimationEvent();
		//this.deleteStopEvent();
        this.setDispatchFieldsReadOnly();
		loadBlocksByBusinesLine('Orders', 'business_line');

		this.updateTabIndexValues();
		this.registerPopulateAccountDataOnChange();
		this.registerAssignedToChangeEvent();
		this.registerCommodityChangeEvent();
		this.registerBusinessLineChangeEvent();
		//OT1599
		this.populateContractData();
		//OT1599 End
		if(jQuery('input:hidden[name="instance"]').val() == 'graebel') {
			this.customRequirements();
			jQuery('[name="orders_miles"]').attr('readonly', true);
            this.registerAmountCreditCheck();
		}
        jQuery('[name="mileage"]').attr('readonly', true);

		this.hideProjectName();
		this.ValuationJS = new Valuation_Common_Js();
		this.ValuationJS.registerEvents(true, 'Orders');
		this.effectiveTariffData = JSON.parse(jQuery('#allAvailableTariffs').val());
		this.registerTariffChangeEvent();

		this.populateAccountData(undefined, true);
		this.registerBillingAddressDescChangeEvent();
		this.registerCustomerNumberChange();

    },
    openPopUp : function(e){
		var thisInstance = this;
		var parentElem = jQuery(e.target).closest('td');

		var params = this.getPopUpParams(parentElem, e);

                if(!params){
                    return ;
                }


		var isMultiple = false;
		if(params.multi_select) {
			isMultiple = true;
		}

        //OT4042
        //Not sure in what all cases we don't want the confirmation box.
        //If more cases are needed, add them here
		// seems to be Contact specific
        var confirmBox = false;
        if((params.module == 'Contacts' || params.module == 'Accounts')  && params.src_field != "orders_billingcustomerid" ){
            var confirmBox = true;
        }

		// check agentid select exists
		if(jQuery('select[name="agentid"]').length>0){
			params['agentId'] = jQuery('select[name="agentid"]').val();
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
			var dataList = [];
			for(var id in responseData){
				var data = {
					'name' : responseData[id].name,
					'id' : id
					};
				dataList.push(data);
				if(!isMultiple) {
					thisInstance.setReferenceFieldValue(parentElem, data);
					if(confirmBox) {
						if(params.module == 'Contacts'){
							var message = 'Would you like to load data from the Contact?';
						}else{
							var message = 'Would you like to load data from the Account?';
						}

						Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
							function(e){
								if(params.module == 'Contacts'){
									jQuery('input[name="order_name"]').val(responseData[id].name);
									jQuery('#Orders_editView_fieldName_orders_billingcustomerid_dropDown').val('Contacts').trigger("liszt:updated");
								}

								jQuery('input[name="orders_billingcustomerid_display"]').val(responseData[id].name);
								jQuery('input[name="orders_billingcustomerid"]').val(data.id).trigger('change');
							},
							function(error, err) {
								//they pressed no don't populate the data.
							}
						);
					}

					if(params.src_field == "orders_billingcustomerid"){
						jQuery('[name="orders_billingcustomerid"]').trigger('change');
					}
			}
			}

			if(isMultiple) {
                    sourceFieldElement.trigger(Vtiger_Edit_Js.refrenceMultiSelectionEvent,{'data':dataList});
			}
                sourceFieldElement.trigger(Vtiger_Edit_Js.postReferenceSelectionEvent,{'data':responseData});
		});
	},
    getPopUpParams: function (container, e) {
        var params = this._super(container);
        var sourceFieldElement = jQuery('input[class="sourceField"]', container);

        if (sourceFieldElement.attr('name') == 'orders_contacts' || sourceFieldElement.attr('name').indexOf('consignment') >= 0) {
            params['contact_type'] = 'Transferee';
            params['cvid'] = 50;

        }


        if (sourceFieldElement.attr('name') == 'account_contract') {
            var parentIdElement = jQuery('[name="orders_account"]');
            if (parentIdElement.length > 0 && parentIdElement.val().length > 0 && parentIdElement.val() != 0) {
                params['accountId'] = parentIdElement.val();

            }else{
                 var params = {
                    title: app.vtranslate('JS_ERROR'),
                    text: app.vtranslate('Account can not be null. Please choose an Account first'),
                    animation: 'show',
                    type: 'error'
                };
                Vtiger_Helper_Js.showPnotify(params);
                return false;
            }

            if(jQuery('[name="business_line"]').val() != ''){
                params['businessLine'] = jQuery('[name="business_line"]').val();
            }else{
                var params = {
                    title: app.vtranslate('JS_ERROR'),
                    text: app.vtranslate('Business Line can not be null. Please choose a business line first'),
                    animation: 'show',
                    type: 'error'
                };
                Vtiger_Helper_Js.showPnotify(params);
                return false;


            }

        }

		var sourceFieldName= sourceFieldElement.attr('name');
		if( sourceFieldName.indexOf("moveroles_employees") !== -1) {
			var parentTr = sourceFieldElement.closest('tr');
			var parentIdElement  = parentTr.find('[name^="moveroles_role"][class="sourceField"]');
			var moduleName = app.getModuleName();
			if(parentIdElement.length > 0 && parentIdElement.val().length > 0 && parentIdElement.val() != 0) {
				params.relatedparent_id = parentIdElement.val();
				params.relatedparent_module = 'EmployeeRoles';
			}
		}

        return params;
    },
    editViewForm: false,
    resourcesCheckCache: {},
	  /**
     * This function will return the current form
     */
    getForm: function () {
        if (this.editViewForm == false) {
            this.editViewForm = jQuery('#EditView');
        }
        return this.editViewForm;
    },
    getGrossWeight: function () {
        return jQuery('input[name="orders_gweight"]').val();
    },
    getTareWeight: function () {
        return jQuery('input[name="orders_tweight"]').val();
    },
    getRGrossWeight: function () {
        return jQuery('input[name="orders_rgweight"]').val();
    },
	 getRTareWeight: function () {
        return jQuery('input[name="orders_rtweight"]').val();
    },
	/**
     * This function will return the current RecordId
     */
    getRecordId: function () {
        return jQuery('input[name="record"]').val();
    },
    setNetWeight: function (netweight) {
        jQuery('input[name="orders_netweight"]').val(netweight);
    },
	setRNetWeight: function (rnetweight) {
        jQuery('input[name="orders_rnetweight"]').val(rnetweight);
    },
	calculateNetWeight : function() {
		console.dir('blur event triggered');
		var grossweight = this.getGrossWeight();
		var tareweight = this.getTareWeight();


		if (grossweight != '' && tareweight != '') {

			var netweight = grossweight - tareweight;

			   if (netweight < 0) {
				alert(app.vtranslate('JS_WEIGHT_NEGATIVE'));
			} else {
				this.setNetWeight(netweight);

			}


		} else {
			console.dir('empty value found');
		}
	},
	calculateRNetWeight : function() {
		console.dir('blur event triggered');
		var rgrossweight = this.getRGrossWeight();
		var rtareweight = this.getRTareWeight();


		if (rgrossweight != '' && rtareweight != '') {

			var rnetweight = rgrossweight - rtareweight;

			   if (rnetweight < 0) {
				alert(app.vtranslate('JS_RWEIGHT_NEGATIVE'));
			} else {
				this.setRNetWeight(rnetweight);

			}


		} else {
			console.dir('empty value found');
		}
	},
    checkWeight: function () {
		var thisInstance = this;
        jQuery('#Orders_editView_fieldName_orders_gweight').on('blur', function () {
            thisInstance.calculateNetWeight()
        });
        jQuery('#Orders_editView_fieldName_orders_tweight').on('blur', function () {
            thisInstance.calculateNetWeight()
        });
    },
	checkRWeight: function () {
		var thisInstance = this;
        jQuery('#Orders_editView_fieldName_orders_rgweight').on('blur', function () {
            thisInstance.calculateRNetWeight()
        });
        jQuery('#Orders_editView_fieldName_orders_rtweight').on('blur', function () {
            thisInstance.calculateRNetWeight()
        });
    },
	checkForSharedServices: function() {
		var dataUrl = "index.php?module=Orders&action=AgentSharedServices";
		AppConnector.request(dataUrl).then(
			function (data) {
				if (data.success) {
					jQuery('select[name="agentid"] option').each(function() {
						var selection = jQuery(this).html();
						if(selection) {
							var option = selection.split('(');
							if(jQuery.inArray(option[0].trim(), data.result) !== -1) {
								jQuery(this).html(selection+' - Shared Services');
							}
						}
					});
					jQuery('select[name="agentid"]').trigger('liszt:updated');
				}
			},
			function (error, err) {

			}
		);
	},
	lockReceivedDate: function() {
		if(jQuery('form').data('lockdatefield')) {
			jQuery('input[name="received_date"]').prop('disabled', true).datepicker();
		}
	},
    customRequirements: function () {
        var thisInstance = this;
        var form = this.getForm();
        form.on(Vtiger_Edit_Js.recordPreSave, function (e) {
            if(thisInstance.customRequirementsPassed)
            {
                return true;
            }

            if(typeof form.data('orders-submit') != "undefined") {
                e.preventDefault();
                return false;
            }
            form.data('orders-submit', 'true');
                //@TODO: condense these to one call since it's working with the same data.
                var promises = [];
                promises.push(thisInstance.performCheckCreditHold(true));
                promises.push(thisInstance.performLastCreditCheck(true));
                if (jQuery('input:hidden[name="instance"]').val() == 'graebel') {
                    promises.push(thisInstance.performCheckBillingClerkEmail());
                    promises.push(thisInstance.performCheckBookingAgentAddress());
                }
                //hold up until these -t-w-o- three (four?) things check their checks.
                $.when.apply($, promises).then(function (checkHold, lastCheck, billingClerkCheck, bookingAgentCheck) {
			var salesRole = jQuery('option:selected[value="Salesperson"]').closest('tr').not(':hidden').length;
			var salesOrg = jQuery('option:selected[value="Sales Org"]').closest('tr').not(':hidden').length;
			var bookingAgent = jQuery('option:selected[value="Booking Agent"]').closest('tr').not(':hidden').length;
			var billingClerk = jQuery('option:selected[value="Billing Clerk"]').closest('tr').not(':hidden').length;
                    var carrier = jQuery('option:selected[value="Carrier"]').closest('tr').not(':hidden').length;
                    //Carrier is currently only required for graebel
                    if (jQuery('input:hidden[name="instance"]').val() != 'graebel') {
                        carrier = 1;
                    }
			var commissions = jQuery('input[name^="sales_commission_"]').not(':hidden');
			var dates = {
				orders_ldate: 'orders_ddate',
				orders_ltdate: 'orders_dtdate',
				orders_pldate: 'orders_pddate'
			};
			message = 'Missing Requirements: <br>';
			var totalValue = 0;
			for(var i=0; i < commissions.length; i++)
			{
				totalValue += Number(commissions[i].value);
			}
                    var billingClerkEmailFailed = false;
                    if(billingClerkCheck && billingClerkCheck.length > 0)
                    {
                        billingClerkEmailFailed = true;
                        for(var i = 0; i < billingClerkCheck.length;++i)
                        {
                            message += '<br> - ' + billingClerkCheck[i]['name'] + ' has an invalid email address';
                        }
                    }
                    var bookingAgentCheckAddressFailed = false;
                    if(bookingAgentCheck && bookingAgentCheck.length > 0)
                    {
                        bookingAgentCheckAddressFailed = true;
                        for(var i = 0; i < bookingAgentCheck.length;++i)
                        {
                            message += '<br> - ' + bookingAgentCheck[i]['name'] + ' has an invalid address';
                        }
                    }
			var loadDeliveryFailed = false;
			for(var fieldName in dates)
			{
				var loadDate = jQuery('[name="'+fieldName+'"]').val();
				var deliveryDate = jQuery('[name="'+dates[fieldName]+'"]').val();
				if(loadDate != '' && deliveryDate != '')
				{
					loadDate = new Date(loadDate);
					deliveryDate = new Date(deliveryDate);
					if(loadDate > deliveryDate)
					{
						loadDeliveryFailed = true;
						message += '<br> - Load dates must be before delivery dates';
						break;
					}
				}
			}
                    if (
                        salesRole == 0 ||
                        salesOrg == 0 ||
                        bookingAgent == 0 ||
                        totalValue != 100 ||
                        billingClerk == 0 ||
                        carrier == 0 ||
                        loadDeliveryFailed ||
                        checkHold ||
                        lastCheck ||
                        billingClerkEmailFailed ||
                        bookingAgentCheckAddressFailed
                    ) {
				e.preventDefault();
				if(bookingAgent == 0){
					message += '<br> - Participating Booking Agent';
				}
				if(salesOrg == 0){
					message += '<br> - Participating Sales Org';
				}
                        if (carrier == 0) {
                            message += '<br> - Participating Carrier';
                }
				if(salesRole == 0){
					message += '<br> - Salesperson Move Role';
				}
				if(billingClerk == 0){
					message += '<br> - Billing Clerk Move Role';
				}
				if(totalValue != 100)
				{
					message += '<br> - Total commission must equal 100%';
				}

                if (checkHold)
                {
                    message += '<br> - New Move cannot be created as there is a Credit Hold.';
                }

                if (lastCheck)
                {
                    message += '<br> - New Move cannot be created until a new Credit Check is done.';
                }

				bootbox.alert(message);
                        form.removeData('orders-submit');
                        //jQuery('#EditView').removeData('submit');
			} else {
                        form.removeData('orders-submit');
                        thisInstance.customRequirementsPassed = true;
                        form.submit();
                        //e.preventDefault();
                        return false;
			}
                }, function () {
                    form.removeData('orders-submit');
                });
            e.preventDefault();
		});
	},
	removeEstimateTypes : function() {
		//This removes options in the estimate type selection instead of removing them
        // from the database because I wasn't sure if this would effect anything else.
		var options = ['', 'Binding', 'Not to Exceed', 'Non-Binding'];
		jQuery('select[name="estimate_type"] option').each(function() {
			var option = jQuery(this).val();
			if(jQuery.inArray(option,options)==-1) {
				jQuery(this).remove();
			}
		});
		jQuery('select[name="estimate_type"]').trigger('liszt:updated');

        // Added this warning so it would alert someone if they were trying
        // to figure out why the values aren't there
		console.warn('Removed options in estimate type in removeEstimateTypes');
	},
	lockFields : function() {
		jQuery('input[name="registered_on"]').prop('disabled', true);
		jQuery('select[name="orders_otherstatus"]').prop('disabled', true).trigger('liszt:updated');
	},
	toggleGSAFields: function(state) {
		var toggleFields = [
			'personal_hhg_weight',
			'pro_gear_weights',
			'LBL_GSA_INFORMATION',
		];

		jQuery.each( toggleFields, function( key, value ) {
			if (value.match("^LBL")) { // IF the value is a label
				if(state == 'show') {
					jQuery('[name="' + value + '"]').removeClass('hide');
				} else {
					jQuery('[name="' + value + '"]').addClass('hide');
				}
			} else { //else its an input
				if(state == 'show') {
					jQuery('[name="'+value+'"]').val('').parent().removeClass('hide').closest('td').prev().find('label').removeClass('hide');
				} else {
					jQuery('[name="'+value+'"]').parent().addClass('hide').closest('td').prev().find('label').addClass('hide');
				}
            }

		});
	},
	//OT 16108 - Military fields only appear when Military billing type selected.
	toggleMilitaryFields: function(state) {
		var toggleFields = [
			'LBL_MILITARY_INFORMATION',
			'LBL_MILITARY_POST_MOVE_SURVEY'
		];

		jQuery.each( toggleFields, function( key, value ) {
			if(state == 'show') {
				jQuery('[name="' + value + '"]').removeClass('hide');
			} else {
				jQuery('[name="' + value + '"]').addClass('hide');
			}
		});
	},
	hideProjectName: function() {
        var thisInstance = this;
        if(jQuery('select[name="business_line"] option:selected').text().indexOf("Work Space") < 0) {
            thisInstance.toggleOrdersInformationFields('hide');
        }

        jQuery('select[name="business_line"]').change(function() {
            if(jQuery('select[name="business_line"] option:selected').text().indexOf("Work Space") >= 0) {
                thisInstance.toggleOrdersInformationFields('show');
            } else{
                thisInstance.toggleOrdersInformationFields('hide');
            }
        });

    },
    toggleOrdersInformationFields: function(state) {
        var toggleFields = [
            'orders_projectname',
        ];

        jQuery.each( toggleFields, function(key, value) {
            if (value.match("^LBL")) { // IF the value is a label
                if(state == 'show') {
                    jQuery('[name="' + value + '"]').removeClass('hide');
                } else {
                    jQuery('[name="' + value + '"]').addClass('hide');
                }
            } else { //else its an input
                if(state == 'show') {
                    jQuery('[name="'+value+'"]').parent().removeClass('hide').closest('td').prev().find('label').removeClass('hide');
                } else {
                    jQuery('[name="'+value+'"]').parent().addClass('hide').closest('td').prev().find('label').addClass('hide');
                }
            }

        });

},
	registerOrderRegistrationEvent: function() {
		var thisInstance = this;
		jQuery('select[name="ordersstatus"]').change(function() {
			if (jQuery(this).val() == 'Registered' && jQuery('input[name="targetenddate"]').val() == '' ) {
				var dateFormat = jQuery('input[name="targetenddate"]').data('dateFormat');
				jQuery('input[name="targetenddate"]').val(thisInstance.getCurrentDate(dateFormat));
			}
		});
	},
	getCurrentDate: function(format) {
		var today = new Date();
		var dd = today.getDate();
		var mm = today.getMonth()+1;
		var yyyy = today.getFullYear();

		if(dd<10) {
			dd='0'+dd;
		}

		if(mm<10) {
			mm='0'+mm;
		}

		if(format == 'yyyy-mm-dd') {
			return yyyy+'-'+mm+'-'+dd;
		} else if(format == 'mm-dd-yyyy') {
			return mm+'-'+dd+'-'+yyyy;
		} else {
			return dd+'-'+mm+'-'+yyyy;
		}
	},
	registerBillingTypeField: function() {
		var thisInstance = this;
		var billingType = jQuery('select[name="billing_type"]').val();
		if(billingType != 'GSA') {
			thisInstance.toggleGSAFields('hide');
		}
		if(billingType != 'Military') {
			thisInstance.toggleMilitaryFields('hide');
		}

		jQuery('select[name="billing_type"]').change(function() {
            if(jQuery('input:hidden[name="instance"]').val() == 'graebel') {
				participantInstance = ParticipatingAgents_Edit_Js.getInstance();
				var carrierRow = participantInstance.findParticipantRow('Carrier');
                businessLineValue = jQuery('select[name="business_line"]').find('option:selected').html();
                    if (carrierRow && carrierRow.attr('data-state') == 'auto-set' && jQuery('input:hidden[name="instance"]').val() == 'graebel'){
                        participantInstance.setDefaultCarrier(businessLineValue);
                    }
            }
			if(jQuery(this).val() == 'GSA') {
				thisInstance.toggleGSAFields('show');
			} else {
				thisInstance.toggleGSAFields('hide');
			}

		});

		jQuery('select[name="billing_type"]').change(function() {
			if(jQuery(this).val() == 'Military') {
				thisInstance.toggleMilitaryFields('show');
			} else {
				thisInstance.toggleMilitaryFields('hide');
			}

		});
		jQuery('select[name="billing_type"]').change(function() {
			if(jQuery(this).val() == 'National Account') {
				jQuery('table[name="LBL_ORDER_ACCOUNT_ADDRESS"]').removeClass('hide');
			} else {
				jQuery('table[name="LBL_ORDER_ACCOUNT_ADDRESS"]').addClass('hide');
			}
		});
		jQuery('select[name="billing_type"]').trigger('change');
    },
	registersReferenceSelectionEvent : function(form) {
        var thisInstance = this;
        if(typeof form == 'undefined') {
                form = this.getForm();
        }
        form.on(Vtiger_Edit_Js.referenceSelectionEvent, function(e, data) {
			if('carrier_company' == e.target.name || 'carrier_scac_code' == e.target.name){
				thisInstance.getSelectedRecordInfo(data.record,data.source_module).then(function(data){
					if(data.result) {
						form.find('input[name="carrier_company"]').val(data.result.id);
						form.find('input[name="carrier_scac_code"]').val(data.result.id);
						form.find("#carrier_company_display").val(data.result.company).attr('readonly',true);
						form.find("#carrier_scac_code_display").val(data.result.scac_code).attr('readonly',true);
        }

				});
			}else if('issuing_office_gbloc' == e.target.name) {
				thisInstance.getSelectedRecordInfo(data.record,data.source_module).then(function(data){
					if(data.result) {
						form.find('input[name="issuing_gbloc_location"]').val(data.result.location);
					}
				});
			}else if('responsible_dest_office_gbloc' == e.target.name) {
				thisInstance.getSelectedRecordInfo(data.record,data.source_module).then(function(data){
					if(data.result) {
						form.find('input[name="dest_gbloc_location"]').val(data.result.location);
					}
				});
			}
		});
        form.on(Vtiger_Edit_Js.postReferenceSelectionEvent, function(e, data) {
            if ('accountId' == e.target.name) {
                thisInstance.performCheckCreditHold();
            }
        });
    },
    //return of false is allow
    performCheckCreditHold: function (silent) {
        if (typeof silent == 'undefined') {
            silent = false;
        }
        var aDeferred = jQuery.Deferred();
        var amount = jQuery('[name="total_valuation"]').val();
        var accountId = jQuery('[name="orders_account"]').val();

        var dataURL = 'index.php?module=Accounts&action=CheckCreditHold&accountId=' + accountId + '&amount=' + amount;
        AppConnector.request(dataURL).then(
            function (data) {
                if (data.success) {
                    if(data.result.isOnHold){
                            if (jQuery('#credit-hold').length > 0) {
                                jQuery('#credit-hold').val("true");
                            } else {
                                jQuery('#EditView').append('<input type="hidden" id="credit-hold" value="true" />');
                            }
                            if (!silent) {
                        Vtiger_Helper_Js.showPnotify({
                            title: 'Account Credit Hold Check',
                            text: 'New Move cannot be created as there is a Credit Hold.',
                            type: 'error',
                            hide: false
                        });
                            }
                                aDeferred.resolve(true);
                        } else {
                            if(jQuery('#credit-hold').length > 0){
                                jQuery('#credit-hold').val("false");
                            }else{
                                    jQuery('#EditView').append('<input type="hidden" id="credit-hold" value="false" />');
                            }
                                aDeferred.resolve(false);
                    }
                }
            },
            function (error) {
                    //default is allow.
                    aDeferred.resolve(false);
            }
        );
        return aDeferred.promise();
    },

    performCheckBillingClerkEmail: function (silent) {
        if (typeof silent == 'undefined') {
            silent = false;
        }
        var aDeferred = jQuery.Deferred();
        var row = jQuery('[name^="moveroles_employees_"]').filter(function() {
            return jQuery(this).closest('tr').find('[name^="moveroles_role_"]').val() == 'Billing Clerk';
        });
        if(row.length <= 0)
        {
            aDeferred.resolve(true);
            return;
        }
        var id = row.map(function() {
            return this.value;
        }).get().join(',');

        var dataURL = 'index.php?module=Orders&action=ValidateAssociateEmail&id=' + id;
        AppConnector.request(dataURL).then(
            function (data) {
                if (data.success) {
                    if (data.result.length > 0) {
                        aDeferred.resolve(data.result);
                    } else {
                        aDeferred.resolve(true);
                    }
                }
            },
            function (error) {
                //default is allow.
                aDeferred.resolve(true);
            }
        );
        return aDeferred.promise();
    },

    performCheckBookingAgentAddress: function (silent) {
        if (typeof silent == 'undefined') {
            silent = false;
        }
        var aDeferred = jQuery.Deferred();
        var row = jQuery('[name^="agents_id_"]').filter(function() {
            return jQuery(this).closest('tr').find('[name^="agent_type_"]').val() == 'Booking Agent' && jQuery(this).attr('name').indexOf('display') < 0;
        });
        if(row.length <= 0)
        {
            aDeferred.resolve(true);
            return;
        }
        var id = row.map(function() {
            return this.value;
        }).get().join(',');

        var dataURL = 'index.php?module=Orders&action=ValidateAgentAddress&id=' + id;
        AppConnector.request(dataURL).then(
            function (data) {
                if (data.success) {
                    if (data.result.length > 0) {
                        aDeferred.resolve(data.result);
                    } else {
                        aDeferred.resolve(true);
                    }
                }
            },
            function (error) {
                //default is allow.
                aDeferred.resolve(true);
            }
        );
        return aDeferred.promise();
    },

	getSelectedRecordInfo : function (record, source_module) {
		var aDeferred = jQuery.Deferred();
		var params={};
		params.module = source_module;
		params.action = 'ActionAjax';
		params.mode = 'getRecordInfo';
		params.record = record;
		AppConnector.request(params).then(
			function(data){
				aDeferred.resolve(data);
			},
			function(error){
				//TODO : Handle error
				aDeferred.reject();
			}
		);
		return aDeferred.promise();
	},
    registersEnableSaveOnAccountClear: function(){
        jQuery('#Orders_editView_fieldName_orders_account_clear').click(function(){
            jQuery('button[type="submit"]').prop('disabled', false);
        });
    },
    registerAmountCreditCheck: function () {
        var thisInstance = this;
        jQuery('[name="total_valuation"]').change(function () {
            thisInstance.performCheckCreditHold();
        });
    },

	registersOrderStatusChangeEvent: function () {
		var orderStatusEle = jQuery('[name="ordersstatus"]');
		orderStatusEle.on('change',function () {
			var reasonEle = jQuery('[name="order_reason"]');
			var orderStatusVal = jQuery(this).val();
			if(orderStatusVal == 'Cancelled'){
                Vtiger_Edit_Js.showCell('order_reason');
			}else{
                Vtiger_Edit_Js.hideCell('order_reason');
			}
			reasonEle.trigger('liszt:updated');
		});
		orderStatusEle.trigger('change');
    },

	/*OT3370*/
	registerClearReferenceSelectionEvent : function(container) {
		container.find('.clearReferenceSelection').on('click', function(e){
			var element = jQuery(e.currentTarget);
			var parentTdElement = element.closest('td');
			var fieldNameElement = parentTdElement.find('.sourceField');
			var fieldName = fieldNameElement.attr('name');

			fieldNameElement.val('');
			parentTdElement.find('#'+fieldName+'_display').removeAttr('readonly').val('');
			element.trigger(Vtiger_Edit_Js.referenceDeSelectionEvent);
			fieldNameElement.trigger(Vtiger_Edit_Js.referenceDeSelectionEvent);

			if (fieldName == 'carrier_company' || fieldName == 'carrier_scac_code'){
				if (fieldName == 'carrier_company'){
					var element = jQuery('span.Orders_editView_fieldName_carrier_scac_code_clear');
				}else if(fieldName == 'carrier_scac_code'){
					var element = jQuery('span.Orders_editView_fieldName_carrier_company_clear');
				}

				var parentTdElement = element.closest('td');
				var fieldNameElement = parentTdElement.find('.sourceField');
				var fieldName = fieldNameElement.attr('name');

				fieldNameElement.val('');
				parentTdElement.find('#'+fieldName+'_display').removeAttr('readonly').val('');
				element.trigger(Vtiger_Edit_Js.referenceDeSelectionEvent);
				fieldNameElement.trigger(Vtiger_Edit_Js.referenceDeSelectionEvent);
			}

			if (fieldName == 'issuing_office_gbloc' || fieldName == 'responsible_dest_office_gbloc'){
				if (fieldName == 'issuing_office_gbloc'){
					var element = jQuery('input[name="issuing_gbloc_location"]');
				}
				if (fieldName == 'responsible_dest_office_gbloc'){
					var element = jQuery('input[name="dest_gbloc_location"]');
				}
				element.val('');
			}

			if(parentTdElement.find('[name="popupReferenceModule"]').val() == "EmployeeRoles"){
				var parentTdElement1 = element.closest('td').next().next();
				var fieldNameElement1 = parentTdElement1.find('.sourceField');
				fieldNameElement1.val('');
				parentTdElement1.find('.autoComplete').removeAttr('readonly');
				parentTdElement1.find('.autoComplete').val('');
				element.trigger(Vtiger_Edit_Js.referenceDeSelectionEvent);
			}

			e.preventDefault();
		})
	},
	/*OT3370*/

	/*OT3370*/
	registersFieldReadOnly : function (container) {
		container.find('input[name="issuing_gbloc_location"]').attr('readonly', 'readonly');
		container.find('input[name="dest_gbloc_location"]').attr('readonly', 'readonly');
	},

	registersFieldMaskInput: function (container) {
		var selector = jQuery('input[name="transferee_ssn"]');
		selector.inputmask("XXX-XX-9999"); //mask with dynamic syntax
	},
	/*OT3370*/

	/*OT3370*/
	registersCalculatorFieldTotal : function (container) {
		var selects = $('[name="q4"], [name="q5"], [name="q6"], [name="q7"], [name="q8"], [name="q9"]');
		// var totalArray = [];
		var items = {};

                //to load values when editing---START
		jQuery.each(selects, function( index, selector ) {
                    var focus = $(this);
                    var name = focus.attr('name');
                    var val = focus.val();
                    if (!val || val == 'N/A') {
                        val = "0";
                    }
                    items[name] = val;
		});
                //to load values when editing---END
                
		jQuery.each(selects, function( index, selector ) {
			jQuery(selector).on('change',function () {
				var focus = $(this);
				var name = focus.attr('name');
				var val = focus.val();
				var total = 0;
				var numItems = 0;
				var avg = 0;
				var item = '';
				var hasValue = 0;

				if (!val || val == 'N/A') {
					val = "0";
				}
				items[name] = val;

				for (var k in items) {
					if (!items.hasOwnProperty(k)) {
						continue;
					}
					item = items[k];
					var myVal = parseFloat(item);
					if (myVal) {
						hasValue++;
						numItems++;
					}
					total += myVal;
				}

				if (hasValue == 0) {
					hasValue = 1;
				}

				avg = (total / numItems);
				if(isNaN(avg)) {
				    avg = '';
                }

				jQuery('input[name="total"]').val(avg);

			});

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

		fieldElement.val(id);
		fieldDisplayElement.val(selectedName).attr('readonly',true);
		fieldElement.trigger(Vtiger_Edit_Js.referenceSelectionEvent, {'source_module' : popupReferenceModule, 'record' : id, 'selectedName' : selectedName});

		fieldDisplayElement.validationEngine('closePrompt',fieldDisplayElement);
		if(jQuery('[name="instance"]').val() != 'graebel') {
			this.registerAddMorolesByChangeAccount(id, container);
		}
	},

	registerAddMorolesByChangeAccount : function(id,container) {
		var thisInstance = new Vtiger_EditBlock_Js;
		var guestModule = 'MoveRoles';

		var referenceModuleName = this.getReferencedModuleName(container);
		var business_line2 = $('[name="business_line2"]').val();
		if(referenceModuleName == 'Accounts'){
			var params = {
				module: 'MoveRoles',
				action: 'FilterByBusinesslineForAccount',
				accountid: id,
				business_line: business_line2
			};
			AppConnector.request(params).then(function (data) {
				if(data.result.length > 0){
					var numberMoveroles = 0;
					jQuery('[name^="MoveRolesTable"]').find('.MoveRolesBlock:visible').each(function (key) {
						var i = key + 1;
						if($(this).find('#moveroles_id_'+i).val()){
							$(this).hide();
							$(this).find('[name=moveroles_deleted_'+i+']').val('deleted');
							numberMoveroles ++
						}else{
							$(this).remove();
						}
					});
					jQuery('[name^="MoveRolesTable"]').find('.numMoveRoles').val(numberMoveroles);
					$.each(data.result, function (k,v) {
						var defaultRecordFields = jQuery('.default' + guestModule);
						var newRecordFields = defaultRecordFields.clone().removeClass('default' + guestModule + ' hide').appendTo('table[name="' + guestModule + 'Table"]');
						newRecordFields.find('.' + guestModule + 'Content').removeClass('hide');
						var recordCounter = jQuery('#num' + guestModule);
						var recordCount = recordCounter.val();
						recordCount++;
						recordCounter.val(recordCount);
						newRecordFields.addClass(guestModule + '_' + recordCount);
						newRecordFields.attr('guestid', recordCount);
						newRecordFields.find('.sourceField').each(function() {
							var oldName = jQuery(this).attr('name');
							var newName = jQuery(this).attr('name')+'_' + recordCount;

							jQuery(this).attr('name', newName);
							newRecordFields.find('[name="'+oldName+'_display"]').attr('name', newName + '_display').attr('id', newName + '_display').addClass('referenceDisplay');
						});

						newRecordFields.find('div').each(function() {
							if (jQuery(this).hasClass('select2')) {
								/* this is ... it shouldn't be in the .tpl to get here, but that would be much more work to case.
								 var defaultId = jQuery(this).attr('id');
								 if (defaultId !== undefined) {
								 jQuery(this).attr('id', defaultId + '_' + recordCount);
								 }
								 */
								jQuery(this).remove();
							}
						});

						newRecordFields.find('input, select').not('.referenceDisplay').not('.sourceField').not('input:hidden[name="popupReferenceModule"]').each(function(){
							var defaultName = jQuery(this).attr('name');
							var defaultId = jQuery(this).attr('id');
							if (defaultName !== undefined) {
								var x = defaultName.match(/\[\]/);
								if (x) {
									jQuery(this).attr('name', defaultName.replace('[]','_'+recordCount+'[]'));
								} else {
									var index = defaultName.search(/\d/);
									var secondIndex = defaultName.indexOf('_', index);
									if(index > 0 && guestModule != 'ExtraStops') {
										if(secondIndex > 0) {
											var secondNumber = defaultName.substr(secondIndex + 1);
										}
										defaultName = defaultName.substr(0, index) + recordCount;
										if(typeof secondNumber != 'undefined')
										{
											defaultName = defaultName + '_' + secondNumber;
										}
										jQuery(this).attr('name', defaultName);
									} else {
										jQuery(this).attr('name', defaultName + '_' + recordCount);
									}
								}
								//console.dir(jQuery(this).attr('name'));
							}

							if (defaultId !== undefined) {
								jQuery(this).attr('id', defaultId+'_'+recordCount);
							}

							if(jQuery(this).is('select')) {
								if (!jQuery(this).hasClass('select2')) {
									jQuery(this).addClass('chzn-select');
								}
							}
						});

						$('[name=moveroles_role_'+recordCount+ ']').val(v['role']);
						$('[name=moveroles_role_'+recordCount+ '_display]').val(v['emprole_desc']);
						$('[name=moveroles_role_'+recordCount+ '_display]').attr('readonly','readonly');

						$('[name=moveroles_employees_'+recordCount+']').val(v['user']);
						$('[name=moveroles_employees_'+recordCount + '_display]').val(v['employee_name']);
						$('[name=moveroles_employees_'+recordCount+ '_display]').attr('readonly','readonly');
						//Register date fields
						app.registerEventForDatePickerFields(jQuery('.dateField'), true);

						//Register the chosen fields
						newRecordFields.find('select.chzn-select').chosen();

						//register the select2 fields
						app.showSelect2ElementView(newRecordFields.find('select.select2'));

						var editInstance = Vtiger_Edit_Js.getInstance();
						editInstance.registerBasicEvents(newRecordFields);
						thisInstance.guestDeleteRecordEvent(guestModule);
						newRecordFields.recordCount = recordCount;
						try {
							eval('check = new ' + guestModule+'_EditBlock_Js();');
							if (typeof check != 'undefined') {
								check.registerBasicEvents(newRecordFields);
							} else {
							}
						} catch (errMT) {
							//do nothing this is fine
						}
						jQuery(this).closest('table').trigger({
							type:"addRecord",
							newRow:newRecordFields
						});
					});


				}
			});
		}


	},
        setCancelButton: function(){
            if(this.getUrlParameter("fromcapacity") == "true"){
                jQuery(".cancelLink").attr("onclick","window.open('index.php?module=Orders&view=List','_self')");
            }
        },
        getUrlParameter: function(sParam) {
	    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
		sURLVariables = sPageURL.split('&'),
		sParameterName,
		i;

	    for (i = 0; i < sURLVariables.length; i++) {
		sParameterName = sURLVariables[i].split('=');

		if (sParameterName[0] === sParam) {
		    return sParameterName[1] === undefined ? true : sParameterName[1];
		}
	    }
	},
        // OT18713 - Bug: Total field should be read only in Military Post Move Survey block
        setFieldReadonly:function(){
            jQuery('table[name="LBL_MILITARY_POST_MOVE_SURVEY"] tbody').find('input[name="total"]').attr('readonly','readonly');
        },
	// Vtiger_Edit_Js.referenceSelectionEvent
	registerBasicEvents: function (container, quickCreateParams) {
		this._super(container);
		this.lockReceivedDate();
        //OT16196 -- estimate type fields are being removed. Oh thank you for the Obvious!
		//this.removeEstimateTypes();
		this.lockFields();
		this.registerBillingTypeField();
		this.registerBusinessLine2ChangeEvent();
		this.registerCommissionChangeEvent();
		this.registerMoveRoleChangeEvent();
		this.updateMoveRoleFieldsVisibility();
		this.registerInvoiceDeliveryFormatRequiredFields();
		this.registerOrderRegistrationEvent();
		this.registersReferenceSelectionEvent();
		this.registersEnableSaveOnAccountClear();
        this.registerRules(true);
		/*OT3370*/
		this.registersFieldReadOnly(container);
		this.registersFieldMaskInput(container);
		this.registersCalculatorFieldTotal(container);
		/*OT3370*/

                this.setCancelButton();


		var thisInstance = this;
		jQuery('.Orders_editView_fieldName_account_contract_clear').on(Vtiger_Edit_Js.referenceDeSelectionEvent, function(){
			thisInstance.unbindContractItems(true);
		});
		// for quick create
		if(typeof quickCreateParams != 'undefined') {
			this.populateAccountData(quickCreateParams.sourceRecord);
		}
		this.registersOrderStatusChangeEvent();
		this.registersReferenceSelectionEvent(container);
        this.setFieldReadonly();
	}
});

function getQueryVariable(variable) {
	var query = window.location.search.substring(1);
	var vars = query.split("&");
	for (var i=0; i<vars.length; i++) {
		var pair = vars[i].split("=");
        if (pair[0] == variable) {
            return pair[1];
        }
	}
	return(false);
}



