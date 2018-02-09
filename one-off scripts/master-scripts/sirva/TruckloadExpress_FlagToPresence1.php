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


include_once('vtlib/Vtiger/Users.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/TariffManager/models/Module.php');

$db = PearDatabase::getInstance();

// Need to remove the truckload field anymore.
$sql = "SELECT * FROM `vtiger_field` WHERE fieldname = 'express_truckload'";
$result = $db->query($sql);
while($row = $result->fetchRow()) {
    $id = $row['fieldid'];
    $sql = "UPDATE `vtiger_field` SET `presence`=1 WHERE `fieldid`=?";
    if(!$db->pquery($sql, [$id])) {
        echo "Error turning off express truckload field of ID $id<br/>\n";
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";