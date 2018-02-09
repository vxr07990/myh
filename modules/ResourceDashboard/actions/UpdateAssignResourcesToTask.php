<?php

/**
 * Resource Management Module
 *
 *
 * @package        ResourceDashboard Module
 * @author         Conrado Maggi - www.vgsglobal.com
 */

class ResourceDashboard_UpdateAssignResourcesToTask_Action extends Vtiger_Delete_Action
{
    public function process(Vtiger_Request $request)
    {
        $db = PearDatabase::getInstance();

        $assignment = 'works';
        $projectId = $request->get('project_id');
        $taskId = $request->get('tasks_id');
        $selectedIds = $request->get('selectedIds');

        foreach ($selectedIds as $resourceId => $qty) {
            if ($qty == 0) {
                $adb->pquery('DELETE FROM vtiger_resourcedashboard WHERE projecttaskid=? AND resourceid=?', array($taskId, $resourceId));
            } else {
                $sql = ("UPDATE `vtiger_resourcedashboard` SET `quantity`=? WHERE projecttaskid = ? AND resourceid = ?");
                $params = array($qty, $taskId, $resourceId);
                $result = $db->pquery($sql, $params);
                if (!$result) {
                    $assignment = 'failed';
                }
            }
        }


        $response = new Vtiger_Response();
        $response->setResult(array('result' => $assignment, 'qty' => $qty));
        $response->emit();
    }
}
