<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
require_once('libraries/nusoap/nusoap.php');

class Orders_GetPaperworkTypes_Action extends Vtiger_BasicAjax_Action
{
    public function checkPermission(Vtiger_Request $request)
    {
        $moduleName                 = $request->getModule();
        $moduleModel                = Vtiger_Module_Model::getInstance($moduleName);
        $currentUserPriviligesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        if (!$currentUserPriviligesModel->hasModuleActionPermission($moduleModel->getId(), 'EditView')) {
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED', $moduleName));
        }
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

    public function process(Vtiger_Request $request)
    {
        $reportIntegration = $this->returnReportIntegrationHandler($request);
        $error = false;
        $recordId   = $request->get('record');
        if (!$recordId) {
            return false;
        }
        $ordersRecord = Vtiger_Record_Model::getInstanceById($recordId, 'Orders');
        if($ordersRecord && $ordersRecord->get('business_line') != 'Interstate') {
            $request->set('restrictBOL', true);
        }

        $info = $reportIntegration->getAvailableReports($request);
        if (!$info) {
            //@NOTE: Not an error because it prints this in that box right now.
            $info = "<div class='contents'>No reports are available at this time</div>";
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
}
