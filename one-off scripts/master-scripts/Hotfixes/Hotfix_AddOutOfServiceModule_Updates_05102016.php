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



/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$moduleInstance = Vtiger_Module::getInstance('OutOfService');

$field = Vtiger_Field::getInstance('outofservice_servicestatus', $moduleInstance);
if ($field) {
    $field->delete();
}

$db = PearDatabase::getInstance();
$result = $db->pquery('DELETE FROM vtiger_outofservice_status WHERE outofservice_status=?', array('Out'));
$result = $db->pquery('DELETE FROM vtiger_outofservice_status WHERE outofservice_status=?', array('Notice'));


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";