/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("Agents_Edit_Js", {}, {

    registerCustomerNumberChange : function()
    {
        jQuery('.contentsDiv').on(Vtiger_Edit_Js.postReferenceSelectionEvent, '[name="agents_custnum"]', function(e,data){
            data = data['data'];
            var message = 'Would you like to load the remote data from the Customer?';
            Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
                function(){
                    data = data[Object.keys(data)[0]];
                    if(typeof data['info'] == 'object')
                    {
                        data = data['info'];
                    }
                    var map = {
                        'agentname': 'label',
                        'agent_address1': 'Address 1',
                        'agent_address2': 'Address 2',
                        'agent_city': 'City',
                        'agent_state': 'State',
                        'agent_zip': 'Zip',
                        'agent_country': 'Country',
                        'agent_phone': 'Primary Phone',
                        'agent_fax': 'Fax',
                        'agent_email': 'Primary Email',
                        'agents_website': 'Website',
                    };
                    Vtiger_Edit_Js.populateData(data, map);
                },
                function(error, err) {
                    //they pressed no don't populate the data.
                }
            );
        });
    },

    registerEvents: function () {
        this._super();
        this.initializeAddressAutofill('Agents');
        //this.registerCustomerNumberChange();
    },

    getPopUpParams: function (container) {
        var params = this._super(container);
        var sourceFieldElement = jQuery('input[class="sourceField"]', container);

        if (sourceFieldElement.attr('name') == 'agent_contacts') {
            params['contact_type'] = 'Agents';
            params['cvid'] = 52;

        }

        return params;
    }
});

