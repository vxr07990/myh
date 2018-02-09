<?php

include_once 'include/fields/DateTimeField.php';
include_once 'include/Webservices/Revise.php';
include_once 'include/Webservices/Create.php';
include_once 'include/Webservices/Retrieve.php';
include_once 'include/Webservices/Delete.php';

class OrdersTask_NewLoadLocalDispatchActuals_View extends Vtiger_ListAjax_View {

    protected $forModule = 'OrdersTask';
    protected $dayDuration = 8;
    protected $default_start = '08:00:00';
    protected $default_task_duration = 1;

    public function __construct() {
	parent::__construct();
	$this->exposeMethod('getEmployeesAssignedToTask');
	$this->exposeMethod('getTaskCPUs');
	$this->exposeMethod('getTaskEquipments');
	$this->exposeMethod('saveActuals');
	$this->exposeMethod('updateOrdersTaskActualsEditableValues');
    }

    function saveActuals(Vtiger_Request $request) {
		$db = PearDatabase::getInstance();
		$user = Users_Record_Model::getCurrentUserModel();

		$task_id = $request->get("task_id");
		$assigned_employees = $request->get("assignedemployees");
		$cpus = $request->get("cpus");
		$equipments = $request->get("equipments");

		$agentid = $db->pquery("SELECT agentid FROM vtiger_crmentity WHERE crmid = ?", array($task_id))->FetchRow()['agentid'];

		//Save Equipments	
		$db->pquery("DELETE FROM vtiger_orderstask_extra WHERE orderstaskid = ? AND blocklabel = ?", [$task_id,'LBL_EQUIPMENT_ACTUALS']);
		
		foreach ($equipments as $sequence => $values) {
			foreach ($values as $fieldname => $fieldvalue){
				$insertResult = $db->pquery("INSERT INTO vtiger_orderstask_extra (orderstaskid,blocklabel,sequence,fieldname,fieldvalue) VALUES (?,?,?,?,?)", array($task_id,'LBL_EQUIPMENT_ACTUALS',$sequence+1,$fieldname,$fieldvalue));
			}
		}

		//Save CPUs
		$db->pquery("DELETE FROM vtiger_orderstask_extra WHERE orderstaskid = ? AND blocklabel = ?", [$task_id,'LBL_CPU_ACTUALS']);

		foreach ($cpus as $sequence => $values) {
			foreach ($values as $fieldname => $fieldvalue){
				$insertResult = $db->pquery("INSERT INTO vtiger_orderstask_extra (orderstaskid,blocklabel,sequence,fieldname,fieldvalue) VALUES (?,?,?,?,?)", array($task_id,'LBL_CPU_ACTUALS',$sequence+1,$fieldname,$fieldvalue));
			}
		}

	//Save Assigned Employees
        $employeesIds = array();
		foreach ($assigned_employees as $employee) {
            $employeesIds[] = $employee['employee_id'];
	    //Save TimeSheets
            $result = $db->pquery("SELECT timesheetsid FROM vtiger_timesheets WHERE employee_id = ? AND ordertask_id = ?",array($employee['employee_id'],$task_id));
            if($db->num_rows($result) > 0){//revise or delete
                $id = $result->FetchRow()['timesheetsid'];
                if($employee['delete']){
//                    $wsid = vtws_getWebserviceEntityId('TimeSheets', $id);
//                    $result = vtws_delete($wsid, $user);
					Vtiger_Utils::ExecuteQuery("DELETE FROM vtiger_timesheets WHERE timesheetsid = $id limit 1");
					continue;
				}else{
					$ts['id'] = vtws_getWebserviceEntityId('TimeSheets', $id);
					$ts['employee_id'] = vtws_getWebserviceEntityId('Employees', $employee['employee_id']);
					$ts['ordertask_id'] = vtws_getWebserviceEntityId('OrdersTask', $task_id);
					$ts['actual_start_date'] = DateTimeField::convertToDBFormat($employee['actualdate']);
					$ts['actual_start_hour'] = DateTimeField::convertToDBTimeZone($employee['actualstarthours'])->format('H:i');
					$ts['actual_end_hour'] = DateTimeField::convertToDBTimeZone($employee['actualendhours'])->format('H:i');
					$ts['timeoff'] = $employee['timeoff'];
					$ts['total_hours'] = $employee['totalhours'];
					$ts['assigned_user_id'] = vtws_getWebserviceEntityId('Users', $user->getId());
					$ts['agentid'] = $agentid;
					$ts['timesheet_personnelroleid'] = vtws_getWebserviceEntityId('EmployeeRoles', $employee['personnelrole']);
					$entity = vtws_revise($ts, $user);
				}
			} else {
				$ts['employee_id'] = vtws_getWebserviceEntityId('Employees', $employee['employee_id']);
				$ts['ordertask_id'] = vtws_getWebserviceEntityId('OrdersTask', $task_id);
				$ts['actual_start_date'] = DateTimeField::convertToDBFormat($employee['actualdate']);
				$ts['actual_start_hour'] = DateTimeField::convertToDBTimeZone($employee['actualstarthours'])->format("H:i");
				$ts['actual_end_hour'] = DateTimeField::convertToDBTimeZone($employee['actualendhours'])->format("H:i");
				$ts['timeoff'] = $employee['timeoff'];
                                $ts['total_hours'] = $employee['totalhours'];
				$ts['assigned_user_id'] = vtws_getWebserviceEntityId('Users', $user->getId());
				$ts['agentid'] = $agentid;
				$ts['timesheet_personnelroleid'] = vtws_getWebserviceEntityId('EmployeeRoles', $employee['personnelrole']);

				$entity = vtws_create('TimeSheets', $ts, $user);
			}
		}
        
        //check if there is a lead employee saved
        $result0 = $db->pquery("SELECT employeeid FROM vtiger_orderstasksemprel WHERE taskid = ? AND lead = '1'", [$task_id]);
        $leadEmployee = 0;
        if($db->num_rows($result0) > 0){
            $leadEmployee = $db->fetchByAssoc($result0)['employeeid'];
        }
        //delete all employees saved in vtiger_orderstasksemprel for this orderstask
        $result = $db->pquery("DELETE FROM vtiger_orderstasksemprel WHERE taskid = ?", [$task_id]);
        
        //insert the employees saved in Actuals so they appear in Local Dispatch
        foreach ($assigned_employees as $employee) {
            $lead = ( $employee['employee_id'] == $leadEmployee ? '1' : '0');
            $insert = $db->pquery("INSERT INTO vtiger_orderstasksemprel (role, lead, employeeid, taskid) VALUES (?,$lead,?,?)", array($employee['personnelrole'],$employee['employee_id'],$task_id));
        }
        
		$resultArray = array("employees" => count($assigned_employees));
		
		$msg = new Vtiger_Response();
		$msg->setResult($resultArray);
		$msg->emit();
    }

