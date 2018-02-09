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
$db = PearDatabase::getInstance();

$module = Vtiger_Module::getInstance('LeadSourceManager');

$field = Vtiger_Field::getInstance('marketing_channel', $module);

if ($field && $field->uitype != 16) {
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET `uitype` = 16 WHERE `fieldname` = 'marketing_channel' AND `tablename` = 'vtiger_leadsourcemanager'");

    //New instance of field with correct UItype
    $updatedField = Vtiger_Field::getInstance('marketing_channel', $module);
    $updatedField->setPicklistValues(['Affinity', 'Interactive', 'Lead Buying', 'Other', 'Referrals', 'Telephone', 'Traditional Marketing']);

    echo 'marketing_channel updated';
} else {
    echo 'marketing_channel does not exist or is already type 16';
}

echo "<br><h1>Finished </h1><br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
