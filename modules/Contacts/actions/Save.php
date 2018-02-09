<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Contacts_Save_Action extends Vtiger_Save_Action
{
    public function process(Vtiger_Request $request)
    {
        $result = Vtiger_Util_Helper::transformUploadedFiles($_FILES, true);
        $_FILES = $result['imagename'];

        //To stop saveing the value of salutation as '--None--'
        $salutationType = $request->get('salutationtype');
        if ($salutationType === '--None--') {
            $request->set('salutationtype', '');
        }

        //OT 2449 - Bidirectional Sync Trigger
        $recordId = $request->get('record');
        $modName = $request->getModule();
        //Check for changes in demographic fields
        $db = PearDatabase::getInstance();
        if (isset($recordId) && !empty($recordId)) {
            $sql = "SELECT firstname, lastname, email, phone, mobile, homephone, otherphone
					FROM `vtiger_contactdetails`
					JOIN `vtiger_contactsubdetails`
					ON `vtiger_contactdetails`.contactid = `vtiger_contactsubdetails`.contactsubscriptionid
					WHERE contactid=?";
            $result = $db->pquery($sql, [$recordId]);
            $row = $result->fetchRow();
            if ($row['firstname']        != $request->get('firstname') ||
               $row['lastname']            != $request->get('lastname') ||
               $row['email']            != $request->get('email') ||
               $row['mobile']            != $request->get('mobile') ||
               $row['homephone']        != $request->get('homephone') ||
               $row['otherphone']        != $request->get('otherphone')) {
                //Set flag to fire bidirectional sync after record is saved through parent::process
                $fireBidirectionalUpdate = true;
            }
            if ($row['firstname']        != $request->get('firstname') ||
               $row['lastname']            != $request->get('lastname')) {
                //First Name: Identifies the First Name (first name) entered in Move HQ. This is filled in for COD Customers
                //Last Name: Identifies the Last Name (last name) entered in Move HQ. This is filled in for COD Customers

                //this isn't a field in the API
                //Fax: This is the primary fax number for the customer entered in move HQ
                $sendAPI = true;
            }
        }

        parent::process($request);

        /*
         * currently under the impression that Update is not fired on contacts, just accounts.
         * also check: modules/Accounts/actions/Save.php for a method to only trigger when particular fields are updated.
        //seems fine to use the sync checker to check this.
        if ($sendAPI) {
            try {
                $customerAPIResponse = MoveCrm\GraebelAPI\customerHandler::triggerCustomerAPI('update', ['recordNumber' => $this->id]);
            } catch (Exception $ex) {
            }
        }
        */

        if ($fireBidirectionalUpdate) {
            $wsdl = getenv('SURVEY_SYNC_URL');
            if ($wsdl) {
                $params              = [];
                $params['username']  = $this->getUsername($request->get('assigned_user_id'));
                $params['accessKey'] = $this->getAccessKey($request->get('assigned_user_id'));
                $params['recordID']  = $this->getObjTypeId($modName)."x".$recordId;
                $params['address']   = getenv('SITE_URL');
                try {
                    $soapClient = new soapclient2($wsdl, 'wsdl');
                    $soapClient->setDefaultRpcParams(true);
                    $soapProxy = $soapClient->getProxy();
                    if (method_exists($soapProxy, 'BidirectionalUpdateNotification')) {
                        $soapResult = $soapProxy->BidirectionalUpdateNotification($params);
                        file_put_contents('logs/devLog.log', "\n".date('Y-m-d H:i:s - ')." Contact Bidirectional Sync SoapResult : ".print_r($soapResult, true), FILE_APPEND);
                    }
                } catch (Exception $ex) {
                    //just don't fatal and throw up....
                    file_put_contents('logs/devLog.log', "\n".date('Y-m-d H:i:s - ')." Contact Bidirectional Sync FAIL : ".print_r($ex, true), FILE_APPEND);
                }
            }
        }
    }

    protected function getAccessKey($userId)
    {
        $db = PearDatabase::getInstance();

        $sql = "SELECT accesskey FROM `vtiger_users` WHERE id=?";
        $params[] = $userId;

        $result = $db->pquery($sql, $params);

        return $db->query_result($result, 0, 'accesskey');
    }

    protected function getObjTypeId($modName)
    {
        $db = PearDatabase::getInstance();

        $sql = "SELECT id FROM `vtiger_ws_entity` WHERE name=?";
        $params[] = $modName;

        $result = $db->pquery($sql, $params);

        return $db->query_result($result, 0, 'id');
    }

    protected function getUsername($userId)
    {
        $db = PearDatabase::getInstance();

        $sql = "SELECT user_name FROM `vtiger_users` WHERE id=?";
        $params[] = $userId;

        $result = $db->pquery($sql, $params);

        return $db->query_result($result, 0, 'user_name');
    }
}
