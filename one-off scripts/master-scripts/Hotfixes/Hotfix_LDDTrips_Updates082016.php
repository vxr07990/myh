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



if (!function_exists('moveFieldsToBlock')) {
    function moveFieldsToBlock($module, $block, $fieldNames)
    {
        if ($module == null || $block == null) {
            echo 'Module or Block are null<br>';
        } else {
            $db = PearDatabase::getInstance();
            $moduleId = $module->id;
            $blockId = $block->id;
            $sql = 'SELECT MAX(sequence) FROM vtiger_field WHERE tabid=? AND block=?';
            $result = $db->pquery($sql, array($moduleId, $blockId));
            if ($result && $db->num_rows($result) > 0) {
                $maxSequence = $result->fetchRow()[0];
            }
            if ($maxSequence == null) {
                $maxSequence = 0;
            }
            echo 'Max sequence: ' . $maxSequence . '<br>';

            foreach ($fieldNames as $index => $fieldName) {
                $sequence = $maxSequence + $index + 1;
                $sql = "UPDATE vtiger_field SET sequence=$sequence, block=$blockId WHERE tabid=$moduleId AND fieldname='$fieldName'";
                Vtiger_Utils::ExecuteQuery($sql);
            }
        }
    }
}

global $adb;

//--bugfix to ServiceHours module--
echo 'Fixing typeofdata of trips_id of vtiger_servicehours table<br>';
$query = 'UPDATE vtiger_field SET typeofdata="V~O" WHERE tablename="vtiger_servicehours" AND columnname="trips_id"';
Vtiger_Utils::ExecuteQuery($query);
echo 'OK<br>';
echo '<br>';

//add modentity entry for existing field servhours_id(uitype4)
$result = $adb->pquery("SELECT * FROM vtiger_modentity_num WHERE semodule='Trips'");
if ($result && $adb->num_rows($result) == 0) {
    echo 'Add vtiger_modentity_num entry for existing field servhours_id(uitype4)<br>';

    $numid = $adb->getUniqueId("vtiger_modentity_num");
    $adb->pquery("INSERT into vtiger_modentity_num values(?,?,?,?,?,?)", array($numid, 'ServiceHours', 'SERHRS', 1, 1, 1));
    echo 'OK<br>';
}

$tripsInstance = Vtiger_Module::getInstance('Trips');

