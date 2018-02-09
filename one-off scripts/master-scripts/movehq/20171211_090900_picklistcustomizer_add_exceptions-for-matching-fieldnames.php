<?php

/**
 * OT4776: Hotfix to change the data type of the "Competitive" field
 * to a checkbox and update existing DB values.
 */

if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

if(!$db) {
    $db = PearDatabase::getInstance();
}

$sql = "SELECT * FROM `vtiger_picklistexceptions`";
$result = $db->query($sql);

$checkSql   = "SELECT * FROM `vtiger_picklistexceptions` WHERE `agentid`=? AND `fieldid`=? AND `value`=? AND `type`=?";

$currentTime = date('Y-m-d H:i:s');
$currentUser = 1;

$fieldMap = [];
$fieldValueMap = [];

while($row =& $result->fetchRow()) {
    if(!array_key_exists($row['fieldid'], $fieldMap)) {
        $fieldMap[$row['fieldid']] = [];
        $fieldValueMap[$row['fieldid']] = [];
    }
    if($row['type'] == 'ADDED') {
        $fieldValueMap[$row['fieldid']][$row['agentid']][$row['type']][$row['id']] = $row['value'];
    } elseif($row['type'] == 'DELETED' || $row['type'] == 'CUSTOM_DELETED') {
        $fieldValueMap[$row['fieldid']][$row['agentid']][$row['type']][$row['id']] = $row['old_val_id'];
    } elseif($row['type'] == 'RENAMED') {
        $fieldValueMap[$row['fieldid']][$row['agentid']][$row['type']][$row['id']] = ['oldValId' => $row['old_val_id'], 'newVal' => $row['value']];
    }
    $fieldModel = Vtiger_Field::getInstance($row['fieldid']);
    $fieldSelectSql = "SELECT fieldid FROM `vtiger_field` WHERE fieldname=? AND fieldid != ?";
    $selectResult = $db->pquery($fieldSelectSql, [$fieldModel->name, $fieldModel->id]);
    while($fieldRow =& $selectResult->fetchRow()) {
        if(!in_array($fieldRow['fieldid'], $fieldMap[$row['fieldid']])) {
            $fieldMap[$row['fieldid']][] = $fieldRow['fieldid'];
        }
//        $checkResult = $db->pquery($checkSql, [$row['agentid'], $fieldRow['fieldid'], $row['value'], $row['type']]);
//        if($checkResult && $db->num_rows($checkResult) == 0) {
//            $db->pquery($insertSql, [$row['agentid'], $fieldRow['fieldid'], $row['value'], $row['type'], $row['createdtime'], $row['modifiedtime'], $row['modifiedby']]);
//        }
    }
}

$addedSql   = "INSERT INTO `vtiger_picklistexceptions` (`agentid`, `fieldid`, `value`, `type`, `createdtime`, `modifiedtime`, `modifiedby`) VALUES (?,?,?,?,?,?,?)";
$deletedSql = "INSERT INTO `vtiger_picklistexceptions` (`agentid`, `fieldid`, `type`, `old_val_id`, `createdtime`, `modifiedtime`, `modifiedby`) VALUES (?,?,?,?,?,?,?)";
$renamedSql = "INSERT INTO `vtiger_picklistexceptions` (`agentid`, `fieldid`, `value`, `type`, `old_val_id`, `createdtime`, `modifiedtime`, `modifiedby`) VALUES (?,?,?,?,?,?,?,?)";

$newFieldValueMap = [];
foreach($fieldMap as $source_fieldid => $target_fieldlist) {
    foreach($target_fieldlist as $target_fieldid) {
        foreach ($fieldValueMap[$source_fieldid] as $agentid => $typeArrays) {
            foreach ($typeArrays as $type => $exceptions) {
                switch ($type) {
                    case 'ADDED':
                        foreach ($exceptions as $exceptionId => $value) {
                            $db->pquery($addedSql, [$agentid, $target_fieldid, $value, $type, $currentTime, $currentTime, $currentUser]);
                            $result                         = $db->query("SELECT LAST_INSERT_ID() AS `id`");
                            $newFieldValueMap[$exceptionId] = $result->fields['id'];
                        }
                        break;
                    case 'DELETED':
                        foreach ($exceptions as $exceptionId => $oldValId) {
                            $db->pquery($deletedSql, [$agentid, $target_fieldid, $type, $oldValId, $currentTime, $currentTime, $currentUser]);
                        }
                        break;
                    case 'CUSTOM_DELETED':
                        foreach ($exceptions as $exceptionId => $oldValId) {
                            $db->pquery($deletedSql, [$agentid, $target_fieldid, $type, $newFieldValueMap[$oldValId], $currentTime, $currentTime, $currentUser]);
                        }
                        break;
                    case 'RENAMED':
                        foreach ($exceptions as $exceptionId => $idValuePair) {
                            $db->pquery($renamedSql, [$agentid, $target_fieldid, $idValuePair['newVal'], $type, $idValuePair['oldValId'], $currentTime, $currentTime, $currentUser]);
                        }
                        break;
                }
            }
        }
    }
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
