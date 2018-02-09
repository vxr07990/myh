<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class OrdersTask_BasicAjax_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        $searchValue = $request->get('search_value');
        $searchModule = $request->get('search_module');

        $parentRecordId = $request->get('parent_id');
        $parentModuleName = $request->get('parent_module');
        $relatedModule = $request->get('module');

        $searchModuleModel = Vtiger_Module_Model::getInstance($searchModule);
        if($searchModule == 'CapacityCalendarCounter'){
            $participatingAgent = ($request->get('participatingAgent')?$request->get('participatingAgent'):'');
            $arrayCapacityCalendarCodes = $searchModuleModel->getAvailableCalendarCodes($participatingAgent,$searchValue);
            foreach ($arrayCapacityCalendarCodes as $data){
                $records[$searchModule][$data] = Vtiger_Record_Model::getInstanceById($data);
            }
        }elseif($searchModule == 'Employees'){
            $sourceRecord = $request->get('recordId');
            $ordersTaksRecord = Vtiger_Record_Model::getInstanceById($sourceRecord, 'OrdersTask');
            $assignedDate = $ordersTaksRecord->get('disp_assigneddate');
            $ordersTaksInstance = Vtiger_Module_Model::getInstance('OrdersTask');
            $roleId = $request->get('roleId') ? $request->get('roleId') : '' ;
            $employees = $ordersTaksInstance->getEmployeesByDateAndRole($assignedDate, $assignedDate, $roleId, '', 'employeePopup' );
            $employees = $employees[$assignedDate];
            $searchValue = strtolower($searchValue);
            foreach ($employees as $id => $recordModel) {
                if( ! strpos(strtolower($recordModel->getName()), $searchValue)){
                    unset($employees[$id]);
                }
            }
            $records[$searchModule] = $employees;
        }else{
            $records = $searchModuleModel->searchRecord($searchValue, $parentRecordId, $parentModuleName, $relatedModule);
        }
        
        $result = array();
        if (is_array($records)) {
            foreach ($records as $moduleName=>$recordModels) {
                foreach ($recordModels as $recordModel) {
                    
                        $result[] = array(
                            'label'=>decode_html($recordModel->getName()),
                            'value'=>decode_html($recordModel->getName()),
                            'id'=>$recordModel->getId()
                        );
                }
            }
        }

        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }
}
