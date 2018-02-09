/**
 * Created by mmuir on 4/21/2017.
 */

Vtiger_Edit_Js("Containers_Edit_Js", {}, {

    registerContainerDimensionChange : function() {
        jQuery('input[name="containers_length"], input[name="containers_width"], input[name="containers_height"]').off('change').on('change', function() {
            var calcLength = parseInt(jQuery('input[name="containers_length"]').val());
            var calcWidth = parseInt(jQuery('input[name="containers_width"]').val());
            var calcHeight = parseInt(jQuery('input[name="containers_height"]').val());
            var cubic = (calcLength*calcWidth*calcHeight) / 1728;
            if (!isNaN(cubic)) {
                jQuery('input[name="containers_cuft"]').val(cubic.toFixed(2));
            } else {
                jQuery('input[name="containers_cuft"]').val('');
            }
            jQuery('input[name="containers_cuft"]').trigger('change');
        });
    },

    registerContainerWeightChange : function() {
        jQuery('input[name="containers_grosswt"], input[name="containers_tarewt"]').off('change').on('change', function() {
            var grossWeight = parseInt(jQuery('input[name="containers_grosswt"]').val());
            var tareWeight = parseInt(jQuery('input[name="containers_tarewt"]').val());
            var netWeight = grossWeight - tareWeight;
            if (!isNaN(netWeight)){
                if(grossWeight >= tareWeight){
                    jQuery('input[name="containers_netwt"]').val(netWeight);
                } else {
                    jQuery('input[name="containers_netwt"]').val(0);
                }
                jQuery('input[name="containers_netwt"]').trigger('change');
            }
        });
    },

    registerContainerDensityChange : function() {
        jQuery('input[name="containers_netwt"], input[name="containers_cuft"]').off('change').on('change', function(){
            var netWt = jQuery('input[name="containers_netwt"]').val();
            var cuFt = jQuery('input[name="containers_cuft"]').val();
            if(cuFt > 0) {
                var density = netWt / cuFt;
                if (!isNaN(density)) {
                    jQuery('input[name="containers_density"]').val(density.toFixed(2));
                } else {
                    jQuery('input[name="containers_density"]').val('');
                }
            }
        });
    },


    registerContainerTypeChange : function(){
        hiddenElement = jQuery('input:hidden[name="containers_containertypes"]');
        hiddenElement.on(Vtiger_Edit_Js.referenceSelectionEvent, function(){
            hiddenElement = jQuery('input:hidden[name="containers_containertypes"]');
            contactId = hiddenElement.val();
            populateContainerTypeData(contactId);
        });
    },

    registerBasicEvents: function (container, quickCreateParams) {
        this._super(container);
        this.registerContainerDimensionChange();
        this.registerContainerWeightChange();
        this.registerContainerDensityChange();
        this.registerContainerTypeChange();
        jQuery('input[name="containers_cuft"]').attr("readonly", "readonly");
        jQuery('input[name="containers_netwt"]').attr("readonly", "readonly");
        jQuery('input[name="containers_density"]').attr("readonly", "readonly");
    }
});

function populateContainerTypeData(containerTypeId) {
    if(containerTypeId>0) {
        var dataUrl = "index.php?module=Containers&action=PopulateContainerTypeData&source="+containerTypeId;
        AppConnector.request(dataUrl).then(
            function(data) {
                if(data.success) {
                    var container = data.result;
                    jQuery('input[name="containers_content"]').val(container.containertypes_content);
                    jQuery('input[name="containers_length"]').val(container.containertypes_length);
                    jQuery('input[name="containers_width"]').val(container.containertypes_width);
                    jQuery('input[name="containers_height"]').val(container.containertypes_height);
                    jQuery('input[name="containers_height"]').trigger('change');
                    jQuery('input[name="containers_tarewt"]').val(container.containertypes_emptywt);
                    jQuery('input[name="containers_desc"]').val(container.containertypes_desc);
                }
            }
        );
    }
}
