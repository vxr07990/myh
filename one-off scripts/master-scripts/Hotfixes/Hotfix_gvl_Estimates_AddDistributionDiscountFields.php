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

echo "<h3>Starting AddDistributionDiscountFields</h3>\n";

$moduleName = 'Estimates';
$blockName = 'LBL_QUOTES_SITDETAILS';
$tableName = 'vtiger_quotes';
$module = Vtiger_Module::getInstance($moduleName);

$block = Vtiger_Block::getInstance($blockName, $module);
if ($block) {
    echo "<p>The $blockName exists, adding new fields</p>\n";

    //Add distribution discount % field
    $fieldName = 'distribution_discount_percentage';
    $field = Vtiger_Field::getInstance($fieldName, $module);
    if ($field) {
        echo "<p>$fieldName Field already present</p>\n";
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_ORDERS_'.strtoupper($fieldName);
        $field->name = $fieldName;
        $field->table = $tableName;
        $field->column = $fieldName;
        $field->columntype = 'decimal(7,2)';
        $field->uitype = '9';
        $field->typeofdata = 'N~O';

        $block->addField($field);
        echo "<p>Added $fieldName Field</p>\n";
    }

    $fieldName = 'distribution_discount';
    $field = Vtiger_Field::getInstance($fieldName, $module);
    if ($field) {
        echo "<p>$fieldName Field already present</p>\n";
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_ORDERS_'.strtoupper($fieldName);
        $field->name = $fieldName;
        $field->table = $tableName;
        $field->column = $fieldName;
        $field->columntype = 'decimal(7,2)';
        $field->uitype = '7';
        $field->typeofdata = 'N~O';

        $block->addField($field);
        echo "<p>Added $fieldName Field</p>\n";
    }
} else {
    echo "<p>The $blockName block not found</p>\n";
}
echo "<h3>Ending AddDistributionDiscountFields</h3>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";