    function getEmployeesAssignedToTask(Vtiger_Request $request) {
	$user = Users_Record_Model::getCurrentUserModel();
	$db = PearDatabase::getInstance();
	$orderTaskID = $request->get('task_id');
	$orderTaskModel = Vtiger_Module_Model::getInstance('OrdersTask');

	$orderTaskRecordModel = Vtiger_Record_Model::getInstanceById($orderTaskID, 'OrdersTask');

	$operationTask = $orderTaskRecordModel->get('operations_task');
	$actualDate = $orderTaskRecordModel->get("disp_assigneddate");

	if ($actualDate != '') {
	    $actualDate = DateTimeField::convertToUserFormat($actualDate);
	} else {
	    $actualDate = '';
	}

	$actualStartHours = DateTimeField::convertToUserTimeZone($orderTaskRecordModel->get("disp_assignedstart"))->format('H:i:s');
	$actualEndHours = DateTimeField::convertToUserTimeZone($orderTaskRecordModel->get("disp_actualend"))->format('H:i:s');

	$roles = $orderTaskModel->getRoles();

	$htmlRoles = '<select class="chzn-select prole">';
	$arrRole = array();
	foreach ($roles as $roleId => $roleArray) {
            if($roleArray['class_type'] == 'Operations'){
                $htmlRoles .= '<option value="' . $roleId . '">' . $roleArray['description'] . '</option>';
            }
	}

	$htmlRoles .= '</select>';


	$html = '<div class="row-fluid">
			<table class="table table-bordered listViewEntriesTable" style="/*min-height:200px !important;height:100%;overflow-y: scroll;overflow-x: scroll*/">
			<thead>
			<tr class="listViewHeaders">
			<th></th>
			<th><strong>Operation Task</strong></th>
			<th><strong>Role</strong></th>
			<th style="padding-left:30px;"><strong>Personnel Name</strong></th>
			<th><strong>Actual Date</strong></th>
			<th><strong>Actual Start Time</strong></th>
			<th><strong>Actual End Time</strong></th>
			<th><strong>Time Off</strong></th>
			<th><strong>Total Worked Hours</strong></th>
			<th></th>
			</tr>
			</thead>
			<tbody>
			<tr class="hide defaultassignedemployee">
				<td style="width:1%;"><input type="checkbox" value="" checked class="select_assigned_employee hide"></td>
				<td class="operationtask" style="width:17%;"></td>
				<td class="personnelrole" style="width:15%;">';

	$html .= $htmlRoles;
	

	$html .= '</td>
				<td class="personnel" style="width:17%;">
					<span class="span10">
						<input name="popupReferenceModule" type="hidden" value="Employees">
						<input name="personnelID" type="hidden" value="0" class="sourceField" data-displayvalue="">
						<div class="row-fluid input-prepend input-append">
							<span class="add-on clearReferenceSelection cursorPointer OrdersTask_editView_fieldName_personnelID_clear">
								<i id="OrdersTask_editView_fieldName_personnelID_clear" class="icon-remove-sign" title="Clear"></i>
							</span>
							<input id="personnelID_display" name="personnelID_display" type="text" class="span7 marginLeftZero autoComplete ui-autocomplete-input" value="" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" placeholder="Type to search" autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true">
							<span class="add-on relatedPopup cursorPointer">
								<i id="OrdersTask_editView_fieldName_personnelID_select" class="icon-search" title="Select"></i>
							</span>
						</div>
					</span>
				</td> 
				<td class="actualdate" style="width:12%;">
					<div class="input-append row-fluid" style="min-width: 150px;">
						<div class="span12 row-fluid date">
							<input type="text" class="dateField" data-date-format="' . $user->get('date_format') . '" type="text" value="' . $actualDate . '" style="width:100px;" readonly/>
							<span class="add-on"><i class="icon-calendar"></i></span>
						</div>
					</div>
				</td>
				<td class="actualstarthours" style="width:12%;"><input class="timepicker" type="text" value="' . $actualStartHours . '" style="width: 100px;"></td>
				<td class="actualendhours" style="width:12%;"><input class="timepicker" type="text" value="' . $actualEndHours . '" style="width: 100px;"></td>
                                <td class="timeoff" style="width:12%;">
                                    <input type="number" min="0" step="0.1"  value style="width:100px; cursor: pointer;">
                                </td>
                                <td class="totalworkedhours" style="width:12%;">
                                    <input type="number" min="0" step="0.1"  value style="width:100px; cursor: pointer;" readonly>
                                </td>
                                <td style="width:3%;"><a class="deleteEmployeeButton"><i title="Delete" class="icon-trash alignMiddle"></i></a></td>
			</tr>';

        $employeesInfoArray = $orderTaskModel->getOrdersTaskTimeSheetInfo($orderTaskID);
        if(empty($employeesInfoArray)){
            $employeesInfoArray = $orderTaskModel->getCrewAssignedToTask($orderTaskID);
        }
        foreach ($employeesInfoArray as $employeesInfo) {

            $actualDate = ($employeesInfo['actual_start_date'] != "") ? DateTimeField::convertToUserFormat($employeesInfo['actual_start_date']) : "";
            $actualStartHours = ($employeesInfo['actual_start_hour'] != "") ? DateTimeField::convertToUserTimeZone($employeesInfo['actual_start_hour'])->format('H:i') : "";
            $actualEndHours = ($employeesInfo['actual_end_hour'] != "") ? DateTimeField::convertToUserTimeZone($employeesInfo['actual_end_hour'])->format('H:i') : "";

            $html .= '<tr class="assignedemployee">
                                    <td style="width:1%;"><input type="checkbox" data-employeeid="' . $employeesInfo['id'] . '" checked class="select_assigned_employee hide"></td>
                                    <td class="operationtask" style="width:12%;">' . $operationTask . '</td>
                                    <td class="personnelrole" style="width:12%;"> '; 

            $html .= '<select class="chzn-select prole">';
            $arrRole = array();
            foreach ($roles as $roleId => $roleArray) {
                if($roleArray['class_type'] == 'Operations'){
                    if($employeesInfo['role'] == $roleId){
                        $selected = 'selected';
                    }else{
                        $selected = '';
                    }

                    $html .= '<option value="' . $roleId . '" ' . $selected . '>' . $roleArray['description'] . '</option>';
                }
            }

            $html .= '</select>';
            $timeoff = (isset($employeesInfo["timeoff"]) ? $employeesInfo["timeoff"] : "");
            $totalworkedhours = (isset($employeesInfo["total_hours"]) ? $employeesInfo["total_hours"] : "");
            $html .= ' </td>
                <td style="width:12%;padding-left:30px;" class="personnel" data-id="' . $employeesInfo['id'] . '">' . $employeesInfo['fullname'] . '</td>
                        <td style="width:12%;" class="actualdate">
                                <div class="input-append row-fluid" style="min-width: 150px;">
                                        <div class="span12 row-fluid date">
                                                <input type="text" class="dateField" data-date-format="' . $user->get('date_format') . '" type="text" value="' . $actualDate . '" style="width:100px;" readonly/>
                                                <span class="add-on"><i class="icon-calendar"></i></span>
                                        </div>
                                </div>
                        </td>
                        <td class="actualstarthours" style="width:12%;"><input class="timepicker" type="text" value="' . $actualStartHours . '" style="width: 100px;"></td>
                        <td class="actualendhours" style="width:12%;"><input class="timepicker" type="text" value="' . $actualEndHours . '" style="width: 100px;"></td>
                        <td class="timeoff" style="width:12%;">
                        <input type="number" min="0" step="0.1"  value="'.$timeoff.'" style="width:100px; cursor: pointer;">
                        </td>
                        <td class="totalworkedhours" style="width:12%;">
                            <input type="number" min="0" step="0.1"  value="'.$totalworkedhours.'" style="width:100px; cursor: pointer;" readonly>
                        </td>
                        <td style="width:3%;"><a class="deleteEmployeeButton" data-employeeid="' . $employeesInfo['id'] . '" data-orderstaskid="' . $orderTaskID . '"><i title="Delete" class="icon-trash alignMiddle"></i></a></td>
                </tr>';
        }
	
	$html .= '</tbody></table><div style="margin-top:1%;"><input type="button" class="btn btn-default" id="addNewPersonnel" value="Add New Personnel"></div></div>';

	$msg = new Vtiger_Response();
	$msg->setResult($html);
	$msg->emit();
    }
 
