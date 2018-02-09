<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 11/10/2016
 * Time: 4:42 PM
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

$db = &PearDatabase::getInstance();

$module = Vtiger_Module::getInstance('VehicleTransportation');

if (!$module) {
    return;
}

$field = Vtiger_Field::getInstance('vehicletrans_oversized', $module);

if (!$field) {
    return;
}

$db->pquery('UPDATE vtiger_field SET uitype=? WHERE fieldid=?', ['16', $field->id]);

$db->pquery('ALTER TABLE '.$module->basetable.' MODIFY COLUMN '.$field->column.' VARCHAR(50)');

$db->pquery('TRUNCATE TABLE vtiger_vehicletrans_oversized');

$field->setPicklistValues(['No', 'Yes']);

$block = Vtiger_Block::getInstance('LBL_VEHICLETRANSPORTATION_INFORMATION', $module);
if (!$block) {
    return;
}

$field = Vtiger_Field::getInstance('vehicletrans_carriertype', $module);
if ($field) {
    echo "The vehicletrans_carriertype field already exists<br>\n";
} else {
    $field             = new Vtiger_Field();
    $field->label      = 'LBL_VEHICLETRANSPORTATION_CARRIERTYPE';
    $field->name       = 'vehicletrans_carriertype';
    $field->table      = 'vtiger_vehicletransportation';
    $field->column     = 'vehicletrans_carriertype';
    $field->columntype = 'VARCHAR(50)';
    $field->uitype     = 16;
    $field->typeofdata = 'V~O';
    $block->addField($field);
    $field->setPicklistValues(['Open', 'Enclosed']);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";