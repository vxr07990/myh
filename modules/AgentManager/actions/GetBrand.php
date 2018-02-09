<?php

class AgentManager_GetBrand_Action extends Vtiger_BasicAjax_Action
{
    
    //Function to get sirva brand based either on vanline or agent record ids
    public function process(Vtiger_Request $request)
    {
        $brand = self::retrieve($request->get('agent_vanline_id'));
        $response = new Vtiger_Response();
        $response->setResult($brand);
        $response->emit();
    }

    public static function retrieve($agentId)
    {
        $record = Vtiger_Record_Model::getInstanceById($agentId);
        $brand = true;

        //Make sure this is actually an existing record
        if ($record->getModule()) {
            //If this is an agentmanager record, get the vanline record
            if ($record->getModuleName() == 'AgentManager') {
                $record = Vtiger_Record_Model::getInstanceById($record->get('vanline_id'));
            } elseif ($record->getModuleName() != 'VanlineManager') {
                $brand = false;
            }
        } else {
            $brand = false;
        }

        if ($brand) {
            $brand = $record->get('vanline_id') == 1 ? 'AVL' : 'NVL';
        }
        return $brand;
    }
}
