<?php
require_once('include/utils/UserInfoUtil.php');

class AgentManager_CoordinatorListEdit_View extends Vtiger_Edit_View
{
    public function process(Vtiger_Request $request)
    {
        $viewer           = $this->getViewer($request);
        $moduleName       = $request->getModule();
        $userModel        = Users_Record_Model::getCurrentUserModel();
        $roleid           = $userModel->roleid;
        $subordinateRoles = getRoleSubordinates($roleid);
        $roles            = $subordinateRoles;
        $roleNames        = [];
        foreach ($subordinateRoles as $id => $role) {
            $roleNames[$id] = getRoleName($role);
        }
        $viewer->assign('COMPANY_LOGO', $companyLogo);
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('USER', $request->get('user'));
        $viewer->assign('SRC_RECORD', $request->get('record'));
        $viewer->assign('USER_ROLE', $roleid);
        $viewer->assign('CURRENT_USER', $userModel);
        $viewer->view('CoordinatorListEdit.tpl', $moduleName);
    }

    public function getHeaderScripts(Vtiger_Request $request)
    {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName            = $request->getModule();
        $jsFileNames           = [
            'modules.Vtiger.resources.Detail',
            'modules.Vtiger.resources.Edit',
            'modules.AgentManager.resources.CoordinatorListEdit',
        ];
        $jsScriptInstances     = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);

        return $headerScriptInstances;
    }
}