    function getTaskEquipments(Vtiger_Request $request) {
		$orderTaskID = $request->get('task_id');
		$otRecordModel = Vtiger_Record_Model::getInstanceById($orderTaskID);
		$eqFields = $otRecordModel->getExtraBlockConfig();
		$eqFields = $eqFields["LBL_EQUIPMENT"]["fields"];
		$eqFieldValues = $otRecordModel->getExtraBlockFieldValues("LBL_EQUIPMENT_ACTUALS");
		if(count($eqFieldValues) < 1)
			$eqFieldValues = $otRecordModel->getExtraBlockFieldValues("LBL_EQUIPMENT");
	
		$colspan = (count($eqFields) * 2 )+1;
	
		$html = '<div class="row-fluid">
			<table  class="table table-bordered blockContainer showInlineTable dynamic_table" name="LBL_EQUIPMENT" >
			<thead>
				<tr>
					<th class="blockHeader" colspan="'.$colspan.'">'.vtranslate("LBL_EQUIPMENT", "OrdersTask").'</th>
				</tr>
			</thead>
			<tbody>            
				<tr class="fieldLabel">
					<td colspan="'.$colspan.'">
						<button type="button" class="addItem">+</button>
						<input type="hidden" name="numItem_LBL_EQUIPMENT" value="'.count($eqFieldValues).'"/>
						<button type="button" class="addItem" style="clear:right;float:right">+</button>
					</td>
				</tr>
				<tr class="defaultItem hide">
					<td class="fieldValue" style="width: 4%;">
						<i title="Delete" class="icon-trash removeItem"></i>
						<input type="hidden" class="default" name="itemId" value="none" />
					</td>';
		foreach ($eqFields as $field){
			$fieldModel = Vtiger_Field_Model::getInstance($field, Vtiger_Module_Model::getInstance("OrdersTask"));
			$viewer = new Vtiger_Viewer();
			$viewer->assign('FIELD_MODEL',$fieldModel);
			$viewer->assign('IS_BASE_FIELD',true);
			$fieldHTML = $viewer->view($fieldModel->getUITypeModel()->getTemplateName(),"OrdersTask",true);
			$mandatory = ($fieldModel->isMandatory()) ? '<span class="redColor">*</span>' : '';
			$html .= '<td class="fieldLabel" style="width: 10%;">
					<label class="muted">' . $mandatory .vtranslate($fieldModel->get('label'),"OrdersTask").'</label>
				</td>
				<td class="fieldValue" style="width:14%;">
					<div class="row-fluid">
						<span class="span10">'. $fieldHTML .'</span>
					</div>
				</td>';

		}
		$html.='</tr>';

		if (count($eqFieldValues) > 0) {
			$count = 1;
			foreach ($eqFieldValues as $eq) {
				$html .= '<tr class="itemRow" data-rowno = "'.$count.'">
					<td class="fieldValue" style="width: 4%;">
						<i title="Delete" class="icon-trash removeItem"></i>
						<input type="hidden" name="itemId_LBL_EQUIPMENT'.$count.'" value="'.$count.'" />
						<input type="hidden" name="itemDelete_LBL_EQUIPMENT'.$count.'" value="" />
					</td>';
					foreach ($eqFields as $field){
						$fieldModel = Vtiger_Field_Model::getInstance($field, Vtiger_Module_Model::getInstance("OrdersTask"));
						$fieldModel->set('fieldvalue',$eq[$field]);
						$viewer = new Vtiger_Viewer();
						$viewer->assign('FIELD_MODEL',$fieldModel);
						$fieldHTML = $viewer->view($fieldModel->getUITypeModel()->getTemplateName(),"OrdersTask",true);
						$mandatory = ($fieldModel->isMandatory()) ? '<span class="redColor">*</span>' : '';
						$html .= '<td class="fieldLabel" style="width: 10%;">
								<label class="muted">'. $mandatory .vtranslate($fieldModel->get('label'),"OrdersTask").'</label>
							</td>
							<td class="fieldValue" style="width:14%;">'.$fieldHTML.'</td>';
					}
				$html .= '</tr>';

				$count++;
			}
		}
	
		$msg = new Vtiger_Response();
		$msg->setResult($html);
		$msg->emit();
    }

