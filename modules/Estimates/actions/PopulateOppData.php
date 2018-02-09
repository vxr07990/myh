<?php
/*******************************************************************************
 * @file			PopulateOppData.php
 * @author			Louis Robinson
 * @company			IGC Software
 * @contact			lrobinson@igcsoftware.com
 * @description		1. Recieves a Vtiger_Request object and builds a query for an
 *						opportunity name.
 *					2. Takes the Vtiger_Request object again for the opportunity
 *					   name and queries for matching customer data for the object
 *					3. Returns the customer and shipping data to the browser via
 *  					jQuery and appends it to the DOM
 *
 *******************************************************************************/
class Estimates_PopulateOppData_Action extends Vtiger_BasicAjax_Action
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
        $params;
        unset($params);

        // now declare
        $params = array();

        // Initial query for "contact_id_display" item from the DOM
        $sql = "SELECT * FROM vtiger_potential, vtiger_potentialscf WHERE vtiger_potential.potentialid = ? AND vtiger_potentialscf.potentialid = ?";

        // Initial request var from the the DOM upon which query is established
        $params[] = $request->get('potential_id');
        $params[] = $request->get('potential_id');

        // pair up $params and the SQL statement in the pquery object call
        $result = $db->pquery($sql, $params);

        // $opportunity
        unset($params);

        $row = $result->fetchRow();

        $info = array();

        // Origin info
        $info['dates'] = [
            // Pack dates
            'pack_date' => (($row['pack_date'] != '')?$row['pack_date']:''),
            'pack_to_date' => (($row['pack_to_date'] != '')?$row['pack_to_date']:''),
            'preffered_ppdate' => (($row['preffered_ppdate'] != '')?$row['preffered_ppdate']:''),

            // Load dates
            'load_date' => (($row['load_date'] != '')?$row['load_date']:''),
            'load_to_date' => (($row['load_to_date'] != '')?$row['load_to_date']:''),
            'preferred_pldate' => (($row['preferred_pldate'] != '')?$row['preferred_pldate']:''),

            // Deliver dates
            'deliver_date' => (($row['deliver_date'] != '')?$row['deliver_date']:''),
            'deliver_to_date' => (($row['deliver_to_date'] != '')?$row['deliver_to_date']:''),
            'preferred_pddate' => (($row['preferred_pddate'] != '')?$row['preferred_pddate']:'')
        ];

        //$info['dates'] = json_encode($info['dates']);
        $info['opp_type'] = (($row['lead_type'] != '')?$row['lead_type']:'');

        $info['origin']['address1'] = $row['origin_address1'];
        $info['origin']['address2'] = $row['origin_address2'];
        $info['origin']['city'] = $row['origin_city'];
        $info['origin']['state'] = $row['origin_state'];
        $info['origin']['zip'] = $row['origin_zip'];
        $info['origin']['phone1'] = $row['origin_phone1'];
        $info['origin']['phone2'] = $row['origin_phone2'];

        // Destination info
        $info['destination']['address1'] = $row['destination_address1'];
        $info['destination']['address2'] = $row['destination_address2'];
        $info['destination']['city'] = $row['destination_city'];
        $info['destination']['state'] = $row['destination_state'];
        $info['destination']['zip'] = $row['destination_zip'];
        $info['destination']['phone1'] = $row['destination_phone1'];
        $info['destination']['phone2'] = $row['destination_phone2'];

        $call = 'SELECT label FROM  vtiger_crmentity';

        $info['contactid'] = $row['contact_id'];
        $info['accountid'] = $row['related_to'];
        $info['businessline'] = $row['business_line'];
        $info['billing_type'] = $row['billing_type'];
        $info['move_type'] = $row['move_type'];
        $info['shipper_type'] = $row['shipper_type'];

        $flag = 0;
        if ($row['related_to'] != 0) {
            $sql = 'SELECT bill_city, bill_street, bill_country, bill_state, bill_code, bill_pobox
			        FROM vtiger_accountbillads
			        WHERE accountaddressid = ?';
            $params[] = $row['related_to'];
        } else {
            $sql = 'SELECT mailingcity, mailingstreet, mailingcountry, mailingstate, mailingzip, mailingpobox
					FROM vtiger_contactaddress
					WHERE contactaddressid = ?';
            $params[] = $row['contact_id'];
            $flag = 1;
        }
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
        // contactdetails and contactaddress

        $sql = 'SELECT label FROM vtiger_crmentity WHERE vtiger_crmentity.crmid = ?';
        $params[] = $info['accountid'];

        $result = $db->pquery($sql, $params);
        unset($params);
        $row = $result->fetchRow();

        if ($row != null) {
            $info['accountlabel'] = $row[0];
        }
        $params[] = $info['contactid'];

        $result = $db->pquery($sql, $params);
        unset($params);
        $row = $result->fetchRow();

        if ($row != null) {
            $info['contactlabel'] = $row[0];
        }

        $stopsRows = array();
        $db = PearDatabase::getInstance();
        $stopSql = 'SELECT * FROM vtiger_extrastops WHERE extrastops_relcrmid = ?';
        $stopResult = $db->pquery($stopSql, array($request->get('potential_id')));
        $stopRow = $stopResult->fetchRow();
        while ($stopRow != null) {
            $sql2 = 'SELECT firstname, lastname FROM `vtiger_contactdetails` WHERE contactid = ?';
            $result2 = $db->pquery($sql2, array($stopRow['extrastops_contact']));
            $row2 = $result2->fetchRow();
            $stopRow['stop_contact_name'] = $row2['firstname'].' '.$row2['lastname'];
            $stopRow['extrastops_date'] = Vtiger_Date_UIType::getDisplayValue($stopRow['extrastops_date']);
            $stopsRows[] = $stopRow;
            $stopRow = $stopResult->fetchRow();
        }

        $info['stops_rows'] = $stopsRows;

        //file_put_contents('logs/pod.log', date('Y-m-d H:i:s - ').print_r($info, true)."\n", FILE_APPEND);

        // Emit data back to jQuery/JavaScript
        $response = new Vtiger_Response();
        $response->setResult($info);
        $response->emit();
    }
}
