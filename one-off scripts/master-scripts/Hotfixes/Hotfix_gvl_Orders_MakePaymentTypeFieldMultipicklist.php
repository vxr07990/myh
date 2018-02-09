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

echo "<h3>Starting MakePaymentTypeFieldMultipicklist</h3>\n";

$db = PearDatabase::getInstance();

$moduleName = 'Orders';
$module = Vtiger_Module::getInstance($moduleName);
$field = Vtiger_Field::getInstance('payment_type', $module);

if ($field) {
    echo "<p>payment_type field already present, setting its ui type to 33</p>\n";

    $sql = 'UPDATE `vtiger_field` SET uitype = ? WHERE fieldid = ? LIMIT 1';
    $db->pquery($sql, [33, $field->id]);

    echo "<p>payment_type field has been set to ui type 33</p>\n";
} else {
    echo "<p>payment_type field still needs added to orders</p>\n";
}


echo "<h3>Ending MakePaymentTypeFieldMultipicklist</h3>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";