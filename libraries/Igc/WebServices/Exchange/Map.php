<?php

namespace Igc\WebServices\Exchange;

use stdClass;

class Map
{
    /**
     * @param  stdClass $event
     * @return stdClass
     */
    public static function calendarItem(stdClass $event)
    {
        return (object) [
            'id'        => $event->CalendarItem->ItemId->Id,
            'changeKey' => $event->CalendarItem->ItemId->ChangeKey,
            'start'     => $event->CalendarItem->Start,
            'end'       => $event->CalendarItem->End,
            'subject'   => $event->CalendarItem->Subject
        ];
    }

    /**
     * @param  array   $events
     * @return stdClass[]
     */
    public static function events(array $events)
    {
        $mapped = [];

        foreach ($events as $event) {
            $mapped[] = Map::calendarItem($event);
        }

        return $mapped;
    }
}
