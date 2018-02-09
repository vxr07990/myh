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


// OT 2893 Add contact name by the order number at the header.

echo "<br><h3>Starting AddContactNameToDetailHeader</h3><br>\n";

$moduleName = 'orders';
$addedField = 'orders_contacts';
$changedField = false;

$db = PearDatabase::getInstance();
//Only adding field if it's not already present.
$sql = "SELECT * FROM `vtiger_entityname` WHERE `tablename` = ? AND `fieldname` NOT LIKE ?";
$result = $db->pquery($sql, ['vtiger_'.$moduleName, '%'.$addedField.'%']);

while ($row =& $result->fetchRow()) {
    $sql = 'UPDATE `vtiger_entityname` SET `fieldname` = ? WHERE `tabid` = ?  LIMIT 1';
    $query = $db->pquery($sql, [$row['fieldname'].','.$addedField, $row['tabid']]);
    $changedField = true;
    echo "<br>Header updated with $addedField<br/>\n";
}

if (!$changedField) {
    echo"<br>Header not changed.<br/>\n";
}

echo "<br><h3>Ending AddContactNameToDetailHeader</h3><br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";