/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Estimates_Edit_Js('Actuals_Edit_Js', {}, {
    moduleName: 'Actuals',
    registerWeightChanges: function () {
        var thisInstance = this;
        jQuery('input[name="gweight"]').on('change', function () {
            thisInstance.calculateNetWeight()
        });
        jQuery('input[name="tweight"]').on('change', function () {
            thisInstance.calculateNetWeight()
        });
    },
    calculateNetWeight: function () {
        var grossWeight = jQuery('input[name="gweight"]').val();
        var tareWeight = jQuery('input[name="tweight"]').val();
        if (grossWeight != '' && tareWeight != '') {

            var netWeight = grossWeight - tareWeight;

            if (netWeight < 0) {
                alert(app.vtranslate('JS_NWEIGHT_NEGATIVE'));
            } else {
                jQuery('input[name="weight"]').val(netWeight).trigger('change');

            }

        }
    },
    getPopUpParams : function(container) {
        var params = {};
        var sourceModule = app.getModuleName();
        var popupReferenceModule = jQuery('input[name="popupReferenceModule"]',container).val();
        var sourceFieldElement = jQuery('input[class="sourceField"]',container);
        var sourceField = sourceFieldElement.attr('name');
        var sourceRecordElement = jQuery('input[name="record"]');
        var sourceRecordId = '';
        if(sourceRecordElement.length > 0) {
            sourceRecordId = sourceRecordElement.val();
        }

        var isMultiple = false;
        if(sourceFieldElement.data('multiple') == true){
            isMultiple = true;
        }

        var params = {
            'module' : popupReferenceModule,
            'src_module' : sourceModule,
            'src_field' : sourceField,
            'src_record' : sourceRecordId
        };

        if(isMultiple) {
            params.multi_select = true ;
        }
        if(sourceField == 'agentcompensationid' && sourceModule == 'Actuals'){
            //params.business_line = jQuery('[name="business_line_est2"]').val();
            params.business_line = jQuery('[name="business_line_est"]').val();
            params.billing_type = jQuery('[name="billing_type"]').val();
            params.authority = jQuery('[name="authority"]').val();
            var interstateMoveBlock = jQuery('[name="LBL_QUOTES_INTERSTATEMOVEDETAILS"]');
            var localMoveBlock = jQuery('[name="LBL_QUOTES_LOCALMOVEDETAILS"]');
            if(!localMoveBlock.hasClass('hide')){
                params.tariff = jQuery('[name="local_tariff"]').val();
                params.effective_date =jQuery('[name="effective_date"]').val();
            }else if(!interstateMoveBlock.hasClass('hide')){
                params.tariff = jQuery('[name="effective_tariff"]').val();
                params.effective_date =jQuery('[name="interstate_effective_date"]').val();
            }
            params.contract = jQuery('[name="contract"]').val();
        }
        return params;
    },



    getReferenceSearchParams : function(element){
        var tdElement = jQuery(element).closest('td');
        var params = {};
        var searchModule = this.getReferencedModuleName(tdElement);
        var sourceField = jQuery('.sourceField',tdElement).attr('name');
        if(sourceField == 'agentcompensationid' ){
            params.src_field = sourceField;
            //params.business_line = jQuery('[name="business_line_est2"]').val();
            params.business_line = jQuery('[name="business_line_est"]').val();
            params.billing_type = jQuery('[name="billing_type"]').val();
            params.authority = jQuery('[name="authority"]').val();
            var interstateMoveBlock = jQuery('[name="LBL_QUOTES_INTERSTATEMOVEDETAILS"]');
            var localMoveBlock = jQuery('[name="LBL_QUOTES_LOCALMOVEDETAILS"]');
            if(!localMoveBlock.hasClass('hide')){
                params.tariff = jQuery('[name="local_tariff"]').val();
                params.effective_date =jQuery('[name="effective_date"]').val();
            }else if(!interstateMoveBlock.hasClass('hide')){
                params.tariff = jQuery('[name="effective_tariff"]').val();
                params.effective_date =jQuery('[name="interstate_effective_date"]').val();
            }
            params.contract = jQuery('[name="contract"]').val();
        }
        params.search_module = searchModule;
        params.agentId = jQuery('[name="agentid"]').val();
        return params;
    },

    registerChangePicklistValuesTariffFilterByOwner : function () {
        var ownerField = jQuery('select[name="agentid"]');
        ownerField.on('change',function () {
            var tariffField = jQuery('select[name="local_tariff"]');
            var tariffIdSelected = tariffField.data('selected-value');
            if (tariffIdSelected == undefined){
                tariffIdSelected = jQuery('select[name="local_tariff"] option:selected').val();
            }
            var params = {
                'module' : app.getModuleName(),
                'action' : 'ActionAjax',
                'mode'	 : 'getPicklistValuesTariffByOwner',
                'agentId': ownerField.val(),
            };

            var tariffFieldUpdateValue = '';
            AppConnector.request(params).then(function(data) {
                if (data.success){
                    tariffFieldUpdateValue += '<option>Select an Option</option>';
                    $.each(data.result,function( index,item) {
                        if(item.id == tariffIdSelected){
                            tariffFieldUpdateValue += '<option value="'+item.id+'" selected="selected">'+item.name+'</option>';
                        }else{
                            tariffFieldUpdateValue += '<option value="'+item.id+'">'+item.name+'</option>';
                        }
                    });
                    tariffField.html(tariffFieldUpdateValue);
                    tariffField.trigger("liszt:updated");
                }
            })
        });
    },

    registerEvents: function () {
        this._super();
        this.registerWeightChanges();
        this.registerChangePicklistValuesTariffFilterByOwner();
    },
});
