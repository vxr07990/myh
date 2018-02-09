<?php

/**
 * Resource Management Module
 * @package        ResourceDashboard Module
 * @author         Conrado Maggi - www.vgsglobal.com
 */
class ResourceDashboard_ResourceSelect_View extends Vtiger_Index_View
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
        $db                        = PearDatabase::getInstance();
        $currentUserPriviligeModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $viewer                    = $this->getViewer($request);
        $refferer                  = $request->get('refferer');
        $assignmentResult          = $request->get('result');
        $moduleName                = $request->getModule();
        if ($refferer != '' && $refferer = 'ProjectTask') {
            $projectTask   = Vtiger_Record_Model::getInstanceById($taskId);
            $startDate     = $projectTask->get('startdate');
            $endDate       = $projectTask->get('enddate');
            $resourcesDash = Vtiger_Module_Model::getInstance('ResourceDashboard');
            $vehicles      = $resourcesDash->getAvailableVehicles($startDate, $endDate);
            $equipment     = $resourcesDash->getAvailableEquiptment($startDate, $endDate);
            $employee      = $resourcesDash->getAvailableEmployee($startDate, $endDate);
            $resources     = array_filter(array_merge_recursive($vehicles, $equipment, $employee));
        } else {
            $result = $db->pquery('SELECT vtiger_projecttask.* FROM vtiger_projecttask
			INNER JOIN vtiger_crmentity ON vtiger_projecttask.projecttaskid = vtiger_crmentity.crmid
			WHERE vtiger_crmentity.deleted=0
			AND vtiger_projecttask.projectid=?
			AND vtiger_projecttask.projecttaskname=?',
                                  [$request->get('projectid'), $request->get('projecttask')]);
            if ($db->num_rows($result) > 0) {
                $projectTaks = $db->fetchByAssoc($result);
            }
            $resourcesDash = Vtiger_Module_Model::getInstance('ResourceDashboard');
            $vehicles      = $resourcesDash->getAvailableVehicles($projectTaks['startdate'], $projectTaks['enddate']);
            $equipment     = $resourcesDash->getAvailableEquiptment($projectTaks['startdate'], $projectTaks['enddate']);
            $employee      = $resourcesDash->getAvailableEmployee($projectTaks['startdate'], $projectTaks['enddate']);
            $resources     = array_filter(array_merge_recursive($vehicles, $equipment, $employee));
        }
        $viewer->assign('MODULE', $moduleName);
        $resourcesTypes = ['', 'Employees', 'Vehicles', 'Equipment'];
        $viewer->assign('RESOURCE_TYPES', $resourcesTypes);
        $viewer->assign('RESOURCE_LIST', $resources);
        $viewer->assign('PROJECT_ID', $projectTaks['projectid']);
        $viewer->assign('TASK_ID', $projectTaks['projecttaskid']);
        $viewer->assign('ARESULT', $assignmentResult);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('CURRENT_USER_PRIVILEGE', $currentUserPriviligeModel);
        $viewer->assign('RECORD', $recordModel);
        $viewer->view('ResourceSelect.tpl', $moduleName);
    }
}
