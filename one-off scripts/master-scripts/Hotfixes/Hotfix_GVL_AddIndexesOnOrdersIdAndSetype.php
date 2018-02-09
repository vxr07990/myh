<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 11/30/2016
 * Time: 9:19 AM
 */
if (function_exists("call_ms_function_ver")) {
    $version = 2;
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

$db->pquery('ALTER TABLE vtiger_orderstask MODIFY COLUMN ordersid INT(11)');

$tableKey = [
    'vtiger_quotes' => ['orders_id'],
    'vtiger_orderstask' => ['ordersid'],
    'vtiger_crmentity' => ['setype'],
];

foreach ($tableKey as $tableName => $key) {
    $addKey = true;
    $sql = 'SHOW INDEX FROM `'.$tableName.'`';
    $result = $db->pquery($sql);
    while ($row = $result->fetchRow()) {
        if (in_array($row['Column_name'], $key)) {
            $addKey = false;
            break;
        }
    }
    if ($addKey) {
        foreach($key as &$item)
        {
            $item = '`'.$item.'`';
        }
        $k = implode(',', $key);
        echo 'Creating key ('.$k.') for '.$tableName.PHP_EOL;
        $db->pquery('ALTER TABLE `'.$tableName.'` ADD KEY('.$k.')');
    }
}

$addKey = true;
$sql = 'SHOW INDEX FROM `vtiger_crmentity`';
$result = $db->pquery($sql);
while ($row = $result->fetchRow()) {
    if ($row['Key_name'] == 'crm_setype_idx') {
        $addKey = false;
        break;
    }
}
if ($addKey) {
    echo 'Creating key (setype) for vtiger_crmentity'.PHP_EOL;
    $db->pquery('ALTER TABLE `vtiger_crmentity` ADD KEY crm_setype_idx (setype)');
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";