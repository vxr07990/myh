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
 * Date: 9/15/2016
 * Time: 2:49 PM
 */

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$moduleName = 'MoveRoles';
$module = Vtiger_Module::getInstance($moduleName);

if (!$module) {
    return;
}

$block = Vtiger_Block::getInstance('LBL_MOVEROLES_INFORMATION', $module);
if (!$block) {
    return;
}

$field = Vtiger_Field::getInstance('sales_commission', $module);
if ($field) {
    echo "The sales_commission field already exists<br>\n";
} else {
    $field             = new Vtiger_Field();
    $field->label      = 'LBL_MOVEROLES_COMMISSION';
    $field->name       = 'sales_commission';
    $field->table      = 'vtiger_moveroles';
    $field->column     = 'sales_commission';
    $field->columntype = 'DECIMAL(12,2)';
    $field->uitype     = 9;
    $field->typeofdata = 'N~O';
    $block->addField($field);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";