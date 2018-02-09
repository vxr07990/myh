<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
vimport('~~/include/Webservices/ConvertLead.php');

class Leads_Save_Action extends Vtiger_Save_Action
{
    public function curlPOST($post_string, $webserviceURL, $key = '', $auth = false)
    {
        $ch = curl_init();

        if (!$auth) {
            $headers = [
                'Authorization: Basic ' . getenv('SIRVA_KEY'),
                'Content-Type: application/x-www-form-urlencoded;charset=UTF-8',
            ];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        } else {
            $headers = [
                'Authorization: Bearer ' . $key,
                'Host: ' . getenv('SIRVA_SITE'),
                'Content-Type: application/json',
            ];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        curl_setopt($ch, CURLOPT_URL, $webserviceURL);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
        $curlResult = curl_exec($ch);
        curl_close($ch);

        return $curlResult;
    }

    public function sendLeadToNational(Vtiger_Request $request)
    {
        $status = $request->get('leadstatus');
        if($status == 'Appointment Set' || $status == 'Booked Direct')
        {
            $leadInfo = [
                'firstname'  => substr($request->get('firstname'), 0, 20),
                'lastname'   => substr($request->get('lastname'), 0, 17),
                'phone'      => substr($request->get('phone'), 0, 10),
                'emailadr'   => substr($request->get('email'), 0, 60),
                'oaddress1'  => substr($request->get('origin_address1'), 0, 24),
                'oaddress2'  => substr($request->get('origin_address2'), 0, 24),
                'ocity'      => substr($request->get('origin_city'), 0, 20),
                'ostate'     => substr($request->get('origin_state'), 0, 2),
                'ozipcode'   => substr($request->get('origin_zip'), 0, 9),
                'daddress1'  => substr($request->get('destination_address1'), 0, 24),
                'daddress2'  => substr($request->get('destination_address2'), 0, 24),
                'dcity'      => substr($request->get('destination_city'), 0, 20),
                'dstate'     => substr($request->get('destination_state'), 0, 2),
                'dzipcode'   => substr($request->get('destination_zip'), 0, 9),
                'leadsource' => substr($request->get('leadsource'), 0, 10)
            ];

            $json = json_encode($leadInfo);
            $addPName = "pName=[$json]";
            $headers = [
                'Content-type: application/x-www-form-urlencoded'
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_URL, getenv('NATIONAL_API'));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $addPName);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
            $curlResult = curl_exec($ch);
            curl_close($ch);

            file_put_contents("logs/nationalAPI.log", "JSON: \n" . $addPName . "\n\n", FILE_APPEND);
            file_put_contents("logs/nationalAPI.log", "CURLResponse: \n" . $curlResult . "\n\n\n", FILE_APPEND);

            $xml = simplexml_load_string($curlResult);
            if(isset($xml[0])){
                $result = substr($xml[0], 0, 3);
                if($result == "201"){
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
        return true;
    }

    public function process(Vtiger_Request $request)
    {

        //This will send the lead to national's webservice to do whatever they want to do with it.
        if(getenv('INSTANCE_NAME') == 'national' && getenv('NATIONAL_API_ON')){
            $this->sendLeadToNational($request);
        }

        //This will check if it is a LMP lead. If it is, it will connect with Sirva's OpenAPI and send them an updated LMP lead.
        if (getenv('INSTANCE_NAME') === 'sirva' && $request->get('lmp_lead_id') != '') {
            $db = PearDatabase::getInstance();
            //file_put_contents('logs/devLog.log', "\n this is Sirva", FILE_APPEND);

//			$sql = 'SELECT agency_code, `vtiger_vanlinemanager`.vanline_id
//					FROM `vtiger_agentmanager`
//					JOIN `vtiger_vanlinemanager`
//					ON `vtiger_vanlinemanager`.vanlinemanagerid = `vtiger_agentmanager`.vanline_id
//					WHERE `vtiger_agentmanager`.groupid = ?';
            $sql = 'SELECT agency_code, `vtiger_vanlinemanager`.vanline_id
					FROM `vtiger_agentmanager`
					JOIN `vtiger_vanlinemanager`
					ON `vtiger_vanlinemanager`.vanlinemanagerid = `vtiger_agentmanager`.vanline_id
					JOIN `vtiger_crmentity`
					ON `vtiger_agentmanager`.agentmanagerid = `vtiger_crmentity`.agentid
					WHERE `vtiger_crmentity`.agentid = ?';
            $agentVanline = $db->pquery($sql, [$request->get('agentId')])->fetchRow();

            $sql = 'SELECT `modcommentsid`, `commentcontent`, `user_name`
                    FROM `vtiger_modcomments`
                    LEFT JOIN `vtiger_users` ON `vtiger_users`.`id` = `vtiger_modcomments`.`userid`
                    WHERE `related_to` = ?';
            file_put_contents('logs/devLog.log', "\n Sql : ".print_r($sql, true), FILE_APPEND);
            $comments = [];
            $commentsResults = $db->pquery($sql, [$request->get('record')]);
            file_put_contents('logs/devLog.log', "\n Record : ".print_r($request->get('record'), true), FILE_APPEND);
            while ($row =& $commentsResults->fetchRow()) {
                $user_name = $row['user_name'];
                if (empty($user_name)) {
                    $sql2 = 'SELECT `vtiger_users`.user_name FROM `vtiger_users`
                            JOIN `vtiger_crmentity` ON `vtiger_users`.id = `vtiger_crmentity`.smownerid
                            WHERE `vtiger_crmentity`.crmid = ?';
                    $result2 = $db->pquery($sql2, [$row['modcommentsid']]);
                    $row2 = $result2->fetchRow();
                    $user_name = $row2['user_name'];
                }
                $comments[] = [
                    "NoteID" => $row['modcommentsid'],
                    "NoteDetail" => $row['commentcontent'],
                    "CreatedBy" => $user_name,
                ];
            }
            //*/
            switch ($request->get('primary_phone_type')) {
                case 'Home':
                    $phone_type = 'H';
                    break;
                case 'Work':
                    $phone_type = 'W';
                    break;
                case 'Cell':
                    $phone_type = 'C';
                    break;
                default:
                    $phone_type = '';
               }
            if ($request->get('preferred_pldate')) {
                $preferred_pldate = DateTime::createFromFormat('Y-m-d', DateTimeField::convertToDBFormat($request->get('preferred_pldate')))->format('m/d/Y');
            } else {
                $preferred_pldate = "";
            }
            if ($request->get('preferred_pddate')) {
                $preferred_pddate = DateTime::createFromFormat('Y-m-d', DateTimeField::convertToDBFormat($request->get('preferred_pddate')))->format('m/d/Y');
            } else {
                $preferred_pddate = "";
            }

            $LMPMessage = [
                "AgentCode"                   => $agentVanline['agency_code'],
                "LeadId"                      => $request->get('lmp_lead_id'),
                "PrimaryContactFirstNme"      => $request->get('firstname'),
                "PrimaryContactLastNme"       => $request->get('lastname'),
                "PrimaryContactEmail"         => $request->get('email'),
                "PrimaryContactFax"           => $request->get('origin_fax'),
                "PrimaryContactHomePhone"     => $request->get('primary_phone_type') == 'Home' ? $request->get('phone') : '',
                "PrimaryContactCellPhone"     => $request->get('mobile'),
                "PrimaryContactWorkPhone"     => $request->get('primary_phone_type') == 'Work' ? $request->get('phone') : '',
                "PrimaryContactWorkPhoneExt"  => $request->get('primary_phone_ext'),
                "PrimaryContactPhoneType"     => $phone_type,// . ' Phone',
                "PrimaryContactPerferredTime" => $request->get('prefer_time'),
                "PrimaryContactLanguage"      => $request->get('languages'),
                "OriginAddressLine1"          => $request->get('origin_address1'),
                "OriginAddressLine2"          => $request->get('origin_address2'),
                "OriginZip"                   => $request->get('origin_zip'),
                "OriginCity"                  => $request->get('origin_city'),
                "OriginStateCode"             => $request->get('origin_state'),
                "OriginCountryCode"           => $request->get('origin_country') == 'United States' ? 'US' : 'CA',
                "DestinationAddressLine1"     => $request->get('destination_address1'),
                "DestinationAddressLine2"     => $request->get('destination_address2'),
            // if the world made sense this is how this would be done
            //	"DestinationZip"              => $request->get('destination_zip'),
            //	"DestinationCity"             => $request->get('destination_city'),
            //	"DestinationStateCode"        => $request->get('destination_state'),
            // but it doesn't so this is the way Sirva needs the data
                "DestinationZip"              => $request->get('destination_city'),//$request->get('destination_zip'),
                "DestinationCity"             => $request->get('destination_state'),//$request->get('destination_city'),
                "DestinationStateCode"        => $request->get('destination_zip'),//$request->get('destination_state'),
            // end super wrongly named field mapping at SIRVA's request.
                "DestinationCountryCode"      => $request->get('destination_country') == 'United States' ? 'US' : 'CA',
                "MoveDate"                    => $preferred_pldate,
                "ExpectedDeliverDate"         => $preferred_pddate,
                "Disposition"                 => $request->get('leadstatus'),
                "NotBookedReason"             => $request->get('disposition_lost_reasons'),
                "OrderNumber"                 => "",
                "DwellingTypeName"            => $request->get('dwelling_type'),
                "FurnishLevel"                => $request->get('furnish_level'),
                "SpecialItems"                => $request->get('special_terms'),
                "Comment"                     => "",
                "Brand"                       => $agentVanline['vanline_id'] == '9' ? 'NAVL' : 'AVL', //9 = NAVL || 1 = AVL
                "KitchenTableClose"           => "",
                "MovingAVehicle"              => $request->get('moving_vehicle') == 'on' ? 'Y' : 'N',
                "FlexibleOnDays"              => $request->get('flexible_on_days')== 'on' ? 'Y' : 'N',
                "CCDisposition"               => $request->get('cc_disposition'),
                "EmployerAssisting"           => $request->get('enabled') == 'on' ? 'Y' : 'N',
                "EmployerName"                => $request->get('contact_name'),
                "FeedbackAgentCode"           => $agentVanline['agency_code'],
                "FeedbackType"                => "",
                "Feedback"                    => "",
                "Notes"                       => $comments,
            ];
            //*/
            $jsonMessage = json_encode($LMPMessage, JSON_PRETTY_PRINT);
            file_put_contents('logs/devLog.log', "\n Message JSON : ".print_r($jsonMessage, true), FILE_APPEND);
            /*/
            $jsonMessage =
                '{
                    "AgentCode":"2004000",
                    "LeadId":"4424491",
                    "PrimaryContactFirstNme":"Alex",
                    "PrimaryContactLastNme":"Smith",
                    "PrimaryContactEmail":"asmith@igcsoftware.com",
                    "PrimaryContactFax":"",
                    "PrimaryContactHomePhone":"2605594899",
                    "PrimaryContactCellPhone":"2605594898",
                    "PrimaryContactWorkPhone":"",
                    "PrimaryContactWorkPhoneExt":"",
                    "PrimaryContactPhoneType":"H",
                    "PrimaryContactPerferredTime":"",
                    "PrimaryContactLanguage":"English",
                    "OriginAddressLine1":"234",
                    "OriginAddressLine2":"",
                    "OriginZip":"23235",
                    "OriginCity":"RICHMOND",
                    "OriginStateCode":"VA",
                    "OriginCountryCode":"US",
                    "DestinationAddressLine1":"tbd",
                    "DestinationAddressLine2":"",
                    "DestinationZip":"RICHMOND",
                    "DestinationCity":"VA",
                    "DestinationStateCode":"23236",
                    "DestinationCountryCode":"US",
                    "MoveDate":"02/26/2016",
                    "ExpectedDeliverDate":"",
                    "Disposition":"Pending",
                    "NotBookedReason":"",
                    "OrderNumber":"",
                    "DwellingTypeName":"2 Bedroom Apt.",
                    "FurnishLevel":"Medium",
                    "SpecialItems":"",
                    "Comment":"",
                    "Brand":"NAVL",
                    "KitchenTableClose":"",
                    "MovingAVehicle":"N",
                    "FlexibleOnDays":"N",
                    "CCDisposition":"Assigned",
                    "EmployerAssisting":"N",
                    "EmployerName":"",
                    "FeedbackAgentCode":"2004000",
                    "FeedbackType":"",
                    "Feedback":"",
                    "Notes": [
                        {
                            "NoteID":"40767377",
                            "NoteDetail":"Finish Lead",
                            "CreatedBy":"SADMIN"
                        },
                        {
                            "NoteID":"40767378",
                            "NoteDetail":"Automated Mails sent successfully.",
                            "CreatedBy":"SADMIN"
                        }
                    ]
                }';
            file_put_contents('logs/devLog.log', "\n Message JSON : ".print_r($jsonMessage,true), FILE_APPEND);
            //*/
            $curlAuthResponse = $this->curlPOST('grant_type=client_credentials', 'http://' . getenv('SIRVA_SITE') . '/UAT/oauth2/AccessRequest');
            file_put_contents('logs/devLog.log', "\n authResponse : ".print_r($curlAuthResponse, true), FILE_APPEND);
            $curlResponse2 = $this->curlPOST($jsonMessage, 'http://' . getenv('SIRVA_SITE').'/UAT/LMP/m7/UpdateLeadDetails', json_decode($curlAuthResponse)->access_token, true);
            file_put_contents('logs/devLog.log', "\n LMP response : ".print_r($curlResponse2, true), FILE_APPEND);
            //*/
        }

        //To stop saveing the value of salutation as '--None--'
        $salutationType = $request->get('salutationtype');
        if ($salutationType === '--None--') {
            $request->set('salutationtype', '');
        }

        $this->convertSurveyDateTime($request);
        //$surveyTime = Vtiger_Time_UIType::getTimeValueWithSeconds($request->get('survey_time'));
        //$datetime = DateTimeField::convertToDBTimeZone($request->get('survey_date').' '.$surveyTime);
        //$request->set('survey_time', $datetime->format('H:i:s'));
        //$request->set('survey_date', $datetime->format('Y-m-d'));

        if ($request->get('record') == '' && $request->get('leadstatus') == 'Qualified') {
            $recordMod = parent::saveRecord($request);
            $recordId = $recordMod->getId();
        } else {
            //file_put_contents('logs/devLog.log', "\n in this else request: \n".print_r($request,true),FILE_APPEND);
            parent::process($request);
            $recordId = $request->get('record');
            if (getenv('INSTANCE_NAME') == 'sirva' && $request->get('disposition_lost_reasons') == 'Pricing') {
                $comps = [
                    'allied'=>$request->get('comp_allied')?$request->get('comp_allied'):0,
                    'atlas'=>$request->get('comp_atlas')?$request->get('comp_atlas'):0,
                    'mayflower'=>$request->get('comp_mayflower')?$request->get('comp_mayflower'):0,
                    'north_american'=>$request->get('comp_northamerican')?$request->get('comp_northamerican'):0,
                    'united'=>$request->get('comp_united')?$request->get('comp_united'):0,
                    'independent'=>$request->get('comp_independent')?$request->get('comp_independent'):0,
                    'other'=>$request->get('comp_other')?$request->get('comp_other'):0,
                ];
                $db = PearDatabase::getInstance();
                $sql = "SELECT * FROM `vtiger_sirva_pricing_comp` WHERE leadid = ?";
                $result = $db->pquery($sql, [$recordId]);
                if ($result) {
                    //prevents fatal error if the table isn't there
                    $row = $result->fetchRow();
                    if ($row) {
                        $sql = "UPDATE `vtiger_sirva_pricing_comp` SET allied=?, atlas=?, mayflower=?, north_american=?, united=?, independent=?, other=? WHERE leadid=?";
                        //file_put_contents('logs/devLog.log', "\n UPDATE `vtiger_sirva_pricing_comp` SET allied={$comps['allied']}, atlas={$comps['atlas']}, mayflower={$comps['mayflower']}, north_american={$comps['north_american']}, united={$comps['united']}, independent={$comps['independent']}, other={$comps['other']} WHERE leadid={$recordId}", FILE_APPEND);
                        $db->pquery($sql, [$comps['allied'], $comps['atlas'], $comps['mayflower'], $comps['north_american'], $comps['united'], $comps['independent'], $comps['other'], $recordId]);
                    } else {
                        $sql = "INSERT INTO `vtiger_sirva_pricing_comp` (leadid, allied, atlas, mayflower, north_american, united, independent, other) VALUES (?,?,?,?,?,?,?,?)";
                        //file_put_contents('logs/devLog.log', "\n INSERT INTO `vtiger_sirva_pricing_comp` (leadid, allied, atlas, mayflower, north_american, united, indepenent, other) VALUES ({$recordId},{$comps['allied']},{$comps['atlas']},{$comps['mayflower']},{$comps['north_american']},{$comps['united']},{$comps['independent']},{$comps['other']})" ,FILE_APPEND);
                        $db->pquery($sql, [$recordId, $comps['allied'], $comps['atlas'], $comps['mayflower'], $comps['north_american'], $comps['united'], $comps['independent'], $comps['other']]);
                    }
                }
            }
        }

//        $vehicleLookupModel = Vtiger_Module_Model::getInstance('VehicleLookup');
//
//        if (is_object($vehicleLookupModel) && in_array('isActive', get_class_methods($vehicleLookupModel))) {
//            if ($vehicleLookupModel->isActive()) {
//                file_put_contents('logs/vehicleSave.log', date('Y-m-d H:i:s - ')."Preparing to call saveVehicles\n", FILE_APPEND);
//                $vehicleLookupModel::saveVehicles($request);
//            }
//        }
//
    //	file_put_contents('logs/ConvertLeadTest.log', date('Y-m-d H:i:s - ').print_r($request, true)."\n", FILE_APPEND);

        if ($request->get('leadstatus') == 'Qualified') {
            //Automatically convert Lead to Opportunity if leadstatus is set to Qualified

        //	file_put_contents('logs/ConvertLeadTest.log', date('Y-m-d H:i:s - ')."Attempting to convert Lead\n", FILE_APPEND);

            //$recordId = $request->get('record');

        //	file_put_contents('logs/ConvertLeadTest.log', date('Y-m-d H:i:s - ').$recordId."\n", FILE_APPEND);
            if ($request->get('company') == '') {
                $modules = array("Contacts","Potentials");
                $transferModule = 'Contacts';
            } else {
                $modules = array("Accounts","Contacts","Potentials");
                $transferModule = 'Accounts';
            }
            $assignId = $request->get('assigned_user_id');
            $currentUser = Users_Record_Model::getCurrentUserModel();

            $entityValues = array();

            $entityValues['transferRelatedRecordsTo'] = $transferModule;
            $entityValues['assignedTo'] = vtws_getWebserviceEntityId(vtws_getOwnerType($assignId), $assignId);
            $entityValues['leadId'] =  vtws_getWebserviceEntityId($request->getModule(), $recordId);

            $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $request->getModule());
            $convertLeadFields = $recordModel->getConvertLeadFields();

            $availableModules = array('Accounts', 'Contacts', 'Potentials');

            if (vtlib_isModuleActive('Accounts') && in_array('Accounts', $modules)) {
                $entityValues['entities']['Accounts']['create'] = true;
                $entityValues['entities']['Accounts']['name'] = 'Accounts';
                $entityValues['entities']['Accounts']['accountname'] = $request->get('company');
                $entityValues['entities']['Accounts']['industry'] = '';
            }
            if (vtlib_isModuleActive('Contacts') && in_array('Contacts', $modules)) {
                $entityValues['entities']['Contacts']['create'] = true;
                $entityValues['entities']['Contacts']['name'] = 'Contacts';
                $entityValues['entities']['Contacts']['lastname'] = $request->get('lastname');
                $entityValues['entities']['Contacts']['firstname'] = $request->get('firstname');
                $entityValues['entities']['Contacts']['email'] = $request->get('email');
            }
            if (vtlib_isModuleActive('Potentials') && in_array('Potentials', $modules)) {
                $entityValues['entities']['Potentials']['create'] = true;
                $entityValues['entities']['Potentials']['name'] = 'Potentials';
                $entityValues['entities']['Potentials']['potentialname'] = $request->get('lastname').' '.$request->get('business_line');
                $entityValues['entities']['Potentials']['business_line'] = $request->get('business_line');
                $entityValues['entities']['Potentials']['sales_stage'] = 'Qualification';
                $entityValues['entities']['Potentials']['closingdate'] = ($request->get('load_from') != '') ? DateTimeField::convertToDBFormat($request->get('load_from')) : DateTimeField::convertToDBFormat(date('m-d-Y', strtotime("+30 days")));
                $entityValues['entities']['Potentials']['amount'] = Vtiger_Currency_UIType::convertToDBFormat('');
            }
            try {
                $result = vtws_convertlead($entityValues, $currentUser);
            } catch (Exception $e) {
                $this->showError($request, $e);
                exit;
            }

            if (!empty($result['Accounts'])) {
                $accountIdComponents = vtws_getIdComponents($result['Accounts']);
                $accountId = $accountIdComponents[1];
            }
            if (!empty($result['Contacts'])) {
                $contactIdComponents = vtws_getIdComponents($result['Contacts']);
                $contactId = $contactIdComponents[1];
            }
            if (!empty($result['Potentials'])) {
                $potentialIdComponents = vtws_getIdComponents($result['Potentials']);
                $potentialId = $potentialIdComponents[1];

                $vehicleLookup = Vtiger_Module_Model::getInstance('VehicleLookup');
                if ($vehicleLookup && $vehicleLookup->isActive()) {
                    $vehicleLookup::transferVehicles($request->get('record'), $potentialId);
                }
            }

            if (!empty($potentialId)) {
                header("Location: index.php?view=Detail&module=Potentials&record=$potentialId");
            } elseif (!empty($accountId)) {
                header("Location: index.php?view=Detail&module=Accounts&record=$accountId");
            } elseif (!empty($contactId)) {
                header("Location: index.php?view=Detail&module=Contacts&record=$contactId");
            } else {
                $this->showError($request);
                exit;
            }
        }
    }
}
