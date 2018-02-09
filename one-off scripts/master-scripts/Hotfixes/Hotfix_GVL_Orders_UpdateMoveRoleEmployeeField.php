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


// OT 16109, display first and last names in Employee Field in Move Roles. Pulls from Employees entity.


$adb = PearDatabase::getInstance();
$module = 'Employees';
$table = 'vtiger_employees';
$newFieldname = 'name,employee_lastname';
echo "Udating vtiger_employees fieldname for employees to include first and last name of employee.";

$result = $adb->pquery("SELECT tabid FROM vtiger_entityname WHERE tablename=? AND modulename=?", [$table, $module]);
if ($result) {
    $adb->pquery("UPDATE vtiger_entityname SET fieldname=? WHERE tablename=? AND modulename=?", [$newFieldname, $table, $module]);
    echo "Updating entity identifier ... DONE";
} else {
    echo "Could not find field \n";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";