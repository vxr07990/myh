<?php
namespace MoveCrm;

require_once('ValuationUtils.php');
require_once('modules/TariffServices/actions/LocalCalcWeightLookup.php');

use PearDatabase;
use Estimates_Record_Model;
use Vtiger_Record_Model;
use Tariffs_Record_Model;
use DateTimeField;
use TariffServices_Record_Model;
use TariffSections_Record_Model;
use ParticipatingAgents_Module_Model;
use Exception;
use Users_Record_Model;
use Carbon\Carbon;
use CurrencyField;

function xmlspecialchars($string)
{
    return htmlspecialchars($string, ENT_COMPAT | ENT_XML1);
}

class arrayBuilder
{
    //@NOTE: non-html reports require the phone numbers to be sent over formatted. (ONLY origin/dest and participating agent phones)
    // set to true to do this, set to false to not
    protected static $formatUSPhoneNumbers = true;
    //@NOTE these are bool used internally only (not sent out).
    protected static $useForReports = false;
    protected static $useOppId = false;
    //@NOTE these are strings that are sent to rating/reports
    protected static $expressLoadingOrigin = 'false';
    protected static $expressLoadingDest   = 'false';
    protected static $hardcoded = [
        'jobStatus'                => 'Estimate',
        'isOA'                     => 'false',
        'selfHaul'                 => 'false',
        'businessChannel'          => 'Consumer',
        'chargeApproved'           => 'false',
        'weight_override_applied'  => 'true',
        //'weight_override_applied' => 'false',  //need to make a toggle.
        'weightFactor'             => 7,
        'otPackOrigin'             => 'false',
        'otLoadUnloadOrigin'       => 'false',
        'dayCertainOrigin'         => 'false',
        'otPackDest'               => 'false',
        'otLoadUnloadDest'         => 'false',
        'dayCertainDest'           => 'false',
        'sitAcOrigin'              => 'false',
        'sitAcDest'                => 'false',
        'irrPercent'               => 4,
        'bookerAdjustment'         => '0.00',
        'peak'                     => 'false',
        'apply50Plus'              => 'false',
        'wheatonPOM'               => 'false',
        'wheatonCNFSG'             => 'false',
        'veterans'                 => 'false',
        //'estimateType'             => 'Non-binding',  //Reports says this should never be hardcoded.
        'printTerms'               => 'false',
        //'fuel_surcharge_pct'      => 4, //this is a terrible way to do this change it please
        'service_charge_type_doe'  => 'DOE',
        'service_charge_type_flat' => 'Flat',
    ];

    /**
     * @param int  $estimateId
     * @param bool $tempTables
     * @param bool $forReports
     * @param bool $oppID
     * @param bool $dov
     * @param bool $requote
     *
     * @return array
     * @throws Exception
     */
    public static function buildArray($estimateId, $tempTables = false, $forReports = false, $oppID = false, $dov = true, $requote = false)
    {
        self::$useForReports = $forReports;
        if ($oppID) {
            self::$useOppId = $oppID;
        }
        $db          = PearDatabase::getInstance();
        $tablePrefix = $tempTables?session_id().'_':'';
        $sql         = "SELECT business_line_est FROM `".$tablePrefix."vtiger_quotescf` WHERE quoteid=?";
        $result      = $db->pquery($sql, [$estimateId]);
        if ($result) {
            $row          = $result->fetchRow();
            $businessLine = $row[0];
        } else {
            throw new Exception('No record found');
        }
        try {
            $tariffType = Estimates_Record_Model::getEffectiveTariff($estimateId, $tablePrefix)['custom_tariff_type'];
            //only has tariff type if it's not a local intra
            $builtArray = !$tariffType ? self::buildLocalArray($estimateId, $tablePrefix) : self::buildInterstateArray($estimateId, $businessLine, $tablePrefix, $dov);
        } catch (Exception $ex) {
            throw $ex;
        }
        return $builtArray;
    }

    public static function buildPVOArray($orderId)
    {
        $db          = PearDatabase::getInstance();
        $sql         = "SELECT * FROM `vtiger_orders` WHERE ordersid=?";
        $ordersRow   = $db->pquery($sql, [$orderId])->fetchRow();
        if ($ordersRow) {
            try {
                return self::initializePaperworkArray($ordersRow);
            } catch (Exception $ex) {
                //file_put_contents('logs/devLog.log', "\n build array exception : ".print_r($ex, true), FILE_APPEND);
                throw $ex;
            }
            //file_put_contents('logs/devLog.log', "\n BuiltArray : ".print_r($builtArray, true), FILE_APPEND);
            return [];
        } else {
            //something went wrong, can't find the order
            return [];
        }
    }

