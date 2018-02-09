<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Accounts_Edit_View extends Vtiger_Edit_View
{
    public function process(Vtiger_Request $request)
    {
        $viewer     = $this->getViewer($request);
        $db         = PearDatabase::getInstance();
        $moduleName = $request->getModule();
        $record     = $request->get('record');

        if (!empty($record) && $request->get('isDuplicate') == true) {
            $recordModel = $this->record?$this->record:Vtiger_Record_Model::getInstanceById($record, $moduleName);


            $viewer->assign('MODE', '');
            //While Duplicating record, If the related record is deleted then we are removing related record info in record model
            $mandatoryFieldModels = $recordModel->getModule()->getMandatoryFieldModels();
            foreach ($mandatoryFieldModels as $fieldModel) {
                if ($fieldModel->isReferenceField()) {
                    $fieldName = $fieldModel->get('name');
                    if (Vtiger_Util_Helper::checkRecordExistance($recordModel->get($fieldName))) {
                        $recordModel->set($fieldName, '');
                    }
                }
            }
        } elseif (!empty($record)) {
            $recordModel = $this->record?$this->record:Vtiger_Record_Model::getInstanceById($record, $moduleName);
            $viewer->assign('RECORD_ID', $record);
            $viewer->assign('MODE', 'edit');
        } else {
            $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
            $viewer->assign('MODE', '');
        }
        if (!$this->record) {
            $this->record = $recordModel;
        }

        if (getenv('INSTANCE_NAME') == 'sirva') {
            /*-----------------------------Grab annual rate increases---------------------------------*/
            $annualRateIncrease = [];
            $result             = $db->pquery('SELECT * FROM `vtiger_annual_rate` WHERE accountid = ?', [$record]);
            $row                = $result->fetchRow();
            while ($row != null) {
                $annualRateIncrease[] = $row;
                //file_put_contents('logs/devLog.log', "\n ROW: ".print_r($row, true), FILE_APPEND);
                $row = $result->fetchRow();
            }
            $viewer->assign('ANNUAL_RATES', $annualRateIncrease);
            /*-----------------------------End annual rate increases----------------------------------*/
        } elseif (getenv('INSTANCE_NAME') == 'graebel') {
            /*-----------------------------Grab salesperson data -------------------------------------*/
            $currentUser = Users_Record_Model::getCurrentUserModel();

            $salesPersons = new Accounts_Salesperson_Model;

            $businessLines = $salesPersons->getBusinessLines();
            if ($record) {
                $currentSalesPerson = $salesPersons->getSalesPersonData($record);
                $viewer->assign('BILLING_ADDRESSES',  $recordModel::getAccountsBillingAddresses($record));
                $viewer->assign('CURRENT_INVOICE_SETTINGS',  $recordModel::getCurrentInvoiceSettings($record));
                $viewer->assign('ACCOUNT_ROLES_VALUES', $recordModel::getAdditionalRoleValues($record));
            }

            $viewer->assign('CURRENT_SALES_PERSONS', $currentSalesPerson);
            $viewer->assign('BUSINESS_LINES', $businessLines);
            $viewer->assign('BOOKING_OFFICES', $currentUser->getAccessibleVanlinesForUser());
            //OT 1976 - Was getting agents instead of users. Changed to getAccessibleUsers() from getAccessibleAgentsForUser() below
            $viewer->assign('SALES_PERSONS', $currentUser->getAccessibleSalesPeople());

            $viewer->assign('COMMODITIES', $recordModel->getCommodities());

            $viewer->assign('INVOICE_SETTINGS_OPTIONS',  $recordModel->getInvoiceOptions());

            $viewer->assign('ACCOUNT_ROLE_OPTIONS', $recordModel->getAccountsRole());
        } elseif (getenv('IGC_MOVEHQ')) {

            /*-----------------------------Grab salesperson data -------------------------------------*/
            $currentUser = Users_Record_Model::getCurrentUserModel();

            $salesPersons = new Accounts_Salesperson_Model;

            $businessLines = $salesPersons->getBusinessLines();

            if ($record) {
                $currentSalesPerson = $salesPersons->getSalesPersonData($record);
                $viewer->assign('BILLING_ADDRESSES',  $recordModel::getAccountsBillingAddresses($record));
                $viewer->assign('CURRENT_INVOICE_SETTINGS',  $recordModel::getCurrentInvoiceSettings($record));
                $viewer->assign('ACCOUNT_ROLES_VALUES', $recordModel::getAdditionalRoleValues($record));
                $viewer->assign('COUNT_ACCOUNT_ROLES_VALUES', count($recordModel::getAdditionalRoleValues($record)));
            }

            $viewer->assign('CURRENT_SALES_PERSONS', $currentSalesPerson);
            $viewer->assign('BUSINESS_LINES', $businessLines);
            $viewer->assign('BOOKING_OFFICES', $currentUser->getAccessibleVanlinesForUser());
            //OT 1976 - Was getting agents instead of users. Changed to getAccessibleUsers() from getAccessibleAgentsForUser() below
            $viewer->assign('SALES_PERSONS', $currentUser->getAccessibleSalesPeople());

            $viewer->assign('COMMODITIES', $recordModel->getCommodities());

            $viewer->assign('INVOICE_SETTINGS_OPTIONS',  $recordModel->getInvoiceOptions());
        }
        /*-----------------------------End salesperson data ----------------------------------*/


        parent::process($request);
    }

    public function getHeaderScripts(Vtiger_Request $request)
    {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName            = $request->getModule();
        
        $jsFileNames           = [
            "modules.Accounts.resources.AnnualRateIncrease",
        ];
        
        if (getenv('INSTANCE_NAME') == 'graebel') {
            $jsFileNames[] = "modules.Accounts.resources.SalesPerson";
        } elseif (getenv('IGC_MOVEHQ')) {
            $jsFileNames[] = "modules.Accounts.resources.SalesPerson";
        }
        
        $jsScriptInstances     = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);

        return $headerScriptInstances;
    }
}
