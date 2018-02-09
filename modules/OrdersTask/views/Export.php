<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class OrdersTask_Export_View extends Vtiger_Export_View
{
    public function checkPermission(Vtiger_Request $request)
    {
        $moduleName                 = $request->getModule();
        $moduleModel                = Vtiger_Module_Model::getInstance($moduleName);
        $currentUserPriviligesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        if (!$currentUserPriviligesModel->hasModuleActionPermission($moduleModel->getId(), 'Export')) {
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
        }
    }

    public function process(Vtiger_Request $request)
    {
        $viewer        = $this->getViewer($request);
        $source_module = $request->getModule();
        $viewId        = $request->get('viewname');
        $selectedIds   = $request->get('selected_ids');
        $excludedIds   = $request->get('excluded_ids');
        $qty = $request->get('qty');
        $viewer->assign('SELECTED_IDS', $selectedIds);
        $viewer->assign('EXCLUDED_IDS', $excludedIds);
        $viewer->assign('VIEWID', $viewId);
        $viewer->assign('QTY', $qty);
        $viewer->assign('SOURCE_MODULE', $source_module);
        $viewer->assign('MODULE', 'Export');
        $viewer->view('Export.tpl', $source_module);
    }
}
