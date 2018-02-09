<?php
/**
 * Created by PhpStorm.
 * User: mmuir
 * Date: 11/11/2016
 * Time: 5:16 PM
 */

if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";


$moduleInstance = Vtiger_Module::getInstance('VehicleMaintenance');
if (!$moduleInstance) {
    print "VehicleMaintenance not found. Exiting<br/>\n";
    return;
}

print "Checking if Default Sharing set up.<br/>\n";
$db = PearDatabase::getInstance();
$sql = 'SELECT * FROM vtiger_def_org_share INNER JOIN vtiger_tab ON vtiger_tab.tabid = vtiger_def_org_share.tabid WHERE name=?';
$result = $db->pquery($sql, ['VehicleMaintenance']);

if ($db->num_rows($result) == 0) {
    print "Default Sharing not set up. Setting<br/>\n";
    $moduleInstance->setDefaultSharing();
    print "Completed<br/>\n";
}

$sql2 = 'SELECT * FROM vtiger_ws_entity WHERE name =?';
$result2 = $db->pquery($sql2, ['VehicleMaintenance']);
print "Checking if Web Service initialized.<br/>\n";
if ($db->num_rows($result2) == 0) {
    print "Web Service not set up. Initializing<br/>\n";
    $moduleInstance->initWebservice();
    print "Completed<br/>\n";
}
print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
