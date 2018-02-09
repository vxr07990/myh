<?php

class Opportunities_GetUsersAgentSettings_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }

    public function process(Vtiger_Request $request)
    {
        $data = [];
        $agents = Users_Record_Model::getCurrentUserModel()->getAccessibleAgentsForUser();
        if ($agents) {
            $agents = array_reverse($agents);
            foreach ($agents as $key => $val) {
                $data['agency_name'] = $val;
                $data['agency_id']   = $key;
            }
        }

        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }
}
