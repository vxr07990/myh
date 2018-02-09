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
 * Contacts Field Model Class
 */
class Contacts_Field_Model extends Vtiger_Field_Model
{

    /**
     * Function to check if the current field is mandatory or not
     * @return <Boolean> - true/false
     */
    public function isMandatory()
    {
        if (getenv('INSTANCE_NAME') == 'sirva' && ($this->get('name') == 'firstname' || $this->get('name') == 'phone' || $this->get('name') == 'email' || $this->get('name') == 'mailingstreet' || $this->get('name') == 'mailingcity' || $this->get('name') == 'mailingstate' || $this->get('name') == 'mailingzip' || $this->get('name') == 'mailingcountry')) {
            return true;
        }

        return parent::isMandatory();
    }
}
