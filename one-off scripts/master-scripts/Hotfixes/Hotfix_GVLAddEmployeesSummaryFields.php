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

echo "<br>begin hotfix alter employees summary fields<br>";

$db = PearDatabase::getInstance();

$result = $db->pquery("SELECT tabid FROM `vtiger_tab` WHERE name  = 'Employees'", []);

if ($result) {
    $employeeTabId = $result->fetchRow()['tabid'];
    $db->pquery("UPDATE `vtiger_field` SET summaryfield = 1 WHERE tabid = ? AND fieldname IN ( 
        'employee_lastname', 
        'employee_prole', 
        'employee_email'
      )", [$employeeTabId]);
} else {
    echo 'employees tabid not found, no action taken.<br>';
}

echo "<br>end hotfix alter employees summary fields<br>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";