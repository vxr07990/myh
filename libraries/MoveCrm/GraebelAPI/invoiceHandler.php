<?php

namespace MoveCrm\GraebelAPI;

include_once('libraries/MoveCrm/GraebelAPI/orderHandler.php');
include_once('modules/Vtiger/models/Record.php');

use Carbon\Carbon;

/*
 * invoiceHandler extends the APIHandler to allow the createInvoice API call.
 * and all those things that need pulled in to do that call.
 *
 */

class invoiceHandler extends orderHandler
{
    private $trigger;
    protected $CREATE_INVOICE_URI = '/api/invoice/createInvoice';
    public $invoiceInfo;
    public $invoice_pkg_format = null; //set a default.
    public $documentArray      = []; //this will hold the list of documents
    protected $TABLE_METHOD    = 'createInvoice';
    public $changedLineItems = [];
    public $removeReadyToInvoice = [];
    const TRIGGER_SIT          = 1;
    const TRIGGER_NEW_DOC      = 2;
    const INVOICE_LOGO_DEFAULT = 'Logo1'; //Probably deprecated but it's in their example
    const UNIT_OF_MEASUREMENT_DEFAULT = null; //'EA';
    const INVOICE_MESSAGE      = null; // DEPRECATED PER EMAIL but null in their example
    const INVOICED_FLAG_DEFAULT = self::CHAR_FALSE;

    protected static $ACCOUNT_TYPE = [
        '' => '01',
    ];

    /**
     * Construct new instance.
     *
     * @param array $initVars
     */
    public function __construct(array $initVars = [])
    {
        parent::__construct($initVars);
        //Can't see when this would be applicable.
        if (array_key_exists('actualNumber', $this->initVars)) {
            $this->actualNumber = $this->initVars['actualNumber'];
        }
        if (array_key_exists('invoice_pkg_format', $this->initVars)) {
            $this->invoice_pkg_format = $this->initVars['invoice_pkg_format'];
        }
        if (array_key_exists('document', $this->initVars)) {
            $this->document = $this->initVars['document'];
        }
        if (array_key_exists('transactionMode', $this->initVars)) {
            $this->transactionMode = $this->initVars['transactionMode'];
        }
        if (array_key_exists('invoiceURI', $this->initVars)) {
            $this->CREATE_INVOICE_URI = $this->initVars['invoiceURI'];
        }
        if (array_key_exists('changedLines', $this->initVars)) {
            $this->changedLineItems = $this->initVars['changedLines'];
            $this->removeReadyToInvoice = $this->changedLineItems['removeReadyToInvoice'];
        }
    }

    /**
     * function createInvoice does the things to create a invoice now.
     *
     * @param bool $invoice_pkg_format
     * @param bool $update
     *
     * @return array|mixed
     * @throws \Exception
     */
    public function createInvoice($invoice_pkg_format = false, $update = false)
    {
        $foundRecord = $this->initializeRecordModels();
        //check invoice_pkg_format set requirement
        if ($invoice_pkg_format) {
            $this->invoice_pkg_format = $invoice_pkg_format;
        }
        if (!$this->invoice_pkg_format) {
            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (invoiceHandler.php: ".__LINE__.") Error there is no Invoice Package Format", FILE_APPEND);
            }
            throw new \Exception(__METHOD__.' Requires an Invoice Package Format to be passed in.');
        }
        if ($foundRecord) {
            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (invoiceHandler.php: ".__LINE__.") Found at least an Actual record to use for Invoice Create", FILE_APPEND);
            }
            $doNotPost = false;

            //@TODO: You'll see this overrides the input we can collapse that... later!
            //Check if it's to be updated.
            $update = $this->createInvoiceHasBeenSent();