    function getTaskCPUs(Vtiger_Request $request) {
		$orderTaskID = $request->get('task_id');
		$otRecordModel = Vtiger_Record_Model::getInstanceById($orderTaskID);
		$cpuFields = $otRecordModel->getExtraBlockConfig();
		$cpuFields = $cpuFields["LBL_CPU"]["fields"];
		$cpusFieldValues = $otRecordModel->getExtraBlockFieldValues("LBL_CPU_ACTUALS");
		if(count($cpusFieldValues) < 1)
			$cpusFieldValues = $otRecordModel->getExtraBlockFieldValues("LBL_CPU");
		
		$colspan = (count($cpuFields) * 2 )+1;
	
		$html = '<div class="row-fluid">
			<table  class="table table-bordered blockContainer showInlineTable dynamic_table" name="LBL_CPU" >
			<thead>
				<tr>
					<th class="blockHeader" colspan="'.$colspan.'">'.vtranslate("LBL_CPU", "OrdersTask").'</th>
				</tr>
			</thead>
			<tbody>            
				<tr class="fieldLabel">
					<td colspan="'.$colspan.'">
						<button type="button" class="addItem">+</button>
						<input type="hidden" name="numItem_LBL_CPU" value="'.count($cpusFieldValues).'"/>
						<button type="button" class="addItem" style="clear:right;float:right">+</button>
					</td>
				</tr>
				<tr class="defaultItem hide">
					<td class="fieldValue" style="width: 4%;">
						<i title="Delete" class="icon-trash removeItem"></i>
						<input type="hidden" class="default" name="itemId" value="none" />
					</td>';
		foreach ($cpuFields as $field){
			$fieldModel = Vtiger_Field_Model::getInstance($field, Vtiger_Module_Model::getInstance("OrdersTask"));
			$viewer = new Vtiger_Viewer();
			$viewer->assign('FIELD_MODEL',$fieldModel);
			$viewer->assign('IS_BASE_FIELD',true);
			$fieldHTML = $viewer->view($fieldModel->getUITypeModel()->getTemplateName(),"OrdersTask",true);
			$mandatory = ($fieldModel->isMandatory()) ? '<span class="redColor">*</span>' : '';
			$html .= '<td class="fieldLabel" style="width: 10%;">
					<label class="muted">'. $mandatory . vtranslate($fieldModel->get('label'),"OrdersTask").'</label>
				</td>
				<td class="fieldValue" style="'.(($fieldModel->getFieldName() == "carton_name") ? "width:17%;" : "width:13%;").'">
					<div class="row-fluid">
						<span class="span10">'. $fieldHTML .'</span>
					</div>
				</td>';

		}
		$html.='</tr>';

		if (count($cpusFieldValues) > 0) {
			$count = 1;
			foreach ($cpusFieldValues as $cpu) {
				$html .= '<tr class="itemRow" data-rowno = "'.$count.'">
					<td class="fieldValue" style="width: 4%;">
						<i title="Delete" class="icon-trash removeItem"></i>
						<input type="hidden" name="itemId_LBL_CPU'.$count.'" value="'.$count.'" />
						<input type="hidden" name="itemDelete_LBL_CPU'.$count.'" value="" />
					</td>';
					foreach ($cpuFields as $field){
						$fieldModel = Vtiger_Field_Model::getInstance($field, Vtiger_Module_Model::getInstance("OrdersTask"));
						$fieldModel->set('fieldvalue',$cpu[$field]);
						$viewer = new Vtiger_Viewer();
						$viewer->assign('FIELD_MODEL',$fieldModel);
						$fieldHTML = $viewer->view($fieldModel->getUITypeModel()->getTemplateName(),"OrdersTask",true);
						$mandatory = ($fieldModel->isMandatory()) ? '<span class="redColor">*</span>' : '';
						$html .= '<td class="fieldLabel" style="width: 10%;">
								<label class="muted">'. $mandatory .vtranslate($fieldModel->get('label'),"OrdersTask").'</label>
							</td>
							<td class="fieldValue" style="'. (($fieldModel->getFieldName() == "carton_name") ? "width:17%;" : "width:13%;").'">'.$fieldHTML.'</td>';
					}
				$html .= '</tr>';

				$count++;
			}
		}
	
		$msg = new Vtiger_Response();
		$msg->setResult($html);
		$msg->emit();
    }

