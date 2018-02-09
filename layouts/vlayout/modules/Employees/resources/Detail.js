/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("Employees_Detail_Js", {
    saveExchangeCredentials : function(form){
        var hostname  = form.find('[name="exchange_hostname"]');
        var username = form.find('[name="exchange_username"]');
        var password  = form.find('[name="exchange_password"]');
        var userid = form.find('[name="userid"]').val();

        var params = {
            'module': 'Employees',
            'action' : "SaveAjax",
            'mode' : 'saveExchangeCredentials',
            'hostname' : hostname.val(),
            'username' : username.val(),
            'password' : password.val(),
            'userid' : userid
        };
        AppConnector.request(params).then(
            function(data) {
                if(data.success){
                    app.hideModalWindow();
                    Vtiger_Helper_Js.showPnotify(app.vtranslate(data.result));
                    jQuery('#Employees_detailView_fieldValue_user_exchange_hostname').find('.value').html(params.hostname);
                    jQuery('#Employees_detailView_fieldValue_user_exchange_username').find('.value').html(params.username);
                    jQuery('#Employees_detailView_fieldValue_user_exchange_password').find('.value').html('********************');
                }else{
                    password.validationEngine('showPrompt', app.vtranslate(data.error.message) , 'error','topLeft',true);
                    return false;
                }
            }
        );
    },
},{
    //Google Address Autofill
    registerEvents: function () {
        this._super();
        this.initializeAddressAutofill('Employees');
        this.hideUsersBlocks();
        this.registerExchangeCredentialsButton();
        this.hideTerminationBlock();
        this.hideContractorsBlock();
        if(jQuery('[name="instance"]').val() == 'graebel'){
        var common = new Employees_Common_Js();
        common.applyAllVisibilityRules(false);
        }
    },

    hideUsersBlocks: function () {
        var move_hq_user = jQuery('#Employees_detailView_fieldValue_move_hq_user').text();
        if(jQuery.trim(move_hq_user) != 'Yes') {
            var dataUrl = "index.php?module=Employees&action=GetHiddenBlocks&formodule=Employees&businessline=Employees_Users";
            AppConnector.request(dataUrl).then(
                function (data) {
                    if (data.success) {

                        for (var key in data.result.show) {
                            var blocklbl = data.result.show[key];
                            var blockHeader = jQuery(document).find('.blockHeader:contains("'+blocklbl+'")');
                            jQuery.each(blockHeader, function(i,e) {
                                if (jQuery(e).text().trim() == blocklbl.trim()) {
                                    var table = jQuery(e).closest('table');
                                    table.hide();
                                    table.next().hide();
                                }
                            });
                        }

                        // HIDE Microsoft Exchange
                        var exchangeCredentialsButton = jQuery('#exchangeCredentialsButton');
                        var table = exchangeCredentialsButton.closest('table');
                        table.hide();
                        table.next().hide();
                    }
                },
                function (error, err) {

                }
            );
        }
    },

    hideTerminationBlock: function() {
        var status = jQuery("[id$='employee_status']").find('.value').text().trim();
        if(status != 'Terminated'){
            var blockHeader = jQuery(document).find('.blockHeader:contains("Termination")');
            var table = blockHeader.closest('table');
            table.hide();
        }
    },

    hideContractorsBlock: function() {
        var type = jQuery("[id$='employee_type']").find('.value').text().trim();
        if(type != 'Contractor'){
            var blockHeader = jQuery(document).find('.blockHeader:contains("Contractors Detailed Information")');
            var table = blockHeader.closest('table');
            table.hide();
        }
    },

    registerExchangeCredentialsButton : function() {
        var thisInstance = this;
        var button = jQuery('#exchangeCredentialsButton');
        if(button.length < 1) {
            return;
        }
        button.off('click').on('click', function() {
            var userId = jQuery('#userId').val();
            if(userId !='') {
                AppConnector.request('index.php?module=Users&view=EditAjax&mode=setExchangeCredentials&recordId=' + userId).then(
                    function (data) {
                        if (data) {
                            var callback = function (data) {
                                var params = app.validationEngineOptions;
                                params.onValidationComplete = function (form, valid) {
                                    if (valid) {
                                        Employees_Detail_Js.saveExchangeCredentials(form)
                                    }
                                    return false;
                                };
                                jQuery('#setExchangeCredentials').validationEngine(app.validationEngineOptions);
                            };
                            app.showModalWindow(data, function (data) {
                                if (typeof callback == 'function') {
                                    callback(data);
                                }
                            });
                        }
                    }
                );
            }
        });
    },
});

jQuery(document).ready(function () {
    $("#Employees_detailView_fieldValue_user_smtp_password").html('********************');
    var exchangePassword = document.querySelector('#Employees_detailView_fieldValue_user_exchange_password > span');

    if (exchangePassword) {
        if (exchangePassword.textContent.trim() != '') {
            exchangePassword.textContent = '********************';
        }
    }
});
