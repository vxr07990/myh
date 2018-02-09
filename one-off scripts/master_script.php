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


/*

    MasterScript will call scripts in (one-off scripts/master-scripts) and will create all modules of move CRM and move HQ in the database.
    Master Script is build to be run in a clean vtiger Database with a clean Source Code.

*/
$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');
include_once('modules/ModComments/ModComments.php');

$movehq = 0;    //change this to change branding for the site 1 = moveHQ, 0 = moveCRM
                //only matters for first run otherwise it does what is already in the database
$db_version = '0.0.0';
$db = PearDatabase::getInstance();
$sql = "SELECT movehq, db_version FROM `database_version`";
$result = $db->pquery($sql, array());
if ($result != null) {
    $row = $result->fetchRow();
    $movehq = $row[0];
    $db_version = $row[1];
}

if ($db_version < '0.1.0') {
    echo "<br><h1>UPDATING TO 0.1.0</h1><br>";
    // Scripts to add moveCRM and moveHQ modules. Scripts are created to run in this order
    require_once('one-off scripts/master-scripts/Create_Version_Table.php');
    Vtiger_Utils::ExecuteQuery("INSERT INTO `database_version` (`movehq`, `db_version`) VALUES ($movehq, '0.1.0')");
} if ($db_version < '0.2.0') {
    echo "<br><h1>UPDATING TO 0.2.0</h1><br>";
    require_once('one-off scripts/master-scripts/Modify_Leads.php');
    require_once('one-off scripts/master-scripts/update_contacts.php');
    require_once('one-off scripts/master-scripts/Update_quotes_20150608.php');
    require_once('one-off scripts/master-scripts/Update_potentials_20150609.php');
    Vtiger_Utils::ExecuteQuery("UPDATE `database_version` SET `movehq` = $movehq ,`db_version` = '0.2.0'");
    $db_version = '0.2.0';
} if ($db_version < '0.3.0') {
    echo "<br><h1>UPDATING TO 0.3.0</h1><br>";
    
    require_once('one-off scripts/master-scripts/Vanlines_20150514.php');
    require_once('one-off scripts/master-scripts/Agents_20150514.php');
    require_once('one-off scripts/master-scripts/VanlineManager_AgentManager_20150604.php');
    require_once('one-off scripts/master-scripts/Users2Vanline20150624.php');
    Vtiger_Utils::ExecuteQuery("UPDATE `database_version` SET `movehq` = $movehq ,`db_version` = '0.3.0'");
    $db_version = '0.3.0';
} if ($db_version < '0.4.0') {
    require_once('one-off scripts/master-scripts/Create_Counties_and_States.php');
    require_once('one-off scripts/master-scripts/Tariffmanager_20150603.php');
    require_once('one-off scripts/master-scripts/LocalServices20150601.php');
    require_once('one-off scripts/master-scripts/Tariffs_20150513.php');
    Vtiger_Utils::ExecuteQuery("UPDATE `database_version` SET `movehq` = $movehq ,`db_version` = '0.4.0'");
    $db_version = '0.4.0';
} if ($db_version < '0.5.0') {
    echo "<br><h1>UPDATING TO 0.4.0</h1><br>";
    require_once('one-off scripts/master-scripts/create_estimates_module.php');
    require_once('one-off scripts/master-scripts/Estimates_EntityName.php'); //adds estimates to entity name since
                                                                             //decoupled modules don't do this on their own
    require_once('one-off scripts/master-scripts/create_opportunities_20150609.php');
    require_once('one-off scripts/master-scripts/Opp_EntityName.php'); //adds estimates to entity name since
                                                                       //decoupled modules don't do this on their own
    require_once('one-off scripts/master-scripts/Stops_20150514.php');
    require_once('one-off scripts/master-scripts/Surveys_20150603.php');
    require_once('one-off scripts/master-scripts/Cubesheets_20150603.php');
    Vtiger_Utils::ExecuteQuery("UPDATE `database_version` SET `movehq` = $movehq ,`db_version` = '0.5.0'");
    $db_version = '0.5.0';
} if ($db_version < '0.6.0') {
    echo "<br><h1>UPDATING TO 0.5.0</h1><br>";
    require_once('one-off scripts/master-scripts/Employees_20150515.php');
    require_once('one-off scripts/master-scripts/TimeOff_20150605.php');
    require_once('one-off scripts/master-scripts/MoveRoles_20150605.php');
    require_once('one-off scripts/master-scripts/Vehicles_20150518.php');
    Vtiger_Utils::ExecuteQuery("UPDATE `database_version` SET `movehq` = $movehq ,`db_version` = '0.6.0'");
    $db_version = '0.6.0';
} if ($db_version < '0.7.0') {
    require_once('one-off scripts/master-scripts/create_orders.php');
    require_once('one-off scripts/master-scripts/Claims_20150515.php');
    require_once('one-off scripts/master-scripts/Equipment_20150518.php');
    require_once('one-off scripts/master-scripts/Accidents_20150605.php');
    require_once('one-off scripts/master-scripts/Storage_20150605.php');
    Vtiger_Utils::ExecuteQuery("UPDATE `database_version` SET `movehq` = $movehq ,`db_version` = '0.7.0'");
    $db_version = '0.7.0';
} if ($db_version < '0.8.0') {
    require_once('one-off scripts/master-scripts/Timesheets_20150605.php');
    //require_once('one-off scripts/master-scripts/Trips.php'); dont have it yet
    //require_once('one-off scripts/master-scripts/WeeklyTimeSheet.php');  dont have it yet
    Vtiger_Utils::ExecuteQuery("UPDATE `database_version` SET `movehq` = $movehq ,`db_version` = '0.8.0'");
    $db_version = '0.8.0';
}
//always fix the menus
require_once('one-off scripts/master-scripts/Fix_Tabs.php');
echo "<h1> Master Script Completed Successfully Updated to $db_version </h1>";
