<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Accounts_Detail_View extends Vtiger_Detail_View
{
    public function process(Vtiger_Request $request)
    {
        $recordId   = $request->get('record');
        $moduleName = $request->getModule();
        $viewer     = $this->getViewer($request);

        if (getenv('INSTANCE_NAME') == 'sirva') {
            /*-----------------------------Grab annual rate increases---------------------------------*/
            $db                 = PearDatabase::getInstance();
            $annualRateIncrease = [];
            $result             = $db->pquery('SELECT * FROM `vtiger_annual_rate` WHERE accountid = ?', [$recordId]);
            $row                = $result->fetchRow();
            while ($row != null) {
                $annualRateIncrease[] = $row;
                $row                  = $result->fetchRow();
            }
            $viewer->assign('ANNUAL_RATES', $annualRateIncrease);
            /*-----------------------------End annual rate increases----------------------------------*/
        } elseif (getenv('INSTANCE_NAME') == 'graebel') {
            $salesPersons = new Accounts_Salesperson_Model;
            $viewer->assign('CURRENT_SALES_PERSONS', $salesPersons->getSalesPersonDetails($recordId));
            $viewer->assign('BILLING_ADDRESSES',  Accounts_Record_Model::getAccountsBillingAddresses($recordId));
            $viewer->assign('INVOICE_SETTINGS',  Accounts_Record_Model::getCurrentInvoiceSettings($recordId));
            $viewer->assign('ADDITIONAL_ROLES',  Accounts_Record_Model::getAdditionalRoleValues($recordId));
        } elseif (getenv('IGC_MOVEHQ')) {
            $salesPersons = new Accounts_Salesperson_Model;
            $viewer->assign('CURRENT_SALES_PERSONS', $salesPersons->getSalesPersonDetails($recordId));
            $viewer->assign('BILLING_ADDRESSES',  Accounts_Record_Model::getAccountsBillingAddresses($recordId));
            $viewer->assign('INVOICE_SETTINGS',  Accounts_Record_Model::getCurrentInvoiceSettings($recordId));
            $viewer->assign('ADDITIONAL_ROLES',  Accounts_Record_Model::getAdditionalRoleValues($recordId));
            if(!empty($recordId)){
                $recordModel = Vtiger_Record_Model::getInstanceById($recordId);
                $businessLine = $recordModel->get('business_line');
                if(empty($businessLine)){
                    $viewer->assign('BUSINESS_LINES',  Accounts_Salesperson_Model::getBusinessLines());
                }else{
                    $viewer->assign('BUSINESS_LINES', explode(' |##| ', $businessLine));
                }
            }
        }
        /*-----------------------------End annual rate increases----------------------------------*/
        //It seems this will only work with graebel, since the script that creates all of this is only in init-graebel.
       /* if(getenv('INSTANCE_NAME') == 'graebel'){
            $salesPersons = new Accounts_Salesperson_Model;

            $viewer->assign('CURRENT_SALES_PERSONS', $salesPersons->getSalesPersonDetails($recordId));

            $viewer->assign('BILLING_ADDRESSES',  Accounts_Record_Model::getAccountsBillingAddresses($recordId));

            $viewer->assign('INVOICE_SETTINGS',  Accounts_Record_Model::getCurrentInvoiceSettings($recordId));

            $viewer->assign('ADDITIONAL_ROLES',  Accounts_Record_Model::getAdditionalRoleValues($recordId));
        }*/
        parent::process($request);
    }

    /**
     * Function to get activities
     *
     * @param Vtiger_Request $request
     *
     * @return <List of activity models>
     */
    public function getActivities(Vtiger_Request $request)
    {
        $moduleName                 = 'Calendar';
        $moduleModel                = Vtiger_Module_Model::getInstance($moduleName);
        $currentUserPriviligesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        if ($currentUserPriviligesModel->hasModulePermission($moduleModel->getId())) {
            $moduleName = $request->getModule();
            $recordId   = $request->get('record');
            $pageNumber = $request->get('page');
            if (empty($pageNumber)) {
                $pageNumber = 1;
            }
            $pagingModel = new Vtiger_Paging_Model();
            $pagingModel->set('page', $pageNumber);
            $pagingModel->set('limit', 10);
            if (!$this->record) {
                $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
            }
            $recordModel       = $this->record->getRecord();
            $moduleModel       = $recordModel->getModule();
            $relatedActivities = $moduleModel->getCalendarActivities('', $pagingModel, 'all', $recordId);
            $viewer            = $this->getViewer($request);
            $viewer->assign('RECORD', $recordModel);
            $viewer->assign('MODULE_NAME', $moduleName);
            $viewer->assign('PAGING_MODEL', $pagingModel);
            $viewer->assign('PAGE_NUMBER', $pageNumber);
            $viewer->assign('ACTIVITIES', $relatedActivities);

            return $viewer->view('RelatedActivities.tpl', $moduleName, true);
        }
    }
}
