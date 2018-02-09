<?php

include_once('include/Webservices/Create.php');
include_once('modules/Users/Users.php');

class Surveys_SaveAjax_Action extends Vtiger_SaveAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        //file_put_contents('logs/devLog.log', "\n".date('Y-m-d H:i:s - ')."In this SaveAjax\n", FILE_APPEND);
        $record = $request->get('record');
        $db = PearDatabase::getInstance();
        $modName = $request->getModule();
        $isVirtual = empty($record) && $request->get('survey_type') == 'LiveSurvey';
        $timeZone = $request->get('timefield_survey_time');

        if (empty($record)) {
            if ($request->get('relationOperation') == 'true') {
                $this->setParentFieldData($request);
            }
            if ($isVirtual) {
                $surveyDateTime = strtotime($request->get('survey_date').' '.$request->get('survey_time'));
            }
        } else {
            $sql = "SELECT survey_date, survey_time, survey_end_time FROM vtiger_surveys WHERE surveysid=?";
            $result = $db->pquery($sql, array($record));
            $row = $result->fetchRow();
            if ($row != null) {
                $date = $row[0];
                $start_time = $row[1];

                $DBTZ = DateTimeField::getDBTimeZone();
                $userModel = Users_Record_Model::getCurrentUserModel();
                $UTZ = $userModel->time_zone;

                $loaded_datetime_string = $date.' '.$start_time;
                $loaded_datetime = DateTimeField::convertTimeZone($loaded_datetime_string, $DBTZ, $DBTZ);
                $user_datetime = DateTimeField::convertToUserTimeZone(date_format($loaded_datetime, 'Y-m-d H:i:s'));
                $requested_user_datetime = DateTimeField::convertTimeZone('00:00', $UTZ, $UTZ);

                $fieldname = $request->get('field');
                if ($fieldname == 'survey_date') {
                    $value = DateTimeField::convertToDBFormat($request->get('value'));
                    $date_parts = explode('-', $value);
                    $time_parts = explode(':', date_format($user_datetime, 'H:i:s'));
                    $requested_user_datetime->setDate($date_parts[0], $date_parts[1], $date_parts[2]);
                    $requested_user_datetime->setTime($time_parts[0], $time_parts[1]);
                    $save_datetime = DateTimeField::convertToDBTimeZone(date_format($requested_user_datetime, 'Y-m-d H:i:s'));
                    $save_date = DateTimeField::convertToUserFormat(date_format($save_datetime, 'Y-m-d'));
                    $request->set('value', $save_date);
                }
                if ($fieldname == 'survey_time') {
                    $value = Vtiger_Time_UIType::getTimeValueWithSeconds($request->get('value'));
                    $date_parts = explode('-', date_format($user_datetime, 'Y-m-d'));
                    $time_parts = explode(':', $value);
                    $db->pquery("UPDATE vtiger_surveys SET survey_date=? WHERE surveysid=?", array($save_date, $record));
//                    $request->set('value', date_format($save_datetime, 'H:i:s'));
                }
                if ($fieldname == 'survey_end_time') {
                    $value = Vtiger_Time_UIType::getTimeValueWithSeconds($request->get('value'));
                    $datetime = DateTimeField::convertToDBTimeZone($date.' '.$value);
//                    $request->set('value', $datetime->format('H:i:s'));
                }
            }
        }

        file_put_contents('logs/DateTime.log', date('Y-m-d H:i:s - ')."Outside of if/else structure\n", FILE_APPEND);

        //stole the parent process code to avoid editing core files for a change specific to this module
        $recordModel = $this->saveRecord($request);
        //the parent saveRecord sets the request with recordID, we could grab that directly or use the updated getRecordID.
        if ($record == null) {
            $record = $this->getRecordID($request);
        }
        file_put_contents('logs/DateTime.log', date('Y-m-d H:i:s - ')."After saveRecord call\n", FILE_APPEND);

        $fieldModelList = $recordModel->getModule()->getFields();
        $result = array();
        foreach ($fieldModelList as $fieldName => $fieldModel) {
            file_put_contents('logs/DateTime.log', date('Y-m-d H:i:s - ').$fieldName."\n", FILE_APPEND);
            $recordFieldValue = $recordModel->get($fieldName);
            if (is_array($recordFieldValue) && ($fieldModel->getFieldDataType() == 'multipicklist' || $fieldModel->getFieldDataType() == 'multiagent')) {
                $recordFieldValue = implode(' |##| ', $recordFieldValue);
            }
            if ($fieldName != 'survey_time' && $fieldName != 'survey_end_time' && $fieldName != 'survey_date') {
                $fieldValue = $displayValue = Vtiger_Util_Helper::toSafeHTML($recordFieldValue);
            }
            if ($fieldName == 'survey_time' || $fieldName == 'survey_end_time') {
                $fieldValue = $displayValue = Vtiger_Util_Helper::toSafeHTML($recordFieldValue);
                $sql = "SELECT survey_date
            						FROM  `vtiger_surveys`
            						WHERE surveysid =?";
                $SQLresult = $db->pquery($sql, array($record));
                $row = $SQLresult->fetchRow();
                $date = $row[0];
                $datetime = DateTimeField::convertToUserTimeZone($date.' '.$fieldValue);
                //file_put_contents('logs/devLog.log', "\nIn $fieldName : ".print_r(date_format($datetime,'H:i:s'),true)."\n", FILE_APPEND);
                $fieldValue = date_format($datetime, 'H:i:s');
                $displayValue = date_format($datetime, 'H:i:s');
            }
            if ($fieldName == 'survey_date') {
                $fieldValue = $displayValue = Vtiger_Util_Helper::toSafeHTML($recordFieldValue);
                $sql = "SELECT survey_time
            						FROM  `vtiger_surveys`
            						WHERE surveysid =?";
                $SQLresult = $db->pquery($sql, array($record));
                $row = $SQLresult->fetchRow();
                $time = $row[0];
                file_put_contents('logs/DateTime.log', date('Y-m-d H:i:s - ')."Preparing to call convertToDBFormat (2)\n", FILE_APPEND);
                $fieldValue = DateTimeField::convertToDBFormat($recordFieldValue);
                //file_put_contents('logs/devLog.log', "\n \$time = $time and \$fieldValue = $fieldValue", FILE_APPEND);
                file_put_contents('logs/DateTime.log', date('Y-m-d H:i:s - ')."Completed convertToDBFormat (2) call. Preparing to call convertToUserTimeZone\n", FILE_APPEND);
                $survey_datetime = DateTimeField::convertToUserTimeZone($recordFieldValue.' '.$time);
                $date_string = date_format($survey_datetime, 'Y-m-d');
                $display_date = DateTimeField::convertToUserFormat($date_string);
                $fieldValue = $display_date;
                $displayValue = $display_date;
            }
            //file_put_contents('logs/devLog.log', "\n $fieldName \$displayValue : ".$displayValue, FILE_APPEND);
            if ($fieldModel->getFieldDataType() !== 'currency' && $fieldModel->getFieldDataType() !== 'datetime' && $fieldModel->getFieldDataType() !== 'date') {
                $displayValue = $fieldModel->getDisplayValue($fieldValue, $recordModel->getId());
            }
            $result[$fieldName] = array('value' => $fieldValue, 'display_value' => $displayValue);
        }
        //Handling salutation type
        if ($request->get('field') === 'firstname' && in_array($request->getModule(), array('Contacts', 'Leads'))) {
            $salutationType = $recordModel->getDisplayValue('salutationtype');
            $firstNameDetails = $result['firstname'];
            $firstNameDetails['display_value'] = $salutationType. " " .$firstNameDetails['display_value'];
            if ($salutationType != '--None--') {
                $result['firstname'] = $firstNameDetails;
            }
        }

        //Moved into saveentity for Surveys
