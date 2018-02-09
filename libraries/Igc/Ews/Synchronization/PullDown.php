<?php

namespace Igc\Ews\Synchronization;

use Html2Text\Html2Text;
use Html2Text\Html2TextException;
use Igc\Ews\Calendar\RecurringEvent;
use MoveCrm\Models\Calendar\Exchange\Metadata as ExchangeMetadata;
use PearDatabase;
use Carbon\Carbon;
use Exchange_List_View;
use PhpEws\DataType\DefaultShapeNamesType;
use PhpEws\DataType\DistinguishedPropertySetIdType;
use PhpEws\DataType\ItemIdType;
use PhpEws\DataType\ItemResponseShapeType;
use PhpEws\DataType\MapiPropertyTypeType;
use PhpEws\DataType\NonEmptyArrayOfBaseItemIdsType;
use PhpEws\DataType\NonEmptyArrayOfPathsToElementType;
use PhpEws\DataType\PathToExtendedFieldType;
use Users_Record_Model;
use MoveCrm\Models\Activity;
use MoveCrm\Models\Calendar\Exchange\Sync;
use MoveCrm\Models\User as CrmUser;
use Igc\Ews\Calendar as ExchangeCalendar;
use WebServiceException;

require_once 'include/Webservices/Create.php';
require_once 'modules/Users/Users.php';

class PullDown extends Exchange_List_View
{
    /**
     * This function will fetch remote
     * changes and persist them to the database
     *
     * @param Users_Record_Model $user
     * @return array
     */
    public static function remoteChanges(Users_Record_Model $user)
    {
        $crmUser = new CrmUser($user);
        $userId = $crmUser->id;
        file_put_contents('logs/devLog.log', "\n UserId : ".print_r($userId, true), FILE_APPEND);

        $db = PearDatabase::getInstance();

        // Instantiate an instance of the ews calendar
        $remoteCalendar = new ExchangeCalendar($user);
        $creates = 0;
        $updates = 0;
        $deletes = 0;

        do {
            //throw new \WebServiceException(500, var_export($remoteCalendar, true));
            #__halt_compiler();
            // If this is the first sync, this will be null, and we'll pull all remote events
            $localSyncState = self::getLocalSyncState($userId, $db);
            //throw new \WebServiceException(500, var_export($localSyncState, true));
            ##throw new WebServiceException(
            ##500,
            //var_export(Sync::where('state', 'abcdefg')->first(), true) // <-- <NULL>
            //var_export(Sync::where('state', $localSyncState)->count(), true)
            ##var_export(Sync::where('state', $localSyncState)->first(), true)
            ##);
            // Perform the synchronization call to the exchange server. If the sync_state on
            // both ends match, there were no remote changes, and nothing needs updated locally
            // > `ResponseMessages->SyncFolderItemResponseMessage`
            ###-### $syncResponse = $remoteCalendar->getSynchronization($localSyncState);
            $syncResponse = $remoteCalendar->syncFolderItems($localSyncState);
            #dump($syncResponse);
            #exit();
            //throw new \WebServiceException(500, var_export($syncResponse, true));
            // Get the current remote sync_state. We'll persist this locally
            // so we know which remote changes have already been acted upon locally
            //-//$sync_state = $syncResponse->ResponseMessages->SyncFolderItemsResponseMessage->SyncState;
            $sync_state = $syncResponse->SyncState;
            // If there are any remote changes (creates, updates, deletes), we'll have the details here
            //-//$changes = $syncResponse->ResponseMessages->SyncFolderItemsResponseMessage->Changes;
            $changes = $syncResponse->Changes;
            // Break out the changes into their various types and persist them
            $creates += self::persistRemoteCreates($changes, $userId, $db, $user);
            $updates += self::persistRemoteUpdates($changes, $userId, $db, $user);
            $deletes += self::persistRemoteDeletes($changes, $db);
            // Update last_sync_time or create the first entry
            if ($localSyncState == null) {
                self::recordFirstSyncTime($userId, $db);
            } else {
                self::updateLastSyncTime($userId, $db);
            }
            // Persist the current remote sync_state to the local DB. This will be
            // used in the next synchronization call to check for new remote changes
            $crmUser->persistSyncState($sync_state);
            $iterationRecordCount = 0;
            if (property_exists($changes, 'Create')) {
                $iterationRecordCount += count($changes->Create);
            }
            if (property_exists($changes, 'Update')) {
                $iterationRecordCount += count($changes->Update);
            }
            if (property_exists($changes, 'Delete')) {
                $iterationRecordCount += count($changes->Delete);
            }
        } while ($iterationRecordCount >= 512);
        file_put_contents('logs/devLog.log', "\n IterationRecordCount : ".print_r($iterationRecordCount, true), FILE_APPEND);
    //throw new \WebServiceException(500, var_export($localSyncState, true));
    ##throw new WebServiceException(
        ##500,
        //var_export(Sync::where('state', 'abcdefg')->first(), true) // <-- <NULL>
        //var_export(Sync::where('state', $localSyncState)->count(), true)
        ##var_export(Sync::where('state', $localSyncState)->first(), true)
    ##);

        // Get a count of each change persisted locally to show in the widget view
        $records = self::prepareChangesForWidgetView($creates, $updates, $deletes);

        // Just returns an array with counts of what changed
        return $records;
    }

