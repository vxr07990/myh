<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/**
 * Calendar Module Model Class
 */
class Inbox_Module_Model extends Vtiger_Module_Model
{

    /**
     * Function returns Calendar Reminder record models
     * @return <Array of Calendar_Record_Model>
     */
    public static function getReminder()
    {
        $db = PearDatabase::getInstance();
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $activityReminder = $currentUserModel->getCurrentUserActivityReminderInSeconds();
        $recordModels = array();

        // if($activityReminder != '' ) {
            $currentTime = time();
        $date = date('Y-m-d', strtotime("+$activityReminder seconds", $currentTime));
        $time = date('H:i',   strtotime("+$activityReminder seconds", $currentTime));
        $query2 = '';
        $params = array();
            //array of group names from the groups table whos keys are the group id
            $my_groups = Users_Record_Model::getCurrentUserGroups(false, false, false);
            //build query that will search for messages in all your groups
            foreach ($my_groups as $my_group=>$value) {
                if ($query2 == '') {
                    $query2 .= 'vtiger_crmentity.smownerid = ?';
                } else {
                    $query2 .= ' OR vtiger_crmentity.smownerid = ?';
                }
                $params[] =  $my_group;
            }
        $reminderActivitiesResult = "SELECT vtiger_inbox.inboxid, vtiger_inbox.inbox_message, vtiger_inbox.inbox_type, vtiger_inbox_read.inbox_id
			FROM (vtiger_inbox, vtiger_crmentity)
			LEFT JOIN vtiger_inbox_read ON vtiger_inbox_read.inbox_id=vtiger_inbox.inboxid AND vtiger_inbox_read.user_id = ?
			WHERE vtiger_crmentity.crmid=vtiger_inbox.inboxid AND ".$query2." LIMIT 20";
            //echo $reminderActivitiesResult;

            $result = $db->pquery($reminderActivitiesResult, array($currentUserModel->getId(), $params));
            //var_dump($result->fetchRow());
            //$rows = $db->num_rows($result);
            if (!$result) {
                return array();
            }
        while ($row =& $result->fetchRow()) {
            if (empty($row['inbox_id'])) {
                $recordsArray[] =
                        array(
                            'id' => $row['inboxid'],
                            'message' => $row['inbox_message'],
                            'subject' => $row['inbox_type'],
                            'onClickTarget'=>'index.php?module=Inbox&view=Detail&record='.$row['inboxid'],
                            'onCloseCall'=>'',
                            'type'=>'error',
                            'icon'=>'',
                            'buttons'=>array('View'=>array('type'=>'', 'link'=>'index.php?module=Inbox&view=Detail&record='.$row['inboxid']))//name=>array('type'=>, 'link'=>)
                        );
            }
        }
        //	}
            return $recordsArray;
    }

    public static function getParticipatingAgentRequestStatus($id)
    {
        $db = PearDatabase::getInstance();
        $sql = 'SELECT vtiger_participating_agents.status, vtiger_users.first_name, vtiger_users.last_name  FROM `vtiger_inbox`, `vtiger_participating_agents` LEFT JOIN vtiger_users
ON vtiger_users.id=vtiger_participating_agents.modified_by WHERE vtiger_inbox.inboxid=? AND vtiger_participating_agents.id=vtiger_inbox.inbox_link';
        $result = $db->pquery($sql, array($id));
        if (!$result) {
            return null;
        }
        $result = $result->fetchRow();
        $db->completeTransaction();

        //file_put_contents('logs/devLog.log', "\n getParticipatingAgentRequestStatus:".print_r($result, true), FILE_APPEND);

        if (count($result)==0) {
            return null;
        } else {
            return array('status'=>$result['status'], 'modified_by_first_name'=>$result['first_name'], 'modified_by_last_name'=>$result['last_name']);
        }
    }
}
