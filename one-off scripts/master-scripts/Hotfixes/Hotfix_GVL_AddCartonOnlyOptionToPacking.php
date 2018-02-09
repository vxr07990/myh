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
 * Date: 9/12/2016
 * Time: 2:14 PM
 */

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$db = PearDatabase::getInstance();

$stmt = 'SELECT * FROM information_schema.columns WHERE table_schema = "'.getenv('DB_NAME').'" AND table_name = "vtiger_packing_items" AND column_name = "containers" LIMIT 1';
$res = $db->pquery($stmt);
if ($db->num_rows($res) > 0) {
    echo 'vtiger_packing_items containers column already exists'.PHP_EOL;
} else {
    $stmt = 'ALTER TABLE `vtiger_packing_items` ADD COLUMN `containers` INT(10) DEFAULT 0';
    $db->pquery($stmt);
}

$stmt = 'SELECT * FROM information_schema.columns WHERE table_schema = "'.getenv('DB_NAME').'" AND table_name = "packing_items_extrastops" AND column_name = "containers" LIMIT 1';
$res = $db->pquery($stmt);
if ($db->num_rows($res) > 0) {
    echo 'packing_items_extrastops containers column already exists'.PHP_EOL;
} else {
    $stmt = 'ALTER TABLE `packing_items_extrastops` ADD COLUMN `containers` INT(10) DEFAULT 0';
    $db->pquery($stmt);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";