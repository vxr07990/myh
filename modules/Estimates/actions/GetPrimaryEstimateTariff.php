<?php

class Estimates_GetPrimaryEstimateTariff_Action extends Vtiger_BasicAjax_Action
{

    //Function to get sirva brand based either on vanline or agent record ids
    public function process(Vtiger_Request $request)
    {
        $db = $db ?: PearDatabase::getInstance();

        $opportunity_id = $request->get('opportunity_id');
        if ($opportunity_id != '') {
            //Have to do an SQL call here, because effective tariff is not a true field
            $tariff = $db->getOne("SELECT `effective_tariff` FROM `vtiger_quotes` where `potentialid` = ${opportunity_id} AND `is_primary` = '1'");
        }

        $tariffModel = Vtiger_Record_Model::getInstanceById($tariff);
        if ($tariff != '') {
            $tariffName = $tariffModel->get('custom_tariff_type');
        }
        if($tariffName == '') {
            $tariffName = $tariffModel->get('tariff_type');
        }

        $response = new Vtiger_Response();
        $response->setResult($tariffName);
        $response->emit();
    }
}
