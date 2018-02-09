/**
 * Created by dbolin on 11/1/2016.
 */
Vtiger_Edit_Js("VehicleTransportation_EditBlock_Js", {
}, {
    registerBasicEvents : function(container) {
        var recordNumber = container.attr('guestid');
        if(!recordNumber)
        {
            return;
        }

        if(jQuery('input[name="instance"]').val() != 'graebel')
        {
            return;
        }

        var isEditView = jQuery('#isEditView').length > 0;
        var tariff1950B = '1950-B';
        var tariff400N104G = '400N - 104G';
        var tariffMMI = 'MMI';
        var vehTariffs = [tariff1950B, tariff400N104G, tariffMMI];

        var rules = {
            contractFlatRateAuto : {
                conditions : [
                    {
                        operator: 'gt',
                        value: '0',
                        targetFields: [
                            {
                                name: 'vehicletrans_ratingtype',
                                inGuestBlock : true,
                                pickListOptions : ['Bulky','Flat Rate']
                            }
                        ]
                    }
                ]
            },
            effective_tariff_custom_type : {
                conditions : [
                    {
                        operator: 'in',
                        not: true,
                        value: vehTariffs,
                        targetFields: [
                            {
                                name: 'vehicletrans_ratingtype',
                                inGuestBlock : true,
                                pickListOptions : ['Bulky']
                            }
                        ]
                    },
                    {
                        operator: 'is',
                        value: tariffMMI,
                        targetFields: [
                            {
                                name: 'vehicletrans_oversized',
                                inGuestBlock: true,
                                pickListOptions: ['Class I', 'Class II', 'Class III', 'Class IV']
                            }
                        ]
                    },
                    {
                        operator: 'is',
                        not: true,
                        value: tariffMMI,
                        targetFields: [
                            {
                                name: 'vehicletrans_carriertype',
                                inGuestBlock: true,
                                hide: true,
                                setValue: 'Select an Option'
                            }
                        ]
                    }
                ]
            },
            vehicletrans_ratingtype : {
                inGuestBlock : true,
                conditions : [
                    {
                        operator: 'is',
                        value: 'Bulky',
                        targetFields: [
                            {
                                name: 'vehicletrans_inoperable',
                                hide: true,
                            },
                            {
                                name: 'vehicletrans_groundclearance',
                                hide: true,
                            },
                            {
                                name: 'vehicletrans_oversized',
                                hide: true,
                            },
                            {
                                name: 'vehicletrans_cube',
                                hide: true,
                            },
                            {
                                name: 'vehicletrans_sitdays',
                                hide: true,
                            },
                            {
                                name: 'vehicletrans_sitmiles',
                                hide: true,
                            },
                            {
                                name: 'vehicletrans_ot',
                                hide: true,
                            },
                            {
                                name: 'vehicletrans_carriertype',
                                hide: true,
                                setValue: 'Select an Option'
                            }
                        ]
                    },
                    {
                        operator : 'in',
                        not: true,
                        value: ['Bulky','Flat Rate'],
                        targetFields: [
                            {
                                name: 'vehicletrans_description',
                                hide: true,
                            },
                            {
                                name: 'vehicletrans_make',
                                hide: true,
                            },
                            {
                                name: 'vehicletrans_modelyear',
                                hide: true,
                            },
                            {
                                name: 'vehicletrans_model',
                                hide: true,
                            },
                            {
                                name: 'vehicletrans_type',
                                hide: true,
                            },
                            {
                                name: 'vehicletrans_inoperable',
                                hide: true,
                            },
                            {
                                name: 'vehicletrans_groundclearance',
                                hide: true,
                            },
                            {
                                name: 'vehicletrans_oversized',
                                hide: true,
                            },
                            {
                                name: 'vehicletrans_weight',
                                hide: true,
                            },
                            {
                                name: 'vehicletrans_cube',
                                hide: true,
                            },
                            {
                                name: 'vehicletrans_sitdays',
                                hide: true,
                            },
                            {
                                name: 'vehicletrans_sitmiles',
                                hide: true,
                            },
                            {
                                name: 'vehicletrans_ot',
                                hide: true,
                            },
                            {
                                name: 'vehicletrans_carriertype',
                                hide: true,
                                setValue: 'Select an Option'
                            }
                        ]
                    },
                ]
            }
        };
        this.applyVisibilityRules(rules, isEditView, '_' + recordNumber);
    },

    registerEvents : function() {
    },
});

