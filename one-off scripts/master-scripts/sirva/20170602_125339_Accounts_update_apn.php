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

$sql = "SELECT fieldid, typeofdata FROM vtiger_field WHERE fieldname='apn'";
$res = $db->query($sql);
if($res) {
    while($row = $res->fetchRow()) {
        // Search to see if the field is currently mandatory
        $tod = explode('~', $row['typeofdata']);
        $optional = array_search('O', $tod);
        if($optional !== false && $optional !== null) {
            // Change optional field in typeofdata to mandatory.
            $tod[$optional] = 'M';
            $tod = implode('~', $tod);
            
            // Update APN field to be mandatory
            $sql = "UPDATE vtiger_field SET typeofdata=? WHERE fieldid=?";
            $db->pquery($sql, [$tod, $row['fieldid']]);
            
            echo "APN Field of ID ".$row['fieldid']." has been updated to be mandatory.<br/>\n";
        }else {
            echo "APN Field of ID ".$row['fieldid']." is already mandatory.<br/>\n";
        }
    }
}else {
    echo "An error occurred trying to get the current APN field. Check MySQL fail log.<br/>\n";
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";