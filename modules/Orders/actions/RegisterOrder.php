<?php

include_once 'modules/Users/Users.php';
include_once 'includes/main/WebUI.php';

class Orders_RegisterOrder_Action extends Vtiger_BasicAjax_Action
{
    // This gets set in validateRegistrationValues() with the estimates id that meets all the requirements
    private $estimateOrderId;

    public function __construct()
    {
        parent::__construct();
    }

    public function process(Vtiger_Request $request)
    {
        try {
            $record_id = $request->get('ordersid');

            // Cant use the below code because it wont return registered on because its a hidden field
            //$orderDetails = Vtiger_DetailView_Model::getInstance('Orders', $record_id)->getRecord();
            //But this one does though!
            $orderRecordModel = Vtiger_Record_Model::getInstance($record_id, 'Orders');

            //@TODO: Add fail error for missing order record model, oh or it's covered in the catch.

            //taking this out I think above is better.
            //$orderDetails = $this->getOrderDetails($record_id);
            $order['record_id']             = $record_id;
            $order['business_line']         = $orderRecordModel->get('business_line');
            $order['origin_city']           = $orderRecordModel->get('origin_city');
            $order['origin_state']          = $orderRecordModel->get('origin_state');
            $order['origin_zip']            = $orderRecordModel->get('origin_zip');
            $order['destination_city']      = $orderRecordModel->get('destination_city');
            $order['destination_state']     = $orderRecordModel->get('destination_state');
            $order['destination_zip']       = $orderRecordModel->get('destination_zip');
            $order['orders_load_date']      = $orderRecordModel->get('orders_ldate');
            $order['orders_delivery_date']  = $orderRecordModel->get('orders_ddate');
            $order['orders_elinehaul']      = $orderRecordModel->get('orders_elinehaul');
            $order['registered_on']         = $orderRecordModel->get('registered_on');
            $order['orders_elinehaul']      = $orderRecordModel->get('orders_elinehaul');

            $data['estimates'] = $this->getEstimateDetails($record_id);
            $data['order'] = $order;
            //$data['agents'] = $this->getParticipatingAgents($record_id);
            //we should get this from the module itself not our own thing.
            $participatingAgentsModel = Vtiger_Module_Model::getInstance('ParticipatingAgents');
            if ($participatingAgentsModel && $participatingAgentsModel->isActive()) {
                $data['agents'] = $participatingAgentsModel::getParticipants($record_id);
            }

            $errors = $this->validateRegistrationValues($data);
            if (is_array($errors)) {
                $response     = new Vtiger_Response();
                $response->setError('error', $errors);
                $response->emit();
            } else {
                $isRegistered = $this->orderRegistration($data);
                if ($isRegistered) {
                    $response     = new Vtiger_Response();
                    $response->setResult($record_id);
                    $response->emit();
                } else {
                    $response     = new Vtiger_Response();
                    $response->setError('error', 'Failed to register order, try again');
                    $response->emit();
                }
            }
        } catch (Exception $e) {
            $response     = new Vtiger_Response();
            $response->setError($e->getMessage(), 'Please contact IGC support for assistance.');
            $response->emit();
        }
    }

