<?php
/**
 * @author            LouReport.php
 * @description       Extended functionality from the Quotes module so we can add to
 *                  it without having to deal with changing the core vtiger code
 * @contact        lrobinson@igcsoftware.com
 * @copyright         IGC Software
 * Leaving this here for comedic value
 */
require_once('libraries/nusoap/nusoap.php');

//class Orders_Getpaperwork_Action extends Vtiger_BasicAjax_Action {
//Because I built the report generation into Estimates I want to extend that which extends Vtiger_BasicAjax_Action
class Orders_Getpaperwork_Action extends Estimates_QuickEstimate_Action
{
    public function __construct()
    {
        parent::__construct();
    }

    public function process(Vtiger_Request $request)
    {
        $request->set('NoRedirect', '1');
        $reportIntegration = $this->returnReportIntegrationHandler($request);
        $info = $this->getNewReports($request, $reportIntegration);
        if (!$info) {
            $error = true;
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
        $reportIntegrationObject = $this->returnReportIntegrationObject($recordId, $request->getModule());
        return new MoveCrm\ReportsIntegration($reportIntegrationObject);
    }

    private function returnReportIntegrationObject($recordId, $moduleName) {

        if (getenv('INSTANCE_NAME') == 'sirva') {
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED', $moduleName));
        } else if (getenv('INSTANCE_NAME') == 'graebel') {
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED', $moduleName));
        }

        return new MoveCrm\ReportsIntegration\OrdersIntegrationObject($recordId);
    }

    public function getNewReports(Vtiger_Request &$request, $reportIntegration)
    {
        $recordId   = $request->get('record');
        $reportName = $request->get('reportName');
        $soapResultKey = 'GetReportResult';
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
}
