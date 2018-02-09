<?php
class OrdersTask_GetReferencePicklist_Action extends Vtiger_Action_Controller
{
    public function checkPermission(Vtiger_Request $request)
    {
        return;
    }
    public function process(Vtiger_Request $request)
    {
        $db = PearDatabase::getInstance();

        $params = ['Operations'];

        $participatingAgent = $request->get('participating_agent');
        $agentRecodModel = Vtiger_Record_Model::getInstanceById($participatingAgent, 'Agents');
        $agentManagerRecordModel =  Vtiger_Record_Model::getInstanceById($agentRecodModel->get("agentmanager_id"), 'AgentManager');
        array_push($params, $agentRecodModel->get("agentmanager_id"), $agentManagerRecordModel->get('vanline_id'));

        //get the employee roles for dropdown based on Participating Agent selected in the Order Task
        $arrayRoles = array();
        $result = $db->pquery("SELECT vtiger_employeeroles.* 
                                FROM vtiger_employeeroles 
                                INNER JOIN vtiger_crmentity ON vtiger_employeeroles.employeerolesid = vtiger_crmentity.crmid
                                WHERE deleted = 0 AND emprole_class_type=? AND  (vtiger_crmentity.agentid = ? OR vtiger_crmentity.agentid = ?) ", $params);
        
	while ($row = $db->fetch_row($result)) {
            $arrayRoles[$row['employeerolesid']] = $row['emprole_desc'];
        }
        
        $response = new Vtiger_Response();
        $response->setResult($arrayRoles);
        $response->emit();
    }
}
