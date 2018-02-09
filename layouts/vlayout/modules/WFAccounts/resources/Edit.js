/**
 * Created by mmuir on 6/27/2017.
 */
Vtiger_Edit_Js("WFAccounts_Edit_Js",{

}, {

    //Stored history of account name and duplicate check result
    duplicateCheckCache : {},

    //This will store the editview form
    editViewForm : false,


    registerRecordPreSaveEvent: function (form) {
        var thisInstance = this;
        if (typeof form == 'undefined') {
            form = this.getForm();
        }

        form.on(Vtiger_Edit_Js.recordPreSave, function (e, data) {
            var accountName = thisInstance.getAccountName(form);
            var recordId = thisInstance.getRecordId(form);
            var params = {};
            if (!(accountName in thisInstance.duplicateCheckCache)) {
                if (typeof form.data('wfaccounts-submit') != "undefined") {
                    e.preventDefault();
                    return false;
                }
                form.data('wfaccounts-submit', 'true');

                Vtiger_Helper_Js.checkDuplicateName({
                    'accountName': accountName,
                    'recordId': recordId,
                    'moduleName': 'WFAccounts'
                }).then(
                    function (data) {
                        form.removeData('wfaccounts-submit');
                        thisInstance.duplicateCheckCache[accountName] = data['success'];
                        form.submit();
                    },
                    function (data, err) {
                        form.removeData('wfaccounts-submit');
                        thisInstance.duplicateCheckCache[accountName] = data['success'];
                        thisInstance.duplicateCheckCache['message'] = data['message'];
                        var message = app.vtranslate('JS_DUPLICATE_CREATION_CONFIRMATION');
                        Vtiger_Helper_Js.showConfirmationBox({'message': message}).then(
                            function (e) {
                                thisInstance.duplicateCheckCache[accountName] = false;
                                form.submit();
                            },
                            function (error, err) {

                            }
                        );
                    }
                );
            } else {
                if (thisInstance.duplicateCheckCache[accountName] == true) {
                    var message = app.vtranslate('JS_DUPLICATE_CREATION_CONFIRMATION');
                    Vtiger_Helper_Js.showConfirmationBox({'message': message}).then(
                        function (e) {
                            thisInstance.duplicateCheckCache[accountName] = false;
                            form.submit();
                        },
                        function (error, err) {

                        }
                    );
                } else {
                    delete thisInstance.duplicateCheckCache[accountName];
                    return true;
                }
            }
            e.preventDefault();
        })
    },

    getAccountName : function(container){
        return jQuery('input[name="name"]',container).val();
    },

    getRecordId : function(container){
        return jQuery('input[name="record"]',container).val();
    },

    registerBasicEvents : function(container) {
        //this._super(container);
        this.registerRecordPreSaveEvent(container);
    }
});
