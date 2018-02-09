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

require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');
global $adb;
$adb->pquery("UPDATE vtiger_field SET uitype = 3333 WHERE fieldname = 'business_line' AND tabid = ?",[getTabid('Accounts')]);
$sqlAlterCommodityBilling = "ALTER TABLE `vtiger_accounts_billing_addresses`
                              MODIFY COLUMN `commodity`  text NOT NULL;";
$adb->pquery($sqlAlterCommodityBilling,[]);

$sqlAlterCommodityInvoice = "ALTER TABLE `vtiger_account_invoicesettings`
                              MODIFY COLUMN `commodity`  text NOT NULL;";
$adb->pquery($sqlAlterCommodityInvoice,[]);

$sqlAlterCommodityAdditionalRole = "ALTER TABLE `vtiger_additional_roles`
                              MODIFY COLUMN `commodity`  text NOT NULL;";
$adb->pquery($sqlAlterCommodityAdditionalRole,[]);


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";