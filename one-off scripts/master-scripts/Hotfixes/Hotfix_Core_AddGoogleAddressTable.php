<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 1/12/2017
 * Time: 4:46 PM
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

$db = &PearDatabase::getInstance();

$stmt = 'SELECT * FROM information_schema.tables WHERE table_schema = "' . getenv('DB_NAME') . '" AND table_name = "vtiger_google_addresscalc" LIMIT 1';
$res = $db->pquery($stmt);
if ($db->num_rows($res) > 0) {
    echo 'vtiger_google_addresscalc already exists'.PHP_EOL;
} else {
    $stmt = 'CREATE TABLE `vtiger_google_addresscalc` 
            (`vtiger_google_addresscalc_id` INT(11) NOT NULL AUTO_INCREMENT, 
            `quoteid` INT(11), `address` VARCHAR(200), `miles` VARCHAR(20), `time` VARCHAR(20),
            PRIMARY KEY (vtiger_google_addresscalc_id), KEY(quoteid))';
    $db->pquery($stmt);

}



print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";