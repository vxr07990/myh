<?php
class Estimates_SaveBulkyItem_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function process(Vtiger_Request $request)
    {
        $db = PearDatabase::getInstance();
        
        $recordId = $request->get('record');
        $itemId = substr($request->get('name'), 5);
        
        $sql = "SELECT * FROM `vtiger_bulky_items` WHERE quoteid=? AND bulkyid=?";
        $params[] = $recordId;
        $params[] = $itemId;
        
        $result = $db->pquery($sql, $params);
        $row = $result->fetchRow();
        
        if ($row == null) {
            //Relation does not exist
            $sql = "INSERT INTO `vtiger_bulky_items` VALUES (?,?,?)";
            $params[] = $request->get('qty');
        } else {
            $sql = "UPDATE `vtiger_bulky_items` SET ship_qty=? WHERE quoteid=? AND bulkyid=?";
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
