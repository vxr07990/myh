<?php

class Orders_SaveCancel_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function process(Vtiger_Request $request)
    {
        $modalAction = $request->get('modal_action');
        $action = ($modalAction == "cancel") ? "Cancelled" : "Uncancelled";
        $ordersStatus = ($modalAction == "cancel") ? "Cancelled" : "";
        $reason = $request->get('reason');
        $orderID = $request->get('order_id');
        $userID = $request->get('user_id');
        
        $dateTimeArr = (explode(" ", $request->get('datetime')));
        $formatedDate = DateTimeField::convertToDBFormat($dateTimeArr[0]);
        $dateTime = $formatedDate . " " . $dateTimeArr[1];

        $db = PearDatabase::getInstance();
        $db->pquery("UPDATE vtiger_orders SET ordersstatus = ? WHERE ordersid = ?", array($ordersStatus, $orderID));
        $db->pquery("INSERT INTO vtiger_orders_cancelation_log(ordersid, action, reason, user, datetime) VALUES(?,?,?,?,?)", array($orderID, $action, $reason, $userID, $dateTime));
               
        $response = new Vtiger_Response();
        $response->setResult("OK");
        $response->emit();
    }
}
