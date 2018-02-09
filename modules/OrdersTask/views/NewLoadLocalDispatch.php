<?php

include_once 'include/fields/DateTimeField.php';
include_once 'include/Webservices/Revise.php';

class OrdersTask_NewLoadLocalDispatch_View extends Vtiger_ListAjax_View
{
    protected $forModule             = 'OrdersTask';
    protected $dayDuration           = 8;
    protected $default_start         = '08:00:00';
    protected $default_task_duration = 1;

    public function __construct()
    {
        parent::__construct();
        $this->exposeMethod('checkForShowCallModal');
        $this->exposeMethod('copyResources');
        $this->exposeMethod('updateVendor');
        $this->exposeMethod('updateAssignedDate');
        $this->exposeMethod('updateCheckCallField');
        $this->exposeMethod('showCallModal');
        $this->exposeMethod('primaryRoleNLeadUpdate');
        $this->exposeMethod('updateTimes');
        $this->exposeMethod('updateDispatchStatus');
        $this->exposeMethod('massTaskResourceHandler');
        $this->exposeMethod('taskResourceHandler');
        $this->exposeMethod('getEmployeeTable');
        $this->exposeMethod('getVehiclesTable');
        $this->exposeMethod('getVendorsTable');
        $this->exposeMethod('getDirectionsData');
        $this->exposeMethod('dragDropUpdate');
        $this->exposeMethod('getParticipantAgentsData');
        $this->exposeMethod('deleteCustomFilterByID');
    }
	
	function deleteCustomFilterByID(Vtiger_Request $request) {
		$toDelete = $request->get('toDelete');
		
		try {
			$filter = Vtiger_Filter::getInstance($toDelete);
			$filter->delete();
		    $message = 'Ok';
		 } catch (Exception $exc) {
		    $message = $exc->message;
		 }
		
		$msg = new Vtiger_Response();
		$msg->setResult($message);
		$msg->emit();
	}
	
    function getColumnsByFilter($cvid) {
		$customView = new CustomView();
		$auxArr = $customView->getColumnsListByCvid($cvid);

		$returnArray = array();
		foreach ($auxArr as $auxItem) {
			$aux = explode(":", $auxItem);
			$auxS = explode("_", $aux[3]);
			$fieldModel = Vtiger_Field_Model::getInstance($aux[1], Vtiger_Module_Model::getInstance($auxS[0]));
			if ($fieldModel) {
				$returnArray[] = $aux[1];
			}
		}

		return $returnArray;
    }
	
	function getViewColumns($tableType, $filterID = NULL){
	    
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$sessionViewName =  ($tableType == "A") ? "crewView" : (($tableType == "E") ? "equipmentView" : "vendorView");

		if($filterID !='0' && $filterID !='' && $filterID != NULL){
			$_REQUEST["customview"] = $filterID;
			$returnVar = $this->getColumnsByFilter($filterID);
		}elseif(isset($_SESSION['lvs']['OrdersTask'][$currentUserModel->getId()][$sessionViewName]) && $_SESSION['lvs']['OrdersTask'][$currentUserModel->getId()][$sessionViewName] != ""){
			$cvid = $_SESSION['lvs']['OrdersTask'][$currentUserModel->getId()][$sessionViewName];
			$_REQUEST["customview"] = $cvid;
			$returnVar = $this->getColumnsByFilter($cvid);
		}else{
			$cvid = $this->getNoCVIDCurrentFilter($tableType);
			$_REQUEST["customview"] = $cvid;
			$returnVar = $this->getColumnsByFilter($cvid);
		}
		
		return $returnVar;
	}
	
	function getNoCVIDCurrentFilter($tableType){
		$db = PearDatabase::getInstance();
		
		$viewName = ($tableType == "A") ? "NewLocalDispatchCrew" : (($tableType == "E") ? "NewLocalDispatchEquipment" : "NewLocalDispatchVendors");
		$result = $db->pquery("SELECT cvid FROM vtiger_customview WHERE setdefault = 1 AND view = ?", array($viewName));
		if($db->num_rows($result) > 0){
			$cvid = $db->query_result($result, 0, "cvid");
		}else{
			$result = $db->pquery("SELECT cvid FROM vtiger_customview WHERE view = ? ORDER BY cvid ASC LIMIT 1", array($viewName));
			if($db->num_rows($result) > 0){
				$cvid = $db->query_result($result, 0, "cvid");
			}else{
				$cvid = 0; //supongo que no deberia llegar aca nunca
			}
		}
		
		return $cvid;
	}
	
    function getTableHeaders($selectedColumns, $tableType){
		$headers = [];
		$moduleName = ($tableType == "A") ? "Employees" : (($tableType == "E") ? "Vehicles" : "Vendors");
		
		foreach($selectedColumns as $fieldName){
			$fieldModel =  Vtiger_Field_Model::getInstance($fieldName,Vtiger_Module_Model::getInstance($moduleName));
			if($fieldModel){
				$fieldLabel = $fieldModel->get("label");
			}else if($fieldName == "smownerid"){
				$fieldLabel = "Assigned To";
			}else{
				$fieldLabel = $fieldName;
			}
			$headers[] = vtranslate($fieldLabel, $moduleName);
		}
		return $headers;
	}
    