    public static function initializePaperworkArray($orderInfo)
    {
        $db = PearDatabase::getInstance();
        $builtArray = [];
        $orderRecordModel = Vtiger_Record_Model::getInstanceById($orderInfo['ordersid']);
        $primaryEstimateRecordModel = $orderRecordModel->getPrimaryEstimateRecordModel();

        switch (getenv('INSTANCE_NAME')) {
            //This should probably be made to get the vanline id from the record in the DB
            case 'graebel':
                $builtArray['van_line_id'] = '17';
                break;
            case 'sirva':
                $builtArray['van_line_id'] = '18';
                break;
            case 'premier':
                $builtArray['van_line_id'] = '191';
                break;
            case 'arpin':
                $builtArray['van_line_id'] = '2';
                break;
            default:
                $builtArray['van_line_id'] = '4';
                break;
        }

        //contact info
        if ($orderInfo['orders_contacts']) {
            list ($builtArray['first_name'], $builtArray['last_name'], $builtArray['email']) = self::getOrderContactInfo($orderInfo['orders_contacts']);
        }

        if ($orderInfo['orders_trip']) {
            $sql     = "SELECT * FROM `vtiger_trips` WHERE tripsid = ?";
            $tripInfo = $db->pquery($sql, [$orderInfo['orders_trip']])->fetchRow();

            $builtArray['trip_id'] = $tripInfo['trips_id'] ?: null;
        }

        $builtArray['gross_weight'] = $orderInfo['orders_gweight'];
        $builtArray['tare_weight'] = $orderInfo['orders_tweight'];
        $builtArray['net_weight'] = $orderInfo['orders_netweight'];
        $builtArray['billed_weight'] = $orderInfo['orders_minweight'];

        //Initialize dates array
        $builtArray['dates'] = self::initializeOrderDates($orderInfo, false);

        //est info
        if ($primaryEstimateRecordModel) {
            $estimateId = $primaryEstimateRecordModel->getId();
            $sql     = "SELECT * FROM `vtiger_quotes` WHERE quoteid = ?";
            $estInfo = $db->pquery($sql, [$estimateId])->fetchRow();
            //file_put_contents('logs/devLog.log', "\n EstInfo : ".print_r($estInfo, true), FILE_APPEND);
            $builtArray['weight_override'] = ($estInfo['weight']) ? xmlspecialchars($estInfo['weight']) : null;
            $builtArray['van_line_id'] = Estimates_Record_Model::getVanlineIdStatic($estimateId);

            if ($primaryEstimateRecordModel &&
                method_exists($primaryEstimateRecordModel, 'getModuleName') &&
                $primaryEstimateRecordModel->getModuleName() == 'Actuals'
            ) {
                //@NOTE: These items come from an Actual if it's there.  Otherwise it's from the orders.
                $builtArray['gross_weight']  = $primaryEstimateRecordModel->get('gweight');
                $builtArray['tare_weight']   = $primaryEstimateRecordModel->get('tweight');
                $builtArray['net_weight']    = $primaryEstimateRecordModel->get('weight');
                $builtArray['billed_weight'] = $primaryEstimateRecordModel->get('billed_weight');

                //loaddate
                $datesArray['load_requested'] = $primaryEstimateRecordModel->get('load_date') ? date('m/d/Y', strtotime($primaryEstimateRecordModel->get('load_date'))) : null;
                //delivery date
                $datesArray['deliver_requested'] = $primaryEstimateRecordModel->get('delivery_date') ? date('m/d/Y', strtotime($primaryEstimateRecordModel->get('delivery_date'))) : null;
            }
        }

        //Initialize dates array
        $builtArray['dates'] = self::initializeOrderDates($orderInfo, false);

        $builtArray['weight_factor']  = self::$hardcoded['weightFactor'];
        $builtArray['job_status']     = self::$hardcoded['jobStatus'];
        $builtArray['order_number']   = $orderInfo['orders_no'];

        $builtArray['gbl_number'] = ($orderInfo['gbl_number']) ? $orderInfo['gbl_number'] : null;

        $builtArray['notes']  = '';
        foreach (\ModComments_Record_Model::getAllParentComments($orderInfo['ordersid']) as $commentModule) {
            $builtArray['notes'] .= ' '.$commentModule->get('commentcontent');
        }

        //account info
        if ($orderInfo['orders_account']) {
            $sql     = "SELECT * FROM `vtiger_account` WHERE accountid = ?";
            $accountInfo = $db->pquery($sql, [$orderInfo['orders_account']])->fetchRow();
            //file_put_contents('logs/devLog.log', "\n AccountInfo : ".print_r($accountInfo, true), FILE_APPEND);
            $builtArray['primary_phone'] = ($accountInfo['primary_phone']) ? $accountInfo['primary_phone'] : null;
            $builtArray['company_name'] = ($accountInfo['accountname']) ? $accountInfo['accountname'] : null;
        }

        $getv = function($estimateModel, $orderInfo, $f)
        {
            $res = null;
            if($estimateModel)
            {
                $res = $estimateModel->get($f);
            }
            $res = $res ?: ($orderInfo[$f] ?: null);
            return $res;
        };

        $builtArray['origin_info'] =[
            'add1' => $getv($primaryEstimateRecordModel, $orderInfo, 'origin_address1'),
            'add2' => $getv($primaryEstimateRecordModel, $orderInfo, 'origin_address2'),
            'city' => $getv($primaryEstimateRecordModel, $orderInfo, 'origin_city'),
            'state' => $getv($primaryEstimateRecordModel, $orderInfo, 'origin_state'),
            'zip' => $getv($primaryEstimateRecordModel, $orderInfo, 'origin_zip'),
            'country' => $getv($primaryEstimateRecordModel, $orderInfo, 'origin_country'),
            'other_phone#1' => self::formatUSPhone($getv($primaryEstimateRecordModel, $orderInfo, 'origin_phone1')),
            'other_phone#2' =>  self::formatUSPhone($getv($primaryEstimateRecordModel, $orderInfo, 'origin_phone2')),
            //non-html compatible phone nodes:
            'home_phone' => self::formatUSPhone($getv($primaryEstimateRecordModel, $orderInfo, 'origin_phone1')),
            'work_phone' => self::formatUSPhone($getv($primaryEstimateRecordModel, $orderInfo, 'origin_phone2')),
        ];

        $builtArray['dest_info'] =[
            'add1' => $getv($primaryEstimateRecordModel, $orderInfo, 'destination_address1'),
            'add2' => $getv($primaryEstimateRecordModel, $orderInfo, 'destination_address2'),
            'city' => $getv($primaryEstimateRecordModel, $orderInfo, 'destination_city'),
            'state' => $getv($primaryEstimateRecordModel, $orderInfo, 'destination_state'),
            'zip' => $getv($primaryEstimateRecordModel, $orderInfo, 'destination_zip'),
            'country' => $getv($primaryEstimateRecordModel, $orderInfo, 'destination_country'),
            'other_phone#1' => self::formatUSPhone($getv($primaryEstimateRecordModel, $orderInfo, 'destination_phone1')),
            'other_phone#2' => self::formatUSPhone($getv($primaryEstimateRecordModel, $orderInfo, 'destination_phone2')),
            //non-html compatible phone nodes:
            'home_phone' => self::formatUSPhone($getv($primaryEstimateRecordModel, $orderInfo, 'destination_phone1')),
            'work_phone' => self::formatUSPhone($getv($primaryEstimateRecordModel, $orderInfo, 'destination_phone2')),
        ];

        //Get Participating Agents
        $participatingAgents = self::initializeParticipatingAgents($orderInfo['ordersid']);
        foreach ($participatingAgents as $type => $agentInfo) {
            $builtArray[$type] = $agentInfo;
        }

        $driverId = $db->pquery("SELECT moveroles_employees FROM `vtiger_moveroles`
                     INNER JOIN `vtiger_crmentity` ON `vtiger_moveroles`.moverolesid  = `vtiger_crmentity`.crmid
                     WHERE moveroles_orders = ? AND moveroles_role = 'Driver' AND deleted = 0 LIMIT 1", [$orderInfo['ordersid']])->fetchRow()['moveroles_employees'];
        //file_put_contents('logs/devLog.log', "\n D-ID: $driverId", FILE_APPEND);
        if ($driverId) {
            $employeeInfo = $db->pquery("SELECT * FROM `vtiger_employees`
                     INNER JOIN `vtiger_crmentity` ON `vtiger_employees`.employeesid  = `vtiger_crmentity`.crmid
                     WHERE employeesid = ? AND deleted = 0", [$driverId])->fetchRow();
            //file_put_contents('logs/devLog.log', "\n EmployeeInfo : ".print_r($employeeInfo, true), FILE_APPEND);
            if ($employeeInfo) {
                $builtArray['pvo_driver_data'] = [
                    'safety_number' => $employeeInfo['driver_no'] ? $employeeInfo['driver_no'] : null,
                    'driver_number' => $employeeInfo['driver_no'] ? $employeeInfo['driver_no'] : null,
                    //'driver_password' => '' ???
                    'driver_email' => $employeeInfo['employee_email'] ? $employeeInfo['employee_email'] : null,
                    'unit_number' => $employeeInfo['contractor_trailernumber'] ? $employeeInfo['contractor_trailernumber'] : null, //???
                    'tractor_number' => $employeeInfo['contractor_trucknumber'] ? $employeeInfo['contractor_trucknumber'] : null, //???
                    //'damages_report_view' => 'Codes', //deault to Codes ???
                    'driver_type' => 'Driver', //default to Driver ???
                ];
                //lastname is mandatory, name is not. Account for both situations
                if ($employeeInfo['name'] && $employeeInfo['employee_lastname']) {
                    $builtArray['pvo_driver_data']['driver_name'] = $employeeInfo['name'] . ' ' . $employeeInfo['employee_lastname'];
                } elseif ($employeeInfo['employee_lastname']) {
                    $builtArray['pvo_driver_data']['driver_name'] = $employeeInfo['employee_lastname'];
                }
            }
        }

        if ($participatingAgents['hauling_agent']) {
            $builtArray['pvo_driver_data']['hauling_agent'] = ($participatingAgents['hauling_agent']['code']) ? $participatingAgents['hauling_agent']['code'] : null;
            $builtArray['pvo_driver_data']['hauling_agent_email'] = ($participatingAgents['hauling_agent']['email']) ? $participatingAgents['hauling_agent']['email'] : null;
        }

        $builtArray['pvo_driver_inventory'] = null;
        if ($primaryEstimateRecordModel) {
            $estimateUploadArray = self::buildInterstateArray($primaryEstimateRecordModel->getId(), $orderInfo['business_line'])['survey_upload'];
            foreach ($estimateUploadArray as $item => $value) {
                // not sure how to handle arrays
                if ($builtArray[$item]) {
                    continue;
                }
                $builtArray[$item] = $value;
            }
        }

        return ['survey_upload' => $builtArray];
    }

    protected static function buildInterstateArray($estimateId, $businessLine, $tablePrefix = '', $dov = true, $requote = false)
    {
        file_put_contents('logs/xml.log', date('Y-m-d H:i:s - ')."Entering buildInterstateArray\n", FILE_APPEND);
        $db = PearDatabase::getInstance();
        $builtArray = [];
        $isSirva    = getenv('INSTANCE_NAME') == 'sirva';
        $isGraebel  = getenv('INSTANCE_NAME') == 'graebel';
        //file_put_contents('logs/devLog.log', "\n IsSirva : ".print_r($isSirva, true), FILE_APPEND);
        // file_put_contents('logs/xml.log', date('Y-m-d H:i:s - ')."Building \$quotesRow\n", FILE_APPEND);
        if ($estimateId) {
            //Get info from quotes tables
            $sql           =
                "SELECT * FROM ".
                "`".$tablePrefix."vtiger_quotes` JOIN ".
                "`".$tablePrefix."vtiger_quotescf` ON ".
                "`".$tablePrefix."vtiger_quotes`.quoteid=".
                "`".$tablePrefix."vtiger_quotescf`.quoteid ".
                " JOIN ".
                "`".$tablePrefix."vtiger_crmentity` ON ".
                "`".$tablePrefix."vtiger_quotes`.quoteid=".
                "`".$tablePrefix."vtiger_crmentity`.crmid ".
                " WHERE `".$tablePrefix."vtiger_quotes`.quoteid=?";
            $result        = $db->pquery($sql, [$estimateId]);
            $quoteRow      = $result->fetchRow();
            $oppId         = $quoteRow['potentialid'];
            $shipperType   = $quoteRow['shipper_type'];
            $conId         = $quoteRow['contactid'];
            $contractId    = $quoteRow['contract'];
            $businessLine  = $quoteRow['business_line_est'];
            $ownerID       = $quoteRow['smownerid'];
            $coordinatorID = $quoteRow['coordinator'];
            $agentID       = $quoteRow['agentid'];
            $orderId       = $quoteRow['orders_id'];
            $contactEmail = $quoteRow['email'];
            //OT 16956
            if (!$isSirva) {
                $detailLineItemArray = self::addDetailedLineItems($estimateId, $tablePrefix);
            }
        } else {
            //no estimateID so we fall back and use the OppID it'll magickally work!
            $oppId    = self::$useOppId;
            $quoteRow = [];
        }
        //OT 16747
        $builtArray = self::addOrderAndAccountFields($orderId, $quoteRow['accountid'], $quoteRow['description']);
        $builtArray['lead_type'] = ($quoteRow['billing_type'] == 'NAT' || $quoteRow['shipper_type'] == 'NAT') ? 'National Account' : 'Consumer';
        if ($detailLineItemArray) {
            $builtArray['detailed_line_items'] = $detailLineItemArray;
        }
        //file_put_contents('logs/xml.log', date('Y-m-d H:i:s - ')."Building \$oppRow\n", FILE_APPEND);
        if ($orderId) {
            try {
                $orderRecord = Vtiger_Record_Model::getInstanceById($orderId, 'Orders');
                if (!$conId) {
                    $conId = $orderRecord->get('orders_contacts');
                }
            } catch (Exception $ex) {

            }
        }
        //Get info from opportunity tables
        if ($oppId) {
            $sql    = "SELECT * FROM `vtiger_potential` JOIN `vtiger_potentialscf` ON vtiger_potential.potentialid=vtiger_potentialscf.potentialid JOIN vtiger_crmentity ON vtiger_potential.potentialid=vtiger_crmentity.crmid WHERE vtiger_potential.potentialid=?";
            $result = $db->pquery($sql, [$oppId]);
            $oppRow = $result->fetchRow();
            if (!$conId) {
                $conId = $oppRow['contact_id'];
            }
        } else {
            $oppRow = [];
        }
        //file_put_contents('logs/xml.log', date('Y-m-d H:i:s - ')."Building \$conRow\n", FILE_APPEND);
        //Get info from contact tables
        if ($conId) {
            //sad.
            $sql    = "SELECT * FROM `vtiger_contactdetails` join vtiger_contactaddress on (vtiger_contactdetails.contactid = vtiger_contactaddress.contactaddressid) WHERE contactid=?";
            $result = $db->pquery($sql, [$conId]);
            $conRow = $result->fetchRow();
            $contactEmail = $conRow['email'];
        } else {
            $conRow = [];
        }
        //file_put_contents('logs/xml.log', date('Y-m-d H:i:s - ')."Building \$contractRow -- ($contractId)\n", FILE_APPEND);
        //Get info from contract tables
        // previously contracts were restricted to interstate moves, but I don't think that's true anymore
        $allowContract = $businessLine == 'Interstate Move';
        if ($isGraebel) {
            $allowContract = true;
        }
        if ($contractId != '0' && $allowContract) {
            $sql           = "SELECT * FROM `vtiger_contracts` WHERE contractsid=?";
            $result        = $db->pquery($sql, [$contractId]);
            $contractRow   = $result->fetchRow();
            $contractModel = Vtiger_Record_Model::getInstanceById($contractId);
        } else {
            $contractRow = [];
        }

        if($isGraebel && $contractRow['related_tariff'])
        {
            $tariffID = $contractRow['related_tariff'];
        } else {
            $tariffID = $quoteRow['effective_tariff'];
        }
        if ($estimateId && $tariffID == 0) {
            //file_put_contents('logs/xml.log', date('Y-m-d H:i:s - ')."Throwing exception no Tariff selected\n", FILE_APPEND);
            //Get info from contract tables
            throw new Exception('No tariff selected on the primary estimate.');
        }

        if ($tariffID) {
            $effectiveTariffRecordModel = Vtiger_Record_Model::getInstanceById($tariffID);
            $customTariffType           = $effectiveTariffRecordModel->get('custom_tariff_type');
            $customTariffID           = $effectiveTariffRecordModel->get('custom_tariff_id') ?: null;
            $tariffManagerName          = $effectiveTariffRecordModel->get('tariffmanagername');
        } else {
            $customTariffType  = '';
            $tariffManagerName = '';
            $customTariffID = null;
        }

        $builtArray['tariff_manager_name'] = $tariffManagerName;

        if ($isSirva && $customTariffType == 'Intra - 400N') {
            $sql = "SELECT vtiger_crmentity.agentid, ".$tablePrefix."vtiger_quotes.local_carrier FROM ".$tablePrefix."vtiger_quotes
                    JOIN vtiger_crmentity ON ".$tablePrefix."vtiger_quotes.quoteid = vtiger_crmentity.crmid
                    WHERE ".$tablePrefix."vtiger_quotes.quoteid = ? AND vtiger_crmentity.deleted = 0";
            $agent = $db->pquery($sql, [$estimateId]);
            $row = $agent->fetchRow();
            $agent = $row[0];
            $localCarrier = $row[1];

            //Initialize local Carriers
            $vanLineBrand = Estimates_Record_Model::getVanlineBrandStatic($estimateId, $tablePrefix);
            $builtArray['local_carriers'] = self::initializeLocalCarrier($db, $agent, $vanLineBrand, $localCarrier);
            if(sizeof($builtArray['local_carriers'])) {
                $builtArray['carrier_agent'] = $builtArray['local_carriers']['carrier#0'];
            }
        }

        //Establish vanline ID
        if ($isSirva) {
            $vanLineId = 18;
            $qlabId    = Estimates_Record_Model::getVanlineIdStatic(($estimateId ? $estimateId : $oppId), $tablePrefix);
        } else {
            $vanLineId = Estimates_Record_Model::getVanlineIdStatic(($estimateId ? $estimateId : $oppId), $tablePrefix);
        }
        //Establish pricing mode, move type, and mileage
        $tariffPricingModeMap = [
            '400N Base' => '400N',
            '400N/104G' => '400N_104G',
            '400NG' => '400NG',
            'Intra - 400N' => 'INTRA_400N',
            'MSI' => 'MSI',
            'MMI' => 'MMI',
            'AIReS' => 'AIReS',
            'RMX400' => 'RMX400',
            'RMW400' => 'RMW400',
            'ISRS200-A' => 'ISRS_200_A',
            '09CapRelo' => '09_CAP_RELO',
            'GSA-500A' => 'GSA500A',
            '400DOE' => '400DOE',
            'Autos Only' => 'AUTO_ONLY',
            'ALLV-2A' => 'NAT_UAS',
            'NAVL-12A' => 'NAT_UAS'
        ];

        if (array_key_exists($customTariffType, $tariffPricingModeMap)
        ) {
            $pricingMode = $tariffPricingModeMap[$customTariffType];
            $moveType = 'Interstate';
            $miles    = $quoteRow['interstate_mileage'];
        } elseif ($customTariffType == 'Base' && $tariffManagerName == '400N') {
            $pricingMode = '400N';
            $moveType    = 'Interstate';
            $miles       = $quoteRow['interstate_mileage'];
        } elseif ($businessLine == 'Interstate Move' || $businessLine == 'Intrastate Move' || $businessLine == 'Interstate' || $businessLine == 'Intrastate') {
            $pricingMode = 'Interstate';
            //$pricingMode = '400N_104G';
            //$pricingMode = '400N';
            $moveType = 'Interstate';
            $miles    = $quoteRow['interstate_mileage'];
        } elseif ($businessLine == 'Commercial Move') {
            //TODO: Fill in appropriate values for commercial moves
            $pricingMode = '';
            $moveType    = '';
        }
        if ($customTariffType == 'Allied Express' || $customTariffType == 'Blue Express') {
            self::$expressLoadingOrigin = 'true';
            self::$expressLoadingDest   = 'true';
        }
        $sql      = "SELECT time_zone FROM `vtiger_users` WHERE id=?";
        $res      = $db->pquery($sql, [Users_Record_Model::getCurrentUserModel()->getId()]);
        $timeZone = $res->fields['time_zone'];
        //Initialize dates array
        if (getenv('INSTANCE_NAME') == 'graebel' && $orderId > 0) {
            $db          = PearDatabase::getInstance();
            $sql         = "SELECT * FROM `vtiger_orders` WHERE ordersid=?";
            $ordersRow   = $db->pquery($sql, [$orderId])->fetchRow();
            $datesArray = self::initializeOrderDates($ordersRow);
            // OT 4163
            // override load date with Estimate one
            // doing this so fuel surcharge will use this date instead of a random order one or today
            $datesArray['load_from']         = ($quoteRow['load_date'])?date('m/d/Y', strtotime($quoteRow['load_date']))
                :$datesArray['load_from'];

            $builtArray['gross_weight'] = $ordersRow['orders_gweight'];
            $builtArray['tare_weight'] = $ordersRow['orders_tweight'];
            $builtArray['net_weight'] = $ordersRow['orders_netweight'];
            //$builtArray['billed_weight'] = $ordersRow['orders_minweight'];
            // OT 3912 - always take billed weight from estimate/actual
            $builtArray['billed_weight'] = $quoteRow['billed_weight'];
            if ($quoteRow['setype'] == 'Actuals') {
                //if it's an actual record we override the weights from the order and the dates.
                $builtArray['gross_weight']  = $quoteRow['gweight'];
                $builtArray['tare_weight']   = $quoteRow['tweight'];
                $builtArray['net_weight']    = $quoteRow['weight'];

                //loaddate
                $datesArray['load_requested'] = $quoteRow['load_date'] ? date('m/d/Y', strtotime($quoteRow['load_date'])) : null;
                //delivery date
                $datesArray['deliver_requested'] = $quoteRow['delivery_date'] ? date('m/d/Y', strtotime($quoteRow['delivery_date'])) : null;
            }
        } else {
            if($orderId) {
                $sql         = "SELECT `vtiger_orders`.* FROM `vtiger_orders` JOIN `vtiger_crmentity` ON `vtiger_orders`.ordersid=`vtiger_crmentity`.crmid WHERE ordersid=? AND deleted=0";
                $ordersRow   = $db->pquery($sql, [$orderId])->fetchRow();
                $datesArray = self::initializeOrderDates($ordersRow);
            } else {
                $datesArray = self::initializeDates($oppRow, $quoteRow);
            }
        }
        //Get Participating Agents
        if ($orderId) {
            $participatingAgents = self::initializeParticipatingAgents($orderId);
        } else {
            $participatingAgents = self::initializeParticipatingAgents($oppId);
        }

        //Initialize misc items array
        if ($estimateId) {
            $miscArray = self::initializeMisc($estimateId, $tablePrefix);
            //initialize Extra Stops array
            $extraStopsArray = self::initializeExtraStops($db, $estimateId, $isSirva, $tablePrefix);
            //Initialize rooms array
            $roomsArray = self::initializeCubesheetNode($estimateId, $hasCubesheetNode, false, $tablePrefix, $extraStopsArray);
            //Initialize accessorials array
            $accessorialsArray = self::initializeInterstateAccessorials($quoteRow, $tablePrefix, $customTariffType);
            //so ministorage is located outside of the accessorials tag.
            $miniStorageOrg  = [];
            $miniStorageDest = [];
            if ($quoteRow['acc_selfstg_origin_applied']) {
                $miniStorageOrg['weight'] = xmlspecialchars($quoteRow['acc_selfstg_origin_weight']);
                $miniStorageOrg['acc_selfstg_ot']  = ($quoteRow['acc_selfstg_origin_ot'] == '0')?'false':'true';
            }
            if ($quoteRow['acc_selfstg_dest_applied']) {
                $miniStorageDest['weight'] = xmlspecialchars($quoteRow['acc_selfstg_dest_weight']);
                $miniStorageDest['acc_selfstg_ot']  = ($quoteRow['acc_selfstg_dest_ot'] == '0')?'false':'true';
            }
            //Initialize SIT array
            $sitArray = self::initializeInterstateSIT($quoteRow);
            //Initialize vehicles array
            $vehiclesArray = self::initializeVehicles($db, $customTariffType, $estimateId, $isSirva, $tablePrefix);
        }
//        if (!$extraStopsArray && $orderId) {
//            //Removing table prefix from stops call as it won't apply to existing order here.
//            $extraStopsArray = self::initializeExtraStops($db, $orderId, $isSirva);
//        }
        //Initialize special services array
        //Hardcoded for now

        $specialServicesArray                        = [];
        $specialServicesArray['excl_use_applied']    = 'false';
        $specialServicesArray['space_res_applied']   = 'false';
        $specialServicesArray['exp_service_applied'] = 'false';

        if ($quoteRow['accesorial_exclusive_vehicle'] && $quoteRow['accesorial_exclusive_vehicle'] !== 'false') {
            $specialServicesArray['excl_use_applied'] = 'true';
        }
        if ($quoteRow['exclusive_use'] && $quoteRow['exclusive_use'] !== 'false') {
            $specialServicesArray['excl_use_applied'] = 'true';
            $specialServicesArray['excl_use_cuft'] = $quoteRow['exclusive_use_cuft'];
        }

        if ($quoteRow['accessorial_space_reserve_bool'] && $quoteRow['accessorial_space_reserve_bool'] !== 'false') {
            $specialServicesArray['space_res_applied']   = 'true';
        }
        if (InputUtils::CheckboxToBool($quoteRow['space_reservation'])) {
            $specialServicesArray['space_res_applied']   = 'true';
            $specialServicesArray['space_res_cuft']   = $quoteRow['space_reservation_cuft'];
        }

        if ($quoteRow['accesorial_expedited_service'] && $quoteRow['accesorial_expedited_service'] !== 'false') {
            $specialServicesArray['exp_service_applied'] = 'true';
            if ($customTariffType == '1950-B') {
                $specialServicesArray['exp_service_weight'] = 5000;
            }
        }

        $specialServicesArray['gsa500_extra_driver_hours'] = xmlspecialchars($quoteRow['gsa500_extra_driver_hours']);
        $specialServicesArray['gsa500_washing_machine_employee'] = xmlspecialchars($quoteRow['gsa500_washing_machine_employee']);
        $specialServicesArray['gsa500_washing_machine_tsp'] = xmlspecialchars($quoteRow['gsa500_washing_machine_tsp']);
        $specialServicesArray['gsa500_washing_machine_pedestal'] = xmlspecialchars($quoteRow['gsa500_washing_machine_pedestal']);

        //Initialize GPP custom pack
        //$gppCustomPack = ($quoteRow['apply_custom_pack_rate_override'] === 'true' || $quoteRow['apply_full_pack_rate_override'] === 'true')?'true':'false';
        //@TODO: this needs more rules.
        $gppCustomPack = 'false';
        if (
            $quoteRow['apply_full_pack_rate_override'] &&
            $quoteRow['apply_full_pack_rate_override'] !== 'false'
        ) {
            $gppCustomPack = 'true';
        } elseif (
            $quoteRow['apply_custom_pack_rate_override'] &&
            $quoteRow['apply_custom_pack_rate_override'] !== 'false'
        ) {
            $gppCustomPack = 'true';
        }

        //Initialize price override
        $priceOverride = $quoteRow['desired_total'];
        //Initialize contract array
        $contractArray = self::initializeContract($contractRow, $contractModel);

        //Initialize interstate data array
        $interstateDataArray = [];
        if ($isSirva) {
            $interstateDataArray['report_value#1']  = ['ValueID' => 12, 'OptionID' => (($quoteRow['grr_override'])?$quoteRow['grr_override_amount']:$quoteRow['grr'])];
            // file_put_contents('logs/devLog.log', "\n DOV Check: " . $dov, FILE_APPEND);
            $interstateDataArray['report_value#2']  = ['ValueID' => 15, 'OptionID' => ($dov == 'true' ? '1' : '0')];
            $interstateDataArray['grr_cp_amount'] = $quoteRow['grr_cp'];
            $interstateDataArray['tpg_origin_smf'] = $quoteRow['local_origin_acc'];
        }
        if ($quoteRow['apply_free_fvp']) {
            $freeFvp                         = [];
            $freeFvp['amount']               = $quoteRow['free_valuation_limit'];
            $freeFvp['per100_rate']          = $quoteRow['rate_per_100'];
            $freeFvp['free_val_lb']          = $quoteRow['min_declared_value_mult'];
            $freeFvp['val_flat_charge']      = $quoteRow['valuation_flat_charge'];
            $freeFvp['increased_base']       = $quoteRow['increased_base'];
            $freeFvp['corp_val_type']        = $quoteRow['free_valuation_type'];
            $interstateDataArray['free_fvp'] = $freeFvp;
        }
        $interstateDataArray['accessorials']               = $accessorialsArray;
        if ($quoteRow['acc_selfstg_origin_applied']) {
            $interstateDataArray['mini_storage location="Origin"'] = $miniStorageOrg;
        }
        if ($quoteRow['acc_selfstg_dest_applied']) {
            $interstateDataArray['mini_storage location="Destination"'] = $miniStorageDest;
        }
        $interstateDataArray['storage_inspection_fee']     = $quoteRow['storage_inspection_fee'] == '0'?'false':'true';
        $interstateDataArray['full_pack']                  = ($quoteRow['full_pack'] == '0')?'false':'true';
        $interstateDataArray['full_unpack']                = ($quoteRow['full_unpack'] == '0')?'false':'true';
        $interstateDataArray['sit']                        = $sitArray;

        if ($isSirva) {
            $sql = 'SELECT * FROM `vtiger_autospotquote` WHERE `estimate_id` = ?';
            $autos = $db->pquery($sql, [$estimateId]);
            $autoArray = [];
            if ($db->num_rows($autos)) {
                $auto_count =1;

                while ($row =& $autos->fetchRow()) {
                    $quote_info = json_decode(urldecode($row['auto_quote_info']));

                    switch (intval($row['auto_quote_select'])) {
                        case 4:
                            $spread_info = $quote_info->rates->two_day_pickup;
                            $spread = 2;
                            break;
                        case 3:
                            $spread_info = $quote_info->rates->four_day_pickup;
                            $spread = 4;
                            break;
                        case 2:
                            $spread_info = $quote_info->rates->seven_day_pickup;
                            $spread = 7;
                            break;
                        case 1:
                            $spread_info = $quote_info->rates->ten_day_pickup;
                            $spread = 10;
                            break;
                    }

                    $auto['make']                   = $row['auto_make'];
                    $auto['model']                  = $row['auto_model'];
                    $auto['year']                   = $row['auto_year'];
                    $auto['service_type']           = 'Quote';
                    $auto['car_on_van']             = $row['auto_transport_typ'] == 'Open Trailer' ? 'true' : 'false';
                    $auto['oversize_class_type']    = 'None';
                    $auto['inoperable']             = $row['auto_condition'] == 'Running' ? 'true' : 'false';
                    $auto['shipping']               = 1;
                    $auto['load_from']              = date('m/d/Y', strtotime($row['auto_load_from']));
                    $auto['load_to']                = str_replace('-', '/', $spread_info->load_to_date);
                    $auto['deliver_from']           = str_replace('-', '/', $spread_info->deliver_from_date);
                    $auto['deliver_to']             = str_replace('-', '/', $spread_info->deliver_to_date);
                    $auto['vehicle_smf']            = $row['auto_smf'];
                    $auto['express_load']           = $row['auto_rush_fee'] != 0 ? 'true' : 'false';
                    $auto['montway_id']             = $quote_info->quote_id;
                    $auto['quote_expiration']       = date('m/d/Y', strtotime($quote_info->expires_at));
                    $auto['quote_data']             = date('m/d/Y');

                    $spread_info_formatted['quote_spread isSelected="true"']['spread'] = $spread;
                    $spread_info_formatted['quote_spread isSelected="true"']['rate'] = $spread_info->price;
                    $spread_info_formatted['quote_spread isSelected="true"']['type'] = $spread_info->type;

                    $auto['quote_spreads'] = $spread_info_formatted;
                    $autoArray['vehicle#' . $auto_count++] = $auto;
                }
            }

            $interstateDataArray['vehicles'] = $autoArray;
        }

        //@TODO: there are also discounts for, but we don't have handling for these:
        // "day_certain"
        // "crate"
        // file_put_contents('logs/devLog.log', "\n $customTariffType", FILE_APPEND);
        if ($customTariffType != '1950-B') {
            $interstateDataArray['discounts positive="false"'] = [
                'bottom_line'  => xmlspecialchars($quoteRow['bottom_line_discount']),
                'accessorials' => xmlspecialchars($quoteRow['accessorial_disc']),
                'linehaul'     => xmlspecialchars($quoteRow['linehaul_disc']),
                'packing'      => xmlspecialchars($quoteRow['packing_disc']),
                'sit'          => xmlspecialchars($quoteRow['sit_disc'])
            ];
        } else {
            $interstateDataArray['discounts positive="false"'] = [
                'bottom_line'  => xmlspecialchars($quoteRow['bottom_line_discount']),
                'sit'          => xmlspecialchars($quoteRow['sit_disc']),
                'crate'        => xmlspecialchars($quoteRow['crating_disc'])
            ];
        }

        if ($isGraebel) {
            if ($quoteRow['valuation_discounted'] && $customTariffType == '1950-B') {
                $interstateDataArray['discounts positive="false"']['discount_val'] = $quoteRow['valuation_discounted']?'true':'false';
            }
            //@TODO: work out which of these is correct, for now send all of them, so reports has access to them.
            $interstateDataArray['discounts positive="false"']['sit_distribution_discount'] = $quoteRow['sit_distribution_discount'];
            $interstateDataArray['discounts positive="false"']['bottom_line_distribution_discount'] = $quoteRow['bottom_line_distribution_discount'];
            $interstateDataArray['discounts positive="false"']['distribution_discount_percentage'] = $quoteRow['distribution_discount_percentage'];
            $interstateDataArray['discounts positive="false"']['distribution_discount'] = $quoteRow['distribution_discount'];
        } elseif (getenv('IGC_MOVEHQ')) {
            if ($quoteRow['valuation_discounted'] && $quoteRow['valuation_discount_amount'] && $customTariffType == '1950-B') {
                $interstateDataArray['discounts positive="false"']['discount_val'] = $quoteRow['valuation_discounted']?'true':'false';
                $interstateDataArray['valuation_charge']                           = $quoteRow['valuation_discount_amount'];
            }
            //@TODO: work out which of these is correct, for now send all of them, so reports has access to them.
            $interstateDataArray['discounts positive="false"']['sit_distribution_discount'] = $quoteRow['sit_distribution_discount'];
            $interstateDataArray['discounts positive="false"']['bottom_line_distribution_discount'] = $quoteRow['bottom_line_distribution_discount'];
            $interstateDataArray['discounts positive="false"']['distribution_discount_percentage'] = $quoteRow['distribution_discount_percentage'];
            $interstateDataArray['discounts positive="false"']['distribution_discount'] = $quoteRow['distribution_discount'];
        }

        if (
            $customTariffType == '400N Base' ||
            $customTariffType == '400N/104G' ||
            $customTariffType == '400NG' ||
            $customTariffType == 'Intra - 400N' ||
            $customTariffType == 'NAVL-12A' ||
            $customTariffType == 'ALLV-2A'
        ) {
            //the user should be allowed to set these, AND 0 is a valid set.
            //if ($quoteRow['accesorial_fuel_surcharge'] > 0) {
            if (isset($quoteRow['accesorial_fuel_surcharge'])) {
                $consumptionFuel = $quoteRow['consumption_fuel'];
                if ($consumptionFuel && $consumptionFuel != 'no' && $consumptionFuel != 'off') {
                    $interstateDataArray['corp_fuel_consumption']  = 'true';
                    $interstateDataArray['fuel_surcharge_consump'] = $quoteRow['accesorial_fuel_surcharge'];
                } else {
                    $interstateDataArray['corp_fuel_consumption'] = 'false';
                    $interstateDataArray['fuel_surcharge_pct']    = $quoteRow['accesorial_fuel_surcharge'];
                }
            }
        } else {
            /*
             * We need to set this for reports.  It is removed from the built array before the xml
             * is built when we are rating.
             *
             * Alternatively we would need some sort of flag passed in.
            */
            //if($quoteRow['accesorial_fuel_surcharge'] > 0) {
            if (isset($quoteRow['accesorial_fuel_surcharge'])) {
                $interstateDataArray['corp_fuel_consumption'] = 'true';
                $interstateDataArray['fuel_surcharge_pct'] = $quoteRow['accesorial_fuel_surcharge'];
            }
        }

        if (isset($quoteRow['irr_charge'])) {
            $interstateDataArray['irr_pct'] = $quoteRow['irr_charge'];
        }

        $interstateDataArray['interstate_val']   = self::getValuationDed($quoteRow);

        // paranoia: make sure this isn't set for 400NG
        if (
            ($customTariffType == '400NG' || $customTariffType == '400DOE')
            && $isGraebel) {
            $interstateDataArray['interstate_val'] = '';
        }
        $interstateDataArray['quote_expiration'] = ($quoteRow['validtill'])?date('m/d/Y', strtotime($quoteRow['validtill'])):date('m/d/Y');
        //@NOTE: this is supposed to be a date and is not wrong.
        $interstateDataArray['effective_tariff']      = ($quoteRow['interstate_effective_date'])?date('m/d/Y', strtotime($quoteRow['interstate_effective_date'])):date('m/d/Y');
        //MM HERE OT 1815
        if ($isGraebel) {
            $interstateDataArray['valuation_amount'] = $quoteRow['total_valuation'];
        } elseif (getenv('IGC_MOVEHQ')) {
            $interstateDataArray['valuation_amount'] = $quoteRow['total_valuation'];
        } else {
            $interstateDataArray['valuation_amount'] = $quoteRow['valuation_amount'];
        }
        $interstateDataArray['booker_adjustment']     = self::$hardcoded['bookerAdjustment'];
        if ($contractRow['waive_peak_rates'] == 1) {
            $interstateDataArray['peak'] = 'false';
        } else {
            $interstateDataArray['peak'] = $quoteRow['pricing_type'] == 'Peak' ? 'true' : 'false';
        }
        // TODO: this is probably wrong as well
        $interstateDataArray['eac']                  = $contractRow['waive_eac_rates'] == 1?'true':'false';
        if(getenv('INSTANCE_NAME') == 'mccollisters')
        {
            $interstateDataArray = array_merge($interstateDataArray, $vehiclesArray);
        } elseif(empty($interstateDataArray['vehicles'])) {
            $interstateDataArray['vehicles'] = $vehiclesArray;
        }
        $builtArray['extra_locations']       = $extraStopsArray;
        $interstateDataArray['apply_50_plus']         = self::$hardcoded['apply50Plus'];
        $interstateDataArray['wheaton_peace_of_mind'] = self::$hardcoded['wheatonPOM'];
        $interstateDataArray['wheaton_cnfsg']         = self::$hardcoded['wheatonCNFSG'];
        $interstateDataArray['veterans_program']      = self::$hardcoded['veterans'];
        // OT 16807
        // Rating returns the same miles if you pass this, otherwise it calculates, so in order to update every time...
        if((!$isGraebel && self::$useForReports) || $customTariffType == 'GSA-500A') {
            $interstateDataArray['miles']                 = $miles;
        } else {
            $interstateDataArray['miles'] = '';
        }
        $builtArray['report_miles'] = $quoteRow['interstate_mileage'];
        if ($isSirva && $quoteRow['pricing_level'] != '' && $quoteRow['pricing_level'] != null) {
            $interstateDataArray['pricing_color'] = xmlspecialchars($quoteRow['pricing_level']);
        }
        if ($isSirva && $quoteRow['demand_color'] != '' && $quoteRow['demand_color'] != null) {
            $interstateDataArray['demand_color'] = xmlspecialchars(strtoupper($quoteRow['demand_color']));
        }
        if ($isGraebel) {
            $interstateDataArray['graebel_flat_auto'] = self::initializeGraebelFlatAuto($estimateId, $tablePrefix);
            $interstateDataArray['graebel_total_auto_weight_add'] = $quoteRow['total_auto_weight_1950B'];
        }
        if ($isGraebel && ($customTariffType == '1950-B' || $tariffManagerName == '400N - 104G')) {
            $interstateDataArray['graebel_special_pack'] = self::initializeGraebelSpecialPack($estimateId, $tablePrefix);
            $interstateDataArray['graebel_one_day_load'] =  [
                                                                'enabled'  => $quoteRow['small_shipment'] ? 'true' : 'false',
                                                                'overtime' => $quoteRow['small_shipment_ot'] ? 'true' : 'false',
                                                                'miles'    => $quoteRow['small_shipment_miles']
                                                            ];
            $interstateDataArray['booker_adjustment'] = $quoteRow['pshipping_booker_commission'];
            $interstateDataArray['graebel_priority'] = $quoteRow['priority_shipping'] ? 'true' : 'false';
            $interstateDataArray['graebel_priority_orig_miles'] = $quoteRow['pshipping_origin_miles'];
            $interstateDataArray['graebel_priority_dest_miles'] = $quoteRow['pshipping_destination_miles'];
        }

        $interstateDataArray['estimate_type']    = $quoteRow['estimate_type'];
        $interstateDataArray['special_services'] = $specialServicesArray;
        $interstateDataArray['gpp_custom_pack']  = $gppCustomPack;
        if (
            $isSirva &&
            $customTariffType != '400N/104G' &&
            $customTariffType != '400N Base' &&
            $customTariffType != '400NG'
        ) {
            self::addGppCustomRates($interstateDataArray, $quoteRow, $tablePrefix);
            $interstateDataArray['total_price_override'] = $priceOverride;
        }
        $interstateDataArray['contract'] = $contractArray;
        //Initialize array to return
        $wsid                                  = $oppId ? vtws_getWebserviceEntityId('Opportunities', $oppId) : '';
        $builtArray['sync_field']              = $wsid;
        $builtArray['estimate_number']         = xmlspecialchars($quoteRow['quote_no']);
        $builtArray['subject']                 = xmlspecialchars($quoteRow['subject']);
        $builtArray['terms_conditions']        = xmlspecialchars($quoteRow['terms_conditions']);
        $builtArray['description']             = xmlspecialchars($quoteRow['description']);
        $builtArray['estimate_load_date']      = xmlspecialchars($quoteRow['load_date']);
        $builtArray['pricing_mode']            = xmlspecialchars($pricingMode);
        $builtArray['van_line_id']             = xmlspecialchars($vanLineId);
        if($customTariffID) {
            $builtArray['custom_tariff_id'] = xmlspecialchars($customTariffID);
        }
        $builtArray['qlab_brand_id']           = ($isSirva)?xmlspecialchars($qlabId):'';
        $builtArray['record_changes']          = '';
        //Was told to do this from a reports person. no names.. just reports.
        $builtArray['contact']['first_name']   = xmlspecialchars($conRow['firstname']);
        $builtArray['contact']['last_name']    = xmlspecialchars($conRow['lastname']);
        $builtArray['contact']['email']        = xmlspecialchars($conRow['email']);
        $builtArray['contact']['add1']         = xmlspecialchars($conRow['mailingstreet']);
        $builtArray['contact']['add2']         = xmlspecialchars($conRow['mailingstreet2']);
        $builtArray['contact']['city']         = xmlspecialchars($conRow['mailingcity']);
        $builtArray['contact']['state']        = xmlspecialchars($conRow['mailingstate']);
        $builtArray['contact']['country']      = xmlspecialchars($conRow['mailingcountry']);
        $builtArray['contact']['zip']          = xmlspecialchars($conRow['mailingzip']);
        $builtArray['contact']['office_phone'] = xmlspecialchars($conRow['phone']);
        $builtArray['contact']['mobile_phone'] = xmlspecialchars($conRow['mobile']);
        $builtArray['contact']['home_phone']   = xmlspecialchars($conRow['homephone']);
        $builtArray['first_name']   = xmlspecialchars($conRow['firstname']);
        $builtArray['last_name']    = xmlspecialchars($conRow['lastname']);
        $builtArray['email']        = xmlspecialchars($contactEmail);
        $builtArray['office_phone'] = xmlspecialchars($conRow['phone']);
        $builtArray['mobile_phone'] = xmlspecialchars($conRow['mobile']);
        $builtArray['dates']                   = $datesArray;
        $builtArray['job_status']              = self::$hardcoded['jobStatus'];
        $builtArray['is_OA']                   = self::$hardcoded['isOA'];
        $builtArray['self_haul']               = self::$hardcoded['selfHaul'];
        $builtArray['business_channel']        = self::$hardcoded['businessChannel'];
        $builtArray['move_type']               = $moveType;
        if($isGraebel) {
            $builtArray['graebel_auto_only'] = InputUtils::CheckboxToBool($quoteRow['gvl_vehicle_only'])? 'true' : 'false';
        }
        $builtArray['billing_info']            = self::buildBillingInfo($orderId);
        $builtArray['origin_info']             = self::initializeAddress($quoteRow, 'Origin', $ordersRow ?: $oppRow, $conRow);
        $builtArray['dest_info']               = self::initializeAddress($quoteRow, 'Destination', $ordersRow ?: $oppRow, $conRow);
        foreach ($participatingAgents as $type => $agentInfo) {
            //do i need if ($agentInfo)?
            $builtArray[$type] = $agentInfo;
        }
        //OT 2479 below
        if ($contractRow['min_weight'] && $contractRow['min_weight'] > $quoteRow['weight']) {
            $builtArray['weight_override']         = xmlspecialchars($contractRow['min_weight']);
        } else {
            $builtArray['weight_override'] = xmlspecialchars(ceil($quoteRow['weight']));
        }

        $builtArray['weight_override_applied'] = self::$hardcoded['weight_override_applied'];
        $builtArray['cube_sheet mode="Road"']  = ($hasCubesheetNode)?['weight_factor' => self::$hardcoded['weightFactor'], 'rooms' => $roomsArray]:[];
        $builtArray['misc_items']              = $miscArray;
        $builtArray['operational_list']        = [];
        $builtArray['time_zone']               = $timeZone;
        $builtArray['interstate_data']         = $interstateDataArray;
        $builtArray['new_notes']               = '';
        $builtArray['office_info']             = self::initializeOfficeInfo($oppRow, $ownerID, $quoteRow);

        $builtArray['ValuationSummary'] = self::getValuationSummary($quoteRow['valuation_options'], self::$useForReports);
        $builtArray['new_notes'] = self::getComments($estimateId, $quoteRow['potentialid'], self::$useForReports);

        if ($imageFilePath = self::getValidImageFile($ownerID, 'Users')) {
            $builtArray['salesperson_photo'] = self::base64Image($imageFilePath);
        }

        if ($imageFilePath = self::getValidImageFile($oppRow['smownerid'], 'Users')) {
            $builtArray['coordinator_photo'] = self::base64Image($imageFilePath);
        }

        if ($imageFilePath = self::getValidImageFile($agentID, 'AgentManager')) {
            $builtArray['company_logo'] = self::base64Image($imageFilePath);
        }

        $builtArray['agentInfo'] = self::getAgentInformation($agentID);

        $builtArray['vanlineInfo'] = self::getVanlineInformation($agentID);

        $builtArray['user_terms']              = ['print_terms' => self::$hardcoded['printTerms']];
        if ($oppId) {
            $builtArray['operational_list'] = self::initializeOppList($oppId);
        }
        if ($isSirva) {
            switch ($customTariffType) {
                case '400N Base':
                case '400N/104G':
                case '400NG':
                    $TPGType = '';
                    break;
                case 'TPG':
                case 'Pricelock':
                    $TPGType = 'TPG';
                    break;
                case 'Allied Express':
                case 'Blue Express':
                    $TPGType = 'TPG Express';
                    break;
                case 'TPG GRR':
                case 'Pricelock GRR':
                    $TPGType = 'TPG GRR';
                    break;
                case 'Truckload Express':
                    $TPGType = 'Truckload Express';
                    break;
                default:
                    $TPGType = '';
                    break;
            }
            $builtArray['tpg_type'] = xmlspecialchars($TPGType);
        }

        if(self::$useForReports && getenv('INSTANCE_NAME') != 'sirva') {
            $builtArray['minimums'] = self::getMinimums($estimateId, $builtArray);
        }

        return ['survey_upload' => $builtArray];
    }

    public static function getMinimums($estimateId, $builtArray) {
        $tariff = Estimates_Record_Model::getEffectiveTariff($estimateId);
        if($tariff['custom_tariff_type']) {
            $wsdlURL = $tariff['rating_url'];
            $soapclient = new \soapclient2($wsdlURL, 'wsdl');
            $soapclient->setDefaultRpcParams(true);
            $soapProxy  = $soapclient->getProxy();

            if(method_exists($soapProxy, 'RateMinimumEstimate')) {
                $arr = [ 'survey_upload' => $builtArray ];
                $params = [
                    'caller' => 'VnbZ1BjT4xtFyCKj21Xr',
                    'ratingInput' => base64_encode(xmlBuilder::build($arr))
                ];

                $response = $soapProxy->RateMinimumEstimate($params);
                return [
                    'rates' => $response['RateMinimumEstimateResult']['Rates'],
                    'totals' => $response['RateMinimumEstimateResult']['Totals']
                ];
            }
        }else {
            // It's a local estimate, these don't apply.
            return false;
        }
    }

    protected static function buildLocalArray($estimateId, $tablePrefix = '')
    {
        // file_put_contents('logs/devLog.log', "\n LOCAL", FILE_APPEND);
        $businessLine = 'Local Move';
        $db           = PearDatabase::getInstance();
        //$recordModel = Estimates_Record_Model::getInstanceById($estimateId);
        $builtArray = [];
        $isSirva    = getenv('INSTANCE_NAME') == 'sirva';
        $isGraebel  = getenv('INSTANCE_NAME') == 'graebel';
        //Get info from quotes tables

        $sql          =
            "SELECT * FROM ".
            "`". $tablePrefix. "vtiger_quotes` JOIN ".
            "`". $tablePrefix. "vtiger_quotescf` ON ".
            "`". $tablePrefix. "vtiger_quotes`.quoteid=".
            "`". $tablePrefix. "vtiger_quotescf`.quoteid " .
            " JOIN " .
            "`".$tablePrefix. "vtiger_crmentity` ON ".
            "`".$tablePrefix. "vtiger_quotes`.quoteid=".
            "`".$tablePrefix. "vtiger_crmentity`.crmid ".
            " WHERE `" . $tablePrefix . "vtiger_quotes`.quoteid=?";
        $result   = $db->pquery($sql, [$estimateId]);
        $quoteRow = $result->fetchRow();
        $oppId    = $quoteRow['potentialid'];
        $conId    = $quoteRow['contactid'];
        $orderId  = $quoteRow['orders_id'];
        //OT 16956
        if (!$isSirva) {
            $detailLineItemArray = self::addDetailedLineItems($estimateId, $tablePrefix);
        }
        $contactEmail = $quoteRow['email'];

        $ownerID = $quoteRow['smownerid'];
        $coordinatorID = $quoteRow['coordinator'];
        $agentID = $quoteRow['agentid'];
        //$contractId = $quoteRow['contract'];
        //$businessLine = $quoteRow['business_line_est'];
        //OT 16747
        $builtArray = self::addOrderAndAccountFields($orderId, $quoteRow['accountid'], $quoteRow['description']);
        $builtArray['lead_type'] = ($quoteRow['billing_type'] == 'NAT' || $quoteRow['shipper_type'] == 'NAT') ? 'National Account' : 'Consumer';
        if ($detailLineItemArray) {
            $builtArray['detailed_line_items'] = $detailLineItemArray;
        }
        //Get info from opportunity tables

        if ($orderId) {
            try {
                $orderRecord = Vtiger_Record_Model::getInstanceById($orderId, 'Orders');
                if (!$conId) {
                    $conId = $orderRecord->get('orders_contacts');
                }
            } catch (Exception $ex) {

            }
        }

        //Get info from opportunity tables
        if ($oppId) {
            $sql    = "SELECT * FROM `vtiger_potential` JOIN `vtiger_potentialscf` ON vtiger_potential.potentialid=vtiger_potentialscf.potentialid JOIN `vtiger_crmentity` ON vtiger_potential.potentialid=vtiger_crmentity.crmid WHERE vtiger_potential.potentialid=?";
            $result = $db->pquery($sql, [$oppId]);
            $oppRow = $result->fetchRow();
            if (!$conId) {
                $conId = $oppRow['contact_id'];
            }
        } else {
            $oppRow = [];
        }
        //Get info from contact tables
        if ($conId) {
            //sad.
            $sql    = "SELECT * FROM `vtiger_contactdetails` join vtiger_contactaddress on (vtiger_contactdetails.contactid = vtiger_contactaddress.contactaddressid) WHERE contactid=?";
            $result = $db->pquery($sql, [$conId]);
            $conRow = $result->fetchRow();

            $contactEmail = $conRow['email'];
        } else {
            $conRow = [];
        }

        if (intval($quoteRow['effective_tariff']) == 0) {
            throw new Exception('No tariff selected');
        }

        //@NOTE: this record model is unused.
        //$effectiveTariffRecordModel = Vtiger_Record_Model::getInstanceById($quoteRow['effective_tariff']);

        //Establish vanline ID
        if ($isSirva) {
            $vanLineId = 18;
            $qlabId    = Estimates_Record_Model::getVanlineIdStatic($estimateId, $tablePrefix);
        } else {
            $vanLineId = Estimates_Record_Model::getVanlineIdStatic($estimateId, $tablePrefix);
        }
        //Establish pricing mode, move type, and mileage
        /*
        if(getenv('INSTANCE_NAME') == 'sirva') {
            $pricingMode = 'Local_Tariff';
        } else {
            $pricingMode = 'Local';
        }
        */
        //@TODO figure this out someday.
        $pricingMode = 'Local_Tariff';
        $moveType    = 'Local';
        //Initialize dates array
        if (getenv('INSTANCE_NAME') == 'graebel' && $orderId > 0) {
            $db          = PearDatabase::getInstance();
            $sql         = "SELECT * FROM `vtiger_orders` WHERE ordersid=?";
            $ordersRow   = $db->pquery($sql, [$orderId])->fetchRow();
            $datesArray = self::initializeOrderDates($ordersRow);
            $builtArray['gross_weight'] = $ordersRow['orders_gweight'];
            $builtArray['tare_weight'] = $ordersRow['orders_tweight'];
            $builtArray['net_weight'] = $ordersRow['orders_netweight'];
            $builtArray['billed_weight'] = $ordersRow['orders_minweight'];
            if ($quoteRow['setype'] == 'Actuals') {
                //if it's an actual record we override the weights from the order and the dates.
                $builtArray['gross_weight']  = $quoteRow['gweight'];
                $builtArray['tare_weight']   = $quoteRow['tweight'];
                $builtArray['net_weight']    = $quoteRow['weight'];
                $builtArray['billed_weight'] = $quoteRow['billed_weight'];
                //loaddate
                $datesArray['load_requested'] = $quoteRow['load_date'] ? date('m/d/Y', strtotime($quoteRow['load_date'])) : null;
                //delivery date
                $datesArray['deliver_requested'] = $quoteRow['delivery_date'] ? date('m/d/Y', strtotime($quoteRow['delivery_date'])) : null;
            }
        } else {
            if($orderId) {
                $sql         = "SELECT `vtiger_orders`.* FROM `vtiger_orders` JOIN `vtiger_crmentity` ON `vtiger_orders`.ordersid=`vtiger_crmentity`.crmid WHERE ordersid=? AND deleted=0";
                $ordersRow   = $db->pquery($sql, [$orderId])->fetchRow();
                $datesArray = self::initializeOrderDates($ordersRow);
            } else {
                $datesArray = self::initializeDates($oppRow, $quoteRow);
            }
        }
        //Get Participating Agents
        if ($orderId) {
            $participatingAgents = self::initializeParticipatingAgents($orderId);
        } else {
            $participatingAgents = self::initializeParticipatingAgents($oppId);
        }
        //Initialize misc items array
        $miscArray = self::initializeMisc($estimateId, $tablePrefix, true);

        //Initialize rooms array
        $roomsArray = self::initializeCubesheetNode($estimateId, $hasCubesheetNode, true, $tablePrefix);

        //Initialize extra stops
        if ($estimateId) {
            $extraStopsArray = self::initializeExtraStops($db, $estimateId, $isSirva, $tablePrefix);
        }
        if (!$extraStopsArray && $orderId) {
            //cutting out tablePrefix as it won't apply to the order
            $extraStopsArray = self::initializeExtraStops($db, $orderId, $isSirva);
        }

        //Initialize dynamic local data
        $localDataArray             = [];

        $localDataArray['tariff']   = self::initializeLocalTariffNode($quoteRow, $tablePrefix);
        $localDataArray['estimate'] = self::initializeLocalEstimateNode($quoteRow, $tablePrefix);

        $sql = "SELECT time_zone FROM `vtiger_users` WHERE id=?";
        $res = $db->pquery($sql, [Users_Record_Model::getCurrentUserModel()->getId()]);
        $timeZone = $res->fields['time_zone'];

        if ($isSirva) {
            // Get agent of Estimate.$tablePrefix
            $sql = "SELECT vtiger_crmentity.agentid, ".$tablePrefix."vtiger_quotes.local_carrier FROM ".$tablePrefix."vtiger_quotes
                    JOIN vtiger_crmentity ON ".$tablePrefix."vtiger_quotes.quoteid = vtiger_crmentity.crmid
                    WHERE ".$tablePrefix."vtiger_quotes.quoteid = ? AND vtiger_crmentity.deleted = 0";
            $agent = $db->pquery($sql, [$estimateId]);
            $row = $agent->fetchRow();
            $agent = $row[0];
            $localCarrier = $row[1];

            //Initialize local Carriers
            $vanLineBrand = Estimates_Record_Model::getVanlineBrandStatic($estimateId, $tablePrefix);
            $builtArray['local_carriers'] = self::initializeLocalCarrier($db, $agent, $vanLineBrand, $localCarrier);
            if(sizeof($builtArray['local_carriers'])) {
                $builtArray['carrier_agent'] = $builtArray['local_carriers']['carrier#0'];
            }
        }

        $builtArray['report_miles'] = $quoteRow['interstate_mileage'];
        //Initialize array to return
        $wsid                                 = $oppId ? vtws_getWebserviceEntityId('Opportunities', $oppId) : '';
        $builtArray['sync_field']             = $wsid;
        $builtArray['estimate_number']        = xmlspecialchars($quoteRow['quote_no']);
        $builtArray['estimate_type']          = $quoteRow['estimate_type'];
        $builtArray['subject']                = xmlspecialchars($quoteRow['subject']);
        $builtArray['terms_conditions']        = xmlspecialchars($quoteRow['terms_conditions']);
        $builtArray['description']             = xmlspecialchars($quoteRow['description']);
        $builtArray['estimate_load_date']     = xmlspecialchars($quoteRow['load_date']);
        $builtArray['pricing_mode']           = xmlspecialchars($pricingMode);
        $builtArray['van_line_id']            = xmlspecialchars($vanLineId);
        $builtArray['qlab_brand_id']          = ($isSirva)?xmlspecialchars($qlabId):'';
        $builtArray['record_changes']         = '';
        //Was told to do this from a reports person. no names.. just reports.
        $builtArray['contact']['first_name']   = xmlspecialchars($conRow['firstname']);
        $builtArray['contact']['last_name']    = xmlspecialchars($conRow['lastname']);
        $builtArray['contact']['email']        = xmlspecialchars($conRow['email']);
        $builtArray['contact']['add1']         = xmlspecialchars($conRow['mailingstreet']);
        $builtArray['contact']['add2']         = xmlspecialchars($conRow['mailingstreet2']);
        $builtArray['contact']['city']         = xmlspecialchars($conRow['mailingcity']);
        $builtArray['contact']['state']        = xmlspecialchars($conRow['mailingstate']);
        $builtArray['contact']['country']      = xmlspecialchars($conRow['mailingcountry']);
        $builtArray['contact']['zip']          = xmlspecialchars($conRow['mailingzip']);
        $builtArray['contact']['office_phone'] = xmlspecialchars($conRow['phone']);
        $builtArray['contact']['mobile_phone'] = xmlspecialchars($conRow['mobile']);
        $builtArray['contact']['home_phone']   = xmlspecialchars($conRow['homephone']);
        $builtArray['first_name']   = xmlspecialchars($conRow['firstname']);
        $builtArray['last_name']    = xmlspecialchars($conRow['lastname']);
        $builtArray['email']        = xmlspecialchars($contactEmail);
        $builtArray['office_phone'] = xmlspecialchars($conRow['phone']);
        $builtArray['mobile_phone'] = xmlspecialchars($conRow['mobile']);
        $builtArray['dates']                  = $datesArray;
        $builtArray['job_status']             = self::$hardcoded['jobStatus'];
        $builtArray['is_OA']                  = self::$hardcoded['isOA'];
        $builtArray['self_haul']              = self::$hardcoded['selfHaul'];
        $builtArray['business_channel']       = self::$hardcoded['businessChannel'];
        $builtArray['move_type']              = $moveType;
        $builtArray['billing_info']           = self::buildBillingInfo($orderId);
        $builtArray['origin_info']            = self::initializeAddress($quoteRow, 'Origin', $ordersRow ?: $oppRow, $conRow);
        $builtArray['dest_info']              = self::initializeAddress($quoteRow, 'Destination', $ordersRow ?: $oppRow, $conRow);
        foreach ($participatingAgents as $type => $agentInfo) {
            //do i need if ($agentInfo)?
            $builtArray[$type] = $agentInfo;
        }
        $builtArray['extra_locations']        = $extraStopsArray;
        $builtArray['weight_override']        = xmlspecialchars(ceil($quoteRow['weight']));
        $builtArray['cube_sheet mode="Road"'] = ($hasCubesheetNode)?['weight_factor' => self::$hardcoded['weightFactor'], 'rooms' => $roomsArray]:[];
        $builtArray['misc_items']             = $miscArray;
        $builtArray['discounts positive="false"'] = [
            'bottom_line'  => xmlspecialchars($quoteRow['local_bl_discount']),
            'sit'          => xmlspecialchars($quoteRow['sit_disc']),
            'crate'        => xmlspecialchars($quoteRow['crating_disc'])
        ];
        $builtArray['operational_list']       = [];
        $builtArray['time_zone']              = $timeZone;
        $builtArray['dynamic_local_data']     = $localDataArray;
        $builtArray['user_terms']             = ['print_terms' => self::$hardcoded['printTerms']];
        $builtArray['notes']                  = '';
        $builtArray['office_info']             = self::initializeOfficeInfo($oppRow, $ownerID, $quoteRow);

        $builtArray['ValuationSummary'] = self::getValuationSummary($quoteRow['valuation_options'], self::$useForReports);
        $builtArray['new_notes'] = self::getComments($estimateId, $quoteRow['potentialid'], self::$useForReports);

        if ($imageFilePath = self::getValidImageFile($ownerID, 'Users')) {
            $builtArray['salesperson_photo'] = self::base64Image($imageFilePath);
        }

        if ($imageFilePath = self::getValidImageFile($oppRow['smownerid'], 'Users')) {
            $builtArray['coordinator_photo'] = self::base64Image($imageFilePath);
        }

        if ($imageFilePath = self::getValidImageFile($agentID, 'AgentManager')) {
            $builtArray['company_logo'] = self::base64Image($imageFilePath);
        }
        $builtArray['agentInfo'] = self::getAgentInformation($agentID);

        if ($oppId) {
            $builtArray['operational_list'] = self::initializeOppList($oppId);
        }

        return ['survey_upload' => $builtArray];
    }

    //OT 16747
    protected static function addOrderAndAccountFields($orderID = false, $accountID = false, $estDesc = '')
    {
        $reportsArray = [];
        $reportsArray['new_notes']['estimates_description'] = $estDesc;
        if ($orderID) {
            try {
                $ordersRecordModel = Vtiger_Record_Model::getInstanceById($orderID);
                $reportsArray['new_notes']['orders_description'] = $ordersRecordModel->get('description');
                $reportsArray['van_line_number'] = $ordersRecordModel->get('orders_vanlineregnum');
                $reportsArray['business_line'] = vtranslate($ordersRecordModel->get('business_line'));
                $reportsArray['order_number'] = $ordersRecordModel->get('orders_no');
                $reportsArray['estimate_type'] = $ordersRecordModel->get('estimate_type');
                $reportsArray['move_roles'] = self::initializeMoveRoles($ordersRecordModel);
                $reportsArray['valuation'] = $ordersRecordModel->get('valuation_deductible');
                $reportsArray['payment'] = self::getPaymentTypes($ordersRecordModel->get('payment_type'));
                $reportsArray['cube'] = $ordersRecordModel->get('orders_ecube');
                $reportsArray['order_tariff'] = self::getLabelfromId($ordersRecordModel->get('tariff_id'));
                $reportsArray['piece_count'] = $ordersRecordModel->get('orders_pcount');
                $reportsArray['trip_id'] =    self::getLabelfromId($ordersRecordModel->get('orders_trip'));

                if(!$accountID) {
                    $accountID = $ordersRecordModel->get('orders_account');
                }
            } catch (Exception $e) {
            }
        }
        if ($accountID) {
            try {
                $accountRecordModel = Vtiger_Record_Model::getInstanceById($accountID);
                $reportsArray['account_name'] = $accountRecordModel->get('accountname');
                $reportsArray['national_account_number'] = $accountRecordModel->get('national_account_number');
            } catch (Exception $e) {
            }
        }
        return $reportsArray;
    }
    //OT 16956
    protected static function addDetailedLineItems($estimateId, $tablePrefix = '')
    {
        $recordModel = Estimates_Record_Model::getInstanceById($estimateId);
        // need to do something better here
        if ($recordModel && method_exists($recordModel, 'getDetailLineItems')) {
            $totalGross= 0;
            $totalInvoiceCostNet= 0;
            $totalDistributableCostNet= 0;
            $listArray      = $recordModel->getDetailLineItems($estimateId, $tablePrefix, true);
            $res = [];
            foreach($listArray as $section => $items)
            {
                $sectionGross = 0;
                $sectionInvoiceCostNet = 0;
                $sectionDistributableCostNet = 0;
                $sectionQuantity = 0;
                foreach($items as $key => $value)
                {
                    $res[strtolower($section)][] = $value;
                    $sectionGross += CurrencyField::convertToDBFormat($value['Gross']);
                    $sectionInvoiceCostNet += CurrencyField::convertToDBFormat($value['InvoiceCostNet']);
                    $sectionDistributableCostNet += CurrencyField::convertToDBFormat($value['DistributableCostNet']);
                    $sectionQuantity += CurrencyField::convertToDBFormat($value['Quantity']);
                }
                $res[strtolower($section)]['SectionName'] = $section;
                $res[strtolower($section)]['Gross'] = $sectionGross;
                $res[strtolower($section)]['InvoiceCostNet'] = $sectionInvoiceCostNet;
                $res[strtolower($section)]['DistributableCostNet'] = $sectionDistributableCostNet;
                $res[strtolower($section)]['Quantity'] = $sectionQuantity;

                $totalGross += $sectionGross;
                $totalInvoiceCostNet += $sectionInvoiceCostNet;
                $totalDistributableCostNet += $sectionDistributableCostNet;
            }
            $res['OverallTotalGross'] = $totalGross;
            $res['OverallTotalInvoiceCostNet'] = $totalInvoiceCostNet;
            $res['OverallTotalDistributableCostNet'] = $totalDistributableCostNet;
            $formattedArray = self::formatArrayForXml($res);
        } else {
            $formattedArray = [];
        }
        return $formattedArray;
    }
    //OT 16956
    protected static function formatArrayForXml($array, $parentLabel = '')
    {
        $formattedArray = [];
        foreach ($array as $label=>$value) {
            $label = preg_replace('/[^a-zA-Z0-9\_]/', $parentLabel.'_', $label);
            $label = preg_replace('/^([0-9])/', $parentLabel.'_$0', $label);
            $label = preg_replace('/([a-z0-9])([A-Z])/', '$1_$2', $label);
            $label = strtolower($label);
            if (is_array($value)) {
                $value = self::formatArrayForXml($value, $label);
            } else {
                $value = xmlspecialchars($value);
            }
            $formattedArray[$label] = $value;
        }
        return $formattedArray;
    }

    protected static function getLabelfromId($passedId)
    {
        $label = '';
        $db  = PearDatabase::getInstance();
        $sql    = "SELECT label FROM `vtiger_crmentity` WHERE crmid = ? LIMIT 1";
        $result = $db->pquery($sql, [$passedId]);
        while ($row =& $result->fetchRow()) {
            $label = $row['label'];
        }
        return $label;
    }

    protected static function getPaymentTypes($fieldValue)
    {
        $type = [];
        $fieldValueArray = explode('|##|', $fieldValue);
        foreach ($fieldValueArray as $index=>$value) {
            $type['type#'.$index] = $value;
        }
        return $type;
    }


    protected static function addGppCustomRates(&$dataArray, $rowData, $tablePrefix = '')
    {
        //Array passed by reference - function modifies original array
        $db  = PearDatabase::getInstance();
        $seq = 1;
        if ($rowData['smf_type'] == false) {
            $itemArray                           = [];
            $itemArray['rate_id']                = 16;
            $itemArray['rate1']                  = xmlspecialchars($rowData['percent_smf']);
            $itemArray['rate2']                  = '0.00';
            $itemArray['flat']                   = '0.00';
            $dataArray['gpp_custom_rates#'.$seq] = $itemArray;
            $seq++;
        } else {
            $itemArray                           = [];
            $itemArray['rate_id']                = 18;
            $itemArray['rate1']                  = xmlspecialchars($rowData['flat_smf']);
            $itemArray['rate2']                  = '0.00';
            $itemArray['flat']                   = '0.00';
            $dataArray['gpp_custom_rates#'.$seq] = $itemArray;
            $seq++;

            $itemArray                           = [];
            $itemArray['rate_id']                = 19;
            $itemArray['rate1']                  = 1;
            $itemArray['rate2']                  = '0.00';
            $itemArray['flat']                   = '0.00';
            $dataArray['gpp_custom_rates#'.$seq] = $itemArray;
            $seq++;
        }
        if ($rowData['tpg_transfactor']) {
            $itemArray                           = [];
            $itemArray['rate_id']                = 15;
            $itemArray['rate1']                  = xmlspecialchars($rowData['tpg_transfactor']);
            $itemArray['rate2']                  = '0.00';
            $itemArray['flat']                   = '0.00';
            $dataArray['gpp_custom_rates#'.$seq] = $itemArray;
            $seq++;
        }
        if ($rowData['apply_custom_sit_rate_override_dest'] == 1) {
            if ($rowData['apply_sit_first_day_dest'] == 1) {
                $destFirstDayRate                    = ($rowData['sit_first_day_dest_override'])?$rowData['sit_first_day_dest_override']:'0.00';
                $itemArray                           = [];
                $itemArray['rate_id']                = 26;
                $itemArray['rate1']                  = xmlspecialchars($destFirstDayRate);
                $itemArray['rate2']                  = '0.00';
                $itemArray['flat']                   = '0.00';
                $dataArray['gpp_custom_rates#'.$seq] = $itemArray;
                $seq++;
            }
            if ($rowData['apply_sit_addl_day_dest'] == 1) {
                $destAddlDayRate                     = ($rowData['sit_addl_day_dest_override'])?$rowData['sit_addl_day_dest_override']:'0.00';
                $itemArray                           = [];
                $itemArray['rate_id']                = 27;
                $itemArray['rate1']                  = xmlspecialchars($destAddlDayRate);
                $itemArray['rate2']                  = '0.00';
                $itemArray['flat']                   = '0.00';
                $dataArray['gpp_custom_rates#'.$seq] = $itemArray;
                $seq++;
            }
            if ($rowData['apply_sit_cartage_dest'] == 1) {
                $destCartRate                        = ($rowData['sit_cartage_dest_override'])?$rowData['sit_cartage_dest_override']:'0.00';
                $itemArray                           = [];
                $itemArray['rate_id']                = 28;
                $itemArray['rate1']                  = xmlspecialchars($destCartRate);
                $itemArray['rate2']                  = '0.00';
                $itemArray['flat']                   = '0.00';
                $dataArray['gpp_custom_rates#'.$seq] = $itemArray;
                $seq++;
            }
        }
        if ($rowData['apply_custom_sit_rate_override'] == 1) {
            if ($rowData['apply_sit_first_day_origin'] == 1) {
                $origFirstDayRate                    = ($rowData['sit_first_day_origin_override'])?$rowData['sit_first_day_origin_override']:'0.00';
                $itemArray                           = [];
                $itemArray['rate_id']                = 11;
                $itemArray['rate1']                  = xmlspecialchars($origFirstDayRate);
                $itemArray['rate2']                  = '0.00';
                $itemArray['flat']                   = '0.00';
                $dataArray['gpp_custom_rates#'.$seq] = $itemArray;
                $seq++;
            }
            if ($rowData['apply_sit_addl_day_origin'] == 1) {
                $origAddlDayRate                     = ($rowData['sit_addl_day_origin_override'])?$rowData['sit_addl_day_origin_override']:'0.00';
                $itemArray                           = [];
                $itemArray['rate_id']                = 12;
                $itemArray['rate1']                  = xmlspecialchars($origAddlDayRate);
                $itemArray['rate2']                  = '0.00';
                $itemArray['flat']                   = '0.00';
                $dataArray['gpp_custom_rates#'.$seq] = $itemArray;
                $seq++;
            }
            if ($rowData['apply_sit_cartage_origin'] == 1) {
                $origCartRate                        = ($rowData['sit_cartage_origin_override'])?$rowData['sit_cartage_origin_override']:'0.00';
                $itemArray                           = [];
                $itemArray['rate_id']                = 17;
                $itemArray['rate1']                  = xmlspecialchars($origCartRate);
                $itemArray['rate2']                  = '0.00';
                $itemArray['flat']                   = '0.00';
                $dataArray['gpp_custom_rates#'.$seq] = $itemArray;
                $seq++;
            }
        }

        if ($rowData['apply_exlabor_rate_origin'] == 1) {
            $exlaborRateOrigin                   = ($rowData['exlabor_rate_origin'])?$rowData['exlabor_rate_origin']:'0.00';
            $exlaborFlatOrigin                   = ($rowData['exlabor_flat_origin'])?$rowData['exlabor_flat_origin']:'0.00';
            $itemArray                           = [];
            $itemArray['rate_id']                = 2;
            $itemArray['rate1']                  = xmlspecialchars($exlaborRateOrigin);
            $itemArray['rate2']                  = '0.00';
            $itemArray['flat']                   = xmlspecialchars($exlaborFlatOrigin);
            $dataArray['gpp_custom_rates#'.$seq] = $itemArray;
            $seq++;
        }
        if ($rowData['apply_exlabor_ot_rate_origin'] == 1) {
            $exlaborOTRateOrigin                 = ($rowData['exlabor_ot_rate_origin'])?$rowData['exlabor_ot_rate_origin']:'0.00';
            $exlaborOTFlatOrigin                 = ($rowData['exlabor_ot_flat_origin'])?$rowData['exlabor_ot_flat_origin']:'0.00';
            $itemArray                           = [];
            $itemArray['rate_id']                = 3;
            $itemArray['rate1']                  = xmlspecialchars($exlaborOTRateOrigin);
            $itemArray['rate2']                  = '0.00';
            $itemArray['flat']                   = xmlspecialchars($exlaborOTFlatOrigin);
            $dataArray['gpp_custom_rates#'.$seq] = $itemArray;
            $seq++;
        }
        if ($rowData['apply_exlabor_rate_dest'] == 1) {
            $exlaborRateDest                     = ($rowData['exlabor_rate_dest'])?$rowData['exlabor_rate_dest']:'0.00';
            $exlaborFlatDest                     = ($rowData['exlabor_flat_dest'])?$rowData['exlabor_flat_dest']:'0.00';
            $itemArray                           = [];
            $itemArray['rate_id']                = 24;
            $itemArray['rate1']                  = xmlspecialchars($exlaborRateDest);
            $itemArray['rate2']                  = '0.00';
            $itemArray['flat']                   = xmlspecialchars($exlaborFlatDest);
            $dataArray['gpp_custom_rates#'.$seq] = $itemArray;
            $seq++;
        }
        if ($rowData['apply_exlabor_ot_rate_dest'] == 1) {
            $exlaborOTRateDest                   = ($rowData['exlabor_ot_rate_dest'])?$rowData['exlabor_ot_rate_dest']:'0.00';
            $exlaborOTFlatDest                   = ($rowData['exlabor_ot_flat_dest'])?$rowData['exlabor_ot_flat_dest']:'0.00';
            $itemArray                           = [];
            $itemArray['rate_id']                = 25;
            $itemArray['rate1']                  = xmlspecialchars($exlaborOTRateDest);
            $itemArray['rate2']                  = '0.00';
            $itemArray['flat']                   = xmlspecialchars($exlaborOTFlatDest);
            $dataArray['gpp_custom_rates#'.$seq] = $itemArray;
            $seq++;
        }
        if ($rowData['apply_full_pack_rate_override'] == 1) {
            $fullPackOverrideRate                = ($rowData['full_pack_rate_override'])?$rowData['full_pack_rate_override']:'0.00';
            $itemArray                           = [];
            $itemArray['rate_id']                = 8;
            $itemArray['rate1']                  = '0.00';
            $itemArray['rate2']                  = '0.00';
            $itemArray['flat']                   = xmlspecialchars($fullPackOverrideRate);
            $dataArray['gpp_custom_rates#'.$seq] = $itemArray;
            $seq++;
        }
        if ($rowData['apply_custom_pack_rate_override'] == 1) {
            $sql    = "SELECT itemid, custom_rate, pack_rate FROM `".$tablePrefix."vtiger_packing_items` WHERE quoteid = ?";
            $result = $db->pquery($sql, [$rowData['quoteid']]);
            while ($row =& $result->fetchRow()) {
                $customOverrideRate                  = ($row['custom_rate'])?$row['custom_rate']:'0.00';
                $packOverrideRate                    = ($row['pack_rate'])?$row['pack_rate']:'0.00';
                $itemArray                           = [];
                $itemArray['rate_id']                = 6;
                $itemArray['sub_id']                 = (getenv('INSTANCE_NAME') == 'sirva' && $row['itemid'] == 102) ? 103 : $row['itemid'];
                $itemArray['rate2']                  = xmlspecialchars($customOverrideRate);
                $itemArray['rate1']                  = xmlspecialchars($packOverrideRate);
                $itemArray['flat']                   = '0.00';
                $dataArray['gpp_custom_rates#'.$seq] = $itemArray;
                $seq++;
            }
        }
        if($rowData['apply_custom_pack_rate_override'] == 1)
        {
            $itemArray                           = [];
            $itemArray['rate_id']                = 7;
            $itemArray['rate1']                  = xmlspecialchars($rowData['tpg_custom_crate_rate']);
            $itemArray['rate2']                  = '0.00';
            $itemArray['flat']                   = '0.00';
            $dataArray['gpp_custom_rates#'.$seq] = $itemArray;
            $seq++;
        }
    }

    public static function initializeOppList($oppId)
    {
        $db        = PearDatabase::getInstance();
        $sql       = "SELECT `vtiger_oplist_answers_sections`.`section_id`,
                      `vtiger_oplist_answers_sections`.`section_order`,
                      `vtiger_oplist_answers_sections`.`section_name`,
                      `vtiger_oplist_answers_questions`.`question_id`,
                      `vtiger_oplist_answers_questions`.`question_order`,
                      `vtiger_oplist_answers_questions`.`question` AS 'question_text',
                      `vtiger_oplist_answers_questions`.`question_type`,
                      `vtiger_oplist_answers_questions`.`default_text_answer`,
                      `vtiger_oplist_answers_questions`.`default_bool_answer`,
                      `vtiger_oplist_answers_questions`.`default_date_answer`,
                      `vtiger_oplist_answers_questions`.`default_datetime_answer`,
                      `vtiger_oplist_answers_questions`.`default_time_answer`,
                      `vtiger_oplist_answers_questions`.`default_int_answer`,
                      `vtiger_oplist_answers_questions`.`default_dec_answer`,
                      `vtiger_oplist_answers_questions`.`text_answer`,
                      `vtiger_oplist_answers_questions`.`bool_answer`,
                      `vtiger_oplist_answers_questions`.`date_answer`,
                      `vtiger_oplist_answers_questions`.`datetime_answer`,
                      `vtiger_oplist_answers_questions`.`time_answer`,
                      `vtiger_oplist_answers_questions`.`int_answer`,
                      `vtiger_oplist_answers_questions`.`dec_answer`,
                      `vtiger_oplist_answers_questions`.`multi_answer_id`
               FROM `vtiger_oplist_answers_questions`
               LEFT JOIN `vtiger_oplist_answers_sections`
                   USING(`section_id`)
               WHERE `vtiger_oplist_answers_questions`.`opp_id` = ? GROUP BY `section_id`,`question_id`";
        //@TODO: This is where we're working.
        $sql       = "SELECT *,`question` AS `question_text` FROM `vtiger_oplist_answers_questions` WHERE `opp_id` = ?";
        $result    = $db->pquery($sql, [$oppId]);
        $questions = [];
        while ($row =& $result->fetchRow()) {
            $sectionInfo = self::retrieveSections($row);
            $section  = 'section section_id="'.$row['section_id'].'" series_id="'.$sectionInfo['section_order'].'"';
            $question = 'question question_id="'.$row['question_id'].'" series_id="'.$row['question_order'].'"';
            if (getenv('INSTANCE_NAME') == 'graebel') {
                $question = 'question question_id="'.$row['question_id'].'"';
//            } elseif (getenv('IGC_MOVEHQ')) {
//                    $question = 'question question_id="'.$row['question_id'].'"';
            }
            $questions[$section]['section_name'] = $questions[$section]['section_name']? :$sectionInfo['section_name'];

            switch ($row['question_type']) {
                case 'Text':
                    $questionType  = 'TEXT';
                    $defaultAnswer = $row['default_text_answer'];
                    $answer        = $row['text_answer'];
                    break;
                case 'Yes/No':
                    $questionType  = 'YESNO';
                    if (getenv('INSTANCE_NAME') == 'graebel') {
                        $defaultAnswer = $row['default_bool_answer']?'Yes':'No';
                        $answer        = $row['bool_answer']?'Yes':'No';
//                    } elseif (getenv('IGC_MOVEHQ')) {
//                            $defaultAnswer = $row['default_bool_answer'] ? 'Yes' : 'No';
//                            $answer = $row['bool_answer'] ? 'Yes' : 'No';
                    } else {
                        $defaultAnswer = $row['default_bool_answer'];
                        $answer = $row['bool_answer'];
                    }
                    break;
                case 'Date':
                    $questionType  = 'DATE';
                    $defaultAnswer = $row['default_date_answer'];
					$answer = DateTimeField::convertToUserFormat($row['date_answer']);
                    //$answer        = $row['date_answer'];
                    break;
                case 'Date and Time':
                    $questionType  = 'DATETIME';
                    $defaultAnswer = $row['default_datetime_answer'];
                    $answer = DateTimeField::convertToUserFormat(explode(' ', $row['datetime_answer'])[0]) . " " . \Vtiger_Time_UIType::getDisplayTimeValue(explode(' ', $row['datetime_answer'])[1]);
                    //$answer        = $row['datetime_answer'];
                    break;
                case 'Time':
                    //@TODO: this may be wrong. if it's empty it sends time now, but not able to verify this idea right now.
                    $questionType  = 'TIME';
                    $defaultAnswer = $row['default_time_answer'];
					$answer = \Vtiger_Time_UIType::getDisplayTimeValue($row['time_answer']);
                    //$answer        = $row['time_answer'];
                    break;
                case 'Quantity':
                    $questionType  = 'QTY';
                    $defaultAnswer = isset($row['default_int_answer']) ? $row['default_int_answer'] : $row['default_dec_answer'];
                    $answer        = isset($row['int_answer']) ? $row['int_answer'] : $row['dec_answer'];
                    break;
                case 'Multiple Choice':
                    $questionType  = 'MULTIPLECHOICE';
                    $defaultAnswer = $row['multi_answer_id'];
                    $answer        = $row['multi_answer_id'];
                    break;
                default:
                    $questionType = 'NONE';
                    break;
            }
            $answers = [];
            $defaultAnswers = [];
            $multipleChoice = [];
            if ($questionType == 'MULTIPLECHOICE') {
                $options = [];
                $sql     = 'SELECT `option_order`, `option_id`, `answer`, `default_selected`, `selected` FROM `vtiger_oplist_answers_multi_option` WHERE `opp_id` = ? AND `section_id` = ? AND `question_id` = ?';
                $result2  = $db->pquery($sql, [$oppId, $row['section_id'], $row['question_id']]);
                while ($row2 =& $result2->fetchRow()) {
                    $option = 'option option_id="'.$row2['option_id'].'" series_id="'.$row2['option_order'].'"';
                    if (getenv('INSTANCE_NAME') == 'graebel') {
                        $option = 'option option_id="'.$row2['option_id'].'" series_id="'.$row['question_order'].'"';
//                    } elseif (getenv('IGC_MOVEHQ')) {
//                        $option = 'option option_id="'.$row2['option_id'].'" series_id="'.$row['question_order'].'"';
                    }
                    if ($row2['default_selected'] == '1') {
                        //$questions[$section]['questions'][$question]['default_answer'] = $row2['option_id'];
                        //$multipleChoice['default_answer'] = $row2['option_id'];
                        $defaultAnswers[] = $row2['option_id'];
                    } else {
                        $defaultAnswers[] = 0;
                    }
                    if ($row2['selected'] == 1) {
                        $answers[] = $row2['option_id'];
                    }
                    $options[$option]['option_name'] = $row2['answer'];
                }
                //$questions[$section]['questions'][$question]['multiple_choice_options'] = $options;
                //$multipleChoice['multiple_choice_options'] = $options;
                $multipleChoice = $options;
                $answer = implode(',', $answers);
                $defaultAnswer = implode(',', $defaultAnswers);
            }
            $questions[$section]['questions'][$question]['question_type']  = $questionType;
            $questions[$section]['questions'][$question]['question_text']  = $row['question_text'];
            $questions[$section]['questions'][$question]['default_answer'] = $defaultAnswer;
            $questions[$section]['questions'][$question]['response']       = $answer;
            if (getenv('INSTANCE_NAME') == 'graebel' && strtolower($questionType) != 'text') {
                $questions[$section]['questions'][$question]['is_limit'] = 'false';
//            } elseif (getenv('IGC_MOVEHQ') && strtolower($questionType) != 'text') {
//                $questions[$section]['questions'][$question]['is_limit'] = 'false';
            }
            if ($questionType == 'MULTIPLECHOICE') {
                $questions[$section]['questions'][$question]['multiple_choice_options'] = $multipleChoice;
            }
        }
        // file_put_contents('logs/devLog.log', "\n HITS INIT OPP LIST ", FILE_APPEND);
        //file_put_contents('logs/devLog.log', "\n Questions : ".print_r($questions, true), FILE_APPEND);
        return $questions;
    }

    protected function retrieveSections($row)
    {
        //@TODO: cache this in self?
        $db = PearDatabase::getInstance();
        $stmt = "SELECT * FROM `vtiger_oplist_answers_sections` WHERE `opp_id` = ? AND `oplist_id` = ? AND `section_id` = ?";
        $result = $db->pquery($stmt, [$row['opp_id'], $row['oplist_id'], $row['section_id']]);
        return $result->fetchRow();
    }

    protected static function initializeAddress($quoteRow, $locationType, $oppRow = [], $conRow = [])
    {
        $location                  = strtolower($locationType);
        $addrArray                 = [];
        $addrArray['first_name']   = xmlspecialchars($conRow['firstname']);
        $addrArray['last_name']    = xmlspecialchars($conRow['lastname']);
        $addrArray['add1']         = xmlspecialchars($quoteRow[$location.'_address1'] ? $quoteRow[$location.'_address1'] : $oppRow[$location.'_address1']);
        $addrArray['add2']         = xmlspecialchars($quoteRow[$location.'_address2'] ? $quoteRow[$location.'_address2'] : $oppRow[$location.'_address2']);
        $addrArray['city']         = xmlspecialchars($quoteRow[$location.'_city'] ? $quoteRow[$location.'_city'] : $oppRow[$location.'_city']);
        $addrArray['state']        = xmlspecialchars($quoteRow[$location.'_state'] ? $quoteRow[$location.'_state'] : $oppRow[$location.'_state']);
        $addrArray['zip']          = xmlspecialchars($quoteRow[$location.'_zip'] ? $quoteRow[$location.'_zip'] : $oppRow[$location.'_zip']);
        $addrArray['country']      = xmlspecialchars($quoteRow[$location.'_country'] ? $quoteRow[$location.'_country'] : $oppRow[$location.'_country']);

        $phone1 = $quoteRow[$location.'_phone1'] ? $quoteRow[$location.'_phone1'] : $oppRow[$location.'_phone1'];
        $phone2 = $quoteRow[$location.'_phone2'] ? $quoteRow[$location.'_phone2'] : $oppRow[$location.'_phone2'];
        $addrArray['home_phone'] = $addrArray['other_phone#1'] = xmlspecialchars(self::formatUSPhone($phone1));
        $addrArray['work_phone'] = $addrArray['other_phone#2'] = xmlspecialchars(self::formatUSPhone($phone2));
        //non-html compatible phone nodes:
        $addrArray['address_type'] = $locationType;

        return $addrArray;
    }

    //@NOTE: non-html reports require the phone numbers to be sent over formatted.
    protected function formatUSPhone($phone) {
        if (!self::$formatUSPhoneNumbers) {
            return $phone;
        }

        if (!$phone) {
            return $phone;
        }

        if (!preg_match('/^[0-9]{7,10}$/',$phone)) {
            return $phone;
        }

        if (strlen($phone) == 7) {
            $phone = preg_replace('/^([0-9]{3})([0-9]{4})$/', '$1-$2', $phone);
        } else if (strlen($phone) == 10) {
            $phone = preg_replace('/^([0-9]{3})([0-9]{3})([0-9]{4})$/', '($1) $2-$3', $phone);
        }
        return $phone;
    }

    /**
     * Function to initialize date information from current Estimate/Actual or fallback to parent Opportunity if dates
     * don't exist on Estimate/Actual record
     *
     * @param array $oppRow - Array containing data from the parent Opportunity
     * @param array $quoteRow - Array containing data from the current Estimate/Actual
     *
     * @return array
     */
    protected static function initializeDates($oppRow, $quoteRow)
    {
        $datesArray                      = [];
        $datesArray['pack_from']         = ($quoteRow['pack_date'])
            ?date('m/d/Y', strtotime($quoteRow['pack_date']))
            :
            (($oppRow['pack_date']) ? date('m/d/Y', strtotime($oppRow['pack_date'])) : null);
        $datesArray['pack_to']           = ($quoteRow['pack_to_date'])
            ?date('m/d/Y', strtotime($quoteRow['pack_to_date']))
            :
            (($oppRow['pack_to']) ? date('m/d/Y', strtotime($oppRow['pack_to'])) : null);

        if (!$datesArray['pack_to']) {
            //I don't know if somewhere they use 'pack_to' for opportunities so i'm adding an if.
            $datesArray['pack_to']  = ($oppRow['pack_to_date'])?date('m/d/Y', strtotime($oppRow['pack_to_date'])):NULL;
        }

        $datesArray['load_from']         = ($quoteRow['load_date'])
            ?date('m/d/Y', strtotime($quoteRow['load_date']))
            :
            (($oppRow['load_date'])?date('m/d/Y', strtotime($oppRow['load_date'])):null);
        //estimates require a load_to date to set the pricing level
        //$datesArray['load_to']           = ($oppRow['load_to_date'])?date('m/d/Y', strtotime($oppRow['load_to_date'])):NULL;
        $datesArray['load_to']         = ($quoteRow['load_to_date'])
            ?date('m/d/Y', strtotime($quoteRow['load_to_date']))
            :
            (($oppRow['load_to_date'])?date('m/d/Y', strtotime($oppRow['load_to_date'])):null);

        $datesArray['deliver_from']      = ($quoteRow['deliver_date'])
            ?date('m/d/Y', strtotime($quoteRow['deliver_date']))
            :
            (($oppRow['deliver_date'])?date('m/d/Y', strtotime($oppRow['deliver_date'])):null);
        $datesArray['deliver_to']        = ($quoteRow['deliver_to_date'])
            ?date('m/d/Y', strtotime($quoteRow['deliver_to_date']))
            :
            (($oppRow['deliver_to_date'])?date('m/d/Y', strtotime($oppRow['deliver_to_date'])):null);
        $datesArray['survey']            = ($quoteRow['survey_date'])
            ?date('m/d/Y', strtotime($quoteRow['survey_date']))
            :
            (($oppRow['survey_date'])?date('m/d/Y', strtotime($oppRow['survey_date'])):null);
        $datesArray['follow_up']         = ($quoteRow['followup_date'])
            ?date('m/d/Y', strtotime($quoteRow['followup_date']))
            :
            (($oppRow['followup_date'])?date('m/d/Y', strtotime($oppRow['followup_date'])):null);
        $datesArray['decision']          = ($quoteRow['decision_date'])
            ?date('m/d/Y', strtotime($quoteRow['decision_date']))
            :
            (($oppRow['decision_date'])?date('m/d/Y', strtotime($oppRow['decision_date'])):null);
        $datesArray['pack_requested']    = ($quoteRow['preferred_ppdate'])
            ?date('m/d/Y', strtotime($quoteRow['preferred_ppdate']))
            :
            (($oppRow['preferred_ppdate'])?date('m/d/Y', strtotime($oppRow['preferred_ppdate'])):null);
        $datesArray['load_requested']    = ($quoteRow['preferred_pldate'])
            ?date('m/d/Y', strtotime($quoteRow['preferred_pldate']))
            :
            (($oppRow['preferred_pldate'])?date('m/d/Y', strtotime($oppRow['preferred_pldate'])):null);
        $datesArray['deliver_requested'] = ($quoteRow['preferred_pddate'])
            ?date('m/d/Y', strtotime($quoteRow['preferred_pddate']))
            :
            (($oppRow['preferred_pddate'])?date('m/d/Y', strtotime($oppRow['preferred_pddate'])):null);

        return $datesArray;
    }

    //intitializeDates is incredebly rigid
    //was easier just to make a new method for orders' dates
    protected static function initializeOrderDates($orderInfo)
    {
        $db = PearDatabase::getInstance();
        $datesArray                      = [];
        $datesArray['pack_from']         = $orderInfo['orders_pdate'] ? date('m/d/Y', strtotime($orderInfo['orders_pdate'])) : null;
        $datesArray['pack_to']           = $orderInfo['orders_ptdate'] ? date('m/d/Y', strtotime($orderInfo['orders_ptdate'])) : null;
        $datesArray['load_from']         = $orderInfo['orders_ldate'] ? date('m/d/Y', strtotime($orderInfo['orders_ldate'])) : null;
        $datesArray['load_to']           = $orderInfo['orders_ltdate'] ? date('m/d/Y', strtotime($orderInfo['orders_ltdate'])) : null;
        $datesArray['deliver_from']      = $orderInfo['orders_ddate'] ? date('m/d/Y', strtotime($orderInfo['orders_ddate'])) : null;
        $datesArray['deliver_to']        = $orderInfo['orders_dtdate'] ? date('m/d/Y', strtotime($orderInfo['orders_dtdate'])) : null;
        $datesArray['survey']            = $orderInfo['orders_surveyd'] ? date('m/d/Y', strtotime($orderInfo['orders_surveyd'])) : null;
        $datesArray['pack_requested']    = $orderInfo['orders_ppdate'] ? date('m/d/Y', strtotime($orderInfo['orders_ppdate'])) : null;
        $datesArray['load_requested']    = $orderInfo['orders_pldate'] ? date('m/d/Y', strtotime($orderInfo['orders_pldate'])) : null;
        $datesArray['deliver_requested'] = $orderInfo['orders_pddate'] ? date('m/d/Y', strtotime($orderInfo['orders_pddate'])) : null;
        $datesArray['received_date']     = $orderInfo['received_date'] ? date('m/d/Y', strtotime($orderInfo['received_date'])) : null;
        $datesArray['registered_on']     = $orderInfo['registered_on'] ? date('m/d/Y', strtotime($orderInfo['registered_on'])) : null;

        if ($orderInfo['orders_opportunities']) {
            //the followupdate in vtiger_potential appears to not be the one used.
            //$sql = "SELECT vtiger_potential.followupdate, vtiger_potentialscf.decision_date FROM `vtiger_potential` join vtiger_potentialscf on (vtiger_potential.potentialid = vtiger_potentialscf.potentialid) WHERE potentialid = ?";
            $sql = "SELECT followup_date, decision_date FROM `vtiger_potentialscf` WHERE potentialid = ?";
            $oppInfo = $db->pquery($sql, [$orderInfo['orders_opportunities']]);
            //OOOHHH my god, it needs to be fetched... the world is falling apart.
            $row     = $oppInfo->fetchRow();
            $datesArray['follow_up'] = $row['followup_date'] ? date('m/d/Y', strtotime($row['followup_date'])) : null;
            $datesArray['decision']  = $row['decision_date'] ? date('m/d/Y', strtotime($row['decision_date'])) : null;
        }
        return $datesArray;
    }

    protected static function initializeInterstateBulkies($recordId, $tablePrefix = '')
    {
        $bulkyArray = [];
        $bulkies    = Estimates_Record_Model::getBulkyItemsStatic($recordId, $tablePrefix);
        $seq        = 1;
        $bulkyItems = [];
        foreach ($bulkies as $itemId => $bulkyItem) {
            if (intval($bulkyItem['qty']) == 0) {
                continue;
            }
            $itemArray                 = [];
            $itemArray['article_name'] = xmlspecialchars($bulkyItem['label'], ENT_XML1, 'UTF-8', false);
            $itemArray['cube']         = 0;
            $itemArray['shipping']     = xmlspecialchars($bulkyItem['qty']);
            $itemArray['itemID']       = xmlspecialchars((getenv('INSTANCE_NAME') == 'sirva' && $itemId == 102) ? 103 : $itemId);
            $itemArray['item_attribs'] = ['crate' => 'false', 'has_weight' => 'false', 'bulky' => 'true', 'carton' => 'false', 'carton_cp' => 'false', 'carton_pbo' => 'false'];
            $bulkyItems['item#'.$seq]  = $itemArray;
            $seq++;
        }
        $bulkyArray['items'] = $bulkyItems;

        return $bulkyArray;
    }

    protected static function initializeInterstatePacking($recordId, $tablePrefix = '')
    {
        $packingArray = [];
        $packies      = Estimates_Record_Model::getPackingItemsStatic($recordId, $tablePrefix);
        $seq          = 1;
        $packingItems = [];
        foreach ($packies as $itemId => $packingItem) {
            if ((int)$packingItem['pack'] == 0 && (int)$packingItem['containers'] == 0
                && (int)$packingItem['unpack'] == 0) {
                continue;
            }
            $itemArray                 = [];
            $itemArray['article_name'] = xmlspecialchars($packingItem['label'], ENT_XML1, 'UTF-8', false);
            $itemArray['cube']         = xmlspecialchars($packingItem['cube']);
            if (getenv('INSTANCE_NAME')=='graebel' || getenv('INSTANCE_NAME')=='sirva') {
            	$itemArray['shipping']     = xmlspecialchars($packingItem['containers']);
			} else {
				$itemArray['shipping']     = xmlspecialchars($packingItem['pack']);
			}
            $itemArray['materials']     = xmlspecialchars($packingItem['containers']);
            $itemArray['pack']         = xmlspecialchars($packingItem['pack']);
            $itemArray['unpack'] = xmlspecialchars($packingItem['unpack']);
            $itemArray['itemID']        = xmlspecialchars((getenv('INSTANCE_NAME') == 'sirva' && $itemId == 102) ? 103 : $itemId);
            $itemArray['item_attribs']  = ['crate' => 'false', 'has_weight' => 'false', 'bulky' => 'false', 'carton' => 'true', 'carton_cp' => 'true', 'carton_pbo' => 'false'];
            $packingItems['item#'.$seq] = $itemArray;
            $seq++;
        }
        $packingArray['items'] = $packingItems;

        return $packingArray;
    }

    protected static function initializeInterstateCrates($recordId, $tablePrefix = '')
    {
        $cratingArray = [];
        $craties      = Estimates_Record_Model::getCratesStatic($recordId, $tablePrefix);
        $seq          = 1;
        $cratingItems = [];
        foreach ($craties as $cratingItem) {
            if ((int)$cratingItem->pack == 0
                && (int)$cratingItem->otpack == 0) {
                continue;
            }
            $itemArray                  = [];
            $itemArray['article_name']  = xmlspecialchars($cratingItem->description . ' (' . $cratingItem->crateid . ')', ENT_XML1, 'UTF-8', false);
            $itemArray['cube']          = xmlspecialchars($cratingItem->cube);
            $itemArray['shipping']      = xmlspecialchars($cratingItem->pack?:$cratingItem->otpack);
            $itemArray['pack']          = xmlspecialchars($cratingItem->pack);
            $itemArray['unpack']        = xmlspecialchars($cratingItem->unpack);
            $itemArray['apply_tariff']          = xmlspecialchars($cratingItem->apply_tariff);
            $itemArray['custom_rate_amount_pack']    = xmlspecialchars($cratingItem->custom_rate_amount);
            $itemArray['custom_rate_amount_unpack']    = xmlspecialchars($cratingItem->custom_rate_amount_unpack);
            if (isset($cratingItem->discount)) {
                $itemArray['discount'] = $cratingItem->discount > 0 ? 'true' : 'false';
            }
            $itemArray['item_attribs']  = ['crate' => 'true', 'has_weight' => 'false', 'bulky' => 'false', 'carton' => 'false', 'carton_cp' => 'false', 'carton_pbo' => 'false'];
            $itemArray['dimensions']    =
                ['length' => xmlspecialchars($cratingItem->crateLength), 'width' => xmlspecialchars($cratingItem->crateWidth), 'height' => xmlspecialchars($cratingItem->crateHeight)];
            if(getenv('INSTANCE_NAME') == 'graebel')
            {
                $itemArray['dimensions']['inches_added'] = '0';
            }
            $cratingItems['item#'.$seq] = $itemArray;
            $seq++;
        }
        $cratingArray['items'] = $cratingItems;
        return $cratingArray;
    }

    protected static function initializeMisc($recordId, $tablePrefix = '', $local = false)
    {
        $miscArray = [];

        // OT 3376
        //if (!$local) {
            //Interstate Misc Items
            $miscCharges = Estimates_Record_Model::getMiscChargesStatic($recordId, $tablePrefix);
            $seq         = 1;
            foreach ($miscCharges as $type => $charges) {
                foreach ($charges as $chargeItem) {
					// this function is only used by rating and reports, so we can skip items not included for rating
                    if (getenv('INSTANCE_NAME') == 'graebel' && !$chargeItem->included) {
                        continue;
                    }
                    $itemArray                = [];
                    $itemArray['description'] = xmlspecialchars($chargeItem->description);
                    $itemArray['discounted']  = $chargeItem->discounted == '1'?'1':'0';
                    $itemArray['charge']      = xmlspecialchars($chargeItem->charge);
                    $itemArray['type']        = xmlspecialchars($type);
                    $itemArray['qty']         = xmlspecialchars($chargeItem->qty);
                    $miscArray['item#'.$seq]  = $itemArray;
                    $seq++;
                }
            }
        //} else {
            //Local Misc Items
        //}

        return $miscArray;
    }

    protected static function initializeContract($contractRow, $contractModel)
    {
        $contractArray = [];
        if (count($contractRow) > 0) {
            $contractArray['number']                  = xmlspecialchars($contractRow['contract_no']);
            $contractArray['begin_date']              = xmlspecialchars($contractRow['begin_date']);
            $contractArray['end_date']                = xmlspecialchars($contractRow['end_date']);
            $contractArray['billing_info']            = [];
            $contractArray['billing_info']['add1']    = xmlspecialchars($contractRow['billing_address1']);
            $contractArray['billing_info']['add2']    = xmlspecialchars($contractRow['billing_address2']);
            $contractArray['billing_info']['city']    = xmlspecialchars($contractRow['billing_city']);
            $contractArray['billing_info']['state']   = xmlspecialchars($contractRow['billing_state']);
            $contractArray['billing_info']['zip']     = xmlspecialchars($contractRow['billing_zip']);
            $contractArray['billing_info']['country'] = xmlspecialchars($contractRow['billing_country']);
            $contractArray['fuel_type']               = xmlspecialchars($contractRow['fuel_surcharge_type']);
            if ($contractRow['fuel_surcharge_type'] == 'Static Fuel Percentage') {
                //$contractArray['fuel_surcharge_pct'] = self::$hardcoded['fuel_surcharge_pct'];//xmlspecialchars($contractRow['fuel_charge']);
                //should just be the percentage they entered.
                $contractArray['fuel_surcharge_pct'] = xmlspecialchars($contractRow['fuel_charge']);
            } else {
                $table = $contractModel->getFuelLookupTable();
                $res = [];
                $i = 0;
                foreach($table as $key => $value)
                {
                    $res['fuel_row#'.$i] = $value;
                    $i++;
                }
                $contractArray['fuel_lookup_table'] = $res;
            }
            $contractArray['fuel_discount']     = xmlspecialchars($contractRow['fuel_disc']);
            $contractArray['waive_eac']     = xmlspecialchars($contractRow['waive_eac_rates']) ? 'true' : 'false';
            $contractArray['extended_sit_miles']     = xmlspecialchars($contractRow['extended_sit_mileage']);
            $ratesArray                         = $contractModel->getAnnualRateIncreases();
            $rateRows                           = [];
            $seq                                = 1;
            foreach ($ratesArray as $rate) {
                $rateRow                   = [];
                $rateRow['effective_date'] = $rate['date'];
                $rateRow['rate_increase']  = $rate['rate'];
                $rateRows['row#'.$seq]     = $rateRow;
                $seq++;
            }
            $contractArray['annual_rate_increases'] = $rateRows;

            // valuation
            $copy = [
                'min_val_per_lb'   => 'min_val_per_lb',
                'free_val'         => 'free_frv',
                'free_val_amount'  => 'free_frv_amount',
            ];
            if ($contractRow['valuation_deductible']) {
                $contractArray['valuation_type'] = ValuationUtils::MapValuationDeductible($contractRow['valuation_deductible']);
                $copy = array_merge($copy, [
                    'maximum_rvp'      => 'maximum_rvp',
                    'rvp_flat_fee'     => 'rvp_flat_fee',
                    'rvp_per_1000'     => 'rvp_per_1000',
                    'rvp_per_1000_sit' => 'rvp_per_1000_sit',
                ]);
            }
            foreach ($copy as $key => $rowKey) {
                $contractArray[$key] = xmlspecialchars($contractRow[$rowKey]);
            }
        }

        return $contractArray;
    }

    protected static function initializeInterstateAccessorials($quoteRow, $tablePrefix='', $customTariffType)
    {

        //Initialize origin accessorials array
        $accOrigin                     = [];
        $accOrigin['extra_lab_hrs']    = xmlspecialchars($quoteRow['acc_exlabor_origin_hours']);
        $accOrigin['extra_lab_OT_hrs'] = xmlspecialchars($quoteRow['acc_exlabor_ot_origin_hours']);
        $accOrigin['wait_hrs']         = xmlspecialchars($quoteRow['acc_wait_origin_hours']);
        $accOrigin['wait_OT_hrs']      = xmlspecialchars($quoteRow['acc_wait_ot_origin_hours']);
        $accOrigin['gsa500_supervisory_hours']         = xmlspecialchars($quoteRow['gsa500_supervisory_hours_origin_regular']);
        $accOrigin['gsa500_supervisory_hours_ot']      = xmlspecialchars($quoteRow['gsa500_supervisory_hours_origin_ot']);
        if ($quoteRow['acc_shuttle_origin_applied']) {
            $accOrigin['shuttle_weight'] = xmlspecialchars($quoteRow['acc_shuttle_origin_weight']);
            $accOrigin['shuttle_miles']  = xmlspecialchars($quoteRow['acc_shuttle_origin_miles']);
            $accOrigin['shuttle_ot'] = $quoteRow['acc_shuttle_origin_ot'] == '1' ? 'true' : 'false';
            if(getenv('RATING_SHUTTLE_APPLY_STOPOFF'))
            {
                $accOrigin['shuttle_apply_stopoff'] = 'true';
            } else {
                $accOrigin['shuttle_apply_stopoff'] = 'false';
            }
        }

        $accOrigin['ot_pack']         = xmlspecialchars($quoteRow['overtime_pack']);
        if ($quoteRow['accesorial_ot_packing'] && InputUtils::CheckboxToBool($quoteRow['accesorial_ot_packing'])) {
            $accOrigin['ot_pack'] = 'true';
        }

        $accOrigin['ot_loadunload']   = xmlspecialchars(self::$hardcoded['otLoadUnloadOrigin']);
        if ($quoteRow['accesorial_ot_loading'] && InputUtils::CheckboxToBool($quoteRow['accesorial_ot_loading'])) {
            $accOrigin['ot_loadunload'] = 'true';
        }

        $accOrigin['day_certain']     = xmlspecialchars(self::$hardcoded['dayCertainOrigin']);
        $accOrigin['express_loading'] = xmlspecialchars(self::$expressLoadingOrigin);
        $accOrigin['express_truckload'] = $quoteRow['express_truckload']?'true':'false';

        if (getenv('INSTANCE_NAME') == 'sirva') {
            $accOrigin['day_certain']      = $quoteRow['acc_day_certain_pickup'] ? 'true' : 'false';
            $accOrigin['day_certain_flat'] = xmlspecialchars($quoteRow['acc_day_certain_fee']);

            $allowedTariffs = ['Allied Express', 'Blue Express'];
            if(in_array($customTariffType, $allowedTariffs)) {
                $accOrigin['express_pickup_type'] = xmlspecialchars($quoteRow['express_pickup_type']);
                $accOrigin['express_pickup_rate'] = xmlspecialchars($quoteRow['express_pickup_rate']);
            }
        }

        //Initialize destination accessorials array
        $accDest                     = [];
        $accDest['extra_lab_hrs']    = xmlspecialchars($quoteRow['acc_exlabor_dest_hours']);
        $accDest['extra_lab_OT_hrs'] = xmlspecialchars($quoteRow['acc_exlabor_ot_dest_hours']);
        $accDest['wait_hrs']         = xmlspecialchars($quoteRow['acc_wait_dest_hours']);
        $accDest['wait_OT_hrs']      = xmlspecialchars($quoteRow['acc_wait_ot_dest_hours']);
        $accDest['gsa500_supervisory_hours']         = xmlspecialchars($quoteRow['gsa500_supervisory_hours_dest_regular']);
        $accDest['gsa500_supervisory_hours_ot']      = xmlspecialchars($quoteRow['gsa500_supervisory_hours_dest_ot']);
        foreach (['hours_per_van', 'hours_first_man', 'additional_men', 'hours_per_additional_man'] as $field) {
            $accDest[$field] = xmlspecialchars($quoteRow[$field]);
        }
        if ($quoteRow['acc_shuttle_dest_applied']) {
            $accDest['shuttle_weight'] = xmlspecialchars($quoteRow['acc_shuttle_dest_weight']);
            $accDest['shuttle_miles']  = xmlspecialchars($quoteRow['acc_shuttle_dest_miles']);
            $accDest['shuttle_ot'] = $quoteRow['acc_shuttle_dest_ot'] == '1' ? 'true' : 'false';
            if(getenv('RATING_SHUTTLE_APPLY_STOPOFF'))
            {
                $accDest['shuttle_apply_stopoff'] = 'true';
            } else {
                $accDest['shuttle_apply_stopoff'] = 'false';
            }
        }

        $accDest['ot_pack']         = xmlspecialchars($quoteRow['overtime_unpack']);
        if ($quoteRow['accesorial_ot_unpacking'] && InputUtils::CheckboxToBool($quoteRow['accesorial_ot_unpacking'])) {
            $accDest['ot_pack'] = 'true';
        }

        $accDest['ot_loadunload']   = xmlspecialchars(self::$hardcoded['otLoadUnloadDest']);
        if ($quoteRow['accesorial_ot_unloading'] && InputUtils::CheckboxToBool($quoteRow['accesorial_ot_unloading'])) {
            $accDest['ot_loadunload'] = 'true';
        }
        if(getenv('INSTANCE_NAME') != 'sirva') {
            $accDest['ot_loadunload_weight'] = xmlspecialchars($quoteRow['acc_ot_dest_weight']);

            $accDest['wait_hrs']         = xmlspecialchars($quoteRow['acc_wait_dest_hours']);
        }

        $accDest['day_certain']     = xmlspecialchars(self::$hardcoded['dayCertainDest']);
        $accDest['express_loading'] = xmlspecialchars(self::$expressLoadingDest);
        $accDest['express_truckload'] = $quoteRow['express_truckload']?'true':'false';
        //OT 16261 - Adding debris fields but not sure if the field names are appropriate
        //$accDest['acc_debris_reg'] = InputUtils::CheckboxToBool($quoteRow['acc_debris_reg'])?'true':'false';
        //$accDest['acc_debris_ot'] = InputUtils::CheckboxToBool($quoteRow['acc_debris_ot'])?'true':'false';
        //$accDest['acc_debris_dod'] = InputUtils::CheckboxToBool($quoteRow['acc_debris_dod'])?'true':'false';
        //THIS IS RIGHT
        $accDest['acc_debris_reg'] = InputUtils::CheckboxToBool($quoteRow['acc_debris_reg'])?1:0;
        $accDest['acc_debris_ot'] = InputUtils::CheckboxToBool($quoteRow['acc_debris_ot'])?1:0;
        $accDest['acc_debris_dod'] = InputUtils::CheckboxToBool($quoteRow['acc_debris_dod'])?1:0;

        if (getenv('INSTANCE_NAME') == 'graebel' || getenv('INSTANCE_NAME') == 'sirva') {
            //Retrieve and populate interstate service charges
            $db = PearDatabase::getInstance();
            //ORIGIN
            $originCharges = [];
            $sql = "SELECT * FROM `".$tablePrefix."vtiger_quotes_inter_servchg` WHERE quoteid=? AND is_dest=0";
            $result = $db->pquery($sql, [$quoteRow['quoteid']]);
            $chargesSeq = 0;
            while ($row = $result->fetchRow()) {
                $originCharge = [];
                $originCharge['applied'] = $row['applied'] == 1 ? 'true' : 'false';
                $originCharge['service_id'] = $row['serviceid'];
                $originCharge['weight'] = $row['service_weight'];

                $originCharges['service_charge#'.$chargesSeq] = $originCharge;
                $chargesSeq++;
            }

            $accOrigin['service_charges'] = $originCharges;
            //DESTINATION
            $destCharges = [];
            $sql = "SELECT * FROM `".$tablePrefix."vtiger_quotes_inter_servchg` WHERE serviceid!=0 AND quoteid=? AND is_dest=1";
            $result = $db->pquery($sql, [$quoteRow['quoteid']]);
            $chargesSeq = 0;
            while ($row = $result->fetchRow()) {
                $destCharge = [];
                $destCharge['applied'] = $row['applied'] == 1 ? 'true' : 'false';
                $destCharge['service_id'] = $row['serviceid'];
                $destCharge['weight'] = $row['service_weight'];

                $destCharges['service_charge#'.$chargesSeq] = $destCharge;
                $chargesSeq++;
            }

            $accDest['service_charges'] = $destCharges;
        }

        // Send proper flag for Express Truckload
        if($customTariffType == 'Truckload Express') {
            $accOrigin['express_truckload'] = 'true';
            $accDest['express_truckload'] = 'true';
        }

        return ['accessorial orig_dest="Origin"' => $accOrigin, 'accessorial orig_dest="Destination"' => $accDest];
    }

    protected static function initializeInterstateSIT($quoteRow)
    {
        //Initialize origin SIT array
        $sitOrigin                    = [];
        //@NOTE: OK having talked to Matt, and testing this, it appears days is the total days INCLUSIVE.
        $sitOrigin['days']            = xmlspecialchars(max(0, $quoteRow['sit_origin_number_days'])) ?: 0;
        $sitOrigin['sit_in']          = $quoteRow['sit_origin_date_in']?xmlspecialchars(date('m/d/Y', strtotime($quoteRow['sit_origin_date_in']))):date('m/d/Y');
        $sitOrigin['sit_pudel']       = $quoteRow['sit_origin_pickup_date']?xmlspecialchars(date('m/d/Y', strtotime($quoteRow['sit_origin_pickup_date']))):date('m/d/Y', strtotime($sitOrigin['sit_in'] . ' + ' . $sitOrigin['days'] . ' days'));

        //fuel surcharges removed from sending out. NOTE we do want this
        $sitOrigin['sit_fs_pct']      = xmlspecialchars($quoteRow['accesorial_fuel_surcharge']);

        $sitOrigin['air_conditioned'] = xmlspecialchars(self::$hardcoded['sitAcOrigin']);

        $sitOrigin['weight']            = xmlspecialchars($quoteRow['sit_origin_weight']);
        if (getenv('INSTANCE_NAME') == 'graebel') {
            $sitOrigin['calculate_miles'] = 'true';
        }
        $sitOrigin['miles']            = xmlspecialchars($quoteRow['sit_origin_miles']);
        $sitOrigin['sit_zip']            = xmlspecialchars($quoteRow['sit_origin_zip']);
        $sitOrigin['sit_ot']     = $quoteRow['sit_origin_overtime'] == '1'?'true':'false';

        //Initialize destination SIT array
        $sitDest                    = [];
        //@NOTE: OK having talked to Matt, and testing this, it appears days is the total days INCLUSIVE.
        $sitDest['days']            = xmlspecialchars(max(0, $quoteRow['sit_dest_number_days'])) ?: 0;
        $sitDest['sit_in']          = $quoteRow['sit_dest_date_in']?xmlspecialchars(date('m/d/Y', strtotime($quoteRow['sit_dest_date_in']))):date('m/d/Y');
        $sitDest['sit_pudel']       = $quoteRow['sit_dest_delivery_date']?xmlspecialchars(date('m/d/Y', strtotime($quoteRow['sit_dest_delivery_date']))):date('m/d/Y', strtotime($sitDest['sit_in'] . ' + ' . $sitDest['days'] . ' days'));

        //fuel surcharges removed from sending out. NOTE we do want this
        $sitDest['sit_fs_pct']      = xmlspecialchars($quoteRow['accesorial_fuel_surcharge']);

        $sitDest['air_conditioned'] = xmlspecialchars(self::$hardcoded['sitAcDest']);

        $sitDest['weight']            = xmlspecialchars($quoteRow['sit_dest_weight']);
        if (getenv('INSTANCE_NAME') == 'graebel') {
            $sitDest['calculate_miles'] = 'true';
        }
        $sitDest['miles']            = xmlspecialchars($quoteRow['sit_dest_miles']);
        $sitDest['sit_zip']            = xmlspecialchars($quoteRow['sit_dest_zip']);
        $sitDest['sit_ot']       = $quoteRow['sit_dest_overtime'] == '1'?'true':'false';
        return ['sit orig_dest="Origin"' => $sitOrigin, 'sit orig_dest="Destination"' => $sitDest];
    }

    protected static function initializeCubesheetNode($recordId, &$hasCubesheetNode, $local = false, $tablePrefix = '', &$extraLocations = null)
    {
//        file_put_contents('logs/xml.log', date('Y-m-d H:i:s - ').print_r(func_get_args(), true)."\n", FILE_APPEND);
        if (!$local) {
//            file_put_contents('logs/xml.log', date('Y-m-d H:i:s - ')."Inside interstate block of initializeCubesheetNode\n", FILE_APPEND);
            //Initialize bulky array
            $bulkyArray = self::initializeInterstateBulkies($recordId, $tablePrefix);
            //Initialize packing array
            $packingArray = self::initializeInterstatePacking($recordId, $tablePrefix);
            //Initialize crating array
            $cratingArray = self::initializeInterstateCrates($recordId, $tablePrefix);
        } else {
            //Initialize bulky array
            $bulkyArray = self::initializeLocalBulkies($recordId, $tablePrefix);
            //Initialize packing array
            $packingArray = self::initializeLocalPacking($recordId, $tablePrefix);
            //Initialize crating array
            $cratingArray = self::initializeLocalCrates($recordId, $tablePrefix);
        }

        // pull packing data from extra locations into $packingArray
        if ($extraLocations) {
            $currentCount = count($packingArray['items']) + 1;
            foreach ($extraLocations as $key => $location) {
                foreach ($location['items'] as $item) {
                    // merge into existing items when possible
                    // this could be slow, but I don't know how else to do it since they're not indexed by id
                    foreach ($packingArray['items'] as $itemNo => &$itemValues) {
                        if ($itemValues['itemID'] == $item['itemID']) {
                            $itemValues['shipping'] = (int)$itemValues['shipping'] + (int)$item['shipping'];
                            $itemValues['materials'] = (int)$itemValues['materials'] + (int)$item['materials'];
                            $itemValues['pack'] = (int)$itemValues['pack'] + (int)$item['pack'];
                            $itemValues['unpack'] = (int)$itemValues['unpack'] + (int)$item['unpack'];
                            continue 2;
                        }
                    }
                    $packingArray['items']['item#' . $currentCount++] = $item;
                }
                unset($extraLocations[$key]['items']);
            }
        }

        //Initialize control booleans
        $hasBulkyRoom     = count($bulkyArray['items']) > 0;
        $hasPackingRoom   = count($packingArray['items']) > 0;
        $hasCratingRoom   = count($cratingArray['items']) > 0;
        $hasCubesheetNode = $hasBulkyRoom || $hasPackingRoom || $hasCratingRoom;
        //$hasMiscNode = count($miscArray) > 0;
        //Initialize rooms array
        $roomsArray                            = [];
        $roomsArray['room name="Bulky Items"'] = ($hasBulkyRoom)?$bulkyArray:'';
        $roomsArray['room name="Carton"']      = ($hasPackingRoom)?$packingArray:'';
        $roomsArray['room name="Crates"']      = ($hasCratingRoom)?$cratingArray:'';

        return $roomsArray;
    }

    protected static function initializeLocalBulkies($recordId, $tablePrefix = '')
    {
        return [];
    }

    protected static function initializeLocalPacking($recordId, $tablePrefix = '')
    {
        return [];
    }

    protected static function initializeLocalCrates($recordId, $tablePrefix = '')
    {
        return [];
    }

    protected static function initializeLocalTariffNode($quoteRow, $tablePrefix = '')
    {
        $tariffNode = [];
        $tariffId   = $quoteRow['effective_tariff'];
        $db         = PearDatabase::getInstance();
        $sql        = "SELECT effectivedatesid FROM `vtiger_effectivedates`
                        JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_effectivedates.effectivedatesid
                        WHERE vtiger_crmentity.deleted = 0 AND effective_date <= ? AND related_tariff = ?
                        ORDER BY `vtiger_effectivedates`.`effective_date` DESC LIMIT 1";
        $result     = $db->pquery($sql, [$quoteRow['effective_date'], $tariffId]);
        $effectiveDateId = $result->fetchRow()[0];
        $tariffModel                  = Tariffs_Record_Model::getInstanceById($tariffId);
        $tariffNode['tariff_name']    = xmlspecialchars($tariffModel->get('tariff_name'));
        $tariffNode['tariff_state']   = xmlspecialchars($tariffModel->get('tariff_state'));
        $tariffNode['effective_date'] = xmlspecialchars(date('m/d/Y', strtotime($quoteRow['effective_date'])));
        $services                     = $tariffModel->getServiceIds($effectiveDateId);
        $sectionsArray                = [];
        $sectionSeq                   = 1;
        foreach ($services as $sectionId => $serviceIdArray) {
            $sectionModel                    = TariffSections_Record_Model::getInstanceById($sectionId);
            $sectionArray                    = [];
            $sectionArray['section_name']    = xmlspecialchars($sectionModel->get('section_name'));
            //if true then the section can be discounted
            $sectionArray['is_discountable'] = $sectionModel->get('is_discountable') == '0'?'false':'true';
            //if true then the section can have a different discount than the bottom line.
            $sectionArray['bottomline_discount_override'] = $sectionModel->get('bottomline_discount_override') == '0'?'false':'true';
            $sectionArray['section_id']      = xmlspecialchars($sectionId);
            $servicesArray                   = [];
            $serviceSeq                      = 1;
            foreach ($serviceIdArray as $serviceId) {
                $serviceModel                  = TariffServices_Record_Model::getInstanceById($serviceId);
                $serviceArray                  = [];
                $serviceArray['service_name']  = xmlspecialchars($serviceModel->get('service_name'));
                $serviceArray['rate_type']     = xmlspecialchars($serviceModel->get('rate_type'));
                $serviceArray['applicability'] = xmlspecialchars($serviceModel->get('applicability'));
                $serviceArray['is_required']   = $serviceModel->get('is_required') == '0'?'false':'true';
                //If the tariffservices_discountable is true then the service itself is discountable.
                $serviceArray['tariffservices_discountable']   = $serviceModel->get('tariffservices_discountable') == '0'?'false':'true';
                // default to false if null
                $serviceArray['invoiceable']   = $serviceModel->get('invoiceable') == '1'?'true':'false';
                $serviceArray['distributable']   = $serviceModel->get('distributable') == '1'?'true':'false';
                $serviceArray['service_id']    = xmlspecialchars($serviceId);
                switch ($serviceModel->get('rate_type')) {
                    case 'Base Plus Trans.':
                        $sql         = "SELECT * FROM `vtiger_tariffbaseplus` WHERE serviceid=?";
                        $result      = $db->pquery($sql, [$serviceId]);
                        $seq         = 1;
                        $chargeArray = [];
                        while ($row =& $result->fetchRow()) {
                            $chargeItem                = [];
                            $chargeItem['from_miles']  = $row['from_miles'];
                            $chargeItem['to_miles']    = $row['to_miles'];
                            $chargeItem['from_weight'] = $row['from_weight'];
                            $chargeItem['to_weight']   = $row['to_weight'];
                            $chargeItem['base_rate']   = $row['base_rate'];
                            $chargeItem['excess']      = $row['excess'];
                            $chargeArray['row#'.$seq]  = $chargeItem;
                            $seq++;
                        }
                        $serviceArray['table'] = $chargeArray;
                        break;
                    case 'Break Point Trans.':
                        $sql         = "SELECT * FROM `vtiger_tariffbreakpoint` WHERE serviceid=?";
                        $result      = $db->pquery($sql, [$serviceId]);
                        $seq         = 1;
                        $chargeArray = [];
                        while ($row =& $result->fetchRow()) {
                            $chargeItem                = [];
                            $chargeItem['from_miles']  = $row['from_miles'];
                            $chargeItem['to_miles']    = $row['to_miles'];
                            $chargeItem['from_weight'] = $row['from_weight'];
                            $chargeItem['to_weight']   = $row['to_weight'];
                            $chargeItem['break_point'] = $row['break_point'];
                            $chargeItem['base_rate']   = $row['base_rate'];
                            $chargeArray['row#'.$seq]  = $chargeItem;
                            $seq++;
                        }
                        $serviceArray['table'] = $chargeArray;
                        break;
                    case 'Weight/Mileage Trans.':
                        $sql         = "SELECT * FROM `vtiger_tariffweightmileage` WHERE serviceid=?";
                        $result      = $db->pquery($sql, [$serviceId]);
                        $seq         = 1;
                        $chargeArray = [];
                        while ($row =& $result->fetchRow()) {
                            $chargeItem                = [];
                            $chargeItem['from_miles']  = $row['from_miles'];
                            $chargeItem['to_miles']    = $row['to_miles'];
                            $chargeItem['from_weight'] = $row['from_weight'];
                            $chargeItem['to_weight']   = $row['to_weight'];
                            $chargeItem['base_rate']   = $row['base_rate'];
                            $chargeArray['row#'.$seq]  = $chargeItem;
                            $seq++;
                        }
                        $serviceArray['table'] = $chargeArray;
                        break;
                    case 'Bulky List':
                        //@NOTE: Hardcoding 'charge_per' to quantity due to lack of support for the 'Hourly' type.
                        // This fixes problems with the Hourly Bulky List type not rating, it will now rate like Quantity.
                        // $serviceArray['charge_per'] = xmlspecialchars($serviceModel->get('bulky_chargeper'));
                        $serviceArray['charge_per'] = 'Quantity';
                        $sql                        = "SELECT * FROM `vtiger_tariffbulky` WHERE serviceid=?";
                        $result                     = $db->pquery($sql, [$serviceId]);
                        $seq                        = 1;
                        while ($row =& $result->fetchRow()) {
                            $bulkyItem                   = [];
                            $bulkyItem['description']    = xmlspecialchars($row['description']);
                            $bulkyItem['weight']         = xmlspecialchars($row['weight']);
                            $bulkyItem['rate']           = xmlspecialchars($row['rate']);
                            $bulkyItem['line_item_id']   = xmlspecialchars($row['line_item_id']);
                            $serviceArray['bulky#'.$seq] = $bulkyItem;
                            $seq++;
                        }
                        break;
                    case 'Charge Per $100 (Valuation)':
                        $serviceArray['has_released']    = xmlspecialchars($serviceModel->get('valuation_released'));
                        $serviceArray['released_amount'] = xmlspecialchars($serviceModel->get('valuation_releasedamount'));
                        $sql         = "SELECT * FROM `vtiger_tariffchargeperhundred` WHERE serviceid=?";
                        $result      = $db->pquery($sql, [$serviceId]);
                        $seq         = 1;
                        $chargeArray = [];
                        while ($row =& $result->fetchRow()) {
                            if(!isset($serviceArray['multiplier'])) {
                                $serviceArray['multiplier'] = xmlspecialchars($row['multiplier']);
                            }
                            $chargeItem                 = [];
                            $chargeItem['deductible']   = xmlspecialchars($row['deductible']);
                            $chargeItem['rate']         = xmlspecialchars($row['rate']);
                            $chargeItem['line_item_id'] = xmlspecialchars($row['line_item_id']);
                            $chargeArray['row#'.$seq]   = $chargeItem;
                            $seq++;
                        }
                        $serviceArray['table'] = $chargeArray;
                        break;
                    case 'County Charge':
                        $sql         = "SELECT * FROM `vtiger_tariffcountycharge` WHERE serviceid=?";
                        $result      = $db->pquery($sql, [$serviceId]);
                        $seq         = 1;
                        $chargeArray = [];
                        while ($row =& $result->fetchRow()) {
                            $chargeItem                 = [];
                            $chargeItem['county_name']  = xmlspecialchars($row['name']);
                            $chargeItem['rate']         = xmlspecialchars($row['rate']);
                            $chargeItem['line_item_id'] = xmlspecialchars($row['line_item_id']);
                            $chargeArray['row#'.$seq]   = $chargeItem;
                            $seq++;
                        }
                        $serviceArray['table'] = $chargeArray;
                        break;
                    case 'Crating Item':
                        $serviceArray['inches_added']   = xmlspecialchars($serviceModel->get('crate_inches'));
                        $serviceArray['min_crate_cube'] = xmlspecialchars($serviceModel->get('crate_mincube'));
                        $serviceArray['crate_rate']     = xmlspecialchars($serviceModel->get('crate_packrate'));
                        $serviceArray['uncrate_rate']   = xmlspecialchars($serviceModel->get('crate_unpackrate'));
                        break;
                    case 'Flat Charge':
                        $serviceArray['rate'] = xmlspecialchars($serviceModel->get('flat_rate'));
                        break;
                    case 'Hourly Set':
                        $serviceArray['has_van']      = xmlspecialchars($serviceModel->get('hourlyset_hasvan'));
                        $serviceArray['has_travel']   = xmlspecialchars($serviceModel->get('hourlyset_hastravel'));
                        $serviceArray['add_man_rate'] = xmlspecialchars($serviceModel->get('hourlyset_addmanrate'));
                        $serviceArray['add_van_rate'] = xmlspecialchars($serviceModel->get('hourlyset_addvanrate'));
                        $sql                          = "SELECT * FROM `vtiger_tariffhourlyset` WHERE serviceid=?";
                        $result                       = $db->pquery($sql, [$serviceId]);
                        $seq                          = 1;
                        $chargeArray                  = [];
                        while ($row =& $result->fetchRow) {
                            $chargeItem                 = [];
                            $chargeItem['men']          = xmlspecialchars($row['men']);
                            $chargeItem['vans']         = xmlspecialchars($row['vans']);
                            $chargeItem['rate']         = xmlspecialchars($row['rate']);
                            $chargeItem['line_item_id'] = xmlspecialchars($row['line_item_id']);
                            $chargeArray['row#'.$seq]   = $chargeItem;
                            $seq++;
                        }
                        $serviceArray['table'] = $chargeArray;
                        break;
                    case 'Storage Valuation':
                    case 'Service Base Charge':
                        $serviceArray['service_charge'] = $serviceModel->getServiceCharges($quoteRow['quoteid'], $tablePrefix)['rate'];
                        if($serviceArray['service_charge'] > 0 || !$serviceModel->get('service_base_charge_matrix')) {
                            $serviceArray['service_charge_type'] = self::$hardcoded['service_charge_type_flat'];
                            if ($serviceArray['service_charge'] > 0) {
                                $serviceArray['service_charge'] = xmlspecialchars($serviceArray['service_charge']);
                            } else {
                                $serviceArray['service_charge'] = xmlspecialchars($serviceModel->get('service_base_charge'));
                            }
                        }
                        else {
                            //If the serivce uses the tabled matrix ignore the rate saved for the estimate
                            $serviceBaseChargeMatrixArray = $serviceModel->getServiceBaseChargeMatrix();
                            if ($serviceBaseChargeMatrixArray > 0) {
                                $serviceBaseChargeMatrix = [];
                                $seq                     = 0;
                                foreach ($serviceBaseChargeMatrixArray as $temp) {
                                    $serviceArray['row#'.$seq++] = [
                                        'from'    => xmlspecialchars($temp['price_from']),
                                        'to'      => xmlspecialchars($temp['price_to']),
                                        'percent' => xmlspecialchars($temp['factor']),
                                    ];
                                }
                                $serviceArray['service_charge_type'] = self::$hardcoded['service_charge_type_doe'];
                            }
                        }
                        $serviceArray['service_charge_applies'] = xmlspecialchars(str_ireplace(' |##| ', ',', $serviceModel->get('service_base_charge_applies')));
                        break;
                    case 'Hourly Simple':
                        $serviceArray['rate'] = xmlspecialchars($serviceModel->get('hourlysimple_rate'));
                        break;
                    case 'Packing Items':
                        $serviceArray['has_containers'] = xmlspecialchars($serviceModel->get('packing_containers'));
                        $serviceArray['has_packing']    = xmlspecialchars($serviceModel->get('packing_haspacking'));
                        $serviceArray['has_unpacking']  = xmlspecialchars($serviceModel->get('packing_hasunpacking'));
                        $serviceArray['sales_tax']      = xmlspecialchars($serviceModel->get('packing_salestax'));
                        $sql                            = "SELECT * FROM `vtiger_tariffpackingitems` WHERE serviceid=?";
                        $result                         = $db->pquery($sql, [$serviceId]);
                        $seq                            = 1;
                        $packingArray                   = [];
                        while ($row =& $result->fetchRow()) {
                            $packingItem                   = [];
                            $packingItem['name']           = xmlspecialchars($row['name']);
                            $packingItem['container_rate'] = xmlspecialchars($row['container_rate']);
                            $packingItem['packing_rate']   = xmlspecialchars($row['packing_rate']);
                            $packingItem['unpacking_rate'] = xmlspecialchars($row['unpacking_rate']);
                            $packingItem['line_item_id']   = xmlspecialchars($row['line_item_id']);
                            $packingArray['row#'.$seq]     = $packingItem;
                            $seq++;
                        }
                        $serviceArray['table'] = $packingArray;
                        break;
                    case 'Per Cu Ft':
                        $serviceArray['rate'] = xmlspecialchars($serviceModel->get('cuft_rate'));
                        break;
                    case 'Per Cu Ft/Per Day':
                        $serviceArray['rate'] = xmlspecialchars($serviceModel->get('cuftperday_rate'));
                        break;
                    case 'Per Cu Ft/Per Month':
                        $serviceArray['rate'] = xmlspecialchars($serviceModel->get('cuftpermonth_rate'));
                        break;
                    case 'Per CWT':
                        $serviceArray['rate'] = xmlspecialchars($serviceModel->get('cwt_rate'));
                        break;
                    case 'Per CWT/Per Day':
                        $serviceArray['rate'] = xmlspecialchars($serviceModel->get('cwtperday_rate'));
                        break;
                    case 'Per CWT/Per Month':
                        $serviceArray['rate'] = xmlspecialchars($serviceModel->get('cwtpermonth_rate'));
                        break;
                    case 'Per Quantity':
                        $serviceArray['rate'] = xmlspecialchars($serviceModel->get('qty_rate'));
                        break;
                    case 'Per Quantity/Per Day':
                        $serviceArray['rate'] = xmlspecialchars($serviceModel->get('qtyperday_rate'));
                        break;
                    case 'Per Quantity/Per Month':
                        $serviceArray['rate'] = xmlspecialchars($serviceModel->get('qtypermonth_rate'));
                        break;
                    case 'Tabled Valuation':
                        $serviceArray['has_released']    = xmlspecialchars($serviceModel->get('valuation_released'));
                        $serviceArray['released_amount'] = xmlspecialchars($serviceModel->get('valuation_releasedamount'));
                        $sql                             = "SELECT * FROM `vtiger_tariffvaluations` WHERE serviceid=?";
                        $result                          = $db->pquery($sql, [$serviceId]);
                        $seq                             = 1;
                        $valuationArray                  = [];
                        while ($row =& $result->fetchRow()) {
                            $valuationItem                 = [];
                            $valuationItem['amount']       = xmlspecialchars($row['amount']);
                            $valuationItem['deductible']   = xmlspecialchars($row['deductible']);
                            $valuationItem['cost']         = xmlspecialchars($row['cost']);
                            $valuationItem['line_item_id'] = xmlspecialchars($row['line_item_id']);
                            $valuationArray['row#'.$seq]   = $valuationItem;
                            $seq++;
                        }
                        $serviceArray['table'] = $valuationArray;
                        break;
                    case 'CWT Per Quantity':
                        $sql    = "SELECT cwtperqty_rate as rate FROM `vtiger_tariffservices` WHERE tariffservicesid=?";
                        $result = $db->pquery($sql, [$serviceId]);
                        $row    = $result->fetchRow();
                        if ($row == null) {
                            continue;
                        }
                        $serviceArray['rate']        = htmlspecialchars($row['rate']);
                        break;
                    case 'Flat Rate By Weight':
                      if($tablePrefix == '') {
                        $sql = "SELECT * FROM `vtiger_quotes_flatratebyweight` WHERE serviceid = ? AND estimateid = ?";
                        $result = $db->pquery($sql,[$serviceId,$quoteRow['quoteid']]);
                        $result = $result->fetchRow();
                        // Weights can be one of two keys, set them in their respective If statments
                        $serviceArray['weight']       = $result['weight'];
                        $serviceArray['weight_cap']   = $result['weight_cap'];
                      } else {
                        $sql = "SELECT * FROM `vtiger_tariffflatratebyweight` WHERE serviceid = ? AND ? BETWEEN `from_weight` AND `to_weight`";
                        $result = $db->pquery($sql,[$serviceId,$quoteRow['local_weight']]);
                        // This is a check to see if it falls outside the range of set tiers
                        // so that it will use the highest tier available
                        if($db->num_rows($result) == 0) {
                          $sql = "SELECT * FROM `vtiger_tariffflatratebyweight` WHERE serviceid = ? ORDER BY to_weight DESC limit 1";
                          $result = $db->pquery($sql,[$serviceId]);
                        }
                        $result = $result->fetchRow();
                        $serviceArray['weight']       = $quoteRow['local_weight'];
                        $serviceArray['weight_cap']   = $result['to_weight'];
                      }
                      // Set the two variables that will always have the same keys
                      $serviceArray['rate']         = $result['rate'];
                      $serviceArray['cwt_overflow'] = $result['cwt_rate'];
                      break;
                    default:
                        break;
                }
                $servicesArray['service#'.$serviceSeq] = $serviceArray;
                $serviceSeq++;
            }
            $sectionArray['services']              = $servicesArray;
            $sectionsArray['section#'.$sectionSeq] = $sectionArray;
            $sectionSeq++;
        }
        $tariffNode['sections'] = $sectionsArray;
        $reportSections         = [];
        $sql                    = "SELECT * FROM `vtiger_tariffreportsections` WHERE tariff_orders_tariff=? AND tariff_orders_type='".$quoteRow['estimate_type']."'";
        $result                 = $db->pquery($sql, [$tariffId]);
        $seq                    = 1;
        while ($row =& $result->fetchRow()) {
            $repSection                             = [];
            $repSection['type']                     = $row['tariff_orders_type'];
            $repSection['title']                    = $row['tariff_orders_title'];
            $repSection['description']              = $row['tariff_orders_description'];
            $repSection['body']                     = $row['tariff_orders_body'];
            $reportSections['report_section#'.$seq] = $repSection;
            $seq++;
        }
        $tariffNode['report_sections'] = $reportSections;
        // file_put_contents('logs/xml.log', date('Y-m-d H:i:s - ')."Effective Date: ".$quoteRow['effective_date'].", ".$effectiveDateId."\n", FILE_APPEND);
        // file_put_contents('logs/xml.log', date('Y-m-d H:i:s - ')."Local Tariff Services: ".print_r($services, true)."\n", FILE_APPEND);
        return $tariffNode;
    }

    protected static function initializeLocalEstimateNode($quoteRow, $tablePrefix = '')
    {
        $estimateNode = [];
        $tariffId     = $quoteRow['effective_tariff'];
        $db           = PearDatabase::getInstance();
        if ($quoteRow['total'] == '') {
            return [];
        }
        $estimateNode['cost_total'] = $quoteRow['total'];
        $estimateNode['bottom_line_discount'] = xmlspecialchars($quoteRow['local_bl_discount']);
        $estimateNode['net_weight']    = $quoteRow['local_weight'];
        $estimateNode['billed_weight'] = $quoteRow['local_billed_weight'];
        $sql = "SELECT effectivedatesid FROM `vtiger_effectivedates`
                            JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_effectivedates.effectivedatesid
                            WHERE vtiger_crmentity.deleted = 0 AND effective_date <= ? AND related_tariff = ?
                            ORDER BY `vtiger_effectivedates`.`effective_date` DESC LIMIT 1";
        $result = $db->pquery($sql, [$quoteRow['effective_date'], $tariffId]);
        $effectiveDateId = $result->fetchRow()[0];
        $tariffModel = Tariffs_Record_Model::getInstanceById($tariffId);
        $services    = $tariffModel->getServiceIds($effectiveDateId);
        //file_put_contents('logs/devLog.log', "\n Services : ".print_r($services, true), FILE_APPEND);
//        file_put_contents('logs/xml.log', date('Y-m-d H:i:s - ')."Estimate Node Services: ".print_r($services, true)."\n", FILE_APPEND);
        $sectionsArray = [];
        $sectionSeq    = 1;
        foreach ($services as $sectionId => $serviceIds) {
            //file_put_contents('logs/devLog.log', "\n In here", FILE_APPEND);
            //file_put_contents('logs/devLog.log', "\n SectionId : ".print_r($sectionId, true), FILE_APPEND);
            //file_put_contents('logs/devLog.log', "\n ServiceIds : ".print_r($serviceIds, true), FILE_APPEND);
//            file_put_contents('logs/xml.log', date('Y-m-d H:i:s - ')."Processing \$sectionId=$sectionId\n", FILE_APPEND);
            //Process section info
            $sql           = "SELECT listprice, section_name
                              FROM `".$tablePrefix."vtiger_inventoryproductrel`
                              JOIN `vtiger_service` ON `".$tablePrefix."vtiger_inventoryproductrel`.`productid`=`vtiger_service`.`serviceid`
                              JOIN `vtiger_tariffsections` ON `vtiger_tariffsections`.`section_name`=`vtiger_service`.`servicename`
                              WHERE `".$tablePrefix."vtiger_inventoryproductrel`.`id`=? AND tariffsectionsid=?";
            //file_put_contents('logs/devLog.log', "\n SQL : ".print_r($sql, true));
            //@TODO: this doesn't seem to pull in the section_name?  who knows why!

            //file_put_contents('logs/devLog.log', "\n Params : ".print_r([$quoteRow['quoteid'], $sectionId], true), FILE_APPEND);
            $sectionResult = $db->pquery($sql, [$quoteRow['quoteid'], $sectionId]);
            $row           = $sectionResult->fetchRow();
            if ($row == null) {
//                file_put_contents('logs/xml.log', date('Y-m-d H:i:s - ')."Preparing to make continue call\n", FILE_APPEND);
                //continue;
            }

            $discSql                           = "SELECT discount_percent FROM `".$tablePrefix."vtiger_quotes_sectiondiscount` WHERE estimateid=? AND sectionid=?";
            //$discResult                        = $db->pquery($sql, [$quoteRow['quoteid'], $sectionId]);
            $discResult                        = $db->pquery($discSql, [$quoteRow['quoteid'], $sectionId]);
            $discRow                           = $discResult->fetchRow();
            $sectionItem                       = [];
            $sectionItem['cost_section_total'] = xmlspecialchars($row['listprice']);
            $sectionItem['section_name']       = xmlspecialchars($row['section_name']);
            $sectionItem['section_id']         = xmlspecialchars($sectionId);
            $sectionItem['section_discount']   = $discRow?xmlspecialchars($discRow['discount_percent']):0;
            $servicesArray                     = [];
            $serviceSeq                        = 1;
            foreach ($serviceIds as $serviceId) {
//                file_put_contents('logs/xml.log', date('Y-m-d H:i:s - ')."Processing \$serviceId=$serviceId\n", FILE_APPEND);
                //Process service info
                $select = "SELECT cost_service_total, cost_container_total, cost_packing_total, cost_unpacking_total, cost_crating_total, cost_uncrating_total, service_name, rate_type ";
                if (getenv('INSTANCE_NAME') == 'graebel') {
                    $select .= ', invoiceable, distributable ';
                } elseif (getenv('IGC_MOVEHQ')) {
                    $select .= ', invoiceable, distributable ';
                }
                $sql           = $select . "
                        FROM `".$tablePrefix."vtiger_quotes_servicecost`
                        JOIN `vtiger_tariffservices` ON `vtiger_tariffservices`.`tariffservicesid`=`".$tablePrefix."vtiger_quotes_servicecost`.`serviceid`
                        WHERE estimateid=? AND serviceid=?";
                $serviceResult = $db->pquery($sql, [$quoteRow['quoteid'], $serviceId]);
                $serviceRow    = $serviceResult->fetchRow();
                if ($serviceRow == null) {
//                    file_put_contents('logs/xml.log', date('Y-m-d H:i:s - ')."No match found\n", FILE_APPEND);
                    continue;
                }

                $serviceItem                         = [];
                $serviceItem['cost_service_total']   = xmlspecialchars($serviceRow['cost_service_total']);
                $serviceItem['cost_container_total'] = xmlspecialchars($serviceRow['cost_container_total']);
                $serviceItem['cost_packing_total']   = xmlspecialchars($serviceRow['cost_packing_total']);
                $serviceItem['cost_unpacking_total'] = xmlspecialchars($serviceRow['cost_unpacking_total']);
                $serviceItem['cost_crating_total']   = xmlspecialchars($serviceRow['cost_crating_total']);
                $serviceItem['cost_uncrating_total'] = xmlspecialchars($serviceRow['cost_uncrating_total']);
                $serviceItem['service_name']         = xmlspecialchars($serviceRow['service_name']);
                $serviceItem['rate_type']            = xmlspecialchars($serviceRow['rate_type']);
                $serviceItem['invoiceable']          = xmlspecialchars($serviceRow['invoiceable']);
                $serviceItem['distributable']        = xmlspecialchars($serviceRow['distributable']);
                $serviceItem['service_id']           = xmlspecialchars($serviceId);
                switch ($serviceRow['rate_type']) {
                    case 'Base Plus Trans.':
                        $sql    = "SELECT * FROM `".$tablePrefix."vtiger_quotes_baseplus` WHERE estimateid=? AND serviceid=?";
                        $result = $db->pquery($sql, [$quoteRow['quoteid'], $serviceId]);
                        $row    = $result->fetchRow();
                        if ($row == null) {
//                            file_put_contents('logs/xml.log', date('Y-m-d H:i:s - ')."Base Plus continue hit\n", FILE_APPEND);
                            continue;
                        }
                        $serviceItem['miles']  = xmlspecialchars($row['mileage']);
                        $serviceItem['weight'] = xmlspecialchars($row['weight']);
                        $serviceItem['rate']   = xmlspecialchars($row['rate']);
                        $serviceItem['excess'] = xmlspecialchars($row['excess']);
                        break;
                    case 'Break Point Trans.':
                        $sql    = "SELECT * FROM `".$tablePrefix."vtiger_quotes_breakpoint` WHERE estimateid=? AND serviceid=?";
                        $result = $db->pquery($sql, [$quoteRow['quoteid'], $serviceId]);
                        $row    = $result->fetchRow();
                        if ($row == null) {
                            continue;
                        }
                        $breakpointLookup = \TariffServices_LocalCalcWeightLookup_Action::lookup($row['mileage'], $row['weight'], $row['rate'], $serviceId);
                        $serviceItem['miles']      = xmlspecialchars($row['mileage']);
                        $serviceItem['weight']     = $breakpointLookup['calcWeight'] > $row['weight'] ? xmlspecialchars($breakpointLookup['calcWeight']) : xmlspecialchars($row['weight']);
                        $serviceItem['rate']       = $breakpointLookup['calcWeight'] > $row['weight'] ? xmlspecialchars($breakpointLookup['rate']) : xmlspecialchars($row['rate']);
                        $serviceItem['calcweight'] = xmlspecialchars($row['breakpoint']);
                        break;
                    case 'Weight/Mileage Trans.':
                        $sql    = "SELECT * FROM `".$tablePrefix."vtiger_quotes_weightmileage` WHERE estimateid=? AND serviceid=?";
                        $result = $db->pquery($sql, [$quoteRow['quoteid'], $serviceId]);
                        $row    = $result->fetchRow();
                        if ($row == null) {
                            continue;
                        }
                        $serviceItem['miles']  = xmlspecialchars($row['mileage']);
                        $serviceItem['weight'] = xmlspecialchars($row['weight']);
                        $serviceItem['rate']   = xmlspecialchars($row['rate']);
                        break;
                    case 'Bulky List':
                        $sql        = "SELECT * FROM `".$tablePrefix."vtiger_quotes_bulky` WHERE estimateid=? AND serviceid=?";
                        $result     = $db->pquery($sql, [$quoteRow['quoteid'], $serviceId]);
                        $bulkyArray = [];
                        $seq        = 1;
                        while ($row =& $result->fetchRow()) {
                            $bulkyItem                    = [];
                            $bulkyItem['description']     = xmlspecialchars($row['description']);
                            $bulkyItem['qty']             = xmlspecialchars($row['qty']);
                            $bulkyItem['weight_add']      = xmlspecialchars($row['weight']);
                            $bulkyItem['rate']            = xmlspecialchars($row['rate']);
                            $bulkyItem['cost_bulky_item'] = xmlspecialchars($row['cost_bulky_item']);
                            $bulkyItem['line_item_id']    = xmlspecialchars($row['bulky_id']);
                            $bulkyArray['bulky#'.$seq]    = $bulkyItem;
                            $seq++;
                        }
                        $serviceItem['bulky_items'] = $bulkyArray;
                        break;
                    case 'Charge Per $100 (Valuation)':
                        $sql    = "SELECT qty1 AS amount, qty2 AS deductible, rate, flag FROM `".$tablePrefix."vtiger_quotes_perunit` WHERE estimateid=? AND serviceid=?";
                        $result = $db->pquery($sql, [$quoteRow['quoteid'], $serviceId]);
                        $row    = $result->fetchRow();
                        if ($row == null) {
                            continue;
                        }
                        if($row['flag'] < 2) {
                            $valuationType = abs($row['flag']-1);
                            $serviceItem['valuationtype'] = xmlspecialchars($valuationType);
                            $serviceItem['amount']   = xmlspecialchars($row['amount']);
                            if($valuationType == 1) {
                                $serviceItem['deductible'] = xmlspecialchars($row['deductible']);
                                $serviceItem['rate']       = xmlspecialchars($row['rate']);
                            }else {
                                $serviceItem['coverage'] = xmlspecialchars($row['rate']);
                            }
                        }

                        break;
                    case 'County Charge':
                        $sql    = "SELECT county, rate FROM `".$tablePrefix."vtiger_quotes_countycharge` WHERE estimateid=? AND serviceid=?";
                        $result = $db->pquery($sql, [$quoteRow['quoteid'], $serviceId]);
                        $row    = $result->fetchRow();
                        if ($row == null) {
                            continue;
                        }
                        $serviceItem['county'] = xmlspecialchars($row['county']);
                        $serviceItem['rate']   = xmlspecialchars($row['rate']);
                        break;
                    case 'Crating Item':
                        $sql        =
                            "SELECT crateid, description, crating_qty AS cratingqty, crating_rate AS cratingrate, uncrating_qty AS uncratingqty, uncrating_rate AS uncratingrate, length, width, height, inches_added AS inchesadded, line_item_id AS id, cost_crating, cost_uncrating FROM `".
                            $tablePrefix.
                            "vtiger_quotes_crating` WHERE estimateid=? AND serviceid=?";
                        $result     = $db->pquery($sql, [$quoteRow['quoteid'], $serviceId]);
                        $crateArray = [];
                        $seq        = 1;
                        while ($row =& $result->fetchRow()) {
                            $crateItem                   = [];
                            $crateItem['ID']             = xmlspecialchars($row['crateid']);
                            $crateItem['line_item_id']   = xmlspecialchars($row['id']);
                            $crateItem['description']    = xmlspecialchars($row['description']);
                            $crateItem['length']         = xmlspecialchars($row['length']);
                            $crateItem['width']          = xmlspecialchars($row['width']);
                            $crateItem['height']         = xmlspecialchars($row['height']);
                            $crateItem['inches_added']   = xmlspecialchars($row['inchesadded']);
                            $crateItem['crating_qty']    = xmlspecialchars($row['cratingqty']);
                            $crateItem['crating_rate']   = xmlspecialchars($row['cratingrate']);
                            $crateItem['uncrating_qty']  = xmlspecialchars($row['uncratingqty']);
                            $crateItem['uncrating_rate'] = xmlspecialchars($row['uncratingrate']);
                            $crateItem['cost_crating']   = xmlspecialchars($row['cost_crating']);
                            $crateItem['cost_uncrating'] = $row['cost_uncrating'];
                            $crateArray['crate#'.$seq]   = $crateItem;
                            $seq++;
                        }
                        $serviceItem['crates'] = $crateArray;
                        break;
                    case 'Flat Charge':
                        $sql    = "SELECT rate, rate_included FROM `".$tablePrefix."vtiger_quotes_perunit` WHERE estimateid=? AND serviceid=?";
                        $result = $db->pquery($sql, [$quoteRow['quoteid'], $serviceId]);
                        $row    = $result->fetchRow();
                        if ($row == null) {
                            continue;
                        }
                        if ($row['rate_included'] == 0) {
                            $serviceItem['rate'] = 0;
                        } else {
                            $serviceItem['rate'] = xmlspecialchars($row['rate']);
                        }
                        break;
                    case 'Hourly Set':
                        $sql    = "SELECT men, hours, vans, traveltime, rate FROM `".$tablePrefix."vtiger_quotes_hourlyset` WHERE estimateid=? AND serviceid=?";
                        $result = $db->pquery($sql, [$quoteRow['quoteid'], $serviceId]);
                        $row    = $result->fetchRow();
                        if ($row == null) {
                            continue;
                        }
                        $serviceItem['men']         = xmlspecialchars($row['men']);
                        $serviceItem['hours']       = xmlspecialchars($row['hours']);
                        $serviceItem['vans']        = xmlspecialchars($row['vans']);
                        $serviceItem['travel_time'] = xmlspecialchars($row['traveltime']);
                        $serviceItem['rate']        = xmlspecialchars($row['rate']);
                        break;
                    case 'Hourly Simple':
                        $sql    = "SELECT qty1 AS quantity, qty2 AS hours, rate FROM `".$tablePrefix."vtiger_quotes_perunit` WHERE estimateid=? AND serviceid=?";
                        $result = $db->pquery($sql, [$quoteRow['quoteid'], $serviceId]);
                        $row    = $result->fetchRow();
                        if ($row == null) {
                            continue;
                        }
                        $serviceItem['quantity'] = xmlspecialchars($row['quantity']);
                        $serviceItem['hours']    = xmlspecialchars($row['hours']);
                        $serviceItem['rate']     = xmlspecialchars($row['rate']);
                        break;
                    case 'Packing Items':
                        $sql          =
                            "SELECT sales_tax, name, packing_id AS packid, container_qty AS containerqty, container_rate AS containerrate, pack_qty AS packqty, pack_rate AS packrate, unpack_qty AS unpackqty, unpack_rate AS unpackrate, cost_container, cost_packing, cost_unpacking FROM `".
                            $tablePrefix.
                            "vtiger_quotes_packing` WHERE estimateid=? AND serviceid=?";
                        $result       = $db->pquery($sql, [$quoteRow['quoteid'], $serviceId]);
                        $packingArray = [];
                        $seq          = 1;
                        while ($row =& $result->fetchRow()) {
                            $serviceItem['sales_tax'] = xmlspecialchars($row['sales_tax']);

                            $packingItem                        = [];
                            $packingItem['name']                = xmlspecialchars($row['name']);
                            $packingItem['container_qty']       = xmlspecialchars($row['containerqty']);
                            $packingItem['container_rate']      = xmlspecialchars($row['containerrate']);
                            $packingItem['pack_qty']            = xmlspecialchars($row['packqty']);
                            $packingItem['pack_rate']           = xmlspecialchars($row['packrate']);
                            $packingItem['unpack_qty']          = xmlspecialchars($row['unpackqty']);
                            $packingItem['unpack_rate']         = xmlspecialchars($row['unpackrate']);
                            $packingItem['line_item_id']        = xmlspecialchars($row['packid']);
                            $packingItem['cost_container']      = xmlspecialchars($row['cost_container']);
                            $packingItem['cost_packing']        = xmlspecialchars($row['cost_packing']);
                            $packingItem['cost_unpacking']      = xmlspecialchars($row['cost_unpacking']);
                            $packingArray['packing_item#'.$seq] = $packingItem;
                            $seq++;
                        }
                        $serviceItem['packing_items'] = $packingArray;
                        break;
                    case 'Per Cu Ft':
                        $sql    = "SELECT qty1 AS cubicfeet, rate FROM `".$tablePrefix."vtiger_quotes_perunit` WHERE estimateid=? AND serviceid=?";
                        $result = $db->pquery($sql, [$quoteRow['quoteid'], $serviceId]);
                        $row    = $result->fetchRow();
                        if ($row == null) {
                            continue;
                        }
                        $serviceItem['cubic_feet'] = xmlspecialchars($row['cubicfeet']);
                        $serviceItem['rate']   = xmlspecialchars($row['rate']);
                        break;
                    case 'Per Cu Ft/Per Day':
                        $sql    = "SELECT qty1 AS cubicfeet, qty2 AS days, rate FROM `".$tablePrefix."vtiger_quotes_perunit` WHERE estimateid=? AND serviceid=?";
                        $result = $db->pquery($sql, [$quoteRow['quoteid'], $serviceId]);
                        $row    = $result->fetchRow();
                        if ($row == null) {
                            continue;
                        }
                        $serviceItem['cubic_feet'] = $row['cubicfeet'];
                        $serviceItem['days']       = xmlspecialchars((int)$row['days']);
                        $serviceItem['rate']       = xmlspecialchars($row['rate']);
                        break;
                    case 'Per Cu Ft/Per Month':
                        $sql    = "SELECT qty1 AS cubicfeet, qty2 AS months, rate FROM `".$tablePrefix."vtiger_quotes_perunit` WHERE estimateid=? AND serviceid=?";
                        $result = $db->pquery($sql, [$quoteRow['quoteid'], $serviceId]);
                        $row    = $result->fetchRow();
                        if ($row == null) {
                            continue;
                        }
                        $serviceItem['cubic_feet'] = xmlspecialchars($row['cubicfeet']);
                        $serviceItem['months']     = xmlspecialchars((int)$row['months']);
                        $serviceItem['rate']       = xmlspecialchars($row['rate']);
                        break;
                    case 'Per CWT':
                    case 'SIT First Day Rate':
                        $sql    = "SELECT qty1 AS weight, rate FROM `".$tablePrefix."vtiger_quotes_perunit` WHERE estimateid=? AND serviceid=?";
                        $result = $db->pquery($sql, [$quoteRow['quoteid'], $serviceId]);
                        $row    = $result->fetchRow();
                        if ($row == null) {
                            continue;
                        }
                        $serviceItem['weight'] = xmlspecialchars($row['weight']);
                        $serviceItem['rate']   = xmlspecialchars($row['rate']);
                        break;
                    case 'Per CWT/Per Day':
                    case 'SIT Additional Day Rate':
                        $sql    = "SELECT qty1 AS weight, qty2 AS days, rate FROM `".$tablePrefix."vtiger_quotes_perunit` WHERE estimateid=? AND serviceid=?";
                        $result = $db->pquery($sql, [$quoteRow['quoteid'], $serviceId]);
                        $row    = $result->fetchRow();
                        if ($row == null) {
                            continue;
                        }
                        $serviceItem['weight'] = xmlspecialchars($row['weight']);
                        $serviceItem['days']   = xmlspecialchars((int)$row['days']);
                        $serviceItem['rate']   = xmlspecialchars($row['rate']);
                        break;
                    case 'Per CWT/Per Month':
                        $sql    = "SELECT qty1 AS weight, qty2 AS months, rate FROM `".$tablePrefix."vtiger_quotes_perunit` WHERE estimateid=? AND serviceid=?";
                        $result = $db->pquery($sql, [$quoteRow['quoteid'], $serviceId]);
                        $row    = $result->fetchRow();
                        if ($row == null) {
                            continue;
                        }
                        $serviceItem['weight'] = xmlspecialchars($row['weight']);
                        $serviceItem['months'] = xmlspecialchars((int)$row['months']);
                        $serviceItem['rate']   = xmlspecialchars($row['rate']);
                        break;
                    case 'Per Quantity':
                        $sql    = "SELECT qty1 AS quantity, rate FROM `".$tablePrefix."vtiger_quotes_perunit` WHERE estimateid=? AND serviceid=?";
                        $result = $db->pquery($sql, [$quoteRow['quoteid'], $serviceId]);
                        $row    = $result->fetchRow();
                        if ($row == null) {
                            continue;
                        }
                        $serviceItem['quantity'] = xmlspecialchars($row['quantity']);
                        $serviceItem['rate']     = xmlspecialchars($row['rate']);
                        break;
                    case 'Per Quantity/Per Day':
                        $sql    = "SELECT qty1 AS quantity, qty2 AS days, rate FROM `".$tablePrefix."vtiger_quotes_perunit` WHERE estimateid=? AND serviceid=?";
                        $result = $db->pquery($sql, [$quoteRow['quoteid'], $serviceId]);
                        $row    = $result->fetchRow();
                        if ($row == null) {
                            continue;
                        }
                        $serviceItem['quantity'] = xmlspecialchars($row['quantity']);
                        $serviceItem['days']     = xmlspecialchars((int)$row['days']);
                        $serviceItem['rate']     = xmlspecialchars($row['rate']);
                        break;
                    case 'Per Quantity/Per Month':
                        $sql    = "SELECT qty1 AS quantity, qty2 AS months, rate FROM `".$tablePrefix."vtiger_quotes_perunit` WHERE estimateid=? AND serviceid=?";
                        $result = $db->pquery($sql, [$quoteRow['quoteid'], $serviceId]);
                        $row    = $result->fetchRow();
                        if ($row == null) {
                            continue;
                        }
                        $serviceItem['quantity'] = xmlspecialchars($row['quantity']);
                        $serviceItem['months']   = xmlspecialchars((int)$row['months']);
                        $serviceItem['rate']     = xmlspecialchars($row['rate']);
                        break;
                    case 'Tabled Valuation':
                        $sql    = "SELECT released AS valuationtype, released_amount AS coverage, amount, deductible, rate FROM `".$tablePrefix."vtiger_quotes_valuation` WHERE estimateid=? AND serviceid=?";
                        $result = $db->pquery($sql, [$quoteRow['quoteid'], $serviceId]);
                        $row    = $result->fetchRow();
                        if ($row == null) {
                            continue;
                        }
                        //reversed.
                        //  valuationtype = 0 when Released is ON
                        //  valuationtype = 1 when Full Valuation is ON
                        if ($row['valuationtype'] == 0) {
                            $serviceItem['valuationtype'] = xmlspecialchars('1');
                            $serviceItem['cost']          = xmlspecialchars($row['rate']);
                        } else {
                            $serviceItem['valuationtype'] = xmlspecialchars('0');
                        }
                        $serviceItem['coverage']      = xmlspecialchars($row['coverage']);
                        $serviceItem['amount']        = xmlspecialchars($row['amount']);
                        $serviceItem['deductible']    = xmlspecialchars($row['deductible']);
                        $serviceItem['rate']          = xmlspecialchars($row['rate']);
                        break;
                    case 'SIT Cartage':
                        $sql    = "SELECT from_weight, to_weight, rate FROM `vtiger_tariffcwtbyweight` WHERE serviceid=?";
                        $result = $db->pquery($sql, [$serviceId]);
                        $row    = $result->fetchRow();
                        if ($row == null) {
                            continue;
                        }
                        $serviceItem['from_weight'] = xmlspecialchars($row['from_weight']);
                        $serviceItem['to_weight']   = xmlspecialchars($row['to_weight']);
                        $serviceItem['rate']        = xmlspecialchars($row['rate']);

                        $sql    = "SELECT weight FROM `".$tablePrefix."vtiger_quotes_cwtbyweight` WHERE estimateid=? AND serviceid=?";
                        $result = $db->pquery($sql, [$quoteRow['quoteid'], $serviceId]);
                        $row    = $result->fetchRow();
                        if ($row == null) {
                            continue;
                        }
                        $serviceItem['weight']      = xmlspecialchars($row['weight']);
                        break;
                    case 'CWT by Weight':
                        $sql    = "SELECT weight,rate FROM `".$tablePrefix."vtiger_quotes_cwtbyweight` WHERE estimateid=? AND serviceid=?";
                        $result = $db->pquery($sql, [$quoteRow['quoteid'], $serviceId]);
                        $row    = $result->fetchRow();
                        if ($row == null) {
                            continue;
                        }
                        $serviceItem['rate']        = xmlspecialchars($row['rate']);
                        $serviceItem['weight']      = xmlspecialchars($row['weight']);
                        break;
                    case 'Storage Valuation':
                        $sql    = "SELECT service_base_charge AS rate, service_base_charge_applies, service_base_charge_matrix FROM `vtiger_tariffservices` WHERE tariffservicesid=?";
                        $result = $db->pquery($sql, [$serviceId]);
                        $row    = $result->fetchRow();
                        if ($row == null) {
                            continue;
                        }
                        $serviceItem['service_base_charge_applies'] = xmlspecialchars($row['service_base_charge_applies']);
                        $serviceItem['service_base_charge_matrix']  = xmlspecialchars($row['service_base_charge_matrix']);
                        $serviceItem['rate']                        = xmlspecialchars($row['rate']);

                        $sql    = "SELECT * FROM `".$tablePrefix."vtiger_quotes_storage_valution` WHERE estimateid=? AND serviceid=?";
                        $result = $db->pquery($sql, [$quoteRow['quoteid'], $serviceId]);
                        $row    = $result->fetchRow();
                        if ($row == null) {
                            continue;
                        }
                        $serviceItem['months']      = xmlspecialchars($row['months']);
                        break;
                    case 'CWT Per Quantity':
                        $sql    = "SELECT cwtperqty_rate as rate FROM `vtiger_tariffservices` WHERE tariffservicesid=?";
                        $result = $db->pquery($sql, [$serviceId]);
                        $row    = $result->fetchRow();
                        if ($row == null) {
                            continue;
                        }
                        $serviceItem['rate']        = htmlspecialchars($row['rate']);

                        $sql    = "SELECT quantity, rate, weight FROM `".$tablePrefix."vtiger_quotes_cwtperqty` WHERE estimateid=? AND serviceid=?";
                        $result = $db->pquery($sql, [$quoteRow['quoteid'], $serviceId]);
                        $row    = $result->fetchRow();
                        if ($row == null) {
                            continue;
                        }
                        $serviceItem['quantity'] = htmlspecialchars($row['quantity']);
                        $serviceItem['rate']     = htmlspecialchars($row['rate'] > 0 ? $row['rate'] : $serviceItem['rate']);
                        $serviceItem['weight']   = htmlspecialchars($row['weight']);
                        break;
                    case 'Flat Rate By Weight':;
                      // if($tablePrefix == '') {
                        $sql = "SELECT * FROM `".$tablePrefix."vtiger_quotes_flatratebyweight` WHERE serviceid = ? AND estimateid = ?";
                        $result = $db->pquery($sql,[$serviceId,$quoteRow['quoteid']]);
                        $result = $result->fetchRow();
                        // Weights can be one of two keys, set them in their respective If statments
                        $serviceItem['weight']       = $result['weight'];
                        $serviceItem['weight_cap']   = $result['weight_cap'];
                      // } else {
                      //   $sql = "SELECT * FROM `vtiger_tariffflatratebyweight` WHERE serviceid = ? AND ? BETWEEN `from_weight` AND `to_weight`";
                      //   $result = $db->pquery($sql,[$serviceId,$quoteRow['local_weight']]);
                      //   // This is a check to see if it falls outside the range of set tiers
                      //   // so that it will use the highest tier available
                      //   if($db->num_rows($result) == 0) {
                      //     $sql = "SELECT * FROM `vtiger_tariffflatratebyweight` WHERE serviceid = ? ORDER BY to_weight DESC limit 1";
                      //     $result = $db->pquery($sql,[$serviceId]);
                      //   }
                      //   $result = $result->fetchRow();
                      //   $serviceItem['weight']       = $quoteRow['local_weight'];
                      //   $serviceItem['weight_cap']   = $result['to_weight'];
                      // }
                      // Set the two variables that will always have the same keys
                      $serviceItem['rate']         = $result['rate'];
                      $serviceItem['cwt_overflow'] = $result['cwt_rate'];
                      break;
                    default:
                        break;
                }

                $servicesArray['service#'.$serviceSeq] = $serviceItem;
                $serviceSeq++;
            }

//            file_put_contents('logs/xml.log', date('Y-m-d H:i:s - ').print_r($servicesArray, true)."\n", FILE_APPEND);

            $sectionItem['services']               = $servicesArray;
            $sectionsArray['section#'.$sectionSeq] = $sectionItem;
            $sectionSeq++;
        }
        $estimateNode['sections'] = $sectionsArray;
        return $estimateNode;
    }

    protected static function initializeVehicles($db, $customTariffType, $recordId, $isSirva, $tablePrefix = '')
    {
        $instanceName = getenv('INSTANCE_NAME');
        $vehiclesArray       = [];
        $allowedSirvaTariffs = ['ALLV-2A', 'NAVL-12A', '400N Base', '400N/104G', '400NG'];
        $runQuery            = true;
        if ($isSirva && in_array($customTariffType, $allowedSirvaTariffs)) {
            $sql = "SELECT * FROM `".$tablePrefix."vtiger_corporate_vehicles` WHERE estimate_id=?";
        } else if($instanceName == 'mccollisters') {
            $sql = "SELECT * FROM `".$tablePrefix."vtiger_vehiclelookup` WHERE vehiclelookup_relcrmid=?";
        } elseif (!$isSirva) {
            $sql = "SELECT * FROM `".$tablePrefix."vtiger_quotes_vehicles` WHERE estimateid=?";
        } else {
            $runQuery = false;
        }
        if ($runQuery) {
            $result = $db->pquery($sql, [$recordId]);
            $seq    = 1;
            while ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)) {
                $vehicle = [];
                if ($isSirva) {
                    $vehicle['make']                = $row['make'];
                    $vehicle['model']               = $row['model'];
                    $vehicle['year']                = $row['year'];
                    $vehicle['weight']              = $row['weight'];
                    $vehicle['service_type']        = $row['service'] ?: 'None';
                    $vehicle['dvp_value']           = $row['dvp_value'];
                    $vehicle['car_on_van']          = $row['car_on_van'];
                    $vehicle['oversize_class_type'] = $row['oversize_class'];
                    $vehicle['inoperable']          = $row['inoperable'];
                    $vehicle['width']               = $row['width'];
                    $vehicle['length']              = $row['length'];
                    $vehicle['height']              = $row['height'];
                    $vehicle['charge']              = $row['charge'];
                    $vehicle['comment']             = $row['comment'];
                    $vehicle['shipping']            = $row['shipping_count'];
                    $vehicle['not_shipping']        = $row['not_shipping_count'];
                } else if($instanceName == 'mccollisters') {
                    $vehicle['name'] = $row['vehiclelookup_color'] . ' '
                                       . $row['vehiclelookup_make'] . ' '
                                       . $row['vehiclelookup_model'] . ' '
                                       . $row['vehiclelookup_vin'];

                    $vehicle['make'] = $row['vehiclelookup_make'];
                    $vehicle['model'] = $row['vehiclelookup_model'];
                    // Can't send this or the rating engine thinks it's Sirva
                    //$vehicle['year'] = $row['vehiclelookup_year'];
                    $vehicle['vin'] = $row['vehiclelookup_vin'];
                    $vehicle['color'] = $row['vehiclelookup_color'];
                    $vehicle['odometer'] = $row['vehiclelookup_odometer'];
                    $vehicle['type'] = $row['vehiclelookup_type'];
                    $vehicle['license_state'] = $row['vehiclelookup_license_state'];
                    $vehicle['license_number'] = $row['vehiclelookup_license_number'];
                    $vehicle['oversize'] = InputUtils::CheckboxToBool($row['vehiclelookup_is_non_standard']) ? 'true' : 'false';
                    $vehicle['non_operable'] = InputUtils::CheckboxToBool($row['vehiclelookup_inoperable']) ? 'true' : 'false';
                } else {
                    $vehicle['description'] = $row['description'];
                    $vehicle['weight']      = $row['weight'];
                    $vehicle['sit_days']    = $row['sit_days'];
                    $vehicle['rate_type']   = $row['rate_type'];
                }
                $vehiclesArray['vehicle#'.$seq] = $vehicle;
                $seq++;
            }
        }

        return $vehiclesArray;
    }

