<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Opportunities_IntlQuote_View extends Vtiger_Index_View
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

    public function process(Vtiger_Request $request)
    {
        $currentUserPriviligeModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $viewer                    = $this->getViewer($request);
        $recordId                  = $request->get('record');
        $moduleName                = $request->getModule();
        $recordModel               = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
        $moduleModel               = $recordModel->getModule();
        $QuoteFields               = $recordModel->getIntlQuote();
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('CURRENT_USER_PRIVILEGE', $currentUserPriviligeModel);
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('RECORD_ID', $recordId);
        $viewer->assign('REGISTER_QUOTES_FIELDS', $QuoteFields);
        //file_put_contents('logs/devLog.log', "\n STS FIELDS: ".print_r($STSFields, true), FILE_APPEND);
        $assignedToFieldModel = $moduleModel->getField('assigned_user_id');
        $assignedToFieldModel->set('fieldvalue', $recordModel->get('assigned_user_id'));
        $viewer->assign('ASSIGN_TO', $assignedToFieldModel);
        $viewer->assign('COMPANY_NAME', $recordModel->get('company'));
        $viewer->view('IntlQuote.tpl', $moduleName);
    }
}
