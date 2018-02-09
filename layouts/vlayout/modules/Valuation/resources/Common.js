/**
 * Created by dbolin on 11/9/2016.
 */

Vtiger_Edit_Js("Valuation_Common_Js", {

    getValuationWeightFactor: function()
    {
        if(typeof contract_MinValPerLb != 'undefined')
        {
            return contract_MinValPerLb;
        } else {
            var weightFactor = jQuery('input[name="min_declared_value_mult"]').val();
            var weightFactorExists = typeof weightFactor !== 'undefined' && weightFactor != '' && weightFactor > 0;
            if(weightFactorExists)
            {
                return weightFactor;
            }
        }
        return undefined;
    },

    },
    {
        applyValuationVisibilityRules : function (isEditView) {
            var rules = {
                valuation_deductible : {
                    conditions : [
                        {
                            operator: 'is',
                            value: '60¢ /lb.',
                            targetFields: [
                                {
                                    name: 'valuation_amount',
                                    readonly: true
                                }
                            ]
                        }
                    ]
                },
                apply_free_fvp: {
                    conditions: [
                        {
                            operator: 'is',
                            not: true,
                            value: 'Yes',
                            targetFields: [
                                {
                                    name: 'free_valuation_type',
                                    hide: true
                                },
                                {
                                    name: 'rate_per_100',
                                    hide: true
                                },
                                {
                                    name: 'valuation_flat_charge',
                                    hide: true
                                },
                                {
                                    name: 'declared_value',
                                    hide: true
                                },
                                {
                                    name: 'free_valuation_limit',
                                    hide: true
                                },
                                {
                                    name: 'min_declared_value_mult',
                                    hide: true
                                },
                                {
                                    name: 'increased_base',
                                    hide: true
                                },
                            ]
                        }
                    ]
                },
                free_valuation_type: {
                    conditions: [
                        {
                            operator: 'is',
                            not: true,
                            value: 'Flat Charge',
                            targetFields: [
                                {
                                    name: 'valuation_flat_charge',
                                    readonly:true,
                                }
                            ]
                        },
                        {
                            operator: 'is',
                            not: true,
                            value: 'Increased Base Liability',
                            targetFields: [
                                {
                                    name: 'increased_base',
                                    readonly:true,
                                }
                            ]
                        },
                    ]
                }
            };
            this.applyVisibilityRules(rules, isEditView);

            // I'd rather be able to read the non-graebel stuff without scrolling into a behemoth, tbh.
            if(jQuery('input[name="instance"]').val() == 'graebel')
            {
                this.graebelVisibilityRules();
            }
        },

        graebelVisibilityRules: function() {
            if(jQuery('input[name="instance"]').val() != 'graebel')
            {
                return;
            }
            var tariff1950B = '1950-B';
            var tariff400NG = '400NG';
            var tariff400DOE = '400DOE';
            var tariff400N = '400N Base';
            var tariffMMI = 'MMI';
            var tariffMSI = 'MSI';
            var tariff400N104G = '400N/104G';
            var tariffAIReS = 'AIReS';
            var tariffRMX400 = 'RMX400';
            var tariffRMW400 = 'RMW400';
            var tariffISRS200A = 'ISRS200-A';
            var tariffCapRelo = '09CapRelo';
            var tariffGSA01 = 'GSA01';
            var tariffGSA500A = 'GSA-500A';
            if(valuationModuleName == 'Orders')
            {
                var contractName = 'account_contract';
            } else {
                var contractName = 'contract';
            }
            var rules = {};
            if(valuationModuleName == 'Orders') {
                rules[contractName] = {
                    conditions: [
                        {
                            operator: 'gt',
                            value: '0',
                            targetFields: [
                                {
                                    name: 'valuation_deductible',
                                    pickListOptions: ['Carrier Based Liability', 'Replacement Value Protection']
                                },
                                {
                                    name: 'valuation_discounted',
                                    hide: true
                                },
                            ]
                        }
                    ]
                }
            }
            rules['contractValuationOverride'] = {
                conditions: [
                    {
                        operator: 'gt',
                        value: '0',
                        targetFields: [
                            {
                                name: 'valuation_deductible',
                                pickListOptions: ['Carrier Based Liability', 'Replacement Value Protection'],
                                readonly: true
                            },
                        ]
                    }
                ]
            };
            rules['business_line'] = {
                    conditions : [
                        {
                            operator : 'contains',
                            value : 'Work Space',
                            or: {
                                source: 'business_line',
                                operator: 'contains',
                                value: 'Commercial'
                            },
                            targetFields: [
                                {
                                    name : 'valuation_amount',
                                    hide : true,
                                    setValue : '0.00'
                                },
                                {
                                    name : 'additional_valuation',
                                    hide : true,
                                    setValue : '0.00'
                                },
                                {
                                    name : 'valuation_discounted',
                                    hide : true,
                                    setValue : false,
                                },
                                {
                                    name : 'total_valuation',
                                    hide : true,
                                },
                                {
                                    name : 'valuation_deductible_amount',
                                    hide : true,
                                    setValue : 'Select an Option',
                                },
                                {
                                    name : 'valuation_deductible',
                                    pickListOptions : [
                                        'Carrier Based Liability',
                                        'Replacement Value Protection',
                                        'Special RVP'
                                    ],
                                }
                            ],
                        },
                        {
                            operator : 'contains',
                            not : true,
                            value : 'Work Space',
                            and: {
                                source: 'business_line',
                                operator: 'contains',
                                not: true,
                                value: 'Commercial'
                            },
                            targetFields : [
                                {
                                    name : 'valuation_declared_value',
                                    hide : true,
                                },
                            ],
                        },
                    ],
                };
            rules['valuation_deductible'] = {
                    conditions : [
                        {
                            operator : 'in',
                            not : true,
                            value : ['Full Value Protection','Full Replacement Value','Free FVP'],
                            targetFields : [
                                {
                                    name : 'valuation_deductible_amount',
                                    hide : true,
                                    setValue : 'Select an Option',
                                },
                            ],
                        },
                        {
                            operator : 'is',
                            value : 'MMI Released Value',
                            targetFields : [
                                {
                                    name : 'valuation_amount',
                                    hide : true,
                                },
                                {
                                    name : 'valuation_total',
                                    hide : true,
                                },
                            ],
                        },
                        {
                            operator : 'is',
                            value : 'MSI Released Value',
                            targetFields : [
                                {
                                    name : 'valuation_amount',
                                    hide : true,
                                },
                                {
                                    name : 'valuation_total',
                                    hide : true,
                                },
                            ],
                        },
                    ],
                };
            rules['effective_tariff_custom_type'] = {
                conditions : [
                    {
                        operator : 'is',
                        value : tariff400NG,
                        // also don't show valuation block for local tariffs
                        or: {
                            source: 'effective_tariff_custom_type',
                            operator: 'is',
                            value: ''
                        },
                        targetFields : [
                            {
                                name: 'valuation_deductible',
                                hide: true,
                                setValue: 'Select an Option',
                                pickListOptions: [],
                            }
                        ],
                        targetBlocks : [
                            {
                                label: 'LBL_QUOTES_VALUATION',
                                hide: true,
                            }
                        ]
                    },
                    {
                        operator: 'in',
                        value: [tariff1950B, tariff400N, tariff400N104G, tariffAIReS, tariffRMX400, tariffRMW400, tariffISRS200A, tariffGSA01],
                        targetFields : [
                            {
                                name : 'valuation_deductible',
                                pickListOptions : ['Full Replacement Value', 'Carrier Based Liability']
                            }
                        ]
                    },
                    {
                        operator: 'is',
                        value: tariffMMI,
                        targetFields : [
                            {
                                name : 'valuation_deductible',
                                pickListOptions : {
                                    'MMI RVP' : 'Replacement Value Protection',
                                    'MMI Released Value' : 'Released Value'
                                }
                            },
                            {
                                name : 'additional_valuation',
                                hide: true,
                                setValue : 0.00,
                            },
                            {
                                name : 'valuation_deductible_amount',
                                hide: true,
                                setValue : 'Select an Option',
                            }
                        ]
                    },
                    {
                        operator: 'is',
                        value: tariffMSI,
                        targetFields : [
                            {
                                name : 'valuation_deductible',
                                pickListOptions : {
                                    'MSI FVR' : 'Full Value Replacement',
                                    'MSI Released Value' : 'Released Value'
                                }
                            },
                            {
                                name : 'additional_valuation',
                                hide : true,
                                setValue : 0.00,
                            }
                        ]
                    },
                    {
                        operator: 'is',
                        value: tariffCapRelo,
                        targetFields : [
                            {
                                name : 'valuation_deductible',
                                pickListOptions : {
                                    'CapRelo FVP' : 'Full Value Protection',
                                }
                            }
                        ]
                    },
                    {
                        operator: 'is',
                        value: tariffGSA500A,
                        targetFields : [
                            {
                                name : 'valuation_deductible',
                                pickListOptions : {
                                    'GSA500A FVP' : 'Full Value Protection',
                                }
                            }
                        ]
                    },
                    {
                        operator : 'is',
                        value: tariff400DOE,
                        targetFields : [
                            {
                                name : 'valuation_deductible',
                                pickListOptions : {
                                    '400DOE FVP' : 'Full Value Protection',
                                }
                            }
                        ]
                    },
                    {
                        operator : 'is',
                        not : true,
                        value : tariff1950B,
                        targetFields : [
                            {
                                name : 'valuation_discounted',
                                hide : true,
                                setValue : false,
                            },
                        ],
                    },
                ]
            };
            this.applyVisibilityRules(rules, isEditView);
        },

        registerValuationChangeEvent : function() {
            var thisInstance = this;
            jQuery('.contentsDiv').on('value_change', 'input[name="valuation_amount"],input[name="additional_valuation"]', function () {
                thisInstance.enforceMinimumValuation().then(function(){
                    jQuery('input[name="total_valuation"]').val(thisInstance.calculateTotalValuation());
                });
            });
        },

        calculateTotalValuation: function(){
            var v1 = jQuery('input[name="valuation_amount"]').val();
            var v2 = jQuery('input[name="additional_valuation"]').val();
            var baseValuation =  parseFloat(v1.replace(/,/g,''));
            if(typeof v2 != 'undefined')
            {
                var additionalValuation = parseFloat(v2.replace(/,/g,''));
                if (Number(additionalValuation) == 0 || isNaN(additionalValuation)){
                    var additionalValuation = 0;
                }
            } else {
                var additionalValuation = 0;
            }
            if (Number(baseValuation) == 0 || isNaN(baseValuation)){
                baseValuation = 0;
            }

            var totalValuation = baseValuation + additionalValuation;
            return totalValuation.toFixed(2);
        },

        enforceMinimumValuation : function() {
            var a = jQuery.Deferred();
            if(jQuery('[name="instance"]').val() == 'sirva')
            {
                // SIRVA does their own thing for this, so just
                a.resolve();
                return a;
            }
            var tariffFieldName = 'effective_tariff';
            var weightFieldName = 'weight';
            var businessLineFieldName = 'business_line_est';
            var effectiveDateFieldName = 'interstate_effective_date';
            var contractFieldName = 'contract';
            if(valuationModuleName == 'Orders')
            {
                tariffFieldName = 'tariff_id';
                weightFieldName = 'orders_eweight';
                businessLineFieldName = 'business_line';
                effectiveDateFieldName = 'received_date';
                contractFieldName = 'account_contract';
            }
            var v = jQuery('input[name="valuation_amount"]').val();
            var effectiveTariff = jQuery('#effective_tariff_custom_type').val();
            if(!effectiveTariff)
            {
                a.resolve();
                return a;
            }
            if(Number(v) == 0 || !v){
                v = '0';
            } else {
                v = v.replace(',','');
            }
            if(effectiveTariff == '400DOE')
            {
                if(v < 125000)
                {
                    jQuery('input[name="valuation_amount"]').val('125,000').trigger('change');
                }
                a.resolve();
                return a;
            }
            var weightFactor = Valuation_Common_Js.getValuationWeightFactor();
            if(effectiveTariff == 'MMI'
                || (jQuery('[name="'+contractFieldName+'"]').val() > 0 && typeof weightFactor != 'undefined')) {
                if (typeof weightFactor == 'undefined') {
                    weightFactor = 6;
                }
                var weightValue = jQuery('input[name="'+weightFieldName+'"]').val().replace(',','');

                var nv = parseInt(weightValue) * parseFloat(weightFactor);
                if(parseFloat(v) < nv)
                {
                    jQuery('input[name="valuation_amount"]').val(nv).trigger('change');
                }
                a.resolve();
                return a;
            }

            var params = new Object();
            params.url = 'index.php?module=Estimates&action=GetMinimumFVPAmount';
            params.data = new Object();
            params.data.EffectiveTariff = jQuery('[name="'+tariffFieldName+'"]').val();;
            params.data.BusinessLine = jQuery('[name="'+businessLineFieldName+'"]').val();
            params.data.Owner = jQuery('[name="agentid"]').val();
            params.data.EffectiveDate = jQuery('[name="'+effectiveDateFieldName+'"]').val();
            params.data.Weight = jQuery('input[name="'+weightFieldName+'"]').val();
            if(typeof params.data.Weight == 'undefined')
            {
                params.data.Weight = 0;
            } else {
                params.data.Weight = params.data.Weight.replace(',','');
            }
            params.data.Deductible = jQuery('[name="valuation_deductible"]').val();
            params.data.DeductibleSubType = jQuery('[name="valuation_deductible_amount"]').val();
            if(typeof params.data.DeductibleSubType == 'undefined')
            {
                params.data.DeductibleSubType = 0;
            } else {
                params.data.DeductibleSubType = params.data.DeductibleSubType.replace(',','');
            }

            AppConnector.request(params).then(function(data) {
                if (data.success != false) {
                    if (parseFloat(v) < data.result.MinimumAmount) {
                        jQuery('input[name="additional_valuation"]').val(data.result.AdditionalAmount);
                        jQuery('input[name="valuation_amount"]').val(data.result.MinimumAmount).trigger('change');
                    }
                } else {
                    if (parseFloat(v) < 6 * jQuery('[name="weight"]').val()) {
                        jQuery('input[name="valuation_amount"]').val(6 * jQuery('[name="weight"]').val()).trigger('change');
                    }
                }
                a.resolve();
            });
            return a;
        },

        registerEvents : function(isEditView, module)
        {
            valuationModuleName = module;
            this.registerValuationChangeEvent();
            this.applyValuationVisibilityRules(isEditView);
        }
    }
);
