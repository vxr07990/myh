<?php

class AddressSegments_Field_Model extends Vtiger_Field_Model
{
    public function getPicklistValues()
    {
        if ($this->getName() =='addresssegments_origin' || $this->getName() == 'addresssegments_destination') {
            $picklistValues = Vtiger_Util_Helper::getPickListValues('extrastops_type');
            foreach ($picklistValues as $value) {
                $fieldPickListValues[$value] = vtranslate($value, 'ExtraStops');
            }
            $fieldPickListValues['Origin'] = vtranslate('Origin', 'ExtraStops');
            $fieldPickListValues['Destination'] = vtranslate('Destination', 'ExtraStops');
            return $fieldPickListValues;
        } elseif ($this->getName() == 'addresssegments_sequence') {
            $fieldPickListValues[1]=1;
            $picklistValues = Vtiger_Util_Helper::getPickListValues('extrastops_sequence');
            foreach ($picklistValues as $value) {
                $fieldPickListValues[$value] = vtranslate($value, 'ExtraStops');
            }
            return $fieldPickListValues;
        } else {
            return parent::getPicklistValues();
        }
    }
}
