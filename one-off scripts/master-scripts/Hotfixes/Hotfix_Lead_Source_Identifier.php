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

include_once('vtlib/Vtiger/Module.php');

if (!$db) {
    $db = PearDatabase::getInstance();
}

$module = Vtiger_Module::getInstance('LeadSourceManager');

$field = Vtiger_Field::getInstance('program_name', $module);

if ($field) {
    $module->setEntityIdentifier($field);
}

Vtiger_Utils::ExecuteQuery('UPDATE vtiger_leadsourcemanager LEFT JOIN vtiger_crmentity ON crmid = leadsourcemanagerid SET label = program_name');


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";