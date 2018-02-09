<?php

require_once ('include/Webservices/Create.php');

function createOnNoticeForInspectionDueDate($entity) {
    $fieldName = 'inspection_duedate';
    $expirationDate = $entity->get('inspection_duedate');
    $status = 'on_notice';
    $result = createVehicleOutOfServiceRecord($entity, $fieldName, $expirationDate, $status);
}

function createOnNoticeForInspectionPhotosDue($entity) {
    $fieldName = 'inspection_photosdate';
    $expirationDate = $entity->get('inspection_photosdate');
    $status = 'on_notice';
    $result = createVehicleOutOfServiceRecord($entity, $fieldName, $expirationDate, $status);
}

function createOutOfServiceForInspectionDueDate($entity) {
    $fieldName = 'inspection_duedate';
    $expirationDate = $entity->get('inspection_duedate');
    $status = 'out_of_service'; //creates Out Of Service
    $result = createVehicleOutOfServiceRecord($entity, $fieldName, $expirationDate, $status);
}

function createOutOfServiceForInspectionPhotosDue($entity) {
    $fieldName = 'inspection_photosdate';
    $expirationDate = $entity->get('inspection_photosdate');
    $status = 'out_of_service'; //creates Out Of Service
    $result = createVehicleOutOfServiceRecord($entity, $fieldName, $expirationDate, $status);
}

//this function creates out of service records for the given vehicle and the given expiration date (based on fieldName)
//depending on $status creates On Notice status or Out Of Service status
//one record for "out of service" status out of service record with an effective date of the day following the expiration
//other record for "on notice" status out of service record with an effective date of 30 days prior to the expiration date
function createVehicleOutOfServiceRecord($entity, $fieldName, $expirationDate, $status) {

    $db = PearDatabase::getInstance();
    $user = Users_Record_Model::getCurrentUserModel();

    $vehicleArray = [
        'outofservice_vehicle' => $entity->get('inspection_vehicle'),
        'assigned_user_id' => $entity->get('assigned_user_id'),
        'agentid' => $entity->get('agentid')
    ];

    switch ($fieldName) {

        case 'inspection_duedate'://from inspection vehicles module
            $vehicleArray['outofservice_reason'] = 'Periodic Inspect - Due';
            break;

        case 'inspection_photosdate'://from inspection vehicles module
            $vehicleArray['outofservice_reason'] = 'Register - Photos due';
            break;
    }

    if ($status == 'on_notice') {
        //create the status "On Notice" entry
        $OnNoticeEffectiveDate = date('Y-m-d', strtotime('-29 day', strtotime($expirationDate)));

        //logic: check if ther is no record with same expiration date for this vehicle
        $result = $db->pquery("SELECT vehicleoutofserviceid FROM vtiger_vehicleoutofservice WHERE outofservice_vehicle=? AND outofservice_status='On Notice' AND outofservice_reason=? AND outofservice_effective_date=?", [$vehicleId, $vehicleArray['outofservice_reason'], $OnNoticeEffectiveDate]);

        if ($result && $db->num_rows($result) == 0) {
            //create the status "On Notice" entry

            $OnNoticeArray = [
                'outofservice_status' => 'On Notice',
                'outofservice_effective_date' => $OnNoticeEffectiveDate,
            ];

            $OnNoticeArray = array_merge($OnNoticeArray, $vehicleArray);

            try {
                $id = vtws_create('VehicleOutofService', $OnNoticeArray, $user);
            } catch (Exception $exc) {
                global $log;
                $log->debug('Error creating Vehicle Out of Service record:' . $exc->getMessage());
            }
        }
    } else {
        //create the status "Out Of Service" entry
        $OutOfServiceEffectiveDate = date('Y-m-d', strtotime('+1 day', strtotime($expirationDate)));

        $result2 = $db->pquery("SELECT vehicleoutofserviceid FROM vtiger_vehicleoutofservice WHERE outofservice_vehicle=? AND outofservice_status='Out of Service' AND outofservice_reason=? AND outofservice_effective_date=?", [$vehicleId, $vehicleArray['outofservice_reason'], $OutOfServiceEffectiveDate]);

        if ($result2 && $db->num_rows($result2) == 0) {
            //create the status "Out Of Service" entry

            $OutOfServiceArray = [
                'outofservice_status' => 'Out of Service',
                'outofservice_effective_date' => $OutOfServiceEffectiveDate
            ];

            $OutOfServiceArray = array_merge($OutOfServiceArray, $vehicleArray);

            try {
                $id2 = vtws_create('VehicleOutofService', $OutOfServiceArray, $user);
            } catch (Exception $exc) {
                global $log;
                $log->debug('Error creating Vehicle Out of Service record:' . $exc->getMessage());
            }
        }
    }

    $created = ['On notice' => $id, 'Out of service' => $id2];
    return $created;
}
