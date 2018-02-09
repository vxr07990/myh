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



require_once 'vtlib/Vtiger/Module.php';

$db = PearDatabase::getInstance();

//Remove related list for Surveys to contacts
Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_relatedlists` WHERE `tabid` = (SELECT `tabid` FROM `vtiger_tab` WHERE `name` = 'Accounts') AND `related_tabid` = (SELECT `tabid` FROM `vtiger_tab` WHERE `name` = 'Cubesheets')");

//Make surveys viewable, but remove add ability
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_relatedlists` SET `actions` = '' WHERE `vtiger_relatedlists`.`tabid` = (SELECT `tabid` FROM `vtiger_tab` WHERE `name` = 'Contacts') AND `related_tabid` = (SELECT `tabid` FROM `vtiger_tab` WHERE `name` = 'Cubesheets')");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";