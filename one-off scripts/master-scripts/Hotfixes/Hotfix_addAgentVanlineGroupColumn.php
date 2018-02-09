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


// include_once('vtlib/Vtiger/Menu.php');
// include_once('vtlib/Vtiger/Module.php');
// include_once('modules/ModTracker/ModTracker.php');
// include_once('modules/ModComments/ModComments.php');
// include_once 'includes/main/WebUI.php';
// include_once 'include/Webservices/Create.php';
// include_once 'modules/Users/Users.php';

echo '<h1>Begin Hotfix: add groupid column to agentman & vanlineman</h1><br>';

global $adb;
$db = PearDatabase::getInstance();
$agentColumns = $adb->getColumnNames('vtiger_agentmanager');
$vanlineColumns = $adb->getColumnNames('vtiger_vanlinemanager');
$vanlines = [];
$agents = [];

//make a note of if the column already exists or if this is a first time run (agentman)
if (!in_array('groupid', $agentColumns) || $_REQUEST['update'] == 'true') {
    $updateAgents = true;
} else {
    $updateAgents = false;
}

//make a note of if the column already exists or if this is a first time run (vanlineman)
if (!in_array('groupid', $vanlineColumns) || $_REQUEST['update'] == 'true') {
    $updateVanlines = true;
} else {
    $updateVanlines = false;
}


//add the column
Vtiger_Utils::addColumn('vtiger_agentmanager', 'groupid', 'INT(11)');
Vtiger_Utils::addColumn('vtiger_vanlinemanager', 'groupid', 'INT(11)');

//grab all agents
$agentSql = 'SELECT agentmanagerid, agency_name FROM vtiger_agentmanager';
$result = $db->pquery($agentSql, []);
$row = $result->fetchRow();
while ($row != null) {
    $agents[$row['agentmanagerid']] = $row['agency_name'];
    $row = $result->fetchRow();
}

//grab all vanlines
$vanlineSql = 'SELECT vanlinemanagerid, vanline_name FROM vtiger_vanlinemanager';
$result = $db->pquery($vanlineSql, []);
$row = $result->fetchRow();
while ($row != null) {
    $vanlines[$row['vanlinemanagerid']] = $row['vanline_name'];
    $row = $result->fetchRow();
}

// file_put_contents('logs/devLog.log', "\n agents: ".print_r($agents, true), FILE_APPEND);
// file_put_contents('logs/devLog.log', "\n agent columns: ".print_r($agentColumns, true), FILE_APPEND);
// file_put_contents('logs/devLog.log', "\n vanlines: ".print_r($vanlines, true), FILE_APPEND);
// file_put_contents('logs/devLog.log', "\n vanline columns: ".print_r($vanlineColumns, true), FILE_APPEND);

if ($updateAgents == true) {
    foreach ($agents as $agentId => $agentName) {
        updateAgent($agentId, $agentName);
    }
}

if ($updateVanlines == true) {
    foreach ($vanlines as $vanlineId => $vanlineName) {
        updateVanline($vanlineId, $vanlineName);
    }
}

function updateAgent($agentId, $agentName)
{
    $db = PearDatabase::getInstance();
    echo "<h1>Updating: $agentName (ID: $agentId)</h1><br>";
    //grab groupId
    $sql = 'SELECT groupid FROM `vtiger_groups` WHERE groupname = ?';
    $result = $db->pquery($sql, [$agentName]);
    $row = $result->fetchRow();
    $groupId = $row['groupid'];
    echo "<h1>Found Group! : (ID: $groupId)</h1><br>";
    //update agentmanager row
    $sql = 'UPDATE `vtiger_agentmanager` SET groupid = ? WHERE agentmanagerid = ?';
    $result = $db->pquery($sql, [$groupId, $agentId]);
}

function updateVanline($vanlineId, $vanlineName)
{
    $db = PearDatabase::getInstance();
    echo "<h1>Updating: $vanlineName (ID: $vanlineId)</h1><br>";
    //grab groupId
    $sql = 'SELECT groupid FROM `vtiger_groups` WHERE groupname = ?';
    $result = $db->pquery($sql, [$vanlineName]);
    $row = $result->fetchRow();
    $groupId = $row['groupid'];
    echo "<h1>Found Group! : (ID: $groupId)</h1><br>";
    //update agentmanager row
    $sql = 'UPDATE `vtiger_vanlinemanager` SET groupid = ? WHERE vanlinemanagerid = ?';
    $result = $db->pquery($sql, [$groupId, $vanlineId]);
}

echo '<h1>End Hotfix: add groupid column to agentman & vanlineman</h1><br>';


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";