Vtiger_Edit_Js('Zip_Auto_Fill_Js', {
    instance: null,

    getInstance: function() {
        if(this.instance == null) {
            this.instance = new Zip_Auto_Fill_Js();
        }
        return this.instance;
    },
    I: function() {
        return this.getInstance();
    }
}, {
    // List of possible ZIP code fields. Ideally, this should be supplied by the module initializing, but at the moment
    // that is not the case.
    zipFieldMap: {
        'origin_zip': {
            'type': 'Origin'
        },
        'destination_zip': {
            'type': 'Destination'
        },
        'agent_zip': {
            'type': 'Agent'
        },
        'bill_code': {
            'type': 'Billing'
        },
        'ship_code': {
            'type': 'AccountShip',
            'id_prepend': 'ship'
        },
        'vanline_zip': {
            'type': 'Vanline'
        },
        'mailingzip': {
            'type': 'Mailing'
        },
        'otherzip': {
            'type': 'Other'
        },
        'stop_zip': {
            'type': 'Stops',
            'id_prepend': 'stop'
        },
    },

    // Controls the address fields that are bound and used to determine if ZIP lookup can be displayed.
    addressFields: {},
    setAddressField: function(type, field, identifier) {
        if(typeof this.addressFields[type] == 'undefined') {
            this.addressFields[type] = {};
        }
        this.addressFields[type][field] = $(identifier);
    },
    getAddressFieldsByType: function(type) {
        if(this.addressFields.hasOwnProperty(type)) {
            return this.addressFields[type];
        }else {
            console.error('No addresses set for '+type+'.');
        }
    },

    // Controls the ZIP lookup buttons on a page, by type.
    lookupButtons: {},
    setButton: function(type, identifier) {
        this.lookupButtons[type] = $(identifier);
    },
    getButton: function(type) {
        if(typeof this.lookupButtons[type] != 'undefined' && this.lookupButtons[type] != null) {
            return this.lookupButtons[type];
        }else {
            console.error('Lookup button for '+type+' has not been set.');
        }
    },

    // Initialization that adds the ZIP Lookup button to every ZIP code field that is found on the page.
    initializeForModule : function(moduleName) {
        // Loop through fields to initialize.
        for(var field in this.zipFieldMap) {
            var type = this.zipFieldMap[field].type;
            var fieldID = '#'+moduleName+'_editView_fieldName_'+field;
            var ele = $(fieldID);
            // If the field is present, add the ZIP lookup button.
            if(this.zipFieldMap.hasOwnProperty(field) && ele.length > 0) {
                // Get the prepending type name and add the button.
                // NOTE: The JS || operator returns the first truey variable (or the last falsey one), not true or false.
                var prepend = this.zipFieldMap[field].id_prepend || type.toLowerCase();
                ele.after('<button id="'+prepend+'ZipButton" type="button" class="hide">'+app.vtranslate('Zip Code Lookup')+'</button>');
                this.setButton(type, '#'+prepend+'ZipButton');

                // Initialize the event handling.
                this.zipAutofill(moduleName, type, prepend);
            }
        }
    },

    // Ensure the ZIP Lookup button should be shown.
    checkZipLookupAllowed: function(type, changed_field) {
        var supportedCountries = ['united states', 'canada', 'usa', 'us', 'ca'];
        var fields = this.getAddressFieldsByType(type);
        var button = this.getButton(type);
        if (fields['city'].val().length > 0 && fields['state'].val().length > 1 && (fields['country'].length == 0 || supportedCountries.indexOf(fields['country'].val().toLowerCase()) != -1)) {
            if (button.hasClass('hide')) {
                button.before('<br />');
            }
            button.removeClass('hide');
        } else {
            button.addClass('hide');
            button.parent().find('br').remove();
        }
    },

    // Bind ZIP Lookup check to fields.
    bindAddressFields: function(type) {
        var fields = this.getAddressFieldsByType(type);
        var thisI = this;
        for(var field in fields) {
            fields[field].on('change', function () {
                thisI.checkZipLookupAllowed(type, $(this));
            });
        }
    },

    // Setup and bind the ZIP autocomplete functionality to the button.
    zipAutofill : function(moduleName, type, dom_part) {
        var thisI = this;
        var button = this.getButton(type);

        // Setup fields for binding.
        var fields = ['city','state','country','zip'];
        fields.forEach(function(field) {
            thisI.setAddressField(type, field, '#'+moduleName+'_editView_fieldName_'+dom_part+'_'+field);
        });

        // Modified to correct OT Defect 11536 & OT Defect 11543
        //These control the appearance of a zip lookup button that isn't currently used in graebel. And also there aren't country fields for origin and destinations on graebel.
        //Soooo...
        if($('[name="instance"]').val() != 'graebel') {
            thisI.bindAddressFields(type);
        }

        button.on('click', function(e) {
            thisI.autocompleteZip(e, type)
        });
    },

    // Gather a list of ZIP codes and display for autocomplete.
    autocompleteZip: function(e, type) {
        // Get the important information.
        var fields = this.getAddressFieldsByType(type);
        if(typeof fields == 'undefined') {
            return;
        }
        var button = this.getButton(type);
        if(typeof button == 'undefined') {
            return;
        }

        // Let the user know we're working on stuff.
        this.busy();

        // Get the params to send to the AJAX call.
        var city = fields['city'].val();
        if(city.substring(0,3).toLowerCase() == 'st ') {
            city = 'Saint '+city.substring(3);
        } else if(city.substring(0,3).toLowerCase() == 'st.') {
            city = 'Saint '+city.substring(4);
        }
        var state = fields['state'].val();
        var country = fields['country'].val();

        var thisI = this;
        var dataURL = "index.php?module=Potentials&action=ReverseAddressLookup&city="+ city +"&state=" + state;
        AppConnector.request(dataURL).then(
            function(data){
                if(data.success) {
                    var zipField = fields['zip'];
                    if(typeof data.result.items == 'undefined') {
                        var address = city + (state != '' ? ', ' + state : '') + (country != '' ? ', ' + country : '');
                        bootbox.alert('Unable to find any postal codes for '+address+'.');
                    }else{
                        zipField.autocomplete({
                            source: data.result.items,
                            minLength: 0,
                            select: function(event, ui) {
                                var selectedValue = ui.item.value;
                                zipField.trigger('blur').val(selectedValue);
                                button.addClass('hide').parent().find('br').remove();

                                // Ensure country is the correct one.
                                if(typeof data.result.country != 'undefined') {
                                    countryField.val(data.result.country);
                                }
                            }
                        });
                        // Call the search to display all the items.
                        zipField.autocomplete('search', '');
                        zipField.focus();
                        jQuery(document.head).append('<style>.ui-autocomplete {height:100px !important;overflow:auto;}</style>');
                    }
                }
                thisI.notBusy();
            },
        function(err) {
            console.error("ERROR: "+err);
            thisI.notBusy();
        });
    },

    // Basic wrapper functions around the progress and cursor indicators.
    progressIndicator: null,
    busy: function() {
        progressIndicator = jQuery.progressIndicator({
            'message': 'Searching for Postal Code',
            'position': 'html',
            'blockInfo': {
                'enabled': true
            }
        });
        document.body.style.cursor='wait';
    },
    notBusy: function() {
        document.body.style.cursor='default';
        progressIndicator.progressIndicator({
            'mode': 'hide'
        });
    }
});
