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


//added bonus fix broken UI types
$sql = "SELECT fieldname FROM `vtiger_field` WHERE uitype = 15";
$result = $db->pquery($sql, array());
while ($row =& $result->fetchRow()) {
    $name = $row[0];
    Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_picklist` WHERE name ='$name'");
}
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET uitype = 16 WHERE uitype = 15");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";