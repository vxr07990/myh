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
 * Date: 8/26/2016
 * Time: 2:52 PM
 */

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$db = PearDatabase::getInstance();

$stmt = 'SELECT * FROM information_schema.columns WHERE table_schema = "'.getenv('DB_NAME').'" AND table_name = "vtiger_misc_accessorials" AND column_name = "included" LIMIT 1';
$res = $db->pquery($stmt);
if ($db->num_rows($res) > 0) {
    echo 'vtiger_misc_accessorials included column already exists'.PHP_EOL;
} else {
    $stmt = 'ALTER TABLE `vtiger_misc_accessorials` ADD COLUMN `included` BIT(1) DEFAULT 1';
    $db->pquery($stmt);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";