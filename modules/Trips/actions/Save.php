<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Trips_Save_Action extends Vtiger_Save_Action
{
    public function process(Vtiger_Request $request)
    {
        if ($request->get('driver_id') != '' &&  $request->get('driver_id') != 0) {
            $db = PearDatabase::getInstance();

            try {
                $employeeModel = Vtiger_Record_Model::getInstanceById($request->get('driver_id'), 'Employees');
                $params = array(
                    $employeeModel->get('employee_lastname'),
                    $employeeModel->get('name'),
                    $employeeModel->get('employee_no'),
                    $employeeModel->get('employee_mphone'),
                    $employeeModel->get('employee_email'),
                    $request->get('record'),
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
        
//        if($request->get('record') != '' && $request->get('record') != 0 ){
//            $tripsRecord = Vtiger_Record_Model::getInstanceById($request->get('record'), 'Trips');
//            $tripsRecord->recalculateTripsFields($request);
//        }
		$oldDriver = $oldAgent = '';
		$oldTripsRecord = false;
		if($request->get('record') != '' && $request->get('record') != 0 ){
		    $oldTripsRecord = Vtiger_Record_Model::getInstanceById($request->get('record'), 'Trips');
		    $oldDriver = $oldTripsRecord->get('driver_id');
		    $oldAgent = $oldTripsRecord->get('agent_unit');
		}

		
		$newDriver = $request->get('driver_id');
		$newAgent = $request->get('agent_unit');
        // we shouldn't compare against the old value because new orders may have been added that need to be updated
		if($oldTripsRecord){
			$orders = $oldTripsRecord->getRelatedOrders();
			if(count($orders > 0)){
				include_once 'include/Webservices/Revise.php';
				$user = Users_Record_Model::getCurrentUserModel();
				foreach ($orders as $order){
					$orderUpdate['id'] = vtws_getWebserviceEntityId('Orders', $order['ordersid']);
					$orderUpdate['driver_trip'] = vtws_getWebserviceEntityId('Employees', $newDriver);
    				$orderUpdate['agent_trip'] = vtws_getWebserviceEntityId('Agents', $newAgent);

					try {
					    vtws_revise($orderUpdate, $user);
					} catch (Exception $exc) {
					    echo $exc->getTraceAsString();
					    MoveCrm\LogUtils::LogToFile('LOG_CRM_FAILS', "VTWS ERROR = ".$exc->getMessage(), true);
					}
					
				}
			}
		}

        parent::process($request);

        $tripsRecord = Vtiger_Record_Model::getInstanceById($request->get('record'), 'Trips');
		$tripsRecord->recalculateTripsFields();
    }

}
