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

// create table module
$sql = "CREATE TABLE IF NOT EXISTS `vte_data_export_tracking` (
	`id` INT (11) NOT NULL AUTO_INCREMENT,
	`track_listview_exports` INT (1) NULL,
	`track_report_exports` INT (1) NULL,
	`track_scheduled_reports` INT (1) NULL,
	`track_copy_records` INT (1) NULL,
	`notification_email` VARCHAR (255) NULL,
	PRIMARY KEY (`id`)
)";

$adb->pquery($sql,array());

$sql = "CREATE TABLE IF NOT EXISTS `vte_data_export_tracking_log` (
	`id` INT (11) NOT NULL AUTO_INCREMENT,
	`type` INT (11) DEFAULT NULL,
	`time` datetime DEFAULT NULL,
	`user` INT (11) DEFAULT NULL,
	`link` VARCHAR (255) DEFAULT NULL,
	`size` DOUBLE DEFAULT NULL,
	`download` VARCHAR (255) DEFAULT NULL,
	PRIMARY KEY (`id`)
)";

$adb->pquery($sql,array());


$sql = "CREATE TABLE IF NOT EXISTS `vte_modules` (
    `module` VARCHAR (50) NOT NULL,
	`valid` INT (1) NULL,
	PRIMARY KEY (`module`)
)";

$adb->pquery($sql,array());

$MODULENAME = 'DataExportTracking';

$moduleInstance = Vtiger_Module::getInstance($MODULENAME);
if ($moduleInstance) {
    echo "Module already present - choose a different name.";
} else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = $MODULENAME;
    $moduleInstance->save();

    mkdir('modules/'.$MODULENAME);
    echo "OK\n";
}

$adb->pquery("DELETE FROM `vte_modules` WHERE module=?;",array('DataExportTracking'));
$adb->pquery("INSERT INTO `vte_modules` (`module`, `valid`) VALUES (?, ?);",array('DataExportTracking','1'));


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";