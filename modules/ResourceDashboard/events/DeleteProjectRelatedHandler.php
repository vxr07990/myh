<?php

/**
 * Resource Management Module
 *
 *
 * @package        ResourceDashboard Module
 * @author         Conrado Maggi - www.vgsglobal.com
 */

class DeleteProjectRelated extends VTEventHandler
{
    public function handleEvent($eventName, $data)
    {
        if ($eventName == 'vtiger.entity.afterdelete' && $data->getModuleName() == 'Project') {
            $db = PearDatabase::getInstance();
            // Entity is about to be saved, take required action
            $projectId = $data->focus->id;

            $sql = "UPDATE vtiger_crmentity,vtiger_projecttask SET deleted = 1 
                        WHERE vtiger_crmentity.crmid = vtiger_projecttask.projecttaskid
                        AND vtiger_projecttask.projectid = ?";
            $db->pquery($sql, array($projectId));
        }
    }
}
