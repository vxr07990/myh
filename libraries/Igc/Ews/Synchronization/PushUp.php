<?php

namespace Igc\Ews\Synchronization;

use PearDatabase;
use PhpEws\DataType\DistinguishedPropertySetIdType;
use PhpEws\DataType\MapiPropertyTypeType;
use PhpEws\DataType\PathToExtendedFieldType;
use Users_Record_Model;
use Calendar_Record_Model;
use Igc\Ews\Calendar\Event;
use PhpEws\DataType\BodyType;
use PhpEws\DataType\ItemIdType;
use Igc\Ews\Calendar\LocalEvent;
use Igc\Ews\Calendar\LocalUpdate;
use PhpEws\DataType\DisposalType;
use PhpEws\DataType\BodyTypeType;
use PhpEws\DataType\ItemClassType;
use PhpEws\DataType\ItemChangeType;
use PhpEws\DataType\CreateItemType;
use PhpEws\DataType\DeleteItemType;
use PhpEws\DataType\UpdateItemType;
use PhpEws\DataType\CalendarItemType;
use PhpEws\DataType\SetItemFieldType;
use Igc\Ews\Calendar as ExchangeCalendar;
use PhpEws\DataType\ImportanceChoicesType;
use PhpEws\DataType\SensitivityChoicesType;
use PhpEws\DataType\PathToUnindexedFieldType;
use PhpEws\DataType\NonEmptyArrayOfAllItemsType;
use PhpEws\DataType\NonEmptyArrayOfBaseItemIdsType;
use PhpEws\DataType\CalendarItemCreateOrDeleteOperationType;
use PhpEws\DataType\ExtendedPropertyType;
use PhpEws\DataType\TimeZoneType;

