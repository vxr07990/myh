<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 2/8/2017
 * Time: 11:22 AM
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
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
$db = &PearDatabase::getInstance();
$db->pquery('DELETE FROM vtiger_vehicle_status WHERE vehicle_status=?',
            ['Out of Service']);
$db->pquery('UPDATE vtiger_vehicles SET vehicle_status=? WHERE vehicle_status=?',
            ['Active', 'Out of Service']);
$res = $db->pquery('SELECT * FROM vtiger_vehicle_status WHERE vehicle_status=?',
                   ['Decommissioned']);
if($res && $db->num_rows($res) == 0)
{
    $module = Vtiger_Module::getInstance('Vehicles');
    if($module) {
        $field = Vtiger_Field::getInstance('vehicle_status', $module);
        if($field)
        {
            $field->setPicklistValues(['Decommissioned']);
            $db->pquery('UPDATE vtiger_vehicle_status SET sortorderid = ((SELECT s1 FROM (SELECT MAX(sortorderid) AS s1 FROM vtiger_vehicle_status) AS s2) + 1) WHERE vehicle_status=?'
                ,['Decommissioned']);
        }
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";