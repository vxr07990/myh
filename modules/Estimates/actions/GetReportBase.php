<?php
/**
 * @author 			LouReport.php
 * @description 	Extended functionality from the Quotes module so we can add to
 *                  it without having to deal with changing the core vtiger code
 * @contact 		lrobinson@igcsoftware.com
 * @copyright		IGC Software
 */
require_once('libraries/nusoap/nusoap.php');
class Estimates_GetReportBase_Action extends Estimates_QuickEstimate_Action
{
    const ESTIMATE_TYPE_DEFAULT     = 'Default';
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

    public function __construct() {
        parent::__construct();

        $this->set('wsdlURL', getenv('REPORTS_URL'));

        $this->set('availableReportsMethod', 'getAvailableReportsCheckCustom');
        $this->set('availableReportsResult', 'GetAvailableReportsCheckCustomResult');

        $this->set('getReportMethod', 'getReport');
        $this->set('getReportResult', 'GetReportResult');

        if (getenv('USE_NEW_MOVERDOC_API')) {
            $this->set('useNewReportAPI', true);
            $this->set('availableReportsMethod', 'getAvailableReports');
            $this->set('availableReportsResult', 'GetAvailableReportsResult');
            $this->set('ApplicationId', getenv('REPORT_ApplicationId'));
            $this->set('ApiKey', getenv('REPORT_ApiKey'));
            $this->set('SharedSecret', getenv('REPORT_SharedSecret'));

            //@NOTE: This is set in vtigerversion.php.
            $this->set('MajorVersion', getenv('major_version'));
            $this->set('MinorVersion', getenv('minor_version'));
            $this->set('Revision', getenv('revision_version'));
        }

        if (getenv('REPORT_VANLINEID_OVERRIDE')) {
            $this->set('vanlineOverride', getenv('REPORT_VANLINEID_OVERRIDE'));
        }
    }

    public function process(Vtiger_Request $request)
    {
        if (getenv('USE_NEW_MOVERDOC_API')) {
            return $this->processNewMethod($request);
        }

        $error = false;
        //@NOTE: $_POST['mode'] == 'local'
        $requestType = $request->get('requestType');
        if ($requestType == 'GetAvailableReports') {
            $info = $this->getAvailableReports($request);
            if (!$info) {
                //@NOTE: Not an error because it prints this in that box right now.
                $info = "<div class='contents'>No reports are available at this time</div>";
            }
            //} elseif ($requestType == 'GetReport') {
        } else {
            $wsdlURL = $request->get('wsdlURL');
            if(empty($wsdlURL) || $wsdlURL == 'undefined') {
                $this->error        = true;
                $this->errorCode    = 'Error Processing Request';
                $this->errorMessage = 'The Reporting Service Address is not configured for your vanline. Please contact IGC Support.';

                $response           = new Vtiger_Response();
                $response->setError($this->errorCode, $this->errorMessage);
                $response->emit();

                return;
            }
            $info = $this->getReport($request);
            if (!$info) {
                $error = true;
            }
        }

        $response = new Vtiger_Response();
        if ($error) {
            if ($this->checkError()) {
                $response->setError($this->errorCode, $this->errorMessage);
            } else {
                $response->setError('Error Processing Request', 'The report failed to generate.');
            }
        } else {
            $response->setResult($info);
        }
        $response->emit();
        return null;
    }

    private function returnReportIntegrationHandler($request) {
        $recordId = $request->get('record');
        $reportIntegrationObject = $this->returnReportIntegrationObject($recordId);
        return new MoveCrm\ReportsIntegration($reportIntegrationObject);
    }

    private function returnReportIntegrationObject($recordId) {

        if (getenv('INSTANCE_NAME') == 'sirva') {
                return new MoveCrm\ReportsIntegration\SirvaEstimatesIntegrationObject($recordId);
        } else if (getenv('INSTANCE_NAME') == 'graebel') {
                return new MoveCrm\ReportsIntegration\GVLEstimatesIntegrationObject($recordId);
        }

        return new MoveCrm\ReportsIntegration\EstimatesIntegrationObject($recordId);
    }

