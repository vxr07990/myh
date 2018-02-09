<?php


namespace Igc\Ews\Synchronization;

use Igc\Ews\Calendar;
use Users_Record_Model;
use Carbon\Carbon;

class Recurrence
{

    /**
     * Recurrence constructor.
     */
    public function __construct()
    {
        $user = Users_Record_Model::getCurrentUserModel();
        $ews = new Calendar($user);
        $start = Carbon::createFromDate(2016, 1, 10);
        $end = Carbon::createFromDate(2016, 1, 13);
        $types = ['singles' => [], 'occurrences' => [], 'masters' => [], 'exceptions' => []];

        $events = $ews->getItems($start, $end);

        $events = $events->ResponseMessages->FindItemResponseMessage->RootFolder->Items->CalendarItem;

        foreach ($events as $k => $event) {
            if ($event->CalendarItemType == "Single") {
                $types['singles'][$k] = $event;
            } elseif ($event->CalendarItemType == "Occurrence") {
                $types['occurrences'][$k] = $event;
            } elseif ($event->CalendarItemType == "RecurringMaster") {
                $types['masters'][$k] = $event;
            } elseif ($event->CalendarItemType == "Exception") {
                $types['exceptions'][$k] = $event;
            }
        }

        //dump($events);
        //dump($types);
    }
}
