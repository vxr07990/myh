<?php
class TariffServices_LocalCountyLookup_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        $serviceid = $request->get('serviceid');
        $county = $request->get('county');
        
        $db = PearDatabase::getInstance();
        
        $sql = "SELECT rate FROM `vtiger_tariffcountycharge` WHERE name=? and serviceid=?";

        $result = $db->pquery($sql, array($county, $serviceid));
        $info = array();
        while ($row =& $result->fetchRow()) {
            $info['rate'] = $row[0];
        }
        if (empty($info['rate'])) {
            $info['rate'] = 0;
        }
        $response = new Vtiger_Response();
        $response->setResult($info);
        $response->emit();
    }
}
