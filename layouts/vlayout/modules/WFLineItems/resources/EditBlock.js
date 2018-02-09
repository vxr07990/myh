Vtiger_EditBlock_Js("WFLineItems_EditBlock_Js", {},
{
  triggerLineItemUpdate: function(){
    console.log('In register events');
    jQuery('input[name^="wfinventory"],input[name^="wfarticle"]').on(Vtiger_Edit_Js.referenceSelectionEvent,function(){
      var field = this;
      // jQuery('input[name="account_id"]', container).on(Vtiger_Edit_Js.referenceSelectionEvent, function(e, data){
  		// 	thisInstance.referenceSelectionEventHandler(data, container);
  		// });
      var params = {
        'module' : 'WFInventoryLocation',
        'action' : 'GetLineItemDetails',
        'fieldname'  : field.attr('name'),
        'record' : field.val()
      };

      AppConnector.request(params).then(
          function(data) {
            console.log(data);
          },
          function(error,err){
          }
      );

    });
  },

  // registerRules: function (isEditView) {
  //   var rules = {
  //     record: {
  //         conditions: [
  //             {
  //                 operator: 'set',
  //                 targetFields: [
  //                     {
  //                         name: 'create_multiple',
  //                         hide: true,
  //                         setValue: 'No'
  //                     },
  //                     {
  //                         name: 'range_from',
  //                         hide: true,
  //                     },
  //                     {
  //                         name: 'range_to',
  //                         hide: true,
  //                     },
  //                     {
  //                         name: 'row_to',
  //                         hide: true,
  //                     },
  //                     {
  //                         name: 'bay_to',
  //                         hide: true,
  //                     },
  //                     {
  //                         name: 'level_to',
  //                         hide: true,
  //                     },
  //                 ]
  //             }
  //         ]
  //     },
  //   };
  //   this.applyVisibilityRules(rules, isEditView);
  // },

  registerEvents: function(){
      this._super();
      this.triggerLineItemUpdate();
  },
});
