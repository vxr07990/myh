<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class OrdersTask_CapacityCalendarOrdersFeed_Action extends Vtiger_BasicAjax_Action {

    public function process(Vtiger_Request $request) {
	
	 $cvid = $request->get('filter_id');
	 $unformattedDate = date('Y-m-d', $request->get('start_date'));
	
	if($request->get('mode') == 'holidays'){
	    
	    $html2 = $this->getHolidayBlockedGuestBlock($unformattedDate, $cvid);
	    
	    $result = array(
		'result' => 'OK',
		'result_data' => $html2
	    );
	    
	    
	    $msg = new Vtiger_Response();
	    $msg->setResult($result);
	    $msg->emit();
	    
	} elseif ($request->get('mode') == 'notes') {
	    
	    $html2 = $this->getDailyNotesGuestBlock($unformattedDate, $cvid);
	    
	    $result = array(
		'result' => 'OK',
		'result_data' => $html2
	    );
	    
	    $msg = new Vtiger_Response();
	    $msg->setResult($result);
	    $msg->emit();
	    
	} else {
	   try {
	   
	    $columns = $this->getColumnsByFilter($cvid);
	    $headers = $this->getTableHeaders($columns);

	    
	    $date = DateTimeField::convertToUserFormat($unformattedDate);
	    $html = '<input type="hidden" data-forsafari="' . $unformattedDate . '" value="' . $date . '" id="selected_date">';
	    $html .= '<br><table class="table table-bordered listViewEntriesTable tasks-for">
                <thead>
                <tr class="listViewHeaders title">
                <th colspan="'. (count($headers) + 1) .'">' .vtranslate('LBL_ORDER_FOR_DATE', 'OrdersTask').' <span class="selected-day"></span></th>
                </tr>
                <tr class="listViewHeaders">';

	    foreach ($headers as $fieldLabel) {
		$html .= '<th>' . $fieldLabel . '</th>';
	    }

	    $html .= '<th style="width: 70px;">' . vtranslate('LBL_Actions', 'OrdersTask') . '</th></tr></thead><tbody>';

	    $date = date('Y-m-d', $request->get('start_date'));

	    $resource = $request->get('resource');
	    $resourceType = $request->get('resource_type');

	    $ordersTaskInstance = Vtiger_Module_Model::getInstance('OrdersTask');
	    $ordersTaskList = $ordersTaskInstance->getOrderTasksByDay($date, $date, $cvid, $resource, $resourceType);

	    $ordersData = $this->getOrdersDetails($ordersTaskList, $columns);

	    if (count($ordersData) > 0) {
		foreach ($ordersData as $orderTaskId => $orderData) {
		    
			//$transfereeName = Vtiger_Functions::getCRMRecordLabels('Contacts', array($orderData['orders_transferees']));
			$html .= '<tr class="task-row listViewEntries" data-id="' . $orderTaskId . '">';

			foreach ($columns as $column) {
				$class = ($column['field'] == "total_estimated_vehicles" || $column['field'] == "total_estimated_personnel") ? ("customToolTip " . $column['field']) : "";
				if($column['field'] == 'dispatch_status'){
					$html .= '<td>' . $this->getDispatchStatusDropdown($orderData['dispatch_status'], $orderTaskId) . '</td>';
				}elseif ($column['field'] == 'orders_transferees'){
					$transfereeName = Vtiger_Functions::getCRMRecordLabels('Contacts', array($orderData['orders_transferees']));
					$html .= '<td>' . $transfereeName[$orderData['orders_transferees']] . '</td>';
				}else{
					$html .= '<td class="'.$class.'">';
					if($class != "") 
						$html .= "<span>";
					$html .= $orderData[$column['field']];
					if($class != "") 
						$html .= "</span>";
					$html .= '</td>';
			    }
			}

			$html .= '<td><i id="' . $orderTaskId . '" class="icon-eye-open"></i>'; 
			 if (Users_Privileges_Model::isPermitted('OrdersTask', 'EditView', $orderTaskId) == 'yes') {
			     $html .= '<i id="' . $orderTaskId . '" class="icon-pencil"></i>  </td>';
			 }
			$html .= '</tr>';
		    
		}
	    }

	    $html .= '</tbody></table>';

	    //daily summary table
	    //filter employees and vehicles
	    $employeeRole = '';
	    if ($resource == 'employees' && $resourceType != 'all' && $resourceType != 'null') {
		$employeeRole = $resourceType;
	    }
	    $vehicleType = '';
	    if ($resource == 'vehicles' && $resourceType != 'all' && $resourceType != 'null') {
		$vehicleType = $resourceType;
	    }

            $numberOfEmployeesForDate = $ordersTaskInstance->getAvailableEmployeeCapacity($date,$date,$employeeRole,$cvid,'count');
            $numberOfVehiclesForDate = $ordersTaskInstance->getAvailableVehiclesCapacity($date,$date,$vehicleType,$cvid,'count');
            $numberOfOrders = $this->getOrdersCountForDate($date, $ordersTaskList);
            $revenueForDate = $this->getRevenueForDate($date, $ordersTaskList);
	    $cccArr = $this->getCapacityCalendarCounterData($date, $ordersTaskList);
			
            $html2 .= '<br><table class="table table-bordered listViewEntriesTable daily-summary-for">
                <thead>
                <tr class="listViewHeaders title">
                <th colspan="8">' . vtranslate('LBL_DAILY_SUMMARY_FOR_DATE', 'OrdersTask') . ' <span class="selected-day"></span></th>
                </tr>
                </thead>
                <tbody>';

	    $html2 .= '<tr>';
            $html2 .= '<td class="fieldLabel medium narrowWidthType"><i class="icon-list pull-right resourcePopup employeesNumber" style="margin-top: 1%;margin-left: 5%"></i> <label class=" pull-right marginRight10px resourcePopup employeesNumber" style="color:#1b426d">' .vtranslate('LBL_NUMBER_OF_EMPLOYEES', 'OrdersTask').'   </label> </td>';
            $html2 .= '<td class="fieldValue medium narrowWidthType"><span class="value">'.(empty($numberOfEmployeesForDate[$date])?'0':$numberOfEmployeesForDate[$date]).'</span></td>';
            $html2 .= '<td class="fieldLabel medium narrowWidthType"><i class="icon-list pull-right resourcePopup vehiclesNumber" style="margin-top: 1%;margin-left: 5%"></i> <label class="pull-right marginRight10px resourcePopup vehiclesNumber" style="color:#1b426d">' .vtranslate('LBL_NUMBER_OF_VEHICLES', 'OrdersTask').'</label></td>';
            $html2 .= '<td class="fieldValue medium narrowWidthType"><span class="value">'.(empty($numberOfVehiclesForDate[$date])?'0':$numberOfVehiclesForDate[$date]).'</span></td>';
            $html2 .= '<td class="fieldLabel medium narrowWidthType"><label class="pull-right marginRight10px">' .vtranslate('LBL_NUMBER_OF_ORDERS_TASKS', 'OrdersTask').'</label></td>';
            $html2 .= '<td class="fieldValue medium narrowWidthType"><span class="value">'.$numberOfOrders.'</span></td>';
            $html2 .= '<td class="fieldLabel medium narrowWidthType"><label class="pull-right marginRight10px">' .vtranslate('LBL_TOTAL_REVENUE_PER_DAY', 'OrdersTask').'</label></td>';
            $html2 .= '<td class="fieldValue medium narrowWidthType"><span class="value">$ '.$revenueForDate.'</span></td>';
            $html2 .= '</tr>';

            $html2 .= '<tr>';
	    $countRows = 0;
	    foreach ($cccArr as $key => $arr) {
		if ($countRows > 0 && ($countRows % 4 == 0)) {
		    $html2 .= '</tr><tr>';
		}
		$html2 .= '<td class="fieldLabel medium narrowWidthType"><label class="pull-right marginRight10px"> ' . $arr['calendarCode'] . '</label></td>';
		$html2 .= '<td class="fieldValue medium narrowWidthType"><span class="value">' . $arr['qty'] . '</span></td>';
		$countRows ++;
	    }
	    while ($countRows % 4 != 0) {
		$html2 .= '<td class="fieldLabel medium narrowWidthType"><label class="pull-right marginRight10px"></label></td>';
		$html2 .= '<td class="fieldValue medium narrowWidthType"><span class="value"></span></td>';
		$countRows ++;
	    }
	    $html2 .= '</tr>';

	    $html2 .= '</tbody></table>';
	    $html2 .= $html;

	    //$html2 .= $this->getDailyNotes($date);

	    if (Users_Privileges_Model::isPermitted('Holiday', 'DetailView')) {
		$html2 .= '<div class="holidays">' . $this->getHolidayBlockedGuestBlock($unformattedDate, $cvid) . '</div>';
	    }

	    if (Users_Privileges_Model::isPermitted('DailyNotes', 'DetailView')) {
		$html2 .= '<div class="notes">' . $this->getDailyNotesGuestBlock($unformattedDate, $cvid) . '</div>';
	    }

	    $result = array(
		'result' => 'OK',
		'result_data' => $html2
	    );

	    $msg = new Vtiger_Response();
	    $msg->setResult($result);
	    $msg->emit();
	} catch (Exception $ex) {
	    echo $ex->getMessage();
	} 
	}
	
	
	
	
    }

    public function getCapacityCalendarCounterData($date, $ordersTaskList) {
	$db = PearDatabase::getInstance();
	$data = array();
	foreach ($ordersTaskList as $ordersTaskId => $ordersTaskData) {
	    $calendarCode = $ordersTaskData['calendarcode'];

	    if ($calendarCode != 0) {
		$auxResult = $db->pquery("SELECT * FROM vtiger_capacitycalendarcounter ccc INNER JOIN vtiger_crmentity  ON ccc.capacitycalendarcounterid = vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted = 0 AND ccc.capacitycalendarcounterid = ?", array($calendarCode))->FetchRow();
		$calendarCode = $auxResult['calendar_code'];
		$countType = $auxResult['order_task_field'];

		if (!array_key_exists($calendarCode, $data)) {
		    $data[$calendarCode] = array('calendarCode' => $calendarCode, 'qty' => '0');
		}

		if ($countType == 'Record Count') {
		    $data[$calendarCode]['qty'] = $data[$calendarCode]['qty'] + 1;
		} else {
		    $fieldname = ($countType == 'Personnel Number') ? 'num_of_personal' : 'num_of_vehicle';
		    $blocklabel = ($countType == 'Personnel Number') ? 'LBL_PERSONNEL' : 'LBL_VEHICLES';

		    $auxxResult = $db->pquery("SELECT fieldname,fieldvalue FROM vtiger_orderstask_extra WHERE orderstaskid = ? AND blocklabel = ? AND fieldname = ?", array($ordersTaskId, $blocklabel, $fieldname));
		    while ($arr = $db->fetch_row($auxxResult)) {
			$val = ($arr['fieldvalue'] != '') ? floatval($arr['fieldvalue']) : 0;
			$data[$calendarCode]['qty'] = $data[$calendarCode]['qty'] + $val;
		    }
		}
	    }
	}

	return $data;
    }

    public function getOwners() {
	$db = PearDatabase::getInstance();
	$ownerArray = array();
	$user = Users_Record_Model::getCurrentUserModel();
	$accesibleAgents = array_keys($user->getAccessibleAgentsForUser());

	$ownerResult = $db->pquery("SELECT am.* FROM vtiger_agentmanager am INNER JOIN vtiger_crmentity  ON am.agentmanagerid = vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted = 0 AND vtiger_crmentity.agentid IN (" . generateQuestionMarks($accesibleAgents) . ")", array($accesibleAgents));
	while ($orow = $db->fetch_row($ownerResult)) {
	    $ownerArray[$orow['agentmanagerid']] = "(" . $orow['agency_code'] . ") " . $orow['agency_name'];
	}

	return $ownerArray;
    }

    public function getDailyNotesGuestBlock($date, $cvid) {
	$db = PearDatabase::getInstance();
	$user = Users_Record_Model::getCurrentUserModel();
	$accesibleAgents = array_keys($user->getAccessibleAgentsForUser());
	
	$orderTaskModel = Vtiger_Module_Model::getInstance('OrdersTask');
	$agentBlineCondition = $orderTaskModel->getBLineAndAgentCond($cvid, false);

	$result = $db->pquery("SELECT * FROM vtiger_dailynotes dn INNER JOIN vtiger_crmentity cr ON dn.dailynotesid = cr.crmid WHERE cr.agentid IN (" . generateQuestionMarks($accesibleAgents) . ") AND cr.deleted = 0 AND dn.dailynotes_date = ?" . $agentBlineCondition, array($accesibleAgents, $date));

	$html = '';

	$ownerArray = $this->getOwners();

	if ($result && $db->num_rows($result) > 0) {
	    $count = $db->num_rows($result);
	} else {
	    $count = 0;
	}


	$html .= "<table name='DailyNotesTable' class='table table-bordered blockContainer showInlineTable' style='margin-top:1%;'>
			<thead> <tr> <th class='blockHeader' colspan='9'>" . vtranslate('LBL_DAILYNOTES_TABLE_HEADER', 'DailyNotes') . " <span class='selected-day'></th> </tr> </thead>
			<tbody>
			<tr class='fieldLabel'>
				<td colspan='9'>
					<button type='button' class='addDailyNotes'>+</button>
					<button class='btn btn-success dailynotesSave' style='margin-top: 2px;clear:right;float:right' type='submit'><strong>Save</strong></button>
					<button type='button' class='addDailyNotes' style='float:right'>+</button>

				</td>
			</tr>
			<tr style='width:100%' class='fieldLabel'>
				<input type='hidden' name='dailynotesNumRows' value='" . $count . "'/></td>
				<td style='text-align:center;margin:auto;width:4%;'> </td>
				<td style='text-align:center;margin:auto;width:32%;'><span class='redColor'>* </span><b>" . vtranslate('Owner', 'DailyNotes') . "</b></td>
				<td style='text-align:center;margin:auto;width:32%;'><span class='redColor'>* </span><b>" . vtranslate('LBL_DAILYNOTES_NOTE', 'DailyNotes') . "</b></td>
			</tr>
			<tr style='margin:auto' class='defaultdailynotesRow dailynotesRow hide'>
				<td class='fieldValue' style='margin:auto'>
					<i title='Delete' class='icon-trash removeDailyNotes' data-id=''></i>
					<input type='hidden' class='default' name='dailynotesDelete' value='' />
					<input type='hidden' class='default' name='dailynotesId' value='none' />
				</td>
				<td class='fieldValue' style='margin:auto'>
					<div class='row-fluid'>
						<span class='span10 owner'>
							<select class='defaultselect'>";
	foreach ($ownerArray as $id => $owner) {
	    $html .= "<option value='" . $id . "'>" . $owner . "</option>";
	}
	$html .= "</select>
						</span>
					</div>
				</td>
				<td class='fieldValue' style='margin:auto'>
					<div class='row-fluid'>
						<span class='span10 note'>
							<textarea rows='3' style='resize:none;'></textarea>
						</span>
					</div>
				</td>
			</tr>";

	$i = 1;
	if ($result && $db->num_rows($result) > 0) {
	    while ($row = $db->fetch_row($result)) {
		$html .= "<tr style='margin:auto' class='dailynotes" . $i . " dailynotesRow'>
				<td class='fieldValue' style='margin:auto'>
					<i title='Delete' class='icon-trash removeDailyNotes'></i>
					<input type='hidden' class='default' name='dailynotesDelete' value='' />
					<input type='hidden' class='default' name='dailynotesId' value='" . $row['dailynotesid'] . "' />
				</td>
				<td class='fieldValue' style='margin:auto'>
					<div class='row-fluid'>
						<span class='span10 owner'>
							<select class='chzn select'>";
		foreach ($ownerArray as $id => $owner) {
		    $selected = ($id == $row['agentid']) ? "selected" : "";
		    $html .= "<option value='" . $id . "' " . $selected . ">" . $owner . "</option>";
		}
		$html .= "</select>
						</span>
					</div>
				</td>
				<td class='fieldValue' style='margin:auto'>
					<div class='row-fluid'>
						<span class='span10 note'>
							<textarea rows='3' style='resize:none;'>" . $row['dailynotes_note'] . "</textarea>
						</span>
					</div>
				</td>
				";
		$i++;
	    }
	}

	$html .= "</tbody></table>";




	return $html;
    }

    public function queryWithCVID($date, $cvid) {
	$db = PearDatabase::getInstance();
	$currentUser = Users_Record_Model::getCurrentUserModel();

	$qGenerator = new QueryGenerator("OrdersTask", $currentUser);
	$cvQuery = $qGenerator->getCustomViewQueryById($cvid);
	$cvQueryArr = explode("WHERE", $cvQuery);
	$cvQueryArr = explode("GROUP BY", $cvQueryArr[1]);
	$cvQueryArr[0] = str_replace("vtiger_crmentity.deleted=0 AND", "", $cvQueryArr[0]);

	$sql = "SELECT * FROM vtiger_orders
				INNER JOIN vtiger_crmentity ON vtiger_orders.ordersid = vtiger_crmentity.crmid
				INNER JOIN vtiger_orderstask ON vtiger_orders.ordersid = vtiger_orderstask.ordersid
				INNER JOIN vtiger_crmentity as crm2 ON vtiger_orderstask.orderstaskid = crm2.crmid
				LEFT JOIN vtiger_crmentityrel ON vtiger_orderstask.orderstaskid = vtiger_crmentityrel.crmid";

	$sql .= " WHERE vtiger_crmentity.deleted = 0 AND crm2.deleted=0 AND " . $cvQueryArr[0] . " AND (vtiger_orderstask.service_date_from = ? OR vtiger_orderstask.disp_assigneddate = ?) AND dispatch_status != 'Rejected'";

	return $db->pquery($sql, array($date, $date));
    }

    public function getHolidayBlockedGuestBlock($date, $cvid) {
	$db = PearDatabase::getInstance();
	$user = Users_Record_Model::getCurrentUserModel();
	$accesibleAgents = array_keys($user->getAccessibleAgentsForUser());

	$orderTaskModel = Vtiger_Module_Model::getInstance('OrdersTask');
	$agentBlineCondition = $orderTaskModel->getBLineAndAgentCond($cvid);
	
	
	$sql = "SELECT * FROM vtiger_holiday hd INNER JOIN vtiger_crmentity  ON hd.holidayid = vtiger_crmentity.crmid WHERE  vtiger_crmentity.agentid IN (" . generateQuestionMarks($accesibleAgents) . ") AND vtiger_crmentity.deleted = 0 AND hd.holiday_date = ? " . $agentBlineCondition;

	$result = $db->pquery($sql, array($accesibleAgents, $date));

	$holidaytypeResult = $db->pquery("SELECT holiday_type FROM vtiger_holiday_type WHERE presence = 1", array());
	while ($htrow = $db->fetch_row($holidaytypeResult)) {
	    $holidayTypeArray[] = $htrow['holiday_type'];
	}
	$holidaybsResult = $db->pquery("SELECT holiday_business_line FROM vtiger_holiday_business_line WHERE presence = 1", array());
	while ($blrow = $db->fetch_row($holidaybsResult)) {
	    $bussineslineArray[] = $blrow['holiday_business_line'];
	}

	$ownerArray = $this->getOwners();

	$html .= "<table name='HolidayBlockedTable' class='table table-bordered blockContainer showInlineTable' style='margin-top:1%;'>
			<thead> <tr> <th class='blockHeader' colspan='9'>" . vtranslate('LBL_HOLIDAYBLOCKED_TABLE_HEADER', 'Holiday') . " <span class='selected-day'></th> </tr> </thead>
			<tbody>
			<tr class='fieldLabel'>
				<td colspan='9'>
					<button type='button' class='addHolidayBlocked'>+</button>
					<button style='margin-top: 2px;clear:right;float:right' class='btn btn-success holidayblockedSave' type='submit'><strong>Save</strong></button>
					<button type='button' class='addHolidayBlocked' style='float:right'>+</button>
				</td>
			</tr>
			<tr style='width:100%' class='fieldLabel'>
				<input type='hidden' name='holidayblockedNumRows' value='" . $db->num_rows($result) . "'/></td>
				<td style='text-align:center;margin:auto;width:4%;'> </td>
				<td style='text-align:center;margin:auto;width:32%;'><span class='redColor'>* </span><b>" . vtranslate('LBL_HOLIDAY_TYPE', 'Holiday') . "</b></td>
				<td style='text-align:center;margin:auto;width:32%;'><span class='redColor'>* </span><b>" . vtranslate('Owner', 'Holiday') . "</b></td>
				<td style='text-align:center;margin:auto;width:32%;'><span class='redColor'>* </span><b>" . vtranslate('LBL_HOLIDAY_BUSINESS_LINE', 'Holiday') . "</b></td>
			</tr>
			<tr style='margin:auto' class='defaultholidayblockedRow holidayblockedRow hide'>
				<td class='fieldValue' style='margin:auto'>
					<i title='Delete' class='icon-trash removeHolidayBlocked' data-id=''></i>
					<input type='hidden' class='default' name='holidayblockedDelete' value='' />
					<input type='hidden' class='default' name='holidayblockedId' value='none' />
				</td>
				<td class='fieldValue' style='margin:auto'>
					<div class='row-fluid'>
						<span class='span10 holidaytype'>
							<select class='defaultselect'>";
	foreach ($holidayTypeArray as $holidayType) {
	    $selected = ($holidayType == "Blocked") ? "selected" : ""; //b. Default Value: Blocked
	    $html .= "<option value='" . $holidayType . "' " . $selected . ">" . $holidayType . "</option>";
	}
	$html .= "</select>
						</span>
					</div>
				</td>
				<td class='fieldValue' style='margin:auto'>
					<div class='row-fluid'>
						<span class='span10 owner'>
							<select class='defaultselect'>";
	foreach ($ownerArray as $id => $owner) {
	    $html .= "<option value='" . $id . "'>" . $owner . "</option>";
	}
	$html .= "</select>
						</span>
					</div>
				</td>
				<td class='fieldValue' style='margin:auto'>
					<div class='row-fluid'>
						<span class='span10 bussinesline'>
							<select class='defaultselect' multiple>";
	foreach ($bussineslineArray as $bussinesLine) {
	    $selected = ($bussinesLine == "All") ? "selected" : ""; //b) Default Value = "All"
	    $html .= "<option value='" . $bussinesLine . "' " . $selected . ">" . $bussinesLine . "</option>";
	}
	$html .= "</select>
						</span>
					</div>
				</td>
			</tr>";

	$i = 1;
	while ($row = $db->fetch_row($result)) {
	    $bussinesline = ($row['holiday_business_line'] != "") ? $row['holiday_business_line'] : "-";

	    $html .= "<tr style='margin:auto' class='holidayblocked" . $i . " holidayblockedRow'>
				<td class='fieldValue' style='margin:auto'>
					<i title='Delete' class='icon-trash removeHolidayBlocked'></i>
					<input type='hidden' class='default' name='holidayblockedDelete' value='' />
					<input type='hidden' class='default' name='holidayblockedId' value='" . $row['holidayid'] . "' />
				</td>
				<td class='fieldValue' style='margin:auto'>
					<div class='row-fluid'>
						<span class='span10 holidaytype'>
							<select class='chzn select'>";
	    foreach ($holidayTypeArray as $holidayType) {
		$selected = ($row['holiday_type'] == $holidayType) ? "selected" : "";
		$html .= "<option value='" . $holidayType . "' " . $selected . ">" . $holidayType . "</option>";
	    }
	    $html .= "</select></span>
					</div>
				</td>
				<td class='fieldValue' style='margin:auto'>
					<div class='row-fluid'>
						<span class='span10 owner'>
							<select class='chzn select'>";
	    foreach ($ownerArray as $id => $owner) {
		$selected = ($id == $row['agentid']) ? "selected" : "";
		$html .= "<option value='" . $id . "' " . $selected . ">" . $owner . "</option>";
	    }
	    $html .= "</select>
						</span>
					</div>
				</td>
				<td class='fieldValue' style='margin:auto'>
					<div class='row-fluid'>
						<span class='span10 bussinesline'>
							<select class='chzn select' multiple>";
	    $bsArray = explode(' |##| ', $bussinesline);
	    foreach ($bussineslineArray as $bussinesLine) {
		$selected = (in_array($bussinesLine, $bsArray)) ? "selected" : "";
		$html .= "<option value='" . $bussinesLine . "' " . $selected . ">" . $bussinesLine . "</option>";
	    }
	    $html .= "</select>
						</span>
					</div>
				</td>
				";
	    $i++;
	}

	$html .= "</tbody></table>";

	return $html;
    }

    public function getTableHeaders($columns) {
	$headers = [];

	foreach ($columns as $arr) {
	    $fieldModel = Vtiger_Field_Model::getInstance($arr['field'], Vtiger_Module_Model::getInstance($arr['module']));
	    if ($fieldModel) {
		$fieldLabel = $fieldModel->get("label");
	    } else if ($arr['field'] == "smownerid") {
		$fieldLabel = "Assigned To";
	    }

	    $headers[] = vtranslate($fieldLabel, $arr['module']);
	}
	return $headers;
    }

    public function getColumnsByFilter($cvid) {
	$customView = new CustomView();
	$auxArr = $customView->getColumnsListByCvid($cvid);

	$returnArray = array();
	foreach ($auxArr as $auxItem) {
	    $aux = explode(":", $auxItem);
	    $auxS = explode("_", $aux[3]);
	    $fieldModel = Vtiger_Field_Model::getInstance($aux[1], Vtiger_Module_Model::getInstance($auxS[0]));
	    if ($fieldModel) {
		$returnArray[] = array("module" => $auxS[0], "field" => $aux[1]);
	    }
	}

	return $returnArray;
    }

    public function getOrdersDetails($ordersTaskList, $columns) {
	$ordersData = array();

	foreach ($ordersTaskList as $ordersTaskId => $value) {
	    $aux = array();
	    $orderTaskRecordModel = Vtiger_Record_Model::getInstanceById($ordersTaskId, 'OrdersTask');
	    foreach ($columns as $arr) {
		$fieldModel = Vtiger_Field_Model::getInstance($arr['field'], Vtiger_Module_Model::getInstance($arr['module']));
		if ($fieldModel) {
		    $displayValue = $fieldModel->getDisplayValue($orderTaskRecordModel->get($arr['field']));
		} else if ($arr['field'] = "smownerid") {
		    $displayValue = Vtiger_Functions::getOwnerRecordLabel($row[$arr['field']]);
		} else {
		    $displayValue = $orderTaskRecordModel->get($arr['field']);
		}
		$aux[$arr['field']] = $displayValue;
	    }

	    $ordersData[$ordersTaskId] = $aux;
	}

	$ordersTable = array();
	foreach ($ordersData as $orderTaskId => $ordersInfo) {
	    $ordersTable[$orderTaskId] = $ordersInfo;
	}

	return $ordersTable;
    }

    public function getDispatchStatusDropdown($status, $orderTaskId) {
	$statusPicklist = Vtiger_Util_Helper::getPickListValues('dispatch_status');

	$dropdown = '';
	$dropdown .= '<SELECT style="width:110px;" class="dispatch_status" id="' . $orderTaskId . '">';
	foreach ($statusPicklist as $value) {
	    $selected = '';

	    if ($value == 'Assigned' || $value == 'Unassigned') {
		continue;
	    }

	    if ($value == $status) {
		$selected = 'selected';
	    }

	    $dropdown .= '<option value="' . $value . '" ' . $selected . '>' . vtranslate($value, 'OrdersTask') . '</option>';
	}

	$dropdown .= '</select>';

	return $dropdown;
    }

    public function getOrdersCountForDate($date, $ordersTaskList) {
	// This use to be a huge function, now is just counting an array. 
	// Not sure if all they need is counting the array of something else. Just leaving it here for now.

	return count($ordersTaskList);
    }

    public function getRevenueForDate($date, $ordersTaskList) {
	$db = PearDatabase::getInstance();

	$ordersIds = $this->getOrdersFromOrdersTasks($ordersTaskList);
	$orders = array();
	$sql = "SELECT vtiger_orders.ordersid, vtiger_quotes.total , vtiger_orderstask.orderstaskid,
                                (
                                CASE 
                                WHEN vtiger_orderstask.disp_assigneddate IS NULL
                                THEN vtiger_orderstask.service_date_from
                                ELSE vtiger_orderstask.disp_assigneddate 
                                END
                                ) AS calendar_date   
                                FROM vtiger_quotes
                                INNER JOIN vtiger_crmentity ON vtiger_quotes.quoteid = vtiger_crmentity.crmid
                                INNER JOIN vtiger_orders ON vtiger_orders.ordersid = vtiger_quotes.orders_id 
                                INNER JOIN vtiger_orderstask ON vtiger_orderstask.ordersid = vtiger_orders.ordersid 
                                WHERE deleted=0 AND vtiger_crmentity.setype = 'Estimates' AND vtiger_quotes.is_primary = 1
                                AND dispatch_status != 'Rejected'
				AND vtiger_orders.ordersid IN (" . generateQuestionMarks($ordersIds) . ") ";
	
        $result = $db->pquery($sql, $ordersIds);
        $revenue = 0;
        if ($db->num_rows($result) > 0) {
            while ($row = $db->fetchByAssoc($result)) {
                
                if (empty($orders[$row['ordersid']]['total_orders'])) {
                    $orders[$row['ordersid']]['total_orders'] = 0;
                }
                if (empty($orders[$row['ordersid']]['total_amount'])) {
                    $orders[$row['ordersid']]['total_amount'] = 0;
                }
                $orders[$row['ordersid']]['total_amount'] = $row['total'];
                $orders[$row['ordersid']]['total_orders'] += 1;
                $orders[$row['ordersid']][$row['calendar_date']] += 1;
            }
            foreach ($orders as $id => $order) {//I think this is not necesary anymore but it makes no harm either
                if (!array_key_exists($date, $order)) {
                    unset($orders[$id]);
                }
            }
            foreach ($orders as $id => $order) {
                $revenue += $order['total_amount'] / $order['total_orders'] * $order[$date];
            }
            $revenue = number_format((float) $revenue, 2, '.', '');
        }
        return $revenue;
    }

    public function getOrdersFromOrdersTasks($ordersTaskList) {
	$ordersIds = array();
	foreach ($ordersTaskList as $ordersTask) {
	    $ordersIds[] = $ordersTask['ordersid'];
	}
	return $ordersIds;
    }

}
