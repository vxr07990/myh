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
error_reporting(E_ERROR);
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');
$Vtiger_Utils_Log = true;

global $adb;
$data = array('Agents', 'CommissionPlans', 'ContainerTypes', 'Equipment', 'ItemCodes', 'Tariffs', 'Vanlines', 'Vehicles', 'Vendors', 'ZoneAdmin');

foreach ($data as $result) {
    $vtranslate = vtranslate($result, $result);
    $val = $adb->pquery("select * from `vtiger_settings_field` WHERE `name`='{$result}' AND `description` ='{$vtranslate }'");
    if ($adb->num_rows($val) == 0) {
        $max_id = $adb->getUniqueID('vtiger_settings_field');
        $adb->pquery("INSERT INTO `vtiger_settings_field` (`fieldid`, `blockid`, `name`, `description`, `linkto`, `sequence`,`pinned`) VALUES (?, ?, ?, ?, ?, ?,?)", array($max_id, '4', $result, vtranslate($result, $result), 'index.php?module=' . $result . '&view=List', $max_id, '1'));
    }
}
 $menucleaner = $adb->pquery("SELECT * FROM `vtiger_settings_field` WHERE `name` = ?", array('MenuCleaner'));
if ($adb->num_rows($menucleaner) == 0) {
    $max_id = $adb->getUniqueID('vtiger_settings_field');
    $adb->pquery("INSERT INTO `vtiger_settings_field` (`fieldid`, `blockid`, `name`, `description`, `linkto`, `sequence`) VALUES ( ?, ?, ?, ?, ?,?)", array($max_id, '4', 'MenuCleaner', 'Menu Cleaner', 'index.php?module=MenuCleaner&parent=Settings&view=Index', $max_id));
}


// Create MenuCleaner module
$moduleInstance = Vtiger_Module::getInstance('MenuCleaner');
if ($moduleInstance) {
    echo "<h2>MenuCleaner already exists </h2><br>";
} else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'MenuCleaner';
    $moduleInstance->save();
    $moduleInstance->initWebservice();
}
echo "Success";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";