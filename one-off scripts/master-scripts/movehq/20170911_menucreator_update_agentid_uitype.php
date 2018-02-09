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

//OT5194 Menu Creator - add owner field from OT Item 4482

$db = PearDatabase::getInstance();

$module = Vtiger_Module::getInstance('MenuCreator');

$db->pquery("UPDATE vtiger_field SET uitype = 1020 WHERE tabid = ? AND columnname = ?", array($module->id, 'agentid'));

echo "Agent field updated!! <br>";