<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 2/3/2017
 * Time: 8:53 AM
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

$module = Vtiger_Module::getInstance('VehicleOutofService');

if(!$module)
{
    return;
}

$field = Vtiger_Field::getInstance('outofservice_status', $module);
if(!$field)
{
    return;
}

$db = &PearDatabase::getInstance();
$db->pquery('TRUNCATE TABLE vtiger_outofservice_status');

$field->setPicklistValues([
                              'Out of Service',
                              'On Notice',
                          ]);


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";