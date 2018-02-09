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
class OPList_Field_Model extends Vtiger_Field_Model
{
    public function isEditEnabled()
    {
        if ($this->get('name') == 'op_move_type' && !(getenv('INSTANCE_NAME') == 'sirva')) {
            return false;
        }
        return parent::isEditEnabled();
    }

    public function isViewEnabled()
    {
        if ($this->get('name') == 'op_move_type' && !(getenv('INSTANCE_NAME') == 'sirva')) {
            return false;
        }
        return parent::isViewEnabled();
    }

    /**
     * Function to get all the available picklist values for the current field
     * @return <Array> List of picklist values if the field is of type picklist or multipicklist, null otherwise.
     */
    public function getPicklistValues()
    {
        if ($this->get('name') == 'business_line') {
            $db = PearDatabase::getInstance();

            $result = $db->pquery('SELECT business_line FROM vtiger_business_line', []);

            $options = array_column($result->GetAll(), 'business_line');

            return array_combine($options, $options);
        } elseif ($this->get('name') == 'op_move_type') {
            $db = PearDatabase::getInstance();

            $result = $db->pquery('SELECT move_type FROM vtiger_move_type', []);

            $options = array_column($result->GetAll(), 'move_type');

            return array_combine($options, $options);
        } else {
            return parent::getPicklistValues();
        }
    }
}
