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
//class OPList_GetReportOPList_Action extends Vtiger_BasicAjax_Action {
//Because we built the report generation into Estimates I want to extend that which extends Vtiger_BasicAjax_Action
class OPList_GetReportOPList_Action extends Estimates_QuickEstimate_Action
{
    public function __construct()
    {
        parent::__construct();
    }

    public function process(Vtiger_Request $request)
    {

        //save record before generating xml
        $request->set('NoRedirect', '1');
        $saveAction = new OPList_SaveOpListAnswers_Action;
        $saveAction->save($request);

        if(getenv('INSTANCE_NAME') != 'sirva'){
            //This is to fix the way oplist works until it is re-written
            $request->set('record',$request->get('source_record'));

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

        $recordId    = $request->get('record');
        $reportID   = $request->get('reportId');
        $reportName  = $request->get('reportName');
        $wsdlURL     = $request->get('wsdlURL');

        $sourceRecordId = $request->get('source_record');

        $arr = [];

        //@TODO feel free to fix this however you feel.
        if (getenv('INSTANCE_NAME') == 'graebel') {
            $oppRecordModel             = Vtiger_Record_Model::getInstanceById($request->get('source_record'));
            $primaryEstimateRecordModel = $oppRecordModel->getPrimaryEstimateRecordModel();
            if ($primaryEstimateRecordModel) {
                $arr = MoveCrm\arrayBuilder::buildArray($primaryEstimateRecordModel->getId(), false, true);
            } else {
                $arr = MoveCrm\arrayBuilder::buildArray(false, false, true, $request->get('source_record'));
            }
        } else {
            $vanLineId  = OPList_Record_Model::getVanlineId($recordId);
            $temp_arr['operational_list'] = MoveCrm\arrayBuilder::initializeOppList($request->get('source_record'));
            if (getenv('INSTANCE_NAME') == 'sirva') {
                if ($vanLineId == 1 || $vanLineId == 9) {
                    $qlabId               = $vanLineId;
                    $vanLineId            = 18;
                    $temp_arr['van_line_id']   = $vanLineId;
                    $temp_arr['qlab_brand_id'] = $qlabId;
                }
            } else {
                $temp_arr['van_line_id'] = $vanLineId;
            }
            $arr['survey_upload'] = $temp_arr;
        }
        $soapResult = $this->generateSoapResult($wsdlURL, $reportID, $recordId, $arr);
        $docid = $this->processReportsResponse($sourceRecordId, $reportName, $soapResult['GetReportResult']);

        $response = new Vtiger_Response();
        $response->setResult($docid);
        $response->emit();
    }

    private function returnReportIntegrationHandler($request) {
        $recordId = $request->get('record');
        $reportIntegrationObject = $this->returnReportIntegrationObject($recordId);
        return new MoveCrm\ReportsIntegration($reportIntegrationObject);
    }

    private function returnReportIntegrationObject($recordId) {

        if (getenv('INSTANCE_NAME') == 'sirva') {
            return new MoveCrm\ReportsIntegration\SirvaOPListIntegrationObject($recordId);
        } else if (getenv('INSTANCE_NAME') == 'graebel') {
            return new MoveCrm\ReportsIntegration\GVLOPListIntegrationObject($recordId);
        }

        return new MoveCrm\ReportsIntegration\OPListIntegrationObject($recordId);
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