    /**
     * Map remote details to vTiger field names and persist.
     * The response object is different depending on whether
     * there was a single event or multiple events. Awesome MS!!
     *
     * @param $changes
     * @param $userId
     * @param $db
     * @param Users_Record_Model $user
     * @return int
     */
    private static function persistRemoteCreates($changes, $userId, $db, Users_Record_Model $user)
    {
        $creates = 0;
        if (property_exists($changes, 'Create')) {
            if (count($changes->Create) > 1) {
                foreach ($changes->Create as $event) {

                    // map exchange details to vtiger calendar fields
                    $eventDetails = self::_mapMultipleEvents($userId, $event);

                    // Get the event's body, as this isn't fetched on initial sync call
                    $calendar = new ExchangeCalendar($user);
                    $eventBody = $calendar->getEventBody($eventDetails['exchange_id'], $eventDetails['exchange_change_key']);
                    $eventDetails['description'] = $eventBody;

                    // Get the event's isSurveyAppointment extended property
                    $isSurveyAppointment = $calendar->checkIfSurveyAppointment($eventDetails['exchange_id'], $eventDetails['exchange_change_key']);

                    /* try {
                        $eventDetails['description'] = Html2Text::convert($eventBody);
                    } catch (Html2TextException $e) {
                        $eventDetails['description'] = $eventBody;
                    } */

                    if ($isSurveyAppointment) {
                        continue;
                    }

                    // if it's already here, don't persist or count again
                    if (self::eventAlreadyExistsLocally($eventDetails, $db)) {
                        continue;
                    }

                    // TODO: Remove continue for recurring items
                    if (self::isThisRecurringMaster($eventDetails)) {
                        continue;
                    }

                    // actually save the single/master event locally
                    $masterParams = self::persistNewCalendarEvent($eventDetails, $db);

                    // If the new event is a recurring master, get the
                    // recurrence details and persist the recurrences locally
                    if (self::isThisRecurringMaster($eventDetails)) {
                        // instantiate a RecurringEvent object
                        $recurringEvent = new RecurringEvent($eventDetails, $user);
                        $recurrenceDetails = $recurringEvent->getRecurrenceDetails();
                        $recurringEvent->persistEventRecurrences($eventDetails, $recurrenceDetails, $masterParams, $db);
                    }
                    $creates++;
                }
            } else {
                foreach ($changes->Create as $event) {
                    $eventDetails = self::_mapSingleEvent($userId, $event);

                    // Get the event's body, as this isn't fetched on initial sync call
                    $calendar = new ExchangeCalendar($user);
                    $eventBody = $calendar->getEventBody($eventDetails['exchange_id'], $eventDetails['exchange_change_key']);
                    $eventDetails['description'] = $eventBody;

                    // Get the event's isSurveyAppointment extended property
                    $isSurveyAppointment = $calendar->checkIfSurveyAppointment($eventDetails['exchange_id'], $eventDetails['exchange_change_key']);

                    /* try {
                        $eventDetails['description'] = Html2Text::convert($eventBody);
                    } catch (Html2TextException $e) {
                        $eventDetails['description'] = $eventBody;
                    } */

                    if ($isSurveyAppointment) {
                        continue;
                    }

                    // if it's already here, don't persist or count again
                    if (self::eventAlreadyExistsLocally($eventDetails, $db)) {
                        continue;
                    }

                    // TODO: Remove continue for recurring items
                    if (self::isThisRecurringMaster($eventDetails)) {
                        continue;
                    }
                    // actually save the single/master event locally
                    $masterParams = self::persistNewCalendarEvent($eventDetails, $db);

                    // If the new event is a recurring master, get the
                    // recurrence details and persist the recurrences locally
                    if (self::isThisRecurringMaster($eventDetails)) {
                        // instantiate a RecurringEvent object
                        $recurringEvent = new RecurringEvent($eventDetails, $user);
                        $recurrenceDetails = $recurringEvent->getRecurrenceDetails();
                        $recurringEvent->persistEventRecurrences($eventDetails, $recurrenceDetails, $masterParams, $db);
                    }
                    $creates++;
                }
            }
        }

        return $creates;
    }

