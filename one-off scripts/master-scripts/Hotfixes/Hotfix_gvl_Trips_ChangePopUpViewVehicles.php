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

echo "<h3>Starting ChangedPopUpViewVehicles</h3>\n";

$moduleName = 'Vehicles';
$blockName = 'LBL_VEHICLES_INFORMATION';
$module = Vtiger_Module::getInstance($moduleName);

$block = Vtiger_Block::getInstance($blockName, $module);
if ($block) {
    echo "<p>The $blockName block exists</p>\n";

    $fieldName = 'vechiles_unit'; // Yes this is the way it was spelled
    $field = Vtiger_Field::getInstance($fieldName, $module);
    if ($field) {
        $db = PearDatabase::getInstance();
        $sql = 'UPDATE `vtiger_field` SET summaryfield = ? WHERE fieldid = ?';
        $db->pquery($sql, [1, $field->id]);
    }
    echo "<p>Updated the $fieldName's summaryfield value</p>\n";

    $fieldName = 'vehicle_number'; // Yes this is the way it was spelled
    $field = Vtiger_Field::getInstance($fieldName, $module);
    if ($field) {
        $db = PearDatabase::getInstance();
        $sql = 'UPDATE `vtiger_field` SET summaryfield = ? WHERE fieldid = ?';
        $db->pquery($sql, [0, $field->id]);
    }

    echo "<p>Updated the $fieldName's summaryfield value</p>\n";
} else {
    echo "<p>The $blockName block was not found</p>\n";
}
echo "<h3>Ending ChangedPopUpViewVehicles</h3>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";