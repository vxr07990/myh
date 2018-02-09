<?php

require_once('includes/main/WebUI.php');
require_once('include/Webservices/Create.php');
require_once('include/Webservices/Revise.php');
require_once('modules/Users/Users.php');
require_once('customWebserviceFunctions.php');

function leadSourceImport()
{
    echo "Start Lead Source Manager Import <br>\n";
    $db = PearDatabase::getInstance();

    //file_put_contents('logs/devLog.log', "\n IT IS SIRVA", FILE_APPEND);
    $filePath = 'go_live/sirva/SirvaLeadSources.csv';
    //file_put_contents('logs/devLog.log', "\n FILE PATH: ".$filePath, FILE_APPEND);

    $values = array();

    $requiredFields = [
            'LMPAssignedAgentOrgId',
            'Brand',
            'LMPProgramId',
            'LMPSourceId',
            'MarketingChannel',
            'AASourceName',
            'AAProgramName',
            //'AAProgramTerms',
            'AASourceType',
            'LeadSourceActive'
        ];

    $headerMapping = [
        'Agency Code' => 'LMPAssignedAgentOrgId',
        'Brand' => 'Brand',
        'LMP Program ID' => 'LMPProgramId',
        'LMP Source Id' => 'LMPSourceId',
        'MKTG Channel' => 'MarketingChannel',
        'Source Name' => 'AASourceName',
        'Program Name' => 'AAProgramName',
        'Program Terms' => 'AAProgramTerms',
        'Source Type' => 'AASourceType',
        'Active' => 'LeadSourceActive',
        ];

    $rosterList = fopen($filePath, 'r');

    //get the first line that's the header row, (should be)
    $headers = fgetcsv($rosterList);

    //map headers
    foreach ($headers as $key => $header) {
        $headers[$key] = $headerMapping[$header];
    }

    //move the file to an array...
    //@NOTE: if this takes too much mem update to process as we go.
    $valuesKey = 0;
    $failed = [];
    while ($value = fgetcsv($rosterList)) {
        $valuesKey++;
        $data = [];

        foreach ($value as $fieldKey => $fieldValue) {
            if ($headers[$fieldKey]) {
                if ($fieldValue == null && in_array($headers[$fieldKey], $requiredFields)) {
                    echo "<h1>REQUIRED = ?????</h1>\n";
                    $fieldValue = '????';
                }
                $data[$headers[$fieldKey]] = $fieldValue;
            }
        }
        //echo print_r($data, true)."\n\n";
        $leadSrcResponse = json_decode(createLeadSource($data));

        if (
            $leadSrcResponse->success == false ||
            $leadSrcResponse->success === 'false'
        ) {
            //so we failed... just die
            //die(json_encode($leadSrcResponse));
            $data['error_code'] = $leadSrcResponse->errors[0]->code;
            $data['error_message'] = $leadSrcResponse->errors[0]->message;
            $failed[] = $data;
            //die(json_encode($data, $leadSrcResponse));
        } else {
            $sourceId = $leadSrcResponse->result->LeadSrcId;
        }
        //print_r($sourceId, true) . "<br />\n";
        echo "lead sources completed: $valuesKey <br>\n";
    }
    fclose($rosterList);

    echo "<br> End Lead Source Import<br>\n";
    echo "These failed:<Br />\n";
    //print json_encode($failed);
    print "Agency Code,Brand,LMP Program ID,LMP Source Id,MKTG Channel,Source Name,Program Name,Source Type,Active,Error Code,Error Message\n";
    foreach ($failed as $item) {
        print
            $item['LMPAssignedAgentOrgId'] .','
            .$item['Brand'] .','
            .$item['LMPProgramId'] .','
            .$item['LMPSourceId'] .','
            .$item['MarketingChannel'] .','
            .$item['AASourceName'] .','
            .$item['AAProgramName'] .','
            //. $item['AAProgramTerms'] . ','
            .$item['AASourceType'] .','
            .$item['LeadSourceActive'] .','
            .$item['error_code'] .','
            .$item['error_message'] . "\n";
    }
}