    /**
     * Map remote details to vTiger field names and persist.
     * The response object is different depending on whether
     * there was a single event or multiple events.
     *
     * @param $changes
     * @param $userId
     * @param $db
     * @return int
     */
    private static function persistRemoteUpdates($changes, $userId, $db, $user)
    {
        $updates = 0;
        if (property_exists($changes, 'Update')) {
            if (count($changes->Update) > 1) {
                foreach ($changes->Update as $event) {
                    $eventDetails = self::_mapMultipleEvents($userId, $event);

                    // Get the event's body, as this isn't fetched on initial sync call
                    $calendar = new ExchangeCalendar($user);
                    $eventBody = $calendar->getEventBody($eventDetails['exchange_id'], $eventDetails['exchange_change_key']);

                    try {
                        $eventDetails['description'] = Html2Text::convert($eventBody);
                    } catch (Html2TextException $e) {
                        $eventDetails['description'] = $eventBody;
                    }

                    // Get the event's isSurveyAppointment extended property
                    $isSurveyAppointment = $calendar->checkIfSurveyAppointment($eventDetails['exchange_id'], $eventDetails['exchange_change_key']);

                    // if it's already synced, don't persist or count again
                    if (self::eventAlreadyInSync($eventDetails, $db)) {
                        continue;
                    }

                    if ($isSurveyAppointment) {
                        self::updateSurveyAppointment($eventDetails, $db);
                        $updates++;
                        continue;
                    }

                    self::persistUpdatedCalendarEvent($eventDetails, $db);
                    $updates++;
                }
            } else {
                foreach ($changes->Update as $event) {
                    $eventDetails = self::_mapSingleEvent($userId, $event);

                    // Get the event's body, as this isn't fetched on initial sync call
                    $calendar = new ExchangeCalendar($user);
                    $eventBody = $calendar->getEventBody($eventDetails['exchange_id'], $eventDetails['exchange_change_key']);
                    $eventDetails['description'] = $eventBody;

                    /* try {
                        $eventDetails['description'] = Html2Text::convert($eventBody);
                    } catch (Html2TextException $e) {
                        $eventDetails['description'] = $eventBody;
                    } */

                    // Get the event's isSurveyAppointment extended property
                    $isSurveyAppointment = $calendar->checkIfSurveyAppointment($eventDetails['exchange_id'], $eventDetails['exchange_change_key']);

                    // if it's already here, don't persist or count again
                    if (self::eventAlreadyInSync($eventDetails, $db)) {
                        continue;
                    }

                    if ($isSurveyAppointment) {
                        self::updateSurveyAppointment($eventDetails, $db);
                        $updates++;
                        continue;
                    }

                    self::persistUpdatedCalendarEvent($eventDetails, $db);
                    $updates++;
                }
            }
        }

        return $updates;
    }

    /**
     * Map remote details to vTiger field names and persist
     * The response object is different depending on whether
     * there was a single event of multiple events.
     *
     * @param $changes
     * @param $db
     * @return int
     */
    private static function persistRemoteDeletes($changes, $db)
    {
        $deletes = 0;
        if (property_exists($changes, 'Delete')) {
            if (count($changes->Delete) > 1) {
                foreach ($changes->Delete as $event) {
                    $eventId = $event->ItemId->Id;
                    if (self::eventAlreadyDeletedLocally($eventId, $db)) {
                        continue;
                    }
                    self::persistDeletedCalendarEvent($eventId, $db);
                    $deletes++;
                }
            } else {
                foreach ($changes->Delete as $event) {
                    $eventId = $event->Id;
                    if (self::eventAlreadyDeletedLocally($eventId, $db)) {
                        continue;
                    }
                    self::persistDeletedCalendarEvent($eventId, $db);
                    $deletes++;
                }
            }
        }

        return $deletes;
    }

