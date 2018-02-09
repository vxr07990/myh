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
 * User: jgriffin
 * Date: 10/6/2016
 * Time: 4:41 PM
 */

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

echo __FILE__.PHP_EOL;

$db = &PearDatabase::getInstance();

$sql = 'SHOW INDEX FROM `vtiger_packing_items`';
if (!$db->pquery($sql)->fetchRow()) {
    $result = $db->pquery('SELECT quoteid,itemid,COUNT(*)-1 AS cnt FROM vtiger_packing_items GROUP BY quoteid,itemid HAVING COUNT(*) > 1');
    while ($stuff = $result->fetchRow()) {
        $stmt = 'DELETE FROM `vtiger_packing_items` WHERE `quoteid`=? AND `itemid`=? LIMIT ' . $stuff['cnt'];
        $db->pquery($stmt, [$stuff['quoteid'], $stuff['itemid']]);
    }
    echo 'Creating combined primary key for vtiger_packing_items'.PHP_EOL;
    $db->pquery('ALTER TABLE `vtiger_packing_items` ADD PRIMARY KEY(quoteid, itemid)');
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";