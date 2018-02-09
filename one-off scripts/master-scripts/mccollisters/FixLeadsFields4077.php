<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 1/31/2017
 * Time: 11:36 AM
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

$module = Vtiger_Module::getInstance('Leads');

$db = &PearDatabase::getInstance();

if(!$module)
{
    return;
}

$fieldNames = ['assigned_user_id','createdtime','modifiedtime','modifiedby','created_user_id'];

foreach ($fieldNames as $fieldName)
{
    $field = Vtiger_Field::getInstance($fieldName, $module);
    if(!$field)
    {
        continue;
    }

    $db->pquery('UPDATE vtiger_field SET presence=? WHERE fieldid=?',
                [0, $field->id]);
}



print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";