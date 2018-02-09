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

/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 10/21/2016
 * Time: 3:01 PM
 */


include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$module = Vtiger_Module::getInstance('ExtraStops');
if (!$module) {
    return;
}

$field = Vtiger_Field::getInstance('extrastops_type', $module);
if (!$field) {
    return;
}

$db = &PearDatabase::getInstance();

$pickList = [
    'Extra Pickup',
    'Extra Delivery',
    ];

$db->pquery('TRUNCATE TABLE `vtiger_extrastops_type`');

$field->setPicklistValues($pickList);


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";