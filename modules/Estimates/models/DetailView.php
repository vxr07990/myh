<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Estimates_DetailView_Model extends Quotes_DetailView_Model
{
    public function getDetailViewLinks($linkParams)
    {
        $currentUserModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $linkModelList = parent::getDetailViewLinks($linkParams);
        $recordModel = $this->getRecord();
        $actualsModuleModel = Vtiger_Module_Model::getInstance('Actuals');
        if ($recordModel->get('orders_id')) {
            $ordersRecordModel = Vtiger_Record_Model::getInstanceById($recordModel->get('orders_id'), 'Orders');
            $isLocked = $ordersRecordModel->isLocked();
        }
        if ($actualsModuleModel) {
            if ($actualsModuleModel && $currentUserModel->hasModuleActionPermission($actualsModuleModel->getId(), 'EditView') && isPermitted($recordModel->getModule()->getName(), 'EditView', $recordModel->getId()) == 'yes' && !$isLocked) {
                $basicActionLink = array(
                'linktype' => 'DETAILVIEWBASIC',
                'linklabel' => vtranslate('LBL_CREATE').' '.vtranslate($actualsModuleModel->getSingularLabelKey(), 'Actuals'),
                'linkurl' => 'Estimates_Detail_Js.I().convertToActual()',
                'linkicon' => ''
            );
                $linkModelList['DETAILVIEWBASIC'][] = Vtiger_Link_Model::getInstanceFromValues($basicActionLink);
            }
        }
        return $linkModelList;
    }
}
