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

global $adb;

$Vtiger_Utils_Log = true;
$isNew = false;

$moduleInstance = Vtiger_Module::getInstance('QuotingTool');

if ($moduleInstance) {
    echo "<h2>Document Designer module already exists</h2><br>";
} else {
    echo "<h2>Create Document Designer module</h2><br>";

    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'QuotingTool';
    $moduleInstance->label = 'Quoting Tool';
    $moduleInstance->parent = 'Tools';
    $moduleInstance->isentitytype = false;
    $moduleInstance->version = '1.0';
    $moduleInstance->save();

    // Create quotingtool tables
    // Create vtiger_quotingtool table
    if (!Vtiger_Utils::CheckTable('vtiger_quotingtool')) {
        $stmt = "CREATE TABLE IF NOT EXISTS `vtiger_quotingtool` (
						  `id` int(19) NOT NULL AUTO_INCREMENT,
						  `filename` varchar(100) NOT NULL,
						  `module` varchar(255) NOT NULL,
						  `body` longtext NOT NULL,
						  `header` text,
						  `content` longtext,
						  `footer` text,
						  `anwidget` tinyint(3) DEFAULT '0',
						  `description` text,
						  `deleted` int(1) NOT NULL DEFAULT '0',
						  `created` datetime NOT NULL,
						  `updated` datetime NOT NULL,
						  `email_subject` varchar(255) DEFAULT NULL,
						  `email_content` text,
						  `mapping_fields` text,
						  `attachments` text,
						  PRIMARY KEY (`id`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
        $adb->pquery($stmt);
        echo "Created table vtiger_quotingtool success <br>";
    } else {
        echo 'Table vtiger_quotingtool already exists' . PHP_EOL . '<br>';
    }

    // Create vtiger_quotingtool_transactions table
    if (!Vtiger_Utils::CheckTable('vtiger_quotingtool_transactions')) {
        $stmt = "CREATE TABLE IF NOT EXISTS `vtiger_quotingtool_transactions` (
						  `id` int(19) unsigned NOT NULL AUTO_INCREMENT,
						  `template_id` int(19) unsigned NOT NULL,
						  `module` varchar(255) NOT NULL,
						  `record_id` int(19) unsigned NOT NULL,
						  `signature` text,
						  `signature_name` varchar(255) DEFAULT NULL,
						  `full_content` longtext,
						  `description` text,
						  `deleted` tinyint(1) NOT NULL DEFAULT '0',
						  `created` datetime NOT NULL,
						  `updated` datetime NOT NULL,
						  `status` tinyint(1) NOT NULL DEFAULT '0',
						  PRIMARY KEY (`id`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
        $adb->pquery($stmt);
        echo "Created table vtiger_quotingtool_transactions success <br>";
    } else {
        echo 'Table vtiger_quotingtool_transactions already exists' . PHP_EOL . '<br>';
    }

    // Create vtiger_quotingtool_settings table
    if (!Vtiger_Utils::CheckTable('vtiger_quotingtool_settings')) {
        $stmt = "CREATE TABLE IF NOT EXISTS `vtiger_quotingtool_settings` (
						  `id` int(19) unsigned NOT NULL AUTO_INCREMENT,
						  `template_id` int(19) unsigned NOT NULL,
						  `created` datetime NOT NULL,
						  `updated` datetime NOT NULL,
						  `description` text,
						  `label_decline` varchar(255) DEFAULT NULL,
						  `label_accept` varchar(255) DEFAULT NULL,
						  `background` text,
						  PRIMARY KEY (`id`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
        $adb->pquery($stmt);
        echo "Created table vtiger_quotingtool_settings success <br>";
    } else {
        echo 'Table vtiger_quotingtool_settings already exists' . PHP_EOL . '<br>';
    }

    // Create vtiger_quotingtool_histories table
    if (!Vtiger_Utils::CheckTable('vtiger_quotingtool_histories')) {
        $stmt = "CREATE TABLE IF NOT EXISTS `vtiger_quotingtool_histories` (
						  `id` int(19) unsigned NOT NULL AUTO_INCREMENT COMMENT '// PK',
						  `created` datetime NOT NULL,
						  `updated` datetime NOT NULL,
						  `deleted` tinyint(1) NOT NULL DEFAULT '0',
						  `template_id` int(19) unsigned NOT NULL DEFAULT '0' COMMENT '// FK - with quotingtool table',
						  `body` longtext,
						  PRIMARY KEY (`id`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $adb->pquery($stmt);
        echo "Created table vtiger_quotingtool_histories success <br>";
    } else {
        echo 'Table vtiger_quotingtool_histories already exists' . PHP_EOL . '<br>';
    }

    $moduleInstance->initWebservice();

    require_once "modules/QuotingTool/QuotingTool.php";
    $quotingTool = new QuotingTool();
    QuotingTool::addWidgetTo($moduleInstance->name);
    $quotingTool->installWorkflows($modulename);

    // Create vte_modules table
    if (!Vtiger_Utils::CheckTable('vte_modules')) {
        $stmt = 'CREATE TABLE `vte_modules` (
                `module`  varchar(50) NOT NULL ,
                `valid`  int(1) NULL ,
                PRIMARY KEY (`module`));';
        $adb->pquery($stmt);
        echo "Created table vte_modules success <br>";
    } else {
        echo 'Table vte_modules already exists' . PHP_EOL . '<br>';
    }

    $adb->pquery("DELETE FROM `vte_modules` WHERE module=?;", array('QuotingTool'));
    $adb->pquery("INSERT INTO `vte_modules` (`module`, `valid`) VALUES (?, ?);", array('QuotingTool', '1'));

    echo '<br>Done - Create Document Designer module<br><br>';
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";