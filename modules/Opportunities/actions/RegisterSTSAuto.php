<?php
require_once('libraries/nusoap/nusoap.php');
class Opportunities_RegisterSTSAuto_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }

    /*
    * Cunstruct an object for STS registration and convert it to JSON
    * JSON will then be sent to Sirva, then we will expect a
    * response message with the status of our request
    */
    public function process(Vtiger_Request $request)
    {
        $db = $db ?: PearDatabase::getInstance();

        $error = '';

        //initial collection of ids required for the STS object
        $opportunitiesId = $request->get('recordId');
        $estimateId      = $db->getOne("SELECT `quoteid` FROM `vtiger_quotes` where `potentialid` = ${opportunitiesId} AND `is_primary` = '1'");
        $cubesheetId     = $db->getOne("SELECT `cubesheetsid` FROM `vtiger_cubesheets` where `potential_id` = ${opportunitiesId} AND `is_primary` = '1'");

        file_put_contents('logs/STS.log', "\nStarting STS registration for oppID-$opportunitiesId on " . date("F j, Y, g:i a") . "...\n\n");

        $stsResource['user']              = Users_Record_Model::getCurrentUserModel()->getData();
        //STS username/id not set up
        if ($stsResource['user']['sts_user_id'] == '' || $stsResource['user']['sts_agent_id'] == '') {
            $response = new Vtiger_Response();
            $response->setResult('Error: Please set up your STS username and STS agentID in your user profile by clicking <a href="index.php?module=Users&view=PreferenceDetail&record=' . Users_Record_Model::getCurrentUserModel()->get('id') . '">here.</a>');

            $response->emit();
            return;
        }

        //first, check to make sure we have all the ids needed, or we will stop and throw a tantrum
        if ($estimateId == '') {
            $response = new Vtiger_Response();
            $response->setResult('Error: There is no primary estimate for this Opportunity. Please setup a primary Estimate for this Opportunity.');

            $response->emit();
            return;
        }
        if ($cubesheetId == '') {
            $response = new Vtiger_Response();
            $response->setResult('Error: There is no primary survey for this Opportunity. Please setup a primary survey for this Opportunity.');

            $response->emit();
            return;
        }

        $sql['vanline']  = 'SELECT `vtiger_vanlinemanager`.`vanline_id`
							FROM `vtiger_vanlinemanager`
							JOIN `vtiger_agentmanager`
								ON `vtiger_agentmanager`.`vanline_id` = `vtiger_vanlinemanager`.`vanlinemanagerid`
							WHERE `vtiger_agentmanager`.`agency_code` = ?';
        $sql['stops']    = 'SELECT extrastops_sequence, extrastops_date, extrastops_address1, extrastops_address2, extrastops_city, extrastops_state, extrastops_zip, extrastops_country, extrastops_phonetype1, extrastops_phone1, extrastops_phonetype2, extrastops_phone2, extrastops_type, extrastops_contact FROM `vtiger_extrastops` WHERE extrastops_relcrmid = ?';

        //stsResource is an array of information from various sources that will be used to build the STS object
        $stsResource['stsInfo']           = $this->requestArrayToSTS($request->get('stsInfo'));
        $stsResource['opportunitiesInfo'] = Vtiger_Record_Model::getInstanceById($opportunitiesId, 'Opportunities')->getData();
        if ($stsResource['opportunitiesInfo']['contact_id'] == '') {
            $response = new Vtiger_Response();
            $response->setResult('Error: There is no contact for this Opportunity. Please add a contact to the opportunity.');

            $response->emit();
            return;
        }
        $stsResource['contactInfo']       = Vtiger_Record_Model::getInstanceById($stsResource['opportunitiesInfo']['contact_id'], 'Contacts')->getData();
        $stsResource['estimateInfo']      = Vtiger_Record_Model::getInstanceById($estimateId, 'Estimates')->getData();
        $stsResource['contractInfo']      = $stsResource['estimateInfo']['contract'] != 0 ? Vtiger_Record_Model::getInstanceById($stsResource['estimateInfo']['contract'], 'Contracts')->getData() : null;
        $stsResource['cubesheetInfo']     = $this->getCubeSheetData($cubesheetId)['GetCubesheetDetailsByRelatedRecordIdResult']['ExtendedCubesheet'];
        if (!isset($stsResource['cubesheetInfo'][0])) {
            $stsResource['cubesheetInfo'] = [$stsResource['cubesheetInfo']];
        }
        file_put_contents('logs/STS.log', "\nCubesheet info: " . print_r($stsResource['cubesheetInfo'], true) . "\n\n", FILE_APPEND);
        $stsResource['vanline']           = $db->pquery($sql['vanline'], [$request->get('agentId')]);
        $stsResource['stops']             = $this->structureStops($db->pquery($sql['stops'], [$stsResource['opportunitiesInfo']['id']]));

        //Begin cunstruction of STS object to be converted to JSON
        $stsMessage = new stdClass();

        //Top-level information
        $stsMessage->AFFILIATE_ORDER_NUMBER = $opportunitiesId;
        $stsMessage->AGENT_ID               = $stsResource['user']['sts_agent_id'];
        $stsMessage->USER_NAME              = $stsResource['user']['sts_user_id'];
        $stsMessage->SHIPPER_LAST_NME       = $stsResource['contactInfo']['lastname'];
        $stsMessage->SHIPPER_FIRST_NME      = $stsResource['contactInfo']['firstname'];
        $stsMessage->SHIPPER_PHONE_NBR      = $stsResource['contactInfo']['phone'];
        $stsMessage->CONSIGNEE_LAST_NME     = $stsResource['contactInfo']['lastname'];
        $stsMessage->CONSIGNEE_FIRST_NME    = $stsResource['contactInfo']['firstname'];
        $stsMessage->CONSIGNEE_PHN_NBR      = $stsResource['contactInfo']['phone'];
        $stsMessage->BOOK_SVC_PROV_NBR      = $stsResource['stsInfo']['ba_code'];
        $stsMessage->BOOK_SVC_PROV_TYPE_CDE = 'A';
        $stsMessage->SHPMT_TYPE_CDE         = 'HHG';
        $stsMessage->SHIPPER_TYPE_CDE       = $this->getShipperTypeCode($stsResource['opportunitiesInfo']['business_channel']);
        $stsMessage->HAUL_AUTH_TYPE_CDE     = $this->getHaulAuthTypeCode($stsResource['stsInfo']['move_type']);
        $stsMessage->PAYMENT_TYPE_CDE       = $stsResource['stsInfo']['payment_type_sts'];
        if ($stsResource['opportunitiesInfo']['lead_type'] == 'National Account') {
            if ($stsResource['stsInfo']['agmt_id'] == '' || $stsResource['stsInfo']['subagmt_nbr'] == '') {
                $response = new Vtiger_Response();
                $response->setResult('Error: Please select an Agmt. ID and a sub-agmt. Number');

                $response->emit();
                return;
            }
            $stsMessage->CUST_AGRMT_NBR         = Vtiger_Record_Model::getInstanceById($stsResource['stsInfo']['agmt_id'], 'Contracts')->getDisplayValue('contract_no');//$stsResource['stsInfo']['agmt_id'];
            $stsMessage->SUB_AGRMT_NBR          = Vtiger_Record_Model::getInstanceById($stsResource['stsInfo']['subagmt_nbr'], 'Contracts')->getDisplayValue('contract_no');//$stsResource['stsInfo']['subagmt_nbr'];
            //file_put_contents('logs/devLog.log', "\n National Account \n AGRMT: " . $stsMessage->CUST_AGRMT_NBR  . " \n SUB AGRMT: " .$stsMessage->SUB_AGRMT_NBR  , FILE_APPEND);
        } else {
            $stsMessage->CUST_AGRMT_NBR         = $stsResource['stsInfo']['agrmt_cod'];
            $stsMessage->SUB_AGRMT_NBR          = $stsResource['stsInfo']['subagrmt_cod'];
            //file_put_contents('logs/devLog.log', "\n Consumer \n AGRMT: " . $stsMessage->CUST_AGRMT_NBR  . " \n SUB AGRMT: " .$stsMessage->SUB_AGRMT_NBR  , FILE_APPEND);
        }

        $stsMessage->DISCOUNT_PCT           = $stsResource['estimateInfo']['bottom_line_discount'];
        if ($stsResource['stsInfo']['agrmt_cod'] == 'UAS' && $stsMessage->DISCOUNT_PCT == 0.00) {
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
        $stsMessage->SELF_HAUL_IND          = $stsResource['stsInfo']['self_haul'] == 'on' ? 'Y' : 'N';
        $stsMessage->SALESPERSON_ID         = '0000';
        $stsMessage->ESTIMATOR_ID           = '0000';
        $stsMessage->CBS_IND                = $stsResource['opportunitiesInfo']['cbs_ind'] == 'on' ? 'Y' : 'N';
        $stsMessage->PACK_LOAD_HAUL_IND     = 'N';
        $stsMessage->CWT_REDUCTN_RATE       = $stsResource['estimateInfo']['grr_override'] == 1 ? $stsResource['estimateInfo']['grr'] : $stsResource['estimateInfo']['grr_override_amount'];
        $stsMessage->NTE_PACK_PRICE_AMT     = $stsResource['estimateInfo']['grr_cp'];
        $stsMessage->SP_SCAC                = $stsResource['opportunitiesInfo']['move_type'] == 'Sirva Military' ? 'SIKM' : '';
        $stsMessage->LOAD_TMO               = $stsResource['opportunitiesInfo']['move_type'] == 'Sirva Military' ? 'BGNC' : '';
        $stsMessage->DLVY_TMO               = $stsResource['opportunitiesInfo']['move_type'] == 'Sirva Military' ? 'JEAT' : '';
        $stsMessage->MOVE_SOURCE_CDE        = $stsResource['opportunitiesInfo']['move_type'] == 'Sirva Military' ? 'I' : '';
        $stsMessage->CUST_CARE_CDE          = $stsResource['opportunitiesInfo']['move_type'] == 'Sirva Military' ? 'M' : '';
        $stsMessage->FULL_PACK_SVC_IND      = $stsResource['estimateInfo']['full_pack'] == '0' ? 'N' : 'Y';
        $stsMessage->MOVE_COORDINATOR       = '';
        $stsMessage->PAYMENT_METHOD         = $stsResource['stsInfo']['payment_method'];
        $stsMessage->SYSTEM_ID              = $stsResource['vanline'] == '9' ? 'NAVL' : 'AVL';
        $stsMessage->CUST_LCTN_NBR          = '30079446';//isset($stsResource['contractInfo']['nat_account_no']) ? $stsResource['contractInfo']['nat_account_no'] : '';
        $stsMessage->CR_REQ_CHECK_IND       = '';
        $stsMessage->VALUATION_TYPE         = $this->mapValuationType($stsResource['estimateInfo']['valuation_deductible']);
        $stsMessage->DECLARED_VALUE         = $stsResource['estimateInfo']['declared_value'];
        $stsMessage->VAL_PER_LB             = '0.60';//$stsResource['estimateInfo']['valuation_amount'];
        $stsMessage->BL_NBR                 = '9999999';
        $stsMessage->SHIPMENT_MILES         = $stsResource['estimateInfo']['interstate_mileage'];
        $stsMessage->RCVD_INIT              = 'ADM';
        $stsMessage->MARKETING_PGM          = 'N';
        $stsMessage->EST_METHOD             = 'M';

        if (isset($stsResource['contractInfo']['nat_account_no'])) {
            $stsMessage->CUST_NBR = $stsResource['contractInfo']['nat_account_no'];
        }

        if ($stsResource['stsInfo']['ha_code'] != '') {
            $stsMessage->SELF_HAUL_AGENT        = $stsResource['stsInfo']['ha_code'];
            $stsMessage->SELF_HAUL_AGENT_TYPE   = 'A';
        }

        //Ref number; Used in the future?
        $stsMessage->REF_NUMBER[0] = new stdClass();
        $stsMessage->REF_NUMBER[0]->CUST_REF_NBR      = '';
        $stsMessage->REF_NUMBER[0]->CUST_REF_TYPE_CDE = '';

        //Begin segments section. This has 1 default segment (MP to MD) with additional opitonal segments(extra stops) located in the proceeding foreach.
        //The first segment extracts the MP to MD in theis array_filter
        $segmentResource = array_filter($stsResource['cubesheetInfo'], function ($sub) {
            if ($sub['FromLocationType'] == 1 && $sub['ToLocationType'] == 2) {
                return true;
            }
        })[0];
        //Used to check load and pack dates
        $todaysDate                               = date('mdy');
        $segment = new stdClass();

        $segment->SEG_NBR                     = 1;
        $segment->EST_WGT                     = intval($segmentResource['TotalWeight']);
        $segment->EST_CUBES                   = intval($segmentResource['TotalCube']);
        $segment->AGRD_LD_FROM_HOUR           = '08';
        $segment->AGRD_LD_FROM_MIN            = '00';
        $segment->AGRD_LD_FROM_DATE           = date('mdy', strtotime($stsResource['opportunitiesInfo']['load_date']));
        if ($segment->AGRD_LD_FROM_DATE < $todaysDate) {
            $error = $error . "- Your load date is missing or before today's date.<br />";
        }
        if ($stsResource['estimateInfo']['destination_address1'] != 'Will Advise') {
            $segment->AGRD_LD_TO_DATE = date('mdy', strtotime($stsResource['opportunitiesInfo']['load_to_date']));
            if ($segment->AGRD_LD_TO_DATE < $todaysDate) {
                $error = $error . "- Your load to date is missing or before today's date.<br />";
            }
            $segment->AGRD_DL_FROM_DATE = date('mdy', strtotime($stsResource['opportunitiesInfo']['deliver_date']));
            if ($segment->AGRD_DL_FROM_DATE < $todaysDate) {
                $error = $error . "- Your deliver date is missing or before today's date.<br />";
            }
            $segment->AGRD_DL_TO_DATE = date('mdy', strtotime($stsResource['opportunitiesInfo']['deliver_to_date']));
            if ($segment->AGRD_DL_TO_DATE < $todaysDate) {
                $error = $error . "- Your deliver to date is missing or before today's date.<br />";
            }
            $segment->PACK_FROM_DATE = date('mdy', strtotime($stsResource['opportunitiesInfo']['pack_date']));
            if ($segment->PACK_FROM_DATE < $todaysDate) {
                $error = $error . "- Your pack date is missing or before today's date.<br />";
            }
            $segment->PACK_TO_DATE = date('mdy', strtotime($stsResource['opportunitiesInfo']['pack_to_date']));
            if ($segment->PACK_TO_DATE < $todaysDate) {
                $error = $error . "- Your pack to date is missing or before today's date.<br />";
            }
            if ($stsResource['opportunitiesInfo']['survey_date'] != '') {
                $stsMessage->SURVEY_DATE            = date('mdy', strtotime($stsResource['opportunitiesInfo']['survey_date']));
            } else {
                $error = $error . "- Your survey date is required.<br />";
            }
        } else {
            if ($stsResource['opportunitiesInfo']['load_to_date'] != '') {
                $segment->AGRD_LD_TO_DATE = date('mdy', strtotime($stsResource['opportunitiesInfo']['load_to_date']));
            }
            if ($stsResource['opportunitiesInfo']['deliver_date'] != '') {
                $segment->AGRD_DL_FROM_DATE = date('mdy', strtotime($stsResource['opportunitiesInfo']['deliver_date']));
            }
            if ($stsResource['opportunitiesInfo']['deliver_to_date'] != '') {
                $segment->AGRD_DL_TO_DATE = date('mdy', strtotime($stsResource['opportunitiesInfo']['deliver_to_date']));
            }
            if ($stsResource['opportunitiesInfo']['pack_date'] != '') {
                $segment->PACK_FROM_DATE = date('mdy', strtotime($stsResource['opportunitiesInfo']['pack_date']));
            }
            if ($stsResource['opportunitiesInfo']['pack_to_date'] != '') {
                $segment->PACK_TO_DATE = date('mdy', strtotime($stsResource['opportunitiesInfo']['pack_to_date']));
            }
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
        $segment->ORIG_LCTN_CITY_NME          = $stsResource['estimateInfo']['origin_city'];
        $segment->ORIG_LCTN_COUNTY_NME        = $stsResource['estimateInfo']['estimates_origin_county'];
        $segment->ORIG_LCTN_ST_CDE            = $stsResource['estimateInfo']['origin_state'];
        $segment->ORIG_LCTN_COUNTRY_CDE       = $this->translateCountryCode($stsResource['estimateInfo']['estimates_origin_country']);
        $segment->ORIG_LCTN_ZIP_CODE          = $stsResource['estimateInfo']['origin_zip'];
        $segment->ORIG_LCTN_SITE_CDE          = 'RES';
        $segment->ORIG_LCTN_TYPE_CDE          = 'MP';
        $segment->ORIG_OT_IND                 = $stsResource['estimateInfo']['acc_ot_origin_applied'];
        $segment->ORIG_PRIM_CNTCT_NME         = 'SAME';//$segmentResource['originInfo']['stop_contact'] ? $segmentResource['originInfo']['stop_contact']['firstname'] . ' ' . $segmentResource['originInfo']['stop_contact']['firstname'] : '';
        $segment->ORIG_PRIM_CNTCT_PHN_NBR     = $stsResource['contactInfo']['phone'];
        $segment->ORIG_PRIM_CNTCT_ADDR1       = $segment->ORIG_LCTN_ADDR1;//$segmentResource['originInfo']['stop_contact'] ?$segmentResource['originInfo']['stop_contact']['mailingstreet'] : '';
        $segment->ORIG_PRIM_CNTCT_ADDR2       = $segment->ORIG_LCTN_ADD2;//'';
        $segment->ORIG_PRIM_CNTCT_CITY_NME    = $segment->ORIG_LCTN_CITY_NME;//$segmentResource['originInfo']['stop_contact'] ?$segmentResource['originInfo']['stop_contact']['mailingcity'] : '';
        $segment->ORIG_PRIM_CNTCT_ST_PR_CDE   = $segment->ORIG_LCTN_ST_CDE;//$segmentResource['originInfo']['stop_contact'] ?$segmentResource['originInfo']['stop_contact']['mailingstate'] : '';
        $segment->ORIG_PRIM_CNTCT_ZIP_CDE     = $segment->ORIG_LCTN_ZIP_CODE;//$segmentResource['originInfo']['stop_contact'] ?$segmentResource['originInfo']['stop_contact']['mailingzip'] : '';
        $segment->ORIG_PRIM_CNTCT_COUNTRY_CDE = $segment->ORIG_LCTN_COUNTRY_CDE;//$this->translateCountryCode($segmentResource['originInfo']['stop_contact'] ?$segmentResource['originInfo']['stop_contact']['mailingcountry'] : '');
        $segment->DEST_LCTN_SPLC              = '';
        $segment->DEST_LCTN_ADDR1             = $stsResource['estimateInfo']['destination_address1'];
        $segment->DEST_LCTN_ADDR2             = $stsResource['estimateInfo']['destination_address2'];
        $segment->DEST_LCTN_CITY_NME          = $stsResource['estimateInfo']['destination_city'];
        $segment->DEST_LCTN_COUNTY_NME        = $stsResource['estimateInfo']['estimates_destination_county'];
        $segment->DEST_LCTN_ST_CDE            = $stsResource['estimateInfo']['destination_state'];
        $segment->DEST_LCTN_COUNTRY_CDE       = $this->translateCountryCode($stsResource['estimateInfo']['estimates_destination_country']);
        $segment->DEST_LCTN_ZIP_CODE          = $stsResource['estimateInfo']['destination_zip'];
        $segment->DEST_LCTN_SITE_CDE          = 'RES';
        $segment->DEST_LCTN_TYPE_CDE          = 'MD';
        $segment->DEST_OT_IND                 = $stsResource['estimateInfo']['acc_ot_dest_applied']  == '0' ? 'N' : 'Y';
        $segment->DEST_PRIM_CNTCT_NME         = 'SAME';//$segmentResource['destinationInfo']['stop_contact'] ? $segmentResource['destinationInfo']['stop_contact']['firstname'] . ' ' . $segmentResource['destinationInfo']['stop_contact']['firstname'] : '';
        $segment->DEST_PRIM_CNTCT_PHN_NBR     = $stsResource['contactInfo']['phone'];
        $segment->DEST_PRIM_CNTCT_ADDR1       = $segment->DEST_LCTN_ADDR1;//$segmentResource['destinationInfo']['stop_contact'] ?$segmentResource['destinationInfo']['stop_contact']['mailingstreet'] : '';
        $segment->DEST_PRIM_CNTCT_ADDR2       = $segment->DEST_LCTN_ADDR2;//'';
        $segment->DEST_PRIM_CNTCT_CITY_NME    = $segment->DEST_LCTN_CITY_NME;//$segmentResource['destinationInfo']['stop_contact'] ?$segmentResource['destinationInfo']['stop_contact']['mailingcity'] : '';
        $segment->DEST_PRIM_CNTCT_ST_PR_CDE   = $segment->DEST_LCTN_ST_CDE;//$segmentResource['destinationInfo']['stop_contact'] ?$segmentResource['destinationInfo']['stop_contact']['mailingstate'] : '';
        $segment->DEST_PRIM_CNTCT_ZIP_CDE     = $segment->DEST_LCTN_ZIP_CODE;//$segmentResource['destinationInfo']['stop_contact'] ?$segmentResource['destinationInfo']['stop_contact']['mailingzip'] : '';
        $segment->DEST_PRIM_CNTCT_COUNTRY_CDE = $segment->DEST_LCTN_COUNTRY_CDE;//$this->translateCountryCode($segmentResource['destinationInfo']['stop_contact'] ? $segmentResource['destinationInfo']['stop_contact']['mailingcountry'] : '');
        $segment->OASV_SVC_PROV_NBR           = $stsResource['stsInfo']['ba_code'];
        $segment->OASV_SVC_PROV_TYPE_CDE      = 'A';
        $segment->EASV_SVC_PROV_NBR           = $stsResource['stsInfo']['ea_code'];
        $segment->EASV_SVC_PROV_TYPE_CDE      = 'A';
        $segment->DASV_SVC_PROV_NBR           = $stsResource['stsInfo']['da_code'];
        $segment->DASV_SVC_PROV_TYPE_CDE      = 'A';
          // Apparently this isn't sent for Auto Spots, so I'm just rolling with it.
//        $segment->ORIG_SIT_DISC_PCT           = $stsResource['estimateInfo']['sit_disc'];
//        $segment->ORIG_SIT_SVC_PROV_NBR       = $stsResource['stsInfo']['oa_code'];
//        $segment->ORIG_SIT_SVC_PROV_TYPE_CDE  = 'A';
//
//        if ($segment->ORIG_SIT_ST_PROV_CDE) {
//            $segment->ORIG_SIT_ST_PROV_CDE        = $this->getStateAbbrByPost($stsResource['estimateInfo']['sit_origin_zip']);
//        }
//        $segment->DEST_SIT_DISC_PCT           = $stsResource['estimateInfo']['sit_disc'];
//        $segment->DEST_SIT_SVC_PROV_NBR       = $stsResource['stsInfo']['da_code'];
//        $segment->DEST_SIT_SVC_PROV_TYPE_CDE  = 'A';
//
//        if ($segment->DEST_SIT_ST_PROV_CDE) {
//            $segment->DEST_SIT_ST_PROV_CDE        = $this->getStateAbbrByPost($stsResource['estimateInfo']['sit_dest_zip']);
//        }
        if ($stsResource['stsInfo']['booker_split'] != '') {
            $segment->SPLIT_AMT_PER  = intval($stsResource['stsInfo']['booker_split']);
        }

        $stsMessage->SEGMENT[] = $segment;

        //All remaining segments that are not the (MP to MD). AKA: extra stops
        foreach ($stsResource['cubesheetInfo'] as $key => $segmentResource) {
            if (($segmentResource['FromLocationType'] == 1 && $segmentResource['ToLocationType'] == 2)) {
                continue;//Skip the MP to MD stop
            }

            $cubeStopOrigin = $this->translateCubeSheetStops($segmentResource['FromLocationType'], 'XP');
            $cubeStopDestination = $this->translateCubeSheetStops($segmentResource['ToLocationType'], 'XD');
            //Skip if we do not have a match for destination and origin between cubesheet and extra stops
            if (!array_key_exists($cubeStopOrigin, $stsResource['stops']) || !array_key_exists($cubeStopDestination, $stsResource['stops'])) {
                continue;
            }

            $segmentResource['originInfo'] = $stsResource['stops'][$cubeStopOrigin];
            $segmentResource['destinationInfo'] = $stsResource['stops'][$cubeStopDestination];

            $segmentResource['originInfo']['stop_contact'] = is_numeric($segmentResource['originInfo']['stop_contact']) ? Vtiger_Record_Model::getInstanceById($segmentResource['originInfo']['stop_contact'], 'Contacts')->getData() : false;
            $segmentResource['destinationInfo']['stop_contact'] = is_numeric($segmentResource['destinationInfo']['stop_contact']) ? Vtiger_Record_Model::getInstanceById($segmentResource['destinationInfo']['stop_contact'], 'Contacts')->getData() : [];

            $segment = new stdClass();
            $segment->SEG_NBR                     = count($stsMessage->SEGMENT) + 1;
            $segment->EST_WGT                     = intval($segmentResource['TotalWeight']);
            $segment->EST_CUBES                   = intval($segmentResource['TotalCube']);
            $segment->ORIG_LCTN_SPLC              = '';
            $segment->ORIG_LCTN_ADDR1             = $segmentResource['originInfo']['stop_address1'];
            $segment->ORIG_LCTN_ADD2              = $segmentResource['originInfo']['stop_address2'];
            $segment->ORIG_LCTN_CITY_NME          = $segmentResource['originInfo']['stop_city'];
            $segment->ORIG_LCTN_COUNTY_NME        = '';
            $segment->ORIG_LCTN_ST_CDE            = $segmentResource['originInfo']['stop_state'];
            $segment->ORIG_LCTN_COUNTRY_CDE       = $this->translateCountryCode($segmentResource['originInfo']['stop_country']);
            $segment->ORIG_LCTN_ZIP_CODE          = $segmentResource['originInfo']['stop_zip'];
            $segment->ORIG_LCTN_SITE_CDE          = 'RES';
            $segment->ORIG_LCTN_TYPE_CDE          = $cubeStopOrigin;
            $segment->ORIG_OT_IND                 = 'N';

            $segment->ORIG_PRIM_CNTCT_NME         = 'SAME';//$segmentResource['originInfo']['stop_contact'] ? $segmentResource['originInfo']['stop_contact']['firstname'] . ' ' . $segmentResource['originInfo']['stop_contact']['firstname'] : '';
            $segment->ORIG_PRIM_CNTCT_PHN_NBR     = $segmentResource['originInfo']['stop_contact'] ? $segmentResource['originInfo']['stop_contact']['phone'] : '';
            $segment->ORIG_PRIM_CNTCT_ADDR1       = $segment->ORIG_LCTN_ADDR1;//$segmentResource['originInfo']['stop_contact'] ? $segmentResource['originInfo']['stop_contact']['mailingstreet'] : '';
            $segment->ORIG_PRIM_CNTCT_ADDR2       = $segment->ORIG_LCTN_ADD2;//'';
            $segment->ORIG_PRIM_CNTCT_CITY_NME    = $segment->ORIG_LCTN_CITY_NME;//$segmentResource['originInfo']['stop_contact'] ? $segmentResource['originInfo']['stop_contact']['mailingcity'] : '';
            $segment->ORIG_PRIM_CNTCT_ST_PR_CDE   = $segment->ORIG_LCTN_ST_CDE;//$segmentResource['originInfo']['stop_contact'] ? $segmentResource['originInfo']['stop_contact']['mailingstate'] : '';
            $segment->ORIG_PRIM_CNTCT_ZIP_CDE     = $segment->ORIG_LCTN_ZIP_CODE;//$segmentResource['originInfo']['stop_contact'] ? $segmentResource['originInfo']['stop_contact']['mailingzip'] : '';
            $segment->ORIG_PRIM_CNTCT_COUNTRY_CDE = $segment->ORIG_LCTN_COUNTRY_CDE;//$this->translateCountryCode($segmentResource['originInfo']['stop_contact'] ? $segmentResource['originInfo']['stop_contact']['mailingcountry'] : '');

            $segment->DEST_LCTN_SPLC              = '';
            $segment->DEST_LCTN_ADDR1             = $segmentResource['destinationInfo']['stop_address1'];
            $segment->DEST_LCTN_ADDR2             = $segmentResource['destinationInfo']['stop_address2'];
            $segment->DEST_LCTN_CITY_NME          = $segmentResource['destinationInfo']['stop_city'];
            $segment->DEST_LCTN_COUNTY_NME        = '';
            $segment->DEST_LCTN_ST_CDE            = $segmentResource['destinationInfo']['stop_state'];
            $segment->DEST_LCTN_COUNTRY_CDE       = $this->translateCountryCode($segmentResource['destinationInfo']['stop_country']);
            $segment->DEST_LCTN_ZIP_CODE          = $segmentResource['destinationInfo']['stop_zip'];
            $segment->DEST_LCTN_SITE_CDE          = 'RES';
            $segment->DEST_LCTN_TYPE_CDE          = $cubeStopDestination;
            $segment->DEST_OT_IND                 = 'N';

            $segment->DEST_PRIM_CNTCT_NME         = 'SAME';//$segmentResource['destinationInfo']['stop_contact'] ? $segmentResource['destinationInfo']['stop_contact']['firstname'] . ' ' . $segmentResource['destinationInfo']['stop_contact']['firstname'] : '';
            $segment->DEST_PRIM_CNTCT_PHN_NBR     = $segmentResource['destinationInfo']['stop_contact'] ? $segmentResource['destinationInfo']['stop_contact']['phone'] : '';
            $segment->DEST_PRIM_CNTCT_ADDR1       = $segment->DEST_LCTN_ADDR1;//$segmentResource['destinationInfo']['stop_contact'] ? $segmentResource['destinationInfo']['stop_contact']['mailingstreet'] : '';
            $segment->DEST_PRIM_CNTCT_ADDR2       = $segment->DEST_LCTN_ADDR2;//'';
            $segment->DEST_PRIM_CNTCT_CITY_NME    = $segment->DEST_LCTN_CITY_NME;//$segmentResource['destinationInfo']['stop_contact'] ? $segmentResource['destinationInfo']['stop_contact']['mailingcity'] : '';
            $segment->DEST_PRIM_CNTCT_ST_PR_CDE   = $segment->DEST_LCTN_ST_CDE;//$segmentResource['destinationInfo']['stop_contact'] ? $segmentResource['destinationInfo']['stop_contact']['mailingstate'] : '';
            $segment->DEST_PRIM_CNTCT_ZIP_CDE     = $segment->DEST_LCTN_ZIP_CODE;//$segmentResource['destinationInfo']['stop_contact'] ? $segmentResource['destinationInfo']['stop_contact']['mailingzip'] : '';
            $segment->DEST_PRIM_CNTCT_COUNTRY_CDE = $segment->DEST_LCTN_COUNTRY_CDE;//$this->translateCountryCode($segmentResource['destinationInfo']['stop_contact'] ? $segmentResource['destinationInfo']['stop_contact']['mailingcountry'] : '');

            $stsMessage->SEGMENT[] = $segment;
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
            'ESTIMATOR_ID'           => "Estimator ID (code)",
            'CWT_REDUCTN_RATE'       => "CWT reduction rate",
            'NTE_PACK_PRICE_AMT'     => "GRR pack",
            'SYSTEM_ID'              => "Vanline Brand",
            //'VALUATION_TYPE'         => "Valuation type",
            'DECLARED_VALUE'         => "Declared Value",
            'VAL_PER_LB'             => "Value per lbs",
            'SHIPMENT_MILES'         => "Interstate mileage",
            'SALESPERSON_ID'         => "Salesperson"
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
            'OASV_SVC_PROV_NBR'           => "Booking agent code",
            'EASV_SVC_PROV_NBR'           => "Estimator agent code",
            'DASV_SVC_PROV_NBR'           => "Destination agent code",
        ];

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

        file_put_contents('logs/STS.log', "---------- STS Object ---------- \n" . print_r($stsMessage, true) . "\n\n", FILE_APPEND);

        $stsJSON = json_encode($stsMessage);
        file_put_contents('logs/STS.log', "---------- STS JSON ---------- \n" . print_r($stsJSON, true) . "\n\n", FILE_APPEND);

        //Connect to Sirva and retrieve auth key
        $curlResponse1 = $this->curlPOST('grant_type=client_credentials', 'http://' . getenv('SIRVA_SITE') . '/UAT/oauth2/AccessRequest');

        //Using auth key, send sts message to Sirva, and retrieve response
        $curlResponse2 = $this->curlPOST($stsJSON, 'http://' . getenv('SIRVA_SITE') . '/UAT/OM/m0/RegisterShipment', json_decode($curlResponse1)->access_token, true);

        $apiResponse = json_decode($curlResponse2);

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

        //refresh page if we have successfully sent STS
        if ($success) {
            $db->pquery('UPDATE `vtiger_potential` SET `register_sts` = 1, `register_sts_number` = ? WHERE potentialid = ?', [$apiResponse->CAMIS_REG_NBR, $opportunitiesId]);
            $messages = 'Success';
        } else {
            $messages = $messages . 'STS information sent, but denied by the server.' . "<br />";
        }

        $response = new Vtiger_Response();
        $response->setResult($messages);
        $response->emit();
    }

    public function curlPOST($post_string, $webserviceURL, $key = '', $registerSTS = false)
    {
        $ch = curl_init();

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
                'Host: ' . getenv('SIRVA_SITE'),
                'Content-Type: application/json',
            ];
            file_put_contents('logs/STS.log', "---------- Sirva Connection (send STS JSON) ---------- \nURL: " . $webserviceURL . "\nHeaders: " . print_r($headers, true), FILE_APPEND);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        curl_setopt($ch, CURLOPT_URL, $webserviceURL);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
        $curlResult = curl_exec($ch);
        curl_close($ch);

        file_put_contents('logs/STS.log', "Result: " . $curlResult . "\n\n", FILE_APPEND);


        return $curlResult;
    }

    public function getCubeSheetData($cubesheetsid)
    {
        $soapclient = new \soapclient2(getenv('CUBESHEET_SERVICE_URL'), 'wsdl');
        $soapclient->setDefaultRpcParams(true);
        $soapProxy = $soapclient->getProxy();

        return $soapProxy->GetCubesheetDetailsByRelatedRecordId(['relatedRecordID' => $cubesheetsid]);
    }

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
                return 'USA';
                break;

            default:
                return 'USA';
                break;
        }
    }

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

    public function mapValuationType($valuation)
    {
        $valuationType = null;
        switch ($valuation) {
            case '60&cent; /lb.':
                $valuationType = 'A';
                break;

            case 'ECP - $0':
                $valuationType = '1';
                break;

            case 'ECP - $250':
                $valuationType = '2';
                break;

            case 'ECP - $500':
                $valuationType = '3';
                break;

            case 'FVP - $0':
                $valuationType = '1';
                break;

            case 'FVP - $250':
                $valuationType = '2';
                break;

            case 'FVP - $500':
                $valuationType = '3';
                break;


            default:
                $valuationType = 'A';
                break;
        }

        return $valuationType;
    }

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

    public function translateContainerCode($containerId)
    {
        switch (intval($containerId)) {
            case 1: // 1.5
                return '3';
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
                return '1';
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

            case 11: // Lamp
                return 'W';
                break;

            case 12: // Mirror
                return '2';
                break;

            case 13: // King/Queen
                return 'N';
                break;

            case 14: // Single/Twin
                return 'S';
                break;

            case 15: // Wardrobe
                return 'K';
                break;

            case 16: // 6.5
                return 'J';
                break;

            case 17: // Matress Cover
                return 'P';
                break;

            case 102: // TV Carton
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
                return "AUTO/VAN";
                break;$success = true;

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

    public function requestArrayToSTS($request)
    {
        $stsRequest = [];
        foreach ($request as $field) {
            $stsRequest[$field['name']] = $field['value'];
        }
        return $stsRequest;
    }

    public function structureStops($query)
    {
        $db = $db ?: PearDatabase::getInstance();

        $structuredStops = [];
        if ($db->num_rows($query)) {
            //Note: if stops have the same destination/origin as another, they will be overwritten.
            while ($row =& $query->fetchRow()) {
                //Only add extra stops if they have a move type
                if ($row['stop_type'] == 'Origin') {
                    $structuredStops['XP' . $row['stop_sequence']] = $row;
                } elseif ($row['stop_type'] == 'Destination') {
                    $structuredStops['XD' . $row['stop_sequence']] = $row;
                }
            }
        }

        return $structuredStops;
    }

    public function translateCubeSheetStops($stopNumber = 0, $prefix = '')
    {
        switch (intval($stopNumber)) {
            case 1:
                return 'MP';
                break;
            case 2:
                return 'MD';
                break;
            case 3:
                return $prefix . 1;
                break;
            case 4:
                return $prefix . 2;
                break;
            case 5:
                return $prefix . 3;
                break;
            case 6:
                return $prefix . 4;
                break;
            case 7:
                return $prefix . 5;
                break;
            case 8:
                return 'SIT';
                break;
            case 9:
                return 'STG';
                break;
            case 10:
                return 'PRM';
                break;
            default:
                return null;
        }
    }
}
