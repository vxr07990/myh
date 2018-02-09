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

$db = PearDatabase::getInstance();

//modify field from int(19) to varchar(250) to store multiple vendors separate by ' |##| '
$db->pquery("ALTER TABLE `vtiger_orderstask` CHANGE `assigned_vendor` `assigned_vendor` VARCHAR(250) NULL DEFAULT NULL;", array());

//add new uitype for assigned vendors
$result = $db->pquery('SELECT * FROM vtiger_ws_fieldtype WHERE uitype=? AND fieldtype=?', array('1010', 'assignedvendors'));
if (!$db->num_rows($result)) {
    $db->pquery("INSERT INTO `vtiger_ws_fieldtype`(`uitype`, `fieldtype`) VALUES (?,?)", array("1010", "assignedvendors"));
}

$db->pquery("UPDATE vtiger_field SET uitype=1010 WHERE columnname=? AND tablename=?", array('assigned_vendor', 'vtiger_orderstask'));


$result = $db->pquery("SELECT fieldid FROM vtiger_field WHERE tablename='vtiger_orderstask' AND  columnname = 'assigned_vendor'", array());
if($db->num_rows($result)){
	$fieldID = $db->query_result($result, 0, 'fieldid');
	$db->pquery("DELETE FROM vtiger_fieldmodulerel WHERE fieldid = ?", array($fieldID));
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";