<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class OrdersTask_CapacityCalendarFeed_Action extends Vtiger_BasicAjax_Action {

    public function process(Vtiger_Request $request) {
	try {
	    $orders = $this->getCapacityCalendar($request);

	    echo json_encode($orders);
	} catch (Exception $ex) {
	    echo $ex->getMessage();
	}
    }

    public function getCapacityCalendar(Vtiger_Request $request) {
	$calendarSettings = $this->getCalendarSettings();

	$startDate = $request->get('start');
	$endDate = $request->get('end');

	//filter employees and vehicles
	$resource = $request->get('resource');
	$resourceType = $request->get('resourceType');
	$employeeRole = '';
	if ($resource == 'employees' && $resourceType != '' && $resourceType != 'all' && $resourceType != 'null') {
	    $employeeRole = $resourceType;
	    $resourceModel = Vtiger_Record_Model::getInstanceById($resourceType, 'EmployeeRoles');
	    $resourceLabel = $resourceModel->get('emprole_desc');
	}
	$vehicleType = '';
	if ($resource == 'vehicles' && $resourceType != '' && $resourceType != 'all' && $resourceType != 'null') {
	    $vehicleType = $resourceType;
	    $resourceLabel = '';
	}

	$cvid = $request->get('filter_id');

	$ordersTaskInstance = Vtiger_Module_Model::getInstance('OrdersTask');
	$availableResources = array();
	if ($resource == 'employees') {
	    $availableResources = $ordersTaskInstance->getAvailableEmployeeCapacity($startDate, $endDate, $employeeRole, $cvid, 'hours');
	} else {
	    $availableResources = $ordersTaskInstance->getAvailableVehiclesCapacity($startDate, $endDate, $vehicleType, $cvid, 'hours');
	}
	$totalHsOfTasks = $ordersTaskInstance->getTotalOrderTasksHoursByDay($startDate, $endDate, $resource, $resourceType, $cvid);
	$blockedDays = $this->getBlockedDays($startDate, $endDate, $cvid);

	$date1 = new DateTime($startDate);
	$date2 = new DateTime($endDate);

	$diff = $date2->diff($date1)->format("%a");

	$orders = array();
        $blockedDaysColor = $this->getBlockedDaysColor();
        $calendarColorSettings = $this->getCalendarColorSettings();
	for ($i = 0; $i < $diff; $i++) {
	    $currentDate = date('Y-m-d', strtotime("+" . $i . " days", strtotime($startDate)));
	    $capacityUsage = $this->getDayCapacity($currentDate, $availableResources, $totalHsOfTasks, $resource, $resourceType, $resourceLabel);


	    $flag = $this->checkDailyNotes($currentDate, $cvid);

	    $text = "";
	    if (array_key_exists($currentDate, $blockedDays)) {
		$text = $blockedDays[$currentDate];
		$isBlocked = (strpos($text, 'Blocked') !== false) ? true : false;
		$isHoliday = (strpos($text, 'Holiday') !== false) ? true : false;
	    } else {
		$isBlocked = $isHoliday = false;
	    }

	    $orderDay = array(
		'title' => ((isset($capacityUsage) && is_numeric($capacityUsage)) ? $capacityUsage . '%' : $capacityUsage),
		'start' => $currentDate,
		'day_index' => $i,
		'hasNotes' => $flag,
		'isBlocked' => $isBlocked,
		'isHoliday' => $isHoliday,
	    );

//          colors are not used any more
	    $weekDay = date("w", strtotime($currentDate));

	    if(strpos($text, 'Blocked') !== false) { //blocked or holiday/blocked (both)
		$orderDay['color'] = $blockedDaysColor;
	    } elseif ((array_key_exists($weekDay, $calendarSettings) && !$calendarSettings[$weekDay])) { //weekend
		$orderDay['color'] = '#bebebe';
		$orderDay['title'] = '';
	    } else if (!is_numeric($capacityUsage)) { //no resources available
		$orderDay['color'] = '#FFF';
	    } else {//holiday or normal day color
		$orderDay['color'] = $this->getDayBackgroundColor($calendarColorSettings,$capacityUsage);
	    }

	    array_push($orders, $orderDay);
	}

	return $orders;
    }

    public function getBlockedDaysColor() {
	$db = PearDatabase::getInstance();
        $currentUserId = Users_Record_Model::getCurrentUserModel()->getId();
	$result = $db->query("SELECT * FROM vtiger_calendar_settings WHERE userid=$currentUserId");

	if ($db->num_rows($result) > 0) {
	    $row = $db->fetchByAssoc($result);
            if((float)$row['percentage_3'] > (float)$row['percentage_2'] && (float)$row['percentage_3'] > (float)$row['percentage_1']){
                $color = $row['color_3'];
            }elseif((float)$row['percentage_2'] > (float)$row['percentage_3'] && (float)$row['percentage_2'] > (float)$row['percentage_1']){
                $color = $row['color_2'];
            }else{
                $color = $row['color_1'];
            }
	} else {
	    $color = '#EF9A9A';
	}

	return $color;
    }

    public function checkDailyNotes($date, $cvid) {
	$db = PearDatabase::getInstance();
	$user = Users_Record_Model::getCurrentUserModel();
	$accesibleAgents = array_keys($user->getAccessibleAgentsForUser());
	$orderTaskModel = Vtiger_Module_Model::getInstance('OrdersTask');
	$agentBlineCondition = $orderTaskModel->getBLineAndAgentCond($cvid, false);
	
	$flag = false;

	$result = $db->pquery("SELECT * FROM vtiger_dailynotes dn INNER JOIN vtiger_crmentity cr ON dn.dailynotesid = cr.crmid WHERE 
			 cr.agentid IN (" . generateQuestionMarks($accesibleAgents) . ") AND cr.deleted = 0 AND dn.dailynotes_date = ?" . $agentBlineCondition, array($accesibleAgents, $date));
	if ($db->num_rows($result) > 0) {
	    $flag = true;
	}

	return $flag;
    }

    public function getBlockedDays($start, $end, $cvid) {
	$db = PearDatabase::getInstance();
	$user = Users_Record_Model::getCurrentUserModel();
	$accesibleAgents = array_keys($user->getAccessibleAgentsForUser());
	$blockedDays = array();
	
	$orderTaskModel = Vtiger_Module_Model::getInstance('OrdersTask');
	$agentBlineCondition = $orderTaskModel->getBLineAndAgentCond($cvid);

	$result = $db->pquery("SELECT hd.* FROM vtiger_holiday hd INNER JOIN vtiger_crmentity cr ON hd.holidayid = cr.crmid 
				    WHERE cr.deleted = 0 
				    AND cr.agentid IN (" . generateQuestionMarks($accesibleAgents) . ")
				    AND hd.holiday_date >= ? 
				    AND hd.holiday_date <= ?" . $agentBlineCondition, array($accesibleAgents, $start, $end));

	if ($db->num_rows($result) > 0) {
	    while ($row = $db->fetch_row($result)) {
		if (array_key_exists($row['holiday_date'], $blockedDays)) {
		    $blockedDays[$row['holiday_date']] = $blockedDays[$row['holiday_date']] . "/" . $row['holiday_type'];
		} else {
		    $blockedDays[$row['holiday_date']] = $row['holiday_type'];
		}
	    }
	}

	return $blockedDays;
    }

    public function getDayBackgroundColor($calendarColorSettings,$usedCapacity) {
	foreach ($calendarColorSettings as $capacity => $color) {
	    if ($usedCapacity <= $capacity) {
		return $color;
	    }
	}
    }

    public function getDayCapacity($currentDate, $availableResources, $totalHsOfTasks, $resource, $resourceType, $resourceLabel = '') {
	if (isset($availableResources[$currentDate]) && $availableResources[$currentDate] > 0) {
	    $number = ($totalHsOfTasks[$currentDate] / $availableResources[$currentDate]) * 100;
	    $usedCapacity = number_format((float) $number, 2, '.', '');
	    return (float) $usedCapacity;
	} else {
	    if ($resource == 'employees' && $resourceType != '' && $resourceType != 'all' && $resourceType != 'null') {

		$text = vtranslate('No ', 'OrdersTask') . $resourceLabel . vtranslate('(s) Available ', 'OrdersTask');
	    } elseif ($resource == 'employees' && ($resourceType == '' || $resourceType == 'all' || $resourceType == 'null')) {
		$text = vtranslate('No Personnel Available', 'OrdersTask');
	    } elseif ($resource == 'vehicles' && ($resourceType == '' || $resourceType == 'all' || $resourceType == 'null')) {

		$text = vtranslate('No Vehicles Available', 'OrdersTask');
	    } elseif ($resource == 'vehicles' && $resourceType != '' && $resourceType != 'all' && $resourceType != 'null') {

		$text = vtranslate('No ', 'OrdersTask') . $resourceType . ' ' . vtranslate('(s) Available ', 'OrdersTask');
	    }

	    return $text;
	}
    }

    public function getCalendarColorSettings() {
	$db = PearDatabase::getInstance();
        $currentUserId = Users_Record_Model::getCurrentUserModel()->getId();
	$settings = array();
	$result = $db->query("SELECT * FROM vtiger_calendar_settings WHERE userid=$currentUserId");
	if($db->num_rows($result) > 0){
            while ($arr = $db->fetchByAssoc($result)) {
                $settings[$arr['percentage_1']] = $arr['color_1'];
                $settings[$arr['percentage_2']] = $arr['color_2'];
                $settings[$arr['percentage_3']] = $arr['color_3'];
            } 
        }
        
	if (count($settings) == 0) {
	    $settings = array(
		50 => '#A5D6A7',
		80 => '#FFE082',
		1000000 => '#EF9A9A'
	    );
	} else {
	    $settings[1000000] = $settings[max(array_keys($settings))]; // In case the % is bigger than the max % selected
	}


	return $settings;
    }

    public function getCalendarSettings() {
	$db = PearDatabase::getInstance();
        $currentUserId = Users_Record_Model::getCurrentUserModel()->getId();
	$settings = array();
	$query = $db->query("SELECT saturday_work_day,sunday_work_day FROM vtiger_calendar_settings WHERE userid=$currentUserId");
	while ($arr = $db->fetch_array($query)) {
	    $settings[6] = ($arr['saturday_work_day'] == '1' ? true : false);
	    $settings[0] = ($arr['sunday_work_day'] == '1' ? true : false);
	}
	if (count($settings) == 0) {
	    $settings = array(
		'6' => true, //Saturdays
		'0' => true, //Sundays
	    );
	}

	return $settings;
    }

}
