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

echo "<h3>Starting AddRegisterOrder</h3>\n";

$moduleName = 'Orders';
$blockName = 'LBL_ORDERS_DATES';
$module = Vtiger_Module::getInstance($moduleName);

$block = Vtiger_Block::getInstance($blockName, $module);
if ($block) {

    //**************** REGISTERED ON FIELD *******************//
    $fieldName = 'registered_on';
    $field = Vtiger_Field::getInstance($fieldName, $module);
    if ($field) {
        echo "<p>$fieldName Field already present</p>\n";
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_ORDERS_'.strtoupper($fieldName);
        $field->name = $fieldName;
        $field->table = 'vtiger_orders';
        $field->column = $fieldName;
        $field->columntype = 'VARCHAR(20)';
        $field->uitype = '5';
        $field->typeofdata = 'D~O';

        $block->addField($field);
        echo "<p>Added $fieldName Field</p>\n";
    }
} else {
    echo "$blockName Doesn't exists\n";
}

// Remove booked by date in orders
$fieldName = 'actualenddate';
$field = Vtiger_Field::getInstance($fieldName, $module);
if ($field) {
    $db = PearDatabase::getInstance();
    $sql = "UPDATE `vtiger_field` SET presence = ? WHERE fieldid = ?";
    $query = $db->pquery($sql, [1, $field->id]);

    echo "Updated $fieldName to hide\n";
}

echo "<h3>Ending AddRegisterOrder</h3>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";