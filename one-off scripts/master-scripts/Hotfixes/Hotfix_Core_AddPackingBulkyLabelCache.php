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

/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 10/18/2016
 * Time: 3:25 PM
 */

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$db = PearDatabase::getInstance();

$stmt = 'SELECT * FROM information_schema.tables WHERE table_schema = "' . getenv('DB_NAME') . '" AND table_name = "bulky_label_cache" LIMIT 1';
$res = $db->pquery($stmt);
if ($db->num_rows($res) > 0) {
    echo 'bulky_label_cache already exists'.PHP_EOL;
} else {
    $stmt = 'CREATE TABLE `bulky_label_cache` 
            (`id` INT(11) NOT NULL AUTO_INCREMENT,
            `vanline` VARCHAR(100),
            `tariff` VARCHAR(50),
            `item_id` INT(11), 
            `label` VARCHAR(100),
            PRIMARY KEY (id),
            KEY (vanline, tariff))';
    $db->pquery($stmt);
}

$stmt = 'SELECT * FROM information_schema.tables WHERE table_schema = "' . getenv('DB_NAME') . '" AND table_name = "bulky_label_cachetime" LIMIT 1';
$res = $db->pquery($stmt);
if ($db->num_rows($res) > 0) {
    echo 'bulky_label_cachetime already exists'.PHP_EOL;
} else {
    $stmt = 'CREATE TABLE `bulky_label_cachetime` 
            (`id` INT(11) NOT NULL AUTO_INCREMENT,
            `vanline` VARCHAR(100),
            `tariff` VARCHAR(50),
            `updated_time` DATETIME, 
            PRIMARY KEY (id),
            KEY (updated_time),
            KEY (vanline, tariff))';
    $db->pquery($stmt);
}

$stmt = 'SELECT * FROM information_schema.tables WHERE table_schema = "' . getenv('DB_NAME') . '" AND table_name = "packing_label_cache" LIMIT 1';
$res = $db->pquery($stmt);
if ($db->num_rows($res) > 0) {
    echo 'packing_label_cache already exists'.PHP_EOL;
} else {
    $stmt = 'CREATE TABLE `packing_label_cache` 
            (`id` INT(11) NOT NULL AUTO_INCREMENT,
            `vanline` VARCHAR(100),
            `tariff` VARCHAR(50),
            `item_id` INT(11), 
            `label` VARCHAR(100),
            PRIMARY KEY (id),
            KEY (vanline, tariff))';
    $db->pquery($stmt);
}

$stmt = 'SELECT * FROM information_schema.tables WHERE table_schema = "' . getenv('DB_NAME') . '" AND table_name = "packing_label_cachetime" LIMIT 1';
$res = $db->pquery($stmt);
if ($db->num_rows($res) > 0) {
    echo 'packing_label_cachetime already exists'.PHP_EOL;
} else {
    $stmt = 'CREATE TABLE `packing_label_cachetime` 
            (`id` INT(11) NOT NULL AUTO_INCREMENT,
            `vanline` VARCHAR(100),
            `tariff` VARCHAR(50),
            `updated_time` DATETIME, 
            PRIMARY KEY (id),
            KEY (updated_time),
            KEY (vanline, tariff))';
    $db->pquery($stmt);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";