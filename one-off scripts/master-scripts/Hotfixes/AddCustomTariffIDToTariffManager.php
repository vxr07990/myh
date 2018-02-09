<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 3/20/2017
 * Time: 10:34 AM
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

$blockName  = 'LBL_TARIFFMANAGER_ADMINISTRATIVE';
$moduleName = 'TariffManager';
$module     = Vtiger_Module::getInstance($moduleName);
if (!$module) {
    echo "<h2>FAILED TO LOAD Module: $moduleName </h2><br />";
} else {
    $block = Vtiger_Block::getInstance($blockName, $module);
    if ($block) {
        $field = Vtiger_Field::getInstance('custom_tariff_id', $module);
        if ($field) {
            echo "The custom_tariff_id field already exists<br>\n";
        } else {
            $field             = new Vtiger_Field();
            $field->label      = 'LBL_CUSTOM_TARIFF_ID';
            $field->name       = 'custom_tariff_id';
            $field->table      = 'vtiger_tariffmanager';
            $field->column     = 'custom_tariff_id';
            $field->columntype = 'VARCHAR(100)';
            $field->uitype     = 2;
            $field->typeofdata = 'V~O';
            $block->addField($field);
        }
        print "<h2>finished add fields to $moduleName module. </h2>\n";
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";