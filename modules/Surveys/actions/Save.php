<?php
require_once('libraries/nusoap/nusoap.php');
include_once('include/Webservices/Create.php');
require_once('include/Webservices/Revise.php');

use Carbon\Carbon;

class Surveys_Save_Action extends Vtiger_Save_Action
{
    public function process(Vtiger_Request $request)
    {
        file_put_contents('logs/SurveySaveCalendar.log', date('Y-m-d H:i:s - ').print_r($request, true)."\n", FILE_APPEND);
        global $current_user;
        $modName     = $request->getModule();
        $surveyTime = Vtiger_Time_UIType::getTimeValueWithSeconds($request->get('survey_time'));
        //Do this time zone conversion for the sake of the survey_date field. Time fields are handled in parent.
        $timeZone = $_REQUEST['timefield_survey_time'];
        if ($timeZone) {
            //If there's a time zone set on the field, use that. Otherwise, use the current user's time zone
            $surveyDate = DateTimeField::convertToDBFormat($request->get('survey_date'));
            $carbonTime = Carbon::createFromFormat('Y-m-d H:i:s', $surveyDate.' '.$surveyTime, $timeZone);
            $datetime = $carbonTime->setTimezone(DateTimeField::getDBTimeZone());
            //$request->set('survey_date', $datetime->format('Y-m-d'));
        } else {
        $datetime   = DateTimeField::convertToDBTimeZone($request->get('survey_date').' '.$surveyTime);
        //$request->set('survey_date', $datetime->format('Y-m-d'));
        }
        parent::process($request);

        $recId = $request->get('record');
        if ($recId == null) {
            //@TODO: if recID is not in the request then it should have failed already.  this function isn't useful.
            $recId = $this->getRecordID($request);
        }

        Surveys_Module_Model::SendSurveyUpdateNotification($recId, $request->get('assigned_user_id'), $modName);

        //@TODO: This may want to be " if (getenv('INSTANCE_NAME') != 'graebel') {
            //Update the opportunity survey date and time
        if (null != $request->get('potential_id') && $request->get('potential_id') != 0) {
            $survey = Vtiger_Record_Model::getInstanceById($recId, 'Surveys');
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
        //Update the order survey date and time
        if (null != $request->get('order_id') && $request->get('order_id') != 0) {
            $orderId = $request->get('order_id');
            $surveyDate = DateTimeField::convertToDBFormat($request->get('survey_date'));
            $surveyTime = DateTimeField::convertToDBTimeZone($request->get('survey_time'))->format('H:i:s');
            $orderArray = [
                'id'               => vtws_getWebserviceEntityId('Orders', $orderId),
                'orders_surveyd'   => $surveyDate,
                'orders_surveyt'   => $surveyTime
            ];
            try {
                vtws_revise($orderArray, $current_user);
            } catch (Exception $exc) {
                global $log;
                $log->debug('Error updating order from surveys:'.$exc->getMessage());
            }
            $db = PearDatabase::getInstance();
            $db->pquery("DELETE FROM `vtiger_fieldtimezonerel` WHERE crmid=? AND fieldid=?", [$request->get('order_id'), 'orders_surveyt']);
            $db->pquery("INSERT INTO `vtiger_fieldtimezonerel` (crmid, fieldid, timezone) VALUES (?,?,?)", [$request->get('order_id'), 'orders_surveyt', $timeZone]);
        }

        //Moved into saveentity for Surveys
//        if ($request->get('survey_type') == 'LiveSurvey') {
//            //Appointment is for a virtual survey - automatically creating Cubesheet record with TokBox data
//            $assignedUser = $request->get('assigned_user_id');
//            $contactId = $request->get('contact_id');
//            $opportunityId = $request->get('potential_id');
//            $surveyDateTime = strtotime($request->get('survey_date').' '.$request->get('survey_time'));
//            //Set code expiration to 2 days after survey appointment
//            $expirationDateTime = $surveyDateTime + (60 * 60 * 24 * 2);
//            file_put_contents('logs/AutocreateCubesheets.log', date('Y-m-d H:i:s - ').'Survey Datetime: '.$surveyDateTime.'; Expiration Datetime: '.$expirationDateTime."\n", FILE_APPEND);
//
//            $contactRecord = Contacts_Record_Model::getInstanceById($contactId);
//            try {
//                $sessionId = Cubesheets_Record_Model::getNewTokboxSession();
//                $user = new Users();
//                //$adminid = '19x1';
//                //$currentuserid = Users_Record_Model::getCurrentUserModel()->getId();
//                $admin_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
//                $data = array(
//                    'cubesheet_name' => $contactRecord->get('lastname').' Virtual Survey',
//                    'contact_id' => $this->getObjTypeId('Contacts').'x'.$contactId,
//                    'potential_id' => $this->getObjTypeId('Opportunities').'x'.$opportunityId,
//                    'assigned_user_id' => '19x'.$assignedUser,
//                    'survey_type' => 'LiveSurvey',
//                    'survey_appointment_id' => $this->getObjTypeId('Surveys').'x'.$recId,
//                    'tokbox_sessionid' => $sessionId,
//                    'tokbox_servertoken' => Cubesheets_Record_Model::getNewTokboxToken($sessionId),
//                    'tokbox_clienttoken' => Cubesheets_Record_Model::getNewTokboxToken($sessionId),
//                    'tokbox_devicecode' => Cubesheets_Record_Model::getNewUniqueDeviceCode(),
//                    'tokbox_code_expiration' => $expirationDateTime
//                );
//                $cubesheet_record = vtws_create('Cubesheets', $data, $admin_user);
//
//                $cubesheetid = substr(strstr($cubesheet_record['id'], 'x'), 1);
//
//                file_put_contents('logs/AutocreateCubesheets.log', date('Y-m-d H:i:s - ').'Cubesheet record successfully created with id '.$cubesheetid."\n", FILE_APPEND);
//
////				$recordModel = Cubesheets_Record_Model::getInstanceById($cubesheetid);
////				$recordModel->getTokboxServerToken();
////				$recordModel->getTokboxClientToken();
////				$recordModel->getDeviceCode();
////				$recordModel->setExpirationDate($expirationDateTime);
//            } catch (WebServiceException $ex) {
//                file_put_contents('logs/AutocreateCubesheets.log', date('Y-m-d H:i:s - ').$ex->getMessage()."\n", FILE_APPEND);
//            }
//        }
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
