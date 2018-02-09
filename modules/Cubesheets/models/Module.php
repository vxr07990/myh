<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Cubesheets_Module_Model extends Inventory_Module_Model
{
    public function getSideBarLinks($linkParams)
    {
        $linkTypes = array('SIDEBARLINK', 'SIDEBARWIDGET');
        $links = Vtiger_Link_Model::getAllByType($this->getId(), $linkTypes, $linkParams);

        $quickLinks = array(
            /*array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => 'LBL_RECORDS_LIST',
                'linkurl' => $this->getListViewUrl(),
                'linkicon' => '',
            ),*/
            array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => 'Parent Record',
                'linkurl' => '',
                'linkicon' => '',
            ),
        );
        foreach ($quickLinks as $quickLink) {
            $links['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);
        }

        $quickWidgets = array(
            array(
                'linktype' => 'SIDEBARWIDGET',
                'linklabel' => 'LBL_RECENTLY_MODIFIED',
                'linkurl' => 'module='.$this->get('name').'&view=IndexAjax&mode=showActiveRecords',
                'linkicon' => ''
            ),
        );
        foreach ($quickWidgets as $quickWidget) {
            $links['SIDEBARWIDGET'][] = Vtiger_Link_Model::getInstanceFromValues($quickWidget);
        }

        return $links;
    }
}
