<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class DataExportTracking_List_View extends Vtiger_Index_View {
    function __construct() {
        $db = PearDatabase::getInstance();
        $sql="SELECT * FROM vtiger_settings_field WHERE `name` = ?";
        $res=$db->pquery($sql,array('Data Export Tracking'));
        $fieldid=$db->query_result($res,0,'fieldid');
        header("location: index.php?module=DataExportTracking&parent=Settings&view=ListData&block=4&fieldid=$fieldid");
    }
}