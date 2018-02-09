Vtiger_Edit_Js("WFLocationTypes_Edit_Js", {},
{
  registerRecordPreSaveEvent : function (form) {
    var thisInstance = this;
    if(typeof form == 'undefined') {
        form = this.getForm();
    }
    jQuery('#EditView').on('submit',function(e){
      e.preventDefault();
      var aDeferred = jQuery.Deferred();
      var params = {
          'module' : app.getModuleName(),
          'action' : "CheckDuplicate",
          'prefix' : jQuery('[name="wflocationtypes_prefix"]').val(),
          'warehouse' : jQuery('[name="warehouse"]').val(),
      };
      if(jQuery('input[name="record"]').val()) {
        params['record'] = jQuery('input[name="record"]').val();
      }
      AppConnector.request(params).then(
          function(data) {
              if(data.result.success) {
                // ¯\_(ツ)_/¯
                jQuery('#EditView')[0].submit();
              } else {
                bootbox.alert(data.result.message);
              }
          },
          function(error,err){
          }
      );
    });
  },

    registerWarehouseSelection : function () {
        var thisInstance = this;
        jQuery('input:hidden[name^="warehouse"]').each(function(){
            jQuery(this).off(Vtiger_Edit_Js.referenceSelectionEvent);
            jQuery(this).on(Vtiger_Edit_Js.referenceSelectionEvent, function(){
                thisInstance.updatePrefixPicklist();
            });
        });
    },

    updatePrefixPicklist : function () {
        var warehouseField = jQuery('[name="warehouse"]');
        if(warehouseField.val().length > 0){
            var params = {
                'module' : app.getModuleName(),
                'action' : "UpdatePicklist",
                'name' : "wflocationtypes_prefix",
                'warehouse' : jQuery('[name="warehouse"]').val(),
                'record' : jQuery('[name="record"]').val()
            };
            AppConnector.request(params).then(
                function(data) {
                    if(data.result.success) {
                        var picklistOptions = data.result.picklist;
                        var prefixPicklist = jQuery('[name="wflocationtypes_prefix"]');
                        var currentVal = prefixPicklist.val();
                        prefixPicklist.empty();
                        jQuery.each(picklistOptions, function(key, value){
                             prefixPicklist.append(jQuery("<option></option>").attr("value", value).text(key));
                         });
                        prefixPicklist.val(currentVal);
                        prefixPicklist.trigger('liszt:updated');

                    }
                },
                function(error,err){
                }
            );
        }
    },

    registerEvents: function(){
        this._super();
        this.registerWarehouseSelection();
        this.updatePrefixPicklist();
    },
});
