/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("AWSDocs_Detail_Js", {}, {
    registerFileDownloadClick: function () {
        jQuery("#AWSDocs_detailView_fieldValue_awsdocs_filename").on("click", function () {

            window.location.href = 'index.php?module=' + app.getModuleName() + '&action=DownloadFile&mode=download&record=' + jQuery('#recordId').val();

            return false;
        });
    },
    registerEvents: function () {
        this.registerFileDownloadClick();
        this._super();

    }

});