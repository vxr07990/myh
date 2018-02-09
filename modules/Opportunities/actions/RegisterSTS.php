<?php
require_once('libraries/nusoap/nusoap.php');
class Opportunities_RegisterSTS_Action extends Vtiger_BasicAjax_Action
{
    public $opportunityId = null;
    public $estimateId = null;
    public $curlAuth = false;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     *
    * Cunstruct an object for STS registration and convert it to JSON
    * JSON will then be sent to Sirva, then we will expect a
    * response message with the status of our request
     *
     * @param Vtiger_Request $request
     * @return Vtiger_Response
     *
    */
    public function process(Vtiger_Request $request)
    {
        $db = $db ?: PearDatabase::getInstance();

        $timer['start'] = microtime(true);

        //Collect errors. If we have any, we will stop before we attempt to register and let the user know.
        $error = '';

        //Initial collection of ids required for the STS object
        //Opportunity
        $this->opportunityId = $request->get('recordId');
        file_put_contents('logs/STS.log', "\nStarting STS registration for oppID-".$this->opportunityId." on " . date("F j, Y, g:i a") . "...\n\n", FILE_APPEND);
        $stsResource['opportunitiesInfo'] = Vtiger_Record_Model::getInstanceById($this->opportunityId, 'Opportunities')->getData();
        if ($stsResource['opportunitiesInfo']['contact_id'] == '') {
            $response = new Vtiger_Response();
            $response->setResult('Error: There is no contact for this Opportunity. Please add a contact to the opportunity.');

            $response->emit();
            return;
        }

        //User
        $stsResource['user']              = Users_Record_Model::getCurrentUserModel()->getData();
        if (($stsResource['user']['sts_user_id'] == '' || $stsResource['user']['sts_agent_id'] == '') && ($stsResource['user']['sts_user_id_nvl'] == '' || $stsResource['user']['sts_agent_id_nvl'] == '')) {
            $response = new Vtiger_Response();
            $response->setResult('Error: Please set up your STS username and STS agentID in your user profile.');

            $response->emit();
            return;
        }

        //Salesperson
        $stsResource['salesperson'] = [];
        if($stsResource['opportunitiesInfo']['sales_person'] != ''){
            $stsResource['salesperson'] = Users_Record_Model::getInstanceById($stsResource['opportunitiesInfo']['sales_person'])->getData();
        }

        //Estimate
        $this->estimateId      = $db->getOne("SELECT `quoteid` FROM `vtiger_quotes` where `potentialid` = $this->opportunityId AND `is_primary` = '1'");
        if ($this->estimateId == '') {
            $response = new Vtiger_Response();
            $response->setResult('Error: There is no primary estimate for this Opportunity. Please setup a primary Estimate for this Opportunity.');

            $response->emit();
            return;
        }

        //Collection of queries used to gather information for the STS object
        $sql['crates']   = 'SELECT length, width, height, pack, ot_pack, unpack, ot_unpack FROM `vtiger_crates` WHERE quoteid = ?';
        $sql['packing']  = 'SELECT pack_qty, ot_pack_qty, itemid, unpack_qty, ot_unpack_qty, pack_cont_qty FROM `vtiger_packing_items` WHERE quoteid = ?';
        $sql['bulky']    = 'SELECT bulkyid, ship_qty, label FROM `vtiger_bulky_items` WHERE quoteid = ?';
        $sql['vanline']  = 'SELECT `vtiger_vanlinemanager`.`vanline_id`
							FROM `vtiger_vanlinemanager`
							JOIN `vtiger_agentmanager`
								ON `vtiger_agentmanager`.`vanline_id` = `vtiger_vanlinemanager`.`vanlinemanagerid`
							WHERE `vtiger_agentmanager`.`agency_code` = ?';
        $sql['acc']      = 'SELECT charge, qty, discount, discounted FROM `vtiger_misc_accessorials` WHERE quoteid = ?';
        $sql['segments'] = 'SELECT * FROM `vtiger_addresssegments` WHERE `addresssegments_relcrmid` = ?';
        $sql['vehicles'] = 'SELECT sts_vehicles FROM `vtiger_quotes` WHERE quoteid = ?';
        $sql['tariff']   = 'SELECT custom_tariff_type FROM `vtiger_tariffmanager` WHERE tariffmanagerid = (SELECT effective_tariff FROM `vtiger_quotes` WHERE `quoteid` = ?)';
        $sql['stops']    = 'SELECT * FROM `vtiger_extrastops` WHERE extrastops_relcrmid = ?';
        $sql['autos']    = 'SELECT * FROM `vtiger_autospotquote` WHERE `estimate_id` = ? AND NULLIF(`registration_number`, "") IS NULL';
        $sql['auto_bulky'] = 'SELECT * FROM `vtiger_quotes_vehicles` WHERE `estimateid` = ?';
        $sql['auto_bulky_corp'] = 'SELECT * FROM `vtiger_corporate_vehicles` WHERE `estimate_id` = ?';
        $sql['local_tariff'] = 'SELECT * FROM `vtiger_tariffs` WHERE `tariffsid` = (SELECT `effective_tariff` FROM `vtiger_quotes` WHERE `quoteid` = ?)';

        //Gathering all the information
        $stsResource['contactInfo']       = Vtiger_Record_Model::getInstanceById($stsResource['opportunitiesInfo']['contact_id'], 'Contacts')->getData();
        $stsResource['estimateInfo']      = Vtiger_Record_Model::getInstanceById($this->estimateId, 'Estimates')->getData();
        $stsResource['contractInfo']      = $stsResource['estimateInfo']['contract'] != 0 ? Vtiger_Record_Model::getInstanceById($stsResource['estimateInfo']['contract'], 'Contracts')->getData() : null;
        $stsResource['crates']            = $db->pquery($sql['crates'], [$this->estimateId]);
        $stsResource['bulky']             = $db->pquery($sql['bulky'], [$this->estimateId]);
        $stsResource['packing']           = $db->pquery($sql['packing'], [$this->estimateId]);
        $stsResource['vanline']           = $db->pquery($sql['vanline'], [$request->get('agentId')]);
        $stsResource['acc']               = $db->pquery($sql['acc'], [$this->estimateId]);
        $stsResource['segments']          = $db->pquery($sql['segments'], [$this->estimateId]);
        $stsResource['autos']             = $db->pquery($sql['autos'], [$this->estimateId]);
        $stsResource['auto_bulky']        = $db->pquery($sql['auto_bulky'], [$this->estimateId]);
        $stsResource['auto_bulky_corp']   = $db->pquery($sql['auto_bulky_corp'], [$this->estimateId]);
        $stsResource['vehicles']          = $db->pquery($sql['vehicles'], [$this->estimateId])->fetchRow()['sts_vehicles'];
        $stsResource['stops']             = $this->structureStops($db->pquery($sql['stops'], [$this->opportunityId]));
        $stsResource['agents']            = $this->structureAgents(ParticipatingAgents_Module_Model::getParticipants($this->opportunityId));

        //Check if it's Max3/4, if not, it's interstate
        $localRsult = $db->pquery($sql['local_tariff'], [$this->estimateId]);
        $isIntrastate = false;
        if($db->num_rows($localRsult)){
            $isIntrastate = true;
            $stsResource['tariff'] = $localRsult->fetchRow();
            //Fix to remove trailing white spaces which seem to occur on occasions
            $stsResource['tariff']['tariff_name'] = trim($stsResource['tariff']['tariff_name']);

            //"the only smart thing I can say right now is you will have to send the one that has data in it" -Jross 4/10/2017 @ 2:20pm
            $sql['intrastate_valuation'] = 'SELECT * FROM `vtiger_quotes_perunit` WHERE `estimateid` = ? AND ratetype = "Charge Per $100 (Valuation)" AND qty1 > 0 LIMIT 1';

            $result = $db->pquery($sql['intrastate_valuation'], [$this->estimateId]);
            if($db->num_rows($localRsult)){
                $stsResource['intrastate_valuation'] = $result->fetchRow();
            }
        }else{
            $stsResource['tariff'] = $db->pquery($sql['tariff'], [$this->estimateId])->fetchRow();
        }

        parse_str($request->get('autoInfo'), $autoRushOptions);

        $autoRushApplied = false;
        foreach($autoRushOptions as $id => $rush) {
            if($rush) {
                $result = $db->pquery("UPDATE vtiger_autospotquote SET auto_rush_fee = 100 WHERE autospotquoteid=?", [$id]);
                if($result) {
                    $autoRushApplied = true;
                }
                else {
                    $response = new Vtiger_Response();
                    $response->setResult("Error: Unable to apply auto rush fee to auto spot quote.");
                    $response->emit();
                }
            }
        }
        // I mean, I don't like using this same code, but I also am unsure how else to do it.
        $ratingObject = new Estimates_GetDetailedRate_Action;
        if ($ratingObject && $autoRushApplied) {
            //create an array to pass to rating.
            $ratingStuff = [
                'pseudoSave' => false,
                    'record' => $this->estimateId,
            ];

            $vt_request  = new Vtiger_Request($ratingStuff);
            //capture the output buffer because the emit does an echo
            ob_start();
            //for reasons!
            require_once('libraries/MoveCrm/arrayBuilder.php');
            require_once('libraries/MoveCrm/xmlBuilder.php');
            $ratingObject->process($vt_request);
            $return = ob_get_contents();
            //$return is the json that rating returns. we could parse this for errors or just like do nothing.
            //stop output buffering.
            ob_end_clean();
        }

        //Gathering and formatting segments information
        $formattedSegments = [];
        while ($row =& $stsResource['segments']->fetchRow()) {
            $formattedSegments[intval($row['addresssegments_sequence'])] = $row;
        }
        $stsResource['segments'] = $formattedSegments;
        if (!array_key_exists(1, $stsResource['segments'])) {
            $response = new Vtiger_Response();
            $response->setResult('Error: There is no primary segment for your estimate.');

            $response->emit();
            return;
        }

        //Some logging to help debug
        file_put_contents('logs/STS.log', "\nSegment info: " . print_r($stsResource['segments'], true) . "\n\n", FILE_APPEND);
        file_put_contents('logs/STS.log', "\nAgents info: " . print_r($stsResource['agents'], true) . "\n\n", FILE_APPEND);
        file_put_contents('logs/STS.log', "\nStops info: " . print_r($stsResource['stops'], true) . "\n\n", FILE_APPEND);

        //Begin cunstruction of STS object to be converted to JSON
        $stsMessage = new stdClass();

        //Top-level information
        $stsMessage->AFFILIATE_ORDER_NUMBER = $this->opportunityId;
        $stsMessage->AGENT_ID               = $stsResource['opportunitiesInfo']['brand'] == 'AVL' ? $stsResource['user']['sts_agent_id'] : $stsResource['user']['sts_agent_id_nvl'];
        $stsMessage->USER_NAME              = $stsResource['opportunitiesInfo']['brand'] == 'AVL' ? $stsResource['user']['sts_user_id'] : $stsResource['user']['sts_user_id_nvl'];
        $stsMessage->SHIPPER_LAST_NME       = $stsResource['contactInfo']['lastname'];
        $stsMessage->SHIPPER_FIRST_NME      = $stsResource['contactInfo']['firstname'];
        $stsMessage->SHIPPER_PHONE_NBR      = $stsResource['contactInfo']['phone'];
        $stsMessage->CONSIGNEE_LAST_NME     = $stsResource['contactInfo']['lastname'];
        $stsMessage->CONSIGNEE_FIRST_NME    = $stsResource['contactInfo']['firstname'];
        $stsMessage->CONSIGNEE_PHN_NBR      = $stsResource['contactInfo']['phone'];
        $stsMessage->BOOK_SVC_PROV_NBR      = $stsResource['agents']['ba']['agent_number'];
        $stsMessage->BOOK_SVC_PROV_TYPE_CDE = 'A';
        $stsMessage->SHPMT_TYPE_CDE         = 'HHG';
        $stsMessage->SHIPPER_TYPE_CDE       = $this->getShipperTypeCode($stsResource['opportunitiesInfo']['business_channel']);
        $stsMessage->HAUL_AUTH_TYPE_CDE     = $this->getHaulAuthTypeCode($stsResource['opportunitiesInfo']['move_type']);
        $stsMessage->PAYMENT_TYPE_CDE       = $stsResource['opportunitiesInfo']['payment_type_sts'];
        if($stsResource['tariff']['tariff_name'] == 'MAX3'){
            $stsMessage->CUST_AGRMT_NBR = 'Max3';
            $stsMessage->SUB_AGRMT_NBR  = '001';
        }elseif($stsResource['tariff']['tariff_name'] == 'MAX4'){
            $stsMessage->CUST_AGRMT_NBR = 'Max4';
            $stsMessage->SUB_AGRMT_NBR  = '002';
        }elseif ($stsResource['opportunitiesInfo']['business_channel'] != 'Consumer') {
            if ($stsResource['opportunitiesInfo']['agmt_id'] == '' || $stsResource['opportunitiesInfo']['subagmt_nbr'] == '') {
                $response = new Vtiger_Response();
                $response->setResult('Error: Please select an Agmt. ID and a sub-agmt. Number');

                $response->emit();
                return;
            }
            $stsMessage->CUST_AGRMT_NBR         = $stsResource['opportunitiesInfo']['agmt_id'];
            $stsMessage->SUB_AGRMT_NBR          = $stsResource['opportunitiesInfo']['subagmt_nbr'];
        } else {
            $stsMessage->CUST_AGRMT_NBR         = $stsResource['opportunitiesInfo']['agrmt_cod'];
            $stsMessage->SUB_AGRMT_NBR          = $stsResource['opportunitiesInfo']['subagrmt_cod'];
        }

        //Some flags for the last steps of this process.
        $curlResponse = false;
        $autoOnly = $stsMessage->CUST_AGRMT_NBR == '204-A' ? true : false;
        $preReg = $stsResource['opportunitiesInfo']['register_sts_number'] == '' ? false : true;

        $stsMessage->DISCOUNT_PCT           = $stsResource['estimateInfo']['bottom_line_discount'];
        if ($stsResource['opportunitiesInfo']['agrmt_cod'] == 'UAS' && $stsMessage->DISCOUNT_PCT == 0.00) {
            $error = $error . "- Your estimate's bottom line discount is $0. Please update your discount.<br />";
        }
        $stsMessage->EST_CHG_AMT            = number_format((float)$stsResource['estimateInfo']['hdnGrandTotal'], 2, '.', '');
        $stsMessage->EST_TYPE               = $stsResource['estimateInfo']['estimate_type'];
        if ($stsMessage->EST_TYPE == 'Binding' && $stsMessage->EST_CHG_AMT == 0.00) {
            $error = $error . "- You cannot have a $0 charge for a binding estimate. Please ensure your estimate is rated.<br />";
        }
        $stsMessage->PEAK_SEASON_IND        = $stsResource['estimateInfo']['pricing_type'] == 'Peak' ? 'Y' : 'N';
        $stsMessage->PRODUCT_CDE            = '';
        $stsMessage->PRODUCT_TYPE_CDE       = '';
        $stsMessage->SELF_HAUL_IND          = $stsResource['opportunitiesInfo']['self_haul'] == '1' ? 'Y' : 'N';
        $stsMessage->SALESPERSON_ID         = $stsResource['opportunitiesInfo']['brand'] == 'AVL' ? $stsResource['salesperson']['sts_salesperson_avl'] : $stsResource['salesperson']['sts_salesperson_navl'];
        $stsMessage->ESTIMATOR_ID           = $stsResource['opportunitiesInfo']['brand'] == 'AVL' ? $stsResource['salesperson']['sts_salesperson_avl'] : $stsResource['salesperson']['sts_salesperson_navl'];
        if($stsMessage->SALESPERSON_ID == ''){
            $stsMessage->SALESPERSON_ID = '0000';
        }
        if($stsMessage->ESTIMATOR_ID == ''){
            $stsMessage->ESTIMATOR_ID = '0000';
        }
        $stsMessage->CBS_IND                = $stsResource['opportunitiesInfo']['cbs_ind'] == '1' ? 'Y' : 'N';
        $stsMessage->PACK_LOAD_HAUL_IND     = 'N';
        $stsMessage->CWT_REDUCTN_RATE       = $stsResource['estimateInfo']['grr_override'] == 1 ? $stsResource['estimateInfo']['grr_override_amount'] : $stsResource['estimateInfo']['grr'];
        $stsMessage->NTE_PACK_PRICE_AMT     = $stsResource['estimateInfo']['grr_estimate'];
        $stsMessage->SP_SCAC                = $stsResource['opportunitiesInfo']['move_type'] == 'Sirva Military' ? 'SIKM' : '';
        $stsMessage->LOAD_TMO               = $stsResource['opportunitiesInfo']['move_type'] == 'Sirva Military' ? 'BGNC' : '';
        $stsMessage->DLVY_TMO               = $stsResource['opportunitiesInfo']['move_type'] == 'Sirva Military' ? 'JEAT' : '';
        $stsMessage->MOVE_SOURCE_CDE        = $stsResource['opportunitiesInfo']['move_type'] == 'Sirva Military' ? 'I' : '';
        $stsMessage->CUST_CARE_CDE          = $stsResource['opportunitiesInfo']['move_type'] == 'Sirva Military' ? 'M' : '';
        $stsMessage->FULL_PACK_SVC_IND      = $stsResource['estimateInfo']['full_pack'] == '0' ? 'N' : 'Y';
        $stsMessage->MOVE_COORDINATOR       = $stsResource['user']['mcid'];
        $stsMessage->PAYMENT_METHOD         = $stsResource['opportunitiesInfo']['payment_method'];
        $stsMessage->SYSTEM_ID              = $stsResource['opportunitiesInfo']['brand'];
        $stsMessage->CR_REQ_CHECK_IND       = '';
        if($isIntrastate){
            $stsMessage->VALUATION_TYPE = 1;
            $stsMessage->DECLARED_VALUE = number_format($stsResource['intrastate_valuation']['qty1'], 2, '.', '');
            $stsMessage->VAL_PER_LB     = 0;
        }else{
            $stsMessage->VALUATION_TYPE = $this->mapValuationType($stsResource['estimateInfo']['valuation_deductible']);
            $stsMessage->DECLARED_VALUE = $stsMessage->VALUATION_TYPE == 'B' ? 0 : number_format($stsResource['estimateInfo']['valuation_amount'], 2, '.', '');
            $stsMessage->VAL_PER_LB     = $stsMessage->VALUATION_TYPE != 'B' && !$stsMessage->DECLARED_VALUE ? '0.60' : 0;
        }
        $stsMessage->BL_NBR                 = '9999999';
        $stsMessage->SHIPMENT_MILES         = $stsResource['estimateInfo']['interstate_mileage'];
        $stsMessage->RCVD_INIT              = 'ADM';
        $stsMessage->MARKETING_PGM          = 'N';
        $stsMessage->EST_METHOD             = 'M';

        $stsMessage->CUST_LCTN_NBR          = $stsResource['opportunitiesInfo']['payment_type_sts'] == 'CHG' ? $stsResource['opportunitiesInfo']['billing_apn'] : '';
        $stsMessage->CUST_NBR               = $stsResource['opportunitiesInfo']['payment_type_sts'] == 'CHG' ? $stsResource['opportunitiesInfo']['national_account_number'] : '';

        if (
                $stsResource['opportunitiesInfo']['self_haul'] == '1' &&
                $stsResource['agents']['ha'] &&
                $stsResource['opportunitiesInfo']['brand'] == 'NVL'
        ) {
            $stsMessage->SELF_HAUL_AGENT        = $stsResource['agents']['ha']['agent_number'];
            $stsMessage->SELF_HAUL_AGENT_TYPE   = 'A';
        }

        //Ref number; Used in the future?
        //Note: The future, is now!
        $stsMessage->REF_NUMBER[0] = new stdClass();
        $stsMessage->REF_NUMBER[0]->CUST_REF_NBR      = $stsResource['opportunitiesInfo']['ref_number'];
        $stsMessage->REF_NUMBER[0]->CUST_REF_TYPE_CDE = $stsResource['opportunitiesInfo']['ref_type'];

        //Used to check load and pack dates
        $todaysDate = strtotime('today');

        //Begin segments section. This has 1 default segment (MP to MD) with additional opitonal segments(extra stops) located in the proceeding foreach.
        //The first segment extracts the MP to MD
        $segmentResource = $stsResource['segments'][1];
        unset($stsResource['segments'][1]);

        $segment = new stdClass();

        $segment->SEG_NBR                     = 1;
        $segment->EST_WGT                     = intval($segmentResource['addresssegments_weightoverride']) ? intval($segmentResource['addresssegments_weightoverride']) : intval($segmentResource['addresssegments_weight']);
        $segment->EST_CUBES                   = intval($segmentResource['addresssegments_cubeoverride']) ? intval($segmentResource['addresssegments_cubeoverride']) : intval($segmentResource['addresssegments_cube']);
        $segment->AGRD_LD_FROM_HOUR           = '08';
        $segment->AGRD_LD_FROM_MIN            = '00';

        // Below are the rules to determine what dates are sent and when to validate them.
        //  •No dates – Will advise
        //  •One date only.  Can be either the Load From, Load To, Del From, or Del To when miles are 500 or greater.
        //  •All 4 dates within transit guide
        //  •All 4 dates – any dates when miles are < 500.

        $dates = ['load_date', 'load_to_date', 'deliver_date', 'deliver_to_date'];
        $dateCount = 0;

        foreach ($dates as $date) {
            if($stsResource['opportunitiesInfo'][$date] != ''){
                $dateCount++;
            }
        }

        if ($dateCount) {
            $longDistance = (intval($stsResource['estimateInfo']['interstate_mileage']) > 500);

            $segment->AGRD_LD_TO_DATE = date('mdy', strtotime($stsResource['opportunitiesInfo']['load_to_date']));
            if($dateCount < 4 && $stsResource['opportunitiesInfo']['load_to_date'] == '' && $longDistance){
                unset($segment->AGRD_LD_TO_DATE);
            }
            elseif (strtotime($stsResource['opportunitiesInfo']['load_to_date']) < $todaysDate && !$autoOnly) {
                $error = $error . "- Your load to date is missing or before today's date.<br />";
            }

            $segment->AGRD_LD_FROM_DATE = date('mdy', strtotime($stsResource['opportunitiesInfo']['load_date']));
            if($dateCount < 4 && $stsResource['opportunitiesInfo']['load_date'] == '' && $longDistance){
                unset($segment->AGRD_LD_FROM_DATE);
            }
            elseif (strtotime($stsResource['opportunitiesInfo']['load_date']) < $todaysDate && !$autoOnly) {
                $error = $error . "- Your load date is missing or before today's date.<br />";
            }

            $segment->AGRD_DL_FROM_DATE = date('mdy', strtotime($stsResource['opportunitiesInfo']['deliver_date']));
            if($dateCount < 4 && $stsResource['opportunitiesInfo']['deliver_date'] == '' && $longDistance){
                unset($segment->AGRD_DL_FROM_DATE);
            }
            elseif (strtotime($stsResource['opportunitiesInfo']['deliver_date']) < $todaysDate && !$autoOnly) {
                $error = $error . "- Your deliver date is missing or before today's date.<br />";
            }

            $segment->AGRD_DL_TO_DATE = date('mdy', strtotime($stsResource['opportunitiesInfo']['deliver_to_date']));
            if($dateCount < 4 && $stsResource['opportunitiesInfo']['deliver_to_date'] == '' && $longDistance){
                unset($segment->AGRD_DL_TO_DATE);
            }
            elseif (strtotime($stsResource['opportunitiesInfo']['deliver_to_date']) < $todaysDate && !$autoOnly) {
                $error = $error . "- Your deliver to date is missing or before today's date.<br />";
            }
        }

        if ($stsResource['opportunitiesInfo']['pack_date'] != '') {
            $segment->PACK_FROM_DATE = date('mdy', strtotime($stsResource['opportunitiesInfo']['pack_date']));
        }
        if ($stsResource['opportunitiesInfo']['pack_to_date'] != '') {
            $segment->PACK_TO_DATE = date('mdy', strtotime($stsResource['opportunitiesInfo']['pack_to_date']));
        }
        if ($stsResource['estimateInfo']['shipper_type'] == 'NAT') {
            $stsMessage->SURVEY_DATE = date('mdy');
        } elseif ($stsResource['estimateInfo']['interstate_effective_date'] != '') {
            $stsMessage->SURVEY_DATE            = date('mdy', strtotime($stsResource['estimateInfo']['interstate_effective_date']));
        } else {
            $error = $error . "- Your rating date is required.<br />";
        }
        if (!$isIntrastate && $stsResource['estimateInfo']['shipper_type'] != 'NAT' && strtotime($stsResource['estimateInfo']['validtill']) < $todaysDate) {
            $error = $error . "- The estimate you are trying to register is expired.<br />";
        }
        $segment->AGRD_LD_TO_HOUR             = '10';
        $segment->AGRD_LD_TO_MIN              = '00';
        $segment->AGRD_DL_FROM_HOUR           = '08';
        $segment->AGRD_DL_FROM_MIN            = '00';
        $segment->AGRD_DL_TO_HOUR             = '10';
        $segment->AGRD_DL_TO_MIN              = '00';
        $segment->ORIG_LCTN_SPLC              = '';
        $segment->ORIG_LCTN_ADDR1             = $stsResource['estimateInfo']['origin_address1'];
        $segment->ORIG_LCTN_ADD2              = $stsResource['estimateInfo']['origin_address2'];
        $segment->ORIG_LCTN_CITY_NME          = str_replace('-', ' ', $stsResource['estimateInfo']['origin_city']);
        $segment->ORIG_LCTN_COUNTY_NME        = $stsResource['estimateInfo']['estimates_origin_county'];
        $segment->ORIG_LCTN_ST_CDE            = $stsResource['estimateInfo']['origin_state'];
        $segment->ORIG_LCTN_COUNTRY_CDE       = $this->translateCountryCode($stsResource['estimateInfo']['estimates_origin_country']);
        $segment->ORIG_LCTN_ZIP_CODE          = str_replace(' ', '', $stsResource['estimateInfo']['origin_zip']);
        $segment->ORIG_LCTN_SITE_CDE          = 'RES';
        $segment->ORIG_LCTN_TYPE_CDE          = 'MP';
        $segment->ORIG_PRIM_CNTCT_NME         = 'SAME';
        $segment->ORIG_PRIM_CNTCT_PHN_NBR     = $stsResource['contactInfo']['phone'];
        $segment->ORIG_PRIM_CNTCT_ADDR1       = $segment->ORIG_LCTN_ADDR1;
        $segment->ORIG_PRIM_CNTCT_ADDR2       = $segment->ORIG_LCTN_ADD2;
        $segment->ORIG_PRIM_CNTCT_CITY_NME    = str_replace('-', ' ', $segment->ORIG_LCTN_CITY_NME);
        $segment->ORIG_PRIM_CNTCT_ST_PR_CDE   = $segment->ORIG_LCTN_ST_CDE;
        $segment->ORIG_PRIM_CNTCT_ZIP_CDE     = $segment->ORIG_LCTN_ZIP_CODE;
        $segment->ORIG_PRIM_CNTCT_COUNTRY_CDE = $segment->ORIG_LCTN_COUNTRY_CDE;
        $segment->DEST_LCTN_SPLC              = '';
        $segment->DEST_LCTN_ADDR1             = $stsResource['estimateInfo']['destination_address1'];
        $segment->DEST_LCTN_ADDR2             = $stsResource['estimateInfo']['destination_address2'];
        $segment->DEST_LCTN_CITY_NME          = str_replace('-', ' ', $stsResource['estimateInfo']['destination_city']);
        $segment->DEST_LCTN_COUNTY_NME        = $stsResource['estimateInfo']['estimates_destination_county'];
        $segment->DEST_LCTN_ST_CDE            = $stsResource['estimateInfo']['destination_state'];
        $segment->DEST_LCTN_COUNTRY_CDE       = $this->translateCountryCode($stsResource['estimateInfo']['estimates_destination_country']);
        $segment->DEST_LCTN_ZIP_CODE          = str_replace(' ', '', $stsResource['estimateInfo']['destination_zip']);
        $segment->DEST_LCTN_SITE_CDE          = 'RES';
        $segment->DEST_LCTN_TYPE_CDE          = 'MD';
        if(
            $stsResource['tariff']['custom_tariff_type'] == 'TPG' ||
            $stsResource['tariff']['custom_tariff_type'] == 'TPG GRR' ||
            $stsResource['tariff']['custom_tariff_type'] == 'Pricelock' ||
            $stsResource['tariff']['custom_tariff_type'] == 'Pricelock GRR' ||
            $stsResource['tariff']['custom_tariff_type'] == 'Allied Express' ||
            $stsResource['tariff']['custom_tariff_type'] == 'Blue Express'
        ){
            $segment->DEST_OT_IND             = ($stsResource['estimateInfo']['accesorial_ot_unloading']  == '0' ? 'N' : 'Y');
            $segment->ORIG_OT_IND             = ($stsResource['estimateInfo']['accesorial_ot_loading']  == '0' ? 'N' : 'Y');
        } else{
            $segment->DEST_OT_IND             = ($stsResource['estimateInfo']['acc_ot_dest_applied']  == '0' ? 'N' : 'Y');
            $segment->ORIG_OT_IND             = ($stsResource['estimateInfo']['acc_ot_origin_applied']  == '0' ? 'N' : 'Y');
        }
        $segment->DEST_PRIM_CNTCT_NME         = 'SAME';
        $segment->DEST_PRIM_CNTCT_PHN_NBR     = $stsResource['contactInfo']['phone'];
        $segment->DEST_PRIM_CNTCT_ADDR1       = $segment->DEST_LCTN_ADDR1;
        $segment->DEST_PRIM_CNTCT_ADDR2       = $segment->DEST_LCTN_ADDR2;
        $segment->DEST_PRIM_CNTCT_CITY_NME    = str_replace('-', ' ', $segment->DEST_LCTN_CITY_NME);
        $segment->DEST_PRIM_CNTCT_ST_PR_CDE   = $segment->DEST_LCTN_ST_CDE;
        $segment->DEST_PRIM_CNTCT_ZIP_CDE     = $segment->DEST_LCTN_ZIP_CODE;
        $segment->DEST_PRIM_CNTCT_COUNTRY_CDE = $segment->DEST_LCTN_COUNTRY_CDE;
        $segment->OASV_SVC_PROV_NBR           = $stsResource['agents']['oa']['agent_number'];
        $segment->OASV_SVC_PROV_TYPE_CDE      = 'A';
        $segment->EASV_SVC_PROV_NBR           = $stsResource['agents']['ea']['agent_number'];
        $segment->EASV_SVC_PROV_TYPE_CDE      = 'A';
        $segment->DASV_SVC_PROV_NBR           = $stsResource['agents']['da']['agent_number'];
        $segment->DASV_SVC_PROV_TYPE_CDE      = 'A';

        if($db->num_rows($stsResource['autos']) <= 0) {
            $segment->ORIG_SIT_DISC_PCT           = $stsResource['estimateInfo']['sit_disc'];
            $segment->ORIG_SIT_SVC_PROV_NBR       = $stsResource['agents']['oa']['agent_number'];
            $segment->ORIG_SIT_SVC_PROV_TYPE_CDE  = 'A';

            if ($segment->ORIG_SIT_ST_PROV_CDE) {
                $segment->ORIG_SIT_ST_PROV_CDE        = $this->getStateAbbrByPost($stsResource['estimateInfo']['sit_origin_zip']);
            }
            $segment->DEST_SIT_DISC_PCT           = $stsResource['estimateInfo']['sit_disc'];
            $segment->DEST_SIT_SVC_PROV_NBR       = $stsResource['agents']['da']['agent_number'];
            $segment->DEST_SIT_SVC_PROV_TYPE_CDE  = 'A';

            if ($segment->DEST_SIT_ST_PROV_CDE) {
                $segment->DEST_SIT_ST_PROV_CDE        = $this->getStateAbbrByPost($stsResource['estimateInfo']['sit_dest_zip']);
            }
        }

        if ($stsResource['opportunitiesInfo']['booker_split'] != '') {
            $segment->SPLIT_AMT_PER  = intval($stsResource['opportunitiesInfo']['booker_split']);
        }
        // I think this is right, since the ticket mentions Split Booking under the same field in STS.
        if ($stsResource['opportunitiesInfo']['origin_split'] != '') {
            $segment->SPLIT_AMT_PER  = intval($stsResource['opportunitiesInfo']['origin_split']);
        }

        //List of items that need to be skipped
        //I sure hope these titles don't change.
        $skipAutoList = [
            '4 X 4 Vehicle',
            'Automobile',
            'Pickup Truck',
            'Limousine',
            'Van',
            'Pickup & Camper',
        ];

        //Items section(bulkies)
        while ($row =& $stsResource['bulky']->fetchRow()) {
            if(!in_array($row['label'], $skipAutoList) && intval($row['ship_qty'])){
                $code = $this->translateBulkyItemCode($row['bulkyid']);
                for ($i=intval($row['ship_qty']); $i > 0; $i--) {
                    $item = new stdClass();

                    $item->ITEM_TYPE_CDE = $code;
                    $item->ITEM_QTY      = 1;
                    $item->ITEM_WGT      = '';
                    $item->ITEM_LENGTH   = '';
                    $item->ITEM_WDTH     = '';
                    $item->ITEM_HGT      = '';
                    //$item->ITEM_MAKE     = '';
                    //$item->ITEM_MODEL    = '';
                    //$item->ITEM_YEAR     = '';

                    $segment->ITEM[] = $item;
                }
            }
        }

        //Auto(bulkies)
        while ($row =& $stsResource['auto_bulky']->fetchRow()) {
            $item = new stdClass();

            $item->ITEM_TYPE_CDE = 'VEHICLE';
            $item->ITEM_QTY      = 1;
            $item->ITEM_WGT      = $row['weight'];
            $item->ITEM_LENGTH   = '';
            $item->ITEM_WDTH     = '';
            $item->ITEM_HGT      = '';
            $item->ITEM_MAKE     = $row['make'];
            $item->ITEM_MODEL    = $row['model'];
            $item->ITEM_YEAR     = $row['year'];

            $segment->ITEM[] = $item;
        }

        //Auto(bulkies) for corp vehicles
        while ($row =& $stsResource['auto_bulky_corp']->fetchRow()) {
            $item = new stdClass();

            $item->ITEM_TYPE_CDE = 'VEHICLE';
            $item->ITEM_QTY      = $row['shipping_count'];
            $item->ITEM_WGT      = $row['weight'];
            $item->ITEM_LENGTH   = $row['length'];
            $item->ITEM_WDTH     = $row['width'];
            $item->ITEM_HGT      = $row['height'];
            $item->ITEM_MAKE     = $row['make'];
            $item->ITEM_MODEL    = $row['model'];
            $item->ITEM_YEAR     = $row['year'];

            $segment->ITEM[] = $item;
        }

        $crates = false;
        //Crates section
        while ($row =& $stsResource['crates']->fetchRow()) {
            $crates = true;
            $crate = new stdClass();

            $crate->CRATE_LENGTH                 = $row['length'];
            $crate->CRATE_WIDTH                  = $row['width'];
            $crate->CRATE_HEIGHT                 = $row['height'];
            $crate->CRATE_PACK_QTY               = $row['pack'];
            $crate->CRATE_PACK_SVC_PROV_NBR      = '';
            $crate->CRATE_PACK_SVC_PROV_TYPE_CDE = '';
            $crate->CRATE_PACK_OT_IND            = $row['ot_pack'] > 0 ? 'Y' : 'N';
            $crate->CRATE_PACK_BOUND_IND         = $row['pack'] > 0 ? 'Y' : 'N';
            $crate->CRATE_UNPK_QTY               = $row['unpack'];
            $crate->CRATE_UNPK_OT_IND            = $row['ot_unpack'] > 0 ? 'Y' : 'N';
            $crate->CRATE_UNPK_BOUND_IND         = $row['ot_unpack'] > 0 ? 'Y' : 'N';
            $crate->CRATE_UNPK_MIN_IND           = '';

            $segment->CRATE[] = $crate;
        }

        //TPG-PRICELOCK-GRR-EXPRESS (for both brands)
        $sendRate = (
                        $stsResource['tariff']['custom_tariff_type'] == 'TPG' ||
                        $stsResource['tariff']['custom_tariff_type'] == 'TPG GRR' ||
                        $stsResource['tariff']['custom_tariff_type'] == 'Pricelock' ||
                        $stsResource['tariff']['custom_tariff_type'] == 'Pricelock GRR' ||
                        $stsResource['tariff']['custom_tariff_type'] == 'Allied Express' ||
                        $stsResource['tariff']['custom_tariff_type'] == 'Blue Express'
                    );
        if ($sendRate) {
            $rates = json_decode(base64_decode($stsResource['estimateInfo']['pack_rates']));
            if (!is_array($rates->PackItem)) {
                $singleArray = $rates->PackItem;
                $rates->PackItem = [$rates->PackItem];
            }
            $sortedRates = [];
            foreach ($rates->PackItem as $value) {
                $value->ItemID = ($value->ItemID == 103 ? 102 : $value->ItemID);
                $sortedRates[$value->ItemID] = $value;
            }
        }

        //Crate rates
        if ($crates && $sendRate) {
            $guar = new stdClass();

            $guar->CONT_TYPE_CDE  = "Q";
            $guar->PACK_GUAR_RATE = number_format($sortedRates['R']->PackingDiscounted, 2, '.', '');

            $stsMessage->GUARANTEED_RATE[] = $guar;

            $guar = new stdClass();

            $guar->CONT_TYPE_CDE  = "R";
            $guar->PACK_GUAR_RATE = number_format($sortedRates['R']->PackingDiscounted, 2, '.', '');

            $stsMessage->GUARANTEED_RATE[] = $guar;
        }

        //Containers section (packing)
        while ($row =& $stsResource['packing']->fetchRow()) {
            if (intval($row['pack_qty']) || intval($row['unpack_qty']) || intval($row['pack_cont_qty'])) {
                $container = new stdClass();

                $container->CONTAINER_TYP_CDE = $this->translateContainerCode($row['itemid'], ($stsMessage->SHIPPER_TYPE_CDE  == 'NAT' || $stsMessage->CUST_AGRMT_NBR == 'UAS'));

                //Don't send packing for any matresses or covers
                $sendPack = !(($row['itemid'] == '14' || $row['itemid'] == '13' || $row['itemid'] == '10' || $row['itemid'] == '7' || $row['itemid'] == '6' || $row['itemid'] == '17') && $sendRate);

                if (intval($row['pack_cont_qty']) && !($stsMessage->SHIPPER_TYPE_CDE  == 'NAT' || $stsMessage->CUST_AGRMT_NBR == 'UAS')) {
                    $container->CONTAINER_QTY     = $row['pack_cont_qty'];
                }
                if (intval($row['pack_qty']) && $sendPack) {
                    $container->PACK_QTY          = $row['pack_qty'];
                    $container->PACK_OT_IND       = intval($row['ot_pack_qty']) > 0 ? 'Y' : 'N';
                    if ($stsMessage->SHIPPER_TYPE_CDE  == 'NAT' || $stsMessage->CUST_AGRMT_NBR == 'UAS') {
                        $container->CONTAINER_QTY = $row['pack_qty'];
                    }
                }
                if (intval($row['unpack_qty']) && $sendPack) {
                    $container->UNPACK_QTY        = $row['unpack_qty'];
                    $container->UNPACK_OT_IND     = intval($row['ot_unpack_qty']) > 0 ? 'Y' : 'N';
                }

                if ($stsResource['tariff']['custom_tariff_type'] == 'Blue Express' || $stsResource['tariff']['custom_tariff_type'] == 'Allied Express') {
                    $container->CONTAINER_BOUND_IND = 'Y';
                }

                $container->PACK_BOUND_IND = $stsResource['tariff']['custom_tariff_type'] == 'Blue Express' || $stsResource['tariff']['custom_tariff_type'] == 'Allied Express' ? 'Y' : '';

                $segment->CONTAINER[] = $container;

                //Send pack rate
                if ($sendRate) {
                    if (isset($sortedRates[$row['itemid']]) && (floatval($sortedRates[$row['itemid']]->Packing) > 0 || floatval($sortedRates[$row['itemid']]->Container) > 0)) {
                        $guar = new stdClass();

                        $guar->CONT_TYPE_CDE           = $container->CONTAINER_TYP_CDE;
                        if (floatval($sortedRates[$row['itemid']]->Container) > 0) {
                            $guar->CONT_GUAR_RATE      = number_format(($sortedRates[$row['itemid']]->ContainerDiscounted / $container->CONTAINER_QTY), 2, '.', '');
                        }
                        if (floatval($sortedRates[$row['itemid']]->Packing) > 0) {
                            $guar->PACK_GUAR_RATE      = number_format(($sortedRates[$row['itemid']]->PackingDiscounted / $container->PACK_QTY), 2, '.', '');
                        }

                        $stsMessage->GUARANTEED_RATE[] = $guar;
                    }
                }
            }
        }

        //Day certain
        if ($stsResource['estimateInfo']['acc_day_certain_pickup']) {
            $otherServiceOrig = new stdClass();
            $otherServiceOrig->OTHER_SERVICE_CDE       = 'DCPU';
            $otherServiceOrig->OTHER_SVC_PROV_NBR      = $stsResource['agents']['oa']['agent_number'];
            $otherServiceOrig->OTHER_SVC_PROV_TYPE_CDE = 'A';
            $otherServiceOrig->OTHER_DISC_PREM_PCT     = 0;
            $otherServiceOrig->OTHER_BOUND_IND         = 'Y';
            $otherServiceOrig->OTHER_CHARGE_AMT        = $stsResource['estimateInfo']['acc_day_certain_fee'];
            $otherServiceOrig->OTHER_DR_CR_CDE         = 'C';
            $segment->ORIG_OTHER_SERVICE[]             = $otherServiceOrig;
        }

        //SIT section
        if (intval($stsResource['estimateInfo']['sit_origin_weight']) > 0 && $stsResource['estimateInfo']['sit_origin_date_in'] != '') {
            $otherServiceOrig = new stdClass();
            $otherServiceOrig->SIT_LEVEL            = 'I';
            $otherServiceOrig->SIT_IN_DATE          = date('mdy', strtotime($stsResource['estimateInfo']['sit_origin_date_in']));
            $otherServiceOrig->SIT_OUT_DATE         = $stsResource['estimateInfo']['sit_origin_pickup_date'] == '' ? date('mdy', strtotime($stsResource['estimateInfo']['sit_origin_date_in'])) : date('mdy', strtotime($stsResource['estimateInfo']['sit_origin_pickup_date']));
            $otherServiceOrig->REQ_OUT_DATE         = date('mdy', strtotime($stsResource['estimateInfo']['sit_origin_date_in'] . "+" . $stsResource['estimateInfo']['sit_origin_number_days']." days"));
            $otherServiceOrig->SIT_WEIGHT           = $stsResource['estimateInfo']['sit_origin_weight'];
            $otherServiceOrig->SIT_BOUND_IND        = '';
            $otherServiceOrig->CARTAGE_OT_CDE       = $stsResource['estimateInfo']['sit_origin_overtime'];
            $otherServiceOrig->CARTAGE_SVC_PROV_NBR = $stsResource['agents']['oa']['agent_number'];
            $otherServiceOrig->CARTAGE_SP_TYPE_CDE  = 'A';
            $otherServiceOrig->CARTAGE_MILES        = $stsResource['estimateInfo']['sit_origin_miles'];
            $otherServiceOrig->CARTAGE_BOUND_IND    = '';
            $segment->ORIG_SIT_OCCURRENCE[]       = $otherServiceOrig;
        }

        if (intval($stsResource['estimateInfo']['sit_dest_weight']) > 0 && $stsResource['estimateInfo']['sit_dest_date_in'] != '') {
            $otherServiceDest = new stdClass();
            $otherServiceDest->SIT_LEVEL            = 'I';
            $otherServiceDest->SIT_IN_DATE          = date('mdy', strtotime($stsResource['estimateInfo']['sit_dest_date_in']));
            $otherServiceDest->SIT_OUT_DATE         = $stsResource['estimateInfo']['sit_dest_delivery_date'] == '' ? date('mdy', strtotime($stsResource['estimateInfo']['sit_dest_date_in'])) : date('mdy', strtotime($stsResource['estimateInfo']['sit_dest_delivery_date']));
            $otherServiceDest->REQ_OUT_DATE         = date('mdy', strtotime($stsResource['estimateInfo']['sit_dest_date_in'] . "+" . $stsResource['estimateInfo']['sit_dest_number_days']." days"));
            $otherServiceDest->SIT_WEIGHT           = $stsResource['estimateInfo']['sit_dest_weight'];
            $otherServiceDest->SIT_BOUND_IND        = '';
            $otherServiceDest->CARTAGE_OT_CDE       = $stsResource['estimateInfo']['sit_dest_overtime'];
            $otherServiceDest->CARTAGE_SVC_PROV_NBR = $stsResource['agents']['da']['agent_number'];
            $otherServiceDest->CARTAGE_SP_TYPE_CDE  = 'A';
            $otherServiceDest->CARTAGE_MILES        = $stsResource['estimateInfo']['sit_dest_miles'];
            $otherServiceDest->CARTAGE_BOUND_IND    = '';
            $segment->DEST_SIT_OCCURRENCE[]       = $otherServiceDest;
        }

        //Shuttle service
        if ($stsResource['estimateInfo']['acc_shuttle_origin_applied'] && !($stsResource['tariff']['custom_tariff_type'] == 'Allied Express' || $stsResource['tariff']['custom_tariff_type'] == 'Blue Express')) {
            $otherServiceOrig = new stdClass();
            $otherServiceOrig->OTHER_SERVICE_CDE   = 'AUXF';
            $otherServiceOrig->OTHER_DISC_PREM_PCT = 0;
            $otherServiceOrig->OTHER_PARM_NAME1    = 'SERVICE-WGT';
            $otherServiceOrig->OTHER_PARM_VALUE1   = $stsResource['estimateInfo']['acc_shuttle_origin_weight'];
            $otherServiceOrig->OTHER_PARM_NAME2    = 'MILES';
            $otherServiceOrig->OTHER_PARM_VALUE2   = ($stsMessage->SHIPPER_TYPE_CDE  == 'NAT' || $stsMessage->CUST_AGRMT_NBR == 'UAS') ? $stsResource['estimateInfo']['acc_shuttle_origin_miles'] : 10;
            $otherServiceOrig->OTHER_PARM_NAME3    = 'OT-IND';
            $otherServiceOrig->OTHER_PARM_VALUE3   = $stsResource['estimateInfo']['acc_shuttle_origin_ot'] == '1' ? 'Y' : 'N';
            $segment->ORIG_OTHER_SERVICE[]       = $otherServiceOrig;
        }
        if ($stsResource['estimateInfo']['acc_shuttle_dest_applied'] && !($stsResource['tariff']['custom_tariff_type'] == 'Allied Express' || $stsResource['tariff']['custom_tariff_type'] == 'Blue Express')) {
            $otherServiceDest = new stdClass();
            $otherServiceDest->OTHER_SERVICE_CDE   = 'AUXF';
            $otherServiceDest->OTHER_DISC_PREM_PC  = 0;
            $otherServiceDest->OTHER_PARM_NAME1    = 'SERVICE-WGT';
            $otherServiceDest->OTHER_PARM_VALUE1   = $stsResource['estimateInfo']['acc_shuttle_dest_weight'];
            $otherServiceDest->OTHER_PARM_NAME2    = 'MILES';
            $otherServiceDest->OTHER_PARM_VALUE2   = ($stsMessage->SHIPPER_TYPE_CDE  == 'NAT' || $stsMessage->CUST_AGRMT_NBR == 'UAS') ? $stsResource['estimateInfo']['acc_shuttle_dest_miles'] : 10;
            $otherServiceDest->OTHER_PARM_NAME3    = 'OT-IND';
            $otherServiceDest->OTHER_PARM_VALUE3   = $stsResource['estimateInfo']['acc_shuttle_dest_ot'] == '1' ? 'Y' : 'N';
            $segment->DEST_OTHER_SERVICE[]       = $otherServiceDest;
        }

        //OT service
        if ($stsMessage->SHIPPER_TYPE_CDE  == 'NAT' || $stsMessage->CUST_AGRMT_NBR == 'UAS') {
            if ($stsResource['estimateInfo']['accesorial_ot_loading']) {
                $otherServiceOrig = new stdClass();
                $otherServiceOrig->OTHER_SERVICE_CDE   = 'OTLU';
                $otherServiceOrig->OTHER_DISC_PREM_PCT = 0;
                $otherServiceOrig->OTHER_CHARGE_AMT    = 0;
                $otherServiceOrig->OTHER_DR_CR_CDE     = 'C';
                $otherServiceOrig->OTHER_PARM_NAME1    = 'LD/ULD WGT';
                $otherServiceOrig->OTHER_PARM_VALUE1   = $stsResource['estimateInfo']['weight'];
                $segment->ORIG_OTHER_SERVICE[]       = $otherServiceOrig;
            }
            if ($stsResource['estimateInfo']['accesorial_ot_unloading']) {
                $otherServiceDesat = new stdClass();
                $otherServiceDest->OTHER_SERVICE_CDE   = 'OTLU';
                $otherServiceDest->OTHER_DISC_PREM_PCT = 0;
                $otherServiceDest->OTHER_CHARGE_AMT    = 0;
                $otherServiceDest->OTHER_DR_CR_CDE     = 'C';
                $otherServiceDest->OTHER_PARM_NAME1    = 'LD/ULD WGT';
                $otherServiceDest->OTHER_PARM_VALUE1   = $stsResource['estimateInfo']['weight'];
                $segment->DEST_OTHER_SERVICE[]       = $otherServiceDest;
            }
        }

        //Wait time
        if (intval($stsResource['estimateInfo']['acc_wait_origin_hours']) > 0) {
            $otherServiceOrig = new stdClass();
            $otherServiceOrig->OTHER_SERVICE_CDE   = 'LABR';
            $otherServiceOrig->OTHER_SVC_PROV_NBR  = $stsResource['agents']['oa']['agent_number'];
            $otherServiceOrig->OTHER_SVC_TYPE_CDE  = 'A';
            $otherServiceOrig->OTHER_DISC_PREM_PCT = 0;
            $otherServiceOrig->OTHER_PARM_NAME1    = 'LABR-HRS';
            $otherServiceOrig->OTHER_PARM_VALUE1   = $stsResource['estimateInfo']['acc_wait_origin_hours'];
            $otherServiceOrig->OTHER_PARM_NAME2    = 'NBR-MEN';
            $otherServiceOrig->OTHER_PARM_VALUE2   = 1;
            $otherServiceOrig->OTHER_PARM_NAME3    = 'OT-IND';
            $otherServiceOrig->OTHER_PARM_VALUE3   = "N";
            if ($stsMessage->SHIPPER_TYPE_CDE  == 'NAT' || $stsMessage->CUST_AGRMT_NBR == 'UAS') {
                $otherServiceOrig->OTHER_QUALIFIER_CDE = 'GENERAL';
            }
            $segment->ORIG_OTHER_SERVICE[]         = $otherServiceOrig;
        }
        if (intval($stsResource['estimateInfo']['acc_wait_ot_origin_hours']) > 0) {
            $otherServiceOrig = new stdClass();
            $otherServiceOrig->OTHER_SERVICE_CDE   = 'LABR';
            $otherServiceOrig->OTHER_SVC_PROV_NBR  = $stsResource['agents']['oa']['agent_number'];
            $otherServiceOrig->OTHER_SVC_TYPE_CDE  = 'A';
            $otherServiceOrig->OTHER_PARM_NAME1    = 'LABR-HRS';
            $otherServiceOrig->OTHER_PARM_VALUE1   = $stsResource['estimateInfo']['acc_wait_ot_origin_hours'];
            $otherServiceOrig->OTHER_PARM_NAME2    = 'NBR-MEN';
            $otherServiceOrig->OTHER_PARM_VALUE2   = 1;
            $otherServiceOrig->OTHER_PARM_NAME3    = 'OT-IND';
            $otherServiceOrig->OTHER_PARM_VALUE3   = "Y";
            $otherServiceOrig->OTHER_DISC_PREM_PCT = 0;
            if ($stsMessage->SHIPPER_TYPE_CDE  == 'NAT' || $stsMessage->CUST_AGRMT_NBR == 'UAS') {
                $otherServiceOrig->OTHER_QUALIFIER_CDE = 'GENERAL';
            }
            $segment->ORIG_OTHER_SERVICE[]         = $otherServiceOrig;
        }

        if (intval($stsResource['estimateInfo']['acc_wait_dest_hours']) > 0) {
            $otherServiceDest = new stdClass();
            $otherServiceDest->OTHER_SERVICE_CDE   = 'LABR';
            $otherServiceDest->OTHER_SVC_PROV_NBR  = $stsResource['agents']['da']['agent_number'];
            $otherServiceDest->OTHER_SVC_TYPE_CDE  = 'A';
            $otherServiceDest->OTHER_PARM_NAME1    = 'LABR-HRS';
            $otherServiceDest->OTHER_PARM_VALUE1   = $stsResource['estimateInfo']['acc_wait_dest_hours'];
            $otherServiceDest->OTHER_PARM_NAME2    = 'NBR-MEN';
            $otherServiceDest->OTHER_PARM_VALUE2   = 1;
            $otherServiceDest->OTHER_PARM_NAME3    = 'OT-IND';
            $otherServiceDest->OTHER_PARM_VALUE3   = "N";
            $otherServiceDest->OTHER_DISC_PREM_PCT = 0;
            if ($stsMessage->SHIPPER_TYPE_CDE  == 'NAT' || $stsMessage->CUST_AGRMT_NBR == 'UAS') {
                $otherServiceDest->OTHER_QUALIFIER_CDE = 'GENERAL';
            }
            $segment->DEST_OTHER_SERVICE[]         = $otherServiceDest;
        }
        if (intval($stsResource['estimateInfo']['acc_wait_ot_dest_hours']) > 0) {
            $otherServiceDest = new stdClass();
            $otherServiceDest->OTHER_SERVICE_CDE   = 'LABR';
            $otherServiceDest->OTHER_SVC_PROV_NBR  = $stsResource['agents']['da']['agent_number'];
            $otherServiceDest->OTHER_SVC_TYPE_CDE  = 'A';
            $otherServiceDest->OTHER_PARM_NAME1    = 'LABR-HRS';
            $otherServiceDest->OTHER_PARM_VALUE1   = $stsResource['estimateInfo']['acc_wait_ot_dest_hours'];
            $otherServiceDest->OTHER_PARM_NAME2    = 'NBR-MEN';
            $otherServiceDest->OTHER_PARM_VALUE2   = 1;
            $otherServiceDest->OTHER_PARM_NAME3    = 'OT-IND';
            $otherServiceDest->OTHER_PARM_VALUE3   = "Y";
            $otherServiceDest->OTHER_DISC_PREM_PCT = 0;
            if ($stsMessage->SHIPPER_TYPE_CDE  == 'NAT' || $stsMessage->CUST_AGRMT_NBR == 'UAS') {
                $otherServiceDest->OTHER_QUALIFIER_CDE = 'GENERAL';
            }
            $segment->DEST_OTHER_SERVICE[]         = $otherServiceDest;
        }

        //Extra labor
        if (intval($stsResource['estimateInfo']['acc_exlabor_origin_hours']) > 0) {
            $otherServiceOrig = new stdClass();
            $otherServiceOrig->OTHER_SERVICE_CDE   = 'LABR';
            $otherServiceOrig->OTHER_SVC_PROV_NBR  = $stsResource['agents']['oa']['agent_number'];
            $otherServiceOrig->OTHER_SVC_TYPE_CDE  = 'A';
            $otherServiceOrig->OTHER_DISC_PREM_PCT = 0;
            $otherServiceOrig->OTHER_PARM_NAME1    = 'LABR-HRS';
            $otherServiceOrig->OTHER_PARM_VALUE1   = $stsResource['estimateInfo']['acc_exlabor_origin_hours'];
            $otherServiceOrig->OTHER_PARM_NAME2    = 'NBR-MEN';
            $otherServiceOrig->OTHER_PARM_VALUE2   = 1;
            $otherServiceOrig->OTHER_PARM_NAME3    = 'OT-IND';
            $otherServiceOrig->OTHER_PARM_VALUE3   = "N";
            if ($stsMessage->SHIPPER_TYPE_CDE  == 'NAT' || $stsMessage->CUST_AGRMT_NBR == 'UAS') {
                $otherServiceOrig->OTHER_QUALIFIER_CDE = 'GENERAL';
            }
            $segment->ORIG_OTHER_SERVICE[]         = $otherServiceOrig;
        }
        if (intval($stsResource['estimateInfo']['acc_exlabor_ot_origin_hours']) > 0) {
            $otherServiceOrig = new stdClass();
            $otherServiceOrig->OTHER_SERVICE_CDE   = 'LABR';
            $otherServiceOrig->OTHER_SVC_PROV_NBR  = $stsResource['agents']['oa']['agent_number'];
            $otherServiceOrig->OTHER_SVC_TYPE_CDE  = 'A';
            $otherServiceOrig->OTHER_PARM_NAME1    = 'LABR-HRS';
            $otherServiceOrig->OTHER_PARM_VALUE1   = $stsResource['estimateInfo']['acc_exlabor_ot_origin_hours'];
            $otherServiceOrig->OTHER_PARM_NAME2    = 'NBR-MEN';
            $otherServiceOrig->OTHER_PARM_VALUE2   = 1;
            $otherServiceOrig->OTHER_PARM_NAME3    = 'OT-IND';
            $otherServiceOrig->OTHER_PARM_VALUE3   = "Y";
            $otherServiceOrig->OTHER_DISC_PREM_PCT = 0;
            if ($stsMessage->SHIPPER_TYPE_CDE  == 'NAT' || $stsMessage->CUST_AGRMT_NBR == 'UAS') {
                $otherServiceOrig->OTHER_QUALIFIER_CDE = 'GENERAL';
            }
            $segment->ORIG_OTHER_SERVICE[]         = $otherServiceOrig;
        }

        if (intval($stsResource['estimateInfo']['acc_exlabor_dest_hours']) > 0) {
            $otherServiceDest = new stdClass();
            $otherServiceDest->OTHER_SERVICE_CDE   = 'LABR';
            $otherServiceDest->OTHER_SVC_PROV_NBR  = $stsResource['agents']['da']['agent_number'];
            $otherServiceDest->OTHER_SVC_TYPE_CDE  = 'A';
            $otherServiceDest->OTHER_PARM_NAME1    = 'LABR-HRS';
            $otherServiceDest->OTHER_PARM_VALUE1   = $stsResource['estimateInfo']['acc_exlabor_dest_hours'];
            $otherServiceDest->OTHER_PARM_NAME2    = 'NBR-MEN';
            $otherServiceDest->OTHER_PARM_VALUE2   = 1;
            $otherServiceDest->OTHER_PARM_NAME3    = 'OT-IND';
            $otherServiceDest->OTHER_PARM_VALUE3   = "N";
            $otherServiceDest->OTHER_DISC_PREM_PCT = 0;
            if ($stsMessage->SHIPPER_TYPE_CDE  == 'NAT' || $stsMessage->CUST_AGRMT_NBR == 'UAS') {
                $otherServiceDest->OTHER_QUALIFIER_CDE = 'GENERAL';
            }
            $segment->DEST_OTHER_SERVICE[]         = $otherServiceDest;
        }
        if (intval($stsResource['estimateInfo']['acc_exlabor_ot_dest_hours']) > 0) {
            $otherServiceDest = new stdClass();
            $otherServiceDest->OTHER_SERVICE_CDE   = 'LABR';
            $otherServiceDest->OTHER_SVC_PROV_NBR  = $stsResource['agents']['da']['agent_number'];
            $otherServiceDest->OTHER_SVC_TYPE_CDE  = 'A';
            $otherServiceDest->OTHER_PARM_NAME1    = 'LABR-HRS';
            $otherServiceDest->OTHER_PARM_VALUE1   = $stsResource['estimateInfo']['acc_exlabor_ot_dest_hours'];
            $otherServiceDest->OTHER_PARM_NAME2    = 'NBR-MEN';
            $otherServiceDest->OTHER_PARM_VALUE2   = 1;
            $otherServiceDest->OTHER_PARM_NAME3    = 'OT-IND';
            $otherServiceDest->OTHER_PARM_VALUE3   = "Y";
            $otherServiceDest->OTHER_DISC_PREM_PCT = 0;
            if ($stsMessage->SHIPPER_TYPE_CDE  == 'NAT' || $stsMessage->CUST_AGRMT_NBR == 'UAS') {
                $otherServiceDest->OTHER_QUALIFIER_CDE = 'GENERAL';
            }
            $segment->DEST_OTHER_SERVICE[]         = $otherServiceDest;
        }

        //Self/Mini Storage
        if ($stsResource['estimateInfo']['acc_selfstg_origin_applied']) {
            $otherServiceOrig = new stdClass();
            $otherServiceOrig->OTHER_SERVICE_CDE   = 'WHPD';
            $otherServiceOrig->OTHER_SVC_PROV_NBR  = $stsResource['agents']['oa']['agent_number'];
            $otherServiceOrig->OTHER_SVC_TYPE_CDE  = 'A';
            $otherServiceOrig->OTHER_DISC_PREM_PCT = 0;
            $otherServiceOrig->OTHER_PARM_NAME1    = 'SERVICE-WGT';
            $otherServiceOrig->OTHER_PARM_VALUE1   = $stsResource['estimateInfo']['acc_selfstg_origin_weight'];
            $otherServiceOrig->OTHER_PARM_NAME2    = 'OT-IND';
            $otherServiceOrig->OTHER_PARM_VALUE2   = $stsResource['estimateInfo']['acc_selfstg_origin_ot'] == '1' ? 'Y' : 'N';
            $segment->ORIG_OTHER_SERVICE[]         = $otherServiceOrig;
        }
        if ($stsResource['estimateInfo']['acc_selfstg_dest_applied']) {
            $otherServiceDest = new stdClass();
            $otherServiceDest->OTHER_SERVICE_CDE   = 'WHPD';
            $otherServiceDest->OTHER_SVC_PROV_NBR  = $stsResource['agents']['da']['agent_number'];
            $otherServiceDest->OTHER_SVC_TYPE_CDE  = 'A';
            $otherServiceDest->OTHER_DISC_PREM_PCT = 0;
            $otherServiceDest->OTHER_PARM_NAME1    = 'SERVICE-WGT';
            $otherServiceDest->OTHER_PARM_VALUE1   = $stsResource['estimateInfo']['acc_selfstg_dest_weight'];
            $otherServiceDest->OTHER_PARM_NAME2    = 'OT-IND';
            $otherServiceDest->OTHER_PARM_VALUE2   = $stsResource['estimateInfo']['acc_selfstg_dest _ot'] == '1' ? 'Y' : 'N';
            $segment->DEST_OTHER_SERVICE[]         = $otherServiceDest;
        }

        //Flat/QTY Misc Items
        while ($row =& $stsResource['acc']->fetchRow()) {
            $charge   = $row['charge'];
            $qty      = $row['qty'] != '' ? $row['qty'] : 1;
            $discount = $row['discounted'] && ($row['discount'] != '' || $row['discount'] != 0) ? ($row['discount'] / 100) : 1;

            $otherServiceOrig = new stdClass();
            $otherServiceOrig->OTHER_SERVICE_CDE   = 'ADVC';
            $otherServiceOrig->OTHER_SVC_PROV_NBR  = $stsResource['agents']['oa']['agent_number'];
            $otherServiceOrig->OTHER_SVC_TYPE_CDE  = 'A';
            $otherServiceOrig->OTHER_DISC_PREM_PCT = 0;
            //if ($stsMessage->SHIPPER_TYPE_CDE  == 'NAT' || $stsMessage->CUST_AGRMT_NBR == 'UAS') {
                $otherServiceOrig->OTHER_QUALIFIER_CDE = 'ADVC';
            //}
            $otherServiceOrig->OTHER_CHARGE_AMT    = $charge * $qty * $discount;
            if($otherServiceOrig->OTHER_CHARGE_AMT > 0){
            $otherServiceOrig->OTHER_DR_CR_CDE     = 'C';
            } else{
                $otherServiceOrig->OTHER_DR_CR_CDE     = 'D';
                $otherServiceOrig->OTHER_CHARGE_AMT = abs($otherServiceOrig->OTHER_CHARGE_AMT);
            }
            $segment->ORIG_OTHER_SERVICE[]       = $otherServiceOrig;
        }

        //local_origin_acc
        if (intval($stsResource['estimateInfo']['local_origin_acc']) > 0) {
            $otherServiceOrig = new stdClass();
            $otherServiceOrig->OTHER_SERVICE_CDE   = 'ADVC';
            $otherServiceOrig->OTHER_SVC_PROV_NBR  = $stsResource['agents']['oa']['agent_number'];
            $otherServiceOrig->OTHER_SVC_TYPE_CDE  = 'A';
            $otherServiceOrig->OTHER_DISC_PREM_PCT = 0;
            $otherServiceOrig->OTHER_QUALIFIER_CDE = 'LCDC';
            $otherServiceOrig->OTHER_CHARGE_AMT    = $stsResource['estimateInfo']['local_origin_acc'];
            $otherServiceOrig->OTHER_DR_CR_CDE     = 'C';
            $segment->ORIG_OTHER_SERVICE[]       = $otherServiceOrig;
        }

        //expedited service
        if ($stsResource['estimateInfo']['accesorial_expedited_service']){
            $otherServiceOrig = new stdClass();
            $otherServiceOrig->OTHER_SERVICE_CDE   = 'XSVC';
            $otherServiceOrig->OTHER_DISC_PREM_PCT = 0;
            $otherServiceOrig->OTHER_DR_CR_CDE     = 'C';
            $segment->ORIG_OTHER_SERVICE[]       = $otherServiceOrig;
        }

        $stsMessage->SEGMENT[] = $segment;

        //All remaining segments that are not the (MP to MD). AKA: extra stops
        foreach ($stsResource['segments'] as $segmentNumber => $segmentResource) {
            $segmentStopOrigin = $this->translateCubeSheetStops($segmentResource['addresssegments_origin']);
            $segmentStopDestination = $this->translateCubeSheetStops($segmentResource['addresssegments_destination']);

            //Fail if we do not have a match for destination and origin between segments and extra stops
            if ((!array_key_exists($segmentStopOrigin, $stsResource['stops']) && $segmentStopOrigin != 'MP') || (!array_key_exists($segmentStopDestination, $stsResource['stops']) && $segmentStopDestination != 'MD')) {
                $response = new Vtiger_Response();
                $response->setResult('Error: Your segments and extra stops do not match.');

                $response->emit();
                return;
            }

            $segmentResource['originInfo'] = $stsResource['stops'][$segmentStopOrigin];
            $segmentResource['destinationInfo'] = $stsResource['stops'][$segmentStopDestination];

            $segmentResource['originInfo']['extrastops_contact'] = intval($segmentResource['originInfo']['stop_contact']) ? Vtiger_Record_Model::getInstanceById($segmentResource['originInfo']['stop_contact'], 'Contacts')->getData() : false;
            $segmentResource['destinationInfo']['extrastops_contact'] = intval($segmentResource['destinationInfo']['stop_contact']) ? Vtiger_Record_Model::getInstanceById($segmentResource['destinationInfo']['stop_contact'], 'Contacts')->getData() : [];

            $segment = new stdClass();
            $segment->SEG_NBR                     = $segmentNumber;
            $segment->EST_WGT                     = intval($segmentResource['addresssegments_weightoverride']) ? intval($segmentResource['addresssegments_weightoverride']) : intval($segmentResource['addresssegments_weight']);
            $segment->EST_CUBES                   = intval($segmentResource['addresssegments_cubeoverride']) ? intval($segmentResource['addresssegments_cubeoverride']) : intval($segmentResource['addresssegments_cube']);
            $segment->ORIG_LCTN_SPLC              = '';
            if ($segmentStopOrigin == 'MP') {
                $segment->ORIG_LCTN_ADDR1         = $stsResource['estimateInfo']['origin_address1'];
                $segment->ORIG_LCTN_ADD2          = $stsResource['estimateInfo']['origin_address2'];
                $segment->ORIG_LCTN_CITY_NME      = str_replace('-', ' ', $stsResource['estimateInfo']['origin_city']);
                $segment->ORIG_LCTN_COUNTY_NME    = '';
                $segment->ORIG_LCTN_ST_CDE        = $stsResource['estimateInfo']['origin_state'];
                $segment->ORIG_LCTN_COUNTRY_CDE   = $this->translateCountryCode($stsResource['estimateInfo']['estimates_origin_count']);
            } else {
                $segment->ORIG_LCTN_ADDR1             = $segmentResource['originInfo']['extrastops_address1'];
                $segment->ORIG_LCTN_ADD2              = $segmentResource['originInfo']['extrastops_address2'];
                $segment->ORIG_LCTN_CITY_NME          = str_replace('-', ' ', $segmentResource['originInfo']['extrastops_city']);
                $segment->ORIG_LCTN_COUNTY_NME        = '';
                $segment->ORIG_LCTN_ST_CDE            = $segmentResource['originInfo']['extrastops_state'];
                $segment->ORIG_LCTN_COUNTRY_CDE       = $this->translateCountryCode($segmentResource['originInfo']['extrastops_country']);
            }
            $segment->ORIG_LCTN_ZIP_CODE          = str_replace(' ', '', $segmentResource['originInfo']['extrastops_zip']);
            $segment->ORIG_LCTN_SITE_CDE          = 'RES';
            $segment->ORIG_LCTN_TYPE_CDE          = $segmentStopOrigin == 'MP' ? 'MP' : 'XP';//preg_replace('/[0-9]+/', '', $segmentStopOrigin);
            $segment->ORIG_OT_IND                 = 'N';

            $segment->ORIG_PRIM_CNTCT_NME         = 'SAME';
            $segment->ORIG_PRIM_CNTCT_PHN_NBR     = $segmentResource['originInfo']['extrastops_contact'] ? $segmentResource['originInfo']['extrastops_contact']['phone'] : '';
            $segment->ORIG_PRIM_CNTCT_ADDR1       = $segment->ORIG_LCTN_ADDR1;
            $segment->ORIG_PRIM_CNTCT_AfDDR2      = $segment->ORIG_LCTN_ADD2;
            $segment->ORIG_PRIM_CNTCT_CITY_NME    = str_replace('-', ' ', $segment->ORIG_LCTN_CITY_NME);
            $segment->ORIG_PRIM_CNTCT_ST_PR_CDE   = $segment->ORIG_LCTN_ST_CDE;
            $segment->ORIG_PRIM_CNTCT_ZIP_CDE     = $segment->ORIG_LCTN_ZIP_CODE;
            $segment->ORIG_PRIM_CNTCT_COUNTRY_CDE = $segment->ORIG_LCTN_COUNTRY_CDE;

            $segment->DEST_LCTN_SPLC              = '';
            if ($segmentStopDestination == 'MD') {
                $segment->DEST_LCTN_ADDR1             = $stsResource['estimateInfo']['destination_address1'];
                $segment->DEST_LCTN_ADDR2             = $stsResource['estimateInfo']['destination_address2'];
                $segment->DEST_LCTN_CITY_NME          = str_replace('-', ' ', $stsResource['estimateInfo']['destination_city']);
                $segment->DEST_LCTN_COUNTY_NME        = '';
                $segment->DEST_LCTN_ST_CDE            = $stsResource['estimateInfo']['destination_state'];
                $segment->DEST_LCTN_COUNTRY_CDE       = $this->translateCountryCode($stsResource['estimateInfo']['destination_origin_count']);
            } else {
                $segment->DEST_LCTN_ADDR1             = $segmentResource['destinationInfo']['extrastops_address1'];
                $segment->DEST_LCTN_ADDR2             = $segmentResource['destinationInfo']['extrastops_address2'];
                $segment->DEST_LCTN_CITY_NME          = str_replace('-', ' ', $segmentResource['destinationInfo']['extrastops_city']);
                $segment->DEST_LCTN_COUNTY_NME        = '';
                $segment->DEST_LCTN_ST_CDE            = $segmentResource['destinationInfo']['extrastops_state'];
                $segment->DEST_LCTN_COUNTRY_CDE       = $this->translateCountryCode($segmentResource['destinationInfo']['extrastops_country']);
            }
            $segment->DEST_LCTN_ZIP_CODE          = str_replace(' ', '', $segmentResource['destinationInfo']['extrastops_zip']);
            $segment->DEST_LCTN_SITE_CDE          = 'RES';
            $segment->DEST_LCTN_TYPE_CDE          = $segmentStopDestination == 'MD' ? 'MD' : 'XD';//preg_replace('/[0-9]+/', '', $segmentStopDestination);
            $segment->DEST_OT_IND                 = 'N';

            $segment->DEST_PRIM_CNTCT_NME         = 'SAME';
            $segment->DEST_PRIM_CNTCT_PHN_NBR     = $segmentResource['destinationInfo']['extrastops_contact'] ? $segmentResource['destinationInfo']['extrastops_contact']['phone'] : '';
            $segment->DEST_PRIM_CNTCT_ADDR1       = $segment->DEST_LCTN_ADDR1;
            $segment->DEST_PRIM_CNTCT_ADDR2       = $segment->DEST_LCTN_ADDR2;
            $segment->DEST_PRIM_CNTCT_CITY_NME    = str_replace('-', ' ', $segment->DEST_LCTN_CITY_NME);
            $segment->DEST_PRIM_CNTCT_ST_PR_CDE   = $segment->DEST_LCTN_ST_CDE;
            $segment->DEST_PRIM_CNTCT_ZIP_CDE     = $segment->DEST_LCTN_ZIP_CODE;
            $segment->DEST_PRIM_CNTCT_COUNTRY_CDE = $segment->DEST_LCTN_COUNTRY_CDE;

            $segment->OASV_SVC_PROV_NBR           = $stsResource['agents']['ba']['agent_number'];
            $segment->OASV_SVC_PROV_TYPE_CDE      = 'A';
            $segment->EASV_SVC_PROV_NBR           = $stsResource['agents']['ea']['agent_number'];
            $segment->EASV_SVC_PROV_TYPE_CDE      = 'A';
            $segment->DASV_SVC_PROV_NBR           = $stsResource['agents']['da']['agent_number'];
            $segment->DASV_SVC_PROV_TYPE_CDE      = 'A';
            $segment->ORIG_SIT_SVC_PROV_NBR       = $stsResource['agents']['oa']['agent_number'];
            $segment->ORIG_SIT_SVC_PROV_TYPE_CDE  = 'A';

            $stsMessage->SEGMENT[] = $segment;
        }

        //Auto Spot Quotes
        $independent_autos = [];
        $autoCount = 0;
        if ($db->num_rows($stsResource['autos'])) {
            while ($row =& $stsResource['autos']->fetchRow()) {
                $auto = new stdClass();

                $quote_info = json_decode(urldecode($row['auto_quote_info']));

                $auto->AUTO_MAKE         = $row['auto_make'];
                $auto->AUTO_MODEL        = $row['auto_model'];
                $auto->AUTO_YEAR         = $row['auto_year'];
                $auto->AUTO_QUOTE_OPT    = $row['auto_quote_select'];
                $auto->AUTO_RUSH         = $row['auto_rush_fee'];
                $auto->AUTO_QUOTE_NBR    = $quote_info->quote_id;
                $auto->AUTO_ADD_ON_PRICE = $row['auto_smf'];
                switch (intval($auto->AUTO_QUOTE_OPT)) {
                    case 4:
                        $autoInfo = $quote_info->rates->two_day_pickup;
                        break;
                    case 3:
                        $autoInfo = $quote_info->rates->four_day_pickup;
                        break;
                    case 2:
                        $autoInfo = $quote_info->rates->seven_day_pickup;
                        break;
                    case 1:
                        $autoInfo = $quote_info->rates->ten_day_pickup;
                        break;
                }

                $auto->AUTO_PRICE = $autoInfo->price;

                //Autos that have the exact same dates need to be registered together
                $autoGroup  = $row['auto_load_from'];
                $autoGroup .= $autoInfo->load_to_date;
                $autoGroup .= $autoInfo->deliver_from_date;
                $autoGroup .= $autoInfo->deliver_to_date;

                $dayInterval = "3";
                $today = new DateTime('NOW');
                $lastChance = new DateTime($row['auto_load_from'] . ' -' . $dayInterval . ' days');
                if($today >= $lastChance) {
                    $dayDiff = $today->diff($lastChance)->format('%a');

                    $response = new Vtiger_Response();
                    $error = 'Error: Auto Spot Quote (' . $auto->AUTO_MAKE . ') cannot be registered, ';
                    if($dayDiff > $dayInterval) {
                        $error .= 'load date has passed.';
                    }
                    else {
                        $error .= 'less than ' . $dayInterval . ' days to load date.';
                    }

                    $response->setResult($error);
                    $response->emit();
                    return;
                }

                $stsMessage->EST_CHG_AMT -= (number_format((float)$auto->AUTO_PRICE, 2, '.', '') + number_format((float)$row['auto_smf'], 2, '.', '') + number_format((float)$row['auto_rush_fee'], 2, '.', ''));

                if($autoRushOptions[$auto_info['autospotquoteid']] === 1){
                    $stsMessage->EST_CHG_AMT -= 100;
                }

                $independent_autos[$autoGroup][] = ['object' => $auto, 'info' => $quote_info, 'load_date' => $row['auto_load_from'], 'id' => $row['autospotquoteid'], 'sts_number' => $row['registration_number']];
                $autoCount++;
            }
        }


        //Default weight for autos only
        if($autoOnly){
            $stsMessage->SEGMENT[0]->EST_WGT   = 3500 * $autoCount;
            $stsMessage->SEGMENT[0]->EST_CUBES = 700 * $autoCount;
            $stsMessage->TRANS_REVENUE_AMT = intval($stsMessage->EST_CHG_AMT);
        }

        //Required fields. We will check to make sure these have values before sending STS
        $requiredFields = [
            'AGENT_ID'               => "Agent ID",
            'SHIPPER_LAST_NME'       => "Shipper's last name",
            'SHIPPER_FIRST_NME'      => "Shipper's first name",
            'SHIPPER_PHONE_NBR'      => "Shipper's phone number (office)",
            'CONSIGNEE_LAST_NME'     => "Consignee's last name",
            'CONSIGNEE_FIRST_NME'    => "Consignee's first name",
            'CONSIGNEE_PHN_NBR'      => "Consignee's phone number",
            'BOOK_SVC_PROV_NBR'      => "Booking agent code",
            'SHIPPER_TYPE_CDE'       => "Shipper type code",
            'HAUL_AUTH_TYPE_CDE'     => "Hauling authority type code",
            'PAYMENT_TYPE_CDE'       => "Payment type code",
            'CUST_AGRMT_NBR'         => "Customer agreement number",
            'SUB_AGRMT_NBR'          => "Sub agreement number",
            'DISCOUNT_PCT'              => "Discount percent",
            'EST_CHG_AMT'            => "Total estimate charge",
            'EST_TYPE'                => "Estimate type",
            //'ESTIMATOR_ID'           => "Estimator ID (code)",
            'CWT_REDUCTN_RATE'       => "CWT reduction rate",
            'SYSTEM_ID'              => "Vanline Brand",
            'SHIPMENT_MILES'         => "Interstate mileage",
            //'SALESPERSON_ID'         => "Salesperson"
        ];
        $requiredFieldsFromSegment =['ORIG_LCTN_ADDR1'             => "Origin address",
            'ORIG_LCTN_CITY_NME'          => "Origin city",
            'ORIG_LCTN_ST_CDE'            => "Origin state",
            'ORIG_LCTN_COUNTRY_CDE'       => "Origin country",
            'ORIG_LCTN_ZIP_CODE'          => "origin postal/zip code",
            'DEST_LCTN_ADDR1'             => "Destination address",
            'DEST_LCTN_CITY_NME'          => "Destination city",
            'DEST_LCTN_ST_CDE'            => "Destination state",
            'DEST_LCTN_COUNTRY_CDE'       => "Destination country",
            'DEST_LCTN_ZIP_CODE'          => "Destination zip",
            'OASV_SVC_PROV_NBR'           => "Booking agent",
            'EASV_SVC_PROV_NBR'           => "Estimating agent",
            'DASV_SVC_PROV_NBR'           => "Destination agent",
        ];

        //Remove HHG required fields that are not applicable to the auto only
        if($autoOnly){
            unset($requiredFields['EST_CHG_AMT']);
        }

        foreach ($requiredFields as $field => $name) {
            if ($stsMessage->$field == '') {
                $error = $error . '-' . $name . '<br />';
            }
        }
        foreach ($requiredFieldsFromSegment as $field => $name) {
            if ($stsMessage->SEGMENT[0]->$field == '') {
                $error = $error . '-' . $name . '<br />';
            }
        }

        //If we have errors, throw a tantrum
        if ($error != '') {
            $response = new Vtiger_Response();
            $response->setResult('Error: The following fields are required or incorrect: <hr />' . $error);

            $response->emit();
            file_put_contents('logs/STS.log', "---------- STS Error ---------- \n" . print_r($error, true) . "\n\n", FILE_APPEND);
            return;
        }

        //Autos only will skip hhg registration. Instead, only do autos
        //Also skip hhg if it has already been registered, but we still have autos that have not registered
        if(!$autoOnly && !$preReg){

            file_put_contents('logs/STS.log', "---------- STS Object HHG ---------- \n" . print_r($stsMessage, true) . "\n\n", FILE_APPEND);

            $stsJSON = json_encode($stsMessage);
            file_put_contents('logs/STS.log', "---------- STS JSON HHG ---------- \n" . print_r($stsJSON, true) . "\n\n", FILE_APPEND);

            //Connect to Sirva and retrieve auth key
            $this->curlAuth = $this->curlPOST('grant_type=client_credentials', getenv('SIRVA_SITE') . '/oauth2/AccessRequest');

            //Using auth key, send sts message to Sirva, and retrieve response
            $curlResponse = $this->curlPOST($stsJSON, getenv('SIRVA_SITE') . '/OM/m0/RegisterShipment', json_decode($this->curlAuth)->access_token, true);

            $apiResponse = json_decode($curlResponse);

            $success = false;

            //Handle the incoming response from Sirva
            $messages = "Message:<br />";
            if (is_array($apiResponse->RESPONSE_MESSAGE)) {
                foreach ($apiResponse->RESPONSE_MESSAGE as $msg) {
                    $messages = $messages . '-' . $msg->RESPONSE_MESSAGE_TEXT . "<br />";
                }
            }

            //This is how we determine if the STS was successfull or not. If we get a number back, we are in the clear.
            //If it is missing or is 'null' then we can assume it has not gone through.
            //For testing, you can comment out the $success = true. This will cause it the register the submission on QIO2
            if (property_exists($apiResponse, 'CAMIS_REG_NBR') && $apiResponse->CAMIS_REG_NBR != null) {
                $success = true;
            }
        } else{
            file_put_contents('logs/STS.log', "---------- No HHG ----------". "\n\n", FILE_APPEND);
            $success = true;
        }

        //Our main registration was successful, this is not an autos only and have not previously registered HHG.
        if ($success) {
            if(!$autoOnly && !$preReg){
                file_put_contents('logs/STS.log', "---------- Success HHG ----------". "\n\n", FILE_APPEND);
                $db->pquery('UPDATE `vtiger_potential` SET `sts_response` = ?, `register_sts` = 1, `register_sts_number` = ?, /*`opportunity_disposition` = "Booked", `sales_stage` = "Closed Won",*/ `registration_date` = CURDATE() WHERE potentialid = ?', ['Processing complete', $apiResponse->CAMIS_REG_NBR, $this->opportunityId]);
            }
            $messages = 'Success';

            //All other autos with diffrent load dates must be registered independently
            file_put_contents('logs/STS.log', "---------- Auto Count: " . $autoCount . " ----------" . "\n\n", FILE_APPEND);
            if (count($independent_autos)) {
                //Auto defaults and other information.
                unset($stsMessage->GUARANTEED_RATE);
                unset($stsMessage->CONTAINER);
                unset($stsMessage->SEGMENT[0]->CONTAINER);
                unset($stsMessage->SEGMENT[0]->ORIG_OTHER_SERVICE);
                unset($stsMessage->SEGMENT[0]->DEST_OTHER_SERVICE);
                unset($stsMessage->SEGMENT[0]->ORIG_SIT_OCCURRENCE);
                unset($stsMessage->SEGMENT[0]->DEST_SIT_OCCURRENCE);
                unset($stsMessage->DISCOUNT_PCT);

                $stsMessage->CUST_AGRMT_NBR       = '204-A';
                $stsMessage->SUB_AGRMT_NBR        = '001';
                $stsMessage->AUTO_VENDOR          = "MONTWAY";
                $stsMessage->AUTO_TG_OVERIDE_FLAG = "Y";
                $stsMessage->SELF_HAUL_IND        = "N";
                $stsMessage->SHIPPER_TYPE_CDE     = 'PVT';
                $stsMessage->PAYMENT_TYPE_CDE     = 'COD';
                $stsMessage->CBS_IND              = 0;
                $stsMessage->PAYMENT_METHOD       = 'CHK';
                $stsMessage->VALUATION_TYPE       = 1;
                $failure                          = false;

                //Process each auto group together
                foreach ($independent_autos as $autoGroup => $autos_info) {

                    //Setup defaults for the group
                    unset($stsMessage->SEGMENT[0]->ITEM);
                    $stsMessage->SEGMENT               = array_intersect_key($stsMessage->SEGMENT, [0]); //Remove all but primary segment
                    $stsMessage->TRANS_REVENUE_AMT     = 0;
                    $stsMessage->AUTOS_ONLY            = [];
                    $stsMessage->SEGMENT[0]->EST_WGT   = 0;
                    $stsMessage->SEGMENT[0]->EST_CUBES = 0;
                    $stsMessage->DECLARED_VALUE        = 0;
                    $stsMessage->EST_CHG_AMT           = 0;
                    $completedAutos                    = [];

                    foreach ($autos_info as $auto_info) {
                        //Skip if we already have already registered this auto or if they want to skip it from the registration prompt
                        if($auto_info['sts_number'] != '' || $autoRushOptions[$auto_info['id']] === 0){
                            file_put_contents('logs/STS.log', "---------- Skipping Auto ----------". "\n\n", FILE_APPEND);
                            continue;
                        }

                        //Add vehicle to the bulky section as well
                        //Guess we don't like this no more
                        //$bulky = new stdClass();

                        //$bulky->ITEM_TYPE_CDE = 'VEHICLE';
                        //$bulky->ITEM_QTY      = 1;
                        //$bulky->ITEM_WGT      = 3500;
                        //$bulky->ITEM_LENGTH   = '';
                        //$bulky->ITEM_WDTH     = '';
                        //$bulky->ITEM_HGT      = '';
                        //$bulky->ITEM_MAKE     = $auto_info['object']->AUTO_MAKE;
                        //$bulky->ITEM_MODEL    = $auto_info['object']->AUTO_MODEL;
                        //$bulky->ITEM_YEAR     = $auto_info['object']->AUTO_YEAR;

                        //$stsMessage->SEGMENT[0]->ITEM[] = $bulky;

                        switch (intval($auto_info['object']->AUTO_QUOTE_OPT)) {
                            case 4:
                                $dates = $auto_info['info']->rates->two_day_pickup;
                                break;
                            case 3:
                                $dates = $auto_info['info']->rates->four_day_pickup;
                                break;
                            case 2:
                                $dates = $auto_info['info']->rates->seven_day_pickup;
                                break;
                            case 1:
                                $dates = $auto_info['info']->rates->ten_day_pickup;
                                break;
                        }

                        $stsMessage->TRANS_REVENUE_AMT += (intval($dates->price) + intval($auto_info['object']->AUTO_RUSH));
                        $auto_info['object']->AUTO_PRICE = $stsMessage->TRANS_REVENUE_AMT;

                        //Add $100 if they requested rush request from the registration prompt
                        if($autoRushOptions[$auto_info['id']] === 1){
                            $stsMessage->TRANS_REVENUE_AMT += 100;
                            $auto_info['object']->AUTO_PRICE+=100;
                            $db->pquery('UPDATE `vtiger_autospotquote` SET `auto_rush_fee` = 100.00 WHERE `autospotquoteid` = ?', [$auto_info['id']]);
                        }

                        $stsMessage->EST_CHG_AMT                   += (intval($dates->price) + intval($auto_info['object']->AUTO_RUSH) + $auto_info['object']->AUTO_ADD_ON_PRICE);
                        $stsMessage->AUTOS_ONLY[]                  = $auto_info['object'];
                        $stsMessage->SEGMENT[0]->AGRD_LD_FROM_DATE = date('mdy', strtotime($auto_info['load_date']));
                        $stsMessage->SEGMENT[0]->AGRD_LD_TO_DATE   = date('mdy', strtotime(str_replace('-', '/', $dates->load_to_date)));
                        $stsMessage->SEGMENT[0]->AGRD_DL_FROM_DATE = date('mdy', strtotime(str_replace('-', '/', $dates->deliver_from_date)));
                        $stsMessage->SEGMENT[0]->AGRD_DL_TO_DATE   = date('mdy', strtotime(str_replace('-', '/', $dates->deliver_to_date)));
                        $stsMessage->SEGMENT[0]->EST_WGT          += 3500;
                        $stsMessage->SEGMENT[0]->EST_CUBES        += 700;
                        $stsMessage->DECLARED_VALUE               += 50000.00;
                        $completedAutos[] = $auto_info['id'];
                    }
                    $response = $this->registerAutoGroup($completedAutos, $stsMessage, $autoOnly);
                    if($response){
                        $failure = $response;
                    }
                }
                if($failure){
                    $messages = $failure;
                }
            }

        } else {
            $db->pquery('UPDATE `vtiger_potential` SET `sts_response` = ? WHERE potentialid = ?', [str_replace('<br />', '', $messages), $this->opportunityId]);
            $messages = "Response from OpenAPI/STS: <br />" . $messages . "<br />";
        }

        $timer['end'] = microtime(true);

        file_put_contents('logs/STS.log', "\nTimes:", FILE_APPEND);
        file_put_contents('logs/STS.log', "\nOverall:".($timer['end'] - $timer['start'])/60, FILE_APPEND);

        if($messages == 'Success'){
            $db->pquery('UPDATE `vtiger_potential`  LEFT JOIN `vtiger_potentialscf` USING(`potentialid`) SET `opportunity_disposition` = "Booked", `sent_to_mobile` = 0 WHERE potentialid = ?', [$this->opportunityId]);

            //Save action will trigger LMP update if needed
            $opp = Vtiger_Record_Model::getInstanceById($this->opportunityId, 'Opportunities');
            $opp->set('sales_stage', 'Closed Won');
            $opp->set('mode', 'edit');
            $opp->save();

            $db->pquery('UPDATE `vtiger_quotes` SET `quotestage` = "Booked" WHERE `quoteid` = ?', [$this->estimateId]);
        }

        $response = new Vtiger_Response();
        $response->setResult($messages);
        $response->emit();
    }

