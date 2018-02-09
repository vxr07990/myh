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
 * AutoSpotQuote Field Model Class
 */
class AutoSpotQuote_Field_Model extends Vtiger_Field_Model
{

    /**
     * Function to get Default Field Value
     * @return <String> defaultvalue
     */
    public function getDefaultFieldValue()
    {
        if ($this->name == 'auto_load_from') {
            return date('Y-m-d');
        } else {
            return $this->defaultvalue;
        }
    }

    /**
     * Function to check whether the current field is read-only
     * @return <Boolean> - true/false
     */
    public function isReadOnly()
    {
        if ($this->get('name') == 'registration_number') {
            return true;
        }

        return parent::isReadOnly();
    }

    /**
     * Function to check whether the current field is editable
     * @return <Boolean> - true/false
     */
    public function isEditable()
    {
        if ($this->get('name') == 'registration_number') {
            return true;
        }

        return parent::isEditable();
    }
}
