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
 * Date: 10/24/2016
 * Time: 2:58 PM
 */


include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$db = PearDatabase::getInstance();

$stmt = 'SELECT * FROM information_schema.tables WHERE table_schema = "' . getenv('DB_NAME') . '" AND table_name = "gvl_integration_users" LIMIT 1';
$res = $db->pquery($stmt);
if ($db->num_rows($res) > 0) {
    echo 'gvl_integration_users already exists'.PHP_EOL;
} else {
    $stmt = 'CREATE TABLE `gvl_integration_users` 
    (`userid` INT(11) NOT NULL, PRIMARY KEY (userid))';
    $db->pquery($stmt);
}

$db->pquery('INSERT INTO `gvl_integration_users` (userid) VALUES (170)');


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";