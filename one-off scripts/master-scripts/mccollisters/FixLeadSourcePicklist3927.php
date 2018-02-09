<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 1/23/2017
 * Time: 2:40 PM
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

$field = Vtiger_Field::getInstance('leadsource', $module);
if(!$field)
{
    return;
}

$db = &PearDatabase::getInstance();
$db->pquery('TRUNCATE TABLE vtiger_leadsource');

$field->setPicklistValues([
"American Car Collector",
"Cold Call",
"Conference",
"Direct Mail",
"duPont Registry",
"Employee",
"Existing Customer",
"Hagerty Classic Cars",
"Hemmings Classic Car",
"Hemmings Muscle Machines",
"Hemmings Sports & Exotic Car",
"Other",
"Magazine",
"Partner",
"Public Relations",
"Saw the truck",
"Self Generated",
"Trade Show",
"Web Site",
"Word of mouth",
"Web Form"
    ]);



print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";