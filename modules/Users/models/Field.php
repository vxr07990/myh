<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/**
 * User Field Model Class
 */
class Users_Field_Model extends Vtiger_Field_Model
{

//    /**
//     * Function to check whether the current field is read-only
//     * @return <Boolean> - true/false
//     */
//    public function isReadOnly()
//    {
//        $currentUserModel = Users_Record_Model::getCurrentUserModel();
//
//        if (
//            $currentUserModel->isParentVanLineUser() == false &&
//            $currentUserModel->isAdminUser() == false
//        ) {
//            if (
//                getenv('INSTANCE_NAME') == 'uvlc' &&
//                $currentUserModel->isAgencyAdmin()
//            ) {
//                return false;
//            }
//            if (
//                $this->get('uitype') == 98 ||
//                $this->get('uitype') == 1003 ||
//                $this->get('uitype') == 156 ||
//                $this->get('uitype') == 115
//            ) {
//                return true;
//            }
//        }
//    }
        /**
     * Function to check if the field is shown in detail view
     * @return <Boolean> - true/false
     */
    public function isViewEnabled()
    {
        if ($this->getDisplayType() == '4' || in_array($this->get('presence'), array(1, 3))) {
            return false;
        }
        return true;
    }


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
     * Function to check whether field is ajax editable'
     * @return <Boolean>
     */
    public function isAjaxEditable()
    {
        if (!$this->isEditable() || $this->get('uitype') == 105 || $this->get('uitype') == 106 || $this->get('uitype') == 98 || $this->get('uitype') == 101) {
            return false;
        }
        return true;
    }

    /**
     * Function to get all the available picklist values for the current field
     * @return <Array> List of picklist values if the field is of type picklist or multipicklist, null otherwise.
     */
    public function getPicklistValues($dateStamp = null)
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
        return parent::getPicklistValues($dateStamp);
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

        if (($fieldName == 'move_coordinator') || ($fieldName == 'move_coordinator_navl')) {
            return $this->getMoveCoordinatorName($value);
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

         if (
             !$currentUser->isAdminUser()
         ) {
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

        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        if (
            $currentUserModel->isParentVanLineUser() == false &&
            $currentUserModel->isAdminUser() == false
        ) {
            if (
                getenv('INSTANCE_NAME') == 'uvlc' &&
                $currentUserModel->isAgencyAdmin()
            ) {
                $this->set('editable', true);
            }
            if (
                $this->get('uitype') == 98 ||   //uitype 98 -> roleid
                $this->get('uitype') == 1003 || //uitype 1003 -> agent_ids (member of)
                $this->get('uitype') == 156 ||  //uitype 156 -> is_admin flag
                $this->get('uitype') == 115 ||  //uitype 115 -> status (inactive / active)
                $this->get('uitype') == 101     //uitype 101 -> reports_to_id (supervisor)
            ) {
                $this->set('editable', false);
            }
        }

        return $this->get('editable');
    }

    /**
     * Function to check whether the current field is read-only
     * @return <Boolean> - true/false
     */
    public function isReadOnly()
    {
        //TFS 30287
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        if(
            (!$currentUserModel->isParentVanLineUser() && !$currentUserModel->isAdminUser()) &&
            ($this->get('name') == 'sts_salesperson_avl'  || $this->get('name') == 'sts_salesperson_navl' || $this->get('name') == 'amc_salesperson_id_nvl'  || $this->get('name') == 'amc_salesperson_id')
        ){
            return true;
        }

        return parent::isReadOnly();
    }

    /**
     * Function which will check if empty piclist option should be given
     */
    public function isEmptyPicklistOptionAllowed()
    {
        if ($this->getFieldName() == 'reminder_interval') {
            return true;
        }
        return false;
    }

    public function getMoveCoordinatorName($agentmanager_id)
    {
        $db = PearDatabase::getInstance();

        $sql = "SELECT first_name, last_name FROM `vtiger_users` WHERE id = ?";

        $result  = $db->pquery($sql, [$agentmanager_id]);
        $row = $result->fetchRow();

        $first_name = $row['first_name'];
        $last_name = $row['last_name'];

        return $first_name." ".$last_name;
    }
}
