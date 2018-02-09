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
 * Date: 10/25/2016
 * Time: 12:34 PM
 */


include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$db = &PearDatabase::getInstance();

$module = Vtiger_Module::getInstance('OrdersTask');
if (!$module) {
    return;
}
$fields = ['operations_task', 'participating_agent', 'service_date_from', 'service_date_to', 'dispatch_status', 'notes_to_dispatcher'];

foreach ($fields as $fieldName) {
    $field = Vtiger_Field::getInstance($fieldName, $module);
    if (!$field) {
        continue;
    }
    $db->pquery('UPDATE vtiger_field SET summaryfield=1 WHERE fieldid=?', [$field->id]);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";