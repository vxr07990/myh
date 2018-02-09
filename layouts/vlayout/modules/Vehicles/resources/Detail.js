Vtiger_Detail_Js("Vehicles_Detail_Js", {}, {
    registerEvents: function () {
        this._super();
        this.registerLicensedGrossWeightChange();
    },
    registerLicensedGrossWeightChange: function () {
        var licensedGrossWeight = jQuery('#Vehicles_detailView_fieldValue_vehicle_grossweight').text().trim();
        if (!licensedGrossWeight || licensedGrossWeight < 55000) {
            jQuery('#Vehicles_detailView_fieldLabel_vehicle_2290_exp_date').find('label').addClass('hide');
            jQuery('#Vehicles_detailView_fieldValue_vehicle_2290_exp_date').find('span').addClass('hide');
        }
    }
});
