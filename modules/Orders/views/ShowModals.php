<?php

class Orders_ShowModals_View extends Vtiger_IndexAjax_View
{
    public function __construct()
    {
        parent::__construct();
        $this->exposeMethod('showAdd2TripModal');
        $this->exposeMethod('showCreateOverflowModal');
        $this->exposeMethod('showCancelModal');
    }

    public function process(Vtiger_Request $request)
    {
        $mode = $request->get('mode');
        if (!empty($mode)) {
            if ($mode == 'showMove2TripModal') {
                $this->showAdd2TripModal($request);
            } else {
                $this->invokeExposedMethod($mode, $request);
            }

            return;
        }
    }

    public function showCancelModal(Vtiger_Request $request)
    {
        $orderId = $request->get('order_id');
        $action = $request->get('modalaction');
        $cancelReason = array("Transferee Cancelled Move","Competitive Bid","Registered in Error","Pre-Planned Overflow Not Necessary","Customer (Client/Account) Cancelled Move","Overflow Shipment went on Main");
        $dateFormatArray = array("dd-mm-yyyy" => "d-m-Y", "mm-dd-yyyy" => "m-d-Y", "yyyy-mm-dd" => "Y-m-d");
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $dateFormat = $currentUser->get('date_format');

        $viewer = $this->getViewer($request);
        $viewer->assign('MODULENAME', "Orders");
        $viewer->assign('MODAL_ACTION', $action);
        $viewer->assign('RECORD_ID', $orderId);
        $viewer->assign('CURRENT_USER_FULL_NAME', $currentUser->get('first_name').' '.$currentUser->get('last_name'));
        $viewer->assign('CURRENT_USER_ID', $currentUser->getId());
        $viewer->assign('CURRENT_DATE_TIME', date($dateFormatArray[$dateFormat]." H:i:s"));
        if ($action == 'cancel') {
            $viewer->assign('CANCEL_REASON_LIST', $cancelReason);
        }

        echo $viewer->view('CancelModal.tpl', "Orders", true);
    }

    public function showCreateOverflowModal(Vtiger_Request $request)
    {
        $recordModel = Vtiger_Record_Model::getInstanceById($request->get('order_id'), 'Orders');
        $modelData = $recordModel->getData();

        $linehaul = number_format($modelData['orders_elinehaul'], 2, ',', '.');

        $db = PearDatabase::getInstance();
        $result = $db->pquery("SELECT * FROM vtiger_vehicletrans_type", array());
        if ($result && $db->num_rows($result)>0) {
            while ($row = $db->fetchByAssoc($result)) {
                $vehicleType[] = array("id" => $row['vehicletrans_typeid'], "value" => $row['vehicletrans_type']);
            }
        }
        $result = $db->pquery("SELECT * FROM vtiger_vehicletrans_ratingtype", array());
        if ($result && $db->num_rows($result)>0) {
            while ($row = $db->fetchByAssoc($result)) {
                $vehicleRatingType[] = array("val" => $row['vehicletrans_ratingtype'], "text" => $row['vehicletrans_ratingtype']);
            }
        }
        $viewer = $this->getViewer($request);
        $viewer->assign('ORDERID', $request->get('order_id'));
        $viewer->assign('ORDERCUBE', ceil($modelData['orders_ecube']));
        $viewer->assign('ORDERLINEHAUL', $linehaul);
        $viewer->assign('ORDEREWEIGHT', ceil($modelData['orders_eweight']));
        $viewer->assign('MODULENAME', 'Orders');
        $viewer->assign('TYPE_PICKLIST', $vehicleType);
        $viewer->assign('RATINGTYPE_PICKLIST', $vehicleRatingType);
        $viewer->assign('current_user', Users_Record_Model::getCurrentUserModel());

        echo $viewer->view('CreateOverflow.tpl', 'Orders', true);
    }

