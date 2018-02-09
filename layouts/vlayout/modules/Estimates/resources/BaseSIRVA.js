Estimates_BaseTariff_Js("Estimates_BaseSIRVA_Js", {
	getInstance: function() {
		if(Estimates_BaseSIRVA_Js.currentInstance)
		{
			return Estimates_BaseSIRVA_Js.currentInstance;
		}
		Estimates_BaseSIRVA_Js.currentInstance = new Estimates_BaseSIRVA_Js();
		return Estimates_BaseSIRVA_Js.currentInstance;
	},
	I: function(){
		return Estimates_BaseSIRVA_Js.getInstance();
	},
}, {

	/**
	 * Compare user submitted dates to Transit Guide.
	 * @param  {string} for_instance   The instance it should run in, set to 'ANY' in order to allow for all.
	 *     @NOTE: If the current instance is not equal to the instance provided, it will pass without checking.
	 * @param  {function} ok_callback  The function to call on no error.
	 * @param  {function} err_callback The function to call on error.
	 * @return {undefined}             Returns nothing, uses specified callbacks instead.
	 */
	confirmDatesWithTransitGuide: function(for_instance, ok_callback, err_callback) {
		var current_instance = $('input[name="instance"]').val();
		if(for_instance != 'ANY' && current_instance != for_instance) {
			// Automatically pass this logic check if not the instance this is being called for.
			ok_callback({notice: 'Instance does not need to check Transit Guide, passing...'});
		} else {
			// Load Date is only required if pricing level and color are locked.
			var pricing_lock = jQuery('input:checkbox[name="pricing_color_lock"]').is(":checked");
			// We don't care about transit guide if pricing can be manually set.
			if(!pricing_lock) {
				return ok_callback({notice: 'Pricing can be manually set, not checking with transit guide.'});
			}

			// Gather date object for comparison
			//@TODO: This is disgusting, but it works.
			var user_dates = {
				load_date: {
					value: $('input[name="load_date"]').val(),
					label: 'Load From Date',
					required: true
				},
				load_to_date: {
					value: $('input[name="load_to_date"]').val(),
					label: 'Load To Date',
					required: true
				},
				deliver_date: {
					value: $('input[name="deliver_date"]').val(),
					label: 'Deliver From Date',
					required: false
				},
				deliver_to_date: {
					value: $('input[name="deliver_to_date"]').val(),
					label: 'Deliver To Date',
					required: false
				}
			};

			// Run transit guide to confirm everything is handy dandy.
			this.getTransitGuide(function(data) {
				// Transit Guide dates returned from AJAX call.
				var tg_dates = data.result.standard;

				// Compare user submitted dates with transit guide dates.
				var err = '';
				var warn = '';
				Object.keys(user_dates).forEach(function(key, index) {
					if(user_dates[key].required && user_dates[key].value == '') {
						// If a required date is empty, throw an error.
						err += '<li>' + user_dates[key].label + ' is required and cannot be empty.</li>';
					}else if(user_dates[key].value != tg_dates[key]) {
						// If a date sent by user fields does not equal Transit Guide's return, warn them.
						warn += '<li>' + user_dates[key].label + ' is incorrect, does not match Transit Guide.</li>';
					}
				});

				// Send back any warnings.
				if(warn != '') {
					warn += 'It is recommended you run Transit Guide after rating.';
					data.warnings = warn;
				}

				// Use the right callback.
				if(err != '') {
					err = "There were errors attempting to rate this Estimate:<br/>" + err;
					err_callback(err);
				}else{
					ok_callback(data);
				}
			}, function(err) {
				err_callback(err);
			});
		}
	},

	/*
	 * Function to show the transit date picker messagebox
	 */
	showTransitGuideBox : function(data) {
		var aDeferred = jQuery.Deferred();
		var standard = false;
		var optional = false;

		var button = {};

		for (var desc in data.results) {
			//@TODO: this should be removed...
			if (desc == 'standard') {
				standard = true;
			}
			if (desc == 'optional') {
				optional = true;
			}
			//
			//	//@TODO: Figure out how to make this set a callback to use with the correct results.
			//	//@TODO: Because variables are by ref then it's always the "last" one.
			//	if (desc.length > 0) {
			//		button[desc] = {
			//			'label': app.vtranslate(desc),
			//			//'className' : "btn-tg-" + desc.toLowerCase(),
			//			'className': "btn-danger",
			//			callback: function () {
			//				aDeferred.resolve(data.results[desc]);
			//			}
			//			/*
			//			 // this processes the function when it sees it instead of setting(?) the function.
			//			 callback : (function(aDef, val) {
			//			 aDef.resolve(val);
			//			 })(aDeferred, data.results[desc])
			//			 */
			//		}
			//	}
		}

		//@TODO: not do this... seriously... make that loop work for the love of god.
		if (standard) {
			button['Standard'] = {
				'label': app.vtranslate('Standard'),
				'className': "btn-danger",
				callback: function () {
					aDeferred.resolve(data.results['standard']);
				}
			}
		}
		if (optional) {
			button['Optional'] = {
				'label': app.vtranslate('Optional'),
				'className': "btn-danger",
				callback: function () {
					aDeferred.resolve(data.results['optional']);
				}
			}
		}

		var bootBoxModal = bootbox.dialog({
			message: data['message'],
			buttons: button
		});

		bootBoxModal.on('hidden', function (e) {
			//In Case of multiple modal. like mass edit and quick create, if bootbox is shown and hidden , it will remove
			// modal open
			if (jQuery('#globalmodal').length > 0) {
				// Mimic bootstrap modal action body state change
				jQuery('body').addClass('modal-open');
			}
		});
		return aDeferred.promise();
	},
	/**
	 * Get transit guide and callback to appropriate function on result.
	 * @param  {function} ok_callback  The function to call on no error.
	 * @param  {function} err_callback The function to call on error.
	 * @return {undefined}             Returns nothing, uses specified callbacks instead.
	 */
	getTransitGuide: function(ok_callback, err_callback) {
		var load_date = jQuery('input[name="load_date"]').val();
		var origin_zip = jQuery('input[name="origin_zip"]').val();
		var destination_zip = jQuery('input[name="destination_zip"]').val();
		var origin_country = jQuery('input[name="nameestimates_origin_country"]').val();
		var destination_country = jQuery('input[name="estimates_destination_country"]').val();
		var tariff = jQuery('input[name="effective_tariff_holder"]').val() == '' ? '' : jQuery('select[name="effective_tariff"]').val();
		var record = jQuery('input[name="record"]').val();
		var express_truckload = jQuery('#effective_tariff_custom_type').val() == 'Truckload Express';
		var agentid = jQuery('select[name="agentid"]').val();
		var business_line_est = jQuery('select[name="business_line_est"]').val();
		var weight = jQuery('input[name="weight"]').val();
		var extra_stops_origin = [];
		var extra_stops_destination = [];

		var error = '';
		if(load_date == ''){
			error = error + '<br />- Load Date';
		}
		if(origin_zip == ''){
			error = error + '<br />- Origin Zip';
		}
		if(destination_zip == ''){
			error = error + '<br />- Destination Zip';
		}
		if(tariff == ''){
			error = error + '<br />- Effective Tariff';
		}
		if(agentid == ''){
			error = error + '<br />- Owner';
		}
		if(business_line_est == ''){
			error = error + '<br />- Business Line';
		}
		if(weight == ''){
			error = error + '<br />- Weight';
		}
		if(error != ''){
			err_callback('Please make sure all required fields are filled out:' + error);
			return;
		}

		var numStops = jQuery('#numStops').val();
		for (var i = 1; i <= numStops; i++) {
			//var extraStop = jQuery('#extrastops_id_'+ i).closest('tbody');
			var sequence = jQuery('input[name="extrastops_sequence_' + i + '"]').val();
			var zip = jQuery('input[name="extrastops_zip_' + i + '"]').val();
			var type = jQuery('select[name="extrastops_type_' + i + '"]').val();

			if (zip !== 'undefined') {
				if (sequence === 'undefined') {
					sequence = i+numStops;  //just set it to the "end"
				}
				if (type == 'Origin' || type == 'Extra Pickup') {
					extra_stops_origin[sequence] = zip;
				} else if (type == 'Destination' || type == 'Extra Delivery') {
					extra_stops_destination[sequence] = zip;
				}
			}
		}

		//leaving it open to have the load_date entered from the popup.
		var url = 'index.php?module=Opportunities&action=GetTransitGuide'
			+ '&load_date=' + load_date
			+ '&origin_zip=' + origin_zip
			+ '&destination_zip=' + destination_zip
			+ '&origin_country=' + origin_country
			+ '&destination_country=' + destination_country
			+ '&record=' + record
			+ '&extra_stops_origin=' + extra_stops_origin.toString()
			+ '&extra_stops_destination=' + extra_stops_destination.toString()
			+ '&edit=1'
			+ '&tariff=' + tariff
			+ '&agentid=' + agentid
			+ '&weight=' + weight
			+ '&express_truckload=' + express_truckload
			+ '&business_line_est=' + business_line_est;

		AppConnector.request(url).then(
			function(data) {
				if(data.success) {
					ok_callback(data);
				} else {
					err_callback(data.error);
				}
			},
			function(error) {
				console.dir('error 4');
			}
		);
	},

	quickRateDetail : function() {
		var currentTd = jQuery('#interstateRateQuick').closest('td');
		currentTd.progressIndicator();
		jQuery('#interstateRateQuick').addClass('hide');

		var dataURL = 'index.php?module=Estimates&action=QuickEstimate&record='+getQueryVariable('record');
		AppConnector.request(dataURL).then(
			function(data) {
				if(data.success) {
					var mileageTD = jQuery('#Estimates_detailView_fieldValue_interstate_mileage');
					mileageTD.progressIndicator();
					mileageTD.find('span').addClass('hide');
					mileageTD.find('span').html(data.result.mileage);
					mileageTD.find('span').removeClass('hide');
					mileageTD.progressIndicator({'mode':'hide'});

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
					//jQuery('td:contains("Grand Total")').siblings().find('span').html(parseFloat(data.result.rateEstimate).toFixed(2));
					currentTd.progressIndicator({'mode':'hide'});
					jQuery('#interstateRateQuick').removeClass('hide');
				}
				else {
					bootbox.alert(data.error.code + ': ' + data.error.message);
					currentTd.progressIndicator({'mode':'hide'});
					jQuery('#interstateRateQuick').removeClass('hide');
				}
			},
			function(error, err) {
				bootbox.alert(error + ': ' + err);
				currentTd.progressIndicator({'mode':'hide'});
			}
		);
	},

	quickRateEdit : function() {
		var thisInstance = this;
		var lineItemTable = this.getLineItemContentsContainer();

		var data = thisInstance.getQuickRateEditQuery();
		if(!data.success) {
			bootbox.alert(data.errorString); return;
		}

		var currentTd = jQuery('#interstateRateQuick').closest('td');
		currentTd.progressIndicator();
		jQuery('#interstateRateQuick').addClass('hide');
		jQuery('.interstateRateDetail').addClass('hide');
		jQuery('th:contains("Item Details")').closest('table').find('tbody').addClass('hide');
		jQuery('td:contains("Grand Total")').closest('table').addClass('hide');
		jQuery('th:contains("Item Details")').closest('table').progressIndicator();

		var dataURL = "index.php?module=Estimates&action=GetRateEstimate&record="+getQueryVariable("record")+data.queryString;

		AppConnector.request(dataURL).then(
			function(data) {
				if(data.success) {
					jQuery('#Estimates_editView_fieldName_interstate_mileage').val(parseInt(data.result.mileage));
					//Remove first product line item if blank
					var firstItem = jQuery('input[name="productName1"]');
					if(firstItem.val() == '') {
						firstItem.closest('tr.'+ thisInstance.rowClass).remove();
						thisInstance.checkLineItemRow();
						thisInstance.lineItemDeleteActions();
					}
					//Update Rate Estimate field
					//jQuery('input[name="rate_estimate"]').val(parseFloat(data.result.rateEstimate).toFixed(2));

					//Adjust existing line items or add new line items if they do not exist
					for(var key in data.result.lineitems) {
						//var lineItem = jQuery('input[value="'+key+'"]');
						var lineItem = jQuery('.SER' + data.result.lineitemids[key]);
						if(lineItem.length) {
							//Line item exists
							lineItem.closest('tr').find('input[name*="listPrice"]').val(parseFloat(data.result.lineitems[key]).toFixed(2));
							thisInstance.quantityChangeActions(lineItem.closest('tr'));
						}
						else if(jQuery('input[name*="productName"]').length == 13) {
							//Twelve line items exist in addition to the clone copy, but vals are not set, indicating new Estimate
							if(key == 'Transportation') {
								lineItem = jQuery('input[name="productName1"]');
							} else if(key == 'Fuel Surcharge') {
								lineItem = jQuery('input[name="productName2"]');
							} else if(key == 'Packing') {
								lineItem = jQuery('input[name="productName3"]');
							} else if(key == 'Unpacking') {
								lineItem = jQuery('input[name="productName4"]');
							} else if(key == 'Valuation') {
								lineItem = jQuery('input[name="productName5"]');
							} else if(key == 'Origin Accessorials') {
								lineItem = jQuery('input[name="productName6"]');
							} else if(key == 'Origin SIT') {
								lineItem = jQuery('input[name="productName7"]');
							} else if(key == 'Destination Accessorials') {
								lineItem = jQuery('input[name="productName8"]');
							} else if(key == 'Destination SIT') {
								lineItem = jQuery('input[name="productName9"]');
							} else if(key == 'Bulky Items') {
								lineItem = jQuery('input[name="productName10"]');
							} else if(key == 'Miscellaneous Services') {
								lineItem = jQuery('input[name="productName11"]');
							} else if(key == 'IRR') {
								lineItem = jQuery('input[name="productName12"]');
							}
							lineItem.closest('tr').find('input[name*="listPrice"]').val(parseFloat(data.result.lineitems[key]).toFixed(2));
							thisInstance.quantityChangeActions(lineItem.closest('tr'));
						}
						else {
							//Create new line item
							var newRow = thisInstance.getBasicRow().addClass(thisInstance.rowClass);
							jQuery('.lineItemPopup[data-module-name="Products"]',newRow).remove();
							var sequenceNumber = thisInstance.getNextLineItemRowNumber();
							newRow = newRow.appendTo(lineItemTable);
							thisInstance.checkLineItemRow();
							newRow.find('input.rowNumber').val(sequenceNumber);
							thisInstance.updateLineItemsElementWithSequenceNumber(newRow,sequenceNumber);
							newRow.find('input.productName').addClass('autoComplete');
							thisInstance.registerLineItemAutoComplete(newRow);

							//Populate line item
							newRow.find('#productName'+sequenceNumber).val(key);
							newRow.find('input[name*="listPrice"]').val(parseFloat(data.result.lineitems[key]).toFixed(2));
							newRow.find('input[name*="hdnProductId"]').val(data.result.lineitemids[key]);
							thisInstance.quantityChangeActions(newRow);
						}
					}

					//Remove loading icon and show updated field
					currentTd.progressIndicator({'mode':'hide'});
					jQuery('#interstateRateQuick').removeClass('hide');
					jQuery('.interstateRateDetail').removeClass('hide');
					jQuery('th:contains("Item Details")').closest('table').find('tbody').removeClass('hide');
					jQuery('th:contains("Item Details")').closest('table').progressIndicator({'mode':'hide'});
					jQuery('td:contains("Grand Total")').closest('table').removeClass('hide');
					Estimates_Edit_Js.I().lineItemsJs.hideZeroValServices();
				}
				else {
					bootbox.alert(data.error.code + ": " + data.error.message);
					currentTd.progressIndicator({'mode':'hide'});
					jQuery('#interstateRateQuick').removeClass('hide');
					jQuery('.interstateRateDetail').removeClass('hide');
					jQuery('th:contains("Item Details")').closest('table').find('tbody').removeClass('hide');
					jQuery('th:contains("Item Details")').closest('table').progressIndicator({'mode':'hide'});
					jQuery('td:contains("Grand Total")').closest('table').removeClass('hide');
				}
			},
			function(error, err) {
				bootbox.alert(error + ": " + err);
				currentTd.progressIndicator({'mode':'hide'});
				jQuery('#interstateRateQuick').removeClass('hide');
				jQuery('.interstateRateDetail').removeClass('hide');
				jQuery('th:contains("Item Details")').closest('table').find('tbody').removeClass('hide');
				jQuery('th:contains("Item Details")').closest('table').progressIndicator({'mode':'hide'});
				jQuery('td:contains("Grand Total")').closest('table').removeClass('hide');
			}
		);
	},

	getQuickRateEditQuery: function() {
		var fieldNames = ['weight', 'pickup_date', 'pickup_time', 'origin_zip', 'destination_zip', 'fuel_price', 'full_pack', 'full_unpack', 'bottom_line_discount', 'valuation_deductible', 'valuation_amount'];

		var weight = jQuery('input[name="'+fieldNames[0]+'"]').val();

		var pickupDate = jQuery('input[name="'+fieldNames[1]+'"]').val();
		var dateFormat = jQuery('input[name="'+fieldNames[1]+'"]').attr('data-date-format');
		if(dateFormat == "mm-dd-yyyy") {
			pickupDate = pickupDate.substring(6) + "-" + pickupDate.substring(0,5);
		}
		else if(dateFormat == "dd-mm-yyyy") {
			pickupDate = pickupDate.substring(6) + "-" + pickupDate.substring(3,5) + "-" + pickupDate.substring(0,2);
		}

		var pickupTime = "12:00:00";//jQuery('input[name="'+fieldNames[2]+'"]').val();
		var hour = parseInt(pickupTime.substring(0,2));
		var minute = pickupTime.substring(2,5);
		//if(pickupTime.substring(6) == 'PM' && hour < 12) {hour+=12;}
		//else if(pickupTime.substring(6) == 'AM' && hour == 12) {hour=0;}
		//pickupTime = ("00" + hour).slice(-2) + minute + ":00";

		var pickupDateTime = pickupDate + "T" + pickupTime;

		var originZip = jQuery('input[name="'+fieldNames[3]+'"]').val();

		var destinationZip = jQuery('input[name="'+fieldNames[4]+'"]').val();

		var fuelPrice = 0;

		var fullPackApplied = jQuery('input[name="'+fieldNames[6]+'"]').is(':checked');

		var fullUnpackApplied = jQuery('input[name="'+fieldNames[7]+'"]').is(':checked');

		var bottomLineDiscount = jQuery('input[name="'+fieldNames[8]+'"]').val();

		var valDeductible = jQuery('select[name="'+fieldNames[9]+'"]').siblings('.chzn-container').children('a').children('span').html();

		var valuationAmount = jQuery('input[name="'+fieldNames[10]+'"]').val();

		var selectElement = jQuery('select[name="effective_tariff"]');
		var selectId = selectElement.attr('id');
		var chosenOption = selectElement.siblings('.chzn-container').find('.result-selected').attr('id');
		var effective_tariff = selectElement.find('option:eq('+chosenOption.split('_')[3]+')').val();

		//tack fuel surcharge info onto request (commented out because quick rating for SIRVA might be a thing in the future)
		// var consumptionFuel = jQuery("input:checkbox[name='consumption_fuel']").val();
		// var cfChecked = jQuery("input:checkbox[name='consumption_fuel']").prop('checked');
		// var consumptionFuelCharge = jQuery("input[name='accesorial_fuel_surcharge']").val();

		//Validation
		var errorExists = false;
		var errorNum = 1;
		var errorString = 'The following errors have prevented creation of the rate estimate:\n';

		//if(cfChecked && consumptionFuelCharge <= 0 && consumptionFuelCharge >= 1){errorString += errorNum + ") Fuel Surcharge must be above 0 and below 1.\n"; errorExists = true; errorNum++;}
		//else if(!cfChecked && parseFloat(consumptionFuelCharge) <= 0 && parseFloat(consumptionFuelCharge) >= 100){errorString += errorNum + ") Fuel Surcharge must be above 0 and below 100.\n"; errorExists = true; errorNum++;}

		if(weight <= 0 || weight.length == 0) {errorString += errorNum + ") Weight must be greater than 0.\n"; errorExists = true; errorNum++;}
		if(pickupDate.length != 10) {errorString += errorNum + ") A valid pickup date must be set\n"; errorExists = true; errorNum++;}
		if(isNaN(hour)) {errorString += errorNum + ") Pickup time must be set.\n"; errorExists = true; errorNum++;}
		if(originZip.length < 5) {errorString += errorNum + ") Origin Zip must be valid.\n"; errorExists = true; errorNum++;}
		if(destinationZip.length < 5) {errorString += errorNum + ") Destination Zip must be valid.\n"; errorExists = true; errorNum++;}
		if(fuelPrice.length == 0 || fuelPrice < 0) {errorString += errorNum + ") Fuel Price must be set.\n"; errorExists = true; errorNum++;}
		if(bottomLineDiscount.length == 0 || bottomLineDiscount < 0) {errorString += errorNum + ") Bottom Line Discount must be set and non-negative.\n"; errorExists = true; errorNum++;}
		if(valDeductible === 'Select an Option') {errorString += errorNum + ") Valuation Deductible must be selected.\n"; errorExists = true; errorNum++;}
		if(valuationAmount.length == 0 || valuationAmount < 0) {errorString += errorNum + ") Valuation Amount must be set.\n"; errorExists = true; errorNum++;}
		if(effective_tariff.length == 0) {errorString += errorNum + ") Effective Tariff must be set.\n"; errorExists = true; errorNum++;}

		var valDeductibleValue;
		if(valDeductible === '60¢ / lb.') {valDeductibleValue = "SIXTY_CENTS";}
		else if(valDeductible === 'Zero') {valDeductibleValue = "ZERO";}
		else if(valDeductible === '$250') {valDeductibleValue = "TWO_FIFTY";}
		else {valDeductibleValue = "FIVE_HUNDRED";}

		var queryString = "&weight="+weight+"&pickupDateTime="+pickupDateTime+"&originZip="+originZip+"&destinationZip="+destinationZip+"&fuelPrice="+fuelPrice+"&fullPackApplied="+fullPackApplied+"&fullUnpackApplied="+fullUnpackApplied+"&bottomLineDiscount="+bottomLineDiscount+"&valDeductible="+valDeductibleValue+"&valuationAmount="+valuationAmount+"&effective_tariff="+effective_tariff+'&consumption_fuel='+consumptionFuel+'&consumption_fuel_charge='+consumptionFuelCharge;

		if(errorExists) {
			return {success: false, errorString: errorString};
		}

		return {success: true, queryString: queryString};
	},

	preDetailedRateEdit: function () {
		var thisInstance = this;

		var fieldNames = ['weight', 'load_to_date', 'pickup_time', 'origin_zip', 'destination_zip', 'fuel_price', 'full_pack', 'full_unpack', 'bottom_line_discount', 'valuation_deductible', 'valuation_amount', 'business_line_est', 'pricing_color', 'interstate_effective_date', 'load_date', 'percent_smf', 'flat_smf', 'desired_total', 'smf_type'];
		var weight = jQuery('input[name="' + fieldNames[0] + '"]').val();
		var loadToDate = jQuery('input[name="' + fieldNames[1] + '"]').val();
		var originZip = jQuery('input[name="' + fieldNames[3] + '"]').val();
		var destZip = jQuery('input[name="' + fieldNames[4] + '"]').val();
		var blDiscount = jQuery('input[name="' + fieldNames[8] + '"]').val();
		var valDeductible = jQuery('select[name="' + fieldNames[9] + '"]').siblings('.chzn-container').children('a').children('span').html();
		var valAmount = jQuery('[name="' + fieldNames[10] + '"]').val();
		var effectiveDateUserFormat = jQuery('input[name="' + fieldNames[13] + '"]').val();
		var fullPackOverride = jQuery('input:checkbox[name="apply_full_pack_rate_override"]').prop('checked');
		var express_truckload = jQuery('[name="express_truckload"]').is(':checked');
		var shipper_type = jQuery('[name="shipper_type"]').val();

		//Check corresponding toggles for SIT First Day SIT Add'l Day and SIT Cartage
		var selectElement = jQuery('select[name="effective_tariff"]');
		var selectId = selectElement.attr('id');
		var chosenOption = selectElement.siblings('.chzn-container').find('.result-selected').attr('id');

		var effective_tariff = selectElement.find('option:eq(' + chosenOption.split('_')[3] + ')').val();
		var effective_tariff_name = jQuery('select[name="effective_tariff"]').find(':selected').text();
        var effective_tariff_type = $('#effective_tariff_custom_type').val();
		var errorExists = false;
		var errorNum = 1;
		var errorString = 'The following errors have prevented creation of the rate estimate:<br>';
		if (weight <= 0 || weight.length == 0) {
			errorString += errorNum + ") Weight must be greater than 0.<br>";
			errorExists = true;
			errorNum++;
		}
		if (originZip.length < 5) {
			errorString += errorNum + ") Origin Zip must be valid.<br>";
			errorExists = true;
			errorNum++;
		}
		if (destZip.length < 5) {
			errorString += errorNum + ") Destination Zip must be valid.<br>";
			errorExists = true;
			errorNum++;
		}
		if (blDiscount.length == 0 || blDiscount < 0) {
			errorString += errorNum + ") Bottom Line Discount must be set and non-negative.<br>";
			errorExists = true;
			errorNum++;
		}
		if (valDeductible === 'Select an Option') {
			errorString += errorNum + ") Valuation Deductible must be selected.<br>";
			errorExists = true;
			errorNum++;
		}
		if (valAmount.length == 0 || valAmount < 0) {
			//errorString += errorNum + ") Valuation Amount must be set.<br>";
			//errorExists = true;
			//errorNum++;
			valAmount = 0;
		}
		if (effectiveDateUserFormat.length == 0) {
			errorString += errorNum + ") Effective Date must be set.<br>";
			errorExists = true;
			errorNum++;
		}
		if (effective_tariff.length == 0) {
			errorString += errorNum + ") Effective Tariff must be set.<br>";
			errorExists = true;
			errorNum++;
		}
		if ((shipper_type != 'NAT' && effective_tariff_type != 'Intra - 400N') && loadToDate.length == 0) {
			//Check for pricing level/color
			var pricingLevel = jQuery('select[name="pricing_level"]').find('option:selected').val();
			if (pricingLevel.length == 0) {
				errorString += errorNum + ") Load To Date or Pricing Level must be set.<br>";
				errorExists = true;
				errorNum++;
			}
		}
		if(effective_tariff_name == 'Blue Express' && !express_truckload && weight > 6000) {
			errorString += errorNum + ") Express Loading only applies to shipments less than 6000 lbs.<br>";
			errorExists = true;
			errorNum++;
		}
		if (errorExists) {
			bootbox.alert(errorString, function () {
	    	thisInstance.hideRatingInfoAndButtons(false);
			});
			return false;
		}
		return true;
	},

	toggleDisableHiddenFields: function () {
		//listPrice tr isn't hidden, BUT qty is so we not qty
		//jQuery('.hide').children('input, select, textarea').each(function(){
		jQuery('.hide').children('input:not([name*="qty"]), select, textarea').each(function () {
			if (jQuery(this).prop('disabled')) {
				jQuery(this).prop('disabled', false);
			} else {
				jQuery(this).prop('disabled', true);
			}
		});
	},

	detailedRateEdit: function(requote) {
		var thisInstance = this;
        thisInstance.hideRatingInfoAndButtons(true);
		thisInstance.detailedRate(thisInstance, requote);
	},

	detailedRate: function(thisInstance, requote) {
		if (!thisInstance.detailView) {
			if (!thisInstance.preDetailedRateEdit()) {
				return;
			}
		}
		var lineItemTable = this.getLineItemContentsContainer();
		thisInstance.saveProductCount();
		thisInstance.toggleDisableHiddenFields();
		var turnItBackOff = false;
		jQuery('select[name="demand_color"], select[name="pricing_level"]').each(function () {
			if (jQuery(this).prop('disabled')) {
				jQuery(this).prop('disabled', false).trigger('liszt:updated');
				turnItBackOff = true;
			}
		});
		if (jQuery('input[name="pack_rates"]').length > 0) {
			jQuery('input[name="pack_rates"]').prop('disabled', true);
		}

		//Serialize Service Charges into a single hidden input
        Service_Charges_Js.compile();

		var formData = jQuery.param(jQuery('#EditView').serializeFormData());
		thisInstance.toggleDisableHiddenFields();
		jQuery('select[name="demand_color"], select[name="pricing_level"]').each(function () {
			if (turnItBackOff) {
				jQuery(this).prop('disabled', true).trigger('liszt:updated');
			}
		});
		var index = formData.indexOf('&record=');
		var urlAppend = formData.substring(index, formData.length - 1);

		// For some reason smf_type is not getting serialized.
		// I'm not sure where and this resolves the issue.
		var SMFType = jQuery('input:checkbox[name="smf_type"]').prop('checked');
		if (SMFType == true) {
			urlAppend = urlAppend + '&smf_type=1';
		} else {
			//shouldn't need this but want to be sure.
			urlAppend = urlAppend + '&smf_type=0';
		}

		var dataURL = 'index.php?module=Estimates&action=GetDetailedRate&type=editview&pseudoSave=1' + urlAppend;

		if (requote) {
			dataURL += '&requote=1';
		}
		//console.dir(dataURL);
		AppConnector.request(dataURL).then(function (data) {
				if (jQuery('input[name="pack_rates"]').length > 0) {
					jQuery('input[name="pack_rates"]').prop('disabled', false);
				}
				//console.dir(data);
				if (data.success) {//console.log(data);
					if (data.result.validtill != '') {
						var dateToSet = new Date(data.result.validtill.year, data.result.validtill.month - 1, data.result.validtill.day);
						jQuery('input[name="validtill"]').DatePickerSetDate(dateToSet, true);
					} else {
						var currentEffectiveDate = jQuery('input[name="interstate_effective_date"]').DatePickerGetDate(true);
						var effDateObj = jQuery('input[name="interstate_effective_date"]').DatePickerGetDate();
						effDateObj.setMonth(effDateObj.getMonth() + 1);
						jQuery('input[name="validtill"]').DatePickerSetDate(effDateObj, true);
					}
					jQuery('input[name="interstate_effective_date"]').val(jQuery('input[name="interstate_effective_date"]').DatePickerGetDate(true));
					jQuery('input[name="validtill"]').val(jQuery('input[name="validtill"]').DatePickerGetDate(true));

					if(data.result.lineitemsView) {
						jQuery('#contentHolder_DETAILED_LINE_ITEMS').replaceWith(data.result.lineitemsView);
						jQuery('#contentHolder_DETAILED_LINE_ITEMS').removeClass('hide')
					}
					//jQuery('input[name="requote"]').prop('checked', false);

					//@NOTE: Remove All line items to add in the new ones,
					//This will remove all subitem* rows and hide lineitem rows and set all listprices to 0.
					// thisInstance.clearItemList();
					//
					// var final_result_info = 0.00;
					// if(!data.result.lineitems.Containers) {
					//     data.result.lineitems.Containers = 0;
					// }
					// if(data.result.lineitems.Packing || data.result.lineitems.Containers) {
					//     final_result_info = Math.round(
					//         (parseFloat(data.result.lineitems.Packing) + parseFloat(data.result.lineitems.Containers)) * 100) / 100;
					// }
					// if ($('.value_LBL_GRR_ESTIMATE_VAL')) {
					//     $('.value_LBL_GRR_ESTIMATE_VAL').closest('.value').html(final_result_info);
					// }
					// if ($('#Estimates_editView_fieldName_grr_estimate')) {
					//     $('#Estimates_editView_fieldName_grr_estimate').val(final_result_info);
					// }

					if(data.result.grr.cp) {
						$('[name="grr_estimate"]').val(Math.floor(parseFloat(data.result.grr.cp) * 100)/100).trigger('change');
					} else {
						$('[name="grr_estimate"]').val(0).trigger('change');
					}
					jQuery('#Estimates_editView_fieldName_interstate_mileage').val(parseInt(data.result.mileage));

					//thisInstance.updateLineItems(data);

					jQuery('input[name="total"]').val(data.result.rateEstimate);
					jQuery('input[name="grandTotal"]').val(data.result.rateEstimate);

					var selectNode = jQuery('select[name="demand_color"]');
					selectNode.find('option:selected').each(function () {
						jQuery(this).prop('selected', false);
					});

					if (data.result.pricingColor == 'No_color') {
						data.result.pricingColor = '';
					}
					selectNode.find('option[value="' + data.result.pricingColor + '"]').prop('selected', true);

					var selectNode2 = jQuery('select[name="pricing_level"]');
					selectNode2.find('option:selected').each(function () {
						jQuery(this).prop('selected', false);
					});

					if (data.result.pricingLevel == 'NO_LEVEL') {
						data.result.pricingLevel = '';
					}

					if (typeof data.result.pack_rates !== 'undefined') {
						if (jQuery('input[name="pack_rates"]').length == 0) {
							jQuery('#EditView').append('<input type="hidden" name="pack_rates" />');
						}
						jQuery('input[name="pack_rates"]').val(data.result.pack_rates);
					}

					selectNode2.find('option[value="' + data.result.pricingLevel + '"]').prop('selected', true);
					jQuery('select[name="demand_color"]').trigger('liszt:updated').trigger('change');
					jQuery('select[name="pricing_level"]').trigger('liszt:updated').trigger('change');

					//I don't see why we are disabling the color what if they want to rerate?
					//I mean I see sort of why because what if they change it after rating,
					//right but like that's totally on the user, they could change anything after a rate and save it.

					//jQuery('select[name="demand_color"]').prop('disabled', true).trigger('liszt:updated');
					//jQuery('select[name="pricing_level"]').prop('disabled', true).trigger('liszt:updated');
					//jQuery('input:checkbox[name="pricing_color_lock"]').prop('checked', true);

					if (data.result.stsVehicles != '') {
						if (jQuery('input[name="sts_vehicles"]').length > 0) {
							jQuery('input[name="sts_vehicles"]').val(data.result.stsVehicles);
						} else {
							jQuery('<input>').attr({
								type: 'hidden',
								value: data.result.stsVehicles,
								name: 'sts_vehicles'
							}).appendTo('form#EditView');
						}
					}

					if (data.result.accesorial_fuel_surcharge != '') {
						jQuery('#Estimates_editView_fieldName_accesorial_fuel_surcharge').val(data.result.accesorial_fuel_surcharge);
					}

					if (data.result.tpgTransFactor != '') {
						if (jQuery('input[name="tpg_transfactor"]').length > 0) {
							jQuery('input[name="tpg_transfactor"]').val(data.result.tpg_transfactor);
						} else {
							jQuery('<input>').attr({
								type: 'hidden',
								value: data.result.tpg_transfactor,
								name: 'tpg_transfactor'
							}).appendTo('form#EditView');
						}
					}
					var grr_cwt = parseFloat(data.result.TPGGRRRate);
					jQuery('#Estimates_editView_fieldName_grr').val(grr_cwt.toFixed(2));
					jQuery('#Estimates_editView_fieldName_grr_cp').val(data.result.grr.cp);

					thisInstance.hideRatingInfoAndButtons(false);
					// Estimates_Edit_Js.I().lineItemsJs.hideZeroValServices();
					// //console.dir(data.result);
					var transTotal = data.result.trans_total;

					// //console.dir(transTotal);
					var desiredTotal = jQuery('input[name="desired_total"]').val();
					if (desiredTotal == '' || desiredTotal == 0) {
						var SMFType = jQuery('input:checkbox[name="smf_type"]').prop('checked');
						var fieldNames = ['weight', 'pickup_date', 'pickup_time', 'origin_zip', 'destination_zip', 'fuel_price', 'full_pack', 'full_unpack', 'bottom_line_discount', 'valuation_deductible', 'valuation_amount', 'business_line_est', 'pricing_color', 'interstate_effective_date', 'load_date', 'percent_smf', 'flat_smf', 'desired_total', 'smf_type'];
						if (SMFType == true) {
							//console.dir('flatSMF');
							//it would occasionally not have flatSMF here and throw an error, fixed by sourcing it from the field in emergencies
							if (!flatSMF) {
								var flatSMF = jQuery('input[name="flat_smf"]').val();
							}
							//it's a flat SMF, take total minus flat SMF to get standard charge use this to calc percent SMF
							var total = transTotal - flatSMF;
							//console.dir(total);
							var newPercentSMF = (flatSMF / total) * 100;
							jQuery('input[name="' + fieldNames[15] + '"]').val(parseFloat(newPercentSMF).toFixed(2));
							jQuery('input[name="' + fieldNames[16] + '"]').val(jQuery('input[name="' + fieldNames[16] + '"]').val());
						} else {
							//console.dir('percentSMF');
							//it's a percent SMF
							var newFlatSMF = parseFloat(data.result.TPGMgmtFee).toFixed(2);
							jQuery('input[name="' + fieldNames[16] + '"]').val(newFlatSMF);
							jQuery('input[name="' + fieldNames[15] + '"]').val(jQuery('input[name="' + fieldNames[15] + '"]').val());
						}
					}
					else {
						var fieldNames = ['weight',
							'pickup_date',
							'pickup_time', 'origin_zip', 'destination_zip', 'fuel_price', 'full_pack',
							'full_unpack', 'bottom_line_discount', 'valuation_deductible', 'valuation_amount',
							'business_line_est', 'pricing_color', 'interstate_effective_date', 'load_date',
							'percent_smf',
							'flat_smf', 'desired_total', 'smf_type'];
						//console.dir('desired total');
						//var newFlatSMF = data.result.TPGMgmtFee;
						//var total = transTotal - newFlatSMF;
						//console.dir(total);
						var newFlatSMF = parseFloat(data.result.TPGMgmtFee).toFixed(2);

						var total = transTotal - newFlatSMF;
						//console.dir(total);
						var newPercentSMF = (newFlatSMF / total) * 100;

						//console.dir('new percent : '+newPercentSMF);
						//console.dir('new flat : '+newFlatSMF);
						jQuery('input[name="' + fieldNames[15] + '"]').val(newPercentSMF.toFixed(2));
						jQuery('input[name="' + fieldNames[16] + '"]').val(newFlatSMF);
					}
					//var type = jQuery('select[name="effective_tariff"]').next().find('li.result-selected').html().toLowerCase();
					//if (
					//    type.indexOf('tpg') >= 0 ||
					//    type.indexOf('uas') >= 0 ||
					//    type.indexOf('pricelock') >= 0 ||
					//    type.indexOf('express') >= 0
					//) {
					//    //NO.
					//    //jQuery('input[name="sit_origin_fuel_percent"]').prop('readonly', true).val('4');
					//    //jQuery('input[name="sit_dest_fuel_percent"]').prop('readonly', true).val('4');
					//}
				} else {
					bootbox.alert(data.error.code + ": " + data.error.message);
					thisInstance.hideRatingInfoAndButtons(false);
				}
                jQuery('input[name="hasUnratedChanges"]').val('0');
			}
		);
	},

	saveFieldValues : function (fieldDetailList) {
		var aDeferred = jQuery.Deferred();

		var recordId = jQuery('#recordId').val();

		var data = {};
		if(typeof fieldDetailList != 'undefined'){
			data = fieldDetailList;
		}

		data['record'] = recordId;

		data['module'] = app.getModuleName();
		data['action'] = 'SaveAjax';

		AppConnector.request(data).then(
			function(reponseData){
				aDeferred.resolve(reponseData);
			}
		);

		return aDeferred.promise();
	},
	fieldUpdatedEvent : 'Vtiger.Field.Updated',
	detailedRateDetail : function() {
		//console.dir('Function to generate XML and retrieve rate');
		var thisInstance = this;
		//var dataURL = 'index.php?module=Estimates&action=GetTPGPricelockRateEstimate&record='+getQueryVariable('record');
		//changed to GetDetailedRate because this is base not tpg.  I
		var dataURL = 'index.php?module=Estimates&action=GetDetailedRate&record='+getQueryVariable('record');
		// jQuery('th:contains("Item Details")').closest('table').find('tbody').addClass('hide');
		// jQuery('td:contains("Grand Total")').closest('table').addClass('hide');
		// jQuery('th:contains("Item Details")').closest('table').progressIndicator();
		jQuery('#interstateRateQuick').addClass('hide');
		jQuery('.interstateRateDetail').addClass('hide');
		jQuery('.interstateRateDetail').closest('td').progressIndicator();
		AppConnector.request(dataURL).then(
			function(data) {
				if(data.success) {
					jQuery('#contentHolder_DETAILED_LINE_ITEMS').replaceWith(data.result.lineitemsView);
					jQuery('#contentHolder_DETAILED_LINE_ITEMS').removeClass('hide')
					app.currentPageController.lineItemsJs.saveDetailLineItems();
				}
				else {
					alert(data.error.code + ': ' + data.error.message);
					// jQuery('th:contains("Item Details")').closest('table').find('tbody').removeClass('hide');
					// jQuery('th:contains("Item Details")').closest('table').progressIndicator({'mode':'hide'});
					// jQuery('td:contains("Grand Total")').closest('table').removeClass('hide');
				}
                jQuery('#interstateRateQuick').removeClass('hide');
                jQuery('.interstateRateDetail').closest('td').progressIndicator({'mode':'hide'});
                jQuery('.interstateRateDetail').removeClass('hide');
			},
			function(error) {
			}
		);
	},
	reportButtonEdit : function() {
		//console.dir('Base Edit reports button');
		var thisInstance = this;
		var assigned_user_id = jQuery('select[name="assigned_user_id"]').find('option:selected').val();
		var dataURL = 'index.php?module=Estimates&action=GetReportTPGPricelock&record='+getQueryVariable('record')+'&requestType=GetAvailableReports&type=editview&assigned_user_id='+assigned_user_id+'&local='+(jQuery("[name='business_line_est']").val() == 'Local Move')+'&effectiveTariff='+jQuery("[name='effective_tariff']").val()+'&effective_date='+jQuery("#Estimates_detailView_fieldValue_effective_date").find('input').val();
		if(jQuery("[name='business_line_est']").val() == 'Local Move' && $('#effective_tariff_custom_type').val() == ''){
			var dataURL = 'index.php?module=Estimates&action=GetReportLocal&record='+getQueryVariable('record')+'&requestType=GetAvailableReports&type=editview&assigned_user_id='+assigned_user_id+'&local='+(jQuery("[name='business_line_est']").val() == 'Local Move')+'&effectivetariff='+jQuery("[name='local_tariff']").val()+'&effective_date='+jQuery("#Estimates_detailView_fieldValue_effective_date").find('input').val()+'&effectivedateid='+jQuery("[name='EffectiveDateId']").val();
		}

		AppConnector.request(dataURL).then(
			function(data) {
				if(data.success) {
					if(jQuery('#reportContent').length == 0)
					{
						jQuery('.contentsDiv').append('<div id="reportContent"></div>');
					}
					console.dir(data.result);
					jQuery('#reportContent').html(data.result);
					jQuery.colorbox(
					{
						inline:true, width:'300px', height:'40%', left:'25%', top:'25%',
						href:'#reportContent',
						onClosed:function(){
							jQuery(document.body).css({overflow:'auto'});
						},
						onComplete:function(){
							jQuery(document.body).css({overflow:'hidden'});
						}
					});
					jQuery('#reportContent').find('button').each(function() {
						//console.dir(jQuery(this));
						jQuery(this).on('click', function() {
							jQuery('#reportContent').find('.contents').addClass('hide');
							jQuery('#reportContent').progressIndicator();
							var reportURL = 'index.php?module=Estimates&action=GetReportTPGPricelock&record='+getQueryVariable('record')+'&reportId='+jQuery(this).attr('name')+'&reportName='+encodeURIComponent(jQuery(this).html())+'&type=editview&assigned_user_id='+assigned_user_id;
							if(jQuery("[name='business_line_est']").val() == 'Local Move'){
								var reportURL = 'index.php?module=Estimates&action=GetReportLocal&record='+getQueryVariable('record')+'&requestType=GetReport&type=editview&assigned_user_id='+assigned_user_id+'&local='+(jQuery("[name='business_line_est']").val() == 'Local Move')+'&effectivetariff='+jQuery("[name='local_tariff']").val()+'&effective_date='+jQuery("#Estimates_detailView_fieldValue_effective_date").find('input').val()+'&effectivedateid='+jQuery("[name='EffectiveDateId']").val()+'&reportId='+jQuery(this).attr('name')+'&reportName='+encodeURIComponent(jQuery(this).html());
							}
							var fieldNames = ['weight', 'pickup_date', 'pickup_time', 'origin_zip', 'destination_zip', 'fuel_price', 'full_pack', 'full_unpack', 'bottom_line_discount', 'valuation_deductible', 'valuation_amount', 'business_line'];
							reportURL = reportURL+'&'+fieldNames[0]+'='+jQuery('input[name="'+fieldNames[0]+'"]').val();

							reportURL = reportURL+'&'+fieldNames[3]+'='+jQuery('input[name="'+fieldNames[3]+'"]').val();

							reportURL = reportURL+'&'+fieldNames[4]+'='+jQuery('input[name="'+fieldNames[4]+'"]').val();

							reportURL = reportURL+'&'+fieldNames[6]+'='+(jQuery('input[name="'+fieldNames[6]+'"]').is(':checked') ? '1':'0');

							reportURL = reportURL+'&'+fieldNames[7]+'='+(jQuery('input[name="'+fieldNames[7]+'"]').is(':checked') ? '1':'0');

							reportURL = reportURL+'&'+fieldNames[8]+'='+jQuery('input[name="'+fieldNames[8]+'"]').val();

							reportURL = reportURL+'&'+fieldNames[9]+'='+jQuery('select[name="'+fieldNames[9]+'"]').siblings('.chzn-container').children('a').children('span').html();

							reportURL = reportURL+'&'+fieldNames[10]+'='+jQuery('input[name="'+fieldNames[10]+'"]').val();

							reportURL = reportURL+'&'+fieldNames[11]+'='+jQuery('select[name="'+fieldNames[11]+'"]').siblings().first().find('span').html();


							jQuery('#inline_content').find('input').each(function() {
								if(jQuery(this).attr('type') == 'hidden' || jQuery(this).closest('tr').hasClass('hide')) {return;}
								if(jQuery(this).attr('type') == 'checkbox') {
									reportURL = reportURL+'&'+jQuery(this).attr('name')+'='+(jQuery(this).is(':checked') ? '1':'0');
								}
								else {
									reportURL = reportURL+'&'+jQuery(this).attr('name')+'='+jQuery(this).val();
								}
							});

							reportURL = reportURL+'&interstate_mileage='+jQuery('input[name="interstate_mileage"]').val();
							reportURL = reportURL+'&effective_tariff='+jQuery('select[name="effective_tariff"]').val();
							reportURL = reportURL + '&wsdlURL='+jQuery('input[name="wsdlURL"]').val();
							reportURL = reportURL+'&validtill='+jQuery('input[name="validtill"]').val();
							var includeDOV = jQuery('[name="includeDOV"]').attr('checked') == 'checked' ? true : false;
							reportURL = reportURL + '&includeDOV=' + includeDOV;
							console.dir(reportURL);
							AppConnector.request(reportURL).then(
								function(data) {
									if(data.success) {
										jQuery('#EditView').append('<input type="hidden" name="gotoDocuments" value="'+data.result+'">');
										jQuery('#EditView').append('<input type="hidden" name="reportSave" value="1">');
										jQuery('#EditView').submit();
										//jQuery.when(function(){
										//	jQuery('#EditView').submit();
										//}).done(function(){
										//	window.location.href = 'index.php?module=Documents&view=Detail&record='+data.result;
										//});
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
	},
	reportButtonDetail : function() {
		var thisInstance = this;
		//console.dir('this is getting called here');
		jQuery('#getReportSelectButton').closest('td').progressIndicator();
		jQuery('#getReportSelectButton').addClass('hide');
		var dataURL = 'index.php?module=Estimates&action=GetReportTPGPricelock&record='+getQueryVariable('record')+'&requestType=GetAvailableReports&local='+(jQuery("[name='business_line_est']").val() == 'Local Move')+'&effectiveTariff='+jQuery("[name='local_tariff']").val()+'&effective_date='+jQuery("#Estimates_detailView_fieldValue_effective_date").find('input').val();
		if(jQuery('#Estimates_detailView_fieldValue_business_line_est').find('span.value').html().trim() == 'Local Move' && $('#effective_tariff_custom_type').val() == ''){
			var dataURL = 'index.php?module=Estimates&action=GetReportLocal&record='+getQueryVariable('record')+'&requestType=GetAvailableReports&local='+(jQuery('#Estimates_detailView_fieldValue_business_line_est').find('span.value').html().trim() == 'Local Move')+'&effectivetariff='+jQuery("[name='local_tariff']").val()+'&effective_date='+jQuery("#Estimates_detailView_fieldValue_effective_date").find('input').val()+'&effectivedateid='+jQuery("[name='EffectiveDateId']").val();
		}
		//console.dir(dataURL);
		AppConnector.request(dataURL).then(
			function(data) {
				if(data.success) {
					if(jQuery('#reportContent').length == 0)
					{
						jQuery('.contentsDiv').append('<div id="reportContent"></div>');
					}
					jQuery('#reportContent').html(data.result);
					if (!jQuery.isFunction(jQuery.fn.colorbox)) {
						jQuery.getScript("libraries/jquery/colorbox/jquery.colorbox-min.js").then(function(){
							jQuery.colorbox({inline:true, width:'300px', height:'40%', left:'25%', top:'25%', href:'#reportContent', onClosed:function(){jQuery(document.body).css({overflow:'auto'});jQuery('#reportContent').html('');}, onComplete:function(){jQuery(document.body).css({overflow:'hidden'});}});
						});
					}
					else{
						jQuery.colorbox({inline:true, width:'300px', height:'40%', left:'25%', top:'25%', href:'#reportContent', onClosed:function(){jQuery(document.body).css({overflow:'auto'});jQuery('#reportContent').html('');}, onComplete:function(){jQuery(document.body).css({overflow:'hidden'});}});
					}

					jQuery('#reportContent').find('button').each(function() {
						jQuery(this).on('click', function() {
							jQuery('#reportContent').find('.contents').addClass('hide');
							jQuery('#reportContent').progressIndicator();
							var reportURL = 'index.php?module=Estimates&action=GetReportTPGPricelock&record='+getQueryVariable('record')+'&reportId='+jQuery(this).attr('name')+'&reportName='+encodeURIComponent(jQuery(this).html());
							reportURL = reportURL + '&wsdlURL='+jQuery('input[name="wsdlURL"]').val();
							reportURL = reportURL + '&effective_tariff='+jQuery('select[name="effective_tariff"]').val();
							var includeDOV = jQuery('[name="includeDOV"]').attr('checked') == 'checked' ? true : false;
							reportURL = reportURL + '&includeDOV=' + includeDOV;
							// console.dir(reportURL);
							if(jQuery('#Estimates_detailView_fieldValue_business_line_est').find('span.value').html().trim() == 'Local Move'){
								var reportURL = 'index.php?module=Estimates&action=GetReportLocal&record='+getQueryVariable('record')+'&requestType=GetReport&local='+(jQuery("[name='business_line_est']").val() == 'Local Move')+'&effectivetariff='+jQuery("[name='local_tariff']").val()+'&effective_date='+jQuery("#Estimates_detailView_fieldValue_effective_date").find('input').val()+'&effectivedateid='+jQuery("[name='EffectiveDateId']").val()+'&reportId='+jQuery(this).attr('name')+'&reportName='+encodeURIComponent(jQuery(this).html());
							}
							AppConnector.request(reportURL).then(
								function(data) {
									if(data.success) {
										window.location.href = 'index.php?module=Documents&view=Detail&record='+data.result;
										//console.log(data.result);
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
	},
	unblockUASFields : function(){
		jQuery("input[name='linehaul_disc'], input[name='packing_disc'], input[name='accessorial_disc']").prop('disabled', false);
	},
	registerFuelSurchargeEvent : function(){
		var thisInstance = this;
		if(
			thisInstance.tariffType != 'ALLV-2A' &&
            thisInstance.tariffType != 'NAVL-12A' &&
            thisInstance.tariffType != '400N Base' &&
            thisInstance.tariffType != '400N/104G' &&
			thisInstance.tariffType != '400NG' &&
            thisInstance.tariffType != 'Intra - 400N'
		){
			jQuery('input:checkbox[name="consumption_fuel"]').addClass('hide');
			jQuery('input:checkbox[name="consumption_fuel"]').prop('checked', false);
			jQuery('input:checkbox[name="consumption_fuel"]').parent('td').prev('td').find('label').addClass('hide');
		}
		if(thisInstance.tariffType != 'Blue Express') {
			jQuery('input:checkbox[name="express_truckload"]').addClass('hide');
			jQuery('input:checkbox[name="express_truckload"]').prop('checked', false);
			jQuery('input:checkbox[name="express_truckload"]').parent('td').prev('td').find('label').addClass('hide');
		}
		jQuery('input[name="accesorial_fuel_surcharge"]').on('change', function(){
			var correctTariff = false;
			if(
				thisInstance.tariffType == "ALLV-2A" ||
                thisInstance.tariffType == "400N Base" ||
                thisInstance.tariffType == "400N/104G" ||
				thisInstance.tariffType == "400NG" ||
                thisInstance.tariffType == "Intra - 400N" ||
                thisInstance.tariffType == "NAVL-12A"
			){
				correctTariff = true;
			}
			//check to see if fuel consumption checkbox field exists & and appropriate tariff is selected
			if(jQuery('input:checkbox[name="consumption_fuel"]') && correctTariff){
				//check to see if fuel consumption checkbox field exists
				if(jQuery('input:checkbox[name="consumption_fuel"]').prop('checked') == true){
					//box checked
					//jQuery('input[name="accesorial_fuel_surcharge"]').attr('step', 0.0001).attr('min', 0.0000).attr('max', 0.9999);
					jQuery('input[name="accesorial_fuel_surcharge"]').attr('step', 0.0001);
					var fuelSurcharge = parseFloat(jQuery('input[name="accesorial_fuel_surcharge"]').val());
					if(fuelSurcharge >= 1){
						fuelSurcharge= fuelSurcharge/10000; //.9999;
					}
					if(fuelSurcharge < 0){
						fuelSurcharge= '';
					}
					fuelSurcharge = fuelSurcharge.toFixed(4);
					jQuery('input[name="accesorial_fuel_surcharge"]').val(fuelSurcharge);
				} else{
					//box not checked
					jQuery('input[name="accesorial_fuel_surcharge"]').attr('step', 1);//.attr('min', 0).attr('max', 100);
					var fuelSurcharge = jQuery('input[name="accesorial_fuel_surcharge"]').val();
					if (fuelSurcharge && fuelSurcharge < 1) {
						fuelSurcharge = fuelSurcharge * 10000;
					}
					jQuery('input[name="accesorial_fuel_surcharge"]').val(fuelSurcharge);
				}
			}
		});
		jQuery('input:checkbox[name="consumption_fuel"]').on('change', function(){
			jQuery('input[name="accesorial_fuel_surcharge"]').trigger('change');
		});
	},
	setCustomTariffType: function () {
		if (jQuery('#EditView').length < 1) {
			//we're in detail view fuck this other non-sense
			this.tariffType = jQuery('#tariffType').val();
		} else {
			jQuery('input:checkbox[name="pricing_color_lock"]').prop('checked', false)
			jQuery('select[name="demand_color"]').prop('disabled', false).trigger('liszt:updated');
			jQuery('select[name="pricing_level"]').prop('disabled', false).trigger('liszt:updated');
			var selectTag = jQuery('select[name="effective_tariff"]');
			selectTag.siblings('.chzn-container').find('.chzn-results');
			var val = selectTag.find('option:selected').val();
			this.tariffType = jQuery('#tariffType_' + val).val();
		}
		//console.dir('Tariff Type: '+this.tariffType);
		this.disableGRRFields();
		this.hideFreeFVP();
		this.unblockUASFields();
		switch (this.tariffType) {
			case 'TPG':
				this.estimateType(['Binding']);
				break;
			case 'Allied Express':
				this.estimateType(['Binding']);
				break;
			case 'TPG GRR':
				this.enableGRRFields();
				this.estimateType(['Not to Exceed']);
				break;
			case 'ALLV-2A':
				this.enableFreeFVP();
				this.estimateType(['Non-Binding']);
				break;
			case 'Pricelock':
				this.estimateType(['Binding']);
				break;
			case 'Blue Express':
				this.estimateType(['Binding']);
				break;
			case 'Pricelock GRR':
				this.enableGRRFields();
				this.estimateType(['Not to Exceed']);
				break;
			case 'NAVL-12A':
				this.enableFreeFVP();
				this.estimateType(['', 'Binding', 'Non-Binding', 'Not to Exceed']);
				break;
			case '400N Base':
				this.enableFreeFVP();
				this.estimateType(['', 'Binding', 'Non-Binding', 'Not to Exceed']);
				break;
			case '400N/104G':
				this.enableFreeFVP();
				this.estimateType(['', 'Binding', 'Non-Binding', 'Not to Exceed']);
				break;
			case '400NG':
				this.enableFreeFVP();
				this.estimateType(['Not to Exceed', 'Non-Binding']);
				break;
			case 'Local/Intra':
				this.estimateType(['', 'Binding', 'Non-Binding', 'Not to Exceed']);
				break;
			case 'Max 3':
				//I don't think we can get here, because Max 3/Max 4 are "Local Move"
                // and customJS is set on effective_tariff not local_tariff or move_type.
				this.estimateType(['', 'Binding', 'Non-Binding', 'Not to Exceed']);
				break;
			case 'Max 4':
				this.estimateType(['', 'Binding', 'Non-Binding', 'Not to Exceed']);
				break;
			case 'Intra - 400N':
				this.enableFreeFVP();
				this.estimateType(['', 'Binding', 'Non-Binding', 'Not to Exceed']);
				break;
			case 'Canada Gov\'t':
				this.estimateType(['', 'Binding', 'Not to Exceed']);
				break;
			case 'Canada Non-Govt':
				this.estimateType(['', 'Binding', 'Not to Exceed']);
				break;
			case 'UAS':
				jQuery("input[name='linehaul_disc'], input[name='packing_disc'], input[name='accessorial_disc']").prop('disabled', true);
				this.estimateType(['Non-Binding']);
				break;
			default:
				this.estimateType(['', 'Binding', 'Non-Binding', 'Not to Exceed']);
				break;
		}
	},
	estimateType: function (options) {
		var field = jQuery('select[name="estimate_type"]');
		var selected = field.val();
		field.empty();

		jQuery.each(options, function (index, value) {
			var option = jQuery('<option></option>').attr("value", value).text(value);
			field.append(option);

			if (value == selected) {
				field.val(value);
			}
		});
		field.trigger('liszt:updated');
	},
	enableGRRFieldsDetail: function () {
		//console.dir('This gets fired now');
		jQuery('td.value_LBL_ESTIMATES_GRR').removeClass('hide');
		jQuery('td.value_LBL_ESTIMATES_GRR').prev('td').removeClass('hide');
		//value_LBL_ESTIMATES_GRROVERIDEAMOUNT
		jQuery('td.value_LBL_ESTIMATES_GRROVERIDEAMOUNT').removeClass('hide');
		jQuery('td.value_LBL_ESTIMATES_GRROVERIDEAMOUNT').prev('td').removeClass('hide');

		jQuery('td.value_LBL_GRR_ESTIMATE_VAL').removeClass('hide');
		jQuery('td.value_LBL_GRR_ESTIMATE_VAL').prev('td').removeClass('hide');

		//value_LBL_ESTIMATES_GRROVERIDE
		jQuery('td.value_LBL_ESTIMATES_GRROVERIDE').removeClass('hide');
		jQuery('td.value_LBL_ESTIMATES_GRROVERIDE').prev('td').removeClass('hide');

	},
	enableGRRFields: function () {
		jQuery('#Estimates_editView_fieldName_grr_override').closest('tr').find('td').removeClass('hide');
		jQuery('#Estimates_editView_fieldName_grr_override_label').closest('td').removeClass('hide');

		jQuery('#Estimates_editView_fieldName_grr_override_amount').closest('td').removeClass('hide');
		jQuery('#Estimates_editView_fieldName_grr_override_amount_label').closest('td').removeClass('hide');

		jQuery('#Estimates_editView_fieldName_grr').closest('td').removeClass('hide');
		jQuery('#Estimates_editView_fieldName_grr_label').closest('td').removeClass('hide');

		jQuery('#Estimates_editView_fieldName_grr').prop("readonly", true);

		jQuery('#Estimates_editView_fieldName_grr_override').change(function () {
			if (this.checked) {
				jQuery('#Estimates_editView_fieldName_grr_override_amount').prop("readonly", false);
			}
			else {
				jQuery('#Estimates_editView_fieldName_grr_override_amount').prop("readonly", true);
			}
		}).trigger('change');
	},
	disableGRRFields: function () {
		jQuery('#Estimates_editView_fieldName_grr_override').closest('tr').find('td').addClass('hide');
		jQuery('#Estimates_editView_fieldName_grr_override_label').closest('td').addClass('hide');

		jQuery('#Estimates_editView_fieldName_grr_override_amount').closest('td').addClass('hide');
		jQuery('#Estimates_editView_fieldName_grr_override_amount_label').closest('td').addClass('hide');

		jQuery('#Estimates_editView_fieldName_grr').closest('td').addClass('hide');
		jQuery('#Estimates_editView_fieldName_grr_label').closest('td').addClass('hide');
	},

	disableContainerColumn: function() {
		jQuery('.contQtyField').prop('readonly',true);
		jQuery('.packQtyField').on('change', function() {
			var name = jQuery(this).attr('name');
			var regExp = /\d+/g;
			var rowNumbers = name.match(regExp);
			var packFieldId = rowNumbers[0];

			jQuery('input[name="pack_cont'+packFieldId+'"]').val(jQuery(this).val());
		});
	},

	hideExtraRatingBlocks : function() {
		jQuery('#longcarry_table').addClass('hide');
		jQuery('#stair_table').addClass('hide');
		jQuery('#elevator_table').addClass('hide');
	},

	registerEvents: function() {
		this.setCustomTariffType();
		this.registerFuelSurchargeEvent();
		jQuery('input[name="accesorial_fuel_surcharge"]').trigger('change');
        var selectedText = jQuery('select[name="effective_tariff"]').find('option:selected').text();
        if (selectedText && (selectedText.search('400NG') >= 0)) {
			jQuery('#getReportSelectButton').addClass('hide');
			jQuery('#getReportSelectButton').closest('tr').addClass('hide');
		} else {
            if(jQuery('#EditView')){
                this.registerReportsButtonEdit();
            } else {
                this.registerReportsButtonDetail();
            }
		}
		this.updateTabIndexValues();
		//So this is registering a checkbox event handler from the parent.
		// I am unsure it seems like this shouldn't be done like this..
		this.registerVehicleSpaceExclusivity();
		this.registerLockSaveOnUnratedChanges();
		if(this.tariffType == 'UAS' ||
			this.tariffType == '400N/104G' ||
			this.tariffType == 'Intra - 400N' ||
			this.tariffType == '400N Base' ||
			this.tariffType == 'ALLV-2A') {
			this.disableContainerColumn();
			this.hideTPGPricelockBlock();
			this.hideExtraRatingBlocks();
		}
	},

	initialize: function()
	{

	}

});