    /*
     * Returns false if no errors, or an array of errors
     */
    private function validateRegistrationValues($data)
    {
        $errors = [];
        if ($data['order']['registered_on']>0) {
            $errors[] = 'Order has already been registered';
            return $errors;
        }

        // Other required fields
        if (empty($data['order']['origin_state'])) {
            $errors[] = 'Origin state required in the order';
        }
        if (empty($data['order']['destination_state'])) {
            $errors[] = 'Destination state required in the order';
        }
        if (empty($data['order']['origin_city'])) {
            $errors[] = 'Origin city required in the order';
        }
        if (empty($data['order']['origin_zip'])) {
            $errors[] = 'Origin zip required in the order';
        }
        if (empty($data['order']['destination_city'])) {
            $errors[] = 'Destination city required in the order';
        }
        if (empty($data['order']['destination_zip'])) {
            $errors[] = 'Destination Zip Required in the order';
        }
        if (empty($data['order']['orders_load_date'])) {
            $errors[] = 'Load date required in the order';
        }
        if (empty($data['order']['orders_delivery_date'])) {
            $errors[] = 'Delivery date required in the order';
        }
        if (empty($data['order']['orders_elinehaul'])) {
            $errors[] = 'Linehaul amount is required in the order';
        }

        //Business Line = HHG - Interstate or HHG Intrastate and Origin and Destination state = TX.
        //OK there's two readings of this condition: I took it to mean: if (interstate || (intrastate && org =tx && dest = tx))
        //this interpretation is (Business Line = HHG - Interstate or HHG Intrastate) and Origin = TX and Destination state = TX
        //so I asked KT and no response I'm leaving this in if my interpretation is incorrect

//        // Check business line for required values
//        if($data['order']['business_line'] != 'Interstate Move' && $data['order']['business_line'] != 'Intrastate Move') {
//            $errors[] = 'Business line must be HHG - Interstate or HHG Intrastate';
//        }
//
//        if(
//            (strtoupper($data['order']['origin_state']) != 'TX' &&
//                strtoupper($data['order']['origin_state']) != 'TEXAS')
//            || (strtoupper($data['order']['destination_state']) != 'TX' &&
//                strtoupper($data['order']['destination_state']) != 'TEXAS')) {
//            $errors[] = 'Origin state and destination state must be TX '.$data['order']['origin_state'].' = '.$data['order']['destination_state'];
//        }

        if ($data['order']['business_line'] == 'Intrastate Move') {
            if (
                (strtoupper($data['order']['origin_state']) != 'TX' &&
                strtoupper($data['order']['origin_state']) != 'TEXAS')
                || (strtoupper($data['order']['destination_state']) != 'TX' &&
                strtoupper($data['order']['destination_state']) != 'TEXAS')
            ) {
                $errors[] = 'Origin and Destination State must both be TX if the Business line is HHG Intrastate';
            }
        } elseif ($data['order']['business_line'] != 'Interstate Move') {
            $errors[] = 'Business line must be HHG - Interstate or HHG Intrastate';
        }

        // Order must have Booking Agent, Origin Agent, Destination Agent,
        // Invoicing Agent, Carrier, Survey Agent and a Sales Org.
        $requiredAgentTypes = ['Booking Agent', 'Origin Agent', 'Destination Agent', 'Invoicing Agent', 'Carrier', 'Survey Agent', 'Sales Org'];
        $setAgentTypes = [];
        foreach ($data['agents'] as $val) {
            $setAgentTypes[] = $val;
        }
        $missing = array_diff($requiredAgentTypes, $setAgentTypes);
        if (count($missing)>0) {
            foreach ($missing as $val) {
                $errors[] = 'The order is missing a '.$val;
            }
        }

        //There must be a primary and accepted estimate or an estimate with a a pre-reg
        // estimate type.  (The linehaul from either of these should update the Estimated Linehaul field in the Order Details block.) Valuation Block must be filled out except for additional valuation.
        $requiredMet = false;
        $estimateId = '';
        foreach ($data['estimates'] as $estimate) {
            $primary = $estimate['is_primary'];
            $stage = $estimate['quotestage'];
            $type = $estimate['estimate_type'];
            $deductible = $estimate['valuation_deductible'];
            $amount = $estimate['amount'];

            if (
                ($estimate['is_primary'] == 1 && $estimate['quotestage'] == 'Accepted')
                || $estimate['estimate_type'] == 'Pre-Reg'
            ) {
                if ($estimate['valuation_deductible'] && $estimate['valuation_amount']>0) {
                    $requiredMet = true;
                    $this->estimateOrderId = $estimate['quoteid'];
                    break;
                }
            }
        }

        if ($requiredMet === false) {
            // Not sure how to word this monstrosity of an error
            $errors = 'An order must have an estimate attached that is marked as primary and accepted, or the estimate type must be marked as Pre-Reg. Also the estimate must have valuation options';
        }

        // Check if errors exists and if so return them
        if ($errors) {
            return $errors;
            exit;
        }
        return true;
    }

    /*
     * Returns boolean value
     */
    private function orderRegistration($data)
    {
        //The linehaul from either of these should update the Estimated Linehaul field in the Order Details block.
        // orders_elinehaul, registered_on,  -> estimates linehaul_disc
        $db = PearDatabase::getInstance();
        $timestamp = date('Y-m-d');

        // TODO: Need to add line haul from the estimate see ot item for details = 1974
        //@NOTE: line haul should be coming from the rated estimate or already be on the order from the estimate save process.
        $sql = 'UPDATE `vtiger_orders` SET registered_on = ?  WHERE ordersid = ?';
        $result = $db->pquery($sql, [$timestamp, $data['order']['record_id']]);
        return true;
    }

    private function getOrderDetails($record_id)
    {
        $db = PearDatabase::getInstance();

        $sql = 'SELECT * FROM `vtiger_orders` WHERE ordersid = ?';
        $result = $db->pquery($sql, [$record_id]);
        $data = $result->fetchRow();
        return $data;
    }

    private function getEstimateDetails($record_id)
    {
        $db = PearDatabase::getInstance();
        $sql = 'SELECT is_primary, quotestage, estimate_type, valuation_deductible, valuation_amount, free_valuation_limit, valuation_flat_charge, free_valuation_type FROM `vtiger_quotes` WHERE orders_id = ?';
        $result = $db->pquery($sql, [$record_id]);
        $data = [];
        while ($row = $result->fetchRow()) {
            $data[] = $row;
        }

        return $data;
    }

    private function getParticipatingAgents($record_id)
    {
        $db = PearDatabase::getInstance();
        $sql = 'SELECT agent_type FROM `vtiger_participatingagents` WHERE rel_crmid = ? AND deleted=0';
        $result = $db->pquery($sql, [$record_id]);
        //Booking Agent, Origin Agent, Destination Agent, Invoicing Agent, Carrier, Survey Agent, Sales Org.
        $data = [];
        while ($row = $result->fetchRow()) {
            $data[] = $row['agent_type'];
        }

        return $data;
    }
}
