<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

 class Project_Record_Model extends Vtiger_Record_Model
 {

    /**
     * Function to get the summary information for module
     * @return <array> - values which need to be shown as summary
     */
    public function getSummaryInfo()
    {
        $adb = PearDatabase::getInstance();
        
        $query ='SELECT smownerid,enddate,projecttaskstatus,projecttaskpriority
				FROM vtiger_projecttask
						INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_projecttask.projecttaskid
							AND vtiger_crmentity.deleted=0
						WHERE vtiger_projecttask.projectid = ? ';

        $result = $adb->pquery($query, array($this->getId()));

        $tasksOpen = $taskCompleted = $taskDue = $taskDeferred = $numOfPeople = 0;
        $highTasks = $lowTasks = $normalTasks = $otherTasks = 0;
        $currentDate = date('Y-m-d');
        $inProgressStatus = array('Open', 'In Progress');
        $usersList = array();

        while ($row = $adb->fetchByAssoc($result)) {
            $projectTaskStatus = $row['projecttaskstatus'];
            switch ($projectTaskStatus) {

                case 'Open': $tasksOpen++;
                               break;

                case 'Deferred': $taskDeferred++;
                               break;

                case 'Completed': $taskCompleted++;
                                break;
            }
            $projectTaskPriority = $row['projecttaskpriority'];
            switch ($projectTaskPriority) {
                case 'high': $highTasks++;break;
                case 'low': $lowTasks++;break;
                case 'normal': $normalTasks++;break;
                default: $otherTasks++;break;
            }
            
            if (!empty($row['enddate']) && (strtotime($row['enddate']) < strtotime($currentDate)) &&
                    (in_array($row['projecttaskstatus'], $inProgressStatus))) {
                $taskDue++;
            }
            $usersList[] = $row['smownerid'];
        }
        
        $usersList = array_unique($usersList);
        $numOfPeople = count($usersList);

        $summaryInfo['projecttaskstatus'] =  array(
            'LBL_TASKS_OPEN' => $tasksOpen,
            'Progress' => $this->get('progress'),
            'LBL_TASKS_DUE' => $taskDue,
            'LBL_TASKS_COMPLETED' => $taskCompleted,
        );
        
        $summaryInfo['projecttaskpriority'] =  array(
            'LBL_TASKS_HIGH' => $highTasks,
            'LBL_TASKS_NORMAL' => $normalTasks,
            'LBL_TASKS_LOW' => $lowTasks,
            'LBL_TASKS_OTHER' => $otherTasks,
        );
        
        return $summaryInfo;
    }
    
     public function setParentRecordData(Vtiger_Record_Model $parentRecordModel)
     {
         $userModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
         $moduleName = $parentRecordModel->getModuleName();

         $data = array();
         $fieldMappingList = $parentRecordModel->getProjectMappingFields();

         foreach ($fieldMappingList as $fieldMapping) {
             $parentField = $fieldMapping['parentField'];
             $projectField = $fieldMapping['projectField'];
             $fieldModel = Vtiger_Field_Model::getInstance($parentField,  Vtiger_Module_Model::getInstance($moduleName));
             if ($fieldModel->getPermissions()) {
                 $data[$projectField] = $parentRecordModel->get($parentField);
             } else {
                 $data[$projectField] = $fieldMapping['defaultValue'];
             }
         }
         return $this->setData($data);
     }

     public function getMappingFields($forModuleName) {
         $res = [];
         if($forModuleName == 'Estimates' || $forModuleName == 'Actuals')
         {
             foreach ($this->getInventoryMappingFields() as $field)
             {
                 $res[$field['parentField']] = $field['inventoryField'];
             }
             return $res;
         }
         return $res;
     }

     public function getInventoryMappingFields()
     {
         return array(
                array('parentField'=>'linktoaccounts', 'inventoryField'=>'account_id', 'defaultValue'=>''),
                array('parentField'=>'contact_id', 'inventoryField'=>'contact_id', 'defaultValue'=>''),
                array('parentField'=>'business_line', 'inventoryField'=>'business_line', 'defaultValue'=>''),
                
                //Origin Address Fields
                array('parentField'=>'origin_address1', 'inventoryField'=>'origin_address1', 'defaultValue'=>''),
                array('parentField'=>'origin_address2', 'inventoryField'=>'origin_address2', 'defaultValue'=>''),
                array('parentField'=>'origin_city', 'inventoryField'=>'origin_city', 'defaultValue'=>''),
                array('parentField'=>'origin_state', 'inventoryField'=>'origin_state', 'defaultValue'=>''),
                array('parentField'=>'origin_zip', 'inventoryField'=>'origin_zip', 'defaultValue'=>''),
                array('parentField'=>'origin_country', 'inventoryField'=>'origin_country', 'defaultValue'=>''),
                array('parentField'=>'origin_phone1', 'inventoryField'=>'origin_phone1', 'defaultValue'=>''),
                array('parentField'=>'origin_phone2', 'inventoryField'=>'origin_phone2', 'defaultValue'=>''),
                array('parentField'=>'origin_description', 'inventoryField'=>'origin_description', 'defaultValue'=>''),
                array('parentField'=>'origin_flightsofstairs', 'inventoryField'=>'origin_flightsofstairs', 'defaultValue'=>''),

                //Destination Address Fields
                //array('parentField'=>'destination_address1', 'inventoryField'=>'destination_address1', 'defaultValue'=>''),
                //array('parentField'=>'destination_address2', 'inventoryField'=>'destination_address2', 'defaultValue'=>''),
                //array('parentField'=>'destination_city', 'inventoryField'=>'destination_city', 'defaultValue'=>''),
                //array('parentField'=>'destination_state', 'inventoryField'=>'destination_state', 'defaultValue'=>''),
                //array('parentField'=>'destination_zip', 'inventoryField'=>'destination_zip', 'defaultValue'=>''),
                //array('parentField'=>'destination_country', 'inventoryField'=>'destination_country', 'defaultValue'=>''),
                //array('parentField'=>'destination_phone1', 'inventoryField'=>'destination_phone1', 'defaultValue'=>''),
                //array('parentField'=>'destination_phone2', 'inventoryField'=>'destination_phone2', 'defaultValue'=>''),
                //array('parentField'=>'destination_description', 'inventoryField'=>'destination_description', 'defaultValue'=>''),
                //array('parentField'=>'destination_flightsofstairs', 'inventoryField'=>'destination_flightsofstairs', 'defaultValue'=>''),

                //Dates
                //array('parentField'=>'pack_date', 'inventoryField'=>'pack_date', 'defaultValue'=>''),
                //array('parentField'=>'pack_to_date', 'inventoryField'=>'pack_to_date', 'defaultValue'=>''),
                //array('parentField'=>'load_date', 'inventoryField'=>'load_date', 'defaultValue'=>''),
                //array('parentField'=>'load_to_date', 'inventoryField'=>'load_to_date', 'defaultValue'=>''),
                //array('parentField'=>'deliver_date', 'inventoryField'=>'deliver_date', 'defaultValue'=>''),
                //array('parentField'=>'deliver_to_date', 'inventoryField'=>'deliver_to_date', 'defaultValue'=>''),
                array('parentField'=>'survey_date', 'inventoryField'=>'survey_date', 'defaultValue'=>''),
                array('parentField'=>'survey_time', 'inventoryField'=>'survey_time', 'defaultValue'=>''),
                //array('parentField'=>'followup_date', 'inventoryField'=>'followup_date', 'defaultValue'=>''),
                //array('parentField'=>'decision_date', 'inventoryField'=>'decision_date', 'defaultValue'=>''),

                //Additional Info
                //array('parentField'=>'comm_res', 'inventoryField'=>'comm_res', 'defaultValue'=>''),
                //array('parentField'=>'include_packing', 'inventoryField'=>'include_packing', 'defaultValue'=>''),
                //array('parentField'=>'estimate_type', 'inventoryField'=>'estimate_type', 'defaultValue'=>''),
                //array('parentField'=>'pricing_type', 'inventoryField'=>'pricing_type', 'defaultValue'=>''),
                //array('parentField'=>'isconvertedfromlead', 'inventoryField'=>'isconvertedfromlead', 'defaultValue'=>'')
        );
     }
 }
