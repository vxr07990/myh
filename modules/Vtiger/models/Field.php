<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
include_once 'vtlib/Vtiger/Field.php';

/**
 * Vtiger Field Model Class
 */
class Vtiger_Field_Model extends Vtiger_Field
{
    public $webserviceField = false;
    const REFERENCE_TYPE            = 'reference';
    const OWNER_TYPE                = 'owner';
    const CURRENCY_LIST             = 'currencyList';
    const QUICKCREATE_MANDATORY     = 0;
    const QUICKCREATE_NOT_ENABLED   = 1;
    const QUICKCREATE_ENABLED       = 2;
    const QUICKCREATE_NOT_PERMITTED = 3;

    /**
     * Function to get the value of a given property
     *
     * @param  <String> $propertyName
     *
     * @return <Object>
     * @throws Exception
     */
    public function get($propertyName)
    {
        if (property_exists($this, $propertyName)) {
            return $this->$propertyName;
        }

        return null;
    }

    /**
     * Function which sets value for given name
     *
     * @param <String> $name - name for which value need to be assinged
     * @param <type> $value - values that need to be assigned
     *
     * @return Vtiger_Field_Model
     */
    public function set($name, $value)
    {
        $this->$name = $value;

        return $this;
    }

    /**
     * Function to get the Field Id
     * @return <Number>
     */
    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        //had to add this for the case of picklists that are duplicated with custom names
        //they needed this so that get picklist values would still work AND still have custom field names
        if ($this->noncustomname) {
            return $this->noncustomname;
        }
        return $this->name;
    }

    public function getFieldName()
    {
        return $this->name;
    }

    /**
     * Function to retrieve full data
     * @return <array>
     */
    public function getData()
    {
        return get_object_vars($this);
    }

    public function getOptionValue($param, $options)
    {
        //Example format for typeofdata with minimum value included: I~O~MIN=0
        if(!$param || !$options){
            return;
        }
        $returnVal = false;
        if(is_array($options)){
            foreach($options as $option){
                list($type, $val) = explode('=', $option);
                if(strtolower($type) == strtolower($param)){
                    $returnVal = $val;
                    break;
                }
            }
        }
        return $returnVal;
    }


    public function getTypeOptions() {
        //Getting rid of non-optional typeofdata parameters
        $typeOfData = $this->get('typeofdata');
        $components = explode('~', $typeOfData);
        unset($components[0],$components[1]);
        return $components;
    }

    public function getModule()
    {
        if (!$this->module) {
            $moduleObj = $this->block->module;
            //fix for opensource emailTemplate listview break
            if (empty($moduleObj)) {
                return false;
            }
            $this->module = Vtiger_Module_Model::getInstanceFromModuleObject($moduleObj);
        }

        return $this->module;
    }

    public function setModule($moduleInstance)
    {
        $this->module = $moduleInstance;
    }

    /**
     * Function to retieve display value for a value
     *
     * @param  <String> $value - value which need to be converted to display value
     *
     * @return <String> - converted display value
     */
    public function getDisplayValue($value, $record = false, $recordInstance = false)
    {
        if (!$this->uitype_instance) {
            $this->uitype_instance = Vtiger_Base_UIType::getInstanceFromField($this);
        }
        $uiTypeInstance = $this->uitype_instance;

        $typeofdata = $this->typeofdata;
        $typeofdataPieces = explode('~', $typeofdata);
        if($recordInstance && $typeofdataPieces[0] == 'T' && count($typeofdataPieces) > 3 && $typeofdataPieces[2] == 'REL' && $value != '') {
            $dateField = $typeofdataPieces[3];
            $dateValue = $recordInstance->get($dateField);
            $value = $dateValue . ' ' . $value;
        }

        return $uiTypeInstance->getDisplayValue($value, $record, $recordInstance);
    }

    /*
     * Function to format a phone number with () and dashes
     */
    public function getPhoneNumber($phone)
    {
        if (strlen($phone) == 7) {
            $phone = substr($phone, 0, 3).'-'.substr($phone, 3, 4);
        } elseif (strlen($phone) == 10) {
            $phone = '('.substr($phone, 0, 3).') '.substr($phone, 3, 3).'-'.substr($phone, 6, 4);
        }

        return $phone;
    }

    /**
     * Function to retrieve display type of a field
     * @return <String> - display type of the field
     */
    public function getDisplayType()
    {
        return $this->get('displaytype');
    }

    /**
     * Function to get the Webservice Field Object for the current Field Object
     * @return WebserviceField instance
     */
    public function getWebserviceFieldObject()
    {
        if ($this->webserviceField == false) {
            $db                    = PearDatabase::getInstance();
            $row                   = [];
            $row['uitype']         = $this->get('uitype');
            $row['block']          = $this->get('block');
            $row['tablename']      = $this->get('table');
            $row['columnname']     = $this->get('column');
            $row['fieldname']      = $this->get('name');
            $row['fieldlabel']     = $this->get('label');
            $row['displaytype']    = $this->get('displaytype');
            $row['masseditable']   = $this->get('masseditable');
            $row['typeofdata']     = $this->get('typeofdata');
            $row['presence']       = $this->get('presence');
            $row['tabid']          = $this->getModuleId();
            $row['fieldid']        = $this->get('id');
            //$row['readonly']       = !$this->get('readonly');
            if ($this->get('readonly')) {
                $row['readonly']       = !$this->getProfileReadWritePermission();
            } else {
                $row['readonly']       = !$this->get('readonly');
            }
            $row['defaultvalue']   = $this->get('defaultvalue');
            $this->webserviceField = WebserviceField::fromArray($db, $row);
        }

        return $this->webserviceField;
    }

    /**
     * Function to get the Webservice Field data type
     * @return <String> Data type of the field
     */
    public function getFieldDataType()
    {
        if (!$this->fieldDataType) {
            $uiType = $this->get('uitype');
            if ($uiType == '69') {
                $fieldDataType = 'image';
            } elseif ($uiType == '26') {
                $fieldDataType = 'documentsFolder';
            } elseif ($uiType == '27') {
                $fieldDataType = 'fileLocationType';
            } elseif ($uiType == '9') {
                $fieldDataType = 'percentage';
            } elseif ($uiType == '28') {
                $fieldDataType = 'documentsFileUpload';
            } elseif ($uiType == '83') {
                $fieldDataType = 'productTax';
            } elseif ($uiType == '117') {
                $fieldDataType = 'currencyList';
            } elseif ($uiType == '55' && $this->getName() === 'salutationtype') {
                $fieldDataType = 'picklist';
 //           } else if ($uiType == '55' && $this->getName() === 'firstname') {
 //               $fieldDataType = 'salutation';
            } elseif ($uiType == '54') {
                $fieldDataType = 'multiowner';
                // Not in New Securities
                //} else if($uiType == '200') {
                //	$fieldDataType = 'multiagent';
                //} else if($uiType == '560') {
                //	$fieldDataType = 'checkbox';
            } elseif ($uiType == '172') {
                $fieldDataType = 'accountingIntegrationReference';
            } else {
                $webserviceField = $this->getWebserviceFieldObject();
                $fieldDataType   = $webserviceField->getFieldDataType();
            }
            $this->fieldDataType = $fieldDataType;
        }

        return $this->fieldDataType;
    }

    /**
     * Function to get list of modules the field refernced to
     * @return <Array> -  list of modules for which field is refered to
     */
    public function getReferenceList()
    {
        $webserviceField = $this->getWebserviceFieldObject();
        if($webserviceField->getUIType() == '172')
        {
            $res = [];
            $db = &PearDatabase::getInstance();
            $result = $db->pquery('SELECT `type`,`subtype` FROM vtiger_accountingintegration_fieldrel WHERE fieldid=?',
                                  [$webserviceField->getFieldId()]);
            while($result && $row = $result->fetchRow())
            {
                $res[] = [
                    'type' => $row['type'],
                    'subtype' => $row['subtype'],
                ];
            }
            return $res;
        }

        return $webserviceField->getReferenceList();
    }

    /**
     * Function to identify number fields which can be batch added to or subtracted from using a popup
     * @return <Boolean> - True/False
     */
    public function isBatchAddSubtract()
    {
        return false;
    }


    /**
     * Function to check if the field is named field of the module
     * @return <Boolean> - True/False
     */
    public function isNameField()
    {
        $nameFieldObject = Vtiger_Cache::get('EntityField', $this->getModuleName());
        if (!$nameFieldObject) {
            $moduleModel = $this->getModule();
            if (!empty($moduleModel)) {
                $moduleEntityNameFields = $moduleModel->getNameFields();
            } else {
                $moduleEntityNameFields = [];
            }
        } else {
            $moduleEntityNameFields = explode(',', $nameFieldObject->fieldname);
        }
        if (in_array($this->get('name'), $moduleEntityNameFields)) {
            return true;
        }

        return false;
    }

    /**
     * Function to check whether the current field is read-only
     * @return <Boolean> - true/false
     */
    public function isReadOnly()
    {
        $webserviceField = $this->getWebserviceFieldObject();

        return $webserviceField->isReadOnly();
    }

    /**
     * Function to get the UI Type model for the uitype of the current field
     * @return Vtiger_Base_UIType or UI Type specific model instance
     */
    public function getUITypeModel()
    {
        return Vtiger_Base_UIType::getInstanceFromField($this);
    }

    public function isRoleBased()
    {
        if ($this->get('uitype') == '15' || $this->get('uitype') == '33' || ($this->get('uitype') == '55' && $this->getFieldName() == 'salutationtype')) {
            return true;
        }

        return false;
    }

    /**
     * Function to get all the available picklist values for the current field
     * @return <Array> List of picklist values if the field is of type picklist or multipicklist, null otherwise.
     */
    public function getPicklistValues($dateStamp = null, $agentid = null)
    {
        $fieldDataType = $this->getFieldDataType();
        if ($this->getName() == 'hdnTaxType') {
            return null;
        }
        //file_put_contents('logs/devLog.log', "\n fieldName: ".$this->getName(), FILE_APPEND);
        //file_put_contents('logs/devLog.log', "\n fieldDataType: $fieldDataType", FILE_APPEND);
        //file_put_contents('logs/devLog.log', "\n fieldName: ".$this->getName(), FILE_APPEND);
        //file_put_contents('logs/devLog.log', "\n fieldDataType: $fieldDataType", FILE_APPEND);
        $hasCustomTable = Vtiger_Utils::CheckTable('vtiger_picklistexceptions');
        if($fieldDataType == 'custompicklist' && $hasCustomTable){
            $currentUser = Users_Record_Model::getCurrentUserModel();
            if($agentid === NULL) {
                $agentid = $currentUser->getPrimaryOwnerForUser();
            }

            $picklistValues = Vtiger_Util_Helper::getCustomPicklistValues($this->getName(), $this->getId(), $agentid);
            //Just in case they need to translate a value
            foreach ($picklistValues as $value) {
                $fieldPickListValues[$value] = vtranslate($value, $this->getModuleName());
            }
            return $fieldPickListValues;

        } elseif ($fieldDataType == 'picklist' || $fieldDataType == 'multipicklist' || $fieldDataType == 'multipicklistall' || $fieldDataType == 'custompicklist') {
            //$currentUser = Users_Record_Model::getCurrentUserModel();
            if ($this->getName() == 'shared_assigned_to') {
                $currentUser    = Users_Record_Model::getCurrentUserModel();
                $agents         = $currentUser->getAccessibleAgentsForUser();
                $picklistValues = [];
                foreach ($agents as $key => $val) {
                    $picklistValues[] = $val;
                }
            } elseif ($this->getName() == 'sales_person') {
                $picklistValues = [];
                //@TODO: eventually put this here instead of the .tpl.
                //$currentUser    = Users_Record_Model::getCurrentUserModel();
                //$picklistValues = $currentUser->getAccessibleSalesPeople();
            } elseif ($this->getName() == 'payment_type' && getenv('INSTANCE_NAME') == 'graebel') {
                $db = PearDatabase::getInstance();
                $result = $db->query('SELECT payment_type FROM `vtiger_payment_type`');
                $picklistValues = [];
                while ($row = $result->fetchRow()) {
                    $picklistValues[] = $row['payment_type'];
                }
            } elseif ($this->getName() == 'payment_type' && getenv('IGC_MOVEHQ')) {
                $db = PearDatabase::getInstance();
                $result = $db->query('SELECT payment_type FROM `vtiger_payment_type`');
                $picklistValues = [];
                while ($row = $result->fetchRow()) {
                    $picklistValues[] = $row['payment_type'];
                }
            } elseif ($this->getName() == "itemcodes_default_revenue_agent") {
                $db = PearDatabase::getInstance();
                $result = $db->pquery('SELECT `agent_type` FROM `vtiger_agent_type`');
                $picklistValues = [];
                while ($row = $result->fetchRow()) {
                    $picklistValues[] = $row['agent_type'];
                }
            } elseif ($this->isRoleBased()) {
                $userModel = Users_Record_Model::getCurrentUserModel();
                $picklistValues = Vtiger_Util_Helper::getRoleBasedPicklistValues($this->getName(), $userModel->get('roleid'));
            } else {
                $picklistValues = Vtiger_Util_Helper::getPickListValues($this->getName());
            }
            foreach ($picklistValues as $value) {
                if($this->getName() == 'time_zone') {
                    $fieldPickListValues[$value] = getTimeZoneDisplayValue($value, $dateStamp);
                } else {
                    $fieldPickListValues[$value] = vtranslate($value, $this->getModuleName());
                }
            }

            //file_put_contents('logs/devLog.log', "\n fieldPickListValues: ".print_r($fieldPickListValues, true), FILE_APPEND);
            return $fieldPickListValues;
            //}
//            file_put_contents('logs/devLog.log', "\n Old Security Called : ".print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true), FILE_APPEND);
            //old securities
            /*if($fieldDataType == 'multiagent'){

                $userModel = Users_Record_Model::getCurrentUserModel();

                $isAdmin = $userModel->isAdminUser();

                $db = PearDatabase::getInstance();

                $userId = $userModel->getId();


                if(!$isAdmin){
                    $sql = "SELECT `vtiger_users2vanline`.vanlineid
                            FROM `vtiger_users2vanline`
                            JOIN `vtiger_crmentity`
                            ON `vtiger_users2vanline`.vanlineid = `vtiger_crmentity`.crmid
                            WHERE userid = ? AND `vtiger_crmentity`.deleted=0";
                    $result = $db->pquery($sql, array($userId));
                    $row = $result->fetchRow();

                    if($row != null){
                        $vanlineId = $row[0];
                        $sql = "SELECT agency_name
                                FROM `vtiger_agentmanager`
                                JOIN `vtiger_crmentity`
                                ON `vtiger_agentmanager`.agentmanagerid = `vtiger_crmentity`.crmid
                                WHERE vanline_id = ? AND `vtiger_crmentity`.deleted = 0";
                        $result = $db->pquery($sql, array($vanlineId));
                        $row = $result->fetchRow();
                        $agentNames = array();
                        While($row != null){
                            $sql2 = "SELECT groupid FROM `vtiger_groups` WHERE groupname = ?";
                            $result2 = $db->pquery($sql2, array($row[0]));
                            $row2 = $result2->fetchRow();
                            $agentNames[$row2[0]] = $row[0];
                            $row = $result->fetchRow();
                        }
                        //file_put_contents('logs/devLog.log', "\n !isAdmin agentNames : ".print_r($agentNames,true),FILE_APPEND);
                    } else{

                        $sql = "SELECT agency_code
                                FROM `vtiger_user2agency`
                                JOIN `vtiger_crmentity`
                                ON `vtiger_user2agency`.agency_code = `vtiger_crmentity`.crmid
                                WHERE userid = ? AND `vtiger_crmentity`.deleted=0";
                        $result = $db->pquery($sql, array($userId));
                        $row = $result->fetchRow();
                        $usersAgencyId = $row[0];

                        $sql = "SELECT agency_name
                                FROM `vtiger_agentmanager`
                                JOIN `vtiger_crmentity`
                                ON `vtiger_agentmanager`.agentmanagerid = `vtiger_crmentity`.crmid
                                WHERE agentmanagerid = ? AND `vtiger_crmentity`.deleted = 0";

                        $result = $db->pquery($sql, array($usersAgencyId));
                        $row = $result->fetchRow();
                        $usersAgencyName = $row[0];

                        $sql = "SELECT vanline_id
                                FROM  `vtiger_agentmanager`
                                JOIN  `vtiger_crmentity` ON  `vtiger_agentmanager`.vanline_id =  `vtiger_crmentity`.crmid
                                WHERE agentmanagerid = ?
                                AND  `vtiger_crmentity`.deleted =0";
                        $result = $db->pquery($sql, array($usersAgencyId));
                        $row = $result->fetchRow();
                        $vanlineId = $row[0];
                        //file_put_contents('logs/devLog.log', "\n else  vanlineId : ".print_r($vanlineId,true),FILE_APPEND);
                        $sql = "SELECT `vtiger_agentmanager`.agency_name
                                FROM `vtiger_agentmanager`
                                JOIN `vtiger_crmentity`
                                ON `vtiger_agentmanager`.agentmanagerid = `vtiger_crmentity`.crmid
                                WHERE `vtiger_agentmanager`.vanline_id = ? AND `vtiger_crmentity`.deleted = 0";
                        $result = $db->pquery($sql, array($vanlineId));
                        $agentNames = array();
                        While($row =& $result->fetchRow()){
                            $sql2 = "SELECT groupid FROM `vtiger_groups` WHERE groupname = ?";
                            $result2 = $db->pquery($sql2, array($row[0]));
                            $row2 = $result2->fetchRow();
                            if($row[0] != $usersAgencyName){
                                $agentNames[$row2[0]] = $row[0];
                            }
                        }
                        //file_put_contents('logs/devLog.log', "\n else  agentNames : ".print_r($agentNames,true),FILE_APPEND);
                    }
                } else{
                    $sql = "SELECT  `vtiger_agentmanager`.agency_name
                            FROM  `vtiger_agentmanager`
                            JOIN  `vtiger_crmentity`
                            ON  `vtiger_agentmanager`.agentmanagerid =  `vtiger_crmentity`.crmid
                            WHERE  `vtiger_crmentity`.deleted =0";
                    $result = $db->pquery($sql, array());
                    $row = $result->fetchRow();
                    $agentNames = array();
                    While($row != null){
                        $sql2 = "SELECT groupid FROM `vtiger_groups` WHERE groupname = ?";
                        $result2 = $db->pquery($sql2, array($row[0]));
                        $row2 = $result2->fetchRow();
                        $agentNames[$row2[0]] = $row[0];
                        $row = $result->fetchRow();
                    }

                }
                //file_put_contents('logs/devLog.log', "\n returning agentNames : ".print_r($agentNames,true),FILE_APPEND);
                return $agentNames;

            }*/
        }

        return null;
    }
    public function getReferenceValues($referenceModule, $seachValue ='')
    {
        global $list_max_entries_per_page;
        $fieldDataType = $this->getFieldDataType();
        if ($fieldDataType == 'referencemultipicklist' || $fieldDataType == 'referencemultipicklistall') {
            $fieldName = $this->getName();
            $fieldValue = $this->get('fieldvalue');

            $cache = Vtiger_Cache::getInstance();
            if ($cache->getPicklistValues($fieldName)) {
                return $cache->getPicklistValues($fieldName);
            }

            $db = PearDatabase::getInstance();
            if (!empty($seachValue)) {
                $seachValue = "%$seachValue%";
                $seachConditions = sprintf(" AND label LIKE '%s' ", $seachValue);
            } else {
                $seachConditions = '';
            }
            if (!empty($fieldValue)) {
                $fieldValueList = explode(',', $fieldValue);

                $query = "SELECT crmid,label FROM
                              ((SELECT crmid , label,createdtime,2 AS seq  FROM vtiger_crmentity
                              WHERE label IS NOT NULL AND TRIM(label) != '' AND  crmid in (".generateQuestionMarks($fieldValueList).") AND deleted = 0
                              ORDER BY createdtime DESC
                              LIMIT $list_max_entries_per_page)
                              UNION
                              (SELECT crmid , label, createdtime,1 AS seq  FROM vtiger_crmentity
                              WHERE label IS NOT NULL AND TRIM(label) != '' AND setype = ? AND deleted = 0 $seachConditions
                              ORDER BY createdtime DESC
                              LIMIT $list_max_entries_per_page )) AS E
                          GROUP BY E.crmid
                          ORDER BY E.seq DESC, E.createdtime DESC
                          LIMIT $list_max_entries_per_page";
                $params = array($fieldValueList,$referenceModule);
            } else {
                $query = "SELECT crmid , label FROM vtiger_crmentity
                          WHERE setype = ? AND deleted = 0 $seachConditions
                          ORDER BY createdtime DESC
                          LIMIT $list_max_entries_per_page";
                $params = array($referenceModule);
            }

            $values = array();
            $result = $db->pquery($query, $params);

            $num_rows = $db->num_rows($result);
            for ($i=0; $i<$num_rows; $i++) {
                //                Need to decode the picklist values twice which are saved from old ui
                $values[$db->query_result($result, $i, 'crmid')] = decode_html(decode_html($db->query_result($result, $i, 'label')));
            }
            $cache->setPicklistValues($fieldName, $values);
            return $values;
        }
    }
    /**
     * Function to check if the current field is mandatory or not
     * @return <Boolean> - true/false
     */
    public function isMandatory()
    {
        list($type, $mandatory) = explode('~', $this->get('typeofdata'));

        return $mandatory == 'M'?true:false;
    }

    /**
     * Function to get the field type
     * @return <String> type of the field
     */
    public function getFieldType()
    {
        $webserviceField = $this->getWebserviceFieldObject();

        return $webserviceField->getFieldType();
    }

    /**
     * Function to check if the field is shown in detail view
     * @return <Boolean> - true/false
     */
    public function isViewEnabled()
    {
        $permision = $this->getPermissions();
        if ($this->getDisplayType() == '4' || in_array($this->get('presence'), [1, 3])) {
            return false;
        }

        return $permision;
    }

    /**
     * Function to check if the field is shown in detail view
     * @return <Boolean> - true/false
     */
    public function isViewable()
    {
        if (!$this->isViewEnabled()) {
            return false;
        }

        return true;
    }

    /**
     * Function to check if the field is shown in detail view
     * @return <Boolean> - true/false
     */
    public function isViewableInDetailView()
    {
        if (!$this->isViewable() || $this->getDisplayType() == '3' || $this->getDisplayType() == '5') {
            return false;
        }

        return true;
    }

    public function isViewableInFilterView() {
		if(!$this->isViewable()){
			return false;
		}
		if($this->getDisplayType() == '6') {
			return false;
		}

		return true;
	}

    public function isEditEnabled()
    {
        $displayType             = (int) $this->get('displaytype');
        $editEnabledDisplayTypes = [1, 3];
        if (
            !$this->isViewEnabled() ||
            !in_array($displayType, $editEnabledDisplayTypes) ||
            strcasecmp($this->getFieldDataType(), "autogenerated") === 0 ||
            strcasecmp($this->getFieldDataType(), "id") === 0
        ) {
            return false;
        }

        return true;
    }

    public function isQuickCreateEnabled()
    {
        $moduleModel = $this->getModule();
        $quickCreate = $this->get('quickcreate');
        if (($quickCreate == self::QUICKCREATE_MANDATORY || $quickCreate == self::QUICKCREATE_ENABLED
             || $this->isMandatory()) && $this->get('uitype') != 69
        ) {
            //isQuickCreateSupported will not be there for settings
            if (method_exists($moduleModel, 'isQuickCreateSupported') && $moduleModel->isQuickCreateSupported()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Function to check whether summary field or not
     * @return <Boolean> true/false
     */
    public function isSummaryField()
    {
        return ($this->get('summaryfield'))?true:false;
    }

    /**
     * Function to check whether the current field is editable
     * @return <Boolean> - true/false
     */
    public function isEditable()
    {
        if (
            !$this->isEditEnabled() ||
            !$this->isViewable() ||
            ((int) $this->get('displaytype')) != 1 ||
            //$this->isReadOnly() == true ||
            $this->get('uitype') == 4
        ) {
            return false;
        }

        return true;
    }

    /**
     * Function to check whether field is ajax editable'
     * @return <Boolean>
     */
    public function isAjaxEditable()
    {
        $ajaxRestrictedFields = ['4', '72'];
        if (!$this->isEditable() || in_array($this->get('uitype'), $ajaxRestrictedFields)) {
            return false;
        }

        return true;
    }

    /**
     * Static Function to get the instance fo Vtiger Field Model from a given Vtiger_Field object
     *
     * @param Vtiger_Field $fieldObj - vtlib field object
     *
     * @return Vtiger_Field_Model instance
     */
    public static function getInstanceFromFieldObject(Vtiger_Field $fieldObj)
    {
        $objectProperties = get_object_vars($fieldObj);
        $className        = Vtiger_Loader::getComponentClassName('Model', 'Field', $fieldObj->getModuleName());
        $fieldModel       = new $className();
        foreach ($objectProperties as $properName => $propertyValue) {
            $fieldModel->$properName = $propertyValue;
        }

        return $fieldModel;
    }

    /**
     * Function to get the custom view column name transformation of the field for a date field used in date filters
     * @return <String> - tablename:columnname:fieldname:module_fieldlabel
     */
    public function getCVDateFilterColumnName()
    {
        $moduleName        = $this->getModuleName();
        $tableName         = $this->get('table');
        $columnName        = $this->get('column');
        $fieldName         = $this->get('name');
        $fieldLabel        = $this->get('label');
        $escapedFieldLabel = str_replace(' ', '_', $fieldLabel);
        $moduleFieldLabel  = $moduleName.'_'.$escapedFieldLabel;

        return $tableName.':'.$columnName.':'.$fieldName.':'.$moduleFieldLabel;
    }

    /**
     * Function to get the custom view column name transformation of the field
     * @return <String> - tablename:columnname:fieldname:module_fieldlabel:fieldtype
     */
    public function getCustomViewColumnName()
    {
        $moduleName      = $this->getModuleName();
        $tableName       = $this->get('table');
        $columnName      = $this->get('column');
        $fieldName       = $this->get('name');
        $fieldLabel      = $this->get('label');
        $typeOfData      = $this->get('typeofdata');
        $fieldTypeOfData = explode('~', $typeOfData);
        $fieldType       = $fieldTypeOfData[0];
		//file_put_contents('logs/devLog.log', "\n $fieldName :: $fieldType :: " . $this->getFieldDataType(), FILE_APPEND);
        //Special condition need for reference field as they should be treated as string field
        if ($this->getFieldDataType() == 'reference') {
            $fieldType = 'V';
        } else {
            $fieldType = ChangeTypeOfData_Filter($tableName, $columnName, $fieldType);
        }
        $escapedFieldLabel = str_replace(' ', '_', $fieldLabel);
        $moduleFieldLabel  = $moduleName.'_'.$escapedFieldLabel;

        $referenceFieldName = $this->get('reference_fieldname');
        if(!empty($referenceFieldName)) $fieldName = $referenceFieldName;

        return $tableName.':'.$columnName.':'.$fieldName.':'.$moduleFieldLabel.':'.$fieldType;
    }

    /**
     * Function to get the Report column name transformation of the field
     * @return <String> - tablename:columnname:module_fieldlabel:fieldname:fieldtype
     */
    public function getReportFilterColumnName()
    {
        $moduleName      = $this->getModuleName();
        $tableName       = $this->get('table');
        $columnName      = $this->get('column');
        $fieldName       = $this->get('name');
        $fieldLabel      = $this->get('label');
        $typeOfData      = $this->get('typeofdata');
        $fieldTypeOfData = explode('~', $typeOfData);
        $fieldType       = $fieldTypeOfData[0];
        if ($this->getFieldDataType() == 'reference') {
            $fieldType = 'V';
        } else {
            $fieldType = ChangeTypeOfData_Filter($tableName, $columnName, $fieldType);
        }
        $escapedFieldLabel = str_replace(' ', '_', $fieldLabel);
        $moduleFieldLabel  = $moduleName.'_'.$escapedFieldLabel;
        if ($tableName == 'vtiger_crmentity' && $columnName != 'smownerid') {
            $tableName = 'vtiger_crmentity'.$moduleName;
        } elseif ($columnName == 'smownerid') {
            $tableName  = 'vtiger_users'.$moduleName;
            $columnName = 'user_name';
        }

        return $tableName.':'.$columnName.':'.$moduleFieldLabel.':'.$fieldName.':'.$fieldType;
    }

    /**
     * This is set from Workflow Record Structure, since workflow expects the field name
     * in a different format in its filter. Eg: for module field its fieldname and for reference
     * fields its reference_field_name : (reference_module_name) field - salesorder_id: (SalesOrder) subject
     * @return <String>
     */
    public function getWorkFlowFilterColumnName()
    {
        return $this->get('workflow_columnname');
    }

    /**
     * Function to get the field details
     * @return <Array> - array of field values
     */
    public function getFieldInfo($recordId = false)
    {
        $currentUser                     = Users_Record_Model::getCurrentUserModel();
        if($recordId) {
            $db = PearDatabase::getInstance();
            $sql = "SELECT agentid FROM `vtiger_crmentity` WHERE crmid=?";
            $result = $db->pquery($sql, [$recordId]);
            $agentid = $result->fields['agentid'];
        } else {
            $agentid = $currentUser->getPrimaryOwnerForUser();
        }
        $fieldDataType                   = $this->getFieldDataType();
        $this->fieldInfo['mandatory']    = $this->isMandatory();
        $this->fieldInfo['presence']     = $this->isActiveField();
        $this->fieldInfo['quickcreate']  = $this->isQuickCreateEnabled();
        $this->fieldInfo['masseditable'] = $this->isMassEditable();
        $this->fieldInfo['defaultvalue'] = $this->hasDefaultValue();
        $this->fieldInfo['type']         = $fieldDataType;
        $this->fieldInfo['name']         = $this->get('name');
        $this->fieldInfo['label']        = vtranslate($this->get('label'), $this->getModuleName());
        //handling special options in the typeofdata field
        $options = $this->getTypeOptions();
        if($options) {
            $this->fieldInfo['min'] = $this->getOptionValue('min', $options);
            $this->fieldInfo['max'] = $this->getOptionValue('max', $options);
            $this->fieldInfo['step'] = $this->getOptionValue('step', $options);
        }
        //$this->fieldInfo['id'] = $this->getID();
        if ($fieldDataType == 'picklist' || $fieldDataType == 'multipicklist' || $fieldDataType == 'multiowner' || $fieldDataType == 'multipicklistall' || $fieldDataType == 'custompicklist') {
            $pickListValues = $this->getPicklistValues(null, $agentid);
            if (!empty($pickListValues)) {
                $this->fieldInfo['picklistvalues'] = $pickListValues;
            } else {
                $this->fieldInfo['picklistvalues'] = [];
            }
        }
        if ($this->getFieldDataType() == 'date' || $this->getFieldDataType() == 'datetime') {
            $currentUser                    = Users_Record_Model::getCurrentUserModel();
            $this->fieldInfo['date-format'] = $currentUser->get('date_format');
        }
        if ($this->getFieldDataType() == 'time') {
            $currentUser                    = Users_Record_Model::getCurrentUserModel();
            $this->fieldInfo['time-format'] = $currentUser->get('hour_format');
        }
        if ($this->getFieldDataType() == 'currency') {
            $currentUser                          = Users_Record_Model::getCurrentUserModel();
            $this->fieldInfo['currency_symbol']   = $currentUser->get('currency_symbol');
            $this->fieldInfo['decimal_seperator'] = $currentUser->get('currency_decimal_separator');
            $this->fieldInfo['group_seperator']   = $currentUser->get('currency_grouping_separator');
        }
        if ($this->getFieldDataType() == 'owner') {
            $userList                                                         = $currentUser->getAccessibleUsers();
            $groupList                                                        = $currentUser->getAccessibleGroups();
            $pickListValues                                                   = [];
            $pickListValues[vtranslate('LBL_USERS', $this->getModuleName())]  = $userList;
            $pickListValues[vtranslate('LBL_GROUPS', $this->getModuleName())] = $groupList;
            $this->fieldInfo['picklistvalues']                                = $pickListValues;
        }
        if ($this->getFieldDataType() == 'referencemultipicklist' || $this->getFieldDataType() == 'referencemultipicklistall') {
            $referenceList = $this->getReferenceList();
            if (count($referenceList) == 1) {
                $this->fieldInfo['reference_module'] = $referenceList[0];
            } elseif (count($referenceList) > 1) {
                $fieldValue = $this->get('fieldvalue');
                $referenceModuleStruct = $this->getUITypeModel()->getReferenceModule($fieldValue);
                if (!empty($referenceModuleStruct) && in_array($referenceModuleStruct->get('name'), $referenceList)) {
                    $this->fieldInfo['reference_module'] = $referenceModuleStruct->get('name');
                } else {
                    $this->fieldInfo['reference_module'] = $referenceList[0];
                }
            }
            $this->fieldInfo['picklistvalues'] = $this->getReferenceValues($this->fieldInfo['reference_module']);
        }

        return $this->fieldInfo;
    }

    public function setFieldInfo($fieldInfo)
    {
        $this->fieldInfo = $fieldInfo;
    }

    /**
     * Function to get the date values for the given type of Standard filter
     *
     * @param  <String> $type
     *
     * @return <Array> - 2 date values representing the range for the given type of Standard filter
     */
    //@TODO SHAME SHAME SHAME
    protected static function getDateForStdFilterBytype($type, $userPeferredDayOfTheWeek = false)
    {
        $today         = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d"), date("Y")));
        $todayName     = date('l', strtotime($today));
        $tomorrow      = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") + 1, date("Y")));
        $yesterday     = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - 1, date("Y")));
        $currentmonth0 = date("Y-m-d", mktime(0, 0, 0, date("m"), "01", date("Y")));
        $currentmonth1 = date("Y-m-t");
        $lastmonth0    = date("Y-m-d", mktime(0, 0, 0, date("m") - 1, "01", date("Y")));
        $lastmonth1    = date("Y-m-t", strtotime($lastmonth0));
        $nextmonth0    = date("Y-m-d", mktime(0, 0, 0, date("m") + 1, "01", date("Y")));
        $nextmonth1    = date("Y-m-t", strtotime($nextmonth0));
        // (Last Week) If Today is "Sunday" then "-2 week Sunday" will give before last week Sunday date
        if (!$userPeferredDayOfTheWeek) {
            $userPeferredDayOfTheWeek = 'Sunday';
        }
        if ($todayName == $userPeferredDayOfTheWeek) {
            $lastweek0 = date("Y-m-d", strtotime("-1 week $userPeferredDayOfTheWeek"));
        } else {
            $lastweek0 = date("Y-m-d", strtotime("-2 week $userPeferredDayOfTheWeek"));
        }
        $prvDay    = date('l', strtotime(date('Y-m-d', strtotime('-1 day', strtotime($lastweek0)))));
        $lastweek1 = date("Y-m-d", strtotime("-1 week $prvDay"));
        // (This Week) If Today is "Sunday" then "-1 week Sunday" will give last week Sunday date
        if ($todayName == $userPeferredDayOfTheWeek) {
            $thisweek0 = date("Y-m-d", strtotime("-0 week $userPeferredDayOfTheWeek"));
        } else {
            $thisweek0 = date("Y-m-d", strtotime("-1 week $userPeferredDayOfTheWeek"));
        }
        $prvDay    = date('l', strtotime(date('Y-m-d', strtotime('-1 day', strtotime($thisweek0)))));
        $thisweek1 = date("Y-m-d", strtotime("this $prvDay"));
        // (Next Week) If Today is "Sunday" then "this Sunday" will give Today's date
        if ($todayName == $userPeferredDayOfTheWeek) {
            $nextweek0 = date("Y-m-d", strtotime("+1 week $userPeferredDayOfTheWeek"));
        } else {
            $nextweek0 = date("Y-m-d", strtotime("this $userPeferredDayOfTheWeek"));
        }
        $prvDay      = date('l', strtotime(date('Y-m-d', strtotime('-1 day', strtotime($nextweek0)))));
        $nextweek1   = date("Y-m-d", strtotime("+1 week $prvDay"));
        $next7days   = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") + 6, date("Y")));
        $next30days  = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") + 29, date("Y")));
        $next60days  = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") + 59, date("Y")));
        $next90days  = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") + 89, date("Y")));
        $next120days = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") + 119, date("Y")));
        $next180days = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") + 179, date("Y")));
        $next365days = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") + 364, date("Y")));
        $last7days   = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - 6, date("Y")));
        $last30days  = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - 29, date("Y")));
        $last60days  = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - 59, date("Y")));
        $last90days  = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - 89, date("Y")));
        $last120days = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - 119, date("Y")));
        $currentFY0  = date("Y-m-d", mktime(0, 0, 0, "01", "01", date("Y")));
        $currentFY1  = date("Y-m-t", mktime(0, 0, 0, "12", date("d"), date("Y")));
        $lastFY0     = date("Y-m-d", mktime(0, 0, 0, "01", "01", date("Y") - 1));
        $lastFY1     = date("Y-m-t", mktime(0, 0, 0, "12", date("d"), date("Y") - 1));
        $nextFY0     = date("Y-m-d", mktime(0, 0, 0, "01", "01", date("Y") + 1));
        $nextFY1     = date("Y-m-t", mktime(0, 0, 0, "12", date("d"), date("Y") + 1));
        if (date("m") <= 3) {
            $cFq  = date("Y-m-d", mktime(0, 0, 0, "01", "01", date("Y")));
            $cFq1 = date("Y-m-d", mktime(0, 0, 0, "03", "31", date("Y")));
            $nFq  = date("Y-m-d", mktime(0, 0, 0, "04", "01", date("Y")));
            $nFq1 = date("Y-m-d", mktime(0, 0, 0, "06", "30", date("Y")));
            $pFq  = date("Y-m-d", mktime(0, 0, 0, "10", "01", date("Y") - 1));
            $pFq1 = date("Y-m-d", mktime(0, 0, 0, "12", "31", date("Y") - 1));
        } elseif (date("m") > 3 and date("m") <= 6) {
            $cFq  = date("Y-m-d", mktime(0, 0, 0, "04", "01", date("Y")));
            $cFq1 = date("Y-m-d", mktime(0, 0, 0, "06", "30", date("Y")));
            $nFq  = date("Y-m-d", mktime(0, 0, 0, "07", "01", date("Y")));
            $nFq1 = date("Y-m-d", mktime(0, 0, 0, "09", "30", date("Y")));
            $pFq  = date("Y-m-d", mktime(0, 0, 0, "01", "01", date("Y")));
            $pFq1 = date("Y-m-d", mktime(0, 0, 0, "03", "31", date("Y")));
        } elseif (date("m") > 6 and date("m") <= 9) {
            $cFq  = date("Y-m-d", mktime(0, 0, 0, "07", "01", date("Y")));
            $cFq1 = date("Y-m-d", mktime(0, 0, 0, "09", "30", date("Y")));
            $nFq  = date("Y-m-d", mktime(0, 0, 0, "10", "01", date("Y")));
            $nFq1 = date("Y-m-d", mktime(0, 0, 0, "12", "31", date("Y")));
            $pFq  = date("Y-m-d", mktime(0, 0, 0, "04", "01", date("Y")));
            $pFq1 = date("Y-m-d", mktime(0, 0, 0, "06", "30", date("Y")));
        } else {
            $cFq  = date("Y-m-d", mktime(0, 0, 0, "10", "01", date("Y")));
            $cFq1 = date("Y-m-d", mktime(0, 0, 0, "12", "31", date("Y")));
            $nFq  = date("Y-m-d", mktime(0, 0, 0, "01", "01", date("Y") + 1));
            $nFq1 = date("Y-m-d", mktime(0, 0, 0, "03", "31", date("Y") + 1));
            $pFq  = date("Y-m-d", mktime(0, 0, 0, "07", "01", date("Y")));
            $pFq1 = date("Y-m-d", mktime(0, 0, 0, "09", "30", date("Y")));
        }
        $dateValues = [];
        if ($type == "today") {
            $dateValues[0] = $today;
            $dateValues[1] = $today;
        } elseif ($type == "yesterday") {
            $dateValues[0] = $yesterday;
            $dateValues[1] = $yesterday;
        } elseif ($type == "tomorrow") {
            $dateValues[0] = $tomorrow;
            $dateValues[1] = $tomorrow;
        } elseif ($type == "thisweek") {
            $dateValues[0] = $thisweek0;
            $dateValues[1] = $thisweek1;
        } elseif ($type == "lastweek") {
            $dateValues[0] = $lastweek0;
            $dateValues[1] = $lastweek1;
        } elseif ($type == "nextweek") {
            $dateValues[0] = $nextweek0;
            $dateValues[1] = $nextweek1;
        } elseif ($type == "thismonth") {
            $dateValues[0] = $currentmonth0;
            $dateValues[1] = $currentmonth1;
        } elseif ($type == "lastmonth") {
            $dateValues[0] = $lastmonth0;
            $dateValues[1] = $lastmonth1;
        } elseif ($type == "nextmonth") {
            $dateValues[0] = $nextmonth0;
            $dateValues[1] = $nextmonth1;
        } elseif ($type == "next7days") {
            $dateValues[0] = $today;
            $dateValues[1] = $next7days;
        } elseif ($type == "next30days") {
            $dateValues[0] = $today;
            $dateValues[1] = $next30days;
        } elseif ($type == "next60days") {
            $dateValues[0] = $today;
            $dateValues[1] = $next60days;
        } elseif ($type == "next90days") {
            $dateValues[0] = $today;
            $dateValues[1] = $next90days;
        } elseif ($type == "next120days") {
            $dateValues[0] = $today;
            $dateValues[1] = $next120days;
        } elseif ($type == "next180days") {
            $dateValues[0] = $today;
            $dateValues[1] = $next180days;
        } elseif ($type == "next365days") {
            $dateValues[0] = $today;
            $dateValues[1] = $next365days;
        } elseif ($type == "last7days") {
            $dateValues[0] = $last7days;
            $dateValues[1] = $today;
        } elseif ($type == "last30days") {
            $dateValues[0] = $last30days;
            $dateValues[1] = $today;
        } elseif ($type == "last60days") {
            $dateValues[0] = $last60days;
            $dateValues[1] = $today;
        } elseif ($type == "last90days") {
            $dateValues[0] = $last90days;
            $dateValues[1] = $today;
        } elseif ($type == "last120days") {
            $dateValues[0] = $last120days;
            $dateValues[1] = $today;
        } elseif ($type == "thisfy") {
            $dateValues[0] = $currentFY0;
            $dateValues[1] = $currentFY1;
        } elseif ($type == "prevfy") {
            $dateValues[0] = $lastFY0;
            $dateValues[1] = $lastFY1;
        } elseif ($type == "nextfy") {
            $dateValues[0] = $nextFY0;
            $dateValues[1] = $nextFY1;
        } elseif ($type == "nextfq") {
            $dateValues[0] = $nFq;
            $dateValues[1] = $nFq1;
        } elseif ($type == "prevfq") {
            $dateValues[0] = $pFq;
            $dateValues[1] = $pFq1;
        } elseif ($type == "thisfq") {
            $dateValues[0] = $cFq;
            $dateValues[1] = $cFq1;
        } else {
            $dateValues[0] = "";
            $dateValues[1] = "";
        }

        return $dateValues;
    }

    /**
     * Function to get all the date filter type informations
     * @return <Array>
     */
    public static function getDateFilterTypes()
    {
        $dateFilters              = ['custom'      => ['label' => 'LBL_CUSTOM'],
                                     'prevfy'      => ['label' => 'LBL_PREVIOUS_FY'],
                                     'thisfy'      => ['label' => 'LBL_CURRENT_FY'],
                                     'nextfy'      => ['label' => 'LBL_NEXT_FY'],
                                     'prevfq'      => ['label' => 'LBL_PREVIOUS_FQ'],
                                     'thisfq'      => ['label' => 'LBL_CURRENT_FQ'],
                                     'nextfq'      => ['label' => 'LBL_NEXT_FQ'],
                                     'yesterday'   => ['label' => 'LBL_YESTERDAY'],
                                     'today'       => ['label' => 'LBL_TODAY'],
                                     'tomorrow'    => ['label' => 'LBL_TOMORROW'],
                                     'lastweek'    => ['label' => 'LBL_LAST_WEEK'],
                                     'thisweek'    => ['label' => 'LBL_CURRENT_WEEK'],
                                     'nextweek'    => ['label' => 'LBL_NEXT_WEEK'],
                                     'lastmonth'   => ['label' => 'LBL_LAST_MONTH'],
                                     'thismonth'   => ['label' => 'LBL_CURRENT_MONTH'],
                                     'nextmonth'   => ['label' => 'LBL_NEXT_MONTH'],
                                     'last7days'   => ['label' => 'LBL_LAST_7_DAYS'],
                                     'last30days'  => ['label' => 'LBL_LAST_30_DAYS'],
                                     'last60days'  => ['label' => 'LBL_LAST_60_DAYS'],
                                     'last90days'  => ['label' => 'LBL_LAST_90_DAYS'],
                                     'last120days' => ['label' => 'LBL_LAST_120_DAYS'],
                                     'next30days'  => ['label' => 'LBL_NEXT_30_DAYS'],
                                     'next60days'  => ['label' => 'LBL_NEXT_60_DAYS'],
                                     'next90days'  => ['label' => 'LBL_NEXT_90_DAYS'],
                                     'next120days' => ['label' => 'LBL_NEXT_120_DAYS'],
                                     'next180days' => ['label' => 'LBL_NEXT_180_DAYS'],
                                     'next365days' => ['label' => 'LBL_NEXT_365_DAYS']
        ];
        $currentUserModel         = Users_Record_Model::getCurrentUserModel();
        $userPeferredDayOfTheWeek = $currentUserModel->get('dayoftheweek');
        foreach ($dateFilters as $filterType => $filterDetails) {
            $dateValues                            = self::getDateForStdFilterBytype($filterType, $userPeferredDayOfTheWeek);
            $dateFilters[$filterType]['startdate'] = $dateValues[0];
            $dateFilters[$filterType]['enddate']   = $dateValues[1];
        }

        return $dateFilters;
    }

    /**
     * Function to get all the supported advanced filter operations
     * @return <Array>
     */
    public static function getAdvancedFilterOptions()
    {
        return [
            'e'  => 'LBL_EQUALS',
            'n'  => 'LBL_NOT_EQUAL_TO',
            's'  => 'LBL_STARTS_WITH',
            'ew' => 'LBL_ENDS_WITH',
            'c'  => 'LBL_CONTAINS',
            'k'  => 'LBL_DOES_NOT_CONTAIN',
            'l'  => 'LBL_LESS_THAN',
            'g'  => 'LBL_GREATER_THAN',
            'm'  => 'LBL_LESS_THAN_OR_EQUAL',
            'h'  => 'LBL_GREATER_OR_EQUAL',
            'b'  => 'LBL_BEFORE',
            'a'  => 'LBL_AFTER',
            'bw' => 'LBL_BETWEEN',
            'y'  => 'LBL_IS_EMPTY',
            'ny' => 'LBL_IS_NOT_EMPTY',
        ];
    }

    /**
     * Function to get the advanced filter option names by Field type
     * @return <Array>
     */
    public static function getAdvancedFilterOpsByFieldType()
    {
        return [
            'V'  => ['e', 'n', 's', 'ew', 'c', 'k', 'y', 'ny'],
            'N'  => ['e', 'n', 'l', 'g', 'm', 'h', 'y', 'ny'],
            'T'  => ['e', 'n', 'l', 'g', 'm', 'h', 'bw', 'b', 'a', 'y', 'ny'],
            'I'  => ['e', 'n', 'l', 'g', 'm', 'h', 'y', 'ny'],
            'C'  => ['e', 'n', 'y', 'ny'],
            'D'  => ['e', 'n', 'bw', 'b', 'a', 'y', 'ny'],
            'DT' => ['e', 'n', 'bw', 'b', 'a', 'y', 'ny'],
            'NN' => ['e', 'n', 'l', 'g', 'm', 'h', 'y', 'ny'],
            'E'  => ['e', 'n', 's', 'ew', 'c', 'k', 'y', 'ny'],
        ];
    }

    /**
     * Function to retrieve field model for specific block and module
     *
     * @param  <Vtiger_Module_Model> $blockModel - block instance
     *
     * @return <array> List of field model
     */
    public static function getAllForModule($moduleModel)
    {
        $fieldModelList = Vtiger_Cache::get('ModuleFields', $moduleModel->id);
        if (!$fieldModelList) {
            $fieldObjects   = parent::getAllForModule($moduleModel);
            $fieldModelList = [];
            //if module dont have any fields
            if (!is_array($fieldObjects)) {
                $fieldObjects = [];
            }
            foreach ($fieldObjects as $fieldObject) {
                $fieldModelObject                                      = self::getInstanceFromFieldObject($fieldObject);
                $fieldModelList[$fieldModelObject->get('block')->id][] = $fieldModelObject;
                Vtiger_Cache::set('field-'.$moduleModel->getId(), $fieldModelObject->getId(), $fieldModelObject);
                Vtiger_Cache::set('field-'.$moduleModel->getId(), $fieldModelObject->getName(), $fieldModelObject);
            }
            Vtiger_Cache::set('ModuleFields', $moduleModel->id, $fieldModelList);
        }

        return $fieldModelList;
    }

    /**
     * Function to get instance
     *
     * @param  <String> $value - fieldname or fieldid
     * @param  <type> $module - optional - module instance
     *
     * @return <Vtiger_Field_Model>
     */
    public static function getInstance($value, $module = false)
    {
        $fieldObject = null;
        if ($module) {
            $fieldObject = Vtiger_Cache::get('field-'.$module->getId(), $value);
        }
        if (!$fieldObject) {
            $fieldObject = parent::getInstance($value, $module);
            if ($module) {
                Vtiger_Cache::set('field-'.$module->getId(), $value, $fieldObject);
            }
        }
        if ($fieldObject) {
            return self::getInstanceFromFieldObject($fieldObject);
        }

        return false;
    }

    /**
     * Added function that returns the folders in a Document
     * @return <Array>
     */
    public function getDocumentFolders()
    {
        $db      = PearDatabase::getInstance();
        $result  = $db->pquery('SELECT * FROM vtiger_attachmentsfolder', []);
        $rows    = $db->num_rows($result);
        $folders = [];
        for ($i = 0; $i < $rows; $i++) {
            $folderId           = $db->query_result($result, $i, 'folderid');
            $folderName         = $db->query_result($result, $i, 'foldername');
            $folders[$folderId] = $folderName;
        }

        return $folders;
    }

    /**
     * Function checks if the current Field is Read/Write
     * @return <Boolean>
     */
    public function getProfileReadWritePermission()
    {
        return $this->getPermissions('readwrite');
    }

    /**
     * Function returns Client Side Validators name
     * @return <Array> [name=>Name of the Validator, params=>Extra Parameters]
     */
    /**TODO: field validator need to be handled in specific module getValidator api  **/
    public function getValidator()
    {
        $validator = [];
        $fieldName = $this->getName();
        switch ($fieldName) {
            case 'birthday':
                $funcName = ['name' => 'lessThanToday'];
                array_push($validator, $funcName);
                break;
            case 'support_end_date':
                $funcName = ['name'   => 'greaterThanDependentField',
                             'params' => ['support_start_date']];
                array_push($validator, $funcName);
                break;
            case 'support_start_date':
                $funcName = ['name'   => 'lessThanDependentField',
                             'params' => ['support_end_date']];
                array_push($validator, $funcName);
                break;
            case 'targetenddate':
            case 'actualenddate':
            case 'enddate':
                $funcName = ['name'   => 'greaterThanDependentField',
                             'params' => ['startdate']];
                array_push($validator, $funcName);
                break;
            case 'startdate':
                if ($this->getModule()->get('name') == 'Project') {
                    $params = ['targetenddate'];
                } else {
                    //for project task
                    $params = ['enddate'];
                }
                $funcName = ['name'   => 'lessThanDependentField',
                             'params' => $params];
                array_push($validator, $funcName);
                break;
            case 'expiry_date':
            case 'due_date':
                $funcName = ['name'   => 'greaterThanDependentField',
                             'params' => ['start_date']];
                array_push($validator, $funcName);
                break;
            case 'sales_end_date':
                $funcName = ['name'   => 'greaterThanDependentField',
                             'params' => ['sales_start_date']];
                array_push($validator, $funcName);
                break;
            case 'sales_start_date':
                $funcName = ['name'   => 'lessThanDependentField',
                             'params' => ['sales_end_date']];
                array_push($validator, $funcName);
                break;
            case 'qty_per_unit':
            case 'qtyindemand':
            case 'hours':
            case 'days':
                $funcName = ['name' => 'PositiveNumber'];
                array_push($validator, $funcName);
                break;
            case 'employees':
                $funcName = ['name' => 'WholeNumber'];
                array_push($validator, $funcName);
                break;
            case 'related_to':
                $funcName = ['name' => 'ReferenceField'];
                array_push($validator, $funcName);
                break;
            //SalesOrder field sepecial validators
            case 'end_period':
                $funcName1 = ['name'   => 'greaterThanDependentField',
                              'params' => ['start_period']];
                array_push($validator, $funcName1);
                $funcName2 = ['name'   => 'lessThanDependentField',
                              'params' => ['duedate']];
                array_push($validator, $funcName2);
            case 'start_period':
                $funcName = ['name'   => 'lessThanDependentField',
                             'params' => ['end_period']];
                array_push($validator, $funcName);
                break;
        }

        return $validator;
    }

    /**
     * Function to retrieve display value in edit view
     *
     * @param  <String> $value - value which need to be converted to display value
     *
     * @return <String> - converted display value
     */
    public function getEditViewDisplayValue($value)
    {
        if (!$this->uitype_instance) {
            $this->uitype_instance = Vtiger_Base_UIType::getInstanceFromField($this);
        }
        $uiTypeInstance = $this->uitype_instance;

        return $uiTypeInstance->getEditViewDisplayValue($value);
    }

    /**
     * Function to retieve types of file locations in Documents Edit
     * @return <array> - List of file location types
     */
    public function getFileLocationType()
    {
        return ['I' => 'LBL_INTERNAL', 'E' => 'LBL_EXTERNAL'];
    }

    /**
     * Function returns list of Currencies available in the system
     * @return <Array>
     */
    public function getCurrencyList()
    {
        $db     = PearDatabase::getInstance();
        $result = $db->pquery('SELECT * FROM vtiger_currency_info WHERE currency_status = ? AND deleted=0', ['Active']);
        for ($i = 0; $i < $db->num_rows($result); $i++) {
            $currencyId              = $db->query_result($result, $i, 'id');
            $currencyName            = $db->query_result($result, $i, 'currency_name');
            $currencies[$currencyId] = $currencyName;
        }

        return $currencies;
    }

    /**
     * Function to get Display value for RelatedList
     *
     * @param  <String> $value
     *
     * @return <String>
     */
    public function getRelatedListDisplayValue($value)
    {
        if (!$this->uitype_instance) {
            $this->uitype_instance = Vtiger_Base_UIType::getInstanceFromField($this);
        }
        $uiTypeInstance = $this->uitype_instance;

        return $uiTypeInstance->getRelatedListDisplayValue($value);
    }

    /**
     * Function to get Default Field Value
     * @return <String> defaultvalue
     */
    public function getDefaultFieldValue()
    {
        return $this->defaultvalue;
    }

    /**
     * Function whcih will get the databse insert value format from user format
     *
     * @param type $value in user format
     *
     * @return type
     */
    public function getDBInsertValue($value)
    {
        if (!$this->uitype_instance) {
            $this->uitype_instance = Vtiger_Base_UIType::getInstanceFromField($this);
        }
        $uiTypeInstance = $this->uitype_instance;

        return $uiTypeInstance->getDBInsertValue($value);
    }

    /**
     * Function to get visibilty permissions of a Field
     *
     * @param  <String> $accessmode
     *
     * @return <Boolean>
     */
    public function getPermissions($accessmode = 'readonly')
    {
        $user       = Users_Record_Model::getCurrentUserModel();
        $privileges = $user->getPrivileges();
        if ($privileges->hasGlobalReadPermission()) {
            return true;
        } else {
            $modulePermission = Vtiger_Cache::get('modulePermission-'.$accessmode, $this->getModuleId());
            if (!$modulePermission) {
                $modulePermission = self::preFetchModuleFieldPermission($this->getModuleId(), $accessmode);
            }
            if (array_key_exists($this->getId(), $modulePermission)) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Function to Preinitialize the module Field Permissions
     *
     * @param  <Integer> $tabid
     * @param  <String> $accessmode
     *
     * @return <Array>
     */
    public static function preFetchModuleFieldPermission($tabid, $accessmode = 'readonly')
    {
        $adb         = PearDatabase::getInstance();
        $user        = Users_Record_Model::getCurrentUserModel();
        $privileges  = $user->getPrivileges();
        $profilelist = $privileges->get('profiles');
        if (count($profilelist) > 0) {
            if ($accessmode == 'readonly') {
                $query =
                    "SELECT vtiger_profile2field.visible,vtiger_field.fieldid FROM vtiger_field INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid=vtiger_field.fieldid WHERE vtiger_field.tabid=? AND vtiger_profile2field.visible=0 AND vtiger_def_org_field.visible=0  AND vtiger_profile2field.profileid in (".
                    generateQuestionMarks($profilelist).
                    ") AND vtiger_field.presence in (0,2) GROUP BY vtiger_field.fieldid";
            } else {
                $query =
                    "SELECT vtiger_profile2field.visible,vtiger_field.fieldid FROM vtiger_field INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid=vtiger_field.fieldid WHERE vtiger_field.tabid=? AND vtiger_profile2field.visible=0 AND vtiger_profile2field.readonly=0 AND vtiger_def_org_field.visible=0  AND vtiger_profile2field.profileid in (".
                    generateQuestionMarks($profilelist).
                    ") AND vtiger_field.presence in (0,2) GROUP BY vtiger_field.fieldid";
            }
            $params = [$tabid, $profilelist];
        } else {
            if ($accessmode == 'readonly') {
                $query =
                    "SELECT vtiger_profile2field.visible,vtiger_field.fieldid FROM vtiger_field INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid=vtiger_field.fieldid WHERE vtiger_field.tabid=? AND vtiger_profile2field.visible=0 AND vtiger_def_org_field.visible=0  AND vtiger_field.presence in (0,2) GROUP BY vtiger_field.fieldid";
            } else {
                $query =
                    "SELECT vtiger_profile2field.visible,vtiger_field.fieldid FROM vtiger_field INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid=vtiger_field.fieldid WHERE vtiger_field.tabid=? AND vtiger_profile2field.visible=0 AND vtiger_profile2field.readonly=0 AND vtiger_def_org_field.visible=0  AND vtiger_field.presence in (0,2) GROUP BY vtiger_field.fieldid";
            }
            $params = [$tabid];
        }
        $result           = $adb->pquery($query, $params);
        $modulePermission = [];
        $noOfFields       = $adb->num_rows($result);
        for ($i = 0; $i < $noOfFields; ++$i) {
            $row                               = $adb->query_result_rowdata($result, $i);
            $modulePermission[$row['fieldid']] = $row['visible'];
        }
        Vtiger_Cache::set('modulePermission-'.$accessmode, $tabid, $modulePermission);

        return $modulePermission;
    }

    public function __update()
    {
        $db     = PearDatabase::getInstance();
        $query  = 'UPDATE vtiger_field SET typeofdata=?,presence=?,quickcreate=?,masseditable=?,defaultvalue=?,summaryfield=? WHERE fieldid=?';
        $params = [$this->get('typeofdata'),
                   $this->get('presence'),
                   $this->get('quickcreate'),
                   $this->get('masseditable'),
                   $this->get('defaultvalue'),
                   $this->get('summaryfield'),
                   $this->get('id')];
        $db->pquery($query, $params);
    }

    public function updateTypeofDataFromMandatory($mandatoryValue = 'O')
    {
        $mandatoryValue             = strtoupper($mandatoryValue);
        $supportedMandatoryLiterals = ['O', 'M'];
        if (!in_array($mandatoryValue, $supportedMandatoryLiterals)) {
            return;
        }
        $typeOfData    = $this->get('typeofdata');
        $components    = explode('~', $typeOfData);
        $components[1] = $mandatoryValue;
        $this->set('typeofdata', implode('~', $components));

        return $this;
    }

    public function isCustomField()
    {
        return (substr($this->getName(), 0, 3) == 'cf_')?true:false;
    }

    public function hasDefaultValue()
    {
        return $this->defaultvalue == ''?false:true;
    }

    public function isActiveField()
    {
        $presence = $this->get('presence');

        return in_array($presence, [0, 2]);
    }

    public function isMassEditable()
    {
        return $this->masseditable == 1?true:false;
    }

    /**
     * Function which will check if empty piclist option should be given
     */
    public function isEmptyPicklistOptionAllowed()
    {
        return true;
    }

    public function isReferenceField()
    {
        return ($this->getFieldDataType() == self::REFERENCE_TYPE)?true:false;
    }

    public function isOwnerField()
    {
        return ($this->getFieldDataType() == self::OWNER_TYPE)?true:false;
    }

    public static function getInstanceFromFieldId($fieldId, $moduleTabId)
    {
        $db = PearDatabase::getInstance();
        if (is_string($fieldId)) {
            $fieldId = [$fieldId];
        }
        $query          = 'SELECT * FROM vtiger_field WHERE fieldid IN ('.generateQuestionMarks($fieldId).') AND tabid=?';
        $result         = $db->pquery($query, [$fieldId, $moduleTabId]);
        $fieldModelList = [];
        $num_rows       = $db->num_rows($result);
        for ($i = 0; $i < $num_rows; $i++) {
            $row        = $db->query_result_rowdata($result, $i);
            $fieldModel = new self();
            $fieldModel->initialize($row);
            $fieldModelList[] = $fieldModel;
        }

        return $fieldModelList;
    }

    public function getTimezoneValues(Vtiger_Field_Model $fieldModel, $recordModel) {
        $typeofdata = explode('~', $fieldModel->get('typeofdata'));
        if(count($typeofdata) > 3 && $typeofdata[2] == 'REL') {
            $dateField = $typeofdata[3];
            if($recordModel) {
                $dateValue = $recordModel->get($dateField);
            } else {
                $dateValue = null;
            }
            return $this->getPicklistValues($dateValue);
        } else {
            return $this->getPicklistValues();
        }
    }

    public function getRelatedDateField() {
        $typeofdata = explode('~', $this->get('typeofdata'));
        if(count($typeofdata) > 3 && $typeofdata[2] == 'REL') {
            $dateField = $typeofdata[3];
        }

        return $dateField;
    }

}
