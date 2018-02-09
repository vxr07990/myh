<?php

class WFCostCenters_Module_Model extends Vtiger_Module_Model{
    public function getSearchRecordsQuery($searchValue, $parentId=false, $parentModule=false)
    {
        $db = PearDatabase::getInstance();
        if ($parentId && in_array($parentModule, array('WFAccounts'))) {
            $query = "SELECT * FROM vtiger_crmentity
						INNER JOIN vtiger_wfcostcenters ON vtiger_wfcostcenters.wfcostcentersid = vtiger_crmentity.crmid
						WHERE deleted = 0 AND vtiger_wfcostcenters.accounts = '" . $db->sql_escape_string($parentId) . "' AND label like '%" . $db->sql_escape_string($searchValue) . "%'";
            return $query;
        }
        return parent::getSearchRecordsQuery($parentId, $parentModule);
    }

    function getDuplicateCheckFields() {
        return Zend_Json::encode(array('code','accounts'));
    }
}
