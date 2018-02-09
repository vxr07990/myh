<?php
/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
 
include_once 'include/Webservices/Create.php';
include_once 'modules/Users/Users.php';
include_once 'includes/main/WebUI.php';
 
class Potentials_Save_Action extends Vtiger_Save_Action
{
    public function process(Vtiger_Request $request)
    {
        //file_put_contents('logs/devLog.log', "\n In Pot Save Action", FILE_APPEND);
        //Restrict to store indirect relationship from Potentials to Contacts
        $sourceModule = $request->get('sourceModule');
        $relationOperation = $request->get('relationOperation');
        //file_put_contents('logs/devLog.log', "\n In Pot Save Action: got request variables", FILE_APPEND);

        if ($relationOperation && $sourceModule === 'Contacts') {
            $request->set('relationOperation', false);
        }

        $this->convertSurveyDateTime($request);
        //$surveyTime = Vtiger_Time_UIType::getTimeValueWithSeconds($request->get('survey_time'));
        //$datetime = DateTimeField::convertToDBTimeZone($request->get('survey_date').' '.$surveyTime);
        //$request->set('survey_time', $datetime->format('H:i:s'));
        //$request->set('survey_date', $datetime->format('Y-m-d'));

        parent::process($request);
        
        //stops webservice & participating agents save
        //file_put_contents('logs/devLog.log', date('Y-m-d H:i:s - ')."record : ".$request->get('record')."\n", FILE_APPEND);

        /*$db = PearDatabase::getInstance();

        $sql = "SELECT potentialid FROM `vtiger_potential` ORDER BY potentialid DESC LIMIT 1";
        $result = $db->pquery($sql, array());
        $row = $result->fetchRow();
        $oppid = $this->getObjectTypeId($db, 'Opportunities').$row[0];
        $splitOppId = explode('x', $oppid)[1];

        if($request->get('record')){
            $splitOppId = $request->get('record');
        }

        //file_put_contents('logs/devLog.log', "\n splitOppId: $splitOppId", FILE_APPEND);

        $this->saveParticipants($request, $splitOppId);

        $sql = "SELECT stopsid FROM `vtiger_stops` WHERE stop_opp=? AND stop_type=? AND stops_isprimary=?";
        $params[] = $splitOppId;
        $params[] = 'Origin';
        $params[] = '1';
        $result = $db->pquery($sql, $params);
        unset($params);
        $row = $result->fetchRow();
        //file_put_contents('logs/devLog.log', "\n row[0]: $row[0]", FILE_APPEND);
        if($row != NULL) {
            //file_put_contents('logs/devLog.log', date('Y-m-d H:i:s - ')."NULL ROW FOUND2\n", FILE_APPEND);
            $sql2 = "UPDATE `vtiger_stops` SET stops_address1=?, stop_address2=?, stops_city=?, stops_state=?, stop_zip=?, stop_country=?, stop_p1=?, stop_p2=?, stop_description=? WHERE stopsid=?";
            $db->pquery($sql2, array($request->get('origin_address1'), $request->get('origin_address2'), $request->get('origin_city'), $request->get('origin_state'), $request->get('origin_zip'), $request->get('origin_country'), $request->get('origin_phone1'), $request->get('origin_phone2'), $request->get('origin_description1'), $row[0]));
            $sql3 = "UPDATE `vtiger_crmentity` SET smownerid=? WHERE crmid=?";
            $db->pquery($sql3, array($request->get('assigned_user_id'), $row[0]));
        } else{
            //file_put_contents('logs/devLog.log', "\n WS ORIGIN", FILE_APPEND);
            try {
                $user = new Users();
                $current_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
                $data = array(
                    'stops_address1' => $request->get('origin_address1'),
                    'stop_address2' => $request->get('origin_address2'),
                    'stops_city' => $request->get('origin_city'),
                    'stops_state' => $request->get('origin_state'),
                    'stop_zip' => $request->get('origin_zip'),
                    'stop_country' => $request->get('origin_country'),
                    'stop_p1' => $request->get('origin_phone1'),
                    'stop_p2' => $request->get('origin_phone2'),
                    'stop_description' => $request->get('origin_description1'),
                    'assigned_user_id' => '20x'.$request->get('assigned_user_id'),
                    'stops_isprimary' => true,
                    'stop_opp' => $oppid,
                    'stop_order' => $request->get('stop_order'),
                    'stop_sequence'=>'1',
                    'stop_type'=>'Origin'
                    );
                $originStop = vtws_create('Stops', $data, $current_user);
                //file_put_contents('logs/devLog.log', "\n originStop: ".print_r($originStop, true), FILE_APPEND);
                $wsid = $originStop['id'];
                $relcrmid = explode('x',$wsid)[1];
                $sql = "INSERT INTO `vtiger_crmentityrel` (crmid, module, relcrmid, relmodule) VALUES (?,?,?,?)";
                $result = $db->pquery($sql, array($splitOppId, 'Opportunities', $relcrmid, 'Stops'));
            } catch (WebServiceException $ex) {
                echo $ex->getMessage();
            }
        }

        $sql = "SELECT stopsid FROM `vtiger_stops` WHERE stop_opp=? AND stop_type=? AND stops_isprimary=?";
        $params[] = $splitOppId;
        $params[] = 'Destination';
        $params[] = '1';
        $result = $db->pquery($sql, $params);
        unset($params);
        $row = $result->fetchRow();
        if($row != NULL) {
            //file_put_contents('logs/devLog.log', date('Y-m-d H:i:s - ')."NULL ROW FOUND\n", FILE_APPEND);
            $sql2 = "UPDATE `vtiger_stops` SET stops_address1=?, stop_address2=?, stops_city=?, stops_state=?, stop_zip=?, stop_country=?, stop_p1=?, stop_p2=?, stop_description=? WHERE stopsid=?";
            $db->pquery($sql2, array($request->get('destination_address1'), $request->get('destination_address2'), $request->get('destination_city'), $request->get('destination_state'), $request->get('destination_zip'), $request->get('destination_country'), $request->get('destination_phone1'), $request->get('destination_phone2'), $request->get('destination_description1'), $row[0]));
            $sql3 = "UPDATE `vtiger_crmentity` SET smownerid=? WHERE crmid=?";
            $db->pquery($sql3, array($request->get('assigned_user_id'), $row[0]));
        } else{
            //file_put_contents('logs/devLog.log', "\n WS DESTINATION", FILE_APPEND);
            try {
                $user = new Users();
                $current_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
                $data = array(
                    'stops_address1' => $request->get('destination_address1'),
                    'stop_address2' => $request->get('destination_address2'),
                    'stops_city' => $request->get('destination_city'),
                    'stops_state' => $request->get('destination_state'),
                    'stop_zip' => $request->get('destination_zip'),
                    'stop_country' => $request->get('destination_country'),
                    'stop_p1' => $request->get('destination_phone1'),
                    'stop_p2' => $request->get('destination_phone2'),
                    'stop_description' => $request->get('destination_description'),
                    'assigned_user_id' => '20x'.$request->get('assigned_user_id'),
                    'stops_isprimary' => true,
                    'stop_opp' => $oppid,
                    'stop_order' => $request->get('stop_order'),
                    'stop_sequence'=>'2',
                    'stop_type'=>'Destination'
                    );
                $destinationStop = vtws_create('Stops', $data, $current_user);
                //file_put_contents('logs/devLog.log', "\n destinationStop: ".print_r($destinationStop, true), FILE_APPEND);
                $wsid = $destinationStop['id'];
                $relcrmid = explode('x',$wsid)[1];
                $sql = "INSERT INTO `vtiger_crmentityrel` (crmid, module, relcrmid, relmodule) VALUES (?,?,?,?)";
                $result = $db->pquery($sql, array($splitOppId, 'Opportunities', $relcrmid, 'Stops'));
            } catch (WebServiceException $ex) {
                echo $ex->getMessage();
            }
        }*/
    }
    protected function getObjectTypeId($db, $modName)
    {
        $sql = "SELECT id FROM `vtiger_ws_entity` WHERE name=?";

        $params[] = $modName;

        $result = $db->pquery($sql, $params);

        return $db->query_result($result, 0, 'id').'x';
    }

