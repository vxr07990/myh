<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 9/21/2016
 * Time: 4:20 PM
 */

require_once('modules/Estimates/Estimates.php');

class Estimates_SaveDetailLineItems_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        $fieldList = $request->getAll();
        $quoteid = $request->get('record');

        Estimates::saveDetailedLineItem($fieldList, $quoteid);

        $response = new Vtiger_Response();
        $response->setResult(true);
        $response->emit();
    }
}
