<?php
class Storage_PopulateAgentData_Action extends Vtiger_BasicAjax_Action
{

    // inherit from parent constructor
    public function __construct()
    {
        parent::__construct();
    }

    public function process(Vtiger_Request $request)
    {
        $info    = [];
        $agentId = $request->get('agent_id');

        try {
            if ($agentRecord = Vtiger_Record_Model::getInstanceById($agentId, 'Agents')) {
                $info['address1'] = $agentRecord->get('agent_address1');
                $info['city'] = $agentRecord->get('agent_city');
                $info['state'] = $agentRecord->get('agent_state');
                $info['zip'] = $agentRecord->get('agent_zip');
                $info['phone'] = $agentRecord->get('agent_phone');
            } else {
                throw new Exception('Error unable to retrieve agent information.');
            }
        } catch (Exception $ex) {
            throw new Exception('Error retrieving agent information.');
        }
        $response = new Vtiger_Response();
        $response->setResult($info);
        $response->emit();
    }
}
