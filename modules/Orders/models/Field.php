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
 * Vtiger Field Model Class
 */
class Orders_Field_Model extends Vtiger_Field_Model
{
    public function isEmptyPicklistOptionAllowed()
    {
        if($this->getName() == 'ordersstatus'){
            return false;
        }
        //Military Q fields to be exluded
        $militaryFields = [
            'q4',
            'q5',
            'q6',
            'q7',
            'q8',
            'q9'
        ];
        if (in_array($this->getName(), $militaryFields)) {
            return false;
        }

        return parent::isEmptyPicklistOptionAllowed();
    }
  

    /**
     * Function to retieve display value for a value
     *
     * @param  <String> $value - value which need to be converted to display value
     *
     * @return <String> - converted display value
     */
    public function getDisplayValue($value, $record = false, $recordInstance = false)
    {
        
        if($this->name != 'origin_zone' && $this->name != 'empty_zone'){
            return parent::getDisplayValue($value, $record, $recordInstance);
        }else{
            return ZoneAdmin_Module_Model::getZoneAdminDisplayValue($value);
        }
    }
}
