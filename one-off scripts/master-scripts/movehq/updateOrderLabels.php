<?php
if (function_exists("call_ms_function_ver")) {
    $version = 2; // Need to add +1 every time you update that script
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

    require_once 'include/utils/utils.php';
    require_once 'include/utils/CommonUtils.php';

    global $adb;
    $ids_list = [];
    $stmt = 'SELECT * FROM `vtiger_crmentity` WHERE setype=?';
    $res = $adb->pquery($stmt, ['Orders']);
    if ($res && method_exists($res, 'fetchRow')) {
        while ($row = $res->fetchRow()) {
            $ids_list[] = $row['crmid'];
        }
    }

    if ($ids_list) {
        $result = Vtiger_Functions::computeCRMRecordLabels('Orders', $ids_list);
        //print "HERE: " . print_r($result, true) . PHP_EOL;
        foreach ($result as $crmid => $value) {
            $stmt = 'UPDATE `vtiger_crmentity` SET `label`=? WHERE `crmid`=? LIMIT 1';
            $adb->pquery($stmt, [$value, $crmid]);
        }
    }
echo "<br>DONE!<br>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";