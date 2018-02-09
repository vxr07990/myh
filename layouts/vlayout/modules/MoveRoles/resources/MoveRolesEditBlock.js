Vtiger_Edit_Js("MoveRoles_EditBlock_Js", {
    getInstance: function() {
        return new MoveRoles_EditBlock_Js();
    }
}, {
    registerRemoveMoveRolesButton : function(){
        jQuery('html').on( 'click', '.removeMoveRoles', function(){
            if(jQuery(this).siblings('input:hidden[name^="moverolesidId"]').val() == 'none'){
                jQuery(this).parent().parent().remove()
            } else{
                jQuery(this).parent().parent().addClass('hide');
                jQuery(this).siblings('input:hidden[name^="moverolesDelete"]').val('deleted');
            }
        });
    },

    registerAddMoveRolesButtons : function() {
        var thisInstance = this;
        var table = jQuery('[name^="MoveRolesTable"]').find('tbody');

        var button = jQuery('.addMoveRoles');

        var addHandler = function() {
            var newRow = jQuery('.defaultMoveRoles').clone();
            var sequenceNode = jQuery("input[name='numAgents']");
            //a beautiful way to handle the tally that tracks the number of the capacitycalendarcounter we are currently adding
            var sequence = sequenceNode.val();
            sequence++;
            sequenceNode.val(sequence);
            newRow.addClass('newMoveRoles');
            //remove the classes from the default row that cause it to be hidden and labeled
            newRow.removeClass('hide defaultMoveRoles');


            //add the new row to the table
            newRow = newRow.appendTo(table);
            newRow.find('input, select').each(function(idx, ele){
                jQuery(ele).attr('name', jQuery(ele).attr('name')+'_'+sequence);
                jQuery(ele).attr('id', jQuery(ele).attr('id')+'_'+sequence);

                if(jQuery(ele).is('select')) {
                    jQuery(ele).addClass('chzn-select');
                }else{
                    jQuery(ele).css('width', '150px')
                }
            });
            //notifiy the js library that handles the reformating the ui has changed
            newRow.find('.chzn-select').chosen();
            jQuery(document).find('select[name^="moveroles_sequence_"]').trigger("change");
            thisInstance.registerClearReferenceSelectionEvent(newRow);
            thisInstance.registerAutoCompleteFields(newRow);
        };
        button.on('click', addHandler);
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
        var fieldInfo = container.find('input[class="sourceField"]').data('fieldinfo');
        var sourceField = fieldInfo.name;
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

        var sourceFieldName= sourceFieldElement.attr('name');
        if( sourceFieldName.indexOf("moveroles_employees") !== -1) {
            var parentTr = sourceFieldElement.closest('tr.moverolsRow');
            var parentIdElement  = parentTr.find('[name^="moveroles_role"][class="sourceField"]');
            if(parentIdElement.length > 0 && parentIdElement.val().length > 0 && parentIdElement.val() != 0) {
                params.relatedparent_id = parentIdElement.val();
                params.relatedparent_module = 'EmployeeRoles';
            }
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
        container.find('.clearReferenceSelection').on('click', function(e){
            var element = jQuery(e.currentTarget);
            var parentTdElement = element.closest('td');
            var fieldNameElement = parentTdElement.find('.sourceField');
            var fieldInfo = fieldNameElement.data('fieldinfo');
            var fieldName = fieldInfo.name;
            fieldNameElement.val('');
            parentTdElement.find('[name^="'+fieldName+'_display"]').removeAttr('readonly').val('');
            element.trigger(Vtiger_Edit_Js.referenceDeSelectionEvent);
            e.preventDefault();
        })
    },

    /**
     * Function which will handle the reference auto complete event registrations
     * @params - container <jQuery> - element in which auto complete fields needs to be searched
     */
    registerAutoCompleteFields : function(container) {
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
                if(jQuery('select[name="agentid"]').length>0){
                    params['agentId'] = jQuery('select[name="agentid"]').val();
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
            'open' : function(event,ui) {
                //To Make the menu come up in the case of quick create
                jQuery(this).data('autocomplete').menu.element.css('z-index','100001');

            }
        };
        $(document).on('keydown.autoComplete', '.autoComplete', function() {
            $(this).autocomplete(autoCompleteOptions);
        });
        container.find('input.autoComplete').autocomplete(autoCompleteOptions);
    },

    /**
     * Function to search module names
     */
    searchModuleNames : function(params) {
        var aDeferred = jQuery.Deferred();

        if(typeof params.module == 'undefined') {
            params.module = app.getModuleName();
        }
        if(typeof params.action == 'undefined') {
            params.action = 'BasicAjax';
        }
        if (params.search_module == 'Employees' ) {
            var fieldName=params.fieldName;
            var parentTr = jQuery(document).find('[name="'+fieldName+'"]').closest('tr.moverolsRow');
            var parentIdElement  = parentTr.find('[name^="moveroles_role"]');
            if(parentIdElement.length > 0 && parentIdElement.val().length > 0 && parentIdElement.val() != 0) {
                params.parent_id = parentIdElement.val();
                params.parent_module = 'EmployeeRoles';
                params.module = 'MoveRoles';
                params.fieldName =null;
            }
        }

        AppConnector.request(params).then(
            function(data){
                aDeferred.resolve(data);
            },
            function(error){
                aDeferred.reject();
            }
        )
        return aDeferred.promise();
    },

    /**
     * Function to get reference search params
     */
    getReferenceSearchParams : function(element){
        var tdElement = jQuery(element).closest('td');
        var params = {};
        var searchModule = this.getReferencedModuleName(tdElement);
        params.search_module = searchModule;
        params.fieldName = tdElement.find('input.sourceField').attr("name");
        return params;
    },

    getReferencedModuleName : function(parenElement){
        return jQuery('input[name^="popupReferenceModule"]',parenElement).val();
    },

    registerEvents : function() {
        // Update field name
        jQuery(document).find('tr.moverolesRow').each(function (i,tre) {
            var tr= jQuery(tre);
            if(!tr.hasClass('hide')) {
                var sequence = tr.find('.row_num').val();
                tr.find('input, select').each(function(idx, ele){
                    jQuery(ele).attr('name', jQuery(ele).attr('name')+'_'+sequence);
                    jQuery(ele).attr('id', jQuery(ele).attr('id')+'_'+sequence);
                    if(jQuery(ele).is('select')) {
                        jQuery(ele).css('width', '150px')
                    }else{
                        jQuery(ele).css('width', '150px')
                    }

                });
            }
        });
        var container=jQuery('table[name="MoveRolesTable"]');
        this.referenceModulePopupRegisterEvent(container);
        this.registerAddMoveRolesButtons();
        this.registerRemoveMoveRolesButton();
        this.registerClearReferenceSelectionEvent(container);
        this.registerAutoCompleteFields(container);
    }
});

jQuery(document).ready(function() {
    var instance = MoveRoles_EditBlock_Js.getInstance();
    instance.registerEvents();
});
