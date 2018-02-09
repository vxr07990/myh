<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 1/24/2017
 * Time: 8:26 AM
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

$db->pquery('UPDATE vtiger_field SET presence=? WHERE fieldname IN (?,?)',
            [1, 'business_line2', 'business_line_est2']);

$db->pquery('TRUNCATE TABLE vtiger_business_line');

$module = Vtiger_Module::getInstance('Leads');
if($module) {
    $field = Vtiger_Field::getInstance('business_line', $module);
} else {
    $field = null;
}

if($field)
{
    $field->setPicklistValues(['Auto Transportation']);
}

$db->pquery('TRUNCATE TABLE vtiger_business_line_est');

$module = Vtiger_Module::getInstance('Estimates');
if($module) {
    $field = Vtiger_Field::getInstance('business_line_est', $module);
} else {
    $field = null;
}

if($field)
{
    $field->setPicklistValues(['Auto Transportation']);
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";