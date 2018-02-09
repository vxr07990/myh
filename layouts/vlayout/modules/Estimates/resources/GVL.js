/**
 * Created by dbolin on 12/22/2016.
 */
Vtiger_Edit_Js("Estimates_Customer_Js", {

        getInstance: function() {
            if(Estimates_Customer_Js.currentInstance)
            {
                return Estimates_Customer_Js.currentInstance;
            }
            Estimates_Customer_Js.currentInstance = new Estimates_Customer_Js();
            return Estimates_Customer_Js.currentInstance;
        },
        I: function(){
            return Estimates_Customer_Js.getInstance();
        },
    },
    {

        registerRules: function (isEditView)
        {
            var tariffGSA500A = 'GSA-500A';
            var tariffGSA01 = 'GSA01';
            var tariff1950B = '1950-B';
            var tariff400N104G = '400N/104G';
            var rules = {
                subject: {
                    conditions: [
                        {
                            operator: 'always',
                            targetFields: [
                                {
                                    name: 'apply_free_fvp',
                                    hide: true,
                                }
                            ]
                        }
                    ]
                },
                contractFlatRateAuto: {
                    conditions: [
                        {
                            operator: 'lt',
                            value: '1',
                            targetBlocks: [
                                {
                                    label: 'FLAT_RATE_AUTO',
                                    hide: true,
                                }
                            ]
                        }
                    ]
                },
                effective_tariff_custom_type: {
                    conditions: [
                        {
                            operator: 'in',
                            not: true,
                            value: [tariffGSA500A, tariffGSA01],
                            targetFields: [
                                {
                                    name: 'interstate_mileage',
                                    readonly: true
                                }
                            ]
                        },
                        {
                            operator: 'in',
                            not: true,
                            value: [tariff1950B, tariff400N104G],
                            targetBlocks: [
                                {
                                    label: 'LBL_QUOTES_TRANSPORTATIONPRICING',
                                    hide: true,
                                }
                            ]
                        }
                    ]
                }
            };
            this.applyVisibilityRules(rules, isEditView);
        },

        applyCustomTariffVisibility : function (){
            // TODO: move a bunch of this to a rules object
            var customTariffType = jQuery('#effective_tariff_custom_type').val();
            // No containers for the following tariffs
            if (customTariffType == 'RMX400'
                || customTariffType == 'RMW400'
                || customTariffType == '09CapRelo'
                || customTariffType == '400N Base') {
                jQuery('.containerCell').addClass('hide').val('0');
            }
            else {
                jQuery('.containerCell').removeClass('hide');
            }

            if (customTariffType == 'GSA-500A') {
                jQuery('tr[id^="gsa500"]').removeClass('hide');
            } else {
                jQuery('tr[id^="gsa500"]').addClass('hide');
            }

            if (customTariffType == '1950-B' || customTariffType == '400N/104G' || customTariffType == 'GSA01') {
                //1950-B show
                //show upholstery fine finish block
                jQuery('table[name="UpholsteryFineFinishTable"]').removeClass('hide');

                if (customTariffType == '1950-B' || customTariffType == 'GSA01') {
                    if (customTariffType == 'GSA01') {
                        Vtiger_Edit_Js.setReadonly('billed_weight', false);
                        Vtiger_Edit_Js.setReadonly('guaranteed_price', false);
                        Vtiger_Edit_Js.setReadonly('accesorial_fuel_surcharge', false);
                        jQuery('#interstateRateQuick').prop('disabled', true);
                        jQuery('.interstateRateDetail').prop('disabled', true);
                    } else {
                        jQuery('[name="sit_origin_weight"]').prop('readonly', true).val(jQuery('#' + app.currentPageController.moduleName + '_editView_fieldName_billed_weight').val());
                        Vtiger_Edit_Js.setReadonly('billed_weight', true);
                        Vtiger_Edit_Js.setReadonly('guaranteed_price', true);
                        Vtiger_Edit_Js.setReadonly('accesorial_fuel_surcharge', true);
                        jQuery('#interstateRateQuick').prop('disabled', false);
                        jQuery('.interstateRateDetail').prop('disabled', false);
                    }
                    //hide non-applicable discount fields
                    jQuery('input[name="accessorial_disc"],input[name="linehaul_disc"],input[name="packing_disc"]').each(function () {
                        jQuery(this).closest('td').children().each(function () {
                            jQuery(this).addClass('hide');
                        });
                        jQuery(this).closest('td').prev('td').children().each(function () {
                            jQuery(this).addClass('hide');
                        })
                    });
                    //show crate discount field
                    jQuery('input[name="crating_disc"]').closest('td').children().each(function () {
                        jQuery(this).removeClass('hide');
                    })
                    jQuery('input[name="crating_disc"]').closest('td').prev('td').children().each(function () {
                        jQuery(this).removeClass('hide');
                    });
                }
            } else {
                if (customTariffType != '1950-B' && customTariffType != 'GSA01') {
                    if (customTariffType != 'GSA01') {
                        Vtiger_Edit_Js.setReadonly('billed_weight', true);
                        Vtiger_Edit_Js.setReadonly('guaranteed_price', true);
                        Vtiger_Edit_Js.setReadonly('accesorial_fuel_surcharge', true);
                        jQuery('#interstateRateQuick').prop('disabled', false);
                        jQuery('.interstateRateDetail').prop('disabled', false);
                    }
                    if (customTariffType != '400N/104G') {
                        jQuery('table[name="UpholsteryFineFinishTable"]').addClass('hide');
                    }

                    jQuery('[name="sit_origin_weight"]').prop('readonly', false);
                    jQuery('input[name="crating_disc"]').closest('td').children().each(function () {
                        jQuery(this).addClass('hide');
                    })
                    jQuery('input[name="crating_disc"]').closest('td').prev('td').children().each(function () {
                        jQuery(this).addClass('hide');
                    });
                    //restore hidden discount fields
                    jQuery('input[name="accessorial_disc"],input[name="linehaul_disc"],input[name="packing_disc"]').each(function () {
                        jQuery(this).closest('td').children().each(function () {
                            jQuery(this).removeClass('hide');
                        });
                        jQuery(this).closest('td').prev('td').children().each(function () {
                            jQuery(this).removeClass('hide');
                        })
                    });
                }
            }
        },

        registerCustomTariffTypeChangeEvent: function()
        {
            var thisInstance = this;
            jQuery('.contentsDiv').on('value_change', '#effective_tariff_custom_type', function() {
                thisInstance.applyCustomTariffVisibility();
            });
        },

        registerZipChangeEvent : function() {
            var thisInstance = this;
            jQuery('.contentsDiv').on('value_change', 'input[name="origin_zip"], input[name="destination_zip"]', function() {
                var tariffType = jQuery('#effective_tariff_custom_type').val();
                if(tariffType != 'GSA-500A')
                {
                    return;
                }
                //Reach out to server for a list of service charges applicable to the changed zip
                var new_zip  = jQuery(this).val();
                var is_dest  = jQuery(this).attr('name') == "destination_zip" ? 1 : 0;
                var tariffid = jQuery('[name="effective_tariff"]').val();
                var effDate  = jQuery('[name="interstate_effective_date"]').val();
                var dateFormat = jQuery('[name="interstate_effective_date"]').data('date-format');
                var owner = jQuery('[name="agentid"]').val();
                var dataURL  = 'index.php?module=Estimates&action=GetServiceCharges&zip=' + new_zip + '&is_dest=' + is_dest + '&tariffid=' + tariffid + '&effective_date=' + effDate + '&date_format=' + dateFormat + '&owner=' + owner;
                AppConnector.request(dataURL).then(
                    function(data) {
                        if(data.success) {
                            if(is_dest) {
                                //Clear out destination charges
                                jQuery('#destinationServiceCharges').find('.interstateServiceChargeRow').remove();
                                jQuery('#destinationServiceCharges').append(data.result);
                            } else {
                                //Clear out origin charges
                                jQuery('#originServiceCharges').find('.interstateServiceChargeRow').remove();
                                jQuery('#originServiceCharges').append(data.result);
                            }
                        }
                    }
                );
            });
        },

        registerAdditionalValuationEvents : function()
        {
            jQuery('.contentsDiv').on('value_change', '[name="valuation_deductible"], [name="valuation_deductible_amount"], [name="effective_tariff"]'
                , function() {
                    Estimates_Edit_Js.I().ValuationJS.enforceMinimumValuation();
                });
        },

        registerGetLocalMileageButton: function () {
            jQuery('.contentsDiv').on('click', '.localRateMileage', function() {
                jQuery('.localRateMileage').addClass('hide');
                jQuery('.localRateMileage').closest('td').progressIndicator();
                var destZip = jQuery('input[name="destination_zip"]').val();
                var originZip = jQuery('input[name="origin_zip"]').val();
                var localTariff = jQuery('select[name="effective_tariff"]').val();

                if(destZip=='' || localTariff=='' || originZip == '') {
                    var msg = 'The following errors have prevented the mileage lookup:<br>';
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

                    Estimates_Edit_Js.I().showAlertBox({'message' : msg});
                    jQuery('.localRateMileage').removeClass('hide');
                    jQuery('.localRateMileage').closest('td').progressIndicator({'mode': 'hide'});
                } else {
                    var params = new Object();
                    params.url = 'index.php?module=Estimates&action=GetMileage';
                    params.data = new Object();
                    params.data.EffectiveTariff = jQuery('[name="effective_tariff"]').val();
                    params.data.BusinessLine = jQuery('[name="business_line_est"]').val();
                    params.data.Owner = jQuery('[name="agentid"]').val();
                    params.data.EffectiveDate = jQuery('[name="effective_date"]').val();
                    params.data.OriginZip = jQuery('[name="origin_zip"]').val();
                    params.data.DestinationZip = jQuery('[name="destination_zip"]').val();

                    AppConnector.request(params).then(function(data) {
                        if (data.success != false) {
                            jQuery('[name="localmove_mileage"]').val(data.result.Mileage);
                        }
                        jQuery('.localRateMileage').removeClass('hide');
                        jQuery('.localRateMileage').closest('td').progressIndicator({'mode': 'hide'});
                    });
                }
            });
        },

        updateEffectiveTariffPicklist: function() {
            var data = Estimates_Edit_Js.I().effectiveTariffData;
            var res = [];
            // National Account ??
            var currentBusinessLine = jQuery('[name="business_line_est"]').val();
            var allowInterstate = ['Interstate Move', 'Intrastate Move', 'HHG - International Air', 'HHG - International Sea',
                    'HHG - International Surface', 'International Land', 'Interstate - Foreign Hauler'].indexOf(currentBusinessLine) >= 0;
            var allowLocal = ['Local Move','Intrastate Move','Commercial - Distribution', 'Commercial - International Air',
                    'Commercial - Record Storage', 'Commercial - Storage', 'Commercial - Asset Management',
                    'Commercial - Project', 'Work Space - MAC', 'Work Space - Special Services',
                    'Work Space - Commodities'].indexOf(currentBusinessLine) >= 0;
            var allowIntrastate = allowInterstate || allowLocal;

            for(var k in data)
            {
                var d = data[k];
                if(d['is_managed_tariff'])
                {
                    if(currentBusinessLine == 'Interstate - Foreign Hauler' &&
                        d['custom_tariff_type'] != '400NG')
                    {
                        continue;
                    }
                    if(d['is_intrastate'])
                    {
                        if(!allowIntrastate) {
                            continue;
                        }
                    } else {
                        if(!allowInterstate) {
                            continue;
                        }
                    }
                } else {
                    continue;
                }
                res.push(
                    {
                        text: d['tariff_name'],
                        value: d['tariff_id'],
                    }
                    );
            }
            for(var k in data)
            {
                var d = data[k];
                if(!d['is_managed_tariff'])
                {
                    if(d['restricted_business_lines'].length > 0)
                    {
                        if(d['restricted_business_lines'].indexOf(currentBusinessLine) < 0) {
                            continue;
                        }
                    }
                    else if(!allowLocal)
                    {
                        continue;
                    }
                } else {
                    continue;
                }
                res.push(
                    {
                        text: d['tariff_name'],
                        value: d['tariff_id'],
                    }
                );
            }
            Vtiger_Edit_Js.setPicklistOptions('effective_tariff', res);
            Estimates_Edit_Js.I().doAfterTariffPicklistUpdate();
        },

        getBlocksToLoad: function(loadBlocks, prevTariff, newTariff, tariffData)
        {
            if(tariffData['is_managed_tariff'])
            {
                // always reload packing for GVL
                loadBlocks.push('INTERSTATE_MISC_CHARGES');
            }
        },

        postReloadContents: function(isInterstate, prevTariff, newTariff, tariffData)
        {
            if(isInterstate)
            {
                this.applyCustomTariffVisibility();
                if(['1950-B', 'MSI', 'MMI', '09CapRelo', '400N Base', '400N/104G'].indexOf(tariffData['custom_tariff_type']) >= 0)
                {
                    jQuery('[name="irr_charge"]').val('4');
                } else {
                    jQuery('[name="irr_charge"]').val('');
                }
            }
        },

        loadCustomJavascript: function()
        {
            Estimates_Edit_Js.I().currentTariff = Estimates_BaseTariff_Js.getInstance();
            Estimates_Edit_Js.I().currentTariff.initialize();
        },

        updateAgent: function(agent, first) {
            var thisInstance = this;
            if(!first) {
                var params = {
                    module: 'Estimates',
                    action: 'GetAllowedTariffsForUser',
                    owner: agent,
                };
                AppConnector.request(params).then(
                    function (data) {
                        if (data.success) {
                            Estimates_Edit_Js.I().effectiveTariffData = data.result;
                            thisInstance.updateEffectiveTariffPicklist();
                        }
                    }
                );
            }
        },

        registerAgentChangeEvent: function() {
            var thisInstance = this;
            var fn = function (e, first) {
                thisInstance.updateAgent(jQuery(this).val(), first);
            };
            jQuery('.contentsDiv').on('value_change', '[name="agentid"]', fn);
            fn.call(jQuery('[name="agentid"]'), null, true);
        },

        registerEvents: function(isEditView)
        {
            if(isEditView) {
                this.registerCustomTariffTypeChangeEvent();
                this.registerZipChangeEvent();
                this.registerAdditionalValuationEvents();
                this.registerGetLocalMileageButton();
                //this.registerAgentChangeEvent();
            }
            this.applyCustomTariffVisibility();
            this.registerRules(isEditView);
        }
    }
);

