/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js('Cubesheets_Edit_Js',{},{

	effectiveTariffData : false,

	/*//Event that will triggered when reference field is selected
	referenceSelectionEvent : 'Vtiger.Reference.Selection',

	//Event that will triggered when reference field is selected
	referenceDeSelectionEvent : 'Vtiger.Reference.DeSelection',

	//Event that will triggered before saving the record
	recordPreSave : 'Vtiger.Record.PreSave',

	refrenceMultiSelectionEvent : 'Vtiger.MultiReference.Selection',

	preReferencePopUpOpenEvent : 'Vtiger.Referece.Popup.Pre',

	editInstance : false,

    postReferenceSelectionEvent: 'Vtiger.PostReference.Selection',

	/*
	 autopopulate survey name on surveys
	 */
	setSurveyName : function() {
		var thisInstance = this;

		if(app.getModuleName() == 'Cubesheets' && jQuery('input[name="cubesheet_name"]').length>0 && jQuery('input[name="sourceRecord"]').length>0 && jQuery('input[name="sourceModule"]').length>0) {
			var today = new Date();
			var dateString =
				("0" + (today.getMonth() + 1)).slice(-2) +
				'-' + ("0" + (today.getDate())).slice(-2) +
				'-' + today.getFullYear() +
				' ' + today.getHours() +
				':' + (today.getMinutes() < 10 ? '0' : '') + today.getMinutes() +
				':' + today.getSeconds();

			// Check if survey has a name associated with it
			var params = {
				'record': jQuery('input[name="sourceRecord"]').val(),
				'source_module': jQuery('input[name="sourceModule"]').val(),
			};
			var name = '';
			var url = "index.php?module="+app.getModuleName()+"&action=GetData&record="+params['record']+"&source_module="+params['source_module'];
			AppConnector.request(url).then(
				function(data){
                                    if(data.success){
                                        if(data.result.data.potentialname){
                                            if(data.result.data.potentialname.length>0) {
                                                    // A name is associated with the survey
                                                    var nameArray = data.result.data.potentialname.split(' ');
                                                    var lastName;
                                                    if (nameArray.length > 2) {
                                                            lastName = nameArray[nameArray.length - 2].toUpperCase() + ' ' + nameArray[nameArray.length - 1].toUpperCase();
                                                    }
                                                    else {
                                                            lastName = nameArray[nameArray.length - 1].toUpperCase();
                                                    }
                                                    var firstInit = nameArray[0].substr(0, 1).toUpperCase();
                                                    name = lastName + ', ' + firstInit + ' ' + dateString;

                                                    jQuery('input[name="cubesheet_name"]').val(name);
                                            } else {
                                                    jQuery('input[name="cubesheet_name"]').val('Survey '+dateString);
                                            }
                                        }
                                        if(data.result.data.orders_no){
                                            if(data.result.data.orders_no.length>0) {
                                                    // An Order number is associated with the survey
                                                    name = data.result.data.orders_no + ' ' + dateString;

                                                    jQuery('input[name="cubesheet_name"]').val(name);
                                            } else {
                                                    jQuery('input[name="cubesheet_name"]').val('Survey '+dateString);
                                            }
                                        }
                                    }
				},
				function(error){
					jQuery('input[name="cubesheet_name"]').val('Survey '+dateString);
				}
			)
		}
	},

	registerEffectiveDateLimits: function() {
	    thisInstance = this;
		record = jQuery('input[name="record"]').val();
		dateEle = jQuery('input[name="effective_date"]');

		if(record == '') {
			dateEle.val(thisInstance.convertedDate(dateEle));
		}
	},


    convertedDate: function(dateEle){
        var today = new Date();
        var month = ("0" + (today.getMonth()+1)).slice(-2);
        var day = ("0" + today.getDate()).slice(-2);
        var userSettings = dateEle.data('date-format');
        switch(userSettings) {
            case 'yyyy-mm-dd':
                var formattedDate = today.getFullYear()+'-'+month+'-'+day;
                break;
            case 'dd-mm-yyyy':
                var formattedDate = day+'-'+month+'-'+today.getFullYear();
                break;
            default:
                var formattedDate = month+'-'+day+'-'+today.getFullYear();
                break;
        }
        return formattedDate;
    },

	updateEffectiveTariffPicklist: function() {
        var data = this.effectiveTariffData;
        var res = {};

        // National Account ??
        var currentBusinessLine = jQuery('#movetype').val();
        var allowInterstate = [
            'Interstate',
            'Interstate Move',
            'HHG - International Air',
            'HHG - International Sea',
            'HHG - International Surface',
            'International Land',
            'Auto Transportation',
            'Sirva Military',
            'Military'
        ].indexOf(currentBusinessLine) >= 0;

        var allowIntrastate = [
            'Intrastate'
        ].indexOf(currentBusinessLine) >= 0;

        var allowLocal = [
            'Local',
            'Local Move',
            'Intrastate',
            'Intrastate Move',
            'Commercial - Distribution',
            'Commercial - International Air',
            'Commercial - Record Storage',
            'Commercial - Storage',
            'Commercial - Asset Management',
            'Commercial - Project',
            'Work Space - MAC',
            'Work Space - Special Services',
            'Work Space - Commodities'
        ].indexOf(currentBusinessLine) >= 0;

        for(var k in data) {
            var d = data[k];
            if(d['is_managed_tariff']) {
                if(d['is_intrastate']) {
                    if(!allowIntrastate) {
                            continue;
                    }
                } else {
                    if(!allowInterstate) {
                            continue;
                    }
                }
            } else {
                if(!allowLocal) {
                        continue;
                }
            }
            res[d['tariff_id']] = d['tariff_name'];
        }
        Vtiger_Edit_Js.setPicklistOptions('effective_tariff', res);
	},

	registerEvents: function(){
		this._super();
		this.setSurveyName();
		this.registerEffectiveDateLimits();
		this.effectiveTariffData = jQuery.parseJSON(jQuery('#allAvailableTariffs').val());
		this.updateEffectiveTariffPicklist();
		//jQuery('select[name="move_type"]').validationEngine('showPrompt', 'Invalid Test' , 'error', 'topRight', true);

		app.registerEventForDatePickerFields('#EditView');

		var params = app.validationEngineOptions;
		params.onValidationComplete = function(element,valid){
			if(valid){
				var ckEditorSource = editViewForm.find('.ckEditorSource');
				if(ckEditorSource.length > 0){
					var ckEditorSourceId = ckEditorSource.attr('id');
					var fieldInfo = ckEditorSource.data('fieldinfo');
					var isMandatory = fieldInfo.mandatory;
					var CKEditorInstance = CKEDITOR.instances;
					var ckEditorValue = jQuery.trim(CKEditorInstance[ckEditorSourceId].document.getBody().getText());
					if(isMandatory && (ckEditorValue.length === 0)){
						var ckEditorId = 'cke_'+ckEditorSourceId;
						var message = app.vtranslate('JS_REQUIRED_FIELD');
						jQuery('#'+ckEditorId).validationEngine('showPrompt', message , 'error','topLeft',true);
						return false;
					}else{
						return valid;
					}
				}
				return valid;
			}
			return valid
		}
	//this.triggerDisplayTypeEvent();
	},

	getPopUpParams : function(container) {
		var params = {};
		var sourceModule = app.getModuleName();
		var popupReferenceModule = jQuery('input[name="popupReferenceModule"]',container).val();
		var sourceFieldElement = jQuery('input[class="sourceField"]',container);
		var sourceField = sourceFieldElement.attr('name');
		var sourceRecordElement = jQuery('input[name="record"]');
		var sourceRecordId = '';
		var potential_id = jQuery('input[name="potential_id"]').val();
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
			'src_record' : sourceRecordId,
			'potential_id' : potential_id
		};
		if(isMultiple) {
			params.multi_select = true ;
		}
		return params;
	},

	getReferenceSearchParams : function(element){
		var tdElement = jQuery(element).closest('td');
		var params = {};
		var searchModule = this.getReferencedModuleName(tdElement);
		params.search_module = searchModule;
		if(searchModule == 'Surveys'){
			var parent_id = jQuery('input[name="potential_id"]').val();
			params.parent_id = parent_id;
			params.parent_module = 'Opportunities';
			return params;
		}

	},
});
