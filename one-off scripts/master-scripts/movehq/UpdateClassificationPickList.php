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

global $adb;
$moduleInstance = Vtiger_Module::getInstance('EmployeeRoles');
if (!$moduleInstance) {
    return;
}
$data = array('Owner Operator','Lease Driver');
$query = $adb->pquery("SELECT * FROM `vtiger_emprole_class` WHERE `emprole_class` IN (?,?)", $data);
$emproleClass = Vtiger_Field::getInstance('emprole_class', $moduleInstance);
if ($adb->num_rows($query)==0 && $emproleClass) {
    $emproleClass->setPicklistValues($data);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";