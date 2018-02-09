<?php

class AgentManager_GetCoordinators_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        $instanceName = getenv('INSTANCE_NAME');
        if ($instanceName != 'sirva') {
            $response = new Vtiger_Response();
            $response->setError('999', "Action unavailable");
            $response->emit();
            return;
        }
        $arrToReturn = self::getCoordinators($request->get('userid'), $request->get('agentmanagerid'));

        $response = new Vtiger_Response();
        $response->setResult($arrToReturn);
        $response->emit();
    }

    public static function getCoordinators($userId, $agentManagerId) {
        if($userId) {
            $currentUser = Users_Record_Model::getInstanceById($userId, 'Users');
        }else{
            $currentUser = Users_Record_Model::getCurrentUserModel();
        }

        $agentManagerRecord = Vtiger_Record_Model::getInstanceById($agentManagerId, 'AgentManager');

        $vanlineRecord = Vtiger_Record_Model::getInstanceById($agentManagerRecord->get('vanline_id'), 'VanlineManager');

        $isAVL = $vanlineRecord->get('vanline_id') == 1;

        $coordinators = $agentManagerRecord->getCoordinators();

        $default = $currentUser->getDefaultCoordinator($isAVL);

        return ['coordinators' => $coordinators, 'default' => $default];
    }
}
