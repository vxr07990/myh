/**
 * Created by dbolin on 10/24/2016.
 */

Vtiger_Edit_Js("Contracts_Common_Js", {

    },
    {
        applyAllVisibilityRules : function (isEditView){
            // TODO: table this data
            // if(isEditView) {
            //     var tariff1950B = jQuery('[name="tariff_id"]').find('option').filter(function () {
            //         return jQuery(this).text() == '1950-B';
            //     }).val();
            //     var workSpace = 'Commercial';
            // } else {
            //     var tariff1950B = '1950-B';
            //     var workSpace = 'Work Space';
            // }
            var rules = {
                business_line : {
                    conditions : [
                        {
                            operator : 'contains',
                            value : 'International',
                            not : true,
                            targetBlocks : [
                                {
                                    label : 'LBL_CONTRACTS_INTERNATIONAL_INFORMATION',
                                    hide : true,
                                },
                            ],
                        },
                        {
                            operator : 'contains',
                            value : 'Intrastate',
                            not : true,
                            targetBlocks : [
                                {
                                    label : 'LBL_CONTRACTS_INTRA_TARIFF_INFORMATION',
                                    hide : true,
                                },
                            ],
                        },
                    ],
                },
            };
            this.applyVisibilityRules(rules, isEditView);
        },
    }
);
