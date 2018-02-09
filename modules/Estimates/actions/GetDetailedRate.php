<?php
/**
 * @author 			Ryan Paulson, Hacked by Louis Robinson
 * @file 			GetDetailedRate.php
 * @description 	Extended functionality from the Quotes module so we can add to
 *                  it without having to deal with changing the core vtiger code
 * @contact 		lrobinson@igcsoftware.com
 * @copyright		IGC Software
 */
require_once('libraries/nusoap/nusoap.php');
use Carbon\Carbon;


class Estimates_GetDetailedRate_Action extends Estimates_QuickEstimate_Action
{
    public function __construct()
    {
		parent::__construct();
	}

    public function process(Vtiger_Request $request)
    {
		$requestType = $request->get('type');
		$recordId = $request->get('record');
        $tariffId = $request->get('effective_tariff');
        $moduleName = $request->get('module');
		$pseudo = $request->get('pseudoSave') == '1';
        $business_line_est = $request->get('business_line_est');

        if ($requestType != 'editview') {
			$db = isset($db) ? $db : PearDatabase::getInstance();
            $row = Estimates_Record_Model::getEffectiveTariff($recordId);
			$tariffId = $row['effective_tariff'];
            $sql = "SELECT business_line_est FROM `vtiger_quotescf` WHERE quoteid=?";
            $result = $db->pquery($sql, array($recordId));
            $row = $result->fetchRow();
            $business_line_est = $row['business_line_est'];
		}

        if (isset($tariffId) && $tariffId != '') {
			$effectiveTariff = Vtiger_Record_Model::getInstanceById($tariffId);
			$wsdlURL = $effectiveTariff->get('rating_url');
		} else {
            $row = Estimates_Record_Model::getEffectiveTariff($recordId);
			$wsdlURL = $row['rating_url'];
            $tariffId = $row['tariffmanagerid'];
		}
        $this->tariffID = $tariffId;

        if ((isset($wsdlURL) && substr($wsdlURL, 0, 4) != 'http') || !isset($wsdlURL)) {
			$response = new Vtiger_Response();
			$response->setError("Invalid URL provided for rating", "Please contact IGC Support for assistance.");
			$response->emit();
			return;
		}

		//file_put_contents('logs/SaveLog.log', date('Y-m-d H:i:s - ').print_r($allFields, true)."\n", FILE_APPEND);
		// $local = false;
		// file_put_contents('logs/devLog.log', "\n before generateXML", FILE_APPEND);
		// include_once('generatexml.php'); //Generates $xml variable using values contained in $allFields
		// file_put_contents('logs/devLog.log', "\n after generateXML", FILE_APPEND);

        if ($pseudo) {
			$db = PearDatabase::getInstance();
			$focus = CRMEntity::getInstance('Estimates');
			$customTables = array('vtiger_quotes_baseplus' => 'estimateid',
								  'vtiger_quotes_breakpoint' => 'estimateid',
								  'vtiger_quotes_bulky' => 'estimateid',
								  'vtiger_quotes_countycharge' => 'estimateid',
								  'vtiger_quotes_crating' => 'estimateid',
								  'vtiger_quotes_cwtbyweight' => 'estimateid',
								  'vtiger_quotes_hourlyset' => 'estimateid',
								  'vtiger_quotes_packing' => 'estimateid',
								  'vtiger_quotes_perunit' => 'estimateid',
								  'vtiger_quotes_sectiondiscount' => 'estimateid',
								  'vtiger_quotes_servicecost' => 'estimateid',
								  'vtiger_quotes_valuation' => 'estimateid',
								  'vtiger_quotes_vehicles' => 'estimateid',
								  'vtiger_quotes_weightmileage' => 'estimateid',
								  //'vtiger_corporate_vehicles' => 'estimate_id',
								  'vtiger_inventoryshippingrel' => 'id',
								  'vtiger_inventorysubproductrel' => 'id',
								  'vtiger_packing_items' => 'quoteid',
								  'vtiger_misc_accessorials' => 'quoteid',
								  'vtiger_crates' => 'quoteid',
								  'vtiger_bulky_items' => 'quoteid',
								  'vtiger_extrastops' => 'extrastops_relcrmid',
                  'vtiger_quotes_flatratebyweight' => 'estimateid',
                                  'vtiger_extrastopscf' => '',
								  'vtiger_quotes_servicecharge' => 'estimateid',
                                  'vtiger_detailed_lineitems' => 'dli_relcrmid',
                                  'vtiger_quotescf' => 'quoteid');
			$crmIdLookup = [$recordId];

			$vehicleLookupModel = Vtiger_Module_Model::getInstance('VehicleLookup');
            if ($vehicleLookupModel && $vehicleLookupModel->isActive()) {
				$customTables['vtiger_vehiclelookup'] = 'vehiclelookup_relcrmid';
                $customTables['vtiger_vehiclelookupcf'] = '';
			}

            if (getenv('INSTANCE_NAME') == 'sirva') {
				$customTables['vtiger_corporate_vehicles'] = 'estimate_id';
                $customTables['vtiger_quotes_sit'] = 'estimateid';
                $customTables['vtiger_quotes_inter_servchg'] = 'quoteid';
                $customTables['vtiger_addresssegments'] = 'addresssegments_relcrmid';
                $customTables['vtiger_addresssegmentscf'] = '';
                $customTables['vtiger_quotes_storage_valution'] = 'estimateid';
                $customTables['vtiger_quotes_sit'] = 'estimateid';
			}
            if (getenv('INSTANCE_NAME') == 'graebel') {
                $customTables['vtiger_quotes_inter_servchg'] = 'quoteid';
                $customTables['vtiger_upholsteryfinefinish'] = 'uff_relcrmid';
                $customTables['packing_items_extrastops'] = '';
                $customTables['vtiger_vehicletransportation'] = 'vehicletrans_relcrmid';
                $customTables['vtiger_vehicletransportationcf'] = '';
                if ($recordId) {
					$stopsRes = $db->pquery('SELECT extrastopsid FROM vtiger_extrastops WHERE extrastops_relcrmid=?', [$recordId]);
					while ($row = $stopsRes->fetchRow()) {
						$crmIdLookup[] = $row['extrastopsid'];
					}
                    $stopsRes = $db->pquery('SELECT vehicletransportationid FROM vtiger_vehicletransportation WHERE vehicletrans_relcrmid=?', [$recordId]);
                    while ($row = $stopsRes->fetchRow()) {
                        $crmIdLookup[] = $row['vehicletransportationid'];
                    }
                    $stopsRes = $db->pquery('SELECT upholsteryfinefinishid FROM vtiger_upholsteryfinefinish WHERE uff_relcrmid=?', [$recordId]);
                    while ($row = $stopsRes->fetchRow()) {
                        $crmIdLookup[] = $row['upholsteryfinefinishid'];
                    }
				}
            } elseif (getenv('IGC_MOVEHQ')) {
                if ($recordId) {
                    $stopsRes = $db->pquery('SELECT extrastopsid FROM vtiger_extrastops WHERE extrastops_relcrmid=?', [$recordId]);
                    while ($row = $stopsRes->fetchRow()) {
                        $crmIdLookup[] = $row['extrastopsid'];
                    }
                    $stopsRes = $db->pquery('SELECT vehicletransportationid FROM vtiger_vehicletransportation WHERE vehicletrans_relcrmid=?', [$recordId]);
                    while ($row = $stopsRes->fetchRow()) {
                        $crmIdLookup[] = $row['vehicletransportationid'];
                    }
                }
            }
			$tempTabNames = array();

            foreach ($focus->tab_name as $table_name) {
				$tempTableName = session_id().'_'.$table_name;
				$tempTabNames[] = $tempTableName;
				file_put_contents('logs/xml.log', date('Y-m-d H:i:s - ')."Dropping $tempTableName\n", FILE_APPEND);
				$sql = "DROP TEMPORARY TABLE IF EXISTS `$tempTableName`";
				$db->pquery($sql, array());
				file_put_contents('logs/xml.log', date('Y-m-d H:i:s - ')."Creating $tempTableName\n", FILE_APPEND);
                if ($table_name == 'vtiger_crmentity' && count($crmIdLookup) > 1) {
					$sql = "CREATE TEMPORARY TABLE IF NOT EXISTS `$tempTableName` SELECT * FROM $table_name WHERE ".$focus->tab_name_index[$table_name]." IN (".implode(',', $crmIdLookup).")";
                    $db->pquery($sql, []);
                } else {
                    $sql = "CREATE TEMPORARY TABLE IF NOT EXISTS `$tempTableName` SELECT * FROM $table_name WHERE ".$focus->tab_name_index[$table_name]."=?";
                    $db->pquery($sql, array($recordId));
                }

				//Check for corresponding seq table
				$table_name .= '_seq';
				$sql = "SHOW TABLES LIKE '$table_name'";
				$result = $db->pquery($sql, array());
                if ($db->num_rows($result) > 0) {
					$tempTableName .= '_seq';
					$tempTabNames[] = $tempTableName;
					$sql = "DROP TEMPORARY TABLE IF EXISTS `$tempTableName`";
					$db->pquery($sql, array());
					$sql = "CREATE TEMPORARY TABLE IF NOT EXISTS `$tempTableName` SELECT * FROM $table_name";
					$db->pquery($sql, array());
				}
			}

            foreach ($customTables as $table_name => $table_index) {
				$tempTableName = session_id().'_'.$table_name;
				$tempTabNames[] = $tempTableName;
				file_put_contents('logs/xml.log', date('Y-m-d H:i:s - ')."Dropping $tempTableName\n", FILE_APPEND);
				$sql = "DROP TEMPORARY TABLE IF EXISTS `$tempTableName`";
				$db->pquery($sql, array());
				file_put_contents('logs/xml.log', date('Y-m-d H:i:s - ')."Creating $tempTableName\n", FILE_APPEND);
                if ($table_index) {
                    $sql = "CREATE TEMPORARY TABLE IF NOT EXISTS `$tempTableName` SELECT * FROM $table_name WHERE $table_index=?";
                    $db->pquery($sql, array($recordId));
                } else {
                    $sql = "CREATE TEMPORARY TABLE IF NOT EXISTS `$tempTableName` LIKE $table_name";
                    $db->pquery($sql);
                }

				//Check for corresponding seq table
				$table_name .= '_seq';
				$sql = "SHOW TABLES LIKE '$table_name'";
				$result = $db->pquery($sql, array());
                if ($db->num_rows($result) > 0) {
					$tempTableName .= '_seq';
					$tempTabNames[] = $tempTableName;
					$sql = "CREATE TEMPORARY TABLE IF NOT EXISTS `$tempTableName` SELECT * FROM $table_name";
					$db->pquery($sql, array());
				}
                if ($table_name == 'vtiger_detailed_lineitems_seq') {
                    // need to copy service providers
                    $tempTableName = session_id() . '_dli_service_providers';
                    $sql = "CREATE TEMPORARY TABLE IF NOT EXISTS `$tempTableName` SELECT dli_service_providers.*
                            FROM dli_service_providers INNER JOIN vtiger_detailed_lineitems
                              ON (dli_service_providers.dli_id=vtiger_detailed_lineitems.detaillineitemsid) WHERE dli_relcrmid=?";
                    $db->pquery($sql, [$recordId]);
			    }
			}

			$saveAction = new Estimates_Save_Action;
			file_put_contents('logs/xml.log', date('Y-m-d H:i:s - ')."Preparing to call saveAction->process\n", FILE_APPEND);
			$saveAction->process($request);
            $pseudoRecordId = $request->get('record');
		}

		$arr = MoveCrm\arrayBuilder::buildArray($request->get('record'), $pseudo);

        $arr['survey_upload']['interstate_data']['fuel_surcharge_pct'] = '';

        // use RatingEngineDev instead of RatingEngine if .env is set
        if (getenv('USE_DEV_RATING_ENGINE')) {
            $wsdlURL = str_replace('/RatingEngine/', '/RatingEngineDev/', $wsdlURL);
            if(getenv('INSTANCE_NAME') == 'sirva') {
                $wsdlURL = str_replace('sirva-win-qa', 'awsdev1', $wsdlURL);
            }
        }

		//Move soap init here so I can reuse
		$soapclient = new soapclient2($wsdlURL, 'wsdl');
		$soapclient->setDefaultRpcParams(true);
		$soapProxy = $soapclient->getProxy();

		$wsdlParams['caller'] = 'VnbZ1BjT4xtFyCKj21Xr';

		$xml = MoveCrm\xmlBuilder::build($arr);

		file_put_contents('logs/xmlRework.xml', $xml);
		file_put_contents('logs/DetailedRating.xml', $xml."\n");


        if (getenv('INSTANCE_NAME') == 'sirva') {
			//RARRGHHH GRRRRR OK we think ONLY sirva has no base64 encoding.
			$wsdlParams['ratingInput'] = $xml;
		} else {
			$wsdlParams['ratingInput'] = base64_encode($xml);
		}

		file_put_contents('logs/devLog.log', "\n wsdlURLx : ".$wsdlURL, FILE_APPEND);
		//file_put_contents('logs/devLog.log', "\n wsdlParams : ".print_r($wsdlParams,true), FILE_APPEND);
		//file_put_contents('logs/SaveLog.log', date('Y-m-d H:i:s - ').$wsdlURL."\n", FILE_APPEND);
		//file_put_contents('logs/SaveLog.log', date('Y-m-d H:i:s - ').$wsdlParams['ratingInput']."\n", FILE_APPEND);


        if ($pseudo) {
            // get the order id
            $db = &PearDatabase::getInstance();
            $orderRes = $db->pquery('SELECT orders_id FROM `'.session_id() . '_vtiger_quotes` WHERE quoteid=?', [$pseudoRecordId]);
            if ($orderRes && ($orderInfoRow = $orderRes->fetchRow())) {
                $ordersRecordID = $orderInfoRow['orders_id'];
            }
        }
			$info = $this->RateEstimate($soapProxy, $wsdlParams, $request, 'RateEstimateWithLineItems', $recordId, $ordersRecordID, $arr['survey_upload']['interstate_data']['discounts positive="false"']);

        if (getenv('IGC_MOVEHQ')) {
            if (
                $request->get('sourceModule') == 'Orders' &&
                $request->get('sourceRecord') &&
                $request->get('relationOperation')
            ) {
                    $roleParticipants = Estimates_Record_Model::getParticipatingAgentsForDetailLineItemsFromParentIdStatic($request->get('sourceRecord'));
                } else {
                $roleParticipants = Estimates_Record_Model::getParticipatingAgentsForDetailLineItemsStatic($recordId);
                }
                $moveRoles        = Estimates_Record_Model::getMoveRolesForDetailLineItemsStatic($pseudo ? false : $recordId, false, $ordersRecordID);
        } else {
            $roleParticipants = [];
            $moveRoles        = [];
        }

        ob_start();
        $viewer = $this->getViewer($request);
        $viewer->assign('MODULE_NAME', $request->get('module'));
        $viewer->assign('MODULE', $request->get('module'));
        $viewer->assign('INSTANCE_NAME', getenv('INSTANCE_NAME'));
        $viewer->assign('LINEITEMS', $info['lineitemdetailed']);
        //We need to pull the Participating Agents but we don't have a record model.
        $viewer->assign('ROLES', array_keys($roleParticipants));
        $viewer->assign('ROLESLIST', $roleParticipants);
        $viewer->assign('MOVEROLES', $moveRoles);
        $viewer->assign('APPROVAL', Estimates_Record_Model::getDetailLineItemApprovalList());
        $viewer->assign('BUSINESS_LINE', $business_line_est);
        $viewer->assign('IS_EDIT_VIEW', $requestType == 'editview');
        $current_user = Users_Record_Model::getCurrentUserModel();
        $viewer->assign('dateFormat', $current_user->get('date_format'));
        if(getenv('INSTANCE_NAME') == 'graebel') {
            $viewer->view('DetailLineItemEdit.tpl', 'Estimates');
        } elseif(getenv('IGC_MOVEHQ')) {
            $viewer->view('MoveHQLineItemDetail.tpl', 'Estimates');
        } else {
            $viewer->view('MoveCRMLineItemDetail.tpl', 'Estimates');
        }
        $info['lineitemsView'] = ob_get_contents();
        ob_end_clean();

        if ($requestType != 'editview') {
			//file_put_contents('logs/SaveLog.log', date('Y-m-d H:i:s - ')."Before UpdateServices call\n", FILE_APPEND);
			//file_put_contents('logs/SaveLog.log', date('Y-m-d H:i:s - ')."After UpdateServices call\n", FILE_APPEND);
            if (getenv('IGC_MOVEHQ')) {
                    $roleParticipants = Estimates_Record_Model::getParticipatingAgentsForDetailLineItemsStatic($recordId);
                    $moveRoles        = Estimates_Record_Model::getMoveRolesForDetailLineItemsStatic($recordId);
            } else {
                $roleParticipants = [];
                $moveRoles        = [];
            }
                //I think save this even when it's estimates.
                // don't do this anymore
                // line items are processed client side and sent back to be saved via ajax
                // Except SIRVA lol.
                if($request->get('syncwebservice') && $request->get('syncrate') || getenv("INSTANCE_NAME") == "sirva") {
                    $this->updateDetailedServices($recordId, $info['lineitemdetailed'], $request);
                    if(getenv('INSTANCE_NAME') == 'graebel') {
                        $totals = [];
                        Estimates::updateLineItemTotals($recordId, $totals);
                    }
                }

			//declaration instead of the unset.
			$params = [];
			$sql = "UPDATE `vtiger_quotes` SET subtotal=?, total=?, pre_tax_total=?, rate_estimate=?, interstate_mileage=?";
			$params[] = $info['rateEstimate'];
			$params[] = $info['rateEstimate'];
			$params[] = $info['rateEstimate'];
			$params[] = $info['rateEstimate'];
			$params[] = $info['mileage'];

			//limit to graebel because the billed_weight is not trunk.
			if (getenv('INSTANCE_NAME') == 'graebel') {
				if ($info['billed_weight']) {
                    $sql      .= ", billed_weight=?";
					$params[] = $info['billed_weight'];
				}
				if ($info['guaranteed_price']) {
                    $sql      .= ", guaranteed_price=?";
					$params[] = $info['guaranteed_price'];
				}
            } elseif (getenv('IGC_MOVEHQ')) {
                if ($info['billed_weight']) {
                    $sql      .= ", billed_weight=?";
                    $params[] = $info['billed_weight'];
                }
                if ($info['guaranteed_price']) {
                    $sql      .= ", guaranteed_price=?";
                    $params[] = $info['guaranteed_price'];
                }
            }

            if ($info['valuation_options']) {
                $sql .= ', valuation_options=?';
                $params[] = $info['valuation_options'];
            }
            if ($info['accesorial_fuel_surcharge']) {
                $sql .= ', accesorial_fuel_surcharge=?';
                $params[] = $info['accesorial_fuel_surcharge'];
            }
            $sql      .= " WHERE quoteid=? LIMIT 1";
			$params[] = $recordId;
			$db->pquery($sql, $params);
			unset($params);
		}

        if ($pseudo) {
            foreach ($tempTabNames as $table_name) {
				$sql = "DROP TEMPORARY TABLE IF EXISTS `$table_name`";
				$db->pquery($sql, array());
			}
			// drop temp service provider table
			$table_name = session_id() . '_dli_service_providers';
            $sql = "DROP TEMPORARY TABLE IF EXISTS `$table_name`";
            $db->pquery($sql, array());
		}

		$response = new Vtiger_Response();
		$response->setResult($info);
		$response->emit();
	}

