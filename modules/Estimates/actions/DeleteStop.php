<?php

class Estimates_DeleteStop_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function process(Vtiger_Request $request)
    {
        //OLD STOPS
        /*$db = PearDatabase::getInstance();
        $record = $request->get('record');
        $id = $request->get('stopid');
        $sql = "UPDATE `vtiger_extrastops` SET stop_estimate = NULL WHERE stop_estimate = ? AND stopid = ?";
        $result = $db->pquery($sql, array($record, $id));
        $response = new Vtiger_Response();
        $response->setResult(true);
        $response->emit();*/
    }
}
