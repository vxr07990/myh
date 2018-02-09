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


/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 9/27/2016
 * Time: 3:54 PM
 */

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

// this is impossible to do properly in the time we have, so I'm just going to add
// the needed fields to the Extra stops module
// Shuttle: weight, applied (yes/no), overtime (yes/no), miles
// Self stg: weight, applied (yes/no) overtime (yes/no)

$module = Vtiger_Module::getInstance('ExtraStops');
if (!$module) {
    return;
}

$block = Vtiger_Block::getInstance('LBL_EXTRASTOPS_INFORMATION', $module);

if (!$block) {
    return;
}


$field = Vtiger_Field::getInstance('acc_svc_shuttle_weight', $module);
if ($field) {
    echo "The acc_svc_shuttle_weight field already exists<br>\n";
} else {
    $field             = new Vtiger_Field();
    $field->label      = 'LBL_EXTRASTOPS_ACC_SVC_SHUTTLE_WEIGHT';
    $field->name       = 'acc_svc_shuttle_weight';
    $field->table      = 'vtiger_extrastops';
    $field->column     = 'acc_svc_shuttle_weight';
    $field->columntype = 'INT(10)';
    $field->uitype     = 7;
    $field->typeofdata = 'V~O';
    $block->addField($field);
}

$field = Vtiger_Field::getInstance('acc_svc_shuttle_applied', $module);
if ($field) {
    echo "The acc_svc_shuttle_applied field already exists<br>\n";
} else {
    $field             = new Vtiger_Field();
    $field->label      = 'LBL_EXTRASTOPS_ACC_SVC_SHUTTLE_APPLIED';
    $field->name       = 'acc_svc_shuttle_applied';
    $field->table      = 'vtiger_extrastops';
    $field->column     = 'acc_svc_shuttle_applied';
    $field->columntype = 'VARCHAR(3)';
    $field->uitype     = 56;
    $field->typeofdata = 'V~O';
    $block->addField($field);
}

$field = Vtiger_Field::getInstance('acc_svc_shuttle_overtime', $module);
if ($field) {
    echo "The acc_svc_shuttle_overtime field already exists<br>\n";
} else {
    $field             = new Vtiger_Field();
    $field->label      = 'LBL_EXTRASTOPS_ACC_SVC_SHUTTLE_OVERTIME';
    $field->name       = 'acc_svc_shuttle_overtime';
    $field->table      = 'vtiger_extrastops';
    $field->column     = 'acc_svc_shuttle_overtime';
    $field->columntype = 'VARCHAR(3)';
    $field->uitype     = 56;
    $field->typeofdata = 'V~O';
    $block->addField($field);
}

$field = Vtiger_Field::getInstance('acc_svc_shuttle_miles', $module);
if ($field) {
    echo "The acc_svc_shuttle_miles field already exists<br>\n";
} else {
    $field             = new Vtiger_Field();
    $field->label      = 'LBL_EXTRASTOPS_ACC_SVC_SHUTTLE_MILES';
    $field->name       = 'acc_svc_shuttle_miles';
    $field->table      = 'vtiger_extrastops';
    $field->column     = 'acc_svc_shuttle_miles';
    $field->columntype = 'INT(10)';
    $field->uitype     = 7;
    $field->typeofdata = 'V~O';
    $block->addField($field);
}

$field = Vtiger_Field::getInstance('acc_svc_selfstg_weight', $module);
if ($field) {
    echo "The acc_svc_selfstg_weight field already exists<br>\n";
} else {
    $field             = new Vtiger_Field();
    $field->label      = 'LBL_EXTRASTOPS_ACC_SVC_SELFSTG_WEIGHT';
    $field->name       = 'acc_svc_selfstg_weight';
    $field->table      = 'vtiger_extrastops';
    $field->column     = 'acc_svc_selfstg_weight';
    $field->columntype = 'INT(10)';
    $field->uitype     = 7;
    $field->typeofdata = 'V~O';
    $block->addField($field);
}

$field = Vtiger_Field::getInstance('acc_svc_selfstg_applied', $module);
if ($field) {
    echo "The acc_svc_selfstg_applied field already exists<br>\n";
} else {
    $field             = new Vtiger_Field();
    $field->label      = 'LBL_EXTRASTOPS_ACC_SVC_SELFSTG_APPLIED';
    $field->name       = 'acc_svc_selfstg_applied';
    $field->table      = 'vtiger_extrastops';
    $field->column     = 'acc_svc_selfstg_applied';
    $field->columntype = 'VARCHAR(3)';
    $field->uitype     = 56;
    $field->typeofdata = 'V~O';
    $block->addField($field);
}

$field = Vtiger_Field::getInstance('acc_svc_selfstg_overtime', $module);
if ($field) {
    echo "The acc_svc_selfstg_overtime field already exists<br>\n";
} else {
    $field             = new Vtiger_Field();
    $field->label      = 'LBL_EXTRASTOPS_ACC_SVC_SELFSTG_OVERTIME';
    $field->name       = 'acc_svc_selfstg_overtime';
    $field->table      = 'vtiger_extrastops';
    $field->column     = 'acc_svc_selfstg_overtime';
    $field->columntype = 'VARCHAR(3)';
    $field->uitype     = 56;
    $field->typeofdata = 'V~O';
    $block->addField($field);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";