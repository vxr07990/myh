<?php
class Estimates_UpdateEffectiveTariff_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }

    public function process(Vtiger_Request $request)
    {
        $agentid = $request->get('agentid');
        $userAgents = [$agentid=>''];
        //@TODO: sanitize inputs?
        $recordModel = Vtiger_Record_Model::getCleanInstance('Estimates');

        $tariff_results = $recordModel->getCurrentUserTariffs(false, $userAgents);
        $final_results = [];

        foreach ($tariff_results as $tariff_index => $tariff_name) {
            $final_results[$tariff_name->get('id')] = [
                                                        'name' => $tariff_name->get('tariffmanagername'),
                                                        'intra' => ($tariff_name->get('tariff_type') == 'Intrastate') ? 'intraInterstate' : ''
                                                      ];
        }

        $maxSql = 'SELECT tariffsid, tariff_name FROM `vtiger_tariffs`
                        JOIN `vtiger_crmentity` ON (`vtiger_tariffs`.`tariffsid` = `vtiger_crmentity`.`crmid`)
                        WHERE `vtiger_crmentity`.`deleted`=0
                        AND (tariff_type LIKE ? OR tariff_type LIKE ?)';
        //this was erroring out becuase it didnt have $db, this is a stop-gap to make sure estimates in trunk don't explode
        $db = PearDatabase::getInstance();
        $result = $db->pquery($maxSql, ['Max%3', 'Max%4']);
        $row = $result->fetchRow();
        while ($row != null) {
            $currentMax = Tariffs_Record_Model::getInstanceById($row[0]);
            //$currentMax->intrastate = true;
            $final_results[$currentMax->get('id')] = [
                                                        'name' => $currentMax->get('tariff_name'),
                                                        'intra' => 'intraLocal'
                                                     ];
            $row = $result->fetchRow();
        }

        $response = new Vtiger_Response();
        if (empty($final_results)) {
            //if there's an error don't do say it's fine.
            //@todo requires some more thought because if no estimateid for local don't do query but return OK
            //$response->setResult('0');
            $response->setResult(array('error'=>true));
        } else {
            $response->setResult($final_results);
        }
        $response->emit();
    }
}
