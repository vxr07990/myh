<?php
/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
include_once('modules/Users/Users.php');
// include_once('include/webservices/Create.php');

class Opportunities_Save_Action extends Potentials_Save_Action
{
    public function process(Vtiger_Request $request)
    {
        //OT 2449 - Bidirectional Sync Trigger
        $recordId = $request->get('record');
        //$request = CRMEntity::checkSyncFlag(getenv('INSTANCE_NAME'),$request->get('module'),$request);
        if(!$recordId) {
            $recordId = $request->get('oldRecord');
        }
        $modName = $request->getModule();
        //Check for changes in demographic fields
        $db = PearDatabase::getInstance();
        if (isset($recordId) && !empty($recordId)) {
            $fieldList = [
                'origin_address1',
                'origin_address2',
                'origin_city',
                'origin_state',
                'origin_zip',
                'origin_phone1',
                'origin_phone2',
                'destination_address1',
                'destination_address2',
                'destination_city',
                'destination_state',
                'destination_zip',
                'destination_phone1',
                'destination_phone2'];
            if (getenv('INSTANCE_NAME') == 'sirva') {
                $fieldList = array_merge($fieldList, ['origin_phone1_type', 'origin_phone2_type', 'destination_phone1_type', 'destination_phone2_type', 'sales_person']);
            }

            $sql = "SELECT ".implode(', ', $fieldList)."
                    FROM `vtiger_potential`
					JOIN `vtiger_potentialscf`
					ON `vtiger_potential`.potentialid = `vtiger_potentialscf`.potentialid
					WHERE `vtiger_potential`.potentialid=?";
            $result = $db->pquery($sql, [$recordId]);
            $row = $result->fetchRow();
            foreach ($fieldList as $fieldName) {
                if ($row[$fieldName] != $request->get($fieldName)
                ) {
                    //Set flag to fire bidirectional sync after record is saved through parent::process
                    $fireBidirectionalUpdate = true;
                    break;
                }
            }
        }
        $recordModel = $this->saveRecord($request);

        // Sirva only duplication.
        if(getenv('INSTANCE_NAME') == 'sirva' && $request->get('isDuplicate')) {
            // Duplicate participating agents.
            $this->duplicateParticipants($recordId, $recordModel->getID(), $recordModel->get('assigned_user_id'));
        }

        $wsdl = getenv('SURVEY_SYNC_URL');
        if ($fireBidirectionalUpdate && $wsdl) {
            $params = [];
            $params['username'] = $this->getUsername($request->get('assigned_user_id'));
            $params['accessKey'] = $this->getAccessKey($request->get('assigned_user_id'));
            $params['recordID'] = $this->getObjTypeId($modName)."x".$recordId;
            $params['address'] = getenv('SITE_URL');

            $soapClient = new soapclient2($wsdl, 'wsdl');
            $soapClient->setDefaultRpcParams(true);
            $soapProxy = $soapClient->getProxy();
            $soapResult = $soapProxy->BidirectionalUpdateNotification($params);
            file_put_contents('logs/devLog.log', "\n".date('Y-m-d H:i:s - ')." Opportunities Bidirectional Sync SoapResult : ".print_r($soapResult, true), FILE_APPEND);
        }
        if (
            getenv('INSTANCE_NAME') == 'sirva' &&
            isset($recordId) &&
            !empty($recordId)
        ) {
            //Related Opportunities update
            $recId = $request->get('record');
            $recModel = Vtiger_Record_Model::getInstanceById($recId, 'Opportunities');
            $recModel->updateOppFields($recId);

            //Survey Update Notification
            $sentToMobile = $request->get('sent_to_mobile');
            $modName = $request->getModule();
            $surveyorId = $request->get('sales_person', null);
            if (
                $surveyorId != null &&
                $surveyorId != 0 &&
                $sentToMobile == 0
            ) {
                Surveys_Module_Model::SendSurveyUpdateNotification($recId, $surveyorId, $modName);
            }
        }

        if ($request->get('returnToList')) {
            $loadUrl = $recordModel->getModule()->getListViewUrl();
        } else {
            $loadUrl = $recordModel->getDetailViewUrl();
        }
        header('Location: '.$loadUrl);
    }

    protected function getAccessKey($userId)
    {
        $db = PearDatabase::getInstance();

        $sql = "SELECT accesskey FROM `vtiger_users` WHERE id=?";
        $params[] = $userId;

        $result = $db->pquery($sql, $params);

        return $db->query_result($result, 0, 'accesskey');
    }

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
                'Host: ' . parse_url(getenv('SIRVA_SITE'))['host'],
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

    protected function getObjTypeId($modName)
    {
        $db = PearDatabase::getInstance();

        $sql = "SELECT id FROM `vtiger_ws_entity` WHERE name=?";
        $params[] = $modName;

        $result = $db->pquery($sql, $params);

        return $db->query_result($result, 0, 'id');
    }

    protected function getUsername($userId)
    {
        $db = PearDatabase::getInstance();

        $sql = "SELECT user_name FROM `vtiger_users` WHERE id=?";
        $params[] = $userId;

        $result = $db->pquery($sql, $params);

        return $db->query_result($result, 0, 'user_name');
    }

    /* Duplicate rows in specified table by reference ID, and add new CF entry (if true).
     * $module = module to duplicate rows in
     * $refRow = row to reference with $refId
     * $refId = reference ID to find rows to duplicate
     * $newId = ID for new rows to reference
     * $table = manually send table name, needed if table is named differently than module.
     *
     * return = array with status (success (true), failed (false)), and a message if failed.
     */
    protected function duplicateParticipants($oldId, $newId, $owner) {
        global $adb;
        $module = 'ParticipatingAgents';
        $refRow = 'rel_crmid';

        $result = ['success' => true];
        // Basic error handling to avoid HTTP 500 responses.
        if(empty($module) || empty($refRow) || empty($oldId) || empty($newId)) {
            $result = [
                'success' => false,
                'message' => 'Sent empty variable.'
            ];
            return $result;
        }
        if(empty($table)) {
            $table = 'vtiger_'.strtolower($module);
        }

        $oldParticipants = ParticipatingAgents_Module_Model::getParticipants($oldId);

        $toSave = ["numAgents" => sizeof($oldParticipants), 'module' => 'Opportunities', 'agentid' => $owner];
        for($i = 0; $i < sizeof($oldParticipants); $i++) {
			$toSave['participantDelete_'.$i] = 'not';
			$toSave['participantId_'.$i] = 'none';
			$toSave['agent_permission_'.$i] = $oldParticipants[$i]['view_level'];
			$toSave['agents_id_'.$i] = $oldParticipants[$i]['agents_id'];
			$toSave['agent_type_'.$i] = $oldParticipants[$i]['agent_type'];
        }
        ParticipatingAgents_Module_Model::saveParticipants($toSave, $newId);

        return $result;
    }
}
