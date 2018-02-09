<?php
class VehicleLookup_DeleteChecklistItem_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        $db = PearDatabase::getInstance();
        
        $itemId = $request->get('itemid');
        
        $sql = "DELETE FROM `vtiger_vehiclelookup_checklist` WHERE itemid=?";
        $db->pquery($sql, array($itemId));
    }
}
