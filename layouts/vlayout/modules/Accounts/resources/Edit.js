/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("Accounts_Edit_Js",{

},{

    //Stored history of account name and duplicate check result
	duplicateCheckCache : {},

	//This will store the editview form
	editViewForm : false,

	//Address field mapping within module
	addressFieldsMappingInModule : {
										'bill_street':'ship_street',
										'bill_pobox':'ship_pobox',
										'bill_city'	:'ship_city',
										'bill_state':'ship_state',
										'bill_code'	:'ship_code',
										'bill_country':'ship_country'
								},

   // mapping address fields of MemberOf field in the module
   memberOfAddressFieldsMapping : {
                                        'bill_street':'bill_street',
										'bill_pobox':'bill_pobox',
										'bill_city'	:'bill_city',
										'bill_state':'bill_state',
										'bill_code'	:'bill_code',
										'bill_country':'bill_country',
                                        'ship_street' : 'ship_street',
                                        'ship_pobox' : 'ship_pobox',
                                        'ship_city':'ship_city',
                                        'ship_state':'ship_state',
                                        'ship_code':'ship_code',
                                        'ship_country':'ship_country'
                                   },

    mailCommodities    : [],
    missingCommodities : [],
    hasValidationError : false,
    validationErrorMsg : '',

	/**
	 * This function will return the current form
	 */
	getForm : function(){
		if(this.editViewForm == false) {
			this.editViewForm = jQuery('#EditView');
		}
		return this.editViewForm;
	},

	/**
	 * This function will return the account name
	 */
	getAccountName : function(container){
		return jQuery('input[name="accountname"]',container).val();
	},

	/**
	 * This function will return the current RecordId
	 */
	getRecordId : function(container){
		return jQuery('input[name="record"]',container).val();
	},

	/**
	 * This function will register before saving any record
	 */
	registerRecordPreSaveEvent : function(form) {
		var thisInstance = this;
		if(typeof form == 'undefined') {
			form = this.getForm();
		}

		form.on(Vtiger_Edit_Js.recordPreSave, function(e, data) {
			if (!thisInstance.validateBillingAddressRows()) {
				return false;
			}
			// make sure transferee contact is set if visible
			if(jQuery('select[name="billing_type"]').val() == 'Consumer/COD')
			{
				if(!jQuery('input[name="transferee_contact"]').val())
				{
					bootbox.alert("Transferee Contact must be set. <br>");
					return false;
				}
			}
			//OT 16732
			if (jQuery('input[name="effective_date_to[]"]').length > 1)
			{
				if(!thisInstance.validateSalespersonEffectiveDates()){
					return false;
				}

			}

			var accountName = thisInstance.getAccountName(form);
			var recordId = thisInstance.getRecordId(form);
			var params = {};
            if(!(accountName in thisInstance.duplicateCheckCache)) {
				if(typeof form.data('accounts-submit') != "undefined") {
					e.preventDefault();
					return false;
				}
				form.data('accounts-submit', 'true');

				Vtiger_Helper_Js.checkDuplicateName({
                    'accountName' : accountName,
                    'recordId' : recordId,
                    'moduleName' : 'Accounts'
                }).then(
                    function(data){
                    	form.removeData('accounts-submit');
                        thisInstance.duplicateCheckCache[accountName] = data['success'];
                        form.submit();
                    },
                    function(data, err){
						form.removeData('accounts-submit');
                        thisInstance.duplicateCheckCache[accountName] = data['success'];
                        thisInstance.duplicateCheckCache['message'] = data['message'];
						var message = app.vtranslate('JS_DUPLICATE_CREATION_CONFIRMATION');
						Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
							function(e) {
								thisInstance.duplicateCheckCache[accountName] = false;
								form.submit();
							},
							function(error, err) {

							}
						);
                    }
				);
            } else {
				if(thisInstance.duplicateCheckCache[accountName] == true){
					var message = app.vtranslate('JS_DUPLICATE_CREATION_CONFIRMATION');
					Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
						function(e) {
							thisInstance.duplicateCheckCache[accountName] = false;
							form.submit();
						},
						function(error, err) {

						}
					);
				} else {
					delete thisInstance.duplicateCheckCache[accountName];
					return true;
				}
			}
            e.preventDefault();
		})
	},

	/**
	 * Function to swap array
	 * @param Array that need to be swapped
	 */
	swapObject : function(objectToSwap){
		var swappedArray = {};
		var newKey,newValue;
		for(var key in objectToSwap){
			newKey = objectToSwap[key];
			newValue = key;
			swappedArray[newKey] = newValue;
		}
		return swappedArray;
	},

	/**
	 * Function to copy address between fields
	 * @param strings which accepts value as either odd or even
	 */
	copyAddress : function(swapMode, container){
		var thisInstance = this;
		var addressMapping = this.addressFieldsMappingInModule;
		if(swapMode == "false"){
			for(var key in addressMapping) {
				var fromElement = container.find('[name="'+key+'"]');
				var toElement = container.find('[name="'+addressMapping[key]+'"]');
				toElement.val(fromElement.val());
			}
		} else if(swapMode){
			var swappedArray = thisInstance.swapObject(addressMapping);
			for(var key in swappedArray) {
				var fromElement = container.find('[name="'+key+'"]');
				var toElement = container.find('[name="'+swappedArray[key]+'"]');
				toElement.val(fromElement.val());
			}
		}
	},

	/**
	 * Function to register event for copying address between two fileds
	 */
	registerEventForCopyingAddress : function(container){
		var thisInstance = this;
		var swapMode;
		jQuery('[name="copyAddress"]').on('click',function(e){
			var element = jQuery(e.currentTarget);
			var target = element.data('target');
			if(target == "billing"){
				swapMode = "false";
			}else if(target == "shipping"){
				swapMode = "true";
			}
			thisInstance.copyAddress(swapMode, container);
		})
	},

	/**
	 * Function which will register event for Reference Fields Selection
	 */
	registerReferenceSelectionEvent : function(container) {
		var thisInstance = this;

		jQuery('input[name="account_id"]', container).on(Vtiger_Edit_Js.referenceSelectionEvent, function(e, data){
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
		thisInstance.getRecordDetails(data).then(
			function(data){
				var response = data['result'];
				thisInstance.mapAddressDetails(thisInstance.memberOfAddressFieldsMapping, response['data'], container);
			},
			function(error, err){

			});
	},

	/**
	 * Function which will map the address details of the selected record
	 */
	mapAddressDetails : function(addressDetails, result, container) {
		for(var key in addressDetails) {
			// While Quick Creat we don't have address fields, we should  add
            if(container.find('[name="'+key+'"]').length == 0) {
                   container.append("<input type='hidden' name='"+key+"'>");
            }
			container.find('[name="'+key+'"]').val(result[addressDetails[key]]);
			container.find('[name="'+key+'"]').trigger('change');
			container.find('[name="'+addressDetails[key]+'"]').val(result[addressDetails[key]]);
			container.find('[name="'+addressDetails[key]+'"]').trigger('change');
		}
	},

	registerAnnualRateEvents : function(){
		thisInstance = this;
		//console.dir('initialize AnnRate');
		thisInstance.annualRatesIncrease = Accounts_AnnualRateIncrease_Js.getInstance();
		thisInstance.annualRatesIncrease.registerEvents();
	},

	registerAddressAutoLookup : function(sequence) {
		var thisInstance = this;
		var row = jQuery('#billing_address1'+sequence).closest('tr');

		if(jQuery('#billing_address1' + sequence).length) {
			address1 = new google.maps.places.Autocomplete(
				(document.getElementById('billing_address1' + sequence)),
				{ types: ['geocode'] });

			google.maps.event.addListener(address1, 'place_changed', function() {
				thisInstance.fillInBillingAddresses(row, address1, sequence);
			});
		}
		if(jQuery('#billing_address2' + sequence).length) {
			address2 = new google.maps.places.Autocomplete(
				(document.getElementById('billing_address2' + sequence)),
				{ types: ['geocode'] });

			google.maps.event.addListener(address2, 'place_changed', function() {
				thisInstance.fillInBillingAddresses(row, address2, sequence);
			});
		}
		if(jQuery('#billing_city' + sequence).length) {
			city = new google.maps.places.Autocomplete(
				(document.getElementById('billing_city' + sequence)),
				{ types: ['geocode'] });

			google.maps.event.addListener(city, 'place_changed', function() {
				thisInstance.fillInBillingAddresses(row, city, sequence);
			});
		}
		if(jQuery('#billing_state' + sequence).length) {
			state = new google.maps.places.Autocomplete(
				(document.getElementById('billing_state' + sequence)),
				{ types: ['geocode'] });

			google.maps.event.addListener(state, 'place_changed', function() {
				thisInstance.fillInBillingAddresses(row, state, sequence);
			});
		}
		if(jQuery('#billing_zip' + sequence).length) {
			zip = new google.maps.places.Autocomplete(
				(document.getElementById('billing_zip' + sequence)),
				{ types: ['geocode'] });

			google.maps.event.addListener(zip, 'place_changed', function() {
				thisInstance.fillInBillingAddresses(row, zip, sequence);
			});
		}
		if(jQuery('#billing_country' + sequence).length) {
			country = new google.maps.places.Autocomplete(
				(document.getElementById('billing_country' + sequence)),
				{ types: ['geocode'] });

			google.maps.event.addListener(country, 'place_changed', function() {
				thisInstance.fillInBillingAddresses(row, country, sequence);
			});
		}
	},

	fillInBillingAddresses: function(row, autoComplete, sequence) {
		var place = autoComplete.getPlace();

		var address = '';
		var city = '';
		var state = '';
		var country = '';
		var zip = '';
		var streetNum = '';

		if(typeof place.address_components != 'undefined') {

			for (var i=0; i<place.address_components.length; i++) {
				var addressType = place.address_components[i].types[0];

				if(addressType == 'street_number') {
					//streetNum = place.address_components[i].long_name;
					streetNum = place.address_components[i].short_name;
				}
				if(addressType == 'route') {
					address = " "+place.address_components[i].long_name;
				}
				if(addressType == 'locality') {
					city = place.address_components[i].long_name;
				}

				if(addressType == 'administrative_area_level_1') {
					//state = place.address_components[i].long_name;
					state = place.address_components[i].short_name;
				}
				if(addressType == 'postal_code') {
					//zip = place.address_components[i].long_name;
					zip = place.address_components[i].short_name;
				}
				if(addressType == 'country') {
					country = place.address_components[i].long_name;
				}
			}

			if (address) {
				if(streetNum) {
					row.find('#billing_address1' + sequence).val(streetNum+' '+address);
				} else {
					row.find('#billing_address1' + sequence).val(address);
				}
			} else {
				row.find('#billing_address1' + sequence).val('');
			}

			if (city) {
				row.find('#billing_city' + sequence).val(city);
			} else {
				row.find('#billing_city' + sequence).val('');
			}

			if (state) {
				row.find('#billing_state' + sequence).val(state);
			} else {
				row.find('#billing_state' + sequence).val('');
			}

			if (country) {
				row.find('#billing_country' + sequence).val(country);
			} else {
				row.find('#billing_country' + sequence).val('');
			}

			if (zip) {
				row.find('#billing_zip' + sequence).val(zip);
			} else {
				row.find('#billing_zip' + sequence).val('');
			}
		}



	},

	registerAddBillingAddresses : function(){
		var billingAddressBtn = jQuery('.addBillingAddress');
		var thisInstance = this;

		jQuery('[name="billing_id[]"]').each(function(index) {
			if(index!=0) {
				console.log(index);
				thisInstance.registerAddressAutoLookup(index);
			}
		});

		//handler to add new annual rate increase row
		var newBillingAddress = function(){
			var newAddress = jQuery('.defaultBillingAddress').clone(true,true);
			newAddress.removeClass('hide').removeClass('defaultBillingAddress chzn-done');
			var sequence = parseInt(jQuery('input:hidden[name="billingAddressCount"]').val());
			sequence++;
			newAddress.find('select[name="commodity[][]"]').prop('required', true).attr('name', 'commodity['+sequence+'][]');
			newAddress.find('input[name="billing_address_desc[]"]').prop('required', true);
			newAddress.find('input[name="billing_company_name[]"]').prop('required', true);
			newAddress.find('input[name="billing_address1[]"]').prop('required', true).attr('id', 'billing_address1'+sequence);
			newAddress.find('input[name="billing_address2[]"]').attr('id', 'billing_address2'+sequence);
			newAddress.find('input[name="billing_city[]"]').prop('required', true).attr('id', 'billing_city'+sequence);
			newAddress.find('input[name="billing_state[]"]').prop('required', true).attr('id', 'billing_state'+sequence);
			newAddress.find('input[name="billing_zip[]"]').prop('required', true).attr('id', 'billing_zip'+sequence);
			newAddress.find('input[name="billing_country[]"]').prop('required', true).attr('id', 'billing_country'+sequence);

			jQuery('input:hidden[name="billingAddressCount"]').val(sequence);


			newAddress.find('select:not(.multipicklistall)').addClass('selections');
			newAddress.removeClass('defaultBillingAddress').removeClass('hide chzn-done');
			jQuery(newAddress).appendTo('#billingAddressesTable');
			jQuery('.selections').chosen();
			app.showSelect2ElementView(jQuery('.multipicklistall',newAddress));
			thisInstance.registerAddressAutoLookup(sequence);
		}


		billingAddressBtn.on('click', newBillingAddress);
	},

	deleteBillingAddressEvent : function(){
		var thisInstance = this;
		jQuery('.deleteBillingAddress').on('click', function() {
			var currentRow = jQuery(this).closest('tr');
			currentRow.remove();
		});
	},

	registerAddInvoiceSetting : function(){
		var thisInstance = this;
		var billingAddressBtn = jQuery('.addInvoiceSetting');

		//handler to add new annual rate increase row
		var newInvoiceSetting = function(){
			var newInvoice = jQuery('.defaultInvoiceSetting').clone(true,true);
			newInvoice.removeClass('hide').removeClass('defaultInvoiceSetting chzn-done');
			var sequence = parseInt(jQuery('input:hidden[name="InvoiceSettingCount"]').val());
			sequence++;
			newInvoice.find('select[name="invoice_commodity[][]"]').prop('required', true).attr('name', 'invoice_commodity['+sequence+'][]');
			newInvoice.find('.InvoiceSettingCount').html(sequence+'.');
			jQuery('input:hidden[name="InvoiceSettingCount"]').val(sequence);
			newInvoice.find('input').prop('required', true);


			newInvoice.find('select:not(.multipicklistall)').addClass('selections');
			newInvoice.removeClass('defaultInvoiceSetting').removeClass('hide chzn-done');
			jQuery(newInvoice).appendTo('#InvoiceSettingsTable');
			jQuery('.selections').chosen();
			app.showSelect2ElementView(jQuery('.multipicklistall',newInvoice));
			thisInstance.registerInvoiceDeliveryPreferenceRequiredFields();
		}

		billingAddressBtn.on('click', newInvoiceSetting);
	},

	deleteInvoiceSettingEvent : function(){
		var thisInstance = this;
		jQuery('.deleteInvoiceSetting').on('click', function() {
			var currentRow = jQuery(this).closest('tr');
			currentRow.remove();

			jQuery('.invoiceSettingCount').each(function(index) {
				jQuery('input:hidden[name="invoiceSettingCount"]').val(index);
			});
		});
	},

	registerAddAdditionalRoles : function(){
		var thisInstance = this;
		var billingAddressBtn = jQuery('.addAdditionalRole');

		//handler to add new annual rate increase row
		var newInvoiceSetting = function(){
			var newInvoice = jQuery('.defaultAdditionalRole').clone(true, true);
			newInvoice.removeClass('hide').removeClass('defaultAdditionalRole chzn-done');
			var sequence = parseInt(jQuery('input:hidden[name="additionalRolesCount"]').val());

			sequence++;
			jQuery('input:hidden[name="additionalRolesCount"]').val(sequence);
			var currentRow = jQuery(this).closest('tr');
			newInvoice.find('select[class!="multipicklistall"]').addClass('selections');

			jQuery(newInvoice).appendTo('#additionalRolesTable');
			var table = currentRow.closest('table');
			table.find('select[name^="role_commodity"]').each(function (row_index) {
				var focus = $(this);
				// var myRow = focus.closest('tr');
				// var row_index = focus.index($('select[name^="role_commodity"]'));
				focus.attr('name', 'role_commodity[' + row_index + '][]');
			});

			newInvoice.find('select.multipicklistall').select2();
			thisInstance.registerClearReferenceSelectionEvent(newInvoice);
			newInvoice.find('.selections').chosen();

		};

		billingAddressBtn.on('click', newInvoiceSetting);

	},

	deleteAdditionalRoles : function(){
		var thisInstance = this;
		jQuery('.deleteAdditionalRole').on('click', function() {
			var currentRow = jQuery(this).closest('tr');
			var table = currentRow.closest('table');
			currentRow.remove();
			table.find('select[name^="role_commodity"]').each(function (row_index) {
				var focus = $(this);
				focus.attr('name', 'role_commodity[' + row_index + '][]');
			});

		});
	},

	registerSalesPersonEvents : function(){
		thisInstance = this;
		try {
			thisInstance.registerAddSalesPerson = Accounts_SalesPerson_Js.getInstance();
			thisInstance.registerAddSalesPerson.registerEvents();
		} catch (errSPE) {
			//do nothing with this failure, it just means we were unable to load this js because it's not here.
		}
	},

	//set special validation for APN
	setAPN : function() {
		var apn = jQuery('input[name="apn"]');
		//soo I give in and just use attr to set the string I want.
		//NOT mandatory
		//var priorLabel = apn.closest('td').prev('td').children();
		//var value = priorLabel.html();
		//priorLabel.html('<span class="redColor">*</span> ' + value);
		apn.attr('data-validation-engine', 'validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation],custom[integer],minSize[8],maxSize[8]]');
	},

	registerInvoiceDeliveryPreferenceRequiredFields : function() {
        var thisInstance = this;
		jQuery('tr:not(.hide) select[name^="invoice_delivery[]"]').off('change').on('change', function() {
			console.log('change triggered');
			var hasEmail        = false;
			var hasPortal       = false;
			var hasMail         = false;

			thisInstance.mailCommodities = [];
			jQuery('tr:not(.hide) select[name^="invoice_delivery"]').each(function() {
				switch(jQuery(this).find('option:selected').val()) {
					case 'E-mail':
						hasEmail = true;
						break;
					case 'Customer Portal':
						hasPortal = true;
						break;
					case 'Mail':
						hasMail = true;
						//Get the Commodity of the current row, then hold on to it for comparison to the Billing Addresses block
                        thisInstance.mailCommodities.push(jQuery(this).closest('tr').find('select[name^="invoice_commodity"]').find('option:selected').val());
						break;
					default:
						break;
				}
			});

			if(hasEmail) {
				jQuery('input[name="email1"]').data('validationEngine', 'validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]');
				jQuery('input[name="email1"]').attr('data-validation-engine', 'validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]');
				//Add red star to indicate required field
				var fieldLabel = jQuery('input[name="email1"]').closest('td').prev('.fieldLabel').find('label');
				fieldLabel.prepend('<span id="requiredEmailLabel" class="redColor">*</span>');
			} else {
				jQuery('input[name="email1"]').data('validationEngine', 'validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]');
				jQuery('input[name="email1"]').attr('data-validation-engine', 'validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]');
				jQuery('#requiredEmailLabel').remove();
			}

			if(hasPortal) {
				jQuery('input[name="phone"]').data('validationEngine', 'validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]');
				jQuery('input[name="phone"]').attr('data-validation-engine', 'validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]');
				//Add red star to indicate required field
				var fieldLabel = jQuery('input[name="phone"]').closest('td').prev('.fieldLabel').find('label');
				fieldLabel.prepend('<span id="requiredPhoneLabel" class="redColor">*</span>');
			} else {
				jQuery('input[name="phone"]').data('validationEngine', 'validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]');
				jQuery('input[name="phone"]').attr('data-validation-engine', 'validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]');
				jQuery('#requiredPhoneLabel').remove();
			}

			if(hasMail) {
                //Make sure that there is a corresponding row in the Billing Addresses table for the commodities with Mail selected
                var table = jQuery('#billingAddressesTable');
                thisInstance.missingCommodities = [];
				//Iterate over the commodities that require Billing Address info
                for(var i=0; i<thisInstance.mailCommodities.length; i++) {
                    var commodity = thisInstance.mailCommodities[i];
                    if(table.find('option:selected[value="'+commodity+'"]').length == 0) {
                        thisInstance.missingCommodities.push(commodity);
                    }
                }
			}
		});
	},

	validateSalespersonEffectiveDates : function() {
		var today = new Date();
		var stopSave = false;
		var count = 0;
		jQuery('input[name="effective_date_to[]"]').each( function() {
			//Using count value to ignore hidden field used for default row template.
			if(count > 0 && !stopSave) {
				var currentRow = jQuery(this).closest('tr');
				var dateTo = new Date(jQuery(this).val());
				var dateFrom = new Date(currentRow.find('input[name^="effective_date_from"]').val());
				var salesPerson = currentRow.find('select[name^="sales_person"]').find(":selected").text();
				var bookingOffice = currentRow.find('select[name^="booking_office"]').find(":selected").text();
				var businessLine = currentRow.find('select[name^="salesperson_commodity"]').find(":selected").text();

				if (!salesPerson || salesPerson == 'Select an Option' ||
					!bookingOffice || bookingOffice == 'Select an Option' ||
					!businessLine || businessLine == 'Select an Option')
				{
					bootbox.alert ("Salesperson row " + count + " is missing required information.");
					stopSave = true;
					return false;
				}
				if (dateTo < dateFrom) {
					bootbox.alert("Error in Salesperson row for " + salesPerson + ":<br/>Effective Date To field cannot predate Effective Date From date.");
					stopSave = true;
					return false;
				} else if ( today > dateTo){
					bootbox.alert("Error in Salesperson row for " + salesPerson + ":<br/>Effective Date To field cannot predate today's date.");
					stopSave = true;
					return false;
				}
			}
			count++;
		});
		return !stopSave;
	},

    validateBillingAddressRows : function() {

        var thisInstance = this;
        thisInstance.mailCommodities = [];
		var invoiceSelect = jQuery('#InvoiceSettingsTable').find('tr:not(.hide) select');
		var failed = false;
		invoiceSelect.each(function() {
			if(jQuery(this).val() == 'Select an Option'
			|| jQuery(this).val() == '')
			{
				failed = true;
			}
		});
		if(failed)
		{
			bootbox.alert('All invoice settings are required to be filled out. ');
			return false;
		}
		var invoicePayment = jQuery('#InvoiceSettingsTable').find('tr:not(.hide) input[name^="payment_terms"]');
		invoicePayment.each(function() {
			var str = jQuery(this).val();
			var n = Math.floor(Number(str));
			if(String(n) !== str || n < 0)
			{
				failed = true;
			}
		});
		if(failed)
		{
			bootbox.alert('Payment terms must be a positive integer. ');
			return false;
		}

        jQuery('tr:not(.hide) select[name^="invoice_delivery"]').each(function() {
            switch(jQuery(this).find('option:selected').val()) {
                case 'Mail':
                    //Get the Commodity of the current row, then hold on to it for comparison to the Billing Addresses block
                    thisInstance.mailCommodities.push(jQuery(this).closest('tr').find('select[name^="invoice_commodity"]').find('option:selected').val());
                    break;
                default:
                    break;
            }
        });

        //Make sure that there is a corresponding row in the Billing Addresses table for the commodities with Mail selected
        var table = jQuery('#billingAddressesTable');
        thisInstance.missingCommodities = [];
        //Iterate over the commodities that require Billing Address info
        for(var i=0; i<thisInstance.mailCommodities.length; i++) {
            var commodity = thisInstance.mailCommodities[i];
            if(table.find('option:selected[value="'+commodity+'"]').length == 0) {
                thisInstance.missingCommodities.push(commodity);
            }
        }

        if(thisInstance.missingCommodities.length > 0) {
            thisInstance.hasValidationError = true;
            thisInstance.validationErrorMsg = "The following Commodities have an Invoice Delivery preference set to Mail without a corresponding Billing Addresses row:<br>";

            for(var i=0; i<thisInstance.missingCommodities.length; i++) {
                thisInstance.validationErrorMsg += thisInstance.missingCommodities[i]+"<br>";
            }
        } else {
            thisInstance.hasValidationError = false;
            thisInstance.validationErrorMsg = "";
        }

        if(thisInstance.hasValidationError) {
            bootbox.alert(thisInstance.validationErrorMsg);
            //e.preventDefault();
            return false;
        } else {
            if(jQuery('.formErrorContent').length == 0) {
                //jQuery('#EditView').off('submit');
                //jQuery('#EditView').submit();
				return true;
            }
        }
		return false;
    },

	registerBillingTypeChangeEvent : function() {
    	jQuery('select[name="billing_type"]').on('change', function() {
			var selectedValue = jQuery(this).val();
			var contactFieldValue = jQuery('input[name="transferee_contact"]');
			if(selectedValue == 'Consumer/COD')
			{
				contactFieldValue.closest('td').prev('td').removeClass('hide');
				contactFieldValue.closest('td').removeClass('hide');
			} else {
				contactFieldValue.closest('td').prev('td').addClass('hide');
				contactFieldValue.closest('td').addClass('hide');
				contactFieldValue.val('');
				jQuery('input[name="transferee_contact_display"]').attr('readonly', false).val('');
			}
		}).trigger('change');
	},

    applyBillingTypeRules : function (isEditView) {
        if (jQuery('input[name="instance"]').val() != 'graebel') {
            return;
        }
        var rules = {};
        rules['billing_type'] = {
            conditions: [
                {
                    operator: 'is',
                    not: true,
                    value: 'Consumer/COD',
                    targetFields: [
                        {
                            name: 'credit_limit',
                            mandatory: true,
                        }
                    ],
                },
                //unrequired the default state is not-mandatory.
                // {
                //     operator: 'is',
                //     value: 'Consumer/COD',
                //     targetFields: [
                //         {
                //             name: 'credit_limit',
                //             unmandatory: true,
                //         }
                //     ],
                // },
            ],
        };
        this.applyVisibilityRules(rules, isEditView);
    },

	getPopUpParams : function(container) {
		var params = {};
		var sourceModule = app.getModuleName();
		var popupReferenceModule = jQuery('input[name="popupReferenceModule"]',container).val();
		var sourceFieldElement = jQuery('input[class="sourceField"]',container);
		var sourceField = sourceFieldElement.attr('name');
		var sourceRecordElement = jQuery('input[name="record"]');
		var sourceRecordId = '';
		if(sourceRecordElement.length > 0) {
			sourceRecordId = sourceRecordElement.val();
		}

		var isMultiple = false;
		if(sourceFieldElement.data('multiple') == true){
			isMultiple = true;
		}
		var role = '';
		if (container.find('.Employees').length > 0) {
			role = container.closest('tr').find('.EmployeeRoles').val();
			if(!role){
				role = 'false';
			}
		}
		var params = {
			'module' : popupReferenceModule,
			'src_module' : sourceModule,
			'src_field' : sourceField,
			'src_record' : sourceRecordId,
			'role' : role,
		};

		if(isMultiple) {
			params.multi_select = true ;
		}
		return params;
	},
	onChangeOwner : function(container) {
		var currentAgentid = $('[name="agentid"]').val();

		container.find('[name="agentid"]').on('change',function(e){
				if($(this).val() != currentAgentid){
					$('.EmployeeRoles').val('');
					$('.EmployeeRoles').closest('td').find('.autoComplete').val('');
					$('.EmployeeRoles').closest('td').find('.autoComplete').removeAttr('readonly');

					$('.Employees').val('');
					$('.Employees').closest('td').find('.autoComplete').val('');
					$('.Employees').closest('td').find('.autoComplete').removeAttr('readonly');
				}

			});

	},
	removeSign:function (container) {
		container.find('.icon-remove-sign').on('click',function () {
			$(this).closest('td').find('.autoComplete').val('');
			if($(this).closest('td').find('.EmployeeRoles').length > 0){
				$(this).closest('td').find('.EmployeeRoles').val('');
				$(this).closest('td').find('+ td .Employees').val('');
				$(this).closest('td + td').find('+ td .autoComplete').val('');
				$(this).closest('td + td').find('+ td .autoComplete').removeAttr('readonly');


			}
			if($(this).closest('td').find('.Employees').length > 0){
				$(this).closest('td').find('.Employees').val('');
			}
			$(this).closest('td').find('.autoComplete').removeAttr('readonly');
		})
	},
    registerClearReferenceSelectionEvent : function(container) {
        container.find('.clearReferenceSelection').on('click', function(e){
            var element = jQuery(e.currentTarget);
            if(element.closest('#additionalRolesTable').length > 0){
                var parentTdElement = element.closest('td');
                if(parentTdElement.find('[name="popupReferenceModule"]').val() == "EmployeeRoles"){
                    var parentTdElement1 = element.closest('td').next();
                    var fieldNameElement1 = parentTdElement1.find('.sourceField');
                    fieldNameElement1.val('');
                    parentTdElement1.find('.autoComplete').removeAttr('readonly');
                    parentTdElement1.find('.autoComplete').val('');
                    element.trigger(Vtiger_Edit_Js.referenceDeSelectionEvent);
                }
                var fieldNameElement = parentTdElement.find('.sourceField');
                fieldNameElement.val('');
                parentTdElement.find('.autoComplete').removeAttr('readonly');
                parentTdElement.find('.autoComplete').val('');
                element.trigger(Vtiger_Edit_Js.referenceDeSelectionEvent);
                e.preventDefault();
            }else{
                var parentTdElement = element.closest('td');
                var fieldNameElement = parentTdElement.find('.sourceField');
                var fieldName = fieldNameElement.attr('name');
                fieldNameElement.val('');
                parentTdElement.find('#'+fieldName+'_display').removeAttr('readonly').val('');
                element.trigger(Vtiger_Edit_Js.referenceDeSelectionEvent);
                fieldNameElement.trigger(Vtiger_Edit_Js.referenceDeSelectionEvent);
                e.preventDefault();
            }
        })
    },
	getReferenceSearchParams : function(element){
		var tdElement = jQuery(element).closest('td');
		var params = {};
		var employeeroleid = tdElement.prev().find('.EmployeeRoles').val();
		var searchModule = this.getReferencedModuleName(tdElement);
		params.search_module = searchModule;
		params.parent_id = employeeroleid;
		params.parent_module = 'EmployeeRoles';
		return params;
	},
	setReferenceFieldValue : function(container, params) {
		// console.log('fsdafs')
		var sourceField = container.find('input.sourceField').attr('name');

		var fieldElement = container.find('input[name="'+sourceField+'"]');
		var fieldName = sourceField.replace("[]", "");
		console.log(fieldName);

		var sourceFieldDisplay = fieldName+"_display";
		var fieldDisplayElement = container.find('input[name="'+sourceFieldDisplay+'"]');
		var popupReferenceModule = container.find('input[name="popupReferenceModule"]').val();

		var selectedName = params.name;
		var id = params.id;

		fieldElement.val(id);
		fieldDisplayElement.val(selectedName).attr('readonly',true);
		fieldElement.trigger(Vtiger_Edit_Js.referenceSelectionEvent, {'source_module' : popupReferenceModule, 'record' : id, 'selectedName' : selectedName});

		fieldDisplayElement.validationEngine('closePrompt',fieldDisplayElement);
	},
	registerEventsForMultipicklistall : function(form) {
		form.find('select.multipicklistall').on('focus', function (e) {
			var element=jQuery(e.currentTarget);
			element.data('pre-values', element.val());
		}).change(function(e) {
			var element=jQuery(e.currentTarget);
			var preVals=element.data('pre-values');
			var currentVals=element.val();
			if(jQuery.inArray("All",preVals) !== -1 && typeof preVals != 'undefined') {
				// Remove "All"
				element.find('option[value="All"]').prop('selected',false);
				element.select2();
			}else{
				if(jQuery.inArray("All",currentVals) !== -1 && typeof currentVals != 'undefined'){
					// Remove all values != "All"
					element.find('option[value!="All"]').prop('selected',false);
					element.select2();
				}
			}
			element.data('pre-values', element.val());
			var fieldName = element.attr('name');
			if(fieldName == 'business_line[]'){
				var businessLineSelected = element.val();
				var options = "<option value='All'>All</option>";
				if(businessLineSelected == undefined || businessLineSelected == null || businessLineSelected.indexOf('All') != -1){
					var fieldInfo = element.data('fieldinfo');
					if(fieldInfo!=undefined && fieldInfo != null){
						if(typeof  fieldInfo != 'object'){
							fieldInfo = JSON.parse(fieldInfo);
						}
						var picklistValues = fieldInfo.picklistvalues;
						if(picklistValues != undefined && picklistValues != null){
							jQuery.each(picklistValues,function (i,v) {
								options +="<option value='"+v+"'>"+v+"</option>";
							});
						}
					}
				}else{
					var picklistValues = businessLineSelected;
					jQuery.each(businessLineSelected,function (i,v) {
						options +="<option value='"+v+"'>"+v+"</option>";
					});
				}
				jQuery('[name^="commodity["],[name^="invoice_commodity["],[name^="role_commodity["]').each(function () {
					var focus = jQuery(this);
					var selected = jQuery(this).val();
					jQuery(this).html(options);
					if(selected != undefined && selected != null){
						if(picklistValues != undefined && selected.length == picklistValues.length && selected.length > 0){
							focus.find('option[value="All"]').prop('selected',true);
						}else{
							jQuery.each(selected,function (i,v) {
								focus.find('option[value="'+v+'"]').prop('selected',true);
							});
						}
					}
					if(jQuery(this).is('.select2')){
						jQuery(this).select2();
					}
				});
			}
		});
		form.find('select.multipicklistall').trigger("change");
	},

	registerCustomerNumberChange : function()
	{
		jQuery('.contentsDiv').on(Vtiger_Edit_Js.postReferenceSelectionEvent, '[name="customer_number"]', function(e,data){
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
						'accountname': 'label',
						'address1': 'Address 1',
						'address2': 'Address 2',
						'city': 'City',
						'state': 'State',
						'zip': 'Zip',
						'country': 'Country',
						'phone': 'Primary Phone',
						'otherphone': 'Secondary Phone',
						'fax': 'Fax',
						'email1': 'Primary Email',
						'website': 'Website',
					};
					Vtiger_Edit_Js.populateData(data, map);
				},
				function(error, err) {
					//they pressed no don't populate the data.
				}
			);
		});
	},


    registerEvents : function() {
        this._super();
        this.applyBillingTypeRules(true);
        this.registerBillingTypeChangeEvent();
		this.registerCustomerNumberChange();
    },

	/**
	 * Function which will register basic events which will be used in quick create as well
	 *
	 */
	registerBasicEvents : function(container) {
		this._super(container);
		this.registerEventForCopyingAddress(container);
		this.registerReferenceSelectionEvent(container);
        this.registerSalesPersonEvents();
		this.registerAnnualRateEvents();
		this.registerAddBillingAddresses();
		this.deleteBillingAddressEvent();
		this.registerAddInvoiceSetting();
		this.deleteInvoiceSettingEvent();
		this.registerAddAdditionalRoles();
		this.deleteAdditionalRoles();
		//container.trigger(Vtiger_Edit_Js.recordPreSave, {'value': 'edit'});
		this.initializeAddressAutofill('Accounts');
		this.initializeReverseZipAutoFill('Accounts');
		this.setAPN();
		this.registerInvoiceDeliveryPreferenceRequiredFields();
		// this.referenceModuleMoveRolesPopupRegisterEvent(container);
		this.registerBillingTypeChangeEvent();
		this.onChangeOwner(container);
		this.removeSign(container);
		this.registerClearReferenceSelectionEvent(container);
	}
});
