<?php

namespace MoveCrm\GraebelAPI;

include_once('libraries/MoveCrm/GraebelAPI/APIHandler.php');
include_once('modules/Vtiger/models/Record.php');
use Vtiger_Record_Model;
use Orders_Record_Model;
use ParticipatingAgents_Module_Model;

/*
 *
 * orderHandler extends the APIHandler to allow the createOrder function
 * and all those things that need pulled in to do that call.
 *
 */
class orderHandler extends APIHandler
{
    protected static $CREATE_ORDER_URI = '/api/order/createOrder';
    protected static $TRANS_ADDR_TYPE  = [
        'origin'      => 'Origin',
        'destination' => 'Dest'
    ];

    protected static $UNIT_OF_MEASUREMENT = [
        ''              => 'EA',
        'each'          => 'EA',
        'percentage'    => 'PCT',
        'carton weight' => 'CWT',
        'EA'            => 'EA',
        'PCT'           => 'PCT',
        'CWT'           => 'CWT'
    ];

    protected static $ORDER_STATUS = [
        'insert' => 'POST',
        'update' => 'PUT',
        'delete' => 'DELETE',
    ];

    protected static $ShippingAuthority = [
        ''                      => '00',
        // OT 17736 - change default shipping authority to 03
        'default'               => '03',
        'interstate'            => '01',
        'intrastate'            => '02',
        'local'                 => '03',
        'international air'     => '04',
        'international sea'     => '05',
        'international surface' => '05',
        'international land'    => '05',
    ];

    //These are public in order to compare outside what is found/sent.
    public $actualRecordModel;
    public $estimateRecordModel;
    public $accountRecordModel;
    public $participatingAgentInfo;
    public $orderRecordModel;

    /**
     * Construct new instance.
     *
     * @param array $initVars
     */
    public function __construct($initVars)
    {
        parent::__construct($initVars);
    }

    /**
     * @param bool $orderNumber
     *
     * @return \stdClass
     * @throws \Exception
     */
    public function updateOrder($orderNumber = false)
    {
        return $this->createOrder($orderNumber, true);
    }

