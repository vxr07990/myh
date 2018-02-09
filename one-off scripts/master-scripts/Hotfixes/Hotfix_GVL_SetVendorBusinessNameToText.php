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
 * Date: 9/27/2016
 * Time: 12:49 PM
 */

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$moduleInstance = Vtiger_Module::getInstance('Vendors');

if (!$moduleInstance) {
    return;
}

$field = Vtiger_Field::getInstance('vendors_business_name', $moduleInstance);

if (!$field) {
    return;
}

$db = PearDatabase::getInstance();
$db->pquery('UPDATE `vtiger_field` SET uitype = 1 WHERE fieldid = ' . $field->id);


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";