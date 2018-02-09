<?php


namespace Igc\Ews\Calendar;

use Carbon\Carbon;

/**
 * Represents a new local event that needs
 * to be pushed up to the remote exchange server
 *
 * Class LocalEvent
 * @package Igc\Ews\Calendar
 */
class LocalEvent
{
    /**
     * Instance of the currently logged in user
     * @var
     */
    public $user;

    /**
     * Subject line of the calendar event
     * @var
     */
    public $subject;

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
     * Boolean - should we set a reminder
     * @var
     */
    public $setReminder;

    /**
     * What is the timing for the reminder
     * @var
     */
    public $reminderTimeBefore;

    /**
     * Body text for the calendar event
     * @var
     */
    public $body;

    /**
     * This is basically the event ID used across vtiger tables
     * @var
     */
    public $activityId;

    /**
     * This is a flag to mark survey appointment records
     * @var bool
     */
    public $isSurveyAppointment;

    /**
     * LocalEvent constructor.
     * @param $user
     * @param $subject
     * @param $startTime
     * @param $endTime
     * @param $setReminder
     * @param $reminderTimeBefore
     * @param $body
     * @param $activityId
     * @param $isSurveyAppointment
     */
    public function __construct($user,
                                $subject,
                                Carbon $startTime,
                                Carbon $endTime,
                                $setReminder,
                                $reminderTimeBefore,
                                $body,
                                $activityId,
                                $location,
                                $isSurveyAppointment=0)
    {
        $this->user = $user;
        $this->subject = $subject;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->setReminder = $setReminder;
        $this->reminderTimeBefore = $reminderTimeBefore;
        $this->body = $body;
        $this->activityId = $activityId;
        $this->Location = $location;
        $this->isSurveyAppointment = $isSurveyAppointment;
    }

    /**
     * Convert the object to an array should it be necessary
     *
     * @return mixed
     */
    public function mappedToRemote()
    {
        $event['body']                  = $this->body;
        $event['subject']               = $this->subject;
        $event['endTime']               = $this->endTime;
        $event['startTime']             = $this->startTime;
        $event['activityId']            = $this->activityId;
        $event['setReminder']           = $this->setReminder;
        $event['reminderTimeBefore']    = $this->reminderTimeBefore;
        $event['isSurveyAppointment']   = $this->isSurveyAppointment;

        return $event;
    }
}
