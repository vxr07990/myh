<?php

/**
 * Resource Management Module
 *
 *
 * @package        ResourceDashboard Module
 * @author         Conrado Maggi - www.vgsglobal.com
 */
class ResourceDashboard
{

    /**
     * Invoked when special actions are performed on the module.
     * @param String Module name
     * @param String Event Type
     */
    public function vtlib_handler($moduleName, $eventType)
    {
        if ($eventType == 'module.postinstall') {

          
            //Adding Custom events for project management
            include_once('vtlib/Vtiger/Event.php');

            // Event to create tasks on project creation
            if (Vtiger_Event::hasSupport()) {
                Vtiger_Event::register(
                        'Project', 'vtiger.entity.aftersave', 'IGCProjectTasks', 'modules/ResourceDashboard/events/IGCProjectHandler.php'
                );
            }

            // Event to delete project tasks after project delete
            if (Vtiger_Event::hasSupport()) {
                Vtiger_Event::register(
                        'Project', 'vtiger.entity.afterdelete', 'DeleteProjectRelated', 'modules/ResourceDashboard/events/DeleteProjectRelatedHandler.php'
                );
            }

            // Event to remove the conflicted resources
            if (Vtiger_Event::hasSupport()) {
                Vtiger_Event::register(
                        'ProjectTask', 'vtiger.entity.aftersave', 'UnsetRelatedResources', 'modules/ResourceDashboard/events/UnsetRelatedResources.php'
                );
            }

            //Adding related lists
            $Vtiger_Utils_Log = true;
            include_once('vtlib/Vtiger/Menu.php');
            include_once('vtlib/Vtiger/Module.php');


            $module = Vtiger_Module::getInstance('ProjectTask');
            $module->setRelatedList(Vtiger_Module::getInstance('ResourceDashboard'), 'Resources', array(), 'get_resources');

            $module = Vtiger_Module::getInstance('Project');
            $module->setRelatedList(Vtiger_Module::getInstance('ResourceDashboard'), 'Resources', array(), 'get_resources');
        } elseif ($eventType == 'module.disabled') {
        } elseif ($eventType == 'module.preuninstall') {
        } elseif ($eventType == 'module.preupdate') {
        } elseif ($eventType == 'module.postupdate') {
        }
    }
}
