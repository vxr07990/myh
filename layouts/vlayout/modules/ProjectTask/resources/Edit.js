Vtiger_Edit_Js("ProjectTask_Edit_Js", {
}, {
    //This will store the editview form
    editViewForm: false,
    resourcesCheckCache: {},
    /**
     * This function will return the current form
     */
    getForm: function() {
        if (this.editViewForm == false) {
            this.editViewForm = jQuery('#EditView');
        }
        return this.editViewForm;
    },
    /**
     * This function will return the account name
     */
    getStartDate: function(container) {
        return jQuery('input[name="startdate"]', container).val();
    },
    /**
     * This function will return the account name
     */
    getEndDate: function(container) {
        return jQuery('input[name="enddate"]', container).val();
    },
    /**
     * This function will return the current RecordId
     */
    getRecordId: function(container) {
        return jQuery('input[name="record"]', container).val();
    },
    /**
     * This function will register before saving any record
     */
    registerRecordPreSaveEvent: function(form) {
        var thisInstance = this;
        if (typeof form == 'undefined') {
            form = this.getForm();
        }


        form.on(Vtiger_Edit_Js.recordPreSave, function(e, data) {
            var startDate = thisInstance.getStartDate(form);
            var endDate = thisInstance.getEndDate(form);
            var recordId = thisInstance.getRecordId(form);
            var params = {};

            if (!(recordId in thisInstance.resourcesCheckCache)) {


                var params = {
                    'module': 'ResourceDashboard',
                    'action': 'checkProjectTasksResources',
                    'startdate': startDate,
                    'enddate': endDate,
                    'recordid': recordId
                }

                AppConnector.request(params).then(
                        function(data) {
                            var response = data['result'];
                            //@TODO: correct for proper failure message.
                            // I'm not sure how this should have worked, because recordId is not set on the form or page at all.
                            if (response) {
                                var result = response['success'];
                                if (result == true) {
                                    var message = app.vtranslate('LBL_RESOURCE_CONFLICT');
                                    Vtiger_Helper_Js.showConfirmationBox({'message': message}).then(
                                        function (e) {
                                            thisInstance.resourcesCheckCache[recordId] = false;
                                            form.submit();
                                        },
                                        function (error, err) {

                                        }
                                    );
                                } else {
                                    thisInstance.resourcesCheckCache[recordId] = false;
                                    form.submit();
                                }
                            } else {
                                //When it fails it must mean there is no resource conflict so let it create
                                thisInstance.resourcesCheckCache[recordId] = false;
                                form.submit();
                            }
                        }
                );

            }else {
                if (thisInstance.resourcesCheckCache[recordId] == true) {
                    var message = app.vtranslate('LBL_RESOURCE_CONFLICT');
                    Vtiger_Helper_Js.showConfirmationBox({'message': message}).then(
                            function(e) {
                                thisInstance.resourcesCheckCache[recordId] = false;
                                form.submit();
                            },
                            function(error, err) {

                            }
                    );
                } else {
                    delete thisInstance.resourcesCheckCache[recordId];
                    return true;
                }
            }
            e.preventDefault();
        })


    },
    /**
     * Function which will register basic events which will be used in quick create as well
     *
     */
    registerBasicEvents: function(container) {
        this._super(container);
        this.registerRecordPreSaveEvent(container);
        //container.trigger(Vtiger_Edit_Js.recordPreSave, {'value': 'edit'});
    }
});
