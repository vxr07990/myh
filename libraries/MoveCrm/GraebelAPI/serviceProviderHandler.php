<?php

namespace MoveCrm\GraebelAPI;

include_once('libraries/MoveCrm/GraebelAPI/APIHandler.php');
include_once('modules/Vtiger/models/Record.php');

/*
 *
 * serviceProviderHandler extends the APIHandler to allow the createCustomer function
 * and all those things that need pulled in to do that call.
 *
 */
class serviceProviderHandler extends APIHandler
{
    protected static $CREATE_SP_URI = '/api/spdefaults/createSPDefaults';
    public $spInfo;

    const SHOW_SINGLE_VENDOR_AGREEMENT = false;
    const TRIGGER_UPDATE = 1;

    /**
     * Construct new instance.
     *
     * @param array $initVars
     */
    public function __construct(array $initVars = [])
    {
        parent::__construct($initVars);

        if (array_key_exists('vendorsNumber', $this->initVars)) {
            $this->vendorsNumber = $this->initVars['vendorsNumber'];
        }
        if (array_key_exists('vendorAgreementsNumber', $this->initVars)) {
            $this->vendorAgreementsNumber = $this->initVars['vendorAgreementsNumber'];
        }
        $this->vendorAgreementsRecordModel = false;
        $this->vendorsRecordModel = false;
    }