class PushUp extends Event
{
    /**
     * Push new local events up to exchange server
     *
     * @param Users_Record_Model $user
     * @param LocalEvent $event
     * @return object|\PhpEws\DataType\ResponseMessageType
     *
     * php-ews Wiki page
     * @link https://github.com/jamesiarmes/php-ews/wiki/Calendar:-Create-Event
     *
     * TODO: Make recurrence work correctly instead of ignoring recurring events
     */
    public static function newLocalEvent(Users_Record_Model $user, LocalEvent $event)
    {
        $db = PearDatabase::getInstance();
        if($event->activityId == NULL) {
          return null;
        }

        //@NOTE: This is blocking push up of recurring events.
        if (!$event->isSurveyAppointment) {
            $recordModel = \Vtiger_Record_Model::getInstanceById($event->activityId,'Calendar');
            if ($recordModel->isOccurrence()) {
                return null;
            }
            $sql    = "SELECT * FROM `vtiger_recurringevents` WHERE activityid=?";
            $result = $db->pquery($sql, [$event->activityId]);
            if ($db->num_rows($result) > 0) {
                // Event is master of recurring series. Ignore for now
                return null;
            }
        }

        $remoteCalendar = new ExchangeCalendar($user);

        // Start building the request.
        $request = new CreateItemType();
        $request->Items = new NonEmptyArrayOfAllItemsType();
        $request->Items->CalendarItem = new CalendarItemType();

        // Set the subject.
        $request->Items->CalendarItem->Subject = $event->subject;

        // Set the start and end times. For Exchange 2007, you need to include the timezone
        // offset. For Exchange 2010, you should set the StartTimeZone and EndTimeZone
        // properties. If we assume 2007 (we are) this method seems to work every time.
        $request->Items->CalendarItem->Start = $event->startTime->toAtomString();
        $request->Items->CalendarItem->End = $event->endTime->toAtomString();

        // Set reminders
        $request->Items->CalendarItem->ReminderIsSet = $event->setReminder;

        // Set location maybe?
        if($event->isSurveyAppointment){
            $request->Items->CalendarItem->Location = $event->Location;
        }

        // Specify when reminder is displayed (if this is not set, the default is 15 minutes)
        $request->Items->CalendarItem->ReminderMinutesBeforeStart = $event->reminderTimeBefore;

        //Add IsAllDayEvent if record is saved as a To Do
        if (!$event->isSurveyAppointment && $recordModel->getType() == 'Calendar') {
            $request->Items->CalendarItem->IsAllDayEvent = 'true';

            $tzInfo = self::getTimezone();
            $offsetHours = abs($tzInfo[1]) / 3600;
            $remainingSeconds = abs($tzInfo[1]) % 3600;
            $offsetMinutes = $remainingSeconds / 60;
            $offset = ($tzInfo[1] > 0) ? '-PT' : 'PT';
            $offset .= $offsetHours . 'H' . $offsetMinutes . 'M';

            $request->Items->CalendarItem->MeetingTimeZone = new TimeZoneType();
            $request->Items->CalendarItem->MeetingTimeZone->TimeZoneName = $tzInfo[0];
            $request->Items->CalendarItem->MeetingTimeZone->BaseOffset = $offset;
            $GLOBALS['current_event_all_day'] = true;
        } else {
            $GLOBALS['current_event_all_day'] = false;
        }

        // Build the body.
        $request->Items->CalendarItem->Body = new BodyType();
        $request->Items->CalendarItem->Body->BodyType = BodyTypeType::HTML;
        $request->Items->CalendarItem->Body->_ = $event->body;

        // Set the item class type (not required).
        $request->Items->CalendarItem->ItemClass = new ItemClassType();
        $request->Items->CalendarItem->ItemClass->_ = ItemClassType::APPOINTMENT;

        // Set the sensitivity of the event (defaults to normal).
        $request->Items->CalendarItem->Sensitivity = new SensitivityChoicesType();
        $request->Items->CalendarItem->Sensitivity->_ = SensitivityChoicesType::NORMAL;

        // Set the importance of the event.
        $request->Items->CalendarItem->Importance = new ImportanceChoicesType();
        $request->Items->CalendarItem->Importance->_ = ImportanceChoicesType::NORMAL;

        // Don't send meeting invitations.
        $request->SendMeetingInvitations = CalendarItemCreateOrDeleteOperationType::SEND_TO_NONE;

        // Add extended property to indicate if record is a survey appointment
        $extendedProperty = new PathToExtendedFieldType();
        $extendedProperty->PropertyName = "isSurveyAppointment";
        $extendedProperty->PropertyType = MapiPropertyTypeType::BOOLEAN;
        $extendedProperty->DistinguishedPropertySetId = DistinguishedPropertySetIdType::PUBLIC_STRINGS;
        $request->Items->CalendarItem->ExtendedProperty = new ExtendedPropertyType();
        $request->Items->CalendarItem->ExtendedProperty->ExtendedFieldURI = $extendedProperty;
        $request->Items->CalendarItem->ExtendedProperty->Value = $event->isSurveyAppointment;

        // Send the create request to exchange.
        $response = $remoteCalendar->create($request);

        // Let's see what exchange has to say about our request...
        $message = $response->ResponseMessages->CreateItemResponseMessage;

        // Wrap up the response message
        $response = self::buildCalendarResponseObject($message);

        // Create an entry on the calendar_exchange_metadata table
        $sql = "SELECT modifiedtime FROM vtiger_crmentity WHERE crmid = $event->activityId";
        $modTime = $db->query($sql);
        $changeKey = $response->event->changeKey;
        $id = $response->event->id;

        $params =[$id, $event->activityId, $changeKey, $modTime->fields['modifiedtime'], $user->getId(), $event->isSurveyAppointment];
        $sql = "INSERT INTO calendar_exchange_metadata (id, activity_id, change_key, last_sync_time, userid, is_survey_appointment) VALUES (?,?,?,?,?,?)";
        $db->pquery($sql, $params);

        return $response;
    }

