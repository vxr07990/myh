<?php
class Estimates_SaveVehicles_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function process(Vtiger_Request $request)
    {
        $db = PearDatabase::getInstance();
        $updateValue = '';
        $updateField = '';
        if (explode('-', $request->get('name'))[0] == 'vehicleDescription') {
            $updateField = 'description';
            $updateValue = $request->get('vehicleDesc');
        } elseif (explode('-', $request->get('name'))[0] == 'vehicleWeight') {
            $updateField = 'weight';
            $updateValue = $request->get('vehicleWeight');
        }

        if ($request->get('saveNew') == 'true') {
            $sql = "INSERT INTO `vtiger_quotes_vehicles` (estimateid, description, weight) VALUES (?,?,?)";
            $result = $db->pquery($sql, [$request->get('record'), $request->get('vehicleDesc'), $request->get('vehicleWeight')]);
        } else {
            $sql = "UPDATE `vtiger_quotes_vehicles` SET " . ($updateField = 'weight' ? $updateField : 'description') . " =? WHERE vehicle_id=?";
            $result = $db->pquery($sql, [$updateValue, $request->get('vehicleid')]);
        }
        $response = new Vtiger_Response();
        //$response->setResult($updateValue);
        $response->setResult($request->get('saveNew'));
        $response->emit();
    }
}
