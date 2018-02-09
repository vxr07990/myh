<?php
/*******************************************************************************
 * @file			PopulateAccountData.php
 * @author			Louis Robinson
 * @company			IGC Software
 * @contact			lrobinson@igcsoftware.com
 * @description		1. Recieves a Vtiger_Request object and builds a query for an
 *						account id.
 *					2. Takes the Vtiger_Request object again for the account
 *					   id and queries for matching customer data for the object
 *					3. Returns the customer and shipping data to the browser via
 *  					jQuery and appends it to the DOM
 *               Is this accurate? Doesn't it return a json which is
 * 			 	processed by jQuery which in turn updates the DOM
 *
 * @NOTE adapted from populateOppData.php in Estimates.
 *******************************************************************************/
class Orders_PopulateAccountData_Action extends Vtiger_BasicAjax_Action
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

        // clear memory allocation for $params completely
        if (isset($params)) {
            unset($params);
        }

        $business_line = '%' . $request->get('business_line') . '%';

        // now declare
        $params = [];
        $info = [];
        if (getenv('IGC_MOVEHQ')) {

            //-------------- Grab Credit Details ---------------------
            $sql = 'SELECT `vtiger_account`.credit_limit, `vtiger_account`.credit_hold, `vtiger_account`.account_balance, `vtiger_account`.credit_hold_override, `vtiger_account`.business_line, `vtiger_account`.po_required FROM `vtiger_account`
 WHERE `vtiger_account`.accountid = ?';
            $params = [$request->get('accountid'), 'yes'];
            $result = $db->pquery($sql, $params);
            unset($params);

            $row = $result->fetchRow();

            $info['po_required'] = $row['po_required'];
            $info['credit']['credit_limit'] = $row[0];
            $info['credit']['credit_hold'] = $row[1];
            $info['credit']['account_balance'] = $row[2];
            $info['credit']['credit_hold_override'] = $row[3];
            foreach (explode(' |##| ', $row['business_line']) as $line) {
                $info['available_business_lines'][$line] = vtranslate($line, 'Accounts');
            }
            unset($row);
            //-------------- Grab billing addresses -----------------
            $result = $db->pquery('SELECT * FROM `vtiger_accounts_billing_addresses` WHERE account_id = ? AND active = ? AND commodity LIKE ?', [$request->get('accountid'), 'yes', $business_line]);
            $data = [];
            $descData = [];
            while ($row =& $result->fetchRow()) {
                $com = explode(' |##| ', $row['commodity']);
                foreach ($com as &$item) {
                    $item = vtranslate($item, 'Accounts');
                }
                $data[] = [
                    'id'            => $row['id'],
                    'commodity'    => implode(', ', $com),
                    'address1'        => $row['address1'],
                    'address2'        => $row['address2'],
                    'address_desc'    => $row['address_desc'],
                    'company'    => $row['company'],
                    'city'            => $row['city'],
                    'state'        => $row['state'],
                    'zip'            => $row['zip'],
                    'country'        => $row['country'],
                ];
                $descData[$row['id']] = $row['address_desc'];
            }
            $info['addresses'] = $data;
            $info['avail_addr_desc'] = $descData;

            //--------------  Grab invoice settings ------------------
            $result = $db->pquery('SELECT * FROM `vtiger_account_invoicesettings` WHERE record_id = ? AND commodity LIKE ?', [$request->get('accountid'), $business_line]);
            $data = [];
            while ($r =& $result->fetchRow()) {
                $com = explode(' |##| ', $r['commodity']);
                foreach ($com as &$item) {
                    $item = vtranslate($item, 'Accounts');
                }
                $data[] = [
                    'id' => $r['id'],
                    'commodity' => implode(', ', $com),
                    'invoice_template' => $r['invoice_template'],
                    'invoice_packet' => $r['invoice_packet'],
                    'document_format' => $r['document_format'],
                    'invoice_delivery' => $r['invoice_delivery'],
                    'finance_charge' => $r['finance_charge'],
                    'payment_terms' => $r['payment_terms'],
                ];
            }

            $info['invoice'] = $data;

            file_put_contents('logs/myLog.log', print_r($info, true));
        } else {
            $sql = 'SELECT bill_city, bill_street, bill_country, bill_state, bill_code, bill_pobox 
                FROM vtiger_accountbillads 
                WHERE accountaddressid = ?';

            $params[] = $request->get('accountid');
            $result = $db->pquery($sql, $params);
            unset($params);

            $row = $result->fetchRow();

            if ($row != null) {
                $info['billing']['city'] = $row[0];
                $info['billing']['street'] = $row[1];
                $info['billing']['country'] = $row[2];
                $info['billing']['state'] = $row[3];
                $info['billing']['zip'] = $row[4];
                $info['billing']['pobox'] = $row[5];
            } else {
                $info['billing']['city'] = '';
                $info['billing']['street'] = '';
                $info['billing']['country'] = '';
                $info['billing']['state'] = '';
                $info['billing']['zip'] = '';
                $info['billing']['pobox'] = '';
            }
        }

        //file_put_contents('logs/myLog.log', print_r($info, true));

        // Emit data back to jQuery/JavaScript
        $response = new Vtiger_Response();
        $response->setResult($info);
        $response->emit();
    }
}
