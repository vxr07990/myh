<?php

/**
 * Resource Management Module
 *
 *
 * @package        ResourceDashboard Module
 * @author         Conrado Maggi - www.vgsglobal.com
 */
class Vehicles_Module_Model extends Vtiger_Module_Model
{
    public function getSideBarLinks($linkParams)
    {
        $linkTypes = array('SIDEBARLINK', 'SIDEBARWIDGET');
        $links = parent::getSideBarLinks($linkParams);

        
        $quickLinks = array(
            array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => 'LBL_INSPECTIONS_LIST',
                'linkurl' => 'index.php?module=VehicleInspections&view=List',
                'linkicon' => '',
            ),
            array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => 'LBL_MAINTENANCE_LIST',
                'linkurl' => 'index.php?module=VehicleMaintenance&view=List',
                'linkicon' => '',
            )
        );
        foreach ($quickLinks as $quickLink) {
            $links['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);
        }

        return $links;
    }
}
