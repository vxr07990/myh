<?php

class Estimates_SaveTariff_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        $recordId = $request->get('record');
        $tariffId = $request->get('tariffId');
        
        $db = PearDatabase::getInstance();
        $sql = "UPDATE `vtiger_quotes` SET effective_tariff=? WHERE quoteid=?";
        
        $result = $db->pquery($sql, array($tariffId, $recordId));
        
        $sql = "SELECT tariffmanagername FROM `vtiger_tariffmanager` WHERE tariffmanagerid=?";
        
        $result = $db->pquery($sql, array($tariffId));
        $row = $result->fetchRow();
        
        if ($row != null) {
            $info = array('name'=>$row[0]);
        } else {
            $info = array('name'=>'');
        }
        
        $response = new Vtiger_Response();
        $response->setResult($info);
        $response->emit();
    }
}
