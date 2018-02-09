<?php

/**
 * Resource Management Module
 *
 *
 * @package        ResourceDashboard Module
 * @author         Conrado Maggi - www.vgsglobal.com
 */

class ResourceDashboard_AssignResourcesToTask_Action extends Vtiger_Delete_Action
{
    public function process(Vtiger_Request $request)
    {
        $db = PearDatabase::getInstance();

        $assignment = 'works';
        $projectId = $request->get('project_id');
        $taskId = $request->get('tasks_id');
        $selectedIds = $request->get('selectedIds');

        foreach ($selectedIds as $resourceId => $qty) {
            $sql = ("INSERT INTO `vtiger_resourcedashboard` (`projecttaskid`, `resourceid`, `quantity`) VALUES (?, ?, ?)");
            $params = array($taskId, $resourceId, $qty);
            $result = $db->pquery($sql, $params);
            if (!$result) {
                $assignment = 'failed';
            }
        }


        $response = new Vtiger_Response();
        $response->setResult(array('result' => $assignment));
        $response->emit();
    }
}
