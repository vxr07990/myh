<?php

include_once 'include/Webservices/Revise.php';
include_once 'include/Webservices/Delete.php';

class Trips_TripsActions_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        $mode = $request->get('mode');

        switch ($mode) {
            case 'updateSIT':
                $result = $this->updateSIT($request);
                break;
            case 'updateOrdersOtherStatus':
                $result = $this->updateOrdersOtherStatus($request);
                break;
            case 'getRelatedTable4ListView':
                $result = $this->getRelatedTable4ListView($request);
                break;
            case 'reloadOrdersTable':
                $result = $this->reloadOrdersTable($request);
                break;
            case 'updateDateOrders':
                $result = $this->updateDateOrders($request);
                break;
            case 'updateActualWeightOrders':
                $result = $this->updateActualWeightOrders($request);
                break;
            case 'getDriverInfo':
                $result = $this->getDriverInfo($request);
                break;
	    case 'getStateInfo':
                $result = $this->getStateInfo($request);
                break;
            case 'getVehicleInfo':
                $result = $this->getVehicleInfo($request);
                break;
            case 'updateSequenceOrders':
                $result = $this->updateSequenceOrders($request);
                break;
	    case 'deleteCheckin':
		$result = $this->deleteCheckin($request);
                break;
            case 'checkDriverOOS':
                $result = $this->checkDriverOOS($request);
                break;
            default:
                $result = 'ERROR';
                break;
        }
        $msg = new Vtiger_Response();
        $msg->setResult($result);
        $msg->emit();
    }
    
    function checkDriverOOS(Vtiger_Request $request) {
	$db = PearDatabase::getInstance();
	$driverId = $request->get('driverID');

	if ($driverId == "") {
	    return "OK";
	} else {
	    if (OutOfService_Module_Model::getOutOfServiceStatus($driverId)) {
		return "Out of Service";
	    } else {
		return "OK";
	    }
	}
    }

    function getDriverInfo(Vtiger_Request $request) {
	$db = PearDatabase::getInstance();
        $driverId = $request->get('driverId');
        $ownerId = $ownerName = '';
	
        $employeeRecodModel = Vtiger_Record_Model::getInstanceById($driverId, 'Employees');
        $modelData = $employeeRecodModel->getData();

        $owner = $db->pquery('SELECT agentsid, agentname, agent_number FROM vtiger_agents WHERE agentmanager_id = ?', [$modelData['agentid']])->fetchRow();
        if ($owner) {
	    $ownerId = $owner['agentsid'];
	    $ownerName .= $owner['agentname'].' ('.$owner['agent_number'].')';
	}
	
	//OT3313
	$employeeid = $modelData['record_id'];
        $result = $db->pquery('SELECT outofservice_employeesid FROM vtiger_outofservice WHERE outofservice_employeesid = ? AND outofservice_status = "On Notice" AND outofservice_satisfieddate IS NULL', [$employeeid]);
        if ($result && $db->num_rows($result) > 0) {
	    $onNotice = true;
        } else {
	    $onNotice = false;
	}

        
        $employeeArr = array("DriverNo" => $modelData['driver_no'],
					"DriverLastName" => $modelData['employee_lastname'],
					"DriverFirstName" => $modelData['name'],
					"DriverCellPhone" => $modelData['employee_mphone'],
					"DriverEmail" => $modelData['employee_email'],
					"DriverId"=>$driverId,
					"OwnerId" => $ownerId,
					"OwnerName" => $ownerName,
					"OnNotice" => $onNotice,
					"PerformanceRating" => $modelData['employee_performancerating'],
				    "PcqRating" => $modelData['employee_pqcrating'],
				    "CsaRanking" => $modelData['employee_driverclaimratio'],
				    "DriverClaimRatio" => $modelData['employee_driverclaimratio'],);


        return json_encode($employeeArr);
    }

    public function getStateInfo(Vtiger_Request $request)
    {
        $record = $request->get('record');//tripid
        
	if ($record != '') {
            $origin_state = Trips_Record_Model::getTripOriginState($record);
            $destination_state = Trips_Record_Model::getTripDestinationState($record);
	}

        if ($origin_state) {
            $originState = $origin_state;
        } else {
            $originState = '';
        }
        if ($destination_state) {
            $emptyState = $destination_state;
        } else {
            $emptyState = '';
        }
	
        $arr = array("originState" => $originState, "emptyState" => $emptyState);
        return json_encode($arr);
    }

    public function getRelatedTable4ListView(Vtiger_Request $request)
    {
        $db = PearDatabase::getInstance();
        try {
            $html = '';
            $tripid = $request->get('tripid');
            $first_sql = $db->pquery("SELECT vtiger_orders.* FROM vtiger_orders
                    INNER JOIN vtiger_crmentity ON vtiger_orders.ordersid = vtiger_crmentity.crmid 
                    INNER JOIN vtiger_crmentityrel ON vtiger_orders.ordersid = vtiger_crmentityrel.relcrmid 
                    WHERE deleted = 0 AND vtiger_crmentityrel.crmid = ?", array($tripid));


            if ($db->num_rows($first_sql) > 0) {
                while ($arr = $db->fetch_array($first_sql)) {
                    if (Users_Privileges_Model::isPermitted('Orders', 'EditView', $arr['relcrmid']) == 'yes') {
                        if ($arr['orders_contacts'] != '' && $arr['orders_contacts'] != '0') {
                            $ordersContact = Vtiger_Record_Model::getInstanceById($arr['orders_contacts'], 'Contacts');
                            $lastName = $ordersContact->get('lastname');
                            $firstName = $ordersContact->get('firstname');
                        } else {
                            $lastName = '';
                            $firstName = '';
                        }

                        if ($arr['orders_account'] != '' && $arr['orders_account'] != '0') {
                            $ordersAcccount = Vtiger_Record_Model::getInstanceById($arr['orders_account'], 'Accounts');
                            $accountName = $ordersAcccount->get('accountname');
                        } else {
                            $accountName = '';
                        }
                        $html .= '<tr class="listViewEntries" data-id="' . $arr['ordersid'] . '" data-orderid="' . $arr['ordersid'] . '">
                            <td class="" data-field-type="date" nowrap="">' . ($arr['orders_ldate'] != '' ? Vtiger_Date_UIType::getDisplayDateValue($arr['orderstask_ldate']) : '') . '</td>
                            <td class="" data-field-type="string" nowrap="">' . $accountName . '</td>
                            <td class="" data-field-type="string" nowrap="">' . $lastName . '</td>
                            <td class="" data-field-type="string" nowrap="">' . $firstName . '</td>
                            <td class="" data-field-type="string" nowrap="">' . $arr['orders_no'] . '</td>
                            <td class="" data-field-type="double" nowrap="">' . $arr['orders_eweight'] . '</td>
                            <td class="" data-field-type="double" nowrap="">' . $arr['orders_aweight'] . '</td>
                            <td class="" data-field-type="double" nowrap="">' . Vtiger_Currency_UIType::transformDisplayValue($arr['orders_elinehaul']) . '</td>
                            <td class="" data-field-type="date" nowrap="">' . $arr[''] . '</td>
                            <td class="" data-field-type="string" nowrap="">' . $arr[''] . '</td>
                            <td class="" data-field-type="string" nowrap="">' . $arr[''] . '</td>
                            <td class="" data-field-type="string" nowrap="">' . $arr['origin_city'] . '</td>
                            <td class="" data-field-type="string" nowrap="">' . $arr['origin_state'] . '</td>
                            <td class="" data-field-type="string" nowrap="">' . $arr[''] . '</td>
                            <td class="" data-field-type="string" nowrap="">' . $arr['destination_city'] . '</td>
                            <td class="" data-field-type="string" nowrap="">' . $arr['destination_state'] . '</td>
                            <td class="" data-field-type="string" nowrap="">' . $arr[''] . '</td>
                            <td class="" data-field-type="string" nowrap="">' . $arr[''] . '</td>
                            <td class="" data-field-type="string" nowrap="">' . $arr[''] . '</td>
                            <td class="" data-field-type="string" nowrap="">' . $arr[''] . '</td>
                            <td class="" data-field-type="double" nowrap="">' . $arr['orders_gweight'] . '</td>
                            <td class="" data-field-type="double" nowrap="">' . $arr['orders_tweight'] . '</td>
                            <td class="netweight" data-field-type="double" nowrap="">' . $arr['orders_netweight'] . '</td>
                            <td class="" data-field-type="string" nowrap="">' . $arr[''] . '</td>
                        </tr>';
                    }
                }
            }
            return $html;
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }

    //La funcion updateSIT actualiza por pquery, ya que por el record_model de Orders no actualizaba el campo.
    public function updateSIT(Vtiger_Request $request)
    {
        try {
                    $user = Users_Record_Model::getCurrentUserModel();
                    $ordersData['id'] = vtws_getWebserviceEntityId('Orders',  $request->get('orderid'));
                    $ordersData['orders_sit'] = $request->get('sit');
                   
                    vtws_revise($ordersData, $user);
            return "Ok";
        } catch (Exception $exc) {
            return "Fail";
        }
    }

    function updateOrdersOtherStatus(Vtiger_Request $request) {
        $taskId = $request->get('orderid');
        $otherStatus = $request->get('other_status');
        $plannedLoadDate = $request->get('planned_load_date');
        $plannedDeliveryDate = $request->get('planned_delivery_date');
        $actualLoadDate = $request->get('actual_load_date');
        $actualDeliveryDate = $request->get('actual_delivery_date');
        $actualWeight = $request->get('actual_weight');

        $user = Users_Record_Model::getCurrentUserModel();

        try {
            switch ($otherStatus) {
                case 'Confirmed':
                    $ordersData['id'] = vtws_getWebserviceEntityId('Orders', $taskId);
                    $ordersData['orders_plannedloaddate'] = $plannedLoadDate;
                    $ordersData['orders_planneddeliverydate'] = $plannedDeliveryDate;
                    $ordersData['orders_otherstatus'] = $otherStatus;
                    
                    vtws_revise($ordersData, $user);
                    break;
                case 'Loaded':
                    $ordersData['id'] = vtws_getWebserviceEntityId('Orders', $taskId);
                    $ordersData['orders_pudate'] = DateTimeField::convertToDBFormat($actualLoadDate);
                    $ordersData['orders_planneddeliverydate'] = $plannedDeliveryDate;
                    $ordersData['orders_otherstatus'] = $otherStatus;

                    vtws_revise($ordersData, $user);
                    break;
                case 'Delivered':
                    $ordersData['id'] = vtws_getWebserviceEntityId('Orders', $taskId);
                    $ordersData['orders_actualdeliverydate'] = $actualDeliveryDate;
                    $ordersData['orders_netweight'] = $actualWeight;
                    $ordersData['orders_otherstatus'] = $otherStatus;

                    vtws_revise($ordersData, $user);
                    break;
                default: 
                    $ordersData['id'] = vtws_getWebserviceEntityId('Orders', $taskId);
					if($otherStatus == 'blank'){
						$otherStatus = '';
					}
		    
                    $ordersData['orders_otherstatus'] = $otherStatus;

                    if($otherStatus == "blank" || $otherStatus == "Non-Planned"){//planned or actual load and/or delivery dates need to be blank.
                        $ordersData['orders_planneddeliverydate'] = $ordersData['orders_actualdeliverydate'] = $ordersData['orders_plannedloaddate'] = $ordersData['orders_pudate'] = "";
                    }
                    
                    vtws_revise($ordersData, $user);
                    break;
            }

            return "Ok";
        } catch (Exception $exc) {
            //return "Fail";
            return $exc->getTraceAsString();
        }
    }

    public function updateTwoFields($id)
    {
        $db = PearDatabase::getInstance();
        $sql = "SELECT sum(CAST(orders_netweight AS DECIMAL(7,2))) as total FROM vtiger_orderstask
                INNER JOIN vtiger_crmentity ON vtiger_orderstask.orderstaskid = vtiger_crmentity.crmid 
                INNER JOIN vtiger_crmentityrel ON vtiger_orderstask.orderstaskid = vtiger_crmentityrel.relcrmid 
                INNER JOIN vtiger_orders ON vtiger_orderstask.ordersid = vtiger_orders.ordersid 
                WHERE deleted = 0 AND vtiger_crmentityrel.crmid = $id";
        $result = $db->query($sql);
        $totalweight = $db->query_result($result, 0, 'total');

        $tripsRecordModel = Vtiger_Record_Model::getInstanceById($id, 'Trips');
        $tripsRecordModel->set('mode', 'edit');
        $tripsRecordModel->set('total_weight', $totalweight);
        $tripsRecordModel->save();

        return $totalweight;
    }

    public function updateDateOrders(Vtiger_Request $request)
    {
        $taskId = $request->get('orderid');

        $orderRecodModel = Vtiger_Record_Model::getInstanceById($taskId, 'Orders');
        $orderRecodModel->set('mode', 'edit');
        $orderRecodModel->set('orders_pudate', DateTimeField::convertToDBFormat($request->get('aldate'))); //Actual load date
        $orderRecodModel->set('orders_actualpudate', DateTimeField::convertToDBFormat($request->get('addate'))); //Actual delivery date
        $orderRecodModel->set('orders_ldd_pldate', DateTimeField::convertToDBFormat($request->get('pldate'))); //Planned load date
        $orderRecodModel->set('orders_ldd_pddate', DateTimeField::convertToDBFormat($request->get('pddate'))); //Planned delivery date
        //$orderRecodModel->set('orders_ldd_plconfirmed', $request->get('pl_confirmed'));
        //$orderRecodModel->set('orders_ldd_pdconfirmed', $request->get('pd_confirmed'));

        $orderRecodModel->save();
        $tripId = $orderRecodModel->get('orders_trip');
        $tripsRecodModel = Vtiger_Record_Model::getInstanceById($tripId, 'Trips');
        $tripsRecodModel->recalculateTripsFields();

        return 'ok';
    }
    
    public function updateSequenceOrders(Vtiger_Request $request)
    {
        $arrayOrderId = $request->get('orderid');
        $arraySequence = $request->get('sequence');
        $count = count($arrayOrderId);
        
        $db = PearDatabase::getInstance();
        $response = array();
        if ($count > 0) {
            for ($i=0;$i<$count;$i++) {
                $params = array($arraySequence[$i],$arrayOrderId[$i]);
                $result = $db->pquery('UPDATE vtiger_orders SET orders_sequence=? WHERE ordersid=?', $params);
                $response[] =  $db->getAffectedRowCount();
            }
        }
        return $response;
    }

    public function updateActualWeightOrders(Vtiger_Request $request)
    {
        $taskId = $request->get('orderid');
        $tripsId = $request->get('tripsid');
        $actualWeight = $request->get('actual_weight');

        $taskRecodModel = Vtiger_Record_Model::getInstanceById($taskId, 'Orders');
        $taskRecodModel->set('mode', 'edit');
        $taskRecodModel->set('orders_actualweight', $actualWeight);

        $taskRecodModel->save();
        return $this->updateTwoFields($tripsId);
    }

    public function getVehicleInfo(Vtiger_Request $request)
    {
        $vehicleDetails = [];

        if ($request->get('vehicle_id')) {
            $vehicle = Vtiger_Record_Model::getInstanceById($request->get('vehicle_id'), 'Vehicles');
            $vehicleDetails['vehicle_length'] = $vehicle->get('vehicle_length');
            $vehicleDetails['vehicle_cubec'] = $vehicle->get('vehicle_cubec');
            if ($request->get('popuptype') == "trip_vehicle") {
            $vehicleDetails['agent_id'] = $vehicle->get('vehicles_agent_no');
                if ($vehicleDetails['agent_id']) {
                    $db = PearDatabase::getInstance();
                    $sql = 'SELECT agentname FROM `vtiger_agents` WHERE agent_number = ?';//agentsid = ?';
                    $result = $db->pquery($sql, array($vehicleDetails['agent_id']));
                    $row = $result->fetchRow();
                    $vehicleDetails['agentname'] = $row['agentname'];
                }
            }
        }

        return $vehicleDetails;
    }

    public function reloadOrdersTable(Vtiger_Request $request)
    {
        try {
            $ordersId = $request->get('informacion');
            $tripsId = $request->get('tripid');
            $response = $this->updateTwoFields($tripsId);

            $html = '';
            $db = PearDatabase::getInstance();
            $sql = "SELECT orderstaskid,orderstask_ldate,orderstask_pldate,vtiger_orders.* 
                    FROM vtiger_orderstask 
                    INNER JOIN vtiger_crmentity ON vtiger_orderstask.orderstaskid = vtiger_crmentity.crmid 
                    INNER JOIN vtiger_orders ON vtiger_orderstask.ordersid = vtiger_orders.ordersid 
                    INNER JOIN vtiger_crmentityrel ON vtiger_orderstask.orderstaskid = vtiger_crmentityrel.relcrmid
                    WHERE deleted = 0 AND vtiger_crmentityrel.crmid IN ($tripsId)";
            $result = $db->query($sql);
            if ($db->num_rows($result) > 0) {
                while ($arr = $db->fetch_array($result)) {
                    if (Users_Privileges_Model::isPermitted('Orders', 'EditView', $arr['ordersid']) == 'yes') {
                        if ($arr['orders_contacts'] != '' && $arr['orders_contacts'] != '0') {
                            $ordersContact = Vtiger_Record_Model::getInstanceById($arr['orders_contacts'], 'Contacts');
                            $lastName = $ordersContact->get('lastname');
                            $firstName = $ordersContact->get('firstname');
                        } else {
                            $lastName = '';
                            $firstName = '';
                        }
                        if ($arr['orders_account'] != '' && $arr['orders_account'] != '0') {
                            $ordersAcccount = Vtiger_Record_Model::getInstanceById($arr['orders_account'], 'Accounts');
                            $accountName = $ordersAcccount->get('accountname');
                        } else {
                            $accountName = '';
                        }

                        $drivernotes = (strlen($arr['drivers_notes']) > 0) ? $arr['drivers_notes'] : 'No notes from this driver.';

                        $html .= '<tr class="listViewEntries" data-id="' . $arr['orderstaskid'] . '" data-orderid="' . $arr['ordersid'] . '">
                            <td class="" data-field-type="date" nowrap="">' . ($arr['orderstask_ldate'] != '' ? Vtiger_Date_UIType::getDisplayDateValue($arr['orderstask_ldate']) : '') . '</td>
                            <td class="" data-field-type="date" nowrap=""><div class="input-append row-fluid" style="  min-width: 150px;"><div class="span12 row-fluid date"><input type="text" class="dateField" name="pickup_date_" data-date-format="" type="text" value="' . ($arr['orderstask_pldate'] != '' ? Vtiger_Date_UIType::getDisplayDateValue($arr['orderstask_pldate']) : '') . '" style="width:100px;"/><span class="add-on"><i class="icon-calendar"></i></span></div></div></td>
                            <td class="" data-field-type="string" nowrap="">' . $accountName . '</td>
                            <td><a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="' . $drivernotes . '">Drivers Notes</a></td>
                            <td class="" data-field-type="string" nowrap="">' . $lastName . '</td>
                            <td class="" data-field-type="string" nowrap="">' . $firstName . '</td>
                            <td class="" data-field-type="string" nowrap="">' . $arr['orders_no'] . '</td>
                            <td class="" data-field-type="double" nowrap=""><input type="text" value="' . $arr['orders_eweight'] . '" class="eweight" style="width: 80%;"></td>
                            <td class="" data-field-type="double" nowrap="">' . $arr['orders_aweight'] . '</td>
                            <td class="" data-field-type="double" nowrap=""><input type="text" value="' . Vtiger_Currency_UIType::transformDisplayValue($arr['orders_elinehaul']) . '" class="elinehaul" style="width: 80%;"></td>
                            <td class="" data-field-type="date" nowrap="">' . $arr[''] . '</td>
                            <td class="" data-field-type="string" nowrap="">' . $arr[''] . '</td>
                            <td class="" data-field-type="string" nowrap="">' . $arr[''] . '</td>
                            <td class="" data-field-type="string" nowrap="">' . $arr['origin_city'] . '</td>
                            <td class="" data-field-type="string" nowrap="">' . $arr['origin_state'] . '</td>
                            <td class="" data-field-type="string" nowrap="">' . $arr[''] . '</td>
                            <td class="" data-field-type="string" nowrap="">' . $arr['destination_city'] . '</td>
                            <td class="" data-field-type="string" nowrap="">' . $arr['destination_state'] . '</td>
                            <td class="" data-field-type="string" nowrap="">' . $arr[''] . '</td>
                            <td class="" data-field-type="string" nowrap="">' . $arr[''] . '</td>
                            <td class="" data-field-type="string" nowrap="">' . $arr[''] . '</td>
                            <td class="" data-field-type="string" nowrap="">' . $arr[''] . '</td>
                            <td class="" data-field-type="double" nowrap="">' . $arr['orders_gweight'] . '</td>
                            <td class="" data-field-type="double" nowrap="">' . $arr['orders_tweight'] . '</td>
                            <td class="netweight" data-field-type="double" nowrap="">' . $arr['orders_netweight'] . '</td>
                            <td class="" data-field-type="string" nowrap="">' . $arr[''] . '</td>
                            <td class="">
                                <i id="' . $arr['orderstaskid'] . '" class="icon-eye-open"></i> 
                                <i id="' . $arr['orderstaskid'] . '" class="icon-pencil"></i> 
                            </td>
                        </tr>';
                    }
                }
            }
            $html .= '<input type="hidden" value="' . $response . '" id="total_weight">';
            return $html;
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }
    
    
    public function deleteCheckin(Vtiger_Request $request)
    {
        $driverCheckinId = $request->get('drivercheckin_id');
	$user = Users_Record_Model::getCurrentUserModel();
	$id = vtws_getWebserviceEntityId('TripsDriverCheckin', $driverCheckinId);
	
	try {
	    vtws_delete($id, $user);
	    return 'ok';
	} catch (Exception $exc) {
	    return 'fail';
	}
    }
}
