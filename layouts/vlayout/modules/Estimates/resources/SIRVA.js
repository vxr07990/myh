/**
 * Created by dbolin on 12/27/2016.
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

        // Tariff properties because it was scattered all over before.
        //@NOTE: SEE: TariffManager record model (well it should be, but actually see: \Estimates_Record_Model::getTariffInfo)
        tariffProperties: {
            'requote': ['TPG','Pricelock','TPG GRR','Pricelock GRR','Blue Express','Allied Express','Truckload Express',
                        'UAS'],

            'valPicklist': ['TPG','Pricelock','TPG GRR','Pricelock GRR','Blue Express','Allied Express','Truckload Express',
                        'UAS','Autos Only', 'Intra - 400N'],

            'customRates': ['TPG','Pricelock','TPG GRR','Pricelock GRR','Blue Express','Allied Express','Truckload Express'],

            'noContainers': ['UAS','Intra - 400N','400N Base','400N/104G','ALLV-2A','NAVL-12A'],

            'allowedNAT': ['400N Base','400N/104G','Intra - 400N','Local/Intra','ALLV-2A','NAVL-12A','400NG'],

            'leadDisallow': ['ALLV-2A','NAVL-12A','400N Base','400N/104G'],

            'allowedCOD': ['TPG','Pricelock','TPG GRR','Pricelock GRR','Blue Express','Allied Express','Truckload Express','UAS',
                        'Intra - 400N','Autos Only'],

            'militaryOnly': ['400NG']
        },

        getTariffProperty: function(prop, tariff) {
            if(typeof this.tariffProperties[prop] != 'undefined'
                    && this.tariffProperties[prop].indexOf(tariff) != -1) {
                return true;
            }
            return false;
        },
    },
    {
        moveType: null,
        daysToMove: null,
        serviceCharges: null,
        valuation: null,
        sit: null,

        currentBrand: false,

        registerRules: function (isEditView) {
            var rules = {
                subject: {
                    conditions: [
                        {
                            operator: 'always',
                            targetFields: [
                                {
                                    name: 'business_line_est',
                                    hide: true,
                                },
                                {
                                    name: 'lead_type',
                                    hide: true,
                                },
                                {
                                    name: 'weight',
                                    readonly: true
                                },
                                {
                                    name: 'interstate_mileage',
                                    readonly: true,
                                },
                                {
                                    name: 'pricing_color_lock',
                                    hide: true,
                                },
                                {
                                    name: 'smf_type',
                                    hide: true,
                                },
                                {
                                    name: 'grr_cp',
                                    hide: true,
                                },
                                {
                                    name: 'grr',
                                    readonly: true,
                                },
                                {
                                    name: 'grr_estimate',
                                    readonly: true,
                                }
                            ]
                        }
                    ]
                },
                move_type: {
                    conditions: [
                        {
                            operator: 'in',
                            not: true,
                            value: ['Local US','Max 3','Max 4','Intrastate','Intra-Provincial','Local Canada'],
                            and: {
                                source: 'effective_tariff_custom_type',
                                operator: 'is',
                                not: true,
                                value: 'Intra - 400N'
                            },
                            targetFields: [
                                {
                                    name: 'local_carrier',
                                    hide: true,
                                },
                                {
                                    name: 'estimates_origin_county',
                                    hide: true,
                                },
                                {
                                    name: 'estimates_destination_county',
                                    hide: true,
                                },
                            ]
                        }
                    ]
                },
                shipper_type: {
                    conditions: [
                        {
                            operator: 'is',
                            not: true,
                            value: 'NAT',
                            targetFields: [
                                {
                                    name: 'contract',
                                    hide: true,
                                },
                            ]
                        },
                        {
                            operator: 'is',
                            value: 'COD',
                            targetFields: [
                                {
                                    name: 'lead_type',
                                    setValue: 'Consumer'
                                }
                            ]
                        },
                        {
                            operator: 'is',
                            value: 'NAT',
                            targetFields: [
                                {
                                    name: 'lead_type',
                                    setValue: 'National Account'
                                },
                                {
                                    name: 'validtill',
                                    hide: true,
                                }
                            ]
                        },
                        {
                            operator: 'in',
                            not: true,
                            value: ['COD', 'NAT'],
                            targetFields: [
                                {
                                    name: 'lead_type',
                                    setValue: ''
                                }
                            ]
                        },
                    ]
                },
                full_pack: {
                    conditions: [
                        {
                            operator: 'is',
                            not: true,
                            value: 'Yes',
                            targetFields: [
                                {
                                    name: 'apply_full_pack_rate_override',
                                    hide: true,
                                    setValue: false,
                                },
                            ]
                        },
                        {
                            operator: 'is',
                            value: 'Yes',
                            targetFields: [
                                {
                                    name: 'apply_custom_pack_rate_override',
                                    setValue: false,
                                    readonly: true,
                                }
                            ]
                        }
                    ]
                },
                apply_full_pack_rate_override: {
                    conditions: [
                        {
                            operator: 'is',
                            not: true,
                            value: 'Yes',
                            targetFields: [
                                {
                                    name: 'full_pack_rate_override',
                                    hide: true,
                                }
                            ]
                        }
                    ]
                },
                grr_override: {
                    conditions: [
                        {
                            operator: 'is',
                            not: true,
                            value: 'Yes',
                            targetFields: [
                                {
                                    name: 'grr_override_amount',
                                    hide: true,
                                }
                            ]
                        }
                    ]
                },
                apply_custom_sit_rate_override: {
                    conditions: [
                        {
                            operator: 'is',
                            not: true,
                            value: 'Yes',
                            targetFields: [
                                {
                                    name: 'apply_sit_first_day_origin',
                                    hide: true,
                                    setValue: false,
                                },
                                {
                                    name: 'apply_sit_addl_day_origin',
                                    hide: true,
                                    setValue: false,
                                },
                                {
                                    name: 'apply_sit_cartage_origin',
                                    hide: true,
                                    setValue: false,
                                },
                            ]
                        },
                    ]
                },
                apply_sit_first_day_origin: {
                    conditions: [
                        {
                            operator: 'is',
                            not: true,
                            value: 'Yes',
                            targetFields: [
                                {
                                    name: 'sit_first_day_origin_override',
                                    hide: true,
                                    setValue: 0
                                }
                            ]
                        }
                    ]
                },
                apply_sit_addl_day_origin: {
                    conditions: [
                        {
                            operator: 'is',
                            not: true,
                            value: 'Yes',
                            targetFields: [
                                {
                                    name: 'sit_addl_day_origin_override',
                                    hide: true,
                                    setValue: 0
                                }
                            ]
                        }
                    ]
                },
                apply_sit_cartage_origin: {
                    conditions: [
                        {
                            operator: 'is',
                            not: true,
                            value: 'Yes',
                            targetFields: [
                                {
                                    name: 'sit_cartage_origin_override',
                                    hide: true,
                                    setValue: 0
                                }
                            ]
                        }
                    ]
                },
                apply_custom_sit_rate_override_dest: {
                    conditions: [
                        {
                            operator: 'is',
                            not: true,
                            value: 'Yes',
                            targetFields: [
                                {
                                    name: 'apply_sit_first_day_dest',
                                    hide: true,
                                    setValue: false,
                                },
                                {
                                    name: 'apply_sit_addl_day_dest',
                                    hide: true,
                                    setValue: false,
                                },
                                {
                                    name: 'apply_sit_cartage_dest',
                                    hide: true,
                                    setValue: false,
                                },
                            ]
                        },
                    ]
                },
                apply_sit_first_day_dest: {
                    conditions: [
                        {
                            operator: 'is',
                            not: true,
                            value: 'Yes',
                            targetFields: [
                                {
                                    name: 'sit_first_day_dest_override',
                                    hide: true,
                                    setValue: 0
                                }
                            ]
                        }
                    ]
                },
                apply_sit_addl_day_dest: {
                    conditions: [
                        {
                            operator: 'is',
                            not: true,
                            value: 'Yes',
                            targetFields: [
                                {
                                    name: 'sit_addl_day_dest_override',
                                    hide: true,
                                    setValue: 0
                                }
                            ]
                        }
                    ]
                },
                apply_sit_cartage_dest: {
                    conditions: [
                        {
                            operator: 'is',
                            not: true,
                            value: 'Yes',
                            targetFields: [
                                {
                                    name: 'sit_cartage_dest_override',
                                    hide: true,
                                    setValue: 0
                                }
                            ]
                        }
                    ]
                },
                apply_exlabor_rate_origin: {
                    conditions: [
                        {
                            operator: 'is',
                            not: true,
                            value: 'Yes',
                            targetFields: [
                                {
                                    name: 'exlabor_rate_origin',
                                    hide: true,
                                    setValue: '0.00'
                                },
                                {
                                    name: 'exlabor_flat_origin',
                                    hide: true,
                                    setValue: '0.00'
                                },
                            ]
                        }
                    ]
                },
                apply_exlabor_ot_rate_origin: {
                    conditions: [
                        {
                            operator: 'is',
                            not: true,
                            value: 'Yes',
                            targetFields: [
                                {
                                    name: 'exlabor_ot_flat_origin',
                                    hide: true,
                                    setValue: '0.00'
                                },
                                {
                                    name: 'exlabor_ot_rate_origin',
                                    hide: true,
                                    setValue: '0.00'
                                },
                            ]
                        }
                    ]
                },
                apply_exlabor_rate_dest: {
                    conditions: [
                        {
                            operator: 'is',
                            not: true,
                            value: 'Yes',
                            targetFields: [
                                {
                                    name: 'exlabor_rate_dest',
                                    hide: true,
                                    setValue: '0.00'
                                },
                                {
                                    name: 'exlabor_flat_dest',
                                    hide: true,
                                    setValue: '0.00'
                                },
                            ]
                        }
                    ]
                },
                apply_exlabor_ot_rate_dest: {
                    conditions: [
                        {
                            operator: 'is',
                            not: true,
                            value: 'Yes',
                            targetFields: [
                                {
                                    name: 'exlabor_ot_flat_dest',
                                    hide: true,
                                    setValue: '0.00'
                                },
                                {
                                    name: 'exlabor_ot_rate_dest',
                                    hide: true,
                                    setValue: '0.00'
                                },
                            ]
                        }
                    ]
                },
                acc_day_certain_pickup: {
                    conditions: [
                        {
                            operator: 'is',
                            not: true,
                            value: 'Yes',
                            targetFields: [
                                {
                                    name: 'acc_day_certain_fee',
                                    hide: true,
                                    setValue: '0.00',
                                }
                            ]
                        }
                    ]
                },
                pricing_color_lock: {
                    conditions: [
                        {
                            operator: 'is',
                            value: 'Yes',
                            targetFields: [
                                {
                                    name: 'demand_color',
                                    readonly: true,
                                },
                                {
                                    name: 'pricing_level',
                                    readonly: true,
                                },
                            ]
                        }
                    ]
                },
                valuation_deductible: {
                    conditions: [
                        {
                            operator: 'in',
                            not: true,
                            value: ['FVP - $0', 'MVP - $0', 'ECP - $0'],
                            targetFields: [
                                {
                                    name: 'apply_free_fvp',
                                    hide: true
                                }
                            ]
                        },
                        {
                            operator: 'is',
                            value: 'ECP - $0',
                            and: {
                                source: 'shipper_type',
                                operator: 'is',
                                not: true,
                                value: 'NAT'
                            },
                            targetFields: [
                                {
                                    name: 'apply_free_fvp',
                                    hide: true,
                                    value: false
                                }
                            ]
                        },
                        {
                            operator: 'is',
                            value: '60¢ /lb.',
                            targetFields: [
                                {
                                    name: 'valuation_amount_pick',
                                    hide: true
                                },
                                {
                                    name: 'valuation_amount',
                                    hide: true
                                }
                            ]
                        },
                        {
                            operator: 'always',
                            targetFields: [
                                {
                                    name: 'declared_value',
                                    hide: true,
                                },
                                {
                                    name: 'valuation_flat_charge',
                                    hide: true,
                                },
                                {
                                    name: 'free_valuation_type',
                                    hide: true,
                                },
                                {
                                    name: 'increased_base',
                                    hide: true,
                                },
                            ]
                        }
                    ]
                },
                // TARIFF RULES
                effective_tariff_custom_type: {
                    conditions: [
                        {
                            operator: 'in',
                            value: ['TPG', 'Allied Express', 'TPG GRR', 'Pricelock', 'Blue Express', 'Pricelock GRR'],
                            targetFields: [
                                {
                                    name: 'full_unpack',
                                    hide: true,
                                    setValue: false,
                                },
                                {
                                    name: 'full_pack',
                                    hide: true,
                                    setValue: false,
                                },
                                {
                                    name: 'accessorial_disc',
                                    hide: true,
                                    setValue: '',
                                },
                                {
                                    name: 'linehaul_disc',
                                    hide: true,
                                    setValue: '',
                                },
                                // not 100% certain if this is the right condition for this (maybe UAS is supposed to be included)
                                {
                                    name: 'bottom_line_discount',
                                    hide: true,
                                    setValue: '0',
                                },
                            ]
                        },
                        {
                            operator: 'is',
                            value: 'Intra - 400N',
                            targetFields: [
                                {
                                    name: 'validtill',
                                    hide: true,
                                }
                            ]
                        },
                        // TPG / Pricelock (requote) tariffs
                        {
                            operator: 'in',
                            value: ['UAS', 'TPG', 'Allied Express', 'TPG GRR', 'Pricelock', 'Blue Express', 'Pricelock GRR'],
                            targetFields: [
                                {
                                    name: 'irr_charge',
                                    readonly: true,
                                    setValue: 4,
                                },
                                {
                                    name: 'accesorial_fuel_surcharge',
                                    readonly: true,
                                },
                                {
                                    name: 'interstate_effective_date',
                                    readonly: true,
                                },
                                {
                                    name: 'validtill',
                                    readonly: true,
                                },
                                {
                                    name: 'acc_shuttle_dest_weight',
                                    hide: true,
                                },
                                {
                                    name: 'acc_shuttle_dest_applied',
                                    hide: true,
                                },
                                {
                                    name: 'acc_shuttle_dest_ot',
                                    hide: true,
                                },
                                {
                                    name: 'acc_shuttle_dest_over25',
                                    hide: true,
                                },
                                {
                                    name: 'acc_shuttle_dest_miles',
                                    hide: true,
                                }
                            ]
                        },
                        {
                          operator: 'in',
                          value: ['Pricelock', 'UAS', 'Blue Express', 'Pricelock GRR', 'Truckload Express'],
                            and: {
                                source: 'currentBrand',
                                operator: 'is',
                                value: 'NVL'
                            },
                          targetFields: [
                            {
                                name: 'apply_free_fvp',
                                hide: true,
                            }
                          ]
                        },
                        {
                            operator: 'in',
                            not: true,
                            value: ['Pricelock', 'UAS', 'Blue Express', 'Pricelock GRR', 'Truckload Express'],
                            or: {
                                source: 'currentBrand',
                                operator: 'is',
                                value: 'AVL'
                            },
                            targetFields: [
                                {
                                    name: 'acc_day_certain_pickup',
                                    hide: true,
                                    value: false
                                },
                            ]
                        },
                        {
                            operator: 'in',
                            value: ['UAS', 'TPG', 'Allied Express', 'TPG GRR', 'Pricelock', 'Blue Express', 'Pricelock GRR'],
                            and: {
                                source: 'apply_custom_pack_rate_override',
                                operator: 'is',
                                not: true,
                                value: 'Yes'
                            },
                            targetFields: [
                                {
                                    name: 'packing_disc',
                                    readonly:true,
                                    setValue: 0,
                                }
                            ]
                        },
                        {
                            operator: 'in',
                            not: true,
                            value: ['UAS', 'TPG', 'Allied Express', 'TPG GRR', 'Pricelock', 'Blue Express', 'Pricelock GRR', 'Truckload Express'],
                            targetFields: [
                                {
                                    // TODO?
                                    name: 'requote',
                                    hide: true,
                                },
                            ],
                            targetBlocks: [
                                {
                                    label: 'LBL_QUOTES_TPGPRICELOCK',
                                    hide: true,
                                }
                            ]
                        },
                        {
                            operator: 'is',
                            value: 'UAS',
                            targetFields: [
                                {
                                    name: 'sit_disc',
                                    readonly: false,
                                    setValue: '0.00'
                                },
                                {
                                    name: 'linehaul_disc',
                                    readonly: true,
                                    setValue: '0.00'
                                },
                                {
                                    name: 'packing_disc',
                                    readonly: true,
                                    setValue: '0.00'
                                },
                                {
                                    name: 'accessorial_disc',
                                    readonly: true,
                                    setValue: '0.00'
                                },
                                {
                                    name: 'full_pack_applied',
                                    hide: true
                                },
                                {
                                  name: 'local_origin_acc',
                                  hide: true,
                                  setValue: '0.00'
                                },
                            ]
                        },
                        {
                            operator: 'in',
                            value: ['Allied Express', 'Blue Express'],
                            targetFields: [
                                {
                                    name: 'acc_shuttle_origin_weight',
                                    readonly: true,
                                    setValue: 0
                                },
                                {
                                    name: 'acc_shuttle_origin_applied',
                                    readonly: true,
                                    setValue: false
                                },
                                {
                                    name: 'acc_shuttle_origin_ot',
                                    readonly: true,
                                    setValue: false
                                },
                                {
                                    name: 'acc_shuttle_origin_over25',
                                    readonly: true,
                                    setValue: false
                                },
                                {
                                    name: 'acc_shuttle_origin_miles',
                                    readonly: true,
                                    setValue: 0
                                }
                            ]
                        },
                        {
                            operator: 'in',
                            not: true,
                            value: ['Allied Express', 'Blue Express'],
                            targetFields: [
                                {
                                    name: 'express_pickup_type',
                                    hide: true,
                                    setValue: ''
                                },
                                {
                                    name: 'express_pickup_rate',
                                    hide: true,
                                    setValue: 0
                                }
                            ]
                        },
                        {
                            operator: 'in',
                            not: true,
                            value: ['ALLV-2A', 'NAVL-12A', '400N Base', '400N/104G', '400NG', 'Intra - 400N'],
                            targetFields: [
                                {
                                    name: 'consumption_fuel',
                                    hide: true,
                                    setValue: false,
                                }
                            ]
                        },
                        {
                            operator: 'in',
                            value: ['ALLV-2A', 'NAVL-12A', '400N Base', '400N/104G', '400NG'],
                            and: {
                                source: 'contract_display',
                                operator: 'is',
                                not: true,
                                value: ''
                            },
                            targetFields: [
                                {
                                    name: 'apply_custom_sit_rate_override',
                                    hide: true,
                                    setValue: false,
                                },
                                {
                                    name: 'apply_custom_sit_rate_override_dest',
                                    hide: true,
                                    setValue: false,
                                }
                            ]
                        },
                        {
                            operator: 'in',
                            not: true,
                            value: ['TPG GRR', 'Pricelock GRR'],
                            targetFields: [
                                {
                                    name: 'grr',
                                    hide: true,
                                },
                                {
                                    name: 'grr_override',
                                    hide: true,
                                },
                                {
                                    name: 'grr_estimate',
                                    hide: true,
                                }
                            ]
                        },
                        {
                            operator: 'in',
                            value: ['TPG', 'TPG GRR', 'Pricelock'],
                            not: true,
                            targetFields: [
                                {
                                    name: 'percent_smf',
                                    hide: true,
                                },
                                {
                                    name: 'desired_total',
                                    hide: true,
                                }
                            ]
                        },
                        {
                            operator: 'in',
                            value: ['UAS', 'ALLV-2A', 'NAVL-12A'],
                            targetFields: [
                                {
                                    name: 'percent_smf',
                                    hide: true,
                                },
                                {
                                    name: 'flat_smf',
                                    hide: true,
                                },
                                {
                                    name: 'desired_total',
                                    hide: true,
                                },
                            ]
                        },
                        {
                            operator: 'in',
                            value: ['TPG', 'Allied Express', 'Pricelock', 'Blue Express'],
                            targetFields: [
                                {
                                    name: 'estimate_type',
                                    pickListOptions: ['Binding']
                                }
                            ]
                        },
                        {
                            operator: 'in',
                            value: ['TPG GRR', 'Pricelock GRR'],
                            targetFields: [
                                {
                                    name: 'estimate_type',
                                    pickListOptions: ['Not to Exceed']
                                }
                            ]
                        },
                        {
                            operator: 'in',
                            value: ['UAS'],
                            targetFields: [
                                {
                                    name: 'estimate_type',
                                    pickListOptions: ['Non-Binding']
                                }
                            ]
                        },
                        {
                            operator: 'in',
                            value: ['ALLV-2A', '400NG'],
                            targetFields: [
                                {
                                    name: 'estimate_type',
                                    pickListOptions: ['Not to Exceed', 'Non-Binding']
                                }
                            ]
                        },
                        {
                            operator: 'is',
                            value: 'UAS',
                            targetFields: [
                                {
                                    name: 'apply_custom_sit_rate_override',
                                    hide: true,
                                    setValue: false,
                                },
                                {
                                    name: 'apply_custom_sit_rate_override_dest',
                                    hide: true,
                                    setValue: false,
                                },
                                {
                                    name: 'apply_full_pack_rate_override',
                                    hide: true,
                                    setValue: false
                                },
                            ]
                        },
                        {
                            operator: 'is',
                            value: 'MAX 4',
                            targetFields: [
                                {
                                    name: 'validtill',
                                    hide: true,
                                }
                            ]
                        },
                        {
                          operator: 'is',
                          value: 'Truckload Express',
                          targetFields: [
                              {
                                  name: 'acc_shuttle_dest_weight',
                                  readonly: true,
                              },
                              {
                                  name: 'acc_shuttle_dest_applied',
                                  readonly: true,
                              },
                              {
                                  name: 'acc_shuttle_dest_ot',
                                  readonly: true,
                              },
                              {
                                  name: 'acc_shuttle_dest_over25',
                                  readonly: true,
                              },
                              {
                                  name: 'acc_shuttle_dest_miles',
                                  readonly: true,
                              },
                              {
                                  name: 'accesorial_fuel_surcharge',
                                  readonly: true,
                              },
                              {
                                  name: 'accessorial_disc',
                                  hide: true,
                                  value: 0
                              },
                              {
                                  name: 'linehaul_disc',
                                  hide: true,
                                  value: 0
                              },
                              {
                                  name: 'bottom_line_discount',
                                  hide: true,
                                  value: 0
                              },
                              {
                                  name: 'full_pack',
                                  hide: true,
                                  value: 0
                              },
                              {
                                  name: 'full_unpack',
                                  hide: true,
                                  value: 0
                              }
                          ]
                        }
                    ]
                }
            };
            this.applyVisibilityRules(rules, isEditView);
        },

        updateEffectiveTariffPicklist: function() {
            var data = Estimates_Edit_Js.I().effectiveTariffData;
            var res = {};
            // National Account ??
            var currentBusinessLine = jQuery('[name="business_line_est"]').val();
            var leadType = jQuery('[name="lead_type"]').val();
            var billingType = jQuery('[name="billing_type"]').val();
            var shipperType = jQuery('[name="shipper_type"]').val();
            var allowInterstate = ['Interstate Move', 'International Move', 'Military'].indexOf(currentBusinessLine) >= 0;
            var allowLocal = ['Local Move','Intrastate Move'].indexOf(currentBusinessLine) >= 0;
            var allowIntrastate = allowLocal || allowInterstate;
            var defaultTPG = false;
            var defaultPricelock = false;

            for(var k in data)
            {
                var d = data[k];
                if(d['is_managed_tariff'])
                {
                    // Interstate tariff handling
                    // Set Default Tariff
                    if(!defaultTPG && d['custom_tariff_type'] == 'TPG')
                    {
                        defaultTPG = d['tariff_id'];
                    }
                    else if(!defaultPricelock && d['custom_tariff_type'] == 'Pricelock')
                    {
                        defaultPricelock = d['tariff_id'];
                    }

                    // Handle Exclusions
                    if(currentBusinessLine != 'Military' && Estimates_Customer_Js.getTariffProperty('militaryOnly', d['custom_tariff_type'])) {
                        continue;
                    }
                    else if(leadType == 'National Account' && !Estimates_Customer_Js.getTariffProperty('allowedNAT', d['custom_tariff_type']))
                    {
                        continue;
                    }
                    else if(leadType != 'National Account' && !Estimates_Customer_Js.getTariffProperty('allowedCOD', d['custom_tariff_type']))
                    {
                        continue;
                    }
                    if(d['custom_tariff_type'] == '400NG' && currentBusinessLine != 'Military')
                    {
                        continue;
                    }
                    // Handle Interstate vs Intrastate for managed tariffs
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
                    // Local tariff handling
                    if(!allowLocal)
                    {
                        continue;
                    }
                }
                res[d['tariff_id']] = d['tariff_name'];
            }
            Vtiger_Edit_Js.setPicklistOptions('effective_tariff', res);
            var directUpdate = true;
            if(!jQuery('[name="effective_tariff"]').val())
            {
                // Default to TPG or Pricelock
                if(this.currentBrand == 'AVL' && defaultTPG)
                {
                    directUpdate = false;
                    Estimates_Edit_Js.I().afterTariffLoad(function () {
                        Estimates_Edit_Js.I().doAfterTariffPicklistUpdate();
                    });
                    jQuery('[name="effective_tariff"]').val(defaultTPG).trigger('liszt:updated').trigger('change');
                } else if(this.currentBrand == 'NVL' && defaultPricelock)
                {
                    directUpdate = false;
                    Estimates_Edit_Js.I().afterTariffLoad(function () {
                        Estimates_Edit_Js.I().doAfterTariffPicklistUpdate();
                    });
                    jQuery('[name="effective_tariff"]').val(defaultPricelock).trigger('liszt:updated').trigger('change');
                }
            }
            if(directUpdate) {
                Estimates_Edit_Js.I().doAfterTariffPicklistUpdate();
            }
        },

        toggleContainerColumn: function(toggle) {
            var contRow = jQuery('.ContCol');
            // This sucks but we need to hide the correct one, and there are hidden tds in the way.
            // And the td we want to hide has no identifiers.
            var extraTd = $('#Estimates_editView_fieldName_accesorial_ot_packing').closest('td').prev().prev();
            if(toggle) {
                contRow.addClass('hide');
                extraTd.addClass('hide');
            }else {
                contRow.removeClass('hide');
                extraTd.removeClass('hide');
            }
        },

        registerContainerColumnEvents: function() {
            jQuery('.contentsDiv').on('value_change', '.packQtyField', function() {
                var name = jQuery(this).attr('name');
                var regExp = /\d+/g;
                var rowNumbers = name.match(regExp);
                var packFieldId = rowNumbers[0];
                var target = jQuery('input[name="pack_cont'+packFieldId+'"]');
                if(!target.prop('readonly'))
                {
                    return;
                }

                target.val(jQuery(this).val());
            });
        },

        loadLocalEstimateTypes: function(){
            var val = jQuery('[name="effective_tariff"]').val();
            var tariffData = Estimates_Edit_Js.I().effectiveTariffData[val];
            if(typeof tariffData != 'undefined') {
                if (tariffData['is_managed_tariff']) {
                    var options = [];
                } else {
                    var options = tariffData['local_estimate_types'];
                }
                Vtiger_Edit_Js.setPicklistOptions('local_estimate_type', options);
                var thisInstance = this;
                thisInstance.registerfrbw();
            }
        },

        registerEffectiveTariffChangeEvent: function(){
            var thisInstance = this;
            jQuery('.contentsDiv').on('value_change', '[name="effective_tariff"]', function() {
                thisInstance.loadLocalEstimateTypes();
                thisInstance.toggleTransitGuideButton();
                thisInstance.serviceCharges.refresh();
            });
            this.loadLocalEstimateTypes();
        },

        toggleTransitGuideButton: function(){
            var local_tariff_types = ['Max 3', 'Max 4']
            var custom_tariff_type = jQuery('#effective_tariff_custom_type').val();
            if(custom_tariff_type == '' || local_tariff_types.indexOf(custom_tariff_type) > -1){
                jQuery('.transitGuide').addClass('hide');
            } else {
                jQuery('.transitGuide').removeClass('hide');
            }
            // Needs to be its own if block.
            if(jQuery('select[name="move_type"]').val() == 'Intrastate') {
                jQuery('.transitGuide').addClass('hide');
            }
        },

        updateTariffBasedVisibility: function()
        {
            var val = jQuery('#effective_tariff_custom_type').val();
            if(Estimates_Customer_Js.getTariffProperty('requote', val))
            {
                jQuery('[id^="bulkyArticleRow"]').addClass('hide');
            }
            else {
                jQuery('[id^="bulkyArticleRow"]').removeClass('hide');
            }

            // removed, as this is not what they want.
            // the weight will have to be checked after rating, as weight additives can apply
            // if(['Blue Express', 'Allied Express'].indexOf(val) != -1)
            // {
            //     this.setMaxWeight(6000);
            // } else {
            //     this.setMaxWeight(0);
            // }

            if(Estimates_Customer_Js.getTariffProperty('customRates', val))
            {
                jQuery('.customRatesCheckboxRow').removeClass('hide');
            } else {
                //remove the check so it can't get saved as ON when it's hidden.
                jQuery('input:checkbox[name="apply_custom_pack_rate_override"]').prop('checked',false);
                jQuery('.customRatesCheckboxRow').addClass('hide');
            }

            this.toggleContainerColumn(Estimates_Customer_Js.getTariffProperty('noContainers', val));

            this.updateCustomPackRateVisibility();
            this.updateCustomCrateRateVisibility();
        },

        registerEffectiveTariffCustomTypeChangeEvent: function()
        {
            var thisInstance = this;
            var fn = function(e,first){
                var val = jQuery('#effective_tariff_custom_type').val();
                thisInstance.updateTariffBasedVisibility();

                // jQuery('input:checkbox[name="pricing_color_lock"]').prop('checked', false).trigger('change');
                // Estimates_Edit_Js.I().setReadonly('demand_color', false);
                // Estimates_Edit_Js.I().setReadonly('pricing_level', false);

                if(val && !first)
                {
                    thisInstance.valuation.loadOptions(thisInstance.currentBrand);
                }
            };
            jQuery('.contentsDiv').on('value_change', '#effective_tariff_custom_type', fn);
            fn.call(null, null, true);
        },

        registerChangeBillingTypeEvent : function () {
            var thisInstance = this;
            jQuery('.contentsDiv').on('value_change', '[name="billing_type"]', function(){
                thisInstance.updateEffectiveTariffPicklist();
            });
        },

        registerChangeShipperTypeEvent : function () {
            var thisInstance = this;
            jQuery('.contentsDiv').on('value_change', '[name="shipper_type"]', function(){
                thisInstance.updateEffectiveTariffPicklist();
            });
        },

        registerChangeLeadTypeEvent : function () {
            var thisInstance = this;
            jQuery('.contentsDiv').on('value_change', '[name="lead_type"]', function(){
                thisInstance.updateEffectiveTariffPicklist();
            });
        },



        checkPricingColorLock: function() {
            if (
                (
                    jQuery('input[name="load_date"]').val()
                    || jQuery('input[name="load_to_date"]').val()
                )
            ) {
                jQuery('input:checkbox[name="pricing_color_lock"]').prop('checked', true).trigger('change');
            } else {
                jQuery('input:checkbox[name="pricing_color_lock"]').prop('checked', false).trigger('change');
            }
        },

        registerPricingColorLockChecks: function() {
            var thisInstance = this;
            jQuery('.contentsDiv').on('value_change', '[name="interstate_effective_date"],[name="load_date"],[name="load_to_date"]', function() {
                thisInstance.checkPricingColorLock();
            });
        },

        updateAgent: function(agent, first)
        {
            var a = jQuery.Deferred();
            var thisInstance = this;
            var params = {
                module: 'AgentManager',
                action: 'GetBrand',
                agent_vanline_id: agent,
            };
            AppConnector.request(params).then(
                function(data) {
                    if (data.success) {
                        thisInstance.currentBrand = data.result;
                        $('[name="currentBrand"]').val(data.result).trigger('change');
                        thisInstance.valuation.loadOptions(data.result);
                    }
                    a.resolve();
                }
            );
            if(!first) {
                var params = {
                    module: 'Estimates',
                    action: 'GetAllowedTariffsForUser',
                    owner: agent,
                };
                a.then(function() {
                    AppConnector.request(params).then(
                        function (data) {
                            if (data.success) {
                                Estimates_Edit_Js.I().effectiveTariffData = data.result;
                                thisInstance.updateEffectiveTariffPicklist();
                            }
                        }
                    );
                });
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

        getBlocksToLoad: function(loadBlocks, prevTariff, newTariff, tariffData)
        {
            var corpVehicles = ['ALLV-2A','NAVL-12A','400N Base','400N/104G','400NG'];
            var tvPack = ['UAS', 'TPG GRR', 'TPG', 'Pricelock GRR', 'Pricelock', 'Allied Express'];
            var prevData = Estimates_Edit_Js.I().effectiveTariffData[prevTariff];
            if(typeof prevData == 'undefined' && tariffData['is_managed_tariff'])
            {
                loadBlocks.push('SIRVA_VEHICLES', 'INTERSTATE_MISC_CHARGES');
            }
            else
            {
                var res = [];
                var prevType = prevData['custom_tariff_type'];
                var newType = tariffData['custom_tariff_type'];
                if(
                    (corpVehicles.indexOf(prevType) == -1 && corpVehicles.indexOf(newType) != -1)
                    || (corpVehicles.indexOf(prevType) != -1 && corpVehicles.indexOf(newType) == -1)
                    )
                {
                    loadBlocks.push('SIRVA_VEHICLES');
                }
                if(
                    (tvPack.indexOf(prevType) == -1 && tvPack.indexOf(newType) != -1)
                    || (tvPack.indexOf(prevType) != -1 && tvPack.indexOf(newType) == -1)
                )
                {
                    loadBlocks.push('INTERSTATE_MISC_CHARGES');
                }
            }
        },

        postReloadContents: function(isInterstate)
        {
            if(isInterstate) {
                this.valuation.update();
                var sum = 0;
                jQuery('tr.addresssegmentRow').not('.hide').each(function() {
                    var tr = jQuery(this);
                    var weight = parseFloat(tr.find('input[name^="addresssegments_weightoverride_"]').val());
                    if(isNaN(weight) || weight == 0) {
                        weight = parseFloat(tr.find('input[name^="addresssegments_weight_"]').val());
                    }
                    if(isNaN(weight)) {
                        weight=0;
                    }
                    sum += weight;
                });
                jQuery('[name="weight"]').val(sum).trigger('change');
                this.updateTariffBasedVisibility();

                //if( jQuery('[name="shipper_type"]').val() != 'NAT'){
                //    jQuery('#vehiclesTab select[name="vehicleDescription"] option').each(function(index) {
                //        jQuery('#Estimates_editView_fieldName_bulky'+jQuery( this ).data('bulky')).attr('readonly', true);
                //    });
                //}
                MiscItems_Edit_Js.getInstance().registerAutoBulkyInit();
            } else {
                this.loadLocalEstimateTypes();
                Estimates_Edit_Js.I().hideByValuation();
                AddressSegments_EditBlock_Js.getInstance().updateTotalWeights();
            }
        },

        loadCustomJavascript: function()
        {
            var customjs = jQuery('#tariff_customjs').val();
            if (customjs != '' && customjs != 0 && customjs != null) {
                switch (customjs) {
                    case 'Estimates_TPGTariff_Js':
                        Estimates_Edit_Js.I().currentTariff = Estimates_TPGTariff_Js.getInstance();
                        break;
                    case 'Estimates_BaseSIRVA_Js':
                        Estimates_Edit_Js.I().currentTariff = Estimates_BaseSIRVA_Js.getInstance();
                        break;
                    default:
                        break;
                }
            } else {
                Estimates_Edit_Js.I().currentTariff = Estimates_BaseTariff_Js.getInstance();
            }
            Estimates_Edit_Js.I().currentTariff.initialize();
        },

        updateCustomPackRateVisibility: function() {
            if (jQuery('input:checkbox[name="apply_custom_pack_rate_override"]').prop('checked')) {
                jQuery('td.packingCustomRate, td.packingPackRate').each(function () {
                    jQuery(this).removeClass('hide');
                });
                jQuery('input[name="apply_custom_pack_rate_override"]')/*.closest('td').attr('colspan', '4')*/;
                jQuery('button[name="LoadTariffPacking"]').removeClass('hide')/*.closest('td').attr('colspan', '4')*/;
                jQuery('input:checkbox[name="full_pack"]').prop('disabled', true);

            } else {
                jQuery('td.packingCustomRate, td.packingPackRate').each(function () {
                    jQuery(this).addClass('hide');
                });
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
        },

        registerCustomPackRateOverrideEvent: function () {
            var thisInstance = this;
            jQuery('.contentsDiv').on('value_change', 'input:checkbox[name="apply_custom_pack_rate_override"]', function () {
                thisInstance.updateCustomPackRateVisibility();
                // if (thisInstance.detailView) {
                //     //console.dir('save now I think?');
                //     //console.dir(jQuery('input:checkbox[name="apply_custom_pack_rate_override"]'));
                //     thisInstance.parent.ajaxEditHandling(jQuery('input:checkbox[name="apply_custom_pack_rate_override"]').closest('td'));
                // }
            });
            thisInstance.updateCustomPackRateVisibility();
        },

        registerLoadPackingButtonEvent: function () {
            var thisInstance = this;
            jQuery('.contentsDiv').on('click', 'button[name="LoadTariffPacking"]', function () {
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
                var dataUrl = "index.php?module=Estimates&action=UpdateLocalTariffs&assigned_to=" + assigned_to;
                AppConnector.request(dataUrl).then(
                    //function (data) {
                    //if (data.success) {
                    //    console.dir('success!');
                    //    var recordId = getQueryVariable('record');onsole.dir(data.result.userAgents[0]);
                    //    var updateUrl = 'index.php?module=Estimates&view=Edit&record=' + recordId + '&mode=updateLocalTariff&userAgents=' + data.result.userAgents + '&edit=true';
                    //    AppConnector.request(updateUrl).then(
                    function (data) {
                        if (data.success && $('input[name="instance"]').val() != 'sirva') {
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
                                            var loadTariffPackingUrl = "index.php?module=Estimates&action=LoadTariffPacking&tariffId=" + selectedId + "&effectiveDate=" + effectiveDate;
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

        updateCustomCrateRateVisibility : function () {
            if (jQuery('input:checkbox[name="apply_custom_pack_rate_override"]').prop('checked')) {
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
        },

        registerCustomCrateRateOverrideEvent: function () {
            var thisInstance = this;
            jQuery('.contentsDiv').on('value_change', 'input:checkbox[name="apply_custom_pack_rate_override"]', function () {
                thisInstance.updateCustomCrateRateVisibility();
                // if (thisInstance.detailView) {
                //     //console.dir('save now I think?');
                //     //console.dir(jQuery('input:checkbox[name="apply_custom_pack_rate_override"]'));
                //     thisInstance.parent.ajaxEditHandling(jQuery('input:checkbox[name="apply_custom_pack_rate_override"]').closest('td'));
                // }
            });
            thisInstance.updateCustomCrateRateVisibility();
        },

        registerLoadCratingButtonEvent: function () {
            var thisInstance = this;
            jQuery('.contentsDiv').on('click', 'button[name="LoadTariffCrating"]', function () {
                if (jQuery('input[name="interstate_effective_date"]').val() === '') {
                    bootbox.alert('Effective Date must be set to Load Tariff Crating');
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

        registerLineItemExpansion: function() {
            $('[id^="row"]').on('click', function() {
                var row_num = $(this).attr('id');
                $(this).find('img').toggle();
                $('.subitem-' + row_num).slideToggle();
            })
        },

        registerValidDateChecks : function(){
            var dateChecks = [
                {
                    on:'pack_date', from: 'pack_date', to: 'pack_to_date',
                    msg: 'The "Pack From Date" should not be before the "Pack to Date"'
                },
                {
                    on:'pack_to_date',from: 'pack_date',to: 'pack_to_date',
                    msg: 'The "Pack From Date" should not be before the "Pack to Date"'
                },
                {
                    on:'load_date', from: 'load_date', to: 'load_to_date',
                    msg: 'The "Load From Date" should not be before the "Load to Date"'
                },
                {
                    on:'load_to_date', from: 'load_date', to: 'load_to_date',
                    msg: 'The "Load From Date" should not be before the "Load to Date"'
                },
                {
                    on:'deliver_date', from: 'deliver_date', to: 'deliver_to_date',
                    msg: 'The "Deliver From Date" should not be before the "Deliver to Date"'
                },
                {
                    on:'deliver_to_date', from: 'deliver_date', to: 'deliver_to_date',
                    msg: 'The "Deliver From Date" should not be before the "Deliver to Date"'
                },
            ];
            $.each(dateChecks, function(key, date){
                $('.contentsDiv').on('value_change', '[name="'+date.on+'"]' ,function() {
                    var domFrom = $('[name="'+date.from+'"]');
                    var	domTo	= $('[name="'+date.to+'"]');
                    // This won't necessarily work depending on the user's date format
                    // pretty sure it will now
                    var from = Estimates_Edit_Js.getDate(domFrom);
                    var to = Estimates_Edit_Js.getDate(domTo);
                    if(from>to){
                        domFrom.val('');
                        domTo.val('');
                        bootbox.alert(date.msg);
                    }
                });
            });
        },

        registerZipChangeEvent : function() {
            var thisInstance = this;
            jQuery('.contentsDiv').on('value_change', 'input[name="origin_zip"], input[name="destination_zip"]', function() {
                thisInstance.updateInterstateServiceCharges(this);
            });
            // We need the calls to be made on load and on tariff change.
            // Actually we don't want this on load, because it clears out the data. TFS27282
            //this.updateInterstateServiceCharges('input[name="origin_zip"]');
            //this.updateInterstateServiceCharges('input[name="destination_zip"]');
        },

        updateInterstateServiceCharges : function(ele) {
            //Reach out to server for a list of service charges applicable to the changed zip
            var new_zip  = jQuery(ele).val();
            var is_dest  = jQuery(ele).attr('name') == "destination_zip" ? 1 : 0;
            var tariffid = jQuery('[name="effective_tariff"]').val();
            var effDate  = jQuery('[name="interstate_effective_date"]').val();
            var dateFormat = jQuery('[name="interstate_effective_date"]').data('dateFormat');
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
        },

        registerLockSaveOnUnratedChanges: function () {
            //@TODO: These are attempts they can be removed. whenever final.
            // jQuery('input[type!="hidden"], select').on('click', function () {
            // jQuery('input[type!="hidden"], select').on('change', function () {
            // jQuery('input[type!="hidden"], select').on('keyup', function () {
            // jQuery('input[type!="hidden"], select').on('mouseup', function () {

            //Try to only catch *USER* input and mouseup, instead of just on change.
            jQuery('input[type!="hidden"], select, .chzn-container').on('input mouseup', function () {
                jQuery('input[name="hasUnratedChanges"]').val('1');
            });
        },

        registerWeightCalculations: function() {
            $('.contentsDiv').on('value_change', '[id*="weight"]', function() {
                var newNum = Math.ceil($(this).val());
                $(this).val(newNum);
            });
        },

        // Populate load to date if it is currently empty
        registerLoadFromPopulateLoadTo: function() {
            var load_from = jQuery('input[name="load_date"]');
            var load_to = jQuery('input[name="load_to_date"]');
            jQuery('.contentsDiv').on('value_change', 'input[name="load_date"]', function() {
                if (load_to.val() == '') {
                    load_to.val(load_from.val());
                }
            })
        },

        //Sirva requires that there are either a load from AND load to date, or nieghter. Can't be one without the other.
        bindLoadFromToDate: function() {
            var load_from = jQuery('#Estimates_editView_fieldName_load_date');
            var load_to = jQuery('#Estimates_editView_fieldName_load_to_date');
            var labels = jQuery('#Estimates_editView_fieldName_load_date_label, #Estimates_editView_fieldName_load_to_date_label');
            jQuery('.contentsDiv').on('value_change', 'input[name="load_date"],input[name="load_to_date"]', function (){
                //If we have a date in either, we need them both to be manditory
                if (jQuery('[name="shipper_type"]').val() !== 'NAT' && load_from.val().length > 0 || load_to.val().length > 0) {
                    //Since they are bound to have the same requirements, we only need to check if one is already required
                    if (load_from.data('validation-engine').indexOf("required") < 0) {
                        jQuery.merge(load_from, load_to).attr('data-validation-engine', 'validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]');
                        jQuery.merge(load_from, load_to).data('validation-engine', 'validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]');
                        jQuery(labels).each(function() {
                            var name = '<span class="redColor">*</span>' + jQuery(this).html();
                            jQuery(this).html(name);
                        });
                    }
                } else {
                    jQuery.merge(load_from, load_to).attr('data-validation-engine', 'validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]');
                    jQuery.merge(load_from, load_to).data('validation-engine', 'validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]');
                    jQuery(labels).each(function() {
                        var name = jQuery(this).html().replace('<span class="redColor">*</span>', '');
                        jQuery(this).html(name);
                    });
                }
            })
        },

        registerSMFType : function() {
            jQuery('.contentsDiv').on('value_change', 'input[name="percent_smf"]', function () {
                jQuery('input:checkbox[name="smf_type"]').prop('checked', false);
            });
            jQuery('.contentsDiv').on('value_change', 'input[name="flat_smf"]', function () {
                jQuery('input:checkbox[name="smf_type"]').prop('checked', true);
            });
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
            } else {
                weightField.prop('type', 'text');
                weightField.removeAttr('min').removeAttr('max');
                weightField.attr('data-validation-engine', "validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]");
            }
        },

        registerComparePackToUnPack: function () {
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

        registerEstimateTypeDefault: function()
        {
            var fn = function (){
                if(jQuery(this).length == 0)
                {
                    return;
                }
                if(jQuery(this)[0].selectedIndex == 0)
                {
                    jQuery(this)[0].selectedIndex = 1;
                    jQuery(this).trigger('liszt:updated').trigger('change');
                }
            };
            jQuery('.contentsDiv').on('picklist_updated', '[name="estimate_type"]', fn);
            fn.call(jQuery('[name="estimate_type"]'));
        },

        registerExpressTruckloadToggleFields: function() {
            var ele = $('#Estimates_editView_fieldName_express_truckload');
            var thisInstance = this;
            if(ele.is(':checked')) {
                this.expressTruckloadToggleFields();
            }
            ele.on('change', function() {
                thisInstance.expressTruckloadToggleFields();
            });
        },

        expressTruckloadToggleFields: function() {
            // #pricingRow_2 stores OT Load and OT Unload, which are not used on express truckload.
            var eles = [ '#pricingRow_2' ];
            for(var i = 0; i < eles.length; i++) {
                $(eles[i]).toggleClass('hide');
            }
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

        registerfrbw: function() {
          jQuery.each(jQuery('.frbw_id'),function(){
            serviceid = jQuery(this).val();
            if(jQuery('#frbw_tabled'+serviceid).length){
              weight = jQuery('[name="Weight' + serviceid +'"]');

              weight.on('value_change',function() {
                serviceid = jQuery(this).attr('serviceid');
                table = JSON.parse(jQuery('#frbw_tabled'+serviceid).val());
                wval = Number(jQuery(this).val());
                jQuery.each(table,function(key,value) {
                  if(wval >= Number(value['from_weight']) && wval <= Number(value['to_weight'])) {
                    jQuery('.frbw_rate'+serviceid).find('input').val(value['rate']);
                    jQuery('#frbw_cap'+serviceid).val(value['to_weight']);
                    jQuery('.cwt_overflow'+serviceid).addClass('hide');
                    jQuery('[name="Excess'+serviceid+'"]').val(value['excess']);
                  }
                });

                if(wval > Number(table[table.length-1]['to_weight'])) {
                  jQuery('.frbw_rate'+serviceid).find('input').val(table[table.length-1]['rate']);
                  jQuery('.cwt_overflow'+serviceid).removeClass('hide');
                  jQuery('#frbw_cap'+serviceid).val(table[table.length-1]['to_weight']);
                  jQuery('[name="Excess'+serviceid+'"]').val(table[table.length-1]['excess']).removeClass('hide');
                }

              });
            }
          });
        },

        registerTruncateSMFPercent: function() {
            jQuery('.contentsDiv').on('change', '#Estimates_editView_fieldName_percent_smf', function() {
                var input = parseFloat(jQuery('#Estimates_editView_fieldName_percent_smf').val());
                jQuery('#Estimates_editView_fieldName_percent_smf').val(input.toFixed(2));
            });
        },

        businessLineChange: function(businessLine, moveType) {
            // stub in case custom logic is needed.
        },

        moveTypeChange: function(moveType, prevMoveType) {
            this.updateEffectiveTariffPicklist();
        },

        // To ensure move type is properly readonly.
        setMoveTypeReadOnly: function() {
            if($('[name="instance"]').val() != 'sirva') {
                return;
            }

            Vtiger_Edit_Js.setReadonly('move_type', true);
        },

        initializeMoveType: function() {
            this.moveType = new Move_Type_Js();
            this.moveType.onMoveTypeChange(this.moveTypeChange, this);
            this.moveType.onBusinessLineChange(this.businessLineChange, this);
            this.moveType.setFields({
                'originCountry': 'estimates_origin_country',
                'destinationCountry': 'estimates_destination_country',
                'businessLine': 'business_line_est'
            });
            this.moveType.registerEvents();
            this.moveType.disableAfterUpdate();
            this.setMoveTypeReadOnly();
            this.moveType.updateMoveType();
        },

        initializeDaysToMove: function() {
            this.daysToMove = new Days_To_Move_Js();
            this.daysToMove.registerEvents();
        },

        initializeServiceCharges: function() {
            this.serviceCharges = new Service_Charges_Js();
            this.serviceCharges.registerEvents();
        },

        initializeValuation: function() {
            this.valuation = new Valuation_SIRVA_Js();
            this.valuation.registerEvents();
        },

        initializeSIT: function() {
            this.sit = new Estimates_SIT_Js();
            this.sit.registerEvents();
        },

        registerEvents: function(isEditView)
        {
            this.registerRules(isEditView);
            if(isEditView) {
                this.initializeMoveType();
                this.initializeServiceCharges();
                this.initializeDaysToMove();
                this.initializeValuation();
                this.initializeSIT();
                this.updateEffectiveTariffPicklist();
                this.registerTruncateSMFPercent();
                this.registerAgentChangeEvent();
                this.registerEffectiveTariffChangeEvent();
                this.registerEffectiveTariffCustomTypeChangeEvent();
                this.registerChangeBillingTypeEvent();
                this.registerChangeLeadTypeEvent();
                this.toggleTransitGuideButton();
                this.registerPricingColorLockChecks();
                this.checkPricingColorLock();
                this.registerCustomPackRateOverrideEvent();
                this.registerLoadPackingButtonEvent();
                this.registerCustomCrateRateOverrideEvent();
                this.registerLoadCratingButtonEvent();
                this.registerLineItemExpansion();
                this.registerValidDateChecks();
                this.registerZipChangeEvent();
                this.registerLockSaveOnUnratedChanges();
                this.registerWeightCalculations();
                this.registerLoadFromPopulateLoadTo();
                this.bindLoadFromToDate();
                this.registerSMFType();
                this.registerContainerColumnEvents();
                this.registerComparePackToUnPack();
                this.registerEstimateTypeDefault();
                this.registerExpressTruckloadToggleFields();
                this.registerChangeShipperTypeEvent();
                this.registerGetLocalMileageButton();
                this.registerfrbw();

                var editViewForm = this.getForm();

                editViewForm.on('submit', function(e) {
                    if (jQuery('input[name="hasUnratedChanges"]').val() == '1') {
                        //unset this flag.
                        jQuery('input[name="hasUnratedChanges"]').val('0');
                        e.preventDefault();
                        editViewForm.removeData('submit');
                        bootbox.alert('There may have been changes made to this Estimate since it was last rated. It is highly recommended that you perform another rate on this Estimate before saving.');
                    }
                });
            }
        }

    }
);
