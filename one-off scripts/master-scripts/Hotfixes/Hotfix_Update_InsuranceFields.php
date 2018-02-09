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



$moduleInstance = Vtiger_Module::getInstance('Insurance');
if ($moduleInstance) {
    $field1 = Vtiger_Field::getInstance('insurance_carriername', $moduleInstance);
    if ($field1) {
        Vtiger_Utils::ExecuteQuery("UPDATE vtiger_field SET uitype=2, typeofdata='V~O' WHERE fieldname='insurance_carriername'");
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";