<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class ProjectTask_DetailView_Model extends Vtiger_DetailView_Model
{

    /**
     * Function to get the detail view widgets
     * @return <Array> - List of widgets , where each widget is an Vtiger_Link_Model
     */
    public function getWidgets()
    {
        $userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $widgetLinks = parent::getWidgets();
        $widgets = array();

//VGS Adding the new widget to the project dashboard
        $projectTaskInstance = Vtiger_Module_Model::getInstance('ProjectTask');


        if ($userPrivilegesModel->hasModuleActionPermission($projectTaskInstance->getId(), 'DetailView')) {
            $widgets[] = array(
                'linktype' => 'DETAILVIEWWIDGET',
                'linklabel' => 'Resources',
                'linkName' => 'Resources',
                'linkurl' => 'module=ProjectTask&view=Detail&mode=getResources&record=' . $this->getRecord()->getId(),
                'action' => array('Add'),
                'actionURL' => 'module=ResourceDashboard&view=ResourceSelect&taskname=Loading&projectid=' . $this->getRecord()->getId()
            );
        }


        foreach ($widgets as $widgetDetails) {
            $widgetLinks[] = Vtiger_Link_Model::getInstanceFromValues($widgetDetails);
        }

        return $widgetLinks;
    }
}
