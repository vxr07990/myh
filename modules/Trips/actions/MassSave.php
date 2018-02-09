<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Trips_MassSave_Action extends Vtiger_MassSave_Action
{
    public function process(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        $recordModels = $this->getRecordModelsFromRequest($request);
        foreach ($recordModels as $recordId => $recordModel) {
            if ($recordModel->get('driver_id') != '' && $recordModel->get('driver_id') != 0) {
                $db = PearDatabase::getInstance();

                try {
                    $employeeModel = Vtiger_Record_Model::getInstanceById($recordModel->get('driver_id'), 'Employees');
                    $params = array(
                        $employeeModel->get('employee_lastname'),
                        $employeeModel->get('name'),
                        $employeeModel->get('employee_no'),
                        $employeeModel->get('employee_mphone'),
                        $employeeModel->get('employee_email'),
                        $recordId,
                    );

                    $db->pquery("UPDATE vtiger_trips, vtiger_crmentity SET trips_driverlastname=?, trips_driverfirstname=?, trips_driverno=?, trips_drivercellphone=?, 
                                trips_driversemail=? 
                                WHERE vtiger_trips.tripsid = vtiger_crmentity.crmid 
                                AND deleted=0 
                                AND tripsid=?", $params);
                } catch (Exception $exc) {
                    echo $exc->getTraceAsString();
                }
            }
            
            $recordModel->recalculateTripsFields($request);
        }

        parent::process($request);
    }
}
