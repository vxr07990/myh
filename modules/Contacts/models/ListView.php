<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Contacts_ListView_Model extends Vtiger_ListView_Model
{

    /**
     * Function to get the list of Mass actions for the module
     * @param <Array> $linkParams
     * @return <Array> - Associative array of Link type to List of  Vtiger_Link_Model instances for Mass Actions
     */
    public function getListViewMassActions($linkParams)
    {
        $massActionLinks = parent::getListViewMassActions($linkParams);

        $currentUserModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $emailModuleModel = Vtiger_Module_Model::getInstance('Emails');

        if ($currentUserModel->hasModulePermission($emailModuleModel->getId())) {
            $massActionLink = array(
                'linktype' => 'LISTVIEWMASSACTION',
                'linklabel' => 'LBL_SEND_EMAIL',
                'linkurl' => 'javascript:Vtiger_List_Js.triggerSendEmail("index.php?module='.$this->getModule()->getName().'&view=MassActionAjax&mode=showComposeEmailForm&step=step1","Emails");',
                'linkicon' => ''
            );
            $massActionLinks['LISTVIEWMASSACTION'][] = Vtiger_Link_Model::getInstanceFromValues($massActionLink);
        }

        $SMSNotifierModuleModel = Vtiger_Module_Model::getInstance('SMSNotifier');
        if ($SMSNotifierModuleModel && $currentUserModel->hasModulePermission($SMSNotifierModuleModel->getId())) {
            $massActionLink = array(
                'linktype' => 'LISTVIEWMASSACTION',
                'linklabel' => 'LBL_SEND_SMS',
                'linkurl' => 'javascript:Vtiger_List_Js.triggerSendSms("index.php?module='.$this->getModule()->getName().'&view=MassActionAjax&mode=showSendSMSForm","SMSNotifier");',
                'linkicon' => ''
            );
            $massActionLinks['LISTVIEWMASSACTION'][] = Vtiger_Link_Model::getInstanceFromValues($massActionLink);
        }

        $moduleModel = $this->getModule();
        if ($currentUserModel->hasModuleActionPermission($moduleModel->getId(), 'EditView')) {
            $massActionLink = array(
                'linktype' => 'LISTVIEWMASSACTION',
                'linklabel' => 'LBL_TRANSFER_OWNERSHIP',
                'linkurl' => 'javascript:Vtiger_List_Js.triggerTransferOwnership("index.php?module='.$moduleModel->getName().'&view=MassActionAjax&mode=transferOwnership")',
                'linkicon' => ''
            );
            $massActionLinks['LISTVIEWMASSACTION'][] = Vtiger_Link_Model::getInstanceFromValues($massActionLink);
        }

        return $massActionLinks;
    }

    /**
     * Function to get the list of listview links for the module
     * @param <Array> $linkParams
     * @return <Array> - Associate array of Link Type to List of Vtiger_Link_Model instances
     */
    public function getListViewLinks($linkParams)
    {
        $links = parent::getListViewLinks($linkParams);

        $index=0;
        foreach ($links['LISTVIEWBASIC'] as $link) {
            if ($link->linklabel == 'Send SMS') {
                unset($links['LISTVIEWBASIC'][$index]);
            }
            $index++;
        }
        return $links;
    }

            /**
     * Static Function to get the Instance of Vtiger ListView model for a given module and custom view
     * @param <String> $value - Module Name
     * @param <Number> $viewId - Custom View Id
     * @return Vtiger_ListView_Model instance
     */
    public static function getInstanceForPopup($value, $viewId)
    {
        $db = PearDatabase::getInstance();
        $currentUser = vglobal('current_user');

        $modelClassName = Vtiger_Loader::getComponentClassName('Model', 'ListView', $value);
        $instance = new $modelClassName();
        $moduleModel = Vtiger_Module_Model::getInstance($value);

        $queryGenerator = new QueryGenerator($moduleModel->get('name'), $currentUser);
        if (!empty($viewId) && $viewId != "0") {
            $queryGenerator->initForCustomViewById($viewId);
        }else{
            $listFields = $moduleModel->getPopupViewFieldsList();

            $listFields[] = 'id';
            $queryGenerator->setFields($listFields);
        }

        $controller = new ListViewController($db, $currentUser, $queryGenerator);

        return $instance->set('module', $moduleModel)->set('query_generator', $queryGenerator)->set('listview_controller', $controller);
    }

    public static function getCvId($viewname)
    {
        //file_put_contents('logs/Transferees.log', date('Y-m-d H:i:s - ')."Entering getTransfereesCvId() function\n", FILE_APPEND);
        $db = PearDatabase::getInstance();

        $sql = "SELECT cvid FROM `vtiger_customview` WHERE entitytype  = 'Contacts' AND viewname = '$viewname'";

        $result = $db->pquery($sql, array());

        $row = $result->fetchRow();

        return $row[0];
    }
}
