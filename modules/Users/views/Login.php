<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Users_Login_View extends Vtiger_View_Controller
{
    public function loginRequired()
    {
        return false;
    }

    public function preProcess(Vtiger_Request $request, $display = true)
    {
        //changed to prevent login from having double headers
    }

    public function checkPermission(Vtiger_Request $request)
    {
        return true;
    }

    public function process(Vtiger_Request $request)
    {
        $viewer           = $this->getViewer($request);
        $moduleName       = $request->getModule();
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $HQ               = $currentUserModel->getMoveHQVersion();
        $viewer->assign('HQ', $HQ);
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('CURRENT_VERSION', vglobal('vtiger_current_version'));
        $viewer->view('Login.tpl', 'Users');
    }
}
