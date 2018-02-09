<?php

/**
 * Resource Management Module
 *
 *
 * @package        ResourceDashboard Module
 * @author         Conrado Maggi - www.vgsglobal.com
 */
class IGCProjectTasks extends VTEventHandler
{
    public function handleEvent($eventName, $data)
    {
        if ($eventName == 'vtiger.entity.aftersave' && $data->getModuleName() == 'Project') {
            $db = PearDatabase::getInstance();
            // Entity is about to be saved, take required action
            $projectId = $data->focus->id;

            
            if ($data->isNew()) {
                include_once 'include/Webservices/Create.php';
                global $log, $adb;
                
                $projectData = $data->getData();

                $task['projecttaskname'] = 'Loading';
                $task['projectid'] = $data->getId();
                $task['projecttaskprogress'] = 0.0;
                $task['startdate'] = $projectData['startdate'];
                $task['enddate'] = $projectData['targetenddate'];
                $task['projecttaskstatus'] = 'Open';
                $task['assigned_user_id'] = $projectData['assigned_user_id'];

                $ProjectTaskModel = Vtiger_Record_Model::getCleanInstance('ProjectTask');
                $ProjectTaskModel->setData($task);
                $ProjectTaskModel->save();

                $task['projecttaskname'] = 'Transit';

                $ProjectTaskModel = Vtiger_Record_Model::getCleanInstance('ProjectTask');
                $ProjectTaskModel->setData($task);
                $ProjectTaskModel->save();

                $task['projecttaskname'] = 'Delivery';

                $ProjectTaskModel = Vtiger_Record_Model::getCleanInstance('ProjectTask');
                $ProjectTaskModel->setData($task);
                $ProjectTaskModel->save();
            }
        }
    }
}
