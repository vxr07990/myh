<?php
class Orders_GetBillingAddressDetails_Action extends Vtiger_BasicAjax_Action
{

    // inherit from parent constructor
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Setup the main function in which we process HTTP Requests
     * and emit data back to JavaScript to be processed on the DOM
     */
    public function process(Vtiger_Request $request)
    {
        // Instantiate pear db object
        $db = PearDatabase::getInstance();
        $invoice_id = $request->get('id');

        $sql = "SELECT * FROM `vtiger_accounts_billing_addresses` WHERE id = ? AND active = ?";
        $params = [$invoice_id, 'yes'];
        $result = $db->pquery($sql, $params);

        $row = $result->fetchRow();

        $info['address'] = [
            'commodity'         => $row['commodity'],
            'address1'          => $row['address1'],
            'address2'          => $row['address2'],
            'address_desc'      => $row['address_desc'],
            'city'              => $row['city'],
            'state'             => $row['state'],
            'zip'               => $row['zip'],
            'country'           => $row['country'],
            'company'           => $row['company'],
        ];

        $response = new Vtiger_Response();
        $response->setResult($info);
        $response->emit();
    }
}
