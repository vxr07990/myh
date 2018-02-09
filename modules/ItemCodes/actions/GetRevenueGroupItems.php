<?php
class ItemCodes_GetRevenueGroupItems_Action extends Vtiger_Action_Controller
{
    public function checkPermission(Vtiger_Request $request)
    {
        return;
    }
    public function process(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $agentid = $request->get('agentId');

        $items = [];
        if(!empty($agentid)){
            global $adb;
            $sql = "SELECT DISTINCT revenuegroup
                    FROM vtiger_revenuegroupingitem AS RG
                    INNER JOIN vtiger_crmentity AS ERG ON ERG.crmid = RG.revenuegroupingitemid
                    INNER JOIN vtiger_crmentity AS EA ON EA.crmid = RG.revenuegroupingitem_relcrmid
                    WHERE EA.agentid = ? AND ERG.deleted = 0";
            $rs = $adb->pquery($sql,[$agentid]);
            while ($row = $adb->fetchByAssoc($rs)){
                $items[$row['revenuegroup']]= vtranslate($row['revenuegroup'],$moduleName);
            }
        }
        $response = new Vtiger_Response();
        $response->setResult($items);
        $response->emit();
    }
}
