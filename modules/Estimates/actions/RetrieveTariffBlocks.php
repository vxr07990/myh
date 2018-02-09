<?php

class Estimates_RetrieveTariffBlocks_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        $tariffId = $request->get('tariffid');
        $module = $request->getModule();
        $db = PearDatabase::getInstance();
        
        $currentTariffBlocks = array();
        $blocksToHide = array();
        
        if (isset($tariffId) && !empty($tariffId)) {
            $sql = "SELECT blocklabel, show_block FROM `vtiger_tariff_blockrel` JOIN `vtiger_blocks` ON vtiger_tariff_blockrel.blockid=vtiger_blocks.blockid WHERE tariffid=?";
            
            //file_put_contents('logs/TariffBlockEdit.log', date('Y-m-d H:i:s - ')."Preparing to run SQL query: $sql\n", FILE_APPEND);
            $result = $db->pquery($sql, array($tariffId));
            
            while ($row =& $result->fetchRow()) {
                $currentTariffBlocks[$row[0]] = $row[1];
            }
        }
        
        $sql = "SELECT vtiger_blocks.blocklabel FROM `vtiger_tariff_blockrel` JOIN `vtiger_blocks` ON vtiger_tariff_blockrel.blockid=vtiger_blocks.blockid JOIN `vtiger_tab` ON vtiger_blocks.tabid=vtiger_tab.tabid WHERE name=? AND tariffid!=?";
        
        //file_put_contents('logs/TariffBlockEdit.log', date('Y-m-d H:i:s - ')."Preparing to run SQL query: $sql\n", FILE_APPEND);
        $result = $db->pquery($sql, array($module, $tariffId));
        
        while ($row =& $result->fetchRow()) {
            $blocksToHide[] = $row[0];
        }
        
        $info = array();
        $info['currentTariffBlocks'] = $currentTariffBlocks;
        $info['inactiveTariffBlocks'] = $blocksToHide;

        $response = new Vtiger_Response();
        $response->setResult($info);
        $response->emit();
    }
}

?>