    /**
     * Create new local Calendar event because a new one was created remotely
     *
     * @param $eventDetails
     * @param $db
     * @return array
     * @throws WebServiceException
     *
     */
    private static function persistNewCalendarEvent($eventDetails, $db)
    {
        ###dump($eventDetails);
        ###exit;

        $userId = $GLOBALS['current_user_id'];
        if (empty($userId)) {
            $userId = Users_Record_Model::getCurrentUserModel()->getId();
        }

        // Get user's timezone
        $sql = "SELECT time_zone FROM `vtiger_users` WHERE id=?";
        $userInfo = $db->pquery($sql, [$userId]);
        $timeZone = $userInfo->fields['time_zone'];
        $user = new \Users();
        $current_user = $user->retrieveCurrentUserInfoFromFile(\Users::getActiveAdminId());

        if ($eventDetails['is_all_day'] == 'true') {
            // Make sure due_date is correct for all-day tasks
            $endTime = $eventDetails['due_date'] . " " . $eventDetails['time_end'];
            $endTime = Carbon::createFromFormat('Y-m-d H:i:s', $endTime);
            $endTime->subSecond();
            $endTime->setTimezone($timeZone);
            $eventDetails['due_date'] = $endTime->toDateString();
            $eventDetails['time_end'] = '';
            // Create new To Do record
            $retval = vtws_create('Calendar', $eventDetails, $current_user);
        } else {
            // Create new local calendar event
            $retval = vtws_create('Events', $eventDetails, $current_user);
            ##### dump($retval);
        }

        // Insert the local Exchange related metadata.
        $created_time = $retval['createdtime'];
        $activityId = explode('x', $retval['id'])[1];

        $params     = [
            $eventDetails['exchange_id'],
            $activityId,
            $eventDetails['exchange_change_key'],
            $created_time,
            $userId
        ];
        $sql = "INSERT INTO calendar_exchange_metadata (id, activity_id, change_key, last_sync_time, userid)
                VALUES (?, ?, ?, ?, ?)";
        $db->pquery($sql, $params);

        // Update vtiger_activity_reminder info if it exists. This is an
        // update and not an insert because the vtws_create is creating this
        // entry first, so the INSERT fails because of the existing primary key
        // if ($eventDetails['reminder_set']) {
        //     $sql = "UPDATE vtiger_activity_reminder SET reminder_time = ? WHERE activity_id = ?";
        //     $params = [$eventDetails['reminder_time'], $activityId];
        //     $result = $db->pquery($sql, $params);
        //     if (!$result) {
        //         //
        //     }
        // }

        return $params;
    }

    /**
     * Update local event because of update to remote event
     *
     * @param $eventDetails
     * @param $db
     *
     * TODO: Deal with pulling down an "all-day" event. vtiger does not have this
     */
    private static function persistUpdatedCalendarEvent($eventDetails, $db)
    {
        // Grab the activityid from the meta table
        $activityId = self::getActivityIdFromMetaTable($eventDetails, $db);

        // save to variable so we're sure it's the same when it's used twice below
        $now = Carbon::now()->toDateTimeString();

        // update Calendar (vtiger_activity) table
        $params = [
            $eventDetails['subject'],
            $eventDetails['activitytype'],
            $eventDetails['date_start'],
            $eventDetails['due_date'],
            $eventDetails['time_start'],
            $eventDetails['time_end'],
            $eventDetails['duration_hours'],
            $eventDetails['duration_minutes'],
            $eventDetails['eventstatus'],
            $eventDetails['location'],
            $activityId
        ];

        $sql = "UPDATE vtiger_activity SET
                subject = ?,
                activitytype = ?,
                date_start = ?,
                due_date = ?,
                time_start = ?,
                time_end = ?,
                duration_hours = ?,
                duration_minutes = ?,
                eventstatus = ?,
                location = ?
                WHERE vtiger_activity.activityid = ?";

        $resultSet = $db->pquery($sql, $params);

        if (!$resultSet) {
        }

        // update calendar_exchange_metadata table
        $sql = "UPDATE calendar_exchange_metadata SET
                change_key = ?,
                last_sync_time = ?
                WHERE activity_id = ?";
        $params = [$eventDetails['exchange_change_key'], $now, $activityId];
        $resultSet = $db->pquery($sql, $params);

        if (!$resultSet) {
        }

        // update vtiger_crmentity
        $sql = "UPDATE vtiger_crmentity SET
                modifiedtime = ?,
                label = ?,
                description = ?
                WHERE crmid = ?";
        $params = [
            $now,
            $eventDetails['subject'],
            $eventDetails['description'],
            $activityId
        ];
        $resultSet = $db->pquery($sql, $params);
        if (!$resultSet) {
        }

        // update vtiger_activity_reminder info if it exists
        if ($eventDetails['reminder_set']) {
            $sql = "UPDATE vtiger_activity_reminder SET reminder_time = ? WHERE activity_id = ?";
            $params = [$eventDetails['reminder_time'], $activityId];
            $result = $db->pquery($sql, $params);
            if (!$result) {
                //
            }
        }
    }

