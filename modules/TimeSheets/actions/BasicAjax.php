<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class TimeSheets_BasicAjax_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        $searchValue =  $request->get('search_value');
        $searchModule = $request->get('search_module');

        $parentRecordId = $request->get('parent_id');
        $parentModuleName = $request->get('parent_module');
        $relatedModule = $request->get('module');

        if ($searchModule == 'EmployeeRoles') {
            $searchValue = '%' . $searchValue  . '%';
            
            $user =  Users_Record_Model::getCurrentUserModel();
            $accesibleAgents = $user->getBothAccessibleOwnersIdsForUser();

            $db = PearDatabase::getInstance();
            $result = array();
            $sql = "SELECT er.* FROM vtiger_employeeroles er INNER JOIN vtiger_crmentity cr ON er.employeerolesid = cr.crmid 
                                            WHERE cr.deleted = 0 AND cr.agentid IN (" . generateQuestionMarks($accesibleAgents) . ")";
            $sql .= " AND emprole_class_type = 'Operations' AND emprole_desc LIKE ?";
            $resultQuery = $db->pquery($sql,array($accesibleAgents,$searchValue));
            if ($db->num_rows($resultQuery) > 0) {
                while ($row = $db->fetch_row($resultQuery)) {
                    $result[] = array(
                                    'label'=>decode_html($row['emprole_desc']),
                                    'value'=>decode_html($row['emprole_desc']),
                                    'id'=>$row['employeerolesid']
 				);
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
}
