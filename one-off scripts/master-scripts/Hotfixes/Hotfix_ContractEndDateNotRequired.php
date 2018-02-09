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
echo '<h1>Starting Hotfix to Turn Off Contract End Date</h1><br>';

$moduleContracts = Vtiger_Module::getInstance('Contracts');
$field = Vtiger_Field::getInstance('end_date', $moduleContracts);

if ($field) {
    $db = PearDatabase::getInstance();

    $sql = "UPDATE `vtiger_field` SET typeofdata = 'D~O' WHERE columnname = 'end_date' AND tablename = 'vtiger_contracts' LIMIT 1";
    $result = $db->pquery($sql, []);

    echo '<p>The end_date field was set to not required</p>';
} else {
    echo '<p>The end_date was not found</p>';
}
echo '<h1>Ending Hotfix to Turn Off Contract End Date</h1><br>';


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";