    public function RateEstimate($soapProxy, $wsdlParams, $request, $method, $recordId = false, $ordersID = false, $discounts) {
        $db = &PearDatabase::getInstance();
		$info = [];

		$soapResult = $this->getSoapResult($soapProxy, $wsdlParams, $method);
		//file_put_contents('logs/devLog.log', "\n rateResult : ".print_r($rateResult,true), FILE_APPEND);
		file_put_contents('logs/SaveLog.log', date('Y-m-d H:i:s - ').print_r($soapResult, true)."\n", FILE_APPEND);

        $rateResult       = $soapResult[$method . 'Result']['Totals'];
        $weightResult     = $soapResult[$method . 'Result']['Weights'];
		//Sigh... rateResult was already taken for totals! MADNESS.
        $rateValueResults = $soapResult[$method . 'Result']['Rates'];

        $info['billed_weight'] = max($weightResult['TotalWeight'] + $weightResult['VehicleWeight'], $weightResult['SpecialServicesWeight'] + $weightResult['VehicleWeight']);
		$info['accesorial_fuel_surcharge'] = $rateValueResults['FS'];

        $info['valuation_options'] = base64_encode(json_encode($soapResult['RateEstimateWithLineItemsResult']['ValuationOptions']));

        if(getenv('INSTANCE_NAME') == 'sirva') {
            if (isset($soapResult['RateEstimateWithLineItemsResult']['Rates']['Packing']['PackItems']) && getenv('INSTANCE_NAME') == 'sirva') {
                $crate_rate          = $soapResult['RateEstimateWithLineItemsResult']['Totals']['Packing']['CratingRate'];
                $rates               = $soapResult['RateEstimateWithLineItemsResult']['Rates']['Packing']['PackItems'];
                foreach ($rates as $key => $rate) {
                    if($rate['ItemID'] == 103){
                        $rates[$key]['ItemID'] = 102;
                    }
                }
                $rates['PackItem'][] = ['Packing' => $crate_rate, 'ItemID' => 'R'];
                $info['pack_rates'] = json_encode($rates);
            }
            if (isset($soapResult['RateEstimateWithLineItemsResult']['ValidTill'])) {
                $validTillDate     = new Carbon($soapResult['RateEstimateWithLineItemsResult']['ValidTill']);
                $dateString        = $validTillDate->toDateString();
                $dateArray         = explode('-', $dateString);
                $info['validtill'] = ['year' => $dateArray[0], 'month' => $dateArray[1], 'day' => $dateArray[2]];
            } else {
                $info['validtill'] = '';
            }

            // I don't know whether this should be base or total linehaul... total looks right
            $info['trans_total'] =
                $soapResult['RateEstimateWithLineItemsResult']['Totals']['Trans']['TotalLHDiscounted']
                + $soapResult['RateEstimateWithLineItemsResult']['Totals']['Trans']['DestinationATCDiscounted']
                + $soapResult['RateEstimateWithLineItemsResult']['Totals']['Trans']['OriginATCDiscounted'];

            $vehicles            = $soapResult['RateEstimateWithLineItemsResult']['CorporateVehicles']['CorporateVehicle'];
            $info['stsVehicles'] = '';
            foreach ($vehicles as $vehicile) {
                $info['stsVehicles'] = $info['stsVehicles'].$vehicile['Total'];
                if ($vehicile !== end($vehicles)) {
                    $info['stsVehicles'] = $info['stsVehicles'].':';
                }
            }
            $sql    = "SELECT * FROM `vtiger_quotes` WHERE quoteid = ?";
            $result = $db->pquery($sql, [$recordId]);
            if ($db->num_rows($result) > 0) {
                //update
                $sql = "UPDATE `vtiger_quotes` SET sts_vehicles = ? WHERE quoteid = ?";
                $db->pquery($sql, [$info['stsVehicles'], $recordId]);
            }
            $rateResult = $soapResult['RateEstimateWithLineItemsResult']['Totals'];
            // file_put_contents('logs/devLog.log', "\n DP: soapResult : ".print_r($soapResult, true), FILE_APPEND);
            $info['rateEstimate'] = $rateResult['TotalDiscounted'];
            $info['mileage']      = $rateResult['Trans']['Miles']? :0;
            $info['pricingColor'] = ucwords(strtolower($rateResult['Trans']['TPGDemandColor']));
            $info['pricingLevel'] = $rateResult['Trans']['TPGPricingLevel'];
            $info['grr']['cwt']   = isset($soapResult['RateEstimateWithLineItemsResult']['Rates']['Trans']['TPGGRRRate'])?$soapResult['RateEstimateWithLineItemsResult']['Rates']['Trans']['TPGGRRRate']:0;
            $info['grr']['cp']    = (isset($soapResult['RateEstimateWithLineItemsResult']['Totals']['Packing']['TotalPackingDiscounted']) &&
                                     isset($soapResult['RateEstimateWithLineItemsResult']['Totals']['Packing']['TotalContainerDiscounted']))?
                ($soapResult['RateEstimateWithLineItemsResult']['Totals']['Packing']['TotalPackingDiscounted'] +
                 $soapResult['RateEstimateWithLineItemsResult']['Totals']['Packing']['TotalContainerDiscounted']):0;

            $SMFType      = $request->get('smf_type');
            $percentSMF   = $request->get('percent_smf');
            $flatSMF      = $request->get('flat_smf');
            $desiredTotal = $request->get('desired_total');
            if (!$request->get('pseudoSave')) {
                //when it's not pseudo request is just hte recordID so we need to pull the estimate record
                // to get the values populated.
                $estimateRecord = Vtiger_Record_Model::getInstanceById($recordId);
                $SMFType        = $estimateRecord->get('smf_type');
                $percentSMF     = $estimateRecord->get('percent_smf');
                $flatSMF        = $estimateRecord->get('flat_smf');
                $desiredTotal   = $estimateRecord->get('desired_total');
            }
            $info['TPGMgmtFee'] = ($rateResult['Trans']['TPGMgmtFee'] != 0)?($rateResult['Trans']['TPGMgmtFee']):$flatSMF? :0;
            $info['TPGGRRRate'] = ($soapResult['RateEstimateWithLineItemsResult']['Rates']['Trans']['TPGGRRRate'] != 0)?($soapResult['RateEstimateWithLineItemsResult']['Rates']['Trans']['TPGGRRRate']) : 0;

            if(!$request->isEditView()) {
                $sql = "UPDATE `vtiger_quotes` SET pack_rates=? WHERE quoteid=?";
                $db->pquery($sql, [base64_encode($info['pack_rates']), $recordId]);
                if (!empty($info['validtill'])) {
                    $dateArr  = $info['validtill'];
                    $params[] = $dateArr['year'].'-'.$dateArr['month'].'-'.$dateArr['day'];
                    $params[] = $recordId;
                    $sql      = "UPDATE `vtiger_quotes` SET validtill=? WHERE quoteid=?";
                    $db->pquery($sql, $params);
                    unset($params);
                }
                $sql = "UPDATE `vtiger_quotes` SET grr_estimate=? WHERE quoteid=?";
                $db->pquery($sql, [$info['grr']['cp'], $recordId]);
            }

            $info['desired_total'] = $desiredTotal;
            if ($info['desired_total'] == '' || $info['desired_total'] == 0) {
                if ($SMFType) {
                    $total                 = $info['lineitems']['Transportation'] - $flatSMF;
                    $info['newPercentSMF'] = ($flatSMF / $total) * 100;
                } else {
                    $info['newFlatSMF'] = number_format($info['TPGMgmtFee'], 2, '.', '');
                }
            } else {
                $info['newFlatSMF']    = number_format($info['TPGMgmtFee'], 2, '.', '');
                $total                 = $info['lineitems']['Transportation'] - $info['newFlatSMF'];
                $info['newPercentSMF'] = ($info['newFlatSMF'] / $total) * 100;
            }
            $sql    = "UPDATE `vtiger_quotes` SET pricing_color=?, flat_smf=?, percent_smf=?, desired_total=? WHERE quoteid=?";
            $result =
                $db->pquery($sql,
                            [$info['pricingColor'],
                             (($info['newFlatSMF'])?$info['newFlatSMF']:$flatSMF),
                             (($info['newPercentSMF'])?$info['newPercentSMF']:$percentSMF),
                             $info['desired_total'],
                             $recordId]);

            $fuelSurcharge = $soapResult['RateEstimateWithLineItemsResult']['Rates']['FS'];
            $sql = "UPDATE `vtiger_quotes` SET tpg_transfactor=?, accesorial_fuel_surcharge = ? WHERE quoteid=?";
            $db->pquery($sql, [$rateResult['Trans']['TPGTransFactor'], $fuelSurcharge, $recordId]);

            $info['tpg_transfactor']           = $rateResult['Trans']['TPGTransFactor'];
            $info['accesorial_fuel_surcharge'] = $fuelSurcharge;
        }

		if (getenv('INSTANCE_NAME') == 'graebel') {
			if (
				$request->get('quotation_type') == 'Guaranteed' ||
				$request->get('quotation_type') == 'Guranteed Not to Exceed'
			) {
				$info['guaranteed_price'] = $rateResult['TotalDiscounted'];
			}
        } elseif (getenv('IGC_MOVEHQ')) {
            if (
                $request->get('quotation_type') == 'Guaranteed' ||
                $request->get('quotation_type') == 'Guranteed Not to Exceed'
            ) {
                $info['guaranteed_price'] = $rateResult['TotalDiscounted'];
            }
        }

		$info['rateEstimate'] = $rateResult['TotalDiscounted'];
		$info['mileage'] = $rateResult['Trans']['Miles'];

		//@TODO: break to functions.
        if ($method == 'RateEstimateWithLineItems') {
            $lineItemsRes = $soapResult['RateEstimateWithLineItemsResult']['LineItems']['LineItem'];
            //return can be array of 1 thing's array or an array of many thing's arrays
            if (array_key_exists('Description', $lineItemsRes)) {
                $lineItems[] = $lineItemsRes;
            } else {
                $lineItems = $lineItemsRes;
            }
            $correctedLineItems = [];
            $currentUser        = Users_Record_Model::getCurrentUserModel();
            $firstParticipatingAgent = [
                'ParticipantID'   => '',
                'ParticipantName' => '',
            ];
            $firstMoveRole = [
                'ServiceProvider'     => '',
                'ServiceProviderName' => '',
            ];
            if(getenv('IGC_MOVEHQ')) {
            if ($roleParticipants = Estimates_Record_Model::getParticipatingAgentsForDetailLineItemsStatic($recordId)) {
                $ParticipantRole         = key($roleParticipants);
                $firstParticipatingAgent = array_shift($roleParticipants);
                if ($firstParticipatingAgent) {
                    $firstParticipatingAgent['ParticipantRole']   = $ParticipantRole;
                    $firstParticipatingAgent['ParticipantRoleID'] = $firstParticipatingAgent['agents_id'];
                    $firstParticipatingAgent['ParticipantName']   = Estimates_Record_Model::getParticipatingAgentsInfoForDetailLineItems($firstParticipatingAgent['agents_id']);
                }
            }
            if ($moveRoles = Estimates_Record_Model::getMoveRolesForDetailLineItemsStatic($request->get('pseudoSave')?false:$recordId, false, $ordersID)) {
                $firstMoveRole = array_shift($moveRoles);
                if ($firstMoveRole) {
                    $firstMoveRole['ServiceProvider']     = $firstMoveRole['id'];
                    $firstMoveRole['ServiceProviderName'] = Estimates_Record_Model::getMoveRolesInfoForDetailLineItems($firstMoveRole['id']);
                }
            }
            }
            $formatLineDefaults = [
                'moveRole'           => $firstMoveRole,
                'participatingAgent' => $firstParticipatingAgent,
                //@TODO: comment/remove this if approval should not default.
                'approval'           => Estimates_Record_Model::getDetailLineItemApprovalList()[0],
                'sequence'           => 0,
                'discounts'          => $discounts
            ];
            // OT 3343
            //            $sp = [[
            //                       'vendor_id' => $firstMoveRole['ServiceProvider'],
            //                       'name'      => $firstMoveRole['ServiceProviderName']
            //                   ]];
            $sp = [[
                       'vendor_id' => '',
                       'name'      => ''
                   ]];
            $isSirva = getenv("INSTANCE_NAME") == "sirva";
            foreach ($lineItems as $key => $singleLineItem) {
                $itemName = $singleLineItem['Description'];
                $location = $singleLineItem['Location'];
                if (is_array($singleLineItem['Detail']) && is_array($singleLineItem['Detail']['LineItemDetail'][0])) {
                    foreach ($singleLineItem['Detail']['LineItemDetail'] as $count => $lineItemParts) {
                        $formatLineDefaults['sequence'] += 1;
                        $line                            = $this->formatLineItems($lineItemParts, $currentUser, $formatLineDefaults);
                        $line['ServiceProviders']        = $sp;
                        if($isSirva) {
                            $line['Location']            = $location;
                        }
                        $correctedLineItems[$itemName][] = $line;
                    }
                } elseif (is_array($singleLineItem['Detail']) && is_array($singleLineItem['Detail']['LineItemDetail'])) {
                    //this is a single lineitem.
                    $formatLineDefaults['sequence'] += 1;
                    $line                            = $this->formatLineItems($singleLineItem['Detail']['LineItemDetail'], $currentUser, $formatLineDefaults);
                    $line['ServiceProviders']        = $sp;
                    if($isSirva) {
                        $line['Location']            = $location;
                    }
                    $correctedLineItems[$itemName][] = $line;
                } elseif ($itemName && $singleLineItem['Gross']) {
                    //pass true to enable backup description mode, which is more detailed.
                    $formatLineDefaults['sequence'] += 1;
                    $line                            = $this->formatLineItems($singleLineItem, $currentUser, $formatLineDefaults, true);
                    $line['ServiceProviders']        = $sp;
                    $correctedLineItems[$itemName][] = $line;
                }
                /** END * */
            }
            if (getenv('INSTANCE_NAME') == 'graebel') {
                // OT 16259 - roll up packing/unpacking lines
                // Do it for all tariffs for graebel
                $this->rollupPackingUnpackingItems($correctedLineItems);
            }
            $info['lineitemdetailed'] = $correctedLineItems;
        }

		return $info;
	}

    protected function getSoapResult($soapProxy, $wsdlParams, $method)
    {
        if (!method_exists($soapProxy, $method)) {
            $response = new Vtiger_Response();
            $response->setError('Error Processing Request', 'Method ' . $method . ' not found.');
            $response->emit();
            //why isn't this a throw exception?
            return;
        }
        $soapResult = $soapProxy->$method($wsdlParams);

        if (!$soapResult) {
            //So we are breaking here!
            throw new Exception('Error querying rating service.'.PHP_EOL.$soapProxy->getError());
        }
        if ($soapResult['faultcode']) {
            //@TODO: This may still need fixed up for better communication.
            $errorMessage = 'Error querying rating service.';
            if ($soapResult['faultstring']['!']) {
                $errorMessage .= "\n(".$soapResult['faultstring']['!'].")\n";
            }
            throw new Exception($errorMessage);
        }

		return $soapResult;
    }
}
