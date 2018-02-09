
Vtiger_Detail_Js("WFOrders_Detail_Js",{


    registerRules: function(isEditView)
    {
        var thisInstance = Vtiger_Edit_Js.getInstance();
        var rules = {
            weight_date: {
                conditions: [
                    {
                        operator: 'always',
                        targetFields: [
                            {
                                name: 'weight_date',
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
