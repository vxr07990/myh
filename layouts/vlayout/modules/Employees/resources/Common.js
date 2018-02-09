/**
 * Created by dbolin on 10/24/2016.
 */

Vtiger_Edit_Js("Employees_Common_Js", {

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
                employee_type : {
                    conditions : [
                        {
                            operator : 'in',
                            value : ['IC Transportation Contractor',
                                'I/C Co-driver',
                                'TSC Employee',
                                'Terminal Service Contractor',
                                'I/C and TSC',
                                'Contractor Surveyor',
                                'I/C Shuttle',
                                'IC Labor'],
                            not : true,
                            targetBlocks : [
                                {
                                    label : 'LBL_CONTRACTORS_DETAILINFO',
                                    hide : true,
                                },
                            ],
                        },
                        {
                            operator : 'in',
                            value : ['Casual Laborer',
                                'Employee Agent',
                                'Employee - Full Time',
                                'Employee - Part Time',
                                'Temp Agency Employee - Admin',
                                'Temp Agency Employee - Prod',
                                'Administrative'],
                            not : true,
                            targetBlocks : [
                                {
                                    label : 'LBL_EMPLOYEES_DETAILINFO',
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