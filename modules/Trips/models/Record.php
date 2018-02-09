<?php

class Trips_Record_Model extends Vtiger_Record_Model {

    function recalculateTripsFields() {
        $this->recalculateDimesions();
    }

    function recalculateDimesions() {
        $tripId = $this->getId();
        $db = PearDatabase::getInstance();

        $sql = "SELECT sum(CAST(orders_eweight AS DECIMAL(7,2))) as total1,sum(CAST(orders_elinehaul AS DECIMAL(7,2))) as total2, count(ordersid) as shipments, min(orders_ldate) as firstload, max(orders_dtdate) as lastdeliver,   
                SUM(CAST(orders_miles AS DECIMAL(7,2))) as totalmiles, SUM(CAST(orders_ecube AS DECIMAL(7,2))) as totalecube 
                FROM vtiger_orders
                INNER JOIN vtiger_crmentity ON vtiger_orders.ordersid = vtiger_crmentity.crmid 
                WHERE vtiger_crmentity.deleted = 0 AND vtiger_orders.orders_trip = ?";
        $result = $db->pquery($sql, array($tripId));

        if ($db->num_rows($result) > 0) {
            $totalLinehaul = $db->query_result($result, 0, 'total2');
            $totalWeight = $db->query_result($result, 0, 'total1');
            $shipments = $db->query_result($result, 0, 'shipments');
            $firstload = $db->query_result($result, 0, 'firstload');
            $originZone = $this->getTripOriginZone();

            $firstLoad = strtotime($firstload);
            $lastDeliver = strtotime($db->query_result($result, 0, 'lastdeliver'));
            $datediff = $lastDeliver - $firstLoad;
            $tripDays = floor($datediff / (60 * 60 * 24));

            $tripFSC = $this->getTripFSC();

            $totalMiles = $db->query_result($result, 0, 'totalmiles');
            $totalECube = ceil($db->query_result($result, 0, 'totalecube'));

            $tripsRevenue = $this->getTripRevenue() + $totalLinehaul;
            if ($totalMiles > 0) {
                $mileRev = round($tripsRevenue/$totalMiles, 2);
            }

            if ($tripDays > 0) {
                $dailyRev = round($tripsRevenue/$tripDays, 2);
            }

            $avCube = $this->getTripCubeCapacity() - $totalECube;

            $originState = $this->getTripOriginState();
            $destinationState = $this->getTripDestinationState();

            if ($tripId != '') {
                $params = array(
                    $totalLinehaul,
                    $totalWeight,
                    $shipments,
                    $firstload,
                    $originState,
                    $destinationState,
                    $originZone,
                    $tripDays,
                    $tripFSC,
                    $totalMiles,
                    $totalECube,
                    $mileRev,
                    $dailyRev,
                    $avCube,
                    intval($tripId)
                );

                $db->pquery("UPDATE vtiger_trips, vtiger_crmentity SET  total_line_haul=?, total_weight=?, trips_shipmentcount=?, trips_firstload=?, 
                                    origin_state=?,empty_state=?,origin_zone=?, trips_days=?, trips_fuelsurcharge=?, trips_totalmiles=?, trips_totalcube=?, 
                                    trips_milerate=?, trips_dailyrate=?, trips_cubeavailable=?
                                WHERE vtiger_trips.tripsid = vtiger_crmentity.crmid 
                                AND deleted=0 
                                AND tripsid=?", $params);
            }
        }
    }

    public function getTripOriginZone()
    {
        $tripId = $this->getId();
        $db = PearDatabase::getInstance();
        $result = $db->pquery("SELECT origin_zip,origin_state,orders_ldate, origin_zone 
                                FROM vtiger_orders 
                                INNER JOIN vtiger_crmentity ON vtiger_orders.ordersid = vtiger_crmentity.crmid 
                                INNER JOIN vtiger_crmentityrel ON vtiger_orders.ordersid = vtiger_crmentityrel.relcrmid 
                                WHERE deleted = 0 AND vtiger_crmentityrel.crmid = ? ORDER BY orders_ldate ASC LIMIT 1", array($tripId));

        if ($db->num_rows($result) > 0) {
            $originZip = $db->query_result($result, 0, 'origin_zip');
            $originState = $db->query_result($result, 0, 'origin_state');
            $originZone = $db->query_result($result, 0, 'origin_zone');

            if ($originZone == '') {
                $zoneAdminModel = Vtiger_Module_Model::getInstance('ZoneAdmin');
                $originZone = $zoneAdminModel->getAddressZone($originState, $originZip);
            }

            return $originZone;
        }
    }

    public function getTripFSC()
    {
        $tripId = $this->getId();
        $db = PearDatabase::getInstance();
        $tripFSC = 0;
        $result = $db->pquery("SELECT orders_id, ROUND(SUM(listprice),2) AS revenue
                            FROM vtiger_quotes quo
                            INNER JOIN vtiger_inventoryproductrel inv on quo.quoteid = inv.id
                            INNER JOIN vtiger_service serv on inv.productid = serv.serviceid
                            INNER JOIN vtiger_orders orders on quo.orders_id = orders.ordersid
                            INNER JOIN vtiger_crmentity crme on quo.quoteid = crme.crmid
                            INNER JOIN vtiger_crmentityrel ON orders.ordersid = vtiger_crmentityrel.relcrmid 
                            WHERE deleted=0 
                            AND is_primary=1 
                            AND quotestage ='Accepted' 
                            AND servicename LIKE '%Fuel%' 
                            AND vtiger_crmentityrel.crmid = ?", array($tripId));

        if ($result && $db->num_fields($result) > 0) {
            $tripFSC = $db->query_result($result, 0, 'revenue');
        }

        return $tripFSC;
    }

    public function getTripRevenue()
    {
        $tripId = $this->getId();
        $db = PearDatabase::getInstance();
        $tripRev = 0;
        $result = $db->pquery("SELECT orders_id, ROUND(SUM(listprice),2) AS revenue
                            FROM vtiger_quotes quo
                            INNER JOIN vtiger_inventoryproductrel inv on quo.quoteid = inv.id
                            INNER JOIN vtiger_service serv on inv.productid = serv.serviceid
                            INNER JOIN vtiger_orders orders on quo.orders_id = orders.ordersid
                            INNER JOIN vtiger_crmentity crme on quo.quoteid = crme.crmid
                            INNER JOIN vtiger_crmentityrel ON orders.ordersid = vtiger_crmentityrel.relcrmid 
                            WHERE deleted=0 
                            AND is_primary=1 
                            AND quotestage ='Accepted' 
                            AND (servicename LIKE '%Fuel%' OR servicename LIKE '%Accessorials%') 
                            AND vtiger_crmentityrel.crmid = ?", array($tripId));

        if ($result && $db->num_fields($result) > 0) {
            $tripRev = $db->query_result($result, 0, 'revenue');
        }

        return $tripRev;
    }

    public function getTripCubeCapacity()
    {
        $tripId = $this->getId();
        $db = PearDatabase::getInstance();
        $cubeCapacity = 0;
        $result = $db->pquery("SELECT vehicle_cubec 
                            FROM vtiger_vehicles
                            INNER JOIN vtiger_trips ON vtiger_vehicles.vehiclesid = vtiger_trips.trips_vehicle
                            INNER JOIN vtiger_crmentity crme on vtiger_vehicles.vehiclesid = crme.crmid
                            WHERE deleted=0 
                            AND tripsid= ?", array($tripId));

        if ($result && $db->num_fields($result) > 0) {
            $cubeCapacity = $db->query_result($result, 0, 'vehicle_cubec');
        }

        return $cubeCapacity;
    }

    public function getTripOriginState($record = null)
    {
        if(!$record){
            $record = $this->getId();//tripid
        }
        if ($record != '') {
            $db = PearDatabase::getInstance();
            $sql1 = "SELECT `origin_state`,
                    (
                    CASE 
                    WHEN `orders_ldate` IS NOT NULL
                    THEN `orders_ldate`
                    ELSE `orders_ldd_pldate`
                    END
                    ) AS order_date
                    FROM `vtiger_orders` 
                    WHERE `orders_trip` = ? AND (orders_ldate is not null OR orders_ldd_pldate is not null)
                    ORDER BY order_date ASC LIMIT 1";
            $rorigin_state = $db->pquery($sql1, [$record]);
            if($db->num_rows($rorigin_state)){
                $origin_state = $rorigin_state->fetchRow()['origin_state'];
            }
        }

        if ($origin_state) {
            $originState = $origin_state;
        } else {
            $originState = '';
        }

        return $originState;
    }

    public function getTripDestinationState($record = null)
    {
        if(!$record){
            $record = $this->getId();//tripid
        }
        if ($record != '') {
            $db = PearDatabase::getInstance();
            $sql ="SELECT `destination_state`,
                    (
                    CASE 
                    WHEN `orders_ddate` IS NOT NULL
                    THEN `orders_ddate`
                    ELSE `orders_ldd_pddate`
                    END
                    ) AS order_date
                    FROM `vtiger_orders` 
                    WHERE `orders_trip` = ? AND (orders_ddate is not null OR orders_ldd_pddate is not null)
                    ORDER BY order_date DESC LIMIT 1";
            $result = $db->pquery($sql, [$record]);
            if($db->num_rows($result)){
                $destination_state = $result->fetchRow()['destination_state'];
            }
        }

        if ($destination_state) {
            $emptyState = $destination_state;
        } else {
            $emptyState = '';
        }

        return $emptyState;
    }

    public function getRelatedOrders()
    {
        $ordersArray = [];
        $db          = PearDatabase::getInstance();
        $recordId    = $this->getId();

        if ($recordId == '') {
	    return array();
	}

        $stmt = "SELECT vtiger_orders.* FROM vtiger_orders
                    INNER JOIN vtiger_crmentity ON vtiger_orders.ordersid = vtiger_crmentity.crmid
		    WHERE deleted = 0 AND orders_trip=? ORDER BY orders_sequence";

        $result      = $db->pquery($stmt, [$recordId]);
        $currUser = Users_Record_Model::getCurrentUserModel();
        if ($db->num_rows($result) > 0) {
            while ($arr = $db->fetch_array($result)) {
                if (Users_Privileges_Model::isPermitted('Orders', 'EditView', $arr['ordersid']) == 'yes') {
                    if ($arr['orders_contacts'] != '' && $arr['orders_contacts'] != '0') {
                        $ordersContact = Vtiger_Record_Model::getInstanceById($arr['orders_contacts'], 'Contacts');
                        $lastName      = $ordersContact->get('lastname');
                        $firstName     = $ordersContact->get('firstname');
                    } else {
                        $lastName  = '';
                        $firstName = '';
                    }
                    if ($arr['orders_account'] != '' && $arr['orders_account'] != '0') {
                        $ordersAcccount = Vtiger_Record_Model::getInstanceById($arr['orders_account'], 'Accounts');
                        $accountName    = $ordersAcccount->get('accountname');
                    } else {
                        $accountName = '';
                    }

		    $participantAgents = ParticipatingAgents_Module_Model::getParticipants( $arr['ordersid']);

		    foreach ($participantAgents as $pa) {
			if($pa['agent_type'] == 'Origin Agent'){
			    $originAgent = '(' . $pa['agent_number'] . ') ' . $pa['agentName'];
			}elseif ($pa['agent_type'] == 'Destination Agent') {
			     $destAgent = '(' . $pa['agent_number'] . ') ' . $pa['agentName'];
			}
		    }



                    $ordersArray[] = [
                        'ordersid'           => $arr['ordersid'],
                        'orderstaskid'       => $arr['orderstaskid'],
                        'pudate'             => ($arr['orders_ldate'] != ''?Vtiger_Date_UIType::getDisplayDateValue($arr['orders_ldate']):''),
                        'actual_load_date'       => ($arr['orders_pudate'] != ''?Vtiger_Date_UIType::getDisplayDateValue($arr['orders_pudate']):''),
                        'account_name'       => $accountName,
                        'ship_lastname'      => $lastName,
                        'order_no'           => $arr['orders_no'],
                        'orders_sequence'    => $arr['orders_sequence'],
                        'est_weight'         => ceil($arr['orders_eweight']),
                        'actual_weight'      => $arr['orders_actualweight'],
                        //'actual_weight'      => $arr['orders_netweight'],
                        'linehaul'           => Vtiger_Currency_UIType::transformDisplayValue($arr['orders_elinehaul']),
                        'rd_date'            => $arr[''],
                        'origin_city'        => $arr['origin_city'],
                        'origin_state'       => $arr['origin_state'],
                        'dest_agent'         => $destAgent,
						'origin_agent'       => $originAgent,
                        'dest_city'          => $arr['destination_city'],
                        'dest_state'         => $arr['destination_state'],
                        'delivery'           => $arr[''],
                        'actual_weighttare'  => $arr['orders_tweight'],
                        //'drivers_notes'      => (strlen($arr['drivers_notes']) > 0)?$arr['drivers_notes']:'No notes from this driver.',
                        'planned_load_date' => ($arr['orders_ldd_pldate'] != '') ? Vtiger_Date_UIType::getDisplayDateValue($arr['orders_ldd_pldate']) : '',
                        'planned_delivery_date' => ($arr['orders_ldd_pddate'] != ''?Vtiger_Date_UIType::getDisplayDateValue($arr['orders_ldd_pddate']):''),
                        'otherstatus' => $arr['orders_otherstatus'],
                        'actual_delivery_date' => ($arr['orders_actualpudate'] != ''?Vtiger_Date_UIType::getDisplayDateValue($arr['orders_actualpudate']):''),
                        'sit' => $arr['orders_sit'],
                        'dateformat' => $currUser->get("date_format"),
                        'pl_confirmed' => $arr['orders_ldd_plconfirmed'],
                        'pd_confirmed' => $arr['orders_ldd_pdconfirmed'],
			//OT 16552
			'load_to_date'		=> ($arr['orders_ltdate'] != ''?Vtiger_Date_UIType::getDisplayDateValue($arr['orders_ltdate']):''),
			'delivery_date'	=> ($arr['orders_ddate'] != ''?Vtiger_Date_UIType::getDisplayDateValue($arr['orders_ddate']):''),
			'delivery_to_date'	=> ($arr['orders_dtdate'] != ''?Vtiger_Date_UIType::getDisplayDateValue($arr['orders_dtdate']):''),
                    ];
                }
            }
        }
        return $ordersArray;
    }
}