    public function getEmployeeTable(Vtiger_Request $request) {
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
        $arr = ( $request->get("arrPar") ? $request->get("arrPar") : array() );
        if(!is_array($request->get('task_id'))){
            $orderTaskIdArray = array_filter(explode(',',$request->get('task_id')));
        }else{
            $orderTaskIdArray = $request->get('task_id');
        }
		$linkFields = array('name','employee_lastname');
		$employeesModel = Vtiger_Module_Model::getInstance('Employees');


		if($request->get('customview') !== ''){
			$customView = $request->get('customview');
			$_SESSION['lvs']['OrdersTask'][$currentUserModel->getId()]['crewView'] = $customView; //Store in user session the last used filter so we dont fallback to default each time
		}elseif(isset($_SESSION['lvs']['OrdersTask'][$currentUserModel->getId()]['crewView']) && $_SESSION['lvs']['OrdersTask'][$currentUserModel->getId()]['crewView'] != ""){
		   $customView = $_SESSION['lvs']['OrdersTask'][$currentUserModel->getId()]['crewView'];
		}
		$assignedResources = $this->getAssignedResourcesIds($orderTaskIdArray, "Employee");
		$selectedColumns = $this->getViewColumns("A", $customView);
		$employees = $this->getAvailableEmployees($orderTaskIdArray, $arr , $assignedResources, $selectedColumns);

		if (strtolower(getenv('INSTANCE_NAME')) != "graebel") {
			$scheduledTime = $this->resourceScheduledTime($orderTaskIdArray,'employees');
		}

		$html .= '<div class="employees-tables" id="employees-table">';
		$html .= '<input type="hidden" name="employee-pagging" value="no">';
		$rowName = "";


		$headers = $this->getTableHeaders($selectedColumns, "A");
		$html .= '<table id="employeeDataTable" class="table listViewEntriesTable" style="">
                <thead>
                <tr class="searchViewHeaders">
				<th><button class="btn btn-success" id="assign_employee" style="display:block;margin:1% auto;">Update</button></th>
				<th><button class="btn" id="searchEmployee">Search</button></th>
				<th colspan="1"></th>';
		foreach ($selectedColumns as $fieldName) {
			$html .= '<th><input class="listSearchContributor" data-fieldname="' . $fieldName . '" type="text" value="" placeholder="Search.." style="max-width: 100px;"></th>';
		}
		if (strtolower(getenv('INSTANCE_NAME')) != "graebel") {
			$html .= '<th colspan="2"></th>';
		} //availabe hs, scheduled hs

		$html .= '</tr>
					<tr class="listViewHeaders">
					<th>' . vtranslate('LBL_ASSIGNED', 'OrdersTask') . '</th>
					<th>' . vtranslate('LBL_EMPLOYEE_LEAD', 'OrdersTask') . '</th>
				<th>' . vtranslate('LBL_EMPLOYEE_ROLE', 'OrdersTask') . '</th>';

		foreach ($headers as $fieldLabel) {
			$html .= '<th>' . vtranslate($fieldLabel, 'Employees') . '</th>';
		}
		if (strtolower(getenv('INSTANCE_NAME')) != "graebel") {
			$html .= '<th>' . vtranslate('LBL_EMPLOYEE_AVAILABLE', 'OrdersTask') . '</th>';
			$html .= '<th>' . vtranslate('LBL_EMPLOYEE_SCHUDELED', 'OrdersTask') . '</th>';
		}

        $html .= '</tr></thead>';
        $html .= '<tbody>';
		if (count($employees) > 0) {
			foreach ($employees as $employeeid => $employee) {
				$checked = '';
				$isLead = '';
				if (in_array($employeeid, $assignedResources)) {
					$checked = 'checked';
					$isLead = ($this->getLeadEmployee($employeeid, $orderTaskIdArray)) ? "checked" : "";
					$employeeRoleDropdownByTaskid = $this->getPrimaryRoleDropdown($employeeid, $orderTaskIdArray, true);
				} else {
					$employeeRoleDropdownByTaskid = $this->getPrimaryRoleDropdown($employeeid, $orderTaskIdArray, false);
				}
				$isOnNotice = $employee['on_notice'];
				$makeItRed = '';
				if ($isOnNotice) {
					$makeItRed = 'redColor" title="This driver is On Notice';
				}
				$rowBackgroundGrey = '';
				if ($employee['conflicting']) {
					$rowBackgroundGrey = 'conflicting-resource';
				}
				$ajaxSearched = '';
				if(count($arr) > 0){
					$ajaxSearched = ' ';
					//Clear $html beacause we only need the employee rows
					$html = ' ajaxSearched ';
				}
				$html .= '<tr  class="employees ' .
					$ajaxSearched . ' ' .
					$rowName . ' ' .
					$rowBackgroundGrey . ' ' .
					strtolower(str_replace(' ', '', $employee['employee_type'])) .
					' draggable-resource' .
					$disable .
					'" id="' .
					$employeeid .
					'">';
				$html .= '<td><input  class="assigned_resource' . $disable . '" ' . $checked . ' id="assigned_' . $employeeid . '" data-id="' . $employeeid . '" type="checkbox"></td>';
				$html .= '<td><input  class="lead_resource" ' . $isLead . ' id="lead_' . $employeeid . '" data-id="' . $employeeid . '" type="radio" name="optradio"></td>';

				$html .= '<td class="e_prole">' . $employeeRoleDropdownByTaskid . '</td>';

				foreach ($selectedColumns as $fieldName) {
					if ($key == "hiddensmownerid") {
						$html .= '<input type="hidden" value="' . $fieldValue . '" id="ahiddensmownerid">';
					} else {
						$fieldModelInstance = $employeesModel->getField($fieldName);

						if($fieldModelInstance && ($fieldModelInstance->isReferenceField() || $fieldModelInstance->getFieldDataType() == 'agentpicklist')){
							$html .= '<td><div class="tdwrapp"> ' . $fieldModelInstance->getDisplayValue($employee['employee_model']->get($fieldName)) . '</div></td>';
						}elseif ( in_array($fieldName,$linkFields)) {
							$html .= '<td class="'.$makeItRed.'">
								<span class="value" data-field-type="reference">
									<a href="index.php?module=Employees&amp;view=Detail&amp;record=' . $employeeid . '" data-original-title="Employees">'. $fieldModelInstance->getDisplayValue($employee['employee_model']->get($fieldName)) .'</a>
								</span>
							</td>';
						}else{
							$html .= '<td><div class="tdwrapp '.$makeItRed.'">' . $employee['employee_model']->get($fieldName) . '</div></td>';
						}
					}
				}
				if (strtolower(getenv('INSTANCE_NAME')) != "graebel") {
					$html .= '<td class="avail_hs"><div class="tdwrapp">' . $employee['available_hours'] . '</div></td>';
					if (key_exists($employeeid, $scheduledTime)) {
						$schHs = $scheduledTime[$employeeid];
					} else {
						$schHs = 0;
					}
					$overTimeHs = '';
					if ($schHs > 40) {
						$overTimeHs = ' style="background-color:red;" ';
					}
					$html .= '<td class="sch_hs" ' . $overTimeHs . '><div class="tdwrapp">' . $schHs . '</div></td>';
				}
				$html .= '</tr>';
			}
		}
	
		
		$html .= '</tbody></table>';
		$html .= '</div>';
		
		$msg = new Vtiger_Response();
		$msg->setResult($html);
		$msg->emit();
    }

    public function getLeadEmployee($employeeId, $taskIds){
        $db  = PearDatabase::getInstance();
        $leadEmployee = false;
	
	// $taskIds used to be an array, now only one task can be selected at the time?
	if(is_array($taskIds)){
	    $taskId = $taskIds[0];
	}
	
        $result = $db->pquery("SELECT lead FROM vtiger_orderstasksemprel WHERE employeeid = ? AND taskid IN (".$taskId.")", array($employeeId));
        if ($db->num_rows($result) > 0) {
            $leadEmployee = ($db->query_result($result, 0, 'lead') == "1" ? true : false);
        }

        return $leadEmployee;
    }
    
    public function getAssignedResourcesIds($orderTaskIdArray, $resourceType){
        $db  = PearDatabase::getInstance();
        $dbVar = ($resourceType == "Vehicle") ? "assigned_vehicles" : (($resourceType == "Vendors") ? "assigned_vendor" : "assigned_employee");
	$assignedResourcesArray = [];
        if (!is_array($orderTaskIdArray)) {
	    $orderTaskIdArray = [$orderTaskIdArray];
	}
	foreach ($orderTaskIdArray as $taskId) {
	    $taskId = Vtiger_Record_Model::getInstanceById($taskId, 'OrdersTask')->getId();
	    $result = $db->pquery("SELECT $dbVar FROM vtiger_orderstask WHERE orderstaskid = ?", array($taskId));

	    if ($db->num_rows($result) > 0) {
                $assignedResources = $db->query_result($result, 0, $dbVar);
		$resourcesArray = explode(' |##| ', $assignedResources);
		$resourcesArray = array_filter($resourcesArray);
		$assignedResourcesArray = array_merge($assignedResourcesArray, $resourcesArray);
	    }
	}

        return $assignedResourcesArray;
    }
      
