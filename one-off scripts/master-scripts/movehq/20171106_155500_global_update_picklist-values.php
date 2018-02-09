<?php

if (function_exists("call_ms_function_ver")) {
    $version = 2;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "RUNNING: " . __FILE__ . "<br />\n";

$processedFields = [];

$currentTime = date('Y-m-d H:i:s');
$db = PearDatabase::getInstance();
$sql = "SELECT * FROM `vtiger_field` WHERE uitype IN (16,1500)";
$result = $db->query($sql);
while($row =& $result->fetchRow()) {
    $fieldName = $row['fieldname'];
    $tablename = 'vtiger_'.$fieldName;
    if(!Vtiger_Utils::CheckTable($tablename)) {
        continue;
    }

    resetPicklistValues_19250($row['fieldid'], $tablename);
}

function resetPicklistValues_19250($fieldid, $tablename) {
    $db = PearDatabase::getInstance();
    $fieldModel = Vtiger_Field::getInstance($fieldid);
    $fieldName = $fieldModel->name;
    $seqTablename = $tablename.'_seq';
    if(!Vtiger_Utils::CheckTable($tablename) || !Vtiger_Utils::CheckTable($seqTablename)) {
        return false;
    }

    //Get table's primary key
    $indexRes   = $db->query("SHOW KEYS FROM `".$tablename."` WHERE Key_name = 'PRIMARY'");
    $primaryKey = $indexRes->fields['Column_name'];

    if(!$primaryKey) {
        return false;
    }

    //Retrieve values from base table
    $sql = "SELECT *, COUNT(*) AS numRows FROM `$tablename` GROUP BY `$fieldName` ORDER BY `$primaryKey`";
    $result = $db->query($sql);
    $values = [];
    $specialValues = [];
    $hasDuplicates = false;
    while($row =& $result->fetchRow()) {
        $values[] = $row[$fieldName];
        if($row['special']) {
            $specialValues[] = $row[$fieldName];
        }
        if($row['numRows'] > 1) {
            $hasDuplicates = true;
        }
    }

    if(!$hasDuplicates) {
        return;
    }

    Vtiger_Utils::ExecuteQuery("TRUNCATE TABLE `$tablename`");
    Vtiger_Utils::ExecuteQuery("UPDATE `$seqTablename` SET id=0");

    $fieldModel->setPicklistValues($values);

    if($specialValues && is_array($specialValues) && count($specialValues) > 0) {
        Vtiger_Utils::ExecuteQuery("UPDATE `$tablename` SET `special`=1 WHERE `".$fieldModel->name."` IN ('".implode("','", $specialValues)."')");
    }

    return true;
}