    /**
     *
     * Handle curl connection with Sirva's OpenAPI
     *
     * @param string $post_string
     * @param string $webserviceURL
     * @param string $key
     * @param bool $registerSTS
     * @return JSON
     *
     */
    public function curlPOST($post_string, $webserviceURL, $key = '', $registerSTS = false)
    {
        $ch = curl_init();
        $timer = microtime(true);
        if (!$registerSTS) {
            $headers = [
                'Authorization: Basic ' . getenv('SIRVA_KEY'),
                'Content-Type: application/x-www-form-urlencoded;charset=UTF-8',
            ];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            file_put_contents('logs/STS.log', "---------- Sirva Connection (get auth key) ---------- \nURL: " . $webserviceURL . "\nHeaders: " . print_r($headers, true), FILE_APPEND);
        } else {
            $headers = [
                'Authorization: Bearer ' . $key,
                'Host: ' . parse_url(getenv('SIRVA_SITE'))['host'],
                'Content-Type: application/json',
            ];
            file_put_contents('logs/STS.log', "---------- Sirva Connection (send STS JSON) ---------- \nURL: " . $webserviceURL . "\nHeaders: " . print_r($headers, true), FILE_APPEND);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        curl_setopt($ch, CURLOPT_URL, $webserviceURL);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 900);
        curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
        $curlResult = curl_exec($ch);
        curl_close($ch);

        file_put_contents('logs/STS.log', "Result: " . $curlResult . "\n\n", FILE_APPEND);
        file_put_contents('logs/STS.log', "\nTime:".(microtime(true) - $timer)/60, FILE_APPEND);


        return $curlResult;
    }

