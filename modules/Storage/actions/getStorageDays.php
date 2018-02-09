<?php

class Storage_getStorageDays_Action extends Vtiger_BasicAjax_Action
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
        $storageId = $request->get('storage_type');
        $orderId = $request->get('orderId');
        $info = '';

        $result = $db->pquery('SELECT sit_dest_number_days, sit_origin_number_days FROM vtiger_quotes INNER JOIN vtiger_crmentity ON vtiger_quotes.quoteid=vtiger_crmentity.crmid WHERE deleted=0 AND 
	is_primary=? AND orders_id=? ', array(1, $orderId));

        $result = $db->pquery($sql, $params);
        if ($result && $db->num_rows($result) == 1) {
            switch ($storageId) {
        case 'Origin':
            $info['days'] = $db->query_result($result, 0, 'sit_origin_number_days');

            break;
        case 'Destination':
            $info['days'] = $db->query_result($result, 0, 'sit_dest_number_days');

            break;

        default:
            $info['days'] = 0;
            break;
        }
        } else {
            $info['days'] = 0;
        }


        $response = new Vtiger_Response();
        $response->setResult($info);
        $response->emit();
    }
}