    public function resourceScheduledTime($taskId, $resourceType) {
	$db = PearDatabase::getInstance();
	$ordersTaskInstance = Vtiger_Module_Model::getInstance('OrdersTask');
	$orderTaskRecord = Vtiger_Record_Model::getInstanceById($taskId, 'OrdersTask');
	$agentManagerRecord = Vtiger_Record_Model::getInstanceById($orderTaskRecord->get('agentid'), 'AgentManager');
	$payrollWeekStartDay = ($agentManagerRecord->get('payroll_week_start_date') == '' ? 'Sunday' : $agentManagerRecord->get('payroll_week_start_date'));
	$date = $orderTaskRecord->get('disp_assigneddate');
	if (!$date) {
	    $date = $orderTaskRecord->get('service_date_from');
	}
	$firstWorkingDay = date('Y-m-d', strtotime("last " . $payrollWeekStartDay, strtotime($date)));
	$lastWorkingDay = date('Y-m-d', strtotime("+6 days", strtotime($firstWorkingDay)));
	$agentWhere = $ordersTaskInstance->getCapacityCalendarAgentWhere();

	$sql = "SELECT orderstaskid, assigned_employee, assigned_vehicles, disp_assignedstart, disp_actualend, estimated_hours,
                    (
                    CASE 
                    WHEN vtiger_orderstask.disp_assigneddate IS NULL
                    THEN vtiger_orderstask.service_date_from
                    ELSE vtiger_orderstask.disp_assigneddate 
                    END
                    ) AS calendar_date
                    FROM vtiger_orderstask
			INNER JOIN vtiger_crmentity ON vtiger_orderstask.orderstaskid = vtiger_crmentity.crmid
                    INNER JOIN vtiger_agents ON vtiger_orderstask.participating_agent = vtiger_agents.agentsid
                    WHERE deleted = 0 AND $agentWhere 
                    GROUP BY vtiger_orderstask.orderstaskid
                    HAVING calendar_date >= ? AND calendar_date <= ?";

	$resourcesScheduleTime = [];
	$result = $db->pquery($sql, [$firstWorkingDay, $lastWorkingDay]);
	if ($db->num_rows($result) > 0) {
	    while ($row = $db->fetchByAssoc($result)) {
		$startHour = $row['disp_assignedstart'];
		$endHour = $row['disp_actualend'];
                if($startHour == null || $endHour == null){
                    $interval = $row['estimated_hours'] * 3600;
                }else{
                    $datetime1 = strtotime($date . $startHour);
                    $datetime2 = strtotime($date . $endHour);
                    if($datetime2 < $datetime1){
                        $datetime2 = strtotime($date . $endHour." +1 day");
                    }
                    $interval = abs($datetime2 - $datetime1);
                }
                
		if ($resourceType == 'employees') {
		    $resourcesId = array_filter(explode(' |##| ', $row['assigned_employee']));
		    foreach ($resourcesId as $resource) {
			$resourcesScheduleTime[$resource] = $resourcesScheduleTime[$resource] + round($interval / 3600, 2);
		    }
		} else {
		    $resourcesId = array_filter(explode(' |##| ', $row['assigned_vehicles']));
		    foreach ($resourcesId as $resource) {
			$resourcesScheduleTime[$resource] = $resourcesScheduleTime[$resource] + round($interval / 3600, 2);
		    }
		}
	    }
	}

	return $resourcesScheduleTime;
    }
    
    public function getAvailableEmployees($taskIdsArray, $arr = array(), $assignedResources,$selectedColumns){
	
        $db = PearDatabase::getInstance();        
        $orderTaskModel = Vtiger_Module_Model::getInstance('OrdersTask');
        $dateArray = $orderTaskModel->getFromDateAndStartEndTime($taskIdsArray);
                
        
        if(!is_array($selectedColumns)){
            $selectedColumns = explode(",", $selectedColumns);
        } 
        
        $employeesByDate = $orderTaskModel->getEmployeesByDateAndRole($dateArray['fromDate'], $dateArray['toDate'], '', '', 'all', true, $taskIdsArray, $arr);
        
        $employees = $employeesByDate[key($employeesByDate)];
        foreach ($employeesByDate as $date => $employeesArray) {
            if(in_array($date, $dateArray['datesArray'])){
                $employees = array_intersect_key($employees, $employeesArray);
            }    
        }

        $conflictingResources = $this->getConflictingResources($taskIdsArray,'employees');
        foreach ($employees as $employeeId => $value) {
            if (in_array($employeeId, $conflictingResources)) {
                $employees[$employeeId]['conflicting'] = true;
            }else {
                $employees[$employeeId]['conflicting'] = false;
            }
        }
           

        $onNoticeEmployees = $orderTaskModel->getOutOfServiceEmployeesByDate($dateArray['fromDate'],$dateArray['toDate'], 'On Notice');
        //If Employee has at least one On notice entry let show it as on notice even if the lastest entry in the outofservice is status = blank. 
        //if Employee has only one entry with status = blank I do NOT show those in red!
	    $onNoticeIds = [];

        foreach ($onNoticeEmployees as $date => $ids) {
            if(in_array($date, $dateArray['datesArray'])){
                $onNoticeIds = array_merge($onNoticeIds,array_values($ids));
            }
        }

        $onNoticeIds = array_unique($onNoticeIds);
        //mark the employees that have an on notice
        foreach ($employees as $id => $info) {//initialize
            $employees[$id]['on_notice'] = 0;
        }

        foreach ($employees as $id => $info) {
            if (in_array($id, $onNoticeIds)) {
                $employees[$id]['on_notice'] = 1;
            }
        }

        return $employees;
    }
   
    public function getVehiclesTable(Vtiger_Request $request){
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
        if(!is_array($request->get('task_id'))){
            $orderTasksArray = array_filter(explode(',',$request->get('task_id')));
        }else{
            $orderTasksArray = $request->get('task_id');
        }
        $assignedResources = $this->getAssignedResourcesIds($orderTasksArray, "Vehicle");
		$customView = (isset($_SESSION['lvs']['OrdersTask'][$currentUserModel->getId()]['equipmentView']) && $_SESSION['lvs']['OrdersTask'][$currentUserModel->getId()]['equipmentView'] != "") ? $_SESSION['lvs']['OrdersTask'][$currentUserModel->getId()]['equipmentView'] : $request->get('customview');
		
	$selectedColumns = $this->getViewColumns("E", $customView);
        $html     = '';

	$vehicles = $this->getAvailableVehicles($orderTasksArray, $assignedResources,$selectedColumns);

        if (strtolower(getenv('INSTANCE_NAME')) != "graebel") {
            $scheduledTime = $this->resourceScheduledTime($orderTasksArray,'vehicles');
	}

        if (count($vehicles) > 0) {
			//$html .= '<span class="filterActionsDivEquipment hide"><hr><ul class="filterActions"><li onclick="OrdersTask_LocalDispatch_Js.ldFilterCreate(this);" data-rigthtable="Equipment" data-createurl="index.php?module=CustomView&view=EditAjax&source_module=Vehicles&customView=NewLocalDispatchEquipment"><i class="icon-plus-sign"></i> '.vtranslate('LBL_CREATE_NEW_FILTER').'</li></ul></span>';
            $html .= '<div class="vehicles-tables">';
			$currentUserModel = Users_Record_Model::getCurrentUserModel();
			$headers = $this->getTableHeaders($selectedColumns,"E");
			$request->set('customview',$_REQUEST['customview']); //update $request object with the customview (cvid) get on the other function (getViewColumns)
			if($request->get('isChange') == "true"){
				$_SESSION['lvs']['OrdersTask'][$currentUserModel->getId()]['equipmentView'] = $request->get('customview');
			}
            $html .= '<table id="vehiclesDataTable" class="table listViewEntriesTable">
                <thead><tr class="searchViewHeaders">
                    <th><button class="btn btn-success" id="assign_vehicle" style="display:block;margin:1% auto;">Update</button></th>
                    <th><button class="btn" id="SearchVehicle">Search</button></th>';
					foreach($selectedColumns as $fieldName){
						$html .= '<th><input class="listSearchContributor" data-fieldname="'.$fieldName.'" type="text" value="" placeholder="Search.." style="max-width: 100px;"></th>';
					}
			if (strtolower(getenv('INSTANCE_NAME')) != "graebel") {
            //if (getenv('INSTANCE_NAME') == "core") {
		$html .= '<th></th>';
	    } //scheduled hs
            
			$html .= '</tr>
                <tr class="listViewHeaders">
                <th>'.vtranslate('LBL_ASSIGNED', 'OrdersTask').'</th><th></th>';
			
			foreach($headers as $fieldLabel){
				$html .= '<th>' . vtranslate($fieldLabel, 'Vehicles') . '</th>';
			}

			if (strtolower(getenv('INSTANCE_NAME')) != "graebel") {
            //if (getenv('INSTANCE_NAME') == "core") {
		$html .= '<th>' . vtranslate('LBL_EMPLOYEE_SCHUDELED', 'OrdersTask') . '</th>';
	    }

            $html .= '<th></th></tr>
                </thead>
                <tbody>';
            foreach ($vehicles as $vehicle) {
                $checked = '';
                if (in_array($vehicle['id'], $assignedResources)) {
                    $checked = 'checked';
                }
               
				$makeItRed = '';
                if ($vehicle['on_notice']) {
                    $makeItRed = 'redColor" title="This vehicle is On Notice';
                }
                $rowBackgroundGrey = '';
                if($vehicle['conflicting']){
                    $rowBackgroundGrey = 'conflicting-resource';
                }
                $html .= '<tr class="vehicle '.strtolower(str_replace(' ', '', $vehicle['status'])).' draggable-resource'.$disable.' '.$rowBackgroundGrey.' '.$makeItRed.'" id="'.$vehicle['id'].'">';
                $html .= '<td><input class="resource_vehicle'.$disable.'" '.$checked.$checkDisabled.'  id="assigned_'.$vehicle['id'].'" type="checkbox" data-id="'.$vehicle['id'].'"></td><td></td>';

				foreach($vehicle as $key => $fieldValue){
					if($key != "id" && $key != 'status' && $key != 'on_notice'){
						if($key == "hiddensmownerid"){
							$html .= '<input type="hidden" value="' . $fieldValue . '" id="ehiddensmownerid">';
						}elseif ( $key == 'vechiles_unit'){
							$html .= '<td>
                                    <span class="value" data-field-type="reference">
                                        <a href="index.php?module=Vehicles&amp;view=Detail&amp;record='.$vehicle['id'].'" data-original-title="Vehicles">'.$fieldValue.'</a>
                                    </span>
                                </td>';
                        }else{
							$html .= '<td><div class="tdwrapp">' . $fieldValue . '</div></td>';
						}
					}
				}
			if (strtolower(getenv('INSTANCE_NAME')) != "graebel") {
            //if (getenv('INSTANCE_NAME') == "core") {
                    if(key_exists($vehicle['id'], $scheduledTime)){
                        $schHs = $scheduledTime[$vehicle['id']];
                    }else{
                        $schHs = 0;
                    }
                    $html .= '<td class="sch_hs">'.$schHs.'</td>';
                }
                $html .= '</tr>';

            }
			
            $html .= '</tbody>'.$footer.'</table>';
            $html .= '</div>';
        }

        $msg    = new Vtiger_Response();
        $msg->setResult($html);
        $msg->emit();
    }    
	
