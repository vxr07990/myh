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
echo "<br><h1>Starting Removal of Business Line from account_salespersons table</h1><br>\n";

$db = PearDatabase::getInstance();

$sql = 'EXPLAIN `vtiger_account_salespersons`';
$result = $db->query($sql);

$currentFields = [];
while ($row = $result->fetchRow()) {
    $currentFields[] = $row['Field'];
}

if (!in_array('commodity', $currentFields)) {
    $sql = 'ALTER TABLE `vtiger_account_salespersons` DROP COLUMN business_line';
    $db->query($sql);
    echo "<br>Removed business line from vtiger_account_salespersons<br>\n";

    $sql = 'ALTER TABLE `vtiger_account_salespersons` ADD COLUMN commodity VARCHAR(100)';
    $db->query($sql);
    echo "<br>Added commodity from vtiger_account_salespersons<br>\n";
} else {
    echo "<br>Commodity already exisits in vtiger_account_salespersons<br>\n";
}

echo "<br><h1>Ending Removal of Business Line from account_salespersons table</h1><br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";