if (!$tripsInstance) {
    echo 'Trips Module not present<br>';
} else {
    $blockEquipmentInstance = Vtiger_Block::getInstance('LBL_TRIPS_EQUIPMENT_INFORMATION', $tripsInstance);
    if ($blockEquipmentInstance) {
        echo 'block LBL_TRIPS_EQUIPMENT_INFORMATION alredy present<br>';
    } else {
        echo 'Creating block LBL_TRIPS_EQUIPMENT_INFORMATION <br>';
        // Field Setup
        $blockEquipmentInstance = new Vtiger_Block();
        $blockEquipmentInstance->label = 'LBL_TRIPS_EQUIPMENT_INFORMATION';
        $tripsInstance->addBlock($blockEquipmentInstance);
        echo 'OK<br>';
        echo '<br>';
    }

    if ($blockEquipmentInstance) {
        $blockEquipmentID = $blockEquipmentInstance->id;

        echo $blockEquipmentID . '<br>';
        $vehiclesFields = array(
            'trips_vehicle',
            'trips_vehi_cube',
            'trips_vehi_length',
        );
        echo 'Moving vehicle fields<br>';
        moveFieldsToBlock($tripsInstance, $blockEquipmentInstance, $vehiclesFields);
    }

    $blockEquipmentInstance = Vtiger_Block::getInstance('LBL_TRIPS_EQUIPMENT_INFORMATION', $tripsInstance);
    if (!$blockEquipmentInstance) {
        echo 'block LBL_TRIPS_EQUIPMENT_INFORMATION not present<br>';
    } else {
        $field34 = Vtiger_Field::getInstance('trips_trailer', $tripsInstance);
        if (!$field34) {
            $field34 = new Vtiger_Field();
            $field34->label = 'LBL_TRIPS_TRAILER';
            $field34->name = 'trips_trailer';
            $field34->table = 'vtiger_trips';
            $field34->column = $field34->name;
            $field34->columntype = 'INT(10)';
            $field34->uitype = 10;
            $field34->typeofdata = 'I~O';

            $blockEquipmentInstance->addField($field34);
            $field34->setRelatedModules(array('Vehicles'));
        }

        $field341 = Vtiger_Field::getInstance('trips_trailer_cube', $tripsInstance);
        if (!$field341) {
            $field341 = new Vtiger_Field();
            $field341->label = 'LBL_TRIPS_TRAILER_CUBE';
            $field341->name = 'trips_trailer_cube';
            $field341->table = 'vtiger_trips';
            $field341->column = $field341->name;
            $field341->columntype = 'VARCHAR(100)';
            $field341->uitype = 2;
            $field341->typeofdata = 'V~O';

            $blockEquipmentInstance->addField($field341);
        }

        $field342 = Vtiger_Field::getInstance('trips_trailer_length', $tripsInstance);
        if (!$field342) {
            $field342 = new Vtiger_Field();
            $field342->label = 'LBL_TRIPS_TRAILER_LENGTH';
            $field342->name = 'trips_trailer_length';
            $field342->table = 'vtiger_trips';
            $field342->column = $field342->name;
            $field342->columntype = 'VARCHAR(100)';
            $field342->uitype = 2;
            $field342->typeofdata = 'V~O';

            $blockEquipmentInstance->addField($field342);
        }
    }

    $blockDriverInstance = Vtiger_Block::getInstance('LBL_TRIPS_DRIVER', $tripsInstance);
    if (!$blockDriverInstance) {
        echo 'Block LBL_TRIPS_DRIVER not present<br>';
    } else {
        echo 'Moving various fields to block ' . $blockDriverInstance->label . '<br>';
        $fieldNames = array(
            'trips_driverlastname',
            'trips_driverfirstname',
            'trips_driverno',
            'trips_drivercellphone',
            'trips_driversemail',
            'checkin',
            'checkin_notes'
        );
        moveFieldsToBlock($tripsInstance, $blockDriverInstance, $fieldNames);
    }

    $blockDriverInstance = Vtiger_Block::getInstance('LBL_TRIPS_DRIVER', $tripsInstance);
    if (!$blockDriverInstance) {
        echo 'Block LBL_TRIPS_DRIVER not present<br>';
    } else {
        //adding missing fields
        echo 'Adding missing fields to Block LBL_TRIPS_DRIVER of Module Trips<br>';
        $field1 = Vtiger_Field::getInstance('fleet_status', $tripsInstance);
        if ($field1) {
            echo 'The field fleet_status alredy exists <br>';
        } else {
            $field1 = new Vtiger_Field();
            $field1->label = 'Fleet Status';
            $field1->name = 'fleet_status';
            $field1->table = 'vtiger_trips';
            $field1->column = $field1->name;
            $field1->uitype = 15;
            $field1->columntype = 'VARCHAR(255)';
            $field1->typeofdata = 'V~O';
            $blockDriverInstance->addField($field1);
            $field1->setPicklistValues(array('Committed', 'Uncommitted'));
        }
        $field2 = Vtiger_Field::getInstance('hauling_radius', $tripsInstance);
        if ($field2) {
            echo 'The field hauling_radius alredy exists <br>';
        } else {
            $field2 = new Vtiger_Field();
            $field2->label = 'Hauling Radius';
            $field2->name = 'hauling_radius';
            $field2->table = 'vtiger_trips';
            $field2->column = $field2->name;
            $field2->uitype = 15;
            $field2->columntype = 'VARCHAR(255)';
            $field2->typeofdata = 'V~O';
            $blockDriverInstance->addField($field2);
            $field2->setPicklistValues(array('Short haul', 'Long haul'));
        }

        echo 'OK<br>';
    }

    $block2 = Vtiger_Block::getInstance('LBL_TRIPS_INFORMATION', $tripsInstance);
    $field02 = Vtiger_Field::getInstance('trips_committedstatus', $tripsInstance);
    if (!$field02) {
        echo "Creating Field committedstatus in Trips</br>";
        $field2 = new Vtiger_Field();
        $field2->name = 'trips_committedstatus';
        $field2->label = 'Committed Status';
        $field2->uitype = 15;
        $field2->table = 'vtiger_trips';
        $field2->column = $field2->name;
        $field2->columntype = 'VARCHAR(255)';
        $field2->typeofdata = 'V~O';
        $field2->setPicklistValues(array('Committed', 'Uncommitted'));
        $block2->addField($field2);
        echo "OK adding Committed Status Field in Trips</br>";
    } else {
        echo "Field committedstatus already exists in Trips</br>";
    }



    $field11 = Vtiger_Field::getInstance('driver_id', $tripsInstance);
    if ($field11) {
        $adb->pquery("UPDATE vtiger_field SET typeofdata = 'V~O' WHERE columnname = 'driver_id' AND tablename = 'vtiger_trips'", array());
    }

    $field21 = Vtiger_Field::getInstance('trips_drivercellphone', $tripsInstance);
    if ($field21 && $field21->uitype != 11) {
        //Need to make this field as phone
        Vtiger_Utils::ExecuteQuery("UPDATE vtiger_field SET uitype='11' WHERE fieldname='trips_drivercellphone'");
    }
}


