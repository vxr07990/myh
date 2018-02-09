<?php

require_once('modules/Settings/Picklist/models/Field.php');

class PicklistCustomizer_Edit_View extends Vtiger_Index_View
{

    public function checkPermission(Vtiger_Request $request)
        {
            $currentUser = Users_Record_Model::getCurrentUserModel();
            if(!$currentUser->isAdminUser() && !$currentUser->isVanlineUser()){
                 throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
            }

            return true;
        }

    public function preProcess(Vtiger_Request $request, $display = true)
    {
        parent::preProcess($request,true);
    }
    function process(Vtiger_Request $request)
    {
        global $current_user;

        $userModel = Users_Record_Model::getInstanceById($current_user->id, 'Users');

        $listAgentManager = $userModel->getAccessibleOwnersForUser('');
        unset($listAgentManager['agents']);
        $listVanlines = $userModel->getAccessibleVanlinesForUser('');

        $moduleName = $request->getModule();
        $qualifiedName = $request->getModule(false);

        $viewer = $this->getViewer($request);

        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('LIST_VANLINES', $listVanlines);
        $viewer->assign('LIST_AGENTMANAGER', $listAgentManager);
        $viewer->view('PicklistEditView.tpl', $qualifiedName);
    }

    function getHeaderScripts(Vtiger_Request $request)
    {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName = $request->getModule();

        $jsFileNames = array(
            "modules.Settings.Vtiger.resources.Index",
            "modules.$moduleName.resources.Picklist",
        );

        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }
}
