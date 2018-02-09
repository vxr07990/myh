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

// Add new vaules for Rate Type in Tariff Services
$moduleModel = Settings_Picklist_Module_Model::getInstance('TariffServices');
$fieldModel = Settings_Picklist_Field_Model::getInstance('rate_type', $moduleModel);
$rolesSelected = array();
$newPicklistValue = array('Storage Valuation');
foreach ($newPicklistValue as $newValue) {
    $rs = $adb->pquery("SELECT * FROM vtiger_rate_type WHERE rate_type =?", array($newValue));
    if ($adb->num_rows($rs) == 0) {
        $moduleModel->addPickListValues($fieldModel, $newValue, $rolesSelected);
    }
}

// Create vtiger_quotes_storage_valution table
if (!Vtiger_Utils::CheckTable('vtiger_quotes_storage_valution')) {
    Vtiger_Utils::CreateTable(
        "vtiger_quotes_storage_valution",
        "(`estimateid` int(11) DEFAULT NULL,
              `serviceid` int(11) DEFAULT NULL,
              `rate` decimal(12,3) DEFAULT NULL,
              `base_charge_applies` VARCHAR(255) DEFAULT NULL,
              `months` int(11) DEFAULT NULL)",
        true);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";