// Add relationship to orders
global $adb;
$serviceHoursModuleInstance = Vtiger_Module::getInstance('ServiceHours');
$result = $adb->pquery("SELECT * FROM vtiger_relatedlists WHERE tabid=? AND related_tabid=?", array($tripsInstance->id, $serviceHoursModuleInstance->id));

if ($result && $adb->num_rows($result) == 0) {
    $tripsInstance->setRelatedList(Vtiger_Module::getInstance('ServiceHours'), 'Service Hours', array('SELECT'), 'get_dependents_list');
}

$result = $adb->pquery("SELECT * FROM vtiger_relatedlists WHERE tabid=? AND related_tabid=?", array($tripsInstance->id, $tripsInstance->id));

if ($result && $adb->num_rows($result) == 0) {
    $tripsInstance->setRelatedList(Vtiger_Module::getInstance('Trips'), 'Trips', array('SELECT'), 'get_trips');
}

$ordersModuleInstance = Vtiger_Module::getInstance('Orders');
$result = $adb->pquery("SELECT * FROM vtiger_relatedlists WHERE tabid=? AND related_tabid=?", array($tripsInstance->id, $ordersModuleInstance->id));
if ($result && $adb->num_rows($result) > 1) {
    $tripsInstance->unsetRelatedList(Vtiger_Module::getInstance('Orders'));
    $tripsInstance->setRelatedList(Vtiger_Module::getInstance('Orders'), 'Orders', array('SELECT'), 'get_related_list');
}

$employeesModuleInstance = Vtiger_Module::getInstance('Employees');
$result = $adb->pquery("SELECT * FROM vtiger_relatedlists WHERE tabid=? AND related_tabid=?", array($employeesModuleInstance->id, $tripsInstance->id));
if ($result && $adb->num_rows($result) == 0) {
    $employeesModuleInstance->setRelatedList(Vtiger_Module::getInstance('Trips'), 'Trips', array('SELECT'), 'get_dependents_list');
}


//add ModComments Widget
$commentsModule = Vtiger_Module::getInstance('ModComments');
$fieldInstance = Vtiger_Field::getInstance('related_to', $commentsModule);
$fieldInstance->setRelatedModules(array('Trips'));      //vtlib validates if the relationships is already in there
ModComments::addWidgetTo('Trips');                      //vtlib validates if the relationships is already in there
// Adding Summary View fields to be shown on Trips Related List

Vtiger_Utils::ExecuteQuery("UPDATE vtiger_field SET summaryfield = 1 WHERE ("
        . "columnname = 'checkin' OR "
        . "columnname = 'driver_id' OR "
        . "columnname = 'intransitzone' OR "
        . "columnname = 'origin_zone' OR "
        . "columnname = 'total_line_haul' OR "
        . "columnname = 'trips_days' OR "
        . "columnname = 'trips_firstload' OR "
        . "columnname = 'trips_id' OR "
        . "columnname = 'trips_status')");


Vtiger_Utils::ExecuteQuery("UPDATE vtiger_field SET summaryfield = 1 WHERE ("
        . "columnname = 'vechiles_unit' OR "
        . "columnname = 'vehicles_agent_no' OR "
        . "columnname = 'vehicle_status' OR "
        . "columnname = 'vehicle_type' OR "
        . "columnname = 'vechiles_no')");


// Adding Summary View fields to be shown on Trips Related List
Vtiger_Utils::ExecuteQuery("UPDATE vtiger_field SET quickcreate = 2 WHERE tablename = 'vtiger_servicehours'");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";