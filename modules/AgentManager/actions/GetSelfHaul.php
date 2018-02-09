<?php
class AgentManager_GetSelfHaul_Action extends Vtiger_BasicAjax_Action
{

    //Function to get sirva self haul based on agentmanager
    public function process(Vtiger_Request $request)
    {
        $record = Vtiger_Record_Model::getInstanceById($request->get('source'));
        $current_user = Users_Record_Model::getCurrentUserModel();

        $isReadonly = !$current_user->isAgencyAdmin();

        // Had logic tied to it, turns out should always show. Leaving this here in case that changes.
        $isVisible = true;

        //Default to no
        $self_haul = false;
        //Make sure this is actually an existing record
        if ($record->getModule()) {
            //If this is an agentmanager record, get the self haul
            if ($record->getModuleName() == 'AgentManager') {
                $self_haul = intval($record->get('self_haul')) == 1;
            }
        }

        $response = new Vtiger_Response();
        $response->setResult(['readonly' => $isReadonly, 'value' => $self_haul, 'visible' => $isVisible]);
        $response->emit();
    }
}
