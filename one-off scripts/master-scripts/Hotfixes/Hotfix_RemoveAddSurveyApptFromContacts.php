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



Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_relatedlists` SET `actions`='' WHERE tabid=4 AND related_tabid=62");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";