/**
 * Created by mmuir on 6/27/2017.
 */
Vtiger_Edit_Js("WFOrders_Edit_Js",{

}, {

    duplicateCheckCache : {},

    //This will store the editview form
    editViewForm : false,

    /**
     * This function will return the current form
     */
    getForm : function(){
        if(this.editViewForm == false) {
            this.editViewForm = jQuery('#EditView');
        }
        return this.editViewForm;
    },

    registerRecordPreSaveEvent: function (form) {
        var thisInstance = this;
        if (typeof form == 'undefined') {
            form = this.getForm();
        }

        form.on(Vtiger_Edit_Js.recordPreSave, function (e, data) {
            var account = thisInstance.getAccount(form);
            var orderNumber = thisInstance.getOrderNumber(form);
            var recordId = thisInstance.getRecordId(form);
            var accountOrder = account + orderNumber;

            if (!(accountOrder in thisInstance.duplicateCheckCache)) {
                var params = {};

                if (typeof form.data('wforders-submit') != "undefined") {
                    e.preventDefault();
                    return false;
                }
                form.data('wforders-submit', 'true');

                var params = {
                'module' : 'WFOrders',
                'action' : "CheckDuplicate",
                'account' : account,
                'recordId' : recordId,
                'orderNumber' : orderNumber,
                }
                AppConnector.request(params).then(
                    function (data) {
                        form.removeData('wforders-submit');
                        if(data.result.duplicate == true){
                            thisInstance.duplicateCheckCache[accountOrder] = true;
                            var message = app.vtranslate('JS_DUPLICATE_ORDER_CREATION_CONFIRMATION');
                            bootbox.alert(message);
                        }else{
                            thisInstance.duplicateCheckCache[accountOrder] = false;
                            form.submit();
                        }
                    }
                );
            } else{
                if(thisInstance.duplicateCheckCache[accountOrder] == true){
                    var message = app.vtranslate('JS_DUPLICATE_ORDER_CREATION_CONFIRMATION');
                    bootbox.alert(message);
                }else{
                    return true;
                }
            }
             e.preventDefault();
        })
    },

    getAccount : function(container){
        return jQuery('input[name="wforder_account"]',container).val();
    },

    getOrderNumber : function(container){
        return jQuery('input[name="wforder_number"]',container).val();
    },

    getRecordId : function(container){
        return jQuery('input[name="record"]',container).val();
    },

    registerRules: function (isEditView) {
        var rules = {
            show_weight_date: {
                conditions: [
                    {
                        operator: 'is',
                        value : 'false',
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
        this.applyVisibilityRules(rules, isEditView);
    },

    registerChangedWeight: function(){
        thisInstance = this;
        originalVal = jQuery('[name="original_weight"]').val();
        weightField = jQuery('[name="wforder_weight"]');
        weightField.on('value_change', function(){
            if(weightField.val() == originalVal){
                jQuery('[name="show_weight_date"]').val('false').trigger('change');
            } else {
                jQuery('[name="show_weight_date"]').val('true').trigger('change');
            }
        });
    },

    registerBasicEvents: function (container, quickCreateParams) {
        var isEditView = jQuery('#isEditView').length > 0;
        this._super(container);
        this.registerRules(isEditView);
        this.registerChangedWeight();
    }
});
