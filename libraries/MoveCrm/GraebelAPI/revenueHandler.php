<?php

namespace MoveCrm\GraebelAPI;

include_once('libraries/MoveCrm/GraebelAPI/revenueHandler.php');
include_once('modules/Vtiger/models/Record.php');

use Carbon\Carbon;

/*
 * revenueHandler extends the APIHandler to allow the createRevenue API call.
 * and all those things that need pulled in to do that call.
 *
 */

class revenueHandler extends invoiceHandler
{

    //@TODO flag for refactoring.
    protected $CREATE_REVENUE_URI = '/api/revenue/createRevenue';
    public $revenueInfo;
    protected $TABLE_METHOD    = 'createRevenue';
    protected $stopTypeStorage = [];
    public $changedLineItems = [];
    public $removeReadyToDistribute = [];

    //@TODO: Eventually this might need to be something currently it's to be N.
    const METRO_FLAG_DEFAULT = self::CHAR_FALSE;
    const GCS_FLAG_DEFAULT = self::CHAR_FALSE;
    const REVENUE_SEND_FLAG = 'R';
    const INVOICE_SEND_FLAG = 'I';
    const IGNORE_LINE_ITEM = 'ignore_item';

    protected static $GoodsType = [
        ''                         => '00',
        'default'                  => '00',
        'hhg'                      => '01',
        'national account'         => '01',
        'work space - commodities' => '02',
        'work space'               => '03',
        'commercial'               => '03',
    ];

    //flag to allow database lookup for stop code if it's missing.
    //this should be self::BIT_FALSE;
    const FALL_BACK_STOP_CODE = self::BIT_TRUE;
    protected static $LOCATION_STOP_CODE = [
        'orig' => 'ORIG',
        'origin' => 'ORIG',
        'destination' => 'DEST',
        'dest' => 'DEST',
    ];

