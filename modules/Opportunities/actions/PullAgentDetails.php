<?php
class Opportunities_PullAgentDetails_Action extends Opportunities_PopulateAccountDetails_Action
{
    public function process(Vtiger_Request $request)
    {
        $record = Vtiger_Record_Model::getInstanceById($request->get('source'));
        //create a blank default return record so we don't error out.
        $returnRecord = Vtiger_Record_Model::getCleanInstance('Agents');
        $db = PearDatabase::getInstance();
        
        
        //OK we need to get the agency_code to match in the Agents module,
        //because the agentmanagerid MAY not be set.
        $self_haul_agentmanagerID = $record->get('self_haul_agentmanagerid');
        if (!$self_haul_agentmanagerID) {
            //no self_haul_agentmanagerid is set on the owner record so use itself as the self hauler.
            $self_haul_agentmanagerID = $record->getId();
        }
        //pull the agent manager record of the self hauler
        $selfHaulRecord = Vtiger_Record_Model::getInstanceById($self_haul_agentmanagerID);
        
        //find the Agent module record based on the agent_number (agency_code).
        $stmt = 'SELECT * FROM `vtiger_agents` WHERE `agent_number` = ? LIMIT 1';
        $result = $db->pquery($stmt, [$selfHaulRecord->get('agency_code')]);
        if ($result) {
            $row = $result->fetchRow();
            //set the found agent module record (overwriting the clean record)
            $returnRecord = Vtiger_Record_Model::getInstanceById($row['agentsid']);
        }


        $res = new stdClass();
        $res->record_id = $returnRecord->getId();
        $res->agentname = $returnRecord->get('agentname');
        // Emit data back to jQuery/JavaScript
        $response = new Vtiger_Response();
        $response->setResult($res);
        $response->emit();
    }
}
