<?php
/**
 * Created by PhpStorm.
 * User: mmuir
 * Date: 7/24/2017
 * Time: 10:12 AM
 */
class RecordProtection_Field_Model extends Vtiger_Field_Model
{
    public function getPicklistValues(){
        $fieldName = $this->getName();
        if($fieldName == 'module_name'){
            return RecordProtection_Record_Model::getModulePicklistValues();
        }
        if($fieldName == 'flag_name'){
            return RecordProtection_Record_Model::getFlagNamePicklistValues();
        }
        return parent::getPicklistValues();
    }
}
