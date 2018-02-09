<?php
include_once('vtlib/Vtiger/Menu.php');
include_once 'includes/main/WebUI.php';

Vtiger_Utils::ExecuteQuery("ALTER TABLE `calendar_exchange_metadata` DROP INDEX `id`, ADD UNIQUE INDEX `id` (`activity_id`)");
Vtiger_Utils::ExecuteQuery("ALTER TABLE `calendar_exchange_sync` CHANGE COLUMN `state` `state` LONGTEXT NULL AFTER `user_id`");
Vtiger_Utils::ExecuteQuery("ALTER TABLE `calendar_exchange_metadata` CHANGE COLUMN `activity_id` `activity_id` INT(19) unsigned NOT NULL");
