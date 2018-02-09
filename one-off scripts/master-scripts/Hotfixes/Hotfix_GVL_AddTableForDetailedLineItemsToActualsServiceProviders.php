<?php
if (function_exists("call_ms_function_ver")) {
    $version = 2;
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
 * Date: 9/16/2016
 * Time: 12:53 PM
 */


include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$db = PearDatabase::getInstance();

$stmt = 'SELECT * FROM information_schema.tables WHERE table_schema = "' . getenv('DB_NAME') . '" AND table_name = "dli_service_providers" LIMIT 1';
$res = $db->pquery($stmt);
if ($db->num_rows($res) > 0) {
    echo 'dli_service_providers already exists'.PHP_EOL;
} else {
    $stmt = 'CREATE TABLE `dli_service_providers` 
            (`dli_service_providers_id` INT(11) NOT NULL AUTO_INCREMENT, 
            `dli_id` INT(11), `vendor_id` INT(11), `split_amount` DECIMAL(12,2),
            PRIMARY KEY (dli_service_providers_id))';
    $db->pquery($stmt);

    /*
    $sql = 'INSERT INTO `dli_service_providers` (dli_id, vendor_id)
            SELECT `detaillineitemsid`,`dli_service_provider`
            FROM `vtiger_detailed_lineitems` WHERE dli_service_provider IS NOT NULL';
    $db->pquery($sql);
    */
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";