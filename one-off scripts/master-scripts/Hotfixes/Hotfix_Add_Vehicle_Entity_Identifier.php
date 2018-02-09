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



$moduleInstance = Vtiger_Module::getInstance('Vehicles');
if ($moduleInstance) {
    //the field is really just bad spelled!
    $field1 = Vtiger_Field::getInstance('vechiles_unit', $moduleInstance);
    if ($field1) {
        $moduleInstance->setEntityIdentifier($field1);
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";