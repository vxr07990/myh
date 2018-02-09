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



//*/
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');
include_once('modules/ModComments/ModComments.php');
//*/

Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_relatedlists` JOIN `vtiger_tab` ON `vtiger_relatedlists`.tabid=`vtiger_tab`.tabid SET `vtiger_relatedlists`.name='get_dependents_list' WHERE `vtiger_tab`.name='Tariffs'");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";