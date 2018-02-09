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

echo "<br>begin re-add nat acct.<br>";

$db = PearDatabase::getInstance();

$sql = "SELECT business_line FROM `vtiger_business_line` WHERE business_line = 'National Account'";

$typeExists = $db->pquery($sql, [])->fetchRow();

if (!$typeExists) {
    $sql = "SELECT sortorderid FROM `vtiger_business_line` ORDER BY sortorderid DESC LIMIT 1";

    $highestSort = $db->pquery($sql, [])->fetchRow()['sortorderid'];

    $highestSort += 1;

    $sql = "INSERT INTO `vtiger_business_line` (business_line, sortorderid, presence) VALUES ('National Account', ?, 1)";

    $db->pquery($sql, [$highestSort]);
} else {
    echo '<br>National Account business line already exists';
}

$sql = "SELECT business_line_est FROM `vtiger_business_line_est` WHERE business_line_est = 'National Account'";

$typeExists = $db->pquery($sql, [])->fetchRow();

if (!$typeExists) {
    $sql = "SELECT sortorderid FROM `vtiger_business_line_est` ORDER BY sortorderid DESC LIMIT 1";

    $highestSort = $db->pquery($sql, [])->fetchRow()['sortorderid'];

    $highestSort += 1;

    $sql = "INSERT INTO `vtiger_business_line_est` (business_line_est, sortorderid, presence) VALUES ('National Account', ?, 1)";

    $db->pquery($sql, [$highestSort]);
} else {
    echo '<br>National Account business line est already exists';
}

echo "<br>end re-add nat acct";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";