    /**
     * Push updated local events up to exchange server
     *
     * @param Users_Record_Model $user
     * @param LocalUpdate $event
     * @return object|\PhpEws\UpdateItemResponseType
     *
     * Wiki page for the php-ews Calendar Update Event
     * @link https://github.com/jamesiarmes/php-ews/wiki/Calendar:-Update-Event
     *
     * The following is the page that lists all the FieldURI Values
     * @link https://msdn.microsoft.com/en-us/library/office/aa494315(v=exchg.150).aspx
     */
    public static function updatedLocalEvent(Users_Record_Model $user, LocalUpdate $event)
    {
        $db = PearDatabase::getInstance();

        $remoteCalendar = new ExchangeCalendar($user);

        $request = new UpdateItemType();
        $request->ConflictResolution = 'AlwaysOverwrite';
        $request->SendMeetingInvitationsOrCancellations = 'SendOnlyToAll';
        $request->ItemChanges = array();

        $change = new ItemChangeType();
        $change->ItemId = new ItemIdType();
        $change->ItemId->Id = $event->eventId;
        $change->ItemId->ChangeKey = $event->changeKey;

        // Update Subject Property
        $field = new SetItemFieldType();
        $field->FieldURI = new PathToUnindexedFieldType();
        $field->FieldURI->FieldURI = 'item:Subject';
        $field->CalendarItem = new CalendarItemType();
        $field->CalendarItem->Subject = $event->subject;
        $change->Updates->SetItemField[] = $field;

        // Update Start Property
        $field = new SetItemFieldType();
        $field->FieldURI = new PathToUnindexedFieldType();
        $field->FieldURI->FieldURI = 'calendar:Start';
        $field->CalendarItem = new CalendarItemType();
        $field->CalendarItem->Start = $event->startTime->toAtomString();
        $change->Updates->SetItemField[] = $field;

        // Update End Property
        $field = new SetItemFieldType();
        $field->FieldURI = new PathToUnindexedFieldType();
        $field->FieldURI->FieldURI = 'calendar:End';
        $field->CalendarItem = new CalendarItemType();
        $field->CalendarItem->End = $event->endTime->toAtomString();
        $change->Updates->SetItemField[] = $field;

        // Update Body Property
        $field = new SetItemFieldType();
        $field->FieldURI = new PathToUnindexedFieldType();
        $field->FieldURI->FieldURI = 'item:Body';
        $field->CalendarItem = new CalendarItemType();
        $field->CalendarItem->Body = new BodyType();
        $field->CalendarItem->Body->BodyType = BodyTypeType::HTML;
        $field->CalendarItem->Body->_ = $event->body;
        $change->Updates->SetItemField[] = $field;

        // Update ReminderIsSet Property
        $field = new SetItemFieldType();
        $field->FieldURI = new PathToUnindexedFieldType();
        $field->FieldURI->FieldURI = 'item:ReminderIsSet';
        $field->CalendarItem = new CalendarItemType();
        $field->CalendarItem->ReminderIsSet = $event->reminderIsSet;
        $change->Updates->SetItemField[] = $field;

        if ($event->reminderIsSet === true) {
            // Update ReminderMinutesBeforeStart Property
            $field                                           = new SetItemFieldType();
            $field->FieldURI                                 = new PathToUnindexedFieldType();
            $field->FieldURI->FieldURI                       = 'item:ReminderMinutesBeforeStart';
            $field->CalendarItem                             = new CalendarItemType();
            $field->CalendarItem->ReminderMinutesBeforeStart = $event->reminderMinutesBeforeStart;
            $change->Updates->SetItemField[]                 = $field;
        }

        // Update LegacyFreeBusyStatus Property
        if ($event->eventStatus != null) {
            $field                                     = new SetItemFieldType();
            $field->FieldURI                           = new PathToUnindexedFieldType();
            $field->FieldURI->FieldURI                 = 'calendar:LegacyFreeBusyStatus';
            $field->CalendarItem                       = new CalendarItemType();
            $field->CalendarItem->LegacyFreeBusyStatus = $event->eventStatus;
            $change->Updates->SetItemField[]           = $field;
        }

        // Update Location Property
        if ($event->location != null) {
            $field                           = new SetItemFieldType();
            $field->FieldURI                 = new PathToUnindexedFieldType();
            $field->FieldURI->FieldURI       = 'calendar:Location';
            $field->CalendarItem             = new CalendarItemType();
            $field->CalendarItem->Location   = $event->location;
            $change->Updates->SetItemField[] = $field;
        }
//
//        // Update IsRecurring Property
//        $field = new SetItemFieldType();
//        $field->FieldURI = new PathToUnindexedFieldType();
//        $field->FieldURI->FieldURI = 'calendar:IsRecurring';
//        $field->CalendarItem = new CalendarItemType();
//        $field->CalendarItem->IsRecurring = $event->isRecurring;
//        $change->Updates->SetItemField[] = $field;

        // Update CalendarItemType Property
//        $field = new SetItemFieldType();
//        $field->FieldURI = new PathToUnindexedFieldType();
//        $field->FieldURI->FieldURI = 'calendar:CalendarItemType';
//        $field->CalendarItem = new CalendarItemType();
//        $field->CalendarItem->CalendarItemType = $event->calendarItemType;
//        $change->Updates->SetItemField[] = $field;

        $request->ItemChanges[] = $change;

        // Send the update request to exchange.
        $response = $remoteCalendar->update($request);

        // Let's see what exchange has to say about our request...
        $message = $response->ResponseMessages->UpdateItemResponseMessage;

        // Wrap up the response message
        $response = self::buildCalendarResponseObject($message);

        // Update the entry on the calendar_exchange_metadata table
        $sql = "SELECT modifiedtime FROM vtiger_crmentity WHERE crmid = $event->activityId";
        $modTime = $db->query($sql);
        $changeKey = $response->event->changeKey;

        $params =[$changeKey, $modTime->fields['modifiedtime'], $event->activityId];
        $sql = "UPDATE calendar_exchange_metadata SET
              change_key = ?,
              last_sync_time = ?
              WHERE activity_id = ?";
        $db->pquery($sql, $params);

        return $response;
    }

