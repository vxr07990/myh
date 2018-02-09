<?php

class Media_Record_Model extends Vtiger_Record_Model {

    public function getRelatedRecords()
    {
        $db = PearDatabase::getInstance();
        $sql = "SELECT * FROM `vtiger_mediarel` JOIN `vtiger_crmentity` ON `vtiger_mediarel`.crmid=`vtiger_crmentity`.crmid WHERE mediaid=? AND deleted=0";
        $params[] = $this->get('id');

        $result = $db->pquery($sql, $params);

        $recordIds = array();
        while ($row = $result->fetchRow()) {
            $recordIds[] = array('id'=>$row['crmid'], 'module'=>$row['setype'], 'label'=>$row['label'], 'link'=>'index.php?module='.$row['setype'].'&relatedModule=Media&view=Detail&record='.$row['crmid'].'&mode=showRelatedList&tab_label=Media');
        }

        return $recordIds;
    }
}
