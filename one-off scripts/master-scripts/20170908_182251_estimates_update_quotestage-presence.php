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

$fieldName = "quotestage";
$values = [
    "Created" => 1,
    "Delivered" => 1,
    "Reviewed" => 1,
    "Accepted" => 1,
    "Rejected" => 1
];

foreach($values as $value => $presence) {
    $sql = "SELECT {$fieldName}id, presence FROM vtiger_{$fieldName} WHERE {$fieldName}=?";
    $res = $db->pquery($sql, [$value]);
    while($row = $res->fetchRow()) {
        if(!$row['presence']) {
            $sql = "UPDATE vtiger_{$fieldName} SET presence=? WHERE {$fieldName}id=?";
            if(!$db->pquery($sql, [$presence, $row["{$fieldName}id"]])) {
                echo "FAILED ON {$value}\n";
            }
        }
    }
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
