<?php

class Estimates_LoadTariffPacking_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function process(Vtiger_Request $request)
    {
        $db = PearDatabase::getInstance();
        $tariffId = $request->get('tariffId');
        $effectiveDate = $request->get('effectiveDate');
        //select the effectivedateid for the effective date that has most recently passed
        $sql = "SELECT effectivedatesid FROM `vtiger_effectivedates` 
				WHERE effective_date <= ?
				AND related_tariff = ?
				ORDER BY `vtiger_effectivedates`.`effective_date` DESC
				LIMIT 1";
        $result = $db->pquery($sql, [$effectiveDate, $tariffId]);
        $row = $result->fetchRow();
        $effectiveDateId = $row[0];
        //select the serviceid for the oldest applicable packing item
        $sql = "SELECT tariffservicesid FROM `vtiger_tariffservices` 
				WHERE effective_date = ? 
				AND related_Tariff = ? 
				AND rate_type = 'Packing Items'
				ORDER BY `vtiger_tariffservices`.`tariffservicesid` ASC
				LIMIT 1";
        $result = $db->pquery($sql, [$effectiveDateId, $tariffId]);
        $row = $result->fetchRow();
        $serviceId = $row[0];
        $sql = "SELECT packing_rate, pack_item_id FROM `vtiger_tariffpackingitems` WHERE serviceid=? AND pack_item_id != 0";
        $result = $db->pquery($sql, [$serviceId]);
        $packingItems = [];
        while ($row =& $result->fetchRow()) {
            $packingItems[$row['pack_item_id']] = $row['packing_rate'];
        }
        $info = ['packingItems'=>$packingItems];
        $response = new Vtiger_Response();
        $response->setResult($info);
        $response->emit();
    }
}
