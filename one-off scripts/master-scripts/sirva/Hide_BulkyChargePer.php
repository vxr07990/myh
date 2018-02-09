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

include_once('include/database/PearDatabase.php');

if(!$db) {
    $db = PearDatabase::getInstance();
}

$sql = "SELECT fieldid FROM vtiger_field WHERE fieldname='bulky_chargeper'";
$res = $db->query($sql);
if($db->num_rows($res)>0) {
    echo "Hiding Bulky List Charge Per picklist...<br/>\n";
    $id = $res->fetchRow()[0];
    $sql = "UPDATE vtiger_field SET presence=1, displaytype=5 WHERE fieldid=$id";
    $db->query($sql);
}else {
    echo "Bulky List Charge Per picklist does not exist.<br/>\n";
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
