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
 * Date: 10/18/2016
 * Time: 9:04 AM
 */

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$db = &PearDatabase::getInstance();

$modules = ['Actuals', 'Estimates'];
$fields = ['acc_debris_reg', 'acc_debris_ot', 'acc_debris_dod'];
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
        $data = $db->pquery('SELECT tablename,columnname FROM `vtiger_field` WHERE fieldid=?', [$field->id])->fetchRow();
        if (!$data) {
            continue;
        }
        $table = $data['tablename'];
        $column = $data['columnname'];
        $db->pquery("ALTER TABLE `$table` MODIFY COLUMN `$column` VARCHAR(3) DEFAULT NULL");
        $db->pquery('UPDATE `vtiger_field` SET uitype=?, typeofdata=? WHERE fieldid=?',
                    [56, 'V~O', $field->id]);
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";