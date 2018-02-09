<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
********************************************************************************/

use Carbon\Carbon;

/**
 * Class to handle repeating events
 */
class Calendar_RepeatEvents
{

    /**
     * Get timing using YYYY-MM-DD HH:MM:SS input string.
     */
    public static function mktime($fulldateString)
    {
        $splitpart = self::splittime($fulldateString);
        $datepart = split('-', $splitpart[0]);
        $timepart = split(':', $splitpart[1]);
        return mktime($timepart[0], $timepart[1], 0, $datepart[1], $datepart[2], $datepart[0]);
    }
    /**
     * Increment the time by interval and return value in YYYY-MM-DD HH:MM format.
     */
    public static function nexttime($basetiming, $interval)
    {
        return date('Y-m-d H:i', strtotime($interval, $basetiming));
    }
    /**
     * Based on user time format convert the YYYY-MM-DD HH:MM value.
     */
    public static function formattime($timeInYMDHIS)
    {
        global $current_user;
        $format_string = 'Y-m-d H:i';
        switch ($current_user->date_format) {
            case 'dd-mm-yyyy': $format_string = 'd-m-Y H:i'; break;
            case 'mm-dd-yyyy': $format_string = 'm-d-Y H:i'; break;
            case 'yyyy-mm-dd': $format_string = 'Y-m-d H:i'; break;
        }
        return date($format_string, self::mktime($timeInYMDHIS));
    }
    /**
     * Split full timing into date and time part.
     */
    public static function splittime($fulltiming)
    {
        return split(' ', $fulltiming);
    }
    /**
     * Calculate the time interval to create repeated event entries.
     */
    public static function getRepeatInterval($type, $frequency, $recurringInfo, $start_date, $limit_date)
    {
        $repeatInterval = array();
        $starting = self::mktime($start_date);
        $limiting = self::mktime($limit_date);

        if ($type == 'Daily') {
            $count = 0;
            while (true) {
                ++$count;
                $interval = ($count * $frequency);
                if (self::mktime(self::nexttime($starting, "+$interval days")) > $limiting) {
                    break;
                }
                $repeatInterval[] = $interval;
            }
        } elseif ($type == 'Weekly') {
            if ($recurringInfo->dayofweek_to_rpt == null) {
                $count = 0;
                $weekcount = 7;
                while (true) {
                    ++$count;
                    $interval = $count * $weekcount;
                    if (self::mktime(self::nexttime($starting, "+$interval days")) > $limiting) {
                        break;
                    }
                    $repeatInterval[] = $interval;
                }
            } else {
                $count = 0;
                while (true) {
                    ++$count;
                    $interval = $count;
                    $new_timing = self::mktime(self::nexttime($starting, "+$interval days"));
                    $new_timing_dayofweek = date('N', $new_timing);
                    if ($new_timing > $limiting) {
                        break;
                    }
                    if (in_array($new_timing_dayofweek-1, $recurringInfo->dayofweek_to_rpt)) {
                        $repeatInterval[] = $interval;
                    }
                }
            }
        } elseif ($type == 'Monthly') {
            $count = 0;
            $avg_monthcount = 30; // TODO: We need to handle month increments precisely!
            while (true) {
                ++$count;
                $interval = $count * $avg_monthcount;
                if (self::mktime(self::nexttime($starting, "+$interval days")) > $limiting) {
                    break;
                }
                $repeatInterval[] = $interval;
            }
        } elseif ($type == 'Yearly') {
            $count = 0;
            $avg_monthcount = 30;
            while (true) {
                ++$count;
                $interval = $count * $avg_monthcount;
                if (self::mktime(self::nexttime($starting, "+$interval days")) > $limiting) {
                    break;
                }
                $repeatInterval[] = $interval;
            }
        }
        return $repeatInterval;
    }

