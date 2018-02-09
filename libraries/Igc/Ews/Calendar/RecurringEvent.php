<?php


namespace Igc\Ews\Calendar;

use Carbon\Carbon;
use Igc\Ews\Calendar;
use Users_Record_Model;
use Carbon\CarbonInterval;
use PhpEws\DataType\ItemIdType;
use PhpEws\DataType\GetItemType;
use PhpEws\DataType\FindItemType;
use PhpEws\DataType\RestrictionType;
use PhpEws\DataType\DefaultShapeNamesType;
use PhpEws\DataType\ItemResponseShapeType;
use PhpEws\DataType\ItemQueryTraversalType;
use PhpEws\DataType\PathToUnindexedFieldType;
use PhpEws\DataType\DistinguishedFolderIdType;
use PhpEws\DataType\DistinguishedFolderIdNameType;
use PhpEws\DataType\NonEmptyArrayOfBaseItemIdsType;
use PhpEws\DataType\NonEmptyArrayOfBaseFolderIdsType;
use PhpEws\DataType\NonEmptyArrayOfPathsToElementType;

require_once 'include/Webservices/Create.php';


class RecurringEvent
{
    /**
     * Array of event details that have been
     * mapped out of the MS response object
     * @var array
     */
    private $eventDetails;

    /**
     * An instance of the currently logged in user
     * @var Users_Record_Model
     */
    private $user;

    /**
     * Used in for loops for limiting unending recurrences
     * @var int
     */
    private $limit;

    /**
     * Constant used for deciding how many
     * times to recur an event with no end date
     */
    const PERPETUITY = 50;

    public function __construct(array $eventDetails, Users_Record_Model $user)
    {
        $this->eventDetails = $eventDetails;
        $this->user = $user;
        $this->limit = self::PERPETUITY;
    }

    /**
     * Will return an object with the recurrence details if the
     * event has the CalendarItemType: RecurringMaster property
     *
     * @return object
     */
    public function getRecurrenceDetails()
    {
        $calendar = new Calendar($this->user);

        $request = new GetItemType();

        $request->ItemShape = new ItemResponseShapeType();
        $request->ItemShape->BaseShape = DefaultShapeNamesType::ID_ONLY;
        $request->ItemShape->AdditionalProperties = new NonEmptyArrayOfPathsToElementType();

        $entry = new PathToUnindexedFieldType();
        $entry->FieldURI = 'calendar:Recurrence';
        $request->ItemShape->AdditionalProperties->FieldURI[] = $entry;

        $request->ItemIds = new NonEmptyArrayOfBaseItemIdsType();
        $request->ItemIds->ItemId = new ItemIdType();
        $request->ItemIds->ItemId->Id = $this->eventDetails['exchange_id'];

        $response = $calendar->getItem($request);

        $message = $response->ResponseMessages->GetItemResponseMessage->Items->CalendarItem;

        $recurrenceDetails = $this->buildRecurrenceObject($message);

        return $recurrenceDetails;
    }

    /**
     * Create the recurring events based on
     * the recurrence details of the master event
     *
     * @param $eventDetails
     * @param $recurrenceDetails
     * @param $masterParams
     * @param $db
     *
     * TODO: Fix before this breaks on 03:14:07 UTC on 19 January 2038
     * @link http://www.unixtimestamp.com/
     */
    public function persistEventRecurrences($eventDetails, $recurrenceDetails, $masterParams, $db)
    {
        // Set some variables that are used in all of the switch cases below
        $masterActivityId = $masterParams[1];
        $type = $recurrenceDetails->recurrence->type;
        $startDateString = $eventDetails['date_start'];
        $startTimeString = $eventDetails['time_start'];
        $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $startDateString . " " . $startTimeString);
        $endDateString = ($recurrenceDetails->recurrence->EndDateRecurrence->EndDate) ?: null;
        $endDate = ($endDateString) ? Carbon::createFromFormat('Y-m-d-H:i', $endDateString) : null;

