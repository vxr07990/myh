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

$moduleInstance = Vtiger_Module::getInstance('Estimates');
$fieldInstance = Vtiger_Field::getInstance('valuation_deductible', $moduleInstance);
if($fieldInstance) {
    Vtiger_Utils::ExecuteQuery("TRUNCATE TABLE `vtiger_valuation_deductible`");
    $fieldInstance->setPicklistValues(['Full Value Protection', 'Released Valuation']);
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