    /**
     * Repeat Activity instance till given limit.
     */
    public static function repeat($focus, $recurObj)
    {
        global $adb;
        $frequency = $recurObj->recur_freq;
        $repeattype= $recurObj->recur_type;
        
        $base_focus = new Activity();
        $base_focus->column_fields = $focus->column_fields;
        $base_focus->id = $focus->id;

        $skip_focus_fields = array('record_id', 'createdtime', 'modifiedtime', 'recurringtype');

        /** Create instance before and reuse */
        $new_focus = new Activity();

        $eventStartDate = $focus->column_fields['date_start'];
        $interval = strtotime($focus->column_fields['due_date']) -
                strtotime($focus->column_fields['date_start']);

        self::checkOccurrences($focus->column_fields['id'], $recurObj);
        
        foreach ($recurObj->recurringdates as $index => $startDate) {
            if ($index == 0 && $eventStartDate == $startDate) {
                continue;
            }
            $occurrence = self::retrieveOccurrence($focus->column_fields['id'], $startDate);
            if ($occurrence != null) {
                self::updateOccurrence($focus->column_fields['id'], $occurrence, $startDate);
                continue;
            }
            $startDateTimestamp = strtotime($startDate);
            $endDateTime = $startDateTimestamp + $interval;
            $endDate = date('Y-m-d', $endDateTime);
            
            // Reset the new_focus and prepare for reuse
            if (isset($new_focus->id)) {
                unset($new_focus->id);
            }
            $new_focus->column_fields = array();

            foreach ($base_focus->column_fields as $key=>$value) {
                if (in_array($key, $skip_focus_fields)) {
                    // skip copying few fields
                } elseif ($key == 'date_start') {
                    $new_focus->column_fields['date_start'] = $startDate;
                } elseif ($key == 'due_date') {
                    $new_focus->column_fields['due_date']   = $endDate;
                } else {
                    $new_focus->column_fields[$key]         = $value;
                }
            }
            if ($numberOfRepeats > 10 && $index > 10) {
                unset($new_focus->column_fields['sendnotification']);
            }
            $new_focus->save('Calendar');
            $record = $new_focus->id;

            // add repeat event to recurrence relation table
            $sql = "INSERT INTO `vtiger_recurrencerel` VALUES (?,?)";
            $adb->pquery($sql, [$record, $focus->column_fields['id']]);
            
            // add repeat event to contact record
            if (isset($_REQUEST['contactidlist']) && $_REQUEST['contactidlist'] != '') {
                //split the string and store in an array
                $storearray = explode(";", $_REQUEST['contactidlist']);
                $del_sql = "delete from vtiger_cntactivityrel where activityid=?";
                $adb->pquery($del_sql, array($record));
                foreach ($storearray as $id) {
                    if ($id != '') {
                        $sql = "insert into vtiger_cntactivityrel values (?,?)";
                        $adb->pquery($sql, array($id, $record));
                    }
                }
            }
            
            //to delete contact relation while editing event
            if (isset($_REQUEST['deletecntlist']) && $_REQUEST['deletecntlist'] != '' && $_REQUEST['mode'] == 'edit') {
                //split the string and store it in an array
                $storearray = explode(";", $_REQUEST['deletecntlist']);
                foreach ($storearray as $id) {
                    if ($id != '') {
                        $sql = "delete from vtiger_cntactivityrel where contactid=? and activityid=?";
                        $adb->pquery($sql, array($id, $record));
                    }
                }
            }
        }
    }

    /**
     * @param $parentId
     * @param $date
     *
     * @return null | recordId of occurrence
     */
    public static function retrieveOccurrence($parentId, $date)
    {
        $db = PearDatabase::getInstance();
        $sql = "SELECT * FROM `vtiger_recurrencerel`
				JOIN `vtiger_activity` ON `vtiger_activity`.activityid=`vtiger_recurrencerel`.activityid
				JOIN `vtiger_crmentity` ON `vtiger_activity`.activityid=`vtiger_crmentity`.crmid
				WHERE parentid=? AND date_start=? AND deleted=0";
        $result = $db->pquery($sql, [$parentId, $date]);

        if ($db->num_rows($result) == 0) {
            return null;
        }

        $row = $result->fetchRow();
        return $row['activityid'];
    }

    public static function checkOccurrences($parentId, $recurObj)
    {
        // Remove all linked occurrences in vtiger_recurrencerel table that are not on list of recurring dates
        $db = PearDatabase::getInstance();
        $sql = "SELECT `vtiger_activity`.activityid, `vtiger_activity`.date_start, `vtiger_activity`.time_start
				FROM `vtiger_activity`
				JOIN `vtiger_recurrencerel` ON `vtiger_activity`.activityid=`vtiger_recurrencerel`.activityid
				JOIN `vtiger_crmentity` ON `vtiger_activity`.activityid=`vtiger_crmentity`.crmid
				WHERE parentid=? AND deleted=0";
        $result = $db->pquery($sql, [$parentId]);

        global $current_user;
        $userTz = new DateTimeZone($current_user->time_zone);
        $dbTz = DateTimeField::getDBTimeZone();

        while ($row =& $result->fetchRow()) {
            $startDtCarbon = Carbon::createFromFormat('Y-m-d H:i:s', $row['date_start'].' '.$row['time_start'], $dbTz);
            $startDtCarbon->setTimezone($userTz);
            if (in_array($startDtCarbon->toDateString(), $recurObj->recurringdates)) {
                continue;
            }
            self::removeOccurrences($parentId, $row['activityid']);
        }
    }

