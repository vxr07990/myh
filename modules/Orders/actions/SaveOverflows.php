<?php

include_once 'include/Webservices/Create.php';
include_once 'include/Webservices/Retrieve.php';
include_once 'include/Webservices/Delete.php';

class Orders_SaveOverflows_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
        $this->exposeMethod('saveOverflows');
    }

    public function process(Vtiger_Request $request)
    {
        $mode = $request->getMode();
        if (!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
    }

    //@TODO: Remove ordersname as mandatory field¡?
    public function saveOverflows(Vtiger_Request $request)
    {
        $user = Users_Record_Model::getCurrentUserModel();
        $newOrderID = 0;
        try {
           // $mainOrderID = $request->get('order_id');//vtws_getWebserviceEntityId('Orders', $request->get('order_id'));
            $mainOrderID         = vtws_getWebserviceEntityId('Orders', $request->get('order_id'));

            $mainOrder = vtws_retrieve($mainOrderID, $user);
            $overflow = $mainOrder;
	    $overflow['ordersname'] = '??'; //VGS - This field is mandatory is not shown in the UI.?
            $overflow['orders_ecube'] = $request->get('orders_ecube');
            $overflow['orders_elinehaul'] = $request->get('orders_elinehaul');
            $overflow['orders_eweight'] = $request->get('orders_eweight');
            $overflow['description'] = $request->get('description');

            $sql = 'INSERT INTO vtiger_orders_overflow_sequence (orderid) VALUES (?) ON DUPLICATE KEY UPDATE sequence = LAST_INSERT_ID(sequence+1)';
            $db = &PearDatabase::getInstance();
            $db->pquery($sql, [$request->get('order_id')]);
            $nextSeq = $db->getLastInsertID();
            if (!$nextSeq) {
                $nextSeq = 1;
            }

            //OT17511 -- Do not transfer Trips when creating an overflow order.
			$overflow['orders_trip'] = '';
            $overflow['orders_no'] = $mainOrder['orders_no'] . ' O/F' . $nextSeq;

            //OT17609 -- Do not transfer Dispatch Status when creating an overflow order.
            $overflow['orders_otherstatus'] = '';

            $ordersNo = $mainOrder['orders_no'] . ' O/F' . $nextSeq;

            $entity = vtws_create('Orders', $overflow, $user);

            if (!$entity) {
                throw new WebServiceException(10002, vtranslate('Could not create the new Order - Error Unknown', 'Orders'));
            }

            $newOrderID = explode('x', $entity['id'])[1];

            $db = PearDatabase::getInstance();
            $db->pquery('UPDATE vtiger_orders SET orders_no=? WHERE ordersid=?', array($ordersNo, $newOrderID));

            $vehicles = $request->get('vehicles');
            foreach ($vehicles as $vehicle) {
                $vehicleTransportation['vehicletrans_description'] = $vehicle['vin'];
                $vehicleTransportation['vehicletrans_modelyear'] = $vehicle['year'];
                $vehicleTransportation['vehicletrans_model'] = $vehicle['model'];
                $vehicleTransportation['vehicletrans_make'] = $vehicle['make'];
                $vehicleTransportation['vehicletrans_relcrmid'] = vtws_getWebserviceEntityId('Orders', $newOrderID);
                $vehicleTransportation['vehicletrans_type'] = $vehicle['transptype'];
                $vehicleTransportation['vehicletrans_ratingtype'] = $vehicle['ratingtype'];
                $vehicleTransportation['assigned_user_id'] = vtws_getWebserviceEntityId('Users', $user->id);
                $vehicleTransportation['agentid'] = $overflow['agentid'];

                $entity = vtws_create('VehicleTransportation', $vehicleTransportation, $user);
                if (!$entity) {
                    throw new WebServiceException(10002, vtranslate('Could not create the new Vehicle Transportation - Error Unknown', 'Vehicle Transportation'));
                }
            }

            $result = [];
            $result['created'] = 'true';
            $result['msg'] = vtranslate('The new order was succesfully created:  ', 'Orders') . $entity['orders_no'];
            $result['order_id'] = $newOrderID;

            // an overflow can't have more than one parent, so we can use this
            CRMEntity::UpdateRelation($newOrderID, 'Orders', $request->get('order_id'), 'Orders');

            $msg = new Vtiger_Response();
            $msg->setResult($result);
            $msg->emit();
        } catch (Exception $exc) {
            if($newOrderID != 0){
                vtws_delete(vtws_getWebserviceEntityId('Orders', $newOrderID), $user);
            }
            if($exc->getCode() == 'MANDATORY_FIELDS_MISSING'){
                $result = [];
                $result['created'] = 'false';
                $result['msg'] = $this->translateMandatoryFieldError($exc->getMessage());
                $msg = new Vtiger_Response();
                $msg->setResult($result);
                $msg->emit();
            } else {
                $result            = [];
                $result['created'] = 'false';
                $result['msg']     = $exc->getMessage();
                $msg               = new Vtiger_Response();
                $msg->setResult($result);
                $msg->emit();
            }
        }
    }

    public function translateMandatoryFieldError($message, $moduleName = 'Orders'){
        $fieldName = explode(' ', trim($message))[0];
        $module = Vtiger_Module::getInstance($moduleName);
        $field = Vtiger_Field::getInstance($fieldName, $module);
        $label = vtranslate($field->label, $moduleName);
        if($label){
            $message = preg_replace("/".$fieldName."/", $label." field", $message)." in original order.";
        }
        return $message;
    }
}