    public function processNewMethod(Vtiger_Request $request)
    {

        $reportIntegration = $this->returnReportIntegrationHandler($request);

        $error = false;
        $requestType = $request->get('requestType');
        if ($requestType == 'GetAvailableReports') {
            $recordId   = $request->get('record');
            if (!$recordId) {
                return false;
            }
            $info = $reportIntegration->getAvailableReports($request);
            if (!$info) {
                //@NOTE: Not an error because it prints this in that box right now.
                $info = "<div class='contents'>No reports are available at this time</div>";
            }
        } else {
            $info = $this->getNewReports($request, $reportIntegration);
            if (!$info) {
                $error = true;
            }
        }

        $response = new Vtiger_Response();
        if ($error) {
            if ($this->checkError()) {
                $response->setError($this->errorCode, $this->errorMessage);
            } else {
                $response->setError('Error Processing Request', 'The report failed to generate.');
            }
        } else {
            $response->setResult($info);
        }
        $response->emit();
        return null;
	}

    public function getReport (Vtiger_Request &$request)
    {
        $reportName = $request->get('reportName');
        $reportID   = $request->get('reportId');
        $recordId   = $request->get('record');
        $type       = $request->get('type');
        $wsdlURL    = $request->get('wsdlURL');
        $soapResultKey = $this->get('getReportResult');

        if (!$recordId) {
            return false;
        }

        if (!$wsdlURL) {
            return false;
        }

        try {
            $recordModel = Estimates_Record_Model::getInstanceById($recordId);
            $orderID     = $recordModel->get('orders_id');
        } catch (Exception $ex) {
            return false;
        }

        //build the parameters.
        if ($orderID) {
            $reportName = $this->getModifiedReportName($reportName, $orderID);
        } else {
            $reportName = $this->getModifiedReportName($reportName);
        }
        if ($type == 'editview') {
            //save record before generating xml
            $request->set('reportSave', '1');
            $saveAction = new Estimates_Save_Action;
            $saveAction->process($request);
        }
        //@NOTE: we have to send the params request off to generate Soap Result because it's "parent"
        $params = $this->getParams($request);
        $soapResult = $this->generateSoapResult($wsdlURL, $reportID, $recordId, false, false, $params['request']);
        return $this->processReportsResponse($recordId, $reportName, $soapResult[$soapResultKey]);
    }

    public function getNewReports(Vtiger_Request &$request, $reportIntegration)
    {
        $recordId   = $request->get('record');
        $reportName = $request->get('reportName');
        $soapResultKey = $this->get('getReportResult');
        $soapResult = $reportIntegration->getReport($request);
        if ($reportIntegration->checkError()) {
            $errorRes = $reportIntegration->getLastError();
            $this->error = true;
            $this->errorCode = $errorRes['errorCode'];
            $this->errorMessage = $errorRes['errorMessage'];
            return;
        }
        return $this->processReportsResponse($recordId, $reportName, $soapResult[$soapResultKey]);
    }

	function getModifiedReportName($reportName, $orderID = false) {
        if ($orderID) {
            $reportNameEnd    = (strlen($reportName) > 7)?substr($reportName, -7):false;
            $reportNameUpdate = false;
            if ($reportNameEnd === 'Billing') {
                $reportNameUpdate = 'Billing';
            }
            if ($reportNameEnd == 'ibution') {
                $reportNameUpdate = 'Distr';
            }
            if ($reportNameUpdate) {
                $orderRecord              = Orders_Record_Model::getInstanceById($orderID);
                $HQNumber                 = $orderRecord->get('orders_no');
                $reportName               = "$HQNumber $reportNameUpdate Report";
                $participatingAgentsModel = Vtiger_Module_Model::getInstance('ParticipatingAgents');
                if ($participatingAgentsModel && $participatingAgentsModel->isActive()) {
                    $participants = $participatingAgentsModel::getParticipants($orderID);
                    foreach ($participants as $participant) {
                        if ($participant['agent_type'] == 'Booking Agent') {
                            $agent_name  = $participant['agentName'];
                            $agentNumber = $participant['agent_number'];
                            $reportName  = "$agent_name ($agentNumber) $reportName";
                            break;
                        }
                    }
                }
            }
        }

        return $reportName;
    }

