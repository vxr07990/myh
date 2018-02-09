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


//$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

echo "<br>begin add billing types<br>";

$db = PearDatabase::getInstance();

$sql = "SELECT billing_type FROM `vtiger_billing_type` WHERE billing_type = 'WPS'";

$typeExists = $db->pquery($sql, [])->fetchRow();

if (!$typeExists) {
    $sql = "SELECT sortorderid FROM `vtiger_billing_type` ORDER BY sortorderid DESC LIMIT 1";

    $highestSort = $db->pquery($sql, [])->fetchRow()['sortorderid'];

    $highestSort += 1;

    $sql = "INSERT INTO `vtiger_billing_type` (billing_type, sortorderid, presence) VALUES ('WPS', ?, 1)";

    $db->pquery($sql, [$highestSort]);
} else {
    echo '<br>WPS billing type already exists';
}

$sql = "SELECT billing_type FROM `vtiger_billing_type` WHERE billing_type = 'Logistics'";

$typeExists = $db->pquery($sql, [])->fetchRow();

if (!$typeExists) {
    $sql = "SELECT sortorderid FROM `vtiger_billing_type` ORDER BY sortorderid DESC LIMIT 1";

    $highestSort = $db->pquery($sql, [])->fetchRow()['sortorderid'];

    $highestSort += 1;

    $sql = "INSERT INTO `vtiger_billing_type` (billing_type, sortorderid, presence) VALUES ('Logistics', ?, 1)";

    $db->pquery($sql, [$highestSort]);
} else {
    echo '<br>Logistics billing type already exists';
}

$sql = "SELECT billing_type FROM `vtiger_billing_type` WHERE billing_type = 'Single Factor National Account'";

$typeExists = $db->pquery($sql, [])->fetchRow();

if (!$typeExists) {
    $sql = "SELECT sortorderid FROM `vtiger_billing_type` ORDER BY sortorderid DESC LIMIT 1";

    $highestSort = $db->pquery($sql, [])->fetchRow()['sortorderid'];

    $highestSort += 1;

    $sql = "INSERT INTO `vtiger_billing_type` (billing_type, sortorderid, presence) VALUES ('Single Factor National Account', ?, 1)";

    $db->pquery($sql, [$highestSort]);
} else {
    echo '<br>Single Factor National Account billing type already exists';
}

echo "<br>end add billing types";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";