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

$fieldNameList =    [
                        'origin_phone1',
                        'origin_phone2',
                        'destination_phone1',
                        'destination_phone2'
                    ];

$module = Vtiger_Module::getInstance('Opportunities');
foreach($fieldNameList as $fieldName) {
    $field = Vtiger_Field::getInstance($fieldName, $module);
    if(!$field) {
        continue;
    }

    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET presence=2 WHERE fieldid=".$field->id);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";