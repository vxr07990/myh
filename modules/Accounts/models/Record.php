<?php
include_once "include/Webservices/Revise.php";
/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Accounts_Record_Model extends Vtiger_Record_Model {

    /**
     * Function returns the details of Accounts Hierarchy
     * @return <Array>
     */
    public function getAccountHierarchy() {
        $focus = CRMEntity::getInstance($this->getModuleName());
        $hierarchy = $focus->getAccountHierarchy($this->getId());
        $i = 0;
        foreach ($hierarchy['entries'] as $accountId => $accountInfo) {
            preg_match('/<a href="+/', $accountInfo[0], $matches);
            if ($matches != null) {
                preg_match('/[.\s]+/', $accountInfo[0], $dashes);
                preg_match("/<a(.*)>(.*)<\/a>/i", $accountInfo[0], $name);

                $recordModel = Vtiger_Record_Model::getCleanInstance('Accounts');
                $recordModel->setId($accountId);
                $hierarchy['entries'][$accountId][0] = $dashes[0] . "<a href=" . $recordModel->getDetailViewUrl() . ">" . $name[2] . "</a>";
            }
        }
        return $hierarchy;
    }

    /**
     * Function returns the url for create event
     * @return <String>
     */
    public function getCreateEventUrl() {
        $calendarModuleModel = Vtiger_Module_Model::getInstance('Calendar');
        return $calendarModuleModel->getCreateEventRecordUrl() . '&parent_id=' . $this->getId();
    }

    /**
     * Function returns the url for create todo
     * @retun <String>
     */
    public function getCreateTaskUrl() {
        $calendarModuleModel = Vtiger_Module_Model::getInstance('Calendar');
        return $calendarModuleModel->getCreateTaskRecordUrl() . '&parent_id=' . $this->getId();
    }

    /**
     * Function to check duplicate exists or not
     * @return <boolean>
     */
    public function checkDuplicate($ownerid) {
        $db = PearDatabase::getInstance();

        $query = $this->getCheckDuplicateQuery($ownerid);
        $params = array($this->getModule()->getName(), decode_html($this->getName()));

        $record = $this->getId();
        if ($record) {
            $query .= " AND crmid != ?";
            array_push($params, $record);
        }

        $result = $db->pquery($query, $params);
        if ($db->num_rows($result)) {
            return true;
        }
        return false;
    }

    public static function getAccountsBillingAddresses($recordId) {
        $data = [];
        if ($recordId) {
            $db = PearDatabase::getInstance();
            $sql = 'SELECT * FROM `vtiger_accounts_billing_addresses` WHERE account_id = ?';
            $result = $db->pquery($sql, [$recordId]);

            while ($row = & $result->fetchRow()) {
                $data[] = [
                    'id' => $row['id'],
                    'commodity'        => getenv('INSTANCE_NAME') != 'graebel' ? explode(' |##| ', $row['commodity']) : $row['commodity'],
                    'address1' => $row['address1'],
                    'address2' => $row['address2'],
                    'address_desc' => $row['address_desc'],
                    'city' => $row['city'],
                    'state' => $row['state'],
                    'zip' => $row['zip'],
                    'country' => $row['country'],
                    'company' => $row['company'],
                    'active' => $row['active'],
                ];
            }
        }
        return $data;
    }

    public function getMappingFields($forModuleName) {
        $res = [];
        if($forModuleName == 'Estimates' || $forModuleName == 'Actuals')
        {
            foreach ($this->getInventoryMappingFields() as $field)
            {
                $res[$field['parentField']] = $field['inventoryField'];
            }
        }
        return $res;
    }

    /**
     * Function to get List of Fields which are related from Accounts to Inventory Record.
     * @return <array>
     */
    public function getInventoryMappingFields() {
        return array(
            //Billing Address Fields
            array('parentField' => 'bill_city', 'inventoryField' => 'bill_city', 'defaultValue' => ''),
            array('parentField' => 'bill_street', 'inventoryField' => 'bill_street', 'defaultValue' => ''),
            array('parentField' => 'bill_state', 'inventoryField' => 'bill_state', 'defaultValue' => ''),
            array('parentField' => 'bill_code', 'inventoryField' => 'bill_code', 'defaultValue' => ''),
            array('parentField' => 'bill_country', 'inventoryField' => 'bill_country', 'defaultValue' => ''),
            array('parentField' => 'bill_pobox', 'inventoryField' => 'bill_pobox', 'defaultValue' => ''),
            //Shipping Address Fields
            array('parentField' => 'ship_city', 'inventoryField' => 'ship_city', 'defaultValue' => ''),
            array('parentField' => 'ship_street', 'inventoryField' => 'ship_street', 'defaultValue' => ''),
            array('parentField' => 'ship_state', 'inventoryField' => 'ship_state', 'defaultValue' => ''),
            array('parentField' => 'ship_code', 'inventoryField' => 'ship_code', 'defaultValue' => ''),
            array('parentField' => 'ship_country', 'inventoryField' => 'ship_country', 'defaultValue' => ''),
            array('parentField' => 'ship_pobox', 'inventoryField' => 'ship_pobox', 'defaultValue' => '')
        );
    }

    /*
     * Grabs the select box options for invoice settings block
     */

    public function getInvoiceOptions() {
        $db = PearDatabase::getInstance();

        $data = [];

        $result = $db->query('SELECT invoice_document_format FROM `vtiger_invoice_document_format`');
        while ($row = & $result->fetchRow()) {
            $data['document_format'][] = $row['invoice_document_format'];
        }

        $result = $db->query('SELECT invoice_pkg_format FROM `vtiger_invoice_pkg_format`');
        while ($row = & $result->fetchRow()) {
            $data['invoice_pkg_format'][] = $row['invoice_pkg_format'];
        }


        $result = $db->query('SELECT invoice_format FROM `vtiger_invoice_format`');
        while ($row = & $result->fetchRow()) {
            $data['invoice_format'][] = $row['invoice_format'];
        }

        $result = $db->query('SELECT invoice_delivery_format FROM `vtiger_invoice_delivery_format`');
        while ($row = & $result->fetchRow()) {
            $data['delivery_format'][] = $row['invoice_delivery_format'];
        }

        return $data;
    }

    public static function getCurrentInvoiceSettings($record_id) {
        $data = [];

        if ($record_id) {
            $db = PearDatabase::getInstance();

            $result = $db->pquery('SELECT * FROM `vtiger_account_invoicesettings` WHERE record_id = ?', [$record_id]);
            while ($row = & $result->fetchRow()) {
                $data[] = [
                    'id' => $row['id'],
                    'commodity'        => getenv('INSTANCE_NAME') != 'graebel' ? explode(' |##| ', $row['commodity']) : $row['commodity'],
                    'invoice_template' => $row['invoice_template'],
                    'invoice_packet' => $row['invoice_packet'],
                    'document_format' => $row['document_format'],
                    'invoice_delivery' => $row['invoice_delivery'],
                    'finance_charge' => $row['finance_charge'],
                    'payment_terms' => $row['payment_terms'],
                ];
            }
        }
        return $data;
    }

    public function getCommodities() {
        $db = PearDatabase::getInstance();

        $data = [];
        $result = $db->query('SELECT commodity FROM `vtiger_commodity`');
        while ($row = & $result->fetchRow()) {
            $data[] = $row['commodity'];
        }
        return $data;
    }

    public function getAccountsRole() {
        $db = PearDatabase::getInstance();

        $data = [];
        $result = $db->query('SELECT accounts_role FROM `vtiger_accounts_role`');
        while ($row = & $result->fetchRow()) {
            $data[] = $row['accounts_role'];
        }
        return $data;
    }

    public static function getAdditionalRoleValues($record_id) {
        $data = [];

        if ($record_id) {
            $db = PearDatabase::getInstance();

            $result = $db->pquery('SELECT * FROM `vtiger_additional_roles` WHERE account_id = ?', [$record_id]);
            while ($row = & $result->fetchRow()) {
                $data[] = [
                    'id' => $row['id'],
                    'commodity' => explode(' ## ', $row['commodity']),
                    'user' => $row['user'],
                    'role' => $row['role'],
                ];
            }
        }
        return $data;
    }

    public function checkCreditHold($accId, $amount = '') {
        if ($accId != '') {
            //OT17020 didn't know this also gets called.
            $accountInstance = Vtiger_Record_Model::getInstanceById($accId);
            if ($accountInstance->get('billing_type') == 'Consumer/COD') {
                return false;
            }

            // if credit hold override is checked, pass
            if(\MoveCrm\InputUtils::CheckboxToBool($accountInstance->get('credit_hold_override')))
            {
                return false;
            }

    	    //Let's check this only if a credit limit has been set. Otherwise I will lockup 16K accounts that have credit limit = 0;
    	    if($accountInstance->get('credit_limit') == 0 && $accountInstance->get('credit_hold') == 0){
    		  return false;
    	    }

            $db = PearDatabase::getInstance();
            $isOnHold = false;

            $result = $db->pquery("SELECT (account_balance > credit_limit) as isonhold, credit_hold FROM vtiger_account WHERE accountid = ?", [$accId]);

            if ($result && $db->num_rows($result) > 0) {
                $row = $result->fetchRow();
                if ($row['isonhold'] == 1 && $row['credit_hold'] == 0) {
                    $user = Users_Record_Model::getCurrentUserModel();
                    $accountArray = [
                        'id' => vtws_getWebserviceEntityId('Accounts', $accId),
                        'credit_hold' => '1'
                    ];
                    try {
                        $update = vtws_revise($accountArray, $user);
                    } catch (Exception $exc) {
                        global $log;
                        $log->debug('Error updating Account:' . $exc->getMessage());
                    }
                    $isOnHold = true;
                } elseif ($row['credit_hold'] == 1) {
                    $isOnHold = true;  //If the record has been set to On Hold manually from the GUI
                } elseif ($amount != '') {
                    //Check if adding the current move amount does not goes over the credit limit
                    //No need to update the account on-hold status yet

                    $result = $db->pquery("SELECT ((account_balance > credit_limit) OR (account_balance + ? > credit_limit)) as isonhold FROM vtiger_account WHERE accountid = ?", [$amount, $accId]);

                    if ($result && $db->num_rows($result) > 0) {
                        $row = $result->fetchRow();
                            if ($row['isonhold'] == 1) {
                                $isOnHold = true;
                            }
                    }
                }
            }

        } else {
            $isOnHold = false;
        }

        return $isOnHold;
    }

    /**
     * OT1988
     * @param {int} $accId
     * @return bool
     */
    public function creditCheckDone($accId)
    {
        if (!$accId) {
            return true;
        }

        $accountInstance = Vtiger_Record_Model::getInstanceById($accId);
        //OT17020
        if ($accountInstance->get('billing_type') == 'Consumer/COD') {
            return true;
        }

        $creditCheckDate = $accountInstance->get('credit_check_date');

        if (!$creditCheckDate) {
            // When empty or null
            if(getenv('INSTANCE_NAME') == 'graebel')
            {
                return false;
            }
            return true;
        }

        $datetime1 = date_create($creditCheckDate);
        $datetime2 = date_create('now');
        $interval = date_diff($datetime1, $datetime2);
        $diff = (int)$interval->format('%m');
        // Invalid if month >= 9
        $creditCheckDone = $diff < 9;

        return $creditCheckDone;
    }

    public static function getSearchResult($searchKey, $module = false)
    {
        $db = PearDatabase::getInstance();
        $query  = 'SELECT label, crmid, setype, createdtime FROM vtiger_crmentity WHERE label LIKE ? AND vtiger_crmentity.deleted = 0';
        $params = ["%$searchKey%"];
        if ($module !== false) {
            $query .= ' AND setype = ?';
            $params[] = $module;
        }

        if(!getenv('DISABLE_REFERENCE_FIELD_LIMIT_BY_OWNER')) {
            if ($_REQUEST['agentId']){
                $query .= " AND vtiger_crmentity.agentid=?";
                //            $agentId = getAgentIdHasRecords($_REQUEST['agentId'], $module);
                $params[]= $_REQUEST['agentId'];
            }
        }


        //Remove the ordering for now to improve the speed
        //$query .= ' ORDER BY createdtime DESC';
        $result   = $db->pquery($query, $params);
        $noOfRows = $db->num_rows($result);
        $moduleModels = $matchingRecords = $leadIdsList = [];
        for ($i = 0; $i < $noOfRows; ++$i) {
            $row = $db->query_result_rowdata($result, $i);
            if ($row['setype'] === 'Leads') {
                $leadIdsList[] = $row['crmid'];
            }
        }
        $convertedInfo = Leads_Module_Model::getConvertedInfo($leadIdsList);
        for ($i = 0, $recordsCount = 0; $i < $noOfRows && $recordsCount < 100; ++$i) {
            $row = $db->query_result_rowdata($result, $i);
            if ($row['setype'] === 'Leads' && $convertedInfo[$row['crmid']]) {
                continue;
            }
            if (Users_Privileges_Model::isPermitted($row['setype'], 'DetailView', $row['crmid'])) {
                $row['id']  = $row['crmid'];
                $moduleName = $row['setype'];
                if (!array_key_exists($moduleName, $moduleModels)) {
                    $moduleModels[$moduleName] = Vtiger_Module_Model::getInstance($moduleName);
                }
                $moduleModel                              = $moduleModels[$moduleName];
                $modelClassName                           = Vtiger_Loader::getComponentClassName('Model', 'Record', $moduleName);
                $recordInstance                           = new $modelClassName();
                $matchingRecords[$moduleName][$row['id']] = $recordInstance->setData($row)->setModuleFromInstance($moduleModel);
                $recordsCount++;
            }
        }

        return $matchingRecords;
    }

    private function getCheckDuplicateQuery($ownerid) {
        $db = PearDatabase::getInstance();
        $sql = "SELECT setype FROM `vtiger_crmentity` WHERE crmid = ?";
        $result = $db->pquery($sql, [$ownerid]);
        if($result == NULL) {
            return "SELECT 1 FROM `vtiger_crmentity` WHERE setype = ? AND label = ? AND deleted = 0";
        }
        $setype = $result->fields['setype'];

        $whereClause = "setype = ? AND label = ? AND deleted = 0";

        if($setype == 'VanlineManager') {
            $vanlineId = $ownerid;
        } else if ($setype == 'AgentManager') {
            $sql = "SELECT vanline_id FROM `vtiger_agentmanager` WHERE agentmanagerid = ?";
            $result = $db->pquery($sql, [$ownerid]);
            $vanlineId = $result->fields['vanline_id'];
        } else {
            return "SELECT 1 FROM `vtiger_crmentity` WHERE setype = ? AND label = ? AND deleted = 0";
        }

        $sql = "SELECT agentmanagerid FROM `vtiger_agentmanager` WHERE vanline_id = ?";
        $result = $db->pquery($sql, [$vanlineId]);

        $inClause = '('.$vanlineId;
        while($row =& $result->fetchRow()) {
            $inClause .= ','.$row['agentmanagerid'];
        }
        $inClause .= ')';

        $whereClause .= ' AND agentid IN '.$inClause;

        return "SELECT 1 FROM `vtiger_crmentity` WHERE ".$whereClause;
    }
}