    protected static function initializeGraebelFlatAuto($estimateId, $tablePrefix = '')
    {
        $flatAutoArray = [];
        $vtTableName = $tablePrefix . 'vtiger_vehicletransportation';
        $ceTableName = $tablePrefix . 'vtiger_crmentity';
        $db = PearDatabase::getInstance();
        $sql = "SELECT * FROM `$vtTableName` INNER JOIN `$ceTableName` ON `$vtTableName`.vehicletransportationid = `$ceTableName`.crmid WHERE vehicletrans_relcrmid = ? AND deleted = 0";
        $result = $db->pquery($sql, [$estimateId]);
        while ($row =& $result->fetchRow()) {
            $isBulky = $row['vehicletrans_ratingtype'] == 'Bulky';
            $oversize = 'false';
            if (!$isBulky && $row['vehicletrans_oversized']) {
                if (InputUtils::CheckboxToBool($row['vehicletrans_oversized'])) {
                    $oversize = 'true';
                } elseif ($row['vehicletrans_oversized'] != 'No') {
                    $oversize = $row['vehicletrans_oversized'];
                }
            }
            $flatAutoArray['entry#' . $row['vehicletransportationid']] = [
                'rating_type' => $row['vehicletrans_ratingtype'],
                'description' => $row['vehicletrans_description'],
                'miles' => $isBulky ? 0 : $row['vehicletrans_miles'],
                'overtime' => $isBulky ? 0 : ($row['vehicletrans_ot'] ? 'true' : 'false'),
                'diversions' => $isBulky ? '' : $row['vehicletrans_deversions'],
                'oversized' => $oversize,
                'inoperable' => $isBulky ? 'false' : ($row['vehicletrans_inoperable'] ? 'true' : 'false'),
                'insufficient_clearance' => $isBulky ? 'false' : ($row['vehicletrans_groundclearance'] ? 'true' : 'false'),
                'sit_days' => $isBulky ? 0 : $row['vehicletrans_sitdays'],
                'sit_miles' => $isBulky ? 0 : $row['vehicletrans_sitmiles'],
                'val_amt' => $isBulky ? 0 : $row['vehicletrans_valamount'],
                'carrier_type' => $isBulky ? '' : $row['vehicletrans_carriertype'],
            ];
        }

        $quotesTableName = $tablePrefix . 'vtiger_quotes';
        $contractID = $db->pquery("SELECT contract FROM `$quotesTableName` WHERE quoteid=?", [$estimateId])->fetchRow()['contract'];
        if ($contractID) {
            $contractFields = [
                'oversize_vehicle_charge',
                'vehicle_inoperable_charge',
                'auto_overtime_charge',
                'auto_diversion_fee',
                'auto_wait_time_per_hour',
                'auto_max_wait_time_charge',
                'auto_sit_per_day',
                'auto_pickup_delivery_charge',
                'auto_pickup_delivery_mileage',
                'auto_pickup_delivery_addl_mile_charge',
                'auto_waive_eac',
                'auto_waive_fuel_surcharge',
                'auto_waive_irr',
                'auto_waive_org_dest_service_charge',
            ];
            $contractRes = $db->pquery('SELECT '.implode(', ', $contractFields).'
                                        FROM vtiger_contracts INNER JOIN vtiger_crmentity ON (vtiger_contracts.contractsid=vtiger_crmentity.crmid)
                                        WHERE contractsid=? AND deleted=0',
                                       [$contractID]);
            if ($contractRes && $row = $contractRes->fetchRow()) {
                $contractData = [];
                foreach ($contractFields as $key) {
                    $contractData[$key] = $row[$key];
                }

                $sql    = 'SELECT * FROM `vtiger_contract_flat_rate_auto` WHERE contractid=?';
                $result    = $db->pquery($sql, [$contractID]);
                $count = 1;
                while ($row = $result->fetchRow()) {
                    $flat_rate_auto = [
                        'discount' => InputUtils::CheckboxToBool($row['discount']) ? 'true' : 'false',
                        'from_mileage' => $row['from_mileage'],
                        'to_mileage' => $row['to_mileage'],
                        'rate' => $row['rate'],
                    ];
                    $contractData['flat_rate_range#'.$count] = $flat_rate_auto;
                    $count++;
                }

                $flatAutoArray['contract'] = $contractData;
            }
        }

        return $flatAutoArray;
    }

