<?php
class Quotes_DeleteMiscItem_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function process(Vtiger_Request $request)
    {
        $db = PearDatabase::getInstance();
        
        $itemType = $request->get('rowType');
        
        if ($itemType == 'flatItemRow' || $itemType == 'qtyRateItemRow') {
            $table = 'vtiger_misc_accessorials';
        } elseif ($itemType == 'crateRow') {
            $table = 'vtiger_crates';
        }
        
        $sql = "DELETE FROM $table WHERE line_item_id=?";
        $params[] = $request->get('lineItemId');
        
        $result = $db->pquery($sql, $params);
        
        $response = new Vtiger_Response();
        $response->setResult('1');
        $response->emit();
    }
}