    function generateButtonsAndServiceAddress($values) {
        $currentButton = 0;
        $buttons = [];
        $serviceAddress = false;
        foreach ($values as $tagInfo) {
            if ($tagInfo['tag'] == 'REPORTS' && $tagInfo['type'] == 'open' && $tagInfo['level'] == 1 && isset($tagInfo['attributes'])) {
                $serviceAddress = $tagInfo['attributes']['SERVICE_ADDRESS'];
            }
            if ($tagInfo['tag'] == 'REPORT') {
                if ($tagInfo['type'] == 'open') {
                    $buttons[] = [];
                } else {
                    $currentButton++;
                }
            } elseif ($tagInfo['tag'] == 'REPORT_NAME') {
                $buttons[$currentButton]['report_name'] = $tagInfo['value'];
            } elseif ($tagInfo['tag'] == 'REPORT_ID') {
                $buttons[$currentButton]['report_id'] = $tagInfo['value'];
            } elseif ($tagInfo['tag'] == 'REQUIRES_CUSTOMER_INITIALS') {
                $buttons[$currentButton]['requires_customer_initials'] = $tagInfo['value'];
            }
        }
        return [$buttons, $serviceAddress];
    }

    function getSkipArray($businessLine, $module, $estimatesReportsArray, $actualsReportsArray) {
        $skipArray = [];

        if ($module == 'Actuals') {
            $skipArray = $estimatesReportsArray;
        } else if ($module == 'Estimates') {
            $skipArray = $actualsReportsArray;
        }

        if (!preg_match('/Work Space/', $businessLine)) {
            array_push($skipArray, 'Workspace CSO');
        }

        array_push($skipArray, 'Inventory', 'Customer Service Order');

        return $skipArray;
    }

    public function getAvailableReports(Vtiger_Request &$request)
    {
        $estimatesReportsArray = [
            'Estimate of Charges - Binding',
            'Estimate of Charges - Non-Binding',
            'Estimate of Charges - Billing',
            'Estimate of Charges - Distribution'
        ];
        $actualsReportsArray = [
            'Actual Charges - Binding',
            'Actual Charges - Non-Binding',
            'Actual Charges - Billing',
            'Actual Charges - Distribution',
            'Invoice'
        ];

        $wsdlURL = $this->get('wsdlURL');
        if (!$wsdlURL) {
            //@NOTE: backup is from .env
            $wsdlURL = getenv('REPORTS_URL');
        }

        //the wsdl service we're calling
        $functionName = $this->get('availableReportsMethod');

        //How that returns the result key for the return array to be pulled from
        //$soapResultKey = ucfirst($functionName).'Result'; // <-- too cute.
        $soapResultKey = $this->get('availableReportsResult');

        $recordId    = $request->get('record');

        if (!$wsdlURL) {
            return false;
        }

        $module       = '';
        $businessLine = '';
        if ($recordId) {
            try {
                $recordModel  = Estimates_Record_Model::getInstanceById($recordId);

                if($recordModel->get('pricing_mode') == 'Actual Rating'){
                    $module = 'Actuals';
                }else{
                    $module = 'Estimates';
                }

                $businessLine = vtranslate($recordModel->get('business_line_est'));
                $request->set('assigned_user_id',$recordModel->get('assigned_user_id'));
            } catch (Exception $ex) {
                //don't think it matters.
            }
        }

        $skipArray = $this->getSkipArray($businessLine, $module, $estimatesReportsArray, $actualsReportsArray);
        try {
            $soapclient = new soapclient2($wsdlURL, 'wsdl');
            $soapclient->setDefaultRpcParams(true);
            $soapProxy = $soapclient->getProxy();
        } catch (Exception $ex) {
            //$ex->getMessage() probably says something useful at this point. but we don't appear to return errors.
            return false;
        }
        //ensure function exists for the wsdl.
        if (!method_exists($soapProxy, $functionName)) {
            return false;
        }

        $wsdlParams = $this->getParams($request);
        MoveCrm\LogUtils::LogToFile('LOG_REPORT_PROCESS', "wsdlURL : ".print_r($wsdlURL, true) . PHP_EOL . "wsdlParams : ".print_r($wsdlParams, true) . PHP_EOL);
        $soapResult = $soapProxy->$functionName($wsdlParams);
        MoveCrm\LogUtils::LogToFile('LOG_REPORT_PROCESS', "soapResult : ".print_r($soapResult, true));

        $xml = base64_decode($soapResult[$soapResultKey], true);
        if (!$xml || substr($xml, 0, 5) != "<?xml") {
            return false;
        } else {
            $parser = xml_parser_create();
            $values = [];
            $index = [];
            xml_parse_into_struct($parser, $xml, $values, $index);

            list($buttons, $serviceAddress) = $this->generateButtonsAndServiceAddress($values);

            $info = "<table class='contents table table-bordered'><tr><th class='blockheader'>Available Reports</th></tr>";

            if ($serviceAddress) {
                //@NOTE: add ?wsdl to the return service_address value.
                $info .= '<input type="hidden" name="wsdlURL" value="'.urlencode($serviceAddress.'?wsdl').'">';
            }

            foreach ($buttons as $button) {
                if (in_array($button['report_name'], $skipArray)) {
                    continue;
                }
                $info = $info."<tr><td><button type='button' name='$button[report_id]'>$button[report_name]</button></tr></td>";
            }

            $info = $info."</table>";
        }
        return $info;
    }

