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
 * Tariffs Field Model Class
 */
class Tariffs_Field_Model extends Vtiger_Field_Model
{

    /**
     * Function to retrieve display type of a field
     * @return <String> - display type of the field
     */
    public function getDisplayType()
    {
      $user = Users_Record_Model::getCurrentUserModel();
      if ($this->get('name') == 'tariff_type' ||
         ($this->get('name') == 'admin_access' && !$user->isAdminUser())) {
         return 3;
      }
        return parent::getDisplayType();
    }

    /**
     * Function to check whether the current field is read-only
     * @return <Boolean> - true/false
     */
    public function isReadOnly()
    {
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        if (
            ($currentUserModel->isAdminUser() == false && $this->get('uitype') == 98) ||
            //this hides the admin_access field from view for non-admin
            ($currentUserModel->isAdminUser() == false && $this->get('uitype') == 156) ||
            //this hides the tariff_type field from view for non-admin
            ($currentUserModel->isAdminUser() == false && $this->get('name') == 'tariff_type') ||
            $this->get('uitype') == 115
        ) {
            return true;
        }
    }

    public function isEmptyPicklistOptionAllowed()
    {
        if($this->get('name') == 'business_line') {
            return true;
        }
        return false;
    }

    public function getPicklistValues()
    {
        if ($this->get('name') == 'business_line') {
            $db = &PearDatabase::getInstance();

            $result = $db->pquery('SELECT business_line FROM vtiger_business_line ORDER by sortorderid', []);

            $options = array_column($result->GetAll(), 'business_line');

            return array_combine($options, $options);
        } else {
            return parent::getPicklistValues();
        }
    }

    /**
     * Function to check whether the current field is editable
     * @return <Boolean> - true/false
     */
    public function isEditable()
    {
      $user = Users_Record_Model::getCurrentUserModel();
      if ($this->get('name') == 'tariff_type' ||
         ($this->get('name') == 'admin_access' && !$user->isAdminUser())) {
         return false;
      }
      return parent::isEditable();
    }
}
