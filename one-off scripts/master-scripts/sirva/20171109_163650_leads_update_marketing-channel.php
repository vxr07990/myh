<?php
if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('include/database/PearDatabase.php');

$db = PearDatabase::getInstance();

$sql = 'SELECT * FROM `vtiger_field` JOIN `vtiger_picklist` ON `vtiger_field`.fieldname=`vtiger_picklist`.name WHERE uitype=16';
if($res = $db->pquery($sql)) {
    while($row = $res->fetchRow()) {
        if($row['uitype'] == '16') {
            $sql_ = "DELETE FROM vtiger_picklist WHERE picklistid=?";
            if(!$res_ = $db->pquery($sql_, [$row['picklistid']])) {
                print "\e[34mSQL Resource error\e[0m";
                return;
            }
        }
    }
} else {
    print "\e[34mSQL Resource error\e[0m";
    return;
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";