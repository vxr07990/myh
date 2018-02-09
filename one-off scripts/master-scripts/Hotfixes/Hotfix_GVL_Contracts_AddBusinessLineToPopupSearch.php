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


// OT 16124 - Contract field search popup add business line field
// Search options are set in part by summaryfield in vtiger_field being set to 1.

echo "<br><h3>Starting AddBusinessLineToPopupSearch</h3><br>\n";

$moduleName = 'contracts';
$fieldName = 'business_line';

$db = PearDatabase::getInstance();
$sql = "SELECT fieldid, fieldname, tablename, summaryfield FROM `vtiger_field` WHERE tablename = ? AND fieldname = ? AND summaryfield = ?";
$result = $db->pquery($sql, ['vtiger_'.$moduleName, $fieldName, 0]);

while ($row =& $result->fetchRow()) {
    $sql = 'UPDATE `vtiger_field` SET `summaryfield` = 1 WHERE `fieldid` = ?  LIMIT 1';
    $query = $db->pquery($sql, [$row['fieldid']]);
}

echo "<br><h3>Ending AddBusinessLineToPopupSearch</h3><br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";