    public function showAdd2TripModal(Vtiger_Request $request)
    {
        $moduleName       = 'Orders';
        $mode             = $request->get('mode');
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $db               = PearDatabase::getInstance();
        $tripsArray = $this->getTripsList($request);
        $viewer = $this->getViewer($request);
        $viewer->assign('MODULENAME', $moduleName);
        $viewer->assign('TRIPS', $tripsArray);
        $viewer->assign('current_user', $currentUserModel);
        $viewer->assign('orders', $request->get('orderslist'));
        if ($mode == 'showMove2TripModal') {
            $viewer->assign('OLD_TRIP_ID', $request->get('tripid'));
            echo $viewer->view('LDDMove2TripModal.tpl', $moduleName, true);
        } else {
            echo $viewer->view('LDDAdd2TripModal.tpl', $moduleName, true);
        }
    }

    public function getMinEmptyDate($ordersList)
    {
        $db = PearDatabase::getInstance();
        if (is_array($ordersList)) {
            $orders = '('.implode(',', $ordersList).')';
        } else {
            $orders = '('.$ordersList.')';
        }
        $result = $db->pquery("SELECT min(orders_ldate) as mindate FROM vtiger_orders INNER JOIN vtiger_crmentity ON vtiger_orders.ordersid = vtiger_crmentity.crmid
                        WHERE deleted=0 AND ordersid IN $orders");
        if ($db->num_rows($result) > 0) {
            $minDate = $db->query_result($result, 0, 'mindate');

            return date('Y-m-d', strtotime('-5 day', strtotime($minDate)));
        } else {
            return false;
        }
    }

    public function getTripsList(Vtiger_Request $request)
    {
        $db         = PearDatabase::getInstance();
        $mode       = $request->get('mode');
        $ordersList = $request->get('orderslist');
        $tripid     = $request->get('tripid');
        $minEmptyDate = $this->getMinEmptyDate($ordersList);
        if ($mode == 'showMove2TripModal') {
            $result = $db->pquery("SELECT tr.* FROM vtiger_trips tr INNER JOIN vtiger_crmentity cr ON tr.tripsid = cr.crmid 
                     WHERE cr.deleted = 0 AND tr.tripsid <> ?",
                                  [$tripid]);
        } else {
            $sql    = "SELECT tr.* FROM vtiger_trips tr INNER JOIN vtiger_crmentity cr ON tr.tripsid = cr.crmid
                                    WHERE cr.deleted = 0 
                                    AND trips_status != 'Completed' 
                                    AND trips_status != 'Void'";
            $params = [];
            if ($minEmptyDate) {
                $sql .= " AND (empty_date is null OR empty_date > ?)";
                array_push($params, $minEmptyDate);
            }
            $result = $db->pquery($sql, $params);
        }
        $tripsArray = [];
        if ($db->num_rows($result) > 0) {
            while ($row = $db->fetch_row($result)) {
                if (Users_Privileges_Model::isPermitted('Trips', 'EditView', $row['tripsid']) == 'yes') {
                    if(!OutOfService_Module_Model::getOutOfServiceStatus($row['driver_id'])){
                        $aresult = $db->pquery("SELECT agent_number FROM vtiger_agents WHERE agentsid = ?", [$row['agent_unit']]);
                        $aresult = $db->fetch_row($aresult);
                        $trip['tripsid']         = $row['tripsid'];
                        $trip['id_trips']        = $row['trips_id'];
                        $trip['driver_name']     = Vtiger_Functions::getCRMRecordLabel($row['driver_id']);
                        $trip['agent_unit']      = Vtiger_Functions::getCRMRecordLabel($row['agent_unit']);
                        $trip['agent_number']    = $aresult['agent_number'];
                        $trip['origin_zone']     = $row['origin_zone'];
                        $trip['origin_state']    = $row['origin_state'];
                        $trip['intransitzone']   = $row['intransitzone'];
                        $trip['empty_zone']      = $row['empty_zone'];
                        $trip['empty_state']     = $row['empty_state'];
                        $trip['empty_date']      = $row['empty_date'];
                        $trip['planning_notes']  = $row['planning_notes'];
                        $trip['dispatch_notes']  = $row['dispatch_notes'];
                        $trip['total_line_haul'] = $row['total_line_haul'];
                        $trip['total_weight']    = $row['total_weight'];
                        array_push($tripsArray, $trip);
                    }
                }
            }
        }

        return $tripsArray;
    }

}