            $invoiceInfoArray = $this->pullInvoiceInfo($update);
            if ($this->trigger == self::TRIGGER_NEW_DOC) {
                //if it's a new document then we send if it's a part of the packet or if the packet is complete.
                //I didn't see a differing flag, but there might be in future.
                if ($this->checkPacketComplete()) {
                } elseif ($this->checkPacketDocument($this->document)) {
                } else {
                    $doNotPost = true;
                }
            }
            if ($doNotPost) {
                if (self::DEBUG) {
                    file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (invoiceHandler.php: ".__LINE__.") Packet is incomplete, not sending.", FILE_APPEND);
                }
                //Return true with a message that the packet is incomplete, so not sending.
                $responseObject           = new stdClass;
                $responseObject->response = 'Packet is still incomplete, not sending API request.';
                $responseObject->success  = true;

                return $responseObject;
            }
            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (invoiceHandler.php: ".__LINE__.") Sending a POST request for Invoice Create", FILE_APPEND);
            }

            return $this->postRequest($invoiceInfoArray, $this->CREATE_INVOICE_URI);
            /*
             * all calls are post.
            if($this->trigger == self::TRIGGER_UPDATE) {
                return $this->putRequest($invoiceInfoArray, $this->CREATE_INVOICE_URI);
            } else {
                return $this->postRequest($invoiceInfoArray, $this->CREATE_INVOICE_URI);
            }
            */
        } else {
            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (invoiceHandler.php: ".__LINE__.") Failed to find a record to use for Invoice Create", FILE_APPEND);
            }

            return null;
        }
    }

    /**
     * @param bool $invoice_pkg_format
     *
     * @return array|mixed
     */
    public function updateInvoice($invoice_pkg_format = false)
    {
        return $this->createInvoice($invoice_pkg_format, true);
    }

    /**
     * returns the invoice's information
     *
     * @param \Orders_Record_Model $orderRecordModel
     * @param bool                 $update
     *
     * @return array|mixed
     * @throws \Exception
     */
    private function pullInvoiceInfo($update = false)
    {
        $invoiceInfo = [];
        if (
            $this->contactRecordModel &&
            method_exists($this->contactRecordModel, 'getModuleName') &&
            $this->contactRecordModel->getModuleName() == 'Contacts'
        ) {
            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (invoiceHandler.php: ".__LINE__.") Pulling Contact Information for Invoice Create", FILE_APPEND);
            }
            $invoiceInfo['TransfereeFirstName'] = $this->contactRecordModel->get('firstname');
            $invoiceInfo['TransfereeLastName']  = $this->contactRecordModel->get('lastname');
            $invoiceInfo['TransfereeName']      = $this->contactRecordModel->get('firstname').$this->contactRecordModel->get('lastname');
            //These may be overwritten by the account information
            $invoiceInfo['CustomerID']          = $this->contactRecordModel->get('contact_no');
            $invoiceInfo['InvoiceEmail']        = $this->contactRecordModel->get('email1');
            $invoiceInfo['InvoicePhoneNumber']  = $this->contactRecordModel->get('phone');
            /*
             * The following may be overwritten by accounts or orders information
             */
            $invoiceInfo['CA_BillingAddress1'] = $this->contactRecordModel->get('mailingstreet');
            $invoiceInfo['CA_BillingAddress2'] = $this->contactRecordModel->get('mailingpobox');
            $invoiceInfo['CA_City']            = $this->contactRecordModel->get('mailingcity');
            $invoiceInfo['CA_State']           = $this->translateStateToTwoChar($this->contactRecordModel->get('mailingstate'));
            $invoiceInfo['CA_PostalCode']      = $this->contactRecordModel->get('mailingzip');
            $invoiceInfo['CA_Country']         = $this->contactRecordModel->get('mailingcountry');
        }
        if (
            $this->accountRecordModel &&
            method_exists($this->accountRecordModel, 'getModuleName') &&
            $this->accountRecordModel->getModuleName() == 'Accounts'
        ) {
            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (invoiceHandler.php: ".__LINE__.") Pulling Account Information for Invoice Create", FILE_APPEND);
            }
            //Only from Accounts.
            //this is the wrong "account type" we should remove that field.
            //$invoiceInfo['CustomerType'] = self::$ACCOUNT_TYPE[strtolower($this->accountRecordModel->get('account_type'))];
            $invoiceInfo['CustomerType']  = self::$CUSTOMER_TYPE[strtolower($this->accountRecordModel->get('billing_type'))];

            //These override the one from Contacts.
            $invoiceInfo['CustomerID']   = $this->accountRecordModel->get('account_no');

            //These should be overrode by the orders record.
            $invoiceInfo['InvoiceEmail'] = $this->accountRecordModel->get('email1');
            $invoiceInfo['InvoicePhoneNumber'] = $this->accountRecordModel->get('phone');
            $deliveryPreference     = self::CUSTOMER_DELIVERY_PREFERENCE_DEFAULT;
            $invoiceDocumentFormat  = self::CUSTOMER_INVOICE_DOCUMENT_FORMAT_DEFAULT;
            $currentInvoiceSettings = \Accounts_Record_Model::getCurrentInvoiceSettings($this->accountRecordModel->getId())[0];
            if (is_array($currentInvoiceSettings)) {
                if (array_key_exists('document_format', $currentInvoiceSettings) && $currentInvoiceSettings['document_format']) {
                    $invoiceDocumentFormat = $currentInvoiceSettings['document_format'];
                }
                if (array_key_exists('invoice_delivery', $currentInvoiceSettings) && $currentInvoiceSettings['invoice_delivery']) {
                    $deliveryPreference = $currentInvoiceSettings['invoice_delivery'];
                }
                //These are not defaulted and can be empty.
                if (array_key_exists('invoice_template', $currentInvoiceSettings) && $currentInvoiceSettings['invoice_template']) {
                    $invoiceInfo['TemplateCode'] = self::$INVOICE_TEMPLATE[strtolower($currentInvoiceSettings['invoice_template'])];
                }
                if (array_key_exists('invoice_packet', $currentInvoiceSettings) && $currentInvoiceSettings['invoice_packet']) {
                    $invoiceInfo['PacketCode'] = self::$INVOICE_PACKET[strtolower($currentInvoiceSettings['invoice_packet'])];
                }
            }
            $invoiceInfo['InvoiceDocumentFormat'] = self::$INVOICE_DOCUMENT_FORMAT[strtolower($invoiceDocumentFormat)];
            $invoiceInfo['DeliveryPreference']   = self::$DELIVERY_PREFERENCE[strtolower($deliveryPreference)];

            /*
             * The following overrides contact information and may be overwritten by the orders information
             */
            $invoiceInfo['CA_BillingAddress1'] = $this->accountRecordModel->get('address1');
            $invoiceInfo['CA_BillingAddress2'] = $this->accountRecordModel->get('address2');
            $invoiceInfo['CA_City']            = $this->accountRecordModel->get('city');
            $invoiceInfo['CA_State']           = $this->translateStateToTwoChar($this->accountRecordModel->get('state'));
            $invoiceInfo['CA_PostalCode']      = $this->accountRecordModel->get('zip');
            $invoiceInfo['CA_Country']         = $this->accountRecordModel->get('country');
            // Invoice Event <list>  (These use the wrong id and name, example only)
            // $invoiceInfo['invoice_event'] = $this->getInvoiceEvents($this->accountRecordModel->get('id'), $update);
            // Invoice Project <list>  (These use the wrong id and name, example only)
            // $invoiceInfo['invoice_project'] = $this->getInvoiceProjects($this->accountRecordModel->get('id'), $update);
        }
        if (
            $this->contractRecordModel &&
            method_exists($this->contractRecordModel, 'getModuleName') &&
            $this->contractRecordModel->getModuleName() == 'Contracts'
        ) {
            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (invoiceHandler.php: ".__LINE__.") Pulling Contract Information for Invoice Create", FILE_APPEND);
            }
            //Can be overwritten by the Actual record.
            $invoiceInfo['InvoiceDiscount'] = $this->ensureDecimalNumber($this->contractRecordModel->get('bottom_line_disc'));
        }
        if (
            $this->actualRecordModel &&
            method_exists($this->actualRecordModel, 'getModuleName') &&
            $this->actualRecordModel->getModuleName() == 'Actuals'
        ) {
            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (invoiceHandler.php: ".__LINE__.") Pulling Actual Information for Invoice Create", FILE_APPEND);
            }

            $invoiceInfo['ActualID'] = $this->actualRecordModel->get('quote_no');
            $invoiceInfo['MoveHQ_Actual_ID'] = $this->actualRecordModel->getId();

            //@TODO: This may need eliminated if the rules are no documents on the Actual.
            //generate the documents list here. these are documents attached to the Actual
            $this->generateDocumentList($this->actualRecordModel);

            //@TODO: should we source these here first?
            $invoiceInfo['ActualWeight'] = $this->ensureDecimalNumber($this->actualRecordModel->get('weight'));
            $invoiceInfo['BilledWeight'] = $this->ensureDecimalNumber($this->actualRecordModel->get('billed_weight'));
            $invoiceInfo['OriginAddress'] = $this->actualRecordModel->get('origin_address1').' '.$this->actualRecordModel->get('origin_address2');
            $invoiceInfo['OriginCity']    = $this->actualRecordModel->get('origin_city');
            $invoiceInfo['OriginState']   = $this->translateStateToTwoChar($this->actualRecordModel->get('origin_state'));
            $invoiceInfo['DestAddress'] = $this->actualRecordModel->get('destination_address1').' '.$this->actualRecordModel->get('destination_address2');
            $invoiceInfo['DestCity']    = $this->actualRecordModel->get('destination_city');
            $invoiceInfo['DestState']   = $this->translateStateToTwoChar($this->actualRecordModel->get('destination_state'));
            //@TODO JG HERE
            //These are sources from the actual record and no longer from the Order
            $invoiceInfo['Tariff']   = $this->getTariffName($this->actualRecordModel->get('effective_tariff'));
            $invoiceInfo['Miles']    = $this->ensureDecimalNumber($this->actualRecordModel->get('interstate_mileage'));
            $invoiceInfo['startDate'] =
            $invoiceInfo['LoadDate'] = $this->formatDate($this->actualRecordModel->get('load_date'));
            $invoiceInfo['endDate'] =
            $invoiceInfo['DeliveryDate'] = $this->formatDate($this->actualRecordModel->get('delivery_date'));
            //Overwrites the one set by Contracts.
            $invoiceInfo['InvoiceDiscount'] = $this->ensureDecimalNumber($this->actualRecordModel->get('bottom_line_disc'));
        }
        if (
            $this->orderRecordModel &&
            method_exists($this->orderRecordModel, 'getModuleName') &&
            $this->orderRecordModel->getModuleName() == 'Orders'
        ) {
            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (invoiceHandler.php: ".__LINE__.") Pulling Order Information for Invoice Create", FILE_APPEND);
            }

            //@TODO: WORKSPACE workspace
            $invoiceInfo['Project'] = $this->orderRecordModel->get('orders_projectname');
            //$invoiceInfo['InvoiceStart']   = NULL;
            //$invoiceInfo['InvoiceEnd']     = NULL;

            //generate the documents list here. they are apparently attached to the order
            $this->generateDocumentList($this->orderRecordModel);

            //LIES! The types in the doc are LIES!
            //$invoiceInfo['OrderID']           = $this->ensureInteger($this->orderRecordModel->getId());
            $invoiceInfo['OrderID']         = $this->orderRecordModel->get('orders_no');
            $billerInfo = $this->getBillingClerkInfo($this->orderRecordModel->getId());
            $invoiceInfo['Biller']          = $billerInfo['name'];
            $invoiceInfo['Biller_Email_Address'] = $billerInfo['email'];
            //$invoiceInfo['InvoiceDocuments'] = $this->documentArray;
            if (is_array($this->documentArray)) {
                foreach ($this->documentArray as $documentId => $documentData) {
                    $invoiceInfo['InvoiceDocuments'][] = $documentData;
                }
            }
            $invoiceInfo['InvoiceLogo'] = self::INVOICE_LOGO_DEFAULT; //Probably deprecated
            //@TODO: evaluate if we should be using the actuals create time?
            $invoiceInfo['InvoiceDate']    = Carbon::now()->toAtomString();
            $invoiceInfo['InvoiceMessage'] = self::INVOICE_MESSAGE; //Unknown source // DEPRECATED PER EMAIL
            $invoiceInfo['PaymentTerms'] = $this->orderRecordModel->get('payment_terms');
            $invoiceInfo['FinanceCharge'] = $this->orderRecordModel->get('invoice_finance_charge');
            $invoiceInfo['TemplateCode'] = self::$INVOICE_TEMPLATE[strtolower($this->orderRecordModel->get('invoice_format'))];
            $invoiceInfo['PacketCode']   = self::$INVOICE_PACKET[strtolower($this->invoice_pkg_format)];

            $checkThisValue = $this->addExtraBlocks($invoiceInfo, $update);

            $invoiceDocumentFormat = $this->orderRecordModel->get('invoice_document_format')? :self::CUSTOMER_INVOICE_DOCUMENT_FORMAT_DEFAULT;
            $deliveryPreference    = $this->orderRecordModel->get('invoice_delivery_format')? :self::CUSTOMER_DELIVERY_PREFERENCE_DEFAULT;
            $invoiceInfo['InvoiceDocumentFormat'] = self::$INVOICE_DOCUMENT_FORMAT[strtolower($invoiceDocumentFormat)];
            $invoiceInfo['DeliveryPreference']   = self::$DELIVERY_PREFERENCE[strtolower($deliveryPreference)];
            if ($update) {
                $invoiceInfo['OrderStatus'] = self::$ORDER_STATUS['update'];
            } else {
                $invoiceInfo['OrderStatus'] = self::$ORDER_STATUS['insert'];
            }
            foreach (['destination', 'origin'] as $type) {
                //@NOTE: updates DestCity/DestState/OriginCity/OriginState and DestAddress/OriginAddress
                $tempArray = $this->getAddress($this->orderRecordModel, $type);
                //fairly sure I can merge or something.
                foreach ($tempArray as $tKey => $tValue) {
                    $invoiceInfo[$tKey] = $tValue;
                }
            }
            //@TODO: should be sourced from Actual
            //$invoiceInfo['LoadDate']     = $this->formatDate($this->orderRecordModel->get('orders_ldate'));
            //$invoiceInfo['DeliveryDate'] = $this->formatDate($this->orderRecordModel->get('orders_ddate'));
            $invoiceInfo['PO_Number']    = $this->orderRecordModel->get('orders_ponumber');
            //@TODO: only source this from Actual, maybe we want to override from the order?
            //$invoiceInfo['ActualWeight'] = $this->ensureDecimalNumber($this->orderRecordModel->get('orders_netweight'));
            //$invoiceInfo['BilledWeight'] = $this->ensureDecimalNumber($this->orderRecordModel->get('orders_minweight'));
            // Booking Agent Info
            $bookingAgent            = $this->getParticipatingAgent($this->orderRecordModel->getId(), 'Booking Agent');
            //@TODO: for Workspace workspace
            $invoiceInfo['Location'] = $this->getOrderLocation($this->orderRecordModel, $bookingAgent); //Applies to WorkSpace, may need some conditionalization
            //$invoiceInfo['Location'] = NULL;
            if ($bookingAgent) {
                //@NOTE: Based on example input, BO_Address is the agency name.  pobox is an address line, so i guess put both there.
                //$invoiceInfo['BO_Address']    = $bookingAgent['agent_address1'];
                //$invoiceInfo['BO_POBox']      = $bookingAgent['agent_address2'];
                //$invoiceInfo['BO_Address']    = $bookingAgent['agentName'];
                //$invoiceInfo['BO_POBox']      = $bookingAgent['agent_address1'].' '.$bookingAgent['agent_address2'];
                //@NOTE: v3.06 remove POBox and makes Address:
                $invoiceInfo['Booking_Agent'] = $bookingAgent['agent_number'];
                $invoiceInfo['BO_Address']    = $bookingAgent['agent_address1'] . ' ' . $bookingAgent['agent_address2'];
                $invoiceInfo['BO_City']       = $bookingAgent['agent_city'];
                $invoiceInfo['BO_State']      = $this->translateStateToTwoChar($bookingAgent['agent_state']);
                $invoiceInfo['BO_PostalCode'] = $bookingAgent['agent_zip'];
                $invoiceInfo['BO_Country']    = $bookingAgent['agent_country'];
            }
            /*
             * The following overrides contact and account information
             */
            $invoiceInfo['CA_BillingAddress1'] = $this->orderRecordModel->get('bill_street');
            $invoiceInfo['CA_BillingAddress2'] = $this->orderRecordModel->get('bill_pobox');
            $invoiceInfo['CA_City']            = $this->orderRecordModel->get('bill_city');
            $invoiceInfo['CA_State']           = $this->translateStateToTwoChar($this->orderRecordModel->get('bill_state'));
            $invoiceInfo['CA_PostalCode']      = $this->orderRecordModel->get('bill_code');
            $invoiceInfo['CA_Country']         = $this->orderRecordModel->get('bill_country');
            $invoiceInfo['InvoiceEmail']       = $this->orderRecordModel->get('invoice_email');
            $invoiceInfo['InvoicePhoneNumber'] = $this->orderRecordModel->get('invoice_phone');
        }
        //@TODO: refactoring could probably return this much sooner.
        if ($checkThisValue && !$this->hasInvoiceableServices($invoiceInfo[$checkThisValue])) {
            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (invoiceHandler.php: ".__LINE__.")".__METHOD__.' No invoiceable Services Exiting', FILE_APPEND);
            }
            throw new \Exception(__METHOD__.' No invoiceable Services Exiting');
        }
        //@NOTE: required per OT3608
        $invoiceInfo['Invoice_Revenue'] = 'I';
        $this->invoiceInfo = $invoiceInfo;

        return $invoiceInfo;
    }

    private function getBillingClerkInfo($orderId)
    {
        if ($orderId) {
            $db     = \PearDatabase::getInstance();
            $sql    =
                "SELECT `name` AS firstname,
                `employee_lastname` AS lastname,
                `employee_email` AS `email`
                FROM `vtiger_moveroles`
                JOIN `vtiger_employees` ON moveroles_employees=employeesid
                WHERE moveroles_role='Billing Clerk'
                AND moveroles_orders=?
                LIMIT 1";
            $result = $db->pquery($sql, [$orderId]);
            if ($result) {
                $row = $result->fetchRow();
                if ($row) {
                    return [
                        'name'  => $row['lastname'].', '.$row['firstname'],
                        'email' => $row['email']
                    ];
                }
            }
        }

        return [
            'name'  => '',
            'email' => ''
        ];
    }

    private function getOrderLocation($orderRecordModel, $bookingAgent = false)
    {
        if (is_array($bookingAgent) && $bookingAgent) {
            //@NOTE: Corrected information states the location is the booking agent's agency code.
            return $bookingAgent['agent_number'];
        }
        $add1    = $orderRecordModel->get('origin_address1');
        $add2    = $orderRecordModel->get('origin_address2');
        $city    = $orderRecordModel->get('origin_city');
        $state   = $orderRecordModel->get('origin_state');
        $code    = $orderRecordModel->get('origin_zip');
        $country = $orderRecordModel->get('origin_country');

        return $add1.' '.($add2?$add2.' ':'').$city.', '.$state.', '.$country.' '.$code;
    }

    private function getInvoiceEvents($lineItems, $update = false)
    {
        if (!is_array($lineItems)) {
            return [];
        }
        $projectsArray = [];
        foreach ($lineItems as $section => $sectionArray) {
            foreach ($sectionArray as $itemSequence => $itemArray) {
                //@NOTE: don't send items that are ServiceFlag = N  defined as:
                //Invoiceable = Y
                //ReadyToInvoice = Y
                //Invoice = N
                if ($itemArray['Invoiced']) {
                    //don't include already invoiced items.
                    continue;
                }
                if (!$itemArray['Invoiceable']) {
                    //don't include items that are not invoiceable.
                    continue;
                }

                if (!$itemArray['ReadyToInvoice']) {
                    if (in_array($itemArray['DetailLineItemId'], $this->removeReadyToInvoice)) {
                        $tempArray['TransactionType'] = self::$TRANSACTION_TYPE['delete'];
                    } else {
                        //don't include items that are not ready to invoice
                        continue;
                    }
                } else {
                    $tempArray['TransactionType'] = $this->transactionItemAlreadySent($itemArray['DetailLineItemId'], 'DetailLineItem');
                }

                $tempArray['Invoiced']                     = $this->getInvoicedFlag($itemArray['Invoiced']);
                $tempArray['MoveHQ_Actual_Item_Detail_ID'] = $this->ensureInteger($itemArray['DetailLineItemId']);
                $tempArray['ServiceID']                    = $this->ensureInteger($itemArray['DetailLineItemId']);
                $tempArray['EventDesc']                    = $tempArray['ServiceDescription']
                    = $itemArray['ServiceDescription'];
                $tempArray['ServiceFlag']                  = self::CHAR_TRUE; //Y
                //@TODO: This may need to be required an integer.
                $tempArray['EventPhase']                   = $itemArray['InvoicePhase']; //Event phase from detalied line items
                //$tempArray['EventPhase']                   = $this->ensureInteger($itemArray['InvoicePhase']); //Event phase from detalied line items
                //@TODO: This may need to be required an integer.
                $tempArray['Inv_EventID']                  = $itemArray['InvoiceEvent']; //EventNumber from detalied line items
                //$tempArray['Inv_EventID']                  = $this->ensureInteger($itemArray['InvoiceEvent']); //EventNumber from detalied line items
                $tempArray['BaseRate']                     = $this->ensureDecimalNumberOrNull($itemArray['BaseRate']);
                //$tempArray['UnitRate']                     = $this->ensureDecimalNumberOrNull($itemArray['UnitRate']);
                //$tempArray['Rate']                         = $this->ensureDecimalNumber($itemArray['CostNet']);
                $tempArray['Rate']            = $tempArray['UnitRate'] = $this->ensureDecimalNumberOrNull($itemArray['UnitRate']);
                $tempArray['Quantity']                     = $this->ensureDecimalNumber($itemArray['Quantity']);
                $tempArray['Gross']                        = $this->ensureDecimalNumber($itemArray['Gross']);
                $tempArray['Discount']                     = $this->ensureDecimalNumber($itemArray['InvoiceDiscountPct']);
                $tempArray['Sequence']                     = $this->ensureInteger($itemArray['InvoiceSequence']);
                $tempArray['UnitCode']                     = $itemArray['UnitOfMeasurement']? :self::UNIT_OF_MEASUREMENT_DEFAULT;
                $projectsArray[]                           = $tempArray;
            }
        }

        return $projectsArray;
    }

    private function getInvoiceProjects($lineItems, $projectName, $update = false)
    {
        if (!is_array($lineItems)) {
            return [];
        }
        $projectsArray = [];
        foreach ($lineItems as $section => $sectionArray) {
            foreach ($sectionArray as $itemSequence => $itemArray) {
                //@NOTE: don't send items that are ServiceFlag = N  defined as:
                //Invoiceable = Y
                //ReadyToInvoice = Y
                //Invoice = N
                if ($itemArray['Invoiced']) {
                    //don't include already invoiced items.
                    continue;
                }
                if (!$itemArray['Invoiceable']) {
                    //don't include items that are not invoiceable.
                    continue;
                }

                if (!$itemArray['ReadyToInvoice']) {
                    if (in_array($itemArray['DetailLineItemId'], $this->removeReadyToInvoice)) {
                        $tempArray['TransactionType'] = self::$TRANSACTION_TYPE['delete'];
                    } else {
                        //don't include items that are not ready to invoice
                        continue;
                    }
                } else {
                    $tempArray['TransactionType'] = $this->transactionItemAlreadySent($itemArray['DetailLineItemId'], 'DetailLineItem');
                }

                $tempArray['Invoiced']        = $this->getInvoicedFlag($itemArray['Invoiced']);
                $tempArray['ServiceID']       = $tempArray['MoveHQ_Actual_Item_Detail_ID'] = $this->ensureInteger($itemArray['DetailLineItemId']);
                $tempArray['EventDesc']       = $tempArray['ServiceDescription']
                    = $itemArray['ServiceDescription'];
                $tempArray['ServiceFlag']     = self::CHAR_TRUE; //Y
                //@TODO: This may need to be required an integer.
                $tempArray['EventPhase']      = $itemArray['InvoicePhase']; //Event phase from detalied line items
                //$tempArray['EventPhase']      = $this->ensureInteger($itemArray['InvoicePhase']); //Event phase from detalied line items
                $tempArray['Inv_ProjectID']   = $projectName; //id="Orders_editView_fieldName_orders_projectname"
                $tempArray['BaseRate']        = $this->ensureDecimalNumberOrNull($itemArray['BaseRate']);
                //$tempArray['UnitRate']        = $this->ensureDecimalNumberOrNull($itemArray['UnitRate']);
                //$tempArray['Rate']            = $this->ensureDecimalNumber($itemArray['CostNet']);
                $tempArray['Rate']            = $tempArray['UnitRate'] = $this->ensureDecimalNumberOrNull($itemArray['UnitRate']);
                $tempArray['Quantity']        = $this->ensureDecimalNumber($itemArray['Quantity']);
                $tempArray['Gross']           = $this->ensureDecimalNumber($itemArray['Gross']);
                $tempArray['Discount']        = $this->ensureDecimalNumber($itemArray['InvoiceDiscountPct']);
                $tempArray['Sequence']        = $this->ensureInteger($itemArray['InvoiceSequence']);
                $tempArray['UnitCode']        = $itemArray['UnitOfMeasurement']? :self::UNIT_OF_MEASUREMENT_DEFAULT;
                $projectsArray[]              = $tempArray;
            }
        }

        return $projectsArray;
    }
    //@TODO: move to parent. refactor.
    protected function checkModelExists($variable, $moduleName)
    {
        if (!$this->$variable) {
            return false;
        }
        if (!method_exists($this->$variable, 'getModuleName')) {
            return false;
        }
        if ($this->$variable->getModuleName() != $moduleName) {
            return false;
        }
        return true;
    }

    private function addExtraBlocks(&$invoiceInfo, $update = false)
    {
        //@TODO: this may require some default value?  maybe null?
        $template = '';
        $project = '';

        //@TODO: check if should.
        if (!$this->checkModelExists('actualRecordModel', 'Actuals')) {
            return false;
        }

        $lineItems = $this->actualRecordModel->getDetailLineItems();
        if (!is_array($lineItems)) {
            return false;
        }

        if (isset($invoiceInfo['TemplateCode'])) {
            $template = $invoiceInfo['TemplateCode'];
        }

        if (isset($invoiceInfo['Project'])) {
            $project = $invoiceInfo['Project'];
        }

        $checkValue = 'InvoiceServices';
        if (in_array($template, self::$REQUIRES_EVENTS)) {
            //'event item invoice'        => '12',  //events just
            //'event total invoice'       => '13',  //events just
            $invoiceInfo['InvoiceEvents'] = $this->getInvoiceEvents($lineItems, $update);
            $checkValue = 'InvoiceEvents';
        } elseif (in_array($template, self::$REQUIRES_PROJECTS)) {
            //'project one line invoice'  => '11',  //project
            //'jll invoice'               => '14',  //project
            //'cbre invoice'              => '15',  //project
            //'state farm invoice'        => '16',  //project
            //'asurion invoice'           => '17',  //project
            $invoiceInfo['InvoiceProjects'] = $this->getInvoiceProjects($lineItems, $project, $update);
            $checkValue = 'InvoiceProjects';
        } else {
            if ($this->trigger == self::TRIGGER_SIT) {
                //@TODO, consider pulling this into this class so we can control the serviceFlag and such.
                //and define here what is meant by "origin sit".
                $originSITItemCodes             = [];
                $invoiceInfo['InvoiceServices'] = $this->actualRecordModel->pullApiServices(\Actuals_Record_Model::INVOICE_MODE, $originSITItemCodes);
            } else {
                $invoiceInfo['InvoiceServices'] = $this->getInvoiceServices($lineItems, $update);
            }
        }

        return $checkValue;
    }

    //@TODO should refactor same as Order's except estimate vs actual
    private function getInvoiceServices($lineItems, $update = false)
    {
        if (!is_array($lineItems)) {
            return [];
        }
        $invoiceServicesArray = [];
        foreach ($lineItems as $section => $sectionArray) {
            foreach ($sectionArray as $itemSequence => $itemArray) {
                //@TODO JG HERE
                //@NOTE: don't send items that are ServiceFlag = N  defined as:
                //Invoiceable = Y
                //ReadyToInvoice = Y
                //Invoice = N
                if ($itemArray['Invoiced']) {
                    //don't include already invoiced items.
                    continue;
                }
                if (!$itemArray['Invoiceable']) {
                    //don't include items that are not invoiceable.
                    continue;
                }

                if (!$itemArray['ReadyToInvoice']) {
                    if (in_array($itemArray['DetailLineItemId'], $this->removeReadyToInvoice)) {
                        $tempArray['TransactionType'] = self::$TRANSACTION_TYPE['delete'];
                    } else {
                        //don't include items that are not ready to invoice
                        continue;
                    }
                } else {
                    $tempArray['TransactionType'] = $this->transactionItemAlreadySent($itemArray['DetailLineItemId'], 'DetailLineItem');
                }
                
                $tempArray['Invoiced']                     = $this->getInvoicedFlag($itemArray['Invoiced']);
                $tempArray['ServiceID']                    = $this->ensureInteger($itemArray['DetailLineItemId']);
                $tempArray['MoveHQ_Actual_Item_Detail_ID'] = $this->ensureInteger($itemArray['DetailLineItemId']);
                $tempArray['ServiceDescription']           = $itemArray['ServiceDescription'];
                $tempArray['Approval']                     = $itemArray['Approval'];
                //@NOTE: the rules basically say it can never be N.
                //$tempArray['ServiceFlag']        = ($itemArray['Invoiceable'] && $itemArray['ReadyToInvoice'])?self::CHAR_TRUE:self::CHAR_FALSE;
                $tempArray['ServiceFlag'] = self::CHAR_TRUE;
                $tempArray['BaseRate']    = $this->ensureDecimalNumberOrNull($itemArray['BaseRate']);
                $tempArray['Quantity']    = $this->ensureDecimalNumber($itemArray['Quantity']);
                //$tempArray['Rate']               = $this->ensureDecimalNumber($itemArray['CostNet']);
                $tempArray['Rate']            = $tempArray['UnitRate'] = $this->ensureDecimalNumberOrNull($itemArray['UnitRate']);
                $tempArray['Gross']           = $this->ensureDecimalNumber($itemArray['Gross']);
                $tempArray['Discount']        = $this->ensureDecimalNumber($itemArray['InvoiceDiscountPct']);
                $tempArray['Sequence']        = $this->ensureInteger($itemArray['InvoiceSequence']);
                $tempArray['UnitCode']        = $itemArray['UnitOfMeasurement']? :self::UNIT_OF_MEASUREMENT_DEFAULT;
                $invoiceServicesArray[]       = $tempArray;
            }
        }

        return $invoiceServicesArray;
    }

    public function setPackage($invoice_pkg_format)
    {
        if ($invoice_pkg_format) {
            $this->invoice_pkg_format = $invoice_pkg_format;
        }

        return null;
    }

    private function checkPacketDocument($documentId)
    {
        try {
            $documentRecord = \Vtiger_Record_Model::getInstanceById($documentId, 'Documents');
        } catch (\Exception $ex) {
            //don't give a fuck
            return false;
        }
        $rv             = false;
        if ($documentRecord && $this->invoice_pkg_format && $packet = self::$INVOICE_PACKET[strtolower($this->invoice_pkg_format)]) {
            $packetList = self::$INVOICE_PACKET_DOCLIST[$packet];
            if (is_array($packetList) && in_array($documentRecord->get('invoice_document_type'), $packetList)) {
                $rv = true;
            }
        }

        return $rv;
    }

    /*
    private function checkPacketComplete_v1() {
        $rv = true;
        if (is_array($this->documentArray) && $this->invoice_pkg_format && $packet = self::$INVOICE_PACKET[strtolower($this->invoice_pkg_format)]) {
            $packetList = self::$INVOICE_PACKET_DOCLIST[$packet];
            if (is_array($packetList)) {
                foreach ($packetList as $thing) {
                    if (!in_array($thing, $this->documentArray)) {
                        $rv = false;
                    }
                }
            }
        }
        return $rv;
    }
    */
    private function checkPacketComplete()
    {
        $rv = false;
        if ($this->invoice_pkg_format && $packet = self::$INVOICE_PACKET[strtolower($this->invoice_pkg_format)]) {
            $packetList = self::$INVOICE_PACKET_DOCLIST[$packet];
        }
        foreach ($this->documentArray as $documentId => $docArray) {
            if ($this->checkPacketDocument($documentId)) {
                try {
                    $documentRecord = \Vtiger_Record_Model::getInstanceById($documentId, 'Documents');
                } catch (\Exception $ex) {
                    //don't give a fuck
                    continue;
                }
                if (($key = array_search($documentRecord->get('invoice_document_type'), $packetList)) !== false) {
                    unset($packetList[$key]);
                }
            }
        }
        if (!sizeof($packetList)) {
            $rv = true;
        }

        return $rv;
    }

    protected function generateDocumentList($recordModel)
    {
        if (!$recordModel) {
            return false;
        }

        if (!method_exists($recordModel, 'getDocumentIds')) {
            return false;
        }

        $documents = $recordModel->getDocumentIds();

        if ($this->invoice_pkg_format && $packet = self::$INVOICE_PACKET[strtolower($this->invoice_pkg_format)]) {
            $packetList = self::$INVOICE_PACKET_DOCLIST[$packet];
        }

//        file_put_contents('logs/devLog.log', "\n JG HERE (invoiceHandler.php:".__LINE__.") packetList : ".print_r($packetList, true), FILE_APPEND);
//        foreach ($packetList as $docType) {
//            file_put_contents('logs/devLog.log', "\n JG HERE (invoiceHandler.php:".__LINE__.") docType : ".print_r($docType, true), FILE_APPEND);
//        }

        $documentsList = [];
        foreach ($documents as $docID) {
            if (!$docID) {
                continue;
            }

            try {
                $documentRecord = \Vtiger_Record_Model::getInstanceById($docID, 'Documents');
            } catch (\Exception $ex) {
                //don't give a fuck
                continue;
            }

            $addDocument    = $this->addDocument($documentRecord, $packetList);
            if (!$addDocument) {
                //failed to add document.
                continue;
            }
            $documentsList[$addDocument] = 1;
        }

//        foreach ($documentsList as $blah => $count) {
//            file_put_contents('logs/devLog.log', "\n JG HERE (invoiceHandler.php:".__LINE__.") blah : ".print_r($blah, true), FILE_APPEND);
//        }
    }

    public function addDocument($documentRecord, $packetList)
    {
        if (!$documentRecord) {
            return false;
        }
        if (!method_exists($documentRecord, 'getModuleName')) {
            return false;
        }

        if ($documentRecord->getModuleName() != 'Documents') {
            return false;
        }

        //No longer a requirement the flag on the document is sufficient.
//        if (!is_array($packetList)) {
//            return false;
//        }

        $fileDetails = $documentRecord->getFileDetails();
        if ($documentRecord->get('invoice_packet_include')) {
            $this->documentArray[$documentRecord->getId()] = [
                'DocumentID'              => $documentRecord->get('note_no'),
                'Document'                => $documentRecord->get('invoice_document_type'),
                //@TODO JG HERE This means if we HAVE the document or not.
                //'DocumentFlag'            => ($packetList && in_array($documentRecord->get('invoice_document_type'), $packetList)?self::BIT_TRUE:self::BIT_FALSE),
                'DocumentFlag'            => ($documentRecord->get('invoice_packet_include')?self::BIT_TRUE:self::BIT_FALSE),
                'DocumentURL'             => $this->getDocumentURL($fileDetails),
                'DocumentContentKey'      => $this->getDocumentContentKey($documentRecord->getId()),
                'DocumentTransactionType' => $this->transactionItemAlreadySent($documentRecord->getId(), 'Documents')
            ];
        }

        return $documentRecord->get('invoice_document_type');
    }

    private function getDocumentURL($DocInfo)
    {
        if (!is_array($DocInfo)) {
            return false;
        }

        //DocInfo is the attachment Row from db.
        //+---------------+-----------------------------+-------------+------------+-------------------------------+---------+-------+---------------+
        //| attachmentsid | name                        | description | type       | path                          | subject | crmid | attachmentsid |
        //+---------------+-----------------------------+-------------+------------+-------------------------------+---------+-------+---------------+
        //|         68819 | CustomerPayload_GSM_(1).TXT | NULL        | text/plain | storage/2016/September/week3/ | NULL    | 68818 |         68819 |
        //+---------------+-----------------------------+-------------+------------+-------------------------------+---------+-------+---------------+

        //$builtDLUrl = getenv('SITE_URL') . '/' . $DocInfo['path'] . '/' . $DocInfo['attachmentsid'] . '_' . $DocInfo['name'];
        // Whilst I contend any proper implementation should handle // in the path, it's RFC to have a single /

//        $webserviceURL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off'?"https://":"http://")."$_SERVER[HTTP_HOST]".
//                         dirname($_SERVER['SCRIPT_NAME'])."/webservice.php";
        //'DocumentURL'             => rtrim(getenv('SITE_URL'), '/').'/'.$fileDetails['path'].$fileDetails['name'],

        $builtDLUrl = getenv('SITE_URL');
        if (!preg_match('/\/$/', $builtDLUrl)) {
            $builtDLUrl .= '/';
        }
        $builtDLUrl .= $DocInfo['path'];
        if (!preg_match('/\/$/', $builtDLUrl)) {
            $builtDLUrl .= '/';
        }
        $builtDLUrl .= $DocInfo['attachmentsid'] . '_' . $DocInfo['name'];
        return $builtDLUrl;
    }

    //@TODO: This was an incorrect wild guess... no idea what this key is
    private function getDocumentContentKey($docID)
    {
        $rv = false;

        return $rv;
        //below is wrong. their example has something like an md5.  no clue of what.  maybe the file itself?  or the file name?
        if ($DocInfo = $this->getAttachmentInfo($docID)) {
            $rv = $DocInfo['type'];
        }

        return $rv;
    }

    //we'll use this to trigger specific events and return an object in case the user wants access to the whole thing.
    public static function triggerInvoiceAPI($trigger, array $inputVars = [])
    {
        if (!(strtolower(getenv('INSTANCE_NAME')) == 'graebel' && getenv('GVL_API_ON'))) {
            //Don't trigger the API
            return;
        }
        $self = new static($inputVars);
        switch ($trigger) {
            case 'Origin Sit':
                $self->set('trigger', self::TRIGGER_SIT);
                break;
            case 'Document Add':
                $self->set('trigger', self::TRIGGER_NEW_DOC);
                break;
            default:
        }

        return $self->createInvoice();
    }

    protected function initializeRecordModels()
    {
        $ok = parent::initializeRecordModels();
        //record number could also be a actualNumber
        if ($this->recordNumber) {
            try {
                if ($unknownRecordModel = \Vtiger_Record_Model::getInstanceById($this->recordNumber)) {
                    switch ($unknownRecordModel->getModuleName()) {
                        case 'Actuals':
                            $this->actualRecordModel = $unknownRecordModel;
                            $this->actualNumber      = $this->recordNumber;
                            break;
                        default:
                    }
                }
            } catch (\Exception $ex) {
                //throw $ex; //accept this exception
            }
        }
        if (!$this->orderRecordModel) {
            return $ok;
        }
        if (!$this->actualRecordModel) {
            $this->actualRecordModel = $this->orderRecordModel->getPrimaryActualRecordModel();
            if ($this->actualRecordModel) {
                $this->actualNumber = $this->actualRecordModel->getId();
            }
            if (!$this->actualRecordModel) {
                if (self::DEBUG) {
                    file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (invoiceHandler.php: ".__LINE__.") Actual record not found failing.", FILE_APPEND);
                }

                return false;
            }
        }
        if ($this->invoice_pkg_format) {
            return $ok;
        }
        $this->invoice_pkg_format = $this->orderRecordModel->get('invoice_pkg_format');

        return $ok;
    }

    /**
     * Function returns the address information for the record based on the type
     *
     * @param Orders_Record_Model $orderRecordModel
     * @param                     $type
     *
     * @return array
     */
    protected function getAddress($orderRecordModel, $type)
    {
        $addrInfo = parent::getAddress($orderRecordModel, $type);
        //Just change how the state is handled.
        if ($type) {
            //@TODO: Perhaps not have a fall back?
            $prefix                    = self::$TRANS_ADDR_TYPE[$type]?self::$TRANS_ADDR_TYPE[$type]:$type;
            $addrInfo[$prefix.'State'] = $this->translateStateToTwoChar($orderRecordModel->get($type.'_state'));
        }

        return ($addrInfo);
    }

    /**
     * @param $servicesArray
     *
     * @return bool
     */
    protected function hasInvoiceableServices($servicesArray)
    {
        if (!is_array($servicesArray)) {
            return false;
        }
        if (count($servicesArray) <= 0) {
            return false;
        }
        foreach ($servicesArray as $singleService) {
            if ($singleService['ServiceFlag'] == self::CHAR_TRUE) {
                return true;
            }
        }
    }

    /**
     * @return bool
     */
    protected function createInvoiceHasBeenSent()
    {
        return $this->hasBeenSent($this->TABLE_METHOD, $this->actualNumber);
    }

    /**
     * @return mixed
     */
    protected function getLogRecordId()
    {
        return $this->actualNumber;
    }

    /**
     * @return string
     */
    protected function getLogUrl()
    {
        $url = self::$HOST_NAME.$this->CREATE_INVOICE_URI;
        if ($this->httpClient) {
            $url = $this->httpClient->url;
        }
        return $url;
    }

    protected function getInvoicedFlag($flag)
    {
        return $this->getCharFlag($flag, SELF::INVOICED_FLAG_DEFAULT);
    }
}
