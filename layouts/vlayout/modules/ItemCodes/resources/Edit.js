Vtiger_Edit_Js("ItemCodes_Edit_Js",{
    getInstance: function() {
        return new ItemCodes_Edit_Js();
    }
},{
    registerEventForOwnerField: function () {
        var ownerField = jQuery('[name="agentid"]');
        ownerField.on('change',function () {
            var agentid = jQuery(this).val();
            if(agentid !=undefined && !isNaN(agentid)){
                var params = {
                    module: app.getModuleName(),
                    action: 'GetRevenueGroupItems',
                    agentId: agentid
                };
                AppConnector.request(params).then(
                    function (data) {
                        var response = data.result;
                        if(response){
                            var revenueGroupField = jQuery('[name="itemcodes_group"]');
                            var currentValue = revenueGroupField.val();
                            var options = '<option value ="">Select an Option</option>';
                            jQuery.each(response,function (value,label) {
                                var selected = (currentValue == value)?'selected':'';
                                options += '<option value="'+value+'" '+selected+'>'+label+'</option>';
                            });
                            revenueGroupField.html(options);
                            revenueGroupField.trigger('liszt:updated');
                        }
                    }
                );
            }
        });
        ownerField.trigger('change');
    },
    registerEvents : function() {
        this._super();
        this.registerEventForOwnerField();
    }
});
