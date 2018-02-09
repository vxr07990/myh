<?php

include_once 'include/Webservices/Create.php';
include_once 'include/Webservices/DescribeObject.php';
include_once 'include/Webservices/Retrieve.php';
require_once 'modules/SirvaImporter/utils/auxArrays.php';
include_once 'include/Webservices/ConvertLead.php';

class SirvaImporter_ImportStep2_Action extends Vtiger_Action_Controller {

    public $pid;
    //This may be a thing or may not be a thing.
    public $vanline_agency = '9999000';
    //private $vanline_agency = false;
    private $hardUserEmail = '';

    public function checkPermission(Vtiger_Request $request) {
        global $current_user;
        //$moduleName = $request->getModule();
        if (!is_admin($current_user)) {
            throw new AppException('LBL_PERMISSION_DENIED');
        }
    }

    public function process(Vtiger_Request $request) {
	$entityValues = "";
	$this->pid = getmypid();
	$importModule = $request->get('module1');
	$delimiter = $request->get('delimiter');
	//$header = $request->get('has_header');
	//$relListLabel = $request->get('related_list_label');
	$agentId = $request->get('agent_id');
	$maxImports = $request->get('max_imports_lines');
	
	
	//If we are running this form the UI we do just 1 batch. Otherwise we need to read the DB one-by-one
	if(!isset($_REQUEST['max_imports_lines']) || $maxImports == ''){
	    $maxImports = 1;
	}


	for ($j = 0; $j < $maxImports; $j++) {

	    if (php_sapi_name() == 'cli') {
		$arrResult = $this->readDataFromDB($importModule);
		
	    } else {
		$arrResult = $this->readCsv($delimiter);
	    }


	    $this->resetLogFile();
	    //$arrResult = FAIL if it's not valid or whatever.
	    if (is_array($arrResult)) {
		switch ($importModule) {
		    case 'Lead Notes':
			$result = $this->ImportLeadsComments($arrResult, $agentId);
			break;
		    case 'Qualified Lead Activity':
			$result = $this->ImportLeadActivities($arrResult, $agentId);
			break;
		    case 'Extra Stops Data':
			$result = $this->ImportExtraStops($arrResult, $agentId);
			break;
		    case 'Users':
			$result = $this->ImportUsers($arrResult, $agentId);
			break;
		    case 'Lead Data':
			$result = $this->ImportLeadData($arrResult, $agentId);
			break;
		    default:
			break;
		}
	    } else {
		//be vague as hell, because 
		if (php_sapi_name() == 'cli') {
		    print "Failed to open input file to read.\n";
		    break;
		} else {
		    throw new AppException('LBL_PERMISSION_DENIED');
		}
	    }
	    

	    if (php_sapi_name() !== 'cli') {
		$params = '';
		foreach ($result as $key => $value) {
		    $params .= '&' . $key . '=' . $value;
		}
		break;
	    }
	    
	}
	


	if (php_sapi_name() == 'cli') {
	    //cli stuff
	    print "We finished running!\n";
	   
	} else {
	    header("Location: index.php?module=SirvaImporter&view=ImporterResults&parent=Settings" . $params);
	}
    }

