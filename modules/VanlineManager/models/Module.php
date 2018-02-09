<?php

class VanlineManager_Module_Model extends Vtiger_Module_Model
{
    public static function getAllRecords()
    {
        $db = PearDatabase::getInstance();
        
        $sql = "SELECT vanlinemanagerid FROM `vtiger_vanlinemanager` JOIN `vtiger_crmentity` ON vanlinemanagerid=crmid WHERE deleted=0";
        $result = $db->pquery($sql, array());
        
        $records = array();
        
        while ($row =& $result->fetchRow()) {
            $records[] = Vtiger_Record_Model::getInstanceById($row[0]);
        }
        
        return $records;
    }
    
    public function isSummaryViewSupported()
    {
        return false;
    }
    //remove module from quickcreate dropdown list
    public function isQuickCreateSupported()
    {
        return false;
    }
}