    /**
     * Delete local event because of deletion of remote event
     *
     * @param $eventId
     * @param $db
     */
    private static function persistDeletedCalendarEvent($eventId, $db)
    {
        $userId = $GLOBALS['current_user_id'];
        if (empty($userId)) {
            $userId = Users_Record_Model::getCurrentUserModel()->getId();
        }

        // save to variable so we're sure it's the same when it's used twice below
        $now = Carbon::now()->toDateTimeString();

        $sql = "SELECT activity_id FROM calendar_exchange_metadata WHERE BINARY id = ? AND userid=?";
        $params = [$eventId, $userId];
        $resultSet = $db->pquery($sql, $params);
        if (!$resultSet) {
        }
        $activityId = $resultSet->fields['activity_id'];

        // "soft" delete by setting the deleted flag
        $sql = "UPDATE vtiger_crmentity
                SET deleted = 1,
                 modifiedtime = ?
                 WHERE crmid = ?";
        $params = [
            $now,
            $activityId
        ];
        $resultSet = $db->pquery($sql, $params);
        if (!$resultSet) {
        }

        // time stamp last sync on the metadata table
        $sql = "UPDATE calendar_exchange_metadata
                SET last_sync_time = ?
                WHERE activity_id = ?";
        $params = [$now, $activityId];
        $resultSet = $db->pquery($sql, $params);
        if (!$resultSet) {
        }
    }

    /**
     * Map response values to DB columns when there are more than one returned
     *
     * @param $userId
     * @param $event
     * @return array
     */
    private static function _mapMultipleEvents($userId, $event)
    {
        $subject = ($event->CalendarItem->Subject == '') ? 'Unnamed Exchange Event' : $event->CalendarItem->Subject;
        $eventDetails = [
            'subject' => $subject,
            'date_start' => self::_mapDate($event->CalendarItem->Start),
            'time_start' => self::_mapTime($event->CalendarItem->Start),
            'due_date' => self::_mapDate($event->CalendarItem->End),
            'time_end' => self::_mapTime($event->CalendarItem->End),
            'duration_hours' => self::_mapDurationHours($event->CalendarItem->Start, $event->CalendarItem->End),
            'duration_minutes' => self::_mapDurationMinutes($event->CalendarItem->Start, $event->CalendarItem->End),
            'exchange_freebusy' => $event->CalendarItem->LegacyFreeBusyStatus,
            'location' => $event->CalendarItem->Location,
            'exchange_id' => $event->CalendarItem->ItemId->Id,
            'exchange_change_key' => $event->CalendarItem->ItemId->ChangeKey,
            'reminder_set' => $event->CalendarItem->ReminderIsSet,
            'reminder_time' => $event->CalendarItem->ReminderMinutesBeforeStart,
            'calendar_item_type' => $event->CalendarItem->CalendarItemType,
            'assigned_user_id' => '19x'.$userId,
            'is_all_day' => $event->CalendarItem->IsAllDayEvent,
        ];
        if ($eventDetails['is_all_day'] == 'true') {
            $eventDetails['taskstatus'] = 'Not Started';
            $eventDetails['activitytype'] = 'Task';
            $eventDetails['visibility'] = 'Private';
        } else {
            $eventDetails['eventstatus'] = 'Planned';
            $eventDetails['activitytype'] = 'Meeting';
            $eventDetails['visibility'] = 'Public';
        }

        return $eventDetails;
    }