    public function updateOrdersTaskActualsEditableValues(Vtiger_Request $request) {
	$taskID = $request->get('task_id');
	$fieldName = $request->get('fieldName');
	$fieldValue = $request->get('fieldValue');
	$currentUser = Users_Record_Model::getCurrentUserModel();

	$orderTask = [
	    'id' => vtws_getWebserviceEntityId('OrdersTask', $taskID),
		$fieldName => $fieldValue,
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
    
    function updateCrewOfOrdersTask($perform,$id,$task_id){
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $db = PearDatabase::getInstance();
        $result = $db->pquery("SELECT assigned_employee FROM vtiger_orderstask WHERE orderstaskid = ?", [$task_id]);
	    if ($db->num_rows($result) > 0) {
                $employees = $db->query_result($result, 'assigned_employee');
                $employeesArray = array_unique(explode(' |##| ', $employees));
                if($perform == 'delete'){
                    $key = array_search($id, $employeesArray);
                    while($key !== false){
                        unset($employeesArray[$key]);
                        $key = array_search($id, $employeesArray);
                    }
                }elseif($perform == 'insert'){
                    if(array_search($id, $employeesArray) === false){
                        $employeesArray[] = $id;
                    }
                }
                $employeesToSave = implode(' |##| ', $employeesArray);
		$orderTask = [
		    'id'                => vtws_getWebserviceEntityId('OrdersTask', $task_id),
		    'assigned_employee' => $employeesToSave
		];
		try {
		    vtws_revise($orderTask, $currentUser);

		    $res = ['result' => 'OK'];
		 } catch (Exception $exc) {
		    $res = ['result' => 'fail', 'msg' => $exc->message];
		 }
	    }
        return $res;
    }
}
