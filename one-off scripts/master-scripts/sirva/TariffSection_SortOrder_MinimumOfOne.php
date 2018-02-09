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

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

if(!$db) {
    $db = PearDatabase::getInstance();
}

$sql = "SELECT fieldid, typeofdata FROM vtiger_field WHERE fieldname='tariffsection_sortorder'";
$res = $db->query($sql);
if($db->num_rows($res) > 0) {
    echo "Updating sortorder field to have a minimum value of 1...<br/>\n";
    $row = $res->fetchRow();
    $id = $row['fieldid'];
    $typeofdata = $row['typeofdata'];
    if(strpos($typeofdata, 'MIN') === false) {
        $typeofdata .= "~MIN=1";
        $sql = "UPDATE vtiger_field SET typeofdata=? WHERE fieldid=?";
        $res = $db->pquery($sql, [$typeofdata, $id]);
    }else {
        echo "Minimum is already set.<br/>\n";
    }
}else {
    echo "Unable to set minimum sortorder value, field does not exist.<br/>\n";
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
