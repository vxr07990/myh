<?php

/**
 * OT4776: Hotfix to change the data type of the "Competitive" field
 * to a checkbox and update existing DB values.
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

$leadSourceField = Vtiger_Field::getInstance('leadsource', Vtiger_Module::getInstance('Accounts'));

if($leadSourceField) {
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET presence=1 WHERE fieldid=".$leadSourceField->id);
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