//create a lead source from the posted data.
function createLeadSource($postdata)
{
    $db     = PearDatabase::getInstance();
    $errors = [];

    //verify inputs.
    if (!validateMandatory($postdata['Brand']) && !validateMandatory($postdata['LMPAssignedAgentOrgId'])) {
        $errors[] = ['code'    => 'INVALID_VALUE',
                     'message' => 'Either LMPAssignedAgentOrgId OR Brand must be specified.'];
    }

    if (!validateMandatory($postdata['AASourceName'])) {
        $errors[] = ['code'    => 'INVALID_VALUE',
                     'message' => 'AASourceName must be specified.'];
    }

    if (!validateMandatory($postdata['AASourceType'])) {
        $errors[] = ['code'    => 'INVALID_VALUE',
                     'message' => 'AASourceType must be specified.'];
    }

    if (!validateMandatory($postdata['MarketingChannel'])) {
        $errors[] = ['code'    => 'INVALID_VALUE',
                     'message' => 'MarketingChannel must be specified.'];
    }

    if (!validateMandatory($postdata['AAProgramName'])) {
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
    $vanlinemanagers = [];
    $agencyCode = $postdata['LMPAssignedAgentOrgId'];
    $brand      = $postdata['Brand'];
    $active     = $postdata['LeadSourceActive'];

    //if active is explicitly false it's off otherwise it's on.
    if ($active === false || $active === 'false' || $active == 'off' || strtolower($active) == 'n') {
        $active = 'off';
    } elseif (strtolower($active) == 'y') {
        $active = 'on';
    } else {
        $active = 'on';
    }

    if ($agencyCode != '9999000') {
        //require Agency unless it's special vanline agency
        $sql    = "SELECT agentmanagerid,vanline_id FROM `vtiger_agentmanager` WHERE agency_code=?";
        $result = $db->pquery($sql, [$agencyCode]);
        $row    = $result->fetchRow();
        if ($row == null) {
            $errCode    = "INVALID_AGENTID";
            $errMessage = "The provided agentid is not valid";
            $response   = json_encode(generateErrorArray($errCode, $errMessage));
            return($response);
        }
        $agentid = $row['agentmanagerid'];
        $vanlinemanagers[] = [$row['vanline_id'] => getCarrierCodeFromAgencyCode($agencyCode)];
    } else {
        //require a vanlinemanager_id if there is not agency.
        //@TODO: replace if we add a brand to the database so we can select the id.
        //for gods sake.
        $tempVanlinemanagers = [];
        if ($brand == 'AVL') {
            $tempVanlinemanagers[1] = 'AVL';
        } elseif ($brand == 'NAVL') {
            $tempVanlinemanagers[9] = 'NAVL';
        } elseif ($brand == 'SIRVA') {
            $tempVanlinemanagers[1] = 'AVL';
            $tempVanlinemanagers[9] = 'NAVL';
        } else {
            $errCode    = "INVALID_BRAND";
            $errMessage = "The provided brand is not valid";
            $response   = json_encode(generateErrorArray($errCode, $errMessage));
            return($response);
        }
        foreach ($tempVanlinemanagers as $vanline_id => $brand) {
            $sql    = "SELECT vanlinemanagerid FROM `vtiger_vanlinemanager` WHERE vanline_id=?";
            $result = $db->pquery($sql, [$vanline_id]);
            $row    = $result->fetchRow();
            if ($row == null) {
                $errCode    = "INVALID_VANLINE_ID";
                $errMessage = "The calculated vanline_id is not valid";
                $response   = json_encode(generateErrorArray($errCode, $errMessage));

                return ($response);
            }
            $vanlinemanagers[$row['vanlinemanagerid']]  = $brand;
        }
    }

    $createResult = [];
    foreach ($vanlinemanagers as $vanlinemanager_id => $brand) {
        //now that we have verified everything else, and are ready to go with these values,
        //make sure it doesn't already exist!
        $sql    =
            "SELECT * FROM `vtiger_leadsourcemanager` WHERE program_name = ? AND agency_code = ?";
        $params = [$postdata['AAProgramName'], $agencyCode];
        if ($vanlinemanager_id) {
            $sql .= " AND vanlinemanager_id = ?";
            $params[] = $vanlinemanager_id;
        }
        $result = $db->pquery($sql, $params);
        $row    = $result->fetchRow();
        if ($row) {
            //Update if information has changed
            if($row['source_name']       != $postdata['AASourceName']     ||
               $row['source_type']       != $postdata['AASourceType']     ||
               $row['marketing_channel'] != $postdata['MarketingChannel'] ||
               $row['lmp_program_id']    != $postdata['LMPProgramId']     ||
               $row['lmp_source_id']     != $postdata['LMPSourceId']      ||
               $row['program_name']      != $postdata['AAProgramName']    ||
               $row['program_terms']     != $postdata['AAProgramTerms']
                ){
                $leadSrcData = [
                    'id'                => vtws_getWebserviceEntityId('LeadSourceManager', $row['leadsourcemanagerid']),
                    'agentid'           => ($agentid?$agentid:''),
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
                    $leadSrc = vtws_revise($leadSrcData, $current_user);
                    $createResult['LeadSrcId'][] = $leadSrc['id'];
                } catch (WebServiceException $ex) {
                    $response = generateErrorArray('FAILED_UPDATE_OF_LEADSOURCE', $ex->getMessage());

                    return json_encode($response);
                }
            }
            $sourceId = $row['leadsourcemanagerid'];
            //encode it to proper format
            $createResult['LeadSrcId'][] = vtws_getWebserviceEntityId('LeadSourceManager', $sourceId);
        } else {
            //SOOO we are good! let's add it.
            $leadSrcData = [
                'agentid'           => ($agentid?$agentid:'turtles'),//wat
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
                $leadSrc = vtws_create('LeadSourceManager', $leadSrcData, $current_user);
                $createResult['LeadSrcId'][] = $leadSrc['id'];
            } catch (WebServiceException $ex) {
                $response = generateErrorArray('FAILED_CREATION_OF_LEADSOURCE', $ex->getMessage());

                return json_encode($response);
            }
        }
    }

    $response = ['success' => true, 'result' => $createResult];
    return json_encode($response);
}

function getCarrierCodeFromAgencyCode($agentCode)
{
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
