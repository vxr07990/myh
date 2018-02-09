<?php

namespace MoveCrm\WebServices\Exchange\DataMaps;

use stdClass;

class MoveCrmDataMap
{
    /**
     * @param stdClass $event
     * @param int $userId
     * @return array
     */
    public static function calendarItem(stdClass $event, $userId)
    {
        $calendarItem = $event->CalendarItem;

        return [
            'subject'             => $calendarItem->Subject,
            'date_start'          => self::date($calendarItem->Start),
            'time_start'          => self::time($calendarItem->Start),
            'due_date'            => self::date($calendarItem->End),
            'time_end'            => self::time($calendarItem->End),
            'duration_hours'      => self::durationHours($calendarItem->Start, $calendarItem->End),
            'duration_minutes'    => self::durationMinutes($calendarItem->Start, $calendarItem->End),
            'eventstatus'         => $calendarItem->LegacyFreeBusyStatus,
            'location'            => $calendarItem->Location,
            'exchange_id'         => $calendarItem->ItemId->Id,
            'exchange_change_key' => $calendarItem->ItemId->ChangeKey,
            'activitytype'        => 'Meeting',
            'reminder_set'        => $calendarItem->ReminderIsSet,
            'reminder_time'       => $calendarItem->ReminderMinutesBeforeStart,
            'calendar_item_type'  => $calendarItem->CalendarItemType,
            'assigned_user_id'    => $userId
        ];
    }

    /**
     * Map an Exchange date to a moveCRM compatible format.
     *
     * @param  string $dateTime
     *
     * @return string|bool
     */
    public static function date($dateTime)
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
    public static function durationHours($start, $end)
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
    public static function durationMinutes($start, $end)
    {
        return sprintf('%u', date('i', (strtotime($end) - strtotime($start))));
    }

    /**
     * Map an Exchange time to a moveCRM compatible format.
     *
     * @param  string $dateTime
     *
     * @return string|bool
     */
    public static function time($dateTime)
    {
        return date('H:i:s', strtotime($dateTime));
    }
}
