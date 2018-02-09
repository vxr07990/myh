<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class MovePolicies_Edit_View extends Vtiger_Edit_View
{
    protected $record = false;

    public function __construct()
    {
        parent::__construct();
    }

    public function process(Vtiger_Request $request)
    {
        $recordId = $request->get('record');
        if ($recordId != '') {
            $moduleName = $request->getModule();
            $currentUserModel = Users_Record_Model::getCurrentUserModel();
            $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);


            $tariffItems = $recordModel->getTariffItems();
            $itemsCount = $tariffItems['items_count'];
            unset($tariffItems['items_count']);
            
            $miscTariffItems = $recordModel->getMiscTariffItems();
            
            $viewer = $this->getViewer($request);
            $viewer->assign('TARIFF_ITEMS', $tariffItems);
            $viewer->assign('MISC_TARIFF_ITEMS', $miscTariffItems);
            $viewer->assign('MISC_ITEMS_COUNT', count($miscTariffItems));
            $viewer->assign('ITEMS_COUNT', $itemsCount);
        }



        parent::process($request);
    }
}
