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

echo "<br>start add Driver to MoveRoles<br>";

$db = PearDatabase::getInstance();

$sql = "SELECT moveroles_role FROM `vtiger_moveroles_role` WHERE moveroles_role = 'Driver'";

$typeExists = $db->pquery($sql, [])->fetchRow();

if (!$typeExists) {
    $sql = "SELECT sortorderid FROM `vtiger_moveroles_role` ORDER BY sortorderid DESC LIMIT 1";

    $highestSort = $db->pquery($sql, [])->fetchRow()['sortorderid'];

    $highestSort += 1;

    $sql = "INSERT INTO `vtiger_moveroles_role` (moveroles_role, sortorderid, presence) VALUES ('Driver', ?, 1)";

    $db->pquery($sql, [$highestSort]);
} else {
    echo '<br> Driver moveRole already exists';
}

echo "<br>end add Driver to MoveRoles";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";