    public function getAvailableVehicles($taskIdsArray = '', $assignedResources, $selectedColumns){
        
        $ordersTaskModel = Vtiger_Module_Model::getInstance('OrdersTask');
        $datesAndTimes = $ordersTaskModel::getFromDateAndStartEndTime($taskIdsArray);
        $startDate = $datesAndTimes['fromDate'];
        $endDate = $datesAndTimes['toDate'];
        $vehicles = [];
        $vehiclesArry = $ordersTaskModel->getVehiclesByDateAndType($startDate, $endDate, '', '', $taskIdsArray, $assignedResources, $selectedColumns);
        if(!is_array($vehiclesArry)){
            $vehiclesArry = [];
        }

        $vehicles = $vehiclesArry[key($vehiclesArry)];
        foreach ($vehiclesArry as $date => $vehiclesInfo) {
            if(in_array($date, $datesAndTimes['datesArray'])){
                $vehicles = array_intersect_key($vehicles, $vehiclesInfo);
            }    
        }


        $onNoticeVehicles = $ordersTaskModel->getOutOfServiceVehiclesByDate($startDate,$endDate, array_keys($vehicles), 'On Notice');
		//If Vehicle has at least one On notice entry let show it as on notice even if the lastest entry in the outofservice is status = blank. 
		//if vehicle has only one entry with status = blank I do NOT show those in red!
	$onNoticeIds = [];

        foreach ($onNoticeVehicles as $date => $ids) {
            if(in_array($date, $datesAndTimes['datesArray'])){
                $onNoticeIds = array_merge($onNoticeIds,array_values($ids));
            }
        }
        $onNoticeIds = array_unique($onNoticeIds);
        //mark the vehicles that have an on notice
        foreach ($vehicles as $vhId => $vhInfo) {//initialize
            $vehicles[$vhId]['on_notice'] = 0;
        }
        foreach ($vehicles as $vhId => $vhInfo) {
            if (in_array($vhId, $onNoticeIds)) {
                $vehicles[$vhId]['on_notice'] = 1;
            }
        }
        
        $conflictingResources = $this->getConflictingResources($taskIdsArray,'vehicles');
        foreach ($vehicles as $id => $value) {
                if (in_array($id, $conflictingResources)) {
                $vehicles[$id]['conflicting'] = true;
                }else {
                $vehicles[$id]['conflicting'] = false;
                }
            }

        return $vehicles;
    }
    
    public function getDirectionsData(Vtiger_Request $request){
        $tasksId = explode(",", $request->get('task_id'));
        $directionsArray = array();
        
        foreach ($tasksId as $taskId) {
            $return = $this->getDirInfo($taskId);
            if ($return) {
                $directionsArray[] = $return;
            }
        }
                
        $msg = new Vtiger_Response();
        $msg->setResult($directionsArray);
        $msg->emit();
    }
    
    public function getDirInfo($taskId){
        $db = PearDatabase::getInstance();
        
        $sql = "SELECT crel.* FROM vtiger_crmentityrel crel 
            INNER JOIN vtiger_crmentity cr1 ON crel.crmid = cr1.crmid 
            INNER JOIN vtiger_crmentity cr2 ON crel.relcrmid = cr2.crmid 
            WHERE (module = 'Orders' OR module = 'OrdersTask') 
            AND (relmodule = 'OrdersTask' OR relmodule = 'Orders') 
            AND cr1.deleted = 0 AND cr2.deleted = 0 
            AND (crel.crmid = ? OR crel.relcrmid = ?)";
                
        $result = $db->pquery($sql, [$taskId, $taskId]);
        if ($db->num_rows($result) > 0) {
            while ($row = $db->fetch_row($result)) {
                $orderID = ($row[module] == 'Orders') ? $row[crmid] : $row[relcrmid];
                $orderEntity = Vtiger_Record_Model::getInstanceById($orderID, "Orders");
                $data = $orderEntity->getData();
                
                $origAddress = ($data[origin_address1]) ? $data[origin_address1] : $data[origin_address2];
                $origCountry = ($data[origin_country]) ? $data[origin_country] : "United States";
                
                $from = $origAddress.','.$data[origin_city].','.$data[origin_state].','.$origCountry;
                
                $destAddress = ($data[destination_address1]) ? $data[destination_address1] : $data[destination_address2];
                $destCountry = ($data[destination_country]) ? $data[destination_country] : "United States";
                
                $to = $destAddress.','.$data[destination_city].','.$data[destination_state].','.$destCountry;
                
                return array("from" => $from, "to" => $to);
            }
        } else {
            return null;
        } 
    }
        
