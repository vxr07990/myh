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
vimport ('includes.runtime.EntryPoint');
$Vtiger_Utils_Log = true;

global $adb;

Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_ws_fieldtype` (`uitype`, `fieldtype`) VALUES ('555', 'datezone');");
Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_ws_fieldtype` (`uitype`, `fieldtype`) VALUES ('700', 'datetimezone');");
$homeModule = Vtiger_Module::getInstance('Home');
Vtiger_Event::register($homeModule, 'vtiger.entity.aftersave', 'Vtiger_TimezoneUpdater_Handler', 'modules/Vtiger/handlers/TimezoneUpdater.php');

if(!Vtiger_Utils::CheckTable('vtiger_fieldtimezonerel')) {
	Vtiger_Utils::ExecuteQuery("CREATE TABLE `vtiger_fieldtimezonerel` (
			`crmid` int(19) NOT NULL,
			`fieldid` varchar(50) NOT NULL,
			`timezone` varchar(250),
			PRIMARY KEY (`crmid`, `fieldid`)
		);");
}


echo "COMPLETED <br>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";