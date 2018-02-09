<?php

class Cubesheets_CreateEstimate_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        $db = &PearDatabase::getInstance();
        $surveyId = $request->get('record');

        $FromLocationType= array(
            'Extra Pickup 1'=>'3',
            'Extra Pickup 2'=>'4',
            'Extra Pickup 3'=>'5',
            'Extra Pickup 4'=>'6',
            'Extra Pickup 5'=>'7',
            'O - SIT'=>'8',
            'Self Stg PU'=>'9',
            'Perm PU'=>'10',
            'Origin'=>'1'
        );
        $ToLocationType= array(
            'Extra Delivery 1'=>'3',
            'Extra Delivery 2'=>'4',
            'Extra Delivery 3'=>'5',
            'Extra Delivery 4'=>'6',
            'Extra Delivery 5'=>'7',
            'D - SIT'=>'8',
            'Perm Dlv'=>'9',
            'Self Stg Dlv'=>'10',
            'Destination'=>'2'
        );
        $SegmentType = array(
            '0'=>'Road',
            '1'=>'Air',
            '2'=>'Perm',
            '3'=>'Sea',
        );

        //getCubesheetDetailsByRelatedRecord this gives us a CubesheetId
        require_once('libraries/nusoap/nusoap.php');
        require_once('includes/main/WebUI.php');
        require_once('include/Webservices/Create.php');
        require_once('modules/Users/Users.php');

        $soapclient = new \soapclient2(getenv('CUBESHEET_SERVICE_URL'), 'wsdl');
        $soapclient->setDefaultRpcParams(true);
        $soapProxy = $soapclient->getProxy();

        //Use getItems with the currentUserId to match and get anything else for the item or their custom items
        $sql = "SELECT smownerid FROM `vtiger_crmentity` WHERE crmid = ?";
        $result = $db->pquery($sql, [$surveyId]);
        $row = $result->fetchRow();
        $ownerId = $row[0];

        //get a list of all items available to this user
        $soapResponse = $soapProxy->GetItems(['userID'=>(string)$ownerId, 'relatedRecordID' => (string)$surveyId]);

        $userItems = [];
        foreach ($soapResponse['GetItemsResult']['Item'] as $item) {
            $userItems[$item['ItemId']] = $item;
        }

        //get a list of all rooms available to this user
        $soapResponse = $soapProxy->GetRooms(['userID'=>(string)$ownerId, 'relatedRecordID' => (string)$surveyId]);
        $userRooms = [];
        foreach ($soapResponse['GetRoomsResult']['Room'] as $room) {
            $userRooms[$room['RoomId']] = $room;
        }

        //get the cubesheet details, this can be a single item (hash)
        //or for multi segment it's an array of items (array of hashes)

        $soapResponse = $soapProxy->GetCubesheetDetailsByRelatedRecordId(['relatedRecordID' => (string)$surveyId]);

        //OK so we need to get each segment's weight to pass the total weight to estimates
        $cubesheetWeight =  0;
        $totalItemsShipping = 0;
        $totalCube = 0;

        //put each segment's cubesheet id to an array and loop the array.
        $cubeSheetIds = [];

        //but because it's a variable response we need to SEE if it's an array of hashes)
        if (!empty($soapResponse['GetCubesheetDetailsByRelatedRecordIdResult']['ExtendedCubesheet'][0])) {
            $i=1;
            foreach ($soapResponse['GetCubesheetDetailsByRelatedRecordIdResult']['ExtendedCubesheet'] as $segment) {
                $cubeSheetIds[] = $segment['CubeSheetId'];
                $cubesheetWeight += $segment['TotalWeight'];
                $totalItemsShipping += $segment['ItemsShipping'];
                $totalCube += $segment['TotalCube'];
                $addresssegments_list[]=array(
                    'addresssegmentsid'=>'none',
                    'addresssegments_sequence'=>$i,
                    'addresssegments_origin'=>array_search($segment['FromLocationType'], $FromLocationType),
                    'addresssegments_destination'=>array_search($segment['ToLocationType'], $ToLocationType),
                    'addresssegments_transportation'=>$SegmentType[$segment['SegmentType']],
                    'addresssegments_cube'=>$segment['TotalCube'],
                    'addresssegments_weight'=>$segment['TotalWeight'],
                    'addresssegments_weightoverride'=>'',
                    'addresssegments_cubeoverride'=>'',
                );
                $i++;
            }
        } else {
            //made this its own variable so it's easier to work with.
            $segment = $soapResponse['GetCubesheetDetailsByRelatedRecordIdResult']['ExtendedCubesheet'];
            $cubeSheetIds[] = $segment['CubeSheetId'];
            $cubesheetWeight += $segment['TotalWeight'];
            $totalItemsShipping += $segment['ItemsShipping'];
            $totalCube += $segment['TotalCube'];
            $addresssegments_list[]=array(
                'addresssegmentsid'=>'none',
                'addresssegments_sequence'=>'1',
                'addresssegments_origin'=>array_search($segment['FromLocationType'], $FromLocationType),
                'addresssegments_destination'=>array_search($segment['ToLocationType'], $ToLocationType),
                'addresssegments_transportation'=>$SegmentType[$segment['SegmentType']],
                'addresssegments_cube'=>$segment['TotalCube'],
                'addresssegments_weight'=>$segment['TotalWeight'],
                'addresssegments_weightoverride'=>'',
                'addresssegments_cubeoverride'=>'',
            );
        }


        $items = [];
        $totalPackCount = 0;
        foreach ($cubeSheetIds as $cubesheetId) {
            //Use CubesheetId to getSurveyedItems this gives us some basic item info and an ItemId
            $soapResponse = $soapProxy->getSurveyedItems(['CubeSheetId' => $cubesheetId, 'CubeSheetIdSpecified' => true]);
            //$soapR2 = $soapResponse;
            $surveyedItems = $soapResponse['GetSurveyedItemsResult']['SurveyedItems'];

            if (!array_key_exists('0', $surveyedItems)) {
                $surveyedItems = [0 => $surveyedItems];
            }
            foreach ($surveyedItems as $surveyedItem) {
                $cartonBulkyId = $userItems[$surveyedItem['ItemId']]['CartonBulkyId'];
                $packQty = $surveyedItem['PackQty'];
                $totalPackCount += $surveyedItem['PackQty'];
                $containerQty = 0;
//                if ($surveyedItem['ShipQty'] > $surveyedItem['PackQty']) {
//                    //@NOTE: per David: i believe what we do for containers is if the ship quantity is greater than the pack quantity that's your container value
//                    //i.e. shipping = 10, packing = 5, that would mean containers = 10 - 5 (5 containers)
//                    $containerQty = $surveyedItem['ShipQty'] - $surveyedItem['PackQty'];
//                }
                $containerQty = $surveyedItem['ShipQty'];
                if ($cartonBulkyId && isset($items[$surveyedItem['ItemId']])) {
                    $items[$surveyedItem['ItemId']]['Quantity']  += $surveyedItem['ShipQty'];
                    $items[$surveyedItem['ItemId']]['NoShip']    += $surveyedItem['NotShipQty'];
                    $items[$surveyedItem['ItemId']]['PackQty']   += $packQty;
                    $items[$surveyedItem['ItemId']]['UnpackQty'] += $surveyedItem['UnpackQty'];
                    $items[$surveyedItem['ItemId']]['ContainerQty'] += $containerQty;
                    if($userItems[$surveyedItem['ItemId']]['ItemName'] == $items[$surveyedItem['ItemId']]['ArticleName']) {
                        $items[$surveyedItem['ItemId']]['VehicleInfo'] = $surveyedItem['VehicleInfo'];
                    }
                } else {
                    //$items[$cartonBulkyId] = [
                    $items[$surveyedItem['ItemId']] = [
                        'Cube'         => $cubesheetId,
                        'RoomsName'    => $userRooms[$surveyedItem['RoomId']]['Name'],
                        'Id'           => $cartonBulkyId,
                        'ArticleName'  => $userItems[$surveyedItem['ItemId']]['ItemName'],
                        'Length'       => $surveyedItem['Length'],
                        'Width'        => $surveyedItem['Width'],
                        'Height'       => $surveyedItem['Height'],
                        'Weight'       => $surveyedItem['Weight'],
                        'Quantity'     => $surveyedItem['ShipQty'],
                        'NoShip'       => $surveyedItem['NotShipQty'],
                        'PackQty'      => $packQty,
                        //UnpackQty must be equal or less to both ShipQty and PackQty.
                        'UnpackQty'    => $surveyedItem['UnpackQty'],
                        'ContainerQty' => $containerQty, //Cubesheets doesn't have containers, but it's faked with (ship - pack)
                        'CrateFlag'    => ($userItems[$surveyedItem['ItemId']]['IsCrate'] == 'true')?'Yes':'No',
                        'Bulky'        => ($userItems[$surveyedItem['ItemId']]['IsBulky'] == 'true')?'Yes':'No',
                        'FragileFlag'  => '',
                        'CuFt'         => $userItems[$surveyedItem['ItemId']]['CuFt'],
                        'PBOFlag'      => ($userItems[$surveyedItem['ItemId']]['IsPbo'] == 'true')?'Yes':'No',
                        'CPFlag'       => ($userItems[$surveyedItem['ItemId']]['IsCp'] == 'true')?'Yes':'No',
                        'Carton'       => ($userItems[$surveyedItem['ItemId']]['IsPbo'] == 'true' ||
                            $userItems[$surveyedItem['ItemId']]['IsCp'] == 'true')?'Yes':'No',
                        'Comments'     => $surveyedItem['Comment'],
                        'VehicleInfo' => $surveyedItem['VehicleInfo']
                    ];
                }
            }
        }

        $formData = $request->getAll();

        $accountData = [];
        $estimateData = [];

        foreach ($formData as $key => $value) {
            $fieldName = str_replace("account_", "", $key, $count);
            if ($count && $key != 'account_id') {
                $accountData[$fieldName] = $value;
            } else {
                $estimateData[$key] = $value;
            }
        }

        if($estimateData['business_line_est2'])
        {
            list($v1,$v2) = explode(' - ', $estimateData['business_line_est2']);
            if($v2 == 'International')
            {
                $v2 = 'Interstate Move';
            } else {
                $v2 .= ' Move';
            }
            $estimateData['business_line_est'] = $v2;
        }

        $user = new Users();
        $current_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());

        //ws create account
        if ($formData['createAccount'] == 'on') {
            $newAccount = vtws_create('Accounts', $accountData, $current_user);
            $estimateData['account_id'] = $newAccount['id']; //explode('x', $newAccount['id'])[1];
        } else {
            $estimateData['account_id'] = vtws_getWebserviceEntityId('Accounts', $estimateData['account_id']);
        }

        //OT4801 - When an estimate is created from the Mobile side, the "Pricing Type" value should ALWAYS be "Estimate".
        $estimateData['pricing_mode'] = 'Estimate';

        //OT19391 - Setting is_primary to true for non-sirva instances
        if(getenv('INSTANCE_NAME') != 'sirva') {
            if($estimateData['potential_id']) {
                $oppRecord = Vtiger_Record_Model::getInstanceById($estimateData['potential_id'], 'Opportunities');
                if(!$oppRecord->getPrimaryEstimateRecordModel()) {
                    $estimateData['is_primary'] = 1;
                }
            }
        }

        //OT4800
        if(Estimates_Record_Model::isLocalTariff($estimateData['effective_tariff']))
        {
            $estimateData['local_weight'] = ceil($cubesheetWeight);
            $estimateData['local_billed_weight'] = ceil($cubesheetWeight);
            $estimateData['local_cubes'] = ceil($totalCube);
            $estimateData['local_piece_count'] = $totalItemsShipping;
            $estimateData['local_pack_count'] = $totalPackCount;
        }else{
            $estimateData['weight'] = ceil($cubesheetWeight);
            $estimateData['billed_weight'] = ceil($cubesheetWeight);
            $estimateData['estimate_cube'] = ceil($totalCube);
            $estimateData['estimate_piece_count'] = $totalItemsShipping;
            $estimateData['estimate_pack_count'] = $totalPackCount;
        }

        //make UI type 10s WS ids
        $estimateData['assigned_user_id'] = vtws_getWebserviceEntityId('Users', $estimateData['assigned_user_id']);
        //needs to be above vtws_getW setthe potential_id to <tabid>x<id>
        if(!empty($request->get('orders_id'))) {
            $dateFields = [
                'orders_pdate as pack_date',
                'orders_ptdate as pack_to_date',
                'orders_ltdate as load_to_date',
                'orders_ldate as load_date',
                'orders_ddate as deliver_date',
                'orders_dtdate as deliver_to_date',
                'orders_surveyd as survey_date',
                'orders_surveyt as survey_time',
                'orders_ppdate as preffered_ppdate',
                'orders_pddate as preferred_pddate',
                'orders_pldate as preferred_pldate',
            ];

            $dateFieldsString = implode(',', $dateFields);
            $loadDateResult   = $db->pquery(sprintf('SELECT %s FROM `vtiger_orders` WHERE ordersid=?', $dateFieldsString), [$estimateData['orders_id']])->fetchRow();
        } else {
            $dateFields = [
                'pack_date',
                'pack_to_date',
                'load_to_date',
                'load_date',
                'deliver_date',
                'deliver_to_date',
                'survey_date',
                'survey_time',
                'preffered_ppdate',
                'preferred_pddate',
                'preferred_pldate',
                'followup_date',
                'decision_date',
            ];

            $dateFieldsString = implode(',', $dateFields);
            $loadDateResult   = $db->pquery(sprintf('SELECT %s FROM `vtiger_potentialscf` WHERE potentialid = ?', $dateFieldsString), [$estimateData['potential_id']])->fetchRow();
        }

        if(!empty($estimateData['orders_id'])) {
            $estimateData['orders_id'] = vtws_getWebserviceEntityId('Orders', $estimateData['orders_id']);
        }
        if(!empty($estimateData['potential_id'])) {
            $estimateData['potential_id'] = vtws_getWebserviceEntityId('Opportunities', $estimateData['potential_id']);
        }
        if(!empty($loadDateResult)) {
            $estimateData = array_merge($loadDateResult, $estimateData);
        }

        $estimateData['contact_id'] = vtws_getWebserviceEntityId('Contacts', $estimateData['contact_id']);
        //treat these fields differently to handle defaultvalues
        $estimatesModel = Vtiger_Module_Model::getInstance('Estimates');

        $defaultFields = [
            'conversion_rate'
        ];

        if (getenv('INSTANCE_NAME') == 'sirva') {
            $defaultFields[] = 'percent_smf';
            $estimateData['valuation_deductible'] = 'FVP - $0';
            if($estimateData['move_type'] == 'Local/Intra') {
              $estimateData['move_type'] = 'Intrastate';
            }
        }

        foreach ($defaultFields as $defaultField) {
            $fieldModel = Vtiger_Field_Model::getInstance($defaultField, $estimatesModel);
            $estimateData[$defaultField] = $fieldModel->get('defaultvalue');
        }

        //set effective date to today if there isn't one
        if (empty($estimateData['effective_date'])) {
            $estimateData['effective_date'] = date('Y-m-d');
        }
        if (empty($estimateData['interstate_effective_date'])) {
            $estimateData['interstate_effective_date'] = date('Y-m-d');
            $estimateData['validtill'] = date('Y-m-d', strtotime('+1 month'));
        }

        //Correct Estimate element data so it doesn't think it has a record.
        $estimateData['cubesheet'] = vtws_getWebserviceEntityId('Cubesheets', $surveyId);
        unset($estimateData['record']);
        $estimateData['module'] = 'Estimates';

        $isSirva = getenv('INSTANCE_NAME') == 'sirva';
        $isGraebel = getenv('INSTANCE_NAME' == 'graebel');
        if(($isSirva || $isGraebel) && !Estimates_Record_Model::isLocalTariff($estimateData['effective_tariff'])) {
            // Check for service charges.
            // If you're wondering why convert cubesheet is slow, it's partially this.
            // But I am not restructuring rating to add a method to allow multiple zips, at least not as of writing this code.
            $convertedDate = DateTimeField::convertToDBFormat($estimateData['effective_date']);
            $serviceCharges = [];

            $originCharges = Estimates_GetServiceCharges_Action::getServiceCharges($estimateData['origin_zip'], $estimateData['effective_tariff'], $convertedDate);
            if($originCharges['success']) {
                foreach($originCharges['charges'] as $index => $charge) {
                    if(empty($charge)) {
                        continue;
                    }

                    $serviceCharges[] = [
                        'serviceid' => $charge['ServiceID'],
                        'is_dest' => "0",
                        'minimum' => $charge['MinWeight'],
                        'applied' => "on",
                        'always_used' => $charge['AlwaysUsed']?"on":"off",
                        'service_weight' => "",
                        'service_description' => $charge['Description'],
                        'charge' => $charge['Charge']
                    ];
                }
            }

            $destCharges = Estimates_GetServiceCharges_Action::getServiceCharges($estimateData['destination_zip'], $estimateData['effective_tariff'], $convertedDate);
            if($destCharges['success']) {
                foreach($destCharges['charges'] as $index => $charge) {
                    if(empty($charge)) {
                        continue;
                    }

                    $serviceCharges[] = [
                        'serviceid' => $charge['ServiceID'],
                        'is_dest' => "1",
                        'minimum' => $charge['MinWeight'],
                        'applied' => "on",
                        'always_used' => $charge['AlwaysUsed']?"on":"off",
                        'service_weight' => "",
                        'service_description' => $charge['Description'],
                        'charge' => $charge['Charge']
                    ];
                }
            }
            $estimateData['compiledServiceCharges'] = $serviceCharges;
        }
        //ws create estimate
        $newEstimate = vtws_create('Estimates', $estimateData, $current_user);

        $estimateId = explode('x', $newEstimate['id'])[1];

        $extra_stops = ExtraStops_Module_Model::getStops($request->get('potential_id'));

        foreach($extra_stops as $stop){
            $stopRecord = Vtiger_Record_Model::getInstanceById($stop[0], 'ExtraStops');
            $stopRecord->set('extrastopsid', null);
            $stopRecord->set('extrastops_relcrmid', $estimateId);
            $stopRecord->save();
        }

        if (getenv('INSTANCE_NAME') == 'sirva' && !$estimateData['desired_total']) {
            //update estimate's desired price to be null because vtws_create is pretty dumb about those
            $sql = "UPDATE `vtiger_quotes` SET desired_total = NULL WHERE quoteid = ?";
            $db->pquery($sql, [$estimateId]);
        }

        //handle interstate bulkies/packing/crates
        //sigh.
        //@NOTE: What's that am I doing Intrastate twice?
        if (
            $estimateData['business_line_est'] == 'Interstate Move' ||
            $estimateData['business_line_est'] == 'Interstate' ||
            $estimateData['business_line_est'] == 'Intrastate Move' ||
            $estimateData['business_line_est'] == 'Intrastate' ||
            $estimateData['business_line_est'] == 'Military' ||
            $estimateData['move_type'] == 'Interstate' ||
            $estimateData['move_type'] == 'Intrastate' ||
            $estimateData['move_type'] == 'Sirva Military'
        ) {
            $crateSequence = 0;
            //$crates = [];//debugging info
            //$bulkies = [];//ditto
            //$packing = [];//ditto

            foreach ($items as $item) {
                if ($item['Bulky'] == 'Yes') {
                    if(!empty($item['VehicleInfo']))
                    {
                        // vehicle transport
                        $sql = "INSERT INTO `vtiger_quotes_vehicles`
                                  (estimateid,weight,make,model,year,description)
                                  VALUES (?,?,?,?,?,?)";
                        $db->pquery($sql, [$estimateId, $item['Weight'],
                                    $item['VehicleInfo']['VehicleMake'],
                                    $item['VehicleInfo']['VehicleModel'],
                                    $item['VehicleInfo']['VehicleYear'],
                                    "Automobile",
                        ]);
                        //Also add it in as a bulky
                        $sql = "INSERT INTO `vtiger_bulky_items` VALUES (?,?,?,?)";
                        $result = $db->pquery($sql, [$estimateId, $item['Id'], $item['Quantity'], $item['ArticleName']]);
                    } else {
                        //interstate bulkies
                        $sql = "INSERT INTO `vtiger_bulky_items` VALUES (?,?,?,?)";
                        $db->pquery($sql, [$estimateId, $item['Id'], $item['Quantity'], $item['ArticleName']]);
                    }
                    //$bulkies[] = [$estimateId, $item['Id'], $item['Quantity'], $item['ArticleName']];
                } elseif ($item['CPFlag'] == 'Yes') {
                    //interstate packing
                    if(getenv('INSTANCE_NAME') == 'sirva') {
                        $sql = "INSERT INTO `vtiger_packing_items` (quoteid, itemid, pack_qty, unpack_qty, label, ot_pack_qty, ot_unpack_qty, pack_cont_qty) VALUES (?,?,?,?,?,0,0,?)";
                        $db->pquery($sql, [$estimateId, $item['Id'], $item['PackQty'], $item['UnpackQty'], $item['ArticleName'], $item['PackQty']]);
                    } else {
                        $sql = "INSERT INTO `vtiger_packing_items` (quoteid, itemid, pack_qty, unpack_qty, label, ot_pack_qty, ot_unpack_qty, containers) VALUES (?,?,?,?,?,0,0,?)";
                        $db->pquery($sql, [$estimateId, $item['Id'], $item['PackQty'], $item['UnpackQty'], $item['ArticleName'], 0]);
                    }
                    //$packing[] = [$estimateId, $item['Id'], $item['PackQty'], $item['UnpackQty'], $item['ArticleName']];
                } elseif (($item['CrateFlag'] == 'Yes') && $item['Length'] > 0 && $item['Width'] > 0 && $item['Height'] > 0 && ($item['PackQty'] > 0 || $item['UnpackQty'] > 0)) {
                    //interstate crating
                    $padding = 4;
                    if (getenv('INSTANCE_NAME') == 'graebel') {
                        $padding = 0;
                    }
                    $cube = ceil(($item['Length'] + $padding) * ($item['Width'] + $padding) * ($item['Height'] + $padding) / (12 * 12 * 12));
                    $i = 1;

                    $crateSequence++;
                    $sql = "INSERT INTO `vtiger_crates` (quoteid, crateid, description, length, width, height, pack, unpack, cube, line_item_id) VALUES (?,?,?,?,?,?,?,?,?,?)";
                    $db->pquery($sql, [$estimateId, 'C-'.($crateSequence-1), $item['ArticleName'], $item['Length'], $item['Width'], $item['Height'], $item['PackQty'], $item['UnpackQty'], $cube, $crateSequence]);
                    $i++;
                }
            }
        }

        if(Estimates_Record_Model::isLocalTariff($estimateData['effective_tariff']))
        {
            $crateSequence = 0;
            // New logic to get the bulky, packing, and crating services
            $tariff = Vtiger_Record_Model::getInstancebyId($estimateData['effective_tariff'], 'Tariffs');
            $effectiveDate = $tariff->getEffectiveDate();

            if ($request->get('cp_schedule')) {
                $cratingService = $packingService = $tariff->getServiceDetails($request->get('cp_schedule'));
            } else {
                $cratingService = $tariff->getServiceDetails($tariff->getCratingService($effectiveDate));
                $packingService = $tariff->getServiceDetails($tariff->getPackingService($effectiveDate));
            }

            $unpackingService = false;
            if ($request->get('cp_schedule') != $request->get('u_schedule')) {
                $unpackingService = $tariff->getServiceDetails($request->get('u_schedule'));
            }

            $bulkyService   = $tariff->getServiceDetails($tariff->getBulkyService($effectiveDate));

            //handle local crates/packing/bulkys
            foreach ($items as $item) {
                if ($item['Bulky'] == 'Yes') {
                    //local bulkies
                    if ($bulkyService != null) {
                        $sql = "SELECT * FROM `vtiger_tariffbulky` WHERE `vtiger_tariffbulky`.serviceid = ? AND `vtiger_tariffbulky`.CartonBulkyId = ?";
                        $bulkyItem = $db->pquery($sql,[$bulkyService['tariffservicesid'],$item['Id']]);
                        $bulkyItem = $bulkyItem->fetchRow();
                        if($bulkyItem != null) {
                          $sql = "INSERT INTO `vtiger_quotes_bulky` (estimateid, serviceid, description, qty, weight, rate, bulky_id) VALUES (?,?,?,?,?,?,?)";
                          $db->pquery($sql, [$estimateId, $bulkyService['tariffservicesid'], $item['ArticleName'], $item['Quantity'], $bulkyItem['weight'], $bulkyItem['rate'], $item['Id']]);
                        }

                        //$localBulkies[] = [$estimateId, $bulkyService['tariffservicesid'], $item['ArticleName'], $item['Quantity'], $item['Weight'], $item['Id']];
                    }
                } elseif ($item['CPFlag'] == 'Yes') {
                    //local packing
                    $unpackQty = $item['UnpackQty'];
                    if ($unpackingService) {
                        $this->saveLocalPacking($unpackingService['tariffservicesid'], $item, $estimateId, 0, 0, $item['UnpackQty']);
                        $unpackQty = 0;
                    }
                    $this->saveLocalPacking($packingService['tariffservicesid'], $item, $estimateId, $item['ContainerQty'], $item['PackQty'], $unpackQty);

                } elseif (($item['CrateFlag'] == 'Yes') && $item['Length'] > 0 && $item['Width'] > 0 && $item['Height'] > 0) {
                    //local crates
                    if ($cratingService != null) {
                        $crateSequence++;
                        $sql = "INSERT INTO `vtiger_quotes_crating` (estimateid, serviceid, crateid, description, crating_qty, uncrating_qty, length, width, height, line_item_id) VALUES (?,?,?,?,?,?,?,?,?,?)";
                        $db->pquery($sql, [$estimateId, $cratingService['tariffservicesid'], $item['Id'], $item['ArticleName'], $item['PackQty'], $item['UnpackQty'], $item['Length'], $item['Width'], $item['Height'], $crateSequence]);
                        //$localCrating[] = [$estimateId, $cratingService['tariffservicesid'], $item['Id'], $item['ArticleName'], $item['PackQty'], $item['UnpackQty'], $item['Length'], $item['Width'], $item['Height'], $crateSequence];
                    }
                }
            }
        }

        $services = array();
        $sql = "SELECT DISTINCT `vtiger_service`.servicename FROM `vtiger_service` JOIN `vtiger_crmentity` ON `vtiger_service`.serviceid = `vtiger_crmentity`.crmid WHERE `vtiger_crmentity`.smownerid = 1 AND `vtiger_crmentity`.deleted = 0";
        $result = $db->pquery($sql, array());

        while ($row =& $result->fetchRow()) {
            $services[] = $row[0];
        }

        $serviceIds = array();
        $seq = 1;
        foreach ($services as $service) {
            $sql = "SELECT serviceid FROM `vtiger_service` WHERE servicename=?";
            $params[] = $service;

            $result = $db->pquery($sql, $params);
            unset($params);

            $row = $result->fetchRow();

            if ($row == null) {
                continue;
            }

            $sql = "INSERT INTO `vtiger_inventoryproductrel` (id, productid, sequence_no, quantity, listprice) VALUES (?,?,?,?,?)";
            $db->pquery($sql, [$estimateId, $row[0], $seq, 1, 0]);
        }

        // Create Address Segments
        $addressSegmentsModule = Vtiger_Module_Model::getInstance('AddressSegments');
        if($addressSegmentsModule && $addressSegmentsModule->isActive()) {
            if (count($addresssegments_list) > 0) {
                foreach ($addresssegments_list as $addresssegment) {
                    $AddressSegmentsRecordModel = Vtiger_Record_Model::getCleanInstance("AddressSegments");
                    $AddressSegmentsRecordModel->set('mode', '');
                    $AddressSegmentsRecordModel->set('addresssegments_sequence', $addresssegment['addresssegments_sequence']);
                    $AddressSegmentsRecordModel->set('addresssegments_origin', $addresssegment['addresssegments_origin']);
                    $AddressSegmentsRecordModel->set('addresssegments_destination', $addresssegment['addresssegments_destination']);
                    $AddressSegmentsRecordModel->set('addresssegments_transportation', $addresssegment['addresssegments_transportation']);
                    $AddressSegmentsRecordModel->set('addresssegments_cube', $addresssegment['addresssegments_cube']);
                    $AddressSegmentsRecordModel->set('addresssegments_weight', ceil($addresssegment['addresssegments_weight']));
                    $AddressSegmentsRecordModel->set('addresssegments_weightoverride', $addresssegment['addresssegments_weightoverride']);
                    $AddressSegmentsRecordModel->set('addresssegments_cubeoverride', $addresssegment['addresssegments_cubeoverride']);
                    $AddressSegmentsRecordModel->set('addresssegments_relcrmid', $estimateId);
                    $AddressSegmentsRecordModel->save();
                }
            } else {
                $AddressSegmentsRecordModel = Vtiger_Record_Model::getCleanInstance("AddressSegments");
                $AddressSegmentsRecordModel->set('mode', '');
                $AddressSegmentsRecordModel->set('addresssegments_sequence', '1');
                $AddressSegmentsRecordModel->set('addresssegments_origin', 'Origin');
                $AddressSegmentsRecordModel->set('addresssegments_destination', 'Destination');
                $AddressSegmentsRecordModel->set('addresssegments_transportation', '');
                $AddressSegmentsRecordModel->set('addresssegments_cube', '');
                $AddressSegmentsRecordModel->set('addresssegments_weight', '');
                $AddressSegmentsRecordModel->set('addresssegments_weightoverride', '');
                $AddressSegmentsRecordModel->set('addresssegments_cubeoverride', '');
                $AddressSegmentsRecordModel->set('addresssegments_relcrmid', $estimateId);
                $AddressSegmentsRecordModel->save();
            }
        }


        header('Location: index.php?module=Estimates&view=Detail&record='.$estimateId);
        //debugging
        //$info = ['estimateId'=>$estimateId, 'items'=>print_r($items, true), 'bulkies'=>print_r($localBulkies, true), 'packing'=>print_r($localPacking, true), 'crates'=>print_r($localCrating, true)/*, "soapR1"=>print_r($soapR1, true), "soapR2"=>print_r($soapR2, true), "soapR3"=>print_r($soapR3, true), "soapR4"=>print_r($soapR4, true)*/]; //, 'debug'=>['userItems'=>$userItems]
        //$response = new Vtiger_Response();
        //$response->setResult($info);
        //$response->emit();
    }
    public function saveStops($stop, $relcrmid) {
        $db            = PearDatabase::getInstance();
        $description = $stop['extrastops_description'];
        $sequence    = $stop['extrastops_sequence'];
        if ($sequence) {
            $id         = $stop['extrastops_id'];
            $weight     = $stop['extrastops_weight'];
            $isPrimary  = $stop['extrastops_isprimary'];
            $address1   = $stop['extrastops_address1'];
            $address2   = $stop['extrastops_address2'];
            $phone1     = $stop['extrastops_phone1'];
            $phone2     = $stop['extrastops_phone2'];
            $phoneType1 = $stop['extrastops_phonetype1'];
            $phoneType2 = $stop['extrastops_phonetype2'];
            $city       = $stop['extrastops_city'];
            $type       = $stop['extrastops_type'];
            $contact    = $stop['agentid'];
            $state      = $stop['extrastops_state'];
            $zip        = $stop['extrastops_zip'];
            $country    = $stop['extrastops_country'];
            $date       = $stop['extrastops_date'];
            $name       = $stop['extrastops_name'];
            $sql    = "SELECT id FROM `vtiger_extrastops_seq`";
            $result = $db->pquery($sql, []);
            $row    = $result->fetchRow();
            $id     = $row[0];

            $sql = "UPDATE `vtiger_extrastops_seq` SET id = ".($id + 1);
            $db->pquery($sql, []);
            $sql = "INSERT INTO `vtiger_extrastops` (extrastopsid, extrastops_sequence, extrastops_description, extrastops_weight, extrastops_isprimary, extrastops_address1, extrastops_address2, extrastops_phone1, extrastops_phone2, extrastops_phonetype1, extrastops_phonetype2, extrastops_city, extrastops_state, extrastops_zip, extrastops_country, extrastops_date, extrastops_relcrmid, extrastops_type, extrastops_contact,extrastops_name) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $result = $db->pquery($sql,
                        [$id,
                         $sequence,
                         $description,
                         $weight,
                         $isPrimary,
                         $address1,
                         $address2,
                         $phone1,
                         $phone2,
                         $phoneType1,
                         $phoneType2,
                         $city,
                         $state,
                         $zip,
                         $country,
                         $date,
                         $relcrmid,
                         $type,
                         $contact,
                         $name]
            );
        }
    }

    protected function saveLocalPacking($packingServiceId, $item, $estimateId, $containerQty = 0, $packQty = 0, $unpackQty = 0) {
        if (!$packingServiceId) {
            return;
        }
        if (!is_array($item)) {
            return;
        }
        if (!$estimateId) {
            return;
        }
        $db = &PearDatabase::getInstance();
        $sql         = "SELECT * FROM `vtiger_tariffpackingitems` WHERE `vtiger_tariffpackingitems`.serviceid = ? AND `vtiger_tariffpackingitems`.pack_item_id = ?";
        $packingItem = $db->pquery($sql, [$packingServiceId, $item['Id']]);
        $packingItem = $packingItem->fetchRow();
        if ($packingItem != NULL) {
            $sql = "INSERT INTO `vtiger_quotes_packing` (estimateid,
                                                                       serviceid,
                                                                       name,
                                                                       container_qty,
                                                                       container_rate,
                                                                       pack_qty,
                                                                       pack_rate,
                                                                       unpack_qty,
                                                                       unpack_rate,
                                                                       packing_id,
                                                                       cost_container,
                                                                       cost_packing,
                                                                       cost_unpacking)
                                  VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $db->pquery($sql,
                        [$estimateId,
                         $packingServiceId,
                         $packingItem['name'],
                         $containerQty,
                         $packingItem['container_rate'],
                         $packQty,
                         $packingItem['packing_rate'],
                         $unpackQty,
                         $packingItem['unpacking_rate'],
                         $packingItem['pack_item_id'],
                         //@TODO: HERE?
                         //$packingItem['line_item_id'],
                         ($containerQty * $packingItem['container_rate']), //cost_container, may not be used
                         ($packQty * $packingItem['packing_rate']),        //cost_packing, may not be used
                         ($unpackQty * $packingItem['unpacking_rate'])    //cost_unpacking, may not be used
                        ]);
        }
        //$localPacking[] = [$estimateId, $packingService['tariffservicesid'], $item['ArticleName'], $item['PackQty'], $item['UnpackQty'], $item['Id']];
        return true;
    }
}
