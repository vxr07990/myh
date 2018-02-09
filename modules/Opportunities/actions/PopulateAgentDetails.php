<?php
class Opportunities_PopulateAgentDetails_Action extends Opportunities_PopulateAccountDetails_Action
{
    public function process(Vtiger_Request $request)
    {
        $record = Vtiger_Record_Model::getInstanceById($request->get('source'));
        $current_user = Users_Record_Model::getCurrentUserModel();

        $emitData = [];
        // Sirva specific logic to cut down on AJAX calls.
        if(getenv('INSTANCE_NAME') == 'sirva') {
            $type = $request->get('source_type');
            if($type == 'manager') {
                $agent = $request->get('source');
            }else if($type == 'roster') {
                $agent = Agents_Record_Model::getInstanceById($request->get('source'))->get('agentmanager_id');
            }else {
                $agent = false;
            }
            $user = $request->get('userid');
            if(!empty($request->get('record'))) {
                $opp = Opportunities_Record_Model::getInstanceById($request->get('record'), 'Opportunities');
            }else {
                $opp = Opportunities_Record_Model::getCleanInstance('Opportunities');
            }

            $emitData['people'] = [
                'coordinators' => AgentManager_GetCoordinators_Action::getCoordinators($user, $agent),
                'salespeople'=> $opp->getSalesPeopleByOwner($agent)
            ];
        }

        // Emit data back to jQuery/JavaScript
        $response = new Vtiger_Response();
        $response->setResult($emitData);
        $response->emit();
    }
}