    public function taskResourceHandler(Vtiger_Request $request){
        $db = PearDatabase::getInstance();
        $taskIdArray = $request->get('task_id');
        $leadEmployeeID = $request->get('lead_employee_id');
        $resourcesId = $request->get('resource_ids');
        $primaryRoles = $request->get('proles');
        $resourceType = $request->get('resource_type');
        $dbVar = ($resourceType == "Vehicle") ? "assigned_vehicles" : (($resourceType == "Vendor") ? "assigned_vendor" : "assigned_employee");
        $currentUser = Users_Record_Model::getCurrentUserModel();
        
        if (!is_array($taskIdArray)) {
            $taskIdArray = array($request->get('task_id'));
        }
        
	foreach ($taskIdArray as $taskID) {
	    $result = $db->pquery("SELECT $dbVar FROM vtiger_orderstask WHERE orderstaskid = ?", [$taskID]);
	    if ($db->num_rows($result) > 0) {
            $resourcesIdsArray = implode(' |##| ', array_filter($resourcesId));
            $orderTask = [
                'id'                => vtws_getWebserviceEntityId('OrdersTask', $taskID),
                $dbVar => $resourcesIdsArray
            ];

            switch ($resourceType) {
                case 'Vehicle':
                    $orderTask['actual_of_vehicles'] = count(explode(' |##| ', $resourcesIdsArray));
                break;
                case 'Employee':
                    $orderTask['actual_of_crew'] = count(explode(' |##| ', $resourcesIdsArray));
                break;
                default:
                break;
            }

		try {
		    vtws_revise($orderTask, $currentUser);
		    $orderTasksRecordModel = Vtiger_Record_Model::getInstanceById($taskID, 'OrdersTask');

		    switch ($resourceType) {
			case 'Vehicle':
			    $resources = $orderTasksRecordModel->getVehicles($resourcesIdsArray);
			    break;
            case 'Vendor':
                            $resources = $orderTasksRecordModel->getVendors($resourcesIdsArray);
                            break;
            case 'Employee':
                            $resources = $orderTasksRecordModel->getEmployees($resourcesIdsArray);
                 $db->pquery("DELETE FROM vtiger_orderstasksemprel WHERE taskid = ?", [$taskID]);
                for ($i=0;$i<count($primaryRoles);$i++) {
				$lead = ($resourcesId[$i] == $leadEmployeeID) ? "1" : "0";
                    $db->pquery("INSERT INTO vtiger_orderstasksemprel(taskid, employeeid, role, lead) VALUES (?,?,?,?)", [$taskID, $resourcesId[$i], $primaryRoles[$i], $lead]);
			    }
			default:
			    break;
		    }
		   

		    $result = ['result' => 'OK', 'resources' => $resources];
		 } catch (Exception $exc) {
		    $result = ['result' => 'fail', 'msg' => $exc->message];
		 }
	    }
	}
        
        $msg = new Vtiger_Response();
        $msg->setResult($result);
        $msg->emit();
    }
    
    public function dragDropUpdate(Vtiger_Request $request){
        $db = PearDatabase::getInstance();
        $removeTaskID = $request->get('remove_taskid');
        $targetTaskID = $request->get('target_taskid');
        $resID = $request->get('res_id');
        $resourceType = $request->get('resource_type');
        $dbVar = ($resourceType == "Vehicle") ? "assigned_vehicles" : (($resourceType == "Vendor") ? "assigned_vendor" : "assigned_employee");
        $currentUser = Users_Record_Model::getCurrentUserModel();
        
        //remove from original task
        $result = $db->pquery("SELECT $dbVar FROM vtiger_orderstask WHERE orderstaskid = ?", array($removeTaskID));
        if ($db->num_rows($result) > 0) {
            $row = $db->fetch_row($result);
            $ids = explode(' |##| ', $row[0]);
            $index = array_search($resID, $ids);
            unset($ids[$index]);
            $resourcesIdsArray = implode(' |##| ', array_filter($ids));

            $orderTask = [
                'id'                => vtws_getWebserviceEntityId('OrdersTask', $removeTaskID),
                $dbVar => $resourcesIdsArray
            ];


            switch ($resourceType) {
                case 'Vehicle':
                    $orderTask['actual_of_vehicles'] = count(explode(' |##| ', $resourcesIdsArray));
                break;
                case 'Employee':
                    $orderTask['actual_of_crew'] = count(explode(' |##| ', $resourcesIdsArray));
                break;
                default:
                break;
            }


            try {
                vtws_revise($orderTask, $currentUser);
                $orderTasksRecordModel = Vtiger_Record_Model::getInstanceById($removeTaskID, 'OrdersTask');
                
		    switch ($resourceType) {
    			case 'Vehicle':
    			    $resources = $orderTasksRecordModel->getVehicles($resourcesIdsArray);
    			    break;
                case 'Vendor':
                    $resources = $orderTasksRecordModel->getVendors($resourcesIdsArray);
                    break;
                case 'Employee':
                    $resources = $orderTasksRecordModel->getEmployees($resourcesIdsArray);
    			default:
    			    break;
		    }
                
                $result = ['result' => 'OK', 'resources' => $resources];
             } catch (Exception $exc) {
                $result = ['result' => 'fail', 'msg' => $exc->message];
             }
        }
        //add to target task
        $result = $db->pquery("SELECT $dbVar FROM vtiger_orderstask WHERE orderstaskid = ?", array($targetTaskID));
        if ($db->num_rows($result) > 0) {
            $row = $db->fetch_row($result);
            $ids = explode(' |##| ', $row[0]);
            $ids[] = $resID;
            $resourcesIdsArray = implode(' |##| ', array_filter($ids));
            $orderTask = [
                'id'                => vtws_getWebserviceEntityId('OrdersTask', $targetTaskID),
                $dbVar => $resourcesIdsArray                
            ];
            try {
                vtws_revise($orderTask, $currentUser);
                $orderTasksRecordModel = Vtiger_Record_Model::getInstanceById($targetTaskID, 'OrdersTask');
                
                switch ($resourceType) {
                    case Vehicle:
                        $resources = $orderTasksRecordModel->getVehicles($resourcesIdsArray);

                        break;

                    default:
                        $resources = $orderTasksRecordModel->getEmployees($resourcesIdsArray);
                        break;
                }
                
                $result = ['result' => 'OK', 'resources2' => $resources,];
             } catch (Exception $exc) {
                $result = ['result' => 'fail', 'msg' => $exc->message];
             }
        }
 
        $msg = new Vtiger_Response();
        $msg->setResult($result);
        $msg->emit();
    }
    
    public function massTaskResourceHandler(Vtiger_Request $request){
        $tasksID = explode(",", $request->get('task_id'));
        $resourceType = $request->get('resource_type');

        $dbVar = ($resourceType == "Vehicle") ? "assigned_vehicles" : (($resourceType == "Vendor") ? "assigned_vendor" : "assigned_employee");
        $currentUser = Users_Record_Model::getCurrentUserModel();
        
        foreach ($tasksID as $taskID) {
            $orderTask = [
                'id'                => vtws_getWebserviceEntityId('OrdersTask', $taskID),
                $dbVar              => ''
		    ];
            try {
                vtws_revise($orderTask, $currentUser);

                $result = ['result' => 'OK'];
            } catch (Exception $exc) {
                $result = ['result' => 'fail', 'msg' => $exc->message];
            }
        }

        $msg = new Vtiger_Response();
        $msg->setResult($result);
        $msg->emit();  
    }
    
    public function getParticipantAgentsData(Vtiger_Request $request){
        $orderID    = $request->get('orderID');
        
        $participantAgents = ParticipatingAgents_Module_Model::getParticipantAgentsPicklistValues($orderID);
        
        $msg = new Vtiger_Response();
        $msg->setResult($participantAgents);
        $msg->emit();
    }
    
