/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
Settings_Workflows_Edit_Js("Settings_Workflows_Edit2_Js",{},{

	step2Container : false,

	advanceFilterInstance : false,

	init : function() {
		this.initialize();
	},
	/**
	 * Function to get the container which holds all the reports step1 elements
	 * @return jQuery object
	 */
	getContainer : function() {
		return this.step2Container;
	},

	/**
	 * Function to set the reports step1 container
	 * @params : element - which represents the reports step1 container
	 * @return : current instance
	 */
	setContainer : function(element) {
		this.step2Container = element;
		return this;
	},

	/**
	 * Function  to intialize the reports step1
	 */
	initialize : function(container) {
		if(typeof container == 'undefined') {
			container = jQuery('#workflow_step2');
		}
		if(container.is('#workflow_step2')) {
			this.setContainer(container);
		}else{
			this.setContainer(jQuery('#workflow_step2'));
		}
	},

	calculateValues : function(){
		//handled advanced filters saved values.
		var enableFilterElement = jQuery('#enableAdvanceFilters');
		if(enableFilterElement.length > 0 && enableFilterElement.is(':checked') == false) {
			jQuery('#advanced_filter').val(jQuery('#olderConditions').val());
		} else {
			jQuery('[name="filtersavedinnew"]').val("6");
			var advfilterlist = this.advanceFilterInstance.getValues();
			jQuery('#advanced_filter').val(JSON.stringify(advfilterlist));
		}
	},

	submit : function(){
		var aDeferred = jQuery.Deferred();
		var form = this.getContainer();
		this.calculateValues();
		var formData = form.serializeFormData();
		var progressIndicatorElement = jQuery.progressIndicator({
			'position' : 'html',
			'blockInfo' : {
				'enabled' : true
			}
		});
		AppConnector.request(formData).then(
			function(data) {
				form.hide();
				if(data.result) {
					Settings_Vtiger_Index_Js.showMessage({text : app.vtranslate('JS_WORKFLOW_SAVED_SUCCESSFULLY')});
					var workflowRecordElement = jQuery('[name="record"]',form);
					if(workflowRecordElement.val() == '') {
						workflowRecordElement.val(data.result.id);
					}
					var params = {
						module : app.getModuleName(),
						parent : app.getParentModuleName(),
						view : 'Edit',
						mode : 'Step3',
						record : data.result.id
					}
					AppConnector.request(params).then(function(data) {
						aDeferred.resolve(data);
					});
				}
				progressIndicatorElement.progressIndicator({
					'mode' : 'hide'
				})
			},
			function(error,err){

			}
		);
		return aDeferred.promise();
	},

	registerEnableFilterOption : function() {
		jQuery('[name="conditionstype"]').on('change',function(e) {
			var advanceFilterContainer = jQuery('#advanceFilterContainer');
			var currentRadioButtonElement = jQuery(e.currentTarget);
			if(currentRadioButtonElement.hasClass('recreate')){
				if(currentRadioButtonElement.is(':checked')){
					advanceFilterContainer.removeClass('zeroOpacity');
				}
			} else {
				advanceFilterContainer.addClass('zeroOpacity');
			}
		});
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
        return params;
    },

    openPopUp: function (e) {
        var thisInstance = this;
        var parentElem = jQuery(e.target).closest('.conditionRow');

        var params = this.getPopUpParams(parentElem);
        if (params === false) {
            return;
        }

        var isMultiple = false;
        if (params.multi_select) {
            isMultiple = true;
        }

        // check agentid select exists
        if (jQuery('select[name="agentid"]').length > 0) {
            params['agentId'] = jQuery('select[name="agentid"]').val();
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
            var dataList = [];
            for (var id in responseData) {
                var data = {
                    'name': responseData[id].name,
                    'id': id
                };
                dataList.push(data);
                if (!isMultiple) {
                    thisInstance.setReferenceFieldValue(parentElem, data);
                }
            }

            if (isMultiple) {
                sourceFieldElement.trigger(Vtiger_Edit_Js.refrenceMultiSelectionEvent, {'data': dataList});
            }
            sourceFieldElement.trigger(Vtiger_Edit_Js.postReferenceSelectionEvent, {'data': responseData});
        });
    },

    registerClearReferenceSelectionEvent: function (container) {
        container.on('click', '.clearReferenceSelection', function (e) {
            var element = jQuery(e.currentTarget);
            var parentTdElement = element.closest('.conditionRow');
            var fieldNameElement = parentTdElement.find('.sourceField');
            var fieldName = fieldNameElement.attr('name');
            fieldNameElement.val('').trigger('change'); // WHY would you not trigger change?!
            parentTdElement.find('#' + fieldName + '_display').removeAttr('readonly').val('');
            element.trigger(Vtiger_Edit_Js.referenceDeSelectionEvent);
            fieldNameElement.trigger(Vtiger_Edit_Js.referenceDeSelectionEvent);
            e.preventDefault();
        })
    },

    getReferenceSearchParams : function(element){
        var tdElement = jQuery(element).closest('.conditionRow');
        var params = {};
        var searchModule = this.getReferencedModuleName(tdElement);
        params.search_module = searchModule;
        params.fieldName = tdElement.find('input.sourceField').attr("name");
        params.module = jQuery('input[name="module_name"]').val();

        return params;
    },

	registerEvents : function(){
		var opts = app.validationEngineOptions;
		// to prevent the page reload after the validation has completed
		opts['onValidationComplete'] = function(form,valid) {
            //returns the valid status
            return valid;
        };
		opts['promptPosition'] = "bottomRight";
		jQuery('#workflow_step2').validationEngine(opts);

		var container = this.getContainer();
        // When you come to step2 we should remove validation for condition values other than rawtwxt
        jQuery('button[type="submit"]',container).on('click',function(e){
            var fieldUiHolders = jQuery('.fieldUiHolder')
            for(var i=0; i<fieldUiHolders.length;i++){
                var fieldUiHolder  = fieldUiHolders[i];
                var fieldValueElement = jQuery('.getPopupUi',fieldUiHolder);
                var valueType = jQuery('[name="valuetype"]',fieldUiHolder).val();
                if(valueType != 'rawtext'){
                    fieldValueElement.removeAttr('data-validation-engine');
                    fieldValueElement.removeClass('validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]');
                }
            }
        });
		app.changeSelectElementView(container);
		this.advanceFilterInstance = Vtiger_AdvanceFilter_Js.getInstance(jQuery('.filterContainer',container));
		this.getPopUp();
		if(jQuery('[name="filtersavedinnew"]',container).val() == '5'){
			this.registerEnableFilterOption();
		}

        var container = jQuery('#workflow_step2');
        this.referenceModulePopupRegisterEvent(container);
        this.registerClearReferenceSelectionEvent(container);
        this.registerAutoCompleteFields(container);
	}
});
