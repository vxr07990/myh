<?php

require_once ('include/Webservices/Create.php');

function createOnNoticeForLisenceExpirationDate($entity) {
    $fieldName = 'employee_dlexpy';
    $expirationDate = $entity->get('employee_dlexpy');
    $status = 'on_notice';
    $result = createOutOfServiceRecordEmployee($entity, $fieldName, $expirationDate, $status);
}

function createOutOfServiceForLisenceExpirationDate($entity) {
    $fieldName = 'employee_dlexpy';
    $expirationDate = $entity->get('employee_dlexpy');
    $status = 'out_of_service';
    $result = createOutOfServiceRecordEmployee($entity, $fieldName, $expirationDate, $status);
}

function createOnNoticeForBackgroundExpirationDate($entity) {
    $fieldName = 'employees_nbackground';
    $expirationDate = $entity->get('employees_nbackground');
    $status = 'on_notice';
    $result = createOutOfServiceRecordEmployee($entity, $fieldName, $expirationDate, $status);
}

function createOutOfServiceForBackgroundExpirationDate($entity) {
    $fieldName = 'employees_nbackground';
    $expirationDate = $entity->get('employees_nbackground');
    $status = 'out_of_service';
    $result = createOutOfServiceRecordEmployee($entity, $fieldName, $expirationDate, $status);
}

//this function creates out of service records for the given employee and the given expiration date (based on fieldName)
//depending on $status creates On Notice status or Out Of Service status
//one record for "out of service" status out of service record with an effective date of the day following the expiration
//other record for "on notice" status out of service record with an effective date of 30 days prior to the expiration date
function createOutOfServiceRecordEmployee($entity, $fieldName, $expirationDate, $status) {

    $db = PearDatabase::getInstance();
    $user = Users_Record_Model::getCurrentUserModel();

    $employeeArray = [
        'outofservice_employeesid' => $entity->get('id'),
        'assigned_user_id' => $entity->get('assigned_user_id'),
        'agentid' => $entity->get('agentid'),
    ];

    switch ($fieldName) {

        case 'employee_dlexpy':
            $employeeArray['outofservice_type'] = 'Driver´s License';
            $employeeArray['outofservice_typeofreason'] = 'Expired';
            break;

        case 'employees_nbackground':
            $employeeArray['outofservice_type'] = 'Safety';
            $employeeArray['outofservice_typeofreason'] = 'Misc.';
            $employeeArray['outofservice_comment'] = 'Background expiration';
            break;
    }

    if ($status == 'on_notice') {//create the status "On Notice" entry
        $OnNoticeEffectiveDate = date('Y-m-d', strtotime('-29 day', strtotime($expirationDate)));
       
        //logic: check if ther is no record with same expiration date for this employee
        $result = $db->pquery("SELECT outofserviceid FROM vtiger_outofservice WHERE outofservice_employeesid=? AND outofservice_status='On Notice' AND outofservice_type=? AND outofservice_typeofreason=? AND outofservice_effectivedate=?", [$employeeId, $employeeArray['outofservice_type'], $employeeArray['outofservice_typeofreason'], $OnNoticeEffectiveDate]);

        if ($result && $db->num_rows($result) == 0) {
            //create the status "On Notice" entry

            $OnNoticeArray = [
                'outofservice_status' => 'On Notice',
                'outofservice_effectivedate' => $OnNoticeEffectiveDate,
            ];

            $OnNoticeArray = array_merge($OnNoticeArray, $employeeArray);

            try {
                $id = vtws_create('OutOfService', $OnNoticeArray, $user);
            } catch (Exception $exc) {
                global $log;
                $log->debug('Error creating Out of Service record:' . $exc->getMessage());
            }
        }
    } else {//create the status "Out Of Service" entry
        $OutOfServiceEffectiveDate = date('Y-m-d', strtotime('+1 day', strtotime($expirationDate)));

        $result2 = $db->pquery("SELECT outofserviceid FROM vtiger_outofservice WHERE outofservice_employeesid=? AND outofservice_status='Out of Service' AND outofservice_type=? AND outofservice_typeofreason=? AND outofservice_effectivedate=?", [$employeeId, $employeeArray['outofservice_type'], $employeeArray['outofservice_typeofreason'], $OutOfServiceEffectiveDate]);

        if ($result2 && $db->num_rows($result2) == 0) {
            //create the status "Out Of Service" entry

            $OutOfServiceArray = [
                'outofservice_status' => 'Out of Service',
                'outofservice_effectivedate' => $OutOfServiceEffectiveDate
            ];

            $OutOfServiceArray = array_merge($OutOfServiceArray, $employeeArray);

            try {
                $id2 = vtws_create('OutOfService', $OutOfServiceArray, $user);
            } catch (Exception $exc) {
                global $log;
                $log->debug('Error creating Out of Service record:' . $exc->getMessage());
            }
        }
    }

    $created = ['On notice' => $id, 'Out of service' => $id2];
    return $created;
}
