<?php

class Estimates_LoadTariffSIT_Action extends Vtiger_BasicAjax_Action
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
        $sql = "SELECT * FROM
              	(SELECT cartage_cwt_rate FROM `vtiger_tariffservices`
              		WHERE effective_date = ?
              		AND related_tariff = ?
              		AND rate_type = 'SIT Cartage' ) AS a
              join
              	(SELECT cwt_rate AS first_day_rate FROM `vtiger_tariffservices`
              		WHERE effective_date = ?
              		AND related_tariff = ?
              		AND rate_type = 'SIT First Day Rate' ) AS b
              join
              	(SELECT cwtperday_rate AS additional_day_rate FROM `vtiger_tariffservices`
              		WHERE effective_date = ?
              		AND related_tariff = ?
              		AND rate_type = 'SIT Additional Day Rate' ) AS c";

        $result = $db->pquery($sql, [$effectiveDateId, $tariffId,$effectiveDateId, $tariffId,$effectiveDateId, $tariffId]);
        $row = $result->fetchRow();
        $sitItems = ['sit_cartage'=>0,'sit_addl_day'=>0,'sit_first_day'=>0];
        if (!empty($row)) {
            $sitItems['sit_cartage']    = $row['cartage_cwt_rate'];
            $sitItems['sit_addl_day']   = $row['additional_day_rate'];
            $sitItems['sit_first_day']  = $row['first_day_rate'];
        }
        $info = ['sitItems'=>$sitItems];
        $response = new Vtiger_Response();
        $response->setResult($info);
        $response->emit();
    }
}