//        if ($isVirtual) {
//            //Appointment is for a virtual survey - automatically creating Cubesheet record with TokBox data
//                $assignedUser = $request->get('assigned_user_id');
//            $contactId = $request->get('contact_id');
//            $opportunityId = $request->get('potential_id');
//            $surveyDateTime = strtotime($request->get('survey_date').' '.$request->get('survey_time'));
//                //Set code expiration to 2 days after survey appointment
//                $expirationDateTime = $surveyDateTime + (60 * 60 * 24 * 2);
//            file_put_contents('logs/AutocreateCubesheets.log', date('Y-m-d H:i:s - ').'Survey Datetime: '.$surveyDateTime.'; Expiration Datetime: '.$expirationDateTime."\n", FILE_APPEND);
//
//            $contactRecord = Contacts_Record_Model::getInstanceById($contactId);
//            file_put_contents('logs/AutocreateCubesheets.log', date('Y-m-d H:i:s - ')."Preparing to enter try/catch block\n", FILE_APPEND);
//            try {
//                $sessionId = Cubesheets_Record_Model::getNewTokboxSession();
//                $user = new Users();
//                $adminid = '19x1';
//                $currentuserid = Users_Record_Model::getCurrentUserModel()->getId();
//                $admin_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
//                $data = array(
//                        'cubesheet_name' => $contactRecord->get('lastname').' Virtual Survey',
//                        'contact_id' => $this->getObjTypeId('Contacts').'x'.$contactId,
//                        'potential_id' => $this->getObjTypeId('Opportunities').'x'.$opportunityId,
//                        'survey_type' => 'LiveSurvey',
//                        'assigned_user_id' => '19x'.$assignedUser,
//                        'survey_appointment_id' => $this->getObjTypeId('Surveys').'x'.$recordModel->getId(),
//                        'tokbox_sessionid' => $sessionId,
//                        'tokbox_servertoken' => Cubesheets_Record_Model::getNewTokboxToken($sessionId),
//                        'tokbox_clienttoken' => Cubesheets_Record_Model::getNewTokboxToken($sessionId),
//                        'tokbox_devicecode' => Cubesheets_Record_Model::getNewUniqueDeviceCode(),
//                        'tokbox_code_expiration' => $expirationDateTime
//                    );
//                file_put_contents('logs/AutocreateCubesheets.log', date('Y-m-d H:i:s - ')."Preparing to call vtws_create with data: ".print_r($data, true)."\n", FILE_APPEND);
//                $cubesheet_record = vtws_create('Cubesheets', $data, $admin_user);
//
//                $cubesheetid = substr(strstr($cubesheet_record['id'], 'x'), 1);
//
//                file_put_contents('logs/AutocreateCubesheets.log', date('Y-m-d H:i:s - ').'Cubesheet record successfully created with id '.$cubesheetid."\n", FILE_APPEND);
//
////					$recordModel = Cubesheets_Record_Model::getInstanceById($cubesheetid);
////					$recordModel->getTokboxServerToken();
////					$recordModel->getTokboxClientToken();
////					$recordModel->getDeviceCode();
////					$recordModel->setExpirationDate($expirationDateTime);
//            } catch (WebServiceException $ex) {
//                file_put_contents('logs/AutocreateCubesheets.log', date('Y-m-d H:i:s - ').$ex->getMessage()."\n", FILE_APPEND);
//            }
//            file_put_contents('logs/AutocreateCubesheets.log', date('Y-m-d H:i:s - ')."Dropping out of try/catch block\n", FILE_APPEND);
//        }

        $result['_recordLabel'] = $recordModel->getName();
        $result['_recordId'] = $recordModel->getId();

        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($result);
        $response->emit();

        Surveys_Module_Model::SendSurveyUpdateNotification($record, $request->get('assigned_user_id'), $modName);

        //Update the opportunity survey date and time
        if (
            null != $request->get('potential_id') &&
            $request->get('potential_id') != 0
        ) {
            $survey = Vtiger_Record_Model::getInstanceById($record, 'Surveys');
            $opp = Vtiger_Record_Model::getInstanceById($request->get('potential_id'), 'Opportunities');
            $opp->set('survey_date', $survey->get('survey_date'));
            $opp->set('sales_person', $survey->get('assigned_user_id'));
            $opp->set('survey_time', $survey->get('survey_time'));
            if ($opp->get('appointment_type') == '') {
                $opp->set('appointment_type', 'QLAB');
            }
            $opp->set('mode', 'edit');
            $opp->save();

            $db = PearDatabase::getInstance();
            $db->pquery("DELETE FROM `vtiger_fieldtimezonerel` WHERE crmid=? AND fieldid=?", [$request->get('potential_id'), 'survey_time']);
            $db->pquery("INSERT INTO `vtiger_fieldtimezonerel` (crmid, fieldid, timezone) VALUES (?,?,?)", [$request->get('potential_id'), 'survey_time', $timeZone]);
        }
    }

    protected function setParentFieldData(Vtiger_Request &$request)
    {
        //file_put_contents('logs/devLog.log', date('Y-m-d H:i:s - ')."Entering setParentFieldData function\n", FILE_APPEND);
        $db = PearDatabase::getInstance();
        $recordId = $request->get('record');
        $parentRecordId = $request->get('sourceRecord');
        $parentRecordModule = $request->get('sourceModule');
        if ($parentRecordModule == 'Potentials' || $parentRecordModule == 'Opportunities' || $parentRecordModule == 'Orders') {
            if($parentRecordModule == 'Orders'){
				$sql = "SELECT origin_address1, origin_address2, origin_city, origin_state, origin_country, origin_zip, origin_phone1, origin_phone2, origin_description, orders_contacts, orders_account FROM vtiger_orders WHERE vtiger_orders.ordersid=?";
			}else{
				$sql = "SELECT origin_address1, origin_address2, origin_city, origin_state, origin_country, origin_zip, origin_phone1, origin_phone2, origin_description, contact_id, related_to FROM vtiger_potential JOIN vtiger_potentialscf ON vtiger_potential.potentialid=vtiger_potentialscf.potentialid WHERE vtiger_potential.potentialid=?";
			}
			$result = $db->pquery($sql, array($parentRecordId));
            $row = $result->fetchRow();
            if ($row != null) {
                $request->set('address1', $row[0]);
                $request->set('address2', $row[1]);
                $request->set('city', $row[2]);
                $request->set('state', $row[3]);
                $request->set('country', $row[4]);
                $request->set('zip', $row[5]);
                $request->set('phone1', $row[6]);
                $request->set('phone2', $row[7]);
                $request->set('address_description', $row[8]);
                $request->set('contact_id', $row[9]);
                $request->set('account_id', $row[10]);
				if($parentRecordModule != 'Orders')
					$request->set('potential_id', $parentRecordId);
                //file_put_contents('logs/SurveySaveCalendar.log', date('Y-m-d H:i:s - ').print_r($request, true)."\n", FILE_APPEND);
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
}
