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
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

$Vtiger_Utils_Log = true;
global $adb;

echo "<br /> Adding vtiger_orders_cancelation_log table (Orders) <br />";
if(!Vtiger_Utils::CheckTable("vtiger_orders_cancelation_log")) {
$adb->pquery("CREATE TABLE `vtiger_orders_cancelation_log` (
  `id` int(11) NOT NULL,
  `ordersid` int(10) NOT NULL,
  `action` varchar(50) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `user` int(10) NOT NULL,
  `datetime` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;", array());
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";