<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 2/8/2017
 * Time: 7:55 AM
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

$data = [
    'vtiger_moveroles' => [
        'moveroles_employees',
        'moveroles_orders',
        'service_provider'
    ],
    'vtiger_orders' => [
        'orders_account',
        'orders_contacts',
        'orders_trip'
    ]
];

foreach ($data as $tableName => $columns)
{
    $res = $db->pquery("SELECT COUNT(*) FROM $tableName");
    if(!$res)
    {
        continue;
    }
    $fullCount = $res->fetchRow()[0];
    foreach($columns as $column)
    {
        $res = $db->pquery("SELECT COUNT(*) FROM $tableName WHERE $column REGEXP '^[0-9]+$' OR $column IS NULL OR $column=''");
        if(!$res)
        {
            continue;
        }
        $intCount = $res->fetchRow()[0];

        if($fullCount == $intCount)
        {
            echo "Changing column $column on $tableName to INT<br>".PHP_EOL;
            $db->pquery("ALTER TABLE $tableName MODIFY $column INT(11)");
        } else {
            echo "Column $column on $tableName does not contain only integers<br>".PHP_EOL;
        }
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";