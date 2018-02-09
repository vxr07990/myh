<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Invoice_Detail_View extends Inventory_Detail_View
{
    public function showLineItemDetails(Vtiger_Request $request)
    {
        $record          = $request->get('record');
        $moduleName          = $request->getModule();
        $recordModel     = Inventory_Record_Model::getInstanceById($record);
        $actualId = $recordModel->get('actualsid');
        $viewer = $this->getViewer($request);
        if(!empty($actualId)){
            $moduleModel = $recordModel->getModule();
            $results = $moduleModel->getRevenueDistribitionValues($actualId);
            $viewer->assign('BLOCK_DATA',$results);
        }
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->view('LineItems.tpl', 'Invoice');
    }
    public function getHeaderCss(Vtiger_Request $request)
    {
        $headerCssInstances = parent::getHeaderCss($request);

        $cssFileNames = array(
            '~layouts/vlayout/modules/Invoice/resources/style.css',
        );
        $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
        $headerCssInstances = array_merge($headerCssInstances, $cssInstances);

        return $headerCssInstances;
    }
}
