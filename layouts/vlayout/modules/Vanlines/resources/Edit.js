/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("Vanlines_Edit_Js", {}, {
    registerEvents: function () {
        this._super();
        this.initializeAddressAutofill('Vanlines');
    },
    getPopUpParams: function (container) {
        var params = this._super(container);
        var sourceFieldElement = jQuery('input[class="sourceField"]', container);

        if (sourceFieldElement.attr('name') == 'vanline_contact') {
            params['contact_type'] = 'Vanline';
            params['cvid'] = 51;

        }

        return params;
    }
});

