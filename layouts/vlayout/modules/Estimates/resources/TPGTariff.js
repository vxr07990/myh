Estimates_BaseSIRVA_Js("Estimates_TPGTariff_Js", {
    detailView: false,
    currentInstance: false,
    parent: '',
    tariffType: '',
    type: '',
    getInstance: function () {
        return new Estimates_TPGTariff_Js();
    }
}, {
    registerPricingColorLock: function () {
        //console.dir('registerPricingColorLock');
        var thisInstance = this;
        //console.dir(jQuery('input[name="load_date"]').val());
        if (
            (
                jQuery('input[name="load_date"]').val()
                && jQuery('#grandTotal').html() != '0.00'
                && jQuery('input[name="interstate_effective_date"]').val()
            )
        ) {
            //if (jQuery('input[name="load_date"]').val().length > 0) {
            //console.dir('theres a load date');
            jQuery('input:checkbox[name="pricing_color_lock"]').prop('checked', true);
            //}
        } else {
            jQuery('input:checkbox[name="pricing_color_lock"]').prop('checked', false);
        }
        var isLocked = jQuery('input:checkbox[name="pricing_color_lock"]').prop('checked');
        if (isLocked) {
            jQuery('select[name="demand_color"]').prop('disabled', true).trigger('liszt:updated');
            jQuery('select[name="pricing_level"]').prop('disabled', true).trigger('liszt:updated');
        } else {
            jQuery('select[name="demand_color"]').prop('disabled', false).trigger('liszt:updated');
            jQuery('select[name="pricing_level"]').prop('disabled', false).trigger('liszt:updated');
        }
    },
    registerEffectiveDateChange: function () {
        thisInstance = this;
        jQuery('input[name="interstate_effective_date"]').on('change', function () {
            thisInstance.registerPricingColorLock();
        });
    },
    hideTPGPricelockBlock: function () {
        //console.dir('hide that block!');
        var priceLockBlock = jQuery('table[name="LBL_QUOTES_TPGPRICELOCK"]');
        priceLockBlock.addClass('hide');
    },
    unhideTPGPricelockBlock: function () {
        //console.dir('unhide that block!');
        var priceLockBlock = jQuery('table[name="LBL_QUOTES_TPGPRICELOCK"]');
        priceLockBlock.removeClass('hide');
        priceLockBlock.insertAfter(jQuery('table[name="LBL_QUOTES_INTERSTATEMOVEDETAILS"]').next());
        if (!priceLockBlock.next().is('br')) {
            priceLockBlock.after('<br>');
        }
    },
    registerLoadDateChangeEvent: function () {
        //console.dir('load date change is registered');
        var thisInstance = this;
        jQuery('input[name="load_date"]').on('change', function () {
            //console.dir('hey you changed the load date');
            /* if (jQuery(this).val() !== '') {
             //console.dir('in this if'); */
            thisInstance.registerPricingColorLock();
            /* 			} else {
             //console.dir('in this else');
             //only re-enable the pricing color if we haven't rated yet.
             if (jQuery('span[name="grandTotal"]').html() == '0.00') {
             jQuery('input:checkbox[name="pricing_color_lock"]').prop('checked', false);
             thisInstance.registerPricingColorLock();
             }
             } */
        });
    },
    registerSMFType: function () {
        var thisInstance = this;
        //set to 1 if flat was the last thing updated set to 0 if it was percent was the last thing updated
        var SMFTypeToggle = function () {
            //do the toggling
            if (jQuery(this).prop('name') == 'percent_smf') {
                jQuery('input:checkbox[name="smf_type"]').prop('checked', false);
                if (jQuery('#EditView').length == 0) {
                    var currentTdElement = jQuery('input:checkbox[name="smf_type"]').closest('td');
                    var detailViewValue = jQuery('.value', currentTdElement);
                    var editElement = jQuery('.edit', currentTdElement);
                    var actionElement = jQuery('.summaryViewEdit', currentTdElement);
                    var fieldnameElement = jQuery('.fieldname', editElement);
                    var fieldName = fieldnameElement.val();
                    var fieldElement = jQuery('[name="' + fieldName + '"]', editElement);
                    var previousValue = fieldnameElement.data('prevValue');
                    currentTdElement.progressIndicator();
                    //console.dir('Adding Progress Indicator to '+currentTdElement.prop('name'));
                    detailViewValue.addClass('hide');
                    var fieldNameValueMap = {};
                    fieldNameValueMap["value"] = 0;
                    fieldNameValueMap["field"] = fieldName;
                    thisInstance.saveFieldValues(fieldNameValueMap).then(function (response) {
                        var postSaveRecordDetails = response.result;
                        //console.dir('Removing Progress Indicator from ');
                        //console.dir(currentTdElement);
                        currentTdElement.progressIndicator({
                            'mode': 'hide'
                        });
                        detailViewValue.removeClass('hide');
                        jQuery('#interstateRateQuick').removeClass('hide');
                        actionElement.show();
                        detailViewValue.html(postSaveRecordDetails[fieldName].display_value);
                        var newVal = 0;
                        fieldElement.trigger(thisInstance.fieldUpdatedEvent, {
                            'old': previousValue,
                            'new': newVal
                        });
                        fieldnameElement.data('prevValue', newVal);
                        fieldElement.data('selectedValue', newVal);
                    });
                }
            } else if (jQuery(this).prop('name') == 'flat_smf') {
                jQuery('input:checkbox[name="smf_type"]').prop('checked', true);
                if (jQuery('#EditView').length == 0) {
                    //this is detail view force the save.
                    var currentTdElement = jQuery('input:checkbox[name="smf_type"]').closest('td');
                    var detailViewValue = jQuery('.value', currentTdElement);
                    var editElement = jQuery('.edit', currentTdElement);
                    var actionElement = jQuery('.summaryViewEdit', currentTdElement);
                    var fieldnameElement = jQuery('.fieldname', editElement);
                    var fieldName = fieldnameElement.val();
                    var fieldElement = jQuery('[name="' + fieldName + '"]', editElement);
                    var previousValue = fieldnameElement.data('prevValue');
                    currentTdElement.progressIndicator();
                    //console.dir('Adding Progress Indicator to '+currentTdElement.prop('name'));
                    detailViewValue.addClass('hide');
                    var fieldNameValueMap = {};
                    fieldNameValueMap["value"] = 1;
                    fieldNameValueMap["field"] = fieldName;
                    thisInstance.saveFieldValues(fieldNameValueMap).then(function (response) {
                        var postSaveRecordDetails = response.result;
                        //console.dir('Removing Progress Indicator from ');
                        //console.dir(currentTdElement);
                        currentTdElement.progressIndicator({
                            'mode': 'hide'
                        });
                        detailViewValue.removeClass('hide');
                        jQuery('#interstateRateQuick').removeClass('hide');
                        actionElement.show();
                        detailViewValue.html(postSaveRecordDetails[fieldName].display_value);
                        var newVal = 1;
                        fieldElement.trigger(thisInstance.fieldUpdatedEvent, {
                            'old': previousValue,
                            'new': newVal
                        });
                        fieldnameElement.data('prevValue', newVal);
                        fieldElement.data('selectedValue', newVal);
                    });
                }
            }
        };
        jQuery('input[name="percent_smf"]').on('change', SMFTypeToggle);
        jQuery('input[name="percent_smf"]').on('value_change', function(){
          $(this).val(Math.round($(this).val().toFixed(2)));
        });
        jQuery('input[name="flat_smf"]').on('change', SMFTypeToggle);
    },
    detailedRateEdit: function (requote) {
        var thisInstance = this;

        var requoteTariffs = ['TPG', 'Pricelock', 'TPG GRR', 'Pricelock GRR', 'Allied Express', 'Blue Express', 'UAS'];
        var tariffId = jQuery('select[name="effective_tariff"]').val();

        // Hide fields during confirmations.
        thisInstance.hideRatingInfoAndButtons(true);

        //@NOTE: First parameter is the instance it should run in, set to 'ANY' in order to allow for all.
        //@NOTE: If the current instance is not equal to the instance provided, it will pass without checking.
        thisInstance.confirmDatesWithTransitGuide('sirva', function(result) {
            if(result.warnings) {
                bootbox.alert(result.warnings);
            }
            if (requoteTariffs.indexOf(jQuery('#effective_tariff_custom_type').val()) != -1) {
                //Apply requote logic
                var validtill = jQuery('input[name="validtill"]').val();
                var dateformat = jQuery('input[name="validtill"]').attr("data-date-format");

                var dataURL = 'index.php?module=Estimates&action=ValidateValidThroughDate&date=' + validtill + '&format=' + dateformat;

                AppConnector.request(dataURL).then(function(data) {
                    // Date validation to confirm estimate is ratable before deferring to detailed rate function.
                    if(typeof data.result.expired != 'undefined' &&
                        data.result.expired && !requote) {
                        bootbox.alert("Estimate has expired! Please check the Re-quote box to generate a new rate.");
                        console.log('bootbox alert should have fired');
                        thisInstance.hideRatingInfoAndButtons(false);
                    }else if(requote) {
                        var today = new Date();
                        jQuery('input[name="interstate_effective_date"]').DatePickerSetDate(today, true);
                        jQuery('input[name="interstate_effective_date"]').val(jQuery('input[name="interstate_effective_date"]').DatePickerGetDate(true)).trigger('change');
                        thisInstance.detailedRate(thisInstance, requote);
                    }else {
                      // Can't just append it in order to not run if estimate is expired.
                      thisInstance.detailedRate(thisInstance, requote);
                    }
                });
            }else{
                thisInstance.detailedRate(thisInstance, requote);
            }
        }, function(err) {
            bootbox.alert(err);
            console.log('bootbox alert should have fired');
            thisInstance.hideRatingInfoAndButtons(false);
        });
    },
    quickRateEdit: function () {
        var thisInstance = this;
        var lineItemTable = this.getLineItemContentsContainer();
        var data = thisInstance.getQuickRateEditQuery();
        if (!data.success) {
            bootbox.alert(data.errorString);
            return;
        }
        var currentTd = jQuery('#interstateRateQuick').closest('td');
        currentTd.progressIndicator();
        jQuery('#interstateRateQuick').addClass('hide');
        jQuery('.interstateRateDetail').addClass('hide');
        jQuery('th:contains("Item Details")').closest('table').find('tbody').addClass('hide');
        jQuery('td:contains("Grand Total")').closest('table').addClass('hide');
        jQuery('th:contains("Item Details")').closest('table').progressIndicator();
        var dataURL = "index.php?module=" + Estimates_Edit_Js.I().moduleName + "&action=GetRateEstimate&record=" + getQueryVariable("record") + data.queryString;
        AppConnector.request(dataURL).then(function (data) {
            if (data.success) {
                jQuery('#Estimates_editView_fieldName_interstate_mileage').val(parseInt(data.result.mileage));
                //Remove first product line item if blank
                var firstItem = jQuery('input[name="productName1"]');
                if (firstItem.val() == '') {
                    firstItem.closest('tr.' + thisInstance.rowClass).remove();
                    thisInstance.checkLineItemRow();
                    thisInstance.lineItemDeleteActions();
                }
                //Update Rate Estimate field
                //jQuery('input[name="rate_estimate"]').val(parseFloat(data.result.rateEstimate).toFixed(2));
                //Adjust existing line items or add new line items if they do not exist
                for (var key in data.result.lineitems) {
                    //var lineItem = jQuery('input[value="' + key + '"]');
                    var lineItem = jQuery('.SER' + data.result.lineitemids[key]);
                    if (lineItem.length) {
                        //Line item exists
                        lineItem.closest('tr').find('input[name*="listPrice"]').val(parseFloat(data.result.lineitems[key]).toFixed(2));
                        thisInstance.quantityChangeActions(lineItem.closest('tr'));
                    } else if (jQuery('input[name*="productName"]').length == 13) {
                        //Twelve line items exist in addition to the clone copy, but vals are not set, indicating new Estimate
                        if (key == 'Transportation') {
                            lineItem = jQuery('input[name="productName1"]');
                        } else if (key == 'Fuel Surcharge') {
                            lineItem = jQuery('input[name="productName2"]');
                        } else if (key == 'Packing') {
                            lineItem = jQuery('input[name="productName3"]');
                        } else if (key == 'Unpacking') {
                            lineItem = jQuery('input[name="productName4"]');
                        } else if (key == 'Valuation') {
                            lineItem = jQuery('input[name="productName5"]');
                        } else if (key == 'Origin Accessorials') {
                            lineItem = jQuery('input[name="productName6"]');
                        } else if (key == 'Origin SIT') {
                            lineItem = jQuery('input[name="productName7"]');
                        } else if (key == 'Destination Accessorials') {
                            lineItem = jQuery('input[name="productName8"]');
                        } else if (key == 'Destination SIT') {
                            lineItem = jQuery('input[name="productName9"]');
                        } else if (key == 'Bulky Items') {
                            lineItem = jQuery('input[name="productName10"]');
                        } else if (key == 'Miscellaneous Services') {
                            lineItem = jQuery('input[name="productName11"]');
                        } else if (key == 'IRR') {
                            lineItem = jQuery('input[name="productName12"]');
                        }
                        lineItem.closest('tr').find('input[name*="listPrice"]').val(parseFloat(data.result.lineitems[key]).toFixed(2));
                        thisInstance.quantityChangeActions(lineItem.closest('tr'));
                    } else {
                        //Create new line item
                        var newRow = thisInstance.getBasicRow().addClass(thisInstance.rowClass);
                        jQuery('.lineItemPopup[data-module-name="Products"]', newRow).remove();
                        var sequenceNumber = thisInstance.getNextLineItemRowNumber();
                        newRow = newRow.appendTo(lineItemTable);
                        thisInstance.checkLineItemRow();
                        newRow.find('input.rowNumber').val(sequenceNumber);
                        thisInstance.updateLineItemsElementWithSequenceNumber(newRow, sequenceNumber);
                        newRow.find('input.productName').addClass('autoComplete');
                        thisInstance.registerLineItemAutoComplete(newRow);
                        //Populate line item
                        newRow.find('#productName' + sequenceNumber).val(key);
                        newRow.find('input[name*="listPrice"]').val(parseFloat(data.result.lineitems[key]).toFixed(2));
                        newRow.find('input[name*="hdnProductId"]').val(data.result.lineitemids[key]);
                        //the database expects a qty
                        if (data.result.lineitems[key] > 0) {
                            newRow.find('input[name*="qty"]').val(1);
                        }
                        thisInstance.quantityChangeActions(newRow);
                    }
                }
                //Remove loading icon and show updated field
                currentTd.progressIndicator({
                    'mode': 'hide'
                });
                jQuery('#interstateRateQuick').removeClass('hide');
                jQuery('.interstateRateDetail').removeClass('hide');
                jQuery('th:contains("Item Details")').closest('table').find('tbody').removeClass('hide');
                jQuery('th:contains("Item Details")').closest('table').progressIndicator({
                    'mode': 'hide'
                });
                jQuery('td:contains("Grand Total")').closest('table').removeClass('hide');
                Estimates_Edit_Js.I().lineItemsJs.hideZeroValServices();
            } else {
                bootbox.alert(data.error.code + ": " + data.error.message);
                currentTd.progressIndicator({
                    'mode': 'hide'
                });
                jQuery('#interstateRateQuick').removeClass('hide');
                jQuery('.interstateRateDetail').removeClass('hide');
                jQuery('th:contains("Item Details")').closest('table').find('tbody').removeClass('hide');
                jQuery('th:contains("Item Details")').closest('table').progressIndicator({
                    'mode': 'hide'
                });
                jQuery('td:contains("Grand Total")').closest('table').removeClass('hide');
            }
        }, function (error, err) {
            bootbox.alert(error + ": " + err);
            currentTd.progressIndicator({
                'mode': 'hide'
            });
            jQuery('#interstateRateQuick').removeClass('hide');
            jQuery('.interstateRateDetail').removeClass('hide');
            jQuery('th:contains("Item Details")').closest('table').find('tbody').removeClass('hide');
            jQuery('th:contains("Item Details")').closest('table').progressIndicator({
                'mode': 'hide'
            });
            jQuery('td:contains("Grand Total")').closest('table').removeClass('hide');
        });
    },
    getQuickRateEditQuery: function () {
        var fieldNames = ['weight', 'pickup_date', 'pickup_time', 'origin_zip', 'destination_zip', 'fuel_price', 'full_pack', 'full_unpack', 'bottom_line_discount', 'valuation_deductible', 'valuation_amount'];
        var weight = jQuery('input[name="' + fieldNames[0] + '"]').val();
        var pickupDate = jQuery('input[name="' + fieldNames[1] + '"]').val();
        var dateFormat = jQuery('input[name="' + fieldNames[1] + '"]').attr('data-date-format');
        if (dateFormat == "mm-dd-yyyy") {
            pickupDate = pickupDate.substring(6) + "-" + pickupDate.substring(0, 5);
        } else if (dateFormat == "dd-mm-yyyy") {
            pickupDate = pickupDate.substring(6) + "-" + pickupDate.substring(3, 5) + "-" + pickupDate.substring(0, 2);
        }
        var pickupTime = "12:00:00"; //jQuery('input[name="'+fieldNames[2]+'"]').val();
        var hour = parseInt(pickupTime.substring(0, 2));
        var minute = pickupTime.substring(2, 5);
        //if(pickupTime.substring(6) == 'PM' && hour < 12) {hour+=12;}
        //else if(pickupTime.substring(6) == 'AM' && hour == 12) {hour=0;}
        //pickupTime = ("00" + hour).slice(-2) + minute + ":00";
        var pickupDateTime = pickupDate + "T" + pickupTime;
        var originZip = jQuery('input[name="' + fieldNames[3] + '"]').val();
        var destinationZip = jQuery('input[name="' + fieldNames[4] + '"]').val();
        var fuelPrice = 0;
        var fullPackApplied = jQuery('input[name="' + fieldNames[6] + '"]').is(':checked');
        var fullUnpackApplied = jQuery('input[name="' + fieldNames[7] + '"]').is(':checked');
        var bottomLineDiscount = jQuery('input[name="' + fieldNames[8] + '"]').val();
        var valDeductible = jQuery('select[name="' + fieldNames[9] + '"]').siblings('.chzn-container').children('a').children('span').html();
        var valuationAmount = jQuery('input[name="' + fieldNames[10] + '"]').val();
        var selectElement = jQuery('select[name="effective_tariff"]');
        var selectId = selectElement.attr('id');
        var chosenOption = selectElement.siblings('.chzn-container').find('.result-selected').attr('id');
        var effective_tariff = selectElement.find('option:eq(' + chosenOption.split('_')[3] + ')').val();
        //Validation
        var errorExists = false;
        var errorNum = 1;
        var errorString = 'The following errors have prevented creation of the rate estimate:\n';
        if (weight <= 0 || weight.length == 0) {
            errorString += errorNum + ") Weight must be greater than 0.\n";
            errorExists = true;
            errorNum++;
        }
        if (pickupDate.length != 10) {
            errorString += errorNum + ") A valid pickup date must be set\n";
            errorExists = true;
            errorNum++;
        }
        if (isNaN(hour)) {
            errorString += errorNum + ") Pickup time must be set.\n";
            errorExists = true;
            errorNum++;
        }
        if (originZip.length < 5) {
            errorString += errorNum + ") Origin Zip must be valid.\n";
            errorExists = true;
            errorNum++;
        }
        if (destinationZip.length < 5) {
            errorString += errorNum + ") Destination Zip must be valid.\n";
            errorExists = true;
            errorNum++;
        }
        if (fuelPrice.length == 0 || fuelPrice < 0) {
            errorString += errorNum + ") Fuel Price must be set.\n";
            errorExists = true;
            errorNum++;
        }
        if (bottomLineDiscount.length == 0 || bottomLineDiscount < 0) {
            errorString += errorNum + ") Bottom Line Discount must be set and non-negative.\n";
            errorExists = true;
            errorNum++;
        }
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
        if (effective_tariff.length == 0) {
            errorString += errorNum + ") Effective Tariff must be set.\n";
            errorExists = true;
            errorNum++;
        }
        var valDeductibleValue;
        if (valDeductible === '60¢ / lb.') {
            valDeductibleValue = "SIXTY_CENTS";
        } else if (valDeductible === 'Zero') {
            valDeductibleValue = "ZERO";
        } else if (valDeductible === '$250') {
            valDeductibleValue = "TWO_FIFTY";
        } else {
            valDeductibleValue = "FIVE_HUNDRED";
        }
        var queryString = "&weight=" + weight + "&pickupDateTime=" + pickupDateTime + "&originZip=" + originZip + "&destinationZip=" + destinationZip + "&fuelPrice=" + fuelPrice + "&fullPackApplied=" + fullPackApplied + "&fullUnpackApplied=" + fullUnpackApplied + "&bottomLineDiscount=" + bottomLineDiscount + "&valDeductible=" + valDeductibleValue + "&valuationAmount=" + valuationAmount + "&effective_tariff=" + effective_tariff;
        if (errorExists) {
            return {
                success: false,
                errorString: errorString
            };
        }
        return {
            success: true,
            queryString: queryString
        };
    },
    saveFieldValues: function (fieldDetailList) {
        var aDeferred = jQuery.Deferred();
        var recordId = jQuery('#recordId').val();
        var data = {};
        if (typeof fieldDetailList != 'undefined') {
            data = fieldDetailList;
        }
        data['record'] = recordId;
        data['module'] = app.getModuleName();
        data['action'] = 'SaveAjax';
        AppConnector.request(data).then(function (reponseData) {
            aDeferred.resolve(reponseData);
        });
        return aDeferred.promise();
    },
    fieldUpdatedEvent: 'Vtiger.Field.Updated',
    detailedRateDetail: function () {
        //console.dir('Function to generate XML and retrieve rate');
        var thisInstance = this;
        var dataURL = 'index.php?module=Estimates&action=GetDetailedRate&record=' + getQueryVariable('record');
        jQuery('#interstateRateQuick').addClass('hide');
        jQuery('.interstateRateDetail').addClass('hide');
        jQuery('.interstateRateDetail').closest('td').progressIndicator();
        AppConnector.request(dataURL).then(function (data) {
            if (data.success) {
                jQuery('#contentHolder_DETAILED_LINE_ITEMS').replaceWith(data.result.lineitemsView);
                jQuery('#contentHolder_DETAILED_LINE_ITEMS').removeClass('hide')
                app.currentPageController.lineItemsJs.saveDetailLineItems();

                if (typeof data.result != 'undefined') {
                    thisInstance.detailViewUpdateValue(data, 'fieldValue_demand_color', 'pricingColor');
                    thisInstance.detailViewUpdateValue(data, 'fieldValue_pricing_level', 'pricingLevel');
                    thisInstance.detailViewUpdateValue(data, 'fieldValue_percent_smf', 'newPercentSMF');
                    thisInstance.detailViewUpdateValue(data, 'fieldValue_flat_smf', 'newFlatSMF');
                    thisInstance.detailViewUpdateValue(data, 'fieldValue_desired_total', 'desired_total');
                }
            } else {
                alert(data.error.code + ': ' + data.error.message);
            }
            jQuery('#interstateRateQuick').removeClass('hide');
            jQuery('.interstateRateDetail').closest('td').progressIndicator({
                'mode': 'hide'
            });
            jQuery('.interstateRateDetail').removeClass('hide');
        }, function (error) {
        });
    },

    detailViewUpdateValue : function(data, fieldName, valueIndex) {
        htmlValue = '';
        if (
            typeof data.result != 'undefined' &&
            typeof data.result[valueIndex] != 'undefined' &&
            data.result[valueIndex] != 'NO_LEVEL' &&
            data.result[valueIndex] != 'No_color'
        ) {
            htmlValue = data.result[valueIndex];
        }
        jQuery("[id$='"+fieldName+"']").html(htmlValue);
    },

    registerFullPackAppliedEvent: function () {
        //console.dir('registerFullPackAppliedEvent is getting fired');
        var selectNode = jQuery('input:checkbox[name="full_pack"]');
        selectNode.on('change', function () {
            if (jQuery(this).prop('checked')) {
                //unhide the override button
                jQuery('input[name="apply_full_pack_rate_override"]').closest('tr').removeClass('hide');
                jQuery('input[name="apply_full_pack_rate_override"]').closest('td').removeClass('hide').prev().removeClass('hide');
                if (jQuery('input:checkbox[name="apply_full_pack_rate_override"]').prop('checked')) {
                    //if it's checked load the rate and not a filler
                    jQuery('input[name="full_pack_rate_override"]').closest('td').removeClass('hide').prev().removeClass('hide');
                } else {
                    //if it's not checked show the filler
                    jQuery('td.fullPackOverrideFiller').each(function () {
                        jQuery(this).removeClass('hide');
                    });
                }
                jQuery('input:checkbox[name="apply_custom_pack_rate_override"]').prop('disabled', true);
            } else {
                //hide the override button  and the filler and the rate if they aren't already hidden
                jQuery('input:checkbox[name="apply_full_pack_rate_override"]').prop('checked', false).trigger('change');
                if (!jQuery('input[name="apply_full_pack_rate_override"]').closest('td').hasClass('hide')) {
                    jQuery('input[name="apply_full_pack_rate_override"]').closest('td').addClass('hide').prev().addClass('hide');
                }
                if (!jQuery('input[name="full_pack_rate_override"]').closest('td').hasClass('hide')) {
                    jQuery('input[name="full_pack_rate_override"]').closest('td').addClass('hide').prev().addClass('hide');
                }
                if (!jQuery('input[name="apply_full_pack_rate_override"]').closest('tr').hasClass('hide')) {
                    jQuery('input[name="apply_full_pack_rate_override"]').closest('tr').addClass('hide');
                }
                jQuery('td.fullPackOverrideFiller').each(function () {
                    if (!jQuery(this).hasClass('hide')) {
                        jQuery(this).addClass('hide');
                    }
                });
                jQuery('input:checkbox[name="apply_custom_pack_rate_override"]').prop('disabled', false);
            }
        });
    },
    registerFullPackOverrideAppliedEvent: function () {
        var thisInstance = this;
        var selectNode = jQuery('input:checkbox[name="apply_full_pack_rate_override"]');
        selectNode.on('change', function () {
            var node = jQuery(this);
            if (node.prop('checked')) {
                //do stuff if it's checked
                jQuery('td.fullPackOverrideFiller').each(function () {
                    if (!jQuery(this).hasClass('hide')) {
                        jQuery(this).addClass('hide');
                    }
                });
                jQuery('input[name="full_pack_rate_override"]').closest('td').removeClass('hide').prev().removeClass('hide');
            } else {
                //do stuff it it's unchcked
                if (!jQuery('input[name="full_pack_rate_override"]').closest('td').hasClass('hide')) {
                    jQuery('input[name="full_pack_rate_override"]').closest('td').addClass('hide').prev().addClass('hide');
                }
                if (jQuery('input:checkbox[name="full_pack"]').prop('checked')) {
                    jQuery('td.fullPackOverrideFiller').each(function () {
                        jQuery(this).removeClass('hide');
                    });
                }
            }
        });
    },
    unbreakTheCustomTables: function () {
        var content = jQuery('#inline_content');
        content.find('table.hide').removeClass('hide');
        content.find('table').each(function () {
            if (jQuery(this).hasClass('packing') ||
                jQuery(this).hasClass('crating')) {
                //packing
                //crating
                if (jQuery(this).find('tr:not(.hide)').length <= 3) {
                    jQuery(this).addClass('hide');
                    if (jQuery(this).next().is('br')) {
                        jQuery(this).next().remove();
                    }
                }
            } else if (jQuery(this).hasClass('otPacking') ||
                jQuery(this).hasClass('flatCharge') ||
                jQuery(this).hasClass('qtyRate') ||
                jQuery(this).hasClass('misc')) {
                //otPacking
                //flatCharge
                //qtyRate
                //misc (this is the vehicles block)
                if (jQuery(this).find('tr:not(.hide)').length <= 2) {
                    jQuery(this).addClass('hide');
                    if (jQuery(this).next().is('br')) {
                        jQuery(this).next().remove();
                    }
                }
            } else if (jQuery(this).hasClass('bulky')) {
                //bulky
                if (jQuery(this).find('tr:not(.hide)').length <= 1) {
                    jQuery(this).addClass('hide');
                    if (jQuery(this).next().is('br')) {
                        jQuery(this).next().remove();
                    }
                }
            }
        });

    },

    loadTPGPricelockBlocks: function (url) {
        var thisInstance = this;
        var record = jQuery('#recordId').val();
        thisInstance.type = 'detail';
        if (typeof record == 'undefined') {
            record = jQuery('input[name="record"]').val();
            thisInstance.type = 'edit';
        }

        url += '&record=' + record + '&type=' + thisInstance.type;
        var aDeferred = jQuery.Deferred();
        var inlineContent = jQuery('#inline_content');
        inlineContent.children().addClass('hide');
        AppConnector.request(url).then(
            function (responseData) {
                thisInstance.checkTruckLoad(thisInstance.tariffType);
                inlineContent.html(responseData);
                if (!inlineContent.next().is('br')) {
                    inlineContent.after('<br>');
                }
                if (thisInstance.type == 'detail') {
                    thisInstance.unbreakTheCustomTables();
                }
                inlineContent.find('.chzn-select').chosen();
                //re-register bindings as they will be lost otherwise
                thisInstance.registerCustomPackRateOverrideEvent();
                thisInstance.registerCustomCrateRateOverrideEvent();
                thisInstance.registerSITCustomRatesAppliedEvent();
                jQuery('input:checkbox[name="apply_custom_sit_rate_override"]').trigger('change');
                jQuery('input:checkbox[name="apply_custom_sit_rate_override_dest"]').trigger('change');
                thisInstance.registerToggleCustomRateEvent();
                thisInstance.setDayCertianFee();
                thisInstance.setPackingDiscount();
                //thisInstance.registerToggleAccessorialFields();
                thisInstance.registerLoadPackingButtonEvent();
                thisInstance.registerLoadCratingButtonEvent();
                thisInstance.registerLoadSITButtonEvent();
                if (
                    thisInstance.tariffType != 'NAVL-12A' &&
                    thisInstance.tariffType != 'ALLV-2A' &&
                    thisInstance.tariffType != '400N Base' &&
                    thisInstance.tariffType != '400N/104G' &&
                    thisInstance.tariffType != '400NG'
                ) {
                    if (thisInstance.type != 'detail') {
                        //I Moved the last two things from here,
                        // I'm not sure we even need this if or the else, just the NOT detail below.
                    }
                } else {
                    if (thisInstance.type != 'detail') {
                        thisInstance.registerAddVehicle();
                        thisInstance.registerDeleteVehicleEvent();
                        thisInstance.registerVehiclesEventsForLoaded();
                    }
                }
                if (thisInstance.type != 'detail') {
                    thisInstance.parent.registerDeleteMiscItemClickEvent();
                    thisInstance.parent.registerAddMiscItems();
                    thisInstance.parent.registerAddVehicleButtons();
                }
                if (
                    thisInstance.tariffType != 'ALLV-2A' &&
                    thisInstance.tariffType != 'NAVL-12A' &&
                    thisInstance.tariffType != '400N Base' &&
                    thisInstance.tariffType != '400N/104G' &&
                    thisInstance.tariffType != '400N/104G' &&
                    thisInstance.tariffType != 'Intra - 400N'
                ) {
                    jQuery('input:checkbox[name="consumption_fuel"]').addClass('hide');
                    jQuery('input:checkbox[name="consumption_fuel"]').prop('checked', false);
                    jQuery('input:checkbox[name="consumption_fuel"]').parent('td').prev('td').find('label').addClass('hide');
                }
                if ($('[name="effective_tariff"]').val() == 748 || $('[name="effective_tariff"]').val() == 739) {
                    $('.shuttleRow').first().children('td').each(function () {
                        if ($(this).text() == 'Destination') {
                            $(this).text('');
                        }
                    });
                    $.each($('.shuttleDest'), function () {
                        var inputFields = $(this).find('input');
                        if (inputFields.length > 0) {
                            $.each(inputFields, function () {
                                if ($(this).data('validation-engine') && !$(this).hasClass('hide')) {
                                    $(this).addClass('hide');
                                    $(this).attr('disabled', true);
                                    if ($(this).is(':checkbox')) {
                                        $(this).prop('checked', false);
                                    }
                                    else {
                                        $(this).val('');
                                    }
                                }
                            });
                        }
                        else {
                            if ($(this).find('label')) {
                                var labels = $(this).find('label');
                                labels.each(function () {
                                    if (!$(this).hasClass('hide')) {
                                        $(this).addClass('hide');
                                    }
                                });
                            }
                        }
                    });
                }

                if (thisInstance.tariffType == 'UAS' ||
                    thisInstance.tariffType == '400N/104G' ||
                    thisInstance.tariffType == 'Intra - 400N' ||
                    thisInstance.tariffType == '400N Base' ||
                    thisInstance.tariffType == 'ALLV-2A') {
                    thisInstance.disableContainerColumn();
                }

                app.registerEventForDatePickerFields();
                thisInstance.updateTabIndexValues();
                thisInstance.registerEventForLoadTariffButton();
                thisInstance.processForPricingTariffChange();
                thisInstance.checkAndMoveField();
                thisInstance.loadBlocksByBusinesLine();
            }
        );

    },
    disableContainerColumn: function () {
        jQuery('.contQtyField').prop('readonly', true);
        jQuery('.packQtyField').on('change', function () {
            var name = jQuery(this).attr('name');
            var regExp = /\d+/g;
            var rowNumbers = name.match(regExp);
            var packFieldId = rowNumbers[0];

            jQuery('input[name="pack_cont' + packFieldId + '"]').val(jQuery(this).val());
        });
    },
    checkTruckLoad: function (value) {
        if ($('#view').val() == 'Detail') {
            if ($('#Estimates_detailView_fieldValue_effective_tariff').find('input').val() == 'Blue Express') {
                $('.value_LBL_QUOTES_EXPRESSTRUCKLOAD').find('span').addClass('hide');
                $('.value_LBL_QUOTES_EXPRESSTRUCKLOAD').prev('td').find('label').addClass('hide');
            }
        }
        else {
            if (value != 'Blue Express') {
                jQuery('input:checkbox[name="express_truckload"]').addClass('hide');
                jQuery('input:checkbox[name="express_truckload"]').prop('checked', false);
                jQuery('input:checkbox[name="express_truckload"]').parent('td').prev('td').find('label').addClass('hide');
            } else {
                jQuery('input:checkbox[name="express_truckload"]').removeClass('hide');
                jQuery('input:checkbox[name="express_truckload"]').parent('td').prev('td').find('label').removeClass('hide');
            }
            if (value == 'UAS') {
                jQuery('[name="irr_charge"]').prop('readonly', true).val('4');
            }
        }
    },
    onLoadTruckLoad: function () {
        var thisInstance = this;
        var tariffID = $('[name="effective_tariff"]').val();

        thisInstance.checkTruckLoad($('#tariffType_' + tariffID).val());

    },
    setDayCertianFee: function () {
        var names = ['acc_day_certain_pickup'];
        var fieldName = [''];
        var otherFieldToggle = ['acc_day_certain_pickup'];
        var len = names.length;

        var toggleField = function (thisItem, fieldName, otherFieldToggle) {
            if (!jQuery('input:checkbox[name="' + otherFieldToggle + '"]').prop('checked')) {
                jQuery('[name="acc_day_certain_fee"]').attr('disabled', true).trigger('liszt:updated');
            }
            else {
                jQuery('[name="acc_day_certain_fee"]').attr('disabled', false).trigger('liszt:updated');
            }
        };
        for (var i = 0; i < len; i++) {
            jQuery('input:checkbox[name="' + names[i] + '"]').on('change', function () {
                var num = names.indexOf(jQuery(this).attr('name'));
                toggleField(jQuery(this), fieldName[num], otherFieldToggle[num]);
            }).trigger('change');
        }
    },
    setPackingDiscount: function () {
        var names = ['apply_custom_pack_rate_override'];
        var fieldName = [''];
        var otherFieldToggle = ['apply_custom_pack_rate_override'];
        var len = names.length;

        var toggleField = function (thisItem, fieldName, otherFieldToggle) {
            if (!jQuery('input:checkbox[name="' + otherFieldToggle + '"]').prop('checked')) {
                jQuery('[name="packing_disc"]').attr('disabled', true).trigger('liszt:updated');
            }
            else {
                jQuery('[name="packing_disc"]').attr('disabled', false).trigger('liszt:updated');
            }
        };
        for (var i = 0; i < len; i++) {
            jQuery('input:checkbox[name="' + names[i] + '"]').on('change', function () {
                var num = names.indexOf(jQuery(this).attr('name'));
                toggleField(jQuery(this), fieldName[num], otherFieldToggle[num]);
            }).trigger('change');
        }
    },
    registerToggleCustomRateEvent: function () {
        var thisInstance = this;
        var names = ['apply_sit_first_day_origin',
            'apply_sit_first_day_dest',
            'apply_sit_addl_day_origin',
            'apply_sit_addl_day_dest',
            'apply_sit_cartage_origin',
            'apply_sit_cartage_dest',
            'apply_exlabor_rate_origin',
            'apply_exlabor_rate_dest',
            'apply_exlabor_ot_rate_origin',
            'apply_exlabor_ot_rate_dest',
        ];
        var fieldName = ['sit_first_day_origin_override',
            'sit_first_day_dest_override',
            'sit_addl_day_origin_override',
            'sit_addl_day_dest_override',
            'sit_cartage_origin_override',
            'sit_cartage_dest_override',
            'exlabor_rate_origin',
            'exlabor_rate_dest',
            'exlabor_ot_rate_origin',
            'exlabor_ot_rate_dest',
        ];
        var otherFieldToggle = ['apply_sit_first_day_dest',
            'apply_sit_first_day_origin',
            'apply_sit_addl_day_dest',
            'apply_sit_addl_day_origin',
            'apply_sit_cartage_dest',
            'apply_sit_cartage_origin',
            'apply_exlabor_rate_dest',
            'apply_exlabor_rate_origin',
            'apply_exlabor_ot_rate_dest',
            'apply_exlabor_ot_rate_origin',
        ];
        var extraFieldName = ['exlabor_flat_origin',
            'exlabor_flat_dest',
            'exlabor_ot_flat_origin',
            'exlabor_ot_flat_dest',
        ];
        var len = names.length;
        var toggleField = function (thisItem, fieldName, otherFieldToggle) {
            var extraField = false;
            var index = names.indexOf(thisItem.attr('name'));
            if (index >= 6) {
                index -= 6;
                //console.dir('do more work');
                extraField = true;
                var row2 = jQuery('input[name="' + extraFieldName[index] + '"]').closest('tr');
            }
            var row = jQuery('input[name="' + fieldName + '"]').closest('tr');
            if (!jQuery('input:checkbox[name="' + otherFieldToggle + '"]').prop('checked')) {
                if (!thisItem.prop('checked')) {
                    if (!row.hasClass('hide')) {
                        row.addClass('hide');
                        row.children().children(':not(.edit)').each(function () {
                            if (!jQuery(this).hasClass('hide')) {
                                jQuery(this).addClass('hide');
                            }
                        });
                        row.find('input:not(.fieldname)').val(0);
                    }
                    if (extraField) {
                        if (!row2.hasClass('hide')) {
                            row2.addClass('hide');
                            row2.children().children(':not(.edit)').each(function () {
                                if (!jQuery(this).hasClass('hide')) {
                                    jQuery(this).addClass('hide');
                                }
                            });
                            row2.find('input:not(.fieldname)').val(0);
                        }
                    }
                } else {
                    var valueTD = jQuery('input[name="' + fieldName + '"]').closest('td');
                    var labelTD = valueTD.prev();
                    if (extraField) {
                        var extraValueTD = jQuery('input[name="' + extraFieldName[index] + '"]').closest('td');
                        var extraLabelTD = extraValueTD.prev();
                        extraValueTD.children(':not(.edit)').each(function () {
                            if (jQuery(this).hasClass('hide')) {
                                jQuery(this).removeClass('hide');
                            }
                        });
                        extraLabelTD.children().each(function () {
                            if (jQuery(this).hasClass('hide')) {
                                jQuery(this).removeClass('hide');
                            }
                        });
                        if (row2.hasClass('hide')) {
                            row2.removeClass('hide');
                        }
                    }
                    valueTD.children(':not(.edit)').each(function () {
                        if (jQuery(this).hasClass('hide')) {
                            jQuery(this).removeClass('hide');
                        }
                    });
                    labelTD.children().each(function () {
                        if (jQuery(this).hasClass('hide')) {
                            jQuery(this).removeClass('hide');
                        }
                    });
                    if (row.hasClass('hide')) {
                        row.removeClass('hide');
                    }
                }
            } else {
                if (!thisItem.prop('checked')) {
                    var valueTD = jQuery('input[name="' + fieldName + '"]').closest('td');
                    var labelTD = valueTD.prev();
                    if (extraField) {
                        var extraValueTD = jQuery('input[name="' + extraFieldName[index] + '"]').closest('td');
                        var extraLabelTD = extraValueTD.prev();
                        extraValueTD.children(':not(.edit)').each(function () {
                            if (!jQuery(this).hasClass('hide')) {
                                jQuery(this).addClass('hide');
                            }
                        });
                        extraValueTD.find('input:not(.fieldname)').val(0);
                        extraLabelTD.children().each(function () {
                            if (!jQuery(this).hasClass('hide')) {
                                jQuery(this).addClass('hide');
                            }
                        });
                    }
                    valueTD.children(':not(.edit)').each(function () {
                        if (!jQuery(this).hasClass('hide')) {
                            jQuery(this).addClass('hide');
                        }
                    });
                    valueTD.find('input:not(.fieldname)').val(0);
                    labelTD.children().each(function () {
                        if (!jQuery(this).hasClass('hide')) {
                            jQuery(this).addClass('hide');
                        }
                    });
                } else {
                    if (extraField) {
                        row2.children().children(':not(.edit)').each(function () {
                            if (jQuery(this).hasClass('hide')) {
                                jQuery(this).removeClass('hide');
                            }
                        });
                        if (row2.hasClass('hide')) {
                            row2.removeClass('hide');
                        }
                    }
                    row.children().children(':not(.edit)').each(function () {
                        if (jQuery(this).hasClass('hide')) {
                            jQuery(this).removeClass('hide');
                        }
                    });
                    if (row.hasClass('hide')) {
                        row.removeClass('hide');
                    }
                }
            }
        };
        for (var i = 0; i < len; i++) {
            jQuery('input:checkbox[name="' + names[i] + '"]').on('change', function () {
                var num = names.indexOf(jQuery(this).attr('name'));
                toggleField(jQuery(this), fieldName[num], otherFieldToggle[num]);
            }).trigger('change');
        }
    },

    registerCustomPackRateOverrideEvent: function () {
        var thisInstance = this;
        jQuery('input:checkbox[name="apply_custom_pack_rate_override"]').on('change', function () {
            if (jQuery(this).prop('checked')) {
                jQuery('td.packingCustomRate, td.packingPackRate').each(function () {
                    if (jQuery(this).hasClass('hide')) {
                        jQuery(this).removeClass('hide');
                    }
                });
                // jQuery('td.ContCol').each(function() {
                //     jQuery(this).width(width + '%');
                // });
                // jQuery('td.PkCol').each(function () {
                //     jQuery(this).width(width + '%');
                // });
                // jQuery('td.UnpkCol').each(function () {
                //     jQuery(this).width(width + '%');
                // });
                jQuery('input[name="apply_custom_pack_rate_override"]')/*.closest('td').attr('colspan', '4')*/;
                jQuery('button[name="LoadTariffPacking"]').removeClass('hide')/*.closest('td').attr('colspan', '4')*/;
                jQuery('input:checkbox[name="full_pack"]').prop('disabled', true);

            } else {
                jQuery('td.packingCustomRate, td.packingPackRate').each(function () {
                    if (!jQuery(this).hasClass('hide')) {
                        jQuery(this).addClass('hide');
                    }
                });
                // jQuery('td.ContCol').each(function() {
                //     jQuery(this).width('11%');
                // });
                // jQuery('td.PkCol').each(function () {
                //     jQuery(this).width('12%');
                // });
                // jQuery('td.UnpkCol').each(function () {
                //     jQuery(this).width('12%');
                // });
                jQuery('input[name="apply_custom_pack_rate_override"]')/*.closest('td').attr('colspan', '3')*/;
                jQuery('button[name="LoadTariffPacking"]').addClass('hide')/*.closest('td').attr('colspan', '3')*/;
                jQuery('input:checkbox[name="full_pack"]').prop('disabled', false);
            }

            var tblPacking = jQuery('table.packing');
            var packingRows = tblPacking.find('tr[data-pack_item_id]');
            var packingRow = null;
            var packingCells = null;
            var width = 0;

            for (var row = 0; row < packingRows.length; row++) {
                packingRow = $(packingRows[row]);
                packingCells = packingRow.find('td:not(.hide)');
                width = (100 / packingCells.length) + "%";
                packingCells.each(function () {
                    $(this).css({
                        width: width
                    });
                });
            }

            if (thisInstance.detailView) {
                //console.dir('save now I think?');
                //console.dir(jQuery('input:checkbox[name="apply_custom_pack_rate_override"]'));
                thisInstance.parent.ajaxEditHandling(jQuery('input:checkbox[name="apply_custom_pack_rate_override"]').closest('td'));
            }
        }).trigger('change');
    },

    registerCustomCrateRateOverrideEvent: function () {
        var thisInstance = this;
        jQuery('input:checkbox[name="apply_custom_pack_rate_override"]').on('change', function () {
            if (jQuery(this).prop('checked')) {
                jQuery('td.cratingCustomRate').each(function () {
                    if (jQuery(this).hasClass('hide')) {
                        jQuery(this).removeClass('hide');
                    }
                });
                jQuery('button[name="LoadTariffCrating"]').removeClass('hide')/*.closest('td').attr('colspan', '4')*/;
                jQuery('[name="tpg_custom_crate_rate"]').removeClass('hide');
            } else {
                jQuery('td.cratingCustomRate').each(function () {
                    if (!jQuery(this).hasClass('hide')) {
                        jQuery(this).addClass('hide');
                    }
                });
                jQuery('button[name="LoadTariffCrating"]').addClass('hide')/*.closest('td').attr('colspan', '3')*/;
                jQuery('[name="tpg_custom_crate_rate"]').addClass('hide').val('0.00');
            }

            if (thisInstance.detailView) {
                //console.dir('save now I think?');
                //console.dir(jQuery('input:checkbox[name="apply_custom_pack_rate_override"]'));
                thisInstance.parent.ajaxEditHandling(jQuery('input:checkbox[name="apply_custom_pack_rate_override"]').closest('td'));
            }
        }).trigger('change');
    },

    registerSITCustomRatesAppliedEvent: function () {
        var thisInstance = this;
        var selectNode = jQuery('.sit_override');
        selectNode.on('change', function () {
            var location = $(this).data('location');
            if (location == '') {
                return;
            }
            var names = [
                'apply_sit_first_day_' + location,
                //'apply_sit_first_day_dest',
                'sit_first_day_' + location + '_override',
                //'sit_first_day_dest_override',
                'apply_sit_addl_day_' + location,
                //'apply_sit_addl_day_dest',
                'sit_addl_day_' + location + '_override',
                //'sit_addl_day_dest_override',
                'apply_sit_cartage_' + location,
                //'apply_sit_cartage_dest',
                'sit_cartage_' + location + '_override',
                //'sit_cartage_dest_override'
            ];
            var len = names.length;
            var tariffTypesAvailabel = ['TPG', 'TPG GRR', 'Allied Express', 'Pricelock', 'Blue Express', 'Pricelock GRR'];
            var tariffId = jQuery('[name="effective_tariff"]').val();
            var tariffType = jQuery('#tariffType_' + tariffId).val();
            var isShowButton = false;
            if (tariffTypesAvailabel.indexOf(tariffType) > -1) isShowButton = true;

            if (jQuery(this).prop('checked')) {
                //console.dir('checked');
                for (var i = 0; i < len; i++) {
                    var input = jQuery('input[name="' + names[i] + '"]');
                    var label = input.closest('td').prev('td').children('label');


                    if (input.hasClass('hide')) {
                        input.removeClass('hide');
                    }
                    if (label.hasClass('hide')) {
                        label.removeClass('hide');
                    }
                    //console.dir(names[i]+' : '+input.prop('type'));
                    if (input.prop('type') == "hidden") {
                        input = jQuery('input:checkbox[name="' + names[i] + '"]');
                        input.prop('checked', true).trigger('change');
                        if (thisInstance.detailView) {
                            thisInstance.parent.saveItem(input);
                            //thisInstance.triggerDetailViewSave(valueTD);
                        }
                    }
                }
                if (location == 'origin') {
                    if ($('[name="LoadOriginTariffSIT"]').hasClass('hide')) {
                        $('[name="LoadOriginTariffSIT"]').removeClass('hide');
                    }
                    if (isShowButton) {
                        jQuery('a[data-location="origin"]').removeClass('hide');
                    }
                    jQuery('a[data-location="origin"]').removeClass('hide');
                } else {
                    if ($('[name="LoadDestinationTariffSIT"]').hasClass('hide')) {
                        $('[name="LoadDestinationTariffSIT"]').removeClass('hide');

                    }
                    jQuery('a[data-location="dest"]').removeClass('hide');
                }

            }
            else {
                for (var i = 0; i < len; i++) {
                    var input = jQuery('input[name="' + names[i] + '"]');
                    var label = input.closest('td').prev('td').children('label');
                    if (!input.hasClass('hide')) {
                        input.addClass('hide');
                    }
                    if (!label.hasClass('hide')) {
                        label.addClass('hide');
                    }

                    if (input.prop('type') == "hidden") {
                        input = jQuery('input:checkbox[name="' + names[i] + '"]');
                        input.prop('checked', false).trigger('change');
                    }
                    if (thisInstance.detailView) {
                        thisInstance.parent.saveItem(input);
                    }
                }
                if (location == 'origin') {
                    if (!$('[name="LoadOriginTariffSIT"]').hasClass('hide')) {
                        $('[name="LoadOriginTariffSIT"]').addClass('hide');
                    }
                    jQuery('a[data-location="origin"]').addClass('hide');
                } else {
                    if (!$('[name="LoadDestinationTariffSIT"]').hasClass('hide')) {
                        $('[name="LoadDestinationTariffSIT"]').addClass('hide');

                    }
                    jQuery('a[data-location="dest"]').addClass('hide');
                }
            }
        });
    },
    registerToggleAccessorialFields: function () {
        var thisInstance = this;
        var names = ['acc_shuttle_origin_applied',
            'acc_shuttle_dest_applied',
            'acc_shuttle_origin_over25',
            'acc_shuttle_dest_over25',
            'acc_selfstg_origin_applied',
            'acc_selfstg_dest_applied',
        ];
        var len = names.length;
        for (var i = 0; i < len; i++) {
            jQuery('input:checkbox[name="' + names[i] + '"]').on('change', function () {
                thisInstance.parent.toggleAccessorialFields(jQuery(this).attr('name'), jQuery(this).prop('checked') ? 'on' : 'off');
            }).trigger('change');
        }
    },

    registerLoadPackingButtonEvent: function () {
        var thisInstance = this;
        jQuery('button[name="LoadTariffPacking"]').on('click', function () {
            if (jQuery('input[name="interstate_effective_date"]').val() === '') {
                bootbox.alert('Effective Date must be set to Load Tariff Packing');
                return;
            }
            var currentTdElement = jQuery('select[name="assigned_user_id"]').closest('td');
            var selected = currentTdElement.find('.result-selected').html();
            var optionId = currentTdElement.find('.result-selected').attr('id').split('_')[3];
            optionId--; //its off by one from normal because of the groups header
            var selectedId = currentTdElement.find('option:eq(' + optionId + ')').val();
            var assigned_to = selectedId;
            var dataUrl = "index.php?module=" + Estimates_Edit_Js.I().moduleName + "&action=UpdateLocalTariffs&assigned_to=" + assigned_to;
            AppConnector.request(dataUrl).then(
                //function (data) {
                //if (data.success) {
                //    console.dir('success!');
                //    var recordId = getQueryVariable('record');onsole.dir(data.result.userAgents[0]);
                //    var updateUrl = 'index.php?module=Estimates&view=Edit&record=' + recordId + '&mode=updateLocalTariff&userAgents=' + data.result.userAgents + '&edit=true';
                //    AppConnector.request(updateUrl).then(
                function (data) {
                    if (data.success) {
                        var message = '<table class="massEditTable table table-bordered"><tbody><tr><td class="fieldLabel" style="width:40%"><label class="muted pull-right">Local Tariffs</label></td><td class="fieldValue">';
                        message += data.result;
                        message += '</td></tr></tbody></table>';
                        //console.dir(message);
                        bootbox.dialog({
                            className: 'loadTariffPackingContent',
                            title: 'Load Tariff Packing',
                            message: message,
                            onEscape: function () {

                            },
                            buttons: {
                                success: {
                                    label: "Load",
                                    className: "btn-success",
                                    callback: function () {
                                        var currentTdElement = jQuery('div.loadTariffPackingContent').find('select[name="local_tariff"]').closest('td');
                                        var selected = currentTdElement.find('.result-selected').html();
                                        var optionId = currentTdElement.find('.result-selected').attr('id').split('_')[3];
                                        var selectedId = currentTdElement.find('option:eq(' + optionId + ')').val();
                                        var effectiveDateNode = jQuery('input[name="interstate_effective_date"]');
                                        var effectiveDateUserFormat = effectiveDateNode.val();
                                        var dateFormat = effectiveDateNode.data('dateFormat');
                                        var y = '';
                                        var m = '';
                                        var d = '';
                                        for (var i = 0; i < 10; i++) {
                                            if (dateFormat[i] == 'y') {
                                                y += effectiveDateUserFormat[i];
                                            } else if (dateFormat[i] == 'm') {
                                                m += effectiveDateUserFormat[i];
                                            } else if (dateFormat[i] == 'd') {
                                                d += effectiveDateUserFormat[i];
                                            }
                                        }
                                        var effectiveDate = y + '-' + m + '-' + d;
                                        var loadTariffPackingUrl = "index.php?module=" + Estimates_TPGTariff_Js.I().moduleName + "&action=LoadTariffPacking&tariffId=" + selectedId + "&effectiveDate=" + effectiveDate;
                                        //console.dir(loadTariffPackingUrl);
                                        AppConnector.request(loadTariffPackingUrl).then(
                                            function (data) {
                                                //console.dir('trying to load the tariff packing items');
                                                for (var key in data.result.packingItems) {
                                                    var node = jQuery('input[name="packCustomRate' + key + '"]');
                                                    node.val(data.result.packingItems[key]);
                                                    if (thisInstance.detailView) {
                                                        //thisInstance.parent.saveItem(node);
                                                        //thisInstance.parent.ajaxEditHandling(node.closest('td'));
                                                        node.closest('td').progressIndicator();
                                                        jQuery.when(node.trigger('click', thisInstance.parent.ajaxEditHandling.saveHandler)).done(function () {
                                                            node.closest('td').progressIndicator({'mode': 'hide'});
                                                            if (!jQuery(this).closest('td').find('.edit').hasClass('hide')) {
                                                                jQuery(this).closest('td').find('.edit').addClass('hide');
                                                                jQuery(this).closest('td').find('.value').removeClass('hide')
                                                            }
                                                        });
                                                    }
                                                }
                                            }
                                        );

                                    }
                                }
                            }
                        });
                        var node = jQuery('div.loadTariffPackingContent');
                        node.css('overflow', 'visible');
                        node.find('select').chosen();
                    }
                },
                function () {
                    //console.dir('in here I guess?');
                    //console.dir(data);
                    //console.dir(error);
                });
            //}
            //},
            //function (error) {
            //    console.dir('Error: '+error);
            //});

            // Select Tariff services
            var agentid = jQuery('[name="agentid"]').val();
            if (agentid == undefined || agentid == '') {
                return;
            }
            var effectivedate = jQuery('[name="interstate_effective_date"]').val();
            if (effectivedate == undefined || effectivedate == '') {
                return;
            }
            var location = jQuery(this).data('location');
            var params = {
                module: 'Estimates',
                view: 'CustomTariff',
                mode: 'getTariffParkingServices',
                agent_id: agentid,
                effective_date: effectivedate
            };
            var progressIndicatorElement = jQuery.progressIndicator();
            AppConnector.request(params).then(
                function (data) {
                    progressIndicatorElement.progressIndicator({'mode': 'hide'});
                    app.showModalWindow({'data': data, 'css': {'min-width': '30%'}});
                    thisInstance.registerEventForSelectTariffPackingServices(location);
                }
            );
        });
    },

    registerLoadCratingButtonEvent: function () {
        var thisInstance = this;
        jQuery('button[name="LoadTariffCrating"]').on('click', function () {
            if (jQuery('input[name="interstate_effective_date"]').val() === '') {
                bootbox.alert('Effective Date must be set to Load Tariff Packing');
                return;
            }
            var currentTdElement = jQuery('select[name="assigned_user_id"]').closest('td');
            var selected = currentTdElement.find('.result-selected').html();
            var optionId = currentTdElement.find('.result-selected').attr('id').split('_')[3];
            optionId--; //its off by one from normal because of the groups header
            var selectedId = currentTdElement.find('option:eq(' + optionId + ')').val();
            var assigned_to = selectedId;

            // Select Tariff services
            var agentid = jQuery('[name="agentid"]').val();
            if (agentid == undefined || agentid == '') {
                return;
            }
            var location = jQuery(this).data('location');
            var params = {
                module: 'Estimates',
                view: 'CustomTariff',
                mode: 'getTariffCratingServices',
                agent_id: agentid
            };
            var progressIndicatorElement = jQuery.progressIndicator();
            AppConnector.request(params).then(
                function (data) {
                    progressIndicatorElement.progressIndicator({'mode': 'hide'});
                    app.showModalWindow({'data': data, 'css': {'min-width': '30%'}});
                    thisInstance.registerEventForSelectTariffCratingServices(location);
                }
            );
        });
    },

    registerLoadSITButtonEvent: function () {
        var thisInstance = this;
        jQuery('.loadTariffSit').on('click', function () {
            if (jQuery('input[name="interstate_effective_date"]').val() === '') {
                bootbox.alert('Effective Date must be set to Load Tariff Packing');
                return;
            }
            var location = $(this).data('location');
            if (location == '') {
                bootbox.alert('Could not determine the location!');
                return;
            }
            var currentTdElement = jQuery('select[name="assigned_user_id"]').closest('td');
            var selected = currentTdElement.find('.result-selected').html();
            var optionId = currentTdElement.find('.result-selected').attr('id').split('_')[3];
            optionId--; //its off by one from normal because of the groups header
            var selectedId = currentTdElement.find('option:eq(' + optionId + ')').val();
            var assigned_to = selectedId;
            var dataUrl = "index.php?module=" + Estimates_Edit_Js.I().moduleName + "&action=GetSITTariffs&assigned_to=" + assigned_to;
            AppConnector.request(dataUrl).then(
                function (data) {
                    if (data.success) {
                        // console.log(data);
                        var message = '<table class="massEditTable table table-bordered">' +
                            '<tbody>' +
                            '<tr>' +
                            '<td class="fieldLabel" style="width:40%">' +
                            '<label class="muted pull-right">Local Tariffs</label>' +
                            '</td>' +
                            '<td class="fieldValue">';
                        message += data.result;
                        message += '</td></tr></tbody></table>';

                        bootbox.dialog({
                            className: 'loadTariffSITContent',
                            title: 'Load Tariff SIT',
                            message: message,
                            onEscape: function () {

                            },
                            buttons: {
                                success: {
                                    label: "Load",
                                    className: "btn-success",
                                    callback: function () {

                                        var currentTdElement = jQuery('div.loadTariffSITContent').find('select[name="local_tariff"]').closest('td');
                                        var selected = currentTdElement.find('.result-selected').html();
                                        var optionId = currentTdElement.find('.result-selected').attr('id').split('_')[3];
                                        var selectedId = currentTdElement.find('option:eq(' + optionId + ')').val();
                                        var effectiveDateNode = jQuery('input[name="interstate_effective_date"]');
                                        var effectiveDateUserFormat = effectiveDateNode.val();
                                        var dateFormat = effectiveDateNode.data('dateFormat');
                                        var y = '';
                                        var m = '';
                                        var d = '';
                                        for (var i = 0; i < 10; i++) {
                                            if (dateFormat[i] == 'y') {
                                                y += effectiveDateUserFormat[i];
                                            } else if (dateFormat[i] == 'm') {
                                                m += effectiveDateUserFormat[i];
                                            } else if (dateFormat[i] == 'd') {
                                                d += effectiveDateUserFormat[i];
                                            }
                                        }
                                        var effectiveDate = y + '-' + m + '-' + d;
                                        var loadTariffPackingUrl = "index.php?module=" + Estimates_TPGTariff_Js.I().moduleName + "&action=LoadTariffSIT&tariffId=" + selectedId + "&effectiveDate=" + effectiveDate;
                                        AppConnector.request(loadTariffPackingUrl).then(
                                            function (data) {
                                                for (var key in data.result.sitItems) {
                                                    var node = jQuery('input[name="' + key + '_' + location + '_override"]');
                                                    node.val(data.result.sitItems[key]);
                                                }
                                            }
                                        );

                                    }
                                }
                            }
                        });
                        var node = jQuery('div.loadTariffSITContent');
                        node.css('overflow', 'visible');
                        node.find('select').chosen();

                    }
                },
                function () {

                });
        });
    },
    reportButtonEdit: function () {
        //console.dir('TPGregisterReportsButtonEdit/Pricelock reports button');
        var thisInstance = this;
        if($('[name="move_type"]').find(':selected').val() != 'Interstate') {
            return;
        }

            if (!thisInstance.detailView) {
                if (!thisInstance.preDetailedRateEdit()) {
                    return;
                }
            }

        if (jQuery('#getReportSelectButton').closest('tr').prev().children().next().children().html().trim() == '0' || jQuery('#getReportSelectButton').closest('tr').prev().children().next().children().html().trim() == '0.00') {
                thisInstance.showAlertBox({'message': 'You have to rate the estimate before trying to report.'});
                return false;
            }


            var assigned_user_id = jQuery('select[name="assigned_user_id"]').find('option:selected').val();
            //console.dir('sending request to GetReportTPGPricelock');
        jQuery('#getReportSelectButton').closest('td').progressIndicator();
        jQuery('#getReportSelectButton').addClass('hide');
            var dataURL = 'index.php?module=Estimates&action=GetReportTPGPricelock&requestType=GetAvailableReports&type=editview&local=' + (jQuery("[name='business_line_est']").val() == 'Local Move') + '&effectiveTariff=' + jQuery("[name='effective_tariff']").val();
            if (jQuery("[name='business_line_est']").val() == 'Local Move') {
                dataURL = 'index.php?module=Estimates&action=GetReportLocal&requestType=GetAvailableReports&type=editview&local=' + (jQuery("[name='business_line_est']").val() == 'Local Move') + '&effectivetariff=' + jQuery("[name='local_tariff']").val() + '&effectivedateid=' + jQuery("[name='EffectiveDateId']").val();
            }
            if (jQuery("[name='business_line_est']").val() == 'Intrastate Move') {
                dataURL = 'index.php?module=Estimates&action=GetReportTPGPricelock&requestType=GetAvailableReports&type=editview&local=' + (jQuery("[name='business_line_est']").val() == 'Local Move') + '&effectiveTariff=' + jQuery("[name='local_tariff']").val();
            }
            thisInstance.toggleDisableHiddenFields();
            var formData = jQuery.param(jQuery('#EditView').serializeFormData());
            thisInstance.toggleDisableHiddenFields();
            var index = formData.indexOf('&record=');
            var urlAppend = formData.substring(index, formData.length - 1);
            dataURL = dataURL + urlAppend;
            AppConnector.request(dataURL).then(
                function (data) {
                    if (data.success) {
                    if(jQuery('#reportContent').length == 0)
                    {
                        jQuery('.contentsDiv').append('<div id="reportContent"></div>');
                    }
                        jQuery('#reportContent').html(data.result);
                        if (!jQuery.isFunction(jQuery.fn.colorbox)) {
                            jQuery.getScript("libraries/jquery/colorbox/jquery.colorbox-min.js").then(function () {
                                jQuery.colorbox({
                                    inline: true,
                                    width: '300px',
                                    height: '40%',
                                    left: '25%',
                                    top: '25%',
                                    href: '#reportContent',
                                    onClosed: function () {
                                        jQuery(document.body).css({overflow: 'auto'});
                                        jQuery('#reportContent').html('');
                                    },
                                    onComplete: function () {
                                        jQuery(document.body).css({overflow: 'hidden'});
                                    }
                                });
                            });
                        }
                        else {
                            jQuery.colorbox({
                                inline: true,
                                width: '300px',
                                height: '40%',
                                left: '25%',
                                top: '25%',
                                href: '#reportContent',
                                onClosed: function () {
                                    jQuery(document.body).css({overflow: 'auto'});
                                    jQuery('#reportContent').html('');
                                },
                                onComplete: function () {
                                    jQuery(document.body).css({overflow: 'hidden'});
                                }
                            });
                        }
                        jQuery('#reportContent').find('button').each(function () {
                            //console.dir(jQuery(this));
                            jQuery(this).on('click', function () {
                                jQuery('#reportContent').find('.contents').addClass('hide');
                                jQuery('#reportContent').progressIndicator();
                                var reportURL = 'index.php?module=Estimates&action=GetReportTPGPricelock&reportId=' + jQuery(this).attr('name') + '&reportName=' + encodeURIComponent(jQuery(this).html());
                                thisInstance.toggleDisableHiddenFields();
                                var formData = jQuery.param(jQuery('#EditView').serializeFormData());
                                var includeDOV = jQuery('[name="includeDOV"]').attr('checked') == 'checked' ? true : false;
                                thisInstance.toggleDisableHiddenFields();
                                var index = formData.indexOf('&record=');
                                var urlAppend = formData.substring(index, formData.length - 1);
                                reportURL = reportURL + urlAppend;
                                reportURL = reportURL + '&interstate_mileage=' + jQuery('input[name="interstate_mileage"]').val();
                                reportURL = reportURL + '&effective_tariff=' + jQuery('select[name="effective_tariff"]').val();
                                reportURL = reportURL + '&wsdlURL=' + jQuery('input[name="wsdlURL"]').val();
                                reportURL = reportURL + '&validtill=' + jQuery('input[name="validtill"]').val();
                                reportURL = reportURL + '&type=editview&assigned_user_id=' + assigned_user_id;
                                reportURL = reportURL + '&includeDOV=' + includeDOV;
                                // console.dir(reportURL);
                                AppConnector.request(reportURL).then(
                                    function (data) {
                                        if (data.success) {
                                            jQuery('#EditView').append('<input type="hidden" name="gotoDocuments" value="' + data.result + '">');
                                            jQuery('#EditView').append('<input type="hidden" name="reportSave" value="1">');
                                            jQuery('#EditView').submit();
                                        }
                                    },
                                    function (error) {
                                    }
                                );
                            });
                        });
                    }
                jQuery('#getReportSelectButton').closest('td').progressIndicator({'mode': 'hide'});
                jQuery('#getReportSelectButton').removeClass('hide');
                },
                function (error) {
                }
            );
    },
    reportButtonDetail: function () {
        var thisInstance = this;
        //console.dir('in TPG/Tariff registerReportsButtonDetail');
            if (jQuery('#getReportSelectButton').closest('tr').prev().children().next().children().html().trim() == '0') {
                thisInstance.showAlertBox({'message': 'You have to rate the estimate before trying to report.'});
                return false;
            }

            //console.dir('firing registerReportsButton in TPG/Pricelock for detail');
            jQuery('#getReportSelectButton').closest('td').progressIndicator();
            jQuery('#getReportSelectButton').addClass('hide');
        var dataURL = 'index.php?module=Estimates&action=GetReportTPGPricelock&record=' + getQueryVariable('record') + '&requestType=GetAvailableReports&local=' + (jQuery("[name='business_line_est']").val() == 'Local Move') + '&effective_tariff=' + jQuery('select[name="effective_tariff"]').val() + '&effective_date=' + jQuery("#" + Estimates_TPGTariff_Js.I().moduleName + "_detailView_fieldValue_effective_date").find('input').val();
            if (jQuery("[name='business_line_est']").val() == 'Local Move') {
            var dataURL = 'index.php?module=Estimates&action=GetReportLocal&record=' + getQueryVariable('record') + '&requestType=GetAvailableReports&local=' + (jQuery("[name='business_line_est']").val() == 'Local Move') + '&effectivetariff=' + jQuery("[name='local_tariff']").val() + '&effective_date=' + jQuery("#" + Estimates_TPGTariff_Js.I().moduleName + "_detailView_fieldValue_effective_date").find('input').val() + '&effectivedateid=' + jQuery("[name='EffectiveDateId']").val();
            }
            AppConnector.request(dataURL).then(
                function (data) {
                    if (data.success) {
                    if(jQuery('#reportContent').length == 0)
                    {
                        jQuery('.contentsDiv').append('<div id="reportContent"></div>');
                    }
                        jQuery('#reportContent').html(data.result);
                        if (!jQuery.isFunction(jQuery.fn.colorbox)) {
                            jQuery.getScript("libraries/jquery/colorbox/jquery.colorbox-min.js").then(function () {
                                jQuery.colorbox({
                                    inline: true,
                                    width: '300px',
                                    height: '40%',
                                    left: '25%',
                                    top: '25%',
                                    href: '#reportContent',
                                    onClosed: function () {
                                        jQuery(document.body).css({overflow: 'auto'});
                                        jQuery('#reportContent').html('');
                                    },
                                    onComplete: function () {
                                        jQuery(document.body).css({overflow: 'hidden'});
                                    }
                                });
                            });
                        }
                        else {
                            jQuery.colorbox({
                                inline: true,
                                width: '300px',
                                height: '40%',
                                left: '25%',
                                top: '25%',
                                href: '#reportContent',
                                onClosed: function () {
                                    jQuery(document.body).css({overflow: 'auto'});
                                    jQuery('#reportContent').html('');
                                },
                                onComplete: function () {
                                    jQuery(document.body).css({overflow: 'hidden'});
                                }
                            });
                        }

                        jQuery('#reportContent').find('button').each(function () {
                            jQuery(this).on('click', function () {
                                jQuery('#reportContent').find('.contents').addClass('hide');
                                jQuery('#reportContent').progressIndicator();
                            var reportURL = 'index.php?module=Estimates&action=GetReportTPGPricelock&record=' + getQueryVariable('record') + '&reportId=' + jQuery(this).attr('name') + '&reportName=' + encodeURIComponent(jQuery(this).html());
                                reportURL = reportURL + '&wsdlURL=' + jQuery('input[name="wsdlURL"]').val();
                                reportURL = reportURL + '&effective_tariff=' + jQuery('select[name="effective_tariff"]').val();
                                var includeDOV = jQuery('[name="includeDOV"]').attr('checked') == 'checked' ? true : false;
                                reportURL = reportURL + '&includeDOV=' + includeDOV;
                                //console.dir(reportURL);
                                if (jQuery("[name='business_line_est']").val() == 'Local Move') {
                                var reportURL = 'index.php?module=Estimates&action=GetReportLocal&record=' + getQueryVariable('record') + '&requestType=GetReport&local=' + (jQuery("[name='business_line_est']").val() == 'Local Move') + '&effectivetariff=' + jQuery("[name='local_tariff']").val() + '&effective_date=' + jQuery("#" + Estimates_TPGTariff_Js.I().moduleName + "_detailView_fieldValue_effective_date").find('input').val() + '&effectivedateid=' + jQuery("[name='EffectiveDateId']").val() + '&reportId=' + jQuery(this).attr('name') + '&reportName=' + encodeURIComponent(jQuery(this).html());
                                }
                                AppConnector.request(reportURL).then(
                                    function (data) {
                                        if (data.success) {
                                            window.location.href = 'index.php?module=Documents&view=Detail&record=' + data.result;
                                        }
                                    },
                                    function (error) {
                                    }
                                );
                            });
                        });
                        jQuery('#getReportSelectButton').closest('td').progressIndicator({'mode': 'hide'});
                        jQuery('#getReportSelectButton').removeClass('hide');
                    }
                },
                function (error) {
                }
            );
	},
    setCustomTariffType: function () {
        if (jQuery('#EditView').length < 1) {
            //we're in detail view forget this other non-sense
            this.tariffType = jQuery('#tariffType').val();
        } else {
            jQuery('input:checkbox[name="pricing_color_lock"]').prop('checked', false);
            jQuery('select[name="demand_color"]').prop('disabled', false).trigger('liszt:updated');
            jQuery('select[name="pricing_level"]').prop('disabled', false).trigger('liszt:updated');
            var selectTag = jQuery('select[name="effective_tariff"]');
            selectTag.siblings('.chzn-container').find('.chzn-results');
            var val = selectTag.find('option:selected').val();
            this.tariffType = jQuery('#tariffType_' + val).val();
            //console.dir('Tariff Type: '+this.tariffType);
        }
        this.disableGRRFields();
        this.hideFreeFVP();
        this.unblockUASFields();
        this.setMaxWeight(0);
        this.toggleValidThroughDate(true);
        this.toggleFieldsByTariffType(true);

        switch (this.tariffType) {
            case 'TPG':
                this.estimateType(['Binding']);
                this.toggleFieldsByTariffType(false);
                break;
            case 'Allied Express':
                this.estimateType(['Binding']);
                this.setMaxWeight(6000);
                this.toggleFieldsByTariffType(false);
                break;
            case 'TPG GRR':
                this.enableGRRFields();
                this.estimateType(['Not to Exceed']);
                this.toggleFieldsByTariffType(false);
                break;
            case 'ALLV-2A':
                this.enableFreeFVP();
                this.estimateType(['Non-Binding', 'Binding', 'Not to Exceed']);
                break;
            case 'Pricelock':
                this.estimateType(['Binding']);
                this.toggleFieldsByTariffType(false);
                break;
            case 'Blue Express':
                this.estimateType(['Binding']);
                this.setMaxWeight(6000);
                this.toggleFieldsByTariffType(false);
                break;
            case 'Pricelock GRR':
                this.enableGRRFields();
                this.toggleDayCertainPickup(false);
                this.estimateType(['Not to Exceed']);
                this.toggleFieldsByTariffType(false);
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
                this.estimateType(['Not to Exceed', 'Non-Binding']);
                this.enableFreeFVP();
                break;
            case 'Local/Intra':
                this.estimateType(['', 'Binding', 'Non-Binding', 'Not to Exceed']);
                break;
            case 'Max 3':
                //same as BaseSirva.js I don't think we can get here for Max 3/Max 4
                this.estimateType(['', 'Binding', 'Non-Binding', 'Not to Exceed']);
                break;
            case 'Max 4':
                this.estimateType(['', 'Binding', 'Non-Binding', 'Not to Exceed']);
                break;
            case 'Intra - 400N':
                this.enableFreeFVP();
                this.estimateType(['', 'Binding', 'Non-Binding', 'Not to Exceed']);
                this.toggleValidThroughDate(false);
                this.toggleDisplayOtherFields(true);
                break;
            case 'Canada Gov\'t':
                this.estimateType(['', 'Binding', 'Not to Exceed']);
                break;
            case 'Canada Non-Govt':
                this.estimateType(['', 'Binding', 'Not to Exceed']);
                break;
            case 'UAS':
                jQuery('#Estimates_editView_fieldName_acc_shuttle_dest_weight, #Estimates_editView_fieldName_acc_shuttle_dest_applied, #Estimates_editView_fieldName_acc_shuttle_dest_ot, #Estimates_editView_fieldName_acc_shuttle_dest_over25, #Estimates_editView_fieldName_acc_shuttle_dest_miles').prop('disabled', true);
                jQuery("input[name='linehaul_disc'], input[name='packing_disc'], input[name='accessorial_disc']").prop('disabled', true);
                this.estimateType(['Non-Binding']);
                break;
            default:
                this.estimateType(['', 'Binding', 'Non-Binding', 'Not to Exceed']);
                break;
        }
    },

    toggleDisplayOtherFields: function (toggle) {
        var fieldNames = [/*'validtill', 'accessorial_discaccessorial_disc', 'linehaul_disc', 'packing_disc'*/];

        var len = fieldNames.length;
        for (var i = 0; i < len; i++) {
            var input = jQuery('input[name="' + fieldNames[i] + '"]');
            var valueTD = input.closest('td');
            var labelTD = valueTD.prev('td');
            if (input.length > 0) {
                if (toggle && !valueTD.hasClass('hide')) {
                    valueTD.addClass('hide');
                }

                if (toggle && !labelTD.hasClass('hide')) {
                    labelTD.addClass('hide');
                }

                if (input.prop('type') == "hidden") {
                    jQuery('input:checkbox[name="' + fieldNames[i] + '"]').prop('checked', false);
                } else if (input.prop('type') == "text") {
                    input.val(0);
                }
            }
        }
        var keepSizeHide = ['validtill', 'accessorial_disc', 'accessorial_disc', 'linehaul_disc', 'packing_disc'];
        var len = keepSizeHide.length;
        for (var i = 0; i < len; i++) {
            var input = jQuery('[name="' + keepSizeHide[i] + '"]');
            var nextElement = input.siblings('.add-on');

            if (input.length > 0) {
                if (toggle && !input.hasClass('hide')) {
                    if (nextElement.length > 0) {
                        input.parent().addClass('hide');
                    } else {
                        input.addClass('1hide');
                    }
                }

                var labelTD = input.closest('td').prev('td').find('label');
                if (toggle && !labelTD.hasClass('hide')) {
                    labelTD.addClass('hide');
                }

                if (input.prop('type') == "hidden") {
                    jQuery('input:checkbox[name="' + fieldNames[i] + '"]').prop('checked', false);
                } else if (input.prop('type') == "text") {
                    input.val(0);
                }
            }
        }
    },

    toggleFieldsByTariffType: function (toggle) {
        if (toggle) {
            $('#Estimates_editView_fieldName_bottom_line_discount_label').closest('td').removeClass('hide');
            $('#Estimates_editView_fieldName_bottom_line_discount').closest('td').removeClass('hide');

            $('#Estimates_editView_fieldName_full_unpack_label').closest('td').removeClass('hide');
            $('#Estimates_editView_fieldName_full_unpack').closest('td').removeClass('hide');

            $('#Estimates_editView_fieldName_full_pack_label').removeClass('hide');
            $('#Estimates_editView_fieldName_full_pack').removeClass('hide');

            $('#Estimates_editView_fieldName_accessorial_disc_label').closest('td').children().removeClass('hide');
            $('#Estimates_editView_fieldName_accessorial_disc').closest('td').children().removeClass('hide');

            $('#Estimates_editView_fieldName_linehaul_disc_label').closest('td').children().removeClass('hide');
            $('#Estimates_editView_fieldName_linehaul_disc').closest('td').children().removeClass('hide');
        }
        else {
            // $('#Estimates_editView_fieldName_bottom_line_discount_label').closest('td').addClass('hide');
            // $('#Estimates_editView_fieldName_bottom_line_discount').closest('td').addClass('hide');

            $('#Estimates_editView_fieldName_full_unpack_label').closest('td').addClass('hide');
            $('#Estimates_editView_fieldName_full_unpack').closest('td').addClass('hide');

            $('#Estimates_editView_fieldName_full_pack_label').addClass('hide');
            $('#Estimates_editView_fieldName_full_pack').addClass('hide');

            $('#Estimates_editView_fieldName_accessorial_disc_label').closest('td').children().addClass('hide');
            $('#Estimates_editView_fieldName_accessorial_disc').closest('td').children().addClass('hide');

            $('#Estimates_editView_fieldName_linehaul_disc_label').closest('td').children().addClass('hide');
            $('#Estimates_editView_fieldName_linehaul_disc').closest('td').children().addClass('hide');
        }
    },

    toggleDayCertainPickup: function (toggle) {
        if (toggle) {
            $('#Estimates_editView_fieldName_day_certain').css('display', 'block');
            $('#Estimates_editView_fieldName_day_certain_row').css('display', 'block');
        } else {
            $('#Estimates_editView_fieldName_day_certain').css('display', 'none');
            $('#Estimates_editView_fieldName_day_certain_row').css('display', 'none');
        }
    },
    toggleValidThroughDate: function (toggle) {
        var lblValidtill = $('#Estimates_editView_fieldName_validtill_label');
        var txtValidtill = $('#Estimates_editView_fieldName_validtill');
        if (toggle) {
            lblValidtill.removeClass('hide');
            txtValidtill.parent().removeClass('hide');
        }
        else {
            lblValidtill.addClass('hide');
            txtValidtill.parent().addClass('hide');
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
    setMaxWeight: function (max) {
        var weightField = jQuery("input[name='weight']");
        if (max > 0) {
            weightField.prop('type', 'number');
            weightField.attr('data-validation-engine', "validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation], max[" + max + "]]");
            weightField.attr('min', 0).attr('max', max);
            var weight = weightField.val();
            if (weight > max) {
                weightField.val(max);
            }
            this.registerWeightChangeEvent(max);
        } else {
            weightField.prop('type', 'text');
            weightField.removeAttr('min').removeAttr('max');
            weightField.attr('data-validation-engine', "validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]");
            this.registerWeightChangeEvent();
        }
    },
    unblockUASFields: function () {
        jQuery("input[name='linehaul_disc'], input[name='packing_disc'], input[name='accessorial_disc']").prop('disabled', false);
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

        jQuery('#Estimates_editView_fieldName_grr_estimate').prop("readonly", true);


        jQuery('#Estimates_editView_fieldName_grr_estimate').closest('td').removeClass('hide');
        jQuery('#Estimates_editView_fieldName_grr_estimate_label').closest('td').removeClass('hide');

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

        jQuery('#Estimates_editView_fieldName_grr_estimate').closest('td').addClass('hide');
        jQuery('#Estimates_editView_fieldName_grr_estimate_label').closest('td').addClass('hide');

        jQuery('#Estimates_editView_fieldName_grr').closest('td').addClass('hide');
        jQuery('#Estimates_editView_fieldName_grr_label').closest('td').addClass('hide');
    },
    registerAddVehicle: function () {
        var thisInstance = this;
        //console.dir('registering local add vehicles');
        var addVehicleHandler = function () {
            var defaultVehicle = jQuery('.defaultVehicle');
            var newVehicle = defaultVehicle.clone().removeClass('defaultVehicle hide').appendTo('table[name="corpVehicleTable"]');
            //console.dir('registering local vehicles animation');
            thisInstance.registerVehicleAnimationEvent();
            //console.dir('registering local vehicles delete');
            thisInstance.registerDeleteVehicleEvent();
            //thisInstance.registerLookupByVIN();
            var vehicleCounter = jQuery('#numCorporateVehicles');
            var vehicleCount = vehicleCounter.val();
            vehicleCount++;
            vehicleCounter.val(vehicleCount);
            newVehicle.find('.vehicleTitle').find('b').append(' ' + vehicleCount);
            newVehicle.addClass('vehicle_' + vehicleCount);
            newVehicle.find('input, select, button, textarea').each(function () {
                jQuery(this).attr('name', jQuery(this).attr('name') + '_' + vehicleCount);
                if (jQuery(this).attr('name') == 'vehicle_id_' + vehicleCount) {
                    jQuery(this).val(vehicleCount);
                }
                jQuery(this).attr('id', jQuery(this).attr('id') + '_' + vehicleCount);
                if (jQuery(this).prop('nodeName') == 'SELECT') {
                    jQuery(this).chosen();
                }
            });
            //bind the special handler for Service after making the picklist named correctly
            jQuery('select[name="vehicle_service_' + vehicleCount + '"]').siblings('.chzn-container').find('.chzn-results').on('mouseup', thisInstance.corpVehicleServiceHandler);
            jQuery('input[name^="vehicle_weight_' + vehicleCount + '"]').on('change', thisInstance.corpVehicleWeightHandler);
        };
        jQuery('button[name="addVehicle"]').on('click', addVehicleHandler);
        jQuery('button[name="addVehicle2"]').on('click', addVehicleHandler);
    },
    registerVehiclesEventsForLoaded: function () {
        thisInstance = this;
        jQuery('select[name^="vehicle_service_"]').siblings('.chzn-container').find('.chzn-results').on('mouseup', thisInstance.corpVehicleServiceHandler).trigger('mouseup');
        jQuery('input[name^="vehicle_weight_"]').on('change', thisInstance.corpVehicleWeightHandler);
    },

    corpVehicleWeightHandler: function () {
        var selectedId = jQuery(this).attr('name').split('_')[2];
        jQuery('input[name="vehicle_cube_' + selectedId + '"]').val(parseInt(jQuery(this).val() / 7));
    },

    corpVehicleServiceHandler: function () {
        var currentTdElement = jQuery(this).closest('td');
        var vehicle_id = currentTdElement.find('select[name^="vehicle_service_"]').attr('name').split('_')[2];
        var selected = currentTdElement.find('.result-selected').html();
        var optionId = currentTdElement.find('.result-selected').attr('id').split('_')[5];
        var selectedId = currentTdElement.find('option:eq(' + optionId + ')').val();
        //console.dir(selectedId);
        //stop anything from being disabled, only disable when it makes sense
        jQuery('input:disabled').prop('disabled', false);
        switch (selectedId) {
            case 'Contract':
                break;
            case 'Budget':
                jQuery('input:checkbox[name="vehicle_car_on_van_' + vehicle_id + '"]').prop('checked', false).prop('disabled', true);
                jQuery('input:checkbox[name="vehicle_inoperable_' + vehicle_id + '"]').prop('checked', false).prop('disabled', true);
            default:
                jQuery('input[name="vehicle_charge_' + vehicle_id + '"]').val('0.00').prop('disabled', true);
                break;
        }
    },
    registerDeleteVehicleEvent: function () {
        jQuery('.deleteVehicleButton').off('click').on('click', function () {
            var bodyContainer = jQuery(this).closest('tbody');
            var vehicleId = jQuery(this).closest('tbody').find('input:hidden[name^="vehicle_id_"]').val();
            if (vehicleId && vehicleId != 'none') {
                jQuery('table[name="corpVehicleTable"]').append('<input type="hidden" name="removeVehicle_' + vehicleId + '" value="' + vehicleId + '" />');
            }
            bodyContainer.remove();
        });
    },

    registerVehicleAnimationEvent: function () {
        var thisInstance = this;
        //console.dir('in the register bit here');
        jQuery('.vehicleToggle').off('click').on('click', function (e) {
            var currentTarget = jQuery(e.currentTarget);
            var blockId = currentTarget.data('id');
            var closestBlock = currentTarget.closest('.vehicleBlock');
            var bodyContents = closestBlock.find('.vehicleContent');
            var data = currentTarget.data();
            var module = app.getModuleName();
            var hideHandler2 = function () {
                bodyContents.hide('slow');
                app.cacheSet(module + '.' + blockId, 0);
            };
            var showHandler2 = function () {
                bodyContents.show();
                app.cacheSet(module + '.' + blockId, 1);
            };
            if (data.mode == 'show') {
                hideHandler2();
                currentTarget.hide();
                closestBlock.find("[data-mode='hide']").show();
            } else {
                showHandler2();
                currentTarget.hide();
                closestBlock.find("[data-mode='show']").show();
            }
        });
    },

    addPricingFieldsTPG: function () {
        if ($('#EditView').length > 0) {
            $('#Estimates_editView_fieldName_percent_smf_label').parent().removeClass('hide').parent().removeClass('hide').children().removeClass('hide');
            $('.value_LBL_QUOTES_DESIRED_TOTAL').removeClass('hide').parent().removeClass('hide').children().removeClass('hide');
        }
    },

    removeFieldsUAS: function () {
        var thisInstance = this;
        var maxTime = 5;
        var tries = 1;

        var setIt = setInterval(function () {
            //This will still fire even after i changed the tariff again,
            //added a check to ensure we're still UAS type.  otherwise stop it.
            if (jQuery('#EditView').length == 0) {
                thisInstance.tariffType = jQuery('input[name="tariffType"]').val();
            } else {
                var selectTag = jQuery('select[name="effective_tariff"]');
                var val = selectTag.find('option:selected').val();
                thisInstance.tariffType = jQuery('#tariffType_' + val).val();
            }
            if (typeof thisInstance.tariffType != 'undefined' && thisInstance.tariffType.indexOf('UAS') != -1) {
                if (!jQuery('button[name="LoadTariffPacking"]').hasClass('hide')) {
                    jQuery('button[name="LoadTariffPacking"]').addClass('hide').closest('td').attr('colspan', '2');
                    jQuery('button[name="LoadTariffCrating"]').addClass('hide');
                    jQuery('input[name="apply_custom_pack_rate_override"]').closest('td').attr('colspan', '2');
                }

                // jQuery('input[name="apply_custom_pack_rate_override"]').closest('tr').remove();
                // jQuery('input[name="apply_custom_sit_rate_override"]').closest('tr').remove();
                // jQuery('input[name="apply_custom_sit_rate_override_tr"]').closest('tr').remove();
                //this should be functioned...
                var fields = [
                    'percent_smf',
                    'flat_smf',
                    'desired_total',
                    'smf_type'
                ];

                var trarray = [];
                fields.forEach(function (field) {
                    var workingTD = jQuery('input[name="' + field + '"]').closest('td');
                    workingTD.addClass('hide');
                    workingTD.prev('td').addClass('hide');
                    trarray.push(workingTD.parent('tr'));
                });

                trarray.forEach(function (trfield) {
                    var kids = trfield.children('td');
                    var hidden = [];
                    var notHidden = [];

                    kids.each(function (index, child) {
                        //if (child.className.match(/(^|\s+)hide(\s+|$)/)) {
                        if ($(child).hasClass('hide')) {
                            hidden.push(child);
                        } else {
                            //must check if the TD is just plain EMPTY
                            if (child.innerHTML.length != 0 && child.innerHTML != '&nbsp;') {
                                notHidden.push(child);
                            }
                        }
                    });
                    if (notHidden.length <= 0) {
                        //there are NO non-hidden tds so hide the row.
                        trfield.addClass('hide');
                    }
                });
            } else {
                clearInterval(setIt);
            }
            tries++;
            if (tries > maxTime) {
                clearInterval(setIt);
            }
        }, 1500);
    },
    comparePackToUnPack: function () {
        var thisInstance = this;
        $('#page').on('change', jQuery('input[name^="pack"]'), function (e) {
            var elementName = e.target.name;
            if (typeof elementName == 'undefined') {
                return;
            }
            if (elementName.match(/^pack\d+$/g)) {
                var packCount = elementName.split('pack')[1];
                thisInstance.resetPackingValues(elementName, packCount, 'pack', 'unpack', false);
            }
            else if (elementName.match(/^unpack\d+$/g)) {
                var packCount = elementName.split('unpack')[1];
                thisInstance.resetPackingValues(elementName, packCount, 'pack', 'unpack', true);
                thisInstance.resetPackingValues(elementName, packCount, 'pack_cont', 'pack', true);
            }
            else if (elementName.match(/^ot_pack\d+$/g)) {
                var packCount = elementName.split('ot_pack')[1];
                thisInstance.resetPackingValues(elementName, packCount, 'ot_pack', 'ot_unpack', false);
            }
            else if (elementName.match(/^ot_unpack\d+$/g)) {
                var packCount = elementName.split('ot_unpack')[1];
                thisInstance.resetPackingValues(elementName, packCount, 'ot_pack', 'ot_unpack', true);
            }
        });
    },
    resetPackingValues: function (elementName, packCount, pack, unpack, updatePack) {
        var packName = pack + packCount;
        var unpackName = unpack + packCount;
        if (parseFloat(jQuery('input[name="' + unpackName + '"]').val()) >
            parseFloat(jQuery('input[name="' + packName + '"]').val())) {
            if (updatePack) {
                jQuery('input[name="' + packName + '"]').val(jQuery('input[name="' + unpackName + '"]').val());
            }
            else {
                jQuery('input[name="' + unpackName + '"]').val(jQuery('input[name="' + packName + '"]').val());
            }

        }
    },

    registerEventForLoadTariffButton: function () {
        var thisInstance = this;
        jQuery('.btnLoadTariff').off('click').on('click', function () {
            var agentid = jQuery('[name="agentid"]').val();
            if (agentid == undefined || agentid == '') {
                return;
            }
            var location = jQuery(this).data('location');
            var params = {
                module: 'Estimates',
                view: 'CustomTariff',
                mode: 'getTariffServices',
                agent_id: agentid
            };
            var progressIndicatorElement = jQuery.progressIndicator();
            AppConnector.request(params).then(
                function (data) {
                    progressIndicatorElement.progressIndicator({'mode': 'hide'});
                    app.showModalWindow({'data': data, 'css': {'min-width': '30%'}});
                    thisInstance.registerEventForSelectTariffServices(location);
                }
            );
        });
    },
    registerEventForSelectTariffServices: function (location) {
        var thisInstance = this;
        var container = jQuery('.listTariffServices');
        container.find('.listViewEntries').off('click').on('click', function () {
            var serviceid = jQuery(this).data('serviceid');
            var recordData = jQuery(this).data('recordInfo');
            console.log(recordData);
            recordData = recordData.tariff_services;
            var sit_first_day_override = jQuery('[name="sit_first_day_' + location + '_override"]');
            var sit_addl_day_override = jQuery('[name="sit_addl_day_' + location + '_override"]');
            var sit_cartage_override = jQuery('[name="sit_cartage_' + location + '_override"]');
            jQuery.each(recordData, function (index, service) {
                var rateType = service.rate_type;
                var cwtByWeight = service.cwt_by_weight;
                switch (rateType) {
                    case 'SIT Cartage':
                        console.log(recordData);
                        if (rateType == 'SIT Cartage' && cwtByWeight != undefined && cwtByWeight.length > 0) {
                            var sit_weight = jQuery('[name="sit_' + location + '_weight"]').val();
                            var baseRate = false;
                            jQuery.each(cwtByWeight, function (i, v) {
                                if (parseFloat(v['from_weight']) < parseFloat(sit_weight) && parseFloat(v['to_weight']) > parseFloat(sit_weight)) {
                                    baseRate = v['rate'];
                                    return false;
                                }
                            });
                            if (baseRate != false) {
                                sit_cartage_override.val(baseRate);
                            } else {
                                if (service['cartage_cwt_rate']) {
                                    sit_cartage_override.val(service['cartage_cwt_rate']);
                                }
                            }

                        }
                        break;
                    case 'SIT First Day Rate':
                        if (service['cwt_rate']) {
                            sit_first_day_override.val(service['cwt_rate']);
                        }
                        break;
                    case 'SIT Additional Day Rate':
                        if (service['cwtperday_rate']) {
                            sit_addl_day_override.val(service['cwtperday_rate']);
                        }

                        break;
                    default:
                        break;
                }

            });
            app.hideModalWindow();
        });
    },

    /**
     * @param location
     */
    registerEventForSelectTariffPackingServices: function () {
        // var thisInstance = this;
        var container = jQuery('.listTariffServices');

        container.find('.listViewEntries')/*.off('click')*/.on('click', function () {
            var serviceid = jQuery(this).data('serviceid');
            var tariffServices = jQuery(this).data('recordInfo');
            tariffServices = tariffServices.tariff_services;
            jQuery.each(tariffServices, function (index, recordData) {
                if (recordData) {
                    if(typeof recordData['crate_packrate'] != 'undefined' && recordData['crate_packrate'] > 0)
                    {
                        jQuery('[name="tpg_custom_crate_rate"]').val(recordData['crate_packrate']);
                    }
                    if(typeof recordData['tariffpackingitems'] !== 'undefined' && recordData['tariffpackingitems']) {
                        for (var pi in recordData['tariffpackingitems']) {
                            var tblPacking = jQuery('table.packing');
                            // Match both id and name
                            var rowPackingItem = tblPacking.find('[data-pack_item_id="' + pi + '"][data-pack_item_name="' + recordData['tariffpackingitems'][pi]['name'] + '"]');
                            var cellCustomRate = null;
                            var txtCustomRate = null;
                            var txtPackRate = null;
                            var cellPackRate = null;

                            if (rowPackingItem.length > 0) {
                                // Pack rate cell
                                cellPackRate = rowPackingItem.find('.packingPackRate');

                                if (cellPackRate.length > 0) {
                                    txtPackRate = cellPackRate.find('input[name="packPackRate' + pi + '"]');
                                    txtPackRate.val(recordData['tariffpackingitems'][pi]['packing_rate']);
                                }

                                // Custom rate cell
                                cellCustomRate = rowPackingItem.find('.packingCustomRate');

                                if (cellCustomRate.length > 0) {
                                    txtCustomRate = cellCustomRate.find('input[name="packCustomRate' + pi + '"]');
                                    txtCustomRate.val(recordData['tariffpackingitems'][pi]['container_rate']);
                                }
                            }
                        }
                    }
                }
            });
            app.hideModalWindow();
        });
    },

    registerEventForSelectTariffCratingServices: function () {
        // var thisInstance = this;
        var container = jQuery('.listTariffServices');

        container.find('.listViewEntries')/*.off('click')*/.on('click', function () {
            var serviceid = jQuery(this).data('serviceid');
            var tariffServices = jQuery(this).data('recordInfo');
            tariffServices = tariffServices.tariff_services;
            jQuery.each(tariffServices, function (index, recordData) {
                if (recordData) {
                    if(typeof recordData['crate_packrate'] != 'undefined' && recordData['crate_packrate'] > 0)
                    {
                        jQuery('[name="tpg_custom_crate_rate"]').val(recordData['crate_packrate']);
                    }
                }
            });
            app.hideModalWindow();
        });
    },

    processForPricingTariffChange: function () {
        ////LoadValuationOptions
        var thisInstance = this;
        var tariff = jQuery('select[name="effective_tariff"] > option:selected').val();
        if (tariff == '' || tariff == undefined) return;
        var dataUrl = "index.php?module=Estimates&action=LoadValuationOptions&tariffid=" + tariff;
        var currentValuation = jQuery('select[name="valuation_deductible"]').find(':selected').val();

        var params = {
            module: 'AgentManager',
            action: 'GetBrand',
            agent_vanline_id: jQuery('select[name="agentid"]').find('option:selected').val(),
        };
        AppConnector.request(params).then(
            function(data) {
                if (data.success) {
                    dataUrl = dataUrl + "&brand=" + data.result
                }
            }
        ).then(function(){
          AppConnector.request(dataUrl).then(
              function (data) {
                  if (data.success) {
                      jQuery('select[name="valuation_deductible"]').html('<option value="">Select an Option</option>');
                      for (var i = 0; i < data.result.length; i++) {
                          if ((currentValuation == '' && data.result[i]['valuation_name'].substr(3) == ' - $0') || (currentValuation.substr(3) == data.result[i]['valuation_name'].substr(3))) {
                              jQuery('select[name="valuation_deductible"]').append('<option selected data-peround="' + data.result[i]['per_pound'] + '" value="' + data.result[i]['valuation_name'] + '">' + data.result[i]['valuation_name'] + '</option>');
                          } else {
                              jQuery('select[name="valuation_deductible"]').append('<option data-peround="' + data.result[i]['per_pound'] + '" value="' + data.result[i]['valuation_name'] + '">' + data.result[i]['valuation_name'] + '</option>');
                          }
                      }
                      jQuery('select[name="valuation_deductible"]').trigger('change');
                      jQuery('select[name="valuation_deductible"]').trigger('liszt:updated');
                  }
              }
          );
        });
        //-----------
        //if(jQuery(this).next().find('li.result-selected').length > 0){
        //    if(jQuery(this).next().find('li.result-selected').html()){
                var type = thisInstance.tariffType.toLowerCase();
                if (type &&
                    (
                    type.indexOf('tpg') >= 0 ||
                    type.indexOf('uas') >= 0 ||
                    type.indexOf('pricelock') >= 0 ||
                    type.indexOf('truckload') >= 0 ||
                    type.indexOf('grr') >= 0 ||
                    type.indexOf('express') >= 0
                    )
                ) {
                    if(!jQuery('input[name="accesorial_fuel_surcharge"]').hasClass('hide')) {
                        jQuery('input[name="accesorial_fuel_surcharge"]').addClass('hide').parent().parent().prev().children().addClass('hide');
                        jQuery('input[name="accesorial_fuel_surcharge"]').next().addClass('hide');
                    }
                    jQuery('input[name="irr_charge"]').prop('readonly', true).val('4');
                    jQuery('#Estimates_editView_fieldName_bulky_article_changes').parent().prev().children().addClass('hide');
                    jQuery('#Estimates_editView_fieldName_bulky_article_changes').addClass('hide');
                    if (type && type.indexOf('uas') >= 0) {
                        jQuery('table[name="LBL_QUOTES_SITDETAILS"] tbody tr:nth-child(1)').addClass('hide');
                    }
                } else {
                    if(jQuery('input[name="accesorial_fuel_surcharge"]').hasClass('hide')) {
                        jQuery('input[name="accesorial_fuel_surcharge"]').removeClass('hide').parent().parent().prev().children().removeClass('hide');
                        jQuery('input[name="accesorial_fuel_surcharge"]').next().removeClass('hide');
                    }
                    jQuery('input[name="irr_charge"]').prop('readonly', false).val('4');
                }
        //    }
        //}

        var intValue = jQuery('select[name="effective_tariff"]').val();
        //update to the code to handle the MAX4 hide section I am using a different if statement, because I do not want to
        //rely on the text value. If we update the text value, the if statement will not work.
        if (intValue == '32674') {
            var inputDiv = $('input[name="validtill"]').parent('div');
            if (!inputDiv.hasClass('hide')) {
                inputDiv.addClass('hide')
            }
            var label = $('input[name="validtill"]').closest('td').prev('td').find('label');
            if (label) {
                if (!label.hasClass('hide')) {
                    label.addClass('hide');
                }
            }
        }
        else {
            var inputDiv = $('input[name="validtill"]').parent('div');
            if (inputDiv.hasClass('hide')) {
                inputDiv.removeClass('hide')
            }
            var label = $('input[name="validtill"]').closest('td').prev('td').find('label');
            if (label) {
                if (label.hasClass('hide')) {
                    label.removeClass('hide');
                }
            }
        }
        this.checkAndConvertFieldType();
    },

    getBrand: function(){
    	var params = {
    		module: 'AgentManager',
    		action: 'GetBrand',
    		agent_vanline_id: jQuery('select[name="agentid"]').find('option:selected').val(),
    	};
    	AppConnector.request(params).then(
			function(data) {
				if (data.success) {
                    return data.result;
				}
    		}
    	);
    },

    checkAndConvertFieldType: function (valuationAmount) {
        var thisInstance = this;
        var tariffId = jQuery('[name="effective_tariff"]').val();
        var moveType = jQuery('[name="move_type"]').val();
        var shipperType = jQuery('[name="shipper_type"]').val();
        var valuationEle = jQuery('[name="valuation_amount"]');
        var weightFactor = 6;
        if (jQuery('input[name="apply_free_fvp"][type!="hidden"]').prop('checked')) {
            weightFactor = jQuery('input[name="min_declared_value_mult"]').val();
        }
        if (valuationAmount == undefined) {
            valuationAmount = valuationEle.val();
        }
        var weight = jQuery('[name="weight"]').val();
        var params = {
            module: 'Estimates',
            view: 'checkAndLoadValuationAmount',
            tariff_id: tariffId,
            quote_id: jQuery('input[name="record"]').val(),
            valuation_amount: valuationAmount,
            move_type: moveType,
            shipper_type: shipperType,
            weight: weight,
            weight_factor: weightFactor
        }
        jQuery('button[value="submit"]').attr('disabled', 'disabled');
        AppConnector.request(params).then(
            function (data) {
                if (data != '') {
                    jQuery('button[value="submit"]').removeAttr('disabled');
                    var tdElement = valuationEle.closest('td');
                    tdElement.html(data);
                    app.changeSelectElementView(tdElement);
                    thisInstance.registerWeightChangeEvent();
                }
            },
            function (error) {
            }
        ).then(function(){
            thisInstance.valuationChange(jQuery('[name^="valuation_amount"]'));
            jQuery('[name^="valuation_amount"]').on('change', function(){
              thisInstance.valuationChange($(this));
            });
        });
    },

    valuationChange : function(ele) {
      var thisInstance = this;
      if(ele.is('select')) {
        valuationVal = jQuery('select[name="valuation_amount_pick"]').val();
        if(valuationVal == 'Over 250000' || valuationVal == '') {
          jQuery('input[name="valuation_amount"]').siblings('p').removeClass('hide');
          jQuery('input[name="valuation_amount"]').removeClass('hide');
          jQuery('input[name="valuation_amount"]').attr('min',250000);
        } else {
          jQuery('input[name="valuation_amount"]').siblings('p').addClass('hide');
          jQuery('input[name="valuation_amount"]').addClass('hide');
          jQuery('input[name="valuation_amount"]').removeAttr('min');
          jQuery('input[name="valuation_amount"]').val(valuationVal);
        }
      }
      if(ele.is('input')) {
        valuationVal = jQuery('input[name="valuation_amount"]').val();
        var pickerVal = jQuery('select[name="valuation_amount_pick"]').val();
        if(pickerVal == 'Over 250000' && valuationVal < 250000) {
            valuationVal = 250000;
        }else if(isNaN(parseInt(valuationVal))) {
            valuationVal = 0;
        }
        if(typeof valuationVal == 'string') {
            valuationVal = valuationVal.replace(/(,)/gi, '');
        }
        jQuery('input[name="valuation_amount"]').val(Math.ceil(parseInt(valuationVal)/100)*100)
      }
    },

    showSMF: function () {
        jQuery('input[name="percent_smf"]').closest('tr').removeClass('hide');
        jQuery('input[name="desired_total"]').closest('tr').removeClass('hide');
    },

    hideSMF: function () {
        jQuery('input[name="percent_smf"]').closest('tr').addClass('hide');
        jQuery('input[name="desired_total"]').closest('tr').addClass('hide');
    },

    registerEvents: function () {
        var thisInstance = this;
        thisInstance.onLoadTruckLoad();
        if (jQuery('#EditView').length == 0) {
            this.detailView = true;
            this.parent = Estimates_Detail_Js.getInstance();
            this.registerReportsButtonDetail();
            if (jQuery('input#tariffType').val() == 'TPG GRR' || jQuery('input#tariffType').val() == 'Pricelock GRR') {
                this.enableGRRFieldsDetail();
            }
        } else {
            this.parent = Estimates_Edit_Js.getInstance();
            this.registerReportsButtonEdit();
        }
        this.registerLoadPackingButtonEvent();
        this.registerLoadCratingButtonEvent();
        this.registerLoadSITButtonEvent();
        this.setCustomTariffType();
        //console.dir('in registerEvents');

        //OT13854 remove TPG block from Intra - 400N
        //OT13657 remove TPG block from UAS ...
        //changed to just show for TPG or Pricelock... this is an assumption.
        if (thisInstance.tariffType &&
            (
                thisInstance.tariffType.indexOf('TPG') != -1 ||
                thisInstance.tariffType.indexOf('Pricelock') != -1 ||
                thisInstance.tariffType.indexOf('Express') != -1 ||
                thisInstance.tariffType.indexOf('UAS') != -1 ||
                thisInstance.tariffType.indexOf('ALLV') != -1 ||
                thisInstance.tariffType.indexOf('NAVL') != -1
            )
        ) {
            //It is either TPG* or Pricelock* allied or blue express and uas.
            var contractId = jQuery('input[name="contract"]').val();
            if (jQuery('input:hidden[id="view"]').val() == 'Detail') {
                this.loadTPGPricelockBlocks('index.php?module=Estimates&view=CustomTariffDetail&mode=showTPG&tariff_type=' + encodeURIComponent(this.tariffType) + '&contract=' + contractId);
            } else {
                this.loadTPGPricelockBlocks('index.php?module=Estimates&view=CustomTariff&mode=showTPG&tariff_type=' + encodeURIComponent(this.tariffType) + '&contract=' + contractId);
            }
            this.unhideTPGPricelockBlock();
            if (
                thisInstance.tariffType.indexOf('UAS') != -1 ||
                thisInstance.tariffType.indexOf('ALLV') != -1 ||
                thisInstance.tariffType.indexOf('NAVL') != -1
            ) {
                //OT14810 show the demand color and pricing levevl portion only.
                this.removeFieldsUAS();
            }
            if (thisInstance.tariffType.indexOf('TPG') != -1) {
                //OT14810 show the demand color and pricing levevl portion only.
                this.addPricingFieldsTPG();
            }
        } else {
            this.hideTPGPricelockBlock();
        }

        if (thisInstance.tariffType && (thisInstance.tariffType.indexOf('TPG') != -1 || thisInstance.tariffType.indexOf('Pricelock') != -1)) {
            thisInstance.showSMF();
        } else {
            thisInstance.hideSMF();
        }

        this.registerEffectiveDateChange();
        this.registerLoadDateChangeEvent();
        this.registerSMFType();
        this.registerFullPackAppliedEvent();
        this.registerFullPackOverrideAppliedEvent();
        //this.registerToggleAccessorialFields();
        jQuery('input:checkbox[name="full_pack"]').trigger('change');
        jQuery('select[name="valuation_amount"]').trigger('change');
        jQuery('input:checkbox[name="apply_full_pack_rate_override"]').trigger('change');
        if (jQuery('#EditView').length > 0) {
            jQuery('#EditView').on('submit', function (e) {
                e.preventDefault();
                //thisInstance.toggleDisableHiddenFields();
                //this is a somewhat hackish way to fight the asynchronisity issues we were having with this not finishing
                //before doing the submit, this works but is ugly, sorry, but working is what matters
                var fun1 = function () {
                    jQuery('select[name="demand_color"]').prop('disabled', false).trigger('liszt:updated');
                    jQuery('select[name="pricing_level"]').prop('disabled', false).trigger('liszt:updated');
                    jQuery('input:checkbox[name="full_pack"]').prop('disabled', false);
                    jQuery('input:checkbox[name="apply_custom_pack_rate_override"]').prop('disabled', false);
                    jQuery(document).trigger('unhidden_event');
                };
                var fun2 = function () {
                    jQuery('#EditView').unbind('submit').submit();
                };
                jQuery(document).on('unhidden_event', fun2);
                fun1();
            });
        }
        this.registerPricingColorLock();
        this.comparePackToUnPack();
        //@TODO: This doesn't stay registered.
        //So this is registering a checkbox event handler from the parent.
        // I am unsure it seems like this shouldn't be done like this..
        this.registerVehicleSpaceExclusivity();
        thisInstance.registerLockSaveOnUnratedChanges();
    },

    initialize: function()
    {

    }
});
