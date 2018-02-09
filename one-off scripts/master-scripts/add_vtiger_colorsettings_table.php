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



require_once 'vtlib/Vtiger/Menu.php';
require_once 'vtlib/Vtiger/Module.php';
require_once 'modules/ModTracker/ModTracker.php';
require_once 'modules/ModComments/ModComments.php';
require_once 'includes/main/WebUI.php';
require_once 'include/Webservices/Create.php';
require_once 'modules/Users/Users.php';
require_once 'vendor/autoload.php';

$db = PearDatabase::getInstance();

if (!Vtiger_Utils::CheckTable('vtiger_colorsettings')) {
    echo "<li>creating vtiger_colorsettings </li>";
    Vtiger_Utils::ExecuteQuery("CREATE TABLE `vtiger_colorsettings` (
						`id` int(11) NOT NULL,
						`value` varchar(255) NOT NULL,
						`color` varchar(8) NOT NULL DEFAULT '#FFFFFF'
					  ) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;");
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";