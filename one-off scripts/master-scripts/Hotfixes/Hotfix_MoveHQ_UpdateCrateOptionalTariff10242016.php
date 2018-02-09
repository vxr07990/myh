<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 10/24/2016
 * Time: 10:33 AM
 */
if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$db = PearDatabase::getInstance();

$stmt = 'SELECT * FROM information_schema.columns WHERE table_schema = "'.getenv('DB_NAME').'" AND table_name = "vtiger_crates" AND column_name = "custom_rate_amount_unpack" LIMIT 1';
$res = $db->pquery($stmt);
if ($db->num_rows($res) > 0) {
    echo 'vtiger_crates custom_rate_amount_unpack column already exists'.PHP_EOL;
} else {
    $stmt = 'ALTER TABLE `vtiger_crates` ADD COLUMN `custom_rate_amount_unpack` DECIMAL(12,2) DEFAULT 0';
    $db->pquery($stmt);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";