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
 * Date: 10/13/2016
 * Time: 9:07 AM
 */

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');


$module = Vtiger_Module::getInstance('ExtraStops');

if (!$module) {
    return;
}

$newValues = ['Origin', 'Destination', 'Extra Pickup', 'Extra Delivery', 'Storage', 'Diversion'];
$fieldName = 'extrastops_type';
$field = Vtiger_Field::getInstance($fieldName, $module);
if (!$field) {
    return;
}
$db = PearDatabase::getInstance();
$db->pquery('TRUNCATE TABLE `vtiger_' . $fieldName . '`');
$field->setPicklistValues($newValues);


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";