    public function saveParticipants($request, $oppRecord)
    {
        file_put_contents('logs/devLog.log', "\n Old Securities Called saveParticipants in Potentials_Save_Action", FILE_APPEND);
        /*
        $totalParticipants = $request->get('numAgents');

        for($i = 1; $i<=$totalParticipants; $i++){

            $agentTypePrev = $request->get('agent_type'.$i.'_prev');
            $oppParticipantsPrev = $request->get('opp_participants'.$i.'_prev');
            $participantPermissionPrev = $request->get('participantPermission'.$i.'_prev');

            $participantId = $request->get('participantId'.$i);
            $participantIdPrev = $request->get('participantId'.$i.'_prev');

            //file_put_contents('logs/devLog.log', "\n PID: ".$participantIdPrev, FILE_APPEND);

            $agentType = $request->get('agent_type'.$i);
            $oppParticipants = $request->get('opp_participants'.$i);
            $participantPermission = $request->get('participantPermission'.$i);

            /*file_put_contents('logs/devLog.log', "\n ROW NUM: ".$i, FILE_APPEND);
            file_put_contents('logs/devLog.log', "\n RECORD: ".$oppRecord, FILE_APPEND);
            file_put_contents('logs/devLog.log', "\n agentTypePrev: ".$agentTypePrev, FILE_APPEND);
            file_put_contents('logs/devLog.log', "\n oppParticipantsPrev: ".$oppParticipantsPrev, FILE_APPEND);
            file_put_contents('logs/devLog.log', "\n participantPermissionPrev: ".$participantPermissionPrev, FILE_APPEND);
            file_put_contents('logs/devLog.log', "\n agentType: ".$agentType, FILE_APPEND);
            file_put_contents('logs/devLog.log', "\n oppParticipants: ".$oppParticipants, FILE_APPEND);
            file_put_contents('logs/devLog.log', "\n participantPermission: ".$participantPermission, FILE_APPEND);*/
            
            /*if($agentTypePrev == 'none' && $oppParticipantsPrev == 'none' && $participantPermissionPrev == 'none'){
                if(!empty($agentType) || !empty($oppParticipants) || !empty($participantPermission)){
                    $db = PearDatabase::getInstance();
                    $sql = 'INSERT INTO `vtiger_potential_participatingagents`(opportunityid, agentid, agenttype, permissions, participantid) VALUES (?,?,?,?,?)';
                    $db->pquery($sql, array($oppRecord, $oppParticipants, $agentType, $participantPermission, $participantId));
                }
            } else{
                $db = PearDatabase::getInstance();
                $sql = 'UPDATE `vtiger_potential_participatingagents` SET opportunityid = ?, agentid = ?, agenttype = ?, permissions = ?, participantid = ? WHERE opportunityid = ? AND participantid = ?';
                $db->pquery($sql, array($oppRecord, $oppParticipants, $agentType, $participantPermission, $participantId, $oppRecord, $participantIdPrev));
            }
        }*/
    }

    protected function curlPOST($post_string, $webserviceURL)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $webserviceURL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
        $curlResult = curl_exec($ch);
        curl_close($ch);

        return $curlResult;
    }



    protected function curlGET($get_string, $webserviceURL)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $webserviceURL.$get_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
        $curlResult = curl_exec($ch);
        curl_close($ch);

        return $curlResult;
    }
}
