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

$module = Vtiger_Module::getInstance('AgentManager');
$field = Vtiger_Field::getInstance('vanline_id', $module);

if($field) {
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET typeofdata='V~M' WHERE fieldid=".$field->id);
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
