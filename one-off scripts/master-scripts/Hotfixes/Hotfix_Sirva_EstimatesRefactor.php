<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 12/27/2016
 * Time: 10:50 AM
 */


if (function_exists("call_ms_function_ver")) {
    $version = 3;
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
$moduleNames = ['Estimates'];

foreach($moduleNames as $moduleName) {
    $module = Vtiger_Module::getInstance($moduleName);
    if (!$module) {
        continue;
    }
}

$module = Vtiger_Module::getInstance('Cubesheets');

if($module)
{
    $field = Vtiger_Field::getInstance('cubesheets_orderid', $module);
    if($field)
    {
        $db->pquery('UPDATE vtiger_field SET presence=? WHERE fieldid=?', [1, $field->id]);
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";