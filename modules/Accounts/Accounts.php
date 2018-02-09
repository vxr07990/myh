<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/
/*********************************************************************************
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/modules/Accounts/Accounts.php,v 1.53 2005/04/28 08:06:45 rank Exp $
 * Description:  Defines the Account SugarBean Account entity with the necessary
 * methods and variables.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/
class Accounts extends CRMEntity
{
    public $log;
    public $db;
    public $table_name = "vtiger_account";
    public $table_index= 'accountid';
    public $tab_name = array('vtiger_crmentity','vtiger_account','vtiger_accountbillads','vtiger_accountshipads','vtiger_accountscf');
    public $tab_name_index = array('vtiger_crmentity'=>'crmid','vtiger_account'=>'accountid','vtiger_accountbillads'=>'accountaddressid','vtiger_accountshipads'=>'accountaddressid','vtiger_accountscf'=>'accountid');
    /**
     * Mandatory table for supporting custom fields.
     */
    public $customFieldTable = array('vtiger_accountscf', 'accountid');
    public $entity_table = "vtiger_crmentity";

    public $column_fields = array();

    public $sortby_fields = array('accountname','bill_city','website','phone','smownerid');

    //var $groupTable = Array('vtiger_accountgrouprelation','accountid');

    // This is the list of vtiger_fields that are in the lists.
    public $list_fields = array(
            'Account Name'=>array('vtiger_account'=>'accountname'),
            'Billing City'=>array('vtiger_accountbillads'=>'bill_city'),
            'Website'=>array('vtiger_account'=>'website'),
            'Phone'=>array('vtiger_account'=> 'phone'),
            'Assigned To'=>array('vtiger_crmentity'=>'smownerid')
            );

    public $list_fields_name = array(
            'Account Name'=>'accountname',
            'Billing City'=>'bill_city',
            'Website'=>'website',
            'Phone'=>'phone',
            'Assigned To'=>'assigned_user_id'
            );
    public $list_link_field= 'accountname';

    public $search_fields = array(
            'Account Name'=>array('vtiger_account'=>'accountname'),
            'Billing City'=>array('vtiger_accountbillads'=>'bill_city'),
            'Assigned To'=>array('vtiger_crmentity'=>'smownerid'),
            );

    public $search_fields_name = array(
            'Account Name'=>'accountname',
            'Billing City'=>'bill_city',
            'Assigned To'=>'assigned_user_id',
            );
    // This is the list of vtiger_fields that are required
    public $required_fields =  array();

    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    public $mandatory_fields = array('assigned_user_id', 'createdtime', 'modifiedtime', 'accountname');

    //Default Fields for Email Templates -- Pavani
    public $emailTemplate_defaultFields = array('accountname','account_type','industry','annualrevenue','phone','email1','rating','website','fax');

    //Added these variables which are used as default order by and sortorder in ListView
    public $default_order_by = 'accountname';
    public $default_sort_order = 'ASC';

    // For Alphabetical search
    public $def_basicsearch_col = 'accountname';

    public $related_module_table_index = array(
        'Contacts' => array('table_name' => 'vtiger_contactdetails', 'table_index' => 'contactid', 'rel_index' => 'accountid'),
        'Potentials' => array('table_name' => 'vtiger_potential', 'table_index' => 'potentialid', 'rel_index' => 'related_to'),
        'Quotes' => array('table_name' => 'vtiger_quotes', 'table_index' => 'quoteid', 'rel_index' => 'accountid'),
        'SalesOrder' => array('table_name' => 'vtiger_salesorder', 'table_index' => 'salesorderid', 'rel_index' => 'accountid'),
        'Invoice' => array('table_name' => 'vtiger_invoice', 'table_index' => 'invoiceid', 'rel_index' => 'accountid'),
        'HelpDesk' => array('table_name' => 'vtiger_troubletickets', 'table_index' => 'ticketid', 'rel_index' => 'parent_id'),
        'Products' => array('table_name' => 'vtiger_seproductsrel', 'table_index' => 'productid', 'rel_index' => 'crmid'),
        'Calendar' => array('table_name' => 'vtiger_seactivityrel', 'table_index' => 'activityid', 'rel_index' => 'crmid'),
        'Documents' => array('table_name' => 'vtiger_senotesrel', 'table_index' => 'notesid', 'rel_index' => 'crmid'),
        'ServiceContracts' => array('table_name' => 'vtiger_servicecontracts', 'table_index' => 'servicecontractsid', 'rel_index' => 'sc_related_to'),
        'Services' => array('table_name' => 'vtiger_crmentityrel', 'table_index' => 'crmid', 'rel_index' => 'crmid'),
        'Campaigns' => array('table_name' => 'vtiger_campaignaccountrel', 'table_index' => 'campaignid', 'rel_index' => 'accountid'),
        'Assets' => array('table_name' => 'vtiger_assets', 'table_index' => 'assetsid', 'rel_index' => 'account'),
        'Project' => array('table_name' => 'vtiger_project', 'table_index' => 'projectid', 'rel_index' => 'linktoaccountscontacts'),
    );

    public function Accounts()
    {
        $this->log =LoggerManager::getLogger('account');
        $this->db = PearDatabase::getInstance();
        $this->column_fields = getColumnFields('Accounts');
    }

    /** Function to handle module specific operations when saving a entity
    */
    public function save_module($module)
    {
        //file_put_contents('logs/myLog.log', print_r($_REQUEST, TRUE)."\n");
        $columns = $this->column_fields;
        $request = array_merge($_REQUEST, $this->column_fields);

        $recordId = $this->id;
        $db = PearDatabase::getInstance();
        if (getenv('INSTANCE_NAME') == 'sirva') {
            //file_put_contents('logs/devLog.log', "\n COLUMNS/REQUEST: ".print_r($request, true), FILE_APPEND);
            /*-----------------------Save annual rate increases----------------------------*/
            $annualRateTotal = $request['numAnnualRate'];
            //file_put_contents('logs/devLog.log', "\n ANN RATE TOTAL: ".$annualRateTotal, FILE_APPEND);
            for ($annualCount = 1; $annualCount <= $annualRateTotal; $annualCount++) {
                $annualRateId = $request['annualRateId'.$annualCount];
                $deleted      = $request['annualRateDeleted'.$annualCount];
                if ($deleted != 'DELETE') {
                    if ($request['annual_rate_increase'.$annualCount] == '' && $request['annual_rate_date'.$annualCount] == '') {
                        continue;
                    }
                    $date = $request['annual_rate_date'.$annualCount];
                    if ($annualRateId != '0' && $annualRateId != 0) {
                        $result =
                            $db->pquery('UPDATE `vtiger_annual_rate` SET date = ?, rate = ?, accountid = ? WHERE annualrateid = ?',
                                        [$date, $request['annual_rate_increase'.$annualCount], $recordId, $request['annualRateId'.$annualCount]]);
                    } else {
                        $result = $db->pquery('SELECT id from `vtiger_annual_rate_seq`', []);
                        $row    = $result->fetchRow();
                        if ($row[0]) {
                            $annualId = $row[0];
                        } else {
                            $annualId = 0;
                            $result   = $db->pquery('INSERT INTO `vtiger_annual_rate_seq` SET id = ?', [0]);
                        }
                        $annualId++;
                        $result = $db->pquery('UPDATE `vtiger_annual_rate_seq` SET id = ?', [$annualId]);
                        $result =
                            $db->pquery('INSERT INTO `vtiger_annual_rate` (annualrateid, date, rate, accountid) VALUES (?,?,?,?)',
                                        [$annualId, $date, $request['annual_rate_increase'.$annualCount], $recordId]);
                    }
                    //file_put_contents('logs/devLog.log', "\n annual rate #$annualCount - ".print_r(array($date, $request['annual_rate_increase'.$annualCount], $annualId, $recordId), true), FILE_APPEND);
                } else {
                    if ($annualRateId != '') {
                        $db->pquery('UPDATE `vtiger_annual_rate` SET accountid = NULL WHERE annualrateid = ? AND accountid = ?', [$request['annualRateId'.$annualCount], $recordId]);
                        $db->pquery('DELETE FROM `vtiger_annual_rate` WHERE annualrateid = ? AND accountid IS NULL AND contractid IS NULL', [$request['annualRateId'.$annualCount]]);
                    }
                }
            }
            /*-----------------------End annual rate increases----------------------------*/
        } elseif (getenv('INSTANCE_NAME') == 'graebel') {
            /*------------------------Salesperson Save---------------------------------------*/
            $currentDate  = new DateTime("today");
            if (!empty($request['sales_person'])) {
                if ($request['record']) { // if not a new record
                    $currentSalesPersonData = Accounts_Salesperson_Model::getSalesPersonData($request['record']);
                    $currentIds             = [];
                    $updatedIds             = [];
                    foreach ($currentSalesPersonData as $salesPerson) {
                        $currentIds[] = $salesPerson['id'];
                    }
                    for ($i = 0; $i < count($request['sales_person']); $i++) {
                        if ($request['sales_person'][$i]) {
                            $params = [
                                $request['sales_person'][$i],
                                $request['booking_office'][$i],
                                $request['salesperson_commodity'][$i],
                                $request['sales_credit'][$i],
                                $request['sales_comm'][$i],
                                DateTimeField::convertToDBFormat($request['effective_date_from'][$i]),
                                DateTimeField::convertToDBFormat($request['effective_date_to'][$i]),
                                $recordId,
                            ];

                            $badDate = (date_create($params[6]) < date_create($params[5]) || date_create($params[6]) < $currentDate);
                            //check if the salesperson row exists already or not and add or update accordingly
                            if (in_array($request['salesperson_id'][$i], $currentIds) && $request['salesperson_id'][$i] > 0) { //existing row
                                $params[7]    = $request['salesperson_id'][$i];
                                $sql          = 'UPDATE `vtiger_account_salespersons`
							 SET salesperson_id = ?, booking_office_id = ?, commodity = ?, sales_credit = ?, 
							 sales_comm = ?, effective_date_from = ?, effective_date_to = ? WHERE id = ?';
                                $updatedIds[] = $request['salesperson_id'][$i];
                            } elseif (empty($request['salesperson_id'][$i])) { // new row
                                $sql = 'INSERT INTO `vtiger_account_salespersons`
							(salesperson_id, booking_office_id, commodity, sales_credit, sales_comm, effective_date_from, effective_date_to, record_id)
							 VALUES
							 (?, ?, ?, ?, ?, ?, ?, ?)';
                            }
                            $db->pquery($sql, $params);
                            file_put_contents('logs/myLog.log', print_r($params, true));
                            file_put_contents('logs/myLog.log', print_r($request, true), FILE_APPEND);
                        }
                    }
                    //Check for deleted records and get rid of the ones that are no longer used
                    foreach ($currentIds as $id) {
                        if (!in_array($id, $updatedIds)) {
                            $db->pquery('DELETE FROM `vtiger_account_salespersons` WHERE id = ?', [$id]);
                        }
                    }
                } else {
                    for ($i = 0; $i < count($request['sales_person']); $i++) {
                        if ($request['sales_person'][$i]) {
                            $params = [
                                $request['sales_person'][$i],
                                $request['booking_office'][$i],
                                $request['salesperson_commodity'][$i],
                                $request['sales_credit'][$i],
                                $request['sales_comm'][$i],
                                DateTimeField::convertToDBFormat($request['effective_date_from'][$i]),
                                DateTimeField::convertToDBFormat($request['effective_date_to'][$i]),
//								date('Y-m-d', strtotime($request['effective_date_from'][$i])),
//								date('Y-m-d', strtotime($request['effective_date_to'][$i])),
                                $recordId,
                            ];
                            $badDate = (date_create($params[6]) < date_create($params[5]) || date_create($params[6]) < $currentDate);
                            $db->pquery('INSERT INTO `vtiger_account_salespersons`
							(salesperson_id, booking_office_id, commodity, sales_credit, sales_comm, effective_date_from, effective_date_to, record_id)
							 VALUES
							 (?, ?, ?, ?, ?, ?, ?, ?)',
                                        $params);
                        }
                    }
                }
            } elseif ($request['record']) {
                //if there is a record id and the salesperson data is empty, delete all records
                $db->pquery('DELETE FROM `vtiger_additional_roles` WHERE account_id = ?', [$request['record']]);
            }
            /*-------------------------Salesperson End ---------------------------------------*/

            /*------------------------Billing Addresses Save---------------------------------------*/

            $count = $request['billingAddressCount'];

            if ($request['record']) { // if not a new record
                $billingAddresses = Accounts_Record_Model::getAccountsBillingAddresses($request['record']);

                $currentIds             = [];
                $updatedIds             = [];

                foreach ($billingAddresses as $address) {
                    $currentIds[] = $address['id'];
                }

                for ($i = 0; $i <= $count; $i++) {
                    if ($request['billing_address1'][$i]) {
                        $commodity = reset($request['commodity']);
                        unset($request['commodity'][key($request['commodity'])]);
                        if (is_array($commodity)) {
                            $billingAddressCommodityInput = implode(' |##| ', $commodity);
                        } else {
                            $billingAddressCommodityInput = $commodity;
                        }
                        $params = [
                            htmlentities($billingAddressCommodityInput),
                            htmlentities($request['billing_address1'][$i]),
                            htmlentities($request['billing_address2'][$i]),
                            htmlentities($request['billing_address_desc'][$i]),
                            htmlentities($request['billing_city'][$i]),
                            htmlentities($request['billing_state'][$i]),
                            htmlentities($request['billing_zip'][$i]),
                            htmlentities($request['billing_country'][$i]),
                            htmlentities($request['billing_company_name'][$i]),
                            ($request['billing_active'][$i] == 'yes' || $request['billing_active'][$i] == true || $request['billing_active'][$i] == 1) ?'yes':'no',
                        ];

                        if (in_array($request['billing_id'][$i], $currentIds) && $request['billing_id'][$i] > 0) { //existing row
                            $params[] = $request['billing_id'][$i];

                            $sql = 'UPDATE `vtiger_accounts_billing_addresses`
                         SET commodity = ?, address1 = ?, address2 = ?, address_desc = ?, 
                         city = ?, state = ?, zip = ?, country = ?, company = ?, active = ? WHERE id = ?';


                            $updatedIds[] = $request['billing_id'][$i];
                        } elseif (empty($request['billing_id'][$i])) { // new row
                            $params[] = $request['record'];

                            $sql = 'INSERT INTO `vtiger_accounts_billing_addresses`
                        (commodity, address1, address2, address_desc, city, state, zip, country, company, active, account_id)
                         VALUES
                         (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
                        }
                        $db->pquery($sql, $params);
                    }
                }
                //Check for deleted records and get rid of the ones that are no longer used
                foreach ($currentIds as $id) {
                    if (!in_array($id, $updatedIds)) {
                        $db->pquery('DELETE FROM `vtiger_accounts_billing_addresses` WHERE id = ?', [$id]);
                    }
                }
            } else {
                for ($i = 0; $i <= $count; $i++) {
                    if ($request['billing_address1'][$i]) {
                        $commodity = reset($request['commodity']);
                        unset($request['commodity'][key($request['commodity'])]);
                        if (is_array($commodity)) {
                            $billingAddressCommodityInput = implode(' |##| ', $commodity);
                        } else {
                            $billingAddressCommodityInput = $commodity;
                        }
                        $params = [
                            $recordId,
                            htmlentities($billingAddressCommodityInput),
                            htmlentities($request['billing_address_desc'][$i]),
                            htmlentities($request['billing_address1'][$i]),
                            htmlentities($request['billing_address2'][$i]),
                            htmlentities($request['billing_city'][$i]),
                            htmlentities($request['billing_state'][$i]),
                            htmlentities($request['billing_zip'][$i]),
                            htmlentities($request['billing_country'][$i]),
                            htmlentities($request['billing_company_name'][$i]),
                            ($request['billing_active'][$i] == 'yes' || $request['billing_active'][$i] == true || $request['billing_active'][$i] == 1) ?'yes':'no',
                        ];
                        $db->pquery('INSERT INTO `vtiger_accounts_billing_addresses`
                        (account_id,commodity, address_desc, address1, address2, city, state, zip, country, company, active)
                         VALUES
                         (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                            $params);
                    }
                }
            }
            /*------------------------END Billing Addresses Save---------------------------------------*/

            /*------------------------Invoice Settings Save---------------------------------------*/
            if ($request['record']) { // if not a new record
                $currentInvoiceSettings = Accounts_Record_Model::getCurrentInvoiceSettings($request['record']);

                $currentIds             = [];
                $updatedIds             = [];

                foreach ($currentInvoiceSettings as $settings) {
                    $currentIds[] = $settings['id'];
                }

                for ($i = 0; $i < count($request['invoice_commodity']); $i++) {
                    if ($request['invoice_commodity'][$i]) {
                        $params = [
                            htmlentities($request['invoice_commodity'][$i]),
                            htmlentities($request['invoice_format'][$i]),
                            htmlentities($request['invoice_packet'][$i]),
                            htmlentities($request['invoice_document'][$i]),
                            htmlentities($request['invoice_delivery'][$i]),
                            htmlentities($request['invoice_finance_charge'][$i]),
                            htmlentities($request['payment_terms'][$i]),
                        ];

                        if (in_array($request['invoice_id'][$i], $currentIds) && $request['invoice_id'][$i] > 0) { //existing row
                            $params[] = $request['invoice_id'][$i];
                            file_put_contents('logs/myLog.log', print_r($params, true));
                            $sql = 'UPDATE `vtiger_account_invoicesettings`
                         SET commodity = ?, invoice_template = ?, invoice_packet = ?, document_format = ?, 
                         invoice_delivery = ?, finance_charge = ?, payment_terms = ?  WHERE id = ?';

                            $updatedIds[] = htmlentities($request['invoice_id'][$i]);
                        } elseif (empty($request['invoice_id'][$i])) { // new row
                            $params[] = $request['record'];

                            $sql = 'INSERT INTO `vtiger_account_invoicesettings`
                        (commodity, invoice_template, invoice_packet, document_format, invoice_delivery, finance_charge, payment_terms, record_id)
                         VALUES
                         (?, ?, ?, ?, ?, ?, ?, ?)';
                        }
                        $db->pquery($sql, $params);
                    }
                }
                //Check for deleted records and get rid of the ones that are no longer used
                foreach ($currentIds as $id) {
                    if (!in_array($id, $updatedIds)) {
                        $db->pquery('DELETE FROM `vtiger_account_invoicesettings` WHERE id = ?', [$id]);
                    }
                }
            } else {
                for ($i = 0; $i < count($request['invoice_commodity']); $i++) {
                    if ($request['invoice_commodity'][$i]) {
                        $params = [
                            $recordId,
                            htmlentities($request['invoice_commodity'][$i]),
                            htmlentities($request['invoice_format'][$i]),
                            htmlentities($request['invoice_packet'][$i]),
                            htmlentities($request['invoice_document'][$i]),
                            htmlentities($request['invoice_delivery'][$i]),
                            htmlentities($request['invoice_finance_charge'][$i]),
                            htmlentities($request['payment_terms'][$i]),
                        ];
                        $db->pquery('INSERT INTO `vtiger_account_invoicesettings`
                        (record_id,commodity, invoice_template, invoice_packet, document_format, invoice_delivery, finance_charge, payment_terms)
                         VALUES
                         (?, ?, ?, ?, ?, ?, ?, ?)',
                            $params);
                    }
                }
            }
            /*------------------------END Invoice Settings Save---------------------------------------*/

            /*------------------------Additional Roles Settings Save---------------------------------------*/

            if ($request['record']) { // if not a new record
                $recordModal = new Accounts_Record_Model();
                $currentRoles = $recordModal->getAdditionalRoleValues($request['record']);

                $currentIds             = [];
                $updatedIds             = [];

                foreach ($currentRoles as $roles) {
                    $currentIds[] = $roles['id'];
                }

                for ($i = 0; $i <= count($request['role_commodity']); $i++) {
                    if (!empty($request['role_commodity'][$i])) {
                        $params = [
                            htmlentities(implode(' ## ', $request['role_commodity'][$i])),
                            htmlentities($request['role'][$i]),
                            htmlentities($request['user_role'][$i]),
                        ];

                        if (in_array($request['id'][$i], $currentIds) && $request['id'][$i] > 0) { //existing row
                            $params[] = $request['id'][$i];
                            $sql = 'UPDATE `vtiger_additional_roles`
                         SET commodity = ?, user = ?, role = ?  WHERE id = ?';

                            $updatedIds[] = htmlentities($request['id'][$i]);
                        } elseif (empty($request['id'][$i])) { // new row
                            $params[] = $request['record'];

                            $sql = 'INSERT INTO `vtiger_additional_roles`
                        (commodity,role, user, , account_id)
                         VALUES
                         (?, ?, ?, ?)';
                        }
                        $db->pquery($sql, $params);
                    }
                }
                //Check for deleted records and get rid of the ones that are no longer used
                foreach ($currentIds as $id) {
                    if (!in_array($id, $updatedIds)) {
                        $db->pquery('DELETE FROM `vtiger_additional_roles` WHERE id = ?', [$id]);
                    }
                }
            } else {
                for ($i = 0; $i <= count($request['role_commodity']); $i++) {
                    if (!empty($request['role_commodity'][$i])) {
                        $params = [
                            $recordId,
                            htmlentities(implode(' ## ', $request['role_commodity'][$i])),
                            htmlentities($request['role'][$i]),
                            htmlentities($request['user_role'][$i]),
                        ];

                        $db->pquery('INSERT INTO `vtiger_additional_roles`
                        (account_id, commodity, role, user)
                         VALUES
                         (?, ?, ?, ?)',
                            $params);
                    }
                }
            }
            /*------------------------END Additional Roles Settings Save---------------------------------------*/
        } elseif (getenv('IGC_MOVEHQ')) {
            /*------------------------Salesperson Save---------------------------------------*/
//            if (!empty($request['sales_person'])) {
                if ($request['record']) { // if not a new record
                    $currentSalesPersonData = Accounts_Salesperson_Model::getSalesPersonData($request['record']);
                    $currentIds             = [];
                    $updatedIds             = [];
                    foreach ($currentSalesPersonData as $salesPerson) {
                        $currentIds[] = $salesPerson['id'];
                    }
                    for ($i = 0; $i < count($request['sales_person']); $i++) {
                        if ($request['sales_person'][$i]) {
                            $params = [
                                $request['sales_person'][$i],
                                $request['booking_office'][$i],
                                $request['salesperson_commodity'][$i],
                                $request['sales_credit'][$i],
                                $request['sales_comm'][$i],
                                DateTimeField::convertToDBFormat($request['effective_date_from'][$i]),
                                DateTimeField::convertToDBFormat($request['effective_date_to'][$i]),
                                $recordId,
                            ];
                            //check if the salesperson row exists already or not and add or update accordingly
                            if (in_array($request['salesperson_id'][$i], $currentIds) && $request['salesperson_id'][$i] > 0) { //existing row
                                $params[7]    = $request['salesperson_id'][$i];
                                $sql          = 'UPDATE `vtiger_account_salespersons`
							 SET salesperson_id = ?, booking_office_id = ?, commodity = ?, sales_credit = ?, 
							 sales_comm = ?, effective_date_from = ?, effective_date_to = ? WHERE id = ?';
                                $updatedIds[] = $request['salesperson_id'][$i];
                            } elseif (empty($request['salesperson_id'][$i])) { // new row
                                $sql = 'INSERT INTO `vtiger_account_salespersons`
							(salesperson_id, booking_office_id, commodity, sales_credit, sales_comm, effective_date_from, effective_date_to, record_id)
							 VALUES
							 (?, ?, ?, ?, ?, ?, ?, ?)';
                            }
                            $db->pquery($sql, $params);
                            file_put_contents('logs/myLog.log', print_r($params, true));
                            file_put_contents('logs/myLog.log', print_r($request, true), FILE_APPEND);
                        }
                    }
                    //Check for deleted records and get rid of the ones that are no longer used
                    foreach ($currentIds as $id) {
                        if (!in_array($id, $updatedIds)) {
                            $db->pquery('DELETE FROM `vtiger_account_salespersons` WHERE id = ?', [$id]);
                        }
                    }
                } else {
                    for ($i = 0; $i < count($request['sales_person']); $i++) {
                        if ($request['sales_person'][$i]) {
                            $params = [
                                $request['sales_person'][$i],
                                $request['booking_office'][$i],
                                $request['salesperson_commodity'][$i],
                                $request['sales_credit'][$i],
                                $request['sales_comm'][$i],
                                date('Y-m-d', strtotime($request['effective_date_from'][$i])),
                                date('Y-m-d', strtotime($request['effective_date_to'][$i])),
                                $recordId,
                            ];
                            $db->pquery('INSERT INTO `vtiger_account_salespersons`
							(salesperson_id, booking_office_id, commodity, sales_credit, sales_comm, effective_date_from, effective_date_to, record_id)
							 VALUES
							 (?, ?, ?, ?, ?, ?, ?, ?)',
                                        $params);
                        }
                    }
                }

                /*------------------------Billing Addresses Save---------------------------------------*/
                //$businessValues = $columns['business_line'];
                //if($businessValues[0] == 'All'){
                    $moduleModel=Vtiger_Module_Model::getInstance($module);
                    $businessLineField=$moduleModel->getField('business_line');
                    $businessValues = $businessLineField->getPicklistValues();
                //}
                if ($request['record']) { // if not a new record
                    $billingAddresses = Accounts_Record_Model::getAccountsBillingAddresses($request['record']);

                    $currentIds             = [];
                    $updatedIds             = [];

                    foreach ($billingAddresses as $address) {
                        $currentIds[] = $address['id'];
                    }
                    for ($i = 1; $i <= count($request['billing_address1']); $i++) {
                        if ($request['billing_address1'][$i]) {
                            $commodity =  $request['commodity'][$i];
                            if ($commodity[0] == 'All') {
                                $commodity = $businessValues;
                            }
                            $params = [
                                htmlentities(implode(' |##| ', $commodity)),
                                htmlentities($request['billing_address1'][$i]),
                                htmlentities($request['billing_address2'][$i]),
                                htmlentities($request['billing_address_desc'][$i]),
                                htmlentities($request['billing_city'][$i]),
                                htmlentities($request['billing_state'][$i]),
                                htmlentities($request['billing_zip'][$i]),
                                htmlentities($request['billing_country'][$i]),
                                htmlentities($request['billing_company_name'][$i]),
                                $request['billing_active'][$i] = 'yes'?'yes':'no',
                            ];

                            if (in_array($request['billing_id'][$i], $currentIds) && $request['billing_id'][$i] > 0) { //existing row
                                $params[] = $request['billing_id'][$i];

                                $sql = 'UPDATE `vtiger_accounts_billing_addresses`
							 SET commodity = ?, address1 = ?, address2 = ?, address_desc = ?, 
							 city = ?, state = ?, zip = ?, country = ?, company = ?, active = ? WHERE id = ?';


                                $updatedIds[] = $request['billing_id'][$i];
                            } elseif (empty($request['billing_id'][$i])) { // new row
                                $params[] = $request['record'];

                                $sql = 'INSERT INTO `vtiger_accounts_billing_addresses`
							(commodity, address1, address2, address_desc, city, state, zip, country, company, active, account_id)
							 VALUES
							 (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
                            }
                            $db->pquery($sql, $params);
                        }
                    }
                    //Check for deleted records and get rid of the ones that are no longer used
                    foreach ($currentIds as $id) {
                        if (!in_array($id, $updatedIds)) {
                            $db->pquery('DELETE FROM `vtiger_accounts_billing_addresses` WHERE id = ?', [$id]);
                        }
                    }
                } else {
                    for ($i = 1; $i <= count($request['billing_address1']); $i++) {
                        if ($request['billing_address1'][$i]) {
                            $commodity =  $request['commodity'][$i];
                            if ($commodity[0] == 'All') {
                                $commodity = $businessValues;
                            }
                            $params = [
                                $recordId,
                                htmlentities(implode(' |##| ', $commodity)),
                                htmlentities($request['billing_address_desc'][$i]),
                                htmlentities($request['billing_address1'][$i]),
                                htmlentities($request['billing_address2'][$i]),
                                htmlentities($request['billing_city'][$i]),
                                htmlentities($request['billing_state'][$i]),
                                htmlentities($request['billing_zip'][$i]),
                                htmlentities($request['billing_country'][$i]),
                                htmlentities($request['billing_company_name'][$i]),
                                $request['billing_active'][$i] = 'yes'?'yes':'no',
                            ];
                            $db->pquery('INSERT INTO `vtiger_accounts_billing_addresses`
							(account_id,commodity, address_desc, address1, address2, city, state, zip, country, company, active)
							 VALUES
							 (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                                        $params);
                        }
                    }
                }

                /*------------------------Invoice Settings Save---------------------------------------*/

                if ($request['record']) { // if not a new record
                    $currentInvoiceSettings = Accounts_Record_Model::getCurrentInvoiceSettings($request['record']);

                    $currentIds             = [];
                    $updatedIds             = [];

                    foreach ($currentInvoiceSettings as $settings) {
                        $currentIds[] = $settings['id'];
                    }

                    for ($i = 1; $i <= count($request['invoice_commodity']); $i++) {
                        if ($request['invoice_commodity'][$i]) {
                            $invoice_commodity =  $request['invoice_commodity'][$i];
                            if ($invoice_commodity[0] == 'All') {
                                $invoice_commodity = $businessValues;
                            }
                            $params = [
                                htmlentities(implode(' |##| ', $invoice_commodity)),
                                htmlentities($request['invoice_format'][$i]),
                                htmlentities($request['invoice_packet'][$i]),
                                htmlentities($request['invoice_document'][$i]),
                                htmlentities($request['invoice_delivery'][$i]),
                                htmlentities($request['invoice_finance_charge'][$i]),
                                htmlentities($request['payment_terms'][$i]),
                            ];

                            if (in_array($request['invoice_id'][$i], $currentIds) && $request['invoice_id'][$i] > 0) { //existing row
                                $params[] = $request['invoice_id'][$i];
                                file_put_contents('logs/myLog.log', print_r($params, true));
                                $sql = 'UPDATE `vtiger_account_invoicesettings`
							 SET commodity = ?, invoice_template = ?, invoice_packet = ?, document_format = ?, 
							 invoice_delivery = ?, finance_charge = ?, payment_terms = ?  WHERE id = ?';

                                $updatedIds[] = htmlentities($request['invoice_id'][$i]);
                            } elseif (empty($request['invoice_id'][$i])) { // new row
                                $params[] = $request['record'];

                                $sql = 'INSERT INTO `vtiger_account_invoicesettings`
							(commodity, invoice_template, invoice_packet, document_format, invoice_delivery, finance_charge, payment_terms, record_id)
							 VALUES
							 (?, ?, ?, ?, ?, ?, ?, ?)';
                            }
                            $db->pquery($sql, $params);
                        }
                    }
                    //Check for deleted records and get rid of the ones that are no longer used
                    foreach ($currentIds as $id) {
                        if (!in_array($id, $updatedIds)) {
                            $db->pquery('DELETE FROM `vtiger_account_invoicesettings` WHERE id = ?', [$id]);
                        }
                    }
                } else {
                    for ($i = 1; $i <= count($request['invoice_commodity']); $i++) {
                        $invoice_commodity =  $request['invoice_commodity'][$i];
                        if ($invoice_commodity[0] == 'All') {
                            $invoice_commodity = $businessValues;
                        }
                        if ($request['invoice_commodity'][$i]) {
                            $params = [
                                $recordId,
                                htmlentities(implode(' |##| ', $invoice_commodity)),
                                htmlentities($request['invoice_format'][$i]),
                                htmlentities($request['invoice_packet'][$i]),
                                htmlentities($request['invoice_document'][$i]),
                                htmlentities($request['invoice_delivery'][$i]),
                                htmlentities($request['invoice_finance_charge'][$i]),
                                htmlentities($request['payment_terms'][$i]),
                            ];
                            $db->pquery('INSERT INTO `vtiger_account_invoicesettings`
							(record_id,commodity, invoice_template, invoice_packet, document_format, invoice_delivery, finance_charge, payment_terms)
							 VALUES
							 (?, ?, ?, ?, ?, ?, ?, ?)',
                                        $params);
                        }
                    }
                }

                /*------------------------Invoice Settings Save---------------------------------------*/

                if ($request['record']) { // if not a new record
                    $recordModal = new Accounts_Record_Model();
                    $currentRoles = $recordModal->getAdditionalRoleValues($request['record']);

                    $currentIds             = [];
                    $updatedIds             = [];

                    foreach ($currentRoles as $roles) {
                        $currentIds[] = $roles['id'];
                    }

                    for ($i = 1; $i <= count($request['role_commodity']); $i++) {
                        if (!empty($request['role_commodity'][$i])) {
                            if(in_array('All',$request['role_commodity'][$i])){
                                if(!empty($request['business_line'])){
                                    $request['role_commodity'][$i] = $request['business_line'];
                                }else{
                                    $salesPersons = new Accounts_Salesperson_Model;
                                    $request['role_commodity'][$i] = $salesPersons->getBusinessLines();
                                }
                            }
                            $params = [
                                htmlentities(implode(' ## ', $request['role_commodity'][$i])),
                                htmlentities($request['role'][$i]),
                                htmlentities($request['user_role'][$i]),
                            ];
                            if (in_array($request['id'][$i], $currentIds) && $request['id'][$i] > 0) { //existing row
                                $params[] = $request['id'][$i];
                                $sql = 'UPDATE `vtiger_additional_roles`
							 SET commodity = ?, role = ?, user = ?  WHERE id = ?';

                                $updatedIds[] = htmlentities($request['id'][$i]);
                            } elseif (empty($request['id'][$i])) { // new row
                                $params[] = $request['record'];

                                $sql = 'INSERT INTO `vtiger_additional_roles`
							(commodity, role, user, account_id)
							 VALUES
							 (?, ?, ?, ?)';
                            }
                            $db->pquery($sql, $params);
                        }
                    }
                    //Check for deleted records and get rid of the ones that are no longer used
                    foreach ($currentIds as $id) {
                        if (!in_array($id, $updatedIds)) {
                            $db->pquery('DELETE FROM `vtiger_additional_roles` WHERE id = ?', [$id]);
                        }
                    }
                } else {
                    for ($i = 1; $i <= count($request['role_commodity']); $i++) {
                        if (!empty($request['role_commodity'][$i])) {
                            if(in_array('All',$request['role_commodity'][$i])){
                                if(!empty($request['business_line'])){
                                    $request['role_commodity'][$i] = $request['business_line'];
                                }else{
                                    $salesPersons = new Accounts_Salesperson_Model;
                                    $request['role_commodity'][$i] = $salesPersons->getBusinessLines();
                                }
                            }
                            $params = [
                                $recordId,
                                htmlentities(implode(' ## ', $request['role_commodity'][$i])),
                                htmlentities($request['role'][$i]),
                                htmlentities($request['user_role'][$i]),
                            ];
                            $db->pquery('INSERT INTO `vtiger_additional_roles`
							(account_id, commodity, role, user)
							 VALUES
							 (?, ?, ?, ?)',
                                        $params);
                        }
                    }
                }
//            } elseif ($request['record']) {
//                //if there is a record id and the salesperson data is empty, delete all records
//                $db->pquery('DELETE FROM `vtiger_additional_roles` WHERE account_id = ?', [$request['record']]);
//            }
            /*-------------------------Salesperson End ---------------------------------------*/
        }
    }


    // Mike Crowe Mod --------------------------------------------------------Default ordering for us
    /** Returns a list of the associated Campaigns
     * @param $id -- campaign id :: Type Integer
     * @returns list of campaigns in array format
     */
    public function get_campaigns($id, $cur_tab_id, $rel_tab_id, $actions=false)
    {
        global $log, $singlepane_view,$currentModule,$current_user;
        $log->debug("Entering get_campaigns(".$id.") method ...");
        $this_module = $currentModule;

        $related_module = vtlib_getModuleNameById($rel_tab_id);
        require_once("modules/$related_module/$related_module.php");
        $other = new $related_module();
        vtlib_setup_modulevars($related_module, $other);
        $singular_modname = vtlib_toSingular($related_module);

        $parenttab = getParentTab();

        if ($singlepane_view == 'true') {
            $returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
        } else {
            $returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;
        }

        $button = '';

        $button .= '<input type="hidden" name="email_directing_module"><input type="hidden" name="record">';

        if ($actions) {
            if (is_string($actions)) {
                $actions = explode(',', strtoupper($actions));
            }
            if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
                $button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
            }
        }

        $entityIds = $this->getRelatedContactsIds();
        $entityIds = implode(',', $entityIds);

        $userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');

        $query = "SELECT case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name,
				vtiger_campaign.campaignid, vtiger_campaign.campaignname, vtiger_campaign.campaigntype, vtiger_campaign.campaignstatus,
				vtiger_campaign.expectedrevenue, vtiger_campaign.closingdate, vtiger_crmentity.crmid, vtiger_crmentity.smownerid,
				vtiger_crmentity.modifiedtime
				from vtiger_campaign
				INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_campaign.campaignid
				INNER JOIN vtiger_campaignscf ON vtiger_campaignscf.campaignid = vtiger_campaign.campaignid
				LEFT JOIN vtiger_campaignaccountrel ON vtiger_campaignaccountrel.campaignid=vtiger_campaign.campaignid
				LEFT JOIN vtiger_campaigncontrel ON vtiger_campaigncontrel.campaignid=vtiger_campaign.campaignid
				LEFT JOIN vtiger_groups ON vtiger_groups.groupid=vtiger_crmentity.smownerid
				LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
				WHERE vtiger_crmentity.deleted=0 AND (vtiger_campaignaccountrel.accountid=$id";

        if (!empty($entityIds)) {
            $query .= " OR vtiger_campaigncontrel.contactid IN (".$entityIds."))";
        } else {
            $query .= ")";
        }

        $return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

        if ($return_value == null) {
            $return_value = array();
        }
        $return_value['CUSTOM_BUTTON'] = $button;

        $log->debug("Exiting get_campaigns method ...");
        return $return_value;
    }

    /** Returns a list of the associated contacts
     * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc..
     * All Rights Reserved..
     * Contributor(s): ______________________________________..
     */
    public function get_contacts($id, $cur_tab_id, $rel_tab_id, $actions=false)
    {
        global $log, $singlepane_view,$currentModule,$current_user;
        $log->debug("Entering get_contacts(".$id.") method ...");
        $this_module = $currentModule;

        $related_module = vtlib_getModuleNameById($rel_tab_id);
        require_once("modules/$related_module/$related_module.php");
        $other = new $related_module();
        vtlib_setup_modulevars($related_module, $other);
        $singular_modname = vtlib_toSingular($related_module);

        $parenttab = getParentTab();

        if ($singlepane_view == 'true') {
            $returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
        } else {
            $returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;
        }

        $button = '';

        if ($actions && getFieldVisibilityPermission($related_module, $current_user->id, 'account_id', 'readwrite') == '0') {
            if (is_string($actions)) {
                $actions = explode(',', strtoupper($actions));
            }
            if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
                $button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
            }
            if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
                $button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". getTranslatedString($singular_modname) ."' class='crmbutton small create'" .
                    " onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
                    " value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString($singular_modname) ."'>&nbsp;";
            }
        }

        $userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
                            'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
        $query = "SELECT vtiger_contactdetails.*,
			vtiger_crmentity.crmid,
                        vtiger_crmentity.smownerid,
			vtiger_account.accountname,
			case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name
			FROM vtiger_contactdetails
			INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_contactdetails.contactid
			LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_contactdetails.accountid
			INNER JOIN vtiger_contactaddress ON vtiger_contactdetails.contactid = vtiger_contactaddress.contactaddressid
			INNER JOIN vtiger_contactsubdetails ON vtiger_contactdetails.contactid = vtiger_contactsubdetails.contactsubscriptionid
			INNER JOIN vtiger_customerdetails ON vtiger_contactdetails.contactid = vtiger_customerdetails.customerid
			INNER JOIN vtiger_contactscf ON vtiger_contactdetails.contactid = vtiger_contactscf.contactid
			LEFT JOIN vtiger_groups	ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id
			WHERE vtiger_crmentity.deleted = 0
			AND vtiger_contactdetails.accountid = ".$id;

        $return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

        if ($return_value == null) {
            $return_value = array();
        }
        $return_value['CUSTOM_BUTTON'] = $button;

        $log->debug("Exiting get_contacts method ...");
        return $return_value;
    }

    /** Returns a list of the associated opportunities
     * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc..
     * All Rights Reserved..
     * Contributor(s): ______________________________________..
     */
    public function get_opportunities($id, $cur_tab_id, $rel_tab_id, $actions=false)
    {
        global $log, $singlepane_view,$currentModule,$current_user;
        $log->debug("Entering get_opportunities(".$id.") method ...");
        $this_module = $currentModule;

        $related_module = vtlib_getModuleNameById($rel_tab_id);
        require_once("modules/$related_module/$related_module.php");
        $other = new $related_module();
        vtlib_setup_modulevars($related_module, $other);
        $singular_modname = vtlib_toSingular($related_module);

        $parenttab = getParentTab();

        if ($singlepane_view == 'true') {
            $returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
        } else {
            $returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;
        }

        $button = '';

        if ($actions) {
            if (is_string($actions)) {
                $actions = explode(',', strtoupper($actions));
            }
            if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
                $button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
            }
            if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
                $button .= "<input title='".getTranslatedString('LBL_NEW'). " ". getTranslatedString($singular_modname) ."' class='crmbutton small create'" .
                    " onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
                    " value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString($singular_modname) ."'>&nbsp;";
            }
        }

        // TODO: We need to add pull contacts if its linked as secondary in Potentials too.
        // These relations are captued in vtiger_contpotentialrel
        // Better to provide switch to turn-on / off this feature like in
        // Contacts::get_opportunities

        $entityIds = $this->getRelatedContactsIds();
        $entityIds = implode(',', $entityIds);

        $userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');

        $query = "SELECT vtiger_potential.potentialid, vtiger_potential.related_to, vtiger_potential.potentialname, vtiger_potential.sales_stage,vtiger_potential.contact_id,
				vtiger_potential.potentialtype, vtiger_potential.amount, vtiger_potential.closingdate, vtiger_potential.potentialtype, vtiger_account.accountname,
				case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name,vtiger_crmentity.crmid, vtiger_crmentity.smownerid
				FROM vtiger_potential
				INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_potential.potentialid
				LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_potential.related_to
				INNER JOIN vtiger_potentialscf ON vtiger_potential.potentialid = vtiger_potentialscf.potentialid
				LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id
				LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
				WHERE vtiger_crmentity.deleted = 0 AND (vtiger_potential.related_to = $id ";
        if (!empty($entityIds)) {
            $query .= " OR vtiger_potential.contact_id IN (".$entityIds.")";
        }

        $query .= ')';

        $return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

        if ($return_value == null) {
            $return_value = array();
        }
        $return_value['CUSTOM_BUTTON'] = $button;

        $log->debug("Exiting get_opportunities method ...");
        return $return_value;
    }


    /**
    * Function to get Account related Surveys
    * @param  integer   $id      - accountid
    * returns related Surveys record in array format
    */
    public function get_surveys($id, $cur_tab_id, $rel_tab_id, $actions=false)
    {
        global $log, $singlepane_view,$currentModule,$current_user;
        $log->debug("Entering get_surveys(".$id.") method ...");
        $this_module = $currentModule;

        $related_module = vtlib_getModuleNameById($rel_tab_id);
        require_once("modules/$related_module/$related_module.php");
        $other = new $related_module();
        vtlib_setup_modulevars($related_module, $other);
        $singular_modname = vtlib_toSingular($related_module);

        $parenttab = getParentTab();

        if ($singlepane_view == 'true') {
            $returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
        } else {
            $returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;
        }

        $button = '';

        if ($actions && getFieldVisibilityPermission($related_module, $current_user->id, 'account_id', 'readwrite') == '0') {
            if (is_string($actions)) {
                $actions = explode(',', strtoupper($actions));
            }
            if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
                $button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
            }
            if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
                $button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". getTranslatedString($singular_modname) ."' class='crmbutton small create'" .
                    " onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
                    " value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString($singular_modname) ."'>&nbsp;";
            }
        }

        $entityIds = $this->getRelatedContactsIds();
        $entityIds = implode(',', $entityIds);

        $userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');

        $query = "SELECT case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name,
				vtiger_crmentity.*, vtiger_surveys.*, vtiger_potential.potentialname, vtiger_account.accountname
				FROM vtiger_surveys
				INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_surveys.surveysid
				LEFT OUTER JOIN vtiger_account ON vtiger_account.accountid = vtiger_surveys.account_id
				LEFT OUTER JOIN vtiger_potential ON vtiger_potential.potentialid = vtiger_surveys.potential_id
				LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
                LEFT JOIN vtiger_surveyscf ON vtiger_surveyscf.surveysid = vtiger_surveys.surveysid
				LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id
				WHERE vtiger_crmentity.deleted = 0 AND (vtiger_account.accountid = $id";

        if (!empty($entityIds)) {
            $query .= " OR vtiger_surveys.contact_id IN (".$entityIds."))";
        } else {
            $query .= ")";
        }

        $return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

        if ($return_value == null) {
            $return_value = array();
        }
        $return_value['CUSTOM_BUTTON'] = $button;

        $log->debug("Exiting get_surveys method ...");
        return $return_value;
    }

    /** Returns a list of the associated tasks
     * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc..
     * All Rights Reserved..
     * Contributor(s): ______________________________________..
     */
    public function get_activities($id, $cur_tab_id, $rel_tab_id, $actions=false)
    {
        global $log, $singlepane_view,$currentModule,$current_user;
        $log->debug("Entering get_activities(".$id.") method ...");
        $this_module = $currentModule;

        $related_module = vtlib_getModuleNameById($rel_tab_id);
        require_once("modules/$related_module/Activity.php");
        $other = new Activity();
        vtlib_setup_modulevars($related_module, $other);
        $singular_modname = vtlib_toSingular($related_module);

        $parenttab = getParentTab();

        if ($singlepane_view == 'true') {
            $returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
        } else {
            $returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;
        }

        $button = '';

        $button .= '<input type="hidden" name="activity_mode">';

        if ($actions) {
            if (is_string($actions)) {
                $actions = explode(',', strtoupper($actions));
            }
            if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
                if (getFieldVisibilityPermission('Calendar', $current_user->id, 'parent_id', 'readwrite') == '0') {
                    $button .= "<input title='".getTranslatedString('LBL_NEW'). " ". getTranslatedString('LBL_TODO', $related_module) ."' class='crmbutton small create'" .
                        " onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\";this.form.return_module.value=\"$this_module\";this.form.activity_mode.value=\"Task\";' type='submit' name='button'" .
                        " value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString('LBL_TODO', $related_module) ."'>&nbsp;";
                }
                if (getFieldVisibilityPermission('Events', $current_user->id, 'parent_id', 'readwrite') == '0') {
                    $button .= "<input title='".getTranslatedString('LBL_NEW'). " ". getTranslatedString('LBL_TODO', $related_module) ."' class='crmbutton small create'" .
                        " onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\";this.form.return_module.value=\"$this_module\";this.form.activity_mode.value=\"Events\";' type='submit' name='button'" .
                        " value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString('LBL_EVENT', $related_module) ."'>";
                }
            }
        }

        $entityIds = $this->getRelatedContactsIds();
        $entityIds = implode(',', $entityIds);

        $userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');

        $query = "SELECT vtiger_activity.*, vtiger_cntactivityrel.*, vtiger_seactivityrel.crmid as parent_id, vtiger_contactdetails.lastname,
				vtiger_contactdetails.firstname, vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_crmentity.modifiedtime,
				case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name,
				vtiger_recurringevents.recurringtype
				FROM vtiger_activity
				INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_activity.activityid
				LEFT JOIN vtiger_seactivityrel ON vtiger_seactivityrel.activityid = vtiger_activity.activityid
				LEFT JOIN vtiger_cntactivityrel ON vtiger_cntactivityrel.activityid = vtiger_activity.activityid
				LEFT JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_cntactivityrel.contactid
				LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
				LEFT OUTER JOIN vtiger_recurringevents ON vtiger_recurringevents.activityid = vtiger_activity.activityid
				LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
				WHERE vtiger_crmentity.deleted = 0
				AND ((vtiger_activity.activitytype='Task' and vtiger_activity.status not in ('Completed','Deferred'))
				OR (vtiger_activity.activitytype not in ('Emails','Task') and  vtiger_activity.eventstatus not in ('','Held')))
				AND (vtiger_seactivityrel.crmid = $id";

        if (!empty($entityIds)) {
            $query .= " OR vtiger_cntactivityrel.contactid IN (".$entityIds."))";
        } else {
            $query .= ")";
        }
        // There could be more than one contact for an activity.
        $query .= ' GROUP BY vtiger_activity.activityid';

        $return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);
        if ($return_value == null) {
            $return_value = array();
        }
        $return_value['CUSTOM_BUTTON'] = $button;

        $log->debug("Exiting get_activities method ...");
        return $return_value;
    }

    /**
     * Function to get Account related Task & Event which have activity type Held, Completed or Deferred.
     * @param  integer   $id      - accountid
     * returns related Task or Event record in array format
     */
    public function get_history($id)
    {
        global $log;
        $log->debug("Entering get_history(".$id.") method ...");

        $entityIds = $this->getRelatedContactsIds();
        $entityIds = implode(',', $entityIds);

        $userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');

        $query = "SELECT DISTINCT(vtiger_activity.activityid), vtiger_activity.subject, vtiger_activity.status, vtiger_activity.eventstatus,
				vtiger_activity.activitytype, vtiger_activity.date_start, vtiger_activity.due_date, vtiger_activity.time_start, vtiger_activity.time_end,
				vtiger_crmentity.modifiedtime, vtiger_crmentity.createdtime, vtiger_crmentity.description,
				case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name
				FROM vtiger_activity
				INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_activity.activityid
				LEFT JOIN vtiger_seactivityrel ON vtiger_seactivityrel.activityid = vtiger_activity.activityid
				LEFT JOIN vtiger_cntactivityrel ON vtiger_cntactivityrel.activityid = vtiger_activity.activityid
				LEFT JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_cntactivityrel.contactid
				LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
				LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_crmentity.smownerid
				WHERE (vtiger_activity.activitytype != 'Emails')
				AND (vtiger_activity.status = 'Completed'
					OR vtiger_activity.status = 'Deferred'
					OR (vtiger_activity.eventstatus = 'Held' AND vtiger_activity.eventstatus != ''))
				AND vtiger_crmentity.deleted = 0 AND (vtiger_seactivityrel.crmid = $id";

        if (!empty($entityIds)) {
            $query .= " OR vtiger_cntactivityrel.contactid IN (".$entityIds."))";
        } else {
            $query .= ")";
        }

        //Don't add order by, because, for security, one more condition will be added with this query in include/RelatedListView.php
        $log->debug("Exiting get_history method ...");
        return getHistory('Accounts', $query, $id);
    }

    /** Returns a list of the associated emails
     * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc..
     * All Rights Reserved..
     * Contributor(s): ______________________________________..
    */
    public function get_emails($id, $cur_tab_id, $rel_tab_id, $actions=false)
    {
        global $log, $singlepane_view,$currentModule,$current_user, $adb;
        $log->debug("Entering get_emails(".$id.") method ...");
        $this_module = $currentModule;

        $related_module = vtlib_getModuleNameById($rel_tab_id);
        require_once("modules/$related_module/$related_module.php");
        $other = new $related_module();
        vtlib_setup_modulevars($related_module, $other);
        $singular_modname = vtlib_toSingular($related_module);

        $parenttab = getParentTab();

        if ($singlepane_view == 'true') {
            $returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
        } else {
            $returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;
        }

        $button = '';

        $button .= '<input type="hidden" name="email_directing_module"><input type="hidden" name="record">';

        if ($actions) {
            if (is_string($actions)) {
                $actions = explode(',', strtoupper($actions));
            }
            if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
                $button .= "<input title='". getTranslatedString('LBL_ADD_NEW')." ". getTranslatedString($singular_modname)."' accessyKey='F' class='crmbutton small create' onclick='fnvshobj(this,\"sendmail_cont\");sendmail(\"$this_module\",$id);' type='button' name='button' value='". getTranslatedString('LBL_ADD_NEW')." ". getTranslatedString($singular_modname)."'></td>";
            }
        }

        $entityIds = $this->getRelatedContactsIds();
        array_push($entityIds, $id);
        $entityIds = implode(',', $entityIds);

        $userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');

        $query = "SELECT case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name,
			vtiger_activity.activityid, vtiger_activity.subject, vtiger_activity.activitytype, vtiger_crmentity.modifiedtime,
			vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_activity.date_start,vtiger_activity.time_start, vtiger_seactivityrel.crmid as parent_id
			FROM vtiger_activity, vtiger_seactivityrel, vtiger_account, vtiger_users, vtiger_emaildetails, vtiger_email_track, vtiger_crmentity
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid=vtiger_crmentity.smownerid
			WHERE vtiger_seactivityrel.activityid = vtiger_activity.activityid
				AND vtiger_seactivityrel.crmid IN (".$entityIds.")
				AND vtiger_users.id=vtiger_crmentity.smownerid
				AND vtiger_crmentity.crmid = vtiger_activity.activityid
				AND vtiger_crmentity.crmid = vtiger_emaildetails.emailid
				AND vtiger_crmentity.crmid = vtiger_email_track.mailid
				AND vtiger_activity.activitytype='Emails'
				AND vtiger_account.accountid = ".$id."
				AND vtiger_crmentity.deleted = 0";

        $return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

        if ($return_value == null) {
            $return_value = array();
        }
        $return_value['CUSTOM_BUTTON'] = $button;

        $log->debug("Exiting get_emails method ...");
        return $return_value;
    }


    /**
    * Function to get Account related Quotes
    * @param  integer   $id      - accountid
    * returns related Quotes record in array format
    */
    public function get_quotes($id, $cur_tab_id, $rel_tab_id, $actions=false)
    {
        global $log, $singlepane_view,$currentModule,$current_user;
        $log->debug("Entering get_quotes(".$id.") method ...");
        $this_module = $currentModule;

        $related_module = vtlib_getModuleNameById($rel_tab_id);
        require_once("modules/$related_module/$related_module.php");
        $other = new $related_module();
        vtlib_setup_modulevars($related_module, $other);
        $singular_modname = vtlib_toSingular($related_module);

        $parenttab = getParentTab();

        if ($singlepane_view == 'true') {
            $returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
        } else {
            $returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;
        }

        $button = '';

        if ($actions && getFieldVisibilityPermission($related_module, $current_user->id, 'account_id', 'readwrite') == '0') {
            if (is_string($actions)) {
                $actions = explode(',', strtoupper($actions));
            }
            if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
                $button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
            }
            if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
                $button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". getTranslatedString($singular_modname) ."' class='crmbutton small create'" .
                    " onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
                    " value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString($singular_modname) ."'>&nbsp;";
            }
        }

        $entityIds = $this->getRelatedContactsIds();
        $entityIds = implode(',', $entityIds);

        $userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');

        $query = "SELECT case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name,
				vtiger_crmentity.*, vtiger_quotes.*, vtiger_potential.potentialname, vtiger_account.accountname
				FROM vtiger_quotes
				INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_quotes.quoteid
				LEFT OUTER JOIN vtiger_account ON vtiger_account.accountid = vtiger_quotes.accountid
				LEFT OUTER JOIN vtiger_potential ON vtiger_potential.potentialid = vtiger_quotes.potentialid
				LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
                LEFT JOIN vtiger_quotescf ON vtiger_quotescf.quoteid = vtiger_quotes.quoteid
				LEFT JOIN vtiger_quotesbillads ON vtiger_quotesbillads.quotebilladdressid = vtiger_quotes.quoteid
				LEFT JOIN vtiger_quotesshipads ON vtiger_quotesshipads.quoteshipaddressid = vtiger_quotes.quoteid
				LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id
				WHERE vtiger_crmentity.deleted = 0 AND (vtiger_account.accountid = $id";

        if (!empty($entityIds)) {
            $query .= " OR vtiger_quotes.contactid IN (".$entityIds."))";
        } else {
            $query .= ")";
        }

        $return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

        if ($return_value == null) {
            $return_value = array();
        }
        $return_value['CUSTOM_BUTTON'] = $button;

        $log->debug("Exiting get_quotes method ...");
        return $return_value;
    }
    /**
    * Function to get Account related Invoices
    * @param  integer   $id      - accountid
    * returns related Invoices record in array format
    */
    public function get_invoices($id, $cur_tab_id, $rel_tab_id, $actions=false)
    {
        global $log, $singlepane_view,$currentModule,$current_user;
        $log->debug("Entering get_invoices(".$id.") method ...");
        $this_module = $currentModule;

        $related_module = vtlib_getModuleNameById($rel_tab_id);
        require_once("modules/$related_module/$related_module.php");
        $other = new $related_module();
        vtlib_setup_modulevars($related_module, $other);
        $singular_modname = vtlib_toSingular($related_module);

        $parenttab = getParentTab();

        if ($singlepane_view == 'true') {
            $returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
        } else {
            $returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;
        }

        $button = '';

        if ($actions && getFieldVisibilityPermission($related_module, $current_user->id, 'account_id', 'readwrite') == '0') {
            if (is_string($actions)) {
                $actions = explode(',', strtoupper($actions));
            }
            if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
                $button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
            }
            if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
                $button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". getTranslatedString($singular_modname) ."' class='crmbutton small create'" .
                    " onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
                    " value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString($singular_modname) ."'>&nbsp;";
            }
        }

        $entityIds = $this->getRelatedContactsIds();
        $entityIds = implode(',', $entityIds);

        $userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');

        $query = "SELECT case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name,
				vtiger_crmentity.*, vtiger_invoice.*, vtiger_account.accountname, vtiger_salesorder.subject AS salessubject
				FROM vtiger_invoice
				INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_invoice.invoiceid
				LEFT OUTER JOIN vtiger_account ON vtiger_account.accountid = vtiger_invoice.accountid
				LEFT OUTER JOIN vtiger_salesorder ON vtiger_salesorder.salesorderid = vtiger_invoice.salesorderid
                LEFT JOIN vtiger_invoicecf ON vtiger_invoicecf.invoiceid = vtiger_invoice.invoiceid
				LEFT JOIN vtiger_invoicebillads ON vtiger_invoicebillads.invoicebilladdressid = vtiger_invoice.invoiceid
				LEFT JOIN vtiger_invoiceshipads ON vtiger_invoiceshipads.invoiceshipaddressid = vtiger_invoice.invoiceid
				LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
				LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id
				WHERE vtiger_crmentity.deleted = 0 AND (vtiger_invoice.accountid = $id";

        if (!empty($entityIds)) {
            $query .= " OR vtiger_invoice.contactid IN (".$entityIds."))";
        } else {
            $query .= ")";
        }

        $return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

        if ($return_value == null) {
            $return_value = array();
        }
        $return_value['CUSTOM_BUTTON'] = $button;

        $log->debug("Exiting get_invoices method ...");
        return $return_value;
    }

    /**
    * Function to get Account related SalesOrder
    * @param  integer   $id      - accountid
    * returns related SalesOrder record in array format
    */
    public function get_salesorder($id, $cur_tab_id, $rel_tab_id, $actions=false)
    {
        global $log, $singlepane_view,$currentModule,$current_user;
        $log->debug("Entering get_salesorder(".$id.") method ...");
        $this_module = $currentModule;

        $related_module = vtlib_getModuleNameById($rel_tab_id);
        require_once("modules/$related_module/$related_module.php");
        $other = new $related_module();
        vtlib_setup_modulevars($related_module, $other);
        $singular_modname = vtlib_toSingular($related_module);

        $parenttab = getParentTab();

        if ($singlepane_view == 'true') {
            $returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
        } else {
            $returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;
        }

        $button = '';

        if ($actions && getFieldVisibilityPermission($related_module, $current_user->id, 'account_id', 'readwrite') == '0') {
            if (is_string($actions)) {
                $actions = explode(',', strtoupper($actions));
            }
            if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
                $button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
            }
            if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
                $button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". getTranslatedString($singular_modname) ."' class='crmbutton small create'" .
                    " onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
                    " value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString($singular_modname) ."'>&nbsp;";
            }
        }

        $entityIds = $this->getRelatedContactsIds();
        $entityIds = implode(',', $entityIds);

        $userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');

        $query = "SELECT vtiger_crmentity.*, vtiger_salesorder.*, vtiger_quotes.subject AS quotename, vtiger_account.accountname,
				case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name
				FROM vtiger_salesorder
				INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_salesorder.salesorderid
				LEFT OUTER JOIN vtiger_quotes ON vtiger_quotes.quoteid = vtiger_salesorder.quoteid
				LEFT OUTER JOIN vtiger_account ON vtiger_account.accountid = vtiger_salesorder.accountid
				LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
                LEFT JOIN vtiger_invoice_recurring_info ON vtiger_invoice_recurring_info.start_period = vtiger_salesorder.salesorderid
                LEFT JOIN vtiger_salesordercf ON vtiger_salesordercf.salesorderid = vtiger_salesorder.salesorderid
				LEFT JOIN vtiger_sobillads ON vtiger_sobillads.sobilladdressid = vtiger_salesorder.salesorderid
				LEFT JOIN vtiger_soshipads ON vtiger_soshipads.soshipaddressid = vtiger_salesorder.salesorderid
				LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id
				WHERE vtiger_crmentity.deleted = 0 AND (vtiger_salesorder.accountid = $id";

        if (!empty($entityIds)) {
            $query .= " OR vtiger_salesorder.contactid IN (".$entityIds."))";
        } else {
            $query .= ")";
        }

        $return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

        if ($return_value == null) {
            $return_value = array();
        }
        $return_value['CUSTOM_BUTTON'] = $button;

        $log->debug("Exiting get_salesorder method ...");
        return $return_value;
    }
    /**
    * Function to get Account related Tickets
    * @param  integer   $id      - accountid
    * returns related Ticket record in array format
    */
    public function get_tickets($id, $cur_tab_id, $rel_tab_id, $actions=false)
    {
        global $log, $singlepane_view,$currentModule,$current_user;
        $log->debug("Entering get_tickets(".$id.") method ...");
        $this_module = $currentModule;

        $related_module = vtlib_getModuleNameById($rel_tab_id);
        require_once("modules/$related_module/$related_module.php");
        $other = new $related_module();
        vtlib_setup_modulevars($related_module, $other);
        $singular_modname = vtlib_toSingular($related_module);

        $parenttab = getParentTab();

        if ($singlepane_view == 'true') {
            $returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
        } else {
            $returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;
        }

        $button = '';

        if ($actions && getFieldVisibilityPermission($related_module, $current_user->id, 'parent_id', 'readwrite') == '0') {
            if (is_string($actions)) {
                $actions = explode(',', strtoupper($actions));
            }
            if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
                $button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
            }
            if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
                $button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". getTranslatedString($singular_modname) ."' class='crmbutton small create'" .
                    " onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
                    " value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString($singular_modname) ."'>&nbsp;";
            }
        }

        $entityIds = $this->getRelatedContactsIds($id);
        $entityIds = implode(',', $entityIds);

        $userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');

        $query = "SELECT case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name, vtiger_users.id,
				vtiger_troubletickets.title, vtiger_troubletickets.ticketid AS crmid, vtiger_troubletickets.status, vtiger_troubletickets.priority,
				vtiger_troubletickets.parent_id, vtiger_troubletickets.contact_id, vtiger_troubletickets.ticket_no, vtiger_crmentity.smownerid, vtiger_crmentity.modifiedtime
				FROM vtiger_troubletickets
				INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_troubletickets.ticketid
				LEFT JOIN vtiger_ticketcf ON vtiger_troubletickets.ticketid = vtiger_ticketcf.ticketid
				LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_crmentity.smownerid
				LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
				WHERE  vtiger_crmentity.deleted = 0 and (vtiger_troubletickets.parent_id = $id";

        if (!empty($entityIds)) {
            $query .= " OR vtiger_troubletickets.contact_id IN (".$entityIds."))";
        } else {
            $query .= ")";
        }
        $return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

        if ($return_value == null) {
            $return_value = array();
        }
        $return_value['CUSTOM_BUTTON'] = $button;

        $log->debug("Exiting get_tickets method ...");
        return $return_value;
    }
    /**
    * Function to get Account related Products
    * @param  integer   $id      - accountid
    * returns related Products record in array format
    */
    public function get_products($id, $cur_tab_id, $rel_tab_id, $actions=false)
    {
        global $log, $singlepane_view,$currentModule,$current_user;
        $log->debug("Entering get_products(".$id.") method ...");
        $this_module = $currentModule;

        $related_module = vtlib_getModuleNameById($rel_tab_id);
        require_once("modules/$related_module/$related_module.php");
        $other = new $related_module();
        vtlib_setup_modulevars($related_module, $other);
        $singular_modname = vtlib_toSingular($related_module);

        $parenttab = getParentTab();

        if ($singlepane_view == 'true') {
            $returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
        } else {
            $returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;
        }

        $button = '';

        if ($actions) {
            if (is_string($actions)) {
                $actions = explode(',', strtoupper($actions));
            }
            if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
                $button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
            }
            if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
                $button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". getTranslatedString($singular_modname) ."' class='crmbutton small create'" .
                    " onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
                    " value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString($singular_modname) ."'>&nbsp;";
            }
        }

        $entityIds = $this->getRelatedContactsIds();
        array_push($entityIds, $id);
        $entityIds = implode(',', $entityIds);

        $query = "SELECT vtiger_products.productid, vtiger_products.productname, vtiger_products.productcode, vtiger_products.commissionrate,
				vtiger_products.qty_per_unit, vtiger_products.unit_price, vtiger_crmentity.crmid, vtiger_crmentity.smownerid
				FROM vtiger_products
				INNER JOIN vtiger_seproductsrel ON vtiger_products.productid = vtiger_seproductsrel.productid
				and vtiger_seproductsrel.setype IN ('Accounts', 'Contacts')
				INNER JOIN vtiger_productcf ON vtiger_products.productid = vtiger_productcf.productid
				INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_products.productid
				LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_crmentity.smownerid
				LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
				WHERE vtiger_crmentity.deleted = 0 AND vtiger_seproductsrel.crmid IN (".$entityIds.")";

        $return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

        if ($return_value == null) {
            $return_value = array();
        }
        $return_value['CUSTOM_BUTTON'] = $button;

        $log->debug("Exiting get_products method ...");
        return $return_value;
    }

    /** Function to export the account records in CSV Format
    * @param reference variable - where condition is passed when the query is executed
    * Returns Export Accounts Query.
    */
    public function create_export_query($where)
    {
        global $log;
        global $current_user;
        $log->debug("Entering create_export_query(".$where.") method ...");

        include("include/utils/ExportUtils.php");

        //To get the Permitted fields query and the permitted fields list
        $sql = getPermittedFieldsQuery("Accounts", "detail_view");
        $fields_list = getFieldsListFromQuery($sql);

        $query = "SELECT $fields_list,case when (vtiger_users.user_name not like '') then vtiger_users.user_name else vtiger_groups.groupname end as user_name
	       			FROM ".$this->entity_table."
				INNER JOIN vtiger_account
					ON vtiger_account.accountid = vtiger_crmentity.crmid
				LEFT JOIN vtiger_accountbillads
					ON vtiger_accountbillads.accountaddressid = vtiger_account.accountid
				LEFT JOIN vtiger_accountshipads
					ON vtiger_accountshipads.accountaddressid = vtiger_account.accountid
				LEFT JOIN vtiger_accountscf
					ON vtiger_accountscf.accountid = vtiger_account.accountid
	                        LEFT JOIN vtiger_groups
                        	        ON vtiger_groups.groupid = vtiger_crmentity.smownerid
				LEFT JOIN vtiger_users
					ON vtiger_users.id = vtiger_crmentity.smownerid and vtiger_users.status = 'Active'
				LEFT JOIN vtiger_account vtiger_account2
					ON vtiger_account2.accountid = vtiger_account.parentid
				";//vtiger_account2 is added to get the Member of account

        $query .= $this->getNonAdminAccessControlQuery('Accounts', $current_user);
        $where_auto = " vtiger_crmentity.deleted = 0 ";

        if ($where != "") {
            $query .= " WHERE ($where) AND ".$where_auto;
        } else {
            $query .= " WHERE ".$where_auto;
        }

        $log->debug("Exiting create_export_query method ...");
        return $query;
    }

    /** Function to get the Columnnames of the Account Record
    * Used By vtigerCRM Word Plugin
    * Returns the Merge Fields for Word Plugin
    */
    public function getColumnNames_Acnt()
    {
        global $log,$current_user;
        $log->debug("Entering getColumnNames_Acnt() method ...");
        require ('include/utils/LoadUserPrivileges.php');
        if ($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0) {
            $sql1 = "SELECT fieldlabel FROM vtiger_field WHERE tabid = 6 and vtiger_field.presence in (0,2)";
            $params1 = array();
        } else {
            $profileList = getCurrentUserProfileList();
            $sql1 = "select vtiger_field.fieldid,fieldlabel from vtiger_field INNER JOIN vtiger_profile2field on vtiger_profile2field.fieldid=vtiger_field.fieldid inner join vtiger_def_org_field on vtiger_def_org_field.fieldid=vtiger_field.fieldid where vtiger_field.tabid=6 and vtiger_field.displaytype in (1,2,4) and vtiger_profile2field.visible=0 and vtiger_def_org_field.visible=0 and vtiger_field.presence in (0,2)";
            $params1 = array();
            if (count($profileList) > 0) {
                $sql1 .= " and vtiger_profile2field.profileid in (". generateQuestionMarks($profileList) .")  group by fieldid";
                array_push($params1,  $profileList);
            }
        }
        $result = $this->db->pquery($sql1, $params1);
        $numRows = $this->db->num_rows($result);
        for ($i=0; $i < $numRows;$i++) {
            $custom_fields[$i] = $this->db->query_result($result, $i, "fieldlabel");
            $custom_fields[$i] = preg_replace("/\s+/", "", $custom_fields[$i]);
            $custom_fields[$i] = strtoupper($custom_fields[$i]);
        }
        $mergeflds = $custom_fields;
        $log->debug("Exiting getColumnNames_Acnt method ...");
        return $mergeflds;
    }

    /**
     * Move the related records of the specified list of id's to the given record.
     * @param String This module name
     * @param Array List of Entity Id's from which related records need to be transfered
     * @param Integer Id of the the Record to which the related records are to be moved
     */
    public function transferRelatedRecords($module, $transferEntityIds, $entityId)
    {
        global $adb,$log;
        $log->debug("Entering function transferRelatedRecords ($module, $transferEntityIds, $entityId)");

        $rel_table_arr = array("Contacts"=>"vtiger_contactdetails","Potentials"=>"vtiger_potential","Quotes"=>"vtiger_quotes",
                    "SalesOrder"=>"vtiger_salesorder","Invoice"=>"vtiger_invoice","Activities"=>"vtiger_seactivityrel",
                    "Documents"=>"vtiger_senotesrel","Attachments"=>"vtiger_seattachmentsrel","HelpDesk"=>"vtiger_troubletickets",
                    "Products"=>"vtiger_seproductsrel","ServiceContracts"=>"vtiger_servicecontracts","Campaigns"=>"vtiger_campaignaccountrel",
                    "Assets"=>"vtiger_assets","Project"=>"vtiger_project");

        $tbl_field_arr = array("vtiger_contactdetails"=>"contactid","vtiger_potential"=>"potentialid","vtiger_quotes"=>"quoteid",
                    "vtiger_salesorder"=>"salesorderid","vtiger_invoice"=>"invoiceid","vtiger_seactivityrel"=>"activityid",
                    "vtiger_senotesrel"=>"notesid","vtiger_seattachmentsrel"=>"attachmentsid","vtiger_troubletickets"=>"ticketid",
                    "vtiger_seproductsrel"=>"productid","vtiger_servicecontracts"=>"servicecontractsid","vtiger_campaignaccountrel"=>"campaignid",
                    "vtiger_assets"=>"assetsid","vtiger_project"=>"projectid","vtiger_payments"=>"paymentsid");

        $entity_tbl_field_arr = array("vtiger_contactdetails"=>"accountid","vtiger_potential"=>"related_to","vtiger_quotes"=>"accountid",
                    "vtiger_salesorder"=>"accountid","vtiger_invoice"=>"accountid","vtiger_seactivityrel"=>"crmid",
                    "vtiger_senotesrel"=>"crmid","vtiger_seattachmentsrel"=>"crmid","vtiger_troubletickets"=>"parent_id",
                    "vtiger_seproductsrel"=>"crmid","vtiger_servicecontracts"=>"sc_related_to","vtiger_campaignaccountrel"=>"accountid",
                    "vtiger_assets"=>"account","vtiger_project"=>"linktoaccountscontacts","vtiger_payments"=>"relatedorganization");

        foreach ($transferEntityIds as $transferId) {
            foreach ($rel_table_arr as $rel_module=>$rel_table) {
                $id_field = $tbl_field_arr[$rel_table];
                $entity_id_field = $entity_tbl_field_arr[$rel_table];
                // IN clause to avoid duplicate entries
                $sel_result =  $adb->pquery("select $id_field from $rel_table where $entity_id_field=? " .
                        " and $id_field not in (select $id_field from $rel_table where $entity_id_field=?)",
                        array($transferId, $entityId));
                $res_cnt = $adb->num_rows($sel_result);
                if ($res_cnt > 0) {
                    for ($i=0;$i<$res_cnt;$i++) {
                        $id_field_value = $adb->query_result($sel_result, $i, $id_field);
                        $adb->pquery("update $rel_table set $entity_id_field=? where $entity_id_field=? and $id_field=?",
                            array($entityId, $transferId, $id_field_value));
                    }
                }
            }
        }
        parent::transferRelatedRecords($module, $transferEntityIds, $entityId);
        $log->debug("Exiting transferRelatedRecords...");
    }

    /*
     * Function to get the relation tables for related modules
     * @param - $secmodule secondary module name
     * returns the array with table names and fieldnames storing relations between module and this module
     */
    public function setRelationTables($secmodule)
    {
        $rel_tables =  array(
            "Contacts" => array("vtiger_contactdetails"=>array("accountid", "contactid"), "vtiger_account"=>"accountid"),
            "Potentials" => array("vtiger_potential"=>array("related_to", "potentialid"), "vtiger_account"=>"accountid"),
            "Quotes" => array("vtiger_quotes"=>array("accountid", "quoteid"), "vtiger_account"=>"accountid"),
            "SalesOrder" => array("vtiger_salesorder"=>array("accountid", "salesorderid"), "vtiger_account"=>"accountid"),
            "Invoice" => array("vtiger_invoice"=>array("accountid", "invoiceid"), "vtiger_account"=>"accountid"),
            "Calendar" => array("vtiger_seactivityrel"=>array("crmid", "activityid"), "vtiger_account"=>"accountid"),
            "HelpDesk" => array("vtiger_troubletickets"=>array("parent_id", "ticketid"), "vtiger_account"=>"accountid"),
            "Products" => array("vtiger_seproductsrel"=>array("crmid", "productid"), "vtiger_account"=>"accountid"),
            "Documents" => array("vtiger_senotesrel"=>array("crmid", "notesid"), "vtiger_account"=>"accountid"),
            "Campaigns" => array("vtiger_campaignaccountrel"=>array("accountid", "campaignid"), "vtiger_account"=>"accountid"),
            "Emails" => array("vtiger_seactivityrel"=>array("crmid", "activityid"), "vtiger_account"=>"accountid"),
        );
        return $rel_tables[$secmodule];
    }

    /*
     * Function to get the secondary query part of a report
     * @param - $module primary module name
     * @param - $secmodule secondary module name
     * returns the query string formed on fetching the related data for report for secondary module
     */
    public function generateReportsSecQuery($module, $secmodule, $queryPlanner)
    {
        $matrix = $queryPlanner->newDependencyMatrix();
        $matrix->setDependency('vtiger_crmentityAccounts', array('vtiger_groupsAccounts', 'vtiger_usersAccounts', 'vtiger_lastModifiedByAccounts'));
        $matrix->setDependency('vtiger_account', array('vtiger_crmentityAccounts', ' vtiger_accountbillads', 'vtiger_accountshipads', 'vtiger_accountscf', 'vtiger_accountAccounts', 'vtiger_email_trackAccounts'));

        if (!$queryPlanner->requireTable('vtiger_account', $matrix)) {
            return '';
        }

         // Activities related to contact should linked to accounts if contact is related to that account
        if ($module == "Calendar") {
            // query to get all the contacts related to Accounts
            $relContactsQuery = "SELECT contactid FROM vtiger_contactdetails as vtiger_tmpContactCalendar
                        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_tmpContactCalendar.contactid
                        WHERE vtiger_tmpContactCalendar.accountid IS NOT NULL AND vtiger_tmpContactCalendar.accountid !=''
                        AND vtiger_crmentity.deleted=0";

            $query = " left join vtiger_cntactivityrel as vtiger_tmpcntactivityrel ON
                vtiger_activity.activityid = vtiger_tmpcntactivityrel.activityid AND
                vtiger_tmpcntactivityrel.contactid IN ($relContactsQuery)
                left join vtiger_contactdetails as vtiger_tmpcontactdetails on vtiger_tmpcntactivityrel.contactid = vtiger_tmpcontactdetails.contactid ";
        } else {
            $query = "";
        }

        $query .= $this->getRelationQuery($module, $secmodule, "vtiger_account", "accountid", $queryPlanner);

        if ($module == "Calendar") {
            $query .= " OR vtiger_account.accountid = vtiger_tmpcontactdetails.accountid " ;
        }
        // End

        if ($queryPlanner->requireTable('vtiger_crmentityAccounts', $matrix)) {
            $query .= " left join vtiger_crmentity as vtiger_crmentityAccounts on vtiger_crmentityAccounts.crmid=vtiger_account.accountid and vtiger_crmentityAccounts.deleted=0";
        }
        if ($queryPlanner->requireTable('vtiger_accountbillads')) {
            $query .= " left join vtiger_accountbillads on vtiger_account.accountid=vtiger_accountbillads.accountaddressid";
        }
        if ($queryPlanner->requireTable('vtiger_accountshipads')) {
            $query .= " left join vtiger_accountshipads on vtiger_account.accountid=vtiger_accountshipads.accountaddressid";
        }
        if ($queryPlanner->requireTable('vtiger_accountscf')) {
            $query .= " left join vtiger_accountscf on vtiger_account.accountid = vtiger_accountscf.accountid";
        }
        if ($queryPlanner->requireTable('vtiger_accountAccounts', $matrix)) {
            $query .= "	left join vtiger_account as vtiger_accountAccounts on vtiger_accountAccounts.accountid = vtiger_account.parentid";
        }
        if ($queryPlanner->requireTable('vtiger_email_track')) {
            $query .= " LEFT JOIN vtiger_email_track AS vtiger_email_trackAccounts ON vtiger_email_trackAccounts .crmid = vtiger_account.accountid";
        }
        if ($queryPlanner->requireTable('vtiger_groupsAccounts')) {
            $query .= "	left join vtiger_groups as vtiger_groupsAccounts on vtiger_groupsAccounts.groupid = vtiger_crmentityAccounts.smownerid";
        }
        if ($queryPlanner->requireTable('vtiger_usersAccounts')) {
            $query .= " left join vtiger_users as vtiger_usersAccounts on vtiger_usersAccounts.id = vtiger_crmentityAccounts.smownerid";
        }
        if ($queryPlanner->requireTable('vtiger_lastModifiedByAccounts')) {
            $query .= " left join vtiger_users as vtiger_lastModifiedByAccounts on vtiger_lastModifiedByAccounts.id = vtiger_crmentityAccounts.modifiedby ";
        }
        if ($queryPlanner->requireTable("vtiger_createdbyAccounts")) {
            $query .= " left join vtiger_users as vtiger_createdbyAccounts on vtiger_createdbyAccounts.id = vtiger_crmentityAccounts.smcreatorid ";
        }

        return $query;
    }

    /**
    * Function to get Account hierarchy of the given Account
    * @param  integer   $id      - accountid
    * returns Account hierarchy in array format
    */
    public function getAccountHierarchy($id)
    {
        global $log, $adb, $current_user;
        $log->debug("Entering getAccountHierarchy(".$id.") method ...");
        require ('include/utils/LoadUserPrivileges.php');

        $tabname = getParentTab();
        $listview_header = array();
        $listview_entries = array();

        foreach ($this->list_fields_name as $fieldname=>$colname) {
            if (getFieldVisibilityPermission('Accounts', $current_user->id, $colname) == '0') {
                $listview_header[] = getTranslatedString($fieldname);
            }
        }

        $accounts_list = array();

        // Get the accounts hierarchy from the top most account in the hierarch of the current account, including the current account
        $encountered_accounts = array($id);
        $accounts_list = $this->__getParentAccounts($id, $accounts_list, $encountered_accounts);

        // Get the accounts hierarchy (list of child accounts) based on the current account
        $accounts_list = $this->__getChildAccounts($id, $accounts_list, $accounts_list[$id]['depth']);

        // Create array of all the accounts in the hierarchy
        foreach ($accounts_list as $account_id => $account_info) {
            $account_info_data = array();

            $hasRecordViewAccess = (is_admin($current_user)) || (isPermitted('Accounts', 'DetailView', $account_id) == 'yes');

            foreach ($this->list_fields_name as $fieldname=>$colname) {
                // Permission to view account is restricted, avoid showing field values (except account name)
                if (!$hasRecordViewAccess && $colname != 'accountname') {
                    $account_info_data[] = '';
                } elseif (getFieldVisibilityPermission('Accounts', $current_user->id, $colname) == '0') {
                    $data = $account_info[$colname];
                    if ($colname == 'accountname') {
                        if ($account_id != $id) {
                            if ($hasRecordViewAccess) {
                                $data = '<a href="index.php?module=Accounts&action=DetailView&record='.$account_id.'&parenttab='.$tabname.'">'.$data.'</a>';
                            } else {
                                $data = '<i>'.$data.'</i>';
                            }
                        } else {
                            $data = '<b>'.$data.'</b>';
                        }
                        // - to show the hierarchy of the Accounts
                        $account_depth = str_repeat(" .. ", $account_info['depth'] * 2);
                        $data = $account_depth . $data;
                    } elseif ($colname == 'website') {
                        $data = '<a href="http://'. $data .'" target="_blank">'.$data.'</a>';
                    }
                    $account_info_data[] = $data;
                }
            }
            $listview_entries[$account_id] = $account_info_data;
        }

        $account_hierarchy = array('header'=>$listview_header,'entries'=>$listview_entries);
        $log->debug("Exiting getAccountHierarchy method ...");
        return $account_hierarchy;
    }

    /**
    * Function to Recursively get all the upper accounts of a given Account
    * @param  integer   $id      		- accountid
    * @param  array   $parent_accounts   - Array of all the parent accounts
    * returns All the parent accounts of the given accountid in array format
    */
    public function __getParentAccounts($id, &$parent_accounts, &$encountered_accounts)
    {
        global $log, $adb;
        $log->debug("Entering __getParentAccounts(".$id.",".$parent_accounts.") method ...");

        $query = "SELECT parentid FROM vtiger_account " .
                " INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_account.accountid" .
                " WHERE vtiger_crmentity.deleted = 0 and vtiger_account.accountid = ?";
        $params = array($id);

        $res = $adb->pquery($query, $params);

        if ($adb->num_rows($res) > 0 &&
            $adb->query_result($res, 0, 'parentid') != '' && $adb->query_result($res, 0, 'parentid') != 0 &&
            !in_array($adb->query_result($res, 0, 'parentid'), $encountered_accounts)) {
            $parentid = $adb->query_result($res, 0, 'parentid');
            $encountered_accounts[] = $parentid;
            $this->__getParentAccounts($parentid, $parent_accounts, $encountered_accounts);
        }

        $userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
                            'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
        $query = "SELECT vtiger_account.*, vtiger_accountbillads.*," .
                " CASE when (vtiger_users.user_name not like '') THEN $userNameSql ELSE vtiger_groups.groupname END as user_name " .
                " FROM vtiger_account" .
                " INNER JOIN vtiger_crmentity " .
                " ON vtiger_crmentity.crmid = vtiger_account.accountid" .
                " INNER JOIN vtiger_accountbillads" .
                " ON vtiger_account.accountid = vtiger_accountbillads.accountaddressid " .
                " LEFT JOIN vtiger_groups" .
                " ON vtiger_groups.groupid = vtiger_crmentity.smownerid" .
                " LEFT JOIN vtiger_users" .
                " ON vtiger_users.id = vtiger_crmentity.smownerid" .
                " WHERE vtiger_crmentity.deleted = 0 and vtiger_account.accountid = ?";
        $params = array($id);
        $res = $adb->pquery($query, $params);

        $parent_account_info = array();
        $depth = 0;
        $immediate_parentid = $adb->query_result($res, 0, 'parentid');
        if (isset($parent_accounts[$immediate_parentid])) {
            $depth = $parent_accounts[$immediate_parentid]['depth'] + 1;
        }
        $parent_account_info['depth'] = $depth;
        foreach ($this->list_fields_name as $fieldname=>$columnname) {
            if ($columnname == 'assigned_user_id') {
                $parent_account_info[$columnname] = $adb->query_result($res, 0, 'user_name');
            } else {
                $parent_account_info[$columnname] = $adb->query_result($res, 0, $columnname);
            }
        }
        $parent_accounts[$id] = $parent_account_info;
        $log->debug("Exiting __getParentAccounts method ...");
        return $parent_accounts;
    }

    /**
    * Function to Recursively get all the child accounts of a given Account
    * @param  integer   $id      		- accountid
    * @param  array   $child_accounts   - Array of all the child accounts
    * @param  integer   $depth          - Depth at which the particular account has to be placed in the hierarchy
    * returns All the child accounts of the given accountid in array format
    */
    public function __getChildAccounts($id, &$child_accounts, $depth)
    {
        global $log, $adb;
        $log->debug("Entering __getChildAccounts(".$id.",".$child_accounts.",".$depth.") method ...");

        $userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
                            'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
        $query = "SELECT vtiger_account.*, vtiger_accountbillads.*," .
                " CASE when (vtiger_users.user_name not like '') THEN $userNameSql ELSE vtiger_groups.groupname END as user_name " .
                " FROM vtiger_account" .
                " INNER JOIN vtiger_crmentity " .
                " ON vtiger_crmentity.crmid = vtiger_account.accountid" .
                " INNER JOIN vtiger_accountbillads" .
                " ON vtiger_account.accountid = vtiger_accountbillads.accountaddressid " .
                " LEFT JOIN vtiger_groups" .
                " ON vtiger_groups.groupid = vtiger_crmentity.smownerid" .
                " LEFT JOIN vtiger_users" .
                " ON vtiger_users.id = vtiger_crmentity.smownerid" .
                " WHERE vtiger_crmentity.deleted = 0 and parentid = ?";
        $params = array($id);
        $res = $adb->pquery($query, $params);

        $num_rows = $adb->num_rows($res);

        if ($num_rows > 0) {
            $depth = $depth + 1;
            for ($i=0;$i<$num_rows;$i++) {
                $child_acc_id = $adb->query_result($res, $i, 'accountid');
                if (array_key_exists($child_acc_id, $child_accounts)) {
                    continue;
                }
                $child_account_info = array();
                $child_account_info['depth'] = $depth;
                foreach ($this->list_fields_name as $fieldname=>$columnname) {
                    if ($columnname == 'assigned_user_id') {
                        $child_account_info[$columnname] = $adb->query_result($res, $i, 'user_name');
                    } else {
                        $child_account_info[$columnname] = $adb->query_result($res, $i, $columnname);
                    }
                }
                $child_accounts[$child_acc_id] = $child_account_info;
                $this->__getChildAccounts($child_acc_id, $child_accounts, $depth);
            }
        }
        $log->debug("Exiting __getChildAccounts method ...");
        return $child_accounts;
    }

    public function save_related_module($module, $crmid, $with_module, $with_crmids)
    {
        $adb = $this->db;

        if (!is_array($with_crmids)) {
            $with_crmids = array($with_crmids);
        }
        foreach ($with_crmids as $with_crmid) {
            if ($with_module == 'Products') {
                $adb->pquery("insert into vtiger_seproductsrel values(?,?,?)", array($crmid, $with_crmid, $module));
            } elseif ($with_module == 'Campaigns') {
                $checkResult = $adb->pquery('SELECT 1 FROM vtiger_campaignaccountrel WHERE campaignid = ? AND accountid = ?',
                                                array($with_crmid, $crmid));
                if ($checkResult && $adb->num_rows($checkResult) > 0) {
                    continue;
                }
                $adb->pquery("insert into vtiger_campaignaccountrel values(?,?,1)", array($with_crmid, $crmid));
            } else {
                parent::save_related_module($module, $crmid, $with_module, $with_crmid);
            }
        }
    }

    public function getListButtons($app_strings, $mod_strings = false)
    {
        $list_buttons = array();

        if (isPermitted('Accounts', 'Delete', '') == 'yes') {
            $list_buttons['del'] = $app_strings[LBL_MASS_DELETE];
        }
        if (isPermitted('Accounts', 'EditView', '') == 'yes') {
            $list_buttons['mass_edit'] = $app_strings[LBL_MASS_EDIT];
            $list_buttons['c_owner'] = $app_strings[LBL_CHANGE_OWNER];
        }
        if (isPermitted('Emails', 'EditView', '') == 'yes') {
            $list_buttons['s_mail'] = $app_strings[LBL_SEND_MAIL_BUTTON];
        }
        // mailer export
        if (isPermitted('Accounts', 'Export', '') == 'yes') {
            $list_buttons['mailer_exp'] = $mod_strings[LBL_MAILER_EXPORT];
        }
        // end of mailer export
        return $list_buttons;
    }

    /* Function to get attachments in the related list of accounts module */
    public function get_attachments($id, $cur_tab_id, $rel_tab_id, $actions = false)
    {
        global $currentModule, $app_strings, $singlepane_view;
        $this_module = $currentModule;
        $parenttab = getParentTab();

        $related_module = vtlib_getModuleNameById($rel_tab_id);
        $other = CRMEntity::getInstance($related_module);

        // Some standard module class doesn't have required variables
        // that are used in the query, they are defined in this generic API
        vtlib_setup_modulevars($related_module, $other);

        $singular_modname = vtlib_toSingular($related_module);
        $button = '';
        if ($actions) {
            if (is_string($actions)) {
                $actions = explode(',', strtoupper($actions));
            }
            if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
                $button .= "<input title='" . getTranslatedString('LBL_SELECT') . " " . getTranslatedString($related_module) . "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\" value='" . getTranslatedString('LBL_SELECT') . " " . getTranslatedString($related_module) . "'>&nbsp;";
            }
            if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
                $button .= "<input type='hidden' name='createmode' id='createmode' value='link' />" .
                        "<input title='" . getTranslatedString('LBL_ADD_NEW') . " " . getTranslatedString($singular_modname) . "' class='crmbutton small create'" .
                        " onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
                        " value='" . getTranslatedString('LBL_ADD_NEW') . " " . getTranslatedString($singular_modname) . "'>&nbsp;";
            }
        }

        // To make the edit or del link actions to return back to same view.
        if ($singlepane_view == 'true') {
            $returnset = "&return_module=$this_module&return_action=DetailView&return_id=$id";
        } else {
            $returnset = "&return_module=$this_module&return_action=CallRelatedList&return_id=$id";
        }

        $entityIds = $this->getRelatedContactsIds();
        array_push($entityIds, $id);
        $entityIds = implode(',', $entityIds);

        $userNameSql = getSqlForNameInDisplayFormat(array('first_name'=> 'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');

        $query = "SELECT case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name,
				'Documents' ActivityType,vtiger_attachments.type  FileType,crm2.modifiedtime lastmodified,vtiger_crmentity.modifiedtime,
				vtiger_seattachmentsrel.attachmentsid attachmentsid, vtiger_notes.notesid crmid, vtiger_notes.notecontent description,vtiger_notes.*
				from vtiger_notes
				INNER JOIN vtiger_senotesrel ON vtiger_senotesrel.notesid= vtiger_notes.notesid
				LEFT JOIN vtiger_notescf ON vtiger_notescf.notesid= vtiger_notes.notesid
				INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid= vtiger_notes.notesid and vtiger_crmentity.deleted=0
				INNER JOIN vtiger_crmentity crm2 ON crm2.crmid=vtiger_senotesrel.crmid
				LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
				LEFT JOIN vtiger_seattachmentsrel ON vtiger_seattachmentsrel.crmid =vtiger_notes.notesid
				LEFT JOIN vtiger_attachments ON vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid
				LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid= vtiger_users.id
				WHERE crm2.crmid IN (".$entityIds.")";

        $return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

        if ($return_value == null) {
            $return_value = array();
        }
        $return_value['CUSTOM_BUTTON'] = $button;
        return $return_value;
    }

    /**
     * Function to handle the dependents list for the module.
     * NOTE: UI type '10' is used to stored the references to other modules for a given record.
     * These dependent records can be retrieved through this function.
     * For eg: A trouble ticket can be related to an Account or a Contact.
     * From a given Contact/Account if we need to fetch all such dependent trouble tickets, get_dependents_list function can be used.
     */
    public function get_dependents_list($id, $cur_tab_id, $rel_tab_id, $actions = false)
    {
        global $currentModule, $app_strings, $singlepane_view, $current_user;

        $parenttab = getParentTab();

        $related_module = vtlib_getModuleNameById($rel_tab_id);
        $other = CRMEntity::getInstance($related_module);

        // Some standard module class doesn't have required variables
        // that are used in the query, they are defined in this generic API
        vtlib_setup_modulevars($currentModule, $this);
        vtlib_setup_modulevars($related_module, $other);

        $singular_modname = 'SINGLE_' . $related_module;
        $button = '';

        // To make the edit or del link actions to return back to same view.
        if ($singlepane_view == 'true') {
            $returnset = "&return_module=$currentModule&return_action=DetailView&return_id=$id";
        } else {
            $returnset = "&return_module=$currentModule&return_action=CallRelatedList&return_id=$id";
        }

        $return_value = null;
        $dependentFieldSql = $this->db->pquery("SELECT tabid, fieldname, columnname FROM vtiger_field WHERE uitype IN (10,73) AND" .
                " fieldid IN (SELECT fieldid FROM vtiger_fieldmodulerel WHERE relmodule=? AND module=?)", array($currentModule, $related_module));

        $dependentData = [];
        while ($row = $dependentFieldSql->fetchRow()) {
            $dependentData[] = ['column' => $row['columnname'], 'fieldname' => $row['fieldname']];
        }

        if (count($dependentData) > 0) {
            $dependentColumn = $dependentData[0]['column'];
            $dependentField = $dependentData[0]['field'];

            $button .= '<input type="hidden" name="' . $dependentColumn . '" id="' . $dependentColumn . '" value="' . $id . '">';
            $button .= '<input type="hidden" name="' . $dependentColumn . '_type" id="' . $dependentColumn . '_type" value="' . $currentModule . '">';
            if ($actions) {
                if (is_string($actions)) {
                    $actions = explode(',', strtoupper($actions));
                }
                if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes'
                        && getFieldVisibilityPermission($related_module, $current_user->id, $dependentField, 'readwrite') == '0') {
                    $button .= "<input title='" . getTranslatedString('LBL_ADD_NEW') . " " . getTranslatedString($singular_modname, $related_module) . "' class='crmbutton small create'" .
                            " onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
                            " value='" . getTranslatedString('LBL_ADD_NEW') . " " . getTranslatedString($singular_modname, $related_module) . "'>&nbsp;";
                }
            }

            $entityIds = $this->getRelatedContactsIds();
            array_push($entityIds, $id);
            $entityIds = implode(',', $entityIds);

            $userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');

            $query = "SELECT vtiger_crmentity.*, $other->table_name.*";
            $query .= ", CASE WHEN (vtiger_users.user_name NOT LIKE '') THEN $userNameSql ELSE vtiger_groups.groupname END AS user_name";

            $more_relation = '';
            if (!empty($other->related_tables)) {
                foreach ($other->related_tables as $tname => $relmap) {
                    $query .= ", $tname.*";

                    // Setup the default JOIN conditions if not specified
                    if (empty($relmap[1])) {
                        $relmap[1] = $other->table_name;
                    }
                    if (empty($relmap[2])) {
                        $relmap[2] = $relmap[0];
                    }
                    $more_relation .= " LEFT JOIN $tname ON $tname.$relmap[0] = $relmap[1].$relmap[2]";
                }
            }

            $query .= " FROM $other->table_name";
            $query .= " INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = $other->table_name.$other->table_index";
            $query .= $more_relation;
            $query .= " LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid";
            $query .= " LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid";
            $query .= " WHERE vtiger_crmentity.deleted = 0 AND ";
            $checkSql = [];
            foreach ($dependentData as $index => $data) {
                $checkSql[] = $data['column'] . " IN ($entityIds)";
            }
            $query .= '(' . implode(' OR ', $checkSql) . ')';

            $return_value = GetRelatedList($currentModule, $related_module, $other, $query, $button, $returnset);
        }
        if ($return_value == null) {
            $return_value = array();
        }
        $return_value['CUSTOM_BUTTON'] = $button;

        return $return_value;
    }

    /**
     * Function to handle the related list for the module.
     * NOTE: Vtiger_Module::setRelatedList sets reference to this function in vtiger_relatedlists table
     * if function name is not explicitly specified.
     */
    public function get_related_list($id, $cur_tab_id, $rel_tab_id, $actions = false)
    {
        global $currentModule, $app_strings, $singlepane_view;

        $parenttab = getParentTab();

        $related_module = vtlib_getModuleNameById($rel_tab_id);
        $other = CRMEntity::getInstance($related_module);

        // Some standard module class doesn't have required variables
        // that are used in the query, they are defined in this generic API
        vtlib_setup_modulevars($currentModule, $this);
        vtlib_setup_modulevars($related_module, $other);

        $singular_modname = 'SINGLE_' . $related_module;

        $button = '';
        if ($actions) {
            if (is_string($actions)) {
                $actions = explode(',', strtoupper($actions));
            }
            if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
                $button .= "<input title='" . getTranslatedString('LBL_SELECT') . " " . getTranslatedString($related_module) . "' class='crmbutton small edit' " .
                        " type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\"" .
                        " value='" . getTranslatedString('LBL_SELECT') . " " . getTranslatedString($related_module, $related_module) . "'>&nbsp;";
            }
            if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
                $button .= "<input type='hidden' name='createmode' id='createmode' value='link' />" .
                        "<input title='" . getTranslatedString('LBL_ADD_NEW') . " " . getTranslatedString($singular_modname) . "' class='crmbutton small create'" .
                        " onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
                        " value='" . getTranslatedString('LBL_ADD_NEW') . " " . getTranslatedString($singular_modname, $related_module) . "'>&nbsp;";
            }
        }

        // To make the edit or del link actions to return back to same view.
        if ($singlepane_view == 'true') {
            $returnset = "&return_module=$currentModule&return_action=DetailView&return_id=$id";
        } else {
            $returnset = "&return_module=$currentModule&return_action=CallRelatedList&return_id=$id";
        }

        $more_relation = '';
        if (!empty($other->related_tables)) {
            foreach ($other->related_tables as $tname => $relmap) {
                $query .= ", $tname.*";

                // Setup the default JOIN conditions if not specified
                if (empty($relmap[1])) {
                    $relmap[1] = $other->table_name;
                }
                if (empty($relmap[2])) {
                    $relmap[2] = $relmap[0];
                }
                $more_relation .= " LEFT JOIN $tname ON $tname.$relmap[0] = $relmap[1].$relmap[2]";
            }
        }

        $entityIds = $this->getRelatedContactsIds();
        array_push($entityIds, $id);
        $entityIds = implode(',', $entityIds);

        $userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');

        $query = "SELECT vtiger_crmentity.*, $other->table_name.*,
				CASE WHEN (vtiger_users.user_name NOT LIKE '') THEN $userNameSql ELSE vtiger_groups.groupname END AS user_name FROM $other->table_name
				INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = $other->table_name.$other->table_index
				INNER JOIN vtiger_crmentityrel ON (vtiger_crmentityrel.relcrmid = vtiger_crmentity.crmid OR vtiger_crmentityrel.crmid = vtiger_crmentity.crmid)
				$more_relation
				LEFT  JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
				LEFT  JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
				WHERE vtiger_crmentity.deleted = 0 AND (vtiger_crmentityrel.crmid IN (" .$entityIds. ") OR vtiger_crmentityrel.relcrmid IN (". $entityIds . "))";

        $return_value = GetRelatedList($currentModule, $related_module, $other, $query, $button, $returnset);

        if ($return_value == null) {
            $return_value = array();
        }
        $return_value['CUSTOM_BUTTON'] = $button;

        return $return_value;
    }

    /* Function to get related contact ids for an account record*/
    public function getRelatedContactsIds($id = null)
    {
        global $adb;
        if ($id ==null) {
            $id = $this->id;
        }
        $entityIds = array();
        $query = 'SELECT contactid FROM vtiger_contactdetails
				INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_contactdetails.contactid
				WHERE vtiger_contactdetails.accountid = ? AND vtiger_crmentity.deleted = 0';
        $accountContacts = $adb->pquery($query, array($id));
        $numOfContacts = $adb->num_rows($accountContacts);
        if ($accountContacts && $numOfContacts > 0) {
            for ($i=0; $i < $numOfContacts; ++$i) {
                array_push($entityIds, $adb->query_result($accountContacts, $i, 'contactid'));
            }
        }
        return $entityIds;
    }
}
