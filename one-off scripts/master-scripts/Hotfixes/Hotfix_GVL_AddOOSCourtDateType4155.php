<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 2/7/2017
 * Time: 10:49 AM
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

$res = $db->pquery('SELECT * FROM vtiger_outofservice_type WHERE outofservice_type=?',
                   ['Court Date']);
if($res && $db->num_rows($res) == 0)
{
    $module = Vtiger_Module::getInstance('OutOfService');
    if($module) {
        $field = Vtiger_Field::getInstance('outofservice_type', $module);
        if($field)
        {
            $field->setPicklistValues(['Court Date']);
            $db->pquery('UPDATE vtiger_outofservice_type SET sortorderid = ((SELECT s1 FROM (SELECT MAX(sortorderid) AS s1 FROM vtiger_outofservice_type) AS s2) + 1) WHERE outofservice_type=?'
                ,['Court Date']);
        }
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";