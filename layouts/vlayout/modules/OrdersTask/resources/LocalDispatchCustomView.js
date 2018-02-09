jQuery.Class("OrdersTask_LocalDispatchCustomView_Js", {}, {
    checkLocalDispatchMandatoryColumns: function () {
        jQuery('#CustomView').find('.btn-success').on('click', function (e) {

            var mandatoryColumns = ['dispatch_status', 'disp_assigneddate', 'assigned_employee', 'assigned_vehicles'];
            var selectedColumns = jQuery('#viewColumnsSelect').val();
            var selColumnsNames = [];
            var mandatoryMissing = false;

            jQuery.each(selectedColumns, function (index, value) {
                var column = value.split(':')[1];
                selColumnsNames.push(column);
            });

            var AlertMessage = 'The following columns are mandatory: <br>'
            jQuery.each(mandatoryColumns, function (index, value) {
            
                if (jQuery.inArray(value, selColumnsNames) == -1) {
                    AlertMessage =  AlertMessage + '   * ' + app.vtranslate(value) + ' <br>';
                    mandatoryMissing = true;
                }

            });

            if (mandatoryMissing && !jQuery('[name="rightTable"]').val()) {
                bootbox.alert(AlertMessage);
                e.preventDefault();
                return false;
            }

        });
    },
    selectMandatoryFields: function(){
	
    
        if(jQuery('#record').length > 0 && jQuery('#record').val() == ''){
            jQuery("#viewColumnsSelect").find('option[data-field-name="dispatch_status"]').prop("selected",true);
            jQuery("#viewColumnsSelect").find('option[data-field-name="disp_assigneddate"]').prop("selected",true);
            jQuery("#viewColumnsSelect").find('option[data-field-name="assigned_employee"]').prop("selected",true);
            jQuery("#viewColumnsSelect").find('option[data-field-name="assigned_vehicles"]').prop("selected",true);
            jQuery("#viewColumnsSelect").trigger("change");            
        }

        var auxArray = [app.vtranslate("dispatch_status"),app.vtranslate("disp_assigneddate"),app.vtranslate("assigned_employee"),app.vtranslate("assigned_vehicles")];
        
        jQuery("ul.select2-choices li").each(function(){
            if(auxArray.indexOf(jQuery(this).find("div").text()) > -1){
                jQuery(this).find("a").hide();
            }
        });
    }
});