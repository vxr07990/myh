<?php

/**
 * Resource Management Module
 *
 *
 * @package        ResourceDashboard Module
 * @author         Conrado Maggi - www.vgsglobal.com
 */
class UserManagement_Module_Model extends Vtiger_Module_Model
{
    public function getSideBarLinks($linkParams)
    {
        $linkTypes = array('SIDEBARLINK', 'SIDEBARWIDGET');
        $links = parent::getSideBarLinks($linkParams);


        $quickLinks = array(
            array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => 'LBL_USERMANAGEMENT_USERSLIST',
                'linkurl' => 'index.php?module=Users&parent=Settings&view=List',
                'linkicon' => '',
            ),
            array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => 'LBL_USERMANAGEMENT_ROLESLIST',
                'linkurl' => 'index.php?module=Roles&parent=Settings&view=Index',
                'linkicon' => '',
            ),
            array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => 'LBL_USERMANAGEMENT_PROFILESLIST',
                'linkurl' => 'index.php?module=Profiles&parent=Settings&view=List',
                'linkicon' => '',
            ),
            array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => 'LBL_USERMANAGEMENT_GROUPSLIST',
                'linkurl' => 'index.php?module=Groups&parent=Settings&view=List',
                'linkicon' => '',
            ),
        );
        foreach ($quickLinks as $quickLink) {
            $links['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);
        }

        return $links;
    }
/*
            $quickLinks = array(
            array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => 'LBL_USERMANAGEMENT_USERSLIST',
                'linkurl' => $this->getUsersListUrl(),
                'linkicon' => '',
            ),
            array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => 'LBL_USERMANAGEMENT_ROLESLIST',
                'linkurl' => $this->getRolesListUrl(),
                'linkicon' => '',
            ),
            array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => 'LBL_USERMANAGEMENT_PROFILESLIST',
                'linkurl' => $this->getProfilesListUrl(),
                'linkicon' => '',
            ),
            array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => 'LBL_USERMANAGEMENT_GROUPSLIST',
                'linkurl' => $this->getGroupsListUrl(),
                'linkicon' => '',
            ),
        );
        foreach ($quickLinks as $quickLink) {
            $links['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);
        }

        return $links;
    }
    */

    public function getUsersListUrl()
    {
        $usersListModel = Vtiger_Module_Model::getInstance('Users');
        return $usersListModel->getListViewUrl();
    }

    public function getRolesListUrl()
    {
        $rolesListModel = Vtiger_Module_Model::getInstance('Roles');
        return $rolesListModel->getListViewUrl();
    }

    public function getProfilesListUrl()
    {
        $profilesListModel = Vtiger_Module_Model::getInstance('Profiles');
        return $profilesListModel->getListViewUrl();
    }
    
    public function getGroupsListUrl()
    {
        $groupsListModel = Vtiger_Module_Model::getInstance('Groups');
        return $groupsListModel->getListViewUrl();
    }
}
