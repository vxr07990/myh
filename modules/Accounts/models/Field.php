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
 * OpList Field Model Class
 */
class Accounts_Field_Model extends Vtiger_Field_Model
{

    //@NOTE: THis is not useless

    /**
     * Function to get all the available picklist values for the current field
     * @return <Array> List of picklist values if the field is of type picklist or multipicklist, null otherwise.
     */
    public function getPicklistValues()
    {
        if ($this->get('name') == 'business_line') {
            $db = PearDatabase::getInstance();

            $result = $db->pquery('SELECT business_line FROM vtiger_business_line ORDER by sortorderid', []);
            $options = array_column($result->GetAll(), 'business_line');

            return array_combine($options, $options);
        } else {
            return parent::getPicklistValues();
        }
    }
}
