<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 2/7/2017
 * Time: 11:02 AM
 */

if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";


include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$db = &PearDatabase::getInstance();


$module = Vtiger_Module::getInstance('MovingViolation');
if (!$module) {
    return;
}

$field = Vtiger_Field::getInstance('movingviolation_infosource', $module);
if (!$field) {
    return;
}
$db->pquery('UPDATE `vtiger_field` SET uitype=?, typeofdata=? WHERE fieldid=?',
            [16, 'V~O', $field->id]);

$field->setPicklistValues(["MVR","Notification of Moving Violation","Citation","Roadside Inspection"]);


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";