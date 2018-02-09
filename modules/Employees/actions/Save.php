<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Employees_Save_Action extends Vtiger_Save_Action
{
    public function process(Vtiger_Request $request)
    {
        //added to make images work
        $result = Vtiger_Util_Helper::transformUploadedFiles($_FILES, true);
        $_FILES = $result['imagename'];
        //end addition
        $modName     = $request->getModule();
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $timeOut     = Vtiger_Time_UIType::getTimeValueWithSeconds($request->get('time_out'));
        $datetimeOut = DateTimeField::convertToDBTimeZone($request->get('date_out').' '.$timeOut);
        $request->set('time_out', $datetimeOut->format('H:i:s'));
        $request->set('date_out', $datetimeOut->format('Y-m-d'));
        $timeIn     = Vtiger_Time_UIType::getTimeValueWithSeconds($request->get('time_in'));
        $datetimeIn = DateTimeField::convertToDBTimeZone($request->get('date_in').' '.$timeIn);
        $request->set('time_in', $datetimeIn->format('H:i:s'));
        $request->set('date_in', $datetimeIn->format('Y-m-d'));
        $isQualify = $request->get('isqualify');
        $driverNo = $request->get('driver_no');
        if ($isQualify == 'on' && $driverNo == '' && getenv('INSTANCE_NAME') != 'graebel') {
            $newDriverNumber = $this->getDriverNo();
            $request->set('driver_no', $newDriverNumber);
        }
        parent::process($request);
        $recId = $request->get('record');
        if ($recId == null) {
            //@TODO: fix because this won't do the right thing.
            $recId = $this->getRecordID($request);
        }
        if ($recId != '') {
            $employeeModel = Vtiger_Record_Model::getInstanceById($recId, 'Employees');
            //@TODO: this won't 500. I mean it probably won't be right but at least it won't fail.
            if ($employeeModel && method_exists($employeeModel, 'updateRelatedTrips')) {
                $employeeModel->updateRelatedTrips();
            }
        }
    }

    public function getDriverNo()
    {
        $currentSeq = CRMEntity::setModuleSeqNumber('increment', 'Employees', 'DRIVER');
        return $currentSeq;
    }
}
