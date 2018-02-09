/* ********************************************************************************
 * The content of this file is subject to the Related List Search ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
 jQuery.Class("VTEFavorite_Settings_Js",{

},{
     registerSaveSettingsEvent: function() {
         var thisInstance = this;
         jQuery('#status').change(function() {
             var status = $(this).is(":checked");
             var progressIndicatorElement = jQuery.progressIndicator({
                 'position' : 'html',
                 'blockInfo' : {
                     'enabled' : true
                 }
             });

             var params = {};
             params['module'] = 'VTEFavorite';
             params['action'] = 'ActionAjax';
             params['status'] = status;
             AppConnector.request(params).then(
                 function(data) {
                     progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                     var params_massage = {};
                     if(status) params_massage.text = app.vtranslate('VTEFavorite enabled');
                     else params_massage.text = app.vtranslate("VTEFavorite disabled");
                     Settings_Vtiger_Index_Js.showMessage(params_massage);
                 }
             );
         });
    },
    registerEvents : function() {
         this.registerSaveSettingsEvent();
     }
});