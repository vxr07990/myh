/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
Vtiger_Edit_Js("CommissionPlansFilter_Edit_Js",{},{

	registerEventForBusinessLineField: function (container) {
		var thisInstance = this;
		container.find('select[name^="business_line_complansfilter"]').on('change',function () {
			thisInstance.setPicklistValueForTariff(container);
		});
	},
	setPicklistValueForTariff: function (container) {
		var blValue = container.find('select[name^="business_line_complansfilter"]').val();
		var countForTariff = 0;
		var countForTafiffManager = 0;

		if(blValue != '' && blValue != undefined ){
			jQuery.each(blValue,function (index,val) {
				val = val.toLowerCase();
				if(val.indexOf('local') > -1 || val.indexOf('international') > -1 || val =='all') countForTariff++;
				if(val.indexOf('intrastate') > -1  || val.indexOf('interstate') > -1 || val =='all') countForTafiffManager++;
			});
		}
		if(countForTariff > 0 && countForTafiffManager >0 ){
			this.setRelateModuleForTariff(['Tariffs','TariffManager']);
		}else if(countForTariff >0){
			this.setRelateModuleForTariff(['Tariffs']);
		}else if(countForTafiffManager > 0){
			this.setRelateModuleForTariff(['TariffManager']);
		}else{
			this.setRelateModuleForTariff([]);
		}
	},
	setRelateModuleForTariff: function (relatedModules) {
		var tariffEle = jQuery('input[name="related_tariff"]');
		var fieldInfo = tariffEle.data('fieldinfo');
		if(typeof fieldInfo != 'object'){
			fieldInfo = JSON.parse(fieldInfo);
		}
		fieldInfo['reference_module'] = relatedModules;
		tariffEle.data('fieldinfo',fieldInfo);
	},
	getBlockSequence: function (itemBlock) {
		return itemBlock.find('.copyItemButton').data('seq');
	},

	openPopUp : function(e){
		var thisInstance = this;
		var parentElem = jQuery(e.target).closest('td');
		var itemBlock = parentElem.closest('tbody.itemBlock');
		var tdElement = parentElem.closest('td.fieldValue');
		var currentRow = thisInstance.getBlockSequence(itemBlock);
		var sourceField = tdElement.find('.sourceField').attr('name');

		var groupFieldName = "commissionplan_group_"+currentRow;
		var itemCodeFromFieldName = "itemcodefrom_"+currentRow;
		var itemCodeToFieldName = "itemcodeto_"+currentRow;
		if(sourceField == groupFieldName){
			var itemCodeFromVal = jQuery('[name="'+itemCodeFromFieldName+'"]').val();
			var itemCodeToVal = jQuery('[name="'+itemCodeToFieldName+'"]').val();
			if(itemCodeFromVal != '' || itemCodeToVal != ''){
				return;
			}
		}else if(sourceField == itemCodeFromFieldName || sourceField == itemCodeToFieldName){
			var groupVal = jQuery('[name="'+groupFieldName+'"]').val();
			if(groupVal != ''){
				return;
			}
		}
		var params = this.getPopUpParams(parentElem);

		var isMultiple = false;
		if(params.multi_select) {
			isMultiple = true;
		}

		var sourceFieldElement = jQuery('input[class="sourceField"]',parentElem);

		var prePopupOpenEvent = jQuery.Event(Vtiger_Edit_Js.preReferencePopUpOpenEvent);
		sourceFieldElement.trigger(prePopupOpenEvent);

		if(prePopupOpenEvent.isDefaultPrevented()) {
			return ;
		}

		var popupInstance =Vtiger_Popup_Js.getInstance();
		popupInstance.show(params,function(data){
			var responseData = JSON.parse(data);
			var dataList = new Array();
			for(var id in responseData){
				var data = {
					'name' : responseData[id].name,
					'id' : id
				}
				dataList.push(data);
				if(!isMultiple) {
					thisInstance.setReferenceFieldValue(parentElem, data);
				}
			}

			if(isMultiple) {
				sourceFieldElement.trigger(Vtiger_Edit_Js.refrenceMultiSelectionEvent,{'data':dataList});
			}
			sourceFieldElement.trigger(Vtiger_Edit_Js.postReferenceSelectionEvent,{'data':responseData});
		});
	},
	setReferenceFieldValue : function(container, params) {
		var sourceField = container.find('input[class="sourceField"]').attr('name');
		var fieldElement = container.find('input[name^="'+sourceField+'"]');

		var sourceFieldDisplay = sourceField+"_display";
		var fieldDisplayElement = container.find('input[name^="'+sourceFieldDisplay+'"]');
		var popupReferenceModule = container.find('input[name^="popupReferenceModule"]').val();

		var selectedName = params.name;
		var id = params.id;

		fieldElement.val(id)
		fieldDisplayElement.val(selectedName).attr('readonly',true);
		fieldElement.trigger(Vtiger_Edit_Js.referenceSelectionEvent, {'source_module' : popupReferenceModule, 'record' : id, 'selectedName' : selectedName});
		fieldDisplayElement.validationEngine('closePrompt',fieldDisplayElement);

		var itemBlock = container.closest('tbody.itemBlock');
		var currentRow = this.getBlockSequence(itemBlock);
		var groupFieldName = "commissionplan_group_"+currentRow;
		var itemCodeFromFieldName = "itemcodefrom_"+currentRow;
		var itemCodeToFieldName = "itemcodeto_"+currentRow;

		if(sourceField == groupFieldName){
			jQuery('[name="'+itemCodeFromFieldName+'_display"]').val('');
			jQuery('[name="'+itemCodeFromFieldName+'"]').val('');
			jQuery('[name="'+itemCodeToFieldName+'_display"]').val('');
			jQuery('[name="'+itemCodeToFieldName+'"]').val('');
			jQuery('[name="'+itemCodeFromFieldName+'_display"]').prop('readonly',true);
			jQuery('[name="'+itemCodeToFieldName+'_display"]').prop('readonly',true);
		}else if(sourceField == itemCodeFromFieldName || sourceField == itemCodeToFieldName){
			jQuery('[name="'+groupFieldName+'_display"]').prop('readonly',true);
			jQuery('[name="'+groupFieldName+'"]').val('');
			jQuery('[name="'+groupFieldName+'_display"]').val('');
		}
	},
	getPopUpParams : function(container) {
		var params = {};
		var sourceModule = app.getModuleName();
		var popupReferenceModule = jQuery('input[name^="popupReferenceModule"]',container).val();
		var sourceFieldElement = jQuery('input[class="sourceField"]',container);
		var fieldInfo = sourceFieldElement.data('fieldinfo');
		var sourceField = fieldInfo.name;
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
		}

		if(isMultiple) {
			params.multi_select = true ;
		}
		return params;
	},
	referenceModulePopupRegisterEvent : function(container){
		var thisInstance = this;
		container.off("click",'.relatedPopup').on("click",'.relatedPopup',function(e){
			thisInstance.openPopUp(e);
		});

		container.find('.referenceModulesList').chosen().change(function(e){
			var element = jQuery(e.currentTarget);
			var closestTD = element.closest('td').next();
			var popupReferenceModule = element.val();
			var referenceModuleElement = jQuery('input[name^="popupReferenceModule"]', closestTD);
			var prevSelectedReferenceModule = referenceModuleElement.val();
			referenceModuleElement.val(popupReferenceModule);

			//If Reference module is changed then we should clear the previous value
			if(prevSelectedReferenceModule != popupReferenceModule) {
				closestTD.find('.clearReferenceSelection').trigger('click');
			}
		});
	},
	registerClearReferenceSelectionEvent : function(container) {
		var thisInstance = this;
		container.find('.clearReferenceSelection').on('click', function(e){
			var element = jQuery(e.currentTarget);
			var parentTdElement = element.closest('td');
			var fieldNameElement = parentTdElement.find('.sourceField');
			var fieldName = fieldNameElement.attr('name');
			var itemBlock = parentTdElement.closest('tbody.itemBlock');
			var currentRow = thisInstance.getBlockSequence(itemBlock);
			var groupFieldName = "commissionplan_group_"+currentRow;
			var itemCodeFromFieldName = "itemcodefrom_"+currentRow;
			var itemCodeToFieldName = "itemcodeto_"+currentRow;
			if(fieldNameElement.val() == '')return;

			fieldNameElement.val('');
			parentTdElement.find('#'+fieldName+'_display').removeAttr('readonly').val('');

			if(fieldName == groupFieldName){
				if(jQuery('[name="'+itemCodeFromFieldName+'"]').val() ==''){
					jQuery('[name="'+itemCodeFromFieldName+'_display"]').removeAttr('readonly').val('');
				}else{
					return;
				}
				if(jQuery('[name="'+itemCodeToFieldName+'"]').val() ==''){
					jQuery('[name="'+itemCodeToFieldName+'_display"]').removeAttr('readonly').val('');
				}else{
					return;
				}
			}else if(fieldName == itemCodeFromFieldName || fieldName == itemCodeToFieldName){
				var itemCodeFromVal = jQuery('[name="'+itemCodeFromFieldName+'"]').val();
				var itemCodeToVal = jQuery('[name="'+itemCodeToFieldName+'"]').val();
				if(jQuery('[name="'+groupFieldName+'"]').val() =='' && itemCodeFromVal =='' && itemCodeToVal ==''){
					jQuery('[name="'+groupFieldName+'_display"]').removeAttr('readonly').val('');
				}else{
					return;
				}
			}
			element.trigger(Vtiger_Edit_Js.referenceDeSelectionEvent);
			e.preventDefault();
		})
	},

    registerRecordPreSaveEvent: function (form) {
        var thisInstance = this;
        form.on(Vtiger_Edit_Js.recordPreSave, function (e, data) {
            var errors = '';

            if(thisInstance.checkRange('CommissionPlansFilter_editView_fieldName_miles_from', 'CommissionPlansFilter_editView_fieldName_miles_to', false)){
                errors += app.vtranslate('JS_INVALID_MILES') + '<br />';
            }

            if(thisInstance.checkRange('CommissionPlansFilter_editView_fieldName_weight_from', 'CommissionPlansFilter_editView_fieldName_weight_to', false)){
                errors += app.vtranslate('JS_INVALID_WEIGHT') + '<br />';
            }

            if(thisInstance.checkRange('CommissionPlansFilter_editView_fieldName_effective_date_from', 'CommissionPlansFilter_editView_fieldName_effective_date_to', true)){
                errors += app.vtranslate('JS_INVALID_EFFECTIVEDATE') + '<br />';
            }

            if(errors != ''){
                bootbox.alert(errors);
                e.preventDefault();
                return false;
            }

        });
    },

    checkRange: function(from, to, isDateField){
        var fromVal = jQuery('#'+from).val();
        var toVal   = jQuery('#'+to).val();
        if(isDateField){
            var fromVal = new Date(fromVal);
            var toVal = new Date(toVal);
        }else{
            var fromVal = parseInt(fromVal);
            var toVal   = parseInt(toVal);
        }
        return fromVal > toVal;
    },

	registerBasicEvents : function(container){
		this._super(container);
		this.setPicklistValueForTariff(container)
		this.registerEventForBusinessLineField(container)

	}
});
