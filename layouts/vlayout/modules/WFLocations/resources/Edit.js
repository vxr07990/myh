Vtiger_Edit_Js("WFLocations_Edit_Js", {
    setReferenceFieldValue: function (container, params) {
        var thisInstance = this;
        var sourceField = container.find('input.sourceField').attr('name');
        var fieldElement = container.find('input[name="' + sourceField + '"]');
        var sourceFieldDisplay = sourceField + "_display";
        var fieldDisplayElement = container.find('input[name="' + sourceFieldDisplay + '"]');
        var popupReferenceModule = container.find('input[name="popupReferenceModule"]').val();

        var selectedName = params.name;
        var id = params.id;

        fieldElement.val(id);
        fieldDisplayElement.val(selectedName).attr('readonly', true);
        fieldElement.trigger(Vtiger_Edit_Js.referenceSelectionEvent, {
            'source_module': popupReferenceModule,
            'record': id,
            'selectedName': selectedName
        });

        fieldDisplayElement.validationEngine('closePrompt', fieldDisplayElement);

        if (sourceField == 'wflocation_type') {
            thisInstance.registerEventGetInfoItemFromLocationTypesModule();
        }
        if (sourceField == 'wflocation_base') {
            thisInstance.getSlotValues(container);
            thisInstance.getLocationTag(container);
        }
    },

    getLocationTag: function (container) {
        var self = this;
        var params = {
            module: 'WFLocations',
            action: 'ActionAjax',
            mode: 'getPrimaryLocationInfo',
            record: jQuery('.sourceField', container).val()
        }
        AppConnector.request(params).then(function (data) {
            if (data && data.result) {
                jQuery('input[name="base_location_type"]').val(data.result.base_location_type);
                jQuery('.sourceField', container).data('tag', data.result.tag);
                self.generateLocationTag();
                self.registerRules(isEditView);
            }
        });
    },

    getSlotValues: function (container) {
        var self = this;
        var params = {
            module: 'WFLocations',
            action: 'ActionAjax',
            mode: 'getBaseLocationSlots',
            record: jQuery('.sourceField', container).val()
        }
        AppConnector.request(params).then(function (data) {
            if (data && data.result) {
                self.populateSlotValues(data.result.picklistArray);
            }
        });
    },

    populateSlotValues: function(picklistArray){
        var slotField = jQuery('select[name="base_slot[]"]')
        jQuery('select[name="base_slot[]"] option:gt(0)').remove();
        jQuery.each(picklistArray, function(key,value) {
            slotField.append($("<option></option>")
                .attr("value", value).text(key));
        });
        slotField.trigger('liszt:updated');

    },

    registerEventAutoFillLocationCombination: function (container) {
        var thisInstance = this;
        var locationTagInput = jQuery(container.find('input[name="tag"]'));
        locationTagInput.on('keyup paste change', function () {
            thisInstance.fillLocationCombination();
        });
    },
    fillLocationCombination: function () {
        var locationTagInput = jQuery('input[name="tag"]');
        var location_taginput = locationTagInput.val();
        if (location_taginput == undefined) return;
        //var location_taginput = taginput.toUpperCase();
        var localprefix = jQuery('input[name="locationPrefix"]').val();
        var location_combination = localprefix + locationTagInput.val();
        //var location_combination = combination.toUpperCase();
        locationTagInput.val(location_taginput);
        if (localprefix && localprefix.length > 0 && localprefix != 'false') {
            jQuery('input[name="location_combination"]').val(location_combination);
        }
    },
    registerEventGetInfoItemFromLocationTypesModule: function () {
        var thisInstance = this;
        var LocationTypeFieldElement = jQuery('input[name="wflocation_type"]');
        if (LocationTypeFieldElement.val().length > 0) {
            var params = {};
            params.module = app.getModuleName();
            params.action = 'ActionAjax';
            params.location = LocationTypeFieldElement.val();
            params.mode = 'getInfoItemLocationType';
            AppConnector.request(params).then(function (data) {
                if (data && data.success) {
                    var result = data.result;
                    jQuery('input[name="is_default"]').val(result.is_default);
                    jQuery('input[name="base_location"]').val(result.base);
                    jQuery('input[name="container_location"]').val(result.container);
                    jQuery('input[name="locationPrefix"]').val(result.wflocationtypes_prefix);
                }
                thisInstance.registerRules(isEditView);
                thisInstance.fillLocationCombination();
                thisInstance.generateLocationTag();
            });
        }
    },

    registerRules: function (isEditView) {
        var rules = {
            record: {
                conditions: [
                    {
                        operator: 'set',
                        targetFields: [
                            {
                                name: 'create_multiple',
                                hide: true,
                                setValue: 'No'
                            },
                            {
                                name: 'range_from',
                                hide: true,
                            },
                            {
                                name: 'range_to',
                                hide: true,
                            },
                            {
                                name: 'row_to',
                                hide: true,
                            },
                            {
                                name: 'bay_to',
                                hide: true,
                            },
                            {
                                name: 'level_to',
                                hide: true,
                            },
                        ]
                    }
                ]
            },
            create_multiple: {
                conditions: [
                    {
                        operator: 'is',
                        value: 'No',
                        targetFields: [
                            {
                                name: 'range_from',
                                hide: true,
                            },
                            {
                                name: 'range_to',
                                hide: true,
                            },
                            {
                                name: 'row_to',
                                hide: true,
                            },
                            {
                                name: 'bay_to',
                                hide: true,
                            },
                            {
                                name: 'level_to',
                                hide: true,
                            },
                            {
                                name: 'name',
                                defaultLabel: true,
                            },
                            {
                                name: 'tag',
                                defaultLabel: true,
                            },

                        ]
                    },
                    {
                        operator: 'is',
                        value: 'Yes',
                        and: {
                            source: 'wflocation_type_display',
                            operator: 'in',
                            not: true,
                            value: ['Rack', 'Record Storage'],
                        },
                        targetFields: [
                            {
                                name: 'range_from',
                                mandatory: true,
                            },
                            {
                                name: 'range_to',
                                mandatory: true,
                            },
                            {
                                name: 'name',
                                unmandatory: true,
                                readonly: true,
                            },
                        ]
                    },
                    {
                        operator: 'is',
                        value: 'Yes',
                        and: {
                            source: 'record',
                            operator: 'set',
                            not: true,
                        },
                        targetFields: [
                            {
                                name: 'name',
                                setValue: '',
                                addToLabel: '(sample)',
                            },
                            {
                                name: 'tag',
                                addToLabel: '(sample)',
                            },
                        ]
                    },
                    {
                        operator: 'is',
                        value: 'Yes',
                        and: {
                            source: 'wflocation_type_display',
                            operator: 'in',
                            value: ['Rack', 'Record Storage'],
                        },
                        targetFields: [
                            {
                                name: 'row',
                                mandatory: true,
                            },
                            {
                                name: 'row_to',
                                mandatory: true,
                            },
                            {
                                name: 'bay',
                                mandatory: true,
                            },
                            {
                                name: 'bay_to',
                                mandatory: true,
                            },
                            {
                                name: 'level',
                                mandatory: true,
                            },
                            {
                                name: 'level_to',
                                mandatory: true,
                            },
                            {
                                name: 'name',
                                unmandatory: true,
                                readonly: true,
                                //setValue: '', OT19269
                            },
                            {
                                name: 'range_from',
                                unmandatory: true,
                                hide: true,
                                setValue: '',
                            },
                            {
                                name: 'range_to',
                                unmandatory: true,
                                hide: true,
                                setValue: '',
                            },

                        ]
                    },
                    {
                        operator: 'is',
                        value: 'Yes',
                        and: {
                            source: 'wflocation_type_display',
                            operator: 'in',
                            not: true,
                            value: ['Rack', 'Record Storage'],
                        },
                        targetFields: [
                            {
                                name: 'row_to',
                                hide: true,
                            },
                            {
                                name: 'bay_to',
                                hide: true,
                            },
                            {
                                name: 'level_to',
                                hide: true,
                            },
                        ]
                    },
                ]
            },
            wflocation_type: {
                conditions: [
                    {
                        operator: 'set',
                        not: true,
                        targetFields: [
                            {
                                name: 'row',
                                hide: true,
                            },
                            {
                                name: 'bay',
                                hide: true,
                            },
                            {
                                name: 'level',
                                hide: true,
                            },
                            {
                                name: 'wfslot_configuration',
                                hide: true,
                            },
                            {
                                name: 'vault_capacity',
                                hide: true,
                            }
                        ]
                    },
                    {
                        operator: 'set',
                        and: {
                            source: 'wflocation_type_display',
                            operator: 'in',
                            not: true,
                            value: ['Rack', 'Record Storage'],
                        },
                        targetFields: [
                            {
                                name: 'row',
                                hide: true,
                            },
                            {
                                name: 'bay',
                                hide: true,
                            },
                            {
                                name: 'level',
                                hide: true,
                            },
                        ]
                    },
                    {
                        operator: 'set',
                        and: {
                            source: 'wflocation_type_display',
                            operator: 'in',
                            value: ['Rack', 'Record Storage'],
                        },
                        targetFields: [
                            {
                                name: 'row',
                                mandatory: true,
                            },
                            {
                                name: 'bay',
                                mandatory: true,
                            },
                            {
                                name: 'level',
                                mandatory: true,
                            },
                            {
                                name: 'wfslot_configuration',
                                mandatory: true,
                            },
                            {
                                name: 'name',
                                unmandatory: true,
                                readonly: true,
                            },
                        ]
                    },
                    {
                        operator: 'set',
                        and: {
                            source: 'wflocation_type_display',
                            operator: 'in',
                            not: true,
                            value: ['Record Storage', 'Rack'],
                        },
                        targetFields: [
                            {
                                name: 'wfslot_configuration',
                                hide: true,
                            }
                        ]
                    },
                    {
                        operator: 'set',
                        and: {
                            source: 'wflocation_type_display',
                            operator: 'in',
                            not: true,
                            value: ['Pallet', 'Vault'],
                        },
                        and: {
                            source: 'base_location',
                            operator: 'is',
                            value: '1',
                        },
                        targetFields: [
                            {
                                name: 'wflocation_base',
                                hide: true,
                            },
                            {
                                name: 'base_slot',
                                clearMulti: true,
                                hide: true,
                            },
                        ]
                    },
                ]
            },
            base_location: {
                conditions: [
                    {
                        operator: 'in',
                        value: ['0', 'false'],
                        targetFields: [
                            {
                                name: 'wflocation_base',
                                mandatory: true,
                            },
                        ]
                    },
                ]
            },
            wflocation_type_display: {
                conditions: [
                    {
                        operator: 'set',
                        not: true,
                        targetFields: [
                            {
                                name: 'wflocation_base',
                                hide: true,
                            },
                        ]
                    },
                    {
                        operator: 'is',
                        not: true,
                        value: 'Floor',
                        targetFields: [
                            {
                                name: 'vault_capacity',
                                hide: true,
                            },
                        ]
                    },
                    {
                        operator: 'is',
                        value: 'Floor',
                        targetFields: [
                            {
                                name: 'double_high',
                                hide: true,
                            },
                        ]
                    },
                ]
            },
            wflocation_base: {
                conditions: [
                    {
                        operator: 'set',
                        not: true,

                        targetFields: [
                            {
                                name: 'base_slot',
                                hide: true,
                                clearMulti: true,
                            },
                        ]
                    },
                ]
            },
            base_location_type: {
                conditions: [
                    {
                        operator: 'in',
                        not: true,
                        value: ['Rack', 'Record Storage'],

                        targetFields: [
                            {
                                name: 'base_slot',
                                clearMulti: true,
                                hide: true,
                            },
                        ]
                    },
                    {
                        operator: 'in',
                        value: ['Rack', 'Record Storage'],

                        targetFields: [
                            {
                                name: 'base_slot',
                                mandatory: true,
                            },
                        ]
                    },
                    {
                        operator: 'set',
                        and: {
                            source: 'wflocation_type_display',
                            operator: 'is',
                            not: true,
                            value: 'Floor',
                        },
                        targetFields: [
                            {
                                name: 'vault_capacity',
                                hide: true,
                            },
                        ]
                    },
                    {
                        operator: 'set',
                        and: {
                            source: 'wflocation_type_display',
                            operator: 'is',
                            not: true,
                            value: 'Rack',
                        },
                        targetFields: [
                            {
                                name: 'double_high',
                                hide: true,
                            },
                        ]
                    },

                ]
            },
            container_location: {
                conditions: [
                    {
                        operator: 'in',
                        value: ['false', '0'],
                        targetFields: [
                            {
                                name: 'container_capacity',
                                hide: true,
                            },
                            {
                                name: 'container_capacity_on',
                                hide: true,
                            },
                            {
                                name: 'double_high',
                                hide: true,
                            },
                        ]
                    },
                    {
                        operator: 'is',
                        value: '1',
                        and: {
                            source: 'base_location',
                            operator: 'is',
                            value: '0',
                        },
                        targetFields: [
                            {
                                name: 'double_high',
                                hide: true,
                            },
                        ]
                    },
                    {
                        operator: 'is',
                        value: '1',
                        and: {
                            source: 'base_location',
                            operator: 'is',
                            value: '1',
                        },
                        targetFields: [
                            {
                                name: 'wflocation_base',
                                hide: true,
                            },
                            {
                                name: 'container_capacity',
                                hide: true,
                            },
                            {
                                name: 'container_capacity_on',
                                hide: true,
                            },
                        ]
                    }
                ]
            },
        };
        this.applyVisibilityRules(rules, isEditView);
    },

    generateLocationTag: function () {
        var locationTagEle = jQuery('[name="tag"]');
        if(locationTagEle.val().length > 0 && jQuery('[name="record"]').val().length > 0){
            return;
        }
        var primaryLocationTag = jQuery('[name="wflocation_base"]').data('tag');
        var locationPrefix = jQuery('input[name="locationPrefix"]').val();
        if (!locationPrefix || locationPrefix == 'false'){
            locationPrefix = '';
        }
        var locationNameEle = jQuery('[name="name"]');
        var rowField = jQuery('[name="row"]');
        var bayField = jQuery('[name="bay"]');
        var levelField = jQuery('[name="level"]');
        var rangeFromField = jQuery('[name="range_from"]');
        if (rowField.is(":visible") && bayField.is(":visible") && levelField.is(":visible")) {
            rackLocationName = rowField.val() + bayField.val() + levelField.val();
            locationNameEle.val(rackLocationName);
        } else if (rangeFromField.is(":visible")){
            nonRackLocationName = rangeFromField.val();
            locationNameEle.val(nonRackLocationName);
        }
        var locationName = locationNameEle.val();
        var locationPre = jQuery('[name="pre"]').val();
        var locationPost = jQuery('[name="post"]').val();
        var locationTag = '';
        if (typeof locationPrefix != 'undefined' && locationPrefix != '' &&
            typeof locationName != 'undefined' && locationName != '') {
            locationTag = locationPrefix + locationPre + locationName + locationPost;
        }
        else {
            locationTag = locationName;
        }
        if (primaryLocationTag != undefined && primaryLocationTag != '' && locationTag != '') {
            locationTag += '@' + primaryLocationTag;
        }
        locationTagEle.val(locationTag);
    },


    registerEventForNameAndPrefixChange: function () {
        var self = this;
        jQuery('[name = "tag"]').prop('readonly', true);
        jQuery('[name="name"],[name="pre"],[name="post"],[name="row"],[name="bay"],[name="level"], [name="range_from"]').on('change keyup', function () {
            self.generateLocationTag();
        });
    },
    registerBasicEvents: function (container, quickCreateParams) {
        var isEditView = jQuery('#isEditView').length > 0;
        this._super(container);
        this.registerEventGetInfoItemFromLocationTypesModule();
        this.registerEventAutoFillLocationCombination(container);
        this.registerEventForNameAndPrefixChange();
        this.registerRules(isEditView);
    }
});
