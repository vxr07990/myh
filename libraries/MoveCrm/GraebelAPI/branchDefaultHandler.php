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
class branchDefaultHandler extends APIHandler
{
    protected static $CREATE_BRANCH_URI = '/api/branchdefaults/createBranchDefaults';
    public $bdInfo;

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

        if (array_key_exists('agentsNumber', $this->initVars)) {
            $this->agentsNumber = $this->initVars['agentsNumber'];
        }
        if (array_key_exists('branchDefaultsNumber', $this->initVars)) {
            $this->branchDefaultsNumber = $this->initVars['branchDefaultsNumber'];
        }
        $this->branchDefaultsRecordModel = false;
        $this->agentsRecordModel = false;
    }

    /**
     * do the things to create a customer now. recordNumber can override any of the record models used, order/contact/account
     * @param bool $recordNumber
     *
     * @return null|\stdClass
     */
    public function createBranchDefault($recordNumber = false)
    {
        if ($recordNumber) {
            $this->recordNumber = $recordNumber;
        }

        //pull the records we need to work with.  //either Agents and an array of Branch Defaults
        $foundRecord = $this->initializeRecordModels();

        if ($foundRecord) {
            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (serviceProviderHandler.php: " . __LINE__ . ") Found at least one record to use for Branch Default Create", FILE_APPEND);
            }
            $bdInfoArray = $this->pullBranchInfo();
            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (serviceProviderHandler.php: " . __LINE__ . ") Sending a POST request for Branch Default Create", FILE_APPEND);
            }
            return $this->postRequest($bdInfoArray, self::$CREATE_BRANCH_URI);
            /*
             * all calls are post.
            if($this->trigger == self::TRIGGER_UPDATE) {
                return $this->putRequest($bdInfoArray, self::$CREATE_BRANCH_URI);
            } else {
                return $this->postRequest($bdInfoArray, self::$CREATE_BRANCH_URI);
            }
            */
        } else {
            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (serviceProviderHandler.php: " . __LINE__ . ") Failed to find a record to use for Branch Default Create", FILE_APPEND);
            }
            return null;
        }
    }

    /**
     * returns the Branch Defaults information which is attached to an Agents record
     *
     * @return array|mixed
     */
    private function pullBranchInfo()
    {
        $bdInfo = [];
        if (
            $this->agentsRecordModel &&
            method_exists($this->agentsRecordModel, 'getModuleName') &&
            $this->agentsRecordModel->getModuleName() == 'Agents'
        ) {
            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (serviceProviderHandler.php: " . __LINE__ . ") Pulling Agents Information for Branch Default Create", FILE_APPEND);
            }
            $bdInfo['COMP_ID'] = $this->agentsRecordModel->get('agent_number');
        }

        if (is_array($this->branchDefaultsRecordModelArray)) {
            foreach ($this->branchDefaultsRecordModelArray as $recordID => $bdRecordModel) {
                if (
                    $bdRecordModel &&
                    method_exists($bdRecordModel, 'getModuleName') &&
                    $bdRecordModel->getModuleName() == 'BranchDefaults'
                ) {
                    if (self::DEBUG) {
                        file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (serviceProviderHandler.php: ".__LINE__.") Pulling Branch Defaults Information for Branch Default Create", FILE_APPEND);
                    }

                    $bdInfo['ICTYPE_CODE'] = $bdRecordModel->get('bd_ic_type');

                    $sdRecords = $bdRecordModel->getGuestModuleRecords('ServiceDefaults');

                    foreach ($sdRecords as $sdRecordID => $sdRecordModel) {
                        $sdInfo = [];
                        $sdInfo['AUTYPE_ID']          = $sdRecordModel->get('sd_authority');
                        $sdInfo['CLASS']              = $sdRecordModel->get('sd_class');
                        $sdInfo['SITEM_CODE']         = $sdRecordModel->get('sd_standard_item');
                        $sdInfo['BREAKDOWN']          = $sdRecordModel->get('sd_mileage');
                        $sdInfo['EFFDATE']            = $sdRecordModel->get('sd_effective_date');
                        $sdInfo['PCT']                = $sdRecordModel->get('sd_paid_to_ic');
                        $sdInfo['HOURLY']             = $sdRecordModel->get('sd_per_service');
                        $sdInfo['BREAKDOWN_SCHEDULE'] = $sdRecordModel->get('sd_mileage');
                        $sdInfo['CANCELDATE']         = $sdRecordModel->get('sd_cancel_date');
                        $sdInfo['TARIFF_NUMBER']      = $sdRecordModel->get('sd_tariff');
                        $sdInfo['Created_On']         = $sdRecordModel->get('createdtime');
                        $sdInfo['Created_By']         = $this->retreiveUsernameFromID($sdRecordModel->get('created_user_id'));
                        $sdInfo['Modified_On']        = $sdRecordModel->get('modifiedtime');
                        $sdInfo['Modified_By']        = $this->retreiveUsernameFromID($sdRecordModel->get('modifiedby'));
                        $bdInfo['SERVICE_DEFAULTS'][] = $sdInfo;
                    }
                }
            }
        }

        $this->bdInfo = $bdInfo;

        return $bdInfo;
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
        return $self->createBranchDefault();
    }

    /**
     * Override the main one, because here we only want Agents and Branch Defaults.
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
                        case 'Agents':
                            $this->agentsRecordModel = $unknownRecordModel;
                            $this->agentsNumber = $this->recordNumber;
                            break;
                        case 'BranchDefaults':
                            $this->branchDefaultsRecordModel = $unknownRecordModel;
                            $this->branchDefaultsNumber = $this->recordNumber;
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
        foreach (['agents', 'branchDefaults'] as $module) {
            try {
                $this->pullRecordModel($module.'Number', $module.'RecordModel');
                $ok = true;
                if ($module == 'branchDefaults') {
                    //@TODO: eventually make it so we can pull the agents number from the branchDefaults record.
                    if (!$this->agentsNumber && $this->branchDefaultsRecordModel) {
                        //$this->agentsNumber = $this->branchDefaultsRecordModel->getParentRecordID();
                    }
                }
            } catch (\Exception $ex) {
                //throw $ex;
                //it's fine.
            }
        }

        //This block should probably be a function
        if (self::SHOW_SINGLE_VENDOR_AGREEMENT && $this->branchDefaultsRecordModel) {
            //OK we are good to go!
            $this->branchDefaultsRecordModelArray[$this->branchDefaultsRecordModel->get('id')] = $this->branchDefaultsRecordModel;
        } else {
            if ($this->agentsRecordModel) {
                $this->branchDefaultsRecordModelArray = $this->agentsRecordModel->getAllBranchDefaults();
            }
            //use the one passed in to override the one that might by attached from the above function.
            if ($this->branchDefaultsRecordModel) {
                $this->branchDefaultsRecordModelArray[$this->branchDefaultsRecordModel->get('id')] = $this->branchDefaultsRecordModel;
            }
        }

        return $ok;
    }
}
