<?php
/**
 * Created by PhpStorm.
 * User: DBOlin - HACKED BY ALF HUR HURR
 * Date: 3/9/2017
 * Time: 9:17 AM
 */

if (function_exists("call_ms_function_ver")) {
    $version = 2;//need to undo and fix
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

$module = Vtiger_Module::getInstance('Opportunities');
if(!$module)
{
    return;
}

$field = Vtiger_Field::getInstance('agrmt_cod', $module);
if($field)
{
    $db->pquery('UPDATE vtiger_field SET presence=? WHERE fieldid=?',
                [2, $field->id]);
}

$field = Vtiger_Field::getInstance('subagrmt_cod', $module);
if($field)
{
    $db->pquery('UPDATE vtiger_field SET presence=? WHERE fieldid=?',
                [2, $field->id]);
}




print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";