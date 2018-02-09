<?php


namespace Igc\Ews\Calendar;

use Carbon\Carbon;

/**
 * Represents an updated local event that needs
 * to be pushed up to the remote exchange server
 *
 * Class LocalUpdate
 * @package Igc\Ews\Calendar
 */
class LocalUpdate
{
    /**
     * Common Id across vtiger tables
     *
     * @var
     */
    public $activityId;

    /**
     * Unique Exchange Id
     * @var
     */
    public $eventId;

    /**
     * Key that exchange changes every time
     * an event is altered on remote end
     * @var
     */
    public $changeKey;

    /**
     * Subject of the calendar event
     * @var
     */
    public $subject;

    /**
     * Body of the calendar event
     * @var
     */
    public $body;

    /**
     * Start time of the calendar event
     * @var Carbon
     */
    public $startTime;

    /**
     * End time of the calendar event
     * @var Carbon
     */
    public $endTime;

    /**
     * Is there a reminder set?
     * @bool
     */
    public $reminderIsSet;

    /**
     * Number of minutes before to set reminder
     * @var
     */
    public $reminderMinutesBeforeStart;

    /**
     * Free or Busy
     * @var
     */
    public $eventStatus;

    /**
     * Location of the event
     * @var
     */
    public $location;

    /**
     * Is this a recurring event?
     * @bool
     */
    public $isRecurring;

    /**
     * Describes the type of event
     * Single, RecurringMaster, Occurrence, Exception
     * @var
     */
    public $calendarItemType;

    /**
     * LocalUpdate constructor.
     * @param $activityId
     * @param $eventId
     * @param $changeKey
     * @param $subject
     * @param $body
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @param $reminderIsSet
     * @param $reminderMinutesBeforeStart
     * @param $eventStatus
     * @param $location
     * @param $isRecurring
     * @param $calendarItemType
     */
    public function __construct($activityId,
                                $eventId,
                                $changeKey,
                                $subject,
                                $body,
                                Carbon $startTime,
                                Carbon $endTime,
                                $reminderIsSet,
                                $reminderMinutesBeforeStart,
                                $eventStatus,
                                $location,
                                $isRecurring,
                                $calendarItemType)
    {
        $this->activityId                   = $activityId;
        $this->eventId                      = $eventId;
        $this->changeKey                    = $changeKey;
        $this->subject                      = $subject;
        $this->startTime                    = $startTime;
        $this->endTime                      = $endTime;
        $this->body                         = $body;
        $this->reminderIsSet                = $reminderIsSet;
        $this->reminderMinutesBeforeStart   = $reminderMinutesBeforeStart;
        $this->eventStatus                  = $eventStatus;
        $this->location                     = $location;
        $this->isRecurring                  = $isRecurring;
        $this->calendarItemType             = $calendarItemType;
    }
}
