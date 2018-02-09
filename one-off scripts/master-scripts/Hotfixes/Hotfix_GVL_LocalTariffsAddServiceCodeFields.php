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
 * Date: 10/13/2016
 * Time: 3:27 PM
 */

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$module = Vtiger_Module::getInstance('TariffServices');

if (!$module) {
    return;
}

$block = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_INFORMATION', $module);

if (!$block) {
    return;
}

$field = Vtiger_Field::getInstance('service_code', $module);
if (!$field) {
    $field             = new Vtiger_Field();
    $field->label      = 'LBL_TARIFFSERVICES_SERVICELINE';
    $field->name       = 'service_line';
    $field->table      = 'vtiger_tariffservices';  // This is the tablename from your database that the new field will be added to.
    $field->column     = 'service_line';   //  This will be the columnname in your database for the new field.
    $field->columntype = 'VARCHAR(100)';
    $field->uitype     = 16; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field->typeofdata = 'V~M'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData
    $block->addField($field);
    $field->setPicklistValues(['HHG', 'WPS']);
}

$field = Vtiger_Field::getInstance('service_code', $module);
if (!$field) {
    $field             = new Vtiger_Field();
    $field->label      = 'LBL_TARIFFSERVICES_SERVICECODE';
    $field->name       = 'service_code';
    $field->table      = 'vtiger_tariffservices';  // This is the tablename from your database that the new field will be added to.
    $field->column     = 'service_code';   //  This will be the columnname in your database for the new field.
    $field->columntype = 'VARCHAR(100)';
    $field->uitype     = 16; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field->typeofdata = 'V~M'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData
    $block->addField($field);
    // Will be dynamically set based on service line
    $field->setPicklistValues([]);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";