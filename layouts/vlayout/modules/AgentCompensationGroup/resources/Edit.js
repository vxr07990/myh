/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
Vtiger_Edit_Js("AgentCompensationGroup_Edit_Js",{},{

	registerEventForTypeField : function() {
		var editViewForm = this.getForm();
		editViewForm.on('change','[name="agentcompgr_type"]', function (e) {
			var agentcompgr_businessline=editViewForm.find('select[name="agentcompgr_businessline[]"]').val();
			var element=jQuery(e.currentTarget);
			var selectedModule = element.val();
			var isTariffs=false;
			var isTariffsManager=false;
			if(selectedModule == 'Tariffs') {
				if(agentcompgr_businessline != null) {
					jQuery.each(agentcompgr_businessline, function (idx, val) {
						if (val == 'All') {
							isTariffs = true;
							isTariffsManager = true;
							return false;
						} else if (val.indexOf('Local') != -1 || val.indexOf('International') != -1) {
							isTariffs = true;
						} else if (val.indexOf('Interstate') != -1 || val.indexOf('Intrastate') != -1) {
							isTariffsManager = true;
						}
					});

					if (isTariffs && isTariffsManager) {
						selectedModule = 'AgentCompensationGroup';
					} else if (isTariffs) {
						selectedModule = 'Tariffs';
					} else if (isTariffsManager) {
						selectedModule = 'TariffManager';
					}
				}
			}

			var tariffservices_assigntorecord = editViewForm.find('input[name="agentcompgr_tariffcontract"]');
			var parentTd=tariffservices_assigntorecord.closest('td.fieldValue');
			var prevSelectedReferenceModule = parentTd.find('input[name="popupReferenceModule"]').val();
			parentTd.find('input[name="popupReferenceModule"]').val(selectedModule);
			if(selectedModule != prevSelectedReferenceModule) {
				parentTd.find('.clearReferenceSelection').trigger('click');
			}
		});
		editViewForm.on('change','[name="agentcompgr_businessline[]"]', function (e) {
			editViewForm.find('[name="agentcompgr_type"]').trigger("change");
		});
	},

    registerEventForOwnerField : function () {
        var thisInstance = this;
        var editViewForm = this.getForm();
        editViewForm.on('change','[name="agentid"]', function (e) {
            var agentid = jQuery(this).val();
            if(agentid) {
                thisInstance.loadAgentCompensationItems(agentid);
                thisInstance.getRevenueGroupingItem(agentid);
            }
        });
		var url = window.location.href;
		var isDuplicate = url.search("isDuplicate=true");
        if(editViewForm.find('[name="record"]').val() == '' && isDuplicate == -1) {
            editViewForm.find('[name="agentid"]').trigger("change");
        }
        thisInstance.getRevenueGroupingItem(jQuery('[name="agentid"]').val());
    },

    loadAgentCompensationItems: function (agentid) {
        var params = {};
        params['mode'] = 'showEditForm';
        params['module'] = 'AgentCompensationItems';
        params['view'] = 'MassActionAjax';
        params['agentId'] = agentid;
        AppConnector.request(params).then(
            function (data) {
                if (data) {
                    var newItemList = jQuery(data);
                    app.showSelect2ElementView(newItemList.find('.select2'));
                    jQuery('div.AgentCompensationItemsList').html(newItemList);

                }
            }
        )
    },

    getRevenueGroupingItem: function (agentid) {

        var editViewForm = this.getForm();
        var params = {};
        params['mode'] = 'getRevenueGroupingItem';
        params['module'] = 'Escrows';
        params['action'] = 'ActionAjax';
        params['agentId'] = agentid;
        AppConnector.request(params).then(
            function (data) {
                if (data.success) {
                    editViewForm.find('[name^="escrows_chargeback_type_"]').each(function (idx, elm) {
                        var selectedValue = jQuery(elm).attr('data-selected-value');
                        var newOptions='<option value="">Select an Option</option>';
                        jQuery.each(data.result, function (k,val) {
                            if(val == selectedValue ){
                                var selected = "selected";
                            }else{
                                var selected ='';
                            }
                            newOptions +='<option value="'+val+'" '+selected+'>'+val+'</option>';
                        });
                        jQuery(elm).html(newOptions);
                        jQuery(elm).trigger('liszt:updated');
                    });
                }
            }
        )
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

        var form = this.getForm();
        var agentidElement  = form.find('[name="agentid"]');
        if(agentidElement.length > 0 && agentidElement.val().length > 0) {
            params.agentId = agentidElement.val();
        }
        return params;
    },

	openPopUp : function(e){
		var thisInstance = this;
		var parentElem = jQuery(e.target).closest('td');
		var popupReferenceModule = jQuery('input[name="popupReferenceModule"]',parentElem).val();
		if(popupReferenceModule) {
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
		}
	},

	registerEvents : function(){
		this._super();
		this.registerEventForTypeField();
		this.registerEventForOwnerField();

	}
});
