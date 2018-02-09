<?php
class LocationTypes_Module_Model extends Vtiger_Module_Model {

    function getDuplicateCheckFields() {
        return Zend_Json::encode(array('location_types','location_warehouse'));
    }

}
