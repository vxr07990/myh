<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

include_once 'include/Webservices/Create.php';
include_once 'include/Webservices/Revise.php';
include_once 'include/Webservices/Delete.php';

class OrdersTask_CapacityCalendarActions_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        try {
            switch ($request->get('mode')) {
                case 'movetask':
                    $this->moveTaskDate($request);
                    break;
                case 'udpatestatus':
                    $this->udpateTaskStatus($request);
                    break;
                case 'getResourceDropdownOptions':
                    $this->getResourceDropdownOptions($request);
                    break;
				case 'saveHolidayBlocked':
                    $this->saveHolidayBlocked($request);
                    break;
				case 'saveDailyNotes':
                    $this->saveDailyNotes($request);
                default:
                    break;
            }
        } catch (Exception $ex) {
            $msg = new Vtiger_Response();
            $msg->setResult($ex->getMessage());
            $msg->emit();
        }
    }
	
	public function saveDailyNotes($request){
		$dnArr = $request->get('dailyNotes');
		$current_user = Users_Record_Model::getCurrentUserModel();
		
        foreach($dnArr as $arr){
            if (!$arr['dailynotesId']) {
                continue;
            }
            $deleted = $arr['dailynotesDelete'];
            $dailynotesId = $arr['dailynotesId'];
			$owner = $arr['dailynotesOwner'];
			$note = $arr['dailynotesNote'];
			
            if ($deleted == 'deleted') {
				$wsid = vtws_getWebserviceEntityId('DailyNotes', $dailynotesId);
                vtws_delete($wsid, $current_user);
            } else {
				$dailyNotesArr = array(
					'agentid' => $owner,
					'dailynotes_note' => $note,
					'assigned_user_id' => vtws_getWebserviceEntityId('Users', $current_user->id),
				);
				
				if (Users_Privileges_Model::isPermitted('DailyNotes', 'Save')){
					if ($dailynotesId != 'none'){
						$dailyNotesArr['id'] = vtws_getWebserviceEntityId('DailyNotes', $dailynotesId);
						vtws_revise($dailyNotesArr, $current_user);
					} else {
						$dailyNotesArr['dailynotes_date'] = DateTimeField::convertToDBFormat($request->get('dailynotes_date'));
						vtws_create("DailyNotes", $dailyNotesArr, $current_user);


					}
				}else{
					throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
				}
            }
        }

        $msg = new Vtiger_Response();
        $msg->setResult('OK');
        $msg->emit();
    }
    
    public function saveHolidayBlocked($request){
		$hbArr = $request->get('holidayblocked');
		$current_user = Users_Record_Model::getCurrentUserModel();
		
        foreach($hbArr as $arr){
            if (!$arr['holidayblockedId']) {
                continue;
            }
            $deleted = $arr['holidayblockedDelete'];
            $holidayId = $arr['holidayblockedId'];
			$bussinesLine = implode(' |##| ',$arr['holidayblockedBussinesLine']);
			$owner = $arr['holidayblockedOwner'];
			$type = $arr['holidayblockedType'];
			
            if ($deleted == 'deleted') {
				$wsid = vtws_getWebserviceEntityId('Holiday', $holidayId);
                vtws_delete($wsid, $current_user);
            } else {
				$holidayArr = array(
					'holiday_type' => $type,
					'agentid' => $owner,
					'holiday_business_line' => $bussinesLine,
					'assigned_user_id' => vtws_getWebserviceEntityId('Users', $current_user->id),
				);
				
				if (Users_Privileges_Model::isPermitted('Holiday', 'Save')){
					if ($holidayId != 'none'){
						$holidayArr['id'] = vtws_getWebserviceEntityId('Holiday', $holidayId);
						vtws_revise($holidayArr, $current_user);
					} else {
						$holidayArr['holiday_date'] = DateTimeField::convertToDBFormat($request->get('holiday_date'));
						vtws_create("Holiday", $holidayArr, $current_user);
					}
				}else{
					throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
				}
            }
        }

        $msg = new Vtiger_Response();
        $msg->setResult('OK');
        $msg->emit();
    }
	
    /**
     * Deprecated? Need confirmation that we are not allowing to move the task
     * 
     * @param Vtiger_Request $request
     */
    public function moveTaskDate(Vtiger_Request $request)
    {
        $taskId = $request->get('task_id');
        $newDate = $request->get('new_date');

        $taskRecodModel = Vtiger_Record_Model::getInstanceById($taskId, 'OrdersTask');
        $taskRecodModel->set('mode', 'edit');
        $taskRecodModel->set('disp_assigneddate', $newDate);

        $taskRecodModel->save();

        $result = array(
            'result' => 'OK',
        );

        $msg = new Vtiger_Response();
        $msg->setResult($result);
        $msg->emit();
    }

    /**
     * Deprecated? Need confirmation that we are not allowing to update the status of the task
     * 
     * @param Vtiger_Request $request
     */
    public function udpateTaskStatus(Vtiger_Request $request)
    {
        $taskId = $request->get('task_id');
        $newStatus = $request->get('status');

        $taskRecodModel = Vtiger_Record_Model::getInstanceById($taskId, 'OrdersTask');
        $taskRecodModel->set('mode', 'edit');
        $previousMode = $taskRecodModel->get('dispatch_status');


        if ($newStatus == 'Accepted') {
            $serviceDateFrom = $taskRecodModel->get('service_date_from');
            $assignedDate = $taskRecodModel->get('disp_assigneddate');
            
            $assignedDate = ($assignedDate != '' ? $assignedDate : $serviceDateFrom);
            
            $taskRecodModel->set('disp_assigneddate', $assignedDate);
        }

        $taskRecodModel->set('dispatch_status', $newStatus);

        $taskRecodModel->save();

        $result = array(
            'result' => 'OK',
            'old_status' => $previousMode
        );

        $msg = new Vtiger_Response();
        $msg->setResult($result);
        $msg->emit();
    }
    
    public function getResourceDropdownOptions(Vtiger_Request $request)
    {
        $user =  Users_Record_Model::getCurrentUserModel();
        $accesibleAgents = $user->getBothAccessibleOwnersIdsForUser();

        $resource = $request->get('resource');
		$cvid = $request->get('cvid');
				
		$orderTaskModel = Vtiger_Module_Model::getInstance('OrdersTask');
		$agentBlineCondition = $orderTaskModel->getCVIDWHERE($cvid);	
		
		if(count($agentBlineCondition)){
			$agentCondition = $agentBlineCondition["agents"];
			if(count($agentCondition)){
				$accesibleAgents = $agentCondition;
			}
		}
		
        $options = '';
        if($resource == 'employees'){
            $db = PearDatabase::getInstance();
            $resourcePicklist = array();
            $sql = "SELECT er.* FROM vtiger_employeeroles er INNER JOIN vtiger_crmentity cr ON er.employeerolesid = cr.crmid 
					    WHERE er.emprole_class_type = 'Operations' AND cr.deleted = 0 AND cr.agentid IN (" . generateQuestionMarks($accesibleAgents) . ")";
            $result = $db->pquery($sql,array($accesibleAgents));
            if ($db->num_rows($result) > 0) {
                while ($row = $db->fetch_row($result)) {
                    $resourcePicklist[$row['employeerolesid']] = $row['emprole_desc'];
                }
            }
            $options .= '<option value="all">Any Personnel Type</option>';
            
            foreach ($resourcePicklist as $id => $value) {
                $options .= '<option value="' . $id . '">' . vtranslate($value, 'OrdersTask') . '</option>';
            }
        }else{

            $fieldModel = Vtiger_Field_Model::getInstance('vehicle_type', Vtiger_Module_Model::getInstance('Vehicles'));

            $resourcePicklist =  $fieldModel->getPicklistValues();
            $options .= '<option value="all">Any Vehicle Type</option>';
            
            foreach ($resourcePicklist as $id => $value) {
                $options .= '<option value="' . $value . '">' . vtranslate($value, 'OrdersTask') . '</option>';
            }
        }

        $result = array(
            'result' => 'OK',
            'options' => $options
        );
        
        $msg = new Vtiger_Response();
        $msg->setResult($result);
        $msg->emit();
    }
}
