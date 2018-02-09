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

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('include/database/PearDatabase.php');

if(!$db) {
    $db = PearDatabase::getInstance();
}

$sql = "SELECT fieldid, typeofdata FROM vtiger_field WHERE fieldname = 'agent_ids' AND tablename = 'vtiger_users'";
if($res = $db->query($sql)) {
    while($row = $res->fetchRow()) {
        $id = $row['fieldid'];
        $tod = $row['typeofdata'];

        if(updateTypeOfData($tod, "O", "M")) {
            $sql = "UPDATE vtiger_field SET typeofdata = ? WHERE fieldid = ?";
            if(!$db->pquery($sql, [$tod, $id])) {
                echo "Failed attempting to update typeofdata for field {$id}.<br/>\n";
            }
        }else {
            echo "Field {$fieldid} is already mandatory.<br/>\n";
        }
    }
}else {
    echo "Could not get fields.<br/>\n";
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
