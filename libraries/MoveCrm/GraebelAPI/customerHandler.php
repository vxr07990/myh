<?php

namespace MoveCrm\GraebelAPI;

include_once('libraries/MoveCrm/GraebelAPI/APIHandler.php');
include_once('modules/Vtiger/models/Record.php');

/*
 *
 * customerHandler extends the APIHandler to allow the createCustomer function
 * and all those things that need pulled in to do that call.
 *
 */
class customerHandler extends APIHandler
{
    protected static $CREATE_CUSTOMER_URI = '/api/customer/createCustomer';
    public $customerInfo;
    public $addressBook;

    //for tracking posted status.
    protected $TABLE_METHOD    = 'createCustomer';

    const TRIGGER_UPDATE = 1;
    //@TODO: Their API V3.02 document has defined only this as a valid LeadSource
    const OVERRIDE_LEADSOURCE = false; //'Web';  //enter a string if required: Web was required.
    //@TODO: Their API V3.02 document has defined only this type as valid.
    const OVERRIDE_INVOICE_DOCUMENT_FORMAT = 'PDF';

    /**
     * Construct new instance.
     *
     * @param array $initVars
     */
    public function __construct(array $initVars = [])
    {
        parent::__construct($initVars);

        if (array_key_exists('contactNumber', $this->initVars)) {
            $this->contactNumber = $this->initVars['contactNumber'];
        }
        if (array_key_exists('accountNumber', $this->initVars)) {
            $this->accountNumber = $this->initVars['accountNumber'];
        }
        $this->orderRecordModel = false;
        $this->accountRecordModel = false;
        $this->contactRecordModel = false;
    }

