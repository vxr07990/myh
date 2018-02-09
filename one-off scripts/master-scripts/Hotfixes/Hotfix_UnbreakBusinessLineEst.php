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


echo "<br>Unbreaking Business Line Est for Existing DBs<br>";
$quotesInstance = Vtiger_Module::getInstance('Quotes');
$field012 = Vtiger_Field::getInstance('business_line_est', $quotesInstance);
$field012->setPicklistValues(['Local Move', 'Interstate Move', 'Commercial Move']);
$moduleInstance = Vtiger_Module::getInstance('Estimates');
$field12 = Vtiger_Field::getInstance('business_line_est', $moduleInstance);
$field12->setPicklistValues(['Local Move', 'Interstate Move', 'Commercial Move']);
echo "<br>Finished Unbreaking.<br>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";