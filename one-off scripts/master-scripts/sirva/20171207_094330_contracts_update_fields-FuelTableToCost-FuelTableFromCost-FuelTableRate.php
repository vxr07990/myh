<?php
if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('include/database/PearDatabase.php');

print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

// TFS32792 - agreement fuel table does not allow enough digits in the rate

$db = PearDatabase::getInstance();

$columns = ['from_cost', 'to_cost', 'rate'];
$table = 'vtiger_contractfuel';
$counter = 0;

foreach($columns as $column) {
    $after = '';
    $places = 0;

    if($column === $columns[0]) {
        $after = 'contractid';
        $places = 2;
    } elseif($column === $columns[1]) {
        $places = 2;
    } else {
        $places = 4;
    }

    if($after === '') {
        $after = $columns[$counter-1];
    }

    $sql = "ALTER TABLE `$table` CHANGE COLUMN `$column` `$column` DECIMAL(10,$places) NULL DEFAULT NULL AFTER `$after`";
    $res = $db->query($sql);

    $counter++;
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";