<?php

class Accounts_CheckCreditHold_Action extends Vtiger_BasicAjax_Action {

    public function process(Vtiger_Request $request) {
        try {
            $accountId = $request->get('accountId');
            $amount = $request->get('amount');
            $account['isOnHold'] = Accounts_Record_Model::checkCreditHold($accountId, $amount);

            $response = new Vtiger_Response();
            $response->setResult($account);
            $response->emit();
        } catch (Exception $e) {
            $response = new Vtiger_Response();
            $response->setError($e->getMessage(), 'Please contact IGC support for assistance.');
            $response->emit();
        }
    }

}
