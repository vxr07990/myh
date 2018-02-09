<?php
class Contracts_SaveAjaxNewMisc_Action extends Vtiger_Save_Action
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function process(Vtiger_Request $request)
    {
        if (!$db) {
            $db = PearDatabase::getInstance();
        }
        
        $params = array(
                        $request->get('Record'),
                        $request->get('MiscFlatChargeOrQtyRate'),
                        $request->get('MiscDescription'),
                        $request->get('MiscRate'),
                        $request->get('MiscQty'),
                        $request->get('MiscDiscounted'),
                        ($request->get('MiscDiscount') != '' ? $request->get('MiscDiscount') : '0.0')
        );
        
        $sql = "INSERT INTO `contracts`.`vtiger_contracts_misc_items` (`contracts_misc_id`, `contractsid`, `is_quantity_rate`, `description`, `rate`, `quantity`, `discounted`, `discount`) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?)";
        $result = $db->pquery($sql, $params);
        
        $sql = "SELECT MAX(`contracts_misc_id`) FROM `vtiger_contracts_misc_items` WHERE `contractsid` = ?";
        $result = $db->pquery($sql, array($request->get('Record')));
        $returnId = $result->fetchRow();

        //file_put_contents('logs/devLog.log', $returnId[0], FILE_APPEND);
        $response = new Vtiger_Response();
        $response->setResult($returnId[0]);
        $response->emit();
    }
}
