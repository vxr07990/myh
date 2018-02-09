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
class Estimates_GetLocalRate_Action extends Estimates_QuickEstimate_Action
{
    public function __construct()
    {
		parent::__construct();
	}

    public function process(Vtiger_Request $request)
    {
		file_put_contents('logs/xml.log', date('Y-m-d H:i:s - ')."Request : ".print_r($request, true)."\n");
		$requestType = $request->get('type');
		$recordId = $request->get('record');
        $business_line_est = $request->get('business_line_est');
        if (empty($recordId)) {
			$recordId = $request->get('sourceRecord');
		}

		$pseudo = $request->get('pseudoSave') == '1';

		//If pseudo-save is requested, create temp tables
        if ($pseudo) {
			$db = &PearDatabase::getInstance();
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
                                  'vtiger_quotes_cwtperqty' => 'estimateid',
                                  'vtiger_quotes_cwtbyweight' => 'estimateid',
								  'vtiger_inventoryshippingrel' => 'id',
								  'vtiger_inventorysubproductrel' => 'id',
								  'vtiger_packing_items' => 'quoteid',
								  'vtiger_misc_accessorials' => 'quoteid',
								  'vtiger_crates' => 'quoteid',
								  'vtiger_bulky_items' => 'quoteid',
								  'vtiger_extrastops' => 'extrastops_relcrmid',
                  'vtiger_quotes_storage_valution' => 'estimateid',
								  'vtiger_quotes_servicecharge' => 'estimateid',
                  'vtiger_quotes_flatratebyweight' => 'estimateid',
                  'vtiger_quotescf' => 'quoteid');

			$tempTabNames = array();

            $crmIdLookup = [$recordId];

            $vehicleLookupModel = Vtiger_Module_Model::getInstance('VehicleLookup');
            if ($vehicleLookupModel && $vehicleLookupModel->isActive()) {
                $customTables['vtiger_vehiclelookup'] = 'crmid';
            }

            if (getenv('INSTANCE_NAME') == 'sirva') {
                $customTables['vtiger_corporate_vehicles'] = 'estimate_id';
                $customTables['vtiger_addresssegments'] = 'addresssegmentsid';
                $customTables['vtiger_addresssegmentscf'] = 'addresssegmentsid';
                $customTables['vtiger_quotes_sit'] = 'estimateid';
            } elseif (getenv('INSTANCE_NAME') == 'graebel') {
                $customTables['vtiger_quotes_cwtbyweight'] = 'estimateid';
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


            foreach ($focus->tab_name as $table_name) {
				$tempTableName = session_id().'_'.$table_name;
				$tempTabNames[] = $tempTableName;
				file_put_contents('logs/xml.log', date('Y-m-d H:i:s - ')."Dropping $tempTableName\n", FILE_APPEND);
				$sql = "DROP TEMPORARY TABLE IF EXISTS $tempTableName";
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
                    $db->pquery($sql);
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
                              ON (dli_service_providers.dli_id=vtiger_detailed_lineitems.detaillineitemsid)";
                    $db->pquery($sql);
                }
			}

			$saveAction = new Estimates_Save_Action;

			$saveAction->process($request); //Remove Data
		}

		$arr = MoveCrm\arrayBuilder::buildArray($request->get('record'), $pseudo);
		$xml = MoveCrm\xmlBuilder::build($arr);
		// $local = true;
		// include_once('generatexml.php'); //Generates $xml variable using values contained in $allFields

		file_put_contents('logs/xmlRework.xml', $xml."\n");
		unset($wsdlParams);
		//$xml = file_get_contents('logs/localRating.xml');
		$wsdlParams['caller'] = 'VnbZ1BjT4xtFyCKj21Xr';
		$wsdlParams['ratingInput'] = base64_encode($xml);
		//$wsdlParams['ratingInput'] = $xml;
		//$
		$wsdlURL = getenv('LOCAL_RATING_URL');
        // use RatingEngineDev instead of RatingEngine if .env is set
        if(getenv('INSTANCE_NAME') == 'sirva' && getenv('USE_DEV_RATING_ENGINE')) {
            $wsdlURL = getenv('LOCAL_DEV_RATING_URL');
        }elseif (getenv('USE_DEV_RATING_ENGINE')) {
            $wsdlURL = str_replace('/RatingEngine/', '/RatingEngineDev/', $wsdlURL);
        }

