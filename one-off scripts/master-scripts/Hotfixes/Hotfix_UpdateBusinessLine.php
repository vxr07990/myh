<?php
if (function_exists("call_ms_function_ver")) {
    $version = '1';
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";


/**
  *	This hotfix file is to update business_line as defined in OT Defects 12997-99 and 13042.
  * The base creation script files have also been modified to add the correct values, so this
  * hotfix file is to correct the field in versioned databases.
  */
//$Vtiger_Utils_Log = true;
//include_once('vtlib/Vtiger/Menu.php');
//include_once('vtlib/Vtiger/Module.php');

if (!isset($db)) {
    $db = PearDatabase::getInstance();
}

$current_values = array();
$valid_values = array('Local Move','Interstate Move','Intrastate Move','International Move','HHG - International Sea','National Account','Commercial Move', 'Commercial - Distribution', 'Commercial - Record Storage', 'Commercial - Storage', 'Commercial - Asset Management','Commercial - Project', 'Auto Transportation');

//ADD IN SIRVA MILITARY
if (getenv('INSTANCE_NAME') == 'sirva') {
    $valid_values[] = 'Sirva Military';
}

$max_id = 0;

//Get all current values for the business_line field
$sql = "SELECT * FROM `vtiger_business_line`";
$result = $db->pquery($sql, array());
while ($row =& $result->fetchRow()) {
    $business_line = $row['business_line'];
    if (in_array($business_line, $current_values)) {
        $db->pquery("DELETE FROM `vtiger_business_line` WHERE business_lineid=?", array($row['business_lineid']));
    } else {
        $current_values[] = $business_line;
        $max_id = $row['business_lineid'];
    }
}

//Determine which values need added and add them
$values_to_add = array_diff($valid_values, $current_values);

foreach ($values_to_add as $value) {
    $max_id++;
    $db->pquery("INSERT INTO `vtiger_business_line` VALUES (?,?,?,?)", array($max_id, $value, $max_id, 1));
}

//Update business_line_seq table to match max id of business_line table
$db->pquery("UPDATE `vtiger_business_line_seq` SET id=?", array($max_id));

//Update sortorderid field to match order of valid_values array
$sort_order = 0;
foreach ($valid_values as $value) {
    $sort_order++;
    $db->pquery("UPDATE `vtiger_business_line` SET sortorderid=? WHERE business_line=?", array($sort_order, $value));
}

//Repeat above for business_line_est table
$current_values = array();
$valid_values = array('Local Move','Interstate Move','Intrastate Move','International Move','HHG - International Sea','Commercial Move', 'Commercial - Distribution', 'Commercial - Record Storage', 'Commercial - Storage', 'Commercial - Asset Management','Commercial - Project', 'Auto Transportation');
$max_id = 0;

//Get all current values for the business_line_est field
$sql = "SELECT * FROM `vtiger_business_line_est`";
$result = $db->pquery($sql, array());
while ($row =& $result->fetchRow()) {
    $business_line = $row['business_line_est'];
    if (in_array($business_line, $current_values)) {
        $db->pquery("DELETE FROM `vtiger_business_line_est` WHERE business_line_estid=?", array($row['business_line_estid']));
    } else {
        $current_values[] = $business_line;
        $max_id = $row['business_line_estid'];
    }
}

//Determine which values need added and add them
$values_to_add = array_diff($valid_values, $current_values);

foreach ($values_to_add as $value) {
    $max_id++;
    $db->pquery("INSERT INTO `vtiger_business_line_est` VALUES (?,?,?,?)", array($max_id, $value, $max_id, 1));
}

//Update business_line_est_seq table to match max id of business_line_est table
$db->pquery("UPDATE `vtiger_business_line_est_seq` SET id=?", array($max_id));

//Update sortorderid field to match order of valid_values array
$sort_order = 0;
foreach ($valid_values as $value) {
    $sort_order++;
    $db->pquery("UPDATE `vtiger_business_line_est` SET sortorderid=? WHERE business_line_est=?", array($sort_order, $value));
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";