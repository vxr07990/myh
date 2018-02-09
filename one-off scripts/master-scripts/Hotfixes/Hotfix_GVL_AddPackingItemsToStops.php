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
 * Date: 8/30/2016
 * Time: 11:31 AM
 */


include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$db = PearDatabase::getInstance();

$stmt = 'SELECT * FROM information_schema.tables WHERE table_schema = "' . getenv('DB_NAME') . '" AND table_name = "packing_items_extrastops" LIMIT 1';
$res = $db->pquery($stmt);
if ($db->num_rows($res) > 0) {
    echo 'packing_items_extrastops already exists'.PHP_EOL;
} else {
    $stmt = 'CREATE TABLE `packing_items_extrastops` (`itemid` INT(11), `stopid` INT(11), `pack_qty` INT(10) DEFAULT 0, `unpack_qty` INT(10) DEFAULT 0, `ot_pack_qty` INT(10) DEFAULT 0, 
    `ot_unpack_qty` INT(10) DEFAULT 0
    , `label` 
    VARCHAR
    (50))';
    $db->pquery($stmt);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";