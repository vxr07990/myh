<?php

class VendorsOutofService_Module_Model extends Vtiger_Module_Model
{
    public function getSideBarLinks($linkParams)
    {
        $linkTypes = array('SIDEBARLINK', 'SIDEBARWIDGET');
        $links = parent::getSideBarLinks($linkParams);

        
        $quickLinks = array(
            array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => 'LBL_VENDOR_LIST',
                'linkurl' => 'index.php?module=Vendors&view=List',
                'linkicon' => '',
            )
        );
        foreach ($quickLinks as $quickLink) {
            $links['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);
        }

        return $links;
    }
}
