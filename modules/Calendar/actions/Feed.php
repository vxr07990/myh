<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

vimport('~~/include/Webservices/Query.php');

use Carbon\Carbon;

class Calendar_Feed_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        try {
            $result = array();

            $start = $request->get('start');
            $end   = $request->get('end');
            $type = trim($request->get('type'));
            $userid = $request->get('userid');
            $color = $request->get('color');
            $textColor = $request->get('textColor');
            $timeZone = $request->get('timeZone');
            $limit = $request->get('limit');

            switch ($type) {
                case 'Events': $this->pullEvents($start, $end, $result, $userid, $color, $textColor,$timeZone); break;
                case 'Calendar': $this->pullTasks($start, $end, $result, $color, $textColor, $userid, $limit,$timeZone); break;
                case 'Potentials': $this->pullPotentials($start, $end, $result, $color, $textColor); break;
                case 'Contacts':
                            if ($request->get('fieldname') == 'support_end_date') {
                                $this->pullContactsBySupportEndDate($start, $end, $result, $color, $textColor);
                            } else {
                                $this->pullContactsByBirthday($start, $end, $result, $color, $textColor);
                            }
                            break;
                case 'Invoice': $this->pullInvoice($start, $end, $result, $color, $textColor); break;
                case 'MultipleEvents': $this->pullMultipleEvents($start, $end, $result, $request->get('mapping'), $timeZone);break;
                case 'MultipleTasks/Events': $this->pullMultipleTasksAndEvents($start, $end, $result, $request->get('mapping'), $timeZone);break;
                case 'Tasks/Events': $this->pullTasksAndEvents($start, $end, $result, $userid, $color, $textColor,$timeZone);break;
                case 'Project': $this->pullProjects($start, $end, $result, $color, $textColor); break;
                case 'ProjectTask': $this->pullProjectTasks($start, $end, $result, $color, $textColor); break;
                case 'MultipleSurveys': $this->pullMultipleSurveys($start, $end, $result, $request->get('mapping'), $timeZone);break;
                case 'Surveys': $this->pullSurveys($start, $end, $result, $userid, $color, $textColor, $timeZone); break;
                case 'Employees': $this->pullEmployees($start, $end, $result, $color, $textColor); break;
                case 'Vehicles': $this->pullVehicles($start, $end, $result, $color, $textColor); break;
                case 'Equipment': $this->pullEquipment($start, $end, $result, $color, $textColor); break;
                case 'OASurveyRequests': $this->pullOASurveysRequests($start, $end, $result, $color, $textColor); break;
            }
            echo json_encode($result);
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }

    protected function pullOASurveysRequests($start, $end, &$result, $color, $textColor)
    {
        $dbStartDateOject = DateTimeField::convertToDBTimeZone($start);
        $dbStartDateTime = $dbStartDateOject->format('Y-m-d');

        $dbEndDateObject = DateTimeField::convertToDBTimeZone($end);
        $dbEndDateTime = $dbEndDateObject->format('Y-m-d');

        $db = PearDatabase::getInstance();
        $queryResult = $db->pquery("SELECT * FROM vtiger_oasurveyrequests oa
                INNER JOIN vtiger_crmentity cr ON oa.oasurveyrequestsid = cr.crmid
                WHERE cr.deleted = 0 AND oa.oasurveyrequests_status = 'Pending'
                AND ((oasurveyrequests_timeout > '$dbStartDateTime'
                AND  oasurveyrequests_timeout < '$dbEndDateTime') OR (oasurveyrequests_timeout = '') OR (oasurveyrequests_timeout IS NULL) )", array());

        while ($arr = $db->fetchByAssoc($queryResult)) {
            $item = array();
            if (decode_html($arr['message']) != '') {
                $item['title'] = decode_html($arr['message']);
            } else {
                $item['title'] = 'AO Survey Request ' . $arr['oasurveyrequests_id'];
            }

            $item['visibility'] = "Public";
            $item['allDay'] = true;
            $item['status'] = "Planned";//$arr['oasurveyrequests_status']; #Conversion between status!
            $item['activitytype'] = "Meeting";
            $item['id'] = $arr['crmid'];
            $item['start'] = $arr['createdtime'];
            if ($arr['oasurveyrequests_timeout'] != '') {
                $item['end'] = $arr['oasurveyrequests_timeout'];
            } else {
                $item['end'] = $dbEndDateTime;
            }

            //$item['url']   = sprintf('index.php?module=OASurveyRequests&view=Detail&record=%s', $arr['crmid']);
            $item['color'] = $color;
            $item['textColor'] = $textColor;
            $item['module'] = "OASurveyRequests";
            $result[] = $item;
        }
    }

    protected function getGroupsIdsForUsers($userId)
    {
        vimport('~~/include/utils/GetUserGroups.php');

        $userGroupInstance = new GetUserGroups();
        $userGroupInstance->getAllUserGroups($userId);
        return $userGroupInstance->user_groups;
    }

    protected function getUserIdsForGroup($groupId)
    {
        vimport('~~/include/utils/GetGroupUsers.php');

        $groupUserInstance = new GetGroupUsers();
        $groupUserInstance->getAllUsersInGroup($groupId);
        return $groupUserInstance->group_users;
    }

    protected function getUserListFromGroups($groups)
    {
        $userList = array();
        foreach ($groups as $group) {
            $userList = array_merge($userList, $this->getUserIdsForGroup($group));
        }
        return array_unique($userList);
    }

    protected function queryForRecords($query, $onlymine=true)
    {
        $user = Users_Record_Model::getCurrentUserModel();
        if ($onlymine) {
            $groupIds = $this->getGroupsIdsForUsers($user->getId());
            $groupWsIds = array();
            foreach ($groupIds as $groupId) {
                $groupWsIds[] = vtws_getWebserviceEntityId('Groups', $groupId);
            }
            $userwsid = vtws_getWebserviceEntityId('Users', $user->getId());
            $userAndGroupIds = array_merge(array($userwsid), $groupWsIds);
            $query .= " AND assigned_user_id IN ('".implode("','", $userAndGroupIds)."')";
        }
        // TODO take care of pulling 100+ records
        return vtws_query($query.';', $user);
    }

    public function pullEvents($start, $end, &$result, $userid = false, $color = null, $textColor = 'white',$timeZone=false)
    {
        global $current_user;
        if(!$timeZone) {
            $timeZone = $current_user->time_zone;
        }
        $dbStartDateOject = DateTimeField::convertTimeZone($start, $timeZone, DateTimeField::getDBTimeZone());
        $dbStartDateTime = $dbStartDateOject->format('Y-m-d H:i:s');
        $dbStartDateTimeComponents = explode(' ', $dbStartDateTime);
        $dbStartDate = $dbStartDateTimeComponents[0];

        $dbEndDateObject = DateTimeField::convertTimeZone($end, $timeZone, DateTimeField::getDBTimeZone());
        $dbEndDateTime = $dbEndDateObject->format('Y-m-d H:i:s');

        $currentUser = Users_Record_Model::getCurrentUserModel();
        $db = PearDatabase::getInstance();

        $moduleModel = Vtiger_Module_Model::getInstance('Events');
        if ($userid) {
            $focus = new Users();
            $focus->id = $userid;
            $focus->retrieve_entity_info($userid, 'Users');
            $user = Users_Record_Model::getInstanceFromUserObject($focus);
            $userName = $user->getName();
            $queryGenerator = new QueryGenerator($moduleModel->get('name'), $user);
            if(!$timeZone) {
                $timeZone=$user->get('time_zone');
            }
        } else {
            $queryGenerator = new QueryGenerator($moduleModel->get('name'), $currentUser);
        }

        $queryGenerator->setFields(array('subject', 'eventstatus', 'visibility', 'date_start', 'time_start', 'due_date', 'time_end', 'assigned_user_id', 'id', 'activitytype'));
        $query = $queryGenerator->getQuery();

        $query.= " AND vtiger_activity.activitytype NOT IN ('Emails','Task') AND ";
        $hideCompleted = $currentUser->get('hidecompletedevents');
        if ($hideCompleted) {
            $query.= "vtiger_activity.eventstatus != 'HELD' AND ";
        }
        $query.= " ((concat(date_start, '', time_start)  >= '$dbStartDateTime' AND concat(due_date, '', time_end) < '$dbEndDateTime') OR ( due_date >= '$dbStartDate'))";

        $params = array();
        if (empty($userid)) {
            $eventUserId  = $currentUser->getId();
        } else {
            $eventUserId = $userid;
        }

        //$accessibleUsers = array_keys(Users_Record_Model::getCurrentUserModel()->getAccessibleUsers());
        //$params = $accessibleUsers;
        //file_put_contents('logs/devLog.log', "\n AccessibleUsers : ".print_r($accessibleUsers, true), FILE_APPEND);

        $params = $eventUserId;//$userid;

        $query.= " AND vtiger_crmentity.smownerid = ?";
        //file_put_contents('logs/devLog.log', "\n Q Feed.php: $query", FILE_APPEND);
        //file_put_contents('logs/devLog.log', "\n Params : ".print_r($params, true), FILE_APPEND);
        $query .= ' GROUP BY `vtiger_crmentity`.crmid ';
        $queryResult = $db->pquery($query, [$eventUserId]);

        if ($queryResult) {
            while ($record = $db->fetchByAssoc($queryResult)) {
                $item                 = [];
                $crmid                = $record['activityid'];
                $visibility           = $record['visibility'];
                $activitytype         = $record['activitytype'];
                $status               = $record['eventstatus'];
                $item['id']           = $crmid;
                $item['visibility']   = $visibility;
                $item['activitytype'] = $activitytype;
                $item['status']       = $status;
                if (!$currentUser->isAdminUser() && $visibility == 'Private' && $userid && $userid != $currentUser->getId()) {
                    $item['title'] = decode_html($userName).' - '.decode_html(vtranslate('Busy', 'Events')).'*';
                    $item['url']   = '';
                } else {
                    $item['title'] = decode_html($record['subject']).' - ('.decode_html(vtranslate($record['eventstatus'], 'Calendar')).')';
                    $item['url']   = sprintf('index.php?module=Calendar&view=Detail&record=%s', $crmid);
                }
//                $dateTimeFieldInstance = new DateTimeField($record['date_start'].' '.$record['time_start']);
//                $userDateTimeString    = $dateTimeFieldInstance->getFullcalenderDateTimevalue($currentUser);
//                $dateTimeComponents    = explode(' ', $userDateTimeString);
//                $dateComponent         = $dateTimeComponents[0];
//                //Conveting the date format in to Y-m-d . since full calendar expects in the same format
//                $dataBaseDateFormatedString = DateTimeField::__convertToDBFormat($dateComponent, $currentUser->get('date_format'));

                $carbonTime = Carbon::createFromFormat('Y-m-d H:i:s', $record['date_start'].' '.$record['time_start'], DateTimeField::getDBTimeZone());
                $carbonTime->setTimezone($timeZone);
                $dateTimeComponents = explode(' ', $carbonTime->format('Y-m-d H:i:s'));
                $dateComponent         = $dateTimeComponents[0];
                $dataBaseDateFormatedString = $dateComponent;

                // Convert start Time to user time zone
                if($timeZone) {

                    $date = DateTimeField::convertTimeZone($record['date_start'].' '.$record['time_start'], DateTimeField::getDBTimeZone(), $timeZone);
                    $time_start = $date->format("H:i");
                }

                if (count($dateTimeComponents) === 3 && $dateTimeComponents[2] == 'PM') {
                    $startTime = $time_start.' '.$dateTimeComponents[2];
                    $startTime = date("H:i", strtotime("$startTime"));
                    $item['start'] = $dataBaseDateFormatedString.' '.$startTime;
                } else {
                    $item['start'] = $dataBaseDateFormatedString.' '.$time_start;
                }
//                $dateTimeFieldInstance = new DateTimeField($record['due_date'].' '.$record['time_end']);
//                $userDateTimeString    = $dateTimeFieldInstance->getFullcalenderDateTimevalue($currentUser);
//                $dateTimeComponents    = explode(' ', $userDateTimeString);
//                $dateComponent         = $dateTimeComponents[0];
//                //Conveting the date format in to Y-m-d . since full calendar expects in the same format
//                $dataBaseDateFormatedString = DateTimeField::__convertToDBFormat($dateComponent, $currentUser->get('date_format'));

                $carbonTime = Carbon::createFromFormat('Y-m-d H:i:s', $record['due_date'].' '.$record['time_end'], DateTimeField::getDBTimeZone());
                $carbonTime->setTimezone($timeZone);
                $dateTimeComponents = explode(' ', $carbonTime->format('Y-m-d H:i:s'));
                $dateComponent         = $dateTimeComponents[0];
                $dataBaseDateFormatedString = $dateComponent;

                // Convert end Time to user time zone
                if($timeZone) {
                    $date = DateTimeField::convertTimeZone($record['due_date'].' '.$record['time_end'], DateTimeField::getDBTimeZone(), $timeZone);
                    $time_end = $date->format("H:i");
                }
                if (count($dateTimeComponents) === 3 && $dateTimeComponents[2] == 'PM') {
                    $endTime = $time_end.' '.$dateTimeComponents[2];
                    $endTime = date("H:i", strtotime("$endTime"));
                    $item['end'] = $dataBaseDateFormatedString.' '.$endTime;
                } else {
                    $item['end'] = $dataBaseDateFormatedString.' '.$time_end;
                }
                $item['className'] = $cssClass;
                $item['allDay']    = false;
                $item['color']     = $color;
                $item['textColor'] = $textColor;
                $item['module']    = $moduleModel->getName();
                $result[]          = $item;
            }
        }
    }

    protected function pullMultipleEvents($start, $end, &$result, $data, $timeZone=false)
    {
        //file_put_contents('logs/devLog.log', "\n FUNCTION PARAMS: start - ".$start.", end - ".$end.", data - ".print_r($data, true), FILE_APPEND);
        foreach ($data as $id=>$backgroundColorAndTextColor) {
            $userEvents = array();
            $colorComponents = explode(',', $backgroundColorAndTextColor);
            $this->pullEvents($start, $end, $userEvents, $id, $colorComponents[0], $colorComponents[1], $timeZone);
            $result[$id] = $userEvents;
            //file_put_contents('logs/devLog.log', "\n USER EVENTS FOR USER $id : ".print_r($userEvents, true), FILE_APPEND);
        }
    }

    protected function pullMultipleSurveys($start, $end, &$result, $data, $timeZone=false)
    {
        foreach ($data as $id=>$backgroundColorAndTextColor) {
            $userSurveys = array();
            $colorComponents = explode(',', $backgroundColorAndTextColor);
            $this->pullSurveys($start, $end, $userSurveys, $id, $colorComponents[0], $colorComponents[1], $timeZone);
            $result[$id] = $userSurveys;
        }
    }

    protected function pullMultipleTasksAndEvents($start, $end, &$result, $data, $timeZone=false)
    {
        //file_put_contents('logs/devLog.log', "\n FUNCTION PARAMS: start - ".$start.", end - ".$end.", data - ".print_r($data, true), FILE_APPEND);
        foreach ($data as $id=>$backgroundColorAndTextColor) {
            $userTasksAndEvents = array();
            $colorComponents = explode(',', $backgroundColorAndTextColor);
            $this->pullTasks($start, $end, $userTasksAndEvents, $colorComponents[0], $colorComponents[1], $id, false, $timeZone);
            $this->pullEvents($start, $end, $userTasksAndEvents, $id, $colorComponents[0], $colorComponents[1], $timeZone);
            $this->pullSurveys($start, $end, $userTasksAndEvents, $id, $colorComponents[0], $colorComponents[1], $timeZone);
            $result[$id] = $userTasksAndEvents;
            //file_put_contents('logs/devLog.log', "\n USER TASKS AND EVENTS FOR USER $id : ".print_r($userTasksAndEvents, true), FILE_APPEND);
        }
    }

    protected function pullTasksAndEvents($start, $end, &$result, $userid = false, $color = null, $textColor = 'white', $timeZone=false)
    {
        //file_put_contents('logs/devLog.log', "\n FUNCTION PARAMS: start - ".$start.", end - ".$end.", id - ".$userid.", color & textcolor: $color & $textColor", FILE_APPEND);
        $this->pullTasks($start, $end, $result, $color, $textColor, $userid, false, $timeZone);
        $this->pullEvents($start, $end, $result, $userid, $color, $textColor, $timeZone);
        $this->pullSurveys($start, $end, $result, $userid, $color, $textColor, $timeZone);
        //file_put_contents('logs/devLog.log', "\n result = ".print_r($result, true), FILE_APPEND);
    }

    protected function pullMultipleTasks($start, $end, &$result, $data)
    {
        foreach ($data as $id=>$backgroundColorAndTextColor) {
            $userTasks = array();
            $colorComponents = explode(',', $backgroundColorAndTextColor);
            $this->pullTasks($start, $end, $userTasks, $id, $colorComponents[0], $colorComponents[1]);
            $result[$id] = $userTasks;
        }
    }

    public function pullTasks($start, $end, &$result, $color = null, $textColor = 'white', $userid=false, $limit=false, $timeZone=false)
    {
        global $current_user;
        if(!$timeZone) {
            $timeZone = $current_user->time_zone;
        }
        $dbStartDateOject = DateTimeField::convertToDBTimeZone($start);
        $dbStartDateTime = $dbStartDateOject->format('Y-m-d H:i:s');
        $dbStartDateTimeComponents = explode(' ', $dbStartDateTime);
        $dbStartDate = $dbStartDateTimeComponents[0];

        $dbEndDateObject = DateTimeField::convertToDBTimeZone($end);
        $dbEndDateTime = $dbEndDateObject->format('Y-m-d H:i:s');

        $currentUser = Users_Record_Model::getCurrentUserModel();
        $user = Users_Record_Model::getCurrentUserModel();
        $db = PearDatabase::getInstance();

        $moduleModel = Vtiger_Module_Model::getInstance('Calendar');
        $userAndGroupIds = array_merge(array($user->getId()), $this->getGroupsIdsForUsers($user->getId()));
        if ($userid) {
            $focus = new Users();
            $focus->id = $userid;
            $focus->retrieve_entity_info($userid, 'Users');
            $user = Users_Record_Model::getInstanceFromUserObject($focus);
            $userName = $user->getName();
            $queryGenerator = new QueryGenerator($moduleModel->get('name'), $user);
            $taskUser = $user;
        } else {
            $queryGenerator = new QueryGenerator($moduleModel->get('name'), $currentUser);
            $taskUser = $currentUser;
        }

        $userAndGroupIds = array_merge(array($taskUser->getId()), $this->getGroupsIdsForUsers($taskUser->getId()));
        //$queryGenerator = new QueryGenerator($moduleModel->get('name'), $user);

        $queryGenerator->setFields(array('activityid', 'subject', 'taskstatus', 'activitytype', 'date_start', 'time_start', 'due_date', 'time_end', 'id'));
        $query = $queryGenerator->getQuery();

        $query.= " AND vtiger_activity.activitytype = 'Task' AND ";
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $hideCompleted = $currentUser->get('hidecompletedevents');
        if ($hideCompleted) {
            $query.= "vtiger_activity.status != 'Completed' AND ";
        }
        /*
         * it was this, but I don't see why the elseif won't do the same thing.
         * tell me why I'm wrong when should it break.
        $query.= " ((date_start >= ? AND due_date < ?) OR ( due_date >= ?))";
        $params = array($start,$end,$start);
        */
        if ($end && $start) {
            $query.= " (date_start >= ? AND due_date < ?) ";
            $params = array($start,$end);
        } elseif ($start) {
            $query.= " ( due_date >= ?) ";
            $params = array($start);
        } elseif ($end) {
            $query.= " ( due_date <= ?) ";
            $params = array($end);
        }
        if ($userid) {
            $params[] = $userid;
        }
        $params = array_merge($params, $userAndGroupIds);
        $query.= " AND vtiger_crmentity.smownerid = ?";
        $query .= ' GROUP BY `vtiger_crmentity`.crmid ';
        if ($limit) {
            $query .= ' LIMIT ' . $limit;
        }
        //file_put_contents('logs/devLog.log', "\n Q: $query", FILE_APPEND);
        $queryResult = $db->pquery($query, $params);

        if ($queryResult) {
            while ($record = $db->fetchByAssoc($queryResult)) {
                $item                  = [];
                $crmid                 = $record['activityid'];
                $item['title']         = decode_html($record['subject']).' - ('.decode_html(vtranslate($record['status'], 'Calendar')).')';
                $item['status']        = $record['status'];
                $item['activitytype']  = $record['activitytype'];
                $item['id']            = $crmid;

//                $dateTimeFieldInstance = new DateTimeField($record['date_start'].' '.$record['time_start']);
//                $userDateTimeString    = $dateTimeFieldInstance->getFullcalenderDateTimevalue();
//                $dateTimeComponents    = explode(' ', $userDateTimeString);
//                $dateComponent         = $dateTimeComponents[0];
//                //Conveting the date format in to Y-m-d . since full calendar expects in the same format
//                $dataBaseDateFormatedString = DateTimeField::__convertToDBFormat($dateComponent, $user->get('date_format'));

                $carbonTime = Carbon::createFromFormat('Y-m-d H:i:s', $record['date_start'].' '.$record['time_start'], DateTimeField::getDBTimeZone());
                $carbonTime->setTimezone($timeZone);
                $dateTimeComponents = explode(' ', $carbonTime->format('Y-m-d H:i:s'));
                $dateComponent         = $dateTimeComponents[0];
                $dataBaseDateFormatedString = $dateComponent;

                $item['start']              = $dataBaseDateFormatedString.' '.$dateTimeComponents[1];
                $item['end']       = $record['due_date'];
                $item['url']       = sprintf('index.php?module=Calendar&view=Detail&record=%s', $crmid);
                $item['color']     = $color;
                $item['textColor'] = $textColor;
                $item['module']    = $moduleModel->getName();
                $result[]          = $item;
            }
        }
    }

    protected function pullPotentials($start, $end, &$result, $color = null, $textColor = 'white')
    {
        $query = "SELECT potentialname,closingdate FROM Potentials";
        $query.= " WHERE closingdate >= '$start' AND closingdate <= '$end'";
        $records = $this->queryForRecords($query);
        foreach ($records as $record) {
            $item = array();
            list($modid, $crmid) = vtws_getIdComponents($record['id']);
            $item['id'] = $crmid;
            $item['title'] = decode_html($record['potentialname']);
            $item['start'] = $record['closingdate'];
            //module is Opportunities not Potentials this is confusing leaving the old one commented to revert.
            //$item['url']   = sprintf('index.php?module=Potentials&view=Detail&record=%s', $crmid);
            $item['url']   = sprintf('index.php?module=Opportunities&view=Detail&record=%s', $crmid);
            $item['color'] = $color;
            $item['textColor'] = $textColor;
            $result[] = $item;
        }
    }

//Surveys

    public function pullSurveys($start, $end, &$result, $userid = false, $color = null, $textColor = 'white', $timeZone = false)
    {
        global $current_user;
        if(!$timeZone) {
            $timeZone = $current_user->time_zone;
        }
        $dbStartDateOject = DateTimeField::convertToDBTimeZone($start);
        $dbStartDateTime = $dbStartDateOject->format('Y-m-d H:i:s');
        $dbStartDateTimeComponents = explode(' ', $dbStartDateTime);
        $dbStartDate = $dbStartDateTimeComponents[0];

        $dbEndDateObject = DateTimeField::convertToDBTimeZone($end);
        $dbEndDateTime = $dbEndDateObject->format('Y-m-d H:i:s');

        $currentUser = Users_Record_Model::getCurrentUserModel();
        $db = PearDatabase::getInstance();

        $moduleModel = Vtiger_Module_Model::getInstance('Surveys');
        if ($userid) {
            $focus = new Users();
            $focus->id = $userid;
            $focus->retrieve_entity_info($userid, 'Users');
            $user = Users_Record_Model::getInstanceFromUserObject($focus);
            $userName = $user->getName();
            $queryGenerator = new QueryGenerator($moduleModel->get('name'), $user);
        } else {
            $queryGenerator = new QueryGenerator($moduleModel->get('name'), $currentUser);
        }

        $queryGenerator->setFields(array('survey_status', 'survey_date', 'survey_time', 'survey_end_time', 'assigned_user_id', 'id', 'contact_id', 'city', 'state', 'potential_id', 'phone1', 'segment'));

        $query = $queryGenerator->getQuery();

        $query = str_replace(' GROUP BY `vtiger_crmentity`.crmid ', '', $query);

        $query.= " AND vtiger_surveys.survey_status NOT IN ('Completed','Cancelled') AND ";
        //$query.= " contact_id > 0 AND ";
        $query.= " ((concat(survey_date, '', survey_time)  >= '$dbStartDateTime' AND concat(survey_date, '', survey_end_time) < '$dbEndDateTime') OR ( survey_date >= '$dbStartDate'))";

        $params = array();
        if (empty($userid)) {
            $surveyUserId  = $currentUser->getId();
        } else {
            $surveyUserId = $userid;
        }
        $params[] = $surveyUserId;
        $query.= " AND vtiger_crmentity.smownerid IN (".  generateQuestionMarks($params).")";

        $query .= ' GROUP BY `vtiger_crmentity`.crmid ';

        //file_put_contents('logs/CalendarSurveys.log', date('Y-m-d H:i:s - ')."Executing query: $query\n", FILE_APPEND);
        //file_put_contents('logs/devLog.log', "\n Feed Query: $query", FILE_APPEND);
        $queryResult = $db->pquery($query, $params);

        if ($queryResult) {
            while ($record = $db->fetchByAssoc($queryResult)) {
                //file_put_contents('logs/CalendarSurveys.log', date('Y-m-d H:i:s - ').print_r($record, true)."\n", FILE_APPEND);
                $carbonStart    = Carbon::createFromFormat('Y-m-d H:i:s', $record['survey_date'].' '.$record['survey_time']);
                $carbonEnd      = Carbon::createFromFormat('Y-m-d H:i:s', $record['survey_date'].' '.$record['survey_end_time']);
                if ($carbonEnd->lt($carbonStart)) {
                    $carbonEnd->addDay();
                }
                $carbonStart->setTimezone($timeZone);
                $carbonEnd->setTimezone($timeZone);

                //@NOTE: all of this is because we don't want to error on returning survey appointments
                $description = [];
                try {
                    if ($record['contact_id']) {
                        $contact = Vtiger_Record_Model::getInstanceById($record['contact_id'], 'Contacts');
                        if ($contact) {
                            $description['contactName'] = $contact->get('firstname').' '.$contact->get('lastname').',';
                        }
                    }
                } catch (Exception $ex) {
                    //ignore error it's already being ignored somewhere above this.
                }

                try {
                    if ($record['potential_id']) {
                        $opportunity = Vtiger_Record_Model::getInstanceById($record['potential_id'], 'Opportunities');
                        if ($opportunity) {
                            // For SIRVA
                            if(empty($description['contactName']) || getenv('INSTANCE_NAME') == 'sirva') {
                                $contact = Vtiger_Record_Model::getInstanceById($opportunity->get('contact_id'), 'Contacts');
                                if($contact) {
                                    $description['contactName'] = $contact->get('lastname').', '.$contact->get('firstname');
                                }
                            }
                            $description['segment'] = $opportunity->get('segment');
                            if(empty($description['segment'])) {
                                unset($description['segment']);
                            }
                            $description['city'] = !empty($record['city']) ? $record['city'] : $opportunity->get('origin_city');
                            if(empty($description['city'])) {
                                unset($description['city']);
                            }
                            $description['state'] = !empty($record['state']) ? $record['state'] : $opportunity->get('origin_state');
                            if(empty($description['state'])) {
                                unset($description['state']);
                            }
                            $description['move_type'] = $opportunity->get('move_type');
                            if(empty($description['move_type'])) {
                                unset($description['move_type']);
                            }

                            // For non-SIRVA (order doesn't matter here)
                            $business_line = $opportunity->get('business_line');
                        }else {
                            // Good luck lol.
                        }
                    }
                } catch (Exception $ex) {
                }

                $item           = [];
                $crmid          = $record['surveysid'];
                $status         = $record['survey_status'];
                $item['id']     = $crmid;
                $item['status'] = $status;
                //$item['title']  = decode_html('Survey Appointment').' - ('.decode_html(vtranslate($record['survey_status'], 'Surveys')).')';
                if (getenv('INSTANCE_NAME') == 'sirva') {
                    $item['title'] = decode_html("\n".implode(', ', $description));
                } else {
                    $item['title'] = decode_html("\n".$description['contactName'] . $record['city'].', '.$record['state'].' -- '.$business_line.', '.$record['phone1']);
                }
                $item['url']    = sprintf('index.php?module=Surveys&view=Detail&record=%s', $crmid);
//                $dateTimeFieldInstance = new DateTimeField($record['survey_date'].' '.$record['survey_time']);
//                $userDateTimeString    = $dateTimeFieldInstance->getDisplayDateTimeValue($currentUser);
//                $dateTimeComponents    = explode(' ', $userDateTimeString);
//                $dateComponent         = $dateTimeComponents[0];
//                //Converting the date format in to Y-m-d . since full calendar expects in the same format
//                $dataBaseDateFormatedString = DateTimeField::__convertToDBFormat($dateComponent, $currentUser->get('date_format'));

                $dateTimeComponents = explode(' ', $carbonStart->toDateTimeString());
                $dateComponent         = $dateTimeComponents[0];
                $dataBaseDateFormatedString = $dateComponent;
                if (count($dateTimeComponents) === 3 && $dateTimeComponents[2] == 'PM') {
                    $startTime = $dateTimeComponents[1].' '.$dateTimeComponents[2];
                    $startTime = date("H:i", strtotime("$startTime"));
                    $item['start'] = $dataBaseDateFormatedString.' '.$startTime;
                } else {
                    $item['start'] = $dataBaseDateFormatedString.' '.$dateTimeComponents[1];
                }
//                $startTimeZone = getFieldTimeZoneValue('survey_time',$crmid);
//                if(!empty($startTimeZone)){
//                    $startDateModel = DateTimeField::convertTimeZone($item['start'], DateTimeField::getDBTimeZone(), $startTimeZone);
//                    $item['start'] = $dataBaseDateFormatedString .' '.$startDateModel->format("H:i:s");
//                }
                //Converting the date format in to Y-m-d . since full calendar expects in the same format
//                $dataBaseDateFormatedString = DateTimeField::__convertToDBFormat($dateComponent, $currentUser->get('date_format'));

                $dateTimeComponents = explode(' ', $carbonEnd->toDateTimeString());
                $dateComponent      = $dateTimeComponents[0];
                $dataBaseDateFormatedString = $dateComponent;
                if (count($dateTimeComponents) === 3 && $dateTimeComponents[2] == 'PM') {
                    $endTime = $dateTimeComponents[1].' '.$dateTimeComponents[2];
                    $endTime = date("H:i", strtotime("$endTime"));
                    $item['end'] = $dataBaseDateFormatedString.' '.$endTime;
                } else {
                    $item['end'] = $dataBaseDateFormatedString.' '.$dateTimeComponents[1];
                }
//                $endTimeZone = getFieldTimeZoneValue('survey_end_time',$crmid);
//                if(!empty($startTimeZone)){
//                    $endDateModel = DateTimeField::convertTimeZone($item['end'], DateTimeField::getDBTimeZone(), $endTimeZone);
//                    $item['end'] = $dataBaseDateFormatedString .' '.$endDateModel->format("H:i:s");
//                }
                $item['className'] = $cssClass;
                $item['allDay']    = false;
                $item['color']     = $color;
                $item['textColor'] = $textColor;
                $item['module']    = $moduleModel->getName();
                $result[]          = $item;
            }
        }
    }
/*
        protected function pullSurveys($start, $end, &$result, $userid=false, $color = null,$textColor = 'white') {
        $query = "SELECT survey_date,survey_time,survey_end_time,contact_id FROM Surveys";
        $query.= " WHERE survey_date >= '$start' AND survey_date <= '$end'";
        $records = $this->queryForRecords($query);
        $db = PearDatabase::getInstance();
        if($userid){
            $focus = new Users();
            $focus->id = $userid;
            $focus->retrieve_entity_info($userid, 'Users');
            $user = Users_Record_Model::getInstanceFromUserObject($focus);
            $userName = $user->getName();
            $queryGenerator = new QueryGenerator($moduleModel->get('name'), $user);
        }else{
            $queryGenerator = new QueryGenerator($moduleModel->get('name'), $currentUser);
        }
        foreach ($records as $record) {
            $item = array();
            list ($modid, $crmid) = vtws_getIdComponents($record['id']);

            $dateTimeFieldInstance = new DateTimeField($record['survey_date'] . ' ' . $record['survey_time']);
            $userDateTimeString = $dateTimeFieldInstance->getDisplayDateTimeValue($currentUser);
            $dateTimeComponents = explode(' ',$userDateTimeString);
            $dateComponent = $dateTimeComponents[0];
            //Converting the date format in to Y-m-d . since full calendar expects in the same format
            $dataBaseDateFormatedString = DateTimeField::__convertToDBFormat($dateComponent, $currentUser->get('date_format'));
            $item['start'] = $dataBaseDateFormatedString.' '. $dateTimeComponents[1];

            $dateTimeFieldInstance = new DateTimeField($record['survey_date'] . ' ' . $record['survey_end_time']);
            $userDateTimeString = $dateTimeFieldInstance->getDisplayDateTimeValue($currentUser);
            $dateTimeComponents = explode(' ',$userDateTimeString);
            $dateComponent = $dateTimeComponents[0];
            //Conveting the date format in to Y-m-d . since full calendar expects in the same format
            $dataBaseDateFormatedString = DateTimeField::__convertToDBFormat($dateComponent, $currentUser->get('date_format'));
            $item['end']   =  $dataBaseDateFormatedString.' '. $dateTimeComponents[1];

            $contactSql = "SELECT firstname, lastname FROM vtiger_surveys JOIN vtiger_contactdetails ON vtiger_surveys.contact_id=vtiger_contactdetails.contactid WHERE surveysid=?";
            $contactParams[] = $crmid;
            $contactPulResult = $db->pquery($contactSql, $contactParams);
            unset($contactParams);
            $contactRow = $contactPulResult->fetchRow();
            if ($contactRow==NULL){
            $contactName = '';
            } else {
            $contactName = $contactRow[0]. ' ' . $contactRow[1];
            }

            $surveyorSql = "SELECT first_name, last_name FROM vtiger_crmentity JOIN vtiger_users ON vtiger_crmentity.smownerid=vtiger_users.id WHERE crmid=?";
            $surveyorParams[] = $crmid;
            $surveyorPulResult = $db->pquery($surveyorSql, $surveyorParams);
            unset($surveyorParams);
            $surveyorRow = $surveyorPulResult->fetchRow();
            if ($surveyorRow==NULL){
            $surveyorName = '';
            } else {
            $surveyorName = $surveyorRow[0]. ' ' . $surveyorRow[1];
            }

            $item['id'] = $crmid;
            $item['title'] = decode_html($surveyorName.'\'s Survey for '.$contactName);
            $item['url']   = sprintf('index.php?module=Surveys&view=Detail&record=%s', $crmid);
            $item['color'] = $color;
            $item['textColor'] = $textColor;
            $item['allDay'] = false;

            $result[] = $item;


        }
    }
    */
//Employees
    protected function pullEmployees($start, $end, &$result, $color = null, $textColor = 'white')
    {
        $query = "SELECT name,date_out,time_out,date_in,time_in FROM Employees";
        $query.= " WHERE date_out >= '$start' AND date_out <= '$end'";
        $records = $this->queryForRecords($query);
        $db = PearDatabase::getInstance();
        $currentUser = Users_Record_Model::getCurrentUserModel();
        foreach ($records as $record) {
            $item = array();
            list($modid, $crmid) = vtws_getIdComponents($record['id']);

            $dateTimeFieldInstance = new DateTimeField($record['date_out'] . ' ' . $record['time_out']);
            $userDateTimeString = $dateTimeFieldInstance->getDisplayDateTimeValue($currentUser);
            $dateTimeComponents = explode(' ', $userDateTimeString);
            $dateComponent = $dateTimeComponents[0];
            //Converting the date format in to Y-m-d . since full calendar expects in the same format
            $dataBaseDateFormatedString = DateTimeField::__convertToDBFormat($dateComponent, $currentUser->get('date_format'));
            $item['start'] = $dataBaseDateFormatedString.' '. $dateTimeComponents[1];

            $dateTimeFieldInstance = new DateTimeField($record['date_in'] . ' ' . $record['time_in']);
            $userDateTimeString = $dateTimeFieldInstance->getDisplayDateTimeValue($currentUser);
            $dateTimeComponents = explode(' ', $userDateTimeString);
            $dateComponent = $dateTimeComponents[0];
            //Conveting the date format in to Y-m-d . since full calendar expects in the same format
            $dataBaseDateFormatedString = DateTimeField::__convertToDBFormat($dateComponent, $currentUser->get('date_format'));
            $item['end']   =  $dataBaseDateFormatedString.' '. $dateTimeComponents[1];

            $item['id'] = $crmid;
            $item['title'] = decode_html($record['name']);
            $item['url']   = sprintf('index.php?module=Employees&view=Detail&record=%s', $crmid);
            $item['color'] = $color;
            $item['textColor'] = $textColor;
            $item['allDay'] = false;

            $result[] = $item;
        }
    }


//Vehicles
    protected function pullVehicles($start, $end, &$result, $color = null, $textColor = 'white')
    {
        $query = "SELECT name,date_out,time_out,date_in,time_in FROM Vehicles";
        $query.= " WHERE date_out >= '$start' AND date_out <= '$end'";
        $records = $this->queryForRecords($query);
        $db = PearDatabase::getInstance();
        $currentUser = Users_Record_Model::getCurrentUserModel();
        foreach ($records as $record) {
            $item = array();
            list($modid, $crmid) = vtws_getIdComponents($record['id']);

            $dateTimeFieldInstance = new DateTimeField($record['date_out'] . ' ' . $record['time_out']);
            $userDateTimeString = $dateTimeFieldInstance->getDisplayDateTimeValue($currentUser);
            $dateTimeComponents = explode(' ', $userDateTimeString);
            $dateComponent = $dateTimeComponents[0];
            //Converting the date format in to Y-m-d . since full calendar expects in the same format
            $dataBaseDateFormatedString = DateTimeField::__convertToDBFormat($dateComponent, $currentUser->get('date_format'));
            $item['start'] = $dataBaseDateFormatedString.' '. $dateTimeComponents[1];

            $dateTimeFieldInstance = new DateTimeField($record['date_in'] . ' ' . $record['time_in']);
            $userDateTimeString = $dateTimeFieldInstance->getDisplayDateTimeValue($currentUser);
            $dateTimeComponents = explode(' ', $userDateTimeString);
            $dateComponent = $dateTimeComponents[0];
            //Conveting the date format in to Y-m-d . since full calendar expects in the same format
            $dataBaseDateFormatedString = DateTimeField::__convertToDBFormat($dateComponent, $currentUser->get('date_format'));
            $item['end']   =  $dataBaseDateFormatedString.' '. $dateTimeComponents[1];

            $item['id'] = $crmid;
            $item['title'] = decode_html($record['name']);
            $item['url']   = sprintf('index.php?module=Vehicles&view=Detail&record=%s', $crmid);
            $item['color'] = $color;
            $item['textColor'] = $textColor;
            $item['allDay'] = false;

            $result[] = $item;
        }
    }

//Equipment
    protected function pullEquipment($start, $end, &$result, $color = null, $textColor = 'white')
    {
        $query = "SELECT name,date_out,time_out,date_in,time_in FROM Equipment";
        $query.= " WHERE date_out >= '$start' AND date_out <= '$end'";
        $records = $this->queryForRecords($query);
        $db = PearDatabase::getInstance();
        $currentUser = Users_Record_Model::getCurrentUserModel();
        foreach ($records as $record) {
            $item = array();
            list($modid, $crmid) = vtws_getIdComponents($record['id']);

            $dateTimeFieldInstance = new DateTimeField($record['date_out'] . ' ' . $record['time_out']);
            $userDateTimeString = $dateTimeFieldInstance->getDisplayDateTimeValue($currentUser);
            $dateTimeComponents = explode(' ', $userDateTimeString);
            $dateComponent = $dateTimeComponents[0];
            //Converting the date format in to Y-m-d . since full calendar expects in the same format
            $dataBaseDateFormatedString = DateTimeField::__convertToDBFormat($dateComponent, $currentUser->get('date_format'));
            $item['start'] = $dataBaseDateFormatedString.' '. $dateTimeComponents[1];

            $dateTimeFieldInstance = new DateTimeField($record['date_in'] . ' ' . $record['time_in']);
            $userDateTimeString = $dateTimeFieldInstance->getDisplayDateTimeValue($currentUser);
            $dateTimeComponents = explode(' ', $userDateTimeString);
            $dateComponent = $dateTimeComponents[0];
            //Conveting the date format in to Y-m-d . since full calendar expects in the same format
            $dataBaseDateFormatedString = DateTimeField::__convertToDBFormat($dateComponent, $currentUser->get('date_format'));
            $item['end']   =  $dataBaseDateFormatedString.' '. $dateTimeComponents[1];

            $item['id'] = $crmid;
            $item['title'] = decode_html($record['name']);
            $item['url']   = sprintf('index.php?module=Equipment&view=Detail&record=%s', $crmid);
            $item['color'] = $color;
            $item['textColor'] = $textColor;
            $item['allDay'] = false;

            $result[] = $item;
        }
    }

//OASurveyRequests
    protected function pullOASurveyRequests($start, $end, &$result, $color = null, $textColor = 'white')
    {
        $query = "SELECT name,date_out,time_out,date_in,time_in FROM Equipment";
        $query.= " WHERE date_out >= '$start' AND date_out <= '$end'";
        $records = $this->queryForRecords($query);
        $db = PearDatabase::getInstance();
        $currentUser = Users_Record_Model::getCurrentUserModel();
        foreach ($records as $record) {
            $item = array();
            list($modid, $crmid) = vtws_getIdComponents($record['id']);

            $dateTimeFieldInstance = new DateTimeField($record['date_out'] . ' ' . $record['time_out']);
            $userDateTimeString = $dateTimeFieldInstance->getDisplayDateTimeValue($currentUser);
            $dateTimeComponents = explode(' ', $userDateTimeString);
            $dateComponent = $dateTimeComponents[0];
            //Converting the date format in to Y-m-d . since full calendar expects in the same format
            $dataBaseDateFormatedString = DateTimeField::__convertToDBFormat($dateComponent, $currentUser->get('date_format'));
            $item['start'] = $dataBaseDateFormatedString.' '. $dateTimeComponents[1];

            $dateTimeFieldInstance = new DateTimeField($record['date_in'] . ' ' . $record['time_in']);
            $userDateTimeString = $dateTimeFieldInstance->getDisplayDateTimeValue($currentUser);
            $dateTimeComponents = explode(' ', $userDateTimeString);
            $dateComponent = $dateTimeComponents[0];
            //Conveting the date format in to Y-m-d . since full calendar expects in the same format
            $dataBaseDateFormatedString = DateTimeField::__convertToDBFormat($dateComponent, $currentUser->get('date_format'));
            $item['end']   =  $dataBaseDateFormatedString.' '. $dateTimeComponents[1];

            $item['id'] = $crmid;
            $item['title'] = decode_html($record['name']);
            $item['url']   = sprintf('index.php?module=Equipment&view=Detail&record=%s', $crmid);
            $item['color'] = $color;
            $item['textColor'] = $textColor;
            $item['allDay'] = false;

            $result[] = $item;
        }
    }


    protected function pullContacts($start, $end, &$result, $color = null, $textColor = 'white')
    {
        $this->pullContactsBySupportEndDate($start, $end, $result, $color, $textColor);
        $this->pullContactsByBirthday($start, $end, $result, $color, $textColor);
    }

    protected function pullContactsBySupportEndDate($start, $end, &$result, $color = null, $textColor = 'white')
    {
        $query = "SELECT firstname,lastname,support_end_date FROM Contacts";
        $query.= " WHERE support_end_date >= '$start' AND support_end_date <= '$end'";
        $records = $this->queryForRecords($query);
        foreach ($records as $record) {
            $item = array();
            list($modid, $crmid) = vtws_getIdComponents($record['id']);
            $item['id'] = $crmid;
            $item['title'] = decode_html(trim($record['firstname'] . ' ' . $record['lastname']));
            $item['start'] = $record['support_end_date'];
            $item['url']   = sprintf('index.php?module=Contacts&view=Detail&record=%s', $crmid);
            $item['color'] = $color;
            $item['textColor'] = $textColor;
            $result[] = $item;
        }
    }

    protected function pullContactsByBirthday($start, $end, &$result, $color = null, $textColor = 'white')
    {
        $db = PearDatabase::getInstance();
        $user = Users_Record_Model::getCurrentUserModel();
        $startDateComponents = split('-', $start);
        $endDateComponents = split('-', $end);

        $userAndGroupIds = array_merge(array($user->getId()), $this->getGroupsIdsForUsers($user->getId()));
        $params = array($start,$end,$start,$end);
        $params = array_merge($userAndGroupIds, $params);

        $year = $startDateComponents[0];

        $query = "SELECT firstname,lastname,birthday,crmid FROM vtiger_contactdetails";
        $query.= " INNER JOIN vtiger_contactsubdetails ON vtiger_contactdetails.contactid = vtiger_contactsubdetails.contactsubscriptionid";
        $query.= " INNER JOIN vtiger_crmentity ON vtiger_contactdetails.contactid = vtiger_crmentity.crmid";
        $query.= " WHERE vtiger_crmentity.deleted=0 AND smownerid IN (".  generateQuestionMarks($userAndGroupIds) .") AND";
        $query.= " ((CONCAT('$year-', date_format(birthday,'%m-%d')) >= ?
						AND CONCAT('$year-', date_format(birthday,'%m-%d')) <= ?)";


        $endDateYear = $endDateComponents[0];
        if ($year !== $endDateYear) {
            $query .= " OR
						(CONCAT('$endDateYear-', date_format(birthday,'%m-%d')) >= ?
							AND CONCAT('$endDateYear-', date_format(birthday,'%m-%d')) <= ?)";
        }
        $query .= ")";

        $queryResult = $db->pquery($query, $params);

        while ($record = $db->fetchByAssoc($queryResult)) {
            $item = array();
            $crmid = $record['crmid'];
            $recordDateTime = new DateTime($record['birthday']);

            $calendarYear = $year;
            if ($recordDateTime->format('m') < $startDateComponents[1]) {
                $calendarYear = $endDateYear;
            }
            $recordDateTime->setDate($calendarYear, $recordDateTime->format('m'), $recordDateTime->format('d'));
            $item['id'] = $crmid;
            $item['title'] = decode_html(trim($record['firstname'] . ' ' . $record['lastname']));
            $item['start'] = $recordDateTime->format('Y-m-d');
            $item['url']   = sprintf('index.php?module=Contacts&view=Detail&record=%s', $crmid);
            $item['color'] = $color;
            $item['textColor'] = $textColor;
            $result[] = $item;
        }
    }

    protected function pullInvoice($start, $end, &$result, $color = null, $textColor = 'white')
    {
        $query = "SELECT subject,duedate FROM Invoice";
        $query.= " WHERE duedate >= '$start' AND duedate <= '$end'";
        $records = $this->queryForRecords($query);
        foreach ($records as $record) {
            $item = array();
            list($modid, $crmid) = vtws_getIdComponents($record['id']);
            $item['id'] = $crmid;
            $item['title'] = decode_html($record['subject']);
            $item['start'] = $record['duedate'];
            $item['url']   = sprintf('index.php?module=Invoice&view=Detail&record=%s', $crmid);
            $item['color'] = $color;
            $item['textColor'] = $textColor;
            $result[] = $item;
        }
    }

    /**
     * Function to pull all the current user projects
     * @param type $startdate
     * @param type $actualenddate
     * @param type $result
     * @param type $color
     * @param type $textColor
     */
    protected function pullProjects($start, $end, &$result, $color = null, $textColor = 'white')
    {
        $db = PearDatabase::getInstance();
        $user = Users_Record_Model::getCurrentUserModel();
        $userAndGroupIds = array_merge(array($user->getId()), $this->getGroupsIdsForUsers($user->getId()));
        $params = array($start,$end,$start);
        $params = array_merge($params, $userAndGroupIds);

        $query = "SELECT projectname, startdate, targetenddate, crmid FROM vtiger_project";
        $query.= " INNER JOIN vtiger_crmentity ON vtiger_project.projectid = vtiger_crmentity.crmid";
        $query.= " WHERE vtiger_crmentity.deleted=0 AND smownerid IN (". generateQuestionMarks($userAndGroupIds) .") AND ";
        $query.= " ((startdate >= ? AND targetenddate < ?) OR ( targetenddate >= ?))";
        $queryResult = $db->pquery($query, $params);

        while ($record = $db->fetchByAssoc($queryResult)) {
            $item = array();
            $crmid = $record['crmid'];
            $item['id'] = $crmid;
            $item['title'] = decode_html($record['projectname']);
            $item['start'] = $record['startdate'];
            $item['end'] = $record['targetenddate'];
            $item['url']   = sprintf('index.php?module=Project&view=Detail&record=%s', $crmid);
            $item['color'] = $color;
            $item['textColor'] = $textColor;
            $result[] = $item;
        }
    }

    /**
     * Function to pull all the current user porjecttasks
     * @param type $startdate
     * @param type $enddate
     * @param type $result
     * @param type $color
     * @param type $textColor
     */
    protected function pullProjectTasks($start, $end, &$result, $color = null, $textColor = 'white')
    {
        $db = PearDatabase::getInstance();
        $user = Users_Record_Model::getCurrentUserModel();
        $userAndGroupIds = array_merge(array($user->getId()), $this->getGroupsIdsForUsers($user->getId()));
        $params = array($start,$end,$start);
        $params = array_merge($params, $userAndGroupIds);

        $query = "SELECT projecttaskname, startdate, enddate, crmid FROM vtiger_projecttask";
        $query.= " INNER JOIN vtiger_crmentity ON vtiger_projecttask.projecttaskid = vtiger_crmentity.crmid";
        $query.= " WHERE vtiger_crmentity.deleted=0 AND ";
        $query.= " ((startdate >= ? AND enddate < ?) OR ( enddate >= ?))";
        $query.= " AND smownerid IN (". generateQuestionMarks($userAndGroupIds) .")";
        $queryResult = $db->pquery($query, $params);

        while ($record = $db->fetchByAssoc($queryResult)) {
            $item = array();
            $crmid = $record['crmid'];
            $item['id'] = $crmid;
            $item['title'] = decode_html($record['projecttaskname']);
            $item['start'] = $record['startdate'];
            $item['end'] = $record['enddate'];
            $item['url']   = sprintf('index.php?module=ProjectTask&view=Detail&record=%s', $crmid);
            $item['color'] = $color;
            $item['textColor'] = $textColor;
            $result[] = $item;
        }
    }
}
