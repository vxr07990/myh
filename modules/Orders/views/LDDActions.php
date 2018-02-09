<?php
include_once 'include/Webservices/Retrieve.php';
include_once 'include/Webservices/Create.php';
include_once 'include/Webservices/Delete.php';

class Orders_LDDActions_View extends Vtiger_ListAjax_View
{
    public function __construct()
    {
        parent::__construct();
        $this->exposeMethod('updateOnHoldStatus');
        $this->exposeMethod('updateApuStatus');
        $this->exposeMethod('createOverflow');
    }

    public function updateOnHoldStatus($request)
    {
        $ordersId = $request->get('ordersid');
        $onHold   = $request->get('on-hold');
        try {
            $ordersRecodModel = Vtiger_Record_Model::getInstanceById($ordersId, 'Orders');
            $ordersRecodModel->set('mode', 'edit');
            if ($onHold == 'yes') {
                $ordersRecodModel->set('orders_onhold', 1);
            } else {
                $ordersRecodModel->set('orders_onhold', 0);
            }
            $ordersRecodModel->save();
            $result = [
                'result' => 'ok',
            ];
            $msg    = new Vtiger_Response();
            $msg->setResult($result);
            $msg->emit();
        } catch (Exception $exc) {
            $result           = [];
            $result['result'] = 'false';
            $result['msg']    = $exc->getTraceAsString();
            $msg              = new Vtiger_Response();
            $msg->setResult($result);
            $msg->emit();
        }
    }

    public function updateApuStatus($request)
    {
        $user     = Users_Record_Model::getCurrentUserModel();
        $ordersId = $request->get('ordersid');
        $apu      = $request->get('apu');
        $apu_date = $request->get('apu_date');
        if ($apu == 'yes') {
            //need to create the new order task
            //Get the current order information
            try {
                $orderWsId         = vtws_getWebserviceEntityId('Orders', $ordersId);
                $newOrdersTaskData = vtws_retrieve($orderWsId, $user);
                unset($newOrdersTaskData['id']);
                unset($newOrdersTaskData['participating_agent']);
                $newOrdersTaskData['ordersid']            = $orderWsId;
                $newOrdersTaskData['participating_agent'] = $this->getOrderTaskOriginAgent($newOrdersTaskData['ordersid']);
                $newOrdersTaskData['dispatch_status']     = '--';
                $newOrdersTaskData['orderstaskname']      = $newOrdersTaskData['orders_no'].' - Agent Pickup';
                $newOrdersTaskData['operationtasktype']   = 'Origin Services';
                $newOrdersTaskData['servicenameoptions']  = 'APU';
                unset($newOrdersTaskData['orderstask_no']);
                $newOrdersTaskData['pref_date_service'] = DateTimeField::convertToDBFormat($apu_date);
                $newOrdersTaskData['service_date_from'] = DateTimeField::convertToDBFormat($apu_date);
                $newOrdersTaskData['service_date_to']   = DateTimeField::convertToDBFormat($apu_date);
                if (!$newOrdersTaskData['participating_agent']) {
                    throw new WebServiceException(10001, vtranslate('No Origin Agent selected for order', 'OrdersTasks'));
                }
                $entity = vtws_create('OrdersTask', $newOrdersTaskData, $user);
                if (!$entity) {
                    throw new WebServiceException(10002, vtranslate('Could not create the new Order Tasks - Error Unknown', 'OrdersTasks'));
                }
                $newOrderTasksId           = explode('x', $entity['id'])[1];
                $orderUpdate['id']         = $orderWsId;
                $orderUpdate['orders_apu'] = 1;
                vtws_revise($orderUpdate, $user);
                $result            = [];
                $result['created'] = 'true';
                $result['msg']     = vtranslate('New Order Task Created: ', 'OrdersTasks').$entity[orderstask_no];
                $msg               = new Vtiger_Response();
                $msg->setResult($result);
                $msg->emit();
            } catch (Exception $exc) {
                $result            = [];
                $result['created'] = 'false';
                $result['msg']     = $exc->message;
                $msg               = new Vtiger_Response();
                $msg->setResult($result);
                $msg->emit();
            }
        } else {
            try {
                $db     = PearDatabase::getInstance();
                $result = $db->pquery("SELECT relcrmid FROM vtiger_crmentityrel
                        INNER JOIN vtiger_crmentity ON vtiger_crmentityrel.relcrmid = vtiger_crmentity.crmid 
                        WHERE deleted=0 AND vtiger_crmentityrel.crmid =? AND relmodule='OrdersTasks'",
                                      [$ordersId]);
                if ($db->num_rows($result) > 0) {
                    $orderTasksWsId = vtws_getWebserviceEntityId('OrdersTask', $db->query_result($result, 0, 'relcrmid'));
                } else {
                    throw new WebServiceException(10002, vtranslate('Could not find the APU Task', 'OrdersTasks'));
                }
                vtws_delete($orderTasksWsId, $user);
                $result            = [];
                $result['created'] = 'true';
                $result['msg']     = vtranslate('The related Pick Up task was deleted ', 'OrdersTasks');
                $msg               = new Vtiger_Response();
                $msg->setResult($result);
                $msg->emit();
            } catch (Exception $exc) {
                $result            = [];
                $result['created'] = 'false';
                $result['msg']     = $exc->message;
                $msg               = new Vtiger_Response();
                $msg->setResult($result);
                $msg->emit();
            }
        }
    }

    public function getOrderTaskOriginAgent($ordersId)
    {
        $db       = PearDatabase::getInstance();
        $ordersId = explode('x', $ordersId)[1];
        $result   = $db->pquery("SELECT agents_id FROM vtiger_participatingagents WHERE rel_crmid=? AND agent_type='Origin Agent' AND deleted=0", [$ordersId]);
        if ($db->num_rows($result) > 0) {
            return vtws_getWebserviceEntityId('Agents', $db->query_result($result, 0, 'agents_id'));
        } else {
            return false;
        }
    }

    public function createOverflow($request)
    {
        $user    = Users_Record_Model::getCurrentUserModel();
        $orderId = $request->get('order_id');
        try {
            $orderWsId              = vtws_getWebserviceEntityId('Orders', $orderId);
            $newOrder               = vtws_retrieve($orderWsId, $user);
            $newOrder['orders_no']  = $newOrder['orders_no'].' O/F';
            $newOrder['ordersname'] = $newOrder['orders_no'];
            unset($newOrder['id']);
            $entity = vtws_create('Orders', $newOrder, $user);
            if (!$entity) {
                throw new WebServiceException(10002, vtranslate('Could not create the new Order Tasks - Error Unknown', 'OrdersTasks'));
            }
            $result            = [];
            $result['created'] = 'true';
            $result['msg']     = vtranslate('The new order was succesfully created:  ', 'OrdersTasks').$newOrder['orders_no'];
            $msg               = new Vtiger_Response();
            $msg->setResult($result);
            $msg->emit();
        } catch (Exception $exc) {
            $result            = [];
            $result['created'] = 'false';
            $result['msg']     = $exc->message;
            $msg               = new Vtiger_Response();
            $msg->setResult($result);
            $msg->emit();
        }
    }
}
