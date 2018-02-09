<?php

class Vendors_GetICode_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        try {
            $vendorId = $request->get('vendorId');
            $icode = Vtiger_Record_Model::getInstanceById($vendorId)->getDisplayValue('icode');
            $vendor['icode'] = $icode;
        
            $response = new Vtiger_Response();
            $response->setResult($vendor);
            $response->emit();
        } catch (Exception $e) {
            $response     = new Vtiger_Response();
            $response->setError($e->getMessage(), 'Please contact IGC support for assistance.');
            $response->emit();
        }
    }
}
