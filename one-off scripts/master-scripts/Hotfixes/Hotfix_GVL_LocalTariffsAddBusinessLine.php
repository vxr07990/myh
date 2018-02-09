<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 11/28/2016
 * Time: 2:22 PM
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

$moduleName = 'Tariffs';
$module = Vtiger_Module::getInstance($moduleName);

if(!$module)
{
    return;
}

$blockName = 'LBL_TARIFFS_INFORMATION';
$tableName = 'vtiger_tariffs';

$block = Vtiger_Block::getInstance($blockName, $module);
if(!$block)
{
    return;
}
$field = Vtiger_Field::getInstance('business_line', $module);
if ($field) {
    echo "The business_line field already exists<br>\n";
} else {
    $field             = new Vtiger_Field();
    $field->label      = 'LBL_TARIFF_BUSINESS_LINE';
    $field->name       = 'business_line';
    $field->table      = $tableName;
    $field->column     = 'business_line';
    $field->columntype = 'TEXT';
    $field->uitype     = 33;
    $field->typeofdata = 'V~O';
    $block->addField($field);
}



print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";