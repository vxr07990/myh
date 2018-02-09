<?php

namespace Igc\Ews\Synchronization;

use PearDatabase;
use Carbon\Carbon;
use Users_Record_Model;
use Igc\Ews\Calendar\LocalEvent;
use Igc\Ews\Calendar\LocalUpdate;
use MoveCrm\Models\User as CrmUser;
use Igc\Ews\Calendar as ExchangeCalendar;

require_once 'include/Webservices/Create.php';

class Local
{
    /**
     * Get the calendar events that have been
     * created locally since the last sync
     *
     * @param Users_Record_Model $user
     * @return LocalEvent[] Collection of event objects to be pushed up
     */
    public static function getCreates(Users_Record_Model $user)
    {
        file_put_contents('logs/devLog.log', date('Y-m-d H:i:s - ')."Entering getCreates function\n", FILE_APPEND);
        $user = new CrmUser($user);
        $db = PearDatabase::getInstance();

        // Get all (non-deleted) local calendar events for the current user
        $sql = "SELECT crmid, setype FROM vtiger_crmentity
                WHERE setype IN ('Calendar','Surveys')
                AND deleted = 0 AND smownerid=?";
        $localCalEvents = $db->pquery($sql, [$user->id]);

        //@TODO not sure this is entirely wise.
        // Get all previously synchronized local calendar events
        $sql = "SELECT activity_id FROM calendar_exchange_metadata WHERE userid=?";
        $syncedLocalEvents = $db->pquery($sql, [$user->id]);

        // Which local events have not yet been synchronized?
        $unsyncedLocalSurveys = [];
        foreach ($localCalEvents as $localEvent) {
            if (!self::in_array_r($localEvent['crmid'], $syncedLocalEvents)) {
                if ($localEvent['setype'] == 'Surveys') {
                    $unsyncedLocalSurveys[] = $localEvent['crmid'];
                } else {
                    $unsyncedLocalEvents[] = $localEvent['crmid'];
                }
            }
        }

        // Grab user's timezone
        $sql = "SELECT time_zone FROM `vtiger_users` WHERE id=?";
        $userInfo = $db->pquery($sql, [$user->id]);
        $timeZone = $userInfo->fields['time_zone'];

        // Just for a default value.
        $location = NULL;

        // Grab all the data for the unsynced
        // events and build up some LocalEvent objects
        foreach ($unsyncedLocalEvents as $newLocal) {

            // Get the basic event info
            $sql = "SELECT * FROM vtiger_activity WHERE activityid = ?";
            $param = [$newLocal];
            $result = $db->pquery($sql, $param);

            // Get the body/description
            $sql = "SELECT description FROM vtiger_crmentity WHERE crmid = ?";
            $param = [$newLocal];
            $description = $db->pquery($sql, $param);
            $body = (!$description->fields) ? "" : $description->fields['description'];

            // Get reminder info if any
            $sql = "SELECT reminder_time FROM vtiger_activity_reminder WHERE activity_id = ?";
            $param = [$newLocal];
            $reminderTime = $db->pquery($sql, $param);
            $reminderTime = (!$reminderTime->fields) ? false : $reminderTime->fields['reminder_time'];

            $isAllDay = $result->fields['activitytype'] == 'Task';
            $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $result->fields['date_start'] . " " . $result->fields['time_start']);
            $startDate->setTimezone($timeZone);
            $endDate = Carbon::createFromFormat('Y-m-d', $result->fields['due_date']);
            $numDays = $startDate->diffInDays($endDate);

            // Assign values to properties needed to instantiate new LocalEvent object
            $subject = $result->fields['subject'];
            if ($isAllDay) {
                $startTime = $result->fields['date_start']." 00:00:00";
                $startTime = Carbon::createFromFormat('Y-m-d H:i:s', $startTime, $timeZone);
                $endTime = $startTime->copy()->addDays($numDays+1)->subMinute();
            } else {
                $startTime = $result->fields['date_start']." ".$result->fields['time_start'];
                $startTime = Carbon::createFromFormat('Y-m-d H:i:s', $startTime);
                $endTime = $result->fields['due_date']." ".($result->fields['time_end']? :'00:00:00');
                $endTime = Carbon::createFromFormat('Y-m-d H:i:s', $endTime);
            }
            $setReminder = ($reminderTime) ? true : false;
            $reminderTimeBefore = ($setReminder) ? $reminderTime : 0;
            $activityId = $newLocal;

            $event = new LocalEvent(
                $user,
                $subject,
                $startTime,
                $endTime,
                $setReminder,
                $reminderTimeBefore,
                $body,
                $activityId,
                $location
            );
            $events[] = $event;
        }

