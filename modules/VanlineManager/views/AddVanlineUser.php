<?php
require_once('include/utils/UserInfoUtil.php');

class VanlineManager_AddVanlineUser_View extends Vtiger_Popup_View
{
    public function process(Vtiger_Request $request)
    {
        $viewer           = $this->getViewer($request);
        $moduleName       = $this->getModule($request);
        $companyDetails   = Vtiger_CompanyDetails_Model::getInstanceById();
        $companyLogo      = $companyDetails->getLogo();
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
        //file_put_contents('logs/Roles.log', "\n \$subordinateRoles: ".print_r($subordinateRoles, true), FILE_APPEND);
        $viewer->assign('SUB_ROLES', $subordinateRoles);
        //$viewer->assign('ROLES', $roles);
        //file_put_contents('logs/Roles.log', "\n \$roleNames: ".print_r($roleNames, true), FILE_APPEND);
        $viewer->assign('ROLE_NAMES', $roleNames);
        $viewer->view('AddVanlineUser.tpl', $moduleName);
    }

    public function getHeaderScripts(Vtiger_Request $request)
    {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName            = $request->getModule();
        $jsFileNames           = [
            'modules.Vtiger.resources.Detail',
            'modules.Vtiger.resources.Edit',
            'modules.VanlineManager.resources.AddVanlineUser',
        ];
        $jsScriptInstances     = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);

        return $headerScriptInstances;
    }
}
