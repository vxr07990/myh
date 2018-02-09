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
 * WFLocations Field Model Class
 */
class WFLocations_Field_Model extends Vtiger_Field_Model
{

    public function getPicklistValues($baseLocation = NULL) {
        $fieldDataType = $this->getFieldDataType();
        if ($fieldDataType == 'multipicklistall') {
            if ($this->getName() == 'base_slot') {
                if(!$baseLocation){
                    $record = Vtiger_Record_Model::getInstanceById($_REQUEST['record']);
                    if($record){
                        $baseLocation = $record->get('wflocation_base');
                    }
                }
                $baseLocationInstance = Vtiger_Record_Model::getInstanceById($baseLocation);
                $slotType = Vtiger_Record_Model::getInstanceById($baseLocationInstance->get('wfslot_configuration'));
                $pickListReturn = [];
                for($i=1; $i<=6; $i++){
                    $label = $slotType->get('label'.$i);
                    if($label) {
                        $pickListReturn[$label] = $label;
                    } else {
                        break;
                    }
                }
                return $pickListReturn;
            } else {
                return parent::getPicklistValues();
            }
        } else {
            return parent::getPicklistValues();
        }
    }
}
