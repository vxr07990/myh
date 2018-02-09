<?php
class TariffServices_DeleteItem_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function process(Vtiger_Request $request)
    {
        $db = PearDatabase::getInstance();
        
        //$itemType = $request->get('rowType');
        $itemType = explode(' ', $request->get('rowType'))[0];
        
        if ($itemType == 'basePlusRow') {
            $table = 'vtiger_tariffbaseplus';
        } elseif ($itemType == 'breakPointRow') {
            $table = 'vtiger_tariffbreakpoint';
        } elseif ($itemType == 'weightMileageRow') {
            $table = 'vtiger_tariffweightmileage';
        } elseif ($itemType == 'bulkyRow') {
            $table = 'vtiger_tariffbulky';
        } elseif ($itemType == 'chargePerHundredRow') {
            $table = 'vtiger_tariffchargeperhundred';
        } elseif ($itemType == 'countyRow') {
            $table = 'vtiger_tariffcountycharge';
        } elseif ($itemType == 'hourlyRow') {
            $table = 'vtiger_tariffhourlyset';
        } elseif ($itemType == 'cartonRow') {
            $table = 'vtiger_tariffpackingitems';
        } elseif ($itemType == 'valuationRow') {
            $table = 'vtiger_tariffvaluations';
        } elseif ($itemType == 'CWTbyWeightRow') {
            $table = 'vtiger_tariffcwtbyweight';
        } elseif ($itemType == 'serviceChargeRow') {
            $table = 'vtiger_tariffservicebasecharge';
        } else {
            return;
        }
        
        $sql = "DELETE FROM $table WHERE line_item_id=?";
        $params[] = $request->get('lineItemId');

        $result = $db->pquery($sql, $params);
        
        $response = new Vtiger_Response();
        $response->setResult('1');
        $response->emit();
    }
}
