<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
vimport('~~/vtlib/Vtiger/Module.php');

/**
 * Vtiger Module Model Class
 */
class AutoSpotQuote_Module_Model extends Vtiger_Module_Model
{

    /**
     * Function to check whether the module is summary view supported
     * @return <Boolean> - true/false
     */
    public function isSummaryViewSupported()
    {
        return false;
    }

    /**
     * Function to get the url for list view of the module
     * @return <string> - url
     */
    public function getListViewUrl($estimateId)
    {
        //return 'index.php?module='.$this->get('name').'&view='.$this->getListViewName();
        return 'index.php?module=Estimates&relatedModule=AutoSpotQuote&view=Detail&record=' . $estimateId . '&mode=showRelatedList&tab_label=Auto%20Spot%20Quote';
    }

    /**
     * Function to get the Quick Links for the module
     * @param <Array> $linkParams
     * @return <Array> List of Vtiger_Link_Model instances
     */
    public function getSideBarLinks($linkParams, $estimateId)
    {
        $linkTypes = array('SIDEBARLINK', 'SIDEBARWIDGET');
        $links = Vtiger_Link_Model::getAllByType($this->getId(), $linkTypes, $linkParams);

        $quickLinks = array(
            array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => 'LBL_RECORDS_LIST',
                'linkurl' => $this->getListViewUrl($estimateId),
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
