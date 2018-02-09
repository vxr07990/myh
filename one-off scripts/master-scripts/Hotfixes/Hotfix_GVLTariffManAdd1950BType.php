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


//$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

echo "<br>begin add 1950-B<br>";

$db = PearDatabase::getInstance();

$sql = "SELECT custom_tariff_type FROM `vtiger_custom_tariff_type` WHERE custom_tariff_type = '1950-B'";

$tariffExists = $db->pquery($sql, [])->fetchRow();

if (!$tariffExists) {
    $sql = "SELECT sortorderid FROM `vtiger_custom_tariff_type` ORDER BY sortorderid DESC LIMIT 1";

    $highestSort = $db->pquery($sql, [])->fetchRow()['sortorderid'];

    $highestSort += 1;

    $sql = "INSERT INTO `vtiger_custom_tariff_type` (custom_tariff_type, sortorderid, presence) VALUES ('1950-B', ?, 1)";

    $db->pquery($sql, [$highestSort]);
} else {
    echo '<br>1950-B custom tariff type already exists';
}

echo "<br>end add 1950-B";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";