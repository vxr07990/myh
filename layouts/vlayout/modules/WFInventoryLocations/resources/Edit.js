Vtiger_Edit_Js("WFInventoryLocations_Edit_Js", {


    registerLocationChange : function(){
        hiddenElement = jQuery('input:hidden[name="location"]');
        hiddenElement.on(Vtiger_Edit_Js.referenceSelectionEvent, function(){
            thisInstance = this;
            hiddenElement = jQuery('input:hidden[name="location"]');
            locationId = hiddenElement.val();
            if(locationId && locationId>0) {
                var dataUrl = "index.php?module=WFInventoryLocations&action=PopulateLocationType&source="+locationId;
                AppConnector.request(dataUrl).then(
                    function(data) {
                        if(data.success) {
                            var locationType = data.result;
                            jQuery('input[name="location_type"]').val(locationType.wflocationtypes_type).trigger('change');
                        }
                    }
                );
            }
        });
    },


    registerClearReferenceSelectionEvent : function(container) {
        container.find('.clearReferenceSelection').on('click', function(e){
            var element = jQuery(e.currentTarget);
            var parentTdElement = element.closest('td');
            var fieldNameElement = parentTdElement.find('.sourceField');
            var fieldName = fieldNameElement.attr('name');
            if(fieldName == 'location'){
                jQuery('input[name="location_type"]').val('').trigger('change');
            } else if (fieldName == 'warehouse'){
                var closestTDSourceField = jQuery('input[name="location"]').closest('td');
                var clearReferenceSelectionBTN = closestTDSourceField.find('.clearReferenceSelection');
                clearReferenceSelectionBTN.trigger('click');
            }
            fieldNameElement.val('').trigger('change'); // WHY would you not trigger change?!
            parentTdElement.find('#'+fieldName+'_display').removeAttr('readonly').val('');
            element.trigger(Vtiger_Edit_Js.referenceDeSelectionEvent);
            fieldNameElement.trigger(Vtiger_Edit_Js.referenceDeSelectionEvent);
            e.preventDefault();
        })
    },


    registerRules: function (isEditView) {
        var rules = {
            inventory: {
                conditions: [
                    {
                        operator: 'always',
                        targetFields: [
                            {
                                name: 'inventory',
                                hide: true,
                            },

                        ]
                    }
                ]
            },
            location_type: {
                conditions: [
                    {
                        operator: 'in',
                        value: ['Rack', 'Record Storage'],
                        not: true,
                        targetFields: [
                            {
                                name: 'wfinventorylocations_slot',
                                hide: true,
                            }
                        ]
                    },
                    {
                        operator: 'always',
                        targetFields: [
                            {
                                name: 'location_type',
                                readonly: true,
                            }
                        ]
                    }
                ]
            },
            location_display: {
                conditions: [
                    {
                        operator: 'set',
                        not: true,
                        targetFields: [
                            {
                                name: 'location_type',
                                setValue: '',
                            }
                        ]
                    }
                ]
            }
        };
        this.applyVisibilityRules(rules, isEditView);
    },



    getPopUpParams: function (container, e) {
        var params = this._super(container);
        var sourceFieldElement = jQuery('input[class="sourceField"]', container);
        if(['location'].indexOf(sourceFieldElement.attr('name')) > -1){
            var parentElement = jQuery('input[name="warehouse_display"]');
            if(this.warehouseCheck(parentElement)) {
                params['search_key'] = 'wflocation_warehouse';
                params['search_value'] = parentElement.val();
            } else {
                return false;
            }
        } else if (['warehouse'].indexOf(sourceFieldElement.attr('name')) > -1){
            params['search_key'] = 'wfwarehouse_status';
            params['search_value'] = 'Active';
        }
        return params;
    },

    warehouseCheck: function(element) {
        if(element.val().length == 0) {
            var params = {
                title: app.vtranslate('JS_ERROR'),
                text: app.vtranslate('Warehouse can not be null. Please select a Warehouse first'),
                animation: 'show',
                type: 'error'
            };
            Vtiger_Helper_Js.showPnotify(params);
        } else {
            return true;
        }
    },



    registerBasicEvents: function (container) {
        var isEditView = jQuery('#isEditView').length > 0;
        this._super(container);
        this.registerLocationChange();
        this.registerClearReferenceSelectionEvent(container);
        this.registerRules(isEditView);
    }

});
