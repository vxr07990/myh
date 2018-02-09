/**
 * Created by dbolin on 11/11/2016.
 */

Vtiger_Edit_Js("Estimates_Common_Js", {

    },
    {
        lineItemSavePass: false,
        applyEstimateVisibilityRules : function (isEditView, moduleName){
            var rules = {
                subject : {
                    conditions : [
                        {
                            operator: 'always',
                            targetFields: [
                                {
                                    name: 'cubesheet',
                                    hide: true
                                },
                                {
                                    name: 'total_valuation',
                                    readonly: true
                                }
                            ]
                        }
                    ]
                },
                quotation_type :{
                    conditions:[
                        {
                            operator: 'in',
                            not: true,
                            value: ['Guaranteed', 'Guranteed Not to Exceed'],
                            targetFields: [
                                {
                                    name: 'guaranteed_price',
                                    hide: true,
                                    setValue: ''
                                }
                            ]
                        }
                    ]
                }
            };
            this.applyVisibilityRules(rules, isEditView);

            if(jQuery('input[name="instance"]').val() != 'graebel')
            {
                return;
            }
                var tariff1950B = '1950-B';
                var tariffMMI = 'MMI';
                var tariff400NG = '400NG';
                var tariff400DOE = '400DOE';
                var tariffGSA500A = 'GSA-500A';
            var rules = {
                subject : {
                    conditions : [
                        {
                            operator: 'always',
                            targetFields: [
                                {
                                    name: 'guaranteed_price',
                                    hide: true
                                },
                                {
                                    name: 'linehaul_disc',
                                    hide: true
                                },
                                {
                                    name: 'accessorial_disc',
                                    hide: true
                                },
                                {
                                    name: 'packing_disc',
                                    hide: true
                                },
                                {
                                    name: 'sit_origin_auth_no',
                                    readonly: true,
                                },
                                {
                                    name: 'sit_dest_auth_no',
                                    readonly: true,
                                },
                            ]
                        }
                    ]
                },
                effective_tariff_custom_type : {
                    conditions : [
                        {
                            operator : 'in',
                            value : [tariff400NG, tariffGSA500A, tariff400DOE],
                            targetFields : [
                                {
                                    name : 'irr_charge',
                                    hide : true,
                                },
                            ],
                        },
                        {
                            operator : 'in',
                            not: true,
                            value : [tariff400NG, tariffGSA500A, tariff400DOE],
                            targetFields : [
                                {
                                    name : 'storage_inspection_fee',
                                    hide : true,
                                    setValue: false
                                },
                            ],
                        },
                        {
                            // actually don't think we need this, since Valuation/.../Common.js also handles this case
                            operator: 'is',
                            value: tariff400NG,
                            targetBlocks : [
                                {
                                    label: 'LBL_QUOTES_VALUATION',
                                    hide: true,
                                }
                            ]
                        },
                        {
                            operator : 'is',
                            value : tariff1950B,
                            and : {
                                source: 'interstate_effective_date',
                                operator: 'lt',
                                value: new Date(2017, 0, 1),
                                not: true,
                            },
                            targetFields : [
                                {
                                    name: 'priority_shipping',
                                    hide: true
                                }
                            ]
                        },
                        {
                            operator: 'in',
                            not: true,
                            value: [tariffMMI, tariffGSA500A],
                            targetFields : [
                                {
                                    name: 'exclusive_use_cuft',
                                    hide: true,
                                    setValue: '',
                                },
                                {
                                    name: 'space_reservation',
                                    hide: true,
                                    setValue: false,
                                },
                                {
                                    name: 'space_reservation_cuft',
                                    hide: true,
                                    setValue: '',
                                },
                            ]
                        },
                        {
                            operator: 'is',
                            not: true,
                            value: tariffGSA500A,
                            targetBlocks : [
                                {
                                    label: 'INTERSTATE_SERVICE_CHARGES',
                                    hide: true
                                }
                            ]
                        },
                        {
                            operator: 'is',
                            value: tariffGSA500A,
                            targetFields : [
                                {
                                    name: 'irr_charge',
                                    hide: true,
                                }
                            ]
                        }
                    ],
                },
                small_shipment: {
                    conditions : [
                        {
                            operator: 'is',
                            not: true,
                            value: 'Yes',
                            targetFields : [
                                {
                                    name: 'small_shipment_miles',
                                    hide: true,
                                },
                                {
                                    name: 'small_shipment_ot',
                                    hide: true,
                                    setValue: false,
                                }
                            ]
                        }
                    ]
                },
                storage_inspection_fee : {
                    conditions : [
                        {
                            operator: 'is',
                            value: 'Yes',
                            targetFields: [
                                {
                                    name: 'full_pack',
                                    readonly: true,
                                    setValue: false,
                                },
                                {
                                    name: 'full_unpack',
                                    readonly: true,
                                    setValue: false,
                                }
                            ]
                        }
                    ]
                }
            };

            if(moduleName == 'Actuals')
            {
                rules['business_line_est'] = {
                    conditions: [
                        {
                            operator: 'is',
                            value: 'Interstate Move',
                            and: {
                                source: 'billing_type',
                                operator: 'is',
                                value: 'National Accounts'
                            },
                            targetFields: [
                                {
                                    name: 'contract_display',
                                    mandatory: true,
                                }
                            ]
                        }
                    ]
                };
            }

            this.applyVisibilityRules(rules, isEditView);
        },

        populateOppData : function() {
            var thisInstance = this;

            var id = jQuery('input[name="potential_id"]').val();
            var url = 'index.php?module=' + Estimates_Edit_Js.I().moduleName + '&action=PopulateOppData&potential_id=' + id;

            AppConnector.request(url).then(
                function(data) {
                    var setDateField = function(field, date) {
                        var sel = jQuery('input[name="' + field + '"]');
                        if(sel.length == 0)
                        {
                            return;
                        }
                        var dateFormat = sel.data('date-format');
                        var y = date.substr(0, 4);
                        var m = date.substr(5, 2);
                        var d = date.substr(8);
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
                        sel.val(userDate);
                    }
                    if (data.success) {
                        for(var key in data.result.dates) {
                            if(data.result.dates[key]) {
                                setDateField(key, data.result.dates[key]);
                            }
                        }


                        jQuery('select[name="billing_type"]').find('option:selected').prop('selected', false).closest('select').find('option[value="' + data.result['billing_type'] + '"]').prop('selected', true).closest('select').trigger('liszt:updated');
                        jQuery('select[name="shipper_type"]').find('option:selected').prop('selected', false).closest('select').find('option[value="' + data.result['shipper_type'] + '"]').prop('selected', true).closest('select').trigger('liszt:updated');
                        jQuery('select[name="shipper_type"]').trigger('change');
                        jQuery('select[name="lead_type"]').find('option:selected').prop('selected', false).closest('select').find('option[value="' + data.result['opp_type'] + '"]').prop('selected', true).closest('select').trigger('liszt:updated');
                        jQuery('select[name="move_type"]').find('option:selected').prop('selected', false).closest('select').find('option[value="' + data.result['move_type'] + '"]').prop('selected', true).closest('select').trigger('liszt:updated');

                        //is this needed?
                        jQuery('select[name=""]').find('option:selected').prop('selected', false).closest('select').find('option[value="' + data.result['move_type'] + '"]').prop('selected', true).closest('select').trigger('liszt:updated');

                        //we still need this to hide the fields for Local US
                        jQuery('select[name="move_type"]').trigger('change');

                        if (data.result['move_type'] == 'Sirva Military') {
                            // this is SO WRONG
                            // but I'm not going to fix it
                            jQuery('select[name="effective_tariff"]').find('option').each(function (index, element) {
                                var test = element.text.search('400NG');
                                if (test >= 0) {
                                    jQuery('select[name="effective_tariff"]').find('option[value="'+element.value+'"]').prop('selected', true);
                                    jQuery('select[name="effective_tariff"]').trigger('liszt:updated').trigger('change');//.trigger('mouseup');
                                    return false;
                                }
                            });
                        }

                        jQuery('input[name="bill_city"]').val(data.result.billing['city']);
                        jQuery('input[name="bill_street"]').val(data.result.billing['street']);
                        jQuery('input[name="bill_country"]').val(data.result.billing['country']);
                        jQuery('input[name="bill_state"]').val(data.result.billing['state']);
                        jQuery('input[name="bill_code"]').val(data.result.billing['zip']);
                        jQuery('input[name="bill_pobox"]').val(data.result.billing['pobox']);
                        jQuery('input[name="origin_address1"]').val(data.result.origin['address1']);
                        jQuery('input[name="origin_address2"]').val(data.result.origin['address2']);
                        jQuery('input[name="origin_state"]').val(data.result.origin['state']);
                        jQuery('input[name="origin_city"]').val(data.result.origin['city']);
                        jQuery('input[name="origin_zip"]').val(data.result.origin['zip']);
                        jQuery('input[name="origin_phone1"]').val(data.result.origin['phone1']);
                        jQuery('input[name="origin_phone2"]').val(data.result.origin['phone2']);

                        jQuery('input[name="destination_address1"]').val(data.result.destination['address1']);
                        jQuery('input[name="destination_address2"]').val(data.result.destination['address2']);
                        jQuery('input[name="destination_state"]').val(data.result.destination['state']);
                        jQuery('input[name="destination_city"]').val(data.result.destination['city']);
                        jQuery('input[name="destination_zip"]').val(data.result.destination['zip']);
                        jQuery('input[name="destination_phone1"]').val(data.result.destination['phone1']);
                        jQuery('input[name="destination_phone2"]').val(data.result.destination['phone2']);

                        //Logic for remembering picklist values
                        if (data.result['businessline'] && data.result['businessline'].length > 0) {
                            jQuery('select[name="business_line"]').find('option:selected').prop('selected', false);
                            jQuery('select[name="business_line"]').find('option[value="'+data.result['businessline']+'"]').prop('selected', true);
                            jQuery('select[name="business_line"]').trigger('liszt:updated');
                            jQuery('select[name="business_line"]').trigger('change');

                            jQuery('select[name="business_line_est"]').find('option:selected').prop('selected', false);
                            jQuery('select[name="business_line_est"]').find('option[value="'+data.result['businessline']+'"]').prop('selected', true);
                            jQuery('select[name="business_line_est"]').trigger('liszt:updated');
                            jQuery('select[name="business_line_est"]').trigger('change');
                        }

                        // setReferenceFieldValue();
                        // find this, closest td -> containment parameter
                        // params object property  named id contains account id
                        // property named name has label
                        if(typeof data.result['accountlabel'] !== 'undefined'){ //added check to stop setting these to nothing.
                            var container = jQuery('#account_id_display').closest('td');
                            var obj = {id:data.result['accountid'], name:data.result['accountlabel'], suppress:true};

                            thisInstance.setReferenceFieldValue(container, obj);
                        }
                        if(typeof data.result['contactlabel'] !== 'undefined'
                            && data.result['contactid']) { //added check to stop setting these to nothing.
                            var contactName = jQuery('#contact_id_display').closest('td');
                            var contactObj = {id:data.result['contactid'],name:data.result['contactlabel'], suppress:true};

                            thisInstance.setReferenceFieldValue(contactName, contactObj);
                        }

                        //this is old but leaving it in case stops need brought back to estimates
                        var stopsRows = data.result['stops_rows'];

                        //we need to kill the existing stopsRows data
                        //jQuery('.stopBlock').not('.hide').html('');
                        jQuery('.stopBlock').not('.hide').each(function() {
                            $(this).html('');
                        });

                        //reset the number of stops
                        jQuery('#numStops').val(0);

                        //make and set the fields for the new stops
                        var stopsBlockInstance = ExtraStops_EditBlock_Js.getInstance();
                        for(i=0; i<stopsRows.length; i++){
                            var newStop = stopsBlockInstance.addGuestRecord(stopsBlockInstance, 'ExtraStops');
                            var stopNumber = jQuery('#numExtraStops').val();
                            //regular fields
                            newStop.find('input[name="extrastops_name_'+stopNumber+'"]').val(stopsRows[i]['extrastops_name']);

                            if(jQuery('[name="instance"]').val() == 'sirva')
                            {
                                newStop.find('select[name="extrastops_sequence_'+stopNumber+'"]').val(stopsRows[i]['extrastops_sequence']).prop('disabled', true).trigger("liszt:updated");
                            } else {
                                newStop.find('input[name="extrastops_sequence_'+stopNumber+'"]').val(stopsRows[i]['extrastops_sequence']);
                            }
                            newStop.find('input[name="extrastops_weight_'+stopNumber+'"]').val(stopsRows[i]['extrastops_weight']);
                            newStop.find('input[name="extrastops_address1_'+stopNumber+'"]').val(stopsRows[i]['extrastops_address1']);
                            newStop.find('input[name="extrastops_address2_'+stopNumber+'"]').val(stopsRows[i]['extrastops_address2']);
                            newStop.find('input[name="extrastops_phone1_'+stopNumber+'"]').val(stopsRows[i]['extrastops_phone1']);
                            newStop.find('input[name="extrastops_phone2_'+stopNumber+'"]').val(stopsRows[i]['extrastops_phone2']);
                            newStop.find('input[name="extrastops_city_'+stopNumber+'"]').val(stopsRows[i]['extrastops_city']);
                            newStop.find('input[name="extrastops_state_'+stopNumber+'"]').val(stopsRows[i]['extrastops_state']);
                            newStop.find('input[name="extrastops_zip_'+stopNumber+'"]').val(stopsRows[i]['extrastops_zip']);
                            newStop.find('input[name="extrastops_country_'+stopNumber+'"]').val(stopsRows[i]['extrastops_country']);
                            newStop.find('input[name="extrastops_date_'+stopNumber+'"]').val(stopsRows[i]['extrastops_date']);
                            //checkbox
                            if(stopsRows[i]['extrastops_isprimary'] == 1 || stopsRows[i]['extrastops_isprimary'] == 'on' || stopsRows[i]['extrastops_isprimary'] == true){
                                newStop.find('input[name="extrastops_isprimary_'+stopNumber+'"]').prop('checked', true);
                            }
                            //UI type 10
                            thisInstance.setReferenceFieldValue(newStop.find('input[name="extrastops_contact_'+stopNumber+'"]').closest('td'), {id: stopsRows[i]['extrastops_contact'], name: stopsRows[i]['stop_contact_name']});
                            //picklists
                            picklistArray = [
                                'extrastops_description',
                                'extrastops_sirvastoptype',
                                'extrastops_phonetype1',
                                'extrastops_phonetype2',
                                'extrastops_type'
                            ];
                            for(j=0; j < picklistArray.length; j++){
                                if(jQuery('[name="instance"]').val() == 'sirva') {
                                    newStop.find('select[name="'+picklistArray[j]+'_'+stopNumber+'"] > option[value="' + stopsRows[i][picklistArray[j]] + '"]').prop('selected', true).closest('select').prop('disabled', true).trigger('liszt:updated')
                                } else {
                                    newStop.find('select[name="'+picklistArray[j]+'_'+stopNumber+'"] > option[value="' + stopsRows[i][picklistArray[j]] + '"]').prop('selected', true).closest('select').trigger('liszt:updated');
                                }
                            }
                            if(jQuery('[name="instance"]').val() == 'sirva') {
                                newStop.find('input, select').prop("readonly",true);
                            }
                        }
                    }
                },
                function(err) {

                }
            );
        },

        registerPopulateOppDataOnChange : function() {
            var thisInstance = this;
            jQuery('.contentsDiv').on('value_change', 'input[name="potential_id"]', function() {
                if(!jQuery(this).val())
                {
                    return;
                }
                var message = 'Would you like to load data from the Opportunity?';
                Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
                    function () {
                        //need to do the populate here
                        thisInstance.populateOppData();
                    },
                    function(error, err) {
                        //they pressed no don't populate the data.
                    }
                );
            });
        },


        registerLineItemSaveEvent : function()
        {
            var thisInstance = this;
            var form = this.getForm();
            form.on(Vtiger_Edit_Js.recordPreSave, function(e, data) {

                if(thisInstance.lineItemSavePass)
                {
                    return true;
                }

                if(typeof form.data('lineitem-submit') != "undefined") {
                    e.preventDefault();
                    return false;
                }
                form.data('lineitem-submit', 'true');
                e.preventDefault();

                var highestRowIndex = 0;
                jQuery('table.lineItemsEdit tr').not('.defaultLineItemRow, .innerRow').each(function () {
                    if (jQuery('input[name="rowNumber"]', this).val() >= highestRowIndex) {
                        highestRowIndex = Number(jQuery('input[name="rowNumber"]', this).val()) + 1;
                    }
                });
                // save rerate in detail view
                var params = new Object();
                params.url = 'index.php?module=Estimates&action=CheckLineItemsSave&record=' + getQueryVariable('record');
                params.data = new Object();
                jQuery('input').each(function () {
                    if(jQuery(this).is(':checkbox'))
                    {
                        if(jQuery(this).attr('checked')) {
                            params.data[jQuery(this).attr('name')] = jQuery(this).val();
                        }
                    } else {
                        params.data[jQuery(this).attr('name')] = jQuery(this).val();
                    }
                });
                params.data['detailLineItemCount'] = highestRowIndex;
                AppConnector.request(params).then(function(data) {
                    form.removeData('lineitem-submit');
                    if(typeof data == 'string')
                    {
                        data = JSON.parse(data);
                    }
                    if(data.success)
                    {
                        if(data.result)
                        {
                            Estimates_Edit_Js.I().showAlertBox({'message': data.result + ' before a line item can be marked as ready to distribute.'})
                        } else {
                            thisInstance.lineItemSavePass = true;
                            // so confused why we have to do this
                            form.removeClass('validating');
                            form.submit();
                        }
                    }
                });
            });
        },

        registerEvents : function(isEditView, moduleName)
        {
            this.applyEstimateVisibilityRules(isEditView, moduleName);
            this.registerPopulateOppDataOnChange();
            if(jQuery('[name="instance"]').val() == 'graebel')
            {
                this.registerLineItemSaveEvent();
            }
        }
    }
);
