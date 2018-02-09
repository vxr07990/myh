Vtiger_List_Js("WFLocations_List_Js", {

    registerFixedLocationCheck: function () {
        jQuery('input[type="checkbox"]').change(function () {
            var checkedBoxes = jQuery('input[type="checkbox"]:checked');
            var disable = false;
            jQuery.each(checkedBoxes, function () {
                var ele = jQuery(this);
                if (jQuery('input#' + ele.val() + '_fixed').val() == 'true') {
                    disable = true;
                    if (jQuery('input[id="' + ele.val() + '_notice"]').length == 0) {
                        jQuery('input#' + ele.val() + '_fixed').after("<input type='hidden' id='" + ele.val() + "_notice' value=" + ele.val() + " />");
                    }
                }
            });

            var lielement = jQuery('li#WFLocations_listView_massAction_LBL_WFLOCATIONS_MOVE_LOCATION');
            if (disable) {
                Vtiger_List_Js.disableMassAction(lielement, "triggerFixedNotice();");
            } else {
                Vtiger_List_Js.enableMassAction(lielement, "triggerMoveLocation();");
            }
        })
    },

    registerMultiSaveMessage: function () {
        multiSave = jQuery('input[id="multiSaveMessage"]').val();
        if(multiSave && multiSave.length > 0){
            bootbox.alert(multiSave);
        }
    },

    registerEvents: function (container, quickCreateParams) {
        this._super(container);
        this.registerFixedLocationCheck();
        this.registerMultiSaveMessage();
    }

});

function triggerMoveLocation() {
  var checkedBoxes = jQuery('input[type="checkbox"]:checked');
  var ids = [];

  jQuery.each(checkedBoxes,function(){
    ids.push(this.value);
  });

  var params = {
    module: 'WFLocations',
    view: 'MoveLocation',
    location_ids: ids,
  };

  AppConnector.request(params).then(
    function (data) {
      app.showModalWindow(data);
    }
  ).then(
    function () {
      var editInstance = Vtiger_Edit_Js.getInstance();
      editInstance.registerBasicEvents(jQuery('#moveContainer'));
    }
  );
}

function triggerFixedNotice() {
  var notices = jQuery('input[id*="_notice"]');
  var message = '';
  jQuery.each(notices,function(){
    message += jQuery('tr.record'+this.value+"` :nth-child(3)").first().text();
    message += ', ';
  });

    bootbox.alert("Location tags " + message.slice(0, -2) + " are Fixed locations and cannot be moved.");
}
