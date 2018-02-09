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

// OT5139 - ExtraStops - Don't allow for negatives in the pricing module

$db = PearDatabase::getInstance();
$tabid = getTabid("ExtraStops");
$sql = "UPDATE `vtiger_field` SET typeofdata=CONCAT(typeofdata,'~MIN=0') WHERE tabid=? AND fieldname='extrastops_weight' AND typeofdata NOT LIKE '%~MIN=0%'";
$result = $db->pquery($sql, array($tabid));

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";