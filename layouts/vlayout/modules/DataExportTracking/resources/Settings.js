/* ********************************************************************************
 * The content of this file is subject to the Data Export Tracking ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

jQuery.Class("DataExportTracking_Settings_Js",{

},{
    //updatedBlockSequence : {},
    registerEditButtonEvent: function () {
        var thisInstance=this;
        jQuery('#btn_edit').on("click",function(e) {
            var progressIndicatorElement = jQuery.progressIndicator({
                'position' : 'html',
                'blockInfo' : {
                    'enabled' : true
                }
            });
            var url = "index.php?module=DataExportTracking&view=Settings&mode=EditSettings";
            var setting_id = jQuery('#setting_id').val();
            var actionParams = {
                "type":"POST",
                "url":url,
                "dataType":"html",
                "data" : {'setting_id':setting_id}
            };
            AppConnector.request(actionParams).then(
                function(data) {
                    progressIndicatorElement.progressIndicator({'mode':'hide'});
                    if(data) {
                        jQuery('.contentsDiv').html(data);
                        thisInstance.registerSaveButtonEvent();
                        return false;
                    }
                }
            );
        });
    },
    registerSaveButtonEvent: function () {
        var thisInstance=this;
        jQuery('#btn_save').on("click",function(e) {
            var progressIndicatorElement = jQuery.progressIndicator({
                'position' : 'html',
                'blockInfo' : {
                    'enabled' : true
                }
            });
            var url = "index.php?module=DataExportTracking&view=Settings&mode=SaveSettings";
            var setting_id = jQuery('#setting_id').val();
            var track_listview_exports  = jQuery('#track_listview_exports').is(':checked');
            var track_report_exports    = jQuery('#track_report_exports').is(':checked');
            var track_scheduled_reports = jQuery('#track_scheduled_reports').is(':checked');
            var track_copy_records      = jQuery('#track_copy_records').is(':checked');
            var notification_email      = jQuery('#notification_email').val();
            var actionParams = {
                "type":"POST",
                "url":url,
                "dataType":"html",
                "data" : {'setting_id':setting_id,'track_listview_exports':track_listview_exports,'track_report_exports':track_report_exports,'track_scheduled_reports':track_scheduled_reports,'track_copy_records':track_copy_records,'notification_email':notification_email}
            };
            AppConnector.request(actionParams).then(
                function(data) {
                    progressIndicatorElement.progressIndicator({'mode':'hide'});
                    if(data) {
                        jQuery('.contentsDiv').html(data);
                        thisInstance.registerEditButtonEvent();
                        thisInstance.registerBackEvent();
                        var message = app.vtranslate('Settings saved');
                        params = {
                            text: message,
                            type: 'success'
                        };
                        Vtiger_Helper_Js.showMessage(params);
                        return false;
                    }
                }
            );
        });
    },
    registerBackEvent:function(){
        jQuery('#btn_back').on('click',function(){
            var link = "index.php?module=DataExportTracking&parent=Settings&view=ListData";
            window.location.href = link;
        });
    },
	registerEvents : function() {
         this.registerEditButtonEvent();
		 this.registerBackEvent();
	 }
});