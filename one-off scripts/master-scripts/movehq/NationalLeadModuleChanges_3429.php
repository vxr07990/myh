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

// 3429: National -  Lead Module Changes
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');
$Vtiger_Utils_Log = true;
//


echo "<h4>Add Sort Field and Sort Order to custom view of Leads module</h4><br>";
Vtiger_Utils::ExecuteQuery("ALTER TABLE `vtiger_customview` ADD sort_field VARCHAR(50);");
Vtiger_Utils::ExecuteQuery("ALTER TABLE `vtiger_customview` ADD sort_order VARCHAR(50);");

echo "<h4>SUCCESS</h4><br>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";