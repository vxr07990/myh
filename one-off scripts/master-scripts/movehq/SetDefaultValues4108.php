<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 3/24/2017
 * Time: 4:13 PM
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

$module = Vtiger_Module::getInstance('Leads');
if($module)
{
    $field = Vtiger_Field::getInstance('leadstatus', $module);
    if($field)
    {
        $db->pquery('UPDATE vtiger_field SET defaultvalue=? WHERE fieldid=?',
                    ['New', $field->id]);
    }
}

$module = Vtiger_Module::getInstance('Opportunities');
if($module)
{
    $field = Vtiger_Field::getInstance('opportunitystatus', $module);
    if($field)
    {
        $db->pquery('UPDATE vtiger_field SET defaultvalue=? WHERE fieldid=?',
                    ['New', $field->id]);
    }
}

$module = Vtiger_Module::getInstance('Orders');
if($module)
{
    $field = Vtiger_Field::getInstance('ordersstatus', $module);
    if($field)
    {
        $db->pquery('UPDATE vtiger_field SET defaultvalue=? WHERE fieldid=?',
                    ['Initial Order', $field->id]);
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";