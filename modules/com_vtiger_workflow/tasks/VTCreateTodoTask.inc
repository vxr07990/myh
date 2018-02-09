<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once('include/Webservices/Utils.php');
require_once("include/Webservices/VtigerCRMObject.php");
require_once("include/Webservices/VtigerCRMObjectMeta.php");
require_once("include/Webservices/DataTransform.php");
require_once("include/Webservices/WebServiceError.php");
require_once 'include/Webservices/ModuleTypes.php';
require_once('include/Webservices/Create.php');
require_once 'include/Webservices/DescribeObject.php';
require_once 'include/Webservices/WebserviceField.php';
require_once 'include/Webservices/EntityMeta.php';
require_once 'include/Webservices/VtigerWebserviceObject.php';
require_once('modules/com_vtiger_workflow/VTSimpleTemplate.inc');

require_once("modules/Users/Users.php");

class VTCreateTodoTask extends VTTask{
	public $executeImmediately = true;

	public function getFieldNames(){return array('todo', 'description', 'sendNotification', 'time', 'date', 'status', 'priority', 'days', 'direction', 'datefield', 'sendNotification', 'assigned_user_id');}

	public function getContents($entity, $entityCache = false) {
		if (!$this->contents) {
			global $adb, $current_user;
			$taskContents = array();
			$entityId = $entity->getId();

			$utils = new VTWorkflowUtils();
			$adminUser = $utils->adminUser();
			if (!$entityCache) {
				$entityCache = new VTEntityCache($adminUser);
			}

			$st = new VTSimpleTemplate($this->description);
			$taskContents['description'] = $st->render($entityCache, $entityId);
			$this->contents = $taskContents;
			$utils->revertUser();
		}
		if(is_array($this->contents)) {
			$this->contents = Zend_Json::encode($this->contents);
		}
		return $this->contents;
	}

	function getAdmin(){
		$user = Users::getActiveAdminUser();
		global $current_user;
		$this->originalUser = $current_user;
		$current_user = $user;
		return $user;
	}

