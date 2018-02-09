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
class Opportunities_Field_Model extends Vtiger_Field_Model
{
    /**
     * Function to check if the current field is mandatory or not
     * @return <Boolean> - true/false
     */
    public function isMandatory()
    {
        if ($this->get('name') == 'contact_id') {
            return true;
        }

        return parent::isMandatory();
    }

    /**
     * Function to get the Webservice Field data type
     * @return <String> Data type of the field
     */
    public function getFieldDataType()
    {
        if ($this->get('name') == 'brand') {
           $this->set('uitype', 16);
        } elseif ($this->get('name') == 'origin_split') {
            $this->set('uitype', 71);
        } elseif ($this->get('name') == 'appointment_type' && $_REQUEST['view'] =='Edit') {
            $this->set('uitype', 1);
        }

        return parent::getFieldDataType();
    }

    /**
     * Function to check whether the current field is read-only
     * @return <Boolean> - true/false
     */
    public function isReadOnly()
    {
        if ($this->get('name') == 'survey_date' || $this->get('name') == 'survey_time' || $this->get('name') == 'lmp_lead_id' || $this->get('name') == 'program_name' || $this->get('name') == 'appointment_type' || $this->get('name') == 'non_conforming_params' || $this->get('name') == 'non_conforming' || $this->get('name') == 'warm_transfer' || $this->get('name') == 'segment' || $this->get('name') == 'segment_used' || $this->get('name') == 'segment_desc' || $this->get('name') == 'shipper_type') {
           return true;
        }
        //Sirva only rules
        if (getenv('INSTANCE_NAME') == 'sirva') {
            if ($this->get('name') == 'shipper_type' || $this->get('name') == 'origin_fax' || $this->get('name') == 'destination_fax' || $this->get('name') == 'lead_type' || $this->get('name') == 'opportunity_disposition') {
               return true;
            }
        }

        return parent::isReadOnly();
    }

    /**
     * Function to check whether the current field is editable
     * @return <Boolean> - true/false
     */
    public function isEditable()
    {
        if ($this->get('name') == 'survey_date' || $this->get('name') == 'survey_time' || $this->get('name') == 'lmp_lead_id' || $this->get('name') == 'program_name' || $this->get('name') == 'appointment_type' || $this->get('name') == 'non_conforming_params' || $this->get('name') == 'non_conforming' || $this->get('name') == 'warm_transfer' || $this->get('name') == 'segment' || $this->get('name') == 'segment_used' || $this->get('name') == 'segment_desc') {
           return true;
        }

        return parent::isEditable();
    }

    /**
     * Function to get all the available picklist values for the current field
     * @return <Array> List of picklist values if the field is of type picklist or multipicklist, null otherwise.
     */
    public function getPicklistValues()
    {
        if ($this->get('name') == 'brand') {
           return ['AVL' => 'AVL', 'NVL' => 'NVL'];
        }
        if ($this->get('name') == 'subagrmt_cod') {
            $lov = [];
            for ($i = 1; $i <= 999; $i++) {
                $ref = str_pad($i, 3, '0', STR_PAD_LEFT);
                $lov[$ref] = $ref;
            }
            return $lov;
        }

        return parent::getPicklistValues();
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
        if ($this->get('name') == 'brand' && $value == '') {
           return 'AVL';
        }

        return parent::getDBInsertValue($value);
    }

    /**
     * Function whcih will get the databse insert value format from user format
     *
     * @param type $value in user format
     *
     * @return type
     */
    public function isEmptyPicklistOptionAllowed()
    {
        if ($this->get('name') == 'brand') {
           return false;
        }

        return parent::isEmptyPicklistOptionAllowed();
    }

    /**
     * Function to get Default Field Value
     * @return <String> defaultvalue
     */
    public function getDefaultFieldValue()
    {
        if ($this->get('name') == 'sales_stage') {
            return 'Prospecting';
        }
        return parent::getDefaultFieldValue();
    }
}
