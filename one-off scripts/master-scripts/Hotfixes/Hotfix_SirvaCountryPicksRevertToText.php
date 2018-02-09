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


include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

echo "<br>begin hotfix sirva change country picklists back to text (opps/leads)<br>";

Vtiger_Utils::ExecuteQuery(
    "UPDATE `vtiger_field` SET uitype = 1 
    WHERE fieldname IN ('destination_country', 'origin_country') AND uitype = 16 
    AND tablename IN ('vtiger_potential', 'vtiger_leadscf')"
);

echo "<br>begin hotfix sirva change country picklists back to text (opps/leads)<br>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";