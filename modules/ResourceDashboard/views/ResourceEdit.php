<?php

/**
 * Resource Management Module
 * @package        ResourceDashboard Module
 * @author         Conrado Maggi - www.vgsglobal.com
 */
class ResourceDashboard_ResourceEdit_View extends Vtiger_Index_View
{
    public function checkPermission(Vtiger_Request $request)
    {
        $moduleName                 = $request->getModule();
        $moduleModel                = Vtiger_Module_Model::getInstance($moduleName);
        $currentUserPriviligesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        if (!$currentUserPriviligesModel->hasModuleActionPermission($moduleModel->getId(), 'ConvertLead')) {
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED', $moduleName));
        }
    }

    public function process(Vtiger_Request $request)
    {
        $currentUserPriviligeModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $viewer                    = $this->getViewer($request);
        $assignmentResult          = $request->get('result');
        $taskId                    = $request->get('projecttaskid');
        $allocatedQty              = $request->get('allocatedqty');
        $moduleName                = $request->getModule();
        $resourceid                = $request->get('resourceid');
        $resourceDashModel         = Vtiger_Module_Model::getInstance('ResourceDashboard');
        $resources                 = $resourceDashModel->getResourceFreeQtyArray($resourceid, $taskId, $allocatedQty);
        $projectTask               = Vtiger_Record_Model::getInstanceById($taskId);
        $projectId                 = $projectTask->get('projectid');
        $resourcesTypes            = ['', 'Employees', 'Vehicles', 'Equipment'];
        $viewer->assign('RESOURCE_TYPES', $resourcesTypes);
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('RESOURCE_LIST', $resources);
        $viewer->assign('PROJECT_ID', $projectId);
        $viewer->assign('TASK_ID', $taskId);
        $viewer->assign('ARESULT', $assignmentResult);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('CURRENT_USER_PRIVILEGE', $currentUserPriviligeModel);
        $viewer->assign('RECORD', $recordModel);
        $viewer->view('ResourceEdit.tpl', $moduleName);
    }
}
