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
echo "<br><h1>Starting Hotfix Reorder Accounts Blocks</h1><br>\n";

$accountsModule = Vtiger_Module::getInstance('Accounts');

$tabId =  $accountsModule->getId();

echo "<p>Reordering Blocks with a tab id of $tabId</p>\n";
$blockOrder = ['LBL_ACCOUNT_INFORMATION', 'LBL_ACCOUNT_DETAILS', 'LBL_ACCOUNT_CREDIT_REQUEST', 'LBL_ACCOUNT_CREDIT_DETAILS', 'LBL_ACCOUNT_BILLING_ADDRESS', '	
LBL_ACCOUNT_INVOICESETTINGS', 'LBL_ACCOUNT_SALESPERSONS', 'LBL_ACCOUNTS_ADDITIONAL_ROLES'];

$count=0;
$db = PearDatabase::getInstance();
foreach ($blockOrder as $block) {
    $currentBlock = Vtiger_Block::getInstance($block, $accountsModule);
    if ($currentBlock) {
        $count++;
        $params = [$count, $currentBlock->id];
        $sql = 'UPDATE `vtiger_blocks` SET sequence = ? WHERE blockid = ?';
        $db->pquery($sql, $params);
        echo "<p>Updated $block to sequence $currentBlock->id</p>\n";
    }
}

echo "<br><h1>Finished Hotfix Reorder Accounts Blocks</h1><br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";