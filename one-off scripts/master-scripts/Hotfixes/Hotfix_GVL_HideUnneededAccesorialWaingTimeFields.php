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
 * Date: 10/11/2016
 * Time: 4:19 PM
 */


echo __FILE__.PHP_EOL;

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$modules = ['Actuals', 'Estimates'];
$fields = ['hours_per_van', 'hours_first_man', 'additional_men', 'hours_per_additional_man'];

$db = PearDatabase::getInstance();

foreach ($modules as $moduleName) {
    $module = Vtiger_Module::getInstance($moduleName);
    if (!$module) {
        continue;
    }
    foreach ($fields as $fieldName) {
        $field = Vtiger_Field::getInstance($fieldName, $module);
        if (!$field) {
            continue;
        }
        $db->pquery('UPDATE `vtiger_field` SET presence=1 WHERE fieldid=?', [$field->id]);
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";