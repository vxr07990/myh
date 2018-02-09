<?php
use MoveCrm\Models\User;

class WFWeightHistory_CreateHistory_Action extends Vtiger_ActionAjax_Action
{
    public function process(Vtiger_Request $request){
        $history = Vtiger_Record_Model::getCleanInstance('WFWeightHistory');
        $relOrder = Vtiger_Record_Model::getInstanceById($request->get('record'),'WFOrders');
        $history->set('wforder_id',$relOrder->get('id'));
        $history->set('user',User::current()->id);
        $history->set('agentid', $relOrder->get('agentid'));
        $history->set('wfweighthistory_weight',$relOrder->get('wforder_weight'));
        $history->set('weight_date', $relOrder->get('weight_date'));
        $history->save();
    }
}