    /**
     * do the things to create a customer now. recordNumber can override any of the record models used, order/contact/account
     * @param bool $recordNumber
     *
     * @return null|\stdClass
     */
    public function createCustomer($recordNumber = false)
    {
        if ($recordNumber) {
            $this->recordNumber = $recordNumber;
        }

        //I have completely written myself into a hole.
        //The idea of update the customer record with new info without the order has broken my mind!
        //So I'm lost in my nonsense.
        $foundRecord = $this->initializeRecordModels();

        if ($foundRecord) {
            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (customerHandler.php: " . __LINE__ . ") Found at least one record to use for Customer Create", FILE_APPEND);
            }
            $update = $this->createCustomerHasBeenSent();
            $customerInfoArray = $this->pullCustomerInfo();
            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (customerHandler.php: " . __LINE__ . ") Sending a POST request for Customer Create", FILE_APPEND);
            }
            return $this->postRequest($customerInfoArray, self::$CREATE_CUSTOMER_URI);
            /*
             * all calls are post.
            if($this->trigger == self::TRIGGER_UPDATE) {
                return $this->putRequest($customerInfoArray, self::$CREATE_CUSTOMER_URI);
            } else {
                return $this->postRequest($customerInfoArray, self::$CREATE_CUSTOMER_URI);
            }
            */
        } else {
            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (customerHandler.php: " . __LINE__ . ") Failed to find a record to use for Customer Create", FILE_APPEND);
            }
            return null;
        }
    }

    /**
     * returns the customer's information
     * So we source information from the Contact Record Model and the Account Record Model and the Order record model
     * if we have an order record model we can use it's linked contact and account.
     * Data order of precedence Order Record Model, then Account, then Contact.
     *
     * @return array|mixed
     */
    private function pullCustomerInfo()
    {
        $custInfo = [];
        if (
            $this->contactRecordModel &&
            method_exists($this->contactRecordModel, 'getModuleName') &&
            $this->contactRecordModel->getModuleName() == 'Contacts'
        ) {
            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (customerHandler.php: " . __LINE__ . ") Pulling Contact Information for Customer Create", FILE_APPEND);
            }
            $relatedContactModel      = $this->contactRecordModel;
            $custInfo['CustomerName'] = $relatedContactModel->get('firstname').' '.$relatedContactModel->get('lastname');
            $custInfo['FirstName']    = $relatedContactModel->get('firstname');
            $custInfo['LastName']     = $relatedContactModel->get('lastname');

            /*
             * The following may be overwritten by the account information
             */
            $custInfo['CompanyName'] = $custInfo['CustomerName'];
            $custInfo['CustomerID']   = $relatedContactModel->get('contact_no');
            $custInfo['Email']    = $relatedContactModel->get('email1');
            $custInfo['Phone']    = $relatedContactModel->get('phone');
            $custInfo['LeadSource']    = $relatedContactModel->get('leadsource');
            $custInfo['CustomerType']  = self::$CUSTOMER_TYPE[self::CUSTOMER_CUSTOMER_TYPE_DEFAULT];

            /*
             * The following may be overwritten by accounts
             */
            $tempArray['AddressLine1'] = $custInfo['CA_BillingAddress1'] = $relatedContactModel->get('mailingstreet');
            $tempArray['AddressLine2'] = $custInfo['CA_BillingAddress2'] = $relatedContactModel->get('mailingpobox');
            $tempArray['City']         = $custInfo['CA_City'] = $relatedContactModel->get('mailingcity');
            $tempArray['State']        = $custInfo['CA_State'] = $this->translateStateToTwoChar($relatedContactModel->get('mailingstate'));
            $tempArray['ZIP']          = $custInfo['CA_PostalCode'] = $relatedContactModel->get('mailingzip');
            //@NOTE: I figure they may want country, so adding it commented
            $tempArray['Country'] = $custInfo['CA_Country'] = $relatedContactModel->get('mailingcountry');
        }

        if (
            $this->accountRecordModel &&
            method_exists($this->accountRecordModel, 'getModuleName') &&
            $this->accountRecordModel->getModuleName() == 'Accounts'
        ) {
            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (customerHandler.php: " . __LINE__ . ") Pulling Account Information for Customer Create", FILE_APPEND);
            }
            $relatedAccountModel         = $this->accountRecordModel;

            $custInfo['CustomerType']  = self::$CUSTOMER_TYPE[strtolower($relatedAccountModel->get('billing_type'))];
            $custInfo['CompanyName']    = $relatedAccountModel->get('accountname');

            $custInfo['CreditLimit']     = $relatedAccountModel->get('credit_limit');
            $custInfo['CreditCheckPass'] = $relatedAccountModel->get('credit_check_pass')?self::CHAR_TRUE:self::CHAR_FALSE;
            $custInfo['CustomerBalance'] = $relatedAccountModel->get('account_balance');
            $custInfo['NationalAccountNumber'] = $relatedAccountModel->get('national_account_number');
            $custInfo['DUNSNumber'] = $relatedAccountModel->get('duns_number');

            /*
             * The following overrides contact information
             */
            $custInfo['CustomerID'] = $relatedAccountModel->get('account_no');
            //$custInfo['CustomerID']   = $relatedAccountModel->getId();
            $custInfo['Email']    = $relatedAccountModel->get('email1');
            $custInfo['Phone']    = $relatedAccountModel->get('phone');
            $custInfo['LeadSource'] = $relatedAccountModel->get('leadsource');
            $custInfo['CustomerName'] = $custInfo['CompanyName'];

            /*
             * The following overrides contact information
             */
            //iff multiples exist select "first" one (aka top of list with active = checked and business_line match)
            $custInfo['CA_BillingAddress1']    = $relatedAccountModel->get('address1');
            $custInfo['CA_BillingAddress2']    = $relatedAccountModel->get('address2');
            $custInfo['CA_City']    = $relatedAccountModel->get('city');
            $custInfo['CA_State']    = $this->translateStateToTwoChar($relatedAccountModel->get('state'));
            $custInfo['CA_PostalCode']    = $relatedAccountModel->get('zip');
            $custInfo['CA_Country']    = $relatedAccountModel->get('country');
            //@NOTE: only address from the business_line are included in the address book.