    function ImportLeadData($arrResult, $agentId) {
        $importResult             = [];
        $importResult['imported'] = $importResult['failed'] = $importResult['success'] = $importResult['alreadyCreated'] = $i = 0;
        $helper = new helper();


        foreach ($arrResult as $row_no => $data) {
            $user23         = new Users();
            $user = $user23->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());

            $importResult['imported'] = $importResult['imported'] + 1;
            $data['Unqualified Lead'] = trim($data['Unqualified Lead']);
            $data['Qualified Lead']   = trim($data['Qualified Lead']);
            //add a flag that indicates we already imported this record so we don't throw an error when it can't recreate the thing.
            $contactAlreadyCreated = false;
            $oppAlreadyCreated     = false;
            $leadAlreadyCreated    = false;
            try {

                if ($data['Qualified Lead'] !== 'NULL' && $data['Qualified Lead'] !== '' && ($data['Lead Type'] != 'OA Survey' || $data['Lead Disposition'] != 'Completed')) {

                    if ($this->isAlreadyImported($data['Unqualified Lead'], 'Contacts') && $this->isAlreadyImported($data['Unqualified Lead'], 'Opportunities')) {
                        unset($dataLeads); //shot in the bloody dark here.
                        $dataLeads = $helper->helpMeArray($data, 'Leads');
                        $dataLeads['agentid'] = $agentId;
                        $dataLeads['assigned_user_id'] = vtws_getWebserviceEntityId('Users', $this->getUserFromExcel($data['Lead Last Updated By']));
                        $dataLeads['phone'] = $this->verifyPhone($data["Cell Phone"], $data["Work Phone"], $data["Home Phone"], false);
                        $dataLeads['email'] = $this->verifyEmail($data['Email']);
                        $dataLeads['leadstatus'] = $this->verifyLeadStatus($data['UQ Status'],$data['UQ Disposition']);//OT16974 Changes to the data import for QIO2
                        $dataLeads['AAProgramName'] = $this->verifyProgramName($data['Program Name']);
                        $dataLeads['AASourceName'] = $this->verifySourceName($data['Source Name']);
                        $dataLeads['MarketingChannel'] = $this->verifyMarketingChannel($data['Mktg Channel']);
                        $dataLeads['AAProgramTerms'] = $this->verifyProgramTerms($data['Program Terms']);
                        $dataLeads['AASourceType'] = $data['Lead Type'];
                        $dataLeads['LMPAssignedAgentOrgId'] = $data['AgencyCode'];
                        $dataLeads['origin_country'] = $this->verifyCountry($data["Orig Ctry"]);//OT16974 Changes to the data import for QIO2
                        $dataLeads['destination_country'] = $this->verifyCountry($data["Dest Ctry"]);//OT16974 Changes to the data import for QIO2
                        $dataLeads['origin_state'] = $data['Orig St Prov'];
                        $dataLeads['destination_state'] = $data['Dest St Prov'];
			            $dataLeads['Brand'] = $data['Booker Brand'];
                        $sourceId = $this->createLeadSource($dataLeads);
                        $dataLeads['source_name'] = $this->checkSourceId($data['Lead Source'], $sourceId);
                        $leadEntity = vtws_create('Leads', $dataLeads, $user);
                        $arr_aux = explode("x", $leadEntity['id']);
                        $leadId = $arr_aux[1];
                        

                        $entityValues = "";

                       
                            $entityValues = [
                                'transferRelatedRecordsTo' => 'Contacts',
                                'assignedTo' => "19x" . $this->assignedTo($data['Lead Last Updated By']),
                                'leadId' => "10x" . $leadId,
                                'entities' => [
                                    'Contacts' => [
                                        'create' => 1,
                                        'name' => 'Contacts',
                                        'contact_type' => 'Transferee',
                                        'lastname' => $data["Last Name"],
                                        'firstname' => $data["First Name"],
					                   'phone' => $this->verifyPhone($data["Cell Phone"], $data["Work Phone"], $data["Home Phone"], false),
                                        'primary_phone_type' => $this->verifyPhone($data["Cell Phone"], $data["Work Phone"], $data["Home Phone"], true),
                                        'email' => $this->verifyEmail($data['Email']),
                                    ],
                                    'Opportunities' => [
                                        'create' => 1,
                                        'name' => 'Opportunities',
                                        'origin_address1' => $data["Orig Addr 1"],
                                        'origin_city' => $data["Orig City"],
                                        'origin_state' => $data["Orig St Prov"],
                                        'origin_zip' => $data["Orig Zip Postal"],
                                        'origin_country' => $this->verifyCountry($data["Orig Ctry"]),//OT16974 Changes to the data import for QIO2
                                        'origin_phone1' => $this->verifyPhone($data["Cell Phone"], $data["Work Phone"], $data["Home Phone"], false),
                                        'origin_phone1_ext' => NULL,
                                        'origin_phone1_type' => $this->verifyPhone($data["Cell Phone"], $data["Work Phone"], $data["Home Phone"], true),
                                        'origin_phone2' => NULL,
                                        'origin_phone2_ext' => NULL,
                                        'origin_phone2_type' => NULL,
                                        'destination_address1' => $data["Dest Addr 1"],
                                        'destination_country' => $this->verifyCountry($data["Dest Ctry"]),//OT16974 Changes to the data import for QIO2
                                        'destination_phone1' => NULL,
                                        'destination_phone1_ext' => NULL,
                                        'destination_phone1_type' => NULL,
                                        'destination_phone2' => NULL,
                                        'destination_phone2_ext' => NULL,
                                        'destination_phone2_type' => NULL,
                                        'potentialname' => $data['First Name'] . " " . $data['Last Name']. " - ".$data['Qualified Lead'],
                                        'sales_stage' => $this->changeOppStatusForTranslation($data['Lead Disposition']),
//                                      'opportunity_disposition' => $data['Lead Disposition'], // OT16719 is the field sales_stage or they want to populate this field??
					                    'disposition_lost_reasons' => $this->verifyLostReason($data['Lead Disposition'], $data['Detail Disposition']),
                                        'closingdate' => (!empty($FulfillmentDate) ? $FulfillmentDate->format('Y-m-d') : NULL),
                                        'move_type' => $data['Move Type'],
                                        'agentid' => $agentId,
                                        'business_line' => $this->changeBusinessLineFromMoveType($data['Move Type']),
                                        'preferred_language' => $data['Language'] ?: 'English',
                                        'skipparticipation' => TRUE,
                                        'promotion_code'=> $data['Promotion Code'],
                                        'program_terms'=> $data['Program Terms'],
                                        'sales_person'=> $this->getSalesPerson($data['Primary Sales Team Position Name']),
                                        'source_name'=> $this->getSource($data, $agentId),
                                        'program_name'=> $data['Source Name'], //These field two fields do not match the field name with the UI
                                        'special_terms'=>$data['Special Items'],
                                        'employer_comments'=> $data['Comments'],
                                    ],
                                ],
                            ];
			    
			 
			    
                            $user->id = $this->assignedTo($data['Lead Last Updated By']);
                            $_REQUEST['repeat'] = FALSE;
                            $convertlead = vtws_convertlead($entityValues, $user);
			    //add function to create patrticipant agents
			    $array_aux = explode('x', $convertlead['Opportunities']);
			    $opportunityId = $array_aux[1];
			    
			    $array_aux = explode('x', $convertlead['Contacts']);
			    $contactId = $array_aux[1];
			    
			    
			    $this->saveRelation($data['Unqualified Lead'], $data['Qualified Lead'], $contactId, 'Contacts');
			    $this->saveRelation($data['Unqualified Lead'], $data['Qualified Lead'], $opportunityId, 'Opportunities');
			    
                $this->updateImportDB('Lead Data', $data['Importer Dataid'], 1);
			   
			    try {
				    $this->updateCreatedTime($opportunityId, $data['Lmp Lead Created']);
				
			    } catch (Exception $exc) {
				    $this->writeLog($exc->getMessage());
			    }
			    
			    $this->updateOppSelfHaul($opportunityId, $this->getSelfHaul($agentId)); //Not sure why I need to do it this way. Looks like something is forcing the field to be 1
			    $this->insertBookingAgent($opportunityId,$data['Bkr Code']);

                    } else {
                        $oppAlreadyCreated = true;
                    }

                } else {

                    if ($this->isAlreadyImported($data['Unqualified Lead'], 'Contacts')) {
                        unset($dataContacts); //shot in the bloody dark here.
                        $dataContacts                     = $helper->helpMeArray($data, 'Contacts');
                        $dataContacts['agentid']          = $agentId;
                        $dataContacts['contact_type']     = 'Transferee';
                        $dataContacts['assigned_user_id'] = vtws_getWebserviceEntityId('Users', $this->getUserFromExcel($data['Lead Last Updated By']));
                        $contactEntity                    = vtws_create('Contacts', $dataContacts, $user);
                        $contactId                        = explode('x', $contactEntity[id])[1];
                        $this->saveRelation($data['Unqualified Lead'],$data['Qualified Lead'], $contactId, 'Contacts');
                    } else {
                        $contactAlreadyCreated = true;
                    }

                    if ($this->isAlreadyImported($data['Unqualified Lead'], 'Leads')) {
                        unset($dataLeads); //shot in the bloody dark here.
                        $dataLeads                     = $helper->helpMeArray($data, 'Leads');
                        $dataLeads['agentid']          = $agentId;
                        $dataLeads['assigned_user_id'] = vtws_getWebserviceEntityId('Users', $this->getUserFromExcel($data['Lead Last Updated By']));
                        $dataLeads['phone']            = $this->verifyPhone($data["Cell Phone"], $data["Work Phone"], $data["Home Phone"], false);
                        $dataLeads['email']            = $this->verifyEmail($data['Email']);
                        $dataLeads['leadstatus']       = $this->verifyLeadStatus($data['UQ Status'], $data['UQ Disposition']);
                        $dataLeads['AAProgramName']    = $this->verifyProgramName($data['Program Name']);
                        $dataLeads['AASourceName']     = $this->verifySourceName($data['Source Name']);
                        $dataLeads['MarketingChannel'] = $this->verifyMarketingChannel($data['Mktg Channel']);
                        $dataLeads['AAProgramTerms']   = $this->verifyProgramTerms($data['Program Terms']);
                        $dataLeads['AASourceType']     = $data['Lead Type'];
                        $dataLeads['LMPAssignedAgentOrgId'] = $data['AgencyCode'];
                        $dataLeads['Brand']            = $data['Booker Brand'];
			            $dataLeads['origin_state']	= $data['Orig St Prov'];
                        $dataLeads['destination_state'] = $data['Dest St Prov'];
                        $sourceId                      = $this->createLeadSource($dataLeads);
                        $dataLeads['source_name']      = $this->checkSourceId($data['Lead Source'], $sourceId);
                        $leadEntity                    = vtws_create('Leads', $dataLeads, $user);
                        $arr_aux = explode("x", $leadEntity['id']);
                        $leadId  = $arr_aux[1];
                        $this->saveRelation($data['Unqualified Lead'],'', $leadId, 'Leads');
                    } else {
                        $leadAlreadyCreated = true;
                    }
                }
                
                if ($leadAlreadyCreated || $oppAlreadyCreated) {
                    $importResult['alreadyCreated'] = $importResult['alreadyCreated'] + 1;

		            if($leadAlreadyCreated){
                        $this->updateImportDB('Lead Data', $data['Importer Dataid'], 0, 'Lead Already created');
                    }else if($oppAlreadyCreated){
                        $this->updateImportDB('Lead Data', $data['Importer Dataid'], 0, 'Opportunity or Contact found!');
                    }
		    
                } else {
                    $importResult['success'] = $importResult['success'] + 1;
		            $this->updateImportDB('Lead Data', $data['Importer Dataid'], 1);
                }
            } catch (Exception $exc) {
		
                $msg = 'Line: '.$row_no.' Entity not created. Reason: '.$exc->getMessage();
                //$msg = 'Line: ' . $row_no . ' Entity not created. Reason: ' . $exc->message;
                $importResult['log_file'] = $this->writeLog($msg);
                $importResult['failed']   = $importResult['failed'] + 1;
		        $this->updateImportDB('Lead Data', $data['Importer Dataid'], 0, $msg);
			
                unset($exc); //because this just keeps living on and it's not required after we get the message.
            }
	    
        }

