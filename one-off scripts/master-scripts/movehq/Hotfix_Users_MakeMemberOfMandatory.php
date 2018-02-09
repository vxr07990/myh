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

$usersModule = Vtiger_Module::getInstance('Users');
$memberOfField = Vtiger_Field::getInstance('agent_ids', $usersModule);

Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET typeofdata='V~M' WHERE fieldid=".$memberOfField->id);


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";