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
 * Estimates Field Model Class
 */
class Estimates_Field_Model extends Vtiger_Field_Model
{

    /**
     * Function to retrieve display type of a field
     * @return <String> - display type of the field
     */
    public function getDisplayType()
    {
        if ($this->get('name') == 'cubesheet') {
            return 2;
        } elseif(!getenv('IGC_MOVEHQ') && $this->get('name') == 'orders_id'){
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
        if ($this->get('name') == 'cubesheet' || $this->get('name') == 'survey_date' || $this->get('name') == 'survey_time') {
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
        if ($this->get('name') == 'survey_date' || $this->get('name') == 'survey_time') {
            return true;
        }

        return parent::isEditable();
    }

    /**
     * Function to get the Webservice Field data type
     * @return <String> Data type of the field
     */
    public function getFieldDataType()
    {
        if ($this->get('name') == 'billing_apn') {
            $this->set('uitype', 1);
        }

        return parent::getFieldDataType();
    }

    public function getDisplayValue($value, $record = false, $recordInstance = false) {
        if($this->getName() == 'effective_tariff'  && preg_match('/^\d+$/', $value))
        {
            $res = Vtiger_Functions::getCRMRecordLabel($value);
            return $res;
        }
        return parent::getDisplayValue($value, $record, $recordInstance);
    }

    public function isEmptyPicklistOptionAllowed()
    {
        if ($this->get('name') == 'quotestage' ||
            $this->get('name') == 'actuals_stage')
        {
            return false;
        }
        return true;
    }


    /**TODO: field validator need to be handled in specific module getValidator api  **/
    public function getValidator()
    {
        $validator = [];

        $typeOfData = $this->get('typeofdata');
        $components = explode('~', $typeOfData);

        if($components[0] == 'I' && $components[2] == 'MIN=0'){
            $validator[] = ['name'   => 'WholeNumber'];
            return $validator;
        }else{
            return parent::getValidator();
        }

    }

}
