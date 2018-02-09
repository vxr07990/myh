<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
class Accounts_Save_Action extends Vtiger_Save_Action
{
    public function process(Vtiger_Request $request)
    {

        if ($request->get('record')) {
//            implode(' ## ', $name)
            $moduleName = $request->getModule();
            $originalRecordModel = Vtiger_Record_Model::getInstanceById($request->get('record'), $moduleName);
        }
        parent::process($request);
        //These shouldn't exist for accounts, but just in case.
        $pseudo     = $request->get('pseudoSave') == '1';
        $reportSave = $request->get('reportSave') == '1';
        if (!$pseudo && !$reportSave) {
            //trigger api update.
            //if ($originalRecordModel && $this->apiFieldsChanged($originalRecordModel, Vtiger_Record_Model::getInstanceById($request->get('record'), $moduleName))) {
            if ($originalRecordModel) {
                //trigger API UPDATE!
                try {
                    $customerAPIResponse = MoveCrm\GraebelAPI\customerHandler::triggerCustomerAPI('update', ['recordNumber' => $originalRecordModel->getId()]);
                } catch (Exception $ex) {
                    //@TODO: see if this returns an error or just a false response... can not remember.
                    //file_put_contents('logs/devLog.log', "\n CUST API FAIL (Save.php:".__LINE__.") ex->getCode() : ".print_r($ex->getCode(), true), FILE_APPEND);
                    //file_put_contents('logs/devLog.log', "\n CUST API FAIL (Save.php:".__LINE__.") ex->getMessage() : ".print_r($ex->getMessage(), true), FILE_APPEND);
                }
            } elseif (!$originalRecordModel) {
                $fieldList = $_REQUEST;
                if (is_array($this->column_fields)) {
                    $fieldList = array_merge($_REQUEST, $this->column_fields);
                }
                if (empty($fieldList['record'])) {
                    $newRecord = true;
                    if (!empty($fieldList['currentid'])) {
                        $fieldList['record'] = $fieldList['currentid'];
                    } else {
                        //this is OHHH noes... because we don't have the reocrd?
                        //fallback!
                        $fieldList['record'] = $this->id;
                    }
                }
                try {
                    $customerAPIResponse = MoveCrm\GraebelAPI\customerHandler::triggerCustomerAPI('create', ['recordNumber' => $fieldList['record']]);
                } catch (Exception $ex) {
                    //@TODO: see if this returns an error or just a false response... can not remember.
                    //file_put_contents('logs/devLog.log', "\n CUST API FAIL (Save.php:".__LINE__.") ex->getCode() : ".print_r($ex->getCode(), true), FILE_APPEND);
                    //file_put_contents('logs/devLog.log', "\n CUST API FAIL (Save.php:".__LINE__.") ex->getMessage() : ".print_r($ex->getMessage(), true), FILE_APPEND);
                }
            }
        }
    }

    private function apiFieldsChanged($originalRecordModel, $postRecordModel)
    {
        if (!$originalRecordModel) {
            return false;
        }

        if (!$postRecordModel) {
            return false;
        }

        //@TODO: this is to ALWAYS fire update requests... eventually we'll only want to fire if something changes and then only that data.
        return true;

        $fieldsToCheck = [
            'credit_check_pass',
            'credit_limit',
            'credit_hold',
            'credit_check_date',
            'account_balance',
            'customer_number',
            'accounttype',
            'leadsource',
            'accountname',
            'address1',
            'address2',
            'city',
            'state',
            'zip',
            'email1',
            'phone',
        ];
        //phone call explained we don't send customer update for COD and these are from the Contacts record.
        //First Name: Identifies the First Name (first name) entered in Move HQ. This is filled in for COD Customers
        //Last Name: Identifies the Last Name (last name) entered in Move HQ. This is filled in for COD Customers

        //this isn't a field in the API
        //Fax: This is the primary fax number for the customer entered in move HQ

        //This can't exactly be triggered because there's no "default" flag.
        //Customer Invoice Format: This identifies the Invoice template that the Customer wants to get their invoice in. Valid values are:
        //Customer Invoice Document Format: This identifies if the Customer wants the invoice as a PDF/word/Excel/HTML.
        //​Customer Package Format: This identifies the supporting documentation that is required to be submitted with the invoice.​ Valid values are:
        //Customer Billing Delivery Preference: This identifies how the Customer wants to receive their bill. ​
        foreach ($fieldsToCheck as $field) {
            if ($originalRecordModel->get($field) != $postRecordModel->get($field)) {
                return true;
                break; //unneeded, but is useful to remember this is breaking out.
            }
        }
        return false;
    }
}
