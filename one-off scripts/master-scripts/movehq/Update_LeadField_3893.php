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
$leadTabId = getTabid('Leads');
$adb->pquery("UPDATE vtiger_field SET sequence = 1 WHERE fieldname = 'business_line2' AND tabid = ?",[$leadTabId]);
$adb->pquery("UPDATE vtiger_field SET sequence = 2 WHERE fieldname = 'agentid' AND tabid = ?",[$leadTabId]);
$adb->pquery("UPDATE vtiger_field SET sequence = 3 WHERE fieldname = 'leadstatus' AND tabid = ?",[$leadTabId]);
$adb->pquery("UPDATE vtiger_field SET sequence = 4 WHERE fieldname = 'reason_cancelled' AND tabid = ?",[$leadTabId]);
$adb->pquery("UPDATE vtiger_field SET sequence = 5 WHERE fieldname = 'related_account' AND tabid = ?",[$leadTabId]);
$adb->pquery("UPDATE vtiger_field SET sequence = 6 WHERE fieldname = 'leadsource' AND tabid = ?",[$leadTabId]);


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";