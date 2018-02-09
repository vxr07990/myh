<?php
class TariffServices_LocalWeightMileLookup_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        $serviceid = $request->get('serviceid');
        $weight = $request->get('weight');
        $miles = $request->get('miles');

        $db = PearDatabase::getInstance();

        $sql = "SELECT base_rate FROM `vtiger_tariffweightmileage` WHERE ? >= from_miles AND ? <= to_miles and ? >= from_weight and ? <= to_weight and serviceid=?";

        $result = $db->pquery($sql, array($miles, $miles, $weight, $weight, $serviceid));
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
