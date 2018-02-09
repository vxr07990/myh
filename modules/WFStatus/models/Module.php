<?php
class WFStatus_Module_Model extends Vtiger_Module_Model{

    function getDuplicateCheckFields() {
        return Zend_Json::encode(array('wfstatus_code','agentid'));
    }
    public function isCheckBeforeEditDeleteRequired()
    {
        return true;
    }

    public function getTestColumnName(){
        return 'wfstatus_code';
    }

}
