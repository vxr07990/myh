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
include_once('include/database/PearDatabase.php');

if(!$db) {
    $db = PearDatabase::getInstance();
}

$field = "agentid";
$table = "TariffReportSections";

$sql = "SELECT fieldid, typeofdata FROM vtiger_field JOIN vtiger_tab ON vtiger_field.tabid = vtiger_tab.tabid WHERE fieldname = ? AND vtiger_tab.name = ?";
if($res = $db->pquery($sql, [$field, $table])) {
    while($row = $res->fetchRow()) {
        $id = $row['fieldid'];
        $tod = $row['typeofdata'];

        updateTypeOfData($tod, 'M', 'O');

        $sql = "UPDATE vtiger_field SET typeofdata = ? WHERE fieldid = ?";
        if(!$db->pquery($sql, [$tod, $id])) {
            echo "Unable to update the Type Of Data column for field of ID " . $id . ".<br/>\n";
        }
    }
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
