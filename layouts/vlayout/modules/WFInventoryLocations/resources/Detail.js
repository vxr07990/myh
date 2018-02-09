/**
 * Created by mmuir on 10/25/2017.
 */
Vtiger_Detail_Js("WFInventoryLocations_Detail_Js",{


    registerRules: function(isEditView)
    {
        var thisInstance = Vtiger_Edit_Js.getInstance();
        var rules = {
            inventory: {
                conditions: [
                    {
                        operator: 'always',
                        targetFields: [
                            {
                                name: 'inventory',
                                hide: true,
                            },
                        ]
                    },
                ]
            },
            location_type: {
                conditions: [
                    {
                        operator: 'in',
                        value: ['Rack', 'Record Storage'],
                        not: true,
                        targetFields: [
                            {
                                name: 'wfinventorylocations_slot',
                                hide: true,
                            },
                        ]
                    },
                ]
            },
        };
        thisInstance.applyVisibilityRules(rules, isEditView);
    },

    registerEvents : function() {
        var isEditView = jQuery('#isEditView').length > 0;
        this.registerRules(isEditView);
    }
});
