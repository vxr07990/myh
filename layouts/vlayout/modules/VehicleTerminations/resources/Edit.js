Vtiger_Edit_Js("VehicleTerminations_Edit_Js", {}, {

    registerTerminationDateChange: function(){
        thisInstance = this;
        var termdateField = jQuery('input[name = "termination_date"]');
        var termReasonField = jQuery('[name = "termination_reason"]');
        var termCommentField = jQuery('[name = "termination_comments"]');
        termdateField.change(function() {
            if (termdateField.val() == '' || !termdateField.val()){
                Vtiger_Edit_Js.makeFieldNotMandatory(termReasonField);
                Vtiger_Edit_Js.makeFieldNotMandatory(termCommentField);
            } else {
                Vtiger_Edit_Js.makeFieldMandatory(termReasonField);
                Vtiger_Edit_Js.makeFieldMandatory(termCommentField);
            }
    })

    },



    registerEvents: function () {
        this._super();
        this.registerTerminationDateChange();
    },
});
