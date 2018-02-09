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
 * Date: 10/20/2016
 * Time: 10:55 AM
 */

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$db = &PearDatabase::getInstance();

$db->pquery('DELETE FROM `vtiger_quotes_servicecost` WHERE serviceid=0 OR serviceid IS NULL');

$tablePrimaryKey = [
    'vtiger_quotes_sectiondiscount' => ['estimateid', 'sectionid'],
    'vtiger_quotes_servicecost' => ['estimateid', 'serviceid'],
    'vtiger_quotes_baseplus' => ['estimateid', 'serviceid'],
    'vtiger_quotes_breakpoint' => ['estimateid', 'serviceid'],
    'vtiger_quotes_servicecharge' => ['estimateid', 'serviceid'],
    'vtiger_quotes_weightmileage' => ['estimateid', 'serviceid'],
    'vtiger_quotes_perunit' => ['estimateid', 'serviceid'],
    'vtiger_quotes_countycharge' => ['estimateid', 'serviceid'],
    'vtiger_quotes_hourlyset' => ['estimateid', 'serviceid'],
    //'vtiger_quotes_sit' => ['estimateid', 'serviceid'],
    'vtiger_quotes_valuation' => ['estimateid', 'serviceid'],
    'vtiger_quotes_cwtbyweight' => ['estimateid', 'serviceid'],
];

$tableKey = [
    'vtiger_quotes_bulky' => ['estimateid', 'serviceid', 'description', 'bulky_id'],
    'vtiger_quotes_crating' => ['estimateid', 'serviceid', 'line_item_id'],
    'vtiger_quotes_packing' => ['estimateid', 'serviceid', 'name', 'packing_id'],
    'vtiger_orders' => ['orders_vanlineregnum'],
];

foreach ($tablePrimaryKey as $tableName => $key) {
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
        foreach ($key as &$item) {
            $item = '`'.$item.'`';
        }
        $k = implode(',', $key);
        echo 'Creating key ('.$k.') for '.$tableName.PHP_EOL;
        $db->pquery('ALTER TABLE `'.$tableName.'` ADD PRIMARY KEY('.$k.')');
    }
}

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
        foreach ($key as &$item) {
            $item = '`'.$item.'`';
        }
        $k = implode(',', $key);
        echo 'Creating key ('.$k.') for '.$tableName.PHP_EOL;
        $db->pquery('ALTER TABLE `'.$tableName.'` ADD KEY('.$k.')');
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";