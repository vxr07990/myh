<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 1/11/2017
 * Time: 10:13 AM
 */

require_once('modules/Estimates/Estimates.php');

class Estimates_CheckLineItemsSave_Action extends Vtiger_BasicAjax_Action {

    public function process(Vtiger_Request $request) {
        $quoteid = $request->get('record');
        $fieldList = $request->getAll();
        $result = Estimates::saveDetailedLineItem($fieldList, $quoteid, true);
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }
}
