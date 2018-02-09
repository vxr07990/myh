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


$module = Vtiger_Module::getInstance('HelpDesk');
$field = Vtiger_Field::getInstance('product_id', $module);
echo "<h1>Removing ".$field->name." with fieldid = ".$field->id . "</h1>";
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET presence = 1 WHERE fieldid = '.$field->id);


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";