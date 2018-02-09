<?php
class Contracts_PopulateAccountDetails_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }

    public function process(Vtiger_Request $request)
    {

        // Instantiate pear db object
        $db = PearDatabase::getInstance();

        // clear memory allocation for $params completely
        $params = '';
        unset($params);

        // now declare
        $params = array();
        // Initial query for contact details
        $sql = "SELECT * FROM vtiger_account
              INNER JOIN vtiger_accountbillads
              ON vtiger_accountbillads.accountaddressid = vtiger_account.accountid
              WHERE vtiger_account.accountid = ?";

        // Initial request var from the the DOM upon which query is established
        $params[] = $request->get('account_id');


        // pair up $params and the SQL statement in the pquery object call
        $result = $db->pquery($sql, $params);

        // $opportunity
        unset($params);

        $row = $result->fetchRow();

        $info = array();

        // Origin info
        $info['address']['bill_city'] = $row['bill_city'];
        $info['address']['bill_zip'] = $row['bill_code'];
        $info['address']['bill_country'] = $row['bill_country'];
        $info['address']['bill_state'] = $row['bill_state'];
        $info['address']['bill_street'] = $row['bill_street'];
        $info['address']['bill_pobox'] = $row['bill_pobox'];
        $info['address']['phone'] = $row['phone'];

        $flag = 0;

        // Emit data back to jQuery/JavaScript
        $response = new Vtiger_Response();
        $response->setResult($info);
        $response->emit();
    }
}
