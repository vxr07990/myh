<?php

class Employees_Field_Model extends Vtiger_Field_Model
{
    /**
     * Function to get the Webservice Field data type
     * @return <String> Data type of the field
     */
    public function getFieldDataType()
    {
        if ($this->get('uitype') == 99) {
            return 'password';
        } elseif (in_array($this->get('uitype'), array(32, 115))) {
            return 'picklist';
        } elseif ($this->get('uitype') == 101) {
            return 'userReference';
        } elseif ($this->get('uitype') == 98) {
            return 'userRole';
        } elseif ($this->get('uitype') == 105) {
            return 'image';
        } elseif ($this->get('uitype') == 31) {
            return 'theme';
        }
        return parent::getFieldDataType();
    }

    /**
     * Function to get all the available picklist values for the current field
     * @return <Array> List of picklist values if the field is of type picklist or multipicklist, null otherwise.
     */
    public function getPicklistValues()
    {
        if ($this->get('uitype') == 32) {
            return Vtiger_Language_Handler::getAllLanguages();
        }
        if ($this->get('uitype') == 200) {
            return Vtiger_Language_Handler::getAllLanguages();
        } elseif ($this->get('uitype') == '115') {
            $db = PearDatabase::getInstance();

            $query = 'SELECT '.$this->getFieldName().' FROM vtiger_'.$this->getFieldName();
            $result = $db->pquery($query, array());
            $num_rows = $db->num_rows($result);
            $fieldPickListValues = array();
            for ($i=0; $i<$num_rows; $i++) {
                $picklistValue = $db->query_result($result, $i, $this->getFieldName());
                $fieldPickListValues[$picklistValue] = vtranslate($picklistValue, $this->getModuleName());
            }
            return $fieldPickListValues;
        }
        return parent::getPicklistValues();
    }

    /**
     * Function to returns all skins(themes)
     * @return <Array>
     */
    public function getAllSkins()
    {
        return Vtiger_Theme::getAllSkins();
    }

    /**
     * Function to retieve display value for a value
     * @param <String> $value - value which need to be converted to display value
     * @return <String> - converted display value
     */
    public function getDisplayValue($value, $recordId = false)
    {
        if ($this->get('uitype') == 32) {
            return Vtiger_Language_Handler::getLanguageLabel($value);
        }
        $fieldName = $this->getFieldName();
        if (($fieldName == 'currency_decimal_separator' || $fieldName == 'currency_grouping_separator') && ($value == '&nbsp;')) {
            return vtranslate('LBL_Space', 'Users');
        }
        return parent::getDisplayValue($value, $recordId);
    }

    /**
     * Function returns all the User Roles
     * @return
     */
    public function getAllRoles()
    {
        $currentUser = Users_Record_Model::getCurrentUserModel();

        if ($currentUser->isParentVanLineUser() || (getenv('INSTANCE_NAME') == 'uvlc' && $currentUser->isAgencyAdmin())) {
            $accesibleRoles = getRoleAndSubordinatesRoleIds($currentUser->get('roleid'));
            foreach ($accesibleRoles as $roleId) {
                $roles[getRoleName($roleId)] = $roleId;
            }
        } else {
            $roleModels = Settings_Roles_Record_Model::getAll();
            $roles = array();
            foreach ($roleModels as $roleId=>$roleModel) {
                $roleName = $roleModel->getName();
                $roles[$roleName] = $roleId;
            }
        }

        return $roles;
    }

    /**
     * Function to check whether this field editable or not
     * return <boolen> true/false
     */
    public function isEditable()
    {
        $isEditable = $this->get('editable');
        if (!$isEditable) {
            $this->set('editable', parent::isEditable());
        }
        return $this->get('editable');
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
            $seachConditionsParams=array();
            if (!empty($seachValue)) {
                $seachValue = "%$seachValue%";
                $seachConditions = " AND label LIKE ? ";
                $seachConditionsParams[]=$seachValue;
            } else {
                $seachConditions = '';
            }

            if($fieldName == 'employee_secondaryrole'){
                if(!getenv('DISABLE_REFERENCE_FIELD_LIMIT_BY_OWNER')) {

                    //Make Vanlines Roles available to agents

                    $currentUser = Users_Record_Model::getCurrentUserModel();
                    $accessibleAgents =  $currentUser->getBothAccessibleOwnersIdsForUser();
                    $seachConditions .= ' AND vtiger_crmentity.agentid IN ( ' . generateQuestionMarks($accessibleAgents) . ' )';
                    $seachConditionsParams[] = $accessibleAgents;
                }

                if($_REQUEST['employee_primaryrole'] && $referenceModule =='EmployeeRoles') {
                    $seachConditions .= " AND vtiger_crmentity.crmid <> ? ";
                    $seachConditionsParams[]=$_REQUEST['employee_primaryrole'];
                }
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

            if($seachConditions !='') {
                foreach($seachConditionsParams as $conditionParam) {
                    $params[]=$conditionParam;
                }
            }

            $values = array();
            $result = $db->pquery($query, $params);

            $num_rows = $db->num_rows($result);
            for ($i=0; $i<$num_rows; $i++) {
                //Need to decode the picklist values twice which are saved from old ui
                $values[$db->query_result($result, $i, 'crmid')] = decode_html(decode_html($db->query_result($result, $i, 'label')));
            }
            $cache->setPicklistValues($fieldName, $values);
            return $values;
        }
    }
}
