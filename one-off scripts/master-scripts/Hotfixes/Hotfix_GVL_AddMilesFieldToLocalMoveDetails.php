<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 11/10/2016
 * Time: 1:52 PM
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

$moduleNames = ['Estimates', 'Actuals'];
foreach ($moduleNames as $moduleName) {
    $module = Vtiger_Module::getInstance($moduleName);
    if (!$module) {
        continue;
    }

    $block = Vtiger_Block::getInstance('LBL_QUOTES_LOCALMOVEDETAILS', $module);

    if (!$block) {
        continue;
    }

    $field = Vtiger_Field::getInstance('localmove_mileage', $module);
    if ($field) {
        echo "The localmove_mileages field already exists<br>\n";
    } else {
        $field             = new Vtiger_Field();
        $field->label      = 'LBL_ESTIMATES_LOCAL_MILES';
        $field->name       = 'localmove_mileage';
        $field->table      = 'vtiger_quotes';
        $field->column     = 'local_mileage';
        $field->columntype = 'INT(10)';
        $field->uitype     = '7';
        $field->typeofdata = 'I~O';
        $block->addField($field);
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";