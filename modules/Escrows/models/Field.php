<?php

class Escrows_Field_Model extends Vtiger_Field_Model {
    public function getPicklistValues() {
        if ($this->getName() == 'escrows_chargeback_type') {
            global $adb;
            $fieldPickListValues=array();
            $fieldPickListValues['All']='All';
            // get Revenue Grouping Items
            $params=array();
            $query="select DISTINCT vtiger_revenuegroupingitem.revenuegroup from `vtiger_revenuegroupingitem`
                INNER JOIN vtiger_crmentity vtiger_crmentityItem ON vtiger_crmentityItem.crmid=vtiger_revenuegroupingitem.revenuegroupingitemid
                INNER JOIN vtiger_crmentity vtiger_crmentityGroup ON vtiger_crmentityGroup.crmid=vtiger_revenuegroupingitem.revenuegroupingitem_relcrmid
                WHERE vtiger_crmentityItem.deleted=0";
            if(!getenv('DISABLE_REFERENCE_FIELD_LIMIT_BY_OWNER')) {
                if($_REQUEST['agentid']) {
                    $query .= " AND vtiger_crmentityGroup.agentid=?";
                    $params[]=$_REQUEST['agentid'];
                }
            }
            $rs=$adb->pquery($query,$params);
            if($adb->num_rows($rs)>0) {
                while($row=$adb->fetch_array($rs)) {
                    $fieldPickListValues[$row['revenuegroup']]=$row['revenuegroup'];
                }
            }
            return $fieldPickListValues;
        }else{
            return parent::getPicklistValues();
        }
    }
}
