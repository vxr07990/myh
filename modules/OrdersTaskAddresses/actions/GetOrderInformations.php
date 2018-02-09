<?php
class OrdersTaskAddresses_GetOrderInformations_Action extends Vtiger_Action_Controller{
    public function checkPermission(Vtiger_Request $request)
    {
        return;
    }
    function process(Vtiger_Request $request)
    {
        $orderId = $request->get('order_id');
        $extraStops = [];
        if(!empty($orderId)){
            $extraStopsModel = Vtiger_Module_Model::getInstance('ExtraStops');
            if ($extraStopsModel && $extraStopsModel->isActive()) {
                $extraStops = $extraStopsModel->getStops($orderId);
            }
            $orderRecord = Vtiger_Record_Model::getInstanceById($orderId);
            $orderModule = $orderRecord->getModule();
            $addressBlock = Vtiger_Block_Model::getInstance('LBL_ORDERS_ORIGINADDRESS',$orderModule);
            $addressFields = $addressBlock->getFields();
            $addresses = [];
            foreach ($addressFields as $fieldName=>$fieldModel){
                $addresses[$fieldName] = $orderRecord->get($fieldName);
            }

        }
        $results['extra_stops'] = $extraStops;
        $results['addresses'] = $addresses;
        $response = new Vtiger_Response();
        $response->setResult($results);
        $response->emit();
    }
}