		$soapclient = new soapclient2($wsdlURL, 'wsdl');

		$soapclient->setDefaultRpcParams(true);
		$soapProxy = $soapclient->getProxy();

		$errors = array('fault'=>$soapProxy->fault,'faultcode'=>$soapProxy->faultcode,'faultstring'=>$soapProxy->faultstring,'faultdetail'=>$soapProxy->faultdetail,'error_str'=>$soapProxy->error_str);

        if (!method_exists($soapProxy, 'RateLocalEstimate')) {
			$response = new Vtiger_Response();
			$response->setError('Error Processing Request', 'Rate method not found.');
			$response->emit();
            return;
		}


		$soapResult = $soapProxy->RateLocalEstimate($wsdlParams);

		//file_put_contents('logs/devLog.log', "\n WSDL URL: $wsdlURL", FILE_APPEND);
		//file_put_contents('logs/devLog.log', "\n WSDL PARAMS: ".print_r($wsdlParams, true), FILE_APPEND);
		//file_put_contents('logs/devLog.log', "\n SoapResult : ".print_r($soapResult, true), FILE_APPEND);
		$errors = array('fault'=>$soapProxy->fault,'faultcode'=>$soapProxy->faultcode,'faultstring'=>$soapProxy->faultstring,'faultdetail'=>$soapProxy->faultdetail,'error_str'=>$soapProxy->error_str);
		//file_put_contents('logs/soapProxy.log', "\n soapProxy after : ".print_r($soapProxy, true), FILE_APPEND);
		$rateItems = $soapResult['RateLocalEstimateResult']['LocalRating'];
        if ($errors['faultstring']['!']) {
			$response = new Vtiger_Response();
			$response->setError($errors['faultstring']['!']);
			$response->emit();
			return false;
		}

        $info['rateEstimate'] = $soapResult['RateLocalEstimateResult']['GrandTotalDiscounted'];
        $info['miles'] = $soapResult['RateLocalEstimateResult']['Miles'];
        if (empty($soapResult['RateLocalEstimateResult']['rating']['Sections']['LocalSection'][0])) {
			$temp = array();
            foreach ($soapResult['RateLocalEstimateResult']['rating']['Sections']['LocalSection'] as $key=>$value) {
                $temp[$key] = $value;
				unset($soapResult['RateLocalEstimateResult']['rating']['Sections']['LocalSection'][$key]);
			}
			$soapResult['RateLocalEstimateResult']['rating']['Sections']['LocalSection'][] = $temp;
		}
        foreach ($soapResult['RateLocalEstimateResult']['rating']['Sections']['LocalSection'] as $section) {
			//$info['lineitems']['linename'] = $value;
			// Add subtotals to lineitems normally if not SIRVA or if the line item needs no changing.
            $info['lineitems'][$section['Name']] = $section['TotalDiscounted'];
		}
        $current_user = Users_Record_Model::getCurrentUserModel();
		// create detailed line items
        $services = [];
        $data = $soapResult['RateLocalEstimateResult']['rating']['Sections']['LocalSection'];
        $lineItems = [];
        foreach ($data as $section) {
            if (empty($section['Services']['LocalService'][0])) {
                self::createLineItem($section['Services']['LocalService'], $section, $services, $lineItems);
            } else {
                foreach ($section['Services']['LocalService'] as $service) {
                    self::createLineItem($service, $section, $services, $lineItems, $current_user);
                }
            }
        }
        $info['lineitemdetailed'] = $lineItems;