        // Handle unsynced local survey appointments
        foreach ($unsyncedLocalSurveys as $newLocal) {

            // Get the basic survey info
            $sql = "SELECT * FROM vtiger_surveys WHERE surveysid = ?";
            $param = [$newLocal];
            $result = $db->pquery($sql, $param);

            // Generate the body/description
            $body = self::generateSurveyBody($result);
            $reminderTime = false;

            // Assign values to properties needed to instantiate new LocalEvent object
            $subject = self::generateSurveySubject($result);

            // Create the Location
            $location = self::generateSurveyLocation($result);

            $startTime = $result->fields['survey_date']." ".$result->fields['survey_time'];
            $startTime = Carbon::createFromFormat('Y-m-d H:i:s', $startTime);
            $endTime = $result->fields['survey_date']." ".($result->fields['survey_end_time'] ?: '00:00:00');
            $endTime = Carbon::createFromFormat('Y-m-d H:i:s', $endTime);

            if ($endTime->lt($startTime)) {
                $endTime->addDay();
            }

            $setReminder = ($reminderTime) ? true : false;
            $reminderTimeBefore = ($setReminder) ? $reminderTime : 0;
            $activityId = $newLocal;

            $event = new LocalEvent(
                $user,
                $subject,
                $startTime,
                $endTime,
                $setReminder,
                $reminderTimeBefore,
                $body,
                $activityId,
                $location,
                true
            );
            $events[] = $event;
        }
        return $events;
    }

    /**
     * Get the calendar events that have been
     * updated locally since the last sync
     *
     * @return array // of event objects to be pushed up
     */
    public static function getUpdates(Users_Record_Model $user)
    {
        $db = PearDatabase::getInstance();

        // Get all previously synchronized local calendar events
        $sql = "SELECT * FROM calendar_exchange_metadata WHERE userid=?";
        $syncedLocalEvents = $db->pquery($sql, [$user->getId()]);

        // Figure out which events have been changed since the last sync
        foreach ($syncedLocalEvents as $event) {
            $crmid = $event['activity_id'];
            $sql = "SELECT modifiedtime FROM vtiger_crmentity
                    WHERE crmid = ?";
            $param = [$event['activity_id']];
            $result = $db->pquery($sql, $param);

            $modTime = $result->fields['modifiedtime'];
            $lastSyncTime = $event['last_sync_time'];

            if ($modTime !== $lastSyncTime) {
                // before we consider this item as needing updated,
                // let's first check to see if it's been soft-deleted...
                $sql = "SELECT deleted FROM vtiger_crmentity WHERE crmid = $crmid";
                $result = $db->query($sql);
                if (!$result->fields['deleted'] == 1) {
                    if ($event['is_survey_appointment']) {
                        $updatedSurveys[] = $crmid;
                    } else {
                        $needsUpdated[] = $crmid;
                    }
                }
            }
        }

        // Grab all the data for the events that need updated
        foreach ($needsUpdated as $event) {

            // Grab the exchange meta data
            $sql = "SELECT * FROM calendar_exchange_metadata WHERE activity_id = ?";
            $param = [$event];
            $meta = $db->pquery($sql, $param);

            // Grab the basic event information
            $sql = "SELECT * FROM vtiger_activity WHERE activityid = ?";
            $param = [$event];
            $info = $db->pquery($sql, $param);

            // Grab event info from vtiger_crmentity table
            $sql = "SELECT * FROM vtiger_crmentity WHERE crmid = ?";
            $param = [$event];
            $entityInfo = $db->pquery($sql, $param);

            // Grab reminder info from vtiger_activity_reminder if it exists
            $sql = "SELECT * FROM vtiger_activity_reminder WHERE activity_id = ?";
            $param = [$event];
            $reminderInfo = $db->pquery($sql, $param);
            $reminderIsSet = ($db->getRowCount($reminderInfo) == 0) ? false : true;
            $reminderMinutesBeforeStart = $reminderInfo->fields['reminder_time'];

            // Grab recurrence info if it exists
            $sql = "SELECT * FROM vtiger_recurringevents WHERE activityid = ?";
            $param = [$event];
            $recurrence = $db->pquery($sql, $param);
            $isRecurring = ($db->getRowCount($recurrence) == 0) ? false : true;

            // Grab user's timezone
            $sql = "SELECT time_zone FROM `vtiger_users` JOIN `vtiger_crmentity` ON `vtiger_crmentity`.`smownerid`=`vtiger_users`.`id` WHERE crmid=?";
            $userInfo = $db->pquery($sql, [$event]);
            $timeZone = $userInfo->fields['time_zone'];

            // Assign vars to populate LocalUpdate object
            // TODO: assign calendarItemType to deal with recurrences
            $activityId = $event;
            $eventId = $meta->fields['id'];
            $changeKey = $meta->fields['change_key'];
            $subject = $info->fields['subject'];
            $body = $entityInfo->fields['description'];
            $eventStatus = $info->fields['exchange_freebusy'];
            $calendarItemType = "Single";
            $startTime = $info->fields['date_start'] . " " . $info->fields['time_start'];
            $startTime = Carbon::createFromFormat('Y-m-d H:i:s', $startTime);
            $endTime = $info->fields['due_date'] . " " . ($info->fields['time_end'] ?: '23:59:59');
            $endTimeZone = $info->fields['time_end'] ? 'UTC' : $timeZone;
            $endTime = Carbon::createFromFormat('Y-m-d H:i:s', $endTime, $endTimeZone);
            $endTime->setTimezone('UTC');
            $location = null;

            $event = new LocalUpdate($activityId,
                $eventId,
                $changeKey,
                $subject,
                $body,
                $startTime,
                $endTime,
                $reminderIsSet,
                $reminderMinutesBeforeStart,
                $eventStatus,
                $location,
                $isRecurring,
                $calendarItemType);

            $events[] = $event;
        }

        // Handle updated local survey appointments
        foreach ($updatedSurveys as $event) {

            // Grab the exchange meta data
            $sql = "SELECT * FROM calendar_exchange_metadata WHERE activity_id = ?";
            $param = [$event];
            $meta = $db->pquery($sql, $param);

            // Grab the basic appointment information
            $sql = "SELECT * FROM vtiger_surveys WHERE surveysid = ?";
            $param = [$event];
            $info = $db->pquery($sql, $param);

            // Grab event info from vtiger_crmentity table
            $sql = "SELECT * FROM vtiger_crmentity WHERE crmid = ?";
            $param = [$event];
            $entityInfo = $db->pquery($sql, $param);

            $reminderIsSet = false;
            $reminderMinutesBeforeStart = 0;

            $isRecurring = false;

            // Assign vars to populate LocalUpdate object
            // TODO: assign calendarItemType to deal with recurrences

            $subject = self::generateSurveySubject($result);

            $startTime = $info->fields['survey_date']." ".$info->fields['survey_time'];
            $startTime = Carbon::createFromFormat('Y-m-d H:i:s', $startTime);
            $endTime = $info->fields['survey_date']." ".($info->fields['survey_end_time'] ?: '00:00:00');
            $endTime = Carbon::createFromFormat('Y-m-d H:i:s', $endTime);


            $activityId = $event;
            $eventId = $meta->fields['id'];
            $changeKey = $meta->fields['change_key'];
            $body = self::generateSurveyBody($info);
            $eventStatus = null;
            $location = self::generateSurveyLocation($info);
            $calendarItemType = "Single";

            $event = new LocalUpdate($activityId,
                                     $eventId,
                                     $changeKey,
                                     $subject,
                                     $body,
                                     $startTime,
                                     $endTime,
                                     $reminderIsSet,
                                     $reminderMinutesBeforeStart,
                                     $eventStatus,
                                     $location,
                                     $isRecurring,
                                     $calendarItemType);

            $events[] = $event;
        }

        return $events;
    }

    /**
     * Get the calendar events that have been
     * deleted locally since the last sync
     *
     * @return mixed
     */
    public static function getDeletes(Users_Record_Model $user)
    {
        $db = PearDatabase::getInstance();

        // Get all previously synchronized local calendar events
        $sql = "SELECT * FROM calendar_exchange_metadata WHERE userid=?";
        $syncedLocalEvents = $db->pquery($sql, [$user->getId()]);

        // Figure out which events have been deleted since the last sync
        foreach ($syncedLocalEvents as $event) {
            $sql = "SELECT modifiedtime FROM vtiger_crmentity
                    WHERE crmid = ?
                    AND deleted = 1";
            $param = [$event['activity_id']];
            $result = $db->pquery($sql, $param);

            if ($result->fields['modifiedtime']) {
                if ($result->fields['modifiedtime'] !== $event['last_sync_time']) {
                    $needsDeleted[$event['activity_id']]['eventId'] = $event['id'];
                    $needsDeleted[$event['activity_id']]['changeKey'] = $event['change_key'];
                }
            }
        }

        return $needsDeleted;
    }

    /**
     * Function to recursively iterate through a
     * multi-dimensional array and check in_array()
     *
     * @link http://stackoverflow.com/a/4128377/1440617
     *
     * @param $needle
     * @param $haystack
     * @param bool $strict
     *
     * @return bool
     */
    public static function in_array_r($needle, $haystack, $strict = false)
    {
        foreach ($haystack as $item) {
            if (
                ($strict ? $item === $needle : $item == $needle) ||
                (
                    is_array($item) &&
                    self::in_array_r($needle, $item, $strict)
                )
            ) {
                return true;
            }
        }
        return false;
    }

    private static function generateSurveyBody($result)
    {
        $address1   = $result->fields['address1'];
        $address2   = $result->fields['address2'];
        $city       = $result->fields['city'];
        $state      = $result->fields['state'];
        $zip        = $result->fields['zip'];
        $country    = $result->fields['country'];
        $phone1     = $result->fields['phone1'];
        $phone2     = $result->fields['phone2'];
        $comm_res   = $result->fields['comm_res'];
        $notes      = $result->fields['survey_notes'];

        $body = "";
        if ($result->fields['contact_id'] != 0) {
            $db = PearDatabase::getInstance();
            $sql = "SELECT firstname, lastname, email FROM `vtiger_contactdetails` WHERE contactid=?";
            $contactRes = $db->pquery($sql, [$result->fields['contact_id']]);

            $name  = $contactRes->fields['firstname'].' '.$contactRes->fields['lastname'];
            $email = $contactRes->fields['email'];
            $body .= "Contact: $name<br />Email: $email<br />";
        }

        $body .= "Address 1: $address1<br />Address 2: $address2<br />City: $city<br />State: $state<br />Postal Code: $zip<br />Country: $country<br />Phone 1: $phone1<br />Phone 2: $phone2<br />Location Type: $comm_res<br />Notes: $notes";
        return $body;
    }

    private static function generateSurveySubject($result)
    {
        if ($result->fields['contact_id'] == 0) {
            return "Survey Appointment";
        }
        $db = PearDatabase::getInstance();
        $sql = "SELECT firstname, lastname FROM `vtiger_contactdetails` WHERE contactid=?";
        $contactRes = $db->pquery($sql, [$result->fields['contact_id']]);

        return "Survey Appointment for ".$contactRes->fields['firstname']." ".$contactRes->fields['lastname'];
    }

    private static function generateSurveyLocation($result)
    {
        $location = '';
        $location .= $result->fields['address1'] . " ";
        if($result->fields['address2'] == '' || $result->fields['address2'] == NULL)
        {
            $location .= $result->fields['address2'] . " ";
        }
        $location .= $result->fields['city'] . ", ";
        $location .= $result->fields['state'] . " ";
        $location .= $result->fields['zip'] . " ";
        $location .= $result->fields['country'] . " ";

        return $location;
    }
}
