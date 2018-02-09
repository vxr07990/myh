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



$Vtiger_Utils_Log = true;

require_once 'vtlib/Vtiger/Menu.php';
require_once 'vtlib/Vtiger/Module.php';
require_once 'vendor/autoload.php';

echo '<h1>Adding Exchange Sync Schema</h1>', PHP_EOL;
echo '<ul>', PHP_EOL;

if (!Vtiger_Utils::CheckTable('calendar_exchange_sync')) {
    echo '<li>Creating `calendar_exchange_sync` table.', PHP_EOL;

    Vtiger_Utils::CreateTable('calendar_exchange_sync',
                              '(
                                  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                                  `user_id` int(11) unsigned NOT NULL,
                                  `state` varchar(1000) DEFAULT NULL,
                                  `last_sync_time` datetime DEFAULT NULL,
                                  `created_at` datetime DEFAULT NULL,
                                  `updated_at` datetime DEFAULT NULL,
                                  PRIMARY KEY (`id`)
							  )', true);
}

// -----

if (!Vtiger_Utils::CheckTable('calendar_exchange_metadata')) {
    echo '<li>Creating `calendar_exchange_metadata` table.', PHP_EOL;

    Vtiger_Utils::CreateTable('calendar_exchange_metadata',
                              '(
                                  `id` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT "",
                                  `activity_id` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
                                  `change_key` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
                                  UNIQUE KEY `id` (`id`)
							   )', true);
}

echo '</ul>', PHP_EOL;


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";