    /**
     * Map response values to DB columns when only one is returned
     *
     * @param $userId
     * @param $event
     * @return array
     */
    private static function _mapSingleEvent($userId, $event)
    {
        $subject = ($event->Subject == '') ? 'Unnamed Exchange Event' : $event->Subject;
        $eventDetails = [
            'subject' => $subject,
            'date_start' => self::_mapDate($event->Start),
            'time_start' => self::_mapTime($event->Start),
            'due_date' => self::_mapDate($event->End),
            'time_end' => self::_mapTime($event->End),
            'duration_hours' => self::_mapDurationHours($event->Start, $event->End),
            'duration_minutes' => self::_mapDurationMinutes($event->Start, $event->End),
            'exchange_freebusy' => $event->LegacyFreeBusyStatus,
            'location' => $event->Location,
            'exchange_id' => $event->ItemId->Id,
            'exchange_change_key' => $event->ItemId->ChangeKey,
            'reminder_set' => $event->ReminderIsSet,
            'reminder_time' => $event->ReminderMinutesBeforeStart,
            'calendar_item_type' => $event->CalendarItemType,
            'assigned_user_id' => '19x'.$userId,
            'is_all_day' => $event->IsAllDayEvent
        ];
        if ($eventDetails['is_all_day'] == 'true') {
            $eventDetails['taskstatus'] = 'Not Started';
            $eventDetails['activitytype'] = 'Task';
            $eventDetails['visibility'] = 'Private';
        } else {
            $eventDetails['eventstatus'] = 'Planned';
            $eventDetails['activitytype'] = 'Meeting';
            $eventDetails['visibility'] = 'Public';
        }

        return $eventDetails;
    }

    /**
     * Grab the id for the calendar event.
     * this is used across multiple tables.
     *
     * calendar_exchange_metadata   => activity_id
     * vtiger_activity              => activityid
     * vtiger_crmentity             => crmid
     *
     * @param $eventDetails
     * @param $db
     *
     * @return string
     */
    private static function getActivityIdFromMetaTable($eventDetails, $db)
    {
        $userId = $GLOBALS['current_user_id'];
        if (empty($userId)) {
            $userId = Users_Record_Model::getCurrentUserModel()->getId();
        }
        $param = [$eventDetails['exchange_id'], $userId];
        $sql = "SELECT * FROM calendar_exchange_metadata WHERE BINARY id = ? AND userid=?";
        $metaData = $db->pquery($sql, $param);
        if (!$metaData) {
            //
        }
        $activityId = $metaData->fields['activity_id'];
        return $activityId;
    }

    /**
     * Retrieve sync_state value from calendar_exchange_sync table
     *
     * @param $userId
     * @param $db
     * @return string
     */
    protected static function getLocalSyncState($userId, $db)
    {
        $param = [$userId];
        $sql = "SELECT state FROM calendar_exchange_sync WHERE user_id = ?";

        $result = $db->pquery($sql, $param);

        if ($result->_numOfRows == 0) {
            return null;
        }

        return $result->fields['state'];
    }

    /**
     * Map an Exchange date to a moveCRM compatible format.
     *
     * @param  string $dateTime
     *
     * @return string|bool
     */
    private static function _mapDate($dateTime)
    {
        return date('Y-m-d', strtotime($dateTime));
    }

    /**
     * Map the Exchange start/end times to a duration in hours (minus minutes).
     *
     * @param  string $start
     * @param  string $end
     *
     * @return string
     */
    private static function _mapDurationHours($start, $end)
    {
        return sprintf('%u', date('H', (strtotime($end) - strtotime($start))));
    }

    /**
     * Map the Exchange start/end times to a duration in minutes (minus hours).
     *
     * @param  string $start
     * @param  string $end
     *
     * @return string
     */
    private static function _mapDurationMinutes($start, $end)
    {
        return sprintf('%u', date('i', (strtotime($end) - strtotime($start))));
    }

    /**
     * Map an Exchange time to a vTiger compatible format.
     *
     * @param  string $dateTime
     *
     * @return string|bool
     */
    private static function _mapTime($dateTime)
    {
        return date('H:i:s', strtotime($dateTime));
    }

    /**
     * Update the last_sync_time when this is not the first sync
     *
     * @param $userId
     * @param $db
     */
    private static function updateLastSyncTime($userId, $db)
    {
        $now = Carbon::now()->toDateTimeString();
        $sql = "UPDATE calendar_exchange_sync SET last_sync_time = ? WHERE user_id = ?";
        $params = [$now, $userId];

        $db->pquery($sql, $params);
    }

