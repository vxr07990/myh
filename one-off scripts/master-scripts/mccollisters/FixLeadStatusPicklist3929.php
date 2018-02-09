<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 1/23/2017
 * Time: 3:23 PM
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

if(!$module)
{
    return;
}

$field = Vtiger_Field::getInstance('leadstatus', $module);
if(!$field)
{
    return;
}

$db = &PearDatabase::getInstance();
$db->pquery('TRUNCATE TABLE vtiger_leadstatus');

$field->setPicklistValues([
    'Attempted to Contact',
    'Cold',
    'Contact in Future',
    'Contacted',
    'Hot',
    'Junk Lead',
    'Lost Lead',
    'Not Contacted',
    'Pre Qualified',
    'Qualified',
    'Warm',
                          ]);


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";