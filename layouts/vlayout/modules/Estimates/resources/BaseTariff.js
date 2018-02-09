Inventory_Edit_Js("Estimates_BaseTariff_Js", {
	currentInstance: false,
	getInstance: function() {
		if(Estimates_BaseTariff_Js.currentInstance)
		{
			return Estimates_BaseTariff_Js.currentInstance;
		}
		Estimates_BaseTariff_Js.currentInstance = new Estimates_BaseTariff_Js();
		return Estimates_BaseTariff_Js.currentInstance;
	},
	I: function(){
		return Estimates_BaseTariff_Js.getInstance();
	},
}, {

	showAlertBox: function (data) {
		var aDeferred = jQuery.Deferred();
		var bootBoxModal = bootbox.alert(data['message'], function (result) {
			if (result) {
				aDeferred.reject(); //we only want the button to make the modal box disappear
			} else {
				aDeferred.reject();
			}
		});

		bootBoxModal.on('hidden', function (e) {
			//In Case of multiple modal. like mass edit and quick create, if bootbox is shown and hidden , it will remove
			// modal open
			if (jQuery('#globalmodal').length > 0) {
				// Mimic bootstrap modal action body state change
				jQuery('body').addClass('modal-open');
			}
		})
		return aDeferred.promise();
	},

	clearItemList: function () {
		var thisInstance = this;
		//@NOTE: destroy the existing items to start again!
		jQuery('[name^="lineitem"]').remove();
		jQuery('[name^="subitem"]').remove();
		jQuery('[class^="subitem-row"]').remove();

		var allLineItems = jQuery('input[name*="listPrice"]');
		if (typeof allLineItems != 'undefined' && allLineItems.length > 0) {
			jQuery.each(allLineItems, function (i, obj) {
				jQuery(obj).val('0');
				jQuery(obj).closest('tr').addClass('hide');
				thisInstance.quantityChangeActions(jQuery(obj).closest('tr'));
				jQuery(obj).closest('tr').addClass('hide');
			});
		}
	},

	updateLineItems: function(data) {
		var thisInstance = this;
		if (typeof data.result.lineitems == 'undefined') {
			return;
		}
		if (typeof data.result.lineitemids == "undefined") {
			return;
		}
		for (var key in data.result.lineitems) {
			if (typeof data.result.lineitemids[key] == "undefined") {
				continue;
			}
			//var lineItem = jQuery('input[name="productName' + data.result.lineitemids[key] + '"]');
			var lineItem = jQuery('.SER' + data.result.lineitemids[key]);
			// console.log(lineItem);
			lineItem.closest('tr').attr('style', null);
			lineItem.closest('tr').removeAttr('style');
			if (lineItem.length > 0) {
				lineItem.closest('tr').find('input[name*="listPrice"]').val(parseFloat(data.result.lineitems[key]).toFixed(2));
				thisInstance.quantityChangeActions(lineItem.closest('tr'));
			}
			if (typeof lineItem.closest('tr').attr('id') == "undefined") {
				continue;
			}
			var tempid = lineItem.closest('tr').attr('id').replace(/row/, '');
			jQuery('.subitem-row' + tempid).removeAttr('style');
			jQuery('.subitem-row' + tempid).attr('style', '');
			if (key in data.result.lineitem) {
				var moreStuff =
					"<input type='hidden' name='lineitem[]' value='" + tempid + "' />" +
					"<input type='hidden' name='lineitem_x_subtotal" + tempid + "' value='" + data.result.lineitem[key]['Subtotal'] + "' />" +
					"<input type='hidden' name='lineitem_x_description" + tempid + "' value='" + data.result.lineitem[key]['Description'] + "' />" +
					"<input type='hidden' name='lineitem_quantity" + tempid + "' value='" + data.result.lineitem[key]['Quantity'] + "' />" +
					"<input type='hidden' name='lineitem_location" + tempid + "' value='" + data.result.lineitem[key]['Location'] + "' />" +
					"<input type='hidden' name='lineitem_schedule" + tempid + "' value='" + data.result.lineitem[key]['Schedule'] + "' />" +
					"<input type='hidden' name='lineitem_rate" + tempid + "' value='" + data.result.lineitem[key]['Rate'] + "' />" +
					"<input type='hidden' name='lineitem_weight" + tempid + "' value='" + data.result.lineitem[key]['Weight'] + "' />";
				lineItem.closest('tr').after(moreStuff);
			}
			var detailArray = data.result.lineitemdetail ? data.result.lineitemdetail : data.result.lineitemdetailed;
			if (
				detailArray &&
				(key in detailArray) &&
				lineItem.length != 0
			) {
				var moreStuff = "<input type='hidden' name='hassubitem[]' value='" + tempid + "' />";
				lineItem.closest('tr').after(moreStuff);
				jQuery('.subitem-row' + tempid).attr('style', 'display: table-row;');
				jQuery('.subitem-row' + tempid).remove();
				for (var detailKey in detailArray[key].reverse()) {
					var subitemID = tempid + '_' + detailKey;
					var newrow = "<tr class='subitem-row" + tempid;
					// I know this is gross, but it's this or have rating change, I guess
					var costnet = detailArray[key][detailKey]['CostNet'] || detailArray[key][detailKey]['Gross'];
					var rate = detailArray[key][detailKey]['Rate'] || detailArray[key][detailKey]['UnitRate'];
					if (typeof costnet == "undefined" || parseFloat(costnet.replace(',','')) == 0) {
						continue;
					}
					newrow = newrow + "'><td><p style='margin-left: 235px;'>" + detailArray[key][detailKey]['Description'] + "</p></td><td style='text-align:right;'>" + parseFloat(costnet.replace(',','')).toFixed(2) +
						"</td></tr>" +
						"<input type='hidden' name='subitemitems" + tempid + "[]' value='" + subitemID + "' />" +
						"<input type='hidden' name='subitem_costnet" + subitemID + "' value='" + costnet + "' />" +
						"<input type='hidden' name='subitem_quantity" + subitemID + "' value='" + detailArray[key][detailKey]['Quantity'] + "' />" +
						"<input type='hidden' name='subitem_location" + subitemID + "' value='" + detailArray[key][detailKey]['Location'] + "' />" +
						"<input type='hidden' name='subitem_schedule" + subitemID + "' value='" + detailArray[key][detailKey]['Schedule'] + "' />" +
						"<input type='hidden' name='subitem_description" + subitemID + "' value='" + detailArray[key][detailKey]['Description'] + "' />" +
						"<input type='hidden' name='subitem_rate" + subitemID + "' value='" + rate + "' />" +
						"<input type='hidden' name='subitem_weight" + subitemID + "' value='" + detailArray[key][detailKey]['Weight'] + "' />" +
						"<input type='hidden' name='subitem_ratingitem" + subitemID + "' value='" + detailArray[key][detailKey]['RatingItem'] + "' />" +
						"<!-- end new row -->";
					lineItem.closest('tr').after(newrow);
				}
				arrowImages = "<img class='alignMiddle blockToggle' src='layouts/vlayout/skins/tightview/images/arrowRight.png' style='margin-right: 5px;'><img class='alignMiddle blockToggle' src='layouts/vlayout/skins/tightview/images/arrowDown.png' style='margin-right: 5px;  display: none;'>";
				lineItem.closest('tr').find('img').remove();
				lineItem.closest('tr').children().first().find('div').first().prepend(arrowImages);
				jQuery('.subitem-row' + tempid).hide();
			}
		}
	},

	quickRateDetail : function() {
		var currentTd = jQuery('#interstateRateQuick').closest('td');
		currentTd.progressIndicator();
		jQuery('#interstateRateQuick').addClass('hide');

		var dataURL = 'index.php?module=' + app.getModuleName() + '&action=QuickEstimate&record='+this.record;
		AppConnector.request(dataURL).then(
			function(data) {
				if(data.success) {

					var mileageTD = jQuery('#' + app.getModuleName() + '_detailView_fieldValue_interstate_mileage');
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
					jQuery('td:contains("Grand Total")').siblings().find('span').html(parseFloat(data.result.rateEstimate).toFixed(2));
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
        thisInstance.hideRatingInfoAndButtons(true);

		var dataURL = "index.php?module=" + app.getModuleName() + "&action=GetRateEstimate&record="+this.record+data.queryString;
		AppConnector.request(dataURL).then(
			function(data) {
				if(data.success) {
					console.dir("It's gotta be here");

					jQuery('#' + app.getModuleName() + '_editView_fieldName_interstate_mileage').val(parseInt(data.result.mileage));
					jQuery('#' + app.getModuleName() + '_editView_fieldName_billed_weight').val(parseInt(data.result.billed_weight));
					jQuery('#' + app.getModuleName() + '_editView_fieldName_guaranteed_price').val(parseFloat(data.result.guaranteed_price).toFixed(2)).prop('readonly',true);

                    if (typeof data.result.valuation_options !== 'undefined') {
                        if (jQuery('input[name="valuation_options"]').length == 0) {
                            jQuery('#EditView').append('<input type="hidden" name="valuation_options" />');
                        }
                        jQuery('input[name="valuation_options"]').val(data.result.valuation_options);
                    }

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
                    thisInstance.hideRatingInfoAndButtons(false);
					Estimates_Edit_Js.I().lineItemsJs.hideZeroValServices();
				}
				else {
					bootbox.alert(data.error.code + ": " + data.error.message);
					currentTd.progressIndicator({'mode':'hide'});
                    thisInstance.hideRatingInfoAndButtons(false);
				}
			},
			function(error, err) {
				bootbox.alert(error + ": " + err);
				currentTd.progressIndicator({'mode':'hide'});
                thisInstance.hideRatingInfoAndButtons(false);
			}
		);
	},

	getQuickRateEditQuery: function() {
		var fieldNames = ['weight', 'pickup_date', 'pickup_time', 'origin_zip', 'destination_zip', 'fuel_price', 'full_pack', 'full_unpack', 'bottom_line_discount', 'valuation_deductible', 'valuation_amount'];
		var loadDate = jQuery('input[name="load_date"]').val();
		var deliveryDate = jQuery('input[name="delivery_date"]').val();
		var weight = jQuery('input[name="'+fieldNames[0]+'"]').val();

		var pickupDate = jQuery('input[name="'+fieldNames[1]+'"]').val();
		var dateFormat = jQuery('input[name="'+fieldNames[1]+'"]').attr('data-date-format');
		if(dateFormat == "mm-dd-yyyy") {
			pickupDate = pickupDate.substring(6) + "-" + pickupDate.substring(0,5);
			loadDate = loadDate.substring(6) + "-" + loadDate.substring(0,5);
			deliveryDate = deliveryDate.substring(6) + "-" + deliveryDate.substring(0,5);
		}
		else if(dateFormat == "dd-mm-yyyy") {
			pickupDate = pickupDate.substring(6) + "-" + pickupDate.substring(3,5) + "-" + pickupDate.substring(0,2);
			loadDate = loadDate.substring(6) + "-" + loadDate.substring(3,5) + "-" + loadDate.substring(0,2);
			deliveryDate = deliveryDate.substring(6) + "-" + deliveryDate.substring(3,5) + "-" + deliveryDate.substring(0,2);
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


		//Validation
		var errorExists = false;
		var errorNum = 1;
		var errorString = 'The following errors have prevented creation of the rate estimate:\n';

		if(jQuery('[name="gvl_vehicle_only"]:checked').length == 0) {
			if (weight <= 0 || weight.length == 0) {
				errorString += errorNum + ") Weight must be greater than 0.\n";
				errorExists = true;
				errorNum++;
			}
		}
		if (app.getModuleName() == 'Actuals') {
			if (pickupDate.length != 10) {
				errorString += errorNum + ") A valid pickup date must be set\n";
				errorExists = true;
				errorNum++;
			}
			if (loadDate.length != 10) {
				errorString += errorNum + ") A valid load date must be set\n";
				errorExists = true;
				errorNum++;
			}
			if(jQuery('[name="instance"]').val() == 'graebel'
				&& jQuery('[name="business_line_est"]').val() == 'Interstate Move'
				&& jQuery('[name="billing_type"]').val() == 'National Accounts') {
				var contract = jQuery('[name="contract"]').val();
				if (contract.length <= 0 || contract === '0') {
					errorString += errorNum + ") Contract must be set<br>";
					errorExists = true;
					errorNum++;
				}
			}
		}
		if(deliveryDate.length != 10) {errorString += errorNum + ") A valid delivery date must be set\n"; errorExists = true; errorNum++;}
		if(isNaN(hour)) {errorString += errorNum + ") Pickup time must be set.\n"; errorExists = true; errorNum++;}
		if(originZip.length < 5) {errorString += errorNum + ") Origin Zip must be valid.\n"; errorExists = true; errorNum++;}
		if(destinationZip.length < 5) {errorString += errorNum + ") Destination Zip must be valid.\n"; errorExists = true; errorNum++;}
		if(fuelPrice.length == 0 || fuelPrice < 0) {errorString += errorNum + ") Fuel Price must be set.\n"; errorExists = true; errorNum++;}
		if(bottomLineDiscount.length == 0 || bottomLineDiscount < 0) {errorString += errorNum + ") Bottom Line Discount must be set and non-negative.\n"; errorExists = true; errorNum++;}
		if(jQuery('select[name="'+fieldNames[9]+'"]').not(':hidden').length > 0) {
			if (valDeductible === 'Select an Option') {
				errorString += errorNum + ") Valuation Deductible must be selected.\n";
				errorExists = true;
				errorNum++;
			}
			if (valuationAmount.length == 0 || valuationAmount < 0) {
				errorString += errorNum + ") Valuation Amount must be set.\n";
				errorExists = true;
				errorNum++;
			}
		}
		if(effective_tariff.length == 0) {errorString += errorNum + ") Effective Tariff must be set.\n"; errorExists = true; errorNum++;}

		var valDeductibleValue;
		if(valDeductible === '60¢ / lb.') {valDeductibleValue = "SIXTY_CENTS";}
		else if(valDeductible === 'Zero') {valDeductibleValue = "ZERO";}
		else if(valDeductible === '$250') {valDeductibleValue = "TWO_FIFTY";}
		else {valDeductibleValue = "FIVE_HUNDRED";}

		var queryString = "&weight="+weight+"&pickupDateTime="+pickupDateTime+"&originZip="+originZip+"&destinationZip="+destinationZip+"&fuelPrice="+fuelPrice+"&fullPackApplied="+fullPackApplied+"&fullUnpackApplied="+fullUnpackApplied+"&bottomLineDiscount="+bottomLineDiscount+"&valDeductible="+valDeductibleValue+"&valuationAmount="+valuationAmount+"&effective_tariff="+effective_tariff;

		if(errorExists) {
			return {success: false, errorString: errorString};
		}

		return {success: true, queryString: queryString};
	},

	detailedRateEdit : function() {
		if(jQuery('#isLocalRating').val() == 1)
		{
			this.localRateEstimateEdit();
			return;
		}
		var skipValidation = jQuery('select[name="business_line_est"]').find(':selected').val() == 'Auto Transportation';
		var thisInstance = this;
		var lineItemTable = this.getLineItemContentsContainer();
		var deferred = new jQuery.Deferred();
		var dataURL = 'index.php?module=' + app.getModuleName() + '&action=GetDetailedRate&record=' + this.record + '&type=editview';

		var fieldNames = ['weight', 'pickup_date', 'pickup_time', 'origin_zip', 'destination_zip', 'fuel_price', 'full_pack', 'full_unpack', 'bottom_line_discount', 'valuation_deductible', 'valuation_amount', 'business_line_est', 'valuation_deductible_amount'];
		var loadDate = jQuery('input[name="load_date"]').val();
		var deliveryDate = jQuery('input[name="delivery_date"]').val();
		var weight = jQuery('input[name="' + fieldNames[0] + '"]').val();
		var originZip = jQuery('input[name="' + fieldNames[3] + '"]').val();
		var destZip = jQuery('input[name="' + fieldNames[4] + '"]').val();
		var fullPack = (jQuery('input[name="' + fieldNames[6] + '"]').is(':checked') ? '1' : '0');
		var fullUnpack = (jQuery('input[name="' + fieldNames[7] + '"]').is(':checked') ? '1' : '0');
		var blDiscount = jQuery('input[name="' + fieldNames[8] + '"]').val();
		var valDeductible = jQuery('select[name="' + fieldNames[9] + '"]').siblings('.chzn-container').children('a').children('span').html();
		var valAmount = jQuery('input[name="' + fieldNames[10] + '"]').val();
		var businessLine = jQuery('select[name="' + fieldNames[11] + '"]').siblings().first().find('span').html();
		var valDeductibleAmount = jQuery('select[name="' + fieldNames[12] + '"]').siblings('.chzn-container').children('a').children('span').html();

		/*
		dataURL = dataURL+'&'+fieldNames[0]+'='+jQuery('input[name="'+fieldNames[0]+'"]').val();

		dataURL = dataURL+'&'+fieldNames[3]+'='+jQuery('input[name="'+fieldNames[3]+'"]').val();

		dataURL = dataURL+'&'+fieldNames[4]+'='+jQuery('input[name="'+fieldNames[4]+'"]').val();

		dataURL = dataURL+'&'+fieldNames[6]+'='+(jQuery('input[name="'+fieldNames[6]+'"]').is(':checked') ? '1':'0');

		dataURL = dataURL+'&'+fieldNames[7]+'='+(jQuery('input[name="'+fieldNames[7]+'"]').is(':checked') ? '1':'0');

		dataURL = dataURL+'&'+fieldNames[8]+'='+jQuery('input[name="'+fieldNames[8]+'"]').val();

		dataURL = dataURL+'&'+fieldNames[9]+'='+jQuery('select[name="'+fieldNames[9]+'"]').siblings('.chzn-container').children('a').children('span').html();

		dataURL = dataURL+'&'+fieldNames[10]+'='+jQuery('input[name="'+fieldNames[10]+'"]').val();

		dataURL = dataURL+'&'+fieldNames[11]+'='+jQuery('select[name="'+fieldNames[11]+'"]').siblings().first().find('span').html();
		*/
		var selectElement = jQuery('select[name="effective_tariff"]');
		var selectId = selectElement.attr('id');
		var chosenOption = selectElement.siblings('.chzn-container').find('.result-selected').attr('id');
		var effective_tariff = selectElement.find('option:eq(' + chosenOption.split('_')[3] + ')').val();
		var errorString = undefined;
		var errorExists = false;
		var errorNum = 1;

		if (!skipValidation) {
			if (jQuery('[name="gvl_vehicle_only"]:checked').length == 0) {
				{
					if (weight <= 0 || weight.length == 0) {
						errorString += errorNum + ") Weight must be greater than 0.<br>";
						errorExists = true;
						errorNum++;
					}
				}
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
			if (effective_tariff.length == 0) {
				errorString += errorNum + ") Effective Tariff must be set.<br>";
				errorExists = true;
				errorNum++;
			}
			if (jQuery('select[name="' + fieldNames[9] + '"]').not(':hidden').length > 0) {
				if (valDeductible === 'Select an Option') {
					errorString += errorNum + ") Valuation Deductible must be selected.<br>";
					errorExists = true;
					errorNum++;
				}
				if (valAmount.length == 0 || valAmount < 0) {
					errorString += errorNum + ") Valuation Amount must be set.<br>";
					errorExists = true;
					errorNum++;
				}
				if (valDeductibleAmount === 'Select an Option'
					&& (valDeductible == 'Full Value Protection' || valDeductible == 'Replacement Value Protection'
					|| valDeductible == 'Full Replacement Value')) {
					errorString += errorNum + ") Valuation Deductible Amount must be selected.<br>";
					errorExists = true;
					errorNum++;
				}
			}
			var blDiscount = jQuery('input[name="bottom_line_discount"]').val();
			if (blDiscount.length == 0 || blDiscount < 0) {
				errorString += errorNum + ") Bottom Line Discount must be set and non-negative.<br>";
				errorExists = true;
				errorNum++;
			}


		}

		//OT 17012 - Below items shouldn't be mandatory on estimates.
		if (app.getModuleName() == 'Actuals' && jQuery('input[name="instance"]').val() == 'graebel') {
			if (loadDate.length != 10) {
				errorString += errorNum + ") A valid load date must be set<br>";
				errorExists = true;
				errorNum++;
			}
			if (deliveryDate.length != 10) {
				errorString += errorNum + ") A valid delivery date must be set<br>";
				errorExists = true;
				errorNum++;
			}

			if(jQuery('[name="instance"]').val() == 'graebel'
					&& jQuery('[name="business_line_est"]').val() == 'Interstate Move'
					&& jQuery('[name="billing_type"]').val() == 'National Accounts') {
				var contract = jQuery('[name="contract"]').val();
				if (contract.length <= 0 || contract === '0') {
					errorString += errorNum + ") Contract must be set<br>";
					errorExists = true;
					errorNum++;
				}
			}
		}
		var originZip = jQuery('input[name="origin_zip"]').val();
		if (originZip.length < 5) {
			errorString += errorNum + ") Origin Zip must be valid.<br>";
			errorExists = true;
			errorNum++;
		}
		var destZip = jQuery('input[name="destination_zip"]').val();
		if (destZip.length < 5) {
			errorString += errorNum + ") Destination Zip must be valid.<br>";
			errorExists = true;
			errorNum++;
		}

		if (jQuery('select[name="' + fieldNames[9] + '"]').not(':hidden').length > 0) {
			var valDeductible = jQuery('select[name="valuation_deductible"]').val();
			if (valDeductible === 'Select an Option') {
				errorString += errorNum + ") Valuation Deductible must be selected.<br>";
				errorExists = true;
				errorNum++;
			}

			var valAmount = jQuery('input[name="valuation_amount"]').val();
			if (valAmount.length == 0 || valAmount < 0) {
				errorString += errorNum + ") Valuation Amount must be set.<br>";
				errorExists = true;
				errorNum++;
			}
		}

		var effective_tariff = jQuery('select[name="effective_tariff"]').val();
		if (effective_tariff.length == 0) {
			errorString += errorNum + ") Effective Tariff must be set.<br>";
			errorExists = true;
		}

		if (errorExists) {
			bootbox.alert(errorString, function () {
				jQuery('.interstateRateDetail').removeClass('hide').closest('td').progressIndicator({'mode': 'hide'});
				jQuery('#interstateRateQuick').removeClass('hide').closest('td').progressIndicator({'mode': 'hide'});
			});
			return;//{success: false, errorString: errorString};
		}

		thisInstance.saveProductCount();
		if(jQuery('input[name="pack_rates"]').length > 0) {
			jQuery('input[name="pack_rates"]').prop('disabled', true);
		}

		//Serialize Service Charges into a single hidden input
        Service_Charges_Js.compile();

		var formData = jQuery.param(jQuery('#EditView').serializeFormData());
		var index = formData.indexOf('&record=');
		var urlAppend = formData.substring(index, formData.length - 1);
		var dataURL = 'index.php?module=' + app.getModuleName() + '&action=GetDetailedRate&type=editview&pseudoSave=1' + urlAppend;

        thisInstance.hideRatingInfoAndButtons(true);

		AppConnector.request(dataURL).then(
			function(data) {
				if(jQuery('input[name="pack_rates"]').length > 0) {
					jQuery('input[name="pack_rates"]').prop('disabled', false);
				}
				if(data.success) {
					//@TODO fix this someday. so we can combine Actuals JS and Estimates JS.
				    //jQuery('name$='editView_fieldName_interstate_mileage').val...
                    //@NOTE: mileage and billed weight should be readonly because it's returned from rating.
					jQuery('#' + app.getModuleName() + '_editView_fieldName_interstate_mileage').val(parseInt(data.result.mileage)).prop('readonly',true);
					jQuery('#' + app.getModuleName() + '_editView_fieldName_billed_weight').val(parseInt(data.result.billed_weight)).prop('readonly',true);
					jQuery('#' + app.getModuleName() + '_editView_fieldName_accesorial_fuel_surcharge').val(parseInt(data.result.accesorial_fuel_surcharge));
					if(data.result.guaranteed_price) {
                        jQuery('#' + app.getModuleName() + '_editView_fieldName_guaranteed_price').val(parseFloat(data.result.guaranteed_price).toFixed(2)).prop('readonly', true);
                    }

                    if (typeof data.result.valuation_options !== 'undefined') {
                        if (jQuery('input[name="valuation_options"]').length == 0) {
                            jQuery('#EditView').append('<input type="hidden" name="valuation_options" />');
                        }
                        jQuery('input[name="valuation_options"]').val(data.result.valuation_options);
                    }

					if (data.result.lineitemsView) {
						if (jQuery('[name="instance"]').val() == 'graebel') {
							app.currentPageController.lineItemsJs.processRateResult(data.result.lineitemsView, false);
							app.currentPageController.lineItemsJs.registerLineItemEvents();
						} else {
							jQuery('#contentHolder_DETAILED_LINE_ITEMS').replaceWith(data.result.lineitemsView);
							jQuery('#contentHolder_DETAILED_LINE_ITEMS').removeClass('hide');
						}
					}
				}
				else {
					bootbox.alert(data.error.code + ": " + data.error.message);
				}
                thisInstance.hideRatingInfoAndButtons(false);
				deferred.resolve();
			}/*,
			function(err) {
				/*
				alert(error.error.code + ": " + error.error.message);
                thisInstance.hideRatingInfoAndButtons(false);
				*/
			//}
		);
		return deferred.promise();
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

	getLineItemContentsContainerfieldUpdatedEvent : 'Vtiger.Field.Updated',

	detailedRateDetail : function() {
		if(jQuery('#isLocalRating').val() == 1)
		{
			this.localRateEstimateDetail();
			return;
		}
		var thisInstance = this;
		var errorExists = false;
		var errorString = '';
		if (jQuery.trim(jQuery('[id$="_fieldValue_origin_zip"]').find('span.value').html()).length < 5) {
			errorExists = true;
			errorString += 'Origin Zip must be valid<br>\n';
		}
		if (jQuery.trim(jQuery('[id$="_fieldValue_destination_zip"]').find('span.value').html()).length < 5) {
			errorExists = true;
			errorString += 'Destination Zip must be valid<br>\n';
		}
		if (jQuery.trim(jQuery('[id$="_fieldValue_weight"]').find('span.value').html()).length < 1) {
			errorExists = true;
			errorString += 'Weight must be set<br>\n';
		}

		//@TODO: this doesn't popup words...
		// console.dir(errorString);
		if (errorExists) {
			thisInstance.showAlertBox({'message': errorString}).then(
				function (e) {
				},
				function (error, err) {
				}
			);
			return;
		}
		var currentTdElement = jQuery(this).closest('td');

		var deferred = new jQuery.Deferred();
		//console.dir('Function to generate XML and retrieve rate');
		var thisInstance = this;

		var dataURL = 'index.php?module=' + app.getModuleName() + '&action=GetDetailedRate&record='+this.record;
        thisInstance.hideRatingInfoAndButtons(true);
		AppConnector.request(dataURL).then(
			function(data) {
				if(data.success) {
					jQuery('input[name="interstate_mileage"]').prop('readonly',false).val(parseInt(data.result.mileage)).prop('readonly',true);
					jQuery('input[name="billed_weight"]').prop('readonly',false).val(parseInt(data.result.billed_weight)).prop('readonly',true);
					jQuery('#' + app.getModuleName() + '_editView_fieldName_accesorial_fuel_surcharge').val(parseInt(data.result.accesorial_fuel_surcharge));
					jQuery('input[name="guaranteed_price"]').prop('readonly',false).val(parseFloat(data.result.guaranteed_price).toFixed(2)).prop('readonly',true);

                    if (typeof data.result.valuation_options !== 'undefined') {
                        if (jQuery('input[name="valuation_options"]').length == 0) {
                            jQuery('#EditView').append('<input type="hidden" name="valuation_options" />');
                        }
                        jQuery('input[name="valuation_options"]').val(data.result.valuation_options);
                    }
					var currentTdElement = jQuery('input[name="interstate_mileage"]').closest('td');

					if (data.result.lineitemsView) {
						//jQuery('.lineItemsEdit').html(data.result.lineitemsView);
						if(jQuery('[name="instance"]').val() == 'graebel') {
						app.currentPageController.lineItemsJs.processRateResult(data.result.lineitemsView, true);
						// need to update the split spans if necessary
						jQuery('.serviceProviderDiv').each(function () {
							var v = jQuery('input[name^="serviceProviderSplit"]', this).val();
							var vp = jQuery('input[name^="serviceProviderPercent"]', this).val();
							var vm = jQuery('input[name^="serviceProviderMiles"]', this).val();
							var vw = jQuery('input[name^="serviceProviderWeight"]', this).val();
							jQuery(this).find('td')[1].innerHTML = v;
							jQuery(this).find('td')[2].innerHTML = vp;
							jQuery(this).find('td')[3].innerHTML = vm;
							jQuery(this).find('td')[4].innerHTML = vw;
						});
						} else {
							jQuery('#contentHolder_DETAILED_LINE_ITEMS').replaceWith(data.result.lineitemsView);
							jQuery('#contentHolder_DETAILED_LINE_ITEMS').removeClass('hide')
							app.currentPageController.lineItemsJs.saveDetailLineItems();
						}
					}
				}
				else {
					alert(data.error.code + ': ' + data.error.message);
				}
                //Don't be rude, leave this here.
                thisInstance.hideRatingInfoAndButtons(false);
				deferred.resolve();
			},
			function(error) {
                thisInstance.showAlertBox({'message':'There was an error, try again'});
                thisInstance.hideRatingInfoAndButtons(false);
			}
		);
		return deferred.promise();
	},

	reportButtonEdit : function() {
		var thisInstance = this;
        var assigned_user_id = jQuery('select[name="assigned_user_id"]').find('option:selected').val();
        var dataURL = 'index.php?module=' + app.getModuleName() + '&action=GetReport&record='+this.record+'&requestType=GetAvailableReports&type=editview&assigned_user_id='+assigned_user_id+'&local='+(jQuery('#isLocalRating').val() == 1)+'&effectiveTariff='+jQuery("[name='effective_tariff']").val()+'&effective_date='+jQuery("[name='interstate_effective_date']").val();
        if(jQuery('#isLocalRating').val() == 1){
            dataURL = 'index.php?module=' + app.getModuleName() + '&action=GetReportLocal&record=' + this.record + '&requestType=GetAvailableReports&type=editview&assigned_user_id=' + assigned_user_id + '&local=' + (jQuery('#isLocalRating').val() == 1) + '&effectivetariff=' + jQuery("[name='local_tariff']").val() + '&effective_date=' + jQuery("[name='effective_date']").val() + '&effectivedateid=' + jQuery("[name='EffectiveDateId']").val();
        }
        thisInstance.loadReportPopup(dataURL, thisInstance.editReportButtonAction);
    },

	reportButtonDetail : function() {
        var thisInstance = this;
        jQuery('#getReportSelectButton').closest('td').progressIndicator();
        jQuery('#getReportSelectButton').addClass('hide');
        var dataURL = 'index.php?module=' + app.getModuleName() + '&action=GetReport&record=' + this.record + '&requestType=GetAvailableReports';
        if (jQuery('#isLocalRating').val() == 1) {
            dataURL = 'index.php?module=' + app.getModuleName() + '&action=GetReportLocal&record=' + this.record + '&requestType=GetAvailableReports';
        }
        thisInstance.loadReportPopup(dataURL, thisInstance.detailReportButtonAction);
    },

    loadReportPopup : function (dataURL, ReportButtonAction) {
	    var thisInstance = this;
        AppConnector.request(dataURL).then(
            function(data) {
                if(data.success) {
                    if (jQuery('#reportContent').length == 0) {
						jQuery('.contentsDiv').append('<div id="reportContent"></div>');
					}
                    jQuery('#reportContent').html(data.result);
                    thisInstance.showReportColorBox();

                    jQuery('#reportContent').find('button').each(ReportButtonAction);

                } else if (data.error) {
                    thisInstance.showAlertBox({'message': data.error.message});
                }
                jQuery('#getReportSelectButton').closest('td').progressIndicator({'mode': 'hide'});
                jQuery('#getReportSelectButton').removeClass('hide');
                jQuery('td:contains("Grand Total")').closest('table').removeClass('hide');
            },
            function (error) {
                jQuery('#getReportSelectButton').closest('td').progressIndicator({'mode': 'hide'});
                jQuery('#getReportSelectButton').removeClass('hide');
                jQuery('td:contains("Grand Total")').closest('table').removeClass('hide');
                thisInstance.showAlertBox({'message': 'There was an error with rating engine, try again'});
            }
        );
    },

    showReportColorBox : function() {
        jQuery.colorbox({
            inline: true,
            width: '300px',
            height: '40%',
            left: '25%',
            top: '25%',
                        href:'#reportContent',
                        onClosed:function(){
                            jQuery(document.body).css({overflow:'auto'});
                jQuery('#reportContent').html('');
                        },
                        onComplete:function(){
                            jQuery(document.body).css({overflow:'hidden'});
                        }
                    });
    },

    editReportButtonAction : function () {
        var thisInstance = app.currentPageController.currentTariff;
                        jQuery(this).on('click', function() {
            var assigned_user_id = jQuery('select[name="assigned_user_id"]').find('option:selected').val();
                            jQuery('#reportContent').find('.contents').addClass('hide');
                            jQuery('#reportContent').progressIndicator();
								var reportURL = 'index.php?module=' + app.getModuleName() + '&action=GetReport&reportId=' + jQuery(this).attr('name') + '&reportName=' + encodeURIComponent(jQuery(this).html());
                            if(jQuery('#isLocalRating').val() == 1){
                reportURL = 'index.php?module=' + app.getModuleName() + '&action=GetReportLocal&record=' + thisInstance.record + '&requestType=GetReport&viewtype=editview&assigned_user_id=' + assigned_user_id + '&local=' + (jQuery('#isLocalRating').val() == 1) + '&effectivetariff=' + jQuery("[name='local_tariff']").val() + '&effective_date=' + jQuery("#" + app.getModuleName() + "_detailView_fieldValue_effective_date").find('input').val() + '&effectivedateid=' + jQuery("[name='EffectiveDateId']").val() + '&reportId=' + jQuery(this).attr('name') + '&reportName=' + encodeURIComponent(jQuery(this).html());
                            }

                            var formData = jQuery.param(jQuery('#EditView').serializeFormData());
                            var index = formData.indexOf('&record=');
                            var urlAppend = formData.substring(index, formData.length);
                            reportURL = reportURL + urlAppend;
                            reportURL = reportURL + '&interstate_mileage=' + jQuery('input[name="interstate_mileage"]').val();
                            reportURL = reportURL + '&effective_tariff=' + jQuery('select[name="effective_tariff"]').val();
                            reportURL = reportURL + '&wsdlURL=' + jQuery('input[name="wsdlURL"]').val();
                            reportURL = reportURL + '&validtill=' + jQuery('input[name="validtill"]').val();
                            reportURL = reportURL + '&type=editview&assigned_user_id=' + assigned_user_id;
                            AppConnector.request(reportURL).then(
                                function (data) {
                                    if (data.success) {
                                        jQuery('#EditView').append('<input type="hidden" name="gotoDocuments" value="' + data.result + '">');
                                        jQuery('#EditView').append('<input type="hidden" name="reportSave" value="1">');
                                        jQuery('#EditView').submit();
                                        } else {
                                            jQuery.colorbox.close();
                                            thisInstance.showAlertBox({'message':data.error.message});
                                    }
                                },
                                function (error) {
                                }
                            );
                        });
	},

    detailReportButtonAction :  function () {
	    var thisInstance = app.currentPageController.currentTariff;
						jQuery(this).on('click', function() {
							jQuery('#reportContent').find('.contents').addClass('hide');
							jQuery('#reportContent').progressIndicator();
								var reportURL = 'index.php?module=' + app.getModuleName() + '&action=GetReport&record='+thisInstance.record+'&reportId='+jQuery(this).attr('name')+'&reportName='+encodeURIComponent(jQuery(this).html());
								if(jQuery('#isLocalRating').val() == 1) {
									reportURL = 'index.php?module=' + app.getModuleName() + '&action=GetReportLocal&record='+thisInstance.record+'&reportId='+jQuery(this).attr('name')+'&reportName='+encodeURIComponent(jQuery(this).html());
								}
                            reportURL = reportURL + '&wsdlURL=' + jQuery('input[name="wsdlURL"]').val();
							AppConnector.request(reportURL).then(
								function(data) {
									if(data.success) {
										window.location.href = 'index.php?module=Documents&view=Detail&record='+data.result;
									} else {
										jQuery.colorbox.close();
										thisInstance.showAlertBox({'message':data.error.message});
									}
								},
								function(error) {
									    thisInstance.showAlertBox({'message':'There was an error retrieving that report, try again'})
								}
							);
						});
	},

	localRateEstimateEdit : function() {
		var thisInstance = this;
		jQuery('.interstateRateDetail').addClass('hide');
		jQuery('.interstateRateDetail').closest('td').progressIndicator();
		thisInstance.saveProductCount();

		var destZip = jQuery('input[name="destination_zip"]').val();
		var originZip = jQuery('input[name="origin_zip"]').val();
		var localTariff = jQuery('select[name="local_tariff"]').val();
                if(!localTariff){
                    localTariff = jQuery('select[name="effective_tariff"]').val(); 
                }
                jQuery('[name="local_billed_weight"]').val(jQuery('[name="local_weight"]').val()).attr('readonly','readonly');

		if(destZip=='' || localTariff=='' || originZip == '') {
			var msg = 'The following errors have prevented creation of the rate estimate:<br>';
			var count = 1;
			if(originZip=='') {
				msg += count+') Origin zip must be valid.<br>';
				count++;
			}
			if(destZip=='') {
				msg += count+') Destination zip must be valid.<br>';
				count++;
			}
			if(localTariff=='') {
				msg += count+') Effective Tariff must be set';
			}

			thisInstance.showAlertBox({'message' : msg});
			jQuery('.interstateRateDetail').removeClass('hide');
			jQuery('.interstateRateDetail').closest('td').progressIndicator({'mode': 'hide'});
		} else {

			var formData = jQuery.param(jQuery('#EditView').serializeFormData());
			var index = formData.indexOf('&record=');
			var dataURL = 'index.php?module=' + app.getModuleName() + '&action=GetLocalRate&type=editview&pseudoSave=1' + formData.substring(index, formData.length - 1);
			AppConnector.request(dataURL).then(
				function (data) {
					if (data.success) {

                        if (typeof data.result.valuation_options !== 'undefined') {
                            if (jQuery('input[name="valuation_options"]').length == 0) {
                                jQuery('#EditView').append('<input type="hidden" name="valuation_options" />');
                            }
                            jQuery('input[name="valuation_options"]').val(data.result.valuation_options);
                        }
						jQuery('#' + app.getModuleName() + '_editView_fieldName_interstate_mileage').val(parseInt(data.result.mileage));
						jQuery('#' + app.getModuleName() + '_editView_fieldName_billed_weight').val(parseInt(data.result.billed_weight));
						jQuery('#' + app.getModuleName() + '_editView_fieldName_accesorial_fuel_surcharge').val(parseInt(data.result.accesorial_fuel_surcharge));
						var tariffType = jQuery('#tariffType_' + jQuery('select[name="effective_tariff"] > option:selected').val()).val();
						if(jQuery('[name="instance"]').val() == 'graebel' && (tariffType == '1950-B' || tariffType == '400N/104G')) {
							jQuery('[name="sit_origin_weight"]').val(parseInt(data.result.billed_weight));
						}
						jQuery('#' + app.getModuleName() + '_editView_fieldName_guaranteed_price').val(parseFloat(data.result.guaranteed_price).toFixed(2)).prop('readonly',true);
						if(jQuery('[name="instance"]').val() == 'graebel') {
							app.currentPageController.lineItemsJs.processRateResult(data.result.lineitemsView, false);
							app.currentPageController.lineItemsJs.registerLineItemEvents();
						} else if(data.result.lineitemsView) {
							jQuery('#contentHolder_DETAILED_LINE_ITEMS').replaceWith(data.result.lineitemsView);
							jQuery('#contentHolder_DETAILED_LINE_ITEMS').removeClass('hide')
						}
						else {
							//@NOTE: Remove All line items to add in the new ones,
							//This will remove all subitem* rows and hide lineitem rows and set all listprices to 0.
							thisInstance.clearItemList();
							//@NOTE: This will set the line items
							thisInstance.updateLineItems(data);
							//@NOTE: This hides/shows the line items
							Estimates_Edit_Js.I().lineItemsJs.hideZeroValServices();

							/*
							 for (var key in data.result.lineitems) {
							 //console.dir('key : ' + key);
							 //console.dir('value : ' + data.result.lineitems[key]);
							 //console.dir('productId : ' + data.result.lineitemids[key]);
							 //var lineItem = jQuery('input[name="productName' + data.result.lineitemids[key] + '"]');
							 var lineItem = jQuery('.SER' + data.result.lineitemids[key]);
							 if (lineItem.length > 0) {
							 console.dir('found an existing lineItem');
							 lineItem.closest('tr').find('input[name*="listPrice"]').val(parseFloat(data.result.lineitems[key]).toFixed(2));
							 thisInstance.quantityChangeActions(lineItem.closest('tr'));
							 thisInstance.hideZeroValServices();
							 } else {
							 console.dir('Make a new line item now');
							 }
							 }
							 */
							//	var currentTable = jQuery('th:contains("Item Details")').closest('table');
							//	if(typeof data.result.lineitems != 'undefined'){
							//		for(var key in data.result.lineitems) {
							//			console.dir('local key: '+key);
							//			console.dir('local data : '+data.result.lineitems[key]);
							//			var name = currentTable.find('input[value="'+key+'"]').attr("name");
							//			var regExp = /\d+/g;
							//			var number = name.match(regExp)[0];
							//			//#netPrice
							//			var priceNode = currentTable.find('#netPrice'+number);
							//			var priceRow = priceNode.closest('tr');
							//			priceNode.html(parseFloat(data.result.lineitems[key]).toFixed(2));
							//			currentTable.find('input[name="listPrice'+number+'"]').val(parseFloat(data.result.lineitems[key]).toFixed(2));
							//			if(parseFloat(data.result.lineitems[key]) == 0) {
							//				priceRow.addClass('hide');
							//			}
							//			else {
							//				priceRow.removeClass('hide');
							//			}
							//		}
							//	}
							if (typeof data.result.servicecost != 'undefined') {
								data.result.servicecost.forEach(function (key) {
									var serviceid = key.serviceid;
									var cost_service_total = key.cost_service_total;
									var cost_container_total = key.cost_container_total;
									var cost_packing_total = key.cost_packing_total;
									var cost_unpacking_total = key.cost_unpacking_total;
									var cost_crating_total = key.cost_crating_total;
									var cost_uncrating_total = key.cost_uncrating_total;
									jQuery('input[name="cost_service_total' + serviceid + '"]').val(cost_service_total);
									jQuery('input[name="cost_container_total' + serviceid + '"]').val(cost_container_total);
									jQuery('input[name="cost_packing_total' + serviceid + '"]').val(cost_packing_total);
									jQuery('input[name="cost_unpacking_total' + serviceid + '"]').val(cost_unpacking_total);
									jQuery('input[name="cost_crating_total' + serviceid + '"]').val(cost_crating_total);
									jQuery('input[name="cost_uncrating_total' + serviceid + '"]').val(cost_uncrating_total);
								});
							}
							if (typeof data.result.BulkyItems != 'undefined') {
								data.result.BulkyItems.forEach(function (key) {
									var bulkyid = key.bulkyid;
									var cost_bulky_item = key.cost_bulky_item;
									var serviceid = key.serviceid;

									jQuery('#' + bulkyid + '[name^="BulkyCost' + serviceid + '"]').val(cost_bulky_item);
									//<input type="hidden" class="hide" name="BulkyCost200" id="573" value="360.00">
								});
							}
						}
						if (typeof data.result.CratingItems != 'undefined') {
							data.result.CratingItems.forEach(function (key) {
								var cost_crating = key.cost_crating;
								var cost_uncrating = key.cost_uncrating;
								var crateid = key.crateid;
								var serviceid = key.serviceid;

								jQuery('input[name="CratingCost' + serviceid + '-' + crateid + '"]').val(cost_crating);
								jQuery('input[name="UncratingCost' + serviceid + '-' + crateid + '"]').val(cost_uncrating);
								//<input type="hidden" class="hide" name="CratingCost58-1" value="556172.00">
								//<input type="hidden" class="hide" name="UncratingCost58-1" value="104567.00">
							});
						}
						if (typeof data.result.PackingItems != 'undefined') {
							data.result.PackingItems.forEach(function (key) {
								var cost_container = key.cost_container;
								var cost_packing = key.cost_packing;
								var cost_unpacking = key.cost_unpacking;
								var packid = key.packid;
								var serviceid = key.serviceid;

								jQuery('.' + packid + '[name^="ContainerCost' + serviceid + '"]').val(cost_container);
								jQuery('.' + packid + '[name^="PackingCost' + serviceid + '"]').val(cost_packing);
								jQuery('.' + packid + '[name^="UnpackingCost' + serviceid + '"]').val(cost_unpacking);
								//<input type="hidden" class="hide 173" name="ContainerCost165-0" value="6.30">
								//<input type="hidden" class="hide 173" name="PackingCost165-0" value="11.20">
								//<input type="hidden" class="hide 173" name="UnpackingCost165-0" value="14.70">
							});
						}

						jQuery('.interstateRateDetail').removeClass('hide');
						jQuery('.interstateRateDetail').closest('td').progressIndicator({'mode': 'hide'});
						//jQuery('td:contains("Grand Total")').siblings().find('span').html(parseFloat(data.result.rateEstimate).toFixed(2));
						jQuery('[name="localmove_mileage"]').val(data.result.miles);
						jQuery('[name="local_mileage"]').val(data.result.miles);
						jQuery('input[name="total"]').val(parseFloat(data.result.rateEstimate).toFixed(2));
						jQuery('td:contains("Grand Total")').closest('table').removeClass('hide');
					}
					else {
						thisInstance.showAlertBox({'message' : data.error.message});
						jQuery('.interstateRateDetail').removeClass('hide');
						jQuery('.interstateRateDetail').closest('td').progressIndicator({
							'mode': 'hide'
						});
						jQuery('td:contains("Grand Total")').closest('table').removeClass('hide');
					}
				},
				function (error) {
					jQuery('.interstateRateDetail').removeClass('hide');
					jQuery('.interstateRateDetail').closest('td').progressIndicator({'mode': 'hide'});
					jQuery('td:contains("Grand Total")').closest('table').removeClass('hide');
				}
			);
		}
	},

	localRateEstimateDetail : function () {
		var thisInstance = this;
		jQuery('.interstateRateDetail').addClass('hide');
		jQuery('.interstateRateDetail').closest('td').progressIndicator();
		var dataURL = 'index.php?module=' + Estimates_Detail_Js.I().moduleName + '&action=GetLocalRate&type=detailview&record=' + getQueryVariable('record');
		AppConnector.request(dataURL).then(
			function (data) {
				if (data.success) {
					if (jQuery('[name="instance"]').val() == 'graebel') {
						app.currentPageController.lineItemsJs.processRateResult(data.result.lineitemsView, false);
						app.currentPageController.lineItemsJs.registerLineItemEvents();
					} else if(data.result.lineitemsView) {
						jQuery('#contentHolder_DETAILED_LINE_ITEMS').replaceWith(data.result.lineitemsView);
						jQuery('#contentHolder_DETAILED_LINE_ITEMS').removeClass('hide')
					}
					else {
						var currentTable = jQuery('th:contains("Item Details")').closest('table');

						//hide all of the line items to catch even ones not returned.
						currentTable.find('tbody').children().each(function (index) {
							if (index > 0) {
								jQuery(this).addClass('hide');
							}
						});

						for (var key in data.result.lineitems) {
							//find the ones we are using and turn them back on.
							if (parseFloat(data.result.lineitems[key]) != 0) {
								currentTable.find('tbody').children().each(function (index) {
									if (index > 0) {
										var test = jQuery(this).find('td').children().html();
										if (test) {
											if (test.trim() == key) {
												//we match the key.
												jQuery(this).removeClass('hide');
												//update the value that's in span, assuming it's the only span.
												jQuery(this).find('span').html(parseFloat(data.result.lineitems[key]).toFixed(2));
											}
										}
									}
								});
							}
						}
					}
					//jQuery('td:contains("Grand Total")').siblings().find('span').html(parseFloat(data.result.rateEstimate).toFixed(2));
				} else if(data.error) {

					thisInstance.showAlertBox({'message':data.error.message});
				}
				jQuery('.interstateRateDetail').removeClass('hide');
				jQuery('.interstateRateDetail').closest('td').progressIndicator({'mode': 'hide'});
				jQuery('td:contains("Grand Total")').closest('table').removeClass('hide');
			},
			function (error) {
				jQuery('.interstateRateDetail').removeClass('hide');
				jQuery('.interstateRateDetail').closest('td').progressIndicator({'mode': 'hide'});
				jQuery('td:contains("Grand Total")').closest('table').removeClass('hide');
				thisInstance.showAlertBox({'message':'There was an error with rating engine, try again'});
			}
		);
	},

	validateEstimateBeforeReport: function() {
		var originZip 		= jQuery('.value_LBL_QUOTES_ORIGINZIP').find('span').html().trim().length;
		var destZip 	    = jQuery('.value_LBL_QUOTES_DESTINATIONZIP').find('span').html().trim().length;
		if(jQuery('.value_LBL_QUOTES_BUSINESSLINE').length > 0){
			var businessLine    = jQuery('.value_LBL_QUOTES_BUSINESSLINE').find('span').html().trim().length;
		}
		else{
			var businessLine    = jQuery('.value_LBL_QUOTES_MOVETYPE').find('span').html().trim().length;
		}

		var grandTotals = jQuery('.loadedLineItem:not(.hide)').length;
		var count = 1;
		var error = '';
		if(!originZip) {
			error +=  count+') Origin zip is required<br>';
			count++;
		}
		if(!destZip) {
			error += count+') Destination zip is required<br>';
			count++;
		}
		if(!businessLine) {
			error += count+') Business Line is required<br>';
			count++;
		}
		if(grandTotals==0) {
			error += count+') You must rate the estimate first<br>';
		}
		if(error) {
			return error;
		}
		return false;
	},

    initialize : function () {
	    this.record = getQueryVariable('record');
    },

    hideRatingInfoAndButtons: function(toggle) {
        if(toggle) {
            jQuery('#contentHolder_DETAILED_LINE_ITEMS').find('table').addClass('hide');
            jQuery('#contentHolder_DETAILED_LINE_ITEMS').progressIndicator();

            //These no longer exist... does that matter?
            jQuery('th:contains("Item Details")').closest('table').find('tbody').addClass('hide');
            jQuery('th:contains("Item Details")').closest('table').progressIndicator();

            jQuery('td:contains("Grand Total")').closest('table').addClass('hide');

            jQuery('#interstateRateQuick').addClass('hide');
            jQuery('.interstateRateDetail').addClass('hide');
            jQuery('.interstateRateDetail').closest('td').progressIndicator();

            jQuery('.requote').addClass('hide');
            jQuery('.requote').closest('td').progressIndicator();

        }else{
            jQuery('#contentHolder_DETAILED_LINE_ITEMS').find('table').removeClass('hide');
            jQuery('#contentHolder_DETAILED_LINE_ITEMS').progressIndicator({'mode':'hide'});

            jQuery('th:contains("Item Details")').closest('table').find('tbody').removeClass('hide');
            jQuery('th:contains("Item Details")').closest('table').progressIndicator({'mode': 'hide'});

            jQuery('td:contains("Grand Total")').closest('table').removeClass('hide');

            jQuery('#interstateRateQuick').removeClass('hide');
            jQuery('.interstateRateDetail').removeClass('hide');
            jQuery('.interstateRateDetail').closest('td').progressIndicator({'mode': 'hide'});

            jQuery('.requote').removeClass('hide');
            jQuery('.requote').closest('td').progressIndicator({'mode': 'hide'});
        }
    },

});
