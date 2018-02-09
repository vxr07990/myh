<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Orders_DetailView_Model extends Vtiger_DetailView_Model
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

        //old securities
        //$db = PearDatabase::getInstance();

        //$userModel = Users_Record_Model::getCurrentUserModel();
        //$currentUserId = $userModel->getId();

        //$isAdmin = $userModel->isAdminUser();

        //$recordId = $this->getRecord()->getId();

        //$creatorPermissions = false;
        /*
        if($isAdmin){
            $creatorPermissions = true;
        }else{
            $userGroups = array();
            $sql = "SELECT groupid FROM `vtiger_users2group` WHERE userid=?";
            $result = $db->pquery($sql, array($currentUserId));
            $row = $result->fetchRow();

            while($row != NULL){
                $userGroups[] = $row[0];
                $row = $result->fetchRow();
            }
            $groupOwned = array();
            foreach($userGroups as $group){
                $sql = "SELECT crmid FROM `vtiger_crmentity` WHERE smownerid=?";
                $result = $db->pquery($sql, array($group));
                $row = $result->fetchRow();
                while($row != NULL){
                    $groupOwned[] = $row[0];
                    $row = $result->fetchRow();
                }
            }
            foreach($groupOwned as $owned){
                if($owned == $recordId){
                    $creatorPermissions = true;
                }
            }
        } */

        $helpDeskInstance = Vtiger_Module_Model::getInstance('HelpDesk');
        if ($userPrivilegesModel->hasModuleActionPermission($helpDeskInstance->getId(), 'DetailView')) {
            $createPermission = $userPrivilegesModel->hasModuleActionPermission($helpDeskInstance->getId(), 'EditView');
            $widgets[] = array(
                    'linktype' => 'DETAILVIEWWIDGET',
                    'linklabel' => 'HelpDesk',
                    'linkName'    => $helpDeskInstance->getName(),
                    'linkurl' => 'module='.$this->getModuleName().'&view=Detail&record='.$this->getRecord()->getId().
                            '&relatedModule=HelpDesk&mode=showRelatedRecords&page=1&limit=5',
                    'action'    =>    array('Add'),
                    'actionURL' =>    $helpDeskInstance->getQuickCreateUrl()
                );
        }

        $ordersMileStoneInstance = Vtiger_Module_Model::getInstance('OrdersMilestone');
        if ($userPrivilegesModel->hasModuleActionPermission($ordersMileStoneInstance->getId(), 'DetailView')) {
            $createPermission = $userPrivilegesModel->hasModuleActionPermission($ordersMileStoneInstance->getId(), 'EditView');
            $widgets[] = array(
                    'linktype' => 'DETAILVIEWWIDGET',
                    'linklabel' => 'LBL_MILESTONES',
                    'linkName'    => $ordersMileStoneInstance->getName(),
                    'linkurl' => 'module='.$this->getModuleName().'&view=Detail&record='.$this->getRecord()->getId().
                            '&relatedModule=OrdersMilestone&mode=showRelatedRecords&page=1&limit=5',
                    'action'    =>    ($createPermission == true && $creatorPermissions == true) ? array('Add') : array(),
                    'actionURL' =>    $ordersMileStoneInstance->getQuickCreateUrl()
            );
        }

        $ordersTaskInstance = Vtiger_Module_Model::getInstance('OrdersTask');
        if ($userPrivilegesModel->hasModuleActionPermission($ordersTaskInstance->getId(), 'DetailView')) {
            $createPermission = $userPrivilegesModel->hasModuleActionPermission($ordersTaskInstance->getId(), 'EditView');
            $widgets[] = array(
                    'linktype' => 'DETAILVIEWWIDGET',
                    'linklabel' => 'LBL_TASKS',
                    'linkName'    => $ordersTaskInstance->getName(),
                    'linkurl' => 'module='.$this->getModuleName().'&view=Detail&record='.$this->getRecord()->getId().
                            '&relatedModule=OrdersTask&mode=showRelatedRecords&page=1&limit=5',
                    'action'    =>    ($createPermission == true && $creatorPermissions == true) ? array('Add') : array(),
                    'actionURL' =>    $ordersTaskInstance->getQuickCreateUrl()
            );
        }


        $documentsInstance = Vtiger_Module_Model::getInstance('Documents');
        if ($userPrivilegesModel->hasModuleActionPermission($documentsInstance->getId(), 'DetailView')) {
            $createPermission = $userPrivilegesModel->hasModuleActionPermission($documentsInstance->getId(), 'EditView');
            $widgets[] = array(
                    'linktype' => 'DETAILVIEWWIDGET',
                    'linklabel' => 'Documents',
                    'linkName'    => $documentsInstance->getName(),
                    'linkurl' => 'module='.$this->getModuleName().'&view=Detail&record='.$this->getRecord()->getId().
                            '&relatedModule=Documents&mode=showRelatedRecords&page=1&limit=5',
                    'action'    =>    ($createPermission == true && $creatorPermissions == true) ? array('Add') : array(),
                    'actionURL' =>    $documentsInstance->getQuickCreateUrl()
            );
        }

        $moveRolesInstance = Vtiger_Module_Model::getInstance('MoveRoles');
        if ($userPrivilegesModel->hasModuleActionPermission($moveRolesInstance->getId(), 'DetailView')) {
            $widgets[] = array(
                'linktype' => 'DETAILVIEWWIDGET',
                'linklabel' => 'Move Roles',
            );
        }

        foreach ($widgets as $widgetDetails) {
            $widgetLinks[] = Vtiger_Link_Model::getInstanceFromValues($widgetDetails);
        }

        return $widgetLinks;
    }
}
