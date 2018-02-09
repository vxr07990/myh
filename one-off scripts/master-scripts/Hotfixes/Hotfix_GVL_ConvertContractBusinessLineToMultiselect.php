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
 * Time: 12:10 PM
 */


include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

echo __FILE__.PHP_EOL;

$db = &PearDatabase::getInstance();

$module = Vtiger_Module::getInstance('Contracts');

if (!$module) {
    return;
}

$field = Vtiger_Field::getInstance('business_line', $module);

if (!$field) {
    return;
}

if ($db->pquery('SELECT uitype FROM `vtiger_field` WHERE fieldid=?', [$field->id])->fetchRow()[0] != '33') {
    $stmt = 'ALTER TABLE `vtiger_contracts` MODIFY COLUMN `business_line` TEXT DEFAULT NULL';
    $db->pquery($stmt);
    $stmt = 'UPDATE `vtiger_field` SET `uitype` = ? WHERE `fieldid`=?';
    $db->pquery($stmt, [33, $field->id]);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";