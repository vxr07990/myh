<?php
class TariffServices_LocalValuationLookup_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        $serviceid = $request->get('serviceid');
        $deductible = $request->get('deductible');
        
        $db = PearDatabase::getInstance();
        
        $sql = "SELECT rate FROM `vtiger_tariffchargeperhundred` WHERE deductible=? and serviceid=?";

        $result = $db->pquery($sql, array($deductible, $serviceid));
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
