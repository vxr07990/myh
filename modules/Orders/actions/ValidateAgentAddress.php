<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 1/10/2017
 * Time: 4:57 PM
 */

class Orders_ValidateAgentAddress_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $request) {
        $db = &PearDatabase::getInstance();
        $id = $request->get('id');
        $result = [];
        if($id)
        {
            $res = $db->pquery('SELECT agentsid, agentname, agent_number, agent_address1, agent_city, agent_state, agent_zip, agent_country FROM vtiger_agents WHERE agentsid IN(?)', [$id]);
            while ($row = $res->fetchRow()) {
                if (!$row['agent_address1']
                    || !$row['agent_city']
                    || !$row['agent_state']
                    || !$row['agent_zip']
                    || !$row['agent_country']
                ) {
                    $result[] = [
                        'id' => $row['agentsid'],
                        'name' => $row['agentname'] . ' (' . $row['agent_number'] . ')',
                    ];
                }
            }
        }

        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }
}