    public function getParams(Vtiger_Request &$request)
    {
        $wsdlParams = [];
        if ($this->get('useNewReportAPI')) {
            //magick!
            //@NOTE: the outer wrapper of ReportsRequest is actually request.
            //$wsdlParams['ReportsRequest'] = $this->getReportRequestArray($request);
            $wsdlParams['request'] = $this->getReportRequestArray($request);

            return $wsdlParams;
        }

        //@NOTE: changed so I could add a note... sad.
        if ($request->get('mode') == 'local') {
            $wsdlParams = $this->getLocalParams($request);
        } else {
            $wsdlParams = $this->getInterstateParam($request);
        }
        //@NOTE: three options we don't really use.
        //$wsdlParams['estimateType']  = 'Binding';
        //$wsdlParams['estimateType']  = 'NonBinding';
        //$wsdlParams['estimateType']  = 'NotToExceed';
        return $wsdlParams;
    }

    /**
     * Function to get all the report variables for the MoverDocs 2.0 reports
     *
     * @param Vtiger_Request $request
     *
     * @return array
     */
    protected function getReportRequestArray(Vtiger_Request &$request) {
        $recordId   = $request->get('record');
        $userId     = $request->get('assigned_user_id');

        $this->updateApplicationId($request);

        $resultArray = $this->getReportApplicationArray();

        $estimatesRecordModel = $this->getEstimatesRecordModel($recordId);

        $resultArray['VanlineId'] = $this->getReportVanlineId($estimatesRecordModel);
        $resultArray['PricingMode'] = $this->getReportPricingMode($estimatesRecordModel);
        $resultArray['EstimateType'] = $this->getReportEstimateType($request, $estimatesRecordModel);
        $resultArray['IsIntra'] = $this->getReportIsIntra($request, $estimatesRecordModel);
        $resultArray['CustomPassword'] = $this->getCustomReportsPassword($estimatesRecordModel, $userId);
        $resultArray['QueryMode']  = null;

        return $resultArray;
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
    private function getEstimatesRecordModel($recordId) {
        if (!$recordId) {
            return false;
        }

        try {
            $recordModel = Estimates_Record_Model::getInstanceById($recordId);
            if (!$recordModel) {
                return false;
            }
            if ($recordModel->getModuleName() == 'Estimates') {
                return $recordModel;
            }
            if ($recordModel->getModuleName() != 'Estimates') {
                //@TODO: this might not work as expected unless it's a Order or Opportunity.
                return $recordModel->getPrimaryEstimateRecordModel(false);
            }
        } catch (Exception $ex) {
            return false;
        }
        return false;
    }

    /**
     * return standard set of information for MoverDocs 2.0 reports.
     *
     * @return array
     */
    protected function getReportApplicationArray() {
        return [
            'ApplicationId' => $this->get('ApplicationId'),
            'ApiKey'        => $this->get('ApiKey'),
            'SharedSecret'  => $this->get('SharedSecret'),
            'MajorVersion'  => $this->get('MajorVersion'),
            'MinorVersion'  => $this->get('MinorVersion'),
            'Revision'      => $this->get('Revision')
        ];
    }

    /**
     * function to update the application ID in case we need to do it multiple ways.
     *
     * @param Vtiger_Request $request
     */
    private function updateApplicationId(Vtiger_Request &$request) {
        $this->updateApplicationIdByModuleName($request->get('module'));
    }

    /**
     * set the application id from the env variables based on the modulename
     *
     * @param $moduleName
     *
     * @return null
     */
    private function updateApplicationIdByModuleName($moduleName) {
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
        $this->set('ApplicationId', $moduleSpecificApplicationID);
        return null;
    }
    /**
     * function to get the vanline ID for a report from the estimates record model.
     *
     * @param Estimates_Record_Model $estimateRecordModel
     *
     * @return int
     */
    protected function getReportVanlineId(Estimates_Record_Model &$estimateRecordModel) {
        //@NOTE: this is added in for testing or overriding an instances normal id.
        if ($this->get('vanlineOverride')) {
            return $this->get('vanlineOverride');
        }

        //This would be a good time to use that override.
        if (getenv('INSTANCE_NAME') == 'sirva') {
            return 18;
        }

        return $estimateRecordModel->getVanlineId();
    }

    /**
     * function returns if it's Interstate or local_tariff type of report.
     *
     * @param Estimates_Record_Model $estimateRecordModel
     *
     * @return string
     */
    private function getReportPricingMode(Estimates_Record_Model &$estimateRecordModel) {
        $tariffId   = $estimateRecordModel->getCurrentAssignedTariff();
        $tariffInfo = Estimates_Record_Model::getTariffInfo($tariffId);
        //$localTariffSave = Estimates_Record_Model::isLocalTariff($tariffId);
        if ($tariffInfo['is_interstate']) {
            return 'INTERSTATE';
        } else {
            return 'LOCAL_TARIFF';
        }
    }

    /**
     * function returns the estimate type fo the record this is like Binding, Non Binding, etc
     *
     * @param Vtiger_Request $request
     * @param Estimates_Record_Model|bool estimateRecordModel
     *
     * @return string
     */
    private function getReportEstimateType(Vtiger_Request &$request, $estimateRecordModel) {
        if ($estimateRecordModel) {
            $estimate_type = $estimateRecordModel->get('estimate_type');
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
     * @param Vtiger_Request $request
     * @param Estimates_Record_Model|bool estimateRecordModel
     *
     * @return bool
     */
    private function getReportIsIntra(Vtiger_Request &$request, $estimateRecordModel) {
        //Accoring to Reports team, IsIntra means the states match.
        if ($estimateRecordModel) {
            //compare origin and destination states
            $originState      = trim($estimateRecordModel->get('origin_state'));
            $destinationState = trim($estimateRecordModel->get('destination_state'));
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

    /** ^^^ New Report api variable gets ^^^ **/

    protected function getLocalParams(Vtiger_Request &$request)
    {
        $recordId   = $request->get('record');
        $userId     = $request->get('assigned_user_id');
        $vanLineId  = Estimates_Record_Model::getVanlineIdStatic($recordId, '', $userId);
        $wsdlParams = [];

        $wsdlParams['pricingOption'] = 'LOCAL_TARIFF';

        $wsdlParams['estimateType']  = 'Default';

        $wsdlParams['vanLineId'] = $vanLineId;

        if (getenv('INSTANCE_NAME') == 'uvlc') {
            $wsdlParams['vanLineId'] = 20;
        } else if (getenv('INSTANCE_NAME') == 'sirva') {
            if ($vanLineId == 1 || $vanLineId == 9) {
                $wsdlParams['vanLineId'] = 18; //$vanLineId;
                if ($vanLineId == 1) {
                    $wsdlParams['customReportsPassword'] = 'qlabavl';
                } else {
                    $wsdlParams['customReportsPassword'] = 'qlabnavl';
                    //$wsdlParams['customReportsPassword'] = '';
                }
            }
        }

        $estimatesRecordModel = $this->getEstimatesRecordModel($recordId);
        if ($estimatesRecordModel) {
            $wsdlParams['customReportsPassword'] = $this->getCustomReportsPassword($estimatesRecordModel, $userId);
        }

        $wsdlParams['isIntrastate'] = false;
        return $wsdlParams;
    }

    protected function getInterstateParam(Vtiger_Request &$request)
    {
        $recordId   = $request->get('record');
        $userId = $request->get('assigned_user_id');

        $recordModel = Estimates_Record_Model::getInstanceById($recordId);
        $businessLine = vtranslate($recordModel->get('business_line_est'));

        $vanLineId = Estimates_Record_Model::getVanlineIdStatic($recordId, '', $userId);

        $wsdlParams = [];

        if (preg_match('/Work Space/', $businessLine)) {
            $wsdlParams['pricingOption'] = 'LOCAL_TARIFF';
        } else {
            $wsdlParams['pricingOption'] = 'INTERSTATE';
        }
        $wsdlParams['estimateType'] = 'Default';
        $wsdlParams['vanLineId'] = $vanLineId;
        $wsdlParams['isIntrastate'] = 'false';

        //@TODO
        $wsdlParams['customReportsPassword'] = '';
        //Sirva Max 3/Max 4. Other reports go to GetReportTPGPricelock.php
        if (getenv('INSTANCE_NAME') == 'sirva') {
            if ($vanLineId === 1) {
                $wsdlParams['customReportsPassword'] = 'qlabavl';
                $wsdlParams['pricingOption']         = 'LOCAL_TARIFF';
            } elseif ($vanLineId === 9) {
                $wsdlParams['customReportsPassword'] = 'qlabnavl';
                $wsdlParams['pricingOption']         = 'LOCAL_TARIFF';
            }
        }

        $estimatesRecordModel = $this->getEstimatesRecordModel($recordId);
        if ($estimatesRecordModel) {
            $wsdlParams['customReportsPassword'] = $this->getCustomReportsPassword($estimatesRecordModel, $userId);
        }

        return $wsdlParams;
    }

    /**
     * Function to pull the custom reports password, it could be agent specific.  (or gvl/sirva specific)
     *
     * @param Estimates_Record_Model $estimateRecordModel
     * @param int $userId
     *
     * @return string|void
     */
    protected function getCustomReportsPassword(Estimates_Record_Model &$estimateRecordModel, $userId) {
        if (!$estimateRecordModel) {
            return;
        }
        //legacy hard coding solutions for graebel and sirva that maybe can be removed later.
        if (getenv('INSTANCE_NAME') == 'graebel') {
            $customTariffType = $this->getCustomTariffType($estimateRecordModel->getId());
            switch ($customTariffType) {
                case '400N Base':
                    return 'gvl400n104g';
                default:
                    return;
            }
        } else if (getenv('INSTANCE_NAME') == 'sirva') {
            $vanLineId = Estimates_Record_Model::getVanlineIdStatic($estimateRecordModel->getId(),'', $userId);
            if ($vanLineId == 1) {
                return 'qlabavl';
            } elseif ($vanLineId == 9) {
                return 'qlabnavl';
            }
        }

        $agentID = $estimateRecordModel->get('agentid');
        if (!$agentID) {
            return;
        }

        $agentManagerRecord = Vtiger_Record_Model::getInstanceById($agentID);
        if (!$agentManagerRecord) {
            return;
        }

        return $agentManagerRecord->get('custom_reports_pw');
    }

    private function getCustomTariffType($estimateRecordID) {
        try {
            $tariffID                   = Estimates_Record_Model::getCurrentAssignedTariffStatic($estimateRecordID, $tablePrefix = '');
            $effectiveTariffRecordModel = Vtiger_Record_Model::getInstanceById($tariffID);
            return $effectiveTariffRecordModel->get('custom_tariff_type');
        } catch (Exception $ex) {
            //@NOTE: No throw is needed because we just don't have a custom_tariff_type.
        }
        return false;
    }
}
