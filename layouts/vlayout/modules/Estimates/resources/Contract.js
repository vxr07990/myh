/**
 * Created by dbolin on 12/22/2016.
 */
Vtiger_Edit_Js("Estimates_Contract_Js", {

        getInstance: function() {
            if(Estimates_Contract_Js.currentInstance)
            {
                return Estimates_Contract_Js.currentInstance;
            }
            Estimates_Contract_Js.currentInstance = new Estimates_Contract_Js();
            return Estimates_Contract_Js.currentInstance;
        },
        I: function(){
            return Estimates_Contract_Js.getInstance();
        },
    },
    {
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
                fieldElement.trigger(Vtiger_Edit_Js.referenceSelectionEvent, {'source_module' : popupReferenceModule, 'record' : id, 'selectedName' : selectedName});
            }

            fieldDisplayElement.validationEngine('closePrompt',fieldDisplayElement);
            fieldElement.trigger('change');
        },

        populateContractData : function() {
            var thisInstance = this;

            if (jQuery('input:hidden[name="contract"]').length) {
                jQuery('input:hidden[name="contract"]').on(Vtiger_Edit_Js.referenceSelectionEvent, function() {
                    var message = app.vtranslate('JS_MSG_POPULATE_CONTRACT');
                    Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
                        function(e) {
                            //they pressed yes
                            var id = jQuery('input[name="contract"]').val();
                            var currentOwner = jQuery('[name="agentid"]').val();
                            var url = 'index.php?module=' + Estimates_Edit_Js.I().moduleName + '&action=PopulateContractData&contract_id=' + id + '&current_owner=' + currentOwner;
                            AppConnector.request(url).then(
                                function(data) {
                                    if (data.success) {
                                        //clear any existing contract items in order to use this contract's items
                                        thisInstance.unbindContractItems(false);
                                        thisInstance.applyContractEnforcement(data);
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
                            jQuery('[name="contract"]').val('');
                            jQuery('[name="contract"]').closest('tbody').find('[name="contract_display"]').val('').attr('readonly',false);
                            //they pressed no, clear the contract selection
                            thisInstance.removeContractRow();
                            thisInstance.unbindContractItems(false);
                        }
                    );

                })
            }
        },

        addFlatRateAutoItems: function(autoitems) {
            jQuery('.FlatRateAutoRow').remove();
            if(typeof autoitems != 'undefined' && autoitems.length>0) {
                jQuery('[name="contractFlatRateAuto"]').val('1').trigger('change');
                for(var i=0;i<autoitems.length;i++) {
                    var defaultBlock = $('.defaultFlatRateAutoRow').clone();
                    defaultBlock.removeClass('hide').removeClass('defaultFlatRateAutoRow').addClass('FlatRateAutoRow');
                    defaultBlock.find('[name="FlatRateAutoTableFromMileage"]').val(autoitems[i]['from_mileage']);
                    defaultBlock.find('[name="FlatRateAutoTableToMileage"]').val(autoitems[i]['to_mileage']);
                    defaultBlock.find('[name="FlatRateAutoTableRate"]').val(autoitems[i]['rate']);
                    if(autoitems[i]['discount'] == 'on') {
                        defaultBlock.find('[name="FlatRateAutoTableDiscount"]').prop('checked', true).prop('disabled', true);
                    } else {
                        defaultBlock.find('[name="FlatRateAutoTableDiscount"]').prop('checked', false).prop('disabled', true);
                    }
                    jQuery('#auto-rate-table tbody').append(defaultBlock);
                }
            }
        },

        applyContractMiscItems : function(misc_items) {
            //@TODO : review if this will work with Local Tariffs?  might need to move it up into the else.
            //ensure these variables are so and you know like correct.
            thisInstance.flatItemsSequence = jQuery('.flatItemRow').length;
            thisInstance.qtyRateItemsSequence = jQuery('.qtyRateItemRow').length;
            var toggleQty = false;
            var toggleFlat = false;

            for(var i = 0; i<misc_items.length; i++){
                //Ha! attempt at readability and condensation FOILED by non-use!
                var currentMisc = misc_items[i];
                if(currentMisc['is_quantity_rate'] == 0 || currentMisc['is_quantity_rate'] == null){
                    //handle Flat Charges here
                    var flatItemsTable = jQuery('#flatItemsTab');
                    flatItemsTable.removeClass('hide');
                    flatItemsTable.closest('table').removeClass('hide');
                    var newRow = jQuery('.defaultFlatItem').clone(true, true);
                    var sequence = thisInstance.flatItemsSequence++;
                    newRow.removeClass('hide defaultFlatItem');
                    newRow.attr('id', 'flatItemRow'+sequence);
                    newRow.find('input.rowNumber').val(sequence);
                    var name = "flatDescription";
                    newRow.find('.deleteMiscChargeButton').addClass('hide');
                    newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
                    newRow.find('input[name="'+name+sequence+'"]').val(currentMisc['description']).attr('readonly','readonly');
                    newRow.append("<input class='hide enforced' value='1' name='flatEnforced"+sequence+"' />");
                    newRow.append("<input class='hide' value='"+ currentMisc['contracts_misc_id'] +"' name='flatFromContract"+sequence+"' />");
                    newRow.find('.fieldname[value="'+name+'"]').val(name+sequence).attr('readonly','readonly');
                    name = "flatCharge";
                    newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
                    newRow.find('input[name="'+name+sequence+'"]').val(currentMisc['rate']).attr('readonly','readonly');
                    newRow.find('.fieldname[value="'+name+'"]').val(name+sequence).attr('readonly','readonly');
                    name = "flatDiscounted";
                    newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
                    if(currentMisc['discounted'] == 'on'){
                        newRow.find('input[name="'+name+sequence+'"]').prop('checked',true).attr('readonly','readonly');
                    }
                    newRow.find('.fieldname[value="'+name+'"]').val(name+sequence).attr('readonly','readonly');
                    name = "flatChargeToBeRated";
                    newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
                    newRow.find('input[name="'+name+sequence+'"]').prop('checked',true);
                    /*
                     name = "flatDiscountPercent";
                     newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
                     newRow.find('input[name="'+name+sequence+'"]').val(currentMisc['discount']).attr('readonly','readonly');
                     newRow.find('.fieldname[value="'+name+'"]').val(name+sequence).attr('readonly','readonly');
                     */
                    newRow = newRow.appendTo(flatItemsTable);
                    toggleFlat = true;
                }else if(currentMisc['is_quantity_rate'] == 1){
                    //handle Qty/Rate Charges here
                    var qtyRateItemsTable = jQuery('#qtyRateItemsTab');
                    qtyRateItemsTable.removeClass('hide');
                    qtyRateItemsTable.closest('table').removeClass('hide');
                    var newRow = jQuery('.defaultQtyRateItem').clone(true, true);
                    newRow.find('.deleteMiscChargeButton').addClass('hide');
                    //add the hidden input for not deletable here.
                    newRow.find('.deleteMiscChargeButton').closest('td').append('');
                    var sequence = thisInstance.qtyRateItemsSequence++;
                    newRow.append("<input class='hide enforced' value='1' name='qtyRateEnforced"+sequence+"' />");
                    newRow.append("<input class='hide' value='"+ currentMisc['contracts_misc_id'] +"' name='qtyRateFromContract"+sequence+"' />");
                    newRow.removeClass('hide defaultQtyRateItem');
                    newRow.attr('id', 'qtyRateItem'+sequence);
                    newRow.find('input.rowNumber').val(sequence);
                    var name = "qtyRateDescription";
                    newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
                    newRow.find('input[name="'+name+sequence+'"]').val(currentMisc['description']).attr('readonly','readonly');
                    newRow.find('.fieldname[value="'+name+'"]').val(name+sequence).attr('readonly','readonly');
                    name = "qtyRateCharge";
                    newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
                    newRow.find('input[name="'+name+sequence+'"]').val(currentMisc['rate']).attr('readonly','readonly');
                    newRow.find('.fieldname[value="'+name+'"]').val(name+sequence).attr('readonly','readonly');
                    name = "qtyRateQty";
                    newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
                    newRow.find('input[name="'+name+sequence+'"]').val(currentMisc['quantity']).attr('readonly','readonly');
                    newRow.find('.fieldname[value="'+name+'"]').val(name+sequence).attr('readonly','readonly');
                    name = "qtyRateDiscounted";
                    newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
                    if(currentMisc['discounted'] == 'on'){
                        newRow.find('input[name="'+name+sequence+'"]').prop('checked',true).attr('readonly','readonly');
                    }
                    newRow.find('.fieldname[value="'+name+'"]').val(name+sequence).attr('readonly','readonly');
                    name = "qtyChargeToBeRated";
                    newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
                    newRow.find('input[name="'+name+sequence+'"]').prop('checked',true);
                    /*
                     name = "qtyRateDiscountPercent";
                     newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
                     newRow.find('input[name="'+name+sequence+'"]').val(currentMisc['discount']).attr('readonly','readonly');
                     newRow.find('.fieldname[value="'+name+'"]').val(name+sequence).attr('readonly','readonly');
                     */
                    newRow = newRow.appendTo(qtyRateItemsTable);
                    toggleQty = true;
                }
            }
            if (toggleQty) {
                //hide is triggered to show.
                //as they all say 2+2 is 5.
                var table = jQuery('#qty_rate_table');
                table.find('.blockToggle[data-mode="hide"]').trigger('click');
            }
            if (toggleFlat) {
                var table = jQuery('#flat_charge_table');
                table.find('.blockToggle[data-mode="hide"]').trigger('click');
            }
        },

        applyContractEnforcement : function(dataIn) {
            var thisInstance = this;
            var resF = function(data) {
                if (data.success) {
                    var updateData = function () {
                        //set the billing address
                        jQuery('input[name="bill_city"]').val(data.result['city']).attr('readonly', 'readonly');
                        jQuery('input[name="bill_street"]').val(data.result['address1']).attr('readonly', 'readonly');
                        jQuery('input[name="bill_country"]').val(data.result['country']).attr('readonly', 'readonly');
                        jQuery('input[name="billing_apn"]').val(data.result['billing_apn']);
                        jQuery('input[name="bill_state"]').val(data.result['state']).attr('readonly', 'readonly');
                        jQuery('input[name="bill_code"]').val(data.result['zip']).attr('readonly', 'readonly');
                        jQuery('input[name="bill_pobox"]').val(data.result['pobox']).attr('readonly', 'readonly');
                        if (typeof data.result['additional_valuation'] !== 'undefined' && data.result['additional_valuation'] != null) { //added check to stop setting these to nothing.
                            var contractAdditionalValuation = parseFloat(data.result['additional_valuation']).toFixed(2);
                            jQuery('input[name="additional_valuation"]').val(contractAdditionalValuation);
                        }
                        if (data.result['sit_distribution_discount'] > 0) {
                            jQuery('input[name="sit_distribution_discount"]').val(data.result['sit_distribution_discount']).attr('readonly', 'readonly');
                        }
                        if (data.result['bottom_line_distribution_discount'] > 0) {
                            jQuery('input[name="bottom_line_distribution_discount"]').val(data.result['bottom_line_distribution_discount']).attr('readonly', 'readonly');
                        }
                        thisInstance.showContractRow();
                        jQuery('input[name="parent_contract"]').val(data.result['parent_contract']).attr('readonly', 'readonly');
                        jQuery('input[name="nat_account_no"]').val(data.result['nat_account_no']).attr('readonly', 'readonly');

                        thisInstance.showContractRow();
                        jQuery('input[name="parent_contract"]').val(data.result['parent_contract']).attr('readonly', 'readonly');
                        jQuery('input[name="nat_account_no"]').val(data.result['nat_account_no']).attr('readonly', 'readonly');
                        /*console.dir('Contract Info Populating');
                         console.dir('min weight: ' + data.result['min_weight']);
                         if(data.result['min_weight'] || jQuery('[name="weight"]').val()<data.result['min_weight']) {
                         jQuery('input[name="weight"]').val(data.result['min_weight']);
                         } */
                        if (data.result['contract_no'] !== 'undefined' && data.result['contract_no']) {
                            jQuery('input[name="contract_display"]').val(data.result['contract_no']).attr('readonly', 'readonly');
                        }
                        if (data.result['extended_sit_mileage']) {
                            jQuery('input[name="sit_origin_miles"]').val(data.result['extended_sit_mileage']).prop('readonly', true);
                            jQuery('input[name="sit_dest_miles"]').val(data.result['extended_sit_mileage']).prop('readonly', true);
                        }

                        //set the UI type 10s
                        if (typeof data.result['account_label'] !== 'undefined' && data.result['account_label'].length > 0) { //added check to stop setting these to nothing.
                            var container = jQuery('#account_id_display').closest('td');
                            var obj = {id: data.result['account'], name: data.result['account_label'], suppress: true};
                            thisInstance.setReferenceFieldValue(container, obj);
                        }
                        if (typeof data.result['contact_label'] !== 'undefined'
                            && data.result['contact']) { //added check to stop setting these to nothing.
                            var contactName = jQuery('#contact_id_display').closest('td');
                            var contactObj = {id: data.result['contact'], name: data.result['contact_label'], suppress: true};
                            thisInstance.setReferenceFieldValue(contactName, contactObj);
                        }
                        if (typeof data.result['account_label'] !== 'undefined' && typeof data.result['billing_apn'] !== 'undefined') { //added check to stop setting these to nothing.
                            var container = jQuery('#billing_apn_display').closest('td');
                            var obj = {id: data.result['billing_apn'], name: data.result['account_label'], suppress: true};
                            thisInstance.setReferenceFieldValue(container, obj);
                        }

                        if (data.result['effective_date']) {
                            //seems to be the wrong thing to look for?
                            //var dateFormat = jQuery('#Estimates_editView_fieldName_pickup_date').data('dateFormat');
                            var dateField = jQuery('[name="interstate_effective_date"]');
                            if (jQuery('#isLocalRating').val() == 1) {
                                dateField = jQuery('[name="effective_date"]');
                            }
                            var dateFormat = dateField.data('date-format');
                            if (typeof dateFormat != 'undefined') {
                            var y = data.result['effective_date'].substr(0, 4);
                            var m = data.result['effective_date'].substr(5, 2);
                            var d = data.result['effective_date'].substr(8);
                            var userDate = '';
                            for (var i = 0; i < 10; i++) {
                                if (dateFormat[i] == 'y') {
                                    userDate += y[0];
                                    y = y.substr(1);
                                } else if (dateFormat[i] == 'm') {
                                    userDate += m[0];
                                    m = m.substr(1);
                                } else if (dateFormat[i] == 'd') {
                                    userDate += d[0];
                                    d = d.substr(1);
                                } else if (dateFormat[i] == '-') {
                                    userDate += '-';
                                }
                            }
                                if (jQuery('[name="instance"]').val() == 'sirva') {
                                    //@TODO: Why is this a fix, this shouldn't be a fix, but it is.
                                    //@TODO: val() is refusing to set the value, and I cannot get it to set without a separate .attr() call.
                                    jQuery('input[name="interstate_effective_date"]').DatePickerSetDate(userDate, true);
                                    jQuery('input[name="interstate_effective_date"]').val(jQuery('input[name="interstate_effective_date"]').DatePickerGetDate(true)).attr('readonly', 'readonly');
                                } else {
                                    dateField.val(userDate).prop('readonly', true).trigger('change');
                                }
                            }
                        } else {
                            var today = new Date();
                            jQuery('input[name="interstate_effective_date"]').DatePickerSetDate(today, true);
                            jQuery('input[name="interstate_effective_date"]').val(jQuery('input[name="interstate_effective_date"]').DatePickerGetDate(true)).trigger('change');
                        }

                        if (data.result['fuel_surcharge'] && data.result['fuel_type'] == 'Static Fuel Percentage') {
                            $('#Estimates_editView_fieldName_accesorial_fuel_surcharge').val(data.result['fuel_surcharge']);
                            $('#Estimates_editView_fieldName_sit_origin_fuel_percent').val(data.result['fuel_surcharge']);
                            $('#Estimates_editView_fieldName_sit_dest_fuel_percent').val(data.result['fuel_surcharge']);
                        }
                        if(data.result['fuel_type']) {
                            $('#Estimates_editView_fieldName_accesorial_fuel_surcharge').attr('readonly', 'readonly');
                            $('#Estimates_editView_fieldName_sit_origin_fuel_percent').attr('readonly', 'readonly');
                            $('#Estimates_editView_fieldName_sit_dest_fuel_percent').attr('readonly', 'readonly');
                        }

                        if (data.result['irr']) {
                            jQuery('#' + Estimates_Edit_Js.I().moduleName + '_editView_fieldName_irr_charge').val(data.result['irr']).attr('readonly', 'readonly');
                        } else {
                            jQuery('#' + Estimates_Edit_Js.I().moduleName + '_editView_fieldName_irr_charge').val(4);
                        }
                        jQuery('#' + Estimates_Edit_Js.I().moduleName + '_editView_fieldName_linehaul_disc').val(data.result['linehaul_disc']).attr('readonly', 'readonly');
                        jQuery('#' + Estimates_Edit_Js.I().moduleName + '_editView_fieldName_accessorial_disc').val(data.result['accessorial_disc']).attr('readonly', 'readonly');
                        jQuery('#' + Estimates_Edit_Js.I().moduleName + '_editView_fieldName_packing_disc').val(data.result['packing_disc']).attr('readonly', 'readonly');
                        jQuery('#' + Estimates_Edit_Js.I().moduleName + '_editView_fieldName_sit_disc').val(data.result['sit_disc']).attr('readonly', 'readonly');
                        jQuery('#' + Estimates_Edit_Js.I().moduleName + '_editView_fieldName_bottom_line_discount').val(data.result['bottom_line_disc']).attr('readonly', 'readonly');
                        jQuery('#' + Estimates_Edit_Js.I().moduleName + '_editView_fieldName_weight').attr('data-min', data.result['min_weight']);
                        if (jQuery('#' + Estimates_Edit_Js.I().moduleName + '_editView_fieldName_weight').val() == '') {
                            jQuery('#' + Estimates_Edit_Js.I().moduleName + '_editView_fieldName_weight').val(data.result['min_weight']);
                        }

                        if (typeof data.result['valuation_deductible'] != 'undefined' && data.result['valuation_deductible'] != null && data.result['valuation_deductible'].length > 0) {
                            jQuery('[name="contractValuationOverride"]').val('1').trigger('change');
                            var dedAmt = data.result['valuation_deductible'].split('-')[1];
                            if (!dedAmt) {
                                jQuery('select[name="valuation_deductible"]').val(data.result['valuation_deductible']).trigger('liszt:updated').trigger('change');
                            } else {
                                jQuery('select[name="valuation_deductible"] > option').prop('selected', false);
                                jQuery('select[name="valuation_deductible"]').find('[value$="' + dedAmt + '"]').prop('selected',true).trigger('liszt:updated').trigger('change');
                            }
                        }
                        //update the rest of the valuation options:
                        if (typeof data.result['free_fvp_amount'] !== 'undefined') { //added check to stop setting these to nothing.
                            jQuery('input[name="free_valuation_limit"]').val(data.result['free_fvp_amount']).attr('readonly', 'readonly');
                        }

                        if (typeof data.result['min_val_per_lb'] !== 'undefined' && data.result['min_val_per_lb'] != null) { //added check to stop setting these to nothing.
                            jQuery('input[name="min_declared_value_mult"]').val(data.result['min_val_per_lb']).attr('readonly', 'readonly');
                            jQuery('input[name="weight"]').trigger('value_change');
                        }

                        if (typeof data.result['rate_per_100'] !== 'undefined') { //added check to stop setting these to nothing.
                            jQuery('input[name="rate_per_100"]').val(data.result['rate_per_100']).attr('readonly', 'readonly');
                        }
                        if (typeof data.result['additional_valuation'] !== 'undefined' && data.result['additional_valuation'] != null) { //added check to stop setting these to nothing.
                            contractAdditionalValuation = parseFloat(data.result['additional_valuation']).toFixed(2);
                            jQuery('input[name="additional_valuation"]').val(contractAdditionalValuation);
                        }

                        if (typeof data.result['free_fvp_allowed'] !== 'undefined') { //added check to stop setting these to nothing.
                            if (data.result['free_fvp_allowed']) {
                                jQuery('input:checkbox[name="apply_free_fvp"]').prop('checked', true).trigger('change');
                            }
                        }

                        if (typeof data.result['valuation_amount'] !== 'undefined' && data.result['valuation_amount'] != null) {
                            jQuery('input[name="valuation_amount"]').val(parseFloat(data.result['valuation_amount']).toFixed(2));
                        }
                        if (typeof data.result['valuation_deductible_amount'] !== 'undefined' && data.result['valuation_deductible_amount']) {
                            var workingDiv = jQuery('select[name="valuation_deductible_amount"]').siblings().first();
                            workingDiv.find('li.result-selected').removeClass('result-selected');
                            workingDiv.find('li:contains("' + app.vtranslate(data.result['valuation_deductible_amount']) + '")').addClass('result-selected');
                            workingDiv.find('span').html(app.vtranslate(data.result['valuation_deductible_amount']));
                            jQuery('select[name="valuation_deductible_amount"]').data('selectedValue', data.result['valuation_deductible_amount']);
                            jQuery('select[name="valuation_deductible_amount"]').find('option').prop('selected', false);
                            jQuery('select[name="valuation_deductible_amount"]').find('option[value="' + data.result['valuation_deductible_amount'] + '"]').prop('selected', true);
                        }
                        if (typeof data.result['valuation_discounted'] !== 'undefined') {
                            jQuery('input[name="valuation_discounted"]').val(data.result['valuation_discounted']);
                        }
                        if (typeof data.result['valuation_discount_amount'] !== 'undefined' && data.result['valuation_discount_amount'] != null) {
                            jQuery('input[name="valuation_discount_amount"]').val(data.result['valuation_discount_amount']);
                        }
                        if (typeof data.result['total_valuation'] !== 'undefined' && data.result['total_valuation'] != null) {
                            jQuery('input[name="total_valuation"]').val(data.result['total_valuation']);
                        }
                        jQuery('select[name="valuation_deductible"]').trigger('change');

                        //thisInstance.activateCustomJs(jQuery('select[name="effective_tariff"]')).then(function(){
                        if (typeof dataIn != 'undefined') {
                            //FREAKING SUCCESS! deferred is ANNOYING or awesome ... deferring judgement until data is resolved.
                            thisInstance.applyContractMiscItems(data.result.misc_items);
                            if (jQuery('[name="instance"]').val() != 'sirva') {
                                thisInstance.addFlatRateAutoItems(data.result['flat_rate_auto']);
                                if (data.result['waive_peak_rates'] == 1) {
                                    jQuery('select[name="pricing_type"]').find('option[value="Non Peak"]').prop('selected', true);
                                    jQuery('select[name="pricing_type"]').trigger('liszt:updated'); //.trigger('change').trigger('click');
                                }
                            }
                        }
                        jQuery('input[name="weight"]').trigger('change');
                    }
                    //thisInstance.applyInputLocks();
                    if (data.result['business_line']) {
                        // can't really do this now that business_line is multiselect on Contracts
                        if (data.result['business_line'].split(' |##| ').length == 1) {
                            jQuery('[name="business_line_est"]').val(data.result['business_line']).trigger('liszt:updated').trigger('change');
                        }
                    }
                    if (typeof data.result['effective_tariff'] != 'undefined' && data.result['effective_tariff'] != null && data.result['effective_tariff'].length > 0) {
                        /** Bug #19476 - QIO2 NAT Estimate NAT for Effective Tariff s/b Pricing Tariff and the LOV for this field are wrong
                         **else if(!$('[name="effective_tariff"] option:contains('+ data.result.effective_tariff +')').length){
                                         **  bootbox.alert('The associated tariff for this data is not available for the current estimate owner.'
                                         **      +
                                         **       '<br/>Please choose an owner with access to the '+
                                         **       data.result.effective_tariff+' tariff.');
                                        }**/
                        var updateSelectedTariff = function () {
                            if (data.result['effective_tariff_id'] != jQuery('select[name="effective_tariff"]').val()) {
	                    Estimates_Edit_Js.I().afterTariffLoad(updateData);
                                jQuery('select[name="effective_tariff"]').data('selected-value', data.result['effective_tariff_id']).val(data.result['effective_tariff_id']).trigger('liszt:updated').trigger('value_change');
                            } else {
                                updateData();
                        }
                        Estimates_Edit_Js.setReadonly('effective_tariff', true);
			};
                        if (
                            $('[name="instance"]').val() == 'sirva' &&
                            (
                                data.result.effective_tariff.indexOf('MAX') != -1 ||
                                data.result.move_type.length > 0
                            )
                        ) {
                            if (data.result.effective_tariff.indexOf('MAX') != -1) {
                                data.result.move_type = 'Intrastate';
                            }
                            var movetype = $('[name="move_type"]');
                            movetype.val(data.result.move_type);

                            $.when(movetype.trigger('liszt:updated').trigger('change')).done(function () {
				updateSelectedTariff();
                            });
                        } else {
				updateSelectedTariff();
                        }

                    } else {
                        updateData();
                    }
                }
            };
            if(dataIn)
            {
                // if we have an owner, set it first
                if(Number(dataIn.result.owner) > 0 && dataIn.result.owner != jQuery('[name="agentid"]').val())
                {
                    // currently SIRVA only
                    // relying on updating the agentid field to trigger a tariff picklist update; if that does not happen, resF will never be called
                    Estimates_Edit_Js.I().afterTariffPicklistUpdate(function () {
                        resF(dataIn);
                    });
                    jQuery('[name="agentid"]').val(dataIn.result.owner).trigger('liszt:updated').trigger('change');
                } else {
                    resF(dataIn);
                }
            } else {
                if (jQuery('input[name="contract"]').length && (jQuery('input[name="contract"]').val() != '') && (jQuery('input[name="contract"]').val() != 0)) {
                    var id = jQuery('input[name="contract"]').val();
                    var currentOwner = jQuery('[name="agentid"]').val();
                    var url = 'index.php?module=' + Estimates_Edit_Js.I().moduleName + '&action=PopulateContractData&contract_id=' + id + '&current_owner=' + currentOwner;
                    AppConnector.request(url).then(function (data) {
                            // if we have an owner, set it first
                            if(Number(data.result.owner) > 0 && data.result.owner != currentOwner)
                            {
                                // currently SIRVA only
                                // relying on updating the agentid field to trigger a tariff picklist update; if that does not happen, resF will never be called
                                Estimates_Edit_Js.I().afterTariffPicklistUpdate(function () {
                                    resF(data);
                                });
                                jQuery('[name="agentid"]').val(data.result.owner).trigger('liszt:updated').trigger('change');
                            } else {
                                resF(data);
                            }
                        },
                        function (err) {
                        }
                    );
                }
            }
        },

        showContractRow : function() {
            jQuery('#contract_row').removeClass('hide');
            jQuery('input[name="parent_contract"]').closest('td').children().removeClass('hide');
            jQuery('input[name="parent_contract"]').closest('td').prev('td').children().removeClass('hide');
            jQuery('input[name="nat_account_no"]').closest('td').children().removeClass('hide');
            jQuery('input[name="nat_account_no"]').closest('td').prev('td').children().removeClass('hide');
        },

        removeContractRow : function() {
            //console.dir("Remove Contract information")
            //hide the contract row and remove the values
            jQuery('#contract_row').addClass('hide');
            jQuery('input[name="parent_contract"]').val('');
            jQuery('input[name="parent_contract"]').closest('td').children().addClass('hide');
            jQuery('input[name="parent_contract"]').closest('td').prev('td').children().addClass('hide');
            jQuery('input[name="nat_account_no"]').val('');
            jQuery('input[name="nat_account_no"]').closest('td').children().addClass('hide');
            jQuery('input[name="nat_account_no"]').closest('td').prev('td').children().addClass('hide');
        },

        clearContractFields: function() {
            // hide flat rate auto table
            jQuery('[name="contractFlatRateAuto"]').val('0').trigger('change');

            //remove interstate tariff if it's a thing.
            Estimates_Edit_Js.setReadonly('effective_tariff', false);
        },

        unbindContractItems : function(nullFields) {
            //console.dir("start remove contract row");
            Estimates_Contract_Js.I().clearContractFields();
            Estimates_Contract_Js.I().removeAllContractMiscItems();

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
                    if(fieldsToChange[i] == 'interstate_effective_date')
                    {
                        var today = new Date();
                        Vtiger_Edit_Js.setDate(jQuery('input[name="interstate_effective_date"]'), today);
                        jQuery('input[name="' + fieldsToChange[i] + '"]').prop('readonly', false).prop('disabled', false);
                    } else {
                    jQuery('input[name="' + fieldsToChange[i] + '"]').val('').prop('readonly', false).prop('disabled', false);
                    }
                } else {
                    jQuery('input[name="' + fieldsToChange[i] + '"]').prop('readonly', false).prop('disabled', false);
                }
            }
            //jQuery('input[name="irr_charge"]').attr('readonly', true).val('4');

            //Alf found that this is probably an error.
            //clear the account and contact fields using it's click event! THANK YOU Ryan!
            //jQuery('.Estimates_editView_fieldName_account_id_clear').trigger('click');
            //jQuery('.Estimates_editView_fieldName_contact_id_clear').trigger('click');

            //only remove the valuation and effective tariff IF we are just removing the contract,
            //otherwise leave them alone.
            if (nullFields) {
                //Reset the Valuation to "Select an Option"
                //I'm not hardcoding a default value here...
                //Reset the Effective Tariff to "Select an Option"

                jQuery('select[name="valuation_deductible"]').find('option').each(function () {
                    jQuery(this).prop('selected', false);
                });
                jQuery('[name="contractValuationOverride"]').val('0').trigger('change');

                var workingDiv = jQuery('select[name="valuation_deductible"]').siblings().first();
                workingDiv.find('li.result-selected').removeClass('result-selected');
                workingDiv.find('li:contains("Select an Option")').addClass('result-selected');
                jQuery('select[name="valuation_deductible"]').data('selectedValue', 'Select an Option');
                jQuery('select[name="valuation_deductible"]').trigger('liszt:updated');
                jQuery('select[name="valuation_deductible"]').trigger('change');

                //Reset the Effective Tariff to "Select an Option"
                jQuery('select[name="effective_tariff"]').find('option').each(function () {
                    jQuery(this).prop('selected', false);
                });

                var workingDiv = jQuery('select[name="effective_tariff"]').siblings().first();
                workingDiv.find('li.result-selected').removeClass('result-selected');
                workingDiv.find('li:contains("Select an Option")').addClass('result-selected');
                jQuery('select[name="effective_tariff"]').data('selectedValue', 'Select an Option');
                jQuery('select[name="effective_tariff"]').trigger('liszt:updated');
                jQuery('select[name="effective_tariff"]').trigger('change');
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
                    MiscItems_Edit_Js.I().deleteMiscItem(currentRow, serviceid, rowNum);
                }
            });
        },

        registerEvents : function ()
        {
            var thisInstance = this;
            // this actually sets up the event handler
            this.populateContractData();
            jQuery('.' + Estimates_Edit_Js.I().moduleName + '_editView_fieldName_contract_clear').on(Vtiger_Edit_Js.referenceDeSelectionEvent, function() {
                thisInstance.removeContractRow();
                thisInstance.unbindContractItems(true);
            });
            this.applyContractEnforcement();
        }
    }
);
