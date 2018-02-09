<?php
class Storage_GetBillingType_Action extends Vtiger_BasicAjax_Action
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
        $storageId = $request->get('storageId');
        $orderId = $request->get('orderId');
        $info = '';
        
        if (!empty($storageId)) {
            $sql = "SELECT billing_type FROM vtiger_orders JOIN vtiger_storage ON ordersid=storage_orders WHERE storageid = ?";
            $params = [$storageId];
        } else {
            $sql = "SELECT billing_type FROM vtiger_orders WHERE ordersid= ?";
            $params = [$orderId];
        }

        $result = $db->pquery($sql, $params);
        if ($result && $db->num_rows($result) == 1) {
            $info['tariff_type'] = $result->fetchRow()[0];
        }
        

        $response = new Vtiger_Response();
        $response->setResult($info);
        $response->emit();
    }
}
