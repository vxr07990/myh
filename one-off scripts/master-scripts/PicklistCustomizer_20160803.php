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


$picklistCustomizer = Vtiger_Module::getInstance('PicklistCustomizer');
if ($picklistCustomizer) {
    echo "<h2>PicklistCustomizer already exists, updating fields</h2><br>\n";
} else {
    $picklistCustomizer       = new Vtiger_Module();
    $picklistCustomizer->name = 'PicklistCustomizer';
    $picklistCustomizer->save();
    echo "<h2>Creating module PicklistCustomizer and updating fields</h2><br>\n";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";