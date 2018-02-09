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


$MODULENAME = 'VTEFavorite';

$moduleInstance = Vtiger_Module::getInstance($MODULENAME);
if (!$moduleInstance) {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = $MODULENAME;
    $moduleInstance->save();
}

// create table module
//1 : vte_favorite_records
$sql = "CREATE TABLE IF NOT EXISTS `vte_favorite_records` (
					  `id` int(19) NOT NULL AUTO_INCREMENT,
					  `userid` varchar(100) DEFAULT NULL,
					  `module` varchar(100) NOT NULL,
					  `view` varchar(100) NOT NULL,
					  `record` int(19) DEFAULT NULL,
					  `url` varchar(250) NOT NULL,
					  `stars` int(19) DEFAULT NULL,
					  `recordname` varchar(250) DEFAULT NULL,
					  `update` datetime DEFAULT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8;";
$adb->pquery($sql,array());


//2 : vte_favorite_config_module
$sql = "CREATE TABLE IF NOT EXISTS `vte_favorite_config_module` (
					  `id` int(19) NOT NULL AUTO_INCREMENT,
					  `userid` varchar(100) DEFAULT NULL,
					  `module` varchar(100) NOT NULL,
					   `fields` varchar(500) NULL,
					   `limitrecord` int(19) NULL,
					  `order` int(19) DEFAULT NULL,
					  `active` int(19) DEFAULT 1,
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8;";
$adb->pquery($sql,array());

//3 : vte_recently_records
$sql = "CREATE TABLE IF NOT EXISTS `vte_recently_records` (
					  `id` int(19) NOT NULL AUTO_INCREMENT,
					  `userid` varchar(100) DEFAULT NULL,
					  `module` varchar(100) NOT NULL,
					  `view` varchar(100) NOT NULL,
					  `record` int(19) DEFAULT NULL,
					  `url` varchar(250) NOT NULL,
					  `stars` int(19) DEFAULT NULL,
					  `recordname` varchar(250) DEFAULT NULL,
					  `update` datetime DEFAULT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8;";
$adb->query($sql);

//4 : vte_recently_config_module
$sql = "CREATE TABLE IF NOT EXISTS `vte_recently_config_module` (
					  `id` int(19) NOT NULL AUTO_INCREMENT,
					  `userid` varchar(100) DEFAULT NULL,
					  `module` varchar(100) NOT NULL,
					   `fields` varchar(500) NULL,
					   `limitrecord` int(19)  NULL,
					  `order` int(19) DEFAULT NULL,
					  `active` int(19) DEFAULT 1,
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8;";
$adb->query($sql);

//5 : vte_customlist_config_module
$sql = "CREATE TABLE IF NOT EXISTS `vte_customlist_config_module` (
					  `id` int(19) NOT NULL AUTO_INCREMENT,
					  `userid` varchar(100) DEFAULT NULL,
					  `module` varchar(100) NOT NULL,
					  `cvid` int(19) NULL,
					   `cvname` varchar(500) NULL,
					   `fields` varchar(500) NULL,
					   `limitrecord` int(19)  NULL,
					  `order` int(19) DEFAULT NULL,
					  `active` int(19) DEFAULT 1,
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8;";
$adb->query($sql);

$adb->pquery("DELETE FROM `vte_modules` WHERE module=?;", array('VTEFavorite'));
$adb->pquery("INSERT INTO `vte_modules` (`module`, `valid`) VALUES (?, ?);", array('VTEFavorite', '1'));

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";