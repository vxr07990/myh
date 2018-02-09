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



$moduleInstance = Vtiger_Module::getInstance('Employees');

$field = Vtiger_Field::getInstance('date_oos', $moduleInstance);
if ($field) {
    $field->delete();
}

$field = Vtiger_Field::getInstance('date_reinstated', $moduleInstance);
if ($field) {
    $field->delete();
}

$field = Vtiger_Field::getInstance('oos_reason', $moduleInstance);
if ($field) {
    $field->delete();
}

$field = Vtiger_Field::getInstance('oos_comments', $moduleInstance);
if ($field) {
    $field->delete();
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";