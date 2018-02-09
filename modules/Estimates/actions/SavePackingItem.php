<?php
/**
 * @author 			Louis Robinson
 * @file 			SavePackagingItem.php
 * @description 	Extended functionality from the Quotes module so we can add to
 *                  it without having to deal with changing the core vtiger code
 * @contact 		lrobinson@igcsoftware.com
 * @company			IGC Software
 */
class Estimates_SavePackingItem_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * @param pass an object of type Vtiger_Request to the parent class function process()
     */
    public function process(Vtiger_Request $request)
    {
        $db = PearDatabase::getInstance();
        
        $recordId = $request->get('record');
        preg_match('/\d/', $request->get('name'), $m, PREG_OFFSET_CAPTURE);
        $itemId = substr($request->get('name'), $m[0][1]);
        $itemType = substr($request->get('name'), 0, $m[0][1]);
        if ($itemType == 'packCustomRate') {
            $itemType = 'custom_rate';
//		} if($itemType == 'packPackRate'){
//			$itemType = 'pack_rate';
        } else {
            $itemType .= '_qty';
        }
        file_put_contents('logs/devLog.log', "\n itemId : ".$itemId."\n itemType : ".$itemType, FILE_APPEND);
        
        $sql = "SELECT * FROM `vtiger_packing_items` WHERE quoteid=? AND itemid=?";
        $params[] = $recordId;
        $params[] = $itemId;
        
        $result = $db->pquery($sql, $params);
        $row = $result->fetchRow();
        
        if ($row == null) {
            //Relation does not exist
            $sql = "INSERT INTO `vtiger_packing_items` (quoteid, itemid, ".$itemType.") VALUES (?,?,?)";
            $params[] = $request->get('qty');
        } else {
            $sql = "UPDATE `vtiger_packing_items` SET ".$itemType."=? WHERE quoteid=? AND itemid=?";
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
