<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 12/29/2016
 * Time: 10:57 AM
 */

class Estimates_GetAllowedTariffsForUser_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        $owner = $request->get('owner');

        $info = Estimates_Record_Model::getAllowedTariffsForUser($owner);

        $response = new Vtiger_Response();
        $response->setResult($info);
        $response->emit();
    }
}