    protected $storedCompanyID = [];

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
        if (array_key_exists('document', $this->initVars)) {
            $this->document = $this->initVars['document'];
        }
        if (array_key_exists('transactionMode', $this->initVars)) {
            $this->transactionMode = $this->initVars['transactionMode'];
        }
        if (array_key_exists('invoiceURI', $this->initVars)) {
            $this->CREATE_REVENUE_URI = $this->initVars['invoiceURI'];
        }
        if (array_key_exists('changedLines', $this->initVars)) {
            $this->changedLineItems = $this->initVars['changedLines'];
            $this->removeReadyToDistribute = $this->changedLineItems['removeReadyToDistribute'];
        }
    }

    /**
     * function createRevenue does the things to create a invoice now.
     *
     * @param bool $update
     *
     * @return array|mixed
     * @throws \Exception
     */
    public function createRevenue($invoiceFlag = false, $update = false)
    {
        $this->Invoice_Revenue = self::REVENUE_SEND_FLAG;
        if ($invoiceFlag) {
            $this->Invoice_Revenue = self::INVOICE_SEND_FLAG;
        }
        $foundRecord = $this->initializeRecordModels();
        if ($foundRecord) {
            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (revenueHandler.php: ".__LINE__.") Found at least an Actual record to use for Revenue Create", FILE_APPEND);
            }

            //@TODO: You'll see this overrides the input we can collapse that... later!
            //Check if it's to be updated.
            $update = $this->createRevenueHasBeenSent();
            $revenueInfoArray = $this->pullRevenueInfo($update);

            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (revenueHandler.php: ".__LINE__.") Sending a POST request for Revenue Create", FILE_APPEND);
            }

            return $this->postRequest($revenueInfoArray, $this->CREATE_REVENUE_URI);
            /*
             * all calls are post.
            if($this->trigger == self::TRIGGER_UPDATE) {
                return $this->putRequest($revenueInfoArray, $this->CREATE_REVENUE_URI);
            } else {
                return $this->postRequest($revenueInfoArray, $this->CREATE_REVENUE_URI);
            }
            */
        } else {
            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (revenueHandler.php: ".__LINE__.") Failed to find a record to use for Revenue Create", FILE_APPEND);
            }

            return null;
        }
    }

    /**
     * @return array|mixed
     */
    public function updateRevenue($invoiceFlag = false)
    {
        return $this->createRevenue($invoiceFlag, true);
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
    private function pullRevenueInfo($update = false)
    {
        $revenueInfo = [];

        if (
            $this->contactRecordModel &&
            method_exists($this->contactRecordModel, 'getModuleName') &&
            $this->contactRecordModel->getModuleName() == 'Contacts'
        ) {
            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (revenueHandler.php: ".__LINE__.") Pulling Contact Information for Revenue Create", FILE_APPEND);
            }
            $revenueInfo['Transferee_FirstName'] = $this->contactRecordModel->get('firstname');
            $revenueInfo['Transferee_LastName']  = $this->contactRecordModel->get('lastname');
            $revenueInfo['Transferee_FullName']  = $this->contactRecordModel->get('firstname').$this->contactRecordModel->get('lastname');
        }

        if (
            $this->accountRecordModel &&
            method_exists($this->accountRecordModel, 'getModuleName') &&
            $this->accountRecordModel->getModuleName() == 'Accounts'
        ) {
            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (revenueHandler.php: ".__LINE__.") Pulling Account Information for Revenue Create", FILE_APPEND);
            }
            //These override the one from Contacts.
            $revenueInfo['CustomerID']   = $this->accountRecordModel->get('account_no');
        }

        if (
            $this->contractRecordModel &&
            method_exists($this->contractRecordModel, 'getModuleName') &&
            $this->contractRecordModel->getModuleName() == 'Contracts'
        ) {
            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (revenueHandler.php: ".__LINE__.") Pulling Contract Information for Revenue Create", FILE_APPEND);
            }
            //Required but may not always exist, unclear on what to do.
            //@TODO JG HERE
            $revenueInfo['ContractID'] = $this->contractRecordModel->get('contract_no');
            //Can be overwritten by the Actual record.
            $revenueInfo['Distribution_Discount'] = $this->ensureDecimalNumber($this->contractRecordModel->get('bottom_line_distribution_discount'));
        }

        if (
            $this->actualRecordModel &&
            method_exists($this->actualRecordModel, 'getModuleName') &&
            $this->actualRecordModel->getModuleName() == 'Actuals'
        ) {
            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (revenueHandler.php: ".__LINE__.") Pulling Actual Information for Revenue Create", FILE_APPEND);
            }

            $revenueInfo['ActualID'] = $this->actualRecordModel->get('quote_no');
            $revenueInfo['MoveHQ_Actual_ID'] = $this->actualRecordModel->getId();
            $revenueInfo['Default_Tariff_Number'] = $this->getTariffName($this->actualRecordModel->get('effective_tariff'));
            $revenueInfo['GeneralLedgerServices'] = $this->getGeneralLedgerServices($this->actualRecordModel, $update);

            //@TODO: should we source these here first?
            //@TODO JG HERE SOURCED FROM LOCAL DIFFERENT!
            $revenueInfo['ActualWeight'] = $this->ensureDecimalNumber($this->actualRecordModel->get('weight'));
            $revenueInfo['BilledWeight'] = $this->ensureDecimalNumber($this->actualRecordModel->get('billed_weight'));

            //@TODO JG HERE this needs pulled somehow.
            $revenueInfo['Auto_Logistics_Weight'] = $this->ensureDecimalNumber($this->actualRecordModel->get('vehicletrans_weight_1'));
            $revenueInfo['HHG_Logistics_Weight'] = $this->ensureDecimalNumber($UNKNOWN_SOURCE);

            $revenueInfo['BusinessLine'] = vtranslate($this->actualRecordModel->get('business_line_est'), 'Actuals');
            list($goodsType, $shippingAuthority) = explode(' - ', $revenueInfo['BusinessLine']);
            //$revenueInfo['ShipmentAuthority'] = $this->getShippingAuthority($shippingAuthority);
            $revenueInfo['AuthorityType'] = $this->getShippingAuthority($shippingAuthority);
            $revenueInfo['GoodsType'] = $this->getGoodsType($goodsType, $revenueInfo['BusinessLine']);

            //@TODO JG HERE -- for Local Move we need to source this from somewhere else.
            $revenueInfo['Miles'] = $revenueInfo['Distance'] = $this->ensureDecimalNumber($this->actualRecordModel->get('interstate_mileage'));
            $revenueInfo['startDate'] =
            $revenueInfo['LoadDate'] = $this->formatDate($this->actualRecordModel->get('load_date'));
            //$revenueInfo['StorageDate'] = $this->formatDate($this->actualRecordModel->get('sit_origin_date_in'));
            $revenueInfo['StoredDate'] = $this->formatDate($this->actualRecordModel->get('sit_origin_date_in'));
            $revenueInfo['endDate'] =
            $revenueInfo['DeliveryDate'] = $this->formatDate($this->actualRecordModel->get('delivery_date'));

            //@TODO: OT3608: says to do this how invoiceHandler does it which is just a timestamp at send.
            $revenueInfo['InvoiceDate']  = Carbon::now()->toAtomString();

            //Overwrites the one set by Contracts.
            //@TODO: This is a "default" Local may need this added to local move details.
            $revenueInfo['Distribution_Discount'] = $this->ensureDecimalNumber($this->actualRecordModel->get('bottom_line_distribution_discount'));
        }

        if (
            $this->orderRecordModel &&
            method_exists($this->orderRecordModel, 'getModuleName') &&
            $this->orderRecordModel->getModuleName() == 'Orders'
        ) {
            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (revenueHandler.php: ".__LINE__.") Pulling Order Information for Revenue Create", FILE_APPEND);
            }
            $revenueInfo['OrderID'] = $this->orderRecordModel->get('orders_no');
            $revenueInfo['Project'] = $this->orderRecordModel->get('orders_projectname');

            $bookingAgent            = $this->getParticipatingAgent($this->orderRecordModel->getId(), 'Booking Agent');
            $revenueInfo['Company_ID'] = $this->getAgencyNumber($bookingAgent);

            //$revenueInfo['Carrier_Reference'] = $this->orderRecordModel->get('orders_vanlineregnum');
            //@NOTE: this is the HQ<order_no> BookingAgentNumber ex: HQ1234 928
            $revenueInfo['Carrier_Reference'] = $revenueInfo['OrderID'] . ' ' . $revenueInfo['Company_ID'];

            $revenueInfo['GeneralLedgerParticipatingAgents'] = $this->getGeneralLedgerParticipatingAgents($this->orderRecordModel);
            //$revenueInfo['GeneralLedgerMoveRoles'] = $this->getGeneralLedgerMoveRoles($this->orderRecordModel);
            $revenueInfo['GeneralLedgerRoles'] = $this->getGeneralLedgerMoveRoles($this->orderRecordModel);
            $revenueInfo['GeneralLedgerSalesPeoples'] = $this->getGeneralLedgerSalesPeoples($this->orderRecordModel);
        }

        //@TODO: refactoring could probably return this much sooner.
        if (!$this->hasDistributableServices($revenueInfo['GeneralLedgerServices'])) {
            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (revenueHandler.php: ".__LINE__.")".__METHOD__.' No disributable Services Exiting', FILE_APPEND);
            }
            throw new \Exception(__METHOD__.' No disributable Services Exiting');
        }
        $revenueInfo['Invoice_Revenue'] = $this->Invoice_Revenue;
        if (self::DEBUG) {
            file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (revenueHandler.php: ".__LINE__.")".__METHOD__.' Note: Invoice_Revenue ; '. $revenueInfo['Invoice_Revenue'], FILE_APPEND);
        }
        $this->revenueInfo = $revenueInfo;

        return $revenueInfo;
    }

    private function getAgencyNumber($bookingAgent = false)
    {
        if (!is_array($bookingAgent)) {
            return null;
        }
        if (!$bookingAgent) {
            return null;
        }
        return $bookingAgent['agent_number'];
    }

    //@TODO should refactor same as Order's except estimate vs actual
    private function getGeneralLedgerServices($actualRecordModel, $update = false)
    {
        //generate services regular style.
        $distributionServicesArray = [];
        //This requires an Actual linked to the account.
        if ($lineItems = $actualRecordModel->getDetailLineItems()) {
            foreach ($lineItems as $section => $sectionArray) {
                foreach ($sectionArray as $itemSequence => $itemArray) {
                    $transactionType = $this->checkToSendRevenue($itemArray);
                    if ($transactionType === self::IGNORE_LINE_ITEM) {
                        continue;
                    } else {
                        $tempArray['TransactionType'] = $transactionType;
                    }

                    $tempArray['GCS_FLAG']           = $this->getGCSFlag($itemArray['GCS_Flag']);
                    $tempArray['Metro_Flag']         = $this->getMetroFlag($itemArray['Metro_Flag']);
                    $tempArray['ServiceCode']        = $itemArray['TariffItem'];
                    $tempArray['RatingSeq']          = $itemArray['DistributionSequence'];
                    $tempArray['Stop_Type_Code']     = $this->getStopCode($itemArray['Location'], $itemArray['TariffItem']);
                    $tempArray['Bill']               = ($itemArray['Distributable'] && $itemArray['ReadyToDistribute'])?self::CHAR_TRUE:self::CHAR_FALSE;
                    $tempArray['Company_ID']         = $this->getCompanyID($itemArray['RoleNameID']);
                    //$tempArray['MoveHQ_Distr_ServiceID']       = $itemArray['DetailLineItemId'];
                    $tempArray['MoveHQ_Actual_Item_Detail_ID'] = $this->ensureInteger($itemArray['DetailLineItemId']);
                    $tempArray['TariffNumber']                 = $itemArray['TariffItem'];
                    $tempArray['TariffSchd']                   = $this->getTariffName($actualRecordModel->get('effective_tariff'), $itemArray['TariffItem']) . ' ' . $itemArray['TariffSection'];
                    /*
                     * //@TODO: This maybe? I'm not sure right now.
                    if (preg_match('/:/',$itemArray['TariffSection'])) {
                        list($tempArray['TariffSchd'], $tempArray['TariffNumber']) = explode(':', $itemArray['TariffSection']);
                    } else {
                        $tempArray['TariffSchd'] = $itemArray['TariffSection'];
                        $tempArray['TariffNumber'] = $this->getTariffName($actualRecordModel->get('effective_tariff'));
                    }
                    */
                    $tempArray['PerformedDate']                 = $this->formatDate(\DateTimeField::convertToDBFormat($itemArray['DatePerformed']));
                    //'DatePerformed'            => $row['dli_date_performed'] ? DateTimeField::convertToUserFormat($row['dli_date_performed']) : '',
                    $tempArray['Distributable']                 = (!$itemArray['Distributed'] && $itemArray['Distributable'] && $itemArray['ReadyToDistribute'])?self::CHAR_TRUE:self::CHAR_FALSE;
                    $tempArray['BaseRate']                      = $this->ensureDecimalNumberOrNull($itemArray['BaseRate']);
                    $tempArray['Quantity']                      = $this->ensureDecimalNumber($itemArray['Quantity']);
                    $tempArray['InvoiceGrossAmount']            = $this->ensureDecimalNumber($itemArray['Gross']);
                    $tempArray['InvoiceDiscountRate']           = $this->ensureDecimalNumber($itemArray['InvoiceDiscountPct']);
                    $tempArray['InvoiceAmount']                 = $this->ensureDecimalNumber($itemArray['InvoiceCostNet']);
                    $tempArray['UnitOfMeasurement']             = $itemArray['UnitOfMeasurement']? :self::UNIT_OF_MEASUREMENT_DEFAULT;
                    $tempArray['UnitRate']                      = $this->ensureDecimalNumberOrNull($itemArray['UnitRate']);
                    $tempArray['DistributionAmount']            = $this->ensureDecimalNumber($itemArray['DistributableCostNet']);
                    //$tempArray['DistributionAmount'] = ....WROOOONNNNG
                    $tempArray['DistributionGross']             = $this->ensureDecimalNumber($itemArray['Gross']);
                    $tempArray['DistributionDiscount']          = $this->ensureDecimalNumber($itemArray['DistributableDiscountPct']);
                    $tempArray['DistributionNet']               = $this->ensureDecimalNumber($itemArray['DistributableCostNet']);
                    $tempArray['GeneralLedgerServiceProviders'] = $this->getGeneralLedgerServiceProviders($itemArray['ServiceProviders']);
                    $distributionServicesArray[]                = $tempArray;
                }
            }
        }

        return $distributionServicesArray;
    }

    private function checkToSendRevenue($itemArray)
    {
        if ($this->Invoice_Revenue == self::REVENUE_SEND_FLAG) {
            if ($itemArray['Distributed']) {
                //Don't send items that are already Distributed
                return self::IGNORE_LINE_ITEM;
            }
            if (!$itemArray['Distributable']) {
                //Don't send items that are not distributable.
                return self::IGNORE_LINE_ITEM;
            }
            //@NOTE: TransactionType was not existant, this may be incorrect
            if (!$itemArray['ReadyToDistribute']) {
                if (in_array($itemArray['DetailLineItemId'], $this->removeReadyToDistribute)) {
                    return self::$TRANSACTION_TYPE['delete'];
                } else {
                    //Don't send items that are not ready to distribute.
                    return self::IGNORE_LINE_ITEM;
                }
            } else {
                return $this->transactionItemAlreadySent($itemArray['DetailLineItemId'], 'DetailLineItemDistribute');
            }
        } else {
            if ($itemArray['Invoiced']) {
                //don't include already invoiced items.
                return self::IGNORE_LINE_ITEM;
            }
            if (!$itemArray['Invoiceable']) {
                //don't include items that are not invoiceable.
                return self::IGNORE_LINE_ITEM;
            }

            if (!$itemArray['ReadyToInvoice']) {
                if (in_array($itemArray['DetailLineItemId'], $this->removeReadyToInvoice)) {
                    return self::$TRANSACTION_TYPE['delete'];
                } else {
                    //don't include items that are not ready to invoice
                    return self::IGNORE_LINE_ITEM;
                }
            } else {
                return $this->transactionItemAlreadySent($itemArray['DetailLineItemId'], 'DetailLineItem');
            }
        }
        return self::IGNORE_LINE_ITEM;
    }

    protected function getGeneralLedgerServiceProviders($ServiceProviders)
    {
        $spArray = [];
        foreach ($ServiceProviders as $singleServiceProvider) {
            //sub array in service items
            //can be null.
            $tempArray['ServiceProvider']          = $this->getVendorICode($singleServiceProvider['vendor_id']); //i.code]);
            //can be null.
            $tempArray['MoveHQ_ServiceProviderID'] = $this->getVendorNumber($singleServiceProvider['vendor_id']); //VENxxx
            //if ONLY one provider set this to the full distance and weight from the actual.
            $tempArray['Distance']         = $this->ensureDecimalNumber($singleServiceProvider['split_miles']); //userentered
            $tempArray['Weight']           = $this->ensureDecimalNumber($singleServiceProvider['split_weight']); //userentered
            $tempArray['Split_Amount']     = $this->ensureDecimalNumber($singleServiceProvider['split_amount']); //userentered
            if ($tempArray['Split_Amount']) {
                //@TODO JG HERE > HACK
                $singleServiceProvider['split_percent'] = null;
            }
            $tempArray['Split_Percentage'] = $this->ensureDecimalNumber($singleServiceProvider['split_percent']); //userentered
            if ($tempArray['Split_Percentage']) {
                //@TODO JG HERE > HACK
                $tempArray['Split_Amount'] = $this->ensureDecimalNumber(0);
            }
            $spArray[] = $tempArray;
        }
        return $spArray;
    }

    protected function getGeneralLedgerParticipatingAgents($orderRecordModel)
    {
        $returnArray = [];
        $allPAs = $this->getParticipatingAgent($orderRecordModel->getId());
        foreach ($allPAs as $agent_type => $agentRecord) {
            $tempArray = [
                'ParticipatingAgent_type' => $agent_type,
                //'Company' => $this->getCompanyID($agentRecord['agent_number'])
                'Company' => $agentRecord['agent_number']
            ];
            $returnArray[] = $tempArray;
        }
        return $returnArray;
    }

    protected function getGeneralLedgerMoveRoles($orderRecordModel)
    {
        $returnArray = [];
        $allMoveRoles = $orderRecordModel->getMoveRoles();
        foreach ($allMoveRoles as $roleid => $moveRoleRecord) {
            $tempArray = [
                'RoleCode' => $moveRoleRecord['moveroles_role'],
                'Associate' => $this->getAssociateName($moveRoleRecord['moveroles_employees']),
                'ServiceProvider' => $this->getVendorNumber($moveRoleRecord['service_provider'])
            ];
            $returnArray[] = $tempArray;
        }
        return $returnArray;
    }

    protected function getGeneralLedgerSalesPeoples($orderRecordModel)
    {
        $returnArray = [];
        $allMoveRoles = $orderRecordModel->getMoveRoles();
        foreach ($allMoveRoles as $roleid => $moveRoleRecord) {
            $tempArray = [
                'SalesPerson' => $this->getAssociateName($moveRoleRecord['moveroles_employees']),
                'SLCode' => $this->getSalesPersonCode($moveRoleRecord['moveroles_employees']),
                'SalesCommPct' => $moveRoleRecord['sales_commission'],
                'MoveHQ_SalesPersonID' => $this->getSalesPersonId($moveRoleRecord['moveroles_employees'])
            ];
            $returnArray[] = $tempArray;
        }
        return $returnArray;
    }

    //we'll use this to trigger specific events and return an object in case the user wants access to the whole thing.
    public static function triggerRevenueAPI($trigger, array $inputVars = [])
    {
        if (!(strtolower(getenv('INSTANCE_NAME')) == 'graebel' && getenv('GVL_API_ON'))) {
            //Don't trigger the API
            return;
        }
        $self = new static($inputVars);
        switch ($trigger) {
            default:
        }

        return $self->createRevenue($inputVars['invoiceFlag'], true);
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
                    file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (revenueHandler.php: ".__LINE__.") Actual record not found failing.", FILE_APPEND);
                }

                return false;
            }
        }

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
    protected function hasDistributableServices($servicesArray)
    {
        if (!is_array($servicesArray)) {
            return false;
        }
        if (count($servicesArray) <= 0) {
            return false;
        }
        foreach ($servicesArray as $singleService) {
            if ($singleService['Distributable'] == self::CHAR_TRUE) {
                return true;
            }
        }
    }

    /**
     * @return bool
     */
    protected function createRevenueHasBeenSent()
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
        $url = self::$HOST_NAME.$this->CREATE_REVENUE_URI;
        if ($this->httpClient) {
            $url = $this->httpClient->url;
        }
        return $url;
    }

    /**
     *
     * @string $shippingAuthority
     * @string $businessLine
     *
     * @return string
     */
    protected function getGoodsType($goodsType, $businessLine)
    {
        //make lower so the array is simpler.
        $goodsType = strtolower($goodsType);
        $businessLine = strtolower($businessLine);

        if (self::$GoodsType[$businessLine]) {
            return self::$GoodsType[$businessLine];
        }

        if (self::$GoodsType[$goodsType]) {
            return self::$GoodsType[$goodsType];
        }

        return self::$GoodsType['default'];
    }

    /**
     * @param $agentID
     *
     * @return int
     */
    protected function getCompanyID($agentID)
    {
        return $this->getRecordField($agentID, 'agent_number', 'Agents');
    }

    protected function getAssociateName($employeeID)
    {
        return $this->getRecordLabel($employeeID, 'Employees');
    }
    protected function getVendorNumber($vendorID)
    {
        return $this->getRecordField($vendorID, 'vendor_no', 'Vendors');
    }

    protected function getVendorICode($vendorID)
    {
        return $this->getRecordField($vendorID, 'icode', 'Vendors');
    }

    //@TODO JG HERE: one of these is wrong
    protected function getSalesPersonCode($employeeID)
    {
        return $this->getRecordField($employeeID, 'employee_no', 'Employees');
    }

    //@TODO JG HERE: one of these is wrong
    protected function getSalesPersonId($employeeID)
    {
        return $employeeID;
        return $this->getRecordField($employeeID, 'employee_no', 'Employees');
    }

    //Sigh this seemed like a good idea at the time.
    protected function getRecordField($recordID, $fieldName, $moduleName = false)
    {
        if (!$recordID) {
            return null;
        }

        if (!$fieldName) {
            return null;
        }

        if (
            isset($this->recordCache[$recordID.$moduleName]) &&
            isset($this->recordCache[$recordID.$moduleName][$fieldName])
        ) {
            return $this->recordCache[$recordID.$moduleName][$fieldName];
        }

        try {
            $recordModel = \Vtiger_Record_Model::getInstanceById($recordID, $moduleName);
            if ($recordModel) {
                $this->recordCache[$recordID.$moduleName][$fieldName] = $recordModel->get($fieldName);
                return $this->recordCache[$recordID.$moduleName][$fieldName];
            }
        } catch (\Exception $ex) {
            //DON'T CARE!
        }
        return null;
    }

    protected function getRecordLabel($recordID, $moduleName = false)
    {
        if (!$recordID) {
            return;
        }

        $fieldName = 'displayName';

        if (
            isset($this->recordCache[$recordID.$moduleName]) &&
            isset($this->recordCache[$recordID.$moduleName][$fieldName])
        ) {
            return $this->recordCache[$recordID.$moduleName][$fieldName];
        }

        try {
            if ($recordModel = \Vtiger_Record_Model::getInstanceById($recordID, $moduleName)) {
                $this->recordCache[$recordID.$moduleName][$fieldName] = $recordModel->getDisplayName();
                return $this->recordCache[$recordID.$moduleName][$fieldName];
            }
        } catch (\Exception $ex) {
            //DON'T CARE!
        }
        return;
    }

    private function getMetroFlag($Metro_Flag)
    {
        if (!isset($Metro_Flag)) {
            //If it's NOT set (this means not even false)
            return self::METRO_FLAG_DEFAULT;
        }
        if (
            $Metro_Flag === false ||
            strtolower($Metro_Flag) == strtolower(self::CHAR_FALSE)
        ) {
            return self::CHAR_FALSE;
        }
        return self::CHAR_TRUE;
    }

    private function getGCSFlag($GCS_FLAG)
    {
        if (!$GCS_FLAG) {
            return self::GCS_FLAG_DEFAULT;
        }

        return $GCS_FLAG;

//        //@NOTE: It can be a B? so we can't do this.
//        if ($GCS_FLAG == 1) {
//            return self::CHAR_TRUE;
//        }
//        if (strtolower($GCS_FLAG) == 'y') {
//            return self::CHAR_TRUE;
//        }
//        return self::CHAR_FALSE;
    }

    private function getStopCode($location, $tariffCode)
    {
        if (self::FALL_BACK_STOP_CODE) {
            //@NOTE: This is a fallback that we are going to hope for the best on.
            //@TODO this checks every one tariff code in the database which could get a bit excessive.
            $location = $this->recheckStopCode($tariffCode, $location);
        }

        if (!$location) {
            return;
        }

        if (isset(self::$LOCATION_STOP_CODE[strtolower($location)])) {
            return self::$LOCATION_STOP_CODE[strtolower($location)];
        }

        return;
    }

    private function recheckStopCode($tariffCode, $default = '')
    {
        if ($default) {
            return $default;
        }
        if (!$tariffCode) {
            return $default;
        }
        if (array_key_exists($tariffCode, $this->stopTypeStorage)) {
            return $this->stopTypeStorage[$tariffCode];
        }
        $db = \PearDatabase::getInstance();
        $stmt   = 'SELECT `stop_type_code` FROM `vtiger_gvl_tariff_item_map` WHERE `service_code` = ? LIMIT 1';
        $result = $db->pquery($stmt, [$tariffCode]);
        if (!method_exists($result, 'fetchRow')) {
            return $default;
        }
        if ($row = $result->fetchRow()) {
            $this->stopTypeStorage[$tariffCode] = $row['stop_type_code'];
            return $this->stopTypeStorage[$tariffCode];
        }
        return $default;
    }
}