//            $this->pushAddressBook(
//                     $relatedAccountModel->get('address1'),
//                     $relatedAccountModel->get('address2'),
//                     $relatedAccountModel->get('city'),
//                     $relatedAccountModel->get('state'),
//                     $relatedAccountModel->get('zip'),
//                     $relatedAccountModel->get('country'),
//                     $relatedAccountModel->get('accountname'),
//                     $relatedAccountModel->get('accountname'),
//                     $relatedAccountModel->getId()
//                );

            $deliveryPreference = self::CUSTOMER_DELIVERY_PREFERENCE_DEFAULT;
            $invoiceDocumentFormat = self::CUSTOMER_INVOICE_DOCUMENT_FORMAT_DEFAULT;
            $currentInvoiceSettings = \Accounts_Record_Model::getCurrentInvoiceSettings($relatedAccountModel->getId())[0];
            if (is_array($currentInvoiceSettings)) {
                if (array_key_exists('document_format', $currentInvoiceSettings) && $currentInvoiceSettings['document_format']) {
                    $invoiceDocumentFormat = $currentInvoiceSettings['document_format'];
                }
                if (array_key_exists('invoice_delivery', $currentInvoiceSettings) && $currentInvoiceSettings['invoice_delivery']) {
                    $deliveryPreference = $currentInvoiceSettings['invoice_delivery'];
                }
                //These are not defaulted and can be empty.
                if (array_key_exists('invoice_template', $currentInvoiceSettings) && $currentInvoiceSettings['invoice_template']) {
                    $custInfo['InvoiceTemplate'] = self::$INVOICE_TEMPLATE[strtolower($currentInvoiceSettings['invoice_template'])];
                }
                if (array_key_exists('invoice_packet', $currentInvoiceSettings) && $currentInvoiceSettings['invoice_packet']) {
                    $custInfo['Packet'] = self::$INVOICE_PACKET[strtolower($currentInvoiceSettings['invoice_packet'])];
                }
            }
            //@TODO: REmove one of thse when API finalized.
            $custInfo['DeliveryPreferences']    = self::$DELIVERY_PREFERENCE[strtolower($deliveryPreference)];
            $custInfo['DeliveryPreference']    = self::$DELIVERY_PREFERENCE[strtolower($deliveryPreference)];
            $custInfo['InvoiceDocumentFormat'] = self::$INVOICE_DOCUMENT_FORMAT[strtolower($invoiceDocumentFormat)];

            $accountAddress = \Accounts_Record_Model::getAccountsBillingAddresses($relatedAccountModel->getId());
            foreach ($accountAddress as $addressArray) {
                $this->pushAddressBook(
                    $addressArray['address1'],
                    $addressArray['address2'],
                    $addressArray['city'],
                    $addressArray['state'],
                    $addressArray['zip'],
                    $addressArray['country'],
                    $addressArray['address_desc'],
                    $addressArray['company'],
                    $addressArray['id'],
                    $addressArray['commodity'],
                    $addressArray['active']
                );
            }
        }

        //NOTHING comes from the ORDER for customer creation v3.06.
