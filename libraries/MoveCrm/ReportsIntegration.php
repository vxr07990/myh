<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 3/17/2017
 * Time: 12:39 PM
 */

namespace MoveCrm;

require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';
require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

//use MoveCrm\ReportsIntegrationObject\EstimatesIntegration;
//use MoveCrm\ReportsIntegrationObject\OpsListIntegration;
//use MoveCrm\ReportsIntegrationObject\OrdersIntegration;
require_once ('libraries/MoveCrm/ReportsIntegration/IReportsIntegration.php');
use MoveCrm\ReportsIntegration\IReportsIntegration;
use MoveCrm;

class ReportsIntegration implements IReportsIntegration {

    protected $integrationObject;
    protected $db;

    protected $error;
    protected $errorCode;
    protected $errorMessage;

    protected $SUPPORTED_MODULES = [
        'estimates',
        'actuals',
        'cubesheets',
        'orders',
        'opportunities',
    ];

    public function __construct(MoveCrm\ReportsIntegration\IReportsIntegrationObject $obj, array $config = []) {

        $this->set('integrationObject', $obj);
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
    }

    /**
     * @return bool|string
     */
    public function getAvailableReports(\Vtiger_Request &$request) {
        //the wsdl service we're calling
        $functionName = $this->get('availableReportsMethod');

        //How that returns the result key for the return array to be pulled from
        $soapResultKey = $this->get('availableReportsResult');

        $soapProxy = $this->getSoapClient($functionName);
        if (!$soapProxy) {
            MoveCrm\LogUtils::LogToFile('LOG_REPORT_PROCESS', "getAvailableReports: wsdlURL : " . print_r($this->get('wsdlURL'), true) . PHP_EOL . "this->getLastError() : " . print_r($this->getLastError(), true) . PHP_EOL);
            return false;
        }

        $wsdlParams = $this->getReportWSDLParams($request);
        MoveCrm\LogUtils::LogToFile('LOG_REPORT_PROCESS', "wsdlURL : ".print_r($this->get('wsdlURL'), true) . PHP_EOL . "wsdlParams : ".print_r($wsdlParams, true) . PHP_EOL);
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

            $info = "<div id='reportsContent'><table class='contents table table-bordered'><tr><th class='blockheader'>Available Reports</th></tr>";

            if ($serviceAddress) {
                //@NOTE: add ?wsdl to the return service_address value.
                $info .= '<input type="hidden" name="wsdlURL" value="'.urlencode($serviceAddress.'?wsdl').'">';
            }

            foreach ($buttons as $button) {
                $addPiece = '';
                if($request->get('restrictBOL') && $button['report_name'] == 'Bill of Lading') {
                    $addPiece = 'class="lock"';
                }
                $info = $info."<tr><td><button type='button' $addPiece name='$button[report_id]'>$button[report_name]</button></tr></td>";
            }

            $info = $info."</table></div>";
        }
        return $info;
    }

    public function getReport(\Vtiger_Request &$request) {
        $recordId   = $request->get('record');
        $wsdlURL    = $request->get('wsdlURL');
        $reportID   = $request->get('reportId');
//        $reportName = $request->get('reportName');
//        $type       = $request->get('type');
//        $soapResultKey = $this->get('getReportResult');

        if (!$recordId) {
            return false;
        }

        if (!$wsdlURL) {
            return false;
        }

        $functionName = $this->get('getReportMethod');

        //@NOTE: we have to send the params request off to generate Soap Result because it's "parent"
        $wsdlParams = $this->getReportWSDLParams($request);
        $wsdlParams['reportID'] = $reportID;
        $wsdlParams['customerData'] = $this->encodeReportsData($this->integrationObject->getReportCustomerData($recordId));

        $soapProxy = $this->getSoapClient($functionName);
        if (!$soapProxy) {
            MoveCrm\LogUtils::LogToFile('LOG_REPORT_PROCESS', "getReport: wsdlURL : " . print_r($this->get('wsdlURL'), true) . PHP_EOL . "this->getLastError() : " . print_r($this->getLastError(), true) . PHP_EOL);
            return false;
        }

        MoveCrm\LogUtils::LogToFile('LOG_REPORT_PROCESS', "wsdlURL : ".print_r($this->get('wsdlURL'), true) . PHP_EOL . "wsdlParams : ".print_r($wsdlParams, true) . PHP_EOL);
        $soapResult = $soapProxy->$functionName($wsdlParams);
        MoveCrm\LogUtils::LogToFile('LOG_REPORT_PROCESS', "soapResult : ".print_r($soapResult, true));

        $errors = [
            'fault'       => $soapProxy->fault,
            'faultcode'   => $soapProxy->faultcode,
            'faultstring' => $soapProxy->faultstring,
            'faultdetail' => $soapProxy->faultdetail,
            'error_str'   => $soapProxy->error_str
        ];

        if($errors['faultstring']) {
            $this->setLastError($errors['faultstring'], $errors['faultstring']);
        }
        return $soapResult;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public function encodeReportsData($string = '') {
        return base64_encode($string);
    }

    /**
     * @return mixed
     */
    public function checkError() {
        return $this->error;
    }

    /**
     * @return array
     */
    public function getLastError() {
        return [
            'error'        => $this->error,
            'errorCode'    => $this->errorCode,
            'errorMessage' => $this->errorMessage
        ];
    }

    /**
     * @param $errorCode
     * @param $errorMessage
     */
    public function setLastError($errorCode, $errorMessage) {
        $this->error = true;
        $this->errorCode = $errorCode;
        $this->errorMessage = $errorMessage;
    }

    /**
     * @param \Vtiger_Request $request
     *
     * @return array
     */
    public function getReportWSDLParams(\Vtiger_Request &$request) {
        $wsdlParams = [];
        //@NOTE: the outer wrapper of ReportsRequest is actually request.
        //$wsdlParams['ReportsRequest'] = $this->getReportRequestArray($request);
        $wsdlParams['request'] = $this->getReportRequestArray($request);

        return $wsdlParams;
    }

    /**
     * return standard set of information for MoverDocs 2.0 reports.
     *
     * @return array
     */
    public function getReportApplicationArray() {
        //should I update here?  Then I need the request.
        //@TODO: Consider making a "update application array" function and move this there.
        //$this->updateApplicationId($request);
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
     * Function to get all the report variables for the MoverDocs 2.0 reports
     *
     * @param \Vtiger_Request $request
     *
     * @return array
     */
    protected function getReportRequestArray(\Vtiger_Request &$request) {
        $recordId   = $request->get('record');

        $recordModel = $this->integrationObject->getRecordModel($recordId);

        //@TODO: Consider making a "update application array" function and move this there.
        $this->updateApplicationId($request);
        $resultArray = $this->getReportApplicationArray();

        $resultArray['VanlineId'] = $this->integrationObject->getReportVanlineId($recordModel);
        $resultArray['PricingMode'] = $this->integrationObject->getReportPricingMode($recordModel);
        $resultArray['EstimateType'] = $this->integrationObject->getReportEstimateType($request, $recordModel);
        if ($resultArray['PricingMode'] != 'LOCAL_TARIFF') {
            $resultArray['IsIntra'] = $this->integrationObject->getReportIsIntra($request, $recordModel);
        } else {
            $resultArray['IsIntra'] = false;
        }
        $resultArray['CustomPassword'] = $this->integrationObject->getReportCustomPassword($recordModel);
        $resultArray['QueryMode']  = $this->integrationObject->getReportQueryMode($recordModel);

        return $resultArray;
    }

    /**
     * function to update the application ID in case we need to do it multiple ways.
     *
     * @param \Vtiger_Request $request
     */
    protected function updateApplicationId(\Vtiger_Request &$request) {
        $moduleSpecificApplicationID = $this->integrationObject->getApplicationId($request);
        if ($moduleSpecificApplicationID) {
            $this->set('ApplicationId', $moduleSpecificApplicationID);
        }
        //@TODO: consider expanding this out to have it update and key from the intergration object.
    }

    /**
     * Function to figure out where the hells we put the log output
     *
     * @param $notes
     */
    protected function log($notes) {
        //@NOTE HA hilarious!
        if(getenv('DB_SERVER') == 'localhost') {
            file_put_contents('logs/devlog2.log', __FILE__.':'.__LINE__.': notes'.PHP_EOL.print_r($notes, true).PHP_EOL, FILE_APPEND);
        }
    }

    /**
     * @param $wsdlFunctionName
     *
     * @return bool|object
     */
    protected function getSoapClient($wsdlFunctionName) {
        $wsdlURL = $this->get('wsdlURL');

        //ensure returned wsdl is something that might work.
        if(empty($wsdlURL) || $wsdlURL == 'undefined') {
            $this->setLastError('Error Processing Request', 'The Reporting Service Address ('.$wsdlURL.') is not configured for your vanline. Please contact IGC Support.');
            return false;
        }

        try {
            $soapclient = new \soapclient2($wsdlURL, 'wsdl');
            $soapclient->setDefaultRpcParams(true);
            $soapProxy = $soapclient->getProxy();
        } catch (Exception $ex) {
            $this->setLastError($ex->getCode(), $ex->getMessage());
            return false;
        }

        //ensure function exists for the wsdl.
        if (!method_exists($soapProxy, $wsdlFunctionName)) {
            $this->setLastError(11111, 'No WSDL function ('.$wsdlFunctionName.') found.');
            return false;
        }

        return $soapProxy;
    }

    /**
     * @param $values
     *
     * @return array
     */
    protected function generateButtonsAndServiceAddress($values) {
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
    /**
     * Function to get the value of a given property
     * @param <String> $propertyName
     * @return <Object>
     */
    public function get($propertyName)
    {
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
    public function set($propertyName, $value)
    {
        $this->$propertyName = $value;
        return $this;
    }
}
