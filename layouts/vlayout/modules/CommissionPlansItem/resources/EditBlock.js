CommissionPlansFilter_Edit_Js("CommissionPlansItem_EditBlock_Js", {
    getInstance: function() {
        return new CommissionPlansItem_EditBlock_Js();
    }
}, {
    registerAddItemEvent : function(){
        var thisInstance = this;
        jQuery('button[name="addItem"]').off('click').on('click', thisInstance.addItemHandler);
        jQuery('button[name="addItem2"]').off('click').on('click', thisInstance.addItemHandler);
        jQuery('.copyItemButton').off('click').on('click', thisInstance.addItemHandler);
    },

    addItemHandler : function(){

        var thisInstance = CommissionPlansItem_EditBlock_Js.getInstance();
        var defaultItem = jQuery('.defaultItem');
        var currentSeq = false;
        var newItem = defaultItem.clone().removeClass('defaultItem hide').appendTo('table[name="commissionPlansItemTable"]');
        newItem.find('.itemContent').removeClass('hide');
        thisInstance.deleteItemEvent();
        var itemCounter = jQuery('#numItems');
        var itemCount = jQuery('.itemBlock').not('.hide').length;
        itemCounter.val(itemCount);

        newItem.find('.itemTitle').html('<b>&nbsp;&nbsp;&nbsp;Item '+itemCount+'</b>');
        newItem.addClass('item_'+itemCount);

        if(jQuery(this).is('.copyItemButton')){
            currentSeq = jQuery(this).data('seq');
            newItem.find('.itemTitle b').html('&nbsp;&nbsp;&nbsp; '+jQuery('[name="commissiontype_'+currentSeq+'"]').val());
        }
        newItem.find('input, select').not('input:hidden[name="popupReferenceModule"]').each(function(){
            var name = jQuery(this).attr('name');
            var id = jQuery(this).attr('id');
            if(name.indexOf("_display") !=-1){
                var arrFieldName =  name.split('_display');
                var newName = arrFieldName[0]+'_'+itemCount+'_display';
                jQuery(this).attr('name', newName);
                jQuery(this).attr('id', newName);

            }else {
                jQuery(this).attr('name', name+'_'+itemCount);
                jQuery(this).attr('id', id+'_'+itemCount);
            }

            if(currentSeq !== false){
                if(name.indexOf("_display") !=-1){
                    jQuery(this).val(jQuery('[name="'+arrFieldName[0]+'_'+currentSeq+'_display"]').val());
                    jQuery(this).prop('readonly',true);
                }else{
                    jQuery(this).val(jQuery('[name="'+name+'_'+currentSeq+'"]').val());
                }
            }
            if(jQuery(this).is('select')) {
                jQuery(this).addClass('chzn-select');
            }
        });

        newItem.find('.chzn-select').chosen();
        newItem.find('.chzn-select').trigger('liszt:updated');
        newItem.find('.chzn-select').select2('destroy');
        var editInstance = Vtiger_Edit_Js.getInstance();
        editInstance.registerBasicEvents(newItem);
        newItem.itemCount = itemCount;
        thisInstance.referenceModulePopupRegisterEvent(newItem);
        thisInstance.registerEventForCommissionType(newItem);
        thisInstance.registerAddItemEvent();
        newItem.find('.copyItemButton').data('seq',itemCount);
        return newItem;
    },


    deleteItemEvent : function(){
        jQuery('.deleteItemButton').on('click', function(){
            var bodyContainer = jQuery(this).closest('tbody');
            var itemId = bodyContainer.find('input:hidden[name^="CommissionPlansItem_id_"]').val();
            if(itemId && itemId !='none'){
                bodyContainer.find('input:hidden[name^="CommissionPlansItem_deleted"]').val('deleted');
                bodyContainer.addClass('hide');
            }else {
                bodyContainer.remove();
            }
        });
    },

    openPopUp : function(e){
        var thisInstance = this;
        var parentElem = jQuery(e.target).closest('td');

        var params = this.getPopUpParams(parentElem);

        var isMultiple = false;
        if(params.multi_select) {
            isMultiple = true;
        }

        // check agentid select exists
        if(jQuery('select[name="agentid"]').length>0){
            params['agentId'] = jQuery('select[name="agentid"]').val();
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
            var dataList = [];
            for(var id in responseData){
                var data = {
                    'name' : responseData[id].name,
                    'id' : id
                };

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
        // var fieldInfo = container.find('input[class="sourceField"]').data('fieldinfo');
        var sourceField = container.find('input[class="sourceField"]').attr('name');
        if (sourceField == 'itemcodefrom' || sourceField == 'itemcodeto'){
            sourceField = container.find('input[class="sourceField"]').attr('name');
        }
        var fieldElement = container.find('input[name^="'+sourceField+'"]');
        var sourceFieldDisplay = sourceField+"_display";
        var fieldDisplayElement = container.find('input[name^="'+sourceFieldDisplay+'"]');
        var popupReferenceModule = container.find('input[name^="popupReferenceModule"]').val();
        var selectedName = params.name;
        var id = params.id;

        fieldElement.val(id);
        fieldDisplayElement.val(selectedName).attr('readonly',true);
        fieldElement.trigger(Vtiger_Edit_Js.referenceSelectionEvent, {'source_module' : popupReferenceModule, 'record' : id, 'selectedName' : selectedName});

        fieldDisplayElement.validationEngine('closePrompt',fieldDisplayElement);
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
        };

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
    registerEventForCommissionType: function (container) {
        container.find('[name^="commissiontype_"]').on('change',function () {
            var value = jQuery(this).val();
            if(value != '' && value != undefined){
                var titleBlock = jQuery(this).closest('tbody.itemBlock').find('.itemTitle b');
                titleBlock.text(value);
            }
        });
    },
    referenceModulePopupRegisterEvent : function(container){
        var thisInstance = this;
        container.off("click",'.relatedPopup').on("click",'.relatedPopup',function(e){
            var element = jQuery(e.currentTarget);
            var closestTD = element.closest('td');
            var referenceModuleElement = jQuery('input[name="popupReferenceModule"]', closestTD).val();
            if(referenceModuleElement == 'ItemCodes') {
                // check Group
                var itemBlock = closestTD.closest('tbody.itemBlock');
                var commissionplan_group = itemBlock.find('[name^="commissionplan_group_"]').val();
                if(commissionplan_group != '' && commissionplan_group != '0') {
                    var params = {};
                    params.animation = "show";
                    params.type = 'error';
                    params.text = 'Cannot add an Item Code when a Group has been selected';
                    params.title = app.vtranslate('JS_MESSAGE');
                    Vtiger_Helper_Js.showPnotify(params);
                }else{
                    thisInstance.openPopUp(e);
                }
            }else if (referenceModuleElement == 'RevenueGroupingItem') {
                var itemBlock = closestTD.closest('tbody.itemBlock');
                var itemcodefrom = itemBlock.find('[name^="itemcodefrom_"]').val();
                var itemcodeto = itemBlock.find('[name^="itemcodeto_"]').val();
                if((itemcodefrom != '' && itemcodefrom !=0) || (itemcodeto !='' && itemcodeto!='0')) {
                    var params = {};
                    params.animation = "show";
                    params.type = 'error';
                    params.text = 'Cannot add a Group when an Item Code has been selected';
                    params.title = app.vtranslate('JS_MESSAGE');
                    Vtiger_Helper_Js.showPnotify(params);
                }else{
                    thisInstance.openPopUp(e);
                }
            }else{
                thisInstance.openPopUp(e);
            }

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

    registerEvents : function() {
        this.registerAddItemEvent();
        this.deleteItemEvent();
        var container = jQuery('[name="commissionPlansItemTable"]');
        this.referenceModulePopupRegisterEvent(container);
        this.registerEventForCommissionType(container);
    },
});

jQuery(document).ready(function() {
    var instance = CommissionPlansItem_EditBlock_Js.getInstance();
    instance.registerEvents();
});
