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
$Vtiger_Utils_Log = true;
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'modules/ModTracker/ModTracker.php';
include_once 'modules/ModComments/ModComments.php';
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
include_once 'modules/Users/Users.php';
//*/
echo "<br><h1>Starting </h1><br>\n";

$module = Vtiger_Module::getInstance('Leads');

$block = Vtiger_Block::getInstance('LBL_LEADS_INFORMATION', $module);
$field1 = Vtiger_Field::getInstance('lead_type', $module);
if ($block) {
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET `typeofdata` = 'V~M' WHERE `fieldid` = ".$field1->id." AND `block`
 = ".$block->id);
}
echo 'Lead type field is now required';
echo "<br><h1>Finished </h1><br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";