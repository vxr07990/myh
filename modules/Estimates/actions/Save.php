<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

require_once('libraries/nusoap/nusoap.php');

class Estimates_Save_Action extends Quotes_Save_Action
{
    public function process(Vtiger_Request $request)
    {
        $recordId = $request->get('record');
        
        if(isset($_REQUEST['duplicate']) && $request->get('duplicate') == 'yes'){
            $recordId = '';
            $request->set('mode','');
        }

        $modName = $request->getModule();
        //Check for changes in demographic fields
        $db = PearDatabase::getInstance();
        //@NOTE: I am unclear if this needs to be done or not, if it is done, when you save from the ui
        //the survey date time do not transfer back to the opportunity.
        //$this->convertSurveyDateTime($request);
        if (isset($recordId) && !empty($recordId)) {
            $sql = "SELECT origin_address1, origin_address2, origin_city, origin_state, origin_zip, origin_phone1, origin_phone2,
					   	   destination_address1, destination_address2, destination_city, destination_state, destination_zip, destination_phone1, destination_phone2
					FROM `vtiger_quotescf` WHERE quoteid=?";
            $result = $db->pquery($sql, [$recordId]);
            $row = $result->fetchRow();
            if ($row['origin_address1']        != $request->get('origin_address1') ||
               $row['origin_address2']        != $request->get('origin_address2') ||
               $row['origin_city']            != $request->get('origin_city') ||
               $row['origin_state']            != $request->get('origin_state') ||
               $row['origin_zip']            != $request->get('origin_zip') ||
               $row['origin_phone1']        != $request->get('origin_phone1') ||
               $row['origin_phone2']        != $request->get('origin_phone2') ||
               $row['destination_address1'] != $request->get('destination_address1') ||
               $row['destination_address2'] != $request->get('destination_address2') ||
               $row['destination_city']        != $request->get('destination_city') ||
               $row['destination_state']    != $request->get('destination_state') ||
               $row['destination_zip']        != $request->get('destination_zip') ||
               $row['destination_phone1']    != $request->get('destination_phone1') ||
               $row['destination_phone2']    != $request->get('destination_phone2')) {
                //Set flag to fire bidirectional sync after record is saved through parent::process
                $fireBidirectionalUpdate = true;
            }
        }

        $this->convertSurveyDateTime($request);

        parent::process($request);
        
        $fieldList = $_REQUEST;
        if (is_array($this->column_fields)) {
            $fieldList = array_merge($_REQUEST, $this->column_fields);
        }
        if (empty($fieldList['record'])) {
            $newRecord = true;
            if (!empty($fieldList['currentid'])) {
                $fieldList['record'] = $fieldList['currentid'];
            } else {
                //this is OHHH noes... because we don't have the reocrd?
                //fallback!
                $fieldList['record'] = $this->id;
            }
        }
        if ($fieldList['record']) {
            $recordId = $fieldList['record'];
        }

        if (getenv('INSTANCE_NAME') == 'graebel' && $recordId) {
            $db->pquery('UPDATE `vtiger_quotes` SET sit_origin_auth_no=?, sit_dest_auth_no=? WHERE quoteid=?',
                        ['O'.$recordId, 'D'.$recordId, $recordId]);
        }

        //@TODO: MOVE from here to where it'll get called by webservices too.
        //can't move to save entity yet.
        //extrastops save
        // now a guest module
//        $extraStopsModel = Vtiger_Module_Model::getInstance('ExtraStops');
//        if ($extraStopsModel && $extraStopsModel->isActive()) {
//            $extraStopsModel->saveStops($request, $fieldList['record'], $request->get('pseudoSave'));
//        }

        if (!$request->get('pseudoSave')) {

            //@TODO: Won't this fire on pseudo?  I'm not sure, we'll need to ask ryan.
            if ($fireBidirectionalUpdate) {
                $wsdl                = getenv('SURVEY_SYNC_URL');
                $params              = [];
                $params['username']  = $this->getUsername($request->get('assigned_user_id'));
                $params['accessKey'] = $this->getAccessKey($request->get('assigned_user_id'));
                $params['recordID']  = $this->getObjTypeId($modName)."x".$recordId;
                $params['address']   = getenv('SITE_URL');
                $soapClient = new soapclient2($wsdl, 'wsdl');
                $soapClient->setDefaultRpcParams(true);
                $soapProxy  = $soapClient->getProxy();
                $soapResult = $soapProxy->BidirectionalUpdateNotification($params);
                file_put_contents('logs/devLog.log', "\n".date('Y-m-d H:i:s - ')." Estimates Bidirectional Sync SoapResult : ".print_r($soapResult, true), FILE_APPEND);
            }
        }
    }

    protected function getAccessKey($userId)
    {
        $db = PearDatabase::getInstance();

        $sql = "SELECT accesskey FROM `vtiger_users` WHERE id=?";
        $params[] = $userId;

        $result = $db->pquery($sql, $params);

        return $db->query_result($result, 0, 'accesskey');
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

    protected function getObjectTypeId($db, $modName)
    {
        $sql = "SELECT id FROM `vtiger_ws_entity` WHERE name=?";

        $params[] = $modName;

        $result = $db->pquery($sql, $params);

        return $db->query_result($result, 0, 'id').'x';
    }
}