        if ($requestType == 'editview') {
            if (getenv('IGC_MOVEHQ')) {
                if ($request->get('sourceModule') == 'Orders' && $request->get('sourceRecord') && $request->get('relationOperation' && getenv('INSTANCE_NAME') == 'graebel')) {
                    $roleParticipants = Estimates_Record_Model::getParticipatingAgentsForDetailLineItemsFromParentIdStatic($request->get('sourceRecord'));
                } else {
                    $roleParticipants = Estimates_Record_Model::getParticipatingAgentsForDetailLineItemsStatic($recordId);
                }
                $moveRoles = Estimates_Record_Model::getMoveRolesForDetailLineItemsStatic($pseudo?false:$recordId, false, $ordersRecordID);
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
            $viewer->assign('dateFormat', $current_user->get('date_format'));
            //$viewer->view('DetailLineItemEdit.tpl', $request->get('module'));
            if(getenv('INSTANCE_NAME') == 'graebel') {
                $viewer->view('DetailLineItemEdit.tpl', 'Estimates');
            } elseif (getenv('IGC_MOVEHQ')) {
                $viewer->view('MoveHQLineItemDetail.tpl', 'Estimates');
            } else {
                $viewer->view('MoveCRMLineItemDetail.tpl', 'Estimates');
            }
            $info['lineitemsView'] = ob_get_contents();
            ob_end_clean();
        } else {
            if (getenv('IGC_MOVEHQ')) {
                $roleParticipants = Estimates_Record_Model::getParticipatingAgentsForDetailLineItemsStatic($recordId);
                $moveRoles        = Estimates_Record_Model::getMoveRolesForDetailLineItemsStatic($recordId);
            } else {
                $roleParticipants = [];
                $moveRoles        = [];
            }
            if($request->get('syncwebservice') && $request->get('syncrate')) {
                $this->updateDetailedServices($recordId, $info['lineitemdetailed'], $request);
                if(getenv('INSTANCE_NAME') == 'graebel') {
                    $totals = [];
                    Estimates::updateLineItemTotals($recordId, $totals);
                }
            }
            ob_start();
            $viewer = $this->getViewer($request);
            $viewer->assign('MODULE_NAME', $request->get('module'));
            $viewer->assign('MODULE', $request->get('module'));
            $viewer->assign('INSTANCE_NAME', getenv('INSTANCE_NAME'));
            $viewer->assign('LINEITEMS', $info['lineitemdetailed']);
            $viewer->assign('ROLES', array_keys($roleParticipants));
            $viewer->assign('ROLESLIST', $roleParticipants);
            $viewer->assign('MOVEROLES', $moveRoles);
            $viewer->assign('APPROVAL', Estimates_Record_Model::getDetailLineItemApprovalList());
            $viewer->assign('BUSINESS_LINE', $business_line_est);
            $viewer->assign('IS_EDIT_VIEW', $requestType == 'editview');
            //$viewer->view('DetailLineItemDetail.tpl', $request->get('module'));
            $current_user = Users_Record_Model::getCurrentUserModel();
            $viewer->assign('dateFormat', $current_user->get('date_format'));
            if(getenv('INSTANCE_NAME') == 'graebel') {
                $viewer->view('DetailLineItemDetail.tpl', 'Estimates');
            } elseif (getenv('IGC_MOVEHQ')) {
                $viewer->view('MoveHQLineItemDetail.tpl', 'Estimates');
            } else {
                $viewer->view('MoveCRMLineItemDetail.tpl', 'Estimates');
            }
            $info['lineitemsView'] = ob_get_contents();
            ob_end_clean();
        }

        //file_put_contents('logs/SaveLog.log', date('Y-m-d H:i:s - ')."Before UpdateServices call\n", FILE_APPEND);
        $db = &PearDatabase::getInstance();
        if ($requestType != 'editview') {

            $total = $soapResult['RateLocalEstimateResult']['GrandTotalDiscounted'];
            // We could try other options as well but honestly if GrandTotalDiscounted isn't set there's larger issues.

            //file_put_contents('logs/SaveLog.log', date('Y-m-d H:i:s - ')."After UpdateServices call\n", FILE_APPEND);

            $sql = "UPDATE `vtiger_quotes` SET subtotal=? WHERE quoteid=?";
            unset($params);
            $params[] = $total;
            $params[] = $total;
            $params[] = $total;
            $params[] = $info['miles'];
            $params[] = $recordId;
            $result = $db->pquery($sql, $params);


            $sql = "UPDATE `vtiger_quotes` SET total=?, pre_tax_total=?, rate_estimate=?, interstate_mileage=? WHERE quoteid=?";
            $result = $db->pquery($sql, $params);
            unset($params);
        }


		//Drop temp tables if they exist
        if ($pseudo) {
            foreach ($tempTabNames as $table_name) {
				file_put_contents('logs/xml.log', date('Y-m-d H:i:s - ')."Dropping $table_name\n", FILE_APPEND);
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

    protected static $codeToUnitMap = [
        'Base Plus Trans.' => 'Base',
        'Break Point Trans.' => 'Base',
        'Weight/Mileage Trans.' => 'Base',
        'Bulky List' => 'EA',
        'Charge Per $100 (Valuation)' => 'HND',
        'County Charge' => 'Base',
        'Crating Item' => 'CF',
        'Flat Charge' => 'Base',
        'Hourly Avg Lb/Man/Hour' => 'EA',
        'Hourly Set' => 'HRS',
        'Hourly Simple' => 'HRS',
        'Packing Items' => 'EA',
        'Per Cu Ft/Per Day' => 'CF',
        'Per Cu Ft/Per Month' => 'CF',
        'Per CWT' => 'CWT',
        'Per CWT/Per Day' => 'CWT',
        'Per CWT/Per Month' => 'CWT',
        'Per Quantity' => 'EA',
        'Per Quantity/Per Day' => 'EA',
        'Per Quantity/Per Month' => 'EA',
        'Tabled Valuation' => 'Base',
        'CWT by Weight' => 'CWT',
        'Service Base Charge' => 'PCT',
    ];

    protected static function createLineItem($service, $section, &$services, &$lineItems, $currentUser, $fieldPrefix='')
    {
        // need to set Cost, CostNet, Description, DiscountPct, Quantity, Rate, RateNet, RatingItem, Schedule, TariffCode, Weight
        $item = [];
        if (is_array($service['PackingItems'])) {
            if (is_array($service['PackingItems']['LocalService'][0])) {
                foreach ($service['PackingItems']['LocalService'] as $packingItem) {
                    $packingItem['Id'] = $service['Id'];
                    self::createLineItem($packingItem, $section, $services, $lineItems, $currentUser, 'Container');
                    self::createLineItem($packingItem, $section, $services, $lineItems, $currentUser, 'Pack');
                    self::createLineItem($packingItem, $section, $services, $lineItems, $currentUser, 'Unpack');
                }
            } else {
                $service['PackingItems']['LocalService']['Id'] = $service['Id'];
                self::createLineItem($service['PackingItems']['LocalService'], $section, $services, $lineItems, $currentUser, 'Container');
                self::createLineItem($service['PackingItems']['LocalService'], $section, $services, $lineItems, $currentUser, 'Pack');
                self::createLineItem($service['PackingItems']['LocalService'], $section, $services, $lineItems, $currentUser, 'Unpack');
            }
            return;
        }
        if (is_array($service['CrateItems'])) {
            if (is_array($service['CrateItems']['LocalService'][0])) {
                foreach ($service['CrateItems']['LocalService'] as $packingItem) {
                    $packingItem['Id'] = $service['Id'];
                    self::createLineItem($packingItem, $section, $services, $lineItems, $currentUser, 'Crating');
                    self::createLineItem($packingItem, $section, $services, $lineItems, $currentUser, 'Uncrating');
                }
            } else {
                $service['CrateItems']['LocalService']['Id'] = $service['Id'];
                self::createLineItem($service['CrateItems']['LocalService'], $section, $services, $lineItems, $currentUser, 'Crating');
                self::createLineItem($service['CrateItems']['LocalService'], $section, $services, $lineItems, $currentUser, 'Uncrating');
            }
            return;
        }
        if (is_array($service['BulkyItems'])) {
            if (is_array($service['BulkyItems']['LocalService'][0])) {
                foreach ($service['BulkyItems']['LocalService'] as $packingItem) {
                    $packingItem['Id'] = $service['Id'];
                    $packingItem['Description'] = preg_replace('/\s\d$/', '', $packingItem['Description']);
                    self::createLineItem($packingItem, $section, $services, $lineItems, $currentUser, '');
                }
            } else {
                $service['BulkyItems']['LocalService']['Id'] = $service['Id'];
                self::createLineItem($service['BulkyItems']['LocalService'], $section, $services, $lineItems, $currentUser, '');
            }
            return;
        }
        // get the serviceInfo
        if (array_key_exists($service['Id'], $services)) {
            $serviceInfo = $services[$service['Id']];
        } elseif ($service['Id'] != 0) {
            $db = &PearDatabase::getInstance();
            $res = $db->pquery('SELECT * FROM `vtiger_tariffservices` WHERE tariffservicesid=?', [$service['Id']]);
            if ($res && ($row = $res->fetchRow())) {
                $serviceInfo = $row;
                if ($row['service_code']) {
                    $index = strpos($row['service_code'], ' - ');
                    $serviceInfo['code'] = substr($row['service_code'], 0, $index);
                    //$serviceInfo['desc'] = substr($row['service_code'], $index + 3);
                }
                $services[$service['Id']] = $serviceInfo;
            }
        } else {
            $serviceInfo = [
                'code' => 'ADV_CHARGE',
                'rate_type' => $service['Description'],
                'invoiceable' => '1',
                'distributable' => '1',
                'service_name' => $service['Description'],
                'desc' => $service['Name']
            ];
        }
        $item['TariffItem'] = $serviceInfo['code'];
        $item['TariffSection'] = $section['Name'];
        $unit = self::$codeToUnitMap[$serviceInfo['rate_type']];
        if (!$unit) {
            $unit = 'Base';
        }
        if ($serviceInfo['rate_type'] == 'Bulky List') {
            $index = strrpos($service['Description'], ' ');
            $cnt = substr($service['Description'], $index+1);
            if (!$cnt) {
                // 0 count
                return;
            }
            $item['Description'] = $service['Description'];
            // $item['Quantity'] = $cnt;
        } elseif($serviceInfo['rate_type'] == 'County Charge') {
            $item['Description'] = explode('/',$service['Description'])[0];
        } else {
            $item['Description'] = $serviceInfo['desc'] ?: $serviceInfo['service_name'];
        }
        $totalName = 'Total';
        if($fieldPrefix == 'Container') {
            $totalName .= 'WithSalesTax';
        }
        // Check in case interstate packing doesn't have this node.
        if(!$service[$fieldPrefix.$totalName]) {
            $totalName = 'Total';
        }

        if ($fieldPrefix) {
            if ($fieldPrefix == 'Crating' || $fieldPrefix == 'Uncrating') {
                $service['Name'] = $service['CrateDescription'];
            }
            $item['Description'] .= ' - ' . $fieldPrefix . ' ' . $service['Name'];
            $service['TotalDiscounted'] = $service[$fieldPrefix.$totalName] * (100 - $service['DiscountPercent']) / 100;
        }
        $item['Invoiceable'] = $service['Invoiceable'] ?: ($serviceInfo['invoiceable'] === '0' ? '0' : '1');
        $item['Distributable'] = $service['Distributable'] ?: ($serviceInfo['distributable'] === '0' ? '0' : '1');
        $item['InvoiceDiscountPct'] = $service['DiscountPercent'];
        $item['DistributableDiscountPct'] = $service['DiscountPercent'];

        if ($unit == 'Base') {
            $item['BaseRate'] = CurrencyField::convertToUserFormat($service[$fieldPrefix.$totalName], $currentUser);
        } elseif ($unit == 'PCT') {
            $item['UnitOfMeasurement'] = $unit;
            $item['Quantity'] = $service[$fieldPrefix.'Rate'] / 100;
            $item['UnitRate'] = CurrencyField::convertToUserFormat($service[$fieldPrefix.$totalName]*100 / $service[$fieldPrefix.'Rate']);
        } else {
            $item['UnitOfMeasurement'] = $unit;
            $item['UnitRate'] = $service[$fieldPrefix.'Rate'];
            if($fieldPrefix == 'Container') {
                $item['Quantity'] = $item['Quantity'] ?: $service[$fieldPrefix.'Quantity'];
            } elseif ($fieldPrefix == 'Crating' || $fieldPrefix == 'Uncrating') {
                $item['Quantity'] = $item['Quantity'] ?: $service[$fieldPrefix.$totalName] / $service[$fieldPrefix.'Rate'] / $service['Cube'];
            } else {
                $item['Quantity'] = $item['Quantity'] ?: CurrencyField::convertToUserFormat($service[$fieldPrefix.$totalName] / $service[$fieldPrefix.'Rate']);
            }
        }
        $item['Rate_Net'] = CurrencyField::convertToUserFormat($service[$fieldPrefix.'RateNet'], $currentUser);
        $item['Gross'] = CurrencyField::convertToUserFormat($service[$fieldPrefix.$totalName], $currentUser);
        $item['InvoiceCostNet'] = CurrencyField::convertToUserFormat($service['TotalDiscounted'], $currentUser);
        $item['DistributableCostNet'] = CurrencyField::convertToUserFormat($service['TotalDiscounted'], $currentUser);

        //@NOTE: OT17300 addition to set these values to empty if the flags are NOT set.
        $item['InvoiceCostNet'] = self::ensureCostAllowed($item['InvoiceCostNet'], $item['Invoiceable']);
        $item['DistributableCostNet'] = self::ensureCostAllowed($item['DistributableCostNet'], $item['Distributable']);

        $sp = [[
                   'vendor_id' => '',
                   'name'      => ''
               ]];
        $item['ServiceProviders'] = $sp;

        //@NOTE: Once again, Gross totals can be negative. e.g. Misc Items.
        if ($item['Gross'] != 0 || $item['Quantity'] > 0) {
            // Leaving this here because we should, in theory, transition to the
            // ids instead of the names.
            // $serviceid = Estimates_QuickEstimate_Action::getServiceIds([$item['TariffSection']=>'']);
            $parentItem = $item['TariffSection'];
            if($item['TariffSection'] == 'Packing' && strpos($item['Description'], 'Unpack')) {
              $parentItem = 'Unpacking';
            }
            if($item['TariffSection'] == 'Packing' && strpos($item['Description'], 'Container')) {
              $parentItem = 'Containers';
            }
            $item['RatingItem'] = self::getRatingItem($item['Description'],$parentItem);
            $lineItems[$parentItem][] = $item;
        }
    }

	/*
	 * pushed to parent leaving until I am like positive.
	protected function getServiceIds($services) {
		$db         = PearDatabase::getInstance();
		$serviceIds = [];
		foreach ($services as $service => $rate) {
			$sql    = "SELECT serviceid,service_no FROM `vtiger_service` WHERE servicename=?";
			$result = $db->pquery($sql, [$service]);
			$row    = $result->fetchRow();
			if ($row == NULL) {
				continue;
			}
			$serviceIds[$service] = substr($row['service_no'], 3);
			//$serviceIds[$service] = $row['serviceid'];
		}

		return $serviceIds;
	}
	*/
  protected static function getRatingItem($description,$parentItem) {
    $description = explode('-',$description);
    $description = preg_replace('/\s+/', '_', trim($description[1]));
    $description = preg_replace('/\s+/', '_', $parentItem) . "_" . $description;
    return $description;
  }
}
