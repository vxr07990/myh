<?php
/**
 * Created by PhpStorm.
 * User: DBOlin
 * Date: 3/6/2017
 * Time: 11:39 AM
 */

if (function_exists("call_ms_function_ver")) {
    $version = 2;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

if(getenv('IGC_MOVEHQ'))
{
    return;
}

$db = &PearDatabase::getInstance();

$module = Vtiger_Module::getInstance('Cubesheets');

if($module)
{
    $field = Vtiger_Field::getInstance('cubesheets_orderid', $module);
    if($field)
    {
        $db->pquery('UPDATE vtiger_field SET presence=? WHERE fieldid=?',
                    [1,$field->id]);
    }
}

$module = Vtiger_Module::getInstance('Surveys');

if($module)
{
    $field = Vtiger_Field::getInstance('order_id', $module);
    if($field)
    {
        $db->pquery('UPDATE vtiger_field SET presence=? WHERE fieldid=?',
                    [1,$field->id]);
    }
}









print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";