    /**
     * do the things to create a customer now. recordNumber can override any of the record models used, order/contact/account
     * @param bool $recordNumber
     *
     * @return null|\stdClass
     */
    public function createSPDefault($recordNumber = false)
    {
        if ($recordNumber) {
            $this->recordNumber = $recordNumber;
        }

        //pull the records we need to work with.  //either Vendor and an array of Vendor Agreeements.
        $foundRecord = $this->initializeRecordModels();

        if ($foundRecord) {
            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (serviceProviderHandler.php: " . __LINE__ . ") Found at least one record to use for SP Default Create", FILE_APPEND);
            }
            $spInfoArray = $this->pullSPInfo();
            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (serviceProviderHandler.php: " . __LINE__ . ") Sending a POST request for SP Default Create", FILE_APPEND);
            }
            return $this->postRequest($spInfoArray, self::$CREATE_SP_URI);
            /*
             * all calls are post.
            if($this->trigger == self::TRIGGER_UPDATE) {
                return $this->putRequest($spInfoArray, self::$CREATE_SP_URI);
            } else {
                return $this->postRequest($spInfoArray, self::$CREATE_SP_URI);
            }
            */
        } else {
            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (serviceProviderHandler.php: " . __LINE__ . ") Failed to find a record to use for SP Default Create", FILE_APPEND);
            }
            return null;
        }
    }

    /**
     * returns the service provider's information
     * So we source information from the vendor agreement Record Model which is attached ot the Vendorwhich is attached to the Vendor
     *
     * @return array|mixed
     */
    private function pullSPInfo()
    {
        $spInfo = [];
        if (
            $this->vendorsRecordModel &&
            method_exists($this->vendorsRecordModel, 'getModuleName') &&
            $this->vendorsRecordModel->getModuleName() == 'Vendors'
        ) {
            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (serviceProviderHandler.php: " . __LINE__ . ") Pulling Vendors Information for SP Default Create", FILE_APPEND);
            }
            //This really seems to be missing from the API doc, so adding this in for now.
            $spInfo['vendorname'] = $this->vendorsRecordModel->get('vendorname');
            $spInfo['vendor_no'] = $this->vendorsRecordModel->get('vendor_no');
            $spInfo['vendor_status'] = $this->vendorsRecordModel->get('vendor_status');
            $spInfo['fein'] = $this->vendorsRecordModel->get('fein');
            $spInfo['icode'] = $this->vendorsRecordModel->get('icode');
        }

        if (is_array($this->vendorAgreementsRecordModelArray)) {
            foreach ($this->vendorAgreementsRecordModelArray as $recordID => $vaRecordModel) {
                if (
                    $vaRecordModel &&
                    method_exists($vaRecordModel, 'getModuleName') &&
                    $vaRecordModel->getModuleName() == 'VendorAgreements'
                ) {
                    if (self::DEBUG) {
                        file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (serviceProviderHandler.php: ".__LINE__.") Pulling Vendors Agreement Information for SP Default Create", FILE_APPEND);
                    }

                    $spInfo['SL_CODE'] = $vaRecordModel->get('va_i_code');

                    $speRecords = $vaRecordModel->getGuestModuleRecords('serviceproviderexceptions');

                    foreach ($speRecords as $speRecordID => $spRecordModel) {
                        $speInfo = [];
                        $speInfo['AUTYPE_ID']          = $spRecordModel->get('spe_authority');
                        $speInfo['CLASS']              = $spRecordModel->get('spe_class');
                        $speInfo['SITEM_CODE']         = $spRecordModel->get('spe_standard_item');
                        $speInfo['BREAKDOWN']          = $spRecordModel->get('spe_mileage');
                        $speInfo['EFFDATE']            = $spRecordModel->get('spe_effective_date');
                        $speInfo['PCT']                = $spRecordModel->get('spe_paid_to_ic');
                        $speInfo['HOURLY']             = $spRecordModel->get('spe_per_service');
                        $speInfo['BREAKDOWN_SCHEDULE'] = $spRecordModel->get('spe_mileage');
                        $speInfo['CANCELDATE']         = $spRecordModel->get('spe_cancel_date');
                        $speInfo['TARIFF_NUMBER']      = $spRecordModel->get('spe_tariff');
                        $speInfo['Created_On']         = $spRecordModel->get('createdtime');
                        $speInfo['Created_By']         = $this->retreiveUsernameFromID($spRecordModel->get('created_user_id'));
                        $speInfo['Modified_On']        = $spRecordModel->get('modifiedtime');
                        $speInfo['Modified_By']        = $this->retreiveUsernameFromID($spRecordModel->get('modifiedby'));
                        $spInfo['SERVICE_PROVIDER_EXCEPTIONS'][] = $speInfo;
                    }
                }
            }
        }

        $this->spInfo = $spInfo;

        return $spInfo;
    }

    public static function triggerAPI($trigger, array $inputVars = [])
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
        return $self->createSPDefault();
    }

    /**
     * Override the main one, because here we only want Vendor and Vendor Agreements.
     *
     * @return bool
     */
    protected function initializeRecordModels()
    {
        $ok = false;
        if ($this->recordNumber) {
            try {
                if ($unknownRecordModel = \Vtiger_Record_Model::getInstanceById($this->recordNumber)) {
                    switch ($unknownRecordModel->getModuleName()) {
                        case 'Vendors':
                            $this->vendorsRecordModel = $unknownRecordModel;
                            $this->vendorsNumber = $this->recordNumber;
                            break;
                        case 'VendorsAgreements':
                            $this->vendorAgreementsRecordModel = $unknownRecordModel;
                            $this->vendorAgreementsNumber = $this->recordNumber;
                            break;
                        default:
                    }
                }
            } catch (\Exception $ex) {
                //throw $ex; //accept this exception
            }
        }

        //@TODO: find a better way to set the array for this.
        //I am not happy with this... but it's at least a loop instead of each done seperately.
        foreach (['vendors', 'vendorAgreements'] as $module) {
            try {
                $this->pullRecordModel($module.'Number', $module.'RecordModel');
                $ok = true;
                if ($module == 'vendorAgreements') {
                    //@TODO: eventually make it so we can pull the vendors number from the vendorAgreement record.
                    if (!$this->vendorsNumber && $this->vendorAgreementsRecordModel) {
                        //$this->vendorsNumber = $this->vendorAgreementsRecordModel->getParentRecordID();
                    }
                }
            } catch (\Exception $ex) {
                //throw $ex;
                //it's fine.
            }
        }

        //This block should probably be a function
        if (self::SHOW_SINGLE_VENDOR_AGREEMENT && $this->vendorAgreementsRecordModel) {
            //OK we are good to go!
            $this->vendorAgreementsRecordModelArray[$this->vendorAgreementsRecordModel->get('id')] = $this->vendorAgreementsRecordModel;
        } else {
            if ($this->vendorsRecordModel) {
                $this->vendorAgreementsRecordModelArray = $this->vendorsRecordModel->getAllVendorAgreements();
            }
            //use the one passed in to override the one that might by attached from the above function.
            if ($this->vendorAgreementsRecordModel) {
                $this->vendorAgreementsRecordModelArray[$this->vendorAgreementsRecordModel->get('id')] = $this->vendorAgreementsRecordModel;
            }
        }

        return $ok;
    }
}
