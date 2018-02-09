<?php
/**
 * Created by PhpStorm.
 * User: mmuir
 * Date: 11/2/2017
 * Time: 5:07 PM
 */
class WFDriverCond_Field_Model extends WFWarehouseCond_Field_Model {
    // Referring to parent getPicklistValues to handle the custom multipicklists
    public function getPicklistValues() {
        return parent::getPicklistValues();
    }

}