        return $importResult;
    }

    function changeBusinessLineFromMoveType($moveType){
        switch (strtolower($moveType)) {
            case 'local canada':
            case 'local us':
            case 'max 3':
            case 'max 4':
                return "Local Move";
            case 'interstate':
            case 'inter-provincial':
            case 'cross border':
                return "Interstate Move";
            case 'o&i':
                return "Commercial Move";
            case 'sirva military':
                return "Sirva Military";
            case 'intrastate':
            case 'intra-provincial':
                return "Intrastate Move";
            case 'alaska':
            case 'hawaii':
            case 'international':
                return "International Move";
            default:
                return "Local Move";
        }
    }

    function changeOppStatusForTranslation($status){
        switch (strtolower($status)) {
            case 'new':
                return 'Prospecting';
            case 'attempted contact':
                return 'Qualification';
            case 'survey scheduled':
                return 'Needs Analysis';
            case 'pending':
                return 'Value Proposition';
            case 'inactive':
                return 'Id. Decision Makers';
            case 'ready to book':
                return 'Perception Analysis';
            case 'duplicate':
                return 'Proposal or Price Quote';
            case 'booked':
                return 'Closed Won';
            case 'lost':
                return 'Closed Lost';
            default:
                return 'Value Proposition';
        }
    }

    function checkSourceId($leadData, $sourceId){
        if($leadData == 'NULL'){
            return;
        } else {
            return vtws_getWebserviceEntityId('LeadSourceManager', $sourceId);
        }
    }

    function verifyPhone($cell, $work, $home, $type){
        if(!$type) {
            if ($cell == NULL) {
                if ($work == NULL) {
                    if ($home == NULL) {
                        return "6147599148";
                    } else {
                        return $home;
                    }
                } else {
                    return $work;
                }
            } else {
                return $cell;
            }
        } else {
            if ($cell == NULL) {
                if ($work == NULL) {
                    if ($home == NULL) {
                        return "Home";
                    } else {
                        return "Home";
                    }
                } else {
                    return "Work";
                }
            } else {
                return "Cell";
            }
        }
    }

    function verifyEmail($email){
        if($email == NULL){
            return "support@igcsoftware.com";
        } else {
            return $email;
        }
    }

    function verifyLeadStatus($status,$disposition){
	if($status == 'Dead'){
	    return 'Lost';
	}elseif($disposition == NULL){
            return "Do Not Call Requested";
        } else {
            return $disposition;
        }
    }

    function verifyProgramName($program){
        if($program == NULL){
            return "Imported, No Program Given.";
        } else {
            return $program;
        }
    }

    function verifyMarketingChannel($market){
        if($market == NULL){
            return "None";
        } else {
            return $market;
        }
    }

    function verifySourceName($name){
        if($name == NULL){
            return "None Given";
        } else {
            return $name;
        }
    }

    function verifyProgramTerms($term){
        if($term == NULL){
            return "None Given";
        } else {
            return $term;
        }
    }

    function verifyLostReason($leadDisposition,$lostReason){
        if($leadDisposition != 'Lost'){
	       return 'Not applicable';
	    }elseif($lostReason == NULL){
            return "None Given";
        } else {
            return $lostReason;
        }
    }
    
    function verifyCountry($country){
        if($country == 'US'){
	       return 'United States';
        } else {
            return $country;
        }
    }

    function assignedTo($username){
        if($username == "SADMIN" || $username == "QADMIN" || $username == "admin") {
            return 1;
        } else {
            global $adb;
            $result = $adb->pquery("SELECT `id` FROM `vtiger_users` WHERE `user_name` = ?;", [$username]);
            $row = $result->fetchRow();
            $return = $row['id'];
           if (!$return) {
                return 1;
            } else {
                return $return;
            }
        }
    }

    function ImportUsers($arrResult, $agentId) {
        $user                     = Users_Record_Model::getCurrentUserModel();
        $importResult             = [];
        $importResult['imported'] = $importResult['failed'] = $importResult['success'] = $importResult['alreadyCreated'] = 0;
        foreach ($arrResult as $row_no => $data) {
            $importResult['imported'] = $importResult['imported'] + 1;

            //@NOTE: in my example file Brand is mispelled as Band hence this check.
            $data['brand'] = $data['Band'];
            if (!$data['brand']) {
                $data['brand'] = $data['Brand'];
            }

            $data['status']       = $data['Status'];
            $data['user_name']    = $data['QLAB User ID'];
            $data['last_name']    = $data['Last Name'];
            $data['first_name']   = $data['First Name'];
            $data['email1']       = $data['Email'];
            if (!$data['email1']) {
                $data['email1'] = $this->hardUserEmail;
            }
            $data['phone_work']   = $data['Work Ph'];
            $data['phone_fax']    = $data['Fax'];
            $data['phone_mobile'] = $data['Mobile'];
            $data['timezone']     = $data['Time Zone'];
            $data['qm']           = $data['QM'];
            //Agent Id as passed in is useless.
            //$data['agency_code']    = $data['Agent Id'];
            $data['agency_code']    = $data['QLAB User ID'];
            $testMatch = preg_match('/^Q/i', $data['agency_code']);
            $testMatch1 = preg_match('/^AGT/i', $data['agency_code']);
            $testMatch2 = preg_match('/^[0-9]+$/i', $data['agency_code']);

            if ($testMatch === false || $testMatch1 === false || $testMatch2 === false) {
                print "Error: preg_matched failed: $testMatch -- " . $data['agency_code'] . "\n";
                continue;
            } else if ($testMatch) {
                //Q2984000002
                $data['agency_code'] = preg_replace('/^Q/i', '', $data['agency_code']);
                $data['agency_code'] = preg_replace('/[0-9]{3}$/i', '', $data['agency_code']);
            } else if ($testMatch1) {
                //AGT3359000
                $data['agency_code'] = preg_replace('/^AGT/i', '', $data['agency_code']);
                //actually just skip these.
                continue;
            } else if ($testMatch2) {
                //9999000001
                $data['agency_code'] = preg_replace('/[0-9]{3}$/i', '', $data['agency_code']);
                //$data['Role Name'] = 'Child Van Line User';
                $data['Role Name'] = 'Parent Van Line User';
            }

            if ($data['agency_code']) {
                $data['agent_ids'] = $this->getAgentGroupByAgentCode($data['agency_code']);
            } else {
                $data['agent_ids'] = $agentId;
            }

            $data['agent_process_id']    = $data['Agent Process Id'];
            $data['sts_username']        = $data['STS Username'];
            $data['party_id']            = $data['Party Id'];
            $data['google_username']     = $data['Google Username'];
            $data['google_password']     = $data['Google Password'];
            $data['google_calendar_url'] = $data['Google Calendar URL'];
            $data['google_calendar_url'] = $data['Google Calendar URL'];
            $data['sales_number']        = $data['Sales'];
            $data['mc_id']               = $data['MC Id'];
            //$data['currency_id'] = vtws_getWebserviceEntityId('Currency', "1");
            //$data['reports_to_id'] = vtws_getWebserviceEntityId('Users', "1");
            //@NOTE: so if you just to randomPassword it uses userImport.php::randomPassword totally awesome.
            $data['user_password']    = $this->randomPassword();
            $data['confirm_password'] = $data['user_password'];

            if ($data['agency_code'] == $this->vanline_agency || $data['Role Name'] == 'Parent Van Line User' || $data['Role Name'] == 'Child Van Line User') {
                $data['agent_ids'] = $this->getVanlineGroupByBrand($data['brand']);
                //If they are from the "vanline_agency" but failed to set a sensible role.
                if ($data['Role Name'] != 'Parent Van Line User' && $data['Role Name'] != 'Child Van Line User') {
                    $data['Role Name'] = 'Child Van Line User';
                }
            }

            $data['roleid'] = Users_Edit_View::getRoleIdByName($data['Role Name']);
            if (!$data['roleid']) {
                $data['roleid'] = Users_Edit_View::getRoleIdByName('Sales Manager');
            }

            try {
                if (!$data['agent_ids']) {
                    //fail if the agency is not existant.
                    throw new WebServiceException(100001, "Entity not created. Reason: invalid Agency (".$data['agency_code'].")");
                }
                if ($userInfo = $this->userExist($data['user_name'])) {
                    //this is confusing I'm using arrays.
                    //$infoUser = split('#', $userId);
                    $needsUpdate        = true;
                    $existingAgentIds   = explode(' |##| ', $userInfo['agent_ids']);
                    $existingAgentIds[] = $data['agent_ids'];
                    $agentIdString      = '';
                    foreach ($existingAgentIds as $agentId) {
                        if ($data['agent_ids'] != $agentId) {
                            if ($agentIdString) {
                                $agentIdString .= ' |##| ';
                            }
                            $agentIdString .= $agentId;
                        } else {
                            //the user already exists for this agency so just break out and skip the mysql update.
                            $needsUpdate = false;
                            break;
                        }
                    }
                    if ($needsUpdate) {
                        $db = PearDatabase::getInstance();
                        $db->pquery("UPDATE vtiger_users SET agent_ids = ? WHERE id = ?", [$agentIdString, $userInfo['id']]);
                    }
                    $importResult['alreadyCreated'] = $importResult['alreadyCreated'] + 1;
                } else {
                    $entity = vtws_create('Users', $data, $user);
                    if (!$entity) {
                        throw new WebServiceException(100001, "Entity not created. Reason Un-known");
                    }
                    /*
                     * so users don't go to the crm entity table.
                    if (isset($data['createdtime']) && $data['createdtime'] != '' && isset($data['modifiedtime']) && $data['modifiedtime'] != '') {
                        $this->updateCrmEntityTable($entity['id'], $data['createdtime'], $data['modifiedtime']);
                    }
                    */
                    $importResult['success'] = $importResult['success'] + 1;
                }
            } catch (Exception $exc) {
                $msg                      = 'Line: '.$row_no.' ERROR user: '.$data['user_name'].' not created. Reason: '.$exc->getMessage();
                $importResult['log_file'] = $this->writeLog($msg);
                $importResult['failed']   = $importResult['failed'] + 1;
            }
	    
	    $_FILES['userfile']['imported_rows'] = $row_no;
	    
        }

        return $importResult;
    }
    
    /*
     * Search By unqualified lead id for a matching contact. If found will create the comment
     */

    function ImportLeadsComments($arrResult, $agentId) {
        $user                     = Users_Record_Model::getCurrentUserModel();
        $importResult             = [];
        $importResult['imported'] = $importResult['failed'] = $importResult['success'] = $importResult['alreadyCreated'] = 0;
        foreach ($arrResult as $row_no => $data) {
            $importResult['imported']   = $importResult['imported'] + 1;
            $data['Unqualified Lead'] = trim($data['Unqualified Lead']);
            try {
                global $adb;
                $res = $adb->pquery("SELECT * FROM vtiger_sirvaimporter_ids 
                                        INNER JOIN vtiger_crmentity ON vtiger_sirvaimporter_ids.crmid = vtiger_crmentity.crmid
                                        WHERE deleted =0 AND module != 'Opportunities' AND importid = ? ORDER BY id DESC",
                                    [$data['Unqualified Lead']]);
                if ($res && $adb->num_rows($res) > 0) {
                    $arr = $adb->fetch_array($res);
                    $data['assigned_user_id'] = vtws_getWebserviceEntityId('Users', $this->getUserFromExcel($data['Created By']));
                    $data['related_to']       = vtws_getWebserviceEntityId($arr['module'], $arr['crmid']);
                    $data['commentcontent']   = $data['Description'];
                    $data['provider']         = $data['Provider'];
                    $data['note_source']      = $data['Source'];
                    $data['agentid']          = $agentId;
		    
                    $entity = vtws_create('ModComments', $data, $user);
                    if (!$entity) {
                        throw new WebServiceException(100001, "Entity not created. Reason Un-known");
                    }
                    if ((!isset($data['createdtime']) || $data['createdtime'] != '') && (!isset($data['modifiedtime']) || $data['modifiedtime'] != '')) {
                        $this->updateCrmEntityTable($entity['id'], $data['Note Create DateTime'], $data['Note Create DateTime']); #The "Note Create DateTime" field was "Date/Time"
                    }
                    $importResult['success'] = $importResult['success'] + 1;
		    
		    $this->updateImportDB('Lead Notes', $data['Importer Dataid'], 1);
		    
                } else {
                    $msg                      = 'Line: '.$row_no.' ERROR the comment was not created. Reason: Related Record Not Found';
                    $importResult['log_file'] = $this->writeLog($msg);
                    $importResult['failed']   = $importResult['failed'] + 1;
		    $this->updateImportDB('Lead Notes', $data['Importer Dataid'], 0, $msg);
                }
            } catch (Exception $exc) {
                $msg                      = 'Line: '.$row_no.' ERROR the comment was not created. Reason: '.$exc->getMessage();
                $importResult['log_file'] = $this->writeLog($msg);
                $importResult['failed']   = $importResult['failed'] + 1;
		$this->updateImportDB('Lead Notes', $data['Importer Dataid'], 0, $msg);
            }
        }

        return $importResult;
    }
    
    /**
     * Search by Qualified Lead number. When found will create a new event in the calendar attached to
     * both the opportunity and the contact
     * 
     * @param array $arrResult Array with the activities information
     * @param int  $agentId Agent Id
     * @throws WebServiceException
     */
    
    function ImportLeadActivities($arrResult, $agentId) {
        $user                     = Users_Record_Model::getCurrentUserModel();
        $importResult             = [];
        $importResult['imported'] = $importResult['failed'] = $importResult['success'] = $importResult['alreadyCreated'] = 0;
        foreach ($arrResult as $row_no => $data) {
            $importResult['imported'] = $importResult['imported'] + 1;
            $arr_start_date = preg_split('/\s+/', $data['Start Date']);
            $arr_end_date   = preg_split('/\s+/', $data['End Date']);
            $startDate  = new DateTime($arr_start_date[0]);
            $start_date = $startDate->format('Y-m-d');
            $start_time = $arr_start_date[1].' '.$arr_start_date[2];
            $endDate  = new DateTime($arr_end_date[0]);
            $end_date = $endDate->format('Y-m-d');
            $end_time = $arr_end_date[1].' '.$arr_end_date[2];
            $data['assigned_user_id'] = vtws_getWebserviceEntityId('Users', $this->getUserFromExcel($data['Assigned To']));
            $data['duration_hours']   = round(floatval($data['Duration']) / 60);
            $data['duration_minutes'] = round(floatval($data['Duration']));
            $data['date_start']       = $start_date;
            $data['due_date']         = $end_date;
            $data['time_start']       = $start_time;
            $data['time_end']         = $end_time;
            $data['location']         = $data['Location'];
            $data['activitytype']     = $data['Type'];
            $data['subject']          = $data['Description'];
            $data['eventstatus']      = $data['Status'];
            $data['description']      = $data['Description'];
            $data['agentid']          = $agentId;
            $data['Qualified Lead'] = trim($data['Qualified Lead']);
	    $data['visibility'] = 'Private';
            global $adb;
            $res = $adb->pquery("SELECT * FROM vtiger_sirvaimporter_ids 
                                        INNER JOIN vtiger_crmentity ON vtiger_sirvaimporter_ids.crmid = vtiger_crmentity.crmid
                                        WHERE deleted = 0  AND qualifiedlead = ? ORDER BY id DESC",
                                [$data['Qualified Lead']]);
	    
	    //Both the contact and the opp are match by the same ID. We can use both on the event as related modules
	    
            if ($res && $adb->num_rows($res) > 0) {		
		while ($row = $adb->fetch_array($res)) {
		    if ($row['module'] == 'Contacts') {
			$data['contact_id'] = vtws_getWebserviceEntityId($row['module'], $row['crmid']);
		    }else{
			$data['parent_id'] = vtws_getWebserviceEntityId($row['module'], $row['crmid']);
		    }
		}
		
                try {
                    $entity = vtws_create('Events', $data, $user);
                    if (!$entity) {
                        throw new WebServiceException(100001, "Entity not created. Reason Un-known");
                    }
                    $importResult['success'] = $importResult['success'] + 1;
		    $this->updateImportDB('Qualified Lead Activity', $data['Importer Dataid'], 1);
                } catch (Exception $exc) {
                    $msg                      = 'Line: '.$row_no.' ERROR: '.$exc->getMessage();
                    $importResult['log_file'] = $this->writeLog($msg);
                    $importResult['failed']   = $importResult['failed'] + 1;
		    $this->updateImportDB('Qualified Lead Activity', $data['Importer Dataid'], 0, $msg);
                }
            } else {
                $msg                      = 'Line: '.$row_no.' ERROR: Related Record Not Found';
                $importResult['log_file'] = $this->writeLog($msg);
                $importResult['failed']   = $importResult['failed'] + 1;
		$this->updateImportDB('Qualified Lead Activity', $data['Importer Dataid'], 0, $msg);
            }
	    
	    
	    
        }

        return $importResult;
    }

    function ImportExtraStops($arrResult, $agentId) {
        $user = Users_Record_Model::getCurrentUserModel();
        $importResult = array();
        $importResult['imported'] = $importResult['failed'] = $importResult['success'] = $importResult['alreadyCreated'] = 0;
        foreach ($arrResult as $row_no => $data) {
            $importResult['imported'] = $importResult['imported'] + 1;
            $data['Unqualified Lead'] = trim($data['Unqualified Lead']);
            $sequence = trim(substr(trim($data['Stop Name']), -2));
            $stopType = trim(substr(trim($data['Stop Name']), 0, strlen(trim($data['Stop Name'])) - 2));
            $phonetype1 = $phonetype2 = $phone1 = $phone2 = '';
            if ($data['Mobile Phone'] !== '') {
                $phonetype1 = 'Mobile';
                $phone1     = $data['Mobile Phone'];
            }
            if ($data['Home Phone'] !== '') {
                if ($phonetype1 !== '') {
                    $phonetype2 = 'Home';
                    $phone2     = $data['Home Phone'];
                } else {
                    $phonetype1 = 'Home';
                    $phone1     = $data['Home Phone'];
                }
            }
            if ($data['Work Phone'] !== '') {
                if ($phonetype1 !== '') {
                    $phonetype2 = 'Work';
                    $phone2     = $data['Work Phone'];
                } else {
                    $phonetype1 = 'Work';
                    $phone1     = $data['Work Phone'];
                }
            }
            try {
                global $adb;
                $res = $adb->pquery("SELECT * FROM vtiger_sirvaimporter_ids 
                                        INNER JOIN vtiger_crmentity ON vtiger_sirvaimporter_ids.crmid = vtiger_crmentity.crmid
                                        WHERE deleted =0 AND importid = ? AND module=?",
                                    [$data['Unqualified Lead'], 'Opportunities']);
                if ($res && $adb->num_rows($res) > 0) {
                    $arr = $adb->fetch_array($res);
                    $data['stop_opp'] = vtws_getWebserviceEntityId('Opportunities', $arr['crmid']);
                } else {
                    throw new WebServiceException(100001, "Stop not created. Could not find opportunity");
                }
                $stopData = [
                                'extrastops_sequence' => $sequence,
				'extrastops_name' => $data['Stop Name'],
                                'extrastops_address1' => $data['Address1'],
                                'extrastops_address2' => $data['Address2'],
                                'extrastops_city' =>  $data['City'],
                                'extrastops_state' => $data['State'],
                                'extrastops_zip' => $data['Zip Code'],
                                'extrastops_country' =>  $data['Country'],
                                'extrastops_phonetype1' => $phonetype1,
                                'extrastops_phone1' => $phone1,
                                'extrastops_phonetype2' => $phonetype2,
                                'extrastops_phone2' => $phone2,
                                'extrastops_relcrmid' => $data['stop_opp'],
                                'extrastops_type' => $stopType,
                                'assigned_user_id' => vtws_getWebserviceEntityId('Users', $this->getUserFromExcel($data['Assigned To'])),
                                'agentid' => $agentId,
                             ];
                //removed stops contact since I have confirmed the CSV only contains the contact name; I can forsee a lot of situations where you
                //can't just explode on space and use the first & last name to find it.
                //'extrastops_contact' => $data['Contact Name'],
		
                vtws_create('ExtraStops', $stopData, $user);
                $importResult['success'] = $importResult['success'] + 1;
		
		$this->updateImportDB('Extra Stops Data', $data['Importer Dataid'], 1);
		
            } catch (Exception $exc) {
                $msg                      = 'Line: '.$row_no.' ERROR: '.$exc->getMessage();
                $importResult['log_file'] = $this->writeLog($msg);
                $importResult['failed']   = $importResult['failed'] + 1;
		$this->updateImportDB('Extra Stops Data', $data['Importer Dataid'], 0, $msg);
            }
	    
        }

        return $importResult;
    }

    function getUserFromExcel($excelUser) {
        global $adb;
        $user = Users_Record_Model::getCurrentUserModel();
        $res  = $adb->pquery("SELECT id FROM vtiger_users WHERE user_name = ?", [$excelUser]);
        if ($adb->num_rows($res) > 0) {
            return $adb->query_result($res, 0, 'id');
        } else {
            return $user->get('id');
        }
    }

    function saveRelation($importId, $qualifiedLead, $crmId, $modulo) {
        global $adb;
        $adb->pquery("INSERT INTO vtiger_sirvaimporter_ids(`importid`, `qualifiedlead`,`crmid`, `module`) VALUES (?,?,?,?)", [$importId,$qualifiedLead, $crmId, $modulo]);
    }

    function isAlreadyImported($importId, $module) {
        //return true; // Disable this check for now. Not sure if it's required.
        //this is a required operation, because without it, it returns irrelevant errors.
        global $adb;
	
	$sql = "SELECT * FROM vtiger_sirvaimporter_ids 
		INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_sirvaimporter_ids.crmid 
		WHERE vtiger_crmentity.deleted=0 AND vtiger_sirvaimporter_ids.`importid`=? AND vtiger_sirvaimporter_ids.`module`=?";
	
        $params = [$importId, $module];
        
	$res = $adb->pquery($sql, $params);
		
        if ($adb->num_rows($res) > 0) {
            return false;
        } else {
            return true;
        }
    }

    function userExist($userName) {
        global $adb;
        $res = $adb->pquery("SELECT * FROM vtiger_users WHERE user_name = ?", [$userName]);
        if ($adb->num_rows($res) > 0) {
            //return $adb->query_result($res, 0, 'id') . '#' . $adb->query_result($res, 0, 'agent_ids');
            return [
                'id'        => $adb->query_result($res, 0, 'id'),
                'agent_ids' => $adb->query_result($res, 0, 'agent_ids')
            ];
        } else {
            return false;
        }
    }

    /*
    function getRole($rolname) {
        global $adb;


        $res = $adb->pquery("SELECT * FROM vtiger_role WHERE rolename = ?", array(trim($rolname)));
        if ($adb->num_rows($res) > 0) {
            return $adb->query_result($res, 0, 'roleid');
        } else {
            return 'H37'; // rol por default???
        }
    }
    */
    function updateCrmEntityTable($crmid, $createdtime, $modifiedime) {
        global $adb;
        $crmid = explode('x', $crmid)[1];
        $createdtime = DateTimeField::convertToDBTimeZone($createdtime);
        $createdtime = $createdtime->format('Y-m-d H:i:s');
        $modifiedime = DateTimeField::convertToDBTimeZone($modifiedime);
        $modifiedime = $modifiedime->format('Y-m-d H:i:s');
        $adb->pquery("UPDATE vtiger_crmentity SET createdtime = ?, modifiedtime=? WHERE crmid=?", [$createdtime, $modifiedime, $crmid]);
    }

    function readCsv($delimiter) {
        $cli = false;
        if (php_sapi_name() == 'cli') {
            $cli = true;
        }
        global $root_directory;
        $tempName       = $_FILES['userfile']['tmp_name'];
        $targetLocation = $root_directory."test/upload/".$_FILES['userfile']['name'];
        if (
            is_uploaded_file($tempName) ||
            ($cli && file_exists($tempName))
        ) {
            if ($cli) {
                //rename($tempName, $targetLocation);
                //meh don't bother moving the file if this is cmd line run.
                $targetLocation = $tempName;
            } else {
                move_uploaded_file($tempName, $targetLocation);
            }
            $handle = fopen($targetLocation, "r");
            if ($handle) {
                $arrResult = [];
                $header    = NULL;
		$i = 1;
                while (($data = fgetcsv($handle, 2500, $delimiter)) !== false) {
                    if ($header === NULL) {
                        $header = $data;
			
			array_walk($header, 
			    function(&$a) {
				$a = trim(str_replace('#', '', $a)); // Remove the from the header
			    }
			);
			
			
                        continue;
                    }
                    //Remove NULL Values from data array
                    $data        = array_replace($data, array_fill_keys(array_keys($data, 'NULL'), ''));
		    if($i > $_FILES['userfile']['last_imported_line']){
			$arrResult[] = array_combine($header, $data);
			$_FILES['userfile']['last_imported_line'] =  $_FILES['userfile']['last_imported_line'] + 1;
			
		    }
		    $i = $i + 1;
		    
		    //Add 1001 to the import array, we only need 1000
		    if(count($arrResult) == 1000){
			break;
		    }
                    
                }
		
                fclose($handle);
		
		
            } else {
                $arrResult = 'Fail';
            }
        } else {
            $arrResult = 'Fail';
        }

	//Walk the array trough vtlib_purify to remove "special" characters
	array_walk_recursive($arrResult, 'vtlib_purify');
	
        return $arrResult;
    }

    function writeLog($message) {
	
        global $root_directory, $site_URL;
        $logFile = $root_directory."test/upload/vgsimporter.txt";
        $fh = fopen($logFile, 'a');
        if ($fh) {
            $today      = date("Y-m-d, g:i a");
            $stringData = "$today \t";
            $stringData .= " - $message \n";
            fwrite($fh, $stringData);
            fclose($fh);
        }
        if (substr($site_URL, -1) !== '/') {
            return $site_URL.'/test/upload/vgsimporter.txt';
        } else {
            return $site_URL.'test/upload/vgsimporter.txt';
        }
    }

    function resetLogFile() {
        global $root_directory;
        $logFile = $root_directory."test/upload/vgsimporter.log";
        if (file_exists($logFile)) {
            unlink($logFile);
        }
    }

    //pulled from userImport.php in go_live
    function randomPassword() {
        $alphabet    = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass        = []; //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n      = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }

        return implode($pass); //turn the array into a string
    }

    //pulled from syncwebservice.php
    function getAgentGroupByAgentCode($agentCode) {
        $db     = PearDatabase::getInstance();
        $sql    = "SELECT `agentmanagerid` FROM `vtiger_agentmanager` WHERE `agency_code` = ?";
        $result = $db->pquery($sql, [$agentCode]);
        $row    = $result->fetchRow();
        if ($row) {
            return $row['agentmanagerid'];
        }

        return false;
    }

    function getVanlineGroupByBrand($brand) {
        $rv = false;

        $db = PearDatabase::getInstance();

        //require a vanlinemanager_id if there is not agency.
        //@TODO: replace if we add a brand to the database so we can select the id.
        if ($brand == 'AVL') {
            $vanline_id[] = 1;
        } elseif ($brand == 'NAVL') {
            $vanline_id[] = 9;
        } else {
            //well I don't know they get both!
            //this is because the example data has 'asdfsd' entered for the Band.
            $vanline_id[] = 1;
            $vanline_id[] = 9;
        }

        foreach ($vanline_id as $id) {
            $sql    = "SELECT `vanlinemanagerid` FROM `vtiger_vanlinemanager` WHERE `vanline_id` = ?";
            $result = $db->pquery($sql, [$id]);
            $row    = $result->fetchRow();
            if ($row) {
                if ($rv) {
                    $rv .= ' |##| ';
                }
                $rv .= $row['vanlinemanagerid'];
            }
        }

        return $rv;
    }

    function createLeadSource($postdata) {
        $db     = PearDatabase::getInstance();
        //This is to match the "wildcard" agency_code.
        $specialAllAgency = 9999000;
        $errors = [];

        //verify inputs.
        //MUST HAVE THIS
        if (!$this->validateMandatory($postdata['Brand']) && !$this->validateMandatory($postdata['LMPAssignedAgentOrgId']) ) {
            $errors[] = ['code'    => 'INVALID_VALUE',
                'message' => 'Either LMPAssignedAgentOrgId OR Brand must be specified.'];
        }

        //MUST HAVE THIS
        if (!$this->validateMandatory($postdata['AAProgramName'])) {
            $errors[] = ['code'    => 'INVALID_VALUE',
                'message' => 'AAProgramName must be specified.'];
        }

        //return if there are errors.
        if (count($errors) > 0) {
            $response = ['success' => false, 'errors' => $errors];
            return json_encode($response);
        }

        //we are clear of validation, so let's initialize!
        //I realize this is unnecessary, but it makes it clearer to me.
        $agentid = '';
        $vanlinemanager_id = '';
        $agencyCode = $postdata['LMPAssignedAgentOrgId'];
        $brand      = $postdata['Brand'];
        $active     = $postdata['LeadSourceActive'];

        //if active is explicitly false it's off otherwise it's on.
        if ($active === false || $active === 'false' || $active == 'off' || strtolower($active) == 'n') {
            $active = 'off';
        } else if (strtolower($active) == 'y') {
            $active = 'on';
        } else {
            $active = 'on';
        }

        if ($agencyCode != $specialAllAgency) {
            //require Agency unless it's special vanline agency
            $sql    = "SELECT agentmanagerid,vanline_id FROM `vtiger_agentmanager` WHERE agency_code=?";
            $result = $db->pquery($sql, [$agencyCode]);
            $row    = $result->fetchRow();
            if ($row == NULL) {
                $errCode    = "INVALID_AGENTID";
                $errMessage = "The provided agentid is not valid" . print_r($row);
                $response   = json_encode($this->generateErrorArray($errCode, $errMessage));
                return($response);
            }
            $agentid = $row['agentmanagerid'];
            $vanlinemanager_id = $row['vanline_id'];
            $brand = $this->getCarrierCodeFromAgencyCode($agencyCode);
        } else {
            //require a vanlinemanager_id if there is not agency.
            //@TODO: replace if we add a brand to the database so we can select the id.
            if ($brand == 'AVL') {
                $vanline_id = 1;
            } elseif ($brand == 'NAVL') {
                $vanline_id = 9;
            } else {
                $errCode    = "INVALID_BRAND";
                $errMessage = "The provided brand is not valid";
                $response   = json_encode($this->generateErrorArray($errCode, $errMessage));
                return($response);
            }
            $sql    = "SELECT vanlinemanagerid FROM `vtiger_vanlinemanager` WHERE vanline_id=?";
            $result = $db->pquery($sql, [$vanline_id]);
            $row    = $result->fetchRow();
            if ($row == NULL) {
                $errCode    = "INVALID_VANLINE_ID";
                $errMessage = "The calculated vanline_id is not valid";
                $response   = json_encode($this->generateErrorArray($errCode, $errMessage));
                return($response);
            }
            $vanlinemanager_id = $row['vanlinemanagerid'];
        }

        //now that we have verified everything else, and are ready to go with these values,
        //make sure it doesn't already exist!
        //So we THINK we need to select for the specific agency OR the willcard agency AND brand.
        $params = [];
        $sqlWhere = '';

        if ($postdata['AAProgramName']) {
            $sqlWhere .= " program_name = ? ";
            $params[] = $postdata['AAProgramName'];
        }

        /*
         * taken out because alex overpromised.
        if ($postdata['LMPSourceId']) {
            $sqlWhere .= ($sqlWhere?' AND ':'')." lmp_source_id = ? ";
            $params[] = $postdata['LMPSourceId'];
        }

        if ($postdata['AASourceType']) {
            $sqlWhere .= ($sqlWhere?' AND ':'')." source_type = ? ";
            $params[] = $postdata['AASourceType'];
        }

        if ($postdata['MarketingChannel']) {
            $sqlWhere .= ($sqlWhere?' AND ':'')." marketing_channel = ? ";
            $params[] = $postdata['MarketingChannel'];
        }

        if ($postdata['AASourceName']) {
            $sqlWhere .= ($sqlWhere?' AND ':'')." source_name = ? ";
            $params[] = $postdata['AASourceName'];
        }
        */
        //set row to false so that unless it gets set we create
        $row = false;
        if ($sqlWhere) {
            //We need something seemingly unique to select on.
            $sql      = "SELECT leadsourcemanagerid FROM `vtiger_leadsourcemanager` WHERE "
                .($sqlWhere?$sqlWhere.' AND ':'')." (agency_code = ? OR agency_code = ?)"
                ." AND `brand`=?";
            $sql .= " LIMIT 1";
            $params[] = $agencyCode;
            $params[] = $specialAllAgency;
            $params[] = $brand;
            $result = $db->pquery($sql, $params);
            $row    = $result->fetchRow();
        }

        if($row) {
            //already exists
            //We could fail since it's "createLeadSource", but seems softer to just return the existing id.
            $sourceId = $row[0];
            //encode it to proper format
            $wsLeadSrcId = vtws_getWebserviceEntityId('LeadSourceManager', $sourceId);
        }else {
            //so we are creating one do some extra validation
            if (!$this->validateMandatory($postdata['AASourceName'])) {
                $errors[] = ['code'    => 'INVALID_VALUE',
                    'message' => 'AASourceName must be specified.'];
            }

            if (!$this->validateMandatory($postdata['AASourceType'])) {
                $errors[] = ['code'    => 'INVALID_VALUE',
                    'message' => 'AASourceType must be specified.'];
            }

            if (!$this->validateMandatory($postdata['MarketingChannel'])) {
                $errors[] = ['code'    => 'INVALID_VALUE',
                    'message' => 'MarketingChannel must be specified.'];
            }

            //return if there are errors.
            if (count($errors) > 0) {
                $response = ['success' => false, 'errors' => $errors];
                return json_encode($response);
            }

            //SOOO we are good! let's add it.
            $leadSrcData = [
                'agentid'           => $agentid,
                'source_name'       => $postdata['AASourceName'],
                'source_type'       => $postdata['AASourceType'],
                'marketing_channel' => $postdata['MarketingChannel'],
                'lmp_program_id'    => $postdata['LMPProgramId'],
                'lmp_source_id'     => $postdata['LMPSourceId'],
                'program_name'      => $postdata['AAProgramName'],
                'program_terms'     => $postdata['AAProgramTerms'],
                'brand'             => $brand,
                'agency_code'       => $agencyCode,
                'active'            => $active,
                'vanlinemanager_id' => $vanlinemanager_id,
                'agency_related'    => vtws_getWebserviceEntityId('AgentManager', $agentid),
                'vanline_related'   => vtws_getWebserviceEntityId('VanlineManager', $vanlinemanager_id),
            ];
            try {
                $user         = new Users();
                $current_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
                $leadSrc      = vtws_create('LeadSourceManager', $leadSrcData, $current_user);
            } catch (WebServiceException $ex) {
                $response = $this->generateErrorArray('FAILED_CREATION_OF_LEADSOURCE', $ex->getMessage());

                return json_encode($response);
            }
            $wsLeadSrcId = $leadSrc['id'];
        }

        //$leadSrcId   = explode('x', $wsLeadSrcId)[1];
        $response = ['success' => true, 'result' => ['LeadSrcId' => $wsLeadSrcId]];
        return json_encode($response);
    }

    function validateMandatory($value,$mandatory = true){
        if($mandatory && empty($value)){
            return false;
        }
        return true;
    }
    function generateErrorArray($errCode, $errMessage) {
        $result            = [];
        $result['success'] = 'false';
        $error             = [];
        $error['code']     = $errCode;
        $error['message']  = $errMessage;
        $result['errors']  = [$error];

        return $result;
    }
    function getCarrierCodeFromAgencyCode($agentCode) {
        $db     = PearDatabase::getInstance();
        $sql    = "SELECT IF(`vtiger_vanlinemanager`.vanline_id = 9, 'NAVL', IF(`vtiger_vanlinemanager`.vanline_id = 1, 'AVL', '')) AS carrier_code FROM `vtiger_vanlinemanager`
               JOIN `vtiger_agentmanager` ON `vtiger_vanlinemanager`.vanlinemanagerid = `vtiger_agentmanager`.vanline_id
               WHERE `vtiger_agentmanager`.agency_code = ?";
        $result = $db->pquery($sql, [$agentCode]);
        $row    = $result->fetchRow();
        if ($row) {
            return $row['carrier_code'];
        }

        return false;
    }
    
    function insertBookingAgent($opportunityId,$bookerCode){
	$db = PearDatabase::getInstance();
	$result = $db->pquery('SELECT agentsid, agentmanager_id FROM `vtiger_agents` WHERE agent_number = ? LIMIT 1', [$bookerCode]);
	if($result && $db->num_rows($result) > 0){
	    $row = $result->fetchRow();
	    $agents_id = $row['agentsid'];
	    $agentManagerId = $row['agentmanager_id'];
	    $agent_type = 'Booking Agent'; // 1 -> Booking Agent
	    $view_level = 'full';
	    //insert
	    $sql = "INSERT INTO `vtiger_participatingagents` (rel_crmid, agents_id, agentmanager_id, agent_type, view_level, status)
			    VALUES (?,?,?,?,?,?)";
	    $db->pquery($sql, [$opportunityId, $agents_id, $agentManagerId, $agent_type, $view_level, 'Accepted']);
	    //this is probably a concurrency issue waiting to happen
	    $currentParticipantId = $db->pquery('SELECT LAST_INSERT_ID()', [])->fetchRow()[0];
	    //
	    //Do make any request on historical data
	    //
//	    $requestsModel = Vtiger_Module_Model::getInstance('OASurveyRequests');
//	    if($requestsModel && $requestsModel->isActive() && $agent_type != 'Hauling Agent' && $agent_type != 'Destination Agent'){
//		    $requestId = $requestsModel->saveOASurveyRequest($view_level, 'Pending', $agent_type, $agents_id, 'Opportunities', $agents_id, $opportunityId, 'create', '');// $agents_id???
//		    $db->pquery("UPDATE `vtiger_participatingagents` SET oasurveyrequest_id = ? WHERE participatingagentsid = ?", [$requestId, $currentParticipantId]);
//	    }
	}
	
    }
    
    function getSalesPerson($userQId){
	$db = PearDatabase::getInstance();
	$userQId = explode('_', $userQId);
	
	$result = $db->pquery('SELECT first_name, last_name FROM vtiger_users WHERE user_name=?', [$userQId[0]]);
	if($result && $db->num_rows($result) > 0){
	    $salesPerson = $db->query_result($result, 0, 'first_name') . ' ' . $db->query_result($result, 0, 'last_name');
	    return $salesPerson;
	}  else {
	    return '';
	}
    }
    
    function getSource($data, $agentId) {
	
	$db = PearDatabase::getInstance();
	$result = $db->pquery('SELECT leadsourcemanagerid FROM vtiger_leadsourcemanager 
				    INNER JOIN vtiger_crmentity ON vtiger_leadsourcemanager.leadsourcemanagerid=vtiger_crmentity.crmid 
				    WHERE deleted=0 AND program_name=? AND agency_related=?', [$data['Program Name'], $agentId]);
	if ($result && $db->num_rows($result) > 0) {
	    return vtws_getWebserviceEntityId('LeadSourceManager', $db->query_result($result, 0, 'leadsourcemanagerid'));
	} else {
	    
	    $agentRecordModel = Vtiger_Record_Model::getInstanceById($agentId, 'AgentManager');

	    try {
		
		$user23         = new Users();
		$user = $user23->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());

		$leadSource['active'] = 1;
		$leadSource['agency_code'] = $agentRecordModel->get('agency_code');
		$leadSource['agency_related'] = vtws_getWebserviceEntityId('AgentManager', $agentId);
		$leadSource['marketing_channel'] = $data['Mktg Channel'];
		$leadSource['program_name'] = $data['Program Name'];
		$leadSource['program_terms'] = $data['Program Terms'];
		$leadSource['source_name'] = $data['Source Name'];
		if($data['Lead Source'] == ''){
		    $leadSource['source_type'] = 'NULL';
		}  else {
		    $leadSource['source_type'] = $data['Lead Source'];
		}
		 
		if(isset($_REQUEST['agency_brand']) && $_REQUEST['agency_brand'] != ''){
		    $leadSource['brand'] = $_REQUEST['agency_brand'];
		}  else {
		    $leadSource['brand'] = '';
		}
		
		
		$leadSource['vanlinemanager_id'] = $agentRecordModel->get('vanline_id');
		$leadSource['vanline_related'] = vtws_getWebserviceEntityId('VanlineManager',$agentRecordModel->get('vanline_id'));
		$leadSource['agentid'] = $agentId;
		$leadSource['assigned_user_id'] = vtws_getWebserviceEntityId('Users', $user->id);
		
		$leadSource = vtws_create('LeadSourceManager', $leadSource, $user);
		
		return $leadSource['id'];
		
		
	    } catch (Exception $exc) {
		return '';
	    }
	}
    }
    
    function getSelfHaul($agentId){
	$agentModel = Vtiger_Record_Model::getInstanceById($agentId, 'AgentManager');
	
	if($agentModel->get('self_haul') == 'on'){
	    return 'on';
	}  else {
	    return 'off';
	}
    }

    function updateCreatedTime($recordId, $createdTime){
	$createdTime = explode(' ', $createdTime);
	$createDate = DateTimeField::convertToUserFormat($createdTime[0]);
	$createdTime = substr($createdTime[1], 0, 8);
	$createDBTimezone = DateTimeField::convertToDBTimeZone($createDate . ' ' . $createdTime);
	$createDBDate =  $createDBTimezone->format('Y-m-d') . ' ' . $createDBTimezone->format('H:i:s');
	
	$db = PearDatabase::getInstance();
	$db->pquery('UPDATE vtiger_crmentity SET createdtime=? WHERE crmid=?', [$createDBDate, $recordId]);
	
    }
    
    function updateOppSelfHaul($recordId, $selfHaul){
	$db = PearDatabase::getInstance();
	if($selfHaul == 'on'){
	    $selfHaul = 1;
	}  else {
	    $selfHaul = 0;
	}
	
	$db->pquery('UPDATE vtiger_potential SET self_haul=? WHERE potentialid=?', [$selfHaul, $recordId]);
    }
    
    function readDataFromDB($dataType) {

	$tableName = $this->getTableName($dataType);

	$db = PearDatabase::getInstance();
	$db->startTransaction();
	$sql = "SELECT * FROM $tableName WHERE parsed=0 OR parsed IS NULL LIMIT 1 FOR UPDATE";
	$result = $db->pquery($sql);
	$data = [];
	$parsedIds = [];
	if ($result && $db->num_rows($result) > 0) {	    
	    while ($row = $db->fetchByAssoc($result)) {
		$parsedIds[] = $row['importer_dataid'];
		$dataImport = [];
		
		//Need to set the key as they are on the csv :/
		foreach ($row as $key => $value) {
		    $keys = explode('_',$key);
		    
		    array_walk($keys, 
			function(&$a) {
			    $a = ucfirst($a);
			}
		    );
		    $keys = implode(' ', $keys);
		    $dataImport[$keys] = $value;
		}
		
		$dataImport        = array_replace($dataImport, array_fill_keys(array_keys($dataImport, 'NULL'), ''));
		
		$data[] = $dataImport;
		
	    }
	}

	if (count($parsedIds) > 0) {
	    //Marked this 100 records as being imported. Just in case the proces fires from a diff server
	    $sql = "UPDATE $tableName SET parsed=1 WHERE importer_dataid IN (" . generateQuestionMarks($parsedIds) . ")";
	    $db->pquery($sql, [$parsedIds]);
	}
	
	$db->completeTransaction();
	
	return $data;
    }
    
    function updateImportDB($dataType, $dataId, $imported, $importResult = ''){
	$db = PearDatabase::getInstance();
	
	$tableName = $this->getTableName($dataType);

    if($importResult == '' && ($imported = 0 || $imported = '0')){
        $params = [0, $imported, $importResult, $dataId];
    }else{
        $params = [1, $imported, $importResult, $dataId];
    }
	
	$sql = "UPDATE $tableName SET parsed=?, imported=?, import_result=? WHERE importer_dataid = ?";
	$db->pquery($sql, $params);
	
    }
    
    function getTableName($dataType){
	switch ($dataType) {
	    case 'Lead Data':
		return 'sirva_importer_leaddata';
		
		break;
	    
	    case 'Lead Notes':
		return 'sirva_importer_leadnotes';
		
		break;
	    case 'Qualified Lead Activity':
		return 'sirva_importer_leadactivity';
		
		break;
	    
	    case 'Extra Stops Data':
		return 'sirva_importer_extrastops';
		
		break;

	    default:
		return '---';
		break;
	}
    }

}
