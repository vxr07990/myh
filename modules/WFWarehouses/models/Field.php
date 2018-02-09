<?php
/**
 * Created by PhpStorm.
 * User: mmuir
 * Date: 7/5/2017
 * Time: 9:36 AM
 */
class WFWarehouses_Field_Model extends Vtiger_Field_Model {

    public function getPicklistValues() {
        $fieldDataType = $this->getFieldDataType();
        if ($fieldDataType == 'picklist') {
            //$currentUser = Users_Record_Model::getCurrentUserModel();
            if ($this->getName() == 'agent') {
                $currentUser    = Users_Record_Model::getCurrentUserModel();
                $agents         = $currentUser->getAccessibleAgentsForUser();
                $picklistValues = [];
                foreach ($agents as $key => $val) {
                    $picklistValues[] = $val;
                }
                foreach ($picklistValues as $value) {
                    $fieldPickListValues[$value] = vtranslate($value, $this->getModuleName());
                }
                return $fieldPickListValues;
            }  else {
              return parent::getPicklistValues();
            }
        }
    }
}
