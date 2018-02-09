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


//include this stuff to run independent of master script
/*$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');
include_once('modules/ModComments/ModComments.php');
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
include_once 'modules/Users/Users.php';*/

if (!isset($db)) {
    $db = PearDatabase::getInstance();
}

$sql = "SELECT vtiger_ws_referencetype.fieldtypeid, type FROM `vtiger_ws_fieldtype` JOIN `vtiger_ws_referencetype` ON vtiger_ws_fieldtype.fieldtypeid=vtiger_ws_referencetype.fieldtypeid WHERE uitype=66";
$result = $db->pquery($sql, array());

$modules = array('Accounts', 'Campaigns', 'Estimates', 'HelpDesk', 'Leads', 'Opportunities');
$addedModules = array();

while ($row =& $result->fetchRow()) {
    if (!in_array($row['type'], $modules)) {
        echo "Removing ".$row['type']." from table for fieldtypeid ".$row['fieldtypeid'];
        $db->pquery("DELETE FROM `vtiger_ws_referencetype` WHERE fieldtypeid=? AND type=?", array($row['fieldtypeid'], $row['type']));
    } else {
        $addedModules[] = $row['type'];
    }
}

foreach ($modules as $moduleName) {
    if (!in_array($moduleName, $addedModules)) {
        echo "Adding $moduleName to table for fieldtypeid ".$row['fieldtypeid'];
        $db->pquery("INSERT INTO `vtiger_ws_referencetype` VALUES (?,?)", array($row['fieldtypeid'], $moduleName));
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";