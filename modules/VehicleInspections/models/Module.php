<?php

/**
 * Resource Management Module
 *
 *
 * @package        ResourceDashboard Module
 * @author         Conrado Maggi - www.vgsglobal.com
 */
class VehicleInspections_Module_Model extends Vtiger_Module_Model
{
    public function getSideBarLinks($linkParams)
    {
        $linkTypes = array('SIDEBARLINK', 'SIDEBARWIDGET');
        $links = parent::getSideBarLinks($linkParams);

        
        $quickLinks = array(
            array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => 'LBL_VEHICLES_LIST',
                'linkurl' => 'index.php?module=Vehicles&view=List',
                'linkicon' => '',
            )
        );
        foreach ($quickLinks as $quickLink) {
            $links['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);
        }

        return $links;
    }
}
