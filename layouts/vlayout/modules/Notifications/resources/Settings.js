/* ********************************************************************************
 * The content of this file is subject to the Notifications ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
jQuery.Class("Notifications_Settings_Js", {
    instance: false,
    getInstance: function () {
        if (Notifications_Settings_Js.instance == false) {
            var instance = new Notifications_Settings_Js();
            Notifications_Settings_Js.instance = instance;
            return instance;
        }
        return Notifications_Settings_Js.instance;
    }
}, {

    /**
     * @param {int} value
     * @param {String} text
     */
    toggleEnableModule: function (value, text) {
        var progressIndicatorElement = jQuery.progressIndicator({
            'position': 'html',
            'blockInfo': {
                'enabled': true
            }
        });

        var params = {};
        params.module = 'Notifications';
        params.action = 'ActionAjax';
        params.mode = 'enableModule';
        params.value = value;

        AppConnector.request(params).then(
            function (data) {
                progressIndicatorElement.progressIndicator({'mode': 'hide'});
                Settings_Vtiger_Index_Js.showMessage({
                    text: text,
                    type: 'success'
                });
            },
            function (error) {
                console.log(error);
                progressIndicatorElement.progressIndicator({'mode': 'hide'});
            }
        );
    },

    registerEnableModuleEvent: function () {
        var thisInstance = this;

        jQuery('.summaryWidgetContainer').find('#enable_module').change(function (e) {
            var element = e.currentTarget;
            var value = 0;
            var text = "Notifications Disabled";

            if (element.checked) {
                value = 1;
                text = "Notifications Enabled";
            }

            thisInstance.toggleEnableModule(value, text);
        });
    },
    registerEvents: function () {
        this.registerEnableModuleEvent();
    }
});