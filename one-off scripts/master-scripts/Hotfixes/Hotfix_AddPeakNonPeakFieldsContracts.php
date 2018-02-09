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

$moduleName = 'Contracts';
$module = Vtiger_Module::getInstance($moduleName);
$block = Vtiger_Block::getInstance('LBL_CONTRACTS_TARIFF', $module);

echo "<br><h1>Starting To add peak_discount and non_peak_discount in contacts</h1><br>\n";

if ($block) {
    // CONTRACTS Peak Discount
    $fieldCheck = Vtiger_Field::getInstance('peak_discount', $module);
    if ($fieldCheck) {
        echo '<p>estimate_cube Field already present</p>';
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_CONTRACTS_PEAK_DISCOUNT';
        $field->name = 'peak_discount';
        $field->table = 'vtiger_contracts';
        $field->column = 'peak_discount';
        $field->columntype = 'VARCHAR(10)';
        $field->uitype = '9';
        $field->typeofdata = 'I~O';
        $block->addField($field);

        echo '<p>Added peak_discount field to tariff details block</p>';
    }

    // CONTRACTS Non Peak Discount
    $fieldCheck = Vtiger_Field::getInstance('non_peak_discount', $module);
    if ($fieldCheck) {
        echo '<p>estimate_piece_count Field already present</p>';
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_CONTRACTS_NON_PEAK_DISCOUNT';
        $field->name = 'non_peak_discount';
        $field->table = 'vtiger_contracts';
        $field->column = 'non_peak_discount';
        $field->columntype = 'VARCHAR(10)';
        $field->uitype = '9';
        $field->typeofdata = 'I~O';
        $block->addField($field);

        echo '<p>Added non_peak_discount field to tariff details block</p>';
    }
} else {
    echo '<p>LBL_CONTRACTS_TARIFF Block not found</p>';
}

echo "<br><h1>Finished adding fields peak_discount and non_peak_discount</h1><br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";