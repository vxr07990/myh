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
 * Date: 9/28/2016
 * Time: 2:29 PM
 */

echo __FILE__.PHP_EOL;

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$db = PearDatabase::getInstance();

$module = Vtiger_Module::getInstance('Accounts');

if (!$module) {
    return;
}

$field = Vtiger_Field::getInstance('transferee_contact', $module);

if (!$field) {
    return;
}

$db->pquery("UPDATE `vtiger_field` SET typeofdata='V~O' WHERE fieldid=? LIMIT 1", [$field->id]);


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";