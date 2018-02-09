<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Leads_Record_Model extends Vtiger_Record_Model
{

    /**
     * Function returns the url for converting lead
     */
    public function getConvertLeadUrl()
    {
        return 'index.php?module='.$this->getModuleName().'&view=ConvertLead&record='.$this->getId();
    }

    /**
     * Static Function to get the list of records matching the search key
     * @param <String> $searchKey
     * @return <Array> - List of Vtiger_Record_Model or Module Specific Record Model instances
     */
    public static function getSearchResult($searchKey, $module=false)
    {
        $db = PearDatabase::getInstance();

        //$this can't be used because it's static function right?
        //$deletedCondition = $this->getModule()->getDeletedRecordCondition();
        $deletedCondition = Leads_Record_Model::getModule()->getDeletedRecordCondition();
        $query = 'SELECT * FROM vtiger_crmentity
                    INNER JOIN vtiger_leaddetails ON vtiger_leaddetails.leadid = vtiger_crmentity.crmid
                    WHERE label LIKE ? AND '.$deletedCondition;
        $params = array("%$searchKey%");
        if ($_REQUEST['agentId']){
            $query .= " AND vtiger_crmentity.agentid=?";
            $params[]=$_REQUEST['agentId'];
        }
        $result = $db->pquery($query, $params);
        $noOfRows = $db->num_rows($result);

        if(!getenv('DISABLE_REFERENCE_FIELD_LIMIT_BY_OWNER')) {
            if($_REQUEST['agentId']) {
                $query .= ' AND agentid = ?';
                $params[] = $_REQUEST['agentId'];
            }
        }

        $moduleModels = array();
        $matchingRecords = array();
        for ($i=0; $i<$noOfRows; ++$i) {
            $row = $db->query_result_rowdata($result, $i);
            $row['id'] = $row['crmid'];
            $moduleName = $row['setype'];
            if (!array_key_exists($moduleName, $moduleModels)) {
                $moduleModels[$moduleName] = Vtiger_Module_Model::getInstance($moduleName);
            }
            $moduleModel = $moduleModels[$moduleName];
            $modelClassName = Vtiger_Loader::getComponentClassName('Model', 'Record', $moduleName);
            $recordInstance = new $modelClassName();
            $matchingRecords[$moduleName][$row['id']] = $recordInstance->setData($row)->setModuleFromInstance($moduleModel);
        }
        return $matchingRecords;
    }

    /**
     * Function returns Account fields for Lead Convert
     * @return Array
     */
    public function getAccountFieldsForLeadConvert()
    {
        $accountsFields = array();
        $privilegeModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $moduleName = 'Accounts';

        if (!Users_Privileges_Model::isPermitted($moduleName, 'EditView')) {
            return;
        }

        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        if ($moduleModel->isActive()) {
            $fieldModels = $moduleModel->getFields();
            //Fields that need to be shown
            $compulsoryFields = [];
            if (getenv('INSTANCE_NAME') != 'graebel') {
                $compulsoryFields = ['industry'];
            }
            foreach ($fieldModels as $fieldName => $fieldModel) {
                if (
                    method_exists($fieldModel, 'isMandatory') &&
                    $fieldModel->isMandatory() &&
                    $fieldName != 'assigned_user_id'
                ) {
                    $keyIndex = array_search($fieldName, $compulsoryFields);
                    if ($keyIndex !== false) {
                        unset($compulsoryFields[$keyIndex]);
                    }
                    $leadMappedField = $this->getConvertLeadMappedField($fieldName, $moduleName);
                    $fieldModel->set('fieldvalue', $this->get($leadMappedField));
                    $accountsFields[] = $fieldModel;
                }
            }
            foreach ($compulsoryFields as $compulsoryField) {
                $fieldModel = Vtiger_Field_Model::getInstance($compulsoryField, $moduleModel);
                if ($fieldModel->getPermissions('readwrite')) {
                    $industryFieldModel = $moduleModel->getField($compulsoryField);
                    $industryLeadMappedField = $this->getConvertLeadMappedField($compulsoryField, $moduleName);
                    $industryFieldModel->set('fieldvalue', $this->get($industryLeadMappedField));
                    $accountsFields[] = $industryFieldModel;
                }
            }
        }
        return $accountsFields;
    }

    /**
     * Function returns Contact fields for Lead Convert
     * @return Array
     */
    public function getContactFieldsForLeadConvert()
    {
        $contactsFields = array();
        $privilegeModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $moduleName = 'Contacts';

        if (!Users_Privileges_Model::isPermitted($moduleName, 'EditView')) {
            return;
        }

        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        if ($moduleModel->isActive()) {
            $fieldModels = $moduleModel->getFields();
            $compulsoryFields = array('firstname', 'email');
            foreach ($fieldModels as $fieldName => $fieldModel) {
                if (
                    method_exists($fieldModel, 'isMandatory') &&
                    $fieldModel->isMandatory() &&
                    $fieldName != 'assigned_user_id' &&
                    $fieldName != 'account_id'
                ) {
                    $keyIndex = array_search($fieldName, $compulsoryFields);
                    if ($keyIndex !== false) {
                        unset($compulsoryFields[$keyIndex]);
                    }

                    $leadMappedField = $this->getConvertLeadMappedField($fieldName, $moduleName);
                    $fieldValue = $this->get($leadMappedField);
                    if ($fieldName === 'account_id') {
                        $fieldValue = $this->get('company');
                    }
                    $fieldModel->set('fieldvalue', $fieldValue);
                    if ($fieldName == 'contact_type') {
                        $fieldModel->set('fieldvalue', 'Transferee');
                    }
                    $contactsFields[] = $fieldModel;
                }
            }

            foreach ($compulsoryFields as $compulsoryField) {
                $fieldModel = Vtiger_Field_Model::getInstance($compulsoryField, $moduleModel);
                if ($fieldModel->getPermissions('readwrite')) {
                    $leadMappedField = $this->getConvertLeadMappedField($compulsoryField, $moduleName);
                    $fieldModel = $moduleModel->getField($compulsoryField);
                    $fieldModel->set('fieldvalue', $this->get($leadMappedField));
                    $contactsFields[] = $fieldModel;
                }
            }
        }
        return $contactsFields;
    }
    /**
     * Function returns an array to load in PricingCompetitors
     * @return Array
     */
    public function getPricingCompetitors()
    {
        $db = PearDatabase::getInstance();
        $sql = "SELECT allied,atlas,mayflower,north_american,united,independent,other FROM `vtiger_sirva_pricing_comp` WHERE leadid=?";
        $result = $db->pquery($sql, [$this->getId()]);
        if ($result) {
            $row = $result->fetchRow();
            if ($row) {
                return $row;
            }
        }
        return false;
    }
    /**
     * Function returns Potential fields for Lead Convert
     * @return Array
     */
    public function getPotentialsFieldsForLeadConvert()
    {
        $potentialFields = array();
        //$privilegeModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $moduleName = 'Opportunities';

        if (!Users_Privileges_Model::isPermitted($moduleName, 'EditView')) {
            return;
        }

        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        if ($moduleModel->isActive()) {
            $fieldModels = $moduleModel->getFields();

            $compulsoryFields = array('amount', 'potentialname');

            if(getenv('INSTANCE_NAME') == 'sirva') {
                $compulsoryFields[] = 'lead_type';
                $compulsoryFields[] = 'moving_vehicle';
                $compulsoryFields[] = 'number_of_vehicles';
                $compulsoryFields[] = 'vehicle_year';
                $compulsoryFields[] = 'vehicle_make';
                $compulsoryFields[] = 'vehicle_model';
            }

            foreach ($fieldModels as $fieldName => $fieldModel) {
                if (
                    $fieldModel &&
                    method_exists($fieldModel, 'isMandatory') &&
                    $fieldModel->isMandatory() &&
                    $fieldName != 'assigned_user_id' &&
                    $fieldName != 'related_to' &&
                    $fieldName != 'contact_id' &&
                    $fieldModel->get('presence') != 1
                ) {
                    //handle sirva specific business_line/move_type descrepancy
                    if (getenv('INSTANCE_NAME') == 'sirva' && ($fieldName == 'business_line' || $fieldName == 'business_line_est' || $fieldName == 'destination_address1' || $fieldName == 'destination_country')) {
                        continue;
                    }
                    $keyIndex = array_search($fieldName, $compulsoryFields);
                    if ($keyIndex !== false) {
                        unset($compulsoryFields[$keyIndex]);
                    }
                    $leadMappedField = $this->getConvertLeadMappedField($fieldName, $moduleName);
                    $fieldModel->set('fieldvalue', $this->get($leadMappedField) ?: $fieldModel->getDefaultFieldValue());

                    if ($fieldName == 'sales_stage') {
                        $vals = $fieldModel->getPicklistValues();
                        if(array_key_exists('Prospecting', $vals)) {
                            $fieldModel->set('fieldvalue', 'Prospecting');
                        } else {
                            $newVal = $vals[array_keys($vals)[0]];
                            $fieldModel->set('fieldvalue', $newVal);
                        }
                    }
                    //file_put_contents('logs/devLog.log', "\n MAPPING FIELD NAME: ".$leadMappedField, FILE_APPEND);
                    //file_put_contents('logs/devLog.log', "\n MAPPING FIELD VALUE: ".$this->get($leadMappedField), FILE_APPEND);
                    $potentialFields[] = $fieldModel;
                    if ($fieldName == 'move_type') {
                      $fieldModel->set('fieldvalue', $this->get('move_type'));
                    }
                    if ($fieldName == 'origin_country' && getenv('INSTANCE_NAME') == 'sirva') {
                        $destAddrModel = $fieldModels['destination_address1'];
                        $destCountryModel = $fieldModels['destination_country'];
                        $destAddrField = $this->getConvertLeadMappedField('destination_address1', $moduleName);
                        $destCountryField = $this->getConvertLeadMappedField('destination_country', $moduleName);
                        $destAddrModel->set('fieldvalue', $this->get($destAddrField));
                        $destCountryModel->set('fieldvalue', $this->get($destCountryField));
                        $potentialFields[] = $destAddrModel;
                        $potentialFields[] = $destCountryModel;
                    }
                    if ($fieldName == 'potentialname') {
                        if (getenv('INSTANCE_NAME') == 'sirva') {
                            //this sets the opportunity's name to the "<firstname> <lastname>"
                            //name sure the name isn't null... that will force them to enter an opp name.
                            $potentialName = '';
                            if ($this->get('firstname') != null && $this->get('firstname') != 'NULL') {
                                $potentialName = $this->get('firstname');
                            }
                            if ($this->get('lastname') != null && $this->get('lastname') != 'NULL') {
                                $potentialName .= ' ' . $this->get('lastname');
                            }
                            $fieldModel->set('fieldvalue', $potentialName);
                        } elseif (getenv('INSTANCE_NAME') == 'mccollisters') {
                            $fieldModel->set('fieldvalue', 'Auto - '.$this->get('lastname'));
                        }
                    }

                    //file_put_contents('logs/devLog.log', "\n FIELD NAME: ".$fieldName, FILE_APPEND);
                    //file_put_contents('logs/devLog.log', "\n FIELD MODEL: ".print_r($fieldModel, true), FILE_APPEND);
                }
            }
            foreach ($compulsoryFields as $compulsoryField) {
                $fieldModel = Vtiger_Field_Model::getInstance($compulsoryField, $moduleModel);
                if (
                    $fieldModel &&
                    $fieldModel->getPermissions('readwrite')
                ) {
                    $fieldModel = $moduleModel->getField($compulsoryField);
                    $amountLeadMappedField = $this->getConvertLeadMappedField($compulsoryField, $moduleName);
                    $fieldModel->set('fieldvalue', $this->get($amountLeadMappedField));
                    $potentialFields[] = $fieldModel;
                }
            }
        }
        return $potentialFields;
    }

    /**
     * Function returns field mapped to Leads field, used in Lead Convert for settings the field values
     * @param <String> $fieldName
     * @return <String>
     */
    public function getConvertLeadMappedField($fieldName, $moduleName)
    {
        $mappingFields = $this->get('mappingFields');

        if (!$mappingFields) {
            $db = PearDatabase::getInstance();
            $mappingFields = array();

            $result = $db->pquery('SELECT * FROM vtiger_convertleadmapping', array());
            $numOfRows = $db->num_rows($result);

            $accountInstance = Vtiger_Module_Model::getInstance('Accounts');
            $accountFieldInstances = $accountInstance->getFieldsById();

            $contactInstance = Vtiger_Module_Model::getInstance('Contacts');
            $contactFieldInstances = $contactInstance->getFieldsById();

            $potentialInstance = Vtiger_Module_Model::getInstance('Opportunities');
            $potentialFieldInstances = $potentialInstance->getFieldsById();

            $leadInstance = Vtiger_Module_Model::getInstance('Leads');
            $leadFieldInstances = $leadInstance->getFieldsById();

            for ($i=0; $i<$numOfRows; $i++) {
                $row = $db->query_result_rowdata($result, $i);

                if (empty($row['leadfid'])) {
                    continue;
                }

                $leadFieldInstance = $leadFieldInstances[$row['leadfid']];
                if (!$leadFieldInstance) {
                    continue;
                }

                $leadFieldName = $leadFieldInstance->getName();
                $accountFieldInstance = $accountFieldInstances[$row['accountfid']];
                if ($row['accountfid'] && $accountFieldInstance) {
                    $mappingFields['Accounts'][$accountFieldInstance->getName()] = $leadFieldName;
                }
                $contactFieldInstance = $contactFieldInstances[$row['contactfid']];
                if ($row['contactfid'] && $contactFieldInstance) {
                    $mappingFields['Contacts'][$contactFieldInstance->getName()] = $leadFieldName;
                }

                $potentialFieldInstance = $potentialFieldInstances[$row['potentialfid']];
                if ($row['potentialfid'] && $potentialFieldInstance) {
                    $mappingFields['Opportunities'][$potentialFieldInstance->getName()] = $leadFieldName;
                    //file_put_contents('logs/devLog.log', "\n  mappingFields : ".print_r($mappingFields['Potentials'],true), FILE_APPEND);
                }
            }
            //file_put_contents('logs/devLog.log', "\n  mappingFields : ".print_r($mappingFields,true), FILE_APPEND);
            $this->set('mappingFields', $mappingFields);
        }
        //file_put_contents('logs/devLog.log', "\n MAPPING FIELDS ARRAY: ".print_r($mappingFields, true), FILE_APPEND);
        return $mappingFields[$moduleName][$fieldName];
    }

    /**
     * Function returns the fields required for Lead Convert
     * @return <Array of Vtiger_Field_Model>
     */
    public function getConvertLeadFields()
    {
        $convertFields = array();

        $potentialsFields = $this->getPotentialsFieldsForLeadConvert();
        if (!empty($potentialsFields)) {
            $convertFields['Opportunities'] = $potentialsFields;
        }

        $accountFields = $this->getAccountFieldsForLeadConvert();
        if (!empty($accountFields)) {
            $convertFields['Accounts'] = $accountFields;
        }

        $contactFields = $this->getContactFieldsForLeadConvert();
        if (!empty($contactFields)) {
            $convertFields['Contacts'] = $contactFields;
        }

        return $convertFields;
    }

    /**
     * Function returns the url for create event
     * @return <String>
     */
    public function getCreateEventUrl()
    {
        $calendarModuleModel = Vtiger_Module_Model::getInstance('Calendar');
        return $calendarModuleModel->getCreateEventRecordUrl().'&parent_id='.$this->getId();
    }

    /**
     * Function returns the url for create todo
     * @return <String>
     */
    public function getCreateTaskUrl()
    {
        $calendarModuleModel = Vtiger_Module_Model::getInstance('Calendar');
        return $calendarModuleModel->getCreateTaskRecordUrl().'&parent_id='.$this->getId();
    }

    /**
     * Function to check whether the lead is converted or not
     * @return True if the Lead is Converted false otherwise.
     */
    public function isLeadConverted()
    {
        $db = PearDatabase::getInstance();
        $id = $this->getId();
        $sql = "select converted from vtiger_leaddetails where converted = 1 and leadid=?";
        $result = $db->pquery($sql, array($id));
        $rowCount = $db->num_rows($result);
        if ($rowCount > 0) {
            return true;
        }
        return false;
    }
}
