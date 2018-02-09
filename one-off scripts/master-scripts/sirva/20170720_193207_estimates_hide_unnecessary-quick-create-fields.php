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

//$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('include/database/PearDatabase.php');

if(!$db) {
    $db = PearDatabase::getInstance();
}

$fields = [
    "parent_contract",
    "nat_account_no",
    "days_to_move",
    "demand_color",
    "pricing_level",
    "grr_estimate"
];

foreach($fields as $field) {
    $sql = "SELECT fieldid FROM vtiger_field WHERE fieldname=? AND tablename IN ('vtiger_quotes','vtiger_quotescf')";
    if(($res = $db->pquery($sql, [$field])) != false) {
        while(($row = $res->fetchRow()) != false) {
            $id = $row[0];

            if($id) {
                $sql = "UPDATE vtiger_field SET quickcreate=1 WHERE fieldid=?";
                if($db->pquery($sql, [$id]) == false) {
                    echo "Failed to update field $field.<br/>\n";
                }
            }
        }
    }
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
