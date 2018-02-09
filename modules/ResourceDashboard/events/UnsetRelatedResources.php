<?php

/**
 * Resource Management Module
 *
 *
 * @package        ResourceDashboard Module
 * @author         Conrado Maggi - www.vgsglobal.com
 */

class UnsetRelatedResources extends VTEventHandler
{
    public function handleEvent($eventName, $data)
    {
        if ($eventName == 'vtiger.entity.aftersave' && $data->getModuleName() == 'ProjectTask') {
            $db = PearDatabase::getInstance();
            // Entity is about to be saved, take required action
            $taskId = $data->focus->id;
            $startDate = $data->get('startdate');
            $endDate = $data->get('enddate');
            
            $resourceDashModel = Vtiger_Module_Model::getCleanInstance('ResourceDashboard');
            $resourceDashModel->unsetBusyResources($taskId, $startDate, $endDate);
        }
    }
}
