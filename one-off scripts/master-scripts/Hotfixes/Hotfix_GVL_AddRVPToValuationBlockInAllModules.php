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
 * Date: 9/24/2016
 * Time: 10:25 AM
 */

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$newValues = [
    'Full Value Protection',
    'Vehicle Coverage',
    'Carrier Base Liability',
    'Vehicle Transport',
    'Full Replacement Value',
    'Replacement Value Protection',
];

$module = Vtiger_Module::getInstance('Actuals');

if (!$module) {
    return;
}

$field = Vtiger_Field::getInstance('valuation_deductible', $module);

if (!$field) {
    return;
}

$db = PearDatabase::getInstance();

$db->pquery('TRUNCATE TABLE `vtiger_valuation_deductible`');
// same picklist table is used in contracts, estimates, actuals, and orders, so this should update them all
$field->setPicklistValues($newValues);


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";