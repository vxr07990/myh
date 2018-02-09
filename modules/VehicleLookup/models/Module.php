<?php
class VehicleLookup_Module_Model extends Vtiger_Module_Model
{
//    public static function saveVehicles(Vtiger_Request $request)
//    {
//        $db = PearDatabase::getInstance();
//
//        $recordId = $request->get('record');
//
//        file_put_contents('logs/vehicleSave.log', date('Y-m-d H:i:s - ').print_r($request, true)."\n", FILE_APPEND);
//        foreach ($request->getAll() as $fieldName=>$fieldValue) {
//            if (preg_match('/^vehicle_vin_\d+/', $fieldName, $m)) {
//                preg_match('/\d+/', $fieldName, $m, PREG_OFFSET_CAPTURE);
//                $index = substr($fieldName, $m[0][1]);
//                $make = $request->get('vehicle_make_'.$index);
//                $model = $request->get('vehicle_model_'.$index);
//                $year = $request->get('vehicle_year_'.$index);
//                $vin = $request->get('vehicle_vin_'.$index);
//                $color = $request->get('vehicle_color_'.$index);
//                $odometer = $request->get('vehicle_odometer_'.$index);
//                $lstate = $request->get('vehicle_lstate_'.$index);
//                $lnumber = $request->get('vehicle_lnumber_'.$index);
//                $type = $request->get('vehicle_type_'.$index);
//                $id = $request->get('vehicle_id_'.$index);
//
//                unset($params);
//                $params = array($recordId, $make, $model, $year, $vin, $color, $odometer, $lstate, $lnumber, $type);
//
//                if ($id == 'none') {
//                    //INSERT NEW RECORD
//                    $sql = "INSERT INTO `vtiger_vehiclelookup` (crmid, vehicle_make, vehicle_model, vehicle_year, vehicle_vin, vehicle_color, vehicle_odometer, license_state, license_number, vehicle_type) VALUES (?,?,?,?,?,?,?,?,?,?)";
//                } else {
//                    //UPDATE EXISTING RECORD
//                    $sql = "UPDATE `vtiger_vehiclelookup` SET crmid=?, vehicle_make=?, vehicle_model=?, vehicle_year=?, vehicle_vin=?, vehicle_color=?, vehicle_odometer=?, license_state=?, license_number=?, vehicle_type=? WHERE vehicleid=?";
//                    $params[] = $id;
//                }
//
//                file_put_contents('logs/vehicleSave.log', date('Y-m-d H:i:s - ').print_r($params, true)."\n", FILE_APPEND);
//
//                $db->startTransaction();
//                $db->pquery($sql, $params);
//                $db->completeTransaction();
//            } elseif (preg_match('/removeVehicle_\d+/', $fieldName, $m)) {
//                $vehicleId = $fieldValue;
//
//                $sql = "DELETE FROM `vtiger_vehiclelookup` WHERE vehicleid=?";
//                $db->pquery($sql, array($vehicleId));
//            }
//        }
//    }
//
//    public static function transferVehicles($sourceRecordId, $targetRecordId)
//    {
//        $db = PearDatabase::getInstance();
//
//        $sql = "SELECT vehicleid, vehicle_make, vehicle_model, vehicle_year, vehicle_vin, vehicle_color, vehicle_odometer, license_state, license_number, vehicle_type FROM `vtiger_vehiclelookup` WHERE crmid=?";
//        $result = $db->pquery($sql, array($sourceRecordId));
//
//        while ($row =& $result->fetchRow()) {
//            $sql = "INSERT INTO `vtiger_vehiclelookup` (crmid, vehicle_make, vehicle_model, vehicle_year, vehicle_vin, vehicle_color, vehicle_odometer, license_state, license_number, vehicle_type) VALUES (?,?,?,?,?,?,?,?,?,?)";
//            $res = $db->pquery($sql, array($targetRecordId, $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7], $row[8], $row[9]));
//        }
//    }

    public static function transferVehicles($sourceRecordId, $targetRecordId)
    {
        $db = &PearDatabase::getInstance();
        $result = $db->pquery('SELECT vehiclelookupid FROM vtiger_vehiclelookup WHERE vehiclelookup_relcrmid=?',
                              [$sourceRecordId]);
        while($row = $result->fetchRow())
        {
            $record = Vtiger_Record_Model::getInstanceById($row['vehiclelookupid'], 'VehicleLookup');
            $record->setId('');
            $record->set('vehiclelookup_relcrmid', $targetRecordId);
            $record->save();
        }
    }

//
//    public static function getVehicles($record, $parentRecord=false)
//    {
//        $db = PearDatabase::getInstance();
//
//        $vehicleList = array();
//
//        if (!empty($record)) {
//            $sql = "SELECT * FROM `vtiger_vehiclelookup` WHERE crmid=?";
//            $result = $db->pquery($sql, array($record));
//
//            while ($row =& $result->fetchRow()) {
//                if ($parentRecord) {
//                    $row['vehicleid'] = 'none';
//                }
//                $vehicleList[] = $row;
//            }
//        }
//        return $vehicleList;
//    }
    
    public static function saveChecklist($request)
    {
        $db = &PearDatabase::getInstance();
        
        $recordId = $request->get('record');
        
        foreach ($request->getAll() as $fieldName=>$fieldValue) {
            if (preg_match('/^checklistItemDescription_\d+/', $fieldName, $m)) {
                preg_match('/\d+/', $fieldName, $m, PREG_OFFSET_CAPTURE);
                $index = substr($fieldName, $m[0][1]);
                
                $id = $request->get('checklistItemId_'.$index);
                $checklistString = $request->get('checklistItemDescription_'.$index);
                
                unset($params);
                $params = array($recordId, $checklistString);
                
                if ($id == 'default') {
                    $sql = "INSERT INTO `vtiger_vehiclelookup_checklist` (agentmanagerid, checklist_string) VALUES (?,?)";
                } else {
                    $sql = "UPDATE `vtiger_vehiclelookup_checklist` SET agentmanagerid=?, checklist_string=? WHERE itemid=?";
                    $params[] = $id;
                }
                
                $db->startTransaction();
                $db->pquery($sql, $params);
                $db->completeTransaction();
            } elseif (preg_match('/^removeChecklistItem_\d+/', $fieldName, $m)) {
                $itemId = $fieldValue;
        
                $sql = "DELETE FROM `vtiger_vehiclelookup_checklist` WHERE itemid=?";
                $db->pquery($sql, array($itemId));
            }
        }
    }
    
    public static function getChecklist($record)
    {
        $db = &PearDatabase::getInstance();
        
        $checklistArray = array();
        $sql = "SELECT itemid, agentmanagerid, checklist_string FROM `vtiger_vehiclelookup_checklist` WHERE agentmanagerid=?";
        
        if (!empty($record)) {
            $result = $db->pquery($sql, array($record));
            if ($result->numRows() == 0) {
                $result = $db->pquery($sql, array(0));
            }
        } else {
            $result = $db->pquery($sql, array(0));
        }
        
        while ($row =& $result->fetchRow()) {
            $checklistArray[$row[0]] = array('agentmanagerid'=>$row[1], 'checklist_string'=>$row[2]);
        }
        
        return $checklistArray;
    }
}
