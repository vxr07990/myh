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


$module = Vtiger_Module::getInstance('Leads');

$field1 = Vtiger_Field::getInstance('pack', $module);
if ($field1) {
    echo "Field pack exists in Leads module - removing<br />";
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET presence=1 WHERE fieldid=".$field1->id);
}

$field2 = Vtiger_Field::getInstance('pack_to', $module);
if ($field2) {
    echo "Field pack_to exists in Leads module - removing<br />";
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET presence=1 WHERE fieldid=".$field2->id);
}

$field3 = Vtiger_Field::getInstance('preferred_ppdate', $module);
if ($field3) {
    echo "Field preferred_ppdate exists in Leads module - removing<br />";
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET presence=1 WHERE fieldid=".$field3->id);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";