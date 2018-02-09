Vtiger_Edit_Js("Locations_Edit_Js", {
    registerClearReferenceSelectionEvent : function(container) {
        var thisInstance = this;
        container.find('.clearReferenceSelection').on('click', function(e){
            var element = jQuery(e.currentTarget);
            var parentTdElement = element.closest('td');
            var fieldNameElement = parentTdElement.find('.sourceField');
            var fieldName = fieldNameElement.attr('name');
            fieldNameElement.val('');
            if(fieldName == 'location_type'){
                fieldNameElement.removeAttr('data-location-prefix');
                fieldNameElement.removeAttr('data-location-base');
                fieldNameElement.removeAttr('data-location-container');
            }else if(fieldName == 'location_base'){
                fieldNameElement.data('location_tag','');
            }
            parentTdElement.find('#'+fieldName+'_display').removeAttr('readonly').val('');
            element.trigger(Vtiger_Edit_Js.referenceDeSelectionEvent);
            fieldNameElement.trigger(Vtiger_Edit_Js.referenceDeSelectionEvent);
            if(fieldName == 'location_base' || fieldName == 'location_type'){
                thisInstance.enableSlotsField();
                thisInstance.generateLocationTag();
            }
            e.preventDefault();
        })
    },

    setReferenceFieldValue : function(container, params) {
        var thisInstance = this;
        var sourceField = container.find('input.sourceField').attr('name');
        var fieldElement = container.find('input[name="'+sourceField+'"]');
        var sourceFieldDisplay = sourceField+"_display";
        var fieldDisplayElement = container.find('input[name="'+sourceFieldDisplay+'"]');
        var popupReferenceModule = container.find('input[name="popupReferenceModule"]').val();

        var selectedName = params.name;
        var id = params.id;

        fieldElement.val(id);
        fieldDisplayElement.val(selectedName).attr('readonly',true);
        fieldElement.trigger(Vtiger_Edit_Js.referenceSelectionEvent, {'source_module' : popupReferenceModule, 'record' : id, 'selectedName' : selectedName});

        fieldDisplayElement.validationEngine('closePrompt',fieldDisplayElement);

        if (sourceField == 'location_type'){
            thisInstance.registerEventGetInfoItemFromLocationTypesModule(container);
        }
        if(sourceField == 'location_base'){
            thisInstance.getLocationTag(container);
            thisInstance.enableSlotsField();

        }
    },
    getLocationTag: function (container) {
        var self = this;
        var params = {
            module:'Locations',
            action: 'ActionAjax',
            mode: 'getPrimaryLocationInfo',
            record: jQuery('.sourceField',container).val()
        }
        AppConnector.request(params).then(function (data) {
            if(data && data.result){
                jQuery('.sourceField',container).data('location_tag',data.result.location_tag);
                self.generateLocationTag();
            }
        });
    },
    enableSlotsField: function (event) {
        var locationTypeDislay = jQuery('[name="location_type_display"]').val();
        var slotFieldDisplay = jQuery('[name="location_slot_configuration_display"]');
        if(locationTypeDislay == 'Rack'){
            slotFieldDisplay.prop("disabled",false);
        }else{
            slotFieldDisplay.prop("disabled",true);
            if(event != undefined){
                event.stopPropagation();
            }

        }
    },

    registerEventRemoveLabelOfLocationCombination : function (container) {
        var LocationCombinationInput = jQuery(container.find('input[name="location_combination"]'));
        LocationCombinationInput.closest('td').prev().remove();
    },

    registerEventChangeColspanOfLocationDescription : function (container) {
        var LocationDescriptionInput = jQuery(container.find('input[name="location_description"]'));
        var valueLocationDescriptionInput = LocationDescriptionInput.val();
        var ParentTdLocationDescriptionInput = LocationDescriptionInput.closest('td');
        ParentTdLocationDescriptionInput.attr('colspan',2);
        LocationDescriptionInput.remove();
        var textareaHtmlAppend = '<textarea name="location_description">'+valueLocationDescriptionInput+'</textarea>';
        ParentTdLocationDescriptionInput.html(textareaHtmlAppend);
    },

    registerEventAutoFillLocationCombination : function (container) {
        var thisInstance = this;
        var LocationTagInput = jQuery(container.find('input[name="location_tag"]'));
        LocationTagInput.on('keyup paste change',function () {
            thisInstance.fillLocationCombination();
        });
    },
    fillLocationCombination : function () {
        var LocationTagInput =  jQuery('input[name="location_tag"]');
        var taginput = LocationTagInput.val();
        if(taginput == undefined) return;
        var location_taginput = taginput.toUpperCase();
        var localprefix = jQuery('input[name="location_type"]').attr('data-location-prefix');
        var combination = localprefix + LocationTagInput.val();
        var location_combination = combination.toUpperCase();
        LocationTagInput.val(location_taginput);
        if (localprefix && localprefix.length > 0){
            jQuery('input[name="location_combination"]').val(location_combination);
        }
    },
    registerEventGetInfoItemFromLocationTypesModule : function (container) {
        var thisInstance = this;
        var LocationTypeFieldElement = jQuery('input[name="location_type"]');
        if (LocationTypeFieldElement.val().length > 0){
            var params = {};
            params.module   = app.getModuleName();
            params.action   = 'ActionAjax';
            params.location = LocationTypeFieldElement.val();
            params.mode     = 'getInfoItemLocationType';
            AppConnector.request(params).then(function (data) {
                if (data.success){
                    var result = data.result;
                    LocationTypeFieldElement.attr('data-location-prefix', result.location_prefix);
                    LocationTypeFieldElement.attr('data-location-base', result.location_base);
                    LocationTypeFieldElement.attr('data-location-container', result.location_container);
                }
                thisInstance.fillLocationCombination();
                thisInstance.enableSlotsField();
                thisInstance.registerEventShowSlotField(container);
                thisInstance.registerEventShowLocationDoubleHighField(container);
                thisInstance.registerEventShowLocationContainerCapacityOnField(container);
                thisInstance.registerEventShowLocationContainerCapacityField(container);
                thisInstance.generateLocationTag();
            });

        }
    },
    registerEventShowSlotField : function (container) {
        var LocationTypeFieldElement = jQuery(container.find('input[name="location_type"]'));
        var isContainer = false;
        var hasLocationBase = LocationTypeFieldElement.attr('data-location-base');
        var hasLocationContainer = LocationTypeFieldElement.attr('data-location-container');

        if (hasLocationContainer == 1 && hasLocationBase==0){
            isContainer = true;
        }else{
            isContainer = false;
        }

        var FieldElement = $('[name="location_slot"]');
        var ContainerFieldElementNeedleHidden = FieldElement.closest('.row-fluid');
        var TdParentOfFieldElement = ContainerFieldElementNeedleHidden.closest('td');
        var TdParentOfLabelFieldElement = TdParentOfFieldElement.prev();

        if (isContainer == false){
            // TdParentOfLabelFieldElement.children().hide();
            ContainerFieldElementNeedleHidden.hide();
        }else{
            // TdParentOfLabelFieldElement.children().show();
            ContainerFieldElementNeedleHidden.show();
        }
    },

    registerEventShowLocationDoubleHighField : function (container) {
        var LocationTypeFieldElement = jQuery(container.find('input[name="location_type"]'));
        var isContainer = false;
        var hasLocationBase = LocationTypeFieldElement.attr('data-location-base');
        var hasLocationContainer = LocationTypeFieldElement.attr('data-location-container');

        if (hasLocationContainer == 1 && hasLocationBase==1){
            isContainer = true;
        }else{
            isContainer = false;
        }

        var LocationDoubleHighFieldElement = $('[name="location_double_high"]');
        var ContainerOfLocationDoubleHighFieldElementNeedleHidden = LocationDoubleHighFieldElement.closest('.row-fluid');
        var TdParentOfLocationDoubleHighFieldFieldElement = ContainerOfLocationDoubleHighFieldElementNeedleHidden.closest('td');
        var TdParentOfLocationDoubleHighLabelFieldElement = TdParentOfLocationDoubleHighFieldFieldElement.prev();

        if (isContainer == false){
            // TdParentOfLocationDoubleHighLabelFieldElement.children().hide();
            ContainerOfLocationDoubleHighFieldElementNeedleHidden.hide();
        }else{
            // TdParentOfLocationDoubleHighLabelFieldElement.children().show();
            ContainerOfLocationDoubleHighFieldElementNeedleHidden.show();
        }
    },


    registerEventShowLocationContainerCapacityOnField : function (container) {
        var LocationTypeFieldElement = jQuery(container.find('input[name="location_type"]'));
        var isContainer = false;
        var hasLocationBase = LocationTypeFieldElement.attr('data-location-base');
        var hasLocationContainer = LocationTypeFieldElement.attr('data-location-container');

        if (hasLocationContainer == 1 && hasLocationBase==0){
            isContainer = true;
        }else{
            isContainer = false;
        }

        var FieldElement = $('[name="location_container_capacity_on"]');
        var ContainerFieldElementNeedleHidden = FieldElement.closest('.row-fluid');
        var TdParentOfFieldElement = ContainerFieldElementNeedleHidden.closest('td');
        var TdParentOfLabelFieldElement = TdParentOfFieldElement.prev();

        if (isContainer == false){
            // TdParentOfLabelFieldElement.children().hide();
            ContainerFieldElementNeedleHidden.hide();
        }else{
            // TdParentOfLabelFieldElement.children().show();
            ContainerFieldElementNeedleHidden.show();
        }
    },

    registerEventShowLocationContainerCapacityField : function (container) {
        var LocationTypeFieldElement = jQuery(container.find('input[name="location_type"]'));
        var isContainer = false;
        var hasLocationBase = LocationTypeFieldElement.attr('data-location-base');
        var hasLocationContainer = LocationTypeFieldElement.attr('data-location-container');

        if (hasLocationContainer == 1 && hasLocationBase==0){
            isContainer = true;
        }else{
            isContainer = false;
        }

        var FieldElement = $('[name="location_container_capacity"]');
        var ContainerFieldElementNeedleHidden = FieldElement.closest('.row-fluid');
        var TdParentOfFieldElement = ContainerFieldElementNeedleHidden.closest('td');
        var TdParentOfLabelFieldElement = TdParentOfFieldElement.prev();

        if (isContainer == false){
            // TdParentOfLabelFieldElement.children().hide();
            ContainerFieldElementNeedleHidden.hide();
        }else{
            // TdParentOfLabelFieldElement.children().show();
            ContainerFieldElementNeedleHidden.show();
        }
    },

    registerReadonlyForLocationPercentused : function () {
        var LocationPercentusedFielElement = jQuery('input[name="location_percentused"]');
        var LocationPercentusedoverrideFielElement = jQuery('input[name="location_percentusedoverride"]');

        if (LocationPercentusedoverrideFielElement.is(':checked') == true){
            LocationPercentusedFielElement.prop('readonly',false);
        }else{
            LocationPercentusedFielElement.prop('readonly',true);
        }
        LocationPercentusedoverrideFielElement.on('click',function () {
            var checkedField = LocationPercentusedoverrideFielElement.is(':checked');
            if (checkedField == true){
                LocationPercentusedFielElement.prop('readonly',false);
            }else{
                LocationPercentusedFielElement.prop('readonly',true);
            }
        });
    },
    registerEventForSlotChange: function () {
        var self = this;
        jQuery('[name="location_slot_configuration_display"]').closest('.row-fluid').find('.relatedPopup').on('click',function (e) {
            self.enableSlotsField(e);
        });
        jQuery('#Locations_editView_fieldName_location_slot_configuration_create').on('click',function () {
            self.enableSlotsField(e);
        });
    },
    generateLocationTag: function () {
        var locationTagEle = jQuery('[name="location_tag"]');
        var primaryLocationTag = jQuery('[name="location_base"]').data('location_tag');
        var locationPrefix = jQuery('[name="location_type"]').attr('data-location-prefix');
        var locationName = jQuery('[name="location_name"]').val();
        var locationPre = jQuery('[name="location_pre"]').val();
        var locationPost = jQuery('[name="location_post"]').val();
        var locationTag = '';
        if(locationPrefix != undefined && locationPrefix != '' &&
            locationName != undefined && locationName != ''){
            locationTag = locationPrefix+locationPre+locationName+locationPost;
        }
        if(primaryLocationTag != undefined && primaryLocationTag != '' && locationTag !=''){
            locationTag+='@'+primaryLocationTag;
        }
        locationTagEle.val(locationTag);
    },
    registerEventForNameAndPrefixChange:function () {
        var self = this;
        jQuery('[name = "location_tag"]').prop('readonly',true);
        jQuery('[name="location_name"],[name="location_pre"],[name="location_post"]').on('change keyup', function () {
            self.generateLocationTag();
        });
    },
    registerBasicEvents: function (container, quickCreateParams) {
        this._super(container);
        this.registerEventGetInfoItemFromLocationTypesModule(container);
        this.registerEventAutoFillLocationCombination(container);
        this.registerEventShowSlotField(container);
        this.registerEventShowLocationDoubleHighField(container);
        this.registerEventShowLocationContainerCapacityOnField(container);
        this.registerEventShowLocationContainerCapacityField(container);
        this.registerReadonlyForLocationPercentused(container);
        this.enableSlotsField();
        this.registerEventForSlotChange();
        this.registerEventForNameAndPrefixChange();
    }
});
