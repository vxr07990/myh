Vtiger_Edit_Js("MoveRoles_Edit_Js",{},
{
    /**
     * Function to get popup params
     */
    getPopUpParams : function(container) {
        var params = this._super(container);
        var sourceFieldElement = jQuery('input[class="sourceField"]',container);

        if(sourceFieldElement.attr('name') == 'moveroles_employees') {
            var form = this.getForm();
            var parentIdElement  = form.find('[name="moveroles_role"]');
            if(parentIdElement.length > 0 && parentIdElement.val().length > 0 && parentIdElement.val() != 0) {
                var closestContainer = parentIdElement.closest('td');
                params['related_parent_id'] = parentIdElement.val();
                params['related_parent_module'] = closestContainer.find('[name="popupReferenceModule"]').val();
            }
        }
        return params;
    },

    setReferenceFieldValue : function(container, params) {
        var sourceField = container.find('input[class="sourceField"]').attr('name');
        var fieldElement = container.find('input[name="'+sourceField+'"]');
        var sourceFieldDisplay = sourceField+"_display";
        var fieldDisplayElement = container.find('input[name="'+sourceFieldDisplay+'"]');
        var popupReferenceModule = container.find('input[name="popupReferenceModule"]').val();

        var selectedName = params.name;
        var id = params.id;
        //remove Employess
        var preMoveRole= jQuery('input[name="moveroles_employees_display"]').prev();
        preMoveRole.trigger('click');

        fieldElement.val(id);
        fieldDisplayElement.val(selectedName).attr('readonly',true);
        fieldElement.trigger(Vtiger_Edit_Js.referenceSelectionEvent, {'source_module' : popupReferenceModule, 'record' : id, 'selectedName' : selectedName});

        fieldDisplayElement.validationEngine('closePrompt',fieldDisplayElement);
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
            var form = this.getForm();
            var parentIdElement  = form.find('[name="moveroles_role"]');
            if(parentIdElement.length > 0 && parentIdElement.val().length > 0) {
                var closestContainer = parentIdElement.closest('td');
                params.parent_id = parentIdElement.val();
                params.parent_module = closestContainer.find('[name="popupReferenceModule"]').val();
            }
        }

        if(params.search_module == 'Employees') {
            if(typeof params.parent_id !='undefined' && params.parent_id !='') {
                AppConnector.request(params).then(
                    function(data){
                        aDeferred.resolve(data);
                    },
                    function(error){
                        aDeferred.reject();
                    }
                )
            }else{
                var data = {
                    result:''
                };
                aDeferred.resolve(data);
            }

        }else{
            AppConnector.request(params).then(
                function(data){
                    aDeferred.resolve(data);
                },
                function(error){
                    aDeferred.reject();
                }
            )
        }

        return aDeferred.promise();
    },

    referenceModulePopupRegisterEvent : function(container){
        var thisInstance = this;
        container.on("click",'.relatedPopup',function(e){
            // get related module
            var td=jQuery(e.currentTarget).closest('td');
            var popupReferenceModule = td.find('[name="popupReferenceModule"]').val();
            if(popupReferenceModule == 'Employees') {
                // Check Role value
                var moveroles_role = jQuery('[name="moveroles_role"]').val();
                if(moveroles_role !='') {
                    thisInstance.openPopUp(e);
                }else{
                    return;
                }
            }else{
                thisInstance.openPopUp(e);
            }
        });
        container.find('.referenceModulesList').chosen().change(function(e){
            var element = jQuery(e.currentTarget);
            var closestTD = element.closest('td').next();
            var popupReferenceModule = element.val();
            var referenceModuleElement = jQuery('input[name="popupReferenceModule"]', closestTD);
            var prevSelectedReferenceModule = referenceModuleElement.val();
            referenceModuleElement.val(popupReferenceModule);

            //If Reference module is changed then we should clear the previous value
            if(prevSelectedReferenceModule != popupReferenceModule) {
                closestTD.find('.clearReferenceSelection').trigger('click');
            }
        });
    },
});