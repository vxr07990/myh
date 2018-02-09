<?php

/**
 * Resource Management Module
 *
 *
 * @package        ResourceDashboard Module
 * @author         Conrado Maggi - www.vgsglobal.com
 */
class Equipment_Module_Model extends Vtiger_Module_Model
{
    public function getSideBarLinks($linkParams)
    {
        $linkTypes = array('SIDEBARLINK', 'SIDEBARWIDGET');
        $links = parent::getSideBarLinks($linkParams);

        
        $quickLinks = array(
            array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => 'LBL_RESOURCE_DASHBOARD',
                'linkurl' => $this->getResourcesListUrl(),
                'linkicon' => '',
            )
        );
        foreach ($quickLinks as $quickLink) {
            $links['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);
        }

        return $links;
    }
    
    public function isSummaryViewSupported()
    {
        return false;
    }
    
    public function getResourcesListUrl()
    {
        //removed because we're not going to use resource manager and this broke things
        /*$employeeModel = Vtiger_Module_Model::getInstance('ResourceDashboard');
        return $employeeModel->getListViewUrl();*/
    }
}
