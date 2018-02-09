/**
 * Created by dbolin on 1/3/2017.
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
            if(jQuery('[name="movehq"]').val()){
                var rules = {
                    commodities: {
                        conditions: [
                            {
                                operator: 'is',
                                not:true,
                                value: 'Auto',
                                targetBlocks: [
                                    {
                                        label: 'VehicleLookup',
                                        hide: true,
                                    }
                                ]
                            },
                        ]
                    }
                };
            } else {
                var rules = {
                    business_line_est: {
                        conditions: [
                            {
                                operator: 'is',
                                not: true,
                                value: 'Auto Transportation',
                                targetBlocks: [
                                    {
                                        label: 'VehicleLookup',
                                        hide: true,
                                    }
                                ]
                            },
                        ]
                    }
                };
            }
            rules.valuation_deductible = {
                conditions: [
                    {
                        operator: 'is',
                        not: true,
                        value: 'Full Value Protection',
                        targetFields: [
                            {
                                name: 'valuation_deductible_amount',
                                hide: true
                            },
                            {
                                name: 'valuation_amount',
                                hide: true
                            },
                            {
                                name: 'additional_valuation',
                                hide: true
                            },
                            {
                                name: 'valuation_discounted',
                                hide: true
                            }
                        ]
                    },
                    {
                        operator: 'is',
                        not: true,
                        value: 'Released Valuation',
                        targetFields: [
                            {
                                name: 'valuation_deductible_amount'
                            },
                            {
                                name: 'valuation_amount'
                            },
                            {
                                name: 'additional_valuation'
                            },
                            {
                                name: 'valuation_discounted'
                            }
                        ]
                    }
                ]
            }
            this.applyVisibilityRules(rules, isEditView);
        },

        updateEffectiveTariffPicklist: function() {
            var data = Estimates_Edit_Js.I().effectiveTariffData;
            var res = {};
            // National Account ??
            var currentBusinessLine = jQuery('[name="business_line_est"]').val();
            var currentCommodity = jQuery('[name="commodities"]').val();
            var allowInterstate = ['Interstate Move', 'Interstate','Intrastate Move', 'Intrastate', 'International', 'HHG - International Air', 'HHG - International Sea',
                    'HHG - International Surface', 'International Land', 'Auto Transportation'].indexOf(currentBusinessLine) >= 0;
            var allowIntrastate = ['Intrastate Move', 'Intrastate'].indexOf(currentBusinessLine) >= 0;
            var allowLocal = ['Local Move', 'Local','Commercial - Distribution', 'Commercial - International Air',
                    'Commercial - Record Storage', 'Commercial - Storage', 'Commercial - Asset Management',
                    'Commercial - Project', 'Work Space - MAC', 'Work Space - Special Services',
                    'Work Space - Commodities',
                    //OT4883 - Display tariffs module entries for Interstate when business lines match
                    'Interstate Move', 'Interstate','Intrastate Move', 'Intrastate', 'International', 'HHG - International Air', 'HHG - International Sea',
                    'HHG - International Surface', 'International Land', 'Auto Transportation'].indexOf(currentBusinessLine) >= 0;

            for(var k in data)
            {
                var d = data[k];
                if(d['is_managed_tariff'])
                {
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
                    if(!allowLocal)
                    {
                        continue;
                    }

                    if(currentCommodity != '' && Array.isArray(d['restricted_commodities']) && d['restricted_commodities'].indexOf(currentCommodity) == -1){
                        continue;
                    }

                    if(currentBusinessLine != '' && Array.isArray(d['restricted_business_lines']) && d['restricted_business_lines'].indexOf(currentBusinessLine) == -1){
                        continue;
                    }

                }
                res[d['tariff_id']] = d['tariff_name'];
            }
            Vtiger_Edit_Js.setPicklistOptions('effective_tariff', res);
            Estimates_Edit_Js.I().doAfterTariffPicklistUpdate();
        },

        getBlocksToLoad: function(loadBlocks, prevTariff, newTariff, tariffData)
        {
            if(tariffData['tariff_name'] == 'Auto Transport')
            {
                loadBlocks.length = 0;
                loadBlocks.push('VehicleLookup');
            }
        },

        getShowQuery: function(query, isInterstate, newTariff, tariffData)
        {
            if(isInterstate && tariffData['tariff_name'] == 'Auto Transport')
            {
                return '#contentHolder_VehicleLookup,.sectionContentHolder:not(.interstateContent,.localMoveContent)';
            }
            return query;
        },

        postReloadContents: function(isInterstate)
        {
            this.registerLocalBottomLineDiscountChangeEvent();
            this.updateSectionDiscounts();
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

        updateSectionDiscounts: function() {
            var localBLDiscountElement = jQuery('[name="local_bl_discount"]');
            var blDiscountPrevious = localBLDiscountElement.data('prev');
            var blDiscount = localBLDiscountElement.val();
            jQuery('[name^="SectionDiscount"]').each(function () {
                var foundSectionDiscount = jQuery(this);
                var disabled = foundSectionDiscount.attr('disabled');
                var readonly = foundSectionDiscount.attr('readonly');
                var sectionValue = foundSectionDiscount.val();

                if (typeof disabled != 'undefined') {
                    // Skip disabled inputs.
                    return;
                }

                if (typeof readonly != 'undefined') {
                    //Always override a readonly section value.
                    foundSectionDiscount.val(blDiscount);
                    return;
                }
                //@TODO: this might be incorrect user sense.
                if (
                    sectionValue &&
                    //So if the user enterable value should NEVER update once initially set remove this part of the conditional
                    Number(sectionValue) != Number(blDiscountPrevious)
                ) {
                    //Don't override the section value iff it's already set.
                    return;
                }
                foundSectionDiscount.val(blDiscount);
            });
        },

        registerLocalBottomLineDiscountChangeEvent: function() {
            var thisInstance = this;
            jQuery('[name="local_bl_discount"]').on('focusin', function () {
                jQuery(this).data('prev',jQuery(this).val());
            });
            jQuery('[name="local_bl_discount"]').on('value_change', thisInstance.updateSectionDiscounts);
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
                //this.registerCustomTariffTypeChangeEvent();
                this.registerAgentChangeEvent();
                this.postReloadContents();
            }
            this.registerRules(isEditView);
        }
    }
);
