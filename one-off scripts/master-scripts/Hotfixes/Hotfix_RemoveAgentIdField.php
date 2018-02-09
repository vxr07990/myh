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


/*/
$Vtiger_Utils_Log = true;
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'modules/ModTracker/ModTracker.php';
include_once 'modules/ModComments/ModComments.php';
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
include_once 'modules/Users/Users.php';
//*/
echo "<br><h1>Starting Hotfix Remove Agent Id Field</h1><br>\n";

$modulesToRemoveFrom = [
                        'ModComments',
                       ];
$db = PearDatabase::getInstance();
$tabIds = [];
foreach ($modulesToRemoveFrom as $moduleName) {
    $sql = "SELECT * FROM `vtiger_tab` WHERE name = ?";
    $result = $db->pquery($sql, [$moduleName]);
    $row = $result->fetchRow();
    if ($row) {
        $tabId = $row['tabid'];
    } else {
        echo "<br> No module found named <b>".$moduleName."</b><br>";
        continue;
    }
    Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_field` WHERE columnname='agentid' AND fieldname='agentid' AND tabid=$tabId");
}
echo "<br><h1>Finished Hotfix Remove Agent Id Field</h1><br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";