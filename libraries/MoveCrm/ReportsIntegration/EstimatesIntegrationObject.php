<?php
/**
 * Created by PhpStorm.
 * Blah
 */

namespace MoveCrm\ReportsIntegration;

require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';
require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

require_once ('libraries/MoveCrm/ReportsIntegration/IReportsIntegrationObject.php');
use MoveCrm;

class EstimatesIntegrationObject implements IReportsIntegrationObject {

    protected $db;
    const ESTIMATE_TYPE_DEFAULT      = 'Default';
    const ERROR_INVALID_RECORD_MODEL = 10003;

    protected $customerDataXmlRootTag;
    protected $reportCustomerData;
    protected $recordModel = false;
    protected $error;
    protected $errorCode;
    protected $errorMessage;
    protected $recordId;
    protected $reportBuiltArray;
    protected $reportXML;

    protected static $ESTIMATE_TYPE = [
        'Default' => 'Default',
        'Binding' => 'Binding',
        'Non Binding' => 'NonBinding',
        'Not To Exceed' => 'NotToExceed',
        'Weight Allowance' => 'WeightAllowance',
        'No Weight Allowance' => 'NoWeightAllowance',
        'Actual' => 'Actual',
        'EitherOr' => 'EitherOr',
        'Guaranteed' => 'Guaranteed',
        'Firm Price' => 'FirmPrice',
        'Guaranteed 110' => 'Guaranteed110',
        'Guaranteed 110 WV' => 'Guaranteed110WV',
        'No Visual Survey' => 'NoVisualSurvey',
        'Actual Weight' => 'ActualWeight',
        'Charges Guaranteed 110' => 'ChargesGuaranteed110',
        'Weight Variance 10' => 'WeightVariance10',
        'TOM_ONLY' => 'TOM_ONLY',
    ];

    //public function __construct(array $config = []) {
    public function __construct($recordId) {
        if (getenv('REPORT_VANLINEID_OVERRIDE')) {
            $this->set('vanlineOverride', getenv('REPORT_VANLINEID_OVERRIDE'));
        }

        $recordId = trim($recordId);
        if ($recordId) {
            $this->recordId = $recordId;
            $this->setRecordModel();
        }
    }

    /**
     * Function to get the value of a given property
     * @param <String> $propertyName
     * @return <Object>
     */
    public function get($propertyName) {
        if (property_exists($this, $propertyName)) {
            return $this->$propertyName;
        }
    }

    /**
     * Function to set the value of a given property
     * @param <String> $propertyName
     * @param <Mixed> $value
     * @return <Object>
     */
    public function set($propertyName, $value) {
        $this->$propertyName = $value;
        return $this;
    }

    /**
     * function to retrieve the estimate record model from a record's id.
     *
     * This allows you to pass in an order or opportunity's record id and get the primary estimate.
     * Or you pass in the estimate id and get that record.
     *
     * @param int $recordId
     *
     * @return bool|Vtiger_Record_Model
     */
    public function getRecordModel($recordId) {
        if (!$recordId) {
            $recordId = $this->recordId;
        }

        if (!$recordId) {
            return false;
        }

        $this->recordId = $recordId;
        //see if we have a record model
        if ($this->recordModel) {
            //ensure it's one we know.
            if (method_exists($this->recordModel, 'getId')) {
                //ensure it's the same as the one we want.
                if ($this->recordId == $this->recordModel->getId()){
                    return $this->recordModel;
                }
            }
        }

        //set record model for the object to use.
        $this->setRecordModel();
        return $this->recordModel;
    }

    /**
     * Using the class object's recordId get the record model
     *
     * @return bool
     */
    private function setRecordModel() {
        //clear the existing one
        unset($this->recordModel);
        if (!$this->recordId) {
            return false;
        }
        //attempt to get the new one.
        try {
            $recordModel = \Estimates_Record_Model::getInstanceById($this->recordId);
            if (!$recordModel) {
                return false;
            }
            if ($recordModel->getModuleName() == 'Estimates') {
                $this->recordModel = $recordModel;
                return true;
            }
            if ($recordModel->getModuleName() != 'Estimates') {
                //@TODO: this might not work as expected unless it's a Order or Opportunity.
                $this->recordModel = $recordModel->getPrimaryEstimateRecordModel(false);
                return true;
            }
        } catch (\Exception $ex) {
            return false;
        }
        return false;
    }

    /**
     * Retrieve the application ID for this object.
     *
     * @param \Vtiger_Request $request
     *
     * @return null
     */
    public function getApplicationId(\Vtiger_Request &$request) {
        return $this->getApplicationIdByModuleName($request->get('module'));
    }

    /**
     * set the application id from the env variables based on the modulename
     *
     * @param $moduleName
     *
     * @return null
     */
     protected function getApplicationIdByModuleName($moduleName) {
        if (!$moduleName) {
            return null;
        }

        //@NOTE: Examples:
        //REPORT_ApplicationId               = 4
        //REPORT_ApplicationId_estimates     = 4
        //REPORT_ApplicationId_actuals       = 4
        //REPORT_ApplicationId_cubesheets    = 5
        //REPORT_ApplicationId_orders        = 6
        //REPORT_ApplicationId_opportunities = 7

        $moduleName = strtolower($moduleName);
        $moduleSpecificApplicationID = getenv('REPORT_ApplicationId_'.$moduleName);
        if (!$moduleSpecificApplicationID) {
            return null;
        }
        return $moduleSpecificApplicationID;
    }

