/***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("TariffSections_Edit_Js", {
    registerRules: function (isEditView) {
        var rules = {
            is_discountable: {
                conditions: [
                    {
                        operator: 'is',
                        value: 'No',
                        targetFields: [
                            {
                                name: 'bottomline_discount_override',
                                hide: true,
                            },
                        ]
                    },
                ]
            },
        };
        this.applyVisibilityRules(rules, isEditView);
    },

    registerBasicEvents: function (container, quickCreateParams) {
        var isEditView = jQuery('#isEditView').length > 0;
        this._super(container);
        this.registerRules(isEditView);
    }
});

