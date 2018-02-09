<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Trips_BasicAjax_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        $searchValue =  $request->get('search_value');
        $searchModule = $request->get('search_module');

        $parentRecordId = $request->get('parent_id');
        $parentModuleName = $request->get('parent_module');
        $relatedModule = $request->get('module');

        if ($searchModule == 'Employees') {
            $result = [];
            $agentId = $request->get('agentId');
            $firstLoadDate = DateTimeField::convertToDBFormat($request->get('date'));
            $ordersTaksInstance = Vtiger_Module_Model::getInstance('OrdersTask');
            $roleId = Vtiger_Module_Model::getInstance('EmployeeRoles')->getRoleIdFromRoleClassAndAgentId('Driver',$agentId);
            if($roleId && $firstLoadDate != ''){
                $availableEmployees = $ordersTaksInstance->getEmployeesByDateAndRole($firstLoadDate, $firstLoadDate, $roleId, '', 'employeePopup' );
                $availableEmployees = $availableEmployees[$firstLoadDate];
                $onNoticeEmployees = $ordersTaksInstance->getOutOfServiceEmployeesByDate($firstLoadDate, $firstLoadDate, 'On Notice');
                $onNoticeEmployees = $onNoticeEmployees[$firstLoadDate];
                foreach ($availableEmployees as $id => $model) {
                    $name = $model->get('name') . ' ' . $model->get('employee_lastname');
                    if(strpos(strtolower($name), strtolower($searchValue)) === false){
                        continue;
                    }
                    if(in_array($id, $onNoticeEmployees)){
                        $employee['label'] = $name . ' (On Notice)';
                        $employee['value'] = $name . ' (On Notice)';
                    } else {
                        $employee['label'] = $name;
                        $employee['value'] = $name;
                    }

                    
                    $employee['id'] = $model->get('id');

                    $result[] = $employee;
                }
            }
        } elseif ($searchModule == 'Vehicles') {
            $sourceField = $request->get('source_field');
            
            $result = [];
            $firstLoadDate = DateTimeField::convertToDBFormat($request->get('date'));
            $type = $sourceField == 'trips_trailer' ? 'Trailer' : 'Truck';
            if($firstLoadDate != '' && $type != ''){
                $carbCompliant = $this->checkOrdersCA($sourceRecord);
                $ordersTaksInstance = Vtiger_Module_Model::getInstance('OrdersTask');
                $vehicles = $ordersTaksInstance->getVehiclesByDateAndType($firstLoadDate, $firstLoadDate, '', $type, '', [], [], 'vehiclePopup', $carbCompliant)[$firstLoadDate];
                $onNotice = $ordersTaksInstance->getOutOfServiceVehiclesByDate($firstLoadDate, $firstLoadDate, array_keys($vehicles), 'On Notice')[$firstLoadDate]; 
                foreach ($vehicles as $id => $model) {
                    $name = $model->get('vechiles_unit');
                    if(strpos(strtolower($name), strtolower($searchValue)) === false){
                        continue;
                    }
                    if(in_array($id, $onNoticeEmployees)){
                        $record['label'] = $name . ' (On Notice)';
                        $record['value'] = $name . ' (On Notice)';
                    } else {
                        $record['label'] = $name;
                        $record['value'] = $name;
                    }

                    
                    $record['id'] = $model->get('id');

                    $result[] = $record;
                }
            }
        } else {
            $searchModuleModel = Vtiger_Module_Model::getInstance($searchModule);
            $records = $searchModuleModel->searchRecord($searchValue, $parentRecordId, $parentModuleName, $relatedModule);

            $result = array();
            if (is_array($records)) {
                foreach ($records as $moduleName=>$recordModels) {
                    foreach ($recordModels as $recordModel) {
                        $result[] = array('label'=>decode_html($recordModel->getName()), 'value'=>decode_html($recordModel->getName()), 'id'=>$recordModel->getId());
                    }
                }
            }
        }
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }
    
    public function checkOrdersCA($trip_id)
    {
        $db = PearDatabase::getInstance();
        $sql = "SELECT crel.* FROM vtiger_crmentityrel crel
                INNER JOIN vtiger_crmentity cr1 ON crel.crmid = cr1.crmid
                WHERE module = 'Trips' AND relmodule = 'Orders'
                AND cr1.deleted = 0
                AND crel.crmid = ?";

        $result = $db->pquery($sql, [$trip_id]);
        if ($result && $db->num_rows($result) > 0) {
            while ($row = $db->fetch_row($result)) {
                $orderID = ($row[module] == 'Orders') ? $row[crmid] : $row[relcrmid];
                $orderEntity = Vtiger_Record_Model::getInstanceById($orderID, "Orders");

                if ($orderEntity->get('origin_state') == 'CA' || $orderEntity->get('destination_state') == 'CA') {
                    return true;
                }
            }
        }

        return false;
    }
}
