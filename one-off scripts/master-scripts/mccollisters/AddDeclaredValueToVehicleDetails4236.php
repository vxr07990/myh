<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 2/15/2017
 * Time: 3:39 PM
 */

if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('includes/main/WebUI.php');

$db = &PearDatabase::getInstance();

$moduleInstance = Vtiger_Module::getInstance('VehicleLookup');
if(!$moduleInstance)
{
    return;
}

$block = Vtiger_Block::getInstance('LBL_VEHICLELOOKUP_INFORMATION', $moduleInstance);
if (!$block) {
    return;
}

$field = Vtiger_Field::getInstance('vehiclelookup_declared_value', $moduleInstance);
if ($field) {
    echo "The vehiclelookup_declared_value field already exists<br>\n";
} else {
    $field             = new Vtiger_Field();
    $field->label      = 'LBL_VEHICLE_DECLARED_VALUE';
    $field->name       = 'vehiclelookup_declared_value';
    $field->table      = 'vtiger_vehiclelookup';
    $field->column     = 'vehiclelookup_declared_value';
    $field->columntype = 'DECIMAL(10,2)';
    $field->uitype     = 7;
    $field->typeofdata = 'N~O';
    $block->addField($field);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";