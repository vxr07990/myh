<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Users_EditAjax_View extends Vtiger_IndexAjax_View
{
    public function __construct()
    {
        parent::__construct();
        $this->exposeMethod('changePassword');
        $this->exposeMethod('setExchangeCredentials');
    }

    public function process(Vtiger_Request $request)
    {
        $mode = $request->get('mode');
        if (!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);

            return;
        }
    }

    public function changePassword(Vtiger_Request $request)
    {
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $currentUserAgents = $currentUser->getAccessibleAgentsForUser();
        $targetUser = Vtiger_Record_Model::getInstanceById($request->get('recordId'), 'Users');
        $targetUserAgents = $targetUser->getAccessibleAgentsForUser();

        $viewer     = $this->getViewer($request);
        $moduleName = $request->get('module');
        $userId     = $request->get('recordId');
        if($request->get('relModule') && $currentUser->isParentVanLineUser() && count(array_intersect($targetUserAgents, $currentUserAgents)) > 0) {
            //Only include the RELMODULE var if the current user has permission to change the target user's password
            //This controls the presence of the Old Password field
            $viewer->assign('RELMODULE', $request->get('relModule'));
        }
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('USERID', $userId);
        $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->view('ChangePassword.tpl', $moduleName);
    }

    public function setExchangeCredentials(Vtiger_Request $request)
    {
        $viewer     = $this->getViewer($request);
        $moduleName = $request->get('module');
        $userId     = $request->get('recordId');
        $userModel  = Users_Record_Model::getInstanceById($userId, 'Users');
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('USERID', $userId);
        $viewer->assign('USERMODEL', $userModel);
        $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->view('SetExchangeCredentials.tpl', $moduleName);
    }
}