    public function getPrimaryRoleDropdown($employeeId,$taskId, $assignedToTask = false){
	
        $db = PearDatabase::getInstance();
        
        if(is_array($taskId)){
            $taskId = $taskId[0];
        }
        
        $arrayRoles = Vtiger_Cache::get('_localdispatch','user_emproles_' . $taskId);

        if(!is_array($arrayRoles)){
            $currentUser = Users_Record_Model::getCurrentUserModel();
            $accesibleAgents = $currentUser->getBothAccessibleOwnersIdsForUser();
        
        //get the employee roles for dropdown
        $arrayRoles = array();
        $sql = "SELECT * FROM vtiger_employeeroles INNER JOIN vtiger_crmentity ON vtiger_employeeroles.employeerolesid = vtiger_crmentity.crmid
		WHERE deleted=0 AND emprole_class_type = 'Operations' AND vtiger_crmentity.agentid IN (" . generateQuestionMarks($accesibleAgents) . ")";

		$params=  array($accesibleAgents);


	
     if (strtolower(getenv('INSTANCE_NAME')) != "graebel") {
	 
	$orderTaskRecord = Vtiger_Record_Model::getInstanceById($taskId, 'OrdersTask');
        if($orderTaskRecord->get('participating_agent')){
                    $sql .= " AND (vtiger_crmentity.agentid = ? OR vtiger_crmentity.agentid = ?)";
            $agentRecodModel = Vtiger_Record_Model::getInstanceById($orderTaskRecord->get('participating_agent'), 'Agents');
                    $agentManagerRecordModel =  Vtiger_Record_Model::getInstanceById($agentRecodModel->get("agentmanager_id"), 'AgentManager');
                    array_push($params, $agentRecodModel->get("agentmanager_id"), $agentManagerRecordModel->get('vanline_id'));
        }  
    }

    $result = $db->pquery($sql, $params);
        
            if($result && $db->num_rows($result)){
	while ($row = $db->fetch_row($result)) {
            $arrayRoles[$row['employeerolesid']] = $row['emprole_desc'];
        }
        
                Vtiger_Cache::set('_localdispatch','user_emproles_' . $taskId , $arrayRoles);
            }
        }
        $roleSaved = '';
        if ( $assignedToTask ) {
        //check if there is a role alredy assigned for the employee for this task
        $auxResult = $db->pquery("SELECT role FROM vtiger_orderstasksemprel WHERE employeeid = ? AND taskid = ? AND role is not null AND role <> '' AND role <> '--'", array($employeeId,$taskId));
        $roleSaved = ($db->num_rows($auxResult) > 0) ? $db->query_result($auxResult, 0, "role") : "";
        }
                
        $empRoleId = '';
        if ($roleSaved != '') {
            if ( is_numeric($roleSaved) ) {
            $empRoleId = $roleSaved;
        } else {
                if(in_array($roleSaved, $arrayRoles)){
                    $empRoleId = array_search($roleSaved, $arrayRoles);
                }
            }
        } else {
            //if there is no role assigned, get the primary or secondary role of the employee (if it is an Operations Class type role)
            $proleSaved = '';
            $sroleSaved = '';
            $auxResult = $db->pquery("SELECT employee_primaryrole, employee_secondaryrole FROM vtiger_employeescf ecf INNER JOIN vtiger_crmentity cr ON ecf.employeesid=cr.crmid WHERE employeesid = ? AND cr.deleted = 0", array($employeeId));
            if($db->num_rows($auxResult) > 0){
                $proleSaved = $db->query_result($auxResult, 0, "employee_primaryrole");
                $sroleSaved = $db->query_result($auxResult, 0, "employee_secondaryrole");
            }
            if($proleSaved != '' && key_exists($proleSaved, $arrayRoles)){
                $empRoleId = $proleSaved;
            }else if($sroleSaved != ''){
                $sroleSaved = explode(',', $sroleSaved);
                foreach ($sroleSaved as $role) {
                    if(key_exists($role, $arrayRoles)){
                        $empRoleId = $role;
                        break;
                    }
                }
            }
        }
        
        $html = '<select class="chzn-select chznprole" style="height: 21px;padding: 1px;max-width:100px;margin-bottom:0px;"><option value="none">--</option>';

        foreach ($arrayRoles as $id => $description) {
            $selected = ($empRoleId == $id) ? "selected" : "";
            $html .= '<option value="' . $id . '" ' . $selected . '>' . $description . '</option>';
        }
        
        $html .= '</select>';
        
        return $html;
    }
    
    public function updateDispatchStatus(Vtiger_Request $request){
        $taskID = $request->get('task_id');
        $dispatchStatus = $request->get('dispatch_status');
        $dispatchAssignedDatePresent = $request->get('dispatchAssignedDatePresent');

        $currentUser = Users_Record_Model::getCurrentUserModel();
	
        $orderTask = [
            'id'		    => vtws_getWebserviceEntityId('OrdersTask', $taskID),
            'dispatch_status'   => $dispatchStatus
           
        ];
        $date = '';
        if($dispatchAssignedDatePresent && $dispatchStatus == 'Accepted'){
            $ordersTaskRecord = Vtiger_Record_Model::getInstanceById($taskID, 'OrdersTask');
            if(!$ordersTaskRecord->get('date_spread') && $ordersTaskRecord->get('disp_assigneddate') == '' && $ordersTaskRecord->get('service_date_from') != ''){
                $orderTask['disp_assigneddate'] = $ordersTaskRecord->get('service_date_from');
                $date = DateTimeField::convertToUserFormat($orderTask['disp_assigneddate']);
            }
        }
        try {
            vtws_revise($orderTask, $currentUser);

            $result = ['result' => 'OK','assignedDate' => $date];
        } catch (Exception $exc) {
            $result = ['result' => 'fail', 'msg' => $exc->message];
        }
        
        $msg = new Vtiger_Response();
        $msg->setResult($result);
        $msg->emit();  
    }
    
    public function updateTimes(Vtiger_Request $request){
        $taskID = $request->get('task_id');
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $actualStart = $request->get('disp_actualstart');
        $actualEnd = $request->get('disp_actualend');
        if($actualEnd == 'NaN'){
            $record = Vtiger_Record_Model::getInstanceById($taskID, "OrdersTask");
            $estimatedHours = (int)$record->get('estimated_hours');
            $actualEnd = date('h.i A',strtotime(implode(':', explode('.', $actualStart)).' +'.$estimatedHours.' hours'));
        }
        
        $startHour = DateTimeField::convertToDBTimeZone(Vtiger_Time_UIType::getTimeValueWithSeconds(implode(':', explode('.', $actualStart))));
        $endHour = DateTimeField::convertToDBTimeZone(Vtiger_Time_UIType::getTimeValueWithSeconds(implode(':', explode('.', $actualEnd))));
        $diff = $startHour->diff($endHour);
         
        $orderTask = [
            'id'                => vtws_getWebserviceEntityId('OrdersTask', $taskID),
            'disp_assignedstart'   => $startHour->format('H:i:s'),
            'disp_actualend' => $endHour->format('H:i:s'),
            'disp_actualhours' => ($diff->format('%h') + $diff->format('%i')/60)
        ];
        try {
            vtws_revise($orderTask, $currentUser);

            $result = ['result' => 'OK', 'end_date' => $actualEnd];
        } catch (Exception $exc) {
            $result = ['result' => 'fail', 'msg' => $exc->message];
        }
        
        $msg = new Vtiger_Response();
        $msg->setResult($result);
        $msg->emit();  
    }
    
    public function primaryRoleNLeadUpdate(Vtiger_Request $request){
        $db = PearDatabase::getInstance();
        
        $taskID = $request->get('task_id');
        $primaryRole = $request->get('primaryRole');
        $selectedLead = $request->get('selectedLead');
        $employeeId = $request->get('employeeId');
           
        try {
            if ($selectedLead != '0') {
                $db->pquery("UPDATE vtiger_orderstasksemprel SET lead = 0 WHERE taskid = ?", array($taskID)); //clean other lead flags
	    }
            $db->pquery("UPDATE vtiger_orderstasksemprel SET role = ?, lead = ? WHERE taskid = ? AND employeeid = ?", array($primaryRole, $selectedLead, $taskID, $employeeId));
         
            $result = ['result' => 'OK'];
        } catch (Exception $exc) {
            $result = ['result' => 'fail', 'msg' => $exc->message];
        }
 
        $msg = new Vtiger_Response();
        $msg->setResult($result);
        $msg->emit();               
    }
    
