<?php
class Opportunities_PopulateAccountDetails_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }

    public function process(Vtiger_Request $request)
    {
        $record = Vtiger_Record_Model::getInstanceById($request->get('source'));

        // Emit data back to jQuery/JavaScript
        $response = new Vtiger_Response();
        $response->setResult($record);
        $response->emit();
    }
}
