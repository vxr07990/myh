<?php

class Accounts_LastCreditCheck_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        try {
            $accountId = $request->get('accountId');
            $account['creditCheckDone'] = Accounts_Record_Model::creditCheckDone($accountId);
        
            $response = new Vtiger_Response();
            $response->setResult($account);
            $response->emit();
        } catch (Exception $e) {
            $response     = new Vtiger_Response();
            $response->setError($e->getMessage(), 'Please contact IGC support for assistance.');
            $response->emit();
        }
    }
}
