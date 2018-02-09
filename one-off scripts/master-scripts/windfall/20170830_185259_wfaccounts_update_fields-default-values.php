<?php
if (!checkIsWindfallActive()) {
    return;
}
if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: ".__FILE__."<br />\n\e[0m";

        return;
    }
}

print "\e[32mRUNNING: ".__FILE__."<br />\n\e[0m";
$Vtiger_Utils_Log = true;
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';
require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET `defaultvalue` = 1 WHERE `fieldname` = 'download_to_device' AND `tablename` = 'vtiger_wfaccounts'");

Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET `defaultvalue` = 'Approved' WHERE `fieldname` = 'account_status' AND `tablename` = 'vtiger_wfaccounts'");
