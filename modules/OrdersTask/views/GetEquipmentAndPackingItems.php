<?php

class OrdersTask_GetEquipmentAndPackingItems_View extends Vtiger_IndexAjax_View {
    function process(Vtiger_Request $request) {

        $orderId = $request->get('orderId');
        $results = [];
        if($orderId){
            $results['equipment_items'] = OrdersTask_Record_Model::getEquipmentItems($orderId);
        }
        $results['packing_items'] = OrdersTask_Record_Model::getPackingItems($orderId, ['fields' => []]);

        $response = new Vtiger_Response();
        $response->setResult($results);
        $response->emit();

    }
}