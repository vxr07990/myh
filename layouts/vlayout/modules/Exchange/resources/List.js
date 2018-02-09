/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
jQuery.Class("Contact", {
    _init: function () {
        if (jQuery('#ms_sync_button')) {
            jQuery('#ms_sync_button').on('click', function () {
                var buttonText = jQuery('#ms_sync_button b').text();
                jQuery('#ms_sync_button b').text(app.vtranslate('LBL_SYNCRONIZING'));
                jQuery('#ms_sync_button').attr("disabled", "disabled");
                jQuery('#ms_synctime').remove();
                var imagePath = app.vimage_path('Sync.gif');
                jQuery('#ms_sync_details').html('<img src=' + imagePath + ' style="margin-left:40px"/>');
                var url = jQuery('#ms_sync_button').data('url');

                AppConnector.request(url).then(
                    function (data) {
                        var response;
                        console.dir(data);
                        try {
                            response = JSON.parse(data);
                        } catch (e) {
                            console.dir("caught an error");
                        }
                        if (response && response.error.code == '401') {
                            jQuery('#ms_firsttime').val('yes');
                            jQuery('#ms_removeSyncBlock').hide();
                            //jQuery('#ms_sync_button').click();
                            jQuery('#ms_sync_message').find('div').html("<div style='text-align:center'><img src='layouts/vlayout/skins/images/denied.gif' /></div><br />" + app.vtranslate(response.error.message));
                        } else if(response && response.error.code == '999') {
                            jQuery('#ms_sync_message').find('div').html("<div style='text-align:center'><img src='layouts/vlayout/skins/images/denied.gif' /></div><br />The Exchange server returned an error:<br /><div style='font-weight:bold; font-size:120%'>" + app.vtranslate(response.error.message) + "</div>");
                        } else {
                            jQuery('#ms_sync_button b').text(buttonText);
                            jQuery('#ms_sync_button').removeAttr("disabled");
                            jQuery('#ms_sync_details').html(data);
                            // if (jQuery('#ms_norefresh').length == 0) {
                            //     listInstance = Calendar_List_Js.getInstance();
                            //     listInstance.getListViewRecords()
                            // }
                        }
                    }
                );
            });
            jQuery('#ms_remove_sync').on('click', function () {
                var url = jQuery('#ms_remove_sync').data('url');
                AppConnector.request(url).then(
                    function (data) {
                        jQuery('#ms_firsttime').val('yes');
                        jQuery('#ms_removeSyncBlock').hide();
                        var params = {
                            title: app.vtranslate('JS_MESSAGE'),
                            text: app.vtranslate('SYNC_REMOVED_SUCCESSFULLY'),
                            animation: 'show',
                            type: 'info'
                        };
                        Vtiger_Helper_Js.showPnotify(params);
                    }
                );
            });
        }
        var data = jQuery('#ms_mappingTable').html();
        jQuery('#ms_popid').popover({
            'html': true,
            'content': data,
            'title': app.vtranslate('FIELD_MAPPING')
        });

        jQuery('#ms_removePop').popover({
            'html': true,
            'content': app.vtranslate('REMOVE_SYNCHRONIZATION_MESSAGE'),
            'title': app.vtranslate('REMOVE_SYNCHRONIZATION')
        });
    },

    _showMessage: function () {

    },
    _exit: function () {

    }
}, {});

jQuery('document').ready(function () {
    jQuery('#ms_mappingTable').hide();
    Contact._init();
});
