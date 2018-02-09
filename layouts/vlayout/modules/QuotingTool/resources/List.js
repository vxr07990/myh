/* ********************************************************************************
 * The content of this file is subject to the Quoting Tool ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

/** @class QuotingTool_List_Js */
Vtiger_List_Js("QuotingTool_List_Js", {}, {
    /* For License page - Begin */
    init: function () {
        this.initiate();
    },
    /**
     * Function to initiate the step 1 instance
     */
    initiate: function () {
        var step = jQuery(".installationContents").find('.step').val();
        this.initiateStep(step);
    },
    /**
     * Function to initiate all the operations for a step
     * @params step value
     */
    initiateStep: function (stepVal) {
        var step = 'step' + stepVal;
        this.activateHeader(step);
    },

    activateHeader: function (step) {
        var headersContainer = jQuery('.crumbs ');
        headersContainer.find('.active').removeClass('active');
        jQuery('#' + step, headersContainer).addClass('active');
    },

    registerActivateLicenseEvent: function () {
        var aDeferred = jQuery.Deferred();
        jQuery(".installationContents").find('[name="btnActivate"]').click(function () {
            var license_key = jQuery('#license_key');
            if (license_key.val() == '') {
                var errorMsg = "License Key cannot be empty";
                license_key.validationEngine('showPrompt', errorMsg, 'error', 'bottomLeft', true);
                aDeferred.reject();
                return aDeferred.promise();
            } else {
                var progressIndicatorElement = jQuery.progressIndicator({
                    'position': 'html',
                    'blockInfo': {
                        'enabled': true
                    }
                });
                var params = {};
                params['module'] = app.getModuleName();
                params['action'] = 'Activate';
                params['mode'] = 'activate';
                params['license'] = license_key.val();

                AppConnector.request(params).then(
                    function (data) {
                        progressIndicatorElement.progressIndicator({'mode': 'hide'});
                        if (data.success) {
                            var message = data.result.message;
                            if (message != 'Valid License') {
                                jQuery('#error_message').html(message)
                                    .show();
                            } else {
                                document.location.href = "index.php?module=QuotingTool&view=List&mode=step3";
                            }
                        }
                    },
                    function (error) {
                        console.log('error =', error);
                        progressIndicatorElement.progressIndicator({'mode': 'hide'});
                    }
                );
            }
        });
    },

    registerValidEvent: function () {
        jQuery(".installationContents").find('[name="btnFinish"]').click(function () {
            var progressIndicatorElement = jQuery.progressIndicator({
                'position': 'html',
                'blockInfo': {
                    'enabled': true
                }
            });
            var params = {};
            params['module'] = app.getModuleName();
            params['action'] = 'Activate';
            params['mode'] = 'valid';

            AppConnector.request(params).then(
                function (data) {
                    progressIndicatorElement.progressIndicator({'mode': 'hide'});
                    if (data.success) {
                        document.location.href = "index.php?module=QuotingTool&view=List";
                    }
                },
                function (error) {
                    console.log('error =', error);
                    progressIndicatorElement.progressIndicator({'mode': 'hide'});
                }
            );
        });
    },
    /* For License page - End */

    /**
     * Function to register events
     */
    registerEvents: function () {
        this._super();
        /* For License page - Begin */
        this.registerActivateLicenseEvent();
        this.registerValidEvent();
        /* For License page - End */
    }
});

function triggerDuplicate(){
    var checkbox = jQuery('.listViewEntriesTable input[type="checkbox"]:checked');
    if(checkbox.length !== 1){
        var params = {
            title: app.vtranslate('JS_MESSAGE'),
            text: app.vtranslate('Select one record to duplicate'),
            animation: 'show',
            type: 'info'
        };
        Vtiger_Helper_Js.showPnotify(params);
        return false;
    }else{
        var ajaxParams = {
            module: 'QuotingTool',
            action: 'ActionAjax',
            mode: 'duplicate',
            recordid: checkbox.val()
        };
        AppConnector.request(ajaxParams).then(
            function (response) {
                console.log(response);
                window.location.href='index.php?module=' + app.getModuleName() + '&view=Edit&record=' + response.result + '&isDuplicate=true';
            },
            function (error) {
                console.log(error);
            }
        );
    }
}