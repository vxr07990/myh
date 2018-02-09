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


$modulename = 'Estimates';
$sql = "SELECT tabid FROM `vtiger_tab` WHERE name = ?";
$result = $db->pquery($sql, array($modulename));
$row = $result->fetchRow();
$tabid = $row[0];
$sql = "SELECT * FROM `vtiger_entityname` WHERE modulename = ?";
$result = $db->pquery($sql, array($modulename));
$row = $result->fetchRow();
if (empty($row)) {
    $sql = "SELECT tablename, fieldname, entityidfield, entityidcolumn FROM `vtiger_entityname` WHERE modulename = 'Quotes'";
    $result = $db->pquery($sql, array());
    $row = $result->fetchRow();
    $tablename = $row[0];
    $fieldname = $row[1];
    $entityidfield = $row[2];
    $entityidcolumn = $row[3];
    $sql = "INSERT INTO `vtiger_entityname` (tabid, modulename, tablename, fieldname, entityidfield, entityidcolumn) VALUES (?,?,?,?,?,?)";
    $result = $db->pquery($sql, array($tabid, $modulename, $tablename, $fieldname, $entityidfield, $entityidcolumn));
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";