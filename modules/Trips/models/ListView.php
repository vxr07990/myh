<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/**
 * Vtiger ListView Model Class
 */
class Trips_ListView_Model extends Vtiger_ListView_Model
{
    /**
     * Function to get the list of Mass actions for the module
     * @param <Array> $linkParams
     * @return <Array> - Associative array of Link type to List of  Vtiger_Link_Model instances for Mass Actions
     */
    public function getListViewMassActions($linkParams)
    {

        $links = parent::getListViewMassActions($linkParams);
        
        foreach ($links['LISTVIEWMASSACTION'] as $key => $link) {
           if($link->linklabel ==  'LBL_DUPLICATE'){
               unset($links['LISTVIEWMASSACTION'][$key]);
           }
        }
        
        return $links;
    }

}
