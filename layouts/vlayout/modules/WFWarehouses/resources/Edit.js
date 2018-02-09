Vtiger_Edit_Js("WFWarehouses_Edit_Js", {

}, {

    validateInactiveStatus : function() {
        var statusField = jQuery('select[name="wfwarehouse_status"]');
        var statusFieldOriginal = jQuery('select[name="wfwarehouse_status"]').find(':selected').val();
        statusField.on('change', function() {
            var status = statusField.find(':selected').val();
            if (status == 'Active'){
                return false;
            }
            var params = {
                'module' : app.getModuleName(),
                'action' : "ValidateInactive",
                'warehouse' : jQuery('input[name="record"]').val(),
                'invalidStatus' : "Active"
            };
            AppConnector.request(params).then(
                function(data) {
                    if(data && data.result) {
                        if (!data.result.success) {
                            statusField.val(statusFieldOriginal).trigger('liszt:updated');
                            bootbox.alert(data.result.message);
                        }
                    }
                },
                function(error, err){
                }
            );
        })
    },

    registerEvents: function () {
        this.validateInactiveStatus();
        this._super();
        this.initializeAddressAutofill('WFWarehouses');
    }
});
