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

$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

// Get that db though.
global $adb;
$moduleName = 'Contracts';
$fieldName = 'billing_apn';
$moduleInstance = Vtiger_Module::getInstance($moduleName);
$blockInstance = Vtiger_Block::getInstance('LBL_CONTRACTS_BILLING', $moduleInstance);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);

if(!$field) {
    print "\e[32mFIELD NOT FOUND. SKIPPING: " . __FILE__ . "<br />\n\e[0m";
    return;
} else {
    $sql = "UPDATE vtiger_field SET uitype =7 WHERE tablename = 'vtiger_contracts' AND fieldname = 'billing_apn'";
    $result = $adb->pquery($sql, array());
    if($result){
        $sql = "SELECT DISTINCT a.billing_apn, b.accountid, b.account_no FROM vtiger_contracts AS a LEFT JOIN vtiger_account AS b ON a.billing_apn = b.accountid WHERE a.billing_apn IS NOT NULL AND 
        billing_apn != '' AND b.accountid IS NOT NULL";
        $result = $adb->pquery($sql, array());
        if($adb->num_rows($result) > 0){
            while($row = $result->fetchRow()){
                $apn = $row['billing_apn'];
                $accountNo = $row['account_no'];
                $sql = "UPDATE vtiger_contracts SET billing_apn = ? WHERE billing_apn = ?";
                $adb->pquery($sql, [$accountNo, $apn]);
            }
        } else {
            echo "No rows to update.<br>\n";
        }
    } else {
        echo "Unable to update vtiger_field <br>\n";
    }
}


print "\e[32mFINISHED: " . __FILE__ . "<br />\n\e[0m";


