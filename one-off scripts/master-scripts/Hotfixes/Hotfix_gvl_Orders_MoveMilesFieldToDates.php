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

echo "<h3>Starting MoveMilesFieldToDates</h3>\n";

$moduleName = 'Orders';
$module = Vtiger_Module::getInstance($moduleName);

$db = PearDatabase::getInstance();

$block = Vtiger_Block::getInstance('LBL_ORDERS_DATES', $module);

$field = Vtiger_Field::getInstance('orders_miles', $module);
if ($field) {
    echo "orders_miles Field exists lets move it to the dates block<br>\n";
    if ($block) {
        $sql = 'UPDATE `vtiger_field` SET block = ?, fieldlabel = ? WHERE fieldid = ?';
        $db->pquery($sql, [$block->id, 'LBL_ORDERS_MILEAGE', $field->id]);

        echo "orders_miles Field moved to the dates block<br>\n";
    } else {
        echo "LBL_ORDERS_DATES block doesn't exists<br>\n";
    }
} else {
    echo "orders_miles Field doesn't exists<br>\n";
}


echo "<h3>Ending MoveMilesFieldToDates</h3>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";