    /**
     * Push a deletion to the exchange server
     *
     * @param Users_Record_Model $user
     * @param $event
     * @return object|\PhpEws\DataType\ResponseMessageType
     *
     * @link https://github.com/jamesiarmes/php-ews/wiki/Calendar-and-Contact:-Delete
     */
    public static function deletedLocalEvent(Users_Record_Model $user, $event)
    {
        $db = PearDatabase::getInstance();

        $remoteCalendar = new ExchangeCalendar($user);

        // Define the delete item class
        $request = new DeleteItemType();
        // Send to trash can, or use EWSType_DisposalType::HARD_DELETE instead to bypass the bin directly
        $request->DeleteType = DisposalType::MOVE_TO_DELETED_ITEMS;
        // Inform no one who shares the item that it has been deleted
        $request->SendMeetingCancellations = CalendarItemCreateOrDeleteOperationType::SEND_TO_NONE;

        // Set the item to be deleted
        $item = new ItemIdType();
        $item->Id = $event['eventId'];
        $item->ChangeKey = $event['changeKey'];

        // We can use this to mass delete but in this case it's just one item
        $items = new NonEmptyArrayOfBaseItemIdsType();
        $items->ItemId = $item;
        $request->ItemIds = $items;

        // Send the request
        $response = $remoteCalendar->delete($request);

        // Let's see what exchange has to say about our request...
        $message = $response->ResponseMessages->DeleteItemResponseMessage;

        // Wrap up the response message
        $response = self::buildCalendarResponseObject($message);

        // Update the entry on the calendar_exchange_metadata table
        $sql = "SELECT activity_id FROM calendar_exchange_metadata WHERE BINARY id = ? AND userid=?";
        $param = [$item->Id, $user->getId()];
        $result = $db->pquery($sql, $param);
        $crmid = $result->fields['activity_id'];

        $sql = "SELECT modifiedtime FROM vtiger_crmentity WHERE crmid = ?";
        $param = [$crmid];
        $modTime = $db->pquery($sql, $param);
        $modTime = $modTime->fields['modifiedtime'];

        $params =[$modTime, $crmid];
        $sql = "UPDATE calendar_exchange_metadata SET last_sync_time = ?
                WHERE activity_id = ?";
        $db->pquery($sql, $params);

        return $response;
    }

