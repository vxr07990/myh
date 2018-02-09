Vtiger_Edit_Js('Service_Charges_Js', {
    getInstance: function() {
        return new Service_Charges_Js();
    },
    I: function() {
        return this.getInstance();
    },
    compile: function() {
        this.I().compileAndStore();
    },
}, {
    compileAndStore: function() {
        var compiled = this.compileAll();

        // Set the compiled field to the new object.
        $('#compiledServiceCharges').val(JSON.stringify(compiled));
    },

    compileAll: function() {
        // Reset class variable for recompilation.
        var compiled = [];


        // Compiled JSON objects of Service Charge fields.
        var origin = this.compileOne('origin');
        if(origin) {
            origin.forEach(function(ele) {
                compiled.push(ele)
            })
        }
        var dest = this.compileOne('destination');
        if(dest) {
            dest.forEach(function(ele) {
                compiled.push(ele);
            });
        }

        return compiled;
    },

    compileOrigin: function() {
        // Wrapper function for easier use.
        return this.compileOne('origin');
    },

    compileDestination: function() {
        // Wrapper function for easier use.
        return this.compileOne('destination');
    },

    compileOne: function(type) {
        // Gather service charge row area.
        var base_ele = $('#'+type+'ServiceCharges');
        if(base_ele.length < 1) {
            return false;
        }

        var compiled = [];
        base_ele.find('tr.interstateServiceChargeRow').each(function() {
            var row = {};
            $(this).find('input').each(function() {
                var ele = $(this);

                // Variables to add to the compiled JSON object.
                var input_name = ele.attr('name');
                var input_val = null;

                // Val getting.
                if(ele.attr('type') == 'checkbox') {
                    input_val = ele.is(':checked') ? "on" : "off";
                }else {
                    input_val = ele.val();
                }

                // Add to compiled JSON object as long as name is present.
                if(typeof input_name != 'undefined') {
                    row[input_name] = input_val;
                }
            });
            if(!$.isEmptyObject(row)) {
                compiled.push(row);
            }
        });
        if(compiled.length < 1) {
            return false;
        }

        return compiled;
    },

    refresh: function() {
        // Wrapper function for whenever tariffs or other important aspects of the Estimates are changed.
        var origin = $('[name="origin_zip"]');
        var destination = $('[name="destination_zip"]');
        if(origin.val() != '') {
            this.getFromZip(origin);
        }
        if(destination.val() != '') {
            this.getFromZip(destination);
        }
    },

    getFromZip: function(ele) {
        // Ease-of-use wrapper around 'callRating' for external usage.
        ele = $(ele);

        var new_zip  = ele.val();
        var is_dest  = ele.attr('name') == "destination_zip" ? 1 : 0;
        var tariffid = jQuery('[name="effective_tariff"]').val();
        var effDate  = jQuery('[name="interstate_effective_date"]').val();
        var dateFormat = jQuery('[name="interstate_effective_date"]').data('dateFormat');
        var owner = jQuery('[name="agentid"]').val();
        return this.callRating(new_zip, is_dest, tariffid, effDate, dateFormat, owner);
    },

    callRating: function(new_zip, is_dest, tariffid, effDate, dateFormat, owner) {
        var thisI = this;
        //Reach out to server for a list of service charges applicable to the changed zip
        var dataURL  = 'index.php?module=Estimates&action=GetServiceCharges&zip=' + new_zip + '&is_dest=' + is_dest + '&tariffid=' + tariffid + '&effective_date=' + effDate + '&date_format=' + dateFormat + '&owner=' + owner;
        AppConnector.request(dataURL).then(
            function(data) {
                if(data.success) {
                    var originCharges = $('#originServiceCharges');
                    var destinationCharges = $('#destinationServiceCharges');
                    if(is_dest) {
                        //Clear out destination charges
                        destinationCharges.find('.interstateServiceChargeRow').remove();
                        destinationCharges.append(data.result);
                    } else {
                        //Clear out origin charges
                        originCharges.find('.interstateServiceChargeRow').remove();
                        originCharges.append(data.result);
                    }
                    thisI.registerServiceChargeFieldsChange();
                }
            }
        );
    },

    isValidZip: function(zip, country) {
        var numbers_only = ['united states','usa','us'];
        if(typeof zip == 'string' && typeof country == 'string') {
            if(numbers_only.indexOf(country.toLowerCase()) > -1 || country == '') {
                return !isNaN(Number(zip));
            }else {
                return true;
            }
        }else if(typeof zip == 'object' && zip != null) {
            // Get element and set new zip.
            var ele = $(zip);
            zip = ele.val();

            // Variables needed to properly test.
            var is_dest = ele.attr('name') == 'destination_zip' ? true : false;
            var country = is_dest ? $('[name="estimates_destination_country"]').val() : $('[name="estimates_origin_country"]').val();

            return this.isValidZip(zip, country);
        }else {
            return false;
        }
    },

    registerZipChangeEvent : function() {
        var thisI = this;
        // We don't want to call this on pageload since it would be redundant.
        jQuery('.contentsDiv').on('value_change', 'input[name="origin_zip"], input[name="destination_zip"]', function() {
            if(thisI.isValidZip(this)) {
                thisI.getFromZip(this);
            }
        });
    },

    // 👌 What a beautiful method name.
    registerServiceChargeFieldsChange: function() {
        var thisI = this;
        // Need to call off and on, since service charges will add HTML to the DOM, and this is to avoid any rebinding.
        $('#serviceChargesTable').find('input').off('change').on('change', function() {
            thisI.compileAndStore();
        });
        thisI.compileAndStore();
    },

    registerEvents: function() {
        this.registerServiceChargeFieldsChange();
        this.registerZipChangeEvent();
    }
})
