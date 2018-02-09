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

echo "Checking that table 'vtiger_quotes_perunit' exists...<br/>\n";
$sql = "SHOW TABLES LIKE 'vtiger_quotes_perunit'";
$result = $adb->query($sql);
if(!$adb->num_rows($result)) {
    echo "Table 'vtiger_quotes_perunit' does not exist, skipping adding field...<br/>\n";
}else {
    echo "Adding weight field to vtiger_quotes_perunit if it does not exist...<br/>\n";
    $sql = "SHOW COLUMNS FROM `vtiger_quotes_perunit` LIKE 'flag';";
    $result = $adb->query($sql);
    if(!$adb->num_rows($result)) {
        $sql = "ALTER TABLE vtiger_quotes_perunit ADD flag TINYINT(1)";
        $result = $adb->query($sql);
        if($result) {
            echo "Field 'flag' added to vtiger_quotes_perunit.<br/>\n";
        }else{
            echo "Failed to add field 'flag' to vtiger_quotes_perunit.<br/>\n";
        }
    }else{
        echo "Field 'flag' already exists in vtiger_quotes_perunit.<br/>\n";
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
