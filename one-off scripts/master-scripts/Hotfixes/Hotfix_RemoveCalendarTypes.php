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



//include_once('vtlib/Vtiger/Menu.php');

echo '<h1>Begin Hotfix Remove Calendar Types</h1><br>';

function removeCalendarType($type)
{
    echo 'Removing type: '.$type.'<br>';
    $db = PearDatabase::getInstance();
    //Vtiger_Utils::ExecuteQuery('DELETE FROM `vtiger_calendar_user_activitytypes` JOIN `vtiger_calendar_default_activitytypes` ON `vtiger_calendar_user_activitytypes`.defaultid = `vtiger_calendar_default_activitytypes`.id WHERE `vtiger_calendar_default_activitytypes`.fieldname = '.$task);
    //Vtiger_Utils::ExecuteQuery('DELETE FROM `vtiger_calendar_default_activitytypes` WHERE fieldname = '.$task);
    $activityId = false;
    $sql = 'SELECT id FROM `vtiger_calendar_default_activitytypes` WHERE fieldname = ?';
    $result = $db->pquery($sql, [$type]);
    $row = $result->fetchRow();
    if ($row) {
        //get defaultid for activity type
        $activityId = $row[0];
    } else {
        //spit an error if the type isn't found in defaults
        echo '<h1 style="color:red">An error occured, could not find type '.$type.'</h1><br>';
        echo '<h3 style="color:red">If this is your first time running this script then something is horribly wrong, otherwise ignore this.</h3><br>';
        return;
    }
    
    //remove the activity type from default and all users

    $sql = 'DELETE FROM `vtiger_calendar_user_activitytypes` WHERE defaultid = ?';
    $result = $db->pquery($sql, [$type]);
    
    $sql = 'DELETE FROM `vtiger_calendar_default_activitytypes` WHERE fieldname = ?';
    $result = $db->pquery($sql, [$type]);
    
    
    echo $type.' removed!<br>';
}

$typesToRemove = ['Project Task', 'Project', 'Invoice', 'support_end_date'];

foreach ($typesToRemove as $type) {
    removeCalendarType($type);
}

echo '<h1>End Hotfix Remove Calendar Types</h1><br>';

echo '<h1>Adding Survey Appointments to Calendar Types</h1><br>';

$sql = "SELECT * FROM `vtiger_calendar_default_activitytypes` WHERE `module`='Surveys'";
$result = $db->pquery($sql, []);
if ($result->numRows() > 0) {
    //Do nothing
} else {
    $sql    = "UDPATE `vtiger_calendar_default_activitytypes_seq` SET id=id+1";
    $db->pquery($sql, []);
    $sql = "SELECT id FROM `vtiger_calendar_default_activitytypes_seq`";
    $result = $db->pquery($sql, []);
    $row    = $result->fetchRow();
    $id     = $row['id'];

    $sql = "INSERT INTO `vtiger_calendar_default_activitytypes` VALUES (?,?,?,?)";
    $db->pquery($sql, [$id, 'Surveys', 'Surveys', '#81C784']);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";