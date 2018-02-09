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

$moduleName = 'Contracts';
$fieldName = 'nat_account_no';
$newUIType = 1;
//@NOTE: using this because maybe we want it to run even if we don't update the uitype.
$updateExistingRecords = false;

$contracts = Vtiger_Module::getInstance($moduleName);
if (!$contracts) {
    echo $moduleName . " DOES NOT EXIST!".PHP_EOL;

    return;
}

$field1 = Vtiger_Field::getInstance($fieldName, $contracts);
if (!$field1) {
    echo $fieldName . " DOES NOT EXIST!" . PHP_EOL;
    return;
}

if ($field1->uitype != $newUIType) {
    $db   = &PearDatabase::getInstance();
    $stmt = 'UPDATE `vtiger_field` SET `uitype` = ? WHERE `fieldid` = ?';
    $db->pquery($stmt, [$newUIType, $field1->id]);
    $updateExistingRecords = true;
}

if ($updateExistingRecords) {

    $stmt = 'SELECT `contractsid`,`nat_account_no`,`vtiger_account`.`apn` FROM `vtiger_contracts` '
            . ' INNER JOIN `vtiger_account` ON (`nat_account_no` = `accountid`) '
            . ' WHERE `nat_account_no` <> ""';
    $result = $db->query($stmt);

    if ($result && method_exists($result, 'fetchRow')) {
        while($row = $result->fetchRow()) {
            $updateStmt = 'UPDATE `vtiger_contracts` SET `nat_account_no` = ? WHERE `contractsid` = ? LIMIT 1';
            $db->pquery($updateStmt, [$row['apn'], $row['contractsid']]);
        }
    }
}

print "\e[36mFINISHED: " . __FILE__ . "<br />\n\e[0m";
