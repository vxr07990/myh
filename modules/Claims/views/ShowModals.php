<?php

class Claims_ShowModals_View extends Vtiger_IndexAjax_View
{
    public function __construct()
    {
        parent::__construct();
        $this->exposeMethod('showSPRespModal');
    }

    public function process(Vtiger_Request $request)
    {
        $mode = $request->get('mode');
        if (!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
    }

    public function showSPRespModal(Vtiger_Request $request)
    {
        $db = PearDatabase::getInstance();
        $claimID = $request->get('claimID');

        $claimsSummaryId = $db->pquery('SELECT claimssummary_id FROM vtiger_claims WHERE claimsid=?', [$claimID])->fetchRow()[0];
        $orderId = $db->pquery('SELECT claimssummary_orderid FROM vtiger_claimssummary WHERE claimssummaryid=?', [$claimsSummaryId])->fetchRow()[0];

        $employees = $this->getApplicableEmployees($orderId);
        $agents = $this->getApplicableParticipatingAgents($orderId);

        $viewer = $this->getViewer($request);
        $viewer->assign('MODULENAME', "Claims");
        $viewer->assign('PARTICIPANT_AGENTS', $agents);
        $viewer->assign('SERVICE_PROVIDERS', $employees);
        $viewer->assign('ITEMS_LIST', $request->get('itemList'));

        echo $viewer->view('sprModal.tpl', "Claims", true);
    }

    public function getApplicableEmployees($sourceRecord)
    {
        $db = PearDatabase::getInstance();
        $result = $db->pquery("SELECT DISTINCT service_provider FROM vtiger_moveroles WHERE moveroles_orders = ?", [$sourceRecord]);

        $employees = [];
        if ($result) {
            while ($row = & $result->fetchRow()) {
                $aux = Vtiger_Record_Model::getInstanceById($row[0]);
                $employees[] = array("id" => $aux->get('id'), "vendor" => $aux->get('vendorname'));
            }
        }

        return $employees;
    }

    public function getApplicableParticipatingAgents($sourceRecord)
    {
        $request = new Vtiger_Request($_REQUEST, $_REQUEST);
        $agentType = $request->get('agent_type');
        $storageLocation = $request->get('storage_location');
        if ($request->get('storage_orders') != '') {
            $sourceRecord = $request->get('storage_orders');
        }
        $db = PearDatabase::getInstance();
        $sql = "SELECT DISTINCT agents_id FROM `vtiger_participatingagents` WHERE rel_crmid = ? AND deleted=0";
        $params = [$sourceRecord];

        if ($agentType != '') {
            $sql .= ' AND agent_type=?';
            array_push($params, $agentType);
        } elseif ($storageLocation != '') {
            if ($storageLocation == 'Origin' || $storageLocation == 'Destination') {
                $storageLocation .= ' Agent';
                $sql .= ' AND agent_type=?';
                array_push($params, $storageLocation);
            }
        }


        $result = $db->pquery($sql, $params);

        $agents = [];
        if ($result) {
            while ($row = & $result->fetchRow()) {
                $aux = Vtiger_Record_Model::getInstanceById($row[0]);
                $agents[] = array("id" => $aux->get('id'), "agent" => $aux->get('agentname') . " (".$aux->get('agent_number').")");
            }
        }

        return $agents;
    }
}