    protected static function initializeGraebelSpecialPack($estimateId, $tablePrefix)
    {
        $specialPackArray = [];
        $db = PearDatabase::getInstance();
        $sql = "SELECT * FROM `".$tablePrefix."vtiger_upholsteryfinefinish`
                INNER JOIN `".$tablePrefix."vtiger_crmentity`
                ON `".$tablePrefix."vtiger_upholsteryfinefinish`.upholsteryfinefinishid = `".$tablePrefix."vtiger_crmentity`.crmid
                WHERE uff_relcrmid = ? AND deleted = 0";
        $result = $db->pquery($sql, [$estimateId]);
        while ($row =& $result->fetchRow()) {
            $specialPackArray['entry#' . $row['upholsteryfinefinishid']] = [
                    'description' => $row['uff_description'],
                    'upholstery' => $row['uff_upholstery'] ? 'true' : 'false',
                    'fine_finish' => $row['uff_finefinish'] ? 'true' : 'false',
                    'overtime' => $row['uff_overtime'] ? 'true' : 'false',
                    'pieces' => $row['uff_numpieces'],
                    'labor_hours' => $row['uff_unwraphours'],
            ];
        }
        return $specialPackArray;
    }

    protected static function getStopPackingItems($db, $stopId, $tablePrefix)
    {
        if (getenv('INSTANCE_NAME') != 'graebel' && !getenv('IGC_MOVEHQ')) {
            return [];
        }

        $packingArray = [];
        $seq = 1;
        $sql    = "SELECT * FROM `". $tablePrefix ."packing_items_extrastops` WHERE `stopid` =?";
        $result = $db->pquery($sql, [$stopId]);
        if ($db->num_rows($result) > 0) {
            while ($row =& $result->fetchRow()) {
                if ((int)$row['containers'] == 0 && (int)$row['pack_qty'] == 0
                    && (int)$row['unpack_qty'] == 0) {
                    continue;
                }
                $packingData['article_name'] = xmlspecialchars($row['label']);
                if (getenv('INSTANCE_NAME')=='graebel') {
                	$packingData['shipping'] = xmlspecialchars($row['containers']);
				} else {
					$packingData['shipping'] = xmlspecialchars($row['pack_qty']);
				}
                $packingData['materials'] = xmlspecialchars($row['materials']);
                $packingData['pack'] = xmlspecialchars($row['pack_qty']);
                $packingData['unpack'] = xmlspecialchars($row['unpack_qty']);
                $packingData['itemID'] = xmlspecialchars($row['itemid']);
                $packingData['item_attribs']  = ['crate' => 'false', 'has_weight' => 'false', 'bulky' => 'false', 'carton' => 'true', 'carton_cp' => 'true', 'carton_pbo' => 'false'];
                $packingArray['item#'.$seq]= $packingData;
                $seq++;
            }
        }
        return $packingArray;
    }


