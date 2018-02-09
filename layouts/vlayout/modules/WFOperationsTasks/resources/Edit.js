Vtiger_Edit_Js('WFOperationsTasks_Edit_Js', {},
{
  getInstance: function() {
    return new WFOperationsTasks_Edit_Js();
  },

  triggerLineItemUpdate: function(){
    jQuery('input[name^="wfinventory_"],input[name^="wfarticle_"]').on(Vtiger_Edit_Js.referenceSelectionEvent,function(){
      var field = jQuery(this);
      if(!field.val()) {
        return;
      }
      var params = {
        'module' : 'WFLineItems',
        'action' : 'GetLineItemDetails',
        'fieldname'  : field.attr('name'),
        'record' : field.val()
      };

      AppConnector.request(params).then(
          function(data) {
            if(data.success) {
              var rownum = field.attr('name').split('_').pop();
              jQuery('input[name="description_' + rownum +'"]').val(data.result.data.description);
              jQuery('input[name="onhand_' + rownum +'"]').val(data.result.data.onhand);
              Vtiger_Edit_Js.setPicklistOptions("location_" + rownum, data.result.data.locations);
            }
          },
          function(error,err){
          }
      );

    });
  },

  registerRebind : function() {
    var thisInstance = this;
    jQuery('.addWFLineItems').on('click',function(){
      thisInstance.triggerLineItemUpdate();
    });
  },
// Gross don't care
  updateExistingLineItems : function() {
    var thisInstance = this;
    if(jQuery('[name="record"]').val()) {
      jQuery.each(jQuery('input[name^="wfinventory_"],input[name^="wfarticle_"]'),function(){
        var field = jQuery(this);
        if(!field.val()) {
          return;
        }
        var params = {
          'module' : 'WFLineItems',
          'action' : 'GetLineItemDetails',
          'fieldname'  : field.attr('name'),
          'record' : field.val()
        };

        AppConnector.request(params).then(
            function(data) {
              if(data.success) {
                var rownum = field.attr('name').split('_').pop();
                jQuery('input[name="description_' + rownum +'"]').val(data.result.data.description);
                jQuery('input[name="onhand_' + rownum +'"]').val(data.result.data.onhand);
                Vtiger_Edit_Js.setPicklistOptions("location_" + rownum, data.result.data.locations);
              }
            },
            function(error,err){
            }
        );

      });
    }
  },

  // initializeAddressAutofill : function(moduleName) {
  //   var disabledModules = jQuery('#disabledGoogleModules').val();
  //   if(typeof disabledModules == 'undefined'){
  //     disabledModules = [];
  //   } else {
  //     disabledModules = disabledModules.split('::');
  //   }
  //   if (typeof google == 'undefined') {
  //     return;
  //   }
  //
  //   if(disabledModules.indexOf(moduleName) != -1) {
  //     return;
  //   }
  //   var thisInstance = this;
  //     thisInstance.WFOperationsTaskForm = {
  //         street_address: moduleName + '_editView_fieldName_address',
  //         locality: moduleName + '_editView_fieldName_city',
  //         administrative_area_level_1: moduleName + '_editView_fieldName_state',
  //         country: moduleName + '_editView_fieldName_country',
  //         postal_code: moduleName + '_editView_fieldName_zip'
  //     };
  //
  //   var addressFields = [
  //     'address',
  //     'city',
  //     'state',
  //     'country',
  //     'zip'
  //   ];
  //
  //   jQuery.each(addressFields,function(i, val) {
  //     if(jQuery('#' + moduleName + '_editView_fieldName_' + val).length) {
  //       autocompleteOriginAdd = new google.maps.places.Autocomplete(
  //         (document.getElementById(moduleName + '_editView_fieldName_' + val)),
  //         { types: ['geocode'] });
  //
  //       google.maps.event.addListener(autocompleteOriginAdd, 'place_changed', function() {
  //         thisInstance.fillInAddress('WFOperationsTasks', autocompleteOriginAdd);
  //       });
  //     }
  //   });
  //
  // },

  registerEvents: function(){
    this._super();
   // this.initializeAddressAutofill(jQuery('[name="module"]').val());
   this.updateExistingLineItems();
  },
});