    /**
     *
     * Process registration for group of autos
     *
     * @param array $autoList
     * @param object $stsMessage
     * @param int $this->opportunityId
     * @return bool|string
     *
     */
    public function registerAutoGroup($autoList, $stsMessage, $autoOnly){
        $db = $db ?: PearDatabase::getInstance();

        file_put_contents('logs/STS.log', "---------- STS Object for auto group ---------- \n" . print_r($stsMessage, true) . "\n\n", FILE_APPEND);
        $stsJSON = json_encode($stsMessage);
        file_put_contents('logs/STS.log', "---------- STS JSON for auto group ---------- \n" . print_r($stsJSON, true) . "\n\n", FILE_APPEND);

        //Connect to Sirva and retrieve auth key
        if(!$this->curlAuth){
            $this->curlAuth = $this->curlPOST('grant_type=client_credentials', getenv('SIRVA_SITE') . '/oauth2/AccessRequest');
        }

        //Using auth key, send sts message to Sirva, and retrieve response
        $curlResponse = $this->curlPOST($stsJSON, getenv('SIRVA_SITE') . '/OM/m0/RegisterShipment', json_decode($this->curlAuth)->access_token, true);

        $apiResponse = json_decode($curlResponse);

        //These autos registered, so lets update the record
        if (property_exists($apiResponse, 'CAMIS_REG_NBR') && $apiResponse->CAMIS_REG_NBR != null) {
            foreach ($autoList as $autoId){
                $db->pquery("UPDATE `vtiger_autospotquote` SET `registration_number` = ?, `auto_sts_response` = 'Processing complete' WHERE `autospotquoteid` = ?", [$apiResponse->CAMIS_REG_NBR, $autoId]);
                if($autoOnly){
                    $db->pquery('UPDATE `vtiger_potential` SET `sts_response` = ?, `register_sts` = 1, `register_sts_number` = ?, /*`opportunity_disposition` = "Booked", `sales_stage` = "Closed Won", */`registration_date` = CURDATE() WHERE potentialid = ?', ['Processing complete', $apiResponse->CAMIS_REG_NBR, $this->opportunityId]);
                }
            }
            return false;
        } else {
            $messages = "Message:<br />";
            if (is_array($apiResponse->RESPONSE_MESSAGE)) {
                foreach ($apiResponse->RESPONSE_MESSAGE as $msg) {
                    $messages = $messages . '-' . $msg->RESPONSE_MESSAGE_TEXT . "<br />";
                }
            }
            foreach ($autoList as $autoId){
                $db->pquery("UPDATE `vtiger_autospotquote` SET `auto_sts_response` = ? WHERE `autospotquoteid` = ?", [str_replace('<br />', '', $messages), $autoId]);
            }
            return $messages;
        }
    }

