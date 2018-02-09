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
$blockName = 'LBL_MILITARY_INFORMATION';
$module = Vtiger_Module::getInstance($moduleName);

$block = Vtiger_Block::getInstance($blockName, $module);
if ($block) {
    echo "<p>The $blockName block already exists</p>\n";
    addMilitaryInformationBlockOrders($module, $block);
} else {
    $block = new Vtiger_Block();
    $block->label = $blockName;
    $module->addBlock($block);

    // Check to make sure the block was added
    $block = Vtiger_Block::getInstance($blockName, $module);
    if ($block) {
        addMilitaryInformationBlockOrders($module, $block);
    } else {
        echo "<p>The $blockName block failed to create</p>\n";
    }
}



function addMilitaryInformationBlockOrders($module, $block)
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
        echo "<p>Added $fieldName Field</p>\n";
    }

    //**************** Transferee Rank/Grade FIELD *******************//
    $fieldName = 'transferee_rank_grade';
    $field = Vtiger_Field::getInstance($fieldName, $module);
    if ($field) {
        echo "<p>$fieldName Field already present</p>\n";
    } else {
        $picklistOptions = [
            'E-1',
            'E-2',
            'E-3',
            'E-4',
            'E-5',
            'E-6',
            'E-7',
            'E-8',
            'E-9',
            'O-1',
            'O-2',
            'O-3',
            'O-4',
            'O-5',
            'O-6',
            'O-7',
            'O-8',
            'O-9',
            'O-10',
        ];

        $field = new Vtiger_Field();
        $field->label = 'LBL_ORDERS_'.strtoupper($fieldName);
        $field->name = $fieldName;
        $field->table = 'vtiger_orders';
        $field->column = $fieldName;
        $field->columntype = 'VARCHAR(100)';
        $field->uitype = '16';
        $field->typeofdata = 'V~O';
        $field->sequence = 2;

        $block->addField($field);
        $field->setPicklistValues($picklistOptions);

        echo "<p>Added $fieldName Field</p>\n";
    }

    //**************** ORIGIN SCAC CODE FIELD *******************//
    $fieldName = 'origin_scac_code';
    $field = Vtiger_Field::getInstance($fieldName, $module);
    if ($field) {
        echo "<p>valuation_weight Field already present</p>\n";
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_ORDERS_'.strtoupper($fieldName);
        $field->name = $fieldName;
        $field->table = 'vtiger_orders';
        $field->column = $fieldName;
        $field->columntype = 'VARCHAR(100)';
        $field->uitype = '1';
        $field->sequence = 3;
        $field->typeofdata = 'V~O';

        $block->addField($field);
        echo "<p>Added $fieldName Field</p>\n";
    }

    //**************** DESTINATION SCAC CODE FIELD *******************//
    $fieldName = 'destination_scac_code';
    $field = Vtiger_Field::getInstance($fieldName, $module);
    if ($field) {
        echo "<p>$fieldName Field already present</p>\n";
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_ORDERS_'.strtoupper($fieldName);
        $field->name = $fieldName;
        $field->table = 'vtiger_orders';
        $field->column = $fieldName;
        $field->columntype = 'VARCHAR(100)';
        $field->uitype = '1';
        $field->sequence = 4;
        $field->typeofdata = 'V~O';

        $block->addField($field);
        echo "<p>Added $fieldName Field</p>\n";
    }

    //**************** TRANSPORTATION OFFICER FIELD *******************//
    $fieldName = 'transportation_officer';
    $field = Vtiger_Field::getInstance($fieldName, $module);
    if ($field) {
        echo "<p>$fieldName Field already present</p>\n";
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_ORDERS_'.strtoupper($fieldName);
        $field->name = $fieldName;
        $field->table = 'vtiger_orders';
        $field->column = $fieldName;
        $field->columntype = 'VARCHAR(100)';
        $field->uitype = '1';
        $field->sequence = 5;
        $field->typeofdata = 'V~O';

        $block->addField($field);
        echo "<p>Added $fieldName Field</p>\n";
    }

    //**************** TO PHONE NUMBER FIELD *******************//
    $fieldName = 'to_phone_number';
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
        $field->uitype = '1';
        $field->sequence = 6;
        $field->typeofdata = 'V~O';

        $block->addField($field);
        echo "<p>Added $fieldName Field</p>\n";
    }

    //**************** ORDERING ACTIVITY INSTALLATION NAME FIELD *******************//
    $fieldName = 'ordering_installation_name';
    $field = Vtiger_Field::getInstance($fieldName, $module);
    if ($field) {
        echo "<p>$fieldName Field already present</p>\n";
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_ORDERS_'.strtoupper($fieldName);
        $field->name = $fieldName;
        $field->table = 'vtiger_orders';
        $field->column = $fieldName;
        $field->columntype = 'VARCHAR(100)';
        $field->uitype = '1';
        $field->sequence = 7;
        $field->typeofdata = 'V~O';

        $block->addField($field);
        echo "<p>Added $fieldName Field</p>\n";
    }
}

//********** REMOVE OLD orders_gblnumber FROM order details *************//
$fieldName = 'orders_gblnumber';
$field = Vtiger_Field::getInstance($fieldName, $module);
if ($field) {
    echo "<p>Removing $fieldName from order details </p>\n";

    $db = PearDatabase::getInstance();
    $sql = 'UPDATE `vtiger_field` SET presence = ? WHERE fieldid = ?';
    $db->pquery($sql, [1, $field->id]);
}

echo "<h3>Ending MoveMilesFieldToDates</h3>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";