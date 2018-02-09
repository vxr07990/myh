Vtiger_Edit_Js('Move_Type_Js', {
    getInstance: function() {
        return new Move_Type_Js();
    },
    I: function() {
        return this.getInstance();
    }
}, {
    /*
        Internal Variables
     */

    flipped: false,
    afterUpdateDisabled: false,

    /*
        Business Line Map + getter functions
     */

    // Business Line to Move Types map.
    businessLineMap: {
        'Interstate Move': ['Interstate','Inter-Provincial','Cross Border'],
        'Intrastate Move': ['Intrastate','Intra-Provincial'],
        'International Move': ['Alaska','Hawaii','International'],
        'Local Move': ['Local Canada','Local US','Max 3','Max 4'],
        'Military': ['Sirva Military','Military'],
        'Commercial_Move': ['O&I']
    },

    // Functions to get respective business line or move type(s).
    getBusinessLineByMoveType: function(moveType) {
        for(var businessLine in this.businessLineMap) {
            if(this.businessLineMap.hasOwnProperty(businessLine) &&
               this.businessLineMap[businessLine].indexOf(moveType) > -1) {
                return businessLine;
            }
        }
        return undefined;
    },
    getMoveTypesByBusinessLine: function(businessLine) {
        if(Array.isArray(this.businessLineMap[businessLine])) {
            return this.businessLineMap[businessLine];
        }
    },

    /*
        Update callbacks + callback setters
     */

    // Callbacks and respective update functions.
    businessLineUpdate: {callback: function(){}, instance: undefined},
    moveTypeUpdate: {callback: function(){}, instance: undefined},

    // Callback setters to allow custom logic between modules.
    onBusinessLineChange: function(callback, instance) {
        if(typeof callback == 'function') {
            this.businessLineUpdate.callback = callback;
            this.businessLineUpdate.instance = instance;
        }else {
            console.error('Attempted to set Business Line Update Callback to non-function.');
        }
    },
    onMoveTypeChange: function(callback, instance) {
        if(typeof callback == 'function') {
            this.moveTypeUpdate.callback = callback;
            this.moveTypeUpdate.instance = instance;
        }else {
            console.error('Attempted to set Move Type Update Callback to non-function.');
        }
    },

    /*
        Field name variables + Field name setters
     */

    // Field identifiers, to be setable by classes this class plugs into.
    businessLineField: 'business_line',
    emailOptoutField: 'emailoptout',
    moveTypeField: 'move_type',
    originCityField: 'origin_city',
    originStateField: 'origin_state',
    originCountryField: 'origin_country',
    destinationCityField: 'destination_city',
    destinationStateField: 'destination_state',
    destinationCountryField: 'destination_country',

    // Field setters to allow for custom logic between modules.
    setFields: function(fields) {
        for(var key in fields) {
            if(fields.hasOwnProperty(key)) {
                this.setField(key, fields[key]);
            }
        }
    },
    setField: function(key, val) {
        // Ensure the key is a field.
        if(key.indexOf('Field') < 0) {
            key += 'Field';
        }

        if(typeof this[key] == 'string') {
            this[key] = val;
        }else {
            console.error("Attempted to set value of nonexistant field identifier.");
        }
    },
    
    disableAfterUpdate: function(set) {
        if(typeof set == 'undefined') {
            set = true;
        }

        this.afterUpdateDisabled = set;
    },

    /*
        Logic Functions
     */

    updateMoveType: function() {
        // If none, fallback to the current move type.
        var moveTypeField = $('select[name="'+this.moveTypeField+'"]');
        var moveType = this.correctMoveType(moveTypeField.val());

        // Update business line by new move type.
        this.updateBusinessLine(moveType);

        // Module-set update logic callback.
        var prevValue = moveTypeField.data('prev-value');
        var callInstance = this.moveTypeUpdate.instance || this;
        this.moveTypeUpdate.callback.call(callInstance, moveType, prevValue);

        // Update readonly value.
        Vtiger_Edit_Js.setValue(moveTypeField, moveType);
    },

    correctMoveType: function(moveType) {
        // Values to be used to figure out correct move type.
        var originState = $('input[name="'+this.originStateField+'"]').val().toLowerCase();
        var destinationState = $('input[name="'+this.destinationStateField+'"]').val().toLowerCase();
        var originCountry = $('[name="'+this.originCountryField+'"]').val().toLowerCase();
        var destinationCountry = $('[name="'+this.destinationCountryField+'"]').val().toLowerCase();

        if(moveType != 'Sirva Military') {
            if(originCountry == '' || destinationCountry == '') {
                // They are not done setting the addresses, there's no reason to set the move type around.
                return;
            }else if (originCountry === destinationCountry) {
                if(originState === destinationState) {
                    moveType = "Intrastate"
                }else if(originState !== destinationState){
                    moveType = "Interstate";
                }
            // This is gross and I'm sorry
            } else if((originCountry == 'united states' && destinationCountry == 'canada') ||
                      (originCountry == 'canada' && destinationCountry == 'united states')) {
                        moveType = 'Cross Border';
            } else if (originCountry != destinationCountry) {
              moveType = 'International';
            }
        }

        return moveType;
    },

    updateBusinessLine: function(moveType) {
        if(typeof moveType == "undefined") {
            moveType = $('select[name="' + this.moveTypeField + '"]').val();
        }

        // Setup for the change.
        var businessLine = $('select[name="'+this.businessLineField+'"]');
        businessLine.find('option:selected').prop('selected', false);

        var newBusinessLine = this.getBusinessLineByMoveType(moveType);
        if(typeof newBusinessLine == 'undefined') {
            // Defaulting to Interstate, to avoid no blocks from loading on some modules.
            newBusinessLine = 'Interstate Move';
        }

        // Module-set update logic callback.
        var callInstance = this.businessLineUpdate.instance || this;
        this.businessLineUpdate.callback.call(callInstance, newBusinessLine, moveType);

        if(!this.afterUpdateDisabled) {
            this.afterUpdate(moveType);
        }

        // Set new business line values.
        Vtiger_Edit_Js.setValue(businessLine, newBusinessLine);
    },

    /*
        Generic after-change logic, disable by setting the disableAfterUpdate flag.
     */
    afterUpdate: function(moveType) {
        switch (moveType) {
            case 'Local Canada':
            case 'Inter-Provincial':
            case 'Intra-Provincial':
                this.canadaLogic();
                break;
            case 'Interstate':
            case 'Local US':
            case 'Intrastate':
            case 'Alaska':
            case 'Hawaii':
                this.intranationalLogic();
                break;
            case 'International':
                this.internationalLogic();
                break;
            default:
                this.fallbackLogic();
                break;
        }
    },

    canadaLogic: function() {
        Vtiger_Edit_Js.showCell(this.emailOptoutField);

        this.commonLogic('Canada');
    },

    intranationalLogic: function() {
        Vtiger_Edit_Js.hideCell(this.emailOptoutField);

        this.commonLogic('United States');
    },

    internationalLogic: function() {
        Vtiger_Edit_Js.makeFieldNotMandatory(this.originCityField);
        Vtiger_Edit_Js.makeFieldNotMandatory(this.destinationCityField);
    },

    fallbackLogic: function() {
        if($('select[name="' + this.originCountryField + '"]').val() == '') {
            Vtiger_Edit_Js.setValue(this.originCountryField, 'United States', false);
        }
        if($('select[name="' + this.destinationCountryField + '"]').val() == '') {
            Vtiger_Edit_Js.setValue(this.destinationCountryField, 'United States', false);
        }
    },

    commonLogic: function(country) {
        Vtiger_Edit_Js.setValue(this.originCountryField, country, false);
        Vtiger_Edit_Js.setValue(this.destinationCountryField, country, false);
        this.setAddressLabelsAutoFill();

        Vtiger_Edit_Js.makeFieldMandatory(this.originCityField);
        Vtiger_Edit_Js.makeFieldMandatory(this.destinationCityField);
    },

    /*
        Event registration functions
     */

    /*
        Watches for changes to the origin state, destination state, origin country, destination country for changes sets
        move type based on the values matching
     */
    registerMoveTypeByAddress: function() {
        // ??? What's the point of this ???
        $('select[name="'+this.originCountryField+'"]').trigger('liszt:updated');
        $('select[name="'+this.destinationCountryField+'"]').trigger('liszt:updated');

        var thisI = this;
        $('input[name="'+this.originStateField+'"], input[name="'+this.destinationStateField+'"], [name="'+this.originCountryField+'"], [name="'+this.destinationCountryField+'"]').change(function() {
            thisI.updateMoveType();
        });
    },

    /*
        The following register function is arguably redundant, since move type is readonly. However, to allow for other instances to work properly if move type is not read only, it remains.
     */

    // Updates business line logic on move type change.
    registerChangeMoveType : function() {
        var thisI = this;
        $('.contentsDiv').on('value_change', 'select[name="'+this.moveTypeField+'"]', function() {
            var moveType = $(this).val();
            thisI.updateBusinessLine(moveType);
        });
    },

    /*
        Misc initialization functions.
     */

    registerEvents: function() {
        this.registerChangeMoveType();
        this.registerMoveTypeByAddress();
    }
});