    /**
     *
     * Connect to IGC's cubesheet service and retrieve data
     *
     * @param int $cubesheetsid
     * @return soapclient2
     *
     */
    public function getCubeSheetData($cubesheetsid)
    {
        $soapclient = new \soapclient2(getenv('CUBESHEET_SERVICE_URL'), 'wsdl');
        $soapclient->setDefaultRpcParams(true);
        $soapProxy = $soapclient->getProxy();

        return $soapProxy->GetCubesheetDetailsByRelatedRecordId(['relatedRecordID' => $cubesheetsid]);
    }

    /**
     *
     * Structure incoming data agents into a format we can use
     *
     * @param array $agentArray
     * @return array
     *
     */
    public function structureAgents($agentArray)
    {
        $agents['ba'] = array_pop(array_filter($agentArray, function ($sub) {
            if ($sub["agent_type"] == "Booking Agent") {
                return true;
            }
        }));
        $agents['oa'] = array_pop(array_filter($agentArray, function ($sub) {
            if ($sub["agent_type"] == "Origin Agent") {
                return true;
            }
        }));
        $agents['ea'] = array_pop(array_filter($agentArray, function ($sub) {
            if ($sub["agent_type"] == "Estimating Agent") {
                return true;
            }
        }));
        $agents['da'] = array_pop(array_filter($agentArray, function ($sub) {
            if ($sub["agent_type"] == "Destination Agent") {
                return true;
            }
        }));
        $agents['ha'] = array_pop(array_filter($agentArray, function ($sub) {
            if ($sub["agent_type"] == "Hauling Agent") {
                return true;
            }
        }));

        return $agents;
    }

