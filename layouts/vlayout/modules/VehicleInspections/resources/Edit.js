Vtiger_Edit_Js("VehicleInspections_Edit_Js", {}, {
    dueDateUpdates: function () {
        jQuery(document).on('change', '#VehicleInspections_editView_fieldName_inspection_date, select[name="inspection_type"]', function () {
            var inspection = jQuery('select[name="inspection_type"]').val();
            if (inspection == 'Semiannual') {
                jQuery('#VehicleInspections_editView_fieldName_inspection_duedate').val($.datepicker.formatDate(jQuery('#VehicleInspections_editView_fieldName_inspection_duedate').data('date-format').replace('yyyy', 'yy'), new Date(new Date(jQuery('#VehicleInspections_editView_fieldName_inspection_date').val()).setMonth(new Date(jQuery('#VehicleInspections_editView_fieldName_inspection_date').val()).getMonth() + 6))));
            } else if (inspection == 'Annual') {
                jQuery('#VehicleInspections_editView_fieldName_inspection_duedate').val($.datepicker.formatDate(jQuery('#VehicleInspections_editView_fieldName_inspection_duedate').data('date-format').replace('yyyy', 'yy'), new Date(new Date(jQuery('#VehicleInspections_editView_fieldName_inspection_date').val()).setYear(new Date(jQuery('#VehicleInspections_editView_fieldName_inspection_date').val()).getFullYear() + 1))));
            }

        });
    },
   
    registerEvents: function () {
        this._super();
        this.dueDateUpdates();
    }
});

 