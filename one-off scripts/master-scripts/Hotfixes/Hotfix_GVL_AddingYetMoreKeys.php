<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 12/5/2016
 * Time: 1:54 PM
 */
if (function_exists("call_ms_function_ver")) {
    $version = 8;
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

$tableKey = [
    'vtiger_trips' => [['driver_id'],['agent_id'],['agent_unit'],['trips_trailer']],
    'vtiger_outofservice' => [['outofservice_employeesid']],
    'vtiger_orders' => [['orders_contacts'],['orders_account'],['orders_no'],['orders_trip']],
    'vtiger_moveroles' => [['moveroles_orders'],['moveroles_employees'],['service_provider']],
    'vtiger_packing_items' => [['quoteid', 'itemid']],
    'vtiger_bulky_items' => [['quoteid', 'bulkyid']],
    'vtiger_tariffsections' => [['related_tariff']],
    'vtiger_tariffservices' => [['tariff_section'],['effective_date'],['related_tariff']],
    'vtiger_effectivedates' => [['related_tariff']],
    'vtiger_orderstask' => [['participating_agent'],['service_date_to']],
    'vtiger_vehicleoutofservice' => [['outofservice_vehicle']],
];

foreach($tableKey as $tableName => $keys)
{
    foreach($keys as $key)
    {
        ms_AddKeysAYMK($db, $tableName, $key);
    }
}

function ms_AddKeysAYMK($db, $tableName, $key)
{
    $addKey = true;
    $sql = 'SHOW INDEX FROM `'.$tableName.'`';
    $result = $db->pquery($sql);
    if(!$result)
    {
        return;
    }
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





print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";