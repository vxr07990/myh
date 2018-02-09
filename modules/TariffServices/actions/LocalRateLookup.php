<?php
class TariffServices_LocalRateLookup_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        $serviceid = $request->get('serviceid');
        $weight = $request->get('weight');
        $miles = $request->get('miles');
        
        $db = PearDatabase::getInstance();
        
        $sql = "SELECT base_rate, excess FROM `vtiger_tariffbaseplus` WHERE ? >= from_miles AND ? <= to_miles and ? >= from_weight and ? <= to_weight and serviceid=?";

        $result = $db->pquery($sql, array($miles, $miles, $weight, $weight, $serviceid));
        
        
        $info = array();
        while ($row =& $result->fetchRow()) {
            $info['rate'] = $row[0];
            $info['excess'] = $row[1];
        }
        if (!isset($info['rate'])) {
            $info['rate'] = 0;
        }
        if (!isset($info['excess'])) {
            $info['excess'] = 0;
        }
        $response = new Vtiger_Response();
        $response->setResult($info);
        $response->emit();
    }
}
