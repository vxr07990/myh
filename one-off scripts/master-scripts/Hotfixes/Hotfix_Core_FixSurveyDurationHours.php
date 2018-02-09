<?php
if (function_exists("call_ms_function_ver")) {
    $version = 2;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

$newUIType = 7;
$newTypeOfData = 'I~O';
$fieldName = 'duration_hours';

$db = &PearDatabase::getInstance();
foreach (['Calendar', 'Events'] as $moduleName) {
    $moduleInstance = Vtiger_Module::getInstance($moduleName);
    if ($moduleInstance) {
        $stmt = 'SELECT * FROM `vtiger_field` WHERE `tabid` = ? AND `fieldname` = ? LIMIT 1';
        $res = $db->pquery($stmt, [$moduleInstance->getId(), $fieldName]);
        $row = $res->fetchRow();
        if ($row) {
            if ($row['typeofdata'] != $newTypeOfData) {
                $db->pquery("UPDATE `vtiger_field` SET `typeofdata`=? WHERE `fieldid`=?", [$newTypeOfData, $row['fieldid']]);
            }
            if ($row['uitype'] != $newUIType) {
                $db->pquery("UPDATE `vtiger_field` SET `uitype`=? WHERE `fieldid`=?", [$newUIType, $row['fieldid']]);
            }
        }

        //Can't use this: \Vtiger_Functions::getModuleFieldInfos
//        if ($field3 = Vtiger_Field::getInstance($fieldName, $moduleInstance)) {
//            if ($field3->typeofdata != $newTypeOfData) {
//                $db = &PearDatabase::getInstance();
//                $db->pquery("UPDATE `vtiger_field` SET `typeofdata`=? WHERE `fieldid`=?", [$newTypeOfData, $field3->id]);
//            }
//            if ($field3->uitype != $newUIType) {
//                $db = &PearDatabase::getInstance();
//                $db->pquery("UPDATE `vtiger_field` SET `uitype`=? WHERE `fieldid`=?", [$newUIType, $field3->id]);
//            }
//        }
    }
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";