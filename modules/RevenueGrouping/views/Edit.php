<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class RevenueGrouping_Edit_View extends Vtiger_Edit_View
{
    protected $record = false;
    public static  $revenueGroupingItemDefault = [
        'Transportation',
        'Transportation - Other',
        'Containers',
        'Packing',
        'Unpacking',
        'Accessorials',
        'Bulkies',
        'Drayage',
        'Storage',
        'Valuation',
        'Misc Agent Charges',
    ];
    function __construct() {
        parent::__construct();
    }

    public function process(Vtiger_Request $request)
    {
        $viewer= $this->getViewer($request);
        $record     = $request->get('record');
        //logic to include RevenueGroupingItem
        $revenueGroupingItemModel = Vtiger_Module_Model::getInstance('RevenueGroupingItem');
        if ($revenueGroupingItemModel && $revenueGroupingItemModel->isActive()) {
            $viewer->assign('REVENUEGROUPINGITEM_MODULE_MODEL', $revenueGroupingItemModel);
            $viewer->assign('REVENUEGROUPINGITEM_BLOCK_FIELDS', $revenueGroupingItemModel->getFields('LBL_REVENUEGROUPINGITEMSDETAIL'));
            $viewer->assign('REVENUEGROUPINGITEM_LIST', $revenueGroupingItemModel->getRevenueGroupingItem($record));
            $viewer->assign('REVENUEGROUPINGITEM_LIST_DEFAULT', self::$revenueGroupingItemDefault);
        }
        parent::process($request);
    }
    public function getHeaderScripts(Vtiger_Request $request)
    {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName            = $request->getModule();
        $vehicleLookupModel    = Vtiger_Module_Model::getInstance('VehicleLookup');
        $jsFileNames = [
            "modules.RevenueGroupingItem.resources.EditBlock",
        ];

        $jsScriptInstances     = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);

        return $headerScriptInstances;
    }
}