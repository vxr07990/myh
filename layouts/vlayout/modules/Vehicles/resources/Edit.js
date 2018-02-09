Vtiger_Edit_Js("Vehicles_Edit_Js", {}, {
    VINUpdates: function () {
        jQuery(document).on('change', '#Vehicles_editView_fieldName_vehicle_vin', function () {
            if (jQuery(this).val() != '') {
                var dataURL = 'index.php?module=VehicleLookup&action=LookupVIN&vin='+jQuery(this).val();
                AppConnector.request(dataURL).then(
                    function(data) {
                        if(data.success) {
                            console.dir(data.result);
                            if(data.result.isTruck)
                            {
                                jQuery('[name="vehicle_maker"]').val(data.result['Make']);
                                jQuery('[name="vehicle_model"]').val(data.result['Model']);
                                jQuery('[name="vehicle_year"]').val(data.result['Model Year']);
                                //data.result['Gross Vehicle Weight Rating']
                                // not sure how to map gvwr to the existing options
                                //'Drive Type',
                                // e.g. 6x4.  Not sure where this goes
                                jQuery('[name="vehicle_wheelbasslength"]').val(data.result['Wheel Base (inches)']);
                                jQuery('[name="vehicle_cabstyle"]').val(data.result['Cab Type']);
                                jQuery('[name="vehicle_wheels"]').val(data.result['Number of Wheels']);
                                // Wheel Size Front (inches)?
                                jQuery('[name="vehicle_tiresize"]').val(data.result['Wheel Size Rear (inches)']);
                                jQuery('[name="vehicle_cabstyle"]').val(data.result['Cab Type']);
                                jQuery('[name="vehicle_axles"]').val(data.result['Axles']);
                            } else {
                                jQuery('[name="vehicle_maker"]').val(data.result.make.name);
                                jQuery('[name="vehicle_model"]').val(data.result.model.name);
                                jQuery('[name="vehicle_year"]').val(data.result.years[0].year);
                            }
                            //bodyContainer.find('select[id^="vehicle_type"]').find('option').removeAttr('selected');
                            //bodyContainer.find('select[id^="vehicle_type"]').find('option[value="'+data.result.categories.vehicleType+'"]').attr('selected', true);
                        } else {
                            bootbox.alert(data.error.code + ': ' + data.error.message);
                        }
                    },
                    function(error) {
                        console.dir(error);
                    }
                );
            }

        });
    },
    showHideFields: function (fields, state) {
        jQuery.each(fields, function (key, value) {
            if (value.match("LBL")) {
                if (state == 'show') {
                    jQuery('[name="' + value + '"]').removeClass('hide');
                } else {
                    jQuery('[name="' + value + '"]').addClass('hide');
                }
            } else { //else its an input
                if (state == 'show') {
                    jQuery('[name="' + value + '"]').val('').parent().removeClass('hide').closest('td').prev().find('label').removeClass('hide');
                } else {
                    jQuery('[name="' + value + '"]').parent().addClass('hide').closest('td').prev().find('label').addClass('hide');
                }
            }

        });
    },
    registerVehicleTypeChange: function () {
        var thisInstance = this;
        jQuery('select[name="vehicle_type"]').change(function () {
            // Trailer/Straight Truck/Cube Van vehicle type only
            var specificationsFields = ['vehicle_feetcapacity', 'vehicle_insideheight', 'vehicle_suspensiontype', 'LBL_VEHICLES_SPECS'];
            // For tractors only
            var specificationsFieldsDromBoxCubes = ['vehicle_dropboxcubes', 'LBL_VEHICLES_SPECS'];
            // Not required for trailers
            var specificationsFieldsCapacityTank = ['vehicle_fuelcapacitytank', 'LBL_VEHICLES_SPECS'];
            // Not required for tractors
            var specificationsFieldsCapacityGVWR = ['vehicle_gvr', 'LBL_VEHICLES_SPECS'];

            var primaryRole = jQuery(this).find('option:selected').val();

            if (primaryRole.indexOf("Trailer") >= 0 || primaryRole.indexOf("Straight Truck") >= 0 || primaryRole.indexOf("Cube Van") >= 0) {
                thisInstance.showHideFields(specificationsFields, 'show');
            } else {
                thisInstance.showHideFields(specificationsFields, 'hide');
            }
            if (primaryRole.indexOf("Tractors") >= 0) {
                thisInstance.showHideFields(specificationsFieldsDromBoxCubes, 'show');
                thisInstance.showHideFields(specificationsFieldsCapacityGVWR, 'hide');
            } else {
                thisInstance.showHideFields(specificationsFieldsDromBoxCubes, 'hide');
                thisInstance.showHideFields(specificationsFieldsCapacityGVWR, 'show');
            }
            if (primaryRole.indexOf("Trailer") >= 0) {
                thisInstance.showHideFields(specificationsFieldsCapacityTank, 'hide');
            } else {
                thisInstance.showHideFields(specificationsFieldsCapacityTank, 'show');
            }
        });
    },
    registerLicensedGrossWeightChange: function () {
        var licensedGrossWeight = jQuery('[name="vehicle_grossweight"]').val();
        if (!licensedGrossWeight || licensedGrossWeight < 55000) {
            jQuery('[name="vehicle_2290_exp_date"]').parent().addClass('hide').closest('td').prev().find('label').addClass('hide');
        }
        jQuery('[name="vehicle_grossweight"]').change(function () {
            var licensedGrossWeight = jQuery('[name="vehicle_grossweight"]').val();
            if (!licensedGrossWeight || licensedGrossWeight < 55000) {
                jQuery('[name="vehicle_2290_exp_date"]').parent().addClass('hide').closest('td').prev().find('label').addClass('hide');
            } else {
                jQuery('[name="vehicle_2290_exp_date"]').parent().removeClass('hide').closest('td').prev().find('label').removeClass('hide');
            }
        });
    },
    registerEvents: function () {
        this._super();
        this.VINUpdates();
        this.registerVehicleTypeChange();
        this.registerLicensedGrossWeightChange();
    },
});
