<?php
class MovePolicies_DeleteMiscTariffItem_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function process(Vtiger_Request $request)
    {
        $db = PearDatabase::getInstance();
        $sql = "DELETE FROM `vtiger_movepolicies_items` WHERE id=?";

        $result = $db->pquery($sql, array($request->get('lineItemId')));
        $response = new Vtiger_Response();
        $response->setResult('1');
        $response->emit();
    }
}
