<?php
class TariffServices_LocalTabledValuationLookup_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        $serviceid = $request->get('serviceid');
        $deductible = $request->get('deductible');
        $amount = $request->get('amount');
        
        
        $db = PearDatabase::getInstance();
        
        $sql = "SELECT cost FROM `vtiger_tariffvaluations` WHERE deductible=? and amount=? and serviceid=?";
        
        $result = $db->pquery($sql, array($deductible, $amount, $serviceid));
        
        $info = array();
        while ($row =& $result->fetchRow()) {
            $info['rate'] = $row[0];
        }
        if (!isset($info['rate'])) {
            $info['rate'] = 0;
        }
        
        $response = new Vtiger_Response();
        $response->setResult($info);
        $response->emit();
    }
}
