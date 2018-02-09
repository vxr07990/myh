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



include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$db = PearDatabase::getInstance();

$module = Vtiger_Module::getInstance('Vehicles');
if ($module) {
    $field = Vtiger_Field::getInstance('vehicle_platetype', $module);
    if ($field) {
        $fieldid = $field->id;
        $stmt    = 'UPDATE `vtiger_field` SET fieldlabel = "LBL_VEHICLES_PLATE_TYPE" WHERE tablename = "vtiger_vehicles" AND fieldname = "vehicle_platetype" AND fieldlabel = "LBL_VEHICLES_PLATE_EXPIRATION" 
AND fieldid='.$fieldid;
        $db->pquery($stmt);
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";