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
include_once 'modules/Settings/Picklist/models/Module.php';
include_once 'modules/Settings/Picklist/models/Field.php';

echo "<h3>Begin modifications to valuation fields in estimates</h3>";

$moduleName = 'Estimates';
$picklistFieldName = 'valuation_deductible';


$picklistOrder = [
    'Full Value Protection',
    'Vehicle Coverage',
    'Carrier Base Liability',
    'Vehicle Transport',
    'Full Replacement Value',
];

$module = Vtiger_Module::getInstance($moduleName);

$field1 = Vtiger_Field::getInstance($picklistFieldName, $module);
if ($field1) {
    Vtiger_Utils::ExecuteQuery('TRUNCATE TABLE `vtiger_valuation_deductible`');

    $db = PearDatabase::getInstance();
    $sql = 'UPDATE vtiger_valuation_deductible_seq SET id = 0';
    $result = $db->pquery($sql, []);

    echo "<br> Field '$picklistFieldName' is already present <br>";

    $field1->setPicklistValues($picklistOrder);

    echo "<p>Picklist values added to $picklistFieldName</p>";
} else {
    echo "ERROR: field DOES NOT EXIST: $picklistFieldName.<br />";
}

echo "<h3>Ending modifications to valuation fields in estimates</h3>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";