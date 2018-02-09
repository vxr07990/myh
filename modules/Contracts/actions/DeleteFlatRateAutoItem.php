<?php
class Contracts_DeleteFlatRateAutoItem_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }

    public function process(Vtiger_Request $request)
    {
        $db = PearDatabase::getInstance();
        $sql = "DELETE FROM `vtiger_contract_flat_rate_auto` WHERE line_item_id=?";

        $result = $db->pquery($sql, array($request->get('lineItemId')));
        $response = new Vtiger_Response();
        $response->setResult('1');
        $response->emit();
    }
}
