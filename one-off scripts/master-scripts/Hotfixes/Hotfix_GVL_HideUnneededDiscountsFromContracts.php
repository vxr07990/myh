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
 * Time: 3:27 PM
 */

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$module = Vtiger_Module::getInstance('Contracts');
if (!$module) {
    return;
}

$db = &PearDatabase::getInstance();

$fields = [
    'discount_type',
    'peak_discount',
    'non_peak_discount',
    'variable_discount_from',
    'variable_discount_to',
];

foreach ($fields as $fieldName) {
    $field = Vtiger_Field::getInstance($fieldName, $module);
    if (!$field) {
        continue;
    }
    $db->pquery('UPDATE `vtiger_field` SET presence=1 where fieldid=?', [$field->id]);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";