<?php

class MenuGroups_Field_Model extends Vtiger_Field_Model
{

    public function getPicklistValues()
    {
        if ($this->getName() == 'group_module' && getenv('IGC_MOVEHQ')) {
////        if (strpos($this->getName(), 'group_module') !== false && getenv('IGC_MOVEHQ')) {

            $picklistValues = [];
            $allModelsList = Vtiger_Module_Model::getAll(array('0','2'));
            $picklistValues = MenuGroups_Module_Model::returnMenuModels($allModelsList, true);
            $picklistValues = array_combine($picklistValues, $picklistValues);

            $fieldValue = $this->get('fieldvalue');

            if ($fieldValue) {
                $arrFieldValue = explode(' |##| ', $fieldValue);
                foreach ($arrFieldValue as $value) {
                    $picklistValues[$value] = $value;
                }
            }

            $exist = [];

            // Add surfix is (ModuleName)
            foreach ($picklistValues as $key => $item) {
                $label = vtranslate($item, $key);

                if (in_array($label, $exist)) {
                    $picklistValues[$key] = $label . "({$item})";

                    // Duplicate on before
                    foreach ($exist as $k => $i) {
                        if ($i == $label) {
                            $picklistValues[$k] = $i . "({$k})";
                        }
                    }
                } else {
                    $picklistValues[$key] = $label;
                    $exist[$key] = $label;
                }
            }

            return $picklistValues;
        } else {
            return parent::getPicklistValues();
        }
    }

}
