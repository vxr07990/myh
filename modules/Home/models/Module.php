<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Home_Module_Model extends Vtiger_Module_Model
{

    /**
     * Function returns the default view for the Home module
     * @return <String>
     */
    public function getDefaultViewName()
    {
        return 'DashBoard';
    }

    /**
     * Function returns latest comments across CRM
     * @param <Vtiger_Paging_Model> $pagingModel
     * @return <Array>
     */
    public function getComments($pagingModel)
    {
        $db = PearDatabase::getInstance();
        //Below function returns '' currently
        $nonAdminAccessQuery = Users_Privileges_Model::getNonAdminAccessControlQuery('ModComments');
        $userModel = Users_Privileges_Model::getCurrentUserModel();
        //sirva wants history displayed to be only for the current logged in user.
        if(!$userModel->isAdminUser()){
            $userID = $userModel->getid();
            $nonAdminAccessQuery = 'AND userid = '.$userID;
        }
        $result = $db->pquery('SELECT *, vtiger_crmentity.createdtime AS createdtime, vtiger_crmentity.smownerid AS smownerid,
						crmentity2.crmid AS parentId, crmentity2.setype AS parentModule FROM vtiger_modcomments
						INNER JOIN vtiger_crmentity ON vtiger_modcomments.modcommentsid = vtiger_crmentity.crmid
							AND vtiger_crmentity.deleted = 0
						INNER JOIN vtiger_crmentity crmentity2 ON vtiger_modcomments.related_to = crmentity2.crmid
							AND crmentity2.deleted = 0
						 '.$nonAdminAccessQuery.'
						ORDER BY vtiger_crmentity.crmid DESC LIMIT ?, ?',
                array($pagingModel->getStartIndex(), $pagingModel->getPageLimit()));
        $comments = array();
        for ($i=0; $i<$db->num_rows($result); $i++) {
            $row = $db->query_result_rowdata($result, $i);
            if (Users_Privileges_Model::isPermitted($row['setype'], 'DetailView', $row['related_to'])) {
                $commentModel = Vtiger_Record_Model::getCleanInstance('ModComments');
                $commentModel->setData($row);
                $time = $commentModel->get('createdtime');
                $comments[$time] = $commentModel;
            }
        }

        return $comments;
    }

        /**
     * Function returns part of the query to  fetch only  activity
     * @param <String> $type - comments, updates or all
     * @return <String> $query
     */
          public function getActivityQuery($type)
          {
              if ($type == 'updates') {
                  $query=' AND module != "ModComments" ';
                  return $query;
              }
          }


    /**
     * Function returns comments and recent activities across CRM
     * @param <Vtiger_Paging_Model> $pagingModel
     * @param <String> $type - comments, updates or all
     * @return <Array>
     */
    public function getHistory($pagingModel, $type=false)
    {
        if (empty($type)) {
            $type = 'all';
        }
        //TODO: need to handle security
        $comments = array();
        if ($type == 'comments') {
            $modCommentsModel = Vtiger_Module_Model::getInstance('ModComments');
            if ($modCommentsModel->isPermitted('DetailView')) {
                $comments = $this->getComments($pagingModel);
            }
            return $comments;
        }

        //As getComments api is used to get comment infomation,no need of getting
        //comment information again,so avoiding from modtracker
        //updateActivityQuery api is used to update a query to fetch a only activity

        if ($type == 'updates' || $type == 'all') {
            $whoLimit = '';
            $params = [];
                $db = PearDatabase::getInstance();
                $userModel = Users_Privileges_Model::getCurrentUserModel();
                if(!$userModel->isAdminUser()) {
                    $userID = $userModel->getId();
                    $whoLimit = ' AND `whodid` = ? ';
                    $params[] = $userID;
                }
                $queryforActivity= $this->getActivityQuery($type);
                $stmt = 'SELECT vtiger_modtracker_basic.*
                            FROM vtiger_modtracker_basic
                            INNER JOIN vtiger_crmentity ON vtiger_modtracker_basic.crmid = vtiger_crmentity.crmid
                            AND deleted = 0 ' . $queryforActivity . $whoLimit . '
                            ORDER BY vtiger_modtracker_basic.id DESC LIMIT ?, ?';
                $params[] = $pagingModel->getStartIndex();
                $params[] = $pagingModel->getPageLimit();
                $result = $db->pquery($stmt, $params);

                $history = array();
                for ($i=0; $i<$db->num_rows($result); $i++) {
                    $row = $db->query_result_rowdata($result, $i);
                    $moduleName = $row['module'];
                    $recordId = $row['crmid'];
                    if (Users_Privileges_Model::isPermitted($moduleName, 'DetailView', $recordId)) {
                        $modTrackerRecordModel = new ModTracker_Record_Model();
                        $modTrackerRecordModel->setData($row)->setParent($recordId, $moduleName);
                        $time = $modTrackerRecordModel->get('changedon');
                        //@TODO: This suppresses changes at the same time.
                        $history[$time.$i] = $modTrackerRecordModel;
                    }
                }
            return $history;
        }
        return false;
    }

    /**
     * Function returns the Calendar Events for the module
     * @param <String> $mode - upcoming/overdue mode
     * @param <Vtiger_Paging_Model> $pagingModel - $pagingModel
     * @param <String> $user - all/userid
     * @param <String> $recordId - record id
     * @return <Array>
     */
    public function getCalendarActivities($mode, $pagingModel, $user, $recordId = false)
    {
        $currentUser = Users_Record_Model::getCurrentUserModel();

        if (!$user) {
            $user = $currentUser->getId();
        }

        $nowInUserFormat = Vtiger_Datetime_UIType::getDisplayDateTimeValue(date('Y-m-d H:i:s'));
        $nowInDBFormat = Vtiger_Datetime_UIType::getDBDateTimeValue($nowInUserFormat);
        list($currentDate, $currentTime) = explode(' ', $nowInDBFormat);

        /*
        $db = PearDatabase::getInstance();
        $query = "SELECT vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_crmentity.setype, vtiger_activity.* FROM vtiger_activity
                    INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_activity.activityid
                    LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid";

        $query .= Users_Privileges_Model::getNonAdminAccessControlQuery('Calendar');

        $query .= " WHERE vtiger_crmentity.deleted=0
                    AND (vtiger_activity.activitytype NOT IN ('Emails'))
                    AND (vtiger_activity.status is NULL OR vtiger_activity.status NOT IN ('Completed', 'Deferred'))
                    AND (vtiger_activity.eventstatus is NULL OR vtiger_activity.eventstatus NOT IN ('Held'))";

        if ($mode === 'upcoming') {
            $query .= " AND CASE WHEN vtiger_activity.activitytype='Task' THEN due_date >= '$currentDate' ELSE CONCAT(due_date,' ',time_end) >= '$nowInDBFormat' END";
        } elseif ($mode === 'overdue') {
            $query .= " AND CASE WHEN vtiger_activity.activitytype='Task' THEN due_date < '$currentDate' ELSE CONCAT(due_date,' ',time_end) < '$nowInDBFormat' END";
        }

        $params = array();
        if($user != 'all' && $user != '') {
            if($user === $currentUser->id) {
                $query .= " AND vtiger_crmentity.smownerid = ?";
                $params[] = $user;
            }
        }

        $query .= " ORDER BY date_start, time_start LIMIT ?, ?";
        $params[] = $pagingModel->getStartIndex();
        $params[] = $pagingModel->getPageLimit()+1;
        $result = $db->pquery($query, $params);
        $numOfRows = $db->num_rows($result);
        */

        $startTime = '';
        $endTime = '';
        if ($mode === 'upcoming') {
            $startTime = $currentDate;
        } elseif ($mode === 'overdue') {
            $endTime = $currentDate;
        }
        $limit = $pagingModel->getStartIndex() . ', ' . $pagingModel->getPageLimit()+1;

        $request = new Vtiger_Request(
            [
                'start' => $startTime,
                'end' => $endTime,
                'type' => 'Calendar',
                'userid' => $user,
                'color' => '',
                'textColor' => '',
                'limit' => $pagingModel->getStartIndex() . ', ' . $pagingModel->getPageLimit()+1,
            ]
        );
        $calendarFeedAction = new Calendar_Feed_Action;
        //$events = $calendarFeedAction->process($request);
        $calendarFeedAction->pullTasks($startTime, $endTime, $events, '', '', $user, $limit);
        $calendarFeedAction->pullEvents($startTime, $endTime, $events, $user, '', '');
        $calendarFeedAction->pullSurveys($startTime, $endTime, $events, $user, '', '');

        $numOfRows = count($events);
        $activities = array();
        foreach ($events as $row) {
            try {
                $model = Vtiger_Record_Model::getCleanInstance($row['module']);
            } catch (Exception $ex) {
                $model = Vtiger_Record_Model::getCleanInstance('Calendar');
            }
            $model->setData($row);
            if ($row['activitytype'] == 'Task') {
                //$due_date = $row["due_date"];
                $dayEndTime = "23:59:59";
                //$EndDateTime = Vtiger_Datetime_UIType::getDBDateTimeValue($due_date." ".$dayEndTime);
                //$dueDateTimeInDbFormat = explode(' ',$EndDateTime);
                //$dueTimeInDbFormat = $dueDateTimeInDbFormat[1];
                //$model->set('time_end',$dueTimeInDbFormat);
                $model->set('time_end', $dayEndTime);
            }
            $model->set('due_date', $row['end']);
            $model->set('date_start', $row['start']);
            $model->set('subject', $row['title']);
            $model->setId($row['id']);
            $activities[] = $model;
        }

        $pagingModel->calculatePageRange($activities);
        if ($numOfRows > $pagingModel->getPageLimit()) {
            array_pop($activities);
            $pagingModel->set('nextPageExists', true);
        } else {
            $pagingModel->set('nextPageExists', false);
        }

        return $activities;
    }
}
