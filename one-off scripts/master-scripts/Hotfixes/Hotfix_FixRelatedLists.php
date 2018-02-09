<?php
if (function_exists("call_ms_function_ver")) {
    $version = 'AlwaysRun';
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";



Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_relatedlists` SET related_tabid = 60 WHERE related_tabid = 2");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_relatedlists` SET related_tabid = 59 WHERE related_tabid = 20");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_relatedlists` SET presence=1 WHERE tabid=7 AND related_tabid=36");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_relatedlists` SET `name`='get_dependents_list' WHERE tabid=60 AND related_tabid=63");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";