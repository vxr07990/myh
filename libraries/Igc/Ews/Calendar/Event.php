<?php

namespace Igc\Ews\Calendar;

use DateTime;
use DateTimeInterface;
use PhpEws\DataType\CalendarItemType;
use PhpEws\DataType\CreateItemType;
use PhpEws\DataType\NonEmptyArrayOfAllItemsType;

class Event extends CreateItemType
{
    /**
     * [__construct description]
     * @param string            $subject     Summary of the event
     * @param DateTimeInterface $start       Start time of the event
     * @param DateTimeInterface $end         End time of the event
     * @param bool              $sendInvites Send event invites (defaults to `false`)
     */
    public function __construct($subject, DateTimeInterface $start,  DateTimeInterface $end, $sendInvites = false)
    {
        /**
         * CalendarItemCreateOrDeleteOperationType: [SendToNone, SendOnlyToAll, SendToAllAndSaveCopy]
         * @link https://msdn.microsoft.com/en-us/library/office/exchangewebservices.calendaritemcreateordeleteoperationtype%28v=exchg.150%29.aspx
         */
        $this->SendMeetingInvitations = ($sendInvites) ? 'SendToAllAndSaveCopy'
                                                       : 'SendToNone';

        $this->Items = new NonEmptyArrayOfAllItemsType;
        $this->Items->CalendarItem = new CalendarItemType;
        $this->Items->CalendarItem->Subject = $subject;

        // Using the `DateTimeInterface` methods for portability.
        $this->Items->CalendarItem->Start = $start->format(DateTime::W3C);
        $this->Items->CalendarItem->End = $end->format(DateTime::W3C);
    }
}
