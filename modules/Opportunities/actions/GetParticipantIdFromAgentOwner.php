<?php

class Opportunities_GetParticipantIdFromAgentOwner_Action extends Vtiger_BasicAjax_Action {

    public function __construct() {
        parent::__construct();
    }

    public function process(Vtiger_Request $request) {
        $agentManagerId = $request->get('agentmanagerid');
        $info = $this->getInfo($agentManagerId);

        $response = new Vtiger_Response();
        if ($info) {
            $response->setResult($info);
        } else {
            $response->setError('00','no info');
        }
        $response->emit();
    }

    public static function getInfo($id) {
        $info = [];
        if($id){
            $db = &PearDatabase::getInstance();
            $sql = "SELECT agentsid, agentname, agent_number FROM `vtiger_agents` JOIN `vtiger_crmentity` ON `vtiger_agents`.agentsid=`vtiger_crmentity`.crmid WHERE deleted=0 AND agentmanager_id = ? LIMIT 1";
            $result = $db->pquery($sql, [$id]);
            if($result){
                while($row = $result->fetchrow()){
                    $agentId = $row['agentsid'];
                    $agentName = $row['agentname']." (".$row['agent_number'].")";
                    $info = [
                        'agentid'=>$agentId,
                        'agentName'=>$agentName
                    ];
                }
            }
        }

        return $info;
    }
}
