<?php

class WFOrders_Module_Model extends Vtiger_Module_Model {

    public function saveRecord($recordModel) {
        $request = new Vtiger_Request($_REQUEST);
        $recordModel = parent::saveRecord($recordModel);
        $request->set('record', $recordModel->get('id'));
        if($request->get('wforder_weight') && $request->get('wforder_weight') != $request->get('original_weight')) {
            WFWeightHistory_CreateHistory_Action::process($request);
        }
    }
}
