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
 * Date: 10/18/2016
 * Time: 12:49 PM
 */

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$modules = ['Estimates', 'Actuals'];

foreach ($modules as $moduleName) {
    $module = Vtiger_Module::getInstance($moduleName);
    if (!$module) {
        continue;
    }
    $block = Vtiger_Block::getInstance('LBL_QUOTES_SITDETAILS', $module);
    if (!$block) {
        return;
    }
    $field = Vtiger_Field::getInstance('sit_origin_auth_no', $module);
    if ($field) {
        echo "The sit_origin_auth_no field already exists<br>\n";
    } else {
        $field             = new Vtiger_Field();
        $field->label      = 'LBL_QUOTES_SIT_ORIGIN_AUTHNO';
        $field->name       = 'sit_origin_auth_no';
        $field->table      = 'vtiger_quotes';
        $field->column     = 'sit_origin_auth_no';
        $field->columntype = 'VARCHAR(50)';
        $field->uitype     = 1;
        $field->typeofdata = 'V~O';
        $block->addField($field);
    }
    $field = Vtiger_Field::getInstance('sit_dest_auth_no', $module);
    if ($field) {
        echo "The sit_origin_auth_no field already exists<br>\n";
    } else {
        $field             = new Vtiger_Field();
        $field->label      = 'LBL_QUOTES_SIT_DEST_AUTHNO';
        $field->name       = 'sit_dest_auth_no';
        $field->table      = 'vtiger_quotes';
        $field->column     = 'sit_dest_auth_no';
        $field->columntype = 'VARCHAR(50)';
        $field->uitype     = 1;
        $field->typeofdata = 'V~O';
        $block->addField($field);
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";