<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class RevenueGrouping_Delete_Action extends Vtiger_Action_Controller
{
    public function checkPermission(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $record = $request->get('record');

        $currentUserPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        if (!$currentUserPrivilegesModel->isPermitted($moduleName, 'Delete', $record)) {
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
        }
    }

    public function process(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $recordId = $request->get('record');
        $ajaxDelete = $request->get('ajaxDelete');

        global $adb;

        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
        $agentid = $recordModel->get('agentid');

        //delete RevenueGroupingItems related RevenueGrouping
        $deleteSql = "UPDATE `vtiger_crmentity` SET `vtiger_crmentity`.`deleted`=? WHERE `vtiger_crmentity`.`agentid`=? AND `vtiger_crmentity`.`setype` = ?";
        $adb->pquery($deleteSql, array(1,$agentid , 'RevenueGroupingItem'));

        $moduleModel = $recordModel->getModule();

        $recordModel->delete();

        $listViewUrl = $moduleModel->getListViewUrl();
        if ($ajaxDelete) {
            $response = new Vtiger_Response();
            $response->setResult($listViewUrl);
            return $response;
        } else {
            header("Location: $listViewUrl");
        }
    }

    public function validateRequest(Vtiger_Request $request)
    {
        $request->validateWriteAccess();
    }
}
