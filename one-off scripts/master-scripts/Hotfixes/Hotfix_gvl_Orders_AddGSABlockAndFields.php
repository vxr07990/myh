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

echo "<h3>Starting AddMoveDetailsBlockAndFields</h3>\n";

$moduleName = 'Orders';
$blockName = 'LBL_GSA_INFORMATION';
$module = Vtiger_Module::getInstance($moduleName);

$block = Vtiger_Block::getInstance($blockName, $module);
if ($block) {
    echo "<p>The $blockName block already exists</p>\n";
    addOrdersGSABlockFields($module, $block);
} else {
    $block = new Vtiger_Block();
    $block->label = $blockName;
    $module->addBlock($block);

    // Check to make sure the block was added
    $block = Vtiger_Block::getInstance($blockName, $module);
    if ($block) {
        addOrdersGSABlockFields($module, $block);
    } else {
        echo "<p>The $blockName block failed to create</p>\n";
    }
}

function addOrdersGSABlockFields($module, $block)
{

    //**************** GBL NUMBER FIELD *******************//
    $fieldName = 'gbl_number';
    $field = Vtiger_Field::getInstance($fieldName, $module);
    if ($field) {
        echo "<p>$fieldName Field already present</p>\n";
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_ORDERS_'.strtoupper($fieldName);
        $field->name = $fieldName;
        $field->table = 'vtiger_orders';
        $field->column = $fieldName;
        $field->columntype = 'INT(11)';
        $field->uitype = '7';
        $field->sequence = 1;
        $field->typeofdata = 'V~O';

        $block->addField($field);
        
        $field->setRelatedModules('Contacts');

        echo "<p>Added $fieldName Field</p>\n";
    }

    //**************** ACCOUNT CONTACTS FIELD *******************//
    $fieldName = 'account_contacts';
    $field = Vtiger_Field::getInstance($fieldName, $module);
    if ($field) {
        echo "<p>$fieldName Field already present</p>\n";
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_ORDERS_'.strtoupper($fieldName);
        $field->name = $fieldName;
        $field->table = 'vtiger_orders';
        $field->column = $fieldName;
        $field->columntype = 'INT(11)';
        $field->uitype = '10';
        $field->sequence = 1;
        $field->typeofdata = 'V~O';

        $block->addField($field);
        $field->setRelatedModules(['Contacts']);

        echo "<p>Added $fieldName Field</p>\n";
    }

    echo "<h3>Ending AddMoveDetailsBlockAndFields</h3>\n";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";