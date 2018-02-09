<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class OrdersTask_Module_Model extends Vtiger_Module_Model {

    public function getSideBarLinks($linkParams) {
	$linkTypes = array('SIDEBARLINK', 'SIDEBARWIDGET');
	$links = parent::getSideBarLinks($linkParams);
	unset($links['SIDEBARLINK']);

	$quickLinks = array(
	    array(
		'linktype' => 'SIDEBARLINK',
		'linklabel' => 'LBL_ORDERS_LIST',
		'linkurl' => $this->getOrdersListUrl(),
		'linkicon' => '',
	    ),
	    array(
		'linktype' => 'SIDEBARLINK',
		'linklabel' => 'LBL_CAPACITY_CALENDAR',
		'linkurl' => 'index.php?module=OrdersTask&view=LocalDispatchCapacityCalendar',
		'linkicon' => '',
	    ),
	    array(
		'linktype' => 'SIDEBARLINK',
		'linklabel' => 'LBL_LOCAL_DISPATCH', //'LBL_DAY_BOOK',
		'linkurl' => 'index.php?module=OrdersTask&view=NewLocalDispatch', //'index.php?module=OrdersTask&view=LocalDispatchDayBook',
		'linkicon' => '',
	    ),
	    array(
		'linktype' => 'SIDEBARLINK',
		'linklabel' => 'LBL_ACTUALS',
		'linkurl' => 'index.php?module=OrdersTask&view=NewLocalDispatchActuals',
		'linkicon' => '',
	    ),
	    array(
		'linktype' => 'SIDEBARLINK',
		'linklabel' => 'LBL_TASKS_LIST',
		'linkurl' => $this->getListViewUrl(),
		'linkicon' => '',
	    )
	);
	foreach ($quickLinks as $quickLink) {
	    $links['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);
	}

	return $links;
    }

    public function getOrdersListUrl() {
	$taskModel = Vtiger_Module_Model::getInstance('Orders');
	return $taskModel->getListViewUrl();
    }

    public function getMilestonesListUrl() {
	$milestoneModel = Vtiger_Module_Model::getInstance('OrdersMilestone');
	return $milestoneModel->getListViewUrl();
    }

    /**
     * Function to check whether the module is summary view supported
     * @return <Boolean> - true/false
     */
    public function isSummaryViewSupported() {
	return false;
    }

    /**
     * Function to get the Default View Component Name
     * @return string
     */
    public function getDefaultViewName() {
	return 'LocalDispatchCapacityCalendar';
    }

    public function getListViewUrl() {
	if (vtlib_purify($_REQUEST['source_module_view']) == 'NewLocalDispatch') {
	    return 'index.php?module=' . $this->get('name') . '&view=NewLocalDispatch';
	} else {
	    return 'index.php?module=' . $this->get('name') . '&view=' . $this->getListViewName();
	}
    }

    /**
     * Function to get the module field mapping
     * @return <array>
     */
    public function getColumnFieldMapping() {
	$moduleMeta = $this->getModuleMeta();
	$meta = $moduleMeta->getMeta();
	$fieldColumnMapping = $meta->getFieldColumnMapping();

	$fieldData = array('OrdersTask', 'Orders', 'Trips', 'Estimates');

	$fieldsModules = array_unique($fieldData);

	foreach ($fieldsModules as $moduleInFilter) {
	    if ($moduleInFilter == 'OrdersTask') {
		continue;
	    }
	    $relatedModuleModel = Vtiger_Module_Model::getInstance($moduleInFilter);

	    $relatedModuleMeta = $relatedModuleModel->getModuleMeta();
	    $meta = $relatedModuleMeta->getMeta();
	    $fieldColumnMappingRelated = $meta->getFieldColumnMapping();
	    $fieldColumnMapping = array_merge($fieldColumnMapping, $fieldColumnMappingRelated);
	}



	return array_flip($fieldColumnMapping);
    }

    /**
     * Returns and array with the available Vehicles for each
     * day in the calendar.
     *
     * @return array Days as keys
     */
    public function getAvailableVehiclesCapacity($startDate, $endDate, $vehicleType, $cvid, $getData = 'hours')
    {

        $availableVehicles = $this->getVehiclesByDateAndType($startDate, $endDate, $cvid, $vehicleType);

	$availableVehiclesByDate = [];
        if($getData == 'all'){
            $availableVehiclesByDate = $availableVehicles[$startDate];
        }else{
            foreach ($availableVehicles as $date => $vhInfo) {
                if ($getData == 'count') {
                    $availableVehiclesByDate[$date] = count($vhInfo);
                } elseif($getData == 'hours') {
                    foreach ($vhInfo as $vhInf) {
                        $availableVehiclesByDate[$date] = $availableVehiclesByDate[$date] + $vhInf['available_hours'];
                    }
                }
            }
        }


        return $availableVehiclesByDate;
    }

    function getVehiclesByDateAndType($startDate, $endDate, $cvid, $vehicleType = '', $taskIdsArray = '', $assignedResources = array(), $selectedColumns = array(), $getData = '', $carbCompliant = false) {
	$db = PearDatabase::getInstance();
	$user = Users_Record_Model::getCurrentUserModel();
	$accesibleAgents = array_keys($user->getAccessibleAgentsForUser());

	$orderTaskModel = Vtiger_Module_Model::getInstance('OrdersTask');
	if(is_array($taskIdsArray) && count($taskIdsArray) > 0){
		$dateArray = $orderTaskModel->getFromDateAndStartEndTime($taskIdsArray);
	}

        $agentBlineCondition = '';
        if($cvid != ''){
            $agentBlineCondition = $orderTaskModel->getBLineAndAgentCond($cvid, false);
        }
	$sql = "SELECT agentid, vtiger_vehicles.*, vtiger_vehiclescf.*, vtiger_agentmanager.order_task_end_time, vtiger_agentmanager.order_task_start_time, vtiger_agentmanager.agentmanagerid as agentmanid,
                    agency_name, agency_code
                FROM vtiger_vehicles
                INNER JOIN vtiger_crmentity ON vtiger_vehicles.vehiclesid = vtiger_crmentity.crmid 
                INNER JOIN vtiger_agentmanager ON vtiger_crmentity.agentid = vtiger_agentmanager.agentmanagerid 
                INNER JOIN vtiger_vehiclescf ON vtiger_vehicles.vehiclesid = vtiger_vehiclescf.vehiclesid
                WHERE deleted=0 AND vehicle_status='Active' 
                    AND vtiger_crmentity.agentid IN (" . generateQuestionMarks($accesibleAgents) . ") " . $agentBlineCondition;

	$params = [$accesibleAgents];

	if ($vehicleType != '') {
	    if($vehicleType == 'Truck') {
	        $types = [
	            "Truck",
                 "Cube Van",
                 "Straight Truck",
                 "Auto Transport",
                 "O/O Tractor",
                 "O/O Van",
                 "Pack Van",
                 "Local Vans",
                 "Local Tractors",
                 "Tractor",
                 "Yard Tractor",
                 "Van",
                 "B Truck",
                 "Straight Truck Liftgate",
                 "Donkey",
                 "Tractor/Trailer",
                 "Car",
                 "Passenger Van",
                 "Bobtail Van 22'",
                 "Bobtail 24' AR LG",
                 "Bobtail Van 24'",
                 "Tractor-Day Cab",
                 "Bobtail 20' AR LG",
                 "Tractor-Sleeper",
                 "Passenger/Cargo Van",
                 "Cargo Van",
                 "Sprinter Van",
                 "14' Pack Truck",
                 "Straight Truck - Non CDL",
                 "NFA (Climate / NCDL) Truck",
                 "Non- CDL Straight Trucks",
                 "Transit",
                 "CDL- Straight Trucks",
                 "Rental- 16ft",
                 "Forklift 8K BG",
                 "Bobtail Van 15'",
                 "Straight Truck - CDL"
            ];
	        $sql .= " AND vehicle_type IN (".generateQuestionMarks($types).") ";
	        array_push($params, $types);
        } elseif($vehicleType == 'Trailer') {
	        $types = [
	            "Flatbed Trailer",
                 "Double Trailer",
                 "Drop Trailer",
                 "Freight Trailer",
                 "Flat Trailer",
                 "Donkey",
                 "Pallet Trailer",
                 "AR OTR Flatbed Trailer 40'",
                 "Trailer",
                 "O/O Trailer",
                 "Utility Trailer",
                 "Semi Trailer",
                 "Vault Trailer",
                 "NFA (Climate / CDL )",
                 "Trailer 28'",
                 "AR Trailer 48'",
                 "Storage Trailer 45'",
                 "Flatbed Trailer 16'",
                 "Flatbed Trailer 12'",
                 "Storage Trailer 27'",
                 "Trailer 45'",
                 "Forklift Trailer 12'",
                 "Trailer 16'"
            ];
            $sql .= " AND vehicle_type IN (".generateQuestionMarks($types).") ";
            array_push($params, $types);
        } else {
            $sql .= " AND vehicle_type = ? ";
            array_push($params, $vehicleType);
        }
	}

	if ($carbCompliant) {
	    $sql .= " AND vehicle_carb = 'Yes' ";
	}

	if (strtolower(getenv('INSTANCE_NAME')) != "graebel") {
	    $sql .= " AND vehicles_availlocal = 'Yes' ";
	}

        if (strtolower(getenv('INSTANCE_NAME')) != "graebel" && $taskIdsArray != '' && is_array($taskIdsArray)) {
            $agents = [];
            foreach ($taskIdsArray as $id) {
                $orderTaskRecord = Vtiger_Record_Model::getInstanceById($id, 'OrdersTask');
                if($orderTaskRecord->get('participating_agent')){
                    $agentRecodModel = Vtiger_Record_Model::getInstanceById($orderTaskRecord->get('participating_agent'), 'Agents');
                    array_push($agents, $agentRecodModel->get("agentmanager_id"));
                }
            }
            array_unique($agents);
	    if(count($agents) > 0){
		 $sql .= " AND vtiger_crmentity.agentid IN (" . implode(",", $agents). ")";
	    }
	}
        if (count($assignedResources) > 0) {
            $sql .= " ORDER BY vtiger_vehicles.vehiclesid IN (" . implode(",", $assignedResources) . ") DESC";
        }
	$result = $db->pquery($sql, $params);

	$vehiclesArr = array();
	if ($db->num_rows($result) > 0) {
	    while ($row = $result->fetchRow()) {
                if($getData == 'vehiclePopup'){
                    $vehiclesArr[$row['vehiclesid']] = Vtiger_Record_Model::getInstanceById($row['vehiclesid'], 'Vehicles');
                    continue;
                }
                if(count($selectedColumns) > 0){//called from Local Dispatch
                    $vehiclesArr[$row['vehiclesid']]['id']             = $row['vehiclesid'];
                    $vehiclesArr[$row['vehiclesid']]['status']         = $row['vehicle_status'];
                    foreach($selectedColumns as $fieldName){
                            $fieldModel =  Vtiger_Field_Model::getInstance($fieldName,Vtiger_Module_Model::getInstance('Vehicles'));
                            if($fieldModel){
                                    $displayValue = $fieldModel->getDisplayValue($row[$fieldName]);
                            }else if($fieldName = "smownerid"){
                                    $vehiclesArr[$row['vehiclesid']]["hiddensmownerid"] = $row[$fieldName];
                                    $displayValue = Vtiger_Functions::getOwnerRecordLabel($row[$fieldName]);
                            }else{
                                    $displayValue = $row[$fieldName];
                            }
                            $vehiclesArr[$row['vehiclesid']][$fieldName] = $displayValue;
                    }
                }else{//called from Capacity Calendar
                    $vehiclesArr[$row['vehiclesid']]['agentid'] = $row['agentid'];
                    $vehiclesArr[$row['vehiclesid']]['unit'] = $row['vechiles_unit'];
                    $vehiclesArr[$row['vehiclesid']]['type'] = $row['vehicle_type'];
                    $vehiclesArr[$row['vehiclesid']]['availlocal'] = $row['vehicles_availlocal'];
                    $vehiclesArr[$row['vehiclesid']]['availinter'] = $row['vehicles_availinter'];
                    $vehiclesArr[$row['vehiclesid']]['available_hours'] = (float) ($this->convertToHours($row['agentmanid'], 'order_task_end_time' , $row['order_task_end_time']) - $this->convertToHours($row['agentmanid'], 'order_task_start_time', $row['order_task_start_time']));
                }
	    }
	}

	$outOfService = $this->getOutOfServiceVehiclesByDate($startDate, $endDate, array_keys($vehiclesArr));
	$vehiclesAvArr = [];
	$outOfServiceIds = [];

	$date1 = new DateTime($startDate);
	$date2 = new DateTime($endDate);
	$diff = $date2->diff($date1)->format("%a") + 1 ;

	for ($i = 0; $i < $diff; $i++) {
		$currentDate = date('Y-m-d', strtotime("+" . $i . " days", strtotime($startDate)));

		foreach ($vehiclesArr as $vhId => $vhInfo) {
			if (!is_array($outOfService[$currentDate]) || !in_array($vhId, $outOfService[$currentDate])) {
				$vehiclesAvArr[$currentDate][$vhId] = $vhInfo;
			}
		}
	}


	return $vehiclesAvArr;
    }

    function getOutOfServiceVehiclesByDate($startDate, $endDate, $vhList, $status = 'Out of Service') {
        //status can be 'Out of Service' or 'On Notice'
	$db = PearDatabase::getInstance();

        $sql = "SELECT vtiger_vehicleoutofservice.* FROM vtiger_vehicleoutofservice 
			INNER JOIN vtiger_crmentity ON vtiger_vehicleoutofservice.vehicleoutofserviceid = vtiger_crmentity.crmid 
			WHERE deleted=0 AND outofservice_status = '$status' 
                        AND outofservice_effective_date IS NOT NULL 
                        AND outofservice_vehicle IN (" . generateQuestionMarks($vhList) . ")";
        $params = [$vhList];
	$result = $db->pquery($sql, $params);

	$offVh = [];
	if ($result && $db->num_rows($result) > 0) {
	    while ($row = $db->fetchByAssoc($result)) {
		$offVh[] = $row;
	    }
	}

	$date1 = new DateTime($startDate);
	$date2 = new DateTime($endDate);
	$diff = $date2->diff($date1)->format("%a") + 1;


	$outOfServiceVh = [];
	for ($i = 0; $i < $diff; $i++) {
	    $currentDate = date('Y-m-d', strtotime("+" . $i . " days", strtotime($startDate)));

	    foreach ($offVh as $offV) {
			if ($currentDate >= $offV['outofservice_effective_date'] && ($currentDate <= $offV['outofservice_reinstated_date'] || $offV['outofservice_reinstated_date'] == '')) {
				$outOfServiceVh[$currentDate][] = $offV['outofservice_vehicle'];
			}
	    }
	}

	return $outOfServiceVh;
    }

    function getEmployeesByDateAndRole($startDate, $endDate, $employeeRole , $cvid, $getData, $fromLocal = false, $taskIdsArray = false, $arr) {
	$db = PearDatabase::getInstance();
	$user = Users_Record_Model::getCurrentUserModel();
	$accesibleAgents = array_keys($user->getAccessibleAgentsForUser());

	$orderTaskModel = Vtiger_Module_Model::getInstance('OrdersTask');
	if($cvid != ''){
	$agentBlineCondition = $orderTaskModel->getBLineAndAgentCond($cvid, false);
	}


	$date1 = new DateTime($startDate);
	$date2 = new DateTime($endDate);
	$diff = $date2->diff($date1)->format("%a") + 1;



	$sql = "SELECT vtiger_employees.* , vtiger_agentmanager.personnel_end_time, vtiger_agentmanager.personnel_start_time, vtiger_agentmanager.agentmanagerid as agentmanid ";
	if ($getData == 'all') {
            $sql .=", vtiger_employeescf.employee_primaryrole, vtiger_employeescf.employee_secondaryrole, employee_available_localdispatch, employee_available_longdispatch ";
        }
        $sql .= " FROM vtiger_employees 
                INNER JOIN vtiger_crmentity ON vtiger_employees.employeesid = vtiger_crmentity.crmid 
		INNER JOIN vtiger_agentmanager ON vtiger_crmentity.agentid = vtiger_agentmanager.agentmanagerid ";

	$sql .= " INNER JOIN vtiger_employeescf ON vtiger_employees.employeesid = vtiger_employeescf.employeesid ";

        $sql .= " LEFT JOIN vtiger_employeeroles r1 ON vtiger_employeescf.employee_primaryrole=r1.employeerolesid
                    LEFT JOIN vtiger_employeeroles r2 ON vtiger_employeescf.employee_secondaryrole LIKE CONCAT('%',r2.employeerolesid,'%') ";
	$sql .= " WHERE deleted=0 AND employee_status='Active' ";
	$sql .= " AND vtiger_crmentity.agentid IN (" . generateQuestionMarks($accesibleAgents) . ") ";

	$params = [$accesibleAgents];

	if( !$fromLocal && $cvid != '' ){
		$agentBlineCondition = $this->getBLineAndAgentCond($cvid, false);
		$sql .= " " . $agentBlineCondition;

	}elseif( $fromLocal && $taskIdsArray && is_array($taskIdsArray) ){
		$agentList = [];
		foreach ($taskIdsArray as $taskId) {
			$orderTaskRecord = Vtiger_Record_Model::getInstanceById($taskId, 'OrdersTask');
			if ($orderTaskRecord->get('participating_agent')) {
				$agentRecodModel = Vtiger_Record_Model::getInstanceById($orderTaskRecord->get('participating_agent'), 'Agents');
				array_push($agentList, $agentRecodModel->get("agentmanager_id"));
			}
		}

		if(count($agentList)){
			$sql .= " AND vtiger_crmentity.agentid IN (" . generateQuestionMarks($agentList) . ") ";
			array_push($params, $agentList);
		}
	}

	if ($employeeRole != '') {
	    $sql .= " AND (vtiger_employeescf.employee_primaryrole = ? OR vtiger_employeescf.employee_secondaryrole LIKE ? ) ";
	    array_push($params, $employeeRole);
	    array_push($params, '%' . $employeeRole . '%');
	}

        $sql .= " AND employee_type != 'Administrative' AND employee_available_localdispatch = 'Yes' ";
        $sql .= " AND (r1.emprole_class_type = 'Operations' OR r2.emprole_class_type = 'Operations') ";

        foreach ($arr as $key => $value) {
            if($value != ''){
                $auxSql = $auxSql . " AND $key LIKE '%$value%' ";
            }
        }
        if($auxSql){
            $sql .= $auxSql;
        }
	$avaibleEmployees = [];

	$result = $db->pquery($sql, $params);
	if ($db->num_rows($result) > 0) {

	    $offEmployees = $this->getOffEmployeesByDay($startDate, $endDate, $employeeRole);
            $outOfServiceEmployeesByDate = $this->getOutOfServiceEmployeesByDate($startDate, $endDate, $status = 'Out of Service');
	    $roles = $this->getRoles();


	    while ($row = $db->fetchByAssoc($result)) {
		for ($i = 0; $i < $diff; $i++) {
		    $currentDate = date('Y-m-d', strtotime("+" . $i . " days", strtotime($startDate)));
		    $currentWeekDay = date('w', strtotime("+" . $i . " days", strtotime($startDate)));

		    $weekDays = array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday');
		    $myKey = $weekDays[$currentWeekDay];

                    //if is out of service for the current date, continue
                    if(in_array($row['employeesid'], $outOfServiceEmployeesByDate[$currentDate])){
                        continue;
                    }
		    //If person is working today and is not away (time off)

		    if ($row['employees_' . $myKey]) {
                        if($getData == 'employeePopup'){
                            $avaibleEmployees[$currentDate][$row['employeesid']] = Vtiger_Record_Model::getInstanceById($row['employeesid'], 'Employees');
                            continue;
                        }
			if ($getData == 'all') {
                            $avaibleEmployees[$currentDate][$row['employeesid']]['fullname'] = $row['name'].' '.$row['employee_lastname'];
                            $avaibleEmployees[$currentDate][$row['employeesid']]['prole'] = $this->getPrimaryRoleDescription($row['employee_primaryrole'],$roles);
                            $avaibleEmployees[$currentDate][$row['employeesid']]['srole'] = $this->getSecondaryRoleDescription($row['employee_secondaryrole'],$roles);
                            $avaibleEmployees[$currentDate][$row['employeesid']]['avail_local'] = ($row['employee_available_localdispatch']?$row['employee_available_localdispatch']:"");
                            $avaibleEmployees[$currentDate][$row['employeesid']]['avail_long'] = ($row['employee_available_longdispatch']?$row['employee_available_longdispatch']:"");
                        }
                        if (($row['employees_' . $myKey . 'end'] == '' || $row['employees_' . $myKey . 'start'] == '') && $row['employees_' . $myKey . 'all']) {
			    $avaibleEmployees[$currentDate][$row['employeesid']]['available_hours'] = (float) ($this->convertToHours($row['agentmanid'], 'personnel_end_time' , $row['personnel_end_time']) - $this->convertToHours($row['agentmanid'], 'personnel_start_time', $row['personnel_start_time']));
			} else {
			    $avaibleEmployees[$currentDate][$row['employeesid']]['available_hours'] = (float) ($this->convertToHours($row['employeesid'], 'employees_' . $myKey . 'end', $row['employees_' . $myKey . 'end']) - $this->convertToHours($row['employeesid'], 'employees_' . $myKey . 'start',$row['employees_' . $myKey . 'start']));
			}


			if (isset($offEmployees[$currentDate][$row['employeesid']])) {
			    $avaibleEmployees[$currentDate][$row['employeesid']]['available_hours'] = $avaibleEmployees[$currentDate][$row['employeesid']]['available_hours'] - $offEmployees[$currentDate][$row['employeesid']]['off_hours'];

			    if ($avaibleEmployees[$currentDate][$row['employeesid']]['available_hours'] <= 0 || $offEmployees[$currentDate][$row['employeesid']]['off_all_day']) {
				unset($avaibleEmployees[$currentDate][$row['employeesid']]);
			    }
			}
			//Adding all the columns from employees so we can later use this  array in local disptach too

			if(isset($avaibleEmployees[$currentDate][$row['employeesid']])){
			$avaibleEmployees[$currentDate][$row['employeesid']]['employee_model'] = Vtiger_Record_Model::getInstanceById($row['employeesid'], 'Employees');

                        }
		    }
		}
	    }
	}

	return $avaibleEmployees;
    }

    function convertToHours($crmid, $fieldName,  $strTime) {

	if($strTime == ''){
	    return 0;
	}

	if(!Vtiger_Cache::get('local-dispatch-timezones', $crmid . $fieldName)){
	    $adb = PearDatabase::getInstance();
	    $rsCheck=$adb->pquery("SELECT * FROM vtiger_fieldtimezonerel WHERE crmid=? AND fieldid=?",array($crmid, $fieldName));
	    if($adb->num_rows($rsCheck)>0) {
		Vtiger_Cache::set('local-dispatch-timezones', $crmid . $fieldName, $adb->query_result($rsCheck,0,'timezone'));
		$fieldTimeZone = $adb->query_result($rsCheck,0,'timezone');
	    }
	} else {
	    $fieldTimeZone = Vtiger_Cache::get('local-dispatch-timezones', $crmid . $fieldName);
	}
	//Convert to timezone


	//Some crmid have the time zone emtpy. To avoid having error we take the user timezone or fallback to the default timezone.
	if($fieldTimeZone == ''){
	    global $current_user, $default_timezone;
	    $fieldTimeZone = $current_user->time_zone?$current_user->time_zone:$default_timezone;
	}

	$strTime = DateTimeField::convertTimeZone($strTime, DateTimeField::getDBTimeZone(), $fieldTimeZone);
	$strTime = $strTime->format('H:i:s');


	$strTime = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $strTime);
	sscanf($strTime, "%d:%d:%d", $hours, $minutes, $seconds);

	$timeHours = $hours + $minutes / 60 + $seconds / 3600;

	return $timeHours;
    }

    /**
     * Returns and array with the available employees for each
     * day in the calendar.
     *
     * @return array Days as keys
     */
    public function getAvailableEmployeeCapacity($startDate, $endDate, $employeeRole, $cvid, $getData = 'hours')
    {

	$availableEmployees = $this->getEmployeesByDateAndRole($startDate, $endDate, $employeeRole, $cvid, $getData, false);

	$availableEmployeesByDate = [];
        if ($getData == 'all') {
            $availableEmployeesByDate = $availableEmployees[$startDate];
        } else {
            foreach ($availableEmployees as $date => $empInfo) {
                if ($getData == 'count') {
                    $availableEmployeesByDate[$date] = count($empInfo);
                } elseif ($getData == 'hours') {
                    foreach ($empInfo as $empInf) {
                        $availableEmployeesByDate[$date] = $availableEmployeesByDate[$date] + $empInf['available_hours'];
                    }
                }
            }
        }

	return $availableEmployeesByDate;
    }

    public function getOffEmployeesByDay($startDate, $endDate, $employeeRole) {//in use
	$timeOffByDate = array();
	$db = PearDatabase::getInstance();
	$user = Users_Record_Model::getCurrentUserModel();
	$accesibleAgents = array_keys($user->getAccessibleAgentsForUser());


	$sql = "SELECT vtiger_timeoff.*,
                TIME_TO_SEC(TIMEDIFF( vtiger_timeoff.timeoff_hoursend, vtiger_timeoff.timeoff_hourstart ))/3600  AS timeoff_hours, 
		vtiger_agentmanager.personnel_end_time, vtiger_agentmanager.personnel_start_time, vtiger_agentmanager.agentmanagerid as agentmanid
		FROM vtiger_timeoff 
                INNER JOIN vtiger_crmentity as crm1 ON vtiger_timeoff.`timeoffid`=crm1.`crmid`
                INNER JOIN vtiger_employees ON vtiger_timeoff.timeoff_employees = vtiger_employees.employeesid
                INNER JOIN vtiger_crmentity as crm2 ON vtiger_employees.employeesid=crm2.`crmid` 
		INNER JOIN vtiger_agentmanager ON crm2.agentid = vtiger_agentmanager.agentmanagerid
		INNER JOIN vtiger_employeescf ON vtiger_employees.employeesid = vtiger_employeescf.employeesid 
		WHERE crm1.deleted = 0 AND crm2.deleted=0 ";

	if ($employeeRole != '') {
	    $sql .= " AND (vtiger_employeescf.employee_primaryrole = '$employeeRole' OR vtiger_employeescf.employee_secondaryrole LIKE '%$employeeRole%' )";
	}
	$sql .= " AND crm2.agentid IN (" . generateQuestionMarks($accesibleAgents) . ") ";
	$sql .= " AND timeoff_date >= ? AND timeoff_date <= ?";

	$result = $db->pquery($sql, array($accesibleAgents, $startDate, $endDate));

	if ($db->num_rows($result) > 0) {
	    while ($row = $db->fetch_row($result)) {
                if ( $row['timeoff_allday'] ) {
                    $timeOffByDate[$row['timeoff_date']][$row['timeoff_employees']]['off_all_day'] = 1;
                } else if ($row['timeoff_hours'] != '' ) {
		    $timeOffByDate[$row['timeoff_date']][$row['timeoff_employees']]['off_hours'] = $row['timeoff_hours'];
		} else {
		    $timeOffByDate[$row['timeoff_date']][$row['timeoff_employees']]['off_hours'] = (float) ($this->convertToHours($row['agentmanid'], 'personnel_end_time' , $row['personnel_end_time']) - $this->convertToHours($row['agentmanid'], 'personnel_start_time', $row['personnel_start_time']));
		}
	    }
	}

	return $timeOffByDate;
    }

    public function getTotalOrderTasksHoursByDay($startDate, $endDate, $resource, $resourceType, $cvid) {
	$ordersTasksList = $this->getOrderTasksByDay($startDate, $endDate, $cvid, $resource, $resourceType);
	$totalTaskHoursDay = [];
	foreach ($ordersTasksList as $ordersTask) {
	    $totalTaskHoursDay[$ordersTask['calendar_date']] = $totalTaskHoursDay[$ordersTask['calendar_date']] + $ordersTask['estimated_hours'] * $ordersTask['resource_qty'];
	}

	return $totalTaskHoursDay;
    }

    /**
     *
     * Returns an array of all the tasks id on a given date filtered by: Resource Type, Participating Agent, Selected filter id
     *
     * @param type $startDate
     * @param type $endDate
     * @param type $resource employee or vehicle
     * @param type $resourceType id of the employee role or vehicle type name
     * @param type $cvid filter by id
     * @return array array with the following values (OrdersTasksid, ResourceQty, Task Duration,Task Date)
     */
    public function getOrderTasksByDay ($startDate, $endDate, $cvid, $resource = '', $resourceType = '') {
	$db = PearDatabase::getInstance();
	$orderTaskList = array();
	$sql = "SELECT vtiger_orderstask.orderstaskid, vtiger_orderstask.calendarcode, vtiger_orderstask.ordersid,
            (
            CASE 
            WHEN vtiger_orderstask.disp_assigneddate IS NULL
            THEN vtiger_orderstask.service_date_from
            ELSE vtiger_orderstask.disp_assigneddate 
            END
            ) AS calendar_date, vtiger_orderstask.estimated_hours";
        if($resource == 'vehicles'){
            $sql .= ", TIME_TO_SEC(TIMEDIFF( vtiger_agentmanager.order_task_end_time, vtiger_agentmanager.order_task_start_time ))/3600 as default_task_duration";
        } else {
            $sql .= ", TIME_TO_SEC(TIMEDIFF( vtiger_agentmanager.personnel_end_time, vtiger_agentmanager.personnel_start_time ))/3600 as default_task_duration";
        }

	$queryFilter = $this->getQueryFromFilter($cvid);
	$agentWhere = $this->getCapacityCalendarAgentWhere();
	$resourcesWhere = $this->getResourcesWhere($resource, $resourceType);

	$where = $agentWhere . ' AND ' . $queryFilter[1] . $resourcesWhere;

	$sql .= " FROM " . $queryFilter[0] . "
	    INNER JOIN vtiger_agents ON vtiger_orderstask.participating_agent = vtiger_agents.agentsid
            INNER JOIN vtiger_agentmanager ON vtiger_agents.agentmanager_id = vtiger_agentmanager.agentmanagerid";

	$sql .= " WHERE " . $where . "
	    GROUP BY vtiger_orderstask.orderstaskid";


	$sql .= " HAVING calendar_date >= ? AND calendar_date <= ?";

	$result = $db->pquery($sql, array($startDate, $endDate));
	if ($db->num_rows($result) > 0) {

	    while ($row = $db->fetchByAssoc($result)) {

                if( $row['default_task_duration'] < 0 ){
                    $row['default_task_duration'] = 24 + $row['default_task_duration'];
                }
		if ($row['estimated_hours'] != '' && (float) $row['estimated_hours'] != 0 && (float) $row['estimated_hours'] > (float) $row['default_task_duration']) {
		    $row['estimated_hours'] = (float) $row['default_task_duration'];
		} else {
		    $row['estimated_hours'] = (float) $row['estimated_hours'];
		}

		unset($row['default_task_duration']);
		$row['resource_qty'] = 0;

		$orderTaskList[$row['orderstaskid']] = $row;
	    }

	    $orderTaskList = $this->getTaskResourceQty($resource, $resourceType, $orderTaskList);
	}

	return $orderTaskList;
    }

    function getQueryFromFilter($cvid) {
	$currentUser = Users_Record_Model::getCurrentUserModel();
	$qGenerator = new QueryGenerator("OrdersTask", $currentUser);
	$cvQuery = $qGenerator->getCustomViewQueryById($cvid);
	$cvQueryArr = explode("WHERE", $cvQuery);
	$fromCond = explode("FROM", $cvQueryArr[0])[1];
	$cvQueryArr = explode("GROUP BY", $cvQueryArr[1]);

	$agentCondition = getListviewOwnerCondition('OrdersTask');
	if ($agentCondition && $agentCondition != '') {
	    $agentCondition = " AND $agentCondition";
	}

	$cvWhere = str_replace($agentCondition, '', $cvQueryArr[0]);


	//We need an special JOIN because of the capacity calendar
	$fromCond = str_replace('INNER JOIN vtiger_agentmanager ON vtiger_crmentity.agentid = vtiger_agentmanager.agentmanagerid ', '', $fromCond);

	return array($fromCond, $cvWhere);
    }

    function getCapacityCalendarAgentWhere() {
	$user = Users_Record_Model::getCurrentUserModel();

	$accesibleAgents = array_keys($user->getAccessibleAgentsForUser());
	if ($accesibleAgents && $accesibleAgents != '') {
	    $agentWhere = ' vtiger_agents.agentmanager_id IN (' . implode(',', $accesibleAgents) . ')';
	}

	return $agentWhere;
    }

    function getResourcesWhere($resource, $resourceType) {



	if ($resourceType == 'all' || $resourceType == 'null') {
	    return '';
	} elseif ($resource == 'employees' && $resourceType != 'all' && $resourceType != 'null' && $resourceType != '') {

	    $query = "AND vtiger_orderstask.orderstaskid IN (SELECT orderstaskid FROM vtiger_orderstask_extra WHERE fieldvalue = '$resourceType' AND fieldname='personnel_type')";

	    return $query;
	} elseif ($resource == 'vehicles' && $resourceType != 'all' && $resourceType != 'null' && $resourceType != '') {
	    $query = "AND vtiger_orderstask.orderstaskid IN (SELECT orderstaskid FROM vtiger_orderstask_extra WHERE fieldvalue = '$resourceType' AND fieldname='vehicle_type')";

	    return $query;
	} else {
	    return '';
	}
    }

    /**
     * Given a an array of Orderstaskids a resource type and resource name will return an array with the
     * required resource qty for each ordertasksid
     *
     */
    function getTaskResourceQty($resource, $resourceType, $orderTaskList) {
	$db = PearDatabase::getInstance();

	$sql = "SELECT CONCAT(orderstaskid,'-',sequence) as tmpid FROM vtiger_orderstask_extra WHERE orderstaskid IN (" . generateQuestionMarks(array_keys($orderTaskList)) . ") ";

	if ($resource == 'employees' && $resourceType != 'all' && $resourceType != 'null' && $resourceType != '') {
	    $sql .= "AND fieldvalue=? AND fieldname = 'personnel_type' ";
	    $result = $db->pquery($sql, [array_keys($orderTaskList), $resourceType]);
	} elseif ($resource == 'vehicles' && $resourceType != 'all' && $resourceType != 'null' && $resourceType != '') {
	    $sql .= "AND fieldvalue=? AND fieldname = 'vehicle_type' ";
	    $result = $db->pquery($sql, [array_keys($orderTaskList), $resourceType]);
	} elseif ($resource == 'employees' && ($resourceType == 'all' || $resourceType == 'null' || $resourceType == '')) {

	    //Need to sumthe Qty of all the employees required on those task ids
	    $sql .= " AND fieldname = 'personnel_type' ";
	    $result = $db->pquery($sql, [array_keys($orderTaskList)]);
	} elseif ($resource == 'vehicles' && ($resourceType == 'all' || $resourceType == 'null' || $resourceType == '')) {

	    //Need to sum the Qty of all the vehicles required on those task ids
	    $sql .= " AND fieldname = 'vehicle_type' ";
	    $result = $db->pquery($sql, [array_keys($orderTaskList)]);
	} else {
	    return $orderTaskList;
	}

	if ($result && $db->num_rows($result) > 0) {
	    $subArr = [];
	    while ($row = $db->fetch_row($result)) {
		$subArr[] = $row['tmpid'];
	    }
	}

	if (is_array($subArr) && count($subArr) > 0) {
	    $sql = "SELECT fieldvalue, orderstaskid FROM vtiger_orderstask_extra WHERE CONCAT(orderstaskid,'-',sequence) IN (" . generateQuestionMarks($subArr) . ") ";

	    if ($resource == 'employees') {
		$sql .= " AND fieldname = 'num_of_personal' ";
	    } elseif ($resource == 'vehicles') {
		$sql .= " AND fieldname = 'num_of_vehicle' ";
	    }

	    $result = $db->pquery($sql, [$subArr]);

	    if ($result && $db->num_rows($result) > 0) {
		while ($row = $db->fetch_row($result)) {
		    $orderTaskList[$row['orderstaskid']]['resource_qty'] = $orderTaskList[$row['orderstaskid']]['resource_qty'] + $row['fieldvalue'];
		}
	    }
	}

	return $orderTaskList;
    }

    public function getRoles(){
        $user =  Users_Record_Model::getCurrentUserModel();
        $accesibleAgents = $user->getAccessibleOwnersForUser('', true, true);
        unset($accesibleAgents['agents']);
        unset($accesibleAgents['vanlines']);
        $accesibleAgents = array_keys($accesibleAgents);

        $db = PearDatabase::getInstance();
        $resourcePicklist = array();
        $sql = "SELECT er.* FROM vtiger_employeeroles er INNER JOIN vtiger_crmentity cr ON er.employeerolesid = cr.crmid 
                                        WHERE cr.deleted = 0 AND cr.agentid IN (" . generateQuestionMarks($accesibleAgents) . ")";
        $result = $db->pquery($sql,array($accesibleAgents));
        if ($db->num_rows($result) > 0) {
            while ($row = $db->fetch_row($result)) {
                $resourcePicklist[$row['employeerolesid']]['description'] = $row['emprole_desc'];
                $resourcePicklist[$row['employeerolesid']]['class_type'] = $row['emprole_class_type'];
            }
        }
        return $resourcePicklist;
    }

    public function getPrimaryRoleDescription($roleId,$roles)
    {
        return ($roles[$roleId]?$roles[$roleId]['description']:"");
    }

    public function getSecondaryRoleDescription($roleIds,$roles)
    {
        $roleIds = explode(',', $roleIds);
        $description = "";
        $lastElement = end($roleIds);
        foreach ($roleIds as $roleId){
            if($roles[$roleId] && $roles[$roleId]['class_type'] == 'Operations'){
                $description .= $roles[$roleId]['description'];
            }
            if($roleId != $lastElement){$description .= "<br>\n";}
        }
        return $description;
    }

    public function getCVIDWHERE($cvid, $arrFields) {
	$db = PearDatabase::getInstance();
	$agents = $busenessLine = array();

	$qGenerator = new QueryGenerator("OrdersTask", Users_Record_Model::getCurrentUserModel());
	$cvQuery = $qGenerator->getCustomViewQueryById($cvid);

	$columns = $qGenerator->getWhereFields();
	$cv = new CustomView();
	foreach ($columns as $col) {
	    if (in_array($col, $arrFields)) {
		$result = $db->pquery("SELECT * FROM vtiger_cvadvfilter WHERE cvid = ? AND columnname LIKE '%" . $col . "%'", array($cvid));
		while ($row = $db->fetch_row($result)) {
		    $values = ($col == "business_line") ? explode(",", $row['value']) : $row['value'];
		    if ($col == "participating_agent") {
				$res = $db->pquery('SELECT agentmanager_id FROM vtiger_agents WHERE CONCAT(agentname," (",agent_number,")")  = "'.html_entity_decode($values, ENT_QUOTES).'"', array());
			if ($db->num_rows($res)) {
			    $values = array($db->query_result($res, 0, 'agentmanager_id'));
			} else {
			    continue;
			}
		    }
		    foreach ($values as $value) {
			$a = $cv->getAdvComparator($row['comparator'], $value);
			if ($col == "participating_agent") {
			    $agents[] = $a;
			} else if ($col == "business_line") {
			    $businessLine[] = $a;
			}
		    }
		}
	    }
	}

	$retArray = array("agents" => array_unique($agents), "bl" => array_unique($businessLine));

	return (count($retArray)) ? $retArray : array();
    }

    public function getBLineAndAgentCond($cvid, $withBL = true){

	$whereCond = $this->getCVIDWHERE($cvid, array("business_line", "participating_agent"));

	$agentBlineCondition ='';

	if(count($whereCond["agents"])){
	    foreach ($whereCond["agents"] as $agentID) {
		$agents[] = "vtiger_crmentity.agentid " . $agentID;
	    }

	    $agentBlineCondition .= " AND (" . implode(" OR ", $agents) . ")";
	}

	if($withBL){
	   if(count($whereCond["bl"])){
		$businessLine[] = "hd.holiday_business_line = 'All'";
		foreach ($whereCond["bl"] as $bl) {
		    $businessLine[] = "hd.holiday_business_line " . $bl;
		}
		$agentBlineCondition .= " AND (" . implode(" OR ", $businessLine) . ")";
	    }
	}


	return $agentBlineCondition;

    }

    function getFiltersForUser($userID, $tableType, $customView){
		$db = PearDatabase::getInstance();
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$sessionViewName =  ($tableType == "A") ? "crewView" : (($tableType == "E") ? "equipmentView" : "vendorView");
 		$viewName = ($tableType == "A") ? "NewLocalDispatchCrew" : (($tableType == "E") ? "NewLocalDispatchEquipment" : "NewLocalDispatchVendors");
        $accesibleAgents = array_keys($currentUserModel->getAccessibleAgentsForUser());
		$result = $db->pquery("SELECT * FROM vtiger_customview WHERE (userid = ? AND view = ?) OR (userid = ? AND setdefault = '1' AND view = ?) OR (userid != ? AND view = ? AND agentmanager_id IN (" . generateQuestionMarks($accesibleAgents) . ")) ORDER BY userid IN (?) DESC",array($userID, $viewName, 1, $viewName,$userID,$viewName,$accesibleAgents, $userID));

		$html = '<span class="hide ldfilterActionImages pull-right">'
			. '<i title="Delete" data-value="delete" class="icon-trash alignMiddle deleteFilter ldfilterActionImage pull-right"></i>'
			. '<i title="Edit" data-value="edit" class="icon-pencil alignMiddle editFilter ldfilterActionImage pull-right"></i>'
			. '</span>';

		$html .= '<select class="chzn-select-ld ldFilterSelect pull-right" style="/*max-width:250px;*/max-width:60%;padding:0;margin:0;vertical-align:middle;" data-table-type="' . $tableType . '">';
		$html .= '<optgroup label="">';
        $selectedOnce = true;
		while ($row = $db->fetchByAssoc($result)) {
            $selected = '';
            if($customView !='' && $row['cvid'] == $customView){
                $selected =  "selected";
                $selectedOnce = false;
            }

            if($row['setdefault'] == "1" && $customView == '' && $selectedOnce ){
                $selected =  "selected";
                $selectedOnce = false;
		}
            $default = ($row['setdefault'] == "1" && $row['userid'] == 1) ? "true" : "false";

			$html .= '<option data-once="'.$selectedOnce.'" data-isdefault="'.$default.'" value="' . $row['cvid'] . '" ' . $selected . '>' . $row['viewname'] . ' </option>';
		}
		$html .= '</optgroup></select>';

		return $html;

	}

    function getOrdersTaskTimeSheetInfo($ordertaskid) {
		$db = PearDatabase::getInstance();
		$employeesInfo = array();
		$result = $db->pquery("SELECT ts.employee_id as id, ts.timesheet_personnelroleid as role, ts.actual_start_date, ts.actual_start_hour, ts.actual_end_hour, CONCAT(e.name,' ',e.employee_lastname) as fullname, total_hours, timeoff FROM vtiger_timesheets ts JOIN vtiger_employees e ON ts.employee_id = e.employeesid WHERE ordertask_id = ?", array($ordertaskid));
		if ($db->num_rows($result) > 0) {
			while($row = $db->fetchByAssoc($result)){
				$employeesInfo[] = $row;
			}
		}
		return $employeesInfo;
    }

    function getCrewAssignedToTask($ordersTaskId){
        $ordersTaskRecord = Vtiger_Record_Model::getInstanceById($ordersTaskId, 'OrdersTask');
        $assignedCrew = explode(' |##| ', $ordersTaskRecord->get('assigned_employee'));
        $assignedDate = $ordersTaskRecord->get('disp_assigneddate');
        $actualStartHour = $ordersTaskRecord->get('disp_assignedstart');
        $actualEndHour = $ordersTaskRecord->get('disp_actualend');
        $employeesInfo = array();
        $db = PearDatabase::getInstance();
        $sql = "SELECT vtiger_employees.employeesid as id, CONCAT(vtiger_employees.name,' ',vtiger_employees.employee_lastname) as fullname, vtiger_orderstasksemprel.role  "
                . " FROM vtiger_employees "
                . " LEFT JOIN vtiger_orderstasksemprel ON vtiger_orderstasksemprel.employeeid = vtiger_employees.employeesid "
                . " JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_employees.employeesid "
                . " WHERE vtiger_crmentity.deleted = 0 AND vtiger_orderstasksemprel.taskid = ? AND vtiger_employees.employeesid IN (" . generateQuestionMarks($assignedCrew) . ")";
        $result = $db->pquery($sql, array($ordersTaskId,$assignedCrew));
        if($db->num_rows($result) > 0){
            while($row = $db->fetchByAssoc($result)){
                $employeesInfo[$row['id']] = $row;
                $employeesInfo[$row['id']]['actual_start_date'] = $assignedDate;
                $employeesInfo[$row['id']]['actual_start_hour'] = $actualStartHour;
                $employeesInfo[$row['id']]['actual_end_hour'] = $actualEndHour;
            }
		}
		return $employeesInfo;
    }

    function getFromDateAndStartEndTime($taskIdArray){
        $data = [
            'fromDate' => '',
            'toDate' => '',
            'startTime' => '',
            'endTime' => ''
        ];
        foreach ($taskIdArray as $taskId) {
            $orderTaskRecord = Vtiger_Record_Model::getInstanceById($taskId, "OrdersTask");
            //fromDate
            if($orderTaskRecord->get('disp_assigneddate') !=''){
                $fromDate = $orderTaskRecord->get('disp_assigneddate');
            } else {
                $fromDate = $orderTaskRecord->get('service_date_from');
			}

			$data['datesArray'][]= $fromDate;

            if($data['fromDate'] == ''){
                $data['fromDate'] = $fromDate;
            }else{
                $date1 = new DateTime($data['fromDate']);
                $date2 = new DateTime($fromDate);
                if($date2 < $date1){
                    $data['fromDate'] = $fromDate;
                }
            }
            //toDate
            if($orderTaskRecord->get('disp_assigneddate') !=''){
                $toDate = $orderTaskRecord->get('disp_assigneddate');
            } else {
                $toDate = $orderTaskRecord->get('service_date_from');
            }
            if($data['toDate'] == ''){
                $data['toDate'] = $toDate;
            }else{
                $date1 = new DateTime($data['toDate']);
                $date2 = new DateTime($toDate);
                if($date2 > $date1){
                    $data['toDate'] = $toDate;
                }
            }
            //startTime
            $startTime = $orderTaskRecord->get('disp_assignedstart');
            if($startTime != ''){
                if($data['startTime'] == ''){
                    $data['startTime'] = $startTime;
                }else{
                    $date1 = new DateTime(DateTimeField::convertToUserTimeZone($data['startTime'])->format('H:i:s'));
                    $date2 = new DateTime(DateTimeField::convertToUserTimeZone($startTime)->format('H:i:s'));
                    if($date2 < $date1){
                        $data['startTime'] = $startTime;
                    }
                }
            }
            //endTime
            $endTime = $orderTaskRecord->get('disp_actualend');
            if($endTime != ''){
                if($data['endTime'] == ''){
                    $data['endTime'] = $endTime;
                }else{
                    $date1 = new DateTime(DateTimeField::convertToUserTimeZone($data['endTime'])->format('H:i:s'));
                    $date2 = new DateTime(DateTimeField::convertToUserTimeZone($endTime)->format('H:i:s'));
                    if($date2 > $date1){
                        $data['endTime'] = $endTime;
                    }
                }
            }
        }
        return $data;
	}

    public function getOutOfServiceEmployeesByDate($startDate, $endDate, $status = 'Out of Service') {
        //status can be 'Out of Service' or 'On Notice'
	$db = PearDatabase::getInstance();

        $result = $db->pquery("SELECT vtiger_outofservice.* FROM vtiger_outofservice 
                INNER JOIN vtiger_crmentity ON vtiger_outofservice.outofserviceid = vtiger_crmentity.crmid 
                WHERE deleted=0 AND outofservice_status = '$status' 
                AND outofservice_effectivedate IS NOT NULL");

	$offEmps = [];
	if ($result && $db->num_rows($result) > 0) {
	    while ($row = $db->fetchByAssoc($result)) {
		$offEmps[] = $row;
	    }
	}


	$date1 = new DateTime($startDate);
	$date2 = new DateTime($endDate);
	$diff = $date2->diff($date1)->format("%a") + 1;


	$outOfServiceEmps = [];
	for ($i = 0; $i < $diff; $i++) {
	    $currentDate = date('Y-m-d', strtotime("+" . $i . " days", strtotime($startDate)));

	    foreach ($offEmps as $offEmp) {
		if ($offEmp['outofservice_employeesid'] != null && $currentDate >= $offEmp['outofservice_effectivedate'] && ($currentDate <= $offEmp['outofservice_satisfieddate'] || $offEmp['outofservice_satisfieddate'] == '')) {
		    $outOfServiceEmps[$currentDate][] = $offEmp['outofservice_employeesid'];
		}
	    }
	}

	return $outOfServiceEmps;
    }

	public function getAvailableVendors($assignedResources,$selectedColumns = '', $dateArray='', $taskIdsArray = []){
        $db = PearDatabase::getInstance();
        $currentUserModel = Users_Record_Model::getCurrentUserModel();

		$sql = "SELECT * FROM vtiger_vendor v
			INNER JOIN vtiger_crmentity cr ON v.vendorid = cr.crmid 
			INNER JOIN vtiger_agentmanager am ON am.agentmanagerid = cr.agentid  
			WHERE cr.deleted = 0 AND v.vendor_status = 'Active'";
        $params = array();

        $agents = [];
        foreach ($taskIdsArray as $id) {
            $orderTaskRecord = Vtiger_Record_Model::getInstanceById($id, 'OrdersTask');
            if($orderTaskRecord->get('participating_agent')){
                $agentRecodModel = Vtiger_Record_Model::getInstanceById($orderTaskRecord->get('participating_agent'), 'Agents');
                array_push($agents, $agentRecodModel->get("agentmanager_id"));
            }
        }

        if(count($agents) == 0){
            $agents = array_keys($currentUserModel->getAccessibleAgentsForUser());
        }

        array_unique($agents);

        $sql .= " AND cr.agentid  IN (" . generateQuestionMarks($agents) . ")";
        array_push($params,  $agents);


        if($dateArray['fromDate'] !='' && vtlib_isModuleActive('VendorsOutofService')){
            $sql .= " AND v.vendorid NOT IN (SELECT vtiger_vendorsoutofservice.voos_vendorid FROM vtiger_vendorsoutofservice 
                INNER JOIN vtiger_crmentity ON vtiger_vendorsoutofservice.vendorsoutofserviceid = vtiger_crmentity.crmid 
                WHERE deleted=0 AND voos_status = 'Out of Service' 
                AND voos_effective_date <= ? 
                AND (voos_reinstated_date IS NULL OR voos_reinstated_date > ?) 
                AND voos_vendorid > 0)";

            array_push($params, $dateArray['fromDate'], $dateArray['fromDate']);
        }
        if (count($assignedResources) > 0) {
            $sql .= " ORDER BY v.vendorid IN (" . implode(",", $assignedResources) . ") DESC";
        }

        $result = $db->pquery($sql, $params);

        if ($result) {
            while ($row = $db->fetchByAssoc($result)) {

                $vendors[$row['vendorid']]['id']          = $row['vendorid'];
				foreach($selectedColumns as $fieldName){
					$fieldModel =  Vtiger_Field_Model::getInstance($fieldName,Vtiger_Module_Model::getInstance('Vendors'));
					if($fieldModel){
						$displayValue = $fieldModel->getDisplayValue($row[$fieldName]);
					}else if($fieldName = "smownerid"){
						$vendors[$row['vendorid']]["hiddensmownerid"] = $row[$fieldName];
						$displayValue = Vtiger_Functions::getOwnerRecordLabel($row[$fieldName]);
					}else{
						$displayValue = $row[$fieldName];
					}
					$vendors[$row['vendorid']][$fieldName] = $displayValue;
				}
            }
        }
        if(is_array($vendors) && count($vendors)){
            $onNoticeVendors = $this->getOnNoticeVendors($dateArray['fromDate'],array_keys($vendors));

            foreach($vendors as $vendorId => $vendor){
                if(in_array($vendorId,$onNoticeVendors )){
                    $vendors[$vendorId]['on_notice'] = 1;
                }else{
                    $vendors[$vendorId]['on_notice'] = 0;
                }
            }
        }
        return $vendors;
	}

	public function getOnNoticeVendors($dispatchDate, $vendorList){
        $db = PearDatabase::getInstance();

	    $params = [$dispatchDate, $dispatchDate, $vendorList];

	    $result = $db->pquery("SELECT vtiger_vendorsoutofservice.* FROM vtiger_vendorsoutofservice 
			INNER JOIN vtiger_crmentity ON vtiger_vendorsoutofservice.vendorsoutofserviceid = vtiger_crmentity.crmid 
			WHERE deleted=0 AND voos_status = 'On Notice' 
			AND voos_effective_date <= ?  
			AND (voos_reinstated_date IS NULL OR voos_reinstated_date > ?) 
			AND voos_vendorid IN (" . generateQuestionMarks($vendorList) . ")", $params);

        $vendorsOnNotice = array();
        if ($db->num_rows($result) > 0) {
            while ($row = $db->fetchByAssoc($result)) {
                $vendorsOnNotice[]= $row['voos_vendorid'];
            }
        }

        return $vendorsOnNotice;
    }
}
