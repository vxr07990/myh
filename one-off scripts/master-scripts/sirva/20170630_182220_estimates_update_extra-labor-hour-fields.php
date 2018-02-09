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

$fieldNames = [
    '"acc_exlabor_origin_hours"',
    '"acc_exlabor_dest_hours"',
    '"acc_exlabor_ot_origin_hours"',
    '"acc_exlabor_ot_dest_hours"'
];

$sql = "SELECT fieldid, typeofdata FROM vtiger_field WHERE fieldname IN (".implode(",",$fieldNames).")";
$res = $db->query($sql);
if($res) {
    while($row = $res->fetchRow()) {
        // Search to see if the field has a step.
        $tod = $row['typeofdata'];
        if(strpos('STEP', $tod) === false) {
            $tod .= "~STEP=0.01";

            // Update APN field to be mandatory
            $sql = "UPDATE vtiger_field SET typeofdata=? WHERE fieldid=?";
            $db->pquery($sql, [$tod, $row['fieldid']]);
        }
    }
}else {
    echo "An error occurred trying to get the fields. Check MySQL fail log.<br/>\n";
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
