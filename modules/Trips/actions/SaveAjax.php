<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Trips_SaveAjax_Action extends Vtiger_SaveAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        if ($request->get('driver_id') != '' && $request->get('driver_id') != 0) {
            try {
                $employeeModel = Vtiger_Record_Model::getInstanceById($request->get('driver_id'), 'Employees');
                $request->set('trips_driverlastname', $employeeModel->get('employee_lastname'));
                $request->set('trips_driverfirstname', $employeeModel->get('name'));
                $request->set('trips_driverno', $employeeModel->get('employee_no'));
                $request->set('trips_drivercellphone', $employeeModel->get('employee_mphone'));
                $request->set('trips_driversemail', $employeeModel->get('employee_email'));
            } catch (Exception $exc) {
                echo $exc->getTraceAsString();
            }
        }

        if ($request->get('record') != '' && $request->get('record') != 0) {
            $tripsRecord = Vtiger_Record_Model::getInstanceById($request->get('record'), 'Trips');
            $tripsRecord->recalculateTripsFields($request);
        }

        parent::process($request);
    }
}
