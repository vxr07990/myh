<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class CommissionPlansFilter_Edit_View extends Vtiger_Edit_View
{
    protected $record = false;

    public function __construct()
    {
        parent::__construct();
    }

    public function process(Vtiger_Request $request)
    {
        $viewer= $this->getViewer($request);
        $singles = ['commissionplansfilter_status'];
        $viewer->assign('SINGLE_FIELDS',$singles);
        $record     = $request->get('record');
        //logic to include Commission Plans Item
        $commissionPlansItem = Vtiger_Module_Model::getInstance('CommissionPlansItem');
        if ($commissionPlansItem && $commissionPlansItem->isActive()) {
            $viewer->assign('IS_ACTIVE_COMMISSIONPLANSITEM', true);
            $fields = $commissionPlansItem->getFields('LBL_COMMISSIONPLANITEMSDETAIL');
            //set vars and remove rel_crmid for block view
            foreach ($fields as $key => $field) {
                $fieldName = $field->get('name');
                if ($fieldName == 'commissionplansfilterid') {
                    unset($fields[$key]);
                }
            }
            if ($record) {
                $viewer->assign('COMMISSIONPLANITEMS_LIST', $commissionPlansItem->getCommissionPlansItem($record));
            }
            //file_put_contents('logs/devLog.log', "\n StopsFields : ".print_r($stopsFields, true), FILE_APPEND);
            $viewer->assign('COMMISSIONPLANITEMS_BLOCK_FIELDS', $fields);
            $viewer->assign('MODULE_QUICKCREATE_RESTRICTIONS', ['RevenueGroupingItem','ItemCodes']);
        }
        parent::process($request);
    }
    public function getHeaderScripts(Vtiger_Request $request)
    {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $jsFileNames = [
            "modules.CommissionPlansItem.resources.EditBlock",
        ];

        $jsScriptInstances     = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);

        return $headerScriptInstances;
    }
}
