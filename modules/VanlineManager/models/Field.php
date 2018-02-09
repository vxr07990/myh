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
 * Opportunities Field Model Class
 */
class VanlineManager_Field_Model extends Vtiger_Field_Model
{
    /**
     * Function to check whether the current field is read-only
     * @return <Boolean> - true/false
     */
    public function isReadOnly(){
		$currentUser = Users_Record_Model::getCurrentUserModel();
        if ($this->get('name') == 'vanline_id' && !$currentUser->isAdminUser()){
           return true;
        }

        return parent::isReadOnly();
    }
}