    public static function updateOccurrence($parentId, $recordId, $startDate)
    {
        global $current_user;
        // Function to update an existing occurrence instead of making a new one
        $db = PearDatabase::getInstance();
        $sql = "SELECT `vtiger_activity`.subject,
					   `vtiger_activity`.activitytype,
					   `vtiger_activity`.time_start,
					   `vtiger_activity`.time_end,
					   `vtiger_activity`.date_start,
					   `vtiger_activity`.due_date,
					   `vtiger_activity`.sendnotification,
					   `vtiger_activity`.duration_hours,
					   `vtiger_activity`.duration_minutes,
					   `vtiger_activity`.status,
					   `vtiger_activity`.eventstatus,
					   `vtiger_activity`.priority,
					   `vtiger_activity`.location,
					   `vtiger_activity`.notime,
					   `vtiger_activity`.visibility,
					   `vtiger_activity`.exchange_freebusy,
					   `vtiger_crmentity`.modifiedtime,
					   `vtiger_crmentity`.agentid
			    FROM `vtiger_activity`
			    JOIN `vtiger_crmentity` ON `vtiger_activity`.activityid=`vtiger_crmentity`.crmid
			    WHERE `vtiger_activity`.activityid=?";
        $result = $db->pquery($sql, [$parentId]);

        while ($row =& $result->fetchRow()) {
            $userTz = new DateTimeZone($current_user->time_zone);
            $dbTz = DateTimeField::getDBTimeZone();
            $startDtCarbon = Carbon::createFromFormat('Y-m-d H:i:s', $row['date_start'].' '.$row['time_start'], $dbTz);
            $startDtCarbon->setTimezone($userTz);
            $endDtCarbon = Carbon::createFromFormat('Y-m-d H:i:s', $row['due_date'].' '.$row['time_end'], $dbTz);
            $endDtCarbon->setTimezone($userTz);
            $diff = $startDtCarbon->diffInMinutes($endDtCarbon);

            $occurrenceStart = Carbon::createFromFormat('Y-m-d H:i:s', $startDate.' '.$startDtCarbon->toTimeString(), $userTz);
            $occurrenceStart->setTimezone($dbTz);
            $occurrenceEnd = $occurrenceStart->copy()->addMinutes($diff);
            $sql = "UPDATE `vtiger_activity` SET
					   `vtiger_activity`.subject=?,
					   `vtiger_activity`.activitytype=?,
					   `vtiger_activity`.date_start=?,
					   `vtiger_activity`.time_start=?,
					   `vtiger_activity`.due_date=?,
					   `vtiger_activity`.time_end=?,
					   `vtiger_activity`.sendnotification=?,
					   `vtiger_activity`.duration_hours=?,
					   `vtiger_activity`.duration_minutes=?,
					   `vtiger_activity`.status=?,
					   `vtiger_activity`.eventstatus=?,
					   `vtiger_activity`.priority=?,
					   `vtiger_activity`.location=?,
					   `vtiger_activity`.notime=?,
					   `vtiger_activity`.visibility=?,
					   `vtiger_activity`.exchange_freebusy=?
					WHERE `vtiger_activity`.activityid=?";

            $db->pquery($sql, [$row['subject'],
                               $row['activitytype'],
                               $occurrenceStart->toDateString(),
                               $occurrenceStart->toTimeString(),
                               $occurrenceEnd->toDateString(),
                               $occurrenceEnd->toTimeString(),
                               $row['sendnotification'],
                               $row['duration_hours'],
                               $row['duration_minutes'],
                               $row['status'],
                               $row['eventstatus'],
                               $row['priority'],
                               $row['location'],
                               $row['notime'],
                               $row['visibility'],
                               $row['exchange_freebusy'],
                               $recordId]);

            $sql = "UPDATE `vtiger_crmentity` SET
					   `vtiger_crmentity`.modifiedtime=?,
					   `vtiger_crmentity`.agentid=?
					WHERE `vtiger_crmentity`.crmid=?";

            $db->pquery($sql, [$row['modifiedtime'], $row['agentid'], $recordId]);
        }
    }

    /**
     * @param      $parentId
     * @param null $recordId
     *
     * Removes record matching $recordId if set, otherwise removes all occurrences of $parentId
     */
    public static function removeOccurrences($parentId, $recordId = null)
    {
        $db = PearDatabase::getInstance();
        if ($recordId == null) {
            $sql    = "SELECT `activityid` FROM `vtiger_recurrencerel` WHERE parentid=?";
            $result = $db->pquery($sql, [$parentId]);
        } else {
            $sql    = "SELECT `activityid` FROM `vtiger_recurrencerel` WHERE parentid=? AND activityid=?";
            $result = $db->pquery($sql, [$parentId, $recordId]);
        }

        while ($row =& $result->fetchRow()) {
            try {
                $recModel = Vtiger_Record_Model::getInstanceById($row['activityid']);
                $recModel->delete();
            } catch (Exception $e) {
                // Record is already deleted - Do nothing
            }
        }
    }

    public static function repeatFromRequest($focus)
    {
        global $log, $default_charset, $current_user;
        $recurObj = getrecurringObjValue();
        self::repeat($focus, $recurObj);
    }
}
