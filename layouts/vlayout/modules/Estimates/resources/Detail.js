/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Inventory_Detail_Js("Estimates_Detail_Js", {}, {
    moduleName: 'Estimates',

    lineItemsJs: false,
    customerJs: false,

    convertToActual: function() {
        AppConnector.request('index.php?module=Estimates&action=ConvertToActual&record='+getQueryVariable('record')).then(function(data) {
                if(data.result) {
                    window.location.href='index.php?module=Actuals&view=Detail&record='+data.result;
                } else {
                    bootbox.alert(data.error.message);
                }
            }

        )
    },

    registerCustomJavascriptForTariff: function () {
        var thisInstance = this;
        var customjs = jQuery('#tariff_customjs').val();
        if (customjs != '' && customjs != 0 && customjs != null) {
            switch (customjs) {
                case 'Estimates_TPGTariff_Js':
                    thisInstance.currentTariff = Estimates_TPGTariff_Js.getInstance();
                    break;
                case 'Estimates_BaseSIRVA_Js':
                    thisInstance.currentTariff = Estimates_BaseSIRVA_Js.getInstance();
                    break;
                default:
                    break;
            }
        } else {
            thisInstance.currentTariff = Estimates_BaseTariff_Js.getInstance();
        }
        thisInstance.currentTariff.initialize();
    },

    registerReportsButton: function () {
        var thisInstance = this;
        jQuery('.contentsDiv').on('click', '#getReportSelectButton', function() {
            thisInstance.currentTariff.reportButtonDetail();
        });
    },

    registerInterstateRateEstimate: function () {
        var thisInstance = this;
        jQuery('.contentsDiv').on('click', '#interstateRateQuick', function () {
            thisInstance.currentTariff.quickRateDetail();
        });
        jQuery('.contentsDiv').on('click', '.interstateRateDetail', function () {
            thisInstance.currentTariff.detailedRateDetail(false);
        });
        jQuery('.contentsDiv').on('click', '.requote', function () {
            thisInstance.currentTariff.detailedRateDetail(true);
        });
    },

    registerEvents: function () {
        // var progressElement = jQuery.progressIndicator({
        //     'position' : 'html',
        //     'blockInfo' : {
        //         'enabled' : true
        //     }
        // });

        this._super();

        this.effectiveTariffData = jQuery('#allAvailableTariffs').length ? JSON.parse(jQuery('#allAvailableTariffs').val()) : [];
        this.registerCustomJavascriptForTariff();
        this.registerBindings();
        try {
            var common = new Valuation_Common_Js();
            common.registerEvents(false, this.moduleName);
        } catch(e) {}
        try {
            var common = new Estimates_Common_Js();
            common.registerEvents(false, this.moduleName);
        } catch(e) {}
        try {
            var vt = new VehicleTransportation_EditBlock_Js();
            vt.registerEvents();
        } catch (e) {}
        this.customerJs = Estimates_Customer_Js.I();
        this.customerJs.registerEvents(false);

        this.lineItemsJs = new LineItems_Js();
        this.lineItemsJs.registerLineItemEvents();
        this.lineItemsJs.registerMoveHQLineItemEvents();

        // TODO: move this to the proper place
        if(jQuery('#instance').val() == 'graebel') {
        } else if(jQuery('[id$="_fieldValue_business_line_est2"]').length > 0) {
            // Hide the old Business Line field
            Vtiger_Edit_Js.hideCell(jQuery('[id$="_fieldValue_business_line_est"]'));
        }

        //progressElement.progressIndicator({'mode': 'hide'});
        var show = '.sectionContentHolder';
        if(typeof this.customerJs.getShowQuery != 'undefined')
        {
            var newTariff = jQuery('#effective_tariff_custom_type').data('tariffid');
            var tariffData = this.effectiveTariffData[newTariff];
            var managed = false;
            if(typeof tariffData != 'undefined')
            {
                managed = tariffData['is_managed_tariff'];
            }
            var show = this.customerJs.getShowQuery(show, managed, newTariff, tariffData);
        }
        jQuery(show).not('.inactiveBlock').removeClass('hide');
        this.registerHidePricingDetailBlockFields();
    },
    
    registerHidePricingDetailBlockFields: function(){
        var fieldsToHide = [
            'validtill',
            'quotestage',
            'is_primary'
        ];
        if(jQuery('td#Estimates_detailView_fieldValue_pricing_mode > span').text().trim() !== 'Estimate' && 
                jQuery('td#Estimates_detailView_fieldValue_pricing_mode > span').text().trim() !== ''){
            $.each(fieldsToHide,function(index,value){
                var fieldTR = jQuery('td#Estimates_detailView_fieldValue_' + value).closest('tr');
                jQuery('td#Estimates_detailView_fieldValue_' + value + ' > span').addClass('hide');
                jQuery('td#Estimates_detailView_fieldLabel_' + value + ' > label').addClass('hide');
                if(fieldTR.find('td.fieldValue').find('span.hide').length === 2){
                    fieldTR.addClass('hide');
                }
            });

        }
        if(jQuery('td#Estimates_detailView_fieldValue_business_line_est > span').text().trim().indexOf('Local') === -1){
            jQuery('td#Estimates_detailView_fieldValue_is_multi_day > span').addClass('hide');
            jQuery('td#Estimates_detailView_fieldLabel_is_multi_day > label').addClass('hide');
        }
    },

    registerBindings: function () {
        this.registerInterstateRateEstimate();
        this.registerReportsButton();
    },

});
