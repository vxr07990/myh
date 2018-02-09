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

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$module = Vtiger_Module::getInstance('Orders');

if(!$module)
{
    return;
}

$field = Vtiger_Field::getInstance('projectid', $module);

if(!$field)
{
    return;
}

$db = &PearDatabase::getInstance();

$db->pquery('UPDATE vtiger_field SET presence=? WHERE fieldid=?', [1, $field->id]);




print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
