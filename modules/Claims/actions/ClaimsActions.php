<?php

include_once 'include/Webservices/Revise.php';
include_once 'include/Webservices/Delete.php';

class Claims_ClaimsActions_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        $mode = $request->get('mode');
        switch ($mode) {
            case 'saveMassiveSPR':
                $result = $this->saveMassiveSPR($request);
                break;
	    case 'loadOrderAgent':
                $result = $this->loadOrderAgent($request);
                break;
            default:
                $result = 'ERROR';
                break;
        }
        $msg = new Vtiger_Response();
        $msg->setResult($result);
        $msg->emit();
    }
    
    public function saveMassiveSPR(Vtiger_Request $request)
    {
	$db = PearDatabase::getInstance();
        $itemsList = $request->get('itemsList');
	$arrInfo = $request->get('infoArr');
        
        $db->pquery("DELETE FROM vtiger_claims_sprgrid WHERE rel_crmid IN (".implode(",", $itemsList).")", array());
        
        foreach ($arrInfo as $arr) {
            foreach ($itemsList as $item) {
                $amount = $db->pquery("SELECT sum(amount) as total FROM vtiger_claimitems_settlementamount WHERE claimitemsid = ?", array($item))->FetchRow();
                $responAmount = floatval($amount) * intval($arr[respon_percentage]);
                $db->pquery("INSERT INTO vtiger_claims_sprgrid (rel_crmid, agents_id, agent_name, vendors_id, vendors_name, respon_percentage, respon_amount) VALUES (?,?,?,?,?,?,?)", [$item, $arr[agentid], $arr[agentname], $arr[vendorid], $arr[vendorname], $arr[respon_percentage], $responAmount]);
            }
        }
        return "Ok";
    }
    
    public function loadOrderAgent(Vtiger_Request $request)
    {
	$result['result'] = 'NO-OK';
	
        if (isset($_REQUEST['claimssummary_id'])) {
	    $claimSummaryId = $request->get('claimssummary_id');
        } elseif (isset($_REQUEST['claimstype_id'])) {
	    $claimTypeId = $request->get('claimstype_id');
	    $claimTypeModel = Vtiger_Record_Model::getInstanceById($claimTypeId, 'Claims');
	    $claimSummaryId = $claimTypeModel->get('claimssummary_id');
        } else {
	    return $result;
	}
	
	$claimSummaryModel = Vtiger_Record_Model::getInstanceById($claimSummaryId, 'ClaimsSummary');
	$orderId = $claimSummaryModel->get('claimssummary_orderid');
	$participantAgentModel = Vtiger_Module_Model::getInstance('ParticipatingAgents');
	$orderParticipantAgents = $participantAgentModel->getParticipants($orderId);
	
	foreach ($orderParticipantAgents as $orderParticipantAgent) {
            if ($orderParticipantAgent['agent_type'] == $request->get('agent_type')) {
			$result['result'] = 'OK';
			$result['agent_id'] = $orderParticipantAgent['agents_id'];
			$result['agent_name'] = $orderParticipantAgent['agentName'];
	    }
	}
	
	return $result;
    }
}
