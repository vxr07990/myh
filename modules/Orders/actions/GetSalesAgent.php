<?php

include_once 'modules/Users/Users.php';
include_once 'includes/main/WebUI.php';

class Orders_GetSalesAgent_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }

    public function process(Vtiger_Request $request)
    {
        try {
            $db           = PearDatabase::getInstance();
            $salesmanId   = $request->get('salesperson');
            file_put_contents('logs/devLog.log', "\n SID: $salesmanId", FILE_APPEND);
            $primaryAgent = Users_Record_Model::getInstanceById($salesmanId, 'Users')->getPrimaryOwnerForUser($salesmanId);
            file_put_contents('logs/devLog.log', "\n PRIME AGENT: $primaryAgent", FILE_APPEND);
            $sql          = "SELECT agentsid, agentname FROM `vtiger_agents` WHERE agentmanager_id = ?";
            $result       = $db->pquery($sql, [$primaryAgent]);
            $row = $result->fetchRow();
            $agentsId = $row['agentsid'];
            $agentName = $row['agentname'];
            $response     = new Vtiger_Response();
            $response->setResult(['id' => $agentsId, 'name' => $agentName]);
            $response->emit();
        } catch (Exception $e) {
            $response     = new Vtiger_Response();
            $response->setError($e->getMessage(), 'Please contact IGC support for assistance.');
            $response->emit();
        }
    }
}
