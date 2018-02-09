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

$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$moduleName = 'Opportunities';
$fieldName =  'opportunity_disposition';
$newPresence = 1;
$newDisplayType = 0;

$moduleInstance = Vtiger_Module::getInstance($moduleName);

if (!$moduleInstance) {
    return;
}

$fieldInstance = Vtiger_Field::getInstance($fieldName, $moduleInstance);

if (!$fieldInstance) {
    return;
}

$db = &PearDatabase::getInstance();

if ($fieldInstance->presence != $newPresence) {
    $stmt = 'UPDATE `vtiger_field` SET `presence`= ? WHERE `fieldid`=? LIMIT 1';
    print 'Running: '. $stmt . PHP_EOL;
    print 'vars: ' . print_r([$newDisplayType, $fieldInstance->id], true). PHP_EOL;
    if (!$db->pquery($stmt, [$newPresence, $fieldInstance->id])) {
        echo "There was an error changing LBL_OPPORTUNITY_OPPORTUNITYDISPOSITION field to presence 0, please check the MySQL fail log.<br/>\n";
    }
}

if ($fieldInstance->displaytype != $newDisplayType) {
    $stmt = 'UPDATE `vtiger_field` SET `displaytype`= ? WHERE `fieldid`=? LIMIT 1';
    print 'Running: '. $stmt . PHP_EOL;
    print 'vars: ' . print_r([$newDisplayType, $fieldInstance->id], true). PHP_EOL;
    if (!$db->pquery($stmt, [$newDisplayType, $fieldInstance->id])) {
        echo "There was an error changing LBL_OPPORTUNITY_OPPORTUNITYDISPOSITION field to displaytype 0, please check the MySQL fail log.<br/>\n";
    }
}

//// Get that db though.
//$adb = PearDatabase::getInstance();
//
//$sql = "SELECT fieldid FROM vtiger_field WHERE fieldname='opportunity_disposition' AND tablename='vtiger_potential'";
//$res = $adb->query($sql);
//if($res && $adb->num_rows($res) > 0) {
//    $id = $res->fetchRow()[0];
//    echo "Removing LBL_OPPORTUNITY_OPPORTUNITYDISPOSITION field...<br/>\n";
//    $sql = "UPDATE vtiger_field SET displaytype=0 WHERE fieldid=$id";
//    if(!$adb->query($sql)) {
//        echo "There was an error removing LBL_OPPORTUNITY_OPPORTUNITYDISPOSITION field, please check the MySQL fail log.<br/>\n";
//    }else {
//        echo "Successfully removed the LBL_OPPORTUNITY_OPPORTUNITYDISPOSITION field, please check the MySQL fail log.<br/>\n";
//    }
//}else{
//    echo "LBL_OPPORTUNITY_OPPORTUNITYDISPOSITION already deleted, skipping...<br/>\n";
//}
//// Done adding CWT/Quantity


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
