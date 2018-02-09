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
$adb = PearDatabase::getInstance();

echo "Checking that table 'vtiger_tariffvaluations' exists...<br/>\n";
$sql = "SHOW TABLES LIKE 'vtiger_tariffvaluations'";
$result = $adb->query($sql);
if(!$adb->num_rows($result)) {
    echo "Table 'vtiger_tariffvaluations' does not exist, skipping adding field...<br/>\n";
}else {
    echo "Adding weight field to vtiger_tariffvaluations if it does not exist...<br/>\n";
    $sql = "SHOW COLUMNS FROM `vtiger_tariffvaluations` LIKE 'multiplier';";
    $result = $adb->query($sql);
    if(!$adb->num_rows($result)) {
        $sql = "ALTER TABLE vtiger_tariffvaluations ADD multiplier DECIMAL(10,2)";
        $result = $adb->query($sql);
        if($result) {
            echo "Field 'multiplier' added to vtiger_tariffvaluations.<br/>\n";
        }else{
            echo "Failed to add field 'multiplier' to vtiger_tariffvaluations.<br/>\n";
        }
    }else{
        echo "Field 'multiplier' already exists in vtiger_tariffvaluations.<br/>\n";
    }
}

echo "Checking that table 'vtiger_quotes_valuation' exists...<br/>\n";
$sql = "SHOW TABLES LIKE 'vtiger_quotes_valuation'";
$result = $adb->query($sql);
if(!$adb->num_rows($result)) {
    echo "Table 'vtiger_quotes_valuation' does not exist, skipping adding field...<br/>\n";
}else {
    echo "Adding weight field to vtiger_quotes_valuation if it does not exist...<br/>\n";
    $sql = "SHOW COLUMNS FROM `vtiger_quotes_valuation` LIKE 'multiplier';";
    $result = $adb->query($sql);
    if(!$adb->num_rows($result)) {
        $sql = "ALTER TABLE vtiger_quotes_valuation ADD multiplier DECIMAL(10,2)";
        $result = $adb->query($sql);
        if($result) {
            echo "Field 'multiplier' added to vtiger_quotes_perunit.<br/>\n";
        }else{
            echo "Failed to add field 'multiplier' to vtiger_quotes_perunit.<br/>\n";
        }
    }else{
        echo "Field 'multiplier' already exists in vtiger_quotes_perunit.<br/>\n";
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