    protected static function initializeExtraStops($db, $estimateId, $isSirva, $tablePrefix = '')
    {
        $extraStopsArray = [];
        $runQuery        = true;
if(getenv('INSTANCE_NAME')=='sirva')
{
        $sql             = 'SELECT * FROM `'.$tablePrefix.'vtiger_extrastops`
                            INNER JOIN `'.$tablePrefix.'vtiger_crmentity` ON `'.$tablePrefix.'vtiger_crmentity`.crmid = `'.$tablePrefix.'vtiger_extrastops`.extrastops_relcrmid
                            INNER JOIN `vtiger_extrastops_type` ON `vtiger_extrastops_type`.`extrastops_type` = `'.$tablePrefix.'vtiger_extrastops`.`extrastops_type`
                            INNER JOIN `vtiger_extrastops_type_origdest` ON `vtiger_extrastops_type`.`extrastops_typeid` = `vtiger_extrastops_type_origdest`.`type_id`
                            WHERE extrastops_relcrmid=? AND deleted = 0';
} else {
        $sql             = "SELECT * FROM `".$tablePrefix."vtiger_extrastops` INNER JOIN `".$tablePrefix."vtiger_crmentity` ON `".$tablePrefix."vtiger_crmentity`.crmid = `".$tablePrefix."vtiger_extrastops`.extrastopsid WHERE
        extrastops_relcrmid=? AND deleted = 0";
}
        if ($runQuery) {
            $result = $db->pquery($sql, [$estimateId]);
            //@NOTE: seq is so we can have multiple versions of the same row, anything past the # is dropped.
            $seq = 0;
            while ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)) {
                if(!$isSirva) {
                $rowStopType = $row['extrastops_type'];
                // this won't work for translations (but this doesn't appear to be a translatable string as of now)
                if ($rowStopType == 'Extra Pickup') {
                    $rowStopType = 'Origin';
                } elseif ($rowStopType == 'Extra Delivery') {
                    $rowStopType = 'Destination';
                }
                } else {
                    $rowStopType = $row['origdest'];
                    $rowStopId = $row['extrastopsid'];
                }

                $extraStops              = [];
                $address                 = [];
                $extraStops['orig_dest'] = $rowStopType;

                $extraStops['items'] = self::getStopPackingItems($db, $row['extrastopsid'], $tablePrefix);

                $extraStops['sequence']  = $row['extrastops_sequence'];
                $extraStops['id']        = $row['extrastops_description'];
		if($isSirva) {
                	$extraStops['id']        = $row['extrastops_name'];
		}
                $extraStops['weight']    = $row['extrastops_weight'];
                if ($row['extrastops_date']) {
                    //$extraStops['date'] = $row['extrastops_date'];
                    $extraStops['date'] = ($row['extrastops_date'])?date('m/d/Y', strtotime($row['extrastops_date'])):date('m/d/Y');
                }
                $address['add1']    = $row['extrastops_address1'];
                $address['add2']    = $row['extrastops_address2'];
                $address['city']    = $row['extrastops_city'];
                $address['state']   = $row['extrastops_state'];
                $address['zip']     = $row['extrastops_zip'];
                $address['country'] = $row['extrastops_country'];
                $address['phone1']  = $row['extrastops_phone1'];
                $address['phone2']  = $row['extrastops_phone2'];
                $address['address_type'] = $row['extrastops_type'];

                $phone_type_1 = 'home';
                if($row['extrastops_phonetype1'] == 'Cell'){
                    $phone_type_1 = 'mobile';
                } elseif($row['extrastops_phonetype1'] == 'Work'){
                    $phone_type_1 = 'work';
                }

                $phone_type_2 = 'home';
                if($row['extrastops_phonetype2'] == 'Cell'){
                    $phone_type_2 = 'mobile';
                } elseif($row['extrastops_phonetype2'] == 'Work'){
                    $phone_type_2 = 'work';
                }

                if($phone_type_1 == $phone_type_2){
                    $phone_type_2 = 'other';
                }

                $address["$phone_type_1"."_phone"]  = $row['extrastops_phone1'];
                $address["$phone_type_2"."_phone"]  = $row['extrastops_phone2'];

                //$extraStops['extrastops_phonetype1'] = $row['extrastops_phonetype1'];
                //$extraStops['extrastops_phonetype2'] = $row['extrastops_phonetype2'];
                $extraStops['extrastops_isprimary']         = $row['extrastops_isprimary'];
                $address_type                         = self::getAddressTypeForStop($isSirva,
                    ($isSirva?$row['extrastops_sirvastoptype']:$row['extrastops_type']));
                $address['address_type']              = $address_type;
                $extraStops['address']                = $address;
                //TODO: maybe location maybe locations.
                $extraStopsArray['location#'.$seq++] = $extraStops;
            }
        }
        return $extraStopsArray;
    }

    protected static function initializeLocalCarrier($db, $localCarrierId, $vlBrand, $carrier)
    {
        $rvArray = [];
        $stmt = 'SELECT * FROM vtiger_localcarrier
                JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_localcarrier.localcarrierid
                WHERE vtiger_crmentity.agentid = ?';
        $result = $db->pquery($stmt, [$localCarrierId]);
        //@NOTE: seq is so we can have multiple versions of the same row, anything past the # is dropped.
        $seq = 0;
        $tmpCarriers = [];
        while ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)) {
            $carrierInfo = [];
            //I'm setting this version here because the template i'm using is ver. 1
            $carrierInfo['version'] = '1';
            $carrierInfo['qlab_id'] = '';
            $carrierInfo['status'] = 'true';  //can be true/false but since it's set it's true.
            $carrierInfo['name'] = xmlspecialchars($row['carrier_name']);
            $carrierInfo['contact'] = xmlspecialchars($row['contact_name']);
            $carrierInfo['brand'] = $vlBrand;
            $carrierInfo['addr1'] = xmlspecialchars($row['address1']);
            $carrierInfo['addr2'] = xmlspecialchars($row['address2']);
            $carrierInfo['city'] = xmlspecialchars($row['city']);
            $carrierInfo['state'] = xmlspecialchars($row['state']);
            $carrierInfo['zip'] = xmlspecialchars($row['zip']);
            $carrierInfo['country'] = xmlspecialchars($row['country']);
            $carrierInfo['phone'] = $row['phone1'];
            $carrierInfo['fax'] = $row['fax'];
            $carrierInfo['web'] = xmlspecialchars($row['website']);
            $carrierInfo['email'] = xmlspecialchars($row['email']);
            //@NOTE: It's been pointed out the correct variable names are mc_puc and dot. will send both.
            $carrierInfo['mc_num'] = xmlspecialchars($row['mc_number']);
            $carrierInfo['mc_puc'] = $carrierInfo['mc_num'];
            $carrierInfo['dot_num'] = xmlspecialchars($row['dot_number']);
            $carrierInfo['dot'] = $carrierInfo['dot_num'];
            $carrierInfo['local_terms_title'] = xmlspecialchars($row['local_terms_title']);
            $carrierInfo['local_terms_condition'] = xmlspecialchars($row['local_terms_condition']);
            if($carrier == $row['localcarrierid']) {
                $rvArray['carrier#0'] = $carrierInfo;
                $seq = 1;
            }else {
                $tmpCarriers[] = $carrierInfo;
            }
        }
        foreach($tmpCarriers as $carrierInfo) {
            $rvArray['carrier#'.$seq++] = $carrierInfo;
        }
        return $rvArray;
    }

    protected static function getAddressTypeForStop($isSirva, $stop_type)
    {
        $address_type = '';
        if (!$isSirva) {
            $address_type = $stop_type;
        } else {
            //map it back from sirva_stop_type... I think this should be a picklist and a translate.
            //but first make it work then make it nice
            switch ($stop_type) {
                case 'XP1':
                    $address_type = 'Extra Pickup 1';
                    break;
                case 'XP2':
                    $address_type = 'Extra Pickup 2';
                    break;
                case 'XP3':
                    $address_type = 'Extra Pickup 3';
                    break;
                case 'XP4':
                    $address_type = 'Extra Pickup 4';
                    break;
                case 'XP5':
                    $address_type = 'Extra Pickup 5';
                    break;
                case 'XD1':
                    $address_type = 'Extra Delivery 1';
                    break;
                case 'XD2':
                    $address_type = 'Extra Delivery 2';
                    break;
                case 'XD3':
                    $address_type = 'Extra Delivery 3';
                    break;
                case 'XD4':
                    $address_type = 'Extra Delivery 4';
                    break;
                case 'XD5':
                    $address_type = 'Extra Delivery 5';
                    break;
                case 'OSIT':
                    $address_type = 'OSIT';
                    break;
                case 'DSIT':
                    $address_type = 'DSIT';
                    break;
                case 'OSTG':
                    $address_type = 'Self Storage Pickup';
                    break;
                case 'DSTG':
                    $address_type = 'Self Storage Delivery';
                    break;
                case 'OPRM':
                    $address_type = 'Perm PU';
                    break;
                case 'DPRM':
                    $address_type = 'Perm Del';
                    break;
                default:
                    break;
            }
        }

        return $address_type;
    }

    public static function getValuationDed($quoteRow) {
        if (getenv('IGC_MOVEHQ')) {
            return ValuationUtils::MapValuationDeductible($quoteRow['valuation_deductible'],
                                                          $quoteRow['valuation_deductible_amount']);
        } elseif (getenv('INSTANCE_NAME') == 'sirva') {
            if ($quoteRow['valuation_deductible'] == 'MVP - $0'
                || $quoteRow['valuation_deductible'] == 'ECP - $0'
                || $quoteRow['valuation_deductible'] == 'FVP - $0'
            ) {
                return 'FVP - $0 Ded';
            }
            if ($quoteRow['valuation_deductible'] == 'MVP - $250'
                || $quoteRow['valuation_deductible'] == 'ECP - $250'
                || $quoteRow['valuation_deductible'] == 'FVP - $250'
            ) {
                return 'FVP - $250 Ded';
            }
            if ($quoteRow['valuation_deductible'] == 'MVP - $500'
                || $quoteRow['valuation_deductible'] == 'ECP - $500'
                || $quoteRow['valuation_deductible'] == 'FVP - $500'
            ) {
                return 'FVP - $500 Ded';
            }

            //NOTE: 60c/lb is no longer used, copied from above.
            return '';
        } else {
            if (in_array($quoteRow['valuation_deductible'], ['FVP - $0', 'FVP - $250', 'FVP - $500']))
            {
                return $quoteRow['valuation_deductible'] . ' Ded';
            }
            return '0.60/lb';
        }
    }

    protected static function buildBillingInfo($ordersID, $module = 'Orders')
    {
        $array = ['charge_approved' => self::$hardcoded['chargeApproved']];
        if ($ordersID) {
            try {
                $recordModel       = Vtiger_Record_Model::getInstanceById($ordersID, $module);
                $array['company']  = $recordModel->get('bill_company');
                $array['address1'] = $recordModel->get('bill_street');
                $array['address2'] = $recordModel->get('bill_pobox');
                $array['city']     = $recordModel->get('bill_city');
                $array['state']    = $recordModel->get('bill_state');
                $array['zip']      = $recordModel->get('bill_code');
                $array['country']  = $recordModel->get('bill_country');
                $array['phone']    = $recordModel->get('invoice_phone');
                $array['email']    = $recordModel->get('invoice_email');
                $array['billing_type']    = $recordModel->get('billing_type');
                list ($array['first_name'], $array['last_name'], $array['contact_email'], $array['account_name']) = self::getOrderContactInfo($recordModel->get('orders_billingcustomerid'));
            } catch (Exception $ex) {
            }
        }

        return $array;
    }

    protected static function getOrderContactInfo($contactId) {
        if (!$contactId) {
            return [];
        }
        try {
            $recordModel = Vtiger_Record_Model::getInstanceById($contactId);
            if (!$recordModel) {
                return [];
            }
            return [
                xmlspecialchars($recordModel->get('firstname')),  //contact field
                xmlspecialchars($recordModel->get('lastname')),   //contact field
                xmlspecialchars($recordModel->get('email')),      //contact field
                xmlspecialchars($recordModel->get('accountname')) //accounts field
            ];
        } catch (Exception $ex) {
        }
        return [];
    }

    protected static function getAgentInformation($agentID, $module = 'AgentManager')
    {
        if (!$agentID) {
            return [];
        }

        try {
            $recordModel = Vtiger_Record_Model::getInstanceById($agentID, $module);
            return [
                'agency_name'       => xmlspecialchars($recordModel->get('agency_name')),
                'mailing_address_1' => xmlspecialchars($recordModel->get('agentmanager_mailing_address_1')),
                'mailing_address_2' => xmlspecialchars($recordModel->get('agentmanager_mailing_address_2')),
                'mailing_city'      => xmlspecialchars($recordModel->get('agentmanager_mailing_city')),
                'mailing_state'     => xmlspecialchars($recordModel->get('agentmanager_mailing_state')),
                'mailing_zip'       => xmlspecialchars($recordModel->get('agentmanager_mailing_zip')),
                'mailing_country'   => xmlspecialchars($recordModel->get('agentmanager_mailing_country')),
                'phone1'            => xmlspecialchars($recordModel->get('phone1')),
                'fax'               => xmlspecialchars($recordModel->get('fax')),
                'email'             => xmlspecialchars($recordModel->get('email')),
                //dot/state number was <puc> and mc was <mc_puc>
                'puc'               => xmlspecialchars($recordModel->get('state_number'))
            ];
        } catch (Exception $ex) {
        }
        return [];
    }

    protected static function getVanlineInformation($agentId, $module = 'AgentManager')
    {
        if(!$agentId) {
            return [];
        }

        try {
            $agentRecordModel = Vtiger_Record_Model::getInstanceById($agentId, $module);
            $recordModel = Vtiger_Record_Model::getInstanceById($agentRecordModel->get('vanline_id'), 'VanlineManager');
            return [
                'vanline_name'      => xmlspecialchars($recordModel->get('vanline_name')),
                'mailing_address_1' => xmlspecialchars($recordModel->get('address1')),
                'mailing_address_2' => xmlspecialchars($recordModel->get('address2')),
                'mailing_city'      => xmlspecialchars($recordModel->get('city')),
                'mailing_state'     => xmlspecialchars($recordModel->get('state')),
                'mailing_zip'       => xmlspecialchars($recordModel->get('zip')),
                'mailing_country'   => xmlspecialchars($recordModel->get('country')),
                'phone1'            => xmlspecialchars($recordModel->get('phone1')),
                'phone2'            => xmlspecialchars($recordModel->get('phone2')),
                'fax'               => xmlspecialchars($recordModel->get('fax')),
                //dot/state number was <puc> and mc was <mc_puc>
                'mc_puc'            => xmlspecialchars($recordModel->get('mc_number')),
                'puc'               => xmlspecialchars($recordModel->get('dot_number'))
            ];
        } catch (Exception $ex) {
        }
        return [];
    }

    //function to pull an existant image filepath for a record.
    // this is a full absolute path, not relative, for use in internal processing,
    // not serving.
    protected static function getValidImageFile($id, $module = 'Users')
    {
        $imageFilePath = false;
        if ($id) {
            //try here probbaly?
            try {
                // $module = $module ?: 'Users';
                $recordModel = Vtiger_Record_Model::getInstanceById($id, $module);
                //because it's an array we want the first (and only...) value.
                //remember to check if you even have a method before wantanly calling it or shit will break
                if (method_exists($recordModel, 'getImageDetails')) {
                    $imageDetail = $recordModel->getImageDetails()[0];
                    //@TODO: consider instead doing this in the getImageDetails?
                    //remember the path containts part of the filename itself!
                    $imageDetail['imagepath'] = $imageDetail['path'].'_'.$imageDetail['name'];
                    $imageDetail['alt']       = $imageDetail['title'] = $imageDetail['imagename'] = $imageDetail['name'];
                    //ensure a name fields exist and are filled, and that the built path finds a file.
                    // AND verify the image file exists.
                    if (
                        array_key_exists('imagename', $imageDetail) && $imageDetail['imagename'] &&
                        array_key_exists('imagepath', $imageDetail) && $imageDetail['imagepath'] &&
                        //@NOTE: This is a relative path, from index.php
                        file_exists($imageDetail['imagepath'])
                    ) {
                        $imageFilePath = $imageDetail['imagepath'];
                    }
                }
            } catch (Exception $e) {
                //do nothing we didn't have permissions to view that record... OH well no image.
            }
        }
        return $imageFilePath;
    }

    protected static function base64Image($filePath)
    {
        $file_data = file_get_contents($filePath);
        if ($file_data) {
            return base64_encode($file_data);
        }
        return false;
    }

    protected static function initializeParticipatingAgents($recordId)
    {
        //initialize these as things because the end point expects these tags to be.
        $participatingAgents['booking_agent'] = [];
        $participatingAgents['origin_agent'] = [];
        $participatingAgents['dest_agent'] = [];
        //$participatingAgents['carrier_agent'] = [];

        //$participatingAgentModel = Vtiger_Module_Model::getInstance('ParticipatingAgents');
        //if ($recordId && $participatingAgentModel->isActive()) {
        if ($recordId) {
            //$foundAgents = $participatingAgentModel->getParticipants($recordId);
            $foundAgents = ParticipatingAgents_Module_Model::getParticipants($recordId);
            foreach ($foundAgents as $workingAgent) {
                $agent     = [];
                $agentType = $workingAgent['agent_type'];
                switch ($workingAgent['agent_type']) {
                    case 'Booking Agent':
                        $agentType = 'booking_agent';
                        break;
                    case 'Origin Agent':
                        $agentType = 'origin_agent';
                        break;
                    case 'Destination Agent':
                        $agentType = 'dest_agent';
                        break;
                    case 'Carrier':
                        $agentType = 'carrier_agent';
                        break;
                    default:
                        //so xml doesn't have spaces you know what just dump anything not regular char!
                        $agentType = preg_replace('/[^a-zA-Z0-9\_]/i', '_', $agentType);
                        //might as well lower case it to match the others
                        $agentType = strtolower($agentType);
                }
                /*
                       <option style="text-align:left" value="Hauling Agent">Hauling Agent</option>
                       <option style="text-align:left" value="Invoicing Agent">Invoicing Agent</option>
                       <option style="text-align:left" value="Estimating Agent">Estimating Agent</option>
                       <option style="text-align:left" value="Collecting Agent">Collecting Agent</option>
                       <option style="text-align:left" value="Coordinating Agent">Coordinating Agent</option>
                       <option style="text-align:left" value="D/A Coordinating Agent">D/A Coordinating Agent</option>
                       <option style="text-align:left" value="Extra Delivery Agent">Extra Delivery Agent</option>
                       <option style="text-align:left" value="Extra Pickup Agent">Extra Pickup Agent</option>
                       <option style="text-align:left" value="O/A Coordinating Agent">O/A Coordinating Agent</option>
                       <option style="text-align:left" value="Packing Agent">Packing Agent</option>
                       <option style="text-align:left" value="Radial Dispatch Agent">Radial Dispatch Agent</option>
                       <option style="text-align:left" value="Sales Org">Sales Org</option>
                       <option style="text-align:left" value="Split Booking">Split Booking</option>
                       <option style="text-align:left" value="Survey Agent">Survey Agent</option>
                       <option style="text-align:left" value="Unpacking Agent">Unpacking Agent</option>
                       <option style="text-align:left" value="Warehousing Agent">Warehousing Agent</option></select></td><td class="fieldValue" style="text-align:center;margin:auto"><input name="popupReferenceModule" type="hidden" value="Agents"/><div class="input-prepend input-append" style="text-align:center;margin:auto"><input name="agents_id" type="hidden" value class="sourceField default" data-displayvalue="" data-
                */
                foreach ($workingAgent as $key => $data) {
                    switch ($key) {
                        case 'agentName':
                            $key = 'name';
                            break;
                        case 'agent_number':
                            $key = 'code';
                            break;
                        case 'agent_address1':
                            $key = 'add1';
                            break;
                        case 'agent_address2':
                            $key = 'add2';
                            break;
                        case 'agent_city':
                            $key = 'city';
                            break;
                        case 'agent_state':
                            $key = 'state';
                            break;
                        case 'agent_zip':
                            $key = 'zip';
                            break;
                        case 'agent_country':
                            $key = 'country';
                            break;
                        case 'agent_phone':
                            $key = 'phone';
                            break;
                        case 'agent_fax':
                            $key = 'fax';
                            break;
                        case 'agent_email':
                            $key = 'email';
                            break;
                        case 'agent_puc':
                            $key = 'puc';
                            break;
                        case 'agents_mc_number':
                            $key = 'mc_puc';
                            break;
                        case 'agents_dot_number':
                            $key = 'dot';
                            break;
                        default:
                            //ignore any other keys, because this oft has the 0,1,2,3 from db fetch mode both.
                            $key = false;
                    }
                    if ($key) {
                        if ($key == 'phone') {
                            $agent[$key] = self::formatUSPhone($data);
                        } else {
                            $agent[$key] = $data;
                        }
                    }
                }
                $participatingAgents[$agentType] = $agent;
            }
        }
        return $participatingAgents;
    }

    //OT 16747 - adapted from revenueHandler
    protected static function initializeMoveRoles($orderRecordModel)
    {
        $returnArray = [];
        $allMoveRoles = $orderRecordModel->getMoveRoles();
        $i = 0;
        foreach ($allMoveRoles as $roleid => $moveRoleRecord) {
            $tempArray = self::getAssocDetails($moveRoleRecord['moveroles_employees']);
            $assocType = $moveRoleRecord['moveroles_role'];
            if(getenv('IGC_MOVEHQ') && getenv('INSTANCE_NAME') != 'graebel')
            {
                $assocType = \Vtiger_Functions::getCRMRecordLabel($assocType);
            }
            $assocType = preg_replace('/[^a-zA-Z0-9\_]/i', '_', $assocType);
            $assocType = preg_replace('/^2nd/', 'second', $assocType);
            $assocType = preg_replace('/^3rd/', 'third', $assocType);
            $assocType = strtolower($assocType);
            if (!$assocType) {
                continue;
            }
            $returnArray[$assocType]['associate #'.$i++] = $tempArray;
        }
        return $returnArray;
    }
    //OT 16747 - If any additional info needed for move roles, put it here.

    protected static function getAssocDetails($assocID)
    {
        $empArray = [];
        try {
            if ($recordModel = Vtiger_Record_Model::getInstanceById($assocID, 'Employees')) {
                $empArray['first_name']         = $recordModel->get('name');
                $empArray['last_name']          =  $recordModel->get('employee_lastname');
                $empArray['mobile_number']      = $recordModel->get('employee_mphone');
                $empArray['associate_title']    = $recordModel->get('employees_title');
                return $empArray;
            }
        } catch (Exception $e) {
        }
        return;
    }

    protected static function initializeOfficeInfo($opp, $ownerID, $quoteRow) {
        //$isSirva    = getenv('INSTANCE_NAME') == 'sirva';
        if (getenv('INSTANCE_NAME') == 'sirva') {
            return self::initializeOfficeInfoForSirva($opp, $ownerID);
        }

        if ($quoteRow['orders_id']) {
            try {
                return self::getSalesPersonFromMoveRoles($quoteRow['orders_id']);
            } catch (Exception $exception) {

            }
        }

        if ($quoteRow['potentialid']) {
            try {
                return self::getSalesPersonFromMoveRoles($quoteRow['potentialid']);
            } catch (Exception $exception) {

            }
        }
        return [];
    }

    protected static function getSalesPersonFromMoveRoles($parentRecord) {
        $salesPersons = [];
        $salesPersonClassName = 'Salesperson';
        try {
            $parentRecordModel = Vtiger_Record_Model::getInstanceById($parentRecord);
        } catch (Exception $exception) {
            return [];
        }
        if (!$parentRecordModel) {

            return [];
        }
        $moveRolesGuestRecords = $parentRecordModel->getGuestModuleRecords('MoveRoles');
        foreach ($moveRolesGuestRecords as $moveRoleRecord) {
            $employeeRoleID = $moveRoleRecord->get('moveroles_role');
            try {
                $employeeRoleRecord = Vtiger_Record_Model::getInstanceById($employeeRoleID);
            } catch (Exception $exception) {
                continue;
            }
            if ($employeeRoleRecord->get('emprole_class') === $salesPersonClassName) {
                $employeeID = $moveRoleRecord->get('moveroles_employees');
                if ($employeeID) {
                    $singleSalesPerson = self::getSalesPersonInfo($employeeID);
                    if ($singleSalesPerson) {
                        $salesPersons[] = $singleSalesPerson;
                    }
                }
            }
        }
        return $salesPersons[0];
    }

    protected static function getSalesPersonInfo($employeeID) {
        try {
            $salesPersonRecord = Vtiger_Record_Model::getInstanceById($employeeID);
        } catch (Exception $exception) {
            return [];
        }
        if (!$salesPersonRecord) {
            return [];
        }
        if ($salesPersonRecord->get('name')) {
            $salesperson_name = $salesPersonRecord->get('name');
        }
        if ($salesPersonRecord->get('employee_lastname')) {
            if ($salesperson_name) {
                $salesperson_name .= ' ';
            }
            $salesperson_name .= $salesPersonRecord->get('employee_lastname');
        }
        return [
            'salesperson_name'         => $salesperson_name,
            'salesperson_phone'        => $salesPersonRecord->get('employee_mphone'),
            'salesperson_mobile_phone' => $salesPersonRecord->get('employee_mphone'),
            'salesperson_home_phone'   => $salesPersonRecord->get('employee_hphone'),
            'salesperson_fax'          => $salesPersonRecord->get('employee_fax'),
            'salesperson_email'        => $salesPersonRecord->get('employee_email'),
        ];
    }

    protected static function initializeOfficeInfoForSirva($opp, $ownerID) {
        $salesPerson = [];
        $coordinator = [];
//        $salesPerson = ParticipatingAgents_Module_Model::getParticipants($opp['sales_person']);
        if ($opp['smownerid']) {
            $db          = PearDatabase::getInstance();
            $sql = "SELECT * FROM `vtiger_users` WHERE id = ?";
            $result = $db->pquery($sql, [$opp['smownerid']]);
            $coordinator = $result->fetchRow();
        }

        if ($ownerID) {
            $db          = PearDatabase::getInstance();
            $sql = "SELECT * FROM `vtiger_users` WHERE id = ?";
            $result = $db->pquery($sql, [$ownerID]);
            $salesPerson = $result->fetchRow();
        }

        $phoneArray = [
          'Home Phone' => 'phone_home',
          'Mobile Phone' => 'phone_mobile',
          'Office Phone' => 'phone_work',
          'Secondary Phone' => 'phone_other'
        ];
        //Sets the default to the work phone number, in case primary_phone hasn't been set
        $sphoneNumber = $salesPerson['phone_work'];
        $cphoneNumber = $coordinator['phone_work'];

        // NOTE: TFS28083: sphoneNumber and cphoneNumber should always be the work phone.
        // if ($salesPerson['primary_phone']) {
        //     $sphoneNumber = $salesPerson[$phoneArray[$salesPerson['primary_phone']]];
        // }
        // if ($coordinator['primary_phone']) {
        //     $cphoneNumber = $coordinator[$phoneArray[$coordinator['primary_phone']]];
        // }

        $office_info = [
            'salesperson_name'      => $salesPerson['first_name'].' '.$salesPerson['last_name'],
            'salesperson_phone'     => $sphoneNumber,
            'salesperson_fax'       => $salesPerson['phone_fax'],
            'salesperson_email'     => $salesPerson['email1'],

            'coordinator_name'      => $coordinator['first_name'].' '.$coordinator['last_name'],
            'coordinator_emp_id'    => $coordinator['id'],
            'coordinator_phone'     => $cphoneNumber,
            'coordinator_cell'      => $coordinator['phone_mobile'],
            'coordinator_fax'       => $coordinator['phone_fax'],
            'coordinator_email'     => $coordinator['email1'],
        ];



        return $office_info;
    }

    protected static function getValuationSummary($valuation_options, $returnData) {
        if (!$returnData) {
            return [];
        }
        $valuationSummary = [];
        if ($valuation_options) {
            $valuation_options = base64_decode($valuation_options);
            if ($valuation_options) {
                $valuation_options = json_decode($valuation_options, true);
            }
            if (is_array($valuation_options)) {
                foreach ($valuation_options['ValuationSummary'] as $identifier => $option) {
                    $valuationSummary['valuation_option#'.$identifier] = $option;
                }
            }
        }

        return $valuationSummary;
    }

    protected static function getComments($estimateId, $potentialid, $returnData) {
        if (!$returnData) {
            return [];
        }

        //Only add notes if this is for reports, so we don't gunk up the request with records that have a lot of comments
        $newNotes = [];
        $seq = 0;
        if ($estimateId) {
            //Fetch Estimate comments
            foreach (\ModComments_Record_Model::getAllComments($estimateId) as $commentModule) {
                $noteDetails                                 = [];
                $modifiedTime                                = Carbon::createFromFormat('Y-m-d H:i:s', $commentModule->get('modifiedtime'));
                $noteDetails['date_time_stamp']              = $modifiedTime->format('m-d-y h:i A');
                $noteDetails['comment']                      = $commentModule->get('commentcontent');
                $noteDetails['note_type']                    = "0";
                $noteDetails['created_on_device']            = "0";
                $noteDetails['sync_note_id']                 = $commentModule->getId();
                $newNotes['note_entry#'.$seq] = $noteDetails;
                $seq++;
            }
        }
        if ($potentialid != '') {
            //Fetch Opportunity comments
            foreach (\ModComments_Record_Model::getAllComments($potentialid) as $commentModule) {
                $noteDetails                                 = [];
                $modifiedTime                                = Carbon::createFromFormat('Y-m-d H:i:s', $commentModule->get('modifiedtime'));
                $noteDetails['date_time_stamp']              = $modifiedTime->format('m-d-y h:i A');
                $noteDetails['comment']                      = $commentModule->get('commentcontent');
                $noteDetails['note_type']                    = 1;
                $noteDetails['created_on_device']            = "0";
                $noteDetails['sync_note_id']                 = $commentModule->getId();
                $newNotes['note_entry#'.$seq] = $noteDetails;
                $seq++;
            }
        }

        return $newNotes;
    }
}
