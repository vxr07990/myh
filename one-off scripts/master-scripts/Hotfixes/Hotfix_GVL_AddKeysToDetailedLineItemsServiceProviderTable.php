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

$tableKey = [
        'vtiger_detailed_lineitems' => 'dli_relcrmid',
        'dli_service_providers' => 'dli_id',
    ];
foreach ($tableKey as $tableName => $key) {
    $addKey = true;
    $sql = 'SHOW INDEX FROM `'.$tableName.'`';
    $result = $db->pquery($sql);
    while ($row = $result->fetchRow()) {
        if ($row['Column_name'] == $key) {
            $addKey = false;
            break;
        }
    }
    if ($addKey) {
        echo 'Creating key ('.$key.') for '.$tableName.PHP_EOL;
        $db->pquery('ALTER TABLE `'.$tableName.'` ADD KEY(`'.$key.'`)');
    }
}

$tableKey = [
    'vtiger_crmentityrel' => ['crmid', 'relcrmid'],
    'vtiger_participatingagents' => ['rel_crmid'],
];
foreach ($tableKey as $tableName => $keys) {
    foreach ($keys as $key) {
        $addKey = true;
        $sql    = 'SHOW INDEX FROM `'.$tableName.'`';
        $result = $db->pquery($sql);
        while ($row = $result->fetchRow()) {
            if ($row['Column_name'] == $key && $row['Key_name'] != 'PRIMARY') {
                $addKey = false;
                break;
            }
        }
        if ($addKey) {
            echo 'Creating key ('.$key.') for '.$tableName.PHP_EOL;
            $db->pquery('ALTER TABLE `'.$tableName.'` ADD KEY(`'.$key.'`)');
        }
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";