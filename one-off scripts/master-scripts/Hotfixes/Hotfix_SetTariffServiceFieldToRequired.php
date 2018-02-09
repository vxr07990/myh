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


$Vtiger_Utils_Log = true;

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');
include_once('modules/ModComments/ModComments.php');
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
include_once 'modules/Users/Users.php';

$moduleName = 'TariffServices';

echo '<h1>Updating Tariff Service Field (service_base_charge_applies)</h1>';

$module = Vtiger_Module::getInstance($moduleName);
$field = Vtiger_Field::getInstance('service_base_charge_applies', $module);
if ($field) {
    $sql = "UPDATE `vtiger_field` SET `typeofdata` = 'V~M' WHERE `tabid` = '" . $module->getId() . "' AND `fieldid` = '" . $field->id . "' LIMIT 1";
    Vtiger_Utils::ExecuteQuery($sql);
} else {
    echo '<h4>Field service_base_charge_applies not found</h4>';
}

echo '<h2>Tariff Service Field Updated</h2>';


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";