        switch ($type) {
            case "DailyRecurrence":
                // Case-specific interval setup
                $interval = $recurrenceDetails->recurrence->Interval;

                // Cycle through all the intervals and create a
                // local event while we're between the start and end
                $nextRecurrence = $startDate->copy()->addDays($interval);
                $this->persistNewRecurrence($eventDetails, $nextRecurrence, $masterActivityId, $db);

                // Was there an end date set on the recurrence?
                if ($endDate) {
                    while ($nextRecurrence->between($startDate, $endDate)) {
                        $nextRecurrence->addDays($interval);
                        if ($nextRecurrence->between($startDate, $endDate)) {
                            $this->persistNewRecurrence($eventDetails, $nextRecurrence, $masterActivityId, $db);
                        }
                    }
                // If no end date, we have to stop it at some point...
                } else {
                    for ($i = 1; $i <= $this->limit; $i++) {
                        $nextRecurrence->addDays($interval);
                        $this->persistNewRecurrence($eventDetails, $nextRecurrence, $masterActivityId, $db);
                    }
                }
                break;

            case "AbsoluteMonthlyRecurrence":
                // Case-specific interval setup
                $interval = $recurrenceDetails->recurrence->Interval;

                // Cycle through all the intervals and create a
                // local event while we're between the start and end
                $nextRecurrence = $startDate->copy()->addMonths($interval);
                $this->persistNewRecurrence($eventDetails, $nextRecurrence, $masterActivityId, $db);

                // Was there an end date set on the recurrence?
                if ($endDate) {
                    while ($nextRecurrence->between($startDate, $endDate)) {
                        $nextRecurrence->addMonths($interval);
                        if ($nextRecurrence->between($startDate, $endDate)) {
                            $this->persistNewRecurrence($eventDetails, $nextRecurrence, $masterActivityId, $db);
                        }
                    }
                    // If no end date, we have to stop it at some point...
                } else {
                    for ($i = 1; $i <= $this->limit; $i++) {
                        $nextRecurrence->addMonths($interval);
                        $this->persistNewRecurrence($eventDetails, $nextRecurrence, $masterActivityId, $db);
                    }
                }
                break;

            case "RelativeMonthlyRecurrence":
                // Case-specific interval setup
                $interval = $recurrenceDetails->recurrence->Interval;
                $daysOfWeek = $recurrenceDetails->recurrence->DaysOfWeek;
                $daysOfWeek = explode(" ", $daysOfWeek);
                $daysOfWeek = $this->mapDaysOfWeekToCarbonConstants($daysOfWeek);
                $dayOfWeekIndex = $recurrenceDetails->recurrence->DayOfWeekIndex;
                $nth = $this->mapDayOfWeekIndexToInt($dayOfWeekIndex);

                // Cycle through all the intervals and create a
                // local event while we're between the start and end
                foreach ($daysOfWeek as $day => $const) {
                    $nextRecurrence = $startDate->copy()->addMonths($interval)->nthOfMonth($nth, $const);
                    $this->persistNewRecurrence($eventDetails, $nextRecurrence, $masterActivityId, $db);

                    // Was there an end date set on the recurrence?
                    if ($endDate) {
                        while ($nextRecurrence->between($startDate, $endDate)) {
                            $nextRecurrence->addMonths($interval)->nthOfMonth($nth, $const);
                            if ($nextRecurrence->between($startDate, $endDate)) {
                                $this->persistNewRecurrence($eventDetails, $nextRecurrence, $masterActivityId, $db);
                            }
                        }
                        // If no end date, we have to stop it at some point...
                    } else {
                        for ($i = 1; $i <= $this->limit; $i++) {
                            $nextRecurrence->addMonths($interval)->nthOfMonth($nth, $const);
                            $this->persistNewRecurrence($eventDetails, $nextRecurrence, $masterActivityId, $db);
                        }
                    }
                }
                break;

            case "AbsoluteYearlyRecurrence":
                // Case-specific interval setup
                // So far, don't use these when we can just addYear()
                $dayOfMonth = $recurrenceDetails->recurrence->DayOfMonth;
                $month = $recurrenceDetails->recurrence->Month;

                // Cycle through all the intervals and create a
                // local event while we're between the start and end
                $nextRecurrence = $startDate->copy()->addYear();
                $this->persistNewRecurrence($eventDetails, $nextRecurrence, $masterActivityId, $db);

                // Was there an end date set on the recurrence?
                if ($endDate) {
                    while ($nextRecurrence->between($startDate, $endDate)) {
                        $nextRecurrence->addYear();
                        if ($nextRecurrence->between($startDate, $endDate)) {
                            $this->persistNewRecurrence($eventDetails, $nextRecurrence, $masterActivityId, $db);
                        }
                    }
                    // If no end date, we have to stop it at some point...
                } else {
                    for ($i = 1; $i <= $this->limit; $i++) {
                        $nextRecurrence->addYear();
                        $this->persistNewRecurrence($eventDetails, $nextRecurrence, $masterActivityId, $db);
                    }
                }

                break;

            case "RelativeYearlyRecurrence":
                // Case-specific interval setup
                $daysOfWeek = $recurrenceDetails->recurrence->DaysOfWeek;
                $daysOfWeek = explode(" ", $daysOfWeek);
                $daysOfWeek = $this->mapDaysOfWeekToCarbonConstants($daysOfWeek);
                $dayOfWeekIndex = $recurrenceDetails->recurrence->DayOfWeekIndex;
                $nth = $this->mapDayOfWeekIndexToInt($dayOfWeekIndex);
                $month = $recurrenceDetails->recurrence->Month;

                // Cycle through all the intervals and create a
                // local event while we're between the start and end
                foreach ($daysOfWeek as $day => $const) {
                    $nextRecurrence = $startDate->copy()->addYear()->nthOfYear($nth, $const);
                    $this->persistNewRecurrence($eventDetails, $nextRecurrence, $masterActivityId, $db);

                    // Was there an end date set on the recurrence?
                    if ($endDate) {
                        while ($nextRecurrence->between($startDate, $endDate)) {
                            $nextRecurrence->addYear()->nthOfYear($nth, $const);
                            if ($nextRecurrence->between($startDate, $endDate)) {
                                $this->persistNewRecurrence($eventDetails, $nextRecurrence, $masterActivityId, $db);
                            }
                        }
                        // If no end date, we have to stop it at some point...
                    } else {
                        for ($i = 1; $i <= $this->limit; $i++) {
                            $nextRecurrence->addYear()->nthOfYear($nth, $const);
                            $this->persistNewRecurrence($eventDetails, $nextRecurrence, $masterActivityId, $db);
                        }
                    }
                }
                break;

            case "WeeklyRecurrence":
                // Case-specific interval setup
                $interval = $recurrenceDetails->recurrence->Interval;
                $daysOfWeek = $recurrenceDetails->recurrence->DaysOfWeek;
                $daysOfWeek = explode(" ", $daysOfWeek);
                $daysOfWeek = $this->mapDaysOfWeekToCarbonConstants($daysOfWeek);

                // Cycle through all the intervals and create a
                // local event while we're between the start and end
                foreach ($daysOfWeek as $day => $const) {
                    $nextRecurrence = $startDate->copy()->next($const);
                    $this->persistNewRecurrence($eventDetails, $nextRecurrence, $masterActivityId, $db);

                    // Was there an end date set on the recurrence?
                    if ($endDate) {
                        while ($nextRecurrence->between($startDate, $endDate)) {
                            $nextRecurrence->addWeek();
                            if ($nextRecurrence->between($startDate, $endDate)) {
                                $this->persistNewRecurrence($eventDetails, $nextRecurrence, $masterActivityId, $db);
                            }
                        }
                        // If no end date, we have to stop it at some point...
                    } else {
                        for ($i = 1; $i <= $this->limit; $i++) {
                            $nextRecurrence->addWeek();
                            $this->persistNewRecurrence($eventDetails, $nextRecurrence, $masterActivityId, $db);
                        }
                    }
                }
                break;
        }
    }

    /**
     * Save a recurring child to the local calendar
     *
     * @param $eventDetails
     * @param $nextRecurrence
     * @param $masterActivityId
     * @param $db
     * @throws \WebServiceException
     */
    private function persistNewRecurrence($eventDetails, $nextRecurrence, $masterActivityId, $db)
    {
        // Swap out the master's event date for the recurring event date
        $eventDetails = $this->updateEventDate($eventDetails, $nextRecurrence);

        // Create the new event recurrence
        $retval = vtws_create('Events', $eventDetails, Users_Record_Model::getCurrentUserModel());

        // Insert the local Exchange related metadata.
        $created_time = $retval['createdtime'];
        $activityId = explode('x', $retval['id'])[1];
        $params     = [
            "childId_" . $activityId,
            $activityId,
            null,
            $created_time,
            $masterActivityId
        ];
        $sql = "INSERT INTO calendar_exchange_metadata (id, activity_id, change_key, last_sync_time, parent_activity_id)
                VALUES (?, ?, ?, ?, ?)";
        $db->pquery($sql, $params);

        // Update vtiger_activity_reminder info if it exists. This is an
        // update and not an insert because the vtws_create is creating this
        // entry first, so the INSERT fails because of the existing primary key
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
     * Wrap up the response in an object. This will be parsed when
     * we're creating local events based on the recurrence type.
     *
     * @param $message
     * @return object
     */
    private function buildRecurrenceObject($message)
    {
        if (property_exists($message->Recurrence, 'DailyRecurrence')) {
            $response = (object)[
                'id' => $message->ItemId->Id,
                'changeKey' => $message->ItemId->ChangeKey,
                'recurrence' => (object)[
                    'type'              => 'DailyRecurrence',
                    'Interval'          => $message->Recurrence->DailyRecurrence->Interval,
                    'DaysOfWeek'        => null,
                    'DayOfMonth'        => null,
                    'DayOfWeekIndex'    => null,
                    'Month'             => null,
                    'EndDateRecurrence' => (object)[
                        'StartDate' => $message->Recurrence->EndDateRecurrence->StartDate,
                        'EndDate' => $message->Recurrence->EndDateRecurrence->EndDate
                    ]
                ]
            ];
        } elseif (property_exists($message->Recurrence, 'WeeklyRecurrence')) {
            $response = (object)[
                'id' => $message->ItemId->Id,
                'changeKey' => $message->ItemId->ChangeKey,
                'recurrence' => (object)[
                    'type'              => 'WeeklyRecurrence',
                    'Interval'          => $message->Recurrence->WeeklyRecurrence->Interval,
                    'DaysOfWeek'        => $message->Recurrence->WeeklyRecurrence->DaysOfWeek,
                    'DayOfMonth'        => null,
                    'DayOfWeekIndex'    => null,
                    'Month'             => null,
                    'EndDateRecurrence' => (object)[
                        'StartDate' => $message->Recurrence->EndDateRecurrence->StartDate,
                        'EndDate' => $message->Recurrence->EndDateRecurrence->EndDate
                    ]
                ]
            ];
        } elseif (property_exists($message->Recurrence, 'AbsoluteMonthlyRecurrence')) {
            $response = (object)[
                'id' => $message->ItemId->Id,
                'changeKey' => $message->ItemId->ChangeKey,
                'recurrence' => (object)[
                    'type'              => 'AbsoluteMonthlyRecurrence',
                    'Interval'          => $message->Recurrence->AbsoluteMonthlyRecurrence->Interval,
                    'DaysOfWeek'        => null,
                    'DayOfMonth'        => $message->Recurrence->AbsoluteMonthlyRecurrence->DayOfMonth,
                    'DayOfWeekIndex'    => null,
                    'Month'             => null,
                    'EndDateRecurrence' => (object)[
                        'StartDate' => $message->Recurrence->EndDateRecurrence->StartDate,
                        'EndDate' => $message->Recurrence->EndDateRecurrence->EndDate
                    ]
                ]
            ];
        } elseif (property_exists($message->Recurrence, 'RelativeMonthlyRecurrence')) {
            $response = (object)[
                'id' => $message->ItemId->Id,
                'changeKey' => $message->ItemId->ChangeKey,
                'recurrence' => (object)[
                    'type'              => 'RelativeMonthlyRecurrence',
                    'Interval'          => $message->Recurrence->RelativeMonthlyRecurrence->Interval,
                    'DaysOfWeek'        => $message->Recurrence->RelativeMonthlyRecurrence->DaysOfWeek,
                    'DayOfMonth'        => null,
                    'DayOfWeekIndex'    => $message->Recurrence->RelativeMonthlyRecurrence->DayOfWeekIndex,
                    'Month'             => null,
                    'EndDateRecurrence' => (object)[
                        'StartDate' => $message->Recurrence->EndDateRecurrence->StartDate,
                        'EndDate' => $message->Recurrence->EndDateRecurrence->EndDate
                    ]
                ]
            ];
        } elseif (property_exists($message->Recurrence, 'AbsoluteYearlyRecurrence')) {
            $response = (object)[
                'id' => $message->ItemId->Id,
                'changeKey' => $message->ItemId->ChangeKey,
                'recurrence' => (object)[
                    'type'              => 'AbsoluteYearlyRecurrence',
                    'Interval'          => null,
                    'DaysOfWeek'        => null,
                    'DayOfMonth'        => $message->Recurrence->AbsoluteYearlyRecurrence->DayOfMonth,
                    'DayOfWeekIndex'    => null,
                    'Month'             => $message->Recurrence->AbsoluteYearlyRecurrence->Month,
                    'EndDateRecurrence' => (object)[
                        'StartDate' => $message->Recurrence->EndDateRecurrence->StartDate,
                        'EndDate' => $message->Recurrence->EndDateRecurrence->EndDate
                    ]
                ]
            ];
        } elseif (property_exists($message->Recurrence, 'RelativeYearlyRecurrence')) {
            $response = (object)[
                'id' => $message->ItemId->Id,
                'changeKey' => $message->ItemId->ChangeKey,
                'recurrence' => (object)[
                    'type'              => 'RelativeYearlyRecurrence',
                    'Interval'          => null,
                    'DaysOfWeek'        => $message->Recurrence->RelativeYearlyRecurrence->DaysOfWeek,
                    'DayOfMonth'        => null,
                    'DayOfWeekIndex'    => $message->Recurrence->RelativeYearlyRecurrence->DayOfWeekIndex,
                    'Month'             => $message->Recurrence->RelativeYearlyRecurrence->Month,
                    'EndDateRecurrence' => (object)[
                        'StartDate' => $message->Recurrence->EndDateRecurrence->StartDate,
                        'EndDate' => $message->Recurrence->EndDateRecurrence->EndDate
                    ]
                ]
            ];
        }
        return $response;
    }

    /**
     * Used in parsing some of the relative recurrence intervals
     *
     * @param $daysOfWeek
     * @return array
     */
    private function mapDaysOfWeekToCarbonConstants($daysOfWeek)
    {
        $daysOfWeek = array_flip($daysOfWeek);
        foreach ($daysOfWeek as $day => $const) {
            if ($day == "Sunday") {
                $daysOfWeek['Sunday'] = 0;
            } elseif ($day == "Monday") {
                $daysOfWeek['Monday'] = 1;
            } elseif ($day == "Tuesday") {
                $daysOfWeek['Tuesday'] = 2;
            } elseif ($day == "Wednesday") {
                $daysOfWeek['Wednesday'] = 3;
            } elseif ($day == "Thursday") {
                $daysOfWeek['Thursday'] = 4;
            } elseif ($day == "Friday") {
                $daysOfWeek['Friday'] = 5;
            } elseif ($day == "Saturday") {
                $daysOfWeek['Saturday'] = 6;
            }
        }
        return $daysOfWeek;
    }

    /**
     * Used in parsing some of the relative recurrence intervals
     *
     * @param $dayOfWeekIndex
     * @return int
     */
    private function mapDayOfWeekIndexToInt($dayOfWeekIndex)
    {
        switch ($dayOfWeekIndex) {
            case "First":
                return 1;
            case "Second":
                return 2;
            case "Third":
                return 3;
            case "Fourth":
                return 4;
            case "Fifth":
                return 5;
        }
    }

    /**
     * Build the array to pass to vtws_create that includes the
     * details for the new child recurring event
     *
     * @param array $eventDetails
     * @param Carbon $recurrenceDate
     * @return array
     */
    private function updateEventDate($eventDetails, Carbon $recurrenceDate)
    {
        // Get the hours:minutes:seconds from the master event
        // The recurring events are only changing dates (at this point)
        $time = explode(':', $eventDetails['time_start']);

        // new endTime will be set based on the master event's duration
        $durationInterval = CarbonInterval::create(0, 0, 0, 0, $eventDetails['duration_hours'], $eventDetails['duration_minutes'], 0);

        // recurrenceDate (built with the interval logic) didn't include
        // any times so we'll add it to match the times of the master event
        $recurrenceDate->hour = $time[0];
        $recurrenceDate->minute = $time[1];
        $recurrenceDate->second = $time[2];

        // Create new start date in the stringy format vtws_create wants
        $newStartDate = Carbon::createFromDate($recurrenceDate->year, $recurrenceDate->month, $recurrenceDate->day, 'UTC');
        $newStartDateString = $newStartDate->toDateString();

        // Create new start time in the stringy format vtws_create wants
        $newStartTime = Carbon::createFromFormat('H:i:s', $eventDetails['time_start'], 'UTC');
        $newStartTimeString = $newStartTime->toTimeString();

        // Create new end date in the stringy format vtws_create wants
        $newEndDateString = $newStartDate->add($durationInterval)->format('Y-m-d');

        // Create new end time in the stringy format vtws_create wants
        $newEndTime = Carbon::createFromFormat('H:i:s', $eventDetails['time_end'], 'UTC');
        $newEndTimeString = $newEndTime->toTimeString();

        // Set the new dates and times in the array we'll be sending to vtws_create
        $eventDetails['date_start'] = $newStartDateString;
        $eventDetails['time_start'] = $newStartTimeString;
        $eventDetails['due_date'] = $newEndDateString;
        $eventDetails['time_end'] = $newEndTimeString;

        return $eventDetails;
    }

    /**
     * Get a recurrence event by way of the InstanceIndex
     * which begins at 1. As it turns out, this is fairly
     * useless because you have to fetch individually by index
     *
     * Unused... leaving it in for reference
     *
     * @param $details
     * @return mixed
     */
    public function fetchExchangeDetails($details)
    {
        $calendar = new Calendar($this->user);

        $request = new GetItemType();

        $request->ItemShape = new ItemResponseShapeType();
        $request->ItemShape->BaseShape = DefaultShapeNamesType::ALL_PROPERTIES;
        $request->ItemIds = new NonEmptyArrayOfBaseItemIdsType();
        $request->ItemIds->OccurrenceItemId = new ItemIdType();
        $request->ItemIds->OccurrenceItemId->RecurringMasterId = $details->id;
        $request->ItemIds->OccurrenceItemId->ChangeKey = $details->changeKey;
        $request->ItemIds->OccurrenceItemId->InstanceIndex = 1;

        $response = $calendar->getItem($request);

        $id = $response->ResponseMessages->GetItemResponseMessage->Items->CalendarItem->ItemId->Id;

        return $id;
    }

    /**
     * Failed attempt at fetching all child events
     * based on having the master event
     *
     * @param $details
     * @return \PhpEws\FindItemResponseType
     */
    public function findExchangeItems($details)
    {
        ### dump("Here");
        $calendar = new Calendar($this->user);

        $request = new FindItemType();

        $request->Traversal = ItemQueryTraversalType::SHALLOW;
        $request->ItemShape = new ItemResponseShapeType();
        $request->ItemShape->BaseShape = DefaultShapeNamesType::ALL_PROPERTIES;

        $request->ParentFolderIds = new NonEmptyArrayOfBaseFolderIdsType();
        $request->ParentFolderIds->DistinguishedFolderId = new DistinguishedFolderIdType();
        $request->ParentFolderIds->DistinguishedFolderId->Id = DistinguishedFolderIdNameType::CALENDAR;

        $request->Restriction = new RestrictionType();
        $request->Restriction->IsEqualTo->FieldURI->FieldURI = 'calendar:Recurrence';
        $request->Restriction->IsEqualTo->FieldURIOrConstant->Constant->Value = true;

        $response = $calendar->findItem($request);

        return $response;
    }
}
