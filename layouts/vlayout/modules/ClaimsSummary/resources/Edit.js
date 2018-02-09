/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("ClaimsSummary_Edit_Js", {}, {
    registerAutoCompleteFields : function(container){
	var thisInstance = this;
        var autoCompleteOptions = {
            'minLength' : '3',
            'source' : function(request, response){
                //element will be array of dom elements
                //here this refers to auto complete instance
                var inputElement = jQuery(this.element[0]);
                var searchValue = request.term;
                var params = thisInstance.getReferenceSearchParams(inputElement);
                params.search_value = searchValue;
		if(jQuery(inputElement).prop("id") == "claimssummary_representative_display"){
		    console.dir(params);
		    params.agentId = jQuery('select[name=agentid]').val();
		    params.mode = 'getPersonnelForClaimsSummary';
		}
                thisInstance.searchModuleNames(params).then(function(data){
                    var reponseDataList = [];
                    var serverDataFormat = data.result;
                    if(serverDataFormat.length <= 0) {
                        jQuery(inputElement).val('');
                        serverDataFormat = new Array({
                            'label' : app.vtranslate('JS_NO_RESULTS_FOUND'),
                            'type'  : 'no results'
                        });
                    }
                    for(var id in serverDataFormat){
                        var responseData = serverDataFormat[id];
                        reponseDataList.push(responseData);
                    }
                    response(reponseDataList);
                });
            },
            'select' : function(event, ui ){
                var selectedItemData = ui.item;
                //To stop selection if no results is selected
                if(typeof selectedItemData.type != 'undefined' && selectedItemData.type=="no results"){
                    return false;
                }
                selectedItemData.name = selectedItemData.value;
                var element = jQuery(this);
                var tdElement = element.closest('td');
				if(app.getModuleName() == 'Workflows') {
				    tdElement = element.closest('.conditionRow');
                }
                thisInstance.setReferenceFieldValue(tdElement, selectedItemData);

                var sourceField = tdElement.find('input[class="sourceField"]').attr('name');
                var fieldElement = tdElement.find('input[name="'+sourceField+'"]');

                fieldElement.trigger(Vtiger_Edit_Js.postReferenceSelectionEvent,{'data':selectedItemData});
            },
            'change' : function(event, ui) {
                var element = jQuery(this);
                //if you dont have readonly attribute means the user didnt select the item
                if(element.attr('readonly')== undefined) {
                    element.closest('td').find('.clearReferenceSelection').trigger('click');
                }
            },
            'open' : function(event,ui){
                //To Make the menu come up in the case of quick create
                jQuery(this).data('autocomplete').menu.element.css('z-index','100001');
            }
        };
	$(document).on('keydown.autoComplete', '.autoComplete', function() {
	    $(this).autocomplete(autoCompleteOptions);
	});
	container.find('input.autoComplete').autocomplete(autoCompleteOptions);
    },
    getPopUpParams: function (container) {
        var params = this._super(container);
        var sourceFieldElement = jQuery('input[class="sourceField"]', container);

        if (sourceFieldElement.attr('name') == 'claimssummary_representative' && jQuery('[name="claimssummary_preferred"]').val() == "Transferee") {
            params['popup_type'] = 'representative';
	    params['owner'] = jQuery('[name="agentid"]').val();
        }
        return params;
    },
    openPopUp: function (e) {
        var thisInstance = this;
        var parentElem = jQuery(e.target).closest('td');

        var params = this.getPopUpParams(parentElem);

        var isMultiple = false;
        if (params.multi_select) {
            isMultiple = true;
        }
        var sourceFieldElement = jQuery('input[class="sourceField"]', parentElem);
        var prePopupOpenEvent = jQuery.Event(Vtiger_Edit_Js.preReferencePopUpOpenEvent);
        sourceFieldElement.trigger(prePopupOpenEvent);

        if (prePopupOpenEvent.isDefaultPrevented()) {
            return;
        }
        var popupInstance = Vtiger_Popup_Js.getInstance();
        popupInstance.show(params, function (data) {
            var responseData = JSON.parse(data);
            var responseData = JSON.parse(data);
            var dataList = new Array();
            for (var id in responseData) {
                var data = {
                    'name': responseData[id].name,
                    'id': id
                }
                dataList.push(data);
                if (!isMultiple) {
                    thisInstance.setReferenceFieldValue(parentElem, data);
                    if (jQuery(sourceFieldElement).attr("name") == "claimssummary_orderid") {
                        thisInstance.getOrderData(id);
                    }
                }
            }

            if (isMultiple) {
                sourceFieldElement.trigger(Vtiger_Edit_Js.refrenceMultiSelectionEvent, {'data': dataList});
            }
            sourceFieldElement.trigger(Vtiger_Edit_Js.postReferenceSelectionEvent, {'data': responseData});
        });
    },
    getOrderData: function (orderId) {
        instance = this;
        var urlParams = {
            module: 'ClaimsSummary',
            action: 'ClaimsSummaryActions',
            mode: 'getOrderInfo',
            orderId: orderId,
        };
        var progressIndicator = instance.showLoadingMessage('Loading Order Information');
        AppConnector.requestPjax(urlParams).then(
                function (data) {
                    var data = JSON.parse(data.result);
                    jQuery('[name="claimssummary_valuationtype"]').val(data.claimssummary_valuationtype);
                    jQuery('[name="claimssummary_declaredvalue"]').val(data.claimssummary_declaredvalue);
                    jQuery('[name="claimssummary_contactid"]').val(data.claimssummary_contactid);
                    jQuery('[name="claimssummary_accountid"]').val(data.claimssummary_accountid);
                    jQuery('[name="claimssummary_contactid_display"]').val(data.claimssummary_contactid_display);
                    jQuery('[name="claimssummary_accountid_display"]').val(data.claimssummary_accountid_display);
                    jQuery('[name="claimssummary_contactid_display"]').attr('readonly','readonly');
                    jQuery('[name="claimssummary_accountid_display"]').attr('readonly','readonly');
                    jQuery('[name="business_line"]').val(data.business_line).trigger('liszt:updated');
                    
                    instance.hideLoadingMessage(progressIndicator);

                });
    },
    showLoadingMessage: function (message) {
        var loadingMessage = app.vtranslate(message);
        var progressIndicatorElement = jQuery.progressIndicator({
            'message': loadingMessage,
            'position': 'html',
            'blockInfo': {
                'enabled': true
            }
        });

        return progressIndicatorElement;

    },
    hideLoadingMessage: function (progressIndicatorElement) {
        progressIndicatorElement.progressIndicator({
            'mode': 'hide'
        })
    },
});

jQuery(document).ready(function(){
    var instance = new ClaimsSummary_Edit_Js;
    instance.registerAutoCompleteFields(jQuery('#EditView'));
});