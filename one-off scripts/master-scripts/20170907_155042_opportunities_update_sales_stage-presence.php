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

//$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('include/database/PearDatabase.php');

if(!$db) {
    $db = PearDatabase::getInstance();
}

$values = [
    "Closed Won" => 1,
    "Closed Lost" => 1
];

foreach($values as $value => $presence) {
    $sql = "SELECT sales_stage_id, presence FROM vtiger_sales_stage WHERE sales_stage=?";
    $res = $db->pquery($sql, [$value]);
    while($row = $res->fetchRow()) {
        if(!$row['presence']) {
            $sql = "UPDATE vtiger_sales_stage SET presence=? WHERE sales_stage_id=?";
            if(!$db->pquery($sql, [$presence, $row['sales_stage_id']])) {
                echo "FAILED ON {$value}\n";
            }
        }
    }
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
