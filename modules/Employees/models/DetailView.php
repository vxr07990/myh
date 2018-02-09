<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Employees_DetailView_Model extends Vtiger_DetailView_Model
{


    /**
     * Function to get the detail view links (links and widgets)
     * @param <array> $linkParams - parameters which will be used to calicaulate the params
     * @return <array> - array of link models in the format as below
     *                   array('linktype'=>list of link models);
     */
    public function getDetailViewLinks($linkParams)
    {
        if(getenv('INSTANCE_NAME') == 'graebel') {
            return parent::getDetailViewLinks($linkParams);
        } else {
            $linkModelList   = parent::getDetailViewLinks($linkParams);

            $currentUserModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
            $recordModel      = $this->getRecord();
            if ($recordModel->get('userid')) {
                $linkedUserModel = $recordModel->getLinkedUser();
                if (($currentUserModel->isParentVanLineUser() == true ||
                     $currentUserModel->isAdminUser() == true ||
                     $currentUserModel->get('id') == $linkedUserModel->get('id') ||
                     (getenv('INSTANCE_NAME') == 'uvlc' && $currentUserModel->isAgencyAdmin())) && $linkedUserModel->get('status') == 'Active'
                ) {
                    $detailViewLinks = [
                        [
                            'linktype'  => 'DETAILVIEWBASIC',
                            'linklabel' => 'Change Password',
                            'linkurl'   => "javascript:Users_Detail_Js.triggerChangePassword('index.php?module=Users&relModule=Employees&view=EditAjax&mode=changePassword&recordId={$linkedUserModel->get('id')}','Users')",
                            'linkicon'  => ''
                        ]
                    ];
                    foreach ($detailViewLinks as $detailViewLink) {
                        $linkModelList['DETAILVIEWBASIC'][] = Vtiger_Link_Model::getInstanceFromValues($detailViewLink);
                    }
                }
            }

            return $linkModelList;
        }
    }
}
