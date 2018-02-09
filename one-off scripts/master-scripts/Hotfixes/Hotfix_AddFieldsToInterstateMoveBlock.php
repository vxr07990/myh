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

$moduleName = 'Estimates';
$module = Vtiger_Module::getInstance($moduleName);

echo "<br><h1>Starting To add estimate_type in estimates</h1><br>\n";

$blockName = 'LBL_QUOTES_INTERSTATEMOVEDETAILS';
echo "<br><h1>Starting To add fields to the LBL_QUOTES_INTERSTATEMOVEDETAILS block</h1><br>\n";

$block = Vtiger_Block::getInstance($blockName, $module);

if ($block) {
    // ESTIMATE Cube
    $fieldCheck = Vtiger_Field::getInstance('estimate_cube', $module);
    if ($fieldCheck) {
        echo '<p>estimate_cube Field already present</p>';
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_QUOTES_ESTIMATE_CUBE';
        $field->name = 'estimate_cube';
        $field->table = 'vtiger_quotes';
        $field->column = 'estimate_cube';
        $field->columntype = 'VARCHAR(10)';
        $field->uitype = '1';
        $field->typeofdata = 'I~O';
        $block->addField($field);

        echo '<p>Added estimate_cube field to interstate move details block</p>';
    }

    // ESTIMATE Piece Count
    $fieldCheck = Vtiger_Field::getInstance('estimate_piece_count', $module);
    if ($fieldCheck) {
        echo '<p>estimate_piece_count Field already present</p>';
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_QUOTES_ESTIMATE_PIECE_COUNT';
        $field->name = 'estimate_piece_count';
        $field->table = 'vtiger_quotes';
        $field->column = 'estimate_piece_count';
        $field->columntype = 'VARCHAR(10)';
        $field->uitype = '1';
        $field->typeofdata = 'I~O';
        $block->addField($field);

        echo '<p>Added estimate_piece_count field to interstate move details block</p>';
    }

    // ESTIMATE Pack Count
    $fieldCheck = Vtiger_Field::getInstance('estimate_pack_count', $module);
    if ($fieldCheck) {
        echo '<p>estimate_pack_count Field already present</p>';
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_QUOTES_ESTIMATE_PACK_COUNT';
        $field->name = 'estimate_pack_count';
        $field->table = 'vtiger_quotes';
        $field->column = 'estimate_pack_count';
        $field->columntype = 'VARCHAR(10)';
        $field->uitype = '1';
        $field->typeofdata = 'I~O';
        $block->addField($field);

        echo '<p>Added estimate_pack_count field to interstate move details block</p>';
    }
} else {
    echo '<p>LBL_QUOTES_INTERSTATEMOVEDETAILS Block not found</p>';
}

echo "<br><h1>Finished </h1><br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";