//        if (
//            $this->orderRecordModel &&
//            method_exists($this->orderRecordModel, 'getModuleName') &&
//            $this->orderRecordModel->getModuleName() == 'Orders'
//        ) {
//            if (self::DEBUG) {
//                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (customerHandler.php: " . __LINE__ . ") Pulling Order Information for Customer Create", FILE_APPEND);
//            }
//            //These override the ones from the Account.
//            $custInfo['InvoiceTemplate']       = self::$INVOICE_TEMPLATE[strtolower($this->orderRecordModel->get('invoice_format'))];
//            $custInfo['Packet']                = self::$INVOICE_PACKET[strtolower($this->orderRecordModel->get('invoice_pkg_format'))];
//            $custInfo['DeliveryPreferences']    = self::$DELIVERY_PREFERENCE[strtolower($this->orderRecordModel->get('invoice_delivery_format'))];
//            $custInfo['DeliveryPreference']    = self::$DELIVERY_PREFERENCE[strtolower($this->orderRecordModel->get('invoice_delivery_format'))];
//            $custInfo['InvoiceDocumentFormat'] = self::$INVOICE_DOCUMENT_FORMAT[strtolower($this->orderRecordModel->get('invoice_document_format'))];
//
//            /*
//             * The following overrides contact and account information
//             */
//            $custInfo['CA_BillingAddress1'] = $this->orderRecordModel->get('bill_street');
//            $custInfo['CA_BillingAddress2'] = $this->orderRecordModel->get('bill_pobox');
//            $custInfo['CA_City'] = $this->orderRecordModel->get('bill_city');
//            $custInfo['CA_State'] = $this->translateStateToTwoChar($this->orderRecordModel->get('bill_state'));
//            $custInfo['CA_PostalCode'] = $this->orderRecordModel->get('bill_code');
//            custInfo['CA_Country'] = $this->orderRecordModel->get('bill_country');
//
//            //@NOTE: only address from the business_line are included in the address book.
////            $this->pushAddressBook(
////                 $this->orderRecordModel->get('bill_street'),
////                 $this->orderRecordModel->get('bill_pobox'),
////                 $this->orderRecordModel->get('bill_city'),
////                 $this->orderRecordModel->get('bill_state'),
////                 $this->orderRecordModel->get('bill_code'),
////                 $this->orderRecordModel->get('bill_country'),
////                 $custInfo['CompanyName'],
////                 $custInfo['CompanyName'],
////                 $this->orderRecordModel->getId()
////            );
//            $businessLine = $this->orderRecordModel->get('business_line');
//        }

        $custInfo['CustomerAddressbook'] = $this->getAddressBook();

        //@NOTE: Set our overrides here, they are defined at the top
        $custInfo['LeadSource'] = (self::OVERRIDE_LEADSOURCE ? self::OVERRIDE_LEADSOURCE : $custInfo['LeadSource']);
        $custInfo['InvoiceDocumentFormat'] = (self::OVERRIDE_INVOICE_DOCUMENT_FORMAT ? self::OVERRIDE_INVOICE_DOCUMENT_FORMAT : $custInfo['InvoiceDocumentFormat']);

        //@TODO REFACTOR FOR THE LOVE OF IT ALL
        if ($custInfo['CustomerType'] == 'HHG') {
            $custInfo['CreditLimit']     = null;
            $custInfo['CreditCheckPass'] = null;
        } elseif ($custInfo['CustomerType'] == 'COD') {
            //@NOTE: 2016-08-17: Based on an email from Sesha, COD type needs to be 0 and empty string.
            //@NOTE: 2016-08-18: Based on email with Radhika COD type needs to only use NULL.
            $custInfo['CreditLimit']     = null;
            $custInfo['CreditCheckPass'] = null;
            //@TODO: apply error out rules for COD types.
            if (!$custInfo['FirstName']) {
                throw new \Exception('First Name is required for COD.');
            }
            if (!$custInfo['LastName']) {
                throw new \Exception('Last Name is required for COD.');
            }
        } else {
            //remove fname and lname IF not COD
            $custInfo['FirstName'] = null;
            $custInfo['LastName']  = null;
        }

        $this->customerInfo = $custInfo;

        return $custInfo;
    }

    protected function pushAddressBook($address1, $address2, $city, $state, $zip, $country, $address_desc, $companyName, $id, $businessLine = false, $active = true)
    {
        if (!$id) {
            return false;
        }
        if ($this->addressBook[$id]) {
            //Already Exists.
            return true;
        }
        $businessLine = explode(' |##| ', $businessLine);
        $this->addressBook[$id] = [
            "AddressLine1"   => $address1,
            "AddressLine2"   => $address2,
            "City"           => $city,
            //@TODO: should this call be here?
            "State"          => $this->translateStateToTwoChar($state),
            "ZIP"            => $zip,
            "Country"        => $country,
            "AddressBookID"  => $id,
            "AddressDescription"  => $address_desc,
            "CompanyName"    => $companyName,
            "BusinessLine"  => $businessLine,
            "Active"  => $active,
            "TransType"      => self::$TRANSACTION_TYPE['insert']
        ];
        return true;

        /*
        //@TODO: I don't know how to get the address book ID unless we have an address block.
        $tempArray['AddressBookID']    = '';
        if($this->trigger == self::TRIGGER_UPDATE) {
            //@NOTE: V3.02 says TransactionType, however, discussion with API team sets this as TransType.
            //$tempArray['TransactionType'] = self::$TRANSACTION_TYPE['update'];
            $tempArray['TransType'] = self::$TRANSACTION_TYPE['update'];
            file_put_contents('logs/devLog.log', "\n JG HERE (customerHandler.php:" . __LINE__ . ")", FILE_APPEND);
        } else {
            //@NOTE: V3.02 says TransactionType, however, discussion with API team sets this as TransType.
            //$tempArray['TransactionType'] = self::$TRANSACTION_TYPE['insert'];
            $tempArray['TransType'] = self::$TRANSACTION_TYPE['insert'];
            file_put_contents('logs/devLog.log', "\n JG HERE (customerHandler.php:" . __LINE__ . ")", FILE_APPEND);
        }
        //v3.02 defines this as YES/NO discussion determined this to be instead true/false binary.
        //@TODO: FIRST active billing Address is DefaultAddress rest no.
        $tempArray['DefaultAddress'] = self::BIT_TRUE;
        $custInfo['CustomerAddressbook'][] = $tempArray;
        */
    }

    /**
     * @param string $businessLine
     *
     * @return array
     */
    private function getAddressBook($businessLine)
    {
        $addressesArray = [];
        $foundDefault = false;

        if (!is_array($this->addressBook)) {
            return $addressesArray;
        }

        foreach ($this->addressBook as $addressID => $singleAddress) {
            if (!is_array($singleAddress)) {
                continue;
            }
            $tempArray = [
                'AddressBookID' => $addressID,
                'AddressLine1' => $singleAddress['AddressLine1'],
                'AddressLine2' => $singleAddress['AddressLine2'],
                'City'         => $singleAddress['City'],
                //@TODO: should this call be here?
                'State'        => $this->translateStateToTwoChar($singleAddress['State']),
                'ZIP'          => $singleAddress['ZIP'],
                'Country'      => $singleAddress['Country'],
                'TransType'    => self::$TRANSACTION_TYPE['insert']
            ];
            $tempArray['DefaultAddress'] = self::BIT_FALSE;
            //@TODO: fix.
            if (!$foundDefault) {
                if ($singleAddress['Active']) {
                    if ($businessLine) {
                        //if ($singleAddress['BusinessLine'] == $businessLine) {
                        if (in_array($businessLine, $singleAddress['BusinessLine'])) {
                            //set the first found active record with this business line to active.
                            $tempArray['DefaultAddress'] = self::BIT_TRUE;
                            $foundDefault = true;
                        }
                    } else {
                        //NO business lines were passed in so set the first found active record to default.
                        $tempArray['DefaultAddress'] = self::BIT_TRUE;
                        $foundDefault = true;
                    }
                }
            }

            $addressesArray[] = $tempArray;
        }

        return $addressesArray;
    }

    public static function triggerCustomerAPI($trigger, array $inputVars = [])
    {
        if (!(strtolower(getenv('INSTANCE_NAME')) == 'graebel' && getenv('GVL_API_ON'))) {
            //Don't trigger the API
            return;
        }
        $self = new static($inputVars);
        switch ($trigger) {
            case 'update':
                $self->set('trigger', self::TRIGGER_UPDATE);
                break;
            default:
        }
        return $self->createCustomer();
    }

    /**
     * @return bool
     */
    protected function createCustomerHasBeenSent()
    {
        $update = false;
        //@TODO: this is confused because what if there is no Account?  do I have to also check for ContactNumber too?
        if ($this->accountNumber) {
            $update = $this->hasBeenSent($this->TABLE_METHOD, $this->accountNumber);
        } elseif ($this->contactNumber) {
            $update = $this->hasBeenSent($this->TABLE_METHOD, $this->contactNumber);
        }

        if ($update) {
            $this->set('trigger', self::TRIGGER_UPDATE);
        }
        return $update;
    }

    /**
     * @return mixed
     */
    protected function getLogRecordId()
    {
        if ($this->accountNumber) {
            return $this->accountNumber;
        }
        if ($this->contactNumber) {
            return $this->contactNumber;
        }
    }

    /**
     * @return string
     */
    protected function getLogUrl()
    {
        $url = self::$HOST_NAME.$this->CREATE_CUSTOMER_URI;
        if ($this->httpClient) {
            $url = $this->httpClient->url;
        }
        return $url;
    }

    protected function initializeRecordModels()
    {
        $ok = parent::initializeRecordModels();

        if (!$this->accountRecordModel) {
            //don't bother without an account record
            return $ok;
        }

        if ($this->contactRecordModel) {
            //already has a contact so let it go.
            return $ok;
        }

        $this->contactNumber = $this->accountRecordModel->get('transferee_contact');
        if ($this->contactNumber) {
            //set the contact record from the account record.
            $this->pullRecordModel('contactNumber', 'contactRecordModel');
        }

        return $ok;
    }
}
