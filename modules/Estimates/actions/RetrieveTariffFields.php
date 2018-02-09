<?php

class Estimates_RetrieveTariffFields_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        $recordId = $request->get('record');
        $tariffId = $request->get('tariffid');
        $module = $request->getModule();
        $db = PearDatabase::getInstance();
        
        $sql = "SELECT fieldid, default_value, is_mandatory, show_field FROM `vtiger_tariff_fieldrel` WHERE tariffid=?";
        
        $result = $db->pquery($sql, array($tariffId));
        
        $currentTariffFields = array();
        $currentModuleFields = array();
        
        while ($row =& $result->fetchRow()) {
            $currentTariffFields[$row[0]] = array('default_value'=>$row[1], 'is_mandatory'=>$row[2], 'show_field'=>$row[3]);
        }
        
        $sql = "SELECT fieldid, default_value, is_mandatory, show_field FROM `vtiger_tariff_fieldrel` JOIN `vtiger_tab` ON vtiger_tariff_fieldrel.tabid=vtiger_tab.tabid WHERE name=? AND tariffid!=?";
        
        $result = $db->pquery($sql, array($module, $tariffId));
        
        while ($row =& $result->fetchRow()) {
            $currentModuleFields[$row[0]] = array('default_value'=>$row[1], 'is_mandatory'=>$row[2], 'show_field'=>$row[3]);
        }
        
        $info = array();
        
        $info['currentTariffFields'] = $currentTariffFields;
        $info['inactiveTariffFields'] = $currentModuleFields;
        
        $response = new Vtiger_Response();
        $response->setResult($info);
        $response->emit();
    }
}
