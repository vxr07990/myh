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


// Turn on debugging level
$Vtiger_Utils_Log = true;
$adb = PearDatabase::getInstance();

$sql = "SELECT relation_id FROM vtiger_relatedlists WHERE label='OP Lists'";
$res = $adb->query($sql);
if($res) {
    $keepID = $res->fetchRow()[0];
    echo "Keeping related list of ID $keepID.<br/>\n";
    while($disposableID = $res->fetchRow()[0]) {
        echo "Deleting related list of ID $disposableID<br/>\n";
        $sql = "DELETE FROM vtiger_relatedlists WHERE relation_id=$disposableID";
        if(!$adb->query($sql)) {
            echo "There was an error deleting related list of id $disposableID. Please check MySQL fail log.<br/>\n";
        }
    }
}else {
    echo "There was an error gathering the duplicate OP Lists entries. Please check MySQL fail log.<br/>\n";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";