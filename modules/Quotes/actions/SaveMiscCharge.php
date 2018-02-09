<?php
class Quotes_SaveMiscCharge_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function process(Vtiger_Request $request)
    {
        $db = PearDatabase::getInstance();
        $params = array();
        $row = null;
        $line_item_id = $request->get('line_item_id');
        
        if ($line_item_id != '') {
            $sql = "SELECT line_item_id FROM `vtiger_misc_accessorials` WHERE line_item_id=?";
            $params[] = $line_item_id;
            
            $result = $db->pquery($sql, $params);
            unset($params);
            
            $row = $result->fetchRow();
        }
        if ($row == null) {
            //Update line_item_id from vtiger_misc_accessorials_seq and increment id value in table
            $sql = "UPDATE `vtiger_misc_accessorials_seq` SET id=id+1";
            $result = $db->pquery($sql, $params);
            
            $sql = "SELECT id FROM `vtiger_misc_accessorials_seq`";
            $result = $db->pquery($sql, $params);
            $row = $result->fetchRow();
            $line_item_id = $row[0];
            
            //Create new item
            $sql = "INSERT INTO `vtiger_misc_accessorials` (quoteid, description, charge, qty, discounted, discount, charge_type, line_item_id) VALUES (?,?,?,?,?,?,?,?)";
        } else {
            //Update item
            $sql = "UPDATE `vtiger_misc_accessorials` SET quoteid=?, description=?, charge=?, qty=?, discounted=?, discount=?, charge_type=? WHERE line_item_id=?";
        }
        
        $params[] = $request->get('record');
        $params[] = $request->get('description');
        $params[] = $request->get('charge');
        $params[] = $request->get('qty');
        $params[] = $request->get('discounted');
        $params[] = $request->get('discount');
        $params[] = $request->get('type');
        $params[] = $line_item_id;
        
        $result = $db->pquery($sql, $params);
        
        //file_put_contents('logs/MiscChargeSave.log', print_r($result, true), FILE_APPEND);

        $response = new Vtiger_Response();
        $response->setResult($line_item_id);
        $response->emit();
    }
}
