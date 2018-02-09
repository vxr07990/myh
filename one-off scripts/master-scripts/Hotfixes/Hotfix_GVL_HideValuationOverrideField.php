<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 11/15/2016
 * Time: 11:35 AM
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

$modules = ['Contracts','Orders','Estimates','Actuals'];
$db = &PearDatabase::getInstance();

foreach ($modules as $moduleName) {
    $module = Vtiger_Module::getInstance($moduleName);
    if (!$module) {
        continue;
    }
    $field = Vtiger_Field::getInstance('valuation_discount_amount', $module);
    if (!$field) {
        continue;
    }
    $db->pquery('UPDATE vtiger_field SET presence=1 WHERE fieldid=?', [$field->id]);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";