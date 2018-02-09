<?php
/**
 * @package			moveCRM
 * @file 			QuickEstimate.php
 * @description 	Extended functionality from the Quotes module so we can add to
 *                  it without having to deal with changing the core vtiger code
 * @author 			Written by Ryan Paulson, Hacked by Louis Robinson
 * @copyright		IGC Software
 */
require_once('libraries/nusoap/nusoap.php');
include_once 'include/Webservices/Create.php';
include_once 'include/Webservices/Utils.php';
class Estimates_QuickEstimate_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
		parent::__construct();
	}

    public function process(Vtiger_Request $request)
    {
		$recordId = $request->get('record');
		//file_put_contents('logs/QuickEstimate.log', date("Y-m-d H:i:s")." - ".print_r($request, true)."\n", FILE_APPEND);
		$db = PearDatabase::getInstance();

		$sql = "SELECT weight, pickup_date, pickup_time, origin_zip, destination_zip, full_pack, full_unpack, bottom_line_discount, valuation_deductible, valuation_amount, effective_tariff FROM `vtiger_quotes` JOIN `vtiger_quotescf` ON vtiger_quotes.quoteid=vtiger_quotescf.quoteid WHERE vtiger_quotes.quoteid=?";
		$params[] = $recordId;

		$result = $db->pquery($sql, $params);
		unset($params);
		$row = $result->fetchRow();

        if ($row == null) {
            return;
        }

		$fullPack = ($row[5] == 1 ? "true" : "false");
		$fullUnpack = ($row[6] == 1 ? "true" : "false");
		$valDeductible = '';
		//file_put_contents('logs/devLog.log', "\n row[8] : ".$row[8],FILE_APPEND);
        if ($row[8] == "60¢ /lb.") {
			$valDeductible = "SIXTY_CENTS";
        } elseif ($row[8] == "FVP - $0") {
			$valDeductible = "ZERO";
        } elseif ($row[8] == "FVP - $250") {
			$valDeductible = "TWO_FIFTY";
        } elseif ($row[8] == "FVP - $500") {
			$valDeductible = "FIVE_HUNDRED";
		}

		$params = array();

		$params['caller'] = "VnbZ1BjT4xtFyCKj21Xr";
		$params['weight'] = str_replace(",", "", $row[0]);
		$params['pickupDate'] = $row[1]."T00:00:00";
		$params['originZip'] = $row[3];
		$params['destinationZip'] = $row[4];
		$params['fuelPrice'] = 0;
		$params['fullPackApplied'] = $fullPack;
		$params['fullUnpackApplied'] = $fullUnpack;
		$params['bottomLineDiscount'] = $row[7];
		$params['valDeductible'] = $valDeductible;
		$params['valuationAmount'] = str_replace(",", "", $row[9]);

		//$rateEstimateURL = "index.php?module=Quotes&action=GetRateEstimate&record=".$request->get('record')."&weight=".$row[0]."&pickupDateTime=".$row[1]."T00:00:00&originZip=".$row[3]."&destinationZip=".$row[4]."&fuelPrice=0&fullPackApplied=".$fullPack."&fullUnpackApplied=".$fullUnpack."&bottomLineDiscount=".$row[7]."&valDeductible=".$valDeductible."&valuationAmount=".$row[9];

		//file_put_contents('logs/QuickEstimate.log', $rateEstimateURL."\n", FILE_APPEND);

		$tariffId = $row[10];

		$sql = "SELECT rating_url FROM `vtiger_tariffmanager` WHERE tariffmanagerid=?";
		$result = $db->pquery($sql, array($tariffId));
		$row = $result->fetchRow();

		$wsdl = $row[0];

		//file_put_contents('logs/TariffBasedRating.log', date('Y-m-d H:i:s - ').$wsdl."\n", FILE_APPEND);
        if (isset($wsdl) && substr($wsdl, 0, 4) != 'http') {
			$captureData = array();
			$captureData['record'] = $recordId;
			$captureData['tariff'] = $tariffId;
			$captureData['rating_url'] = $wsdl;
			$captureData['userid'] = Users_Record_Model::getCurrentUserModel()->getId();
			$captureData['error_message'] = "Invalid URL provided for rating";
			file_put_contents('logs/RatingErrors.log', date('Y-m-d H:i:s - ').print_r($captureData, true)."\n", FILE_APPEND);
            $this->error = true;
            $this->errorCode = 'Invalid URL provided for rating';
            $this->errorMessage = 'Please contact IGC Support for assistance.';
			$response = new Vtiger_Response();
            $response->setError($this->errorCode, $this->errorMessage);
			$response->emit();
			return;
		}

		//$wsdl = 'https://aws.igcsoftware.com/RatingEngine/RatingService.svc?wsdl';
		file_put_contents('logs/devLog.log', "\nQuickEstimateURL : ".$wsdl, FILE_APPEND);
        file_put_contents('logs/devLog.log', "\nparams : ".print_r($params, true), FILE_APPEND);

		$soapclient = new soapclient2($wsdl, 'wsdl');
		$soapclient->setDefaultRpcParams(true);
		$soapProxy = $soapclient->getProxy();
		//$soapProxy2 = $soapclient->getProxyClassCode();
		//file_put_contents('logs/soap.log', $soapProxy2."\n", FILE_APPEND);
		$soapResult = $soapProxy->RateEstimateSimpleFullReturn($params);

        file_put_contents('logs/devLog.log', "\$soapResult : ".print_r($soapResult, true), FILE_APPEND);

        if (array_key_exists('faultcode', $soapResult)) {
			$captureData = array();
			$captureData['record'] = $recordId;
			$captureData['tariff'] = $tariffId;
			$captureData['rating_url'] = $wsdl;
			$captureData['userid'] = Users_Record_Model::getCurrentUserModel()->getId();
			$captureData['error_message'] = $soapResult['detail']['ExceptionDetail']['Message'];
			$captureData['stack_trace'] = $soapResult['detail']['ExceptionDetail']['StackTrace'];
			file_put_contents('logs/RatingErrors.log', date('Y-m-d H:i:s - ').print_r($captureData, true)."\n", FILE_APPEND);
            $this->error = true;
            $this->errorCode = 'An unexpected error has occurred while rating';
            $this->errorMessage = 'Please contact IGC Support for assistance.';
			$response = new Vtiger_Response();
            $response->setError($this->errorCode, $this->errorMessage);
			$response->emit();
			return;
		}

		$rateResult = $soapResult['RateEstimateSimpleFullReturnResult']['Totals'];

		$info['rateEstimate'] = $rateResult['TotalDiscounted'];

		$info['lineitems']['Transportation'] = $rateResult['Trans']['TotalDiscounted'];
		$info['lineitems']['Fuel Surcharge'] = $rateResult['FSDiscounted'];
		$info['lineitems']['Packing'] = $rateResult['Packing']['TotalPackingDiscounted'];
		$info['lineitems']['Unpacking'] = $rateResult['Packing']['TotalUnpackingDiscounted'];
		$info['lineitems']['Valuation'] = $rateResult['ValuationTotalDiscounted'];
		$info['lineitems']['Origin Accessorials'] = $rateResult['OriginAccessorials']['TotalDiscounted'];
		$info['lineitems']['Origin SIT'] = $rateResult['OriginSIT']['TotalDiscounted'];
		//Valuation on the device is lumped so we need to pull it from the SIT total.
		if ($info['lineitems']['Origin SIT'] && $rateResult['OriginSIT']['SITValDiscounted'] > 0) {
			$info['lineitems']['Origin SIT'] = $info['lineitems']['Origin SIT'] - $rateResult['OriginSIT']['SITValDiscounted'];
			//$info['lineitems']['Valuation'] += $rateResult['OriginSIT']['SITValDiscounted'];
		}
		$info['lineitems']['Destination Accessorials'] = $rateResult['DestinationAccessorials']['TotalDiscounted'];
		$info['lineitems']['Destination SIT'] = $rateResult['DestinationSIT']['TotalDiscounted'];
		//Valuation on the device is lumped so we need to pull it from the SIT total.
		if ($info['lineitems']['Destination SIT'] && $rateResult['DestinationSIT']['SITValDiscounted'] > 0) {
			$info['lineitems']['Destination SIT'] = $info['lineitems']['Destination SIT'] - $rateResult['DestinationSIT']['SITValDiscounted'];
			//$info['lineitems']['Valuation'] += $rateResult['DestinationSIT']['SITValDiscounted'];
		}

		$itemTotal = 0;
        if (is_array($rateResult['MiscItems'])) {
            if (array_key_exists('ChargeDiscounted', $rateResult['MiscItems']['MiscItemPricing'])) {
				$itemTotal = $itemTotal + $rateResult['MiscItems']['MiscItemPricing']['ChargeDiscounted'];
            } else {
                foreach ($rateResult['MiscItems']['MiscItemPricing'] as $item) {
					$itemTotal = $itemTotal + $item['ChargeDiscounted'];
				}
			}
		}

		$bulkyTotal = 0;
        if (is_array($rateResult['Bulkies'])) {
            if (array_key_exists('TotalDiscounted', $rateResult['Bulkies']['BulkyItem'])) {
				$bulkyTotal = $bulkyTotal + $rateResult['Bulkies']['BulkyItem']['TotalDiscounted'];
            } else {
                foreach ($rateResult['Bulkies']['BulkyItem'] as $item) {
					$bulkyTotal = $bulkyTotal + $item['TotalDiscounted'];
				}
			}
		}

		$info['lineitems']['Bulky Items'] = $bulkyTotal;
		$info['lineitems']['Miscellaneous Services'] = $itemTotal;
		$info['lineitems']['IRR'] = $rateResult['IRRDiscounted'];
		$info['mileage'] = $rateResult['Trans']['Miles'];
		$this->updateServices($recordId, $info['lineitems']);

		$sql = "UPDATE `vtiger_quotes` SET interstate_mileage=? WHERE quoteid=?";
        $result = $db->pquery($sql, array($info['mileage'], $recordId));

		$sql = "UPDATE `vtiger_quotes` SET subtotal=? WHERE quoteid=?";
		unset($params);
		$params[] = $info['rateEstimate'];
		$params[] = $recordId;

		$result = $db->pquery($sql, $params);

		$sql = "UPDATE `vtiger_quotes` SET total=? WHERE quoteid=?";
		$result = $db->pquery($sql, $params);

		$sql = "UPDATE `vtiger_quotes` SET pre_tax_total=? WHERE quoteid=?";
		$result = $db->pquery($sql, $params);

		$sql = "UPDATE `vtiger_quotes` SET rate_estimate=? WHERE quoteid=?";
		$result = $db->pquery($sql, $params);
		unset($params);

		//file_put_contents('logs/QuickEstimate.log', print_r($rateResult, true)."\n", FILE_APPEND);

		//file_put_contents('logs/devLog.log', "\n info : ".print_r($info,true),FILE_APPEND);
		$response = new Vtiger_Response();
		$response->setResult($info);
		$response->emit();
	}

    protected function getServiceIds($services)
    {
		$db         = PearDatabase::getInstance();
		$serviceIds = [];
		foreach ($services as $service => $rate) {
			$sql    = "SELECT serviceid,service_no FROM `vtiger_service` WHERE servicename=?";
			$result = $db->pquery($sql, [$service]);
			$row    = $result->fetchRow();
            if ($row == null) {
				continue;
			}
			$serviceIds[$service] = substr($row['service_no'], 3);
		}

		return $serviceIds;
	}

    protected function updateServices($record, $services)
    {
		$db = PearDatabase::getInstance();
		//delete the existing line items for this record.
		$removeStmt = 'DELETE FROM `vtiger_inventoryproductrel` WHERE id=?';
		$db->pquery($removeStmt, [$record]);
		//readd them.
		$sequence = 0;
		foreach ($services as $service => $rate) {
			$sql      = "SELECT serviceid FROM `vtiger_service` WHERE servicename=?";
			$params[] = $service;
			$result   = $db->pquery($sql, $params);
			unset($params);
			$row = $result->fetchRow();
            if ($row == null) {
				continue;
			}
			$serviceId   = $row[0];
			$checkStmt   = 'SELECT `id`,`productid` FROM `vtiger_inventoryproductrel` WHERE id=? AND productid=?';
			$checkResult = $db->pquery($checkStmt, [$record, $serviceId]);
			if (method_exists($checkResult, 'fetchRow') && $checkRow = $checkResult->fetchRow()) {
				$sql      = "UPDATE `vtiger_inventoryproductrel` SET listprice=? WHERE id=? AND productid=?";
				$params[] = $rate;
				$params[] = $record;
				$params[] = $serviceId;
				$db->pquery($sql, $params);
				unset($params);
			} else {
				//insert a new line item.
				$sequence++;
				$sql      = "INSERT INTO `vtiger_inventoryproductrel` (id, productid, sequence_no, quantity, listprice, tax1, tax2, tax3) VALUES (?,?,?,?,?,?,?,?)";
				$params[] = $record;
				$params[] = $serviceId;
				$params[] = $sequence;
				$params[] = 1;
				$params[] = $rate;
				$params[] = 0;
				$params[] = 0;
				$params[] = 0;
				$db->pquery($sql, $params);
				unset($params);
			}
		}
	}

    public function getServiceID($servicename)
    {
		$db = PearDatabase::getInstance();
		$result = $db->pquery("SELECT serviceid FROM vtiger_service WHERE servicename = '$servicename'", array());
		if ($result && $db->num_rows($result) > 0) {
			return $db->query_result($result, 0, 'serviceid');
		} else {
			return false;
		}
	}

    public function updateDetailedServices($recordID, $lineItems, $request)
    {
		$rv = [];
		//@TODO: is this really necessary now?
		$user             = Users_Record_Model::getCurrentUserModel();
		$assigned_user_id = $request->get('assigned_user_id');
		$agentid          = $request->get('agentid');
		$date_var         = date("Y-m-d H:i:s");
		if (!$assigned_user_id && !$agentid) {
			//We're missing these so let's try in the record model
			$srcRecordModel = Vtiger_Record_Model::getInstanceById($recordID);
			if ($srcRecordModel) {
				if (!$assigned_user_id) {
					$assigned_user_id = $srcRecordModel->get('assigned_user_id');
				}
				if (!$agentid) {
					$agentid = $srcRecordModel->get('agentid');
				}
			}
			//We still don't have these?  I swear to god.
			if (!$assigned_user_id) {
				//make it this user
				$assigned_user_id = $user->getId();
			}
			if (!$agentid) {
				//make it their base agency... why does this record need an agency?
				$assigned_user_id = $user->getAccessibleAgentsForUser()[0];
			}
		}
		$db = PearDatabase::getInstance();
		//@TODO: We CANNOT just blanket delete items... but it'll work for now.
		//delete the existing line items for this record.
        $removeStmt = 'DELETE dli_service_providers, vtiger_detailed_lineitems FROM dli_service_providers
                          RIGHT JOIN vtiger_detailed_lineitems ON (dli_service_providers.dli_id = vtiger_detailed_lineitems.detaillineitemsid)
                            WHERE vtiger_detailed_lineitems.dli_relcrmid=?';
        $db->pquery($removeStmt, [$recordID]);
		foreach ($lineItems as $section => $sectionItems) {
			if (is_array($sectionItems)) {
				if (!is_array($sectionItems[0])) {
					//I'm at a point.
					$tmp             = $sectionItems;
					$sectionItems[0] = $tmp;
				}
				//because I kept them ordered by sections.
				foreach ($sectionItems as $lineItem) {
					$element = [
						'dli_tariff_item_number'      => $lineItem['TariffItemNumber'] , //not tabled by rating to return
						'dli_tariff_item_name'        => $lineItem['TariffItem'], //not tabled by rating to return
						'dli_tariff_schedule_section' => $lineItem['Schedule'],
						'dli_return_section_name'     => $section,
						'dli_description'             => $lineItem['Description'],
						//'dli_provider_role'           => $lineItem['ProviderRole'],
                        'dli_participant_role'        => $lineItem['ParticipantRole'],
                        'dli_participant_role_id'     => $lineItem['ParticipantRoleID'],
						'dli_base_rate'               => $lineItem['BaseRate'] ? CurrencyField::convertToDBFormat($lineItem['BaseRate'], $user, true) : '',
						'dli_quantity'                => $lineItem['Quantity'],
                        'dli_unit_of_measurement'     => $lineItem['UnitOfMeasurement'],
						'dli_unit_rate'               => $lineItem['UnitRate'] ? CurrencyField::convertToDBFormat($lineItem['UnitRate'], $user, true) : '',
						'dli_gross'                   => $lineItem['Gross'] ? CurrencyField::convertToDBFormat($lineItem['Gross'], $user, true) : '',
						'dli_invoice_discount'        => $lineItem['InvoiceDiscountPct'],
						'dli_invoice_net'             => $lineItem['InvoiceCostNet'] ? CurrencyField::convertToDBFormat($lineItem['InvoiceCostNet'], $user, true) : '',
						'dli_distribution_discount'   => $lineItem['DistributableDiscountPct'],
						'dli_distribution_net'        => $lineItem['DistributableCostNet'] ? CurrencyField::convertToDBFormat($lineItem['DistributableCostNet'], $user, true) : '',
						'dli_tariff_move_policy'      => $lineItem['MovePolicy'],
						'dli_approval'                => $lineItem['Approval'],
//						'dli_service_provider'        => $lineItem['ServiceProvider'],
						'dli_invoiceable'             => $lineItem['Invoiceable'],
						'dli_distributable'           => $lineItem['Distributable'],
						'dli_invoiced'                => $lineItem['Invoiced'],
						'dli_distributed'             => $lineItem['Distributed'],
                        'dli_ready_to_invoice'        => $lineItem['ReadyToInvoice'] ?: '0',
                        'dli_ready_to_distribute'     => $lineItem['ReadyToDistribute'] ?: '0',
						'dli_invoice_number'          => $lineItem['InvoiceNumber'],
                        'dli_phase'                   => $lineItem['InvoicePhase'],
                        'dli_event'                   => $lineItem['InvoiceEvent'],
                        'dli_invoice_sequence'        => $lineItem['InvoiceSequence'],
                        'dli_distribution_sequence'   => $lineItem['DistributionSequence'],
                        'dli_unit_of_measurement'     => $lineItem['UnitOfMeasurement'],
                        'dli_location'                => $lineItem['Location'],
                        'dli_gcs_flag'                => $lineItem['GCSFlag'],
                        'dli_metro_flag'              => $lineItem['IsMetro'],
                        'dli_item_weight'             => $lineItem['Item_Weight'],
                        'dli_rate_net'                => $lineItem['Rate_Net'] ? CurrencyField::convertToDBFormat($lineItem['Rate_Net'], $user, true) : '',
						'dli_relcrmid'                => $recordID,
						//normally built in the crmentity table function
						'assigned_user_id'            => $assigned_user_id,
						'agentid'                     => $agentid,
						'smownerid'                   => $assigned_user_id,
						'modifiedby'                  => $user->id,
						'createdtime'                 => $db->formatDate($date_var, true),
						'modifiedtime'                => $db->formatDate($date_var, true),
					];
					try {
						$params  = [];
						$tabList = '';
						foreach ($element as $key => $value) {
							if ($value) {
								$tabList .= ($tabList?',':'').' `'.$key.'`';
								$params[] = $value;
							}
						}
						$new_sql = "INSERT INTO `vtiger_detailed_lineitems` (".$tabList.") VALUES (".generateQuestionMarks($params).')';
						$db->pquery($new_sql, $params);
						$element['detaillineitemsid'] = $db->getLastInsertID();
						$rv[] = $element;
					} catch (Exception $e) {
						file_put_contents('logs/devLog.log', "\n Save Exception saving detailed line items! : ".$e->getMessage()."\n line".$e->getLine()."\n", FILE_APPEND);
					}
				}
			}
		}
		//I feel that we will need to do this sometime in the future.
		return $rv;
	}

	//@NOTE: this function was designed to work with Detailed line items as a module instead of an extension.
    public function updateDetailedServicesModule($recordID, $lineItems, $request)
    {
		$moduleName = 'DetailLineItems';
		$user = Users_Record_Model::getCurrentUserModel();
		$assigned_user_id = $request->get('assigned_user_id');
		$agentid         = $request->get('agentid');
		if (!$assigned_user_id && !$agentid) {
			//We're missing these so let's try in the record model
			$srcRecordModel = Vtiger_Record_Model::getInstanceById($recordID);
			if ($srcRecordModel) {
				if (!$assigned_user_id) {
					$assigned_user_id = $srcRecordModel->get('assigned_user_id');
				}
				if (!$agentid) {
					$agentid = $srcRecordModel->get('agentid');
				}
			}
			//We still don't have these?  I swear to god.
			if (!$assigned_user_id) {
				//make it this user
				$assigned_user_id = $user->getId();
			}
			if (!$agentid) {
				//make it their base agency... why does this record need an agency?
				$assigned_user_id = $user->getAccessibleAgentsForUser()[0];
			}
		}
		require_once('include/Webservices/Create.php');
		foreach ($lineItems as $section => $sectionItems) {
			if (is_array($sectionItems)) {
				//because I kept them ordered by sections.
				foreach ($sectionItems as $lineItem) {
					$element = [
						'dli_tariff_item_number'      => '', //not tabled by rating to return
						'dli_tariff_item_name'        => '', //not tabled by rating to return
						'dli_tariff_schedule_section' => $lineItem['Schedule'],
						'dli_description'             => $lineItem['ServiceDescription'],
						//'dli_provider_role' => '',
						'dli_base_rate'               => $lineItem['Rate'],
						'dli_quantity'                => $lineItem['Quantity'],
						//'dli_unit_of_measurement' => '',
						'dli_unit_rate'               => $lineItem['Rate'],
						'dli_gross'                   => $lineItem['Cost'],
						'dli_invoice_discount'        => $lineItem['DisocuntPct'],
						'dli_invoice_net'             => $lineItem['CostNet'],
						'dli_distribution_discount'   => $lineItem['DisocuntPct'],
						'dli_distribution_net'        => $lineItem['CostNet'],
						//'dli_tariff_move_policy' => '',
						//'dli_approval' => '',
						//'dli_service_provider' => '',
						//'dli_invoiceable' => '',
						//'dli_distributable' => '',
						//'dli_invoiced' => '',
						//'dli_distributed' => '',
						//'dli_invoice_number' => '',
                        //'dli_invoice_sequence' => '',
                        //'dli_distribution_sequence' => '',
						'dli_relcrmid'                => vtws_getWebserviceEntityId('Estimates', $recordID),
						'assigned_user_id'            => vtws_getWebserviceEntityId('Users', $assigned_user_id),
						'agentid'                     => $agentid
					];
					try {
						$x = vtws_create($moduleName, $element, $user);
					} catch (Exception $e) {
						file_put_contents('logs/devLog.log', "\n Save Exception saving detailed line items! : ".$e->getMessage()."\n line".$e->getLine()."\n", FILE_APPEND);
					}
				}
			}
		}
	}

    public function processReportsResponse($recordId, $reportName, $getReportResult)
    {
		//Checks to make sure the report generated
        if (empty(base64_decode($getReportResult))) {
            $this->error = true;
            $this->errorCode = 'Error Processing Request';
            $this->errorMessage = 'The report failed to generate.';
			$response = new Vtiger_Response();
			$response->setError($this->errorCode, $this->errorMessage);
			$response->emit();
			return;
		}

		$db = PearDatabase::getInstance();
		$GetReportResultDecoded = base64_decode($getReportResult);
		$reportName = self::clean($reportName);
		//$reportName = $this->clean($reportName);
		$filepath = "/tmp";
		//@TODO: I'm saving the file to /tmp named temp_$PID_blah
		//the thought here is that we can't have concurrency with PID because this same process will move the file, if the file already
		//exists with that name it can be clobbered because that means the other process failed to finish.
		$tmp_filename = $filepath.'/temp_'.getmypid().'_'.$reportName.'.pdf';
		$written = file_put_contents($tmp_filename, fopen($GetReportResultDecoded, 'rb'));

		//false on fail 0 on zero written.
		if (!$written) {
            $this->error = true;
            $this->errorCode = 'Error Processing Request';
            $this->errorMessage = $GetReportResultDecoded;
			$response = new Vtiger_Response();
            $response->setError($this->errorCode, $this->errorMessage);
			$response->emit();
			return;
		}

		//Fetch current user's ID and Access Key for webservice
		$currentUser = Users_Record_Model::getCurrentUserModel();

		//@TODO: maybe consider not hardcoding these, but it's done everywhere so why sacrifice a select call.
		$folderId = '22x1';
		$filename = $reportName.'.pdf';
		$filetype = 'application/pdf';
        $sql      = "SELECT smownerid, agentid FROM `vtiger_crmentity` WHERE crmid = ?";
        $result   = $db->pquery($sql, [$recordId]);
        $row      = $result->fetchRow();

		$ownerId  = '19x'.$row['smownerid'];
		$agentId  = $row['agentid'];

		$documentInfo =
			[
				'notes_title'      => $reportName,
				'filename'         => $filename,
				'assigned_user_id' => $ownerId,
				'folderid'         => $folderId,
				'filetype'         => $filetype,
				'filelocationtype' => 'I',
				'filestatus'       => 1,
				'filesize'         => $written,
				'agentid'          => $agentId
			];

		//@TODO: discussed this with RP, I decided either way was kludgy, so I've chosen to use the existing crmentity function
		//and pass it reports=> true to allow it the option of using rename instead of move_uploaded_file.
		$_FILES[] = [
			'type' => $filetype,
			'size' => $written,
			'tmp_name' => $tmp_filename,
			'original_name' => $reportName.'.pdf',
			'name' => $reportName.'.pdf',
			'reports' => true
		];

		$docCreateResponse = vtws_create('Documents', $documentInfo, $currentUser);

		if(getenv('REPORTS_DEBUG_LOGGING')) {
			file_put_contents('logs/ReportDebug.log', "\n CreateResponse : ".print_r($docCreateResponse, true), FILE_APPEND);
		}


		$docid = substr($docCreateResponse['id'], strpos($docCreateResponse['id'], "x")+1);

		//create the related Records Array:
        $relatedRecordIds = [$recordId];
		//Pull the record model instead of mysql selects.
		$recordModel = Vtiger_Record_Model::getInstanceById($recordId);

		if (
		    $recordModel->getModuleName() == 'Estimates' ||
            $recordModel->getModuleName() == 'Actuals'
        ) {
		    //Relate to the Estimates: Opportunity, Contact, and Account
            $relatedRecordIds[] = $recordModel->get('potential_id');
            $relatedRecordIds[] = $recordModel->get('contact_id');
            $relatedRecordIds[] = $recordModel->get('account_id');
        } else if ($recordModel->getModuleName() == 'Opportunities') {
		    //Why does this handle opportunities reports?
            //Relate to the Opportunity's: Contact, Account, and primary Estimate.
            $relatedRecordIds[] = $recordModel->get('contact_id');
            $relatedRecordIds[] = $recordModel->get('related_to');
            $primaryEstimate = $recordModel->getPrimaryEstimateRecordModel(false);
            if ($primaryEstimate) {
                $relatedRecordIds[] = $primaryEstimate->getId();
            }
        }
		if (!empty($docid)) {
            foreach ($relatedRecordIds as $relatedId) {
		        if (!$relatedId) {
		            continue;
			}
				$sql      = "INSERT INTO `vtiger_senotesrel` VALUES (?, ?)";
                $params = [$relatedId, $docid];
				$db->pquery($sql, $params);
			}
		}
		return $docid;
	}

	function generateSoapResult($wsdlURL, $reportId, $recordId, $arr = false, $xmlRootTag = false, $reportRequestArray = false) {
        $getReportMethod = $this->get('getReportMethod');
        if (!$getReportMethod) {
            $getReportMethod = 'getReport';
        }

		if (!is_array($arr))  {
			file_put_contents('logs/devLog.log', date('Y-m-d H:i:s - ')."Before arrayBuilder call\n", FILE_APPEND);
			$arr = MoveCrm\arrayBuilder::buildArray($recordId, false, true);
		}

		$xml = MoveCrm\xmlBuilder::build($arr, $xmlRootTag);
		file_put_contents('logs/xmlRework.xml', $xml);
		file_put_contents('logs/Report.xml', $xml);

		//@TODO: add try catch
		$soapclient = new soapclient2($wsdlURL, 'wsdl');
		$soapclient->setDefaultRpcParams(true);
		$soapProxy  = $soapclient->getProxy();

		if (!method_exists($soapProxy, $getReportMethod)) {
            $this->error = true;
            $this->errorCode = 'Error Processing Request';
            $this->errorMessage = $getReportMethod.' method not found.';
			$response = new Vtiger_Response();
            $response->setError($this->errorCode, $this->errorMessage);
			$response->emit();
			return;
		}

		$wsdlParams = [
		    'reportID'=>$reportId,
            'byteArray'=>base64_encode($xml),
            ];

        if ($this->get('useNewReportAPI')) {
            //@NOTE: new reports changed byteArray to customerData and requires the ReportsRequest object "reports"
            unset($wsdlParams['byteArray']);
            $wsdlParams['customerData'] = base64_encode($xml);
            $wsdlParams['request'] = $reportRequestArray;
        }

		$soapResult = $soapProxy->$getReportMethod($wsdlParams);
		file_put_contents('logs/devLog.log', "\n wsdlParams : ".print_r($wsdlParams,true),FILE_APPEND);
		file_put_contents('logs/devLog.log', "\n soapResult2 : ".print_r($soapResult,true),FILE_APPEND);

		$errors = [
			'fault'       => $soapProxy->fault,
			'faultcode'   => $soapProxy->faultcode,
			'faultstring' => $soapProxy->faultstring,
			'faultdetail' => $soapProxy->faultdetail,
			'error_str'   => $soapProxy->error_str
		];

		if($errors['faultstring']) {
            $this->error = true;
            $this->errorCode = $errors['faultstring'];
            $this->errorMessage = $errors['faultstring'];
			$response = new Vtiger_Response();
            $response->setError($this->errorCode, $this->errorMessage);
			$response->emit();
			return;
		}
		return $soapResult;
	}

	//@NOTE: local tariffs are formatted: modules/Estimates/actions/GetLocalRate.php
	protected function formatLineItems($lineItemParts, $currentUser, $formatLineDefaults = false, $backupMode = false) {
		//        $lineItemParts['ServiceDescription'] = $singleLineItem['Location'].' '.
		//											   $singleLineItem['Description'].' - '.
		//											   $lineItemParts['Description'].' '.
		//											   $count;

        //@TODO: maybe find a nicer way to handle this
        // take in the defaults and break them out into the individual parts so they can be more obvious in reading.
        $defaultParticipant = [];
        $defaultMoveRole = [];
        $defaultApproval = '';
        $defaultSequence = 0;
        if (is_array($formatLineDefaults)) {
            if (array_key_exists('participatingAgent', $formatLineDefaults) && is_array($formatLineDefaults['participatingAgent'])) {
                $defaultParticipant = $formatLineDefaults['participatingAgent'];
            }
            if (array_key_exists('moveRole', $formatLineDefaults) && is_array($formatLineDefaults['moveRole'])) {
                $defaultMoveRole = $formatLineDefaults['moveRole'];
            }
            if (array_key_exists('approval', $formatLineDefaults)) {
                if (is_array($formatLineDefaults['approval'])) {
                    $defaultApproval = $formatLineDefaults['approval'][0];
                } else {
                    $defaultApproval = $formatLineDefaults['approval'];
                }
            }
            if (array_key_exists('sequence', $formatLineDefaults)) {
                $defaultSequence = $formatLineDefaults['sequence'];
            }
        }
		$lineItemParts['ServiceDescription'] = $lineItemParts['Description'];
		if ($backupMode) {
			$lineItemParts['ServiceDescription'] = $lineItemParts['Location'].' '.$lineItemParts['Description'];
		}

        $lineItemParts['UnitOfMeasurement'] = $lineItemParts['UnitOfMeasure'];
        //@NOTE: No longer do this either because rating knows better.
//		//@NOTE: Move this up here so we can have the Unit of measurement when we attempt to fix stuff.
//        if($lineItemParts['UnitOfMeasure'] != 'Base') {
//            $lineItemParts['UnitOfMeasurement'] = $lineItemParts['UnitOfMeasure'];
//        } else {
//            $lineItemParts['UnitOfMeasurement'] = $this->getDetailedLineItemInfoFromDB($lineItemParts['TariffCode'], 'UnitOfMeasurement');
//        }

        $lineItemParts = $this->attemptToFixValues($lineItemParts, $formatLineDefaults['discounts']);

		//sigh... this is stupid. but it'll work we can improve after release.
		//@todo; create single map and use for database insert.
		$lineItemParts['TariffItemNumber'] = '';
		$lineItemParts['TariffItem'] = $lineItemParts['TariffCode'] ?: '';
		$lineItemParts['TariffSection'] = $lineItemParts['Schedule'] ?: '';
		//$lineItemParts['Description'] = xxx; //<-- this is ServiceDiscription actually.
		//I think I confused a single field as two.
		$lineItemParts['ProviderRole'] = $lineItemParts['Role'] = '';
		$lineItemParts['Gross'] = $lineItemParts['Cost'] ? CurrencyField::convertToUserFormat($lineItemParts['Cost'], $currentUser) : '0';

		$lineItemParts['InvoiceCostNet'] = $this->calculateDetailedCost($lineItemParts['Cost'], $lineItemParts['InvoiceDiscountPct'], $currentUser);
		$lineItemParts['DistributableCostNet'] = $this->calculateDetailedCost($lineItemParts['Cost'], $lineItemParts['DistributableDiscountPct'], $currentUser);

		$lineItemParts['MovePolicy'] = '';
		$lineItemParts['ServiceProvider'] = '';
        $lineItemParts['ServiceProviderName'] = '';
        if (is_array($defaultParticipant)) {
            $lineItemParts['ServiceProvider'] = (array_key_exists('ServiceProvider', $defaultMoveRole) ? $defaultMoveRole['ServiceProvider'] : '');
            $lineItemParts['ServiceProviderName'] = (array_key_exists('ServiceProviderName', $defaultMoveRole) ? $defaultMoveRole['ServiceProviderName'] : '');
        }
        $lineItemParts['ParticipantRole'] = '';
        $lineItem['ParticipantRoleID'] = '';
        $lineItemParts['ParticipantName'] = '';
        if (is_array($defaultParticipant)) {
            $lineItemParts['ParticipantRole'] = $lineItemParts['Role'] = (array_key_exists('ParticipantRole', $defaultParticipant) ? $defaultParticipant['ParticipantRole'] : '');
            $lineItemParts['ParticipantRoleID'] = (array_key_exists('ParticipantRoleID', $defaultParticipant) ? $defaultParticipant['ParticipantRoleID'] : '');
            $lineItemParts['ParticipantName'] = $lineItemParts['RoleName'] = (array_key_exists('ParticipantName', $defaultParticipant) ? $defaultParticipant['ParticipantName'] : '');
        }

        $tariffServiceId = $lineItemParts['Id'];
        $db =& PearDatabase::getInstance();
        unset($row);
        if($tariffServiceId && getenv('INSTANCE_NAME') == 'graebel') {
            $sql = 'SELECT invoiceable, distributable FROM `vtiger_tariffservices` WHERE tariffservicesid=?';
            $res = $db->pquery($sql, [$tariffServiceId]);
            $row = $res->fetchRow();
        }
        if($row) {
            //Local Tariff Services
            $lineItemParts['Invoiceable'] = $row['invoiceable'] ? '1' : '0';
            $lineItemParts['Distributable'] = $row['distributable'] ? '1' : '0';
        } else {
            //Interstate general
            $lineItemParts['Invoiceable'] = $this->getDetailedLineItemInfoFromDB($lineItemParts['TariffCode'], 'Invoiceable', ($lineItemParts['Invoiceable'] ?: '1'));
            $lineItemParts['Distributable'] = $this->getDetailedLineItemInfoFromDB($lineItemParts['TariffCode'], 'Distributable', ($lineItemParts['Distributable'] ?: '1'));

            // Mega hack for 1950-B destination shuttle to not be invoicable
            if($lineItemParts['RatingItem'] == 'DestinationAccessorials_Shuttle'
                && $lineItemParts['TariffReference'] == '1950-B')
            {
                $lineItemParts['Invoiceable'] = '0';
            }
        }

        $lineItemParts['InvoiceSequence'] = '';
        if ($lineItemParts['Invoiceable']) {
            //Only set invoice sequence when it's invoiceable.
            $lineItemParts['InvoiceSequence'] = $this->getDetailedLineItemInfoFromDB($lineItemParts['TariffCode'], 'InvoiceSequence', $defaultSequence);
        }

        $lineItemParts['DistributionSequence'] = '';
        if ($lineItemParts['Distributable']) {
            //Only set distribution sequence when it's distributable
            $lineItemParts['DistributionSequence'] = $this->getDetailedLineItemInfoFromDB($lineItemParts['TariffCode'], 'DistributionSequence', $defaultSequence);
        }
        $lineItemParts['Location'] = $this->getDetailedLineItemInfoFromDB($lineItemParts['TariffCode'], 'Location', $lineItemParts['Location']);
        $lineItemParts['GSCFlag'] = $this->getDetailedLineItemInfoFromDB($lineItemParts['TariffCode'], 'GSCFlag', 'N');

        $lineItemParts['ReadyToInvoice'] = '';
        $lineItemParts['ReadyToDistribute'] = '';
		$lineItemParts['Invoiced'] = '';
		$lineItemParts['Distributed'] = '';
		$lineItemParts['InvoiceNumber'] = '';
        $lineItemParts['InvoicePhase'] = '';
        $lineItemParts['InvoiceEvent'] = '';
		$lineItemParts['Approval'] = $defaultApproval;

        $lineItemParts['InvoiceCostNet'] = $this->ensureCostAllowed($lineItemParts['InvoiceCostNet'], $lineItemParts['Invoiceable']);
        $lineItemParts['DistributableCostNet'] = $this->ensureCostAllowed($lineItemParts['DistributableCostNet'], $lineItemParts['Distributable']);

        $lineItemParts['Item_Weight'] = $lineItemParts['Weight'];
        $lineItemParts['Rate_Net'] = $lineItemParts['RateNet'];
        //@NOTE: local tariffs are formatted: modules/Estimates/actions/GetLocalRate.php
		return $lineItemParts;
	}

    protected static function ensureCostAllowed($value, $allowed)
    {
        if (!$allowed) {
            return '';
        }
        return $value;
    }

    public function getViewer(Vtiger_Request $request)
    {
        if (!$this->viewer) {
			global $vtiger_current_version;
			$viewer = new Vtiger_Viewer();
			$viewer->assign('APPTITLE', getTranslatedString('APPTITLE'));
			$viewer->assign('VTIGER_VERSION', $vtiger_current_version);
			$this->viewer = $viewer;
		}
		return $this->viewer;
	}

	//I think this is old and can be deleted.  But I'm not doing it now.
    protected function getLineItemName($lineitem, $rateName = false)
    {
		//@TODO: consider database this in vtiger_service table set a location + description to map to servicename.
		//Just keep working within the framed box!
		switch ($lineitem['Description']) {
			case 'Bulkies':
				$itemName = 'Bulky Items';
				break;
			case 'Accessorials':
			case 'SIT':
				$itemName = $lineitem['Location'] . ' ' . $lineitem['Description'];
				$rateTotalName = $lineitem['Location'] . $lineitem['Description'];
				break;
			case 'Miscellaneous':
				$itemName = $lineitem['Description'] . ' Services';
				$rateTotalName = 'MiscItems';
				break;
			//case 'Unpacking':
			//case 'Packing':
			//case 'Transportation':
			//case 'Fuel Surcharge':
			default:
				$itemName = $lineitem['Description'];
				$rateTotalName = $lineitem['Description'];
		}

		if ($rateName) {
			return $rateTotalName;
		} else {
			return $itemName;
		}
	}

    //@TODO HERE This function has a lot of assumptions and guessing.
    protected function rollupPackingUnpackingItems(&$correctedLineItems)
    {
        // make this work on both prod/dev rating engine for now
        //        $mergedArray = [];
        //        foreach ($correctedLineItems as $type => $items)
        //        {
        //            $types = explode('/', $type);
        //            foreach ($types as $index => $subType)
        //            {
        //                if(!in_array($subType, ['Packing', 'Unpacking', 'Containers']))
        //                {
        //                    continue 2;
        //                }
        //            }
        //            $mergedArray = array_merge($mergedArray, $items);
        //            unset($correctedLineItems[$type]);
        //        }
        //        $correctedLineItems['Packing/Unpacking/Containers'] = $mergedArray;

            $isPackingItem = function ($item) {
                return
                    (preg_match('/labor/i', $item['Description']) !== 1) &&
                    (preg_match('/debris/i', $item['Description']) !== 1) &&
                    (
                        strpos($item['Description'], 'Packing') !== false
                        || strpos($item['RatingItem'], 'Packing') !== false
                    );
            };
            $isContainer   = function ($item) {
                return
                    (preg_match('/labor/i', $item['Description']) !== 1) &&
                    (preg_match('/debris/i', $item['Description']) !== 1) &&
                    (
                        strpos($item['Description'], 'Containers') !== false
                        || strpos($item['RatingItem'], 'Containers') !== false
                    );
            };
        $isUnpackingItem = function ($item) {
            return
                (preg_match('/labor/i', $item['Description']) !== 1) &&
                (preg_match('/debris/i', $item['Description']) !== 1) &&
                (
                    strpos($item['Description'], 'Unpacking') !== false
                    || strpos($item['RatingItem'], 'Unpacking') !== false
                );
        };
        $rollupPackingItem = null;
        $rollupUnpackingItem = null;
        $rollupContainersItem = null;
        $rollupProps          = ["Cost", "CostNet", "Gross", "Rate", "RateNet", "InvoiceCostNet"];
        $subItemProps         = ["TariffItem", "DistributableCostNet"];
        foreach ($correctedLineItems as $type => &$items) {
            $types = explode('/', $type);
            foreach ($types as $index => $subType) {
                if (!in_array($subType, ['Packing', 'Unpacking', 'Containers'])) {
                    continue 2;
                }
            }
            if (!$rollupPackingItem) {
                for ($i = 0, $lc = count($items); $i < $lc; $i++) {
                    if ($isPackingItem($items[$i])) {
                        $rollupPackingItem = $items[$i];
                        foreach ($rollupProps as $prop) {
                            $rollupPackingItem[$prop] = '';
                        }
                        break;
                    }
                }
            }
            if (!$rollupUnpackingItem) {
                for ($i = 0, $lc = count($items); $i < $lc; $i++) {
                    if ($isUnpackingItem($items[$i])) {
                        $rollupUnpackingItem = $items[$i];
                        foreach ($rollupProps as $prop) {
                            $rollupUnpackingItem[$prop] = '';
                        }
                        break;
                    }
                }
            }
            if (!$rollupContainersItem) {
                for ($i = 0, $lc = count($items); $i < $lc; $i++) {
                    if ($isContainer($items[$i])) {
                        $rollupContainersItem = $items[$i];
                        foreach ($rollupProps as $prop) {
                            $rollupContainersItem[$prop] = '';
                        }
                        break;
                    }
                }
            }
            for ($i = 0, $lc = count($items); $i < $lc; $i++) {
                unset($setItem);
                if ($isPackingItem($items[$i])) {
                    $setItem = &$rollupPackingItem;
                } elseif ($isUnpackingItem($items[$i])) {
                    $setItem = &$rollupUnpackingItem;
                } elseif ($isContainer($items[$i])) {
                    $setItem = &$rollupContainersItem;
                }
                if (!$setItem) {
                    continue;
                }
                foreach ($rollupProps as $prop) {
                    $setItem[$prop] += (double) CurrencyField::convertToDBFormat($items[$i][$prop]);
                    $items[$i][$prop] = '';
                }
                $items[$i]['Invoiceable'] = '0';
            }
        }
        if ($rollupPackingItem) {
            $rollupPackingItem['Description']        = 'Packing';
            $rollupPackingItem['ServiceDescription'] = 'Packing';
            $rollupPackingItem['BaseRate']           = round($rollupPackingItem['Gross'], 2);
            $rollupPackingItem['UnitRate']           = '';
            $rollupPackingItem['Quantity']           = '';
            $rollupPackingItem['Distributable']      = '0';
            foreach ($subItemProps as $prop) {
                $rollupPackingItem[$prop] = '';
            }
            $rollupPackingItem['TariffItem']       = 'PACK_LABOR';
            $correctedLineItems['Custom Packing'] = [$rollupPackingItem];
        }
        if ($rollupContainersItem) {
            $rollupContainersItem['Description']        = 'Containers';
            $rollupContainersItem['ServiceDescription'] = 'Containers';
            $rollupContainersItem['BaseRate']           = round($rollupContainersItem['Gross'], 2);
            $rollupContainersItem['UnitRate']           = '';
            $rollupContainersItem['Quantity']           = '';
            $rollupContainersItem['Distributable']      = '0';
            foreach ($subItemProps as $prop) {
                $rollupContainersItem[$prop] = '';
            }
            $rollupContainersItem['TariffItem']          = 'PACO';
            $correctedLineItems['Custom Packing Containers'] = [$rollupContainersItem];
        }
        if ($rollupUnpackingItem) {
            $rollupUnpackingItem['Description']        = 'Unpacking';
            $rollupUnpackingItem['ServiceDescription'] = 'Unpacking';
            $rollupUnpackingItem['BaseRate']           = round($rollupUnpackingItem['Gross'], 2);
            $rollupUnpackingItem['UnitRate']           = '';
            $rollupUnpackingItem['Quantity']           = '';
            $rollupUnpackingItem['Distributable']      = '0';
            foreach ($subItemProps as $prop) {
                $rollupUnpackingItem[$prop] = '';
            }
            $rollupUnpackingItem['TariffItem']         = 'UNPK_LABOR';
            $correctedLineItems['Custom Unpacking'] = [$rollupUnpackingItem];
        }
    }

    private function getDetailedLineItemInfoFromDB($tariffCode, $itemThing, $defaultValue = '')
    {
        if (!$tariffCode) {
            return $defaultValue;
        }
        if (!$itemThing) {
            return $defaultValue;
        }
        if (!$this->tariffID) {
            return $defaultValue;
        }

        if (!isset($this->detailLineCodeMap[$this->tariffID][$tariffCode])) {
            $tariffNumber = $this->getTariffNumber();
            $lineItemMap = self::accessDatabaseForDetailLineCodeMap($tariffCode, $tariffNumber);
            if (!$lineItemMap) {
               if ($tariffNumber == '104G') {
                   //try again as 400N; for hell.
                   //@TODO allow tariff manager to have a "Fall Back"  tariff_number?
                   $lineItemMap = self::accessDatabaseForDetailLineCodeMap($tariffCode, '400N');
               }
            }
            if (!$lineItemMap) {
                return $defaultValue;
            }
            $this->detailLineCodeMap[$this->tariffID][$tariffCode] = $lineItemMap;
        }

        $row = $this->detailLineCodeMap[$this->tariffID][$tariffCode];
        switch ($itemThing) {
            case 'UnitOfMeasurement':
                return $row['unit_default'];
            case 'InvoiceSequence':
            case 'DistributionSequence':
                return intval($row['invseq']);
            case 'Invoiceable':
                return ($row['invoicable'] == 'Y'?1:0);
            case 'Distributable':
                return ($row['distributable'] == 'Y'?1:0);
            case 'GSCFlag':
                return $row['gcs_flag'];
            case 'Location':
                if (!$defaultValue) {
                    return $row['stop_type_code'];
                }
            default:
                return $defaultValue;
        }
        return $defaultValue;
    }

    private function getTariffNumber()
    {
        if ($this->tariffNumber) {
            return $this->tariffNumber;
        }
        if (!$this->tariffID) {
            return;
        }
        $tariffModel = Vtiger_Record_Model::getInstanceById($this->tariffID);
        $this->tariffNumber = $tariffModel->get('vanline_specific_tariff_id');
        return $this->tariffNumber;
    }

    public static function accessDatabaseForDetailLineCodeMap($tariffCode, $tariffNumber)
    {
        if (!$tariffCode) {
            return false;
        }
        $db     = &PearDatabase::getInstance();
        $stmt   = 'SELECT * FROM `vtiger_gvl_tariff_item_map` WHERE `service_code` = ? AND `tariff_number` = ? LIMIT 1';
        $result = $db->pquery($stmt, [$tariffCode, $tariffNumber]);
        if (!method_exists($result, 'fetchRow')) {
            return false;
        }
        return $result->fetchRow();
    }

    //This is a hack because rating is not returning right values.  Objections have been lodged.
    //pulled the other "value fixin" from formatLineItems to group here
    //@TODO HERE This function also has a lot of assumptions and guessing.
    private function attemptToFixValues($lineItemParts, $discountsArray)
    {
        if ($lineItemParts['UnitOfMeasure'] == 'Base') {
            $lineItemParts['Quantity'] = '';
            $lineItemParts['BaseRate'] = $lineItemParts['Rate'] = $lineItemParts['Cost'];
            $lineItemParts['UnitRate'] = '';
        }

        //@TODO: Hack fix because TRANSPORT returns the base rate probably right, but then the cost is with additives.
        if ($lineItemParts['TariffCode'] == 'TRANSPORT') {
            $lineItemParts['Rate'] = $lineItemParts['Cost'];
        }

        //@NOTE: these comments are really confusing now
        //@TODO: This is a "FIX" but this should really be returned correctly.
        //This should probably be moved out of the this temp function.
        //@NOTE: change from the returned discount to our mapped one.
        if($lineItemParts['IsDiscountApplicable'] !== 'false') {
            $lineItemParts['InvoiceDiscountPct'] = $lineItemParts['DiscountPct'];
            $discountsArray = $this->buildDiscountsArray($discountsArray, $lineItemParts['TariffCode']);
            $lineItemParts['DistributableDiscountPct'] = $discountsArray['bottom_line_distribution_discount'];
        } else {
            $lineItemParts['DiscountPct'] = 0;
            $lineItemParts['InvoiceDiscountPct'] = 0;
            $lineItemParts['DistributableDiscountPct'] = $lineItemParts['DistributionDiscountPct'] = 0;
        }
//        if (array_key_exists('DistributionDiscountPct', $lineItemParts)) {
//            $lineItemParts['DistributableDiscountPct'] = $lineItemParts['DistributionDiscountPct'];
//        } else if ($lineItemParts['DiscountPct']) {
//            //@TODO this should be ripped out so rating handles this instead of us guessing.
//            //OK we still don't get distribution discount back from rating, so we will use the returned discount to trigger setting it.
//            $discountsArray = $this->buildDiscountsArray($discountsArray, $lineItemParts['TariffCode']);
//            //$lineItemParts['InvoiceDiscountPct']       = $discountsArray['bottom_line'];
//            $lineItemParts['DistributableDiscountPct'] = $discountsArray['bottom_line_distribution_discount'];
//        }

        if (preg_match('/^INS_SIT/i', $lineItemParts['TariffCode'])) {
            if ($lineItemParts['IsDiscountApplicable'] && $discountsArray) {
                //$lineItemParts['InvoiceDiscountPct']       = $discountsArray['sit'];
                $lineItemParts['DistributableDiscountPct'] = $discountsArray['sit_distribution_discount'];
            }
        } elseif (preg_match('/^STOR/i', $lineItemParts['TariffCode'])) {
            //FOR HELL.
            if ($lineItemParts['IsDiscountApplicable'] && $discountsArray) {
                //$lineItemParts['InvoiceDiscountPct']       = $discountsArray['sit'];
                $lineItemParts['DistributableDiscountPct'] = $discountsArray['sit_distribution_discount'];
            }
        }

        //null the Quantity for items that have cost but no quantity,
        //these are generally "flat rate" items
        //        if ($lineItemParts['Cost'] && $lineItemParts['Quantity'] == 0) {
        //            $lineItemParts['Quantity'] = '';
        //            $lineItemParts['BaseRate'] = $lineItemParts['Rate'];
        //        } else if ($lineItemParts['Quantity']) {
        //            $lineItemParts['UnitRate'] = $lineItemParts['Rate'];
        //        } else if ($lineItemParts['Quantity'] == 0) {
        //            //@NOTE: shouldn't get here, but never KNOW.
        //            $lineItemParts['UnitRate'] = $lineItemParts['Rate'];
        //        }
        if ($lineItemParts['Rate'] == 0) {
            $lineItemParts['BaseRate'] = round($lineItemParts['Rate'], 2);
        } elseif ($lineItemParts['Cost'] == $lineItemParts['Rate']) {
            //It's a flat charge item.
            if ($lineItemParts['Quantity'] > 0) {
                $lineItemParts['UnitRate'] = round($lineItemParts['Rate'], 4);
            } else {
                $lineItemParts['Quantity'] = '';
                $lineItemParts['BaseRate'] = round($lineItemParts['Rate'], 2);
            }
        } else {
            $lineItemParts['UnitRate'] = round($lineItemParts['Rate'], 4);
        }

        if (!$lineItemParts['UnitRate']) {
            return $lineItemParts;
        }

        $lineItemParts['Quantity'] = (float) bcdiv(1 * $lineItemParts['Quantity'], 1, 4);
        return $lineItemParts;

        //@NOTE: No longer do we update the quantity.
//        //@TODO: It doesn't seem to be the right thing this is a huge leap of faith.
//        if (
//            $lineItemParts['UnitOfMeasurement'] == 'CWT' &&  // STILL a leap.
//            $lineItemParts['Weight'] &&
//            !$lineItemParts['Quantity']
//        ) {
//            //If there is a weight and no Quantity use that for the quantity like the provided example.
//            $lineItemParts['Quantity'] = $lineItemParts['Weight'];
//        }
//
//        //@TODO: rip out when rating handle STOR's weird quantity right.
//        if (preg_match('/^STOR/i',$lineItemParts['TariffCode'])) {
//            //@NOTE: Weight appears to be returned now.
//            //$storeQuantity = $lineItemParts['Quantity'];
//            //$lineItemParts['Quantity'] = $lineItemParts['Cost'] / $lineItemParts['UnitRate'] / $lineItemParts['Quantity'];
//            //$lineItemParts['UnitRate'] = $storeQuantity * $lineItemParts['UnitRate'];
//
//            //Yes, I know we would ordinarily round but after going back and forth all morning, they are requirng that this be truncated and no trailing zeros.
//            $lineItemParts['Quantity'] = (float) bcdiv((($lineItemParts['Weight'] / 100) * $lineItemParts['Quantity']), 1, 4);
//            return $lineItemParts;
//        }
//
////        if ($lineItemParts['Quantity']) {
////            return $lineItemParts;
////        }
//        //Yes, I know we would ordinarily round but after going back and forth all morning, they are requiring that this be truncated and no trailing zeros.
//        $lineItemParts['Quantity'] =(float) bcdiv(($lineItemParts['Cost'] / $lineItemParts['UnitRate']), 1, 4);
//
////
////        //If there is a weight and no Quantity use that for the quantity like the provided example.
////        if ($lineItemParts['Weight'] && !$lineItemParts['Quantity']) {
////            $lineItemParts['Quantity'] = $lineItemParts['Weight'];
////            if ($lineItemParts['Cost'] && $lineItemParts['Quantity'] == 0) {
////                $lineItemParts['Quantity'] = '';
////                $lineItemParts['BaseRate'] = $lineItemParts['Rate'];
////            } else if ($lineItemParts['Quantity']) {
////                $lineItemParts['UnitRate'] = $lineItemParts['Rate'];
////            }
////        }
//        return $lineItemParts;
    }

    private function buildDiscountsArray($discountArray, $tariffCode = false)
    {
        //[bottom_line] => 25.00
        //[sit] => 4.00
        //[crate] => 0.00
        //[sit_distribution_discount] => 0
        //[bottom_line_distribution_discount] => 50.00
        //[distribution_discount_percentage] =>
        //[distribution_discount] =>

        if ($this->ignoreDiscountsByTariffCode($tariffCode)) {
            return [
                'bottom_line'                       => 0.00,
                'sit'                               => 0.00,
                'crate'                             => 0.00,
                'sit_distribution_discount'         => 0.00,
                'bottom_line_distribution_discount' => 0.00,
                'distribution_discount_percentage'  => 0.00,
                'distribution_discount'             => 0.00,
            ];
        }

        if (!$discountArray['crate']) {
            //crate presumably inherits from bottom_line.
            $discountArray['crate'] = $discountArray['bottom_line'];
        }

        if (!$discountArray['bottom_line_distribution_discount']) {
            //distribution discount inherits from bottom_line
            $discountArray['bottom_line_distribution_discount'] = $discountArray['bottom_line'];
        }

        //@TODO: Sit doesn't use bottom line discount
//        if (!$discountArray['sit']) {
//            //sit inherits from bottom_line.
//            $discountArray['sit'] = $discountArray['bottom_line'];
//        }

//        if (!$discountArray['sit_distribution_discount']) {
//            if ($discountArray['distribution_discount_percentage']) {
//                $discountArray['sit_distribution_discount'] = $discountArray['distribution_discount_percentage'];
//            } else if(!$discountArray['distribution_discount']) {
//                $discountArray['sit_distribution_discount'] = $discountArray['distribution_discount'];
//            } else {
//                $discountArray['sit_distribution_discount'] = $discountArray['bottom_line_distribution_discount'];
//            }
//        }
        return $discountArray;
    }

    //@TODO: This section makes some wild assumptions as well.
    private function ignoreDiscountsByTariffCode($tariffCode)
    {
        if (!$tariffCode) {
            return false;
        }
        $tariffCode = strtolower($tariffCode);
        //looking to match: TV_PACK_50, TV_PACK_60, TV_KIT_60-Containers
        if (preg_match('/^tv_/', $tariffCode)) {
            return true;
        }
        return false;
    }

    private function calculateDetailedCost($cost, $discount, $currentUser)
    {
        //@NOTE: Some line items can have negative totals, and need to display as such. e.g. Misc Items.
        // if ($cost <= 0) {
        //     return CurrencyField::convertToUserFormat(0.00, $currentUser);
        // }
        if ($discount <= 0) {
            return CurrencyField::convertToUserFormat($cost, $currentUser);
        }
        $cost = $cost * (100 - $discount)/100;
        return CurrencyField::convertToUserFormat($cost, $currentUser);
    }

    public function clean($string)
    {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

        return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
    }
}
