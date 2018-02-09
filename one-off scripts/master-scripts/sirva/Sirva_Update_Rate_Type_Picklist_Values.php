<?php
if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

require_once 'include/utils/utils.php';
    require_once 'include/utils/CommonUtils.php';

    require_once 'includes/Loader.php';
    vimport('includes.runtime.EntryPoint');

    global $adb;

    // Delete picklist value
    $primaryKey = Vtiger_Util_Helper::getPickListId('rate_type');
    $adb->pquery('DELETE FROM vtiger_rate_type	WHERE '.$primaryKey.' = (25)', array());
    $adb->pquery("DELETE FROM vtiger_picklist_dependency WHERE sourcevalue IN ('SIT ITem') AND sourcefield='rate_type'", array());
    // Add new vaules
    $moduleModel = Settings_Picklist_Module_Model::getInstance('TariffServices');
    $fieldModel = Settings_Picklist_Field_Model::getInstance('rate_type', $moduleModel);
    $rolesSelected = array();
    $newPicklistValue = array('SIT Cartage','SIT First Day Rate','SIT Additional Day Rate');
    foreach ($newPicklistValue as $newValue) {
        $rs = $adb->pquery("SELECT * FROM vtiger_rate_type WHERE rate_type =?", array($newValue));
        if ($adb->num_rows($rs) == 0) {
            $moduleModel->addPickListValues($fieldModel, $newValue, $rolesSelected);
        }
    }


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";