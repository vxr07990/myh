<?php
class TariffServices_LocalCWTbyWeightLookup_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        $serviceid = $request->get('serviceid');
        $weight = $request->get('weight');
        
        $db = PearDatabase::getInstance();
        
        $sql = "SELECT rate FROM `vtiger_tariffcwtbyweight` WHERE ? >= from_weight and ? <= to_weight and serviceid=?";

        $result = $db->pquery($sql, array($weight, $weight, $serviceid));
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