	public function doTask($entityData){
		if(!vtlib_isModuleActive('Calendar')) {
			return;
		}
		global $adb, $current_user;
		$userId = $entityData->get('assigned_user_id');
		if($userId===null){
			$userId = vtws_getWebserviceEntityId('Users', 1);
		}
		$parentModuleName = $entityData->moduleName;
		$baseDate = $entityData->get($this->datefield);
		if($baseDate == '') {
			$baseDate = date('Y-m-d');
		}
		$time = explode(' ',$baseDate);
		if(count($time) < 2) {
            $timeWithSec = Vtiger_Time_UIType::getTimeValueWithSeconds($this->time);
            $dbInsertDateTime = DateTimeField::convertToDBTimeZone($baseDate.' '.$timeWithSec);
            $time = $dbInsertDateTime->format('H:i:s');
		} else {
			$time = $time[1];
		}
		preg_match('/\d\d\d\d-\d\d-\d\d/', $baseDate, $match);
		$baseDate = strtotime($match[0]);
        $date = strftime('%Y-%m-%d', $baseDate+$this->days*24*60*60*(strtolower($this->direction)=='before'?-1:1));
		// Added to check if the user/group is active
		if(!empty($this->assigned_user_id)) {
			$userExists = $adb->pquery('SELECT 1 FROM vtiger_users WHERE id = ? AND status = ?', array($this->assigned_user_id, 'Active'));
			if($adb->num_rows($userExists)) {
				$assignedUserId = vtws_getWebserviceEntityId('Users', $this->assigned_user_id);
				$userId = $assignedUserId;
			} else {
				$groupExist = $adb->pquery('SELECT 1 FROM vtiger_groups WHERE groupid = ?', array($this->assigned_user_id));
				if($adb->num_rows($groupExist)) {
					$assignedGroupId = vtws_getWebserviceEntityId('Groups', $this->assigned_user_id);
					$userId = $assignedGroupId;
				}
                else{
					//collect move roles for order's module MoveRole->Employees ->Users based tasks
					$moveRoles = [];
					$db = PearDatabase::getInstance();
					$result = $db->pquery('SELECT moveroles_role FROM `vtiger_moveroles_role`', []);
					while($row =& $result->fetchRow()){
						$moveRoles[] = $row['moveroles_role'];
					}
					$assignedTo = $this->assigned_user_id;

                    if($assignedTo == 'copyParentOwner'){
						//built in special condition
                        $userId = $entityData->get('assigned_user_id');
                    }
					elseif(in_array($assignedTo , $moveRoles)
						   && $parentModuleName == 'Orders'
					       && getenv('IGC_MOVEHQ')
					){
						//order's move role special condition
						//link orderid->moverole->employees->userid
						// this doesn't work, will need to be fixed after there is actually a link between employees and users
						$recordId = explode('x', $entityData->get('id'))[1];
						$result = $db->pquery("SELECT related_user_id FROM `vtiger_orders` 
										  	  INNER JOIN `vtiger_moveroles` ON `vtiger_moveroles`.moveroles_orders = `vtiger_orders`.ordersid
										  	  INNER JOIN `vtiger_employees` ON `vtiger_moveroles`.moveroles_employees = `vtiger_employees`.employeesid 
										  	  WHERE `vtiger_moveroles`.moveroles_role = ? AND ordersid = ?", [$assignedTo, $recordId]);
						//paranoia in action
						if($result && method_exists($result, 'fetchRow')) {
							$moveRoleUserId = $result->fetchRow()['related_user_id'];
							$moveRoleUserId ? $userId = '19x' . $moveRoleUserId : $userId = '19x1';
						} else{
							$userId = '19x1';
						}
					}
                }
			}
		}
		$taskContents = Zend_Json::decode($this->getContents($entityData));

		$fields = array(
			'activitytype'=>'Task',
			'description'=>$taskContents['description'],
			'subject'=>$this->todo,
			'taskpriority'=>$this->priority,
			'taskstatus'=>$this->status,
			'assigned_user_id'=>$userId,
			'time_start'=> $time,
			'sendnotification'=>($this->sendNotification!='' && $this->sendNotification!='N')?
					true: false,
			'date_start'=>$date,
			'due_date'=>$date,
			'visibility'=>'Private',
			'eventstatus'=>''
		);

		$moduleName = $entityData->getModuleName();
		$adminUser = $this->getAdmin();
		$id = $entityData->getId();
		if($moduleName=='Contacts'){
			$fields['contact_id'] = $id;
		}else{
			$data = vtws_describe('Calendar', $adminUser);
			$fieldInfo = $data['fields'];
			foreach($fieldInfo as $field){
				if($field['name']=='parent_id'){
					$parentIdField = $field;
				}
			}
			$refersTo = $parentIdField['type']['refersTo'];

			if(in_array($moduleName, $refersTo)){
				$fields['parent_id'] = $id;
			}
		}

		$todo = vtws_create('Calendar', $fields, $adminUser);
		$todoIdDetails = vtws_getIdComponents($todo['id']);
		$entityIdDetails = vtws_getIdComponents($id);

		relateEntities(CRMEntity::getInstance($moduleName), $moduleName, $entityIdDetails[1], 'Calendar', $todoIdDetails[1]);

		global $current_user;
		$current_user = $this->originalUser;
	}

	static function conv12to24hour($timeStr){
		$arr = array();
		preg_match('/(\d{1,2}):(\d{1,2})(am|pm)/', $timeStr, $arr);
		if($arr[3]=='am'){
			$hours = ((int)$arr[1]) % 12;
		}else{
			$hours = ((int)$arr[1]) % 12 + 12;
		}
		return str_pad($hours, 2, '0', STR_PAD_LEFT).':'.str_pad($arr[2], 2, '0', STR_PAD_LEFT);
	}

	public function getTimeFieldList() {
		return array('time');
	}

}
?>