    /**
     *
     * Connect to google and attemp to recieve a states abbreviation from a post code
     *
     * @param string $postCode
     * @return string
     *
     */
    public function getStateAbbrByPost($postCode)
    {
        if (!$postCode) {
            return false;
        }

        $postCode = trim($postCode);

        $jsonObj = json_decode(file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?address=$postCode"));

        $locationInfo = $jsonObj->results[0]->address_components;

        $state = array_filter($locationInfo, function ($sub) {
            if (in_array('administrative_area_level_1', $sub->types)) {
                return true;
            }
        });

        if (!$state) {
            return false;
        }

        return array_pop($state)->short_name;
    }

    /**
     *
     * Ensure contry code follows a convention of 'CAN' or 'USA'
     *
     * @param string $country
     * @return string
     *
     */
    public function translateCountryCode($country)
    {
        switch ($country) {
            case 'Canada':
            case 'canada':
            case 'CAN':
                return 'CAN';
                break;

            case 'United States of America':
            case 'United States':
            case 'USA':
            case 'US':
            case 'usa':
            case 'us':
                return 'USA';
                break;

            default:
                return '';
                break;
        }
    }

    /**
     *
     * Translate our movetype to something Sirva's OpenAPI can understand
     *
     * @param string $moveType
     * @return string
     *
     */
    public function getHaulAuthTypeCode($moveType)
    {
        $authType = null;
        switch ($moveType) {
            case 'Intrastate':
            case 'Variable':
            case 'Local Canada':
            case 'Local US':
            case 'Max 3':
            case 'Max 4':
            case 'Intra-Provincial':
                $authType = 'INTRA';
                break;

            case 'Interstate':
            case 'O&I':
            case 'Sirva Military':
            case 'Inter-Provincial':
            case 'Cross Border':
            case 'Alaska':
            case 'Hawaii':
            case 'International':
                $authType = 'INTER';
                break;

            default:
                return '';
                break;
        }

        return $authType;
    }

    /**
     *
     * Translate our valuation to something Sirva's OpenAPI can understand
     *
     * @param string $valuation
     * @return string
     *
     */
    public function mapValuationType($valuation)
    {
        $valuationType = null;
        switch ($valuation) {
            case '60&cent; /lb.':
                $valuationType = 'B';
                break;

            case 'ECP - $0':
            case 'FVP - $0':
            case 'MVP - $0':
                $valuationType = '1';
                break;

            case 'ECP - $250':
            case 'FVP - $250':
            case 'MVP - $250':
                $valuationType = '2';
                break;

            case 'ECP - $500':
            case 'FVP - $500':
            case 'MVP - $500':
                $valuationType = '3';
                break;

            default:
                $valuationType = 'A';
                break;
        }

        return $valuationType;
    }

    /**
     *
     * Translate our business channel to something Sirva's OpenAPI can understand
     *
     * @param string $businessChannel
     * @return string
     *
     */
    public function getShipperTypeCode($businessChannel)
    {
        switch ($businessChannel) {
            case 'Consumer':
                return 'PVT';
                break;

            case 'Corporate':
            case 'Government':
                return 'NAT';
                break;

            case 'Military':
                return 'MIL';
                break;

            default:
                return '';
                break;
        }
    }

    /**
     *
     * Translate our container ids to something Sirva's OpenAPI can understand
     *
     * @param int $containerId
     * @param bool $is_uas
     * @return string
     *
     */
    public function translateContainerCode($containerId, $is_uas = false)
    {
        switch (intval($containerId)) {
            case 1: // 1.5
                return 'E';
                break;

            case 2: // 3.0
                return 'F';
                break;

            case 3: // 4.5
                return 'G';
                break;

            case 4: // 6.0
                return 'H';
                break;

            case 5: // Book | No match. Set to 'other'
                return '4';
                break;

            case 6: // Crib
                return 'L';
                break;

            case 7: // Double Bed
                return 'M';
                break;

            case 8: // Dish Pack
                return 'A';
                break;

            case 9: // Grandfather Clock
                return 'X';
                break;

            case 10: // King/Queen split
                return $is_uas ? 3 : 'B';
                break;

            case 11: // Lamp
                return 'W';
                break;

            case 12: // Mirror
                return 'D';
                break;

            case 13: // King/Queen
                return 'N';
                break;

            case 14: // Single/Twin
                return $is_uas ? 3 : 'S';
                break;

            case 15: // Wardrobe
                return 'K';
                break;

            case 16: // 6.5
                return 'J';
                break;

            case 17: // Matress Cover
                // @NOTE : It was 1 for some reason, don't know why, they say it's P over email, so they can deal with it.
                //return '1';
                return 'P';
                break;

            case 102: // TV Carton
            case 103: // TV Carton
                return '5';
                break;

            case 510: // Heavey Duty
                return '2';
                break;

            case 509: // Other
                return '1';
                break;

            default:
                return '';
                break;
        }
    }

    /**
     *
     * Translate our bulky ids to something Sirva's OpenAPI can understand
     *
     * @param int $bulkyId
     * @return string
     *
     */
    public function translateBulkyItemCode($bulkyId)
    {
        switch (intval($bulkyId)) {
            case 1:  //4x4 Vehicle
            case 3:  //All Terrain Cycle
            case 22: //Go-Cart
            case 56: //Snow Mobile
            case 38: //Motorbike
            case 39: //Motorcycle
            case 62: //Trailer < 14 Ft
            case 35: //Light/Bulky
            case 33: //Kennel
                return 'SM BULKY';
                break;

            case 2:  //Airplane, Glider
                return 'AIRPLANE';
                break;

            case 5:  //Automobile
            case 67: //Van
                return "VEHICLE";
                break;

            case 8:  //Boat Trailer
                return "BOAT TRLR";
                break;

            case 10: //Boat > 14 Ft
                return "BOAT 14'&>";
                break;

            case 9:  //Boat < 14 Ft
            case 12: //Camper Shell
                return "BOAT/CMPR";
                break;

            case 14: //Canoe < 14 Ft
            case 16: //Dinghy < 14 Ft
            case 29: //Jet Ski
            case 31: //Kayak < 14 Ft
            case 50: //Rowboat < 14 Ft
            case 54: //Skiff < 14 Ft
            case 70: //Windsurfer < 14 Ft
                return "BULK < 14'";
                break;

            case 15: //Canoe > 14 Ft
            case 17: //Dinghy > 14 Ft
            case 30: //Jet Ski > 14 Ft
            case 32: //Kayak > 14 Ft
            case 55: //Skiff > 14 Ft
            case 53: //Scull > 14 Ft
            case 51: //Rowboat > 14 Ft
            case 71: //Windsurfer > 14 Ft
                return "CANOE 14'>";
                break;

            case 6:  //Bath
            case 7:  //Bath > 65 Cu Ft
            case 25: //Hot Tub
            case 26: //Hot Tub > 65 Cu Ft
            case 27: //Jacuzzi
            case 28: //Jacuzzi > 65 Cu Ft
            case 57: //Spa
            case 58: //Spa > 65 Cu Ft
            case 68: //Whirlpool Bath
            case 69: //Whirlpool > 65 Cu
                return "HOT TUBS";
                break;

            case 13: //Camper Trailer
            case 11: //Camper, Truckless
            case 37: //Mini Mobile Home
            case 24: //Horse Trailer
                return "LG CAMPER";
                break;

            case 46: //Pickup & Camper
            case 36: //Limousine
            case 61: //Tractor > 25HP
            case 66: //Utility Truck
                return "LG VEHICLE";
                break;

            case 40: //Piano
            case 41: //Piano, Concert
            case 42: //Piano, Grand
            case 43: //Piano, Spinet
            case 44: //Piano, Upright
            case 45: //Piano, Baby Grand
                return "PIANO";
                break;

            case 52: //Satellite Dish
            case 64: //TV/Radio Dish
            case 34: //Large Tv > 40
                return "SATELLITE";
                break;

            case 4:  //Animal House
            case 18: //Doll House
            case 48: //Playhouse
            case 65: //Utility Shed
            case 59: //Tool Shed
                return "SM BUILDNG";
                break;

            case 23: //Golf Cart
            case 49: //Riding Mower
            case 60: //Tractor < 25HP
                return "SM VEHICLE";
                break;

            case 63: //Trailer > 14 Ft
            case 21: //Farm Trailer
            case 19: //Farm Equipment
            case 20: //Farm Implement
                return "TRAILER";
                break;

            case 47: //Pickup Truck
                return "VEHICLE";
                break;

            default:
                return '';
                break;
        }
    }

    /**
     *
     * Format our request to something easir to work with
     *
     * @param array $request
     * @return array
     *
     */
    public function requestArrayToSTS($request)
    {
        $stsRequest = [];
        foreach ($request as $field) {
            $stsRequest[$field['name']] = $field['value'];
        }
        return $stsRequest;
    }

    /**
     *
     * Format our stops to something easir to work with
     *
     * @param query result $query
     * @return array
     *
     */
    public function structureStops($query)
    {
        $db = $db ?: PearDatabase::getInstance();

        $structuredStops = [];
        if ($db->num_rows($query)) {
            //Note: if stops have the same destination/origin as another, they will be overwritten.
            while ($row =& $query->fetchRow()) {
                $type = $this->translateCubeSheetStops($row['extrastops_type']);
                if ($type) {
                    $structuredStops[$type] = $row;
                }
            }
        }

        return $structuredStops;
    }

    /**
     *
     * Translate our stops code to something Sirva's OpenAPI can understand
     *
     * @param string $stop
     * @return string
     *
     */
    public function translateCubeSheetStops($stop)
    {
        switch ($stop) {
            case 'Destination':
                return 'MD';
                break;
            case 'Origin':
                return 'MP';
                break;
            case 'Extra Pickup 1':
                return 'XP1';
                break;
            case 'Extra Pickup 2':
                return 'XP2';
                break;
            case 'Extra Pickup 3':
                return 'XP3';
                break;
            case 'Extra Pickup 4':
                return 'XP4';
                break;
            case 'Extra Pickup 5':
                return 'XP5';
                break;
            case 'Extra Delivery 1':
                return 'XD1';
                break;
            case 'Extra Delivery 2':
                return 'XD2';
                break;
            case 'Extra Delivery 3':
                return 'XD3';
                break;
            case 'Extra Delivery 4':
                return 'XD4';
                break;
            case 'Extra Delivery 5':
                return 'XD5';
                break;
            case 'O - SIT':
            case 'D - SIT':
                return 'SIT';
                break;
            case 'Self Stg PU':
                return 'STG-O';
                break;
            case 'Self Stg Dlv':
                return 'STG-D';
                break;
            case 'Perm Dlv':
                return 'PRM';
                break;
            default:
                return null;
        }
    }
}
