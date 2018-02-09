<?php
class Estimates_DeleteMiscItem_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function process(Vtiger_Request $request)
    {
        $db     = PearDatabase::getInstance();
        $params = [];
        //@TODO: sanitize inputs?
        $itemType = $request->get('rowType');
        //create an error state so we don't do a bad sql query
        $error = false;
        
        if ($itemType == 'flatItemRow' || $itemType == 'qtyRateItemRow') {
            $sql      = "DELETE FROM `vtiger_misc_accessorials` WHERE line_item_id=?";
            $params[] = $request->get('lineItemId');
        } elseif ($itemType == 'vehicleItem vehicleRow') {
            $sql      = "DELETE FROM `vtiger_quotes_vehicles` WHERE vehicle_Id=?";
            $params[] = $request->get('lineItemId');
        } elseif ($itemType == 'crateRow') {
            $sql      = "DELETE FROM `vtiger_crates` WHERE line_item_id=?";
            $params[] = $request->get('lineItemId');
        } elseif ($itemType == 'localCrateRow') {
            $sql = "DELETE FROM `vtiger_quotes_crating` WHERE line_item_id=? AND estimateid=? AND serviceid=?";
            list($serviceId, $lineItemId) = explode('-', $request->get('lineItemId'));
            $estimateId = $request->get('estimateid');
            $serviceId  = preg_replace('/[^0-9]/', '', $serviceId);
            //make sure all these are non-zero.
            if ($lineItemId > 0 && $estimateId > 0 && $serviceId > 0) {
                $params[] = $lineItemId;
                $params[] = $estimateId;
                $params[] = $serviceId;
            } else {
                //this gives no error info, maybe we need a message.
                $error = true;
            }
        } else {
            $error = true;
        }
        
        $response = new Vtiger_Response();
        if ($error) {
            //if there's an error don't do say it's fine.
            //@todo requires some more thought because if no estimateid for local don't do query but return OK
            //$response->setResult('0');
            $response->setResult('1');
        } else {
            $db->pquery($sql, $params);
            $response->setResult('1');
        }
        $response->emit();
    }
}
