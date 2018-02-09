<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Documents_AddFolder_View extends Vtiger_IndexAjax_View
{
    public function checkPermission(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        if (!Users_Privileges_Model::isPermitted($moduleName, 'EditView')) {
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED', $moduleName));
        }
    }

    public function process(Vtiger_Request $request)
    {
        $viewer     = $this->getViewer($request);
        $userModel = Users_Record_Model::getCurrentUserModel();
        $moduleName = $request->getModule();
        $viewer->assign('MODULE', $moduleName);
        $listAgentManager = $userModel->getAccessibleOwnersForUser('');
        unset($listAgentManager['agents']);
        $listVanlines = $userModel->getAccessibleVanlinesForUser('');
        $viewer->assign('LIST_VANLINES', $listVanlines);
        $viewer->assign('LIST_AGENTMANAGER', $listAgentManager);
        $viewer->view('AddFolder.tpl', $moduleName);
    }
}
