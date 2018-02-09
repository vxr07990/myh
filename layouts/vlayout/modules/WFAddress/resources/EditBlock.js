/**
 * Created by mmuir on 9/1/2017.
 */
Vtiger_EditBlock_Js("WFAddress_EditBlock_Js", {
}, {


    bindGoogleSuggestions: function(num) {
        var module = jQuery('#module').val();
        var thisInstance = this;
        if(jQuery('#'+module+'_editView_fieldName_street_address_'+num).length) {
            var autocompleteWFAddress = new google.maps.places.Autocomplete(
                (document.getElementById(module + '_editView_fieldName_street_address_'+num)),
                { types: ['geocode'] });

            google.maps.event.addListener(autocompleteWFAddress, 'place_changed', function() {
                thisInstance.wfAddressFillInAddress(module, autocompleteWFAddress, num);
                jQuery('#' + module + '_editView_fieldName_street_address_'+num).closest('td').find('.formError').remove();
            });
        }
        if(jQuery('#'+module+'_editView_fieldName_secondary_address_'+num).length) {
            var autocompleteWFAddress2 = new google.maps.places.Autocomplete(
                (document.getElementById(module + '_editView_fieldName_secondary_address_'+num)),
                { types: ['geocode'] });

            google.maps.event.addListener(autocompleteWFAddress2, 'place_changed', function() {
                thisInstance.wfAddressFillInAddress(module, autocompleteWFAddress2, num);
                jQuery('#' + module + '_editView_fieldName_secondary_address_'+num).closest('td').find('.formError').remove();
            });
        }
        if(jQuery('#'+module+'_editView_fieldName_wfaddress_city_'+num).length) {
            var autocompleteWFAddressCity = new google.maps.places.Autocomplete(
                (document.getElementById(module + '_editView_fieldName_wfaddress_city_'+num)),
                { types: ['geocode'] });

            google.maps.event.addListener(autocompleteWFAddressCity, 'place_changed', function() {
                thisInstance.wfAddressFillInAddress(module, autocompleteWFAddressCity, num);
                jQuery('#' + module + '_editView_fieldName_wfaddress_city_'+num).closest('td').find('.formError').remove();
            });
        }
        if(jQuery('#'+module+'_editView_fieldName_wfaddress_state_'+num).length) {
            var autocompleteWFAddressState = new google.maps.places.Autocomplete(
                (document.getElementById(module + '_editView_fieldName_wfaddress_state_'+num)),
                { types: ['geocode'] });

            google.maps.event.addListener(autocompleteWFAddressState, 'place_changed', function() {
                thisInstance.wfAddressFillInAddress(module, autocompleteWFAddressState, num);
                jQuery('#' + module + '_editView_fieldName_wfaddress_state_'+num).closest('td').find('.formError').remove();
            });
        }
        if(jQuery('#'+module+'_editView_fieldName_wfaddress_zip_'+num).length) {
            var autocompleteWFAddressZip = new google.maps.places.Autocomplete(
                (document.getElementById(module + '_editView_fieldName_wfaddress_zip_'+num)),
                { types: ['geocode'] });

            google.maps.event.addListener(autocompleteWFAddressZip, 'place_changed', function() {
                thisInstance.wfAddressFillInAddress(module, autocompleteWFAddressZip, num);
                jQuery('#' + module + '_editView_fieldName_wfaddress_zip_'+num).closest('td').find('.formError').remove();
            });


        }
        if(jQuery('#'+module+'_editView_fieldName_wfaddress_country_'+num).length) {
            var autocompleteWFAddressCountry = new google.maps.places.Autocomplete(
                (document.getElementById(module + '_editView_fieldName_wfaddress_country_'+num)),
                { types: ['geocode'] });

            google.maps.event.addListener(autocompleteWFAddressCountry, 'place_changed', function() {
                thisInstance.wfAddressFillInAddress(module, autocompleteWFAddressCountry, num);
                jQuery('#' + module + '_editView_fieldName_wfaddress_country_'+num).closest('td').find('.formError').remove();
            });
        }

    },


    wfAddressFillInAddress : function(formType, autocomplete, num) {
        var module = jQuery('#module').val();
        var thisInstance = this;
        var place = autocomplete.getPlace();
        var street_address = '';
        var form = '';

        thisInstance.WFAddressComponentForm = {
            street_address: module + '_editView_fieldName_street_address_'+num,
            locality: module + '_editView_fieldName_wfaddress_city_'+num,
            administrative_area_level_1: module + '_editView_fieldName_wfaddress_state_'+num,
            country: module + '_editView_fieldName_wfaddress_country_'+num,
            postal_code: module + '_editView_fieldName_wfaddress_zip_'+num
        };
        form = thisInstance.WFAddressComponentForm;
        jQuery(':focus').trigger('blur');

        for (var component in form) {
            jQuery('#'+component).val('');
        }
        var hasAddress = false;
        var hasRoute = false;
        var hasCity = false;
        var hasState = false;
        var hasZip = false;

        if(typeof place.address_components != 'undefined') {
            for (var i=0; i<place.address_components.length; i++) {
                var addressType = place.address_components[i].types[0];
                if(addressType == 'street_number' && place.address_components[i][thisInstance.WFAddressComponentForm[addressType]] != '') {
                    hasAddress = true;
                    street_address = place.address_components[i]['short_name'];

                } else if(addressType == 'route') {
                    hasRoute = true;
                    street_address = street_address + ' ' + place.address_components[i]['short_name'];

                } else if(thisInstance.WFAddressComponentForm[addressType]) {
                    hasCity = true;
                    if(addressType == 'locality') {
                        hasCity = true;
                    } else if(addressType == 'administrative_area_level_1') {
                        hasState = true;
                    } else if(addressType == 'postal_code') {
                        hasZip = true;
                    }

                    var val = place.address_components[i]['short_name'];

                    if(val) {
                        if(addressType == 'locality' && val.substring(0, 3) == 'St ') {
                            val = 'Saint '+val.substring(3);
                        }
                    }
                    if(jQuery('#'+form[addressType]).length) {
                        var field = jQuery('#'+form[addressType]);
                        field.val(val);
                        field.trigger('propertychange');

                        field.validationEngine('validate');
                    }



                }
            }

            if(!hasAddress && !hasRoute && jQuery('#'+form['street_address']).val() != 'Will Advise') {
                /*
                 Removed below because it was removing the street address when the user enters a zip code and clicks
                 a result.
                 */
                //jQuery('#'+form['street_address']).val('');
            } else if(jQuery('#'+form.street_address).val() != 'Will Advise'){
                jQuery('#'+form.street_address).val(street_address);
            }
            if(!hasCity) {
                jQuery('#'+form.locality).val('');
            }
            if(!hasState) {
                jQuery('#'+form.administrative_area_level_1).val('');
            }
            if(!hasZip) {
                jQuery('#'+form.postal_code).val('');
            }

            //trigger Lookup Postal Code to appear after google api populates an address block
            if (hasState && hasCity && !hasZip) {
                // only need to trigger one of the possible fields
                // and only if there's a city and state and no zip

                //var field = jQuery('#'+form['locality']);
                //field.trigger('change');
                var field2 = jQuery('#'+form.administrative_area_level_1);
                field2.trigger('change');
            }
        }
    },


    registerAutoFill: function(num) {
        while (num > 0) {
            this.bindGoogleSuggestions(num);
            num--;
        }
    },


    registerBasicEvents : function(container) {
        //_super.registerBasicEvents(container);
        var recordNumber = container.attr('guestid');
        this.registerAutoFill(recordNumber);
        //this.registerAddressLines(recordNumber);
        // this.populateDefaultAddress(recordNumber);
    },




});

jQuery(document).ready(function() {
    var instance = new WFAddress_EditBlock_Js();
    instance.registerEvents();
});
