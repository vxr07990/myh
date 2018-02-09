<?php
class Contracts_DeleteFuelTable_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function process(Vtiger_Request $request)
    {
        $db = PearDatabase::getInstance();
        file_put_contents('logs/devLog.log', print_r($request->get('lineItemId'), true), FILE_APPEND);
        $sql = "DELETE FROM `vtiger_contractfuel` WHERE line_item_id=?";

        $result = $db->pquery($sql, array($request->get('lineItemId')));
        $response = new Vtiger_Response();
        $response->setResult('1');
        $response->emit();
    }
}