    /**
     * Create the last_sync_time when this is the first sync
     *
     * @param $userId
     * @param $db
     */
    private static function recordFirstSyncTime($userId, $db)
    {
        $now = Carbon::now()->toDateTimeString();
        $sql = "INSERT INTO calendar_exchange_sync (user_id, last_sync_time) VALUES (?,?)";
        $params = [$userId, $now];

        $db->pquery($sql, $params);
    }

    /**
     * Set up the array that will be parsed in the ContentDetails.tpl widget view
     *
     * @param $creates
     * @param $updates
     * @param $deletes
     * @return array
     */
    private static function prepareChangesForWidgetView($creates, $updates, $deletes)
    {
        $countRecords = [
            'vtiger' => [
                'update' => 0,
                'create' => 0,
                'delete' => 0
            ],
            'exchange' => [
                'update' => 0,
                'create' => 0,
                'delete' => 0
            ]
        ];

        $countRecords['vtiger']['create'] = $creates;
        $countRecords['vtiger']['update'] = $updates;
        $countRecords['vtiger']['delete'] = $deletes;

        return $countRecords;
    }

    /**
     * Check if the event already exists locally. This way we
     * won't increment the counts for things that already exist.
     *
     * @param $eventDetails
     * @param $db
     * @return bool
     */
    private static function eventAlreadyExistsLocally($eventDetails, $db)
    {
        $userId = $GLOBALS['current_user_id'];
        if (empty($userId)) {
            $userId = Users_Record_Model::getCurrentUserModel()->getId();
        }
        /* if ((new Activity($eventDetails))->exists()) {
            file_put_contents('_event-exists.log', json_encode($eventDetails) . "\n\n", \FILE_APPEND);

            $metadata = ExchangeMetadata::find($eventDetails['exchange_id']);

            if ($metadata) {
                file_put_contents('_exchange-metadata-exists.log', json_encode($eventDetails) . "\n\n", \FILE_APPEND);
                $metadata->delete();
            }

            return true;
        } */

        $sql = "SELECT * FROM calendar_exchange_metadata WHERE BINARY id = ? AND userid=?";
        $param = [$eventDetails['exchange_id'], $userId];
        $result = $db->pquery($sql, $param);
        $row = $result->fetchRow();
        return $row == null ? false : true;

//        return (!$result->fields) ? false : true;
    }

    private static function eventAlreadyInSync($eventDetails, $db)
    {
        $userId = $GLOBALS['current_user_id'];
        if (empty($userId)) {
            $userId = Users_Record_Model::getCurrentUserModel()->getId();
        }
        $sql = "SELECT * FROM calendar_exchange_metadata WHERE BINARY id = ? AND userid=?";
        $param = [$eventDetails['exchange_id'], $userId];
        $res = $db->pquery($sql, $param);
        $localChangeKey = $res->fields['change_key'];
        $remoteChangeKey = $eventDetails['exchange_change_key'];

        return ($localChangeKey == $remoteChangeKey) ? true : false;
    }

    /**
     * Check if the event was already deleted locally. This way we
     * won't increment the counts for things that were already nixed.
     *
     * @param $eventId
     * @param $db
     * @return bool
     */
    private static function eventAlreadyDeletedLocally($eventId, $db)
    {
        $userId = $GLOBALS['current_user_id'];
        if (empty($userId)) {
            $userId = Users_Record_Model::getCurrentUserModel()->getId();
        }
        $sql = "SELECT activity_id FROM calendar_exchange_metadata WHERE BINARY id = ? AND userid=?";
        $param = [$eventId, $userId];
        $crmid = $db->pquery($sql, $param);
        $crmid = $crmid->fields['activity_id'];

        $sql = "SELECT deleted FROM vtiger_crmentity WHERE crmid = $crmid";
        $result = $db->query($sql);
        if ($result->fields['deleted'] == 1) {
            return true;
        }
        return false;
    }

    private static function isThisRecurringMaster($eventDetails)
    {
        if ($eventDetails['calendar_item_type'] == 'RecurringMaster') {
            return true;
        }

        return false;
    }

    private static function updateSurveyAppointment($eventDetails, $db)
    {
        $activityId = self::getActivityIdFromMetaTable($eventDetails, $db);
        // Only update datetime elements
        $sql = "UPDATE `vtiger_surveys` SET survey_date=?, survey_time=?, survey_end_time=? WHERE surveysid=?";
        $db->pquery($sql, [$eventDetails['date_start'], $eventDetails['time_start'], $eventDetails['time_end'], $activityId]);
    }
}