    /**
     * function to get the vanline ID for a report from the estimates record model.
     *
     * @param \Vtiger_Record_Model $estimatesRecordModel
     *
     * @return int
     */
    public function getReportVanlineId(\Vtiger_Record_Model &$estimatesRecordModel) {
        //@NOTE: this is added in for testing or overriding an instances normal id.
        if ($this->get('vanlineOverride')) {
            return $this->get('vanlineOverride');
        }

        return $estimatesRecordModel->getVanlineId();
        //return \MoveCrm\ValuationUtils::GetVanlineID($estimatesRecordModel->get('agentid'));
    }

    /**
     * function returns if it's Interstate or local_tariff type of report.
     *
     * @param \Vtiger_Record_Model $estimatesRecordModel
     *
     * @return string
     */
    public function getReportPricingMode(\Vtiger_Record_Model &$estimatesRecordModel) {
        //@TODO: I want the requirement to be an EstimateRecordModel... specifically.
        if (get_class($estimatesRecordModel) != 'Estimates_Record_Model') {
            throw new \Exception('Estimate_Record_Model is required for the pricing mode', self::ERROR_INVALID_RECORD_MODEL);
        }
        $tariffInfo = $estimatesRecordModel->getCurrentAssignedTariffInfo();
        if ($tariffInfo['is_interstate']) {
            return 'INTERSTATE';
        } else {
            return 'LOCAL_TARIFF';
        }
    }

    /**
     * function returns the estimate type fo the record this is like Binding, Non Binding, etc
     *
     * @param \Vtiger_Request $request
     * @param \Vtiger_Record_Model $estimatesRecordModel
     *
     * @return string
     */
    public function getReportEstimateType(\Vtiger_Request &$request, \Vtiger_Record_Model $estimatesRecordModel) {
        if ($estimatesRecordModel) {
            $estimate_type = $estimatesRecordModel->get('estimate_type');
        }

        if (!$estimate_type) {
            $estimate_type = $request->get('estimateType');
        }

        if (self::$ESTIMATE_TYPE[$estimate_type]) {
            return self::$ESTIMATE_TYPE[$estimate_type];
        }
        return self::$ESTIMATE_TYPE[self::ESTIMATE_TYPE_DEFAULT];
    }

    /**
     * function to return whether the report isIntrastate or not based on whether the states
     * match.  This is the criteria provided by reports for isIntra to be true.
     *
     * @param \Vtiger_Request $request
     * @param \Vtiger_Record_Model $estimatesRecordModel
     *
     * @return bool
     */
    public function getReportIsIntra(\Vtiger_Request &$request, \Vtiger_Record_Model $estimatesRecordModel) {
        //Accoring to Reports team, IsIntra means the states match.
        if ($estimatesRecordModel) {
            //compare origin and destination states
            $originState      = trim($estimatesRecordModel->get('origin_state'));
            $destinationState = trim($estimatesRecordModel->get('destination_state'));
            if (
                $originState ||
                $destinationState
            ) {
                //either origin OR destination states exists
                if (strtolower($originState) == strtolower($destinationState)) {
                    //origin and destination states match
                    return true;
                }

                //Origin and destination states exist and do not match
                return false;
            }
        }

        //@TODO: This may not be desired behavior, because the record should have an origin/destination
        //@NOTE: I thought mode was passed in to get reports, this may have been a figment of my imagination,
        // however, if it wasn't it'll work even if the case changes.
        if (preg_match('/intrastate/i',$request->get('mode'))) {
            //origin and destination states are both empty rely on the mode variable being set correctly.
            return true;
        }

        return false;
    }

    /**
     * Function to pull the custom reports password, it could be agent specific.
     *
     * @param \Vtiger_Record_Model $estimateRecordModel
     *
     * @return string|void
     */
    public function getReportCustomPassword(\Vtiger_Record_Model &$estimateRecordModel) {
        if (!$estimateRecordModel) {
            return;
        }

        $agentID = $estimateRecordModel->get('agentid');
        if (!$agentID) {
            return;
        }

        $agentManagerRecord = \Vtiger_Record_Model::getInstanceById($agentID);
        if (!$agentManagerRecord) {
            return;
        }

        return $agentManagerRecord->get('custom_reports_pw');
    }

    /**
     * Function to get the query mode for reports.
     * @NOTE: Nobody know what this is, but maybe you will in the future.
     *
     * @param \Vtiger_Record_Model $estimatesRecordModel
     *
     * @return mixed
     */
    public function getReportQueryMode(\Vtiger_Record_Model &$estimatesRecordModel) {
        return null;
    }

    /**
     * @param int $recordId
     *
     * @return string
     */
    public function getReportCustomerData($recordId = 0) {
        if (!$recordId) {
            $recordId = $this->recordId;
        }

        if (!$recordId) {
            return '';
        }

        $this->reportCustomerData = $this->getReportXML($this->getReportArray($recordId), $this->get('customerDataXmlRootTag'));
        return $this->reportCustomerData;
    }

    /**
     * @param int $recordId
     *
     * @return array
     */
    protected function getReportArray($recordId = 0) {
        if (!$recordId) {
            $recordId = $this->recordId;
        }

        if (!$recordId) {
            return [];
        }

        $this->recordId = $recordId;
        //buildArray($estimateId, $tempTables = false, $forReports = false, $oppID = false, $dov = true, $requote = false)
        $this->reportBuiltArray = MoveCrm\arrayBuilder::buildArray($recordId, false, true);
        return $this->reportBuiltArray;
    }

    /**
     * @param array  $preparedArray
     * @param string $xmlRootTag
     *
     * @return string
     */
    protected function getReportXML(array $preparedArray, $xmlRootTag = '') {
        if (!$preparedArray) {
            return '';
        }
        $this->reportXML = MoveCrm\xmlBuilder::build($preparedArray, $xmlRootTag);
        file_put_contents('logs/report.xml', $this->reportXML);
        return $this->reportXML;
    }
}
