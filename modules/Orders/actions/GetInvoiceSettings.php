<?php
class Orders_GetInvoiceSettings_Action extends Vtiger_BasicAjax_Action
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

        $sql = "SELECT * FROM `vtiger_account_invoicesettings` WHERE id = ?";
        $params = [$invoice_id];
        $result = $db->pquery($sql, $params);

        $row = $result->fetchRow();

        $info = [
            'commodity'         => $row['commodity'],
            'invoice_template'  => $row['invoice_template'],
            'invoice_packet'    => $row['invoice_packet'],
            'document_format'   => $row['document_format'],
            'invoice_delivery'  => $row['invoice_delivery'],
            'finance_charge'    => $row['finance_charge'],
            'payment_terms'     => $row['payment_terms'],
        ];

        $response = new Vtiger_Response();
        $response->setResult($info);
        $response->emit();
    }
}