    public function checkForShowCallModal(Vtiger_Request $request){
        $taskId = $request->get('task_id');

        $ordersTaskEntity = Vtiger_Record_Model::getInstanceById($taskId, "OrdersTask");
        $data = $ordersTaskEntity->getData();

        if ($data[ordersid]) {
            $orderEntity = Vtiger_Record_Model::getInstanceById($data[ordersid], "Orders");
            $orderData = $orderEntity->getData();
            
            if ($orderData[orders_contacts]) {
                $message = "OK";
            } else {
                $message = "No Contact related.";
            }
        } else {
            $message = "No Order related";
        }
 
        $msg = new Vtiger_Response();
        $msg->setResult($message);
        $msg->emit();    
    }
    
    public function showCallModal(Vtiger_Request $request){
        $db = PearDatabase::getInstance();
        $taskId = $request->get('task_id');

        $ordersTaskEntity = Vtiger_Record_Model::getInstanceById($taskId, "OrdersTask");
        $data = $ordersTaskEntity->getData();

        $orderEntity = Vtiger_Record_Model::getInstanceById($data[ordersid], "Orders");
        $orderData = $orderEntity->getData();
        
        $businessLine = $orderData[business_line]; //HHG - Interstate --> Interstate Move
        $dispatchStatus = ($businessLine == "HHG - Interstate" || $businessLine == "Interstate Move") ? $orderData[orders_otherstatus] : "none";
        
        $clientEntity = Vtiger_Record_Model::getInstanceById($orderData[orders_contacts], "Contacts");
        $clientData = $clientEntity->getData();
        
        $result = $db->pquery("SELECT * FROM vtiger_check_call WHERE presence = 1", array());
        while ($row = $db->fetch_row($result)) {
            $checkCall[$row[check_callid]] = $row[check_call];
        }
        
        $viewer = $this->getViewer($request);
        $viewer->assign('MODULENAME', "OrdersTask");
        $viewer->assign('RECORD_ID', $taskId);
        $viewer->assign('BUSINESS_LINE', $businessLine); 
        $viewer->assign('DISPATCH_STATUS', $dispatchStatus);
        $viewer->assign('CLIENT_FIRST_NAME', $clientData[firstname]);
        $viewer->assign('CLIENT_LAST_NAME', $clientData[lastname]);
        $viewer->assign('CLIENT_OFFICE_PHONE', $clientData[phone]);
        $viewer->assign('CLIENT_HOME_PHONE', $clientData[homephone]);
        $viewer->assign('CLIENT_MOBILE_PHONE', $clientData[mobile]);
        $viewer->assign('CLIENT_OTHER_PHONE', $clientData[otherphone]);
        $viewer->assign('CHECK_CALL_PICKLIST', $checkCall);
        $viewer->assign('CHECK_CALL_SELECTED', $data[check_call]);

        echo $viewer->view('CallModal.tpl', "OrdersTask", true);
    }    
    
    public function updateCheckCallField(Vtiger_Request $request){
        $taskID = $request->get('task_id');
        $checkCall = $request->get('check_call');
        $currentUser = Users_Record_Model::getCurrentUserModel();
        
        $orderTask = [
            'id'                => vtws_getWebserviceEntityId('OrdersTask', $taskID),
            'check_call'        => $checkCall,
            
        ];
        try {
            vtws_revise($orderTask, $currentUser);

            $result = ['result' => 'OK'];
        } catch (Exception $exc) {
            $result = ['result' => 'fail', 'msg' => $exc->message];
        }
        
        $msg = new Vtiger_Response();
        $msg->setResult($result);
        $msg->emit();  
    }
    
    public function updateAssignedDate(Vtiger_Request $request){
        $taskID = $request->get('task_id');
        $assignedDate = $request->get('assigned_date');
        $currentUser = Users_Record_Model::getCurrentUserModel();
        
        $orderTask = [
            'id'                => vtws_getWebserviceEntityId('OrdersTask', $taskID),
            'disp_assigneddate'        => $assignedDate,
        
        ];
        try {
            vtws_revise($orderTask, $currentUser);

            $result = ['result' => 'OK'];
        } catch (Exception $exc) {
            $result = ['result' => 'fail', 'msg' => $exc->message];
        }
        
        $msg = new Vtiger_Response();
        $msg->setResult($result);
        $msg->emit();   
    }
    
    
    public function getVendorsTable(Vtiger_Request $request){
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
        if(!is_array($request->get('task_id'))){
            $orderTasksArray = array_filter(explode(',',$request->get('task_id')));
        }else{
            $orderTasksArray = $request->get('task_id');
        }
        $orderTaskRecord = Vtiger_Record_Model::getInstanceById($orderTasksArray, "OrdersTask");
        $assignedResources = $this->getAssignedResourcesIds($orderTasksArray, "Vendors");
        
		$customView = (isset($_SESSION['lvs']['OrdersTask'][$currentUserModel->getId()]['vendorView']) && $_SESSION['lvs']['OrdersTask'][$currentUserModel->getId()]['vendorView'] != "") ? $_SESSION['lvs']['OrdersTask'][$currentUserModel->getId()]['vendorView'] : $request->get('customview');
        $selectedColumns = $this->getViewColumns("V", $customView);

        $orderTaskModel = Vtiger_Module_Model::getInstance('OrdersTask');
        $dateArray = $orderTaskModel->getFromDateAndStartEndTime($orderTasksArray);
        $vendors = $orderTaskModel->getAvailableVendors($assignedResources,$selectedColumns, $dateArray, $orderTasksArray);

 	
        $html     = '';
        
        if (count($vendors) > 0) {
			//$html .= '<span class="filterActionsDivVendors hide"><hr><ul class="filterActions"><li onclick="OrdersTask_LocalDispatch_Js.ldFilterCreate(this);" data-rigthtable="Vendors" data-createurl="index.php?module=CustomView&view=EditAjax&source_module=Vendors&customView=NewLocalDispatchVendors"><i class="icon-plus-sign"></i> '.vtranslate('LBL_CREATE_NEW_FILTER').'</li></ul></span>';
            $html .= '<div class="vendors-tables">';
			$headers = $this->getTableHeaders($selectedColumns,"V");
			$request->set('customview',$_REQUEST['customview']); //update $request object with the customview (cvid) get on the other function (getViewColumns)
			if($request->get('isChange') == "true"){
				$_SESSION['lvs']['OrdersTask'][$currentUserModel->getId()]['vendorView'] = $request->get('customview');
			}
            $html .= '<table id="vendorsDataTable" class="table listViewEntriesTable">
                <thead><tr class="searchViewHeaders">
                    <th><button class="btn btn-success" id="assign_vendor">Update</button></th>
                    <th><button class="btn" id="SearchVendor">Search</button></th>';
					foreach($selectedColumns as $fieldName){
						$html .= '<th><input class="listSearchContributor" data-fieldname="'.$fieldName.'" type="text" value="" placeholder="Search.." style="max-width: 100px;"></th>';
					}
					
            $html .= '</tr>
                <tr class="listViewHeaders">
					<th>'.vtranslate('LBL_ASSIGNED', 'OrdersTask').'</th><th></th>';
				foreach($headers as $fieldLabel){
					$html .= '<th>' . vtranslate($fieldLabel, 'Vendors') . '</th>';
				}
			$html .= '<th> </th></tr>
                </thead>
                <tbody>';
            foreach ($vendors as $vendor) {
                $makeItRed = '';
                if ($vendor['on_notice']) {
                    $makeItRed = 'redColor" title="This Vendor is On Notice';
                }
                $checked = (in_array($vendor['id'], $assignedResources)) ? "checked" : "";
                $html .= '<tr class="vendor draggable-resource ' . $makeItRed . '" id="'.$vendor['id'].'">';
                $html .= '<td><input class="resource_vendor" '.$checked.' type="checkbox" id="assigned_' . $vendor['id'] . '" data-id="'.$vendor['id'].'"></td><td></td>';
		    
				foreach($vendor as $key => $fieldValue){
					if($key != "id"){
						if($key == "hiddensmownerid"){
							$html .= '<input type="hidden" value="' . $fieldValue . '" id="vhiddensmownerid">';
						}elseif ( $key == 'vendorname'){
							$html .= '<td>
                                                                    <span class="value" data-field-type="reference">
                                                                        <a href="index.php?module=Vendors&amp;view=Detail&amp;record='.$vendor['id'].'" data-original-title="Vendor">'.$fieldValue.'</a>
                                                                    </span>
                                                                </td>';
                        }else{
							$html .= '<td><div class="tdwrapp">' . $fieldValue . '</div></td>';
						}
					}
				}
				
                $html .= '</tr>';

            }
			
            $html .= '</tbody>'.$footer.'</table>';
            $html .= '</div>';
        }

        $msg    = new Vtiger_Response();
        $msg->setResult($html);
        $msg->emit();
    }

    

