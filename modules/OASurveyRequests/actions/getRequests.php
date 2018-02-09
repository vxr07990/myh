<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
include_once 'include/Webservices/Create.php';
include_once 'include/utils/utils.php';

class OASurveyRequests_getRequests_Action extends Vtiger_Action_Controller
{
    public function __construct()
    {
        $this->exposeMethod('getRequestsForUser');
    }

    public function process(Vtiger_Request $request)
    {
        $mode = $request->getMode();
        if (!empty($mode) && $this->isMethodExposed($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
    }

    public function checkPermission(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        $userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $permission = $userPrivilegesModel->hasModulePermission($moduleModel->getId());

        if (!$permission) {
            throw new AppException('LBL_PERMISSION_DENIED');
        }
    }

    public function getRequestsForUser(Vtiger_Request $request)
    {
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $roleDepth = getRoleDepth($currentUser->getRole());
        if ($roleDepth <= 6 && $roleDepth >= 4) {
            $OAModuleModel = Vtiger_Module_Model::getInstance('OASurveyRequests');

            $recordModels = $OAModuleModel->getPendingRequestForUser();
            if ($recordModels) {
                foreach ($recordModels as $OAModuleModel) {
                    $msgs[] = $OAModuleModel;
                }
            }

            $response = new Vtiger_Response();
            $response->setResult($msgs);
            $response->emit();
        } else {
            $response = new Vtiger_Response();
            $response->setError('1001', 'User role depth do not allow to view requests');
            $response->emit();
        }
    }
}
