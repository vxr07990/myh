<?php
class Quotes_SavePackingItem_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function process(Vtiger_Request $request)
    {
        $db = PearDatabase::getInstance();
        
        $recordId = $request->get('record');
        preg_match('/\d/', $request->get('name'), $m, PREG_OFFSET_CAPTURE);
        $itemId = substr($request->get('name'), $m[0][1]);
        $itemType = substr($request->get('name'), 0, $m[0][1]);
        
        $sql = "SELECT * FROM `vtiger_packing_items` WHERE quoteid=? AND itemid=?";
        $params[] = $recordId;
        $params[] = $itemId;
        
        $result = $db->pquery($sql, $params);
        $row = $result->fetchRow();
        
        if ($row == null) {
            //Relation does not exist
            $sql = "INSERT INTO `vtiger_packing_items` (quoteid, itemid, ".$itemType."_qty) VALUES (?,?,?)";
            $params[] = $request->get('qty');
        } else {
            $sql = "UPDATE `vtiger_packing_items` SET ".$itemType."_qty=? WHERE quoteid=? AND itemid=?";
            unset($params);
            $params[] = $request->get('qty');
            $params[] = $recordId;
            $params[] = $itemId;
        }
        
        $result = $db->pquery($sql, $params);
        
        $response = new Vtiger_Response();
        $response->setResult('1');
        $response->emit();
    }
}