    /**
     * createOrder pulls the required info and sends it to the API endpoint
     *
     * @param bool $orderNumber
     * @param bool $update
     *
     * @return \stdClass
     * @throws \Exception
     */
    public function createOrder($recordNumber = false, $update = false)
    {
        if ($recordNumber) {
            $this->recordNumber = $recordNumber;
        }

        $foundRecord = $this->initializeRecordModels();

        if ($foundRecord) {
            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (orderHandler.php: " . __LINE__ . ") Found at least one record to use for Order Create", FILE_APPEND);
            }
            $orderInfoArray = $this->pullOrderInfo($update);
            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (orderHandler.php: " . __LINE__ . ") Sending a POST request for Order Create", FILE_APPEND);
            }
            return $this->postRequest($orderInfoArray, self::$CREATE_ORDER_URI);
            /*
             * all calls are post.
            if($this->trigger == self::TRIGGER_UPDATE) {
                return $this->putRequest($orderInfoArray, self::$CREATE_ORDER_URI);
            } else {
                return $this->postRequest($orderInfoArray, self::$CREATE_ORDER_URI);
            }
            */
        } else {
            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (orderHandler.php: " . __LINE__ . ") Failed to find a record to use for Order Create", FILE_APPEND);
            }
            return null;
        }
    }

    protected function initializeRecordModels()
    {
        $rv = parent::initializeRecordModels();

        if ($rv && $this->orderRecordModel) {
            return true;
        }
        return false;
    }

    /**
     * returns the order's information to send to the api
     *
     * @param bool                $update
     *
     * @return array|mixed
     * @throws \Exception
     */
    private function pullOrderInfo($update = false)
    {
        $orderInfo = [];
        if (
            $this->contactRecordModel &&
            method_exists($this->contactRecordModel, 'getModuleName') &&
            $this->contactRecordModel->getModuleName() == 'Contacts'
        ) {
            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (orderHandler.php: " . __LINE__ . ") Pulling Contact Information for Order Create", FILE_APPEND);
            }
            $orderInfo['FirstName']      = $this->contactRecordModel->get('firstname');
            $orderInfo['LastName']       = $this->contactRecordModel->get('lastname');
            $orderInfo['TransfereeName'] = $this->contactRecordModel->get('firstname').' '.$this->contactRecordModel->get('lastname');

            //These may be overwritten by the account information
            $orderInfo['CustomerID']         = $this->contactRecordModel->get('contact_no');
            $orderInfo['InvoiceEmail']       = $this->contactRecordModel->get('email1');
            $orderInfo['InvoicePhoneNumber'] = $this->contactRecordModel->get('phone');
            $orderInfo['CustomerType']       = self::$CUSTOMER_TYPE[self::CUSTOMER_CUSTOMER_TYPE_DEFAULT];

            /*
             * The following may be overwritten by accounts or orders information
             */
            $orderInfo['CA_BillingAddress1'] = $this->contactRecordModel->get('mailingstreet');
            $orderInfo['CA_BillingAddress2'] = $this->contactRecordModel->get('mailingpobox');
            $orderInfo['CA_City'] = $this->contactRecordModel->get('mailingcity');
            $orderInfo['CA_State'] = $this->translateStateToTwoChar($this->contactRecordModel->get('mailingstate'));
            $orderInfo['CA_PostalCode'] = $this->contactRecordModel->get('mailingzip');
            $orderInfo['CA_Country'] = $this->contactRecordModel->get('mailingcountry');
        }

        if (
            $this->accountRecordModel &&
            method_exists($this->accountRecordModel, 'getModuleName') &&
            $this->accountRecordModel->getModuleName() == 'Accounts'
        ) {
            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (orderHandler.php: " . __LINE__ . ") Pulling Account Information for Order Create", FILE_APPEND);
            }

            $orderInfo['CustomerID']   = $this->accountRecordModel->get('account_no');
            //$orderInfo['CA_AccountName'] = $this->accountRecordModel->get('accountname');
            //These override the one from Contacts.
            $orderInfo['InvoiceEmail']       = $this->accountRecordModel->get('email1');
            $orderInfo['InvoicePhoneNumber'] = $this->accountRecordModel->get('phone');
            $orderInfo['CustomerType']  = self::$CUSTOMER_TYPE[strtolower($this->accountRecordModel->get('billing_type'))];

            //These should be overrode by the orders record.
            $deliveryPreference = self::CUSTOMER_DELIVERY_PREFERENCE_DEFAULT;
            $invoiceDocumentFormat = self::CUSTOMER_INVOICE_DOCUMENT_FORMAT_DEFAULT;
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
                    $orderInfo['TemplateCode'] = self::$INVOICE_TEMPLATE[strtolower($currentInvoiceSettings['invoice_template'])];
                }
                if (array_key_exists('invoice_packet', $currentInvoiceSettings) && $currentInvoiceSettings['invoice_packet']) {
                    $orderInfo['PacketCode'] = self::$INVOICE_PACKET[strtolower($currentInvoiceSettings['invoice_packet'])];
                }
            }
            $orderInfo['InvoiceDocFormats'] = self::$INVOICE_DOCUMENT_FORMAT[strtolower($invoiceDocumentFormat)];
            //@TODO: minor variation DeliveryPreferences vs DeliveryPreference
            $orderInfo['DeliveryPreferences']    = self::$DELIVERY_PREFERENCE[strtolower($deliveryPreference)];

            /*
             * The following overrides contact information and may be overwritten by the orders information
             */
            $orderInfo['CA_BillingAddress1']    = $this->accountRecordModel->get('address1');
            $orderInfo['CA_BillingAddress2']    = $this->accountRecordModel->get('address2');
            $orderInfo['CA_City']    = $this->accountRecordModel->get('city');
            $orderInfo['CA_State']    = $this->translateStateToTwoChar($this->accountRecordModel->get('state'));
            $orderInfo['CA_PostalCode']    = $this->accountRecordModel->get('zip');
            $orderInfo['CA_Country']    = $this->accountRecordModel->get('country');
        }

        if (
            $this->orderRecordModel &&
            method_exists($this->orderRecordModel, 'getModuleName') &&
            $this->orderRecordModel->getModuleName() == 'Orders'
        ) {
            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (orderHandler.php: ".__LINE__.") Pulling Order Information for Order Create", FILE_APPEND);
            }
            //LIES! The types in the doc are LIES!
            //$orderInfo['OrderID'] = $this->ensureInteger($this->orderRecordModel->getId());
            //$orderInfo['OrderID'] = $this->orderRecordModel->getId();
            $orderInfo['OrderID']       = $this->orderRecordModel->get('orders_no');
            $orderInfo['TemplateCode']  = self::$INVOICE_TEMPLATE[strtolower($this->orderRecordModel->get('invoice_format'))];
            $orderInfo['PacketCode']    = self::$INVOICE_PACKET[strtolower($this->orderRecordModel->get('invoice_pkg_format'))];
            //@TODO: add in with workspace
            $orderInfo['Project'] = '';

            $invoiceDocumentFormat = $this->orderRecordModel->get('invoice_document_format') ?: self::CUSTOMER_INVOICE_DOCUMENT_FORMAT_DEFAULT;
            $deliveryPreference = $this->orderRecordModel->get('invoice_delivery_format') ?: self::CUSTOMER_DELIVERY_PREFERENCE_DEFAULT;

            $orderInfo['InvoiceDocFormats'] = self::$INVOICE_DOCUMENT_FORMAT[strtolower($invoiceDocumentFormat)];
            //@TODO: minor variation DeliveryPreferences vs DeliveryPreference
            $orderInfo['DeliveryPreferences'] = self::$DELIVERY_PREFERENCE[strtolower($deliveryPreference)];

            $orderInfo['OrderStatus'] = vtranslate($this->orderRecordModel->get('ordersstatus'), 'Orders');

            foreach (['destination', 'origin'] as $type) {
                //@NOTE: updates DestCity/DestState/OriginCity/OriginState and DestAddress/OriginAddress
                $tempArray = $this->getAddress($this->orderRecordModel, $type);
                //fairly sure I can merge or something.
                foreach ($tempArray as $tKey => $tValue) {
                    $orderInfo[$tKey] = $tValue;
                }
            }
            $orderInfo['LoadDate']     = $this->formatDate($this->orderRecordModel->get('orders_ldate'));
            $orderInfo['DeliveryDate'] = $this->formatDate($this->orderRecordModel->get('orders_ddate'));
            $orderInfo['PO_Number']    = $this->orderRecordModel->get('orders_ponumber');
            $orderInfo['Tariff']       = $this->getTariffName($this->orderRecordModel->get('tariff_id'));
            //@NOTE: v3.06 pull from the actual. (usually empty).
            $orderInfo['Miles']        = $this->ensureDecimalNumber($this->getActualInfo($this->orderRecordModel, 'interstate_mileage'));
            if ($orderInfo['Miles'] == 0) {
                //fallback on what's on the order itself.
                $orderInfo['Miles'] = $this->ensureDecimalNumber($this->orderRecordModel->get('orders_miles'));
            }
            //@NOTE: v3.06 pull from the actual. (usually empty).
            $orderInfo['ActualWeight'] = $this->ensureDecimalNumber($this->getActualInfo($this->orderRecordModel, 'weight'));

            //@NOTE:  v3.06 API suggests pulling the amount from the primary Actual. (probably doesn't exist at the create point)
            $orderInfo['BilledWeight'] = $this->ensureDecimalNumber($this->getActualInfo($this->orderRecordModel, 'billed_weight'));
            if ($orderInfo['BilledWeight'] == 0) {
                //fallback on what's on the order itself.
                $orderInfo['BilledWeight'] = $this->ensureDecimalNumber($this->orderRecordModel->get('orders_minweight'));
            }

            //$orderInfo['BusinessLine'] = $this->orderRecordModel->get('billing_type');
            //@NOTE: update in v3.06 curious why I thought billing_type was right.
            $orderInfo['BusinessLine'] = vtranslate($this->orderRecordModel->get('business_line'), 'Orders');
            //list ($goodsType, $shippingAuthority) = explode(' - ', vtranslate($this->orderRecordModel->get('business_line'), 'Orders'));
            list($goodsType, $shippingAuthority) = explode(' - ', $orderInfo['BusinessLine']);
            $orderInfo['ShipmentAuthority'] = $this->getShippingAuthority($shippingAuthority);
            //@TODO: I forecast this will be required in the future.
            //$orderInfo['GoodsType'] = $goodsType;
            //@NOTE:  v3.06 API suggests pulling the amount from the primary Estimate.
            $orderInfo['EstimatedAmount'] = $this->ensureDecimalNumber($this->getEstimateInfo($this->orderRecordModel, 'hdnGrandTotal'));
            if ($orderInfo['EstimatedAmount'] == 0) {
                //Fall back to what's on the order itself.
                $orderInfo['EstimatedAmount'] = $this->ensureDecimalNumber($this->orderRecordModel->get('orders_etotal'));
            }
            //booker and OrderDocuments are no longer listed in the API document.
            //$orderInfo['Booker']         = $this->getParticipatingAgent($this->orderRecordModel->getId(), 'Booking Agent');
            //$orderInfo['OrderDocuments'] = $this->getOrderDocuments($this->orderRecordModel, $update);

            //@TODO: These two will likely need some form of update for Workspace moves.
            //$orderInfo['OrderProject'] = $this->getOrderProject($this->orderRecordModel, $update);
            //$orderInfo['OrderEvent'] = $this->getOrderEvent($this->orderRecordModel, $update);
            $orderInfo['OrderServices'] = $this->getOrderServices($this->orderRecordModel, $update);

            /*
             * The following overrides contact and account information
             */
            $orderInfo['CA_BillingAddress1'] = $this->orderRecordModel->get('bill_street');
            $orderInfo['CA_BillingAddress2'] = $this->orderRecordModel->get('bill_pobox');
            $orderInfo['CA_City'] = $this->orderRecordModel->get('bill_city');
            $orderInfo['CA_State'] = $this->translateStateToTwoChar($this->orderRecordModel->get('bill_state'));
            $orderInfo['CA_PostalCode'] = $this->orderRecordModel->get('bill_code');
            $orderInfo['CA_Country'] = $this->orderRecordModel->get('bill_country');
            $orderInfo['InvoiceEmail']       = $this->assignIfNotEmpty($this->orderRecordModel->get('invoice_email'), $orderInfo['InvoiceEmail']);
            $orderInfo['InvoicePhoneNumber'] = $this->assignIfNotEmpty($this->orderRecordModel->get('invoice_phone'), $orderInfo['InvoicePhoneNumber']);

            // Booking Agent Info
            $bookingAgent = $this->getParticipatingAgent($this->orderRecordModel->getId(), 'Booking Agent');

            if ($bookingAgent) {
                //@NOTE: Based on example input, BO_Address is the agency name.  pobox is an address line, so i guess put both there.
                //$orderInfo['BO_Address']    = $bookingAgent['agent_address1'];
                //$orderInfo['BO_POBox']      = $bookingAgent['agent_address2'];

                //$orderInfo['BO_Address']    = $bookingAgent['agentName'];
                //$orderInfo['BO_POBox']      = $bookingAgent['agent_address1'] . ' ' . $bookingAgent['agent_address2'];
                //@NOTE: v3.06 remove POBox and makes Address:
                $orderInfo['BO_Address']    = $bookingAgent['agent_address1'] . ' ' . $bookingAgent['agent_address2'];
                $orderInfo['BO_City']       = $bookingAgent['agent_city'];
                $orderInfo['BO_State']      = $this->translateStateToTwoChar($bookingAgent['agent_state']);
                $orderInfo['BO_PostalCode'] = $bookingAgent['agent_zip'];
                $orderInfo['BO_Country']    = $bookingAgent['agent_country'];
            }
        }

        return $orderInfo;
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
        $addrInfo = [];
        if ($type) {
            //@TODO: Perhaps not have a fall back?
            $prefix                       = self::$TRANS_ADDR_TYPE[$type]?self::$TRANS_ADDR_TYPE[$type]:$type;
            $addrInfo[$prefix.'Address']  = $orderRecordModel->get($type.'_address1').$orderRecordModel->get($type.'_address2');
            //$addrInfo[$prefix.'Address1'] = $orderRecordModel->get($type.'_address1');
            //$addrInfo[$prefix.'Address2'] = $orderRecordModel->get($type.'_address2');
            $addrInfo[$prefix.'City']     = $orderRecordModel->get($type.'_city');
            //append the first three of the zip in parens after the state.
            $addrInfo[$prefix.'State']    = $this->translateStateToTwoChar($orderRecordModel->get($type.'_state')) . '(' . substr($orderRecordModel->get($type.'_zip'), 0, 3) . ')';
            //$addrInfo[$prefix.'Zip']      = $orderRecordModel->get($type.'_zip');
            $addrInfo[$prefix.'Country']  = $orderRecordModel->get($type.'_country');
        }

        return ($addrInfo);
    }

    /**
     * getTariffName pulls the associated tariff.
     *
     * @param $tariffId
     *
     * @return string
     */
    protected function getTariffName($tariffId, $itemCode = false)
    {
        $rv = '';
        try {
            $interstateTariff = Vtiger_Record_Model::getInstanceById($tariffId, 'TariffManager');
            $vid = $interstateTariff->get('vanline_specific_tariff_id');
            if ($vid) {
                if($itemCode !== false)
                {
                    $res = \Estimates_QuickEstimate_Action::accessDatabaseForDetailLineCodeMap($itemCode, $vid);
                    if(!$res && $vid == '104G')
                    {
                        return '400N';
                    }
                    if($res['tariff_number'])
                    {
                        return $res['tariff_number'];
                    }
                }
                return $interstateTariff->get('vanline_specific_tariff_id');
            }
            if ($interstateTariff->get('tariffmanagername')) {
                return $interstateTariff->get('tariffmanagername');
            }
        } catch (\Exception $ex) {
            //ignore errors might be a local tariff
        }
        try {
            $localTariff = Vtiger_Record_Model::getInstanceById($tariffId, 'Tariffs');
            if ($localTariff->get('vanline_specific_tariff_id')) {
                return $localTariff->get('vanline_specific_tariff_id');
            }
            if ($localTariff->get('tariff_name')) {
                return $localTariff->get('tariff_name');
            }
        } catch (\Exception $ex) {
            //throw new \Exception('Unable to pull Related Tariff: (' . $orderRecordModel->get('orders_account') . ').');
            //It may not be a fail error just like you know nothing was set, as it's not required for the Order.
        }

        return $rv;
    }

    /**
     * returns the agency info based on their type in relation to the order.
     *
     * @param string $recordID
     * @param string $agentType
     *
     * @return array|mixed
     */
    protected function getParticipatingAgent($recordID, $agentType = false)
    {
        $agentInfo = [];
        $allPAs = $this->participatingAgentInfo;
        if (!$this->participatingAgentInfo) {
            $allPAs = \ParticipatingAgents_Module_Model::getParticipants($recordID);
            foreach ($allPAs as $paRecord) {
                $this->participatingAgentInfo[$paRecord['agent_type']] = $paRecord;
            }
        }
        if ($agentType) {
            if (array_key_exists($agentType, $this->participatingAgentInfo)) {
                //@TODO: These may need mapped
                $agentInfo = $this->participatingAgentInfo[$agentType];
            }
        } else {
            return $allPAs;
        }
        return $agentInfo;
    }

    /**
     * getOrderServices pulls the associated services for this order from Actuals
     *
     * @param Orders_Record_Model $orderRecordModel
     * @param bool                $update
     *
     * @return string
     */
    private function getOrderServices($orderRecordModel, $update = false)
    {
        $orderServicesArray = [];
        try {
            if ($this->estimateRecordModel) {
                $estimateRecordModel = $this->estimateRecordModel;
            } else {
                $this->estimateRecordModel = $estimateRecordModel = $orderRecordModel->getPrimaryEstimateRecordModel();
            }
            if ($this->estimateRecordModel) {
                if ($lineItems = $estimateRecordModel->getDetailLineItems()) {
                    foreach ($lineItems as $section => $sectionArray) {
                        foreach ($sectionArray as $itemSequence => $itemArray) {
                            $rate = $this->ensureDecimalNumber($itemArray['BaseRate']?$itemArray['BaseRate']:$itemArray['UnitRate']);

                            //$tempArray['OrderID'] = $orderRecordModel->getId(); //removed?
                            //LIES! The types in the doc are LIES!
                            //$tempArray['ServiceID']          = $this->ensureInteger($itemArray['DetailLineItemId']);
                            $tempArray['ServiceID']          = $itemArray['DetailLineItemId'];
                            $tempArray['ServiceDescription'] = $itemArray['ServiceDescription'];
                            //LIES! The types in the doc are LIES!
                            $tempArray['ServiceFlag']        = $itemArray['Invoiceable']?self::CHAR_TRUE:self::CHAR_FALSE;
                            $tempArray['BaseRate']           = $rate;
                            $tempArray['Quantity']           = $this->ensureDecimalNumber($itemArray['Quantity']);
                            $tempArray['Rate']               = $rate;
                            $tempArray['Gross']              = $this->ensureDecimalNumber($itemArray['Gross']);
                            $tempArray['Discount']           = $this->ensureDecimalNumber($itemArray['InvoiceDiscountPct']);
                            $tempArray['TransactionType']    = self::$TRANSACTION_TYPE['insert'];
                            //$tempArray['TransactionType']    = self::$TRANSACTION_TYPE['update'];
                            //$tempArray['TransactionType']    = self::$TRANSACTION_TYPE['delete'];
                            $tempArray['Sequence']    = ($itemSequence + 1);
                            $tempArray['UnitCode']    = self::$UNIT_OF_MEASUREMENT[$itemArray['UnitOfMeasurement']];
                            $orderServicesArray[]     = $tempArray;
                        }
                    }
                }
            }
        } catch (\Exception $ex) {
            //throw new \Exception('Unable to pull Related Tariff: (' . $orderRecordModel->get('orders_account') . ').');
            //It may not be a fail error just like you know nothing was set, as it's not required for the Order.
        }
        return $orderServicesArray;
    }

    /**
     * getOrderProject pulls the associated tariff.
     *
     * @param Orders_Record_Model $orderRecordModel
     * @param bool                $update
     *
     * @return string
     */
    private function getOrderProject($orderRecordModel, $update = false)
    {
        //@TODO: I think this is not what it says it is.
        //There appears to be nothing of use in this document?
        //This appears to be the same as services with different tags?
        // I am assuming that is wrong so making a new function that will be updated with right stuff.

        $orderProjectArray = [];
        //This requires an Actual linked to the account.
        try {
            if ($this->estimateRecordModel) {
                $estimateRecordModel = $this->estimateRecordModel;
            } else {
                $this->estimateRecordModel = $estimateRecordModel = $orderRecordModel->getPrimaryEstimateRecordModel();
            }
            if ($lineItems = $estimateRecordModel->getDetailLineItems()) {
                foreach ($lineItems as $itemSequence => $itemValues) {
                    //@TODO: requires adding or this is something else unclear?
                    $tempArray['ProjectID']       = $itemValues['lineItemId'];
                    $tempArray['Description']     = $itemValues['Description'];
                    $tempArray['DescriptionFlag'] = $itemValues['DescriptionFlag']?self::CHAR_TRUE:self::CHAR_FALSE;
                    $tempArray['Quantity']        = $itemValues['Quantity'];
                    $tempArray['Rate']            = $itemValues['Rate'];
                    $tempArray['TransactionType']    = self::$TRANSACTION_TYPE['insert'];
                    if ($update) {
                        $tempArray['TransactionType']    = self::$TRANSACTION_TYPE['update'];
                    }
                    //$tempArray['TransactionType']    = self::$TRANSACTION_TYPE['delete'];
                    $tempArray['Sequence']    = ($itemSequence + 1);
                    $tempArray['UnitCode']    = self::$UNIT_OF_MEASUREMENT[$itemValues['UnitOfMeasurement']];
                    $orderProjectArray[] = $tempArray;
                }
            }
        } catch (\Exception $ex) {
            //throw new \Exception('Unable to pull Related Tariff: (' . $orderRecordModel->get('orders_account') . ').');
            //It may not be a fail error just like you know nothing was set, as it's not required for the Order.
        }

        return $orderProjectArray;
    }

    /**
     * @param $orderRecordModel
     * @param $update
     *
     * @return array
     */
    private function getOrderEvent($orderRecordModel, $update = false)
    {
        //@TODO: This is totally unclear.  What's an event?
        $orderEventsArray = [];
        //This requires an Actual linked to the account.
        try {
            if ($this->estimateRecordModel) {
                $estimateRecordModel = $this->estimateRecordModel;
            } else {
                $this->estimateRecordModel = $estimateRecordModel = $orderRecordModel->getPrimaryEstimateRecordModel();
            }
            if ($lineItems = $estimateRecordModel->getDetailLineItems()) {
                foreach ($lineItems as $itemSequence => $itemValues) {
                    $tempArray['EventID']            = $itemValues['lineItemId'];
                    //@TODO: decide which is right I'm betting EventID, v3.06 says ServiceID
                    $tempArray['ServiceID']            = $itemValues['lineItemId'];
                    $tempArray['Function']    = $itemValues['Function'];
                    $tempArray['EventDate']   = $itemValues['EventDate'];
                    $tempArray['Project']     = $itemValues['Project'];
                    $tempArray['ServiceFlag'] = $itemValues['service_flag']?self::CHAR_TRUE:self::CHAR_FALSE;
                    $tempArray['Quantity']    = $itemValues['Quantity'];
                    $tempArray['Hours']       = $itemValues['Hours'];
                    $tempArray['Rate']        = $itemValues['Rate'];
                    $tempArray['TransactionType']    = self::$TRANSACTION_TYPE['insert'];
                    if ($update) {
                        $tempArray['TransactionType']    = self::$TRANSACTION_TYPE['update'];
                    }
                    $tempArray['Sequence']    = ($itemSequence + 1);
                    $tempArray['Location']    = $itemValues['Location'];
                    $orderEventsArray[] = $tempArray;
                }
            }
        } catch (\Exception $ex) {
            //throw new \Exception('Unable to pull Related Tariff: (' . $orderRecordModel->get('orders_account') . ').');
            //It may not be a fail error just like you know nothing was set, as it's not required for the Order.
        }

        return $orderEventsArray;
    }

    /**
     * getOrderDocuments pulls the associated tariff.
     *
     * @param Orders_Record_Model $orderRecordModel
     * @param bool                $update
     *
     * @return string
     */
    private function getOrderDocuments($orderRecordModel, $update = false)
    {
        //@TODO: This needs some real work and collaboration
        $orderDocumentsArray = [];
        //This requires an Actual linked to the account.
        try {
            if ($this->accountRecordModel) {
                $accountRecordModel = $this->accountRecordModel;
            } else {
                $relAccount = $orderRecordModel->get('orders_account');
                $accountRecordModel = Vtiger_Record_Model::getInstanceById($relAccount, 'Accounts');
                $this->accountRecordModel = $accountRecordModel;
            }

            $lineItems = $accountRecordModel->getCurrentInvoiceSettings();
            foreach ($lineItems as $itemSequence => $itemValues) {
                $tempArray['OrderID'] = $orderRecordModel->get('orders_no');
                $tempArray['Document'] = $lineItems['DocumentName'];
                $tempArray['DocumentFlag'] = $lineItems['DocumentExists'];
                $tempArray['DocumentURL'] = $lineItems['DocumentURL'];
                $tempArray['DocumentsID'] = $lineItems['id'];
                if ($update) {
                    $tempArray['MoveHQStatus'] = $this->MOVE_HQ_STATUS['update'];
                } else {
                    $tempArray['MoveHQStatus'] = $this->MOVE_HQ_STATUS['insert']; //or update since they should exist?
                }
                $orderDocumentsArray[] = $tempArray;
            }
        } catch (\Exception $ex) {
            //throw new \Exception('Unable to pull Related Tariff: (' . $orderRecordModel->get('orders_account') . ').');
            //It may not be a fail error just like you know nothing was set, as it's not required for the Order.
        }

        return $orderDocumentsArray;
    }

    /**
     * getActualInfo pulls the associated Actuals information by flag or all
     *
     * @param Orders_Record_Model $orderRecordModel
     * @param string              $field
     *
     * @return string
     * @throws \Exception
     */
    private function getActualInfo($orderRecordModel, $field)
    {
        $fieldValue = '';
        try {
            if ($this->actualRecordModel) {
                $actualRecordModel = $this->actualRecordModel;
            } else {
                $actualRecordModel = $this->actualRecordModel = $orderRecordModel->getPrimaryActualRecordModel();
            }
            if ($actualRecordModel) {
                $fieldValue = $actualRecordModel->get($field);
            }
        } catch (\Exception $ex) {
            //@TODO: I don't think we need to exception here.  I think an empty return is fine.
            //throw new \Exception('Unable to get value for field (' . $field . ') of Actual.');
        }
        return $fieldValue;
    }

    /**
     * getEstimateInfo pulls the associated Estimates information by flag or all
     *
     * @param Orders_Record_Model $orderRecordModel
     * @param string              $field
     *
     * @return string
     * @throws \Exception
     */
    private function getEstimateInfo($orderRecordModel, $field)
    {
        $fieldValue = '';
        try {
            if ($this->estimateRecordModel) {
                $estimateRecordModel = $this->estimateRecordModel;
            } else {
                $estimateRecordModel = $this->estimateRecordModel = $orderRecordModel->getPrimaryEstimateRecordModel();
            }
            if ($estimateRecordModel) {
                $fieldValue = $estimateRecordModel->get($field);
            }
        } catch (\Exception $ex) {
            //@TODO: I don't think we need to exception here.  I think an empty return is fine.
            //throw new \Exception('Unable to get value for field (' . $field . ') of Estimate.');
        }
        return $fieldValue;
    }

    /**
     *
     * @string $shippingAuthority
     *
     * @return string
     */
    protected function getShippingAuthority($shippingAuthority)
    {
        //make lower so the array is simpler.
        $shippingAuthority = strtolower($shippingAuthority);

        if (self::$ShippingAuthority[$shippingAuthority]) {
            return self::$ShippingAuthority[$shippingAuthority];
        }
        return self::$ShippingAuthority['default'];
    }
}
