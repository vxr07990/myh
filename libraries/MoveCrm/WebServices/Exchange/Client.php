<?php

namespace MoveCrm\WebServices\Exchange;

use Carbon\Carbon;
use PhpEws\DataType;
use PhpEws\DataType\CalendarViewType;
use PhpEws\DataType\DistinguishedFolderIdType;
use PhpEws\DataType\DistinguishedFolderIdNameType;
use PhpEws\DataType\DefaultShapeNamesType;
use PhpEws\DataType\FindItemType;
use PhpEws\DataType\ItemQueryTraversalType;
use PhpEws\DataType\ItemResponseShapeType;
use PhpEws\DataType\NonEmptyArrayOfBaseFolderIdsType;
use PhpEws\DataType\ResponseMessageType;
use PhpEws\EwsConnection;

class Client
{
    /** @var EwsConnection */
    protected $_connection;

    /**
     * Initialize the EWS client.
     *
     * @param string $hostname
     * @param string $username
     * @param string $password
     * @param string $version
     */
    public function __construct($hostname, $username, $password, $version = EwsConnection::VERSION_2007_SP1)
    {
        $this->_connection = new EwsConnection($hostname, $username, $password, $version);
    }

    /**
     * Create a new item.
     *
     * @param  DataType $item
     * @return ResponseMessageType
     */
    public function create(DataType $item)
    {
        return $this->_connection->CreateItem($item);
    }

    /**
     * Get the calendar events for a certain time period, or the current year if no parameters are supplied.
     *
     * @param  Carbon|null $start
     * @param  Carbon|null $end
     * @return ResponseMessageType
     */
    public function getItems(Carbon $start = null, Carbon $end = null)
    {
        $request = new FindItemType;

        // Use this to search only the items in the parent directory in question or use ::SOFT_DELETED
        // to identify "soft deleted" items, i.e. not visible and not in the trash can.
        $request->Traversal = ItemQueryTraversalType::SHALLOW;

        // This identifies the set of properties to return in an item or folder response
        $request->ItemShape = new ItemResponseShapeType;

        // Alternately you can use `DefaultShapeNamesType::DEFAULT_PROPERTIES;`
        $request->ItemShape->BaseShape = DefaultShapeNamesType::ALL_PROPERTIES;

        // Define the time frame to load calendar items
        $request->CalendarView = new CalendarViewType;

        // Set a default time period if needed
        $start = $start ?: Carbon::now()->startOfYear();
        $end   = $end   ?: Carbon::now()->endOfYear();

        // Set the encompassing time period
        $request->CalendarView->StartDate = $start->toW3cString();
        $request->CalendarView->EndDate   = $end->toW3cString();

        // Only look in the "calendars folder"
        $request->ParentFolderIds = new NonEmptyArrayOfBaseFolderIdsType;
        $request->ParentFolderIds->DistinguishedFolderId = new DistinguishedFolderIdType;
        $request->ParentFolderIds->DistinguishedFolderId->Id = DistinguishedFolderIdNameType::CALENDAR;

        // Send request
        $response = $this->_connection->FindItem($request);

        return $response;
    }

    public function sync($state = null)
    {
        return new Sync($this, $state);
    }

    public function syncFolderItems($request)
    {
        return $this->_connection->SyncFolderItems($request)
                    ->ResponseMessages
                    ->SyncFolderItemsResponseMessage;
    }
}
