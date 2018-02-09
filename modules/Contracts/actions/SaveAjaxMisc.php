<?php
class Contracts_SaveAjaxMisc_Action extends Vtiger_Save_Action
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
        $miscItem = explode('-', $request->get('name'));
        $field;
        switch ($miscItem[0]) {
            case "MiscFlatChargeOrQtyRate":
                $field ='is_quantity_rate';
                break;
            case "MiscDescription":
                $field ='description';
                break;
            case "MiscRate":
                $field ='rate';
                break;
            case "MiscQty":
                $field ='quantity';
                break;
            case "MiscDiscounted":
                $field ='discounted';
                break;
            case "MiscDiscount":
                $field ='discount';
                break;
        }
        
        $sql = 'UPDATE `vtiger_contracts_misc_items` SET `'.$field.'` = ? WHERE `contracts_misc_id` = ?';
        $result = $db->pquery($sql, array($request->get('value'), $request->get('id')));

        $response = new Vtiger_Response();
        $response->setResult('1');
        $response->emit();
    }
}