    public function updateVendor(Vtiger_Request $request){
        $taskIdArray = $request->get('task_id');
        $allOK = true;
        $error ='';
        foreach ($taskIdArray as $taskID) {
            $vendorID = (isset($_REQUEST[vendor_id])) ? $request->get('vendor_id') : "";
            $currentUser = Users_Record_Model::getCurrentUserModel();

            $orderTask = [
        'id'                => vtws_getWebserviceEntityId('OrdersTask', $taskID),
        'assigned_vendor'   => vtws_getWebserviceEntityId('Vendors', $vendorID)
        
        ];
            try {
                vtws_revise($orderTask, $currentUser);
            } catch (Exception $exc) {
                $allOK = false;
                $error .= $exc->message;
            }
        }
        if ($allOK) {
            $result = ['result' => 'OK'];
        } else {
            $result = ['result' => 'fail', 'msg' => $error];
        }
        $msg = new Vtiger_Response();
        $msg->setResult($result);
        $msg->emit();
    }    

    public function copyResources(Vtiger_Request $request){
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $taskId = $request->get('task_id');
        $orderTaskRecord = Vtiger_Record_Model::getInstanceById($taskId, "OrdersTask");
        $taskData = $orderTaskRecord->getData();

        $assignedCrew = $taskData[assigned_employee];
        $assignedEquipment = $taskData[assigned_vehicles];
        $assignedVendor = $taskData[assigned_vendor];

        $tasksIds = $request->get('task_ids_to');

        $orderTask = [
            'id' => vtws_getWebserviceEntityId('OrdersTask', $tasksIds),
            'assigned_employee' => $assignedCrew,
            'assigned_vehicles' => $assignedEquipment
           
        ];

        if ($assignedVendor) {
            $orderTask['assigned_vendor'] = vtws_getWebserviceEntityId('Vendors', $assignedVendor);
        }

        try {
            vtws_revise($orderTask, $currentUser);

            $result = ['result' => 'OK'];
        } catch (Exception $exc) {
            $result = ['result' => 'fail', 'msg' => $exc->message];
        }

        $msg = new Vtiger_Response();
        $msg->setResult($result);
        $msg->emit();
    }
    
public function getConflictingResources($taskIdsArray,$resource){
        if(!is_array($taskIdsArray)){
            $taskIdsArray = [$taskIdsArray];
        }
        $moduleModel = Vtiger_Module_Model::getInstance('OrdersTask');
        $datesAndTimes = $moduleModel::getFromDateAndStartEndTime($taskIdsArray);
        $fromDate = $datesAndTimes['fromDate'];
        $startTime = $datesAndTimes['startTime'];
        $endTime = $datesAndTimes['endTime'];
        $conflictingResources = array();
        $db = PearDatabase::getInstance();
        $sql = "SELECT orderstaskid, service_date_from, disp_assignedstart, disp_actualend, assigned_employee, assigned_vehicles
                FROM vtiger_orderstask  
                INNER JOIN vtiger_crmentity ON vtiger_orderstask.orderstaskid = vtiger_crmentity.crmid 
                LEFT JOIN vtiger_orders ON vtiger_orderstask.ordersid = vtiger_orders.ordersid 
                WHERE vtiger_crmentity.deleted=0 AND vtiger_orderstask.orderstaskid > 0 
                    AND (service_date_from = '$fromDate' OR disp_assigneddate='$fromDate')
                    AND orderstaskid NOT IN (" . generateQuestionMarks($taskIdsArray) . ") GROUP BY `vtiger_crmentity`.crmid ";
        $result = $db->pquery($sql,array($taskIdsArray));
        $orders = [];
        if ( $db->num_rows($result) > 0 ) {
            while ( $row = $db->fetchByAssoc($result) ) {
                $orders[$row['orderstaskid']] = $row;
            }
        }
        $userFormatStartTime = new DateTime(DateTimeField::convertToUserTimeZone($startTime)->format('H:i:s'));
        $userFormatEndTime = new DateTime(DateTimeField::convertToUserTimeZone($endTime)->format('H:i:s'));
        foreach ($orders as $taskId => $data) {
            $dispAssignedStart = new DateTime(DateTimeField::convertToUserTimeZone($data['disp_assignedstart'])->format('H:i:s'));
            $dispActualEnd = new DateTime(DateTimeField::convertToUserTimeZone($data['disp_actualend'])->format('H:i:s'));
            if( ($dispAssignedStart >= $userFormatStartTime && $dispAssignedStart <= $userFormatEndTime) ||                
            ($dispActualEnd >= $userFormatStartTime && $dispActualEnd <= $userFormatEndTime) ||
            ($userFormatStartTime >= $dispAssignedStart && $userFormatStartTime <= $dispActualEnd) ||
            ($userFormatEndTime >= $dispAssignedStart && $userFormatEndTime <= $dispActualEnd)  ){
                //is conflicting, leve it in the array
            }else{
                //is not conflicting, take it out
                unset($orders[$taskId]);
            }
        }
         //original string condition from query:
                //"AND 
//            ( ( disp_assignedstart >= '$startTime' AND disp_assignedstart <= '$endTime' ) 
//                OR ( disp_actualend >= '$startTime' AND disp_actualend <= '$endTime' )
//                OR ( '$startTime' >= disp_assignedstart AND '$startTime' <= disp_actualend )
//                OR ( '$endTime' >= disp_assignedstart AND '$endTime' <= disp_actualend )
//            )";
        
        if ( $resource == 'employees' ) {
            foreach ( $orders as $orderid => $orderData ) {
                $ids = explode(' |##| ', $orderData['assigned_employee']);
                $conflictingResources = array_merge($conflictingResources, $ids);
            }
        } else {
            foreach ( $orders as $orderid => $orderData ) {
                $ids = explode(' |##| ', $orderData['assigned_vehicles']);
                $conflictingResources = array_merge($conflictingResources, $ids);
            }
        }
        return array_unique($conflictingResources);
    }
    
    public function getOnNoticeEmployees($startDate, $empList){
        $db = PearDatabase::getInstance();

	$params = [$startDate, $startDate, $empList];

	$result = $db->pquery("SELECT vtiger_outofservice.outofservice_employeesid FROM vtiger_outofservice 
			INNER JOIN vtiger_crmentity ON vtiger_outofservice.outofserviceid = vtiger_crmentity.crmid 
			WHERE deleted=0 AND outofservice_status = 'On Notice' 
			AND outofservice_effectivedate <= ?  
			AND (outofservice_satisfieddate IS NULL OR outofservice_satisfieddate > ?) 
			AND outofservice_employeesid IN (" . generateQuestionMarks($empList) . ")", $params);
        
        $onNotice = array();
	if ($db->num_rows($result) > 0) {
	    while ($row = $db->fetchByAssoc($result)) {
		$onNotice[] = $row['outofservice_employeesid'];
	    }
	}
        
        return $onNotice;
    }
}