    /**
     * Wrap up the response is a tasty little object
     *
     * @param $message
     * @return object
     */
    private static function buildCalendarResponseObject($message)
    {
        $response = (object)[
            'code' => $message->ResponseCode,
            'class' => $message->ResponseClass,
            'event' => (object)[
                'id' => $message->Items->CalendarItem->ItemId->Id,
                'changeKey' => $message->Items->CalendarItem->ItemId->ChangeKey
            ]
        ];

        return $response;
    }

    protected static function getTimezone()
    {
        $userId        = $GLOBALS['current_user_id'];
        if (empty($userId)) {
            $userId = Users_Record_Model::getCurrentUserModel()->getId();
        }
        $db = \PearDatabase::getInstance();
        $sql = "SELECT time_zone FROM `vtiger_users` WHERE id=?";
        $userInfo = $db->pquery($sql, [$userId]);
        $timeZone = $userInfo->fields['time_zone'];

        // Map of Vtiger-supported timezones to Exchange IDs
        $timezones = array(
            'Pacific/Midway'                 => 'Samoa Standard Time',
            'Pacific/Samoa'                  => 'Samoa Standard Time',
            'Pacific/Honolulu'               => 'Hawaiian Standard Time',
            'America/Anchorage'              => 'Alaskan Standard Time',
            'America/Los_Angeles'            => 'Pacific Standard Time',
            'America/Tijuana'                => 'Pacific Standard Time',
            'America/Denver'                 => 'Mountain Standard Time',
            'America/Chihuahua'              => 'Mexico Standard Time 2',
            'America/Mazatlan'               => 'Mexico Standard Time 2',
            'America/Phoenix'                => 'US Mountain Standard Time',
            'America/Regina'                 => 'Canada Central Standard Time',
            'America/Tegucigalpa'            => 'Central America Standard Time',
            'America/Chicago'                => 'Central Standard Time',
            'America/Mexico_City'            => 'Mexico Standard Time',
            'America/Monterrey'              => 'Mexico Standard Time',
            'America/New_York'               => 'Eastern Standard Time',
            'America/Bogota'                 => 'SA Pacific Standard Time',
            'America/Lima'                   => 'SA Pacific Standard Time',
            'America/Rio_Branco'             => 'SA Pacific Standard Time',
            'America/Indiana/Indianapolis'   => 'US Eastern Standard Time',
            'America/Caracas'                => 'SA Western Standard Time',
            'America/Halifax'                => 'Atlantic Standard Time',
            'America/Manaus'                 => 'SA Western Standard Time',
            'America/Santiago'               => 'Pacific SA Standard Time',
            'America/La_Paz'                 => 'SA Western Standard Time',
            'America/Cuiaba'                 => 'Central Brazilian Standard Time',
            'America/Asuncion'               => 'Paraguay Standard Time',
            'America/St_Johns'               => 'Newfoundland and Labrador Standard Time',
            'America/Argentina/Buenos_Aires' => 'SA Eastern Standard Time',
            'America/Sao_Paulo'              => 'E. South America Standard Time',
            'America/Godthab'                => 'Greenland Standard Time',
            'America/Montevideo'             => 'Montevideo Standard Time',
            'Atlantic/South_Georgia'         => 'Mid-Atlantic Standard Time',
            'Atlantic/Azores'                => 'Azores Standard Time',
            'Atlantic/Cape_Verde'            => 'Cape Verde Standard Time',
            'Europe/London'                  => 'GMT Standard Time',
            'UTC'                            => 'GMT Standard Time',
            'Africa/Monrovia'                => 'Greenwich Standard Time',
            'Africa/Casablanca'              => 'Greenwich Standard Time',
            'Europe/Belgrade'                => 'Central Europe Standard Time',
            'Europe/Sarajevo'                => 'Central European Standard Time',
            'Europe/Brussels'                => 'Romance Standard Time',
            'Africa/Algiers'                 => 'W. Central Africa Standard Time',
            'Europe/Amsterdam'               => 'W. Europe Standard Time',
            'Europe/Minsk'                   => 'GTB Standard Time',
            'Africa/Cairo'                   => 'Egypt Standard Time',
            'Europe/Helsinki'                => 'FLE Standard Time',
            'Europe/Athens'                  => 'GTB Standard Time',
            'Europe/Istanbul'                => 'GTB Standard Time',
            'Asia/Jerusalem'                 => 'Israel Standard Time',
            'Asia/Amman'                     => 'Jordan Standard Time',
            'Asia/Beirut'                    => 'Middle East Standard Time',
            'Africa/Windhoek'                => 'Namibia Standard Time',
            'Africa/Harare'                  => 'South Africa Standard Time',
            'Asia/Kuwait'                    => 'Arab Standard Time',
            'Asia/Baghdad'                   => 'Arabic Standard Time',
            'Africa/Nairobi'                 => 'E. Africa Standard Time',
            'Asia/Tehran'                    => 'Iran Standard Time',
            'Asia/Tbilisi'                   => 'Caucasus Standard Time',
            'Europe/Moscow'                  => 'Russian Standard Time',
            'Asia/Muscat'                    => 'Arabian Standard Time',
            'Asia/Baku'                      => 'Caucasus Standard Time',
            'Asia/Yerevan'                   => 'Caucasus Standard Time',
            'Asia/Karachi'                   => 'West Asia Standard Time',
            'Asia/Tashkent'                  => 'West Asia Standard Time',
            'Asia/Kolkata'                   => 'India Standard Time',
            'Asia/Colombo'                   => 'Sri Lanka Standard Time',
            'Asia/Katmandu'                  => 'Nepal Standard Time',
            'Asia/Dhaka'                     => 'Central Asia Standard Time',
            'Asia/Almaty'                    => 'N. Central Asia Standard Time',
            'Asia/Yekaterinburg'             => 'Ekaterinburg Standard Time',
            'Asia/Rangoon'                   => 'Myanmar Standard Time',
            'Asia/Novosibirsk'               => 'N. Central Asia Standard Time',
            'Asia/Bangkok'                   => 'SE Asia Standard Time',
            'Asia/Brunei'                    => 'China Standard Time',
            'Asia/Krasnoyarsk'               => 'North Asia Standard Time',
            'Asia/Ulaanbaatar'               => 'North Asia East Standard Time',
            'Asia/Kuala_Lumpur'              => 'Singapore Standard Time',
            'Asia/Taipei'                    => 'Taipei Standard Time',
            'Australia/Perth'                => 'W. Australia Standard Time',
            'Asia/Irkutsk'                   => 'North Asia East Standard Time',
            'Asia/Seoul'                     => 'Korea Standard Time',
            'Asia/Tokyo'                     => 'Tokyo Standard Time',
            'Australia/Darwin'               => 'AUS Central Standard Time',
            'Australia/Adelaide'             => 'Cen. Australia Standard Time',
            'Australia/Canberra'             => 'AUS Eastern Standard Time',
            'Australia/Brisbane'             => 'E. Australis Standard Time',
            'Australia/Hobart'               => 'Tasmania Standard Time',
            'Asia/Vladivostok'               => 'Vladivostok Standard Time',
            'Pacific/Guam'                   => 'West Pacific Standard Time',
            'Asia/Yakutsk'                   => 'Yakutsk Standard Time',
            'Pacific/Fiji'                   => 'Fiji Islands Standard Time',
            'Asia/Kamchatka'                 => 'Fiji Islands Standard Time',
            'Pacific/Auckland'               => 'New Zealand Standard Time',
            'Asia/Magadan'                   => 'Central Pacific Standard Time',
            'Pacific/Tongatapu'              => 'Tonga Standard Time',
        );

        $tz = new \DateTimeZone($timeZone);

        return [$timezones[$timeZone], $tz->getOffset(new \DateTime("now", new \DateTimeZone('UTC')))];
    }
}
