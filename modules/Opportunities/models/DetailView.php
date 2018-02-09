<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Opportunities_DetailView_Model extends Vtiger_DetailView_Model
{
    /**
     * Function to get the detail view links (links and widgets)
     * @param <array> $linkParams - parameters which will be used to calicaulate the params
     * @return <array> - array of link models in the format as below
     *                   array('linktype'=>list of link models);
     */
    public function getDetailViewLinks($linkParams)
    {
        $currentUserModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $linkModelList = parent::getDetailViewLinks($linkParams);
        $emailModuleModel = Vtiger_Module_Model::getInstance('Emails');
        $recordModel = $this->getRecord();
        $invoiceModuleModel = Vtiger_Module_Model::getInstance('Invoice');
        $quoteModuleModel = Vtiger_Module_Model::getInstance('Quotes');
        $surveyModuleModel = Vtiger_Module_Model::getInstance('Surveys');
        $projectModuleModel = Vtiger_Module_Model::getInstance('Orders');
        $participantInfo = getParticipantInfoForRecord($recordModel->getId());

        if ($this->allowRegistrationButton($recordModel)) {
            $moduleModel = $this->getModule();
            if ($moduleModel) {
                $basicActionLink                    = [
                    'linktype'  => 'DETAILVIEWBASIC',
                    'linklabel' => 'LBL_REGISTER_OPPORTUNITY',
                    'linkurl'   => "javascript:Vtiger_Detail_Js.triggerSendRegistration('".$moduleModel->getActionUrl('SendRegistration')."','".$this->getModuleName()."');",
                    'linkicon'  => ''
                ];
                $linkModelList['DETAILVIEWBASIC'][] = Vtiger_Link_Model::getInstanceFromValues($basicActionLink);
            }
        }

        if ($currentUserModel->hasModulePermission($emailModuleModel->getId())) {
            $object_param = ZEND_JSON::encode(['documentIds'=>array($recordModel->getId())]);
            $basicActionLink = array(
                'linktype' => 'DETAILVIEWBASIC',
                'linklabel' => 'LBL_SEND_EMAIL',
                'linkurl' => "javascript:Vtiger_Detail_Js.triggerSendEmail('".$object_param."','Emails');",
                'linkicon' => ''
            );
            $linkModelList['DETAILVIEWBASIC'][] = Vtiger_Link_Model::getInstanceFromValues($basicActionLink);
        }

        if(getenv('IGC_MOVEHQ')){
            if ($currentUserModel->hasModuleActionPermission($projectModuleModel->getId(), 'EditView') && isPermitted($recordModel->getModule()->getName(), 'EditView', $recordModel->getId()) == 'yes') {
                $basicActionLink = array(
                    'linktype' => 'DETAILVIEWBASIC',
                    'linklabel' => 'LBL_BOOK_ORDER',
                    'linkurl' => $recordModel->getCreateProjectUrl(),
                    'linkicon' => ''
                );
                $linkModelList['DETAILVIEWBASIC'][] = Vtiger_Link_Model::getInstanceFromValues($basicActionLink);
            }
        }

        if ($currentUserModel->hasModuleActionPermission($invoiceModuleModel->getId(), 'EditView') && isPermitted($recordModel->getModule()->getName(), 'EditView', $recordModel->getId()) == 'yes') {
            $basicActionLink = array(
                'linktype' => 'DETAILVIEW',
                'linklabel' => vtranslate('LBL_CREATE').' '.vtranslate($invoiceModuleModel->getSingularLabelKey(), 'Invoice'),
                'linkurl' => $recordModel->getCreateInvoiceUrl(),
                'linkicon' => ''
            );
            $linkModelList['DETAILVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($basicActionLink);
        }

        if (
            $currentUserModel->hasModuleActionPermission($quoteModuleModel->getId(), 'EditView') &&
            isPermitted($recordModel->getModule()->getName(), 'EditView', $recordModel->getId()) == 'yes' &&
            ((in_array('full', $participantInfo['view_levels']) || in_array('read_only', $participantInfo['view_levels'])) || !isParticipantForRecord($recordModel->getId()))
        ) {
            $basicActionLink = array(
                'linktype' => 'DETAILVIEW',
                'linklabel' => vtranslate('LBL_CREATE').' '.vtranslate($quoteModuleModel->getSingularLabelKey(), 'Quotes'),
                'linkurl' => $recordModel->getCreateQuoteUrl(),
                'linkicon' => ''
            );
            $linkModelList['DETAILVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($basicActionLink);
        }

        if ($currentUserModel->hasModuleActionPermission($surveyModuleModel->getId(), 'EditView') && isPermitted($recordModel->getModule()->getName(), 'EditView', $recordModel->getId()) == 'yes') {
            $basicActionLink = array(
                'linktype' => 'DETAILVIEW',
                'linklabel' => vtranslate('LBL_CREATE').' '.vtranslate($surveyModuleModel->getSingularLabelKey(), 'Surveys'),
                'linkurl' => $recordModel->getCreateSurveyUrl(),
                'linkicon' => ''
            );
            $linkModelList['DETAILVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($basicActionLink);
        }

        if (getenv('INSTANCE_NAME') == 'sirva' &&
            $currentUserModel->hasModuleActionPermission($projectModuleModel->getId(), 'EditView') &&
            isPermitted($recordModel->getModule()->getName(), 'EditView', $recordModel->getId()) == 'yes') {
            $basicActionLink = array(
                'linktype' => 'DETAILVIEW',
                'linklabel' => vtranslate('LBL_CREATE').' '.vtranslate($projectModuleModel->getSingularLabelKey(), 'Orders'),
                'linkurl' => $recordModel->getCreateProjectUrl(),
                'linkicon' => ''
            );
            $linkModelList['DETAILVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($basicActionLink);
        }

        $CalendarActionLinks[] = array();
        $CalendarModuleModel = Vtiger_Module_Model::getInstance('Calendar');
        if ($currentUserModel->hasModuleActionPermission($CalendarModuleModel->getId(), 'EditView') && isPermitted($recordModel->getModule()->getName(), 'EditView', $recordModel->getId()) == 'yes') {
            $CalendarActionLinks[] = array(
                    'linktype' => 'DETAILVIEW',
                    'linklabel' => 'LBL_ADD_EVENT',
                    'linkurl' => $recordModel->getCreateEventUrl(),
                    'linkicon' => ''
            );

            $CalendarActionLinks[] = array(
                    'linktype' => 'DETAILVIEW',
                    'linklabel' => 'LBL_ADD_TASK',
                    'linkurl' => $recordModel->getCreateTaskUrl(),
                    'linkicon' => ''
            );
        }

        foreach ($CalendarActionLinks as $basicLink) {
            if ($basicLink) {
                $linkModelList['DETAILVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($basicLink);
            }
        }

        $userModel = Users_Record_Model::getCurrentUserModel();
        $extraPermission = false;//this is off for now.//$userModel::getExtraPermission($recordModel->getId());
        //file_put_contents('logs/devLog.log', "\n XTRA PERMS: ".$extraPermission, FILE_APPEND);
        //extra logic piled on to lock out no-rates participants from the STS and 1 click quote buttons
        if (
            getenv('INSTANCE_NAME') == 'sirva' &&
            isPermitted($recordModel->getModule()->getName(), 'EditView', $recordModel->getId()) == 'yes' &&
            ((in_array('full', $participantInfo['view_levels']) || in_array('read_only', $participantInfo['view_levels'])) || !isParticipantForRecord($recordModel->getId()))
        ) {
            if ($recordModel->get('move_type') == 'International') {
                $linkModelList['DETAILVIEWBASIC'][] = Vtiger_Link_Model::getInstanceFromValues($basicActionLink);
                $basicActionLink                    = [
                    'linktype'  => 'DETAILVIEWBASIC',
                    'linklabel' => 'LBL_INTL_ONE_CLICK',
                    'linkurl'   => 'Javascript:Opportunities_Detail_Js.intlQuote("'.$recordModel->getIntlQuoteUrl().'",this);',
                    'linkicon'  => ''
                ];
            }
            $linkModelList['DETAILVIEWBASIC'][] = Vtiger_Link_Model::getInstanceFromValues($basicActionLink);
            $stsLinkUrl = $recordModel->allowedSTS() ? 'Javascript:Opportunities_Detail_Js.registerSTS("'.$recordModel->getSTSRegistrationUrl().'",this);' : '';
            //file_put_contents('logs/devLog.log', "\n STS SUCCESS: " . $recordModel->get('stsSuccess'), FILE_APPEND);
            //file_put_contents('logs/devLog.log', "\n RecordModel : ".print_r($recordModel, true), FILE_APPEND);
            $basicActionLink = array(
                'linktype' => 'DETAILVIEWBASIC',
                'linklabel' => 'LBL_STS_REGISTRATION',
                'linkurl' => $stsLinkUrl,
                'linkicon' => '',
                'status' => $recordModel->allowedSTS() ? false : true,
            );
            $linkModelList['DETAILVIEWBASIC'][] = Vtiger_Link_Model::getInstanceFromValues($basicActionLink);
        }
        return $linkModelList;
    }

    /**
     * Function to get the detail view widgets
     * @return <Array> - List of widgets , where each widget is an Vtiger_Link_Model
     */
    public function getWidgets()
    {
        $userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $widgetLinks = parent::getWidgets();
        $widgets = array();

        $documentsInstance = Vtiger_Module_Model::getInstance('Documents');
        if ($userPrivilegesModel->hasModuleActionPermission($documentsInstance->getId(), 'DetailView')) {
            $createPermission = $userPrivilegesModel->hasModuleActionPermission($documentsInstance->getId(), 'EditView');
            $widgets[] = array(
                    'linktype' => 'DETAILVIEWWIDGET',
                    'linklabel' => 'Documents',
                    'linkName'    => $documentsInstance->getName(),
                    'linkurl' => 'module='.$this->getModuleName().'&view=Detail&record='.$this->getRecord()->getId().
                            '&relatedModule=Documents&mode=showRelatedRecords&page=1&limit=5',
                    'action'    =>    ($createPermission == true) ? array('Add') : array(),
                    'actionURL' =>    $documentsInstance->getQuickCreateUrl()
            );
        }

        $contactsInstance = Vtiger_Module_Model::getInstance('Contacts');
        if ($userPrivilegesModel->hasModuleActionPermission($contactsInstance->getId(), 'DetailView')) {
            $createPermission = $userPrivilegesModel->hasModuleActionPermission($contactsInstance->getId(), 'EditView');
            $widgets[] = array(
                    'linktype' => 'DETAILVIEWWIDGET',
                    'linklabel' => 'LBL_RELATED_CONTACTS',
                    'linkName'    => $contactsInstance->getName(),
                    'linkurl' => 'module='.$this->getModuleName().'&view=Detail&record='.$this->getRecord()->getId().
                            '&relatedModule=Contacts&mode=showRelatedRecords&page=1&limit=5',
                    'action'    =>    ($createPermission == true) ? array('Add') : array(),
                    'actionURL' =>    $contactsInstance->getQuickCreateUrl()
            );
        }
        if (getenv('IGC_MOVEHQ') || getenv('INSTANCE_NAME') == 'national') {
            $estimatesInstance = Vtiger_Module_Model::getInstance('Estimates');
            if ($userPrivilegesModel->hasModuleActionPermission($estimatesInstance->getId(), 'DetailView')) {
                $createPermission = $userPrivilegesModel->hasModuleActionPermission($estimatesInstance->getId(), 'EditView');
                $widgets[] = array(
                    'linktype' => 'DETAILVIEWWIDGET',
                    'linklabel' => 'LBL_RELATED_ESTIMATES',
                    'linkName' => $estimatesInstance->getName(),
                    'linkurl' => 'module=' . $this->getModuleName() . '&view=Detail&record=' . $this->getRecord()->getId() .
                        '&relatedModule=Estimates&mode=showRelatedRecords&page=1&limit=5',
					'action' => ($createPermission == true && getenv('IGC_MOVEHQ')) ? array('Add') : array(),
//					'actionURL' => $contactsInstance->getQuickCreateUrl()
                );
            }
        }
        if(getenv('IGC_MOVEHQ')) {
            $moveRolesInstance = Vtiger_Module_Model::getInstance('MoveRoles');
            if ($userPrivilegesModel->hasModuleActionPermission($moveRolesInstance->getId(), 'DetailView')) {
                $widgets[]        = [
                    'linktype'  => 'DETAILVIEWWIDGET',
                    'linklabel' => 'Move Roles',
                ];
            }
        }
        /*
                $productsInstance = Vtiger_Module_Model::getInstance('Products');
                if($userPrivilegesModel->hasModuleActionPermission($productsInstance->getId(), 'DetailView')) {
                    $createPermission = $userPrivilegesModel->hasModuleActionPermission($productsInstance->getId(), 'EditView');
                    $widgets[] = array(
                            'linktype' => 'DETAILVIEWWIDGET',
                            'linklabel' => 'LBL_RELATED_PRODUCTS',
                            'linkName'	=> $productsInstance->getName(),
                            'linkurl' => 'module='.$this->getModuleName().'&view=Detail&record='.$this->getRecord()->getId().
                                    '&relatedModule=Products&mode=showRelatedRecords&page=1&limit=5',
                            'action'	=>	($createPermission == true) ? array('Add') : array(),
                            'actionURL' =>	$productsInstance->getQuickCreateUrl()
                    );
                }
        */
        foreach ($widgets as $widgetDetails) {
            $widgetLinks[] = Vtiger_Link_Model::getInstanceFromValues($widgetDetails);
        }

        return $widgetLinks;
    }

    //@TODO: this needs rethought.
    private function allowRegistrationButton($recordModel) {
        if (getenv('INSTANCE_NAME') != 'arpin') {
            return false;
        }

        if (!$recordModel) {
            return false;
        }

        $registration_number = $recordModel->get('registration_number');
        if ($registration_number) {
            return false;
        }

        $business_line = $recordModel->get('business_line');
        $billing_type = $recordModel->get('billing_type');

        if (
            preg_match('/interstate/i',$business_line) &&
            preg_match('/cod/i',$billing_type)
        ) {
            return true;
        }

        return false;
    }
}
