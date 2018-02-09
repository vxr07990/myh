<?php

namespace Igc\Ews;

use Carbon\Carbon;
use PhpEws\DataType;
use PhpEws\DataType\GetItemType;
use PhpEws\DataType\ItemIdType;
use PhpEws\DataType\NonEmptyArrayOfBaseItemIdsType;
use PhpEws\DataType\NonEmptyArrayOfPathsToElementType;
use Users_Record_Model;
use PhpEws\EwsConnection;
use PhpEws\DataType\FindItemType;
use PhpEws\DataType\CalendarItemType;
use PhpEws\DataType\CalendarViewType;
use PhpEws\DataType\SyncFolderItemsType;
use PhpEws\DataType\ResponseMessageType;
use PhpEws\DataType\DefaultShapeNamesType;
use PhpEws\DataType\ItemResponseShapeType;
use PhpEws\DataType\ItemQueryTraversalType;
use PhpEws\DataType\DistinguishedFolderIdType;
use PhpEws\DataType\DistinguishedFolderIdNameType;
use PhpEws\DataType\NonEmptyArrayOfBaseFolderIdsType;
use PhpEws\DataType\PathToExtendedFieldType;
use PhpEws\DataType\MapiPropertyTypeType;
use PhpEws\DataType\DistinguishedPropertySetIdType;

class Calendar
{
    /** @var EwsConnection */
    protected $_ews;

    protected $user;

    /**
     * Initialize EWS client.
     *
     * @param Users_Record_Model $user
     * @param string $driver
     */
    public function __construct(Users_Record_Model $user, $driver = EwsConnection::VERSION_2007_SP2)
    {
        $hostname = $user->get('user_exchange_hostname');
        $username = $user->get('user_exchange_username');
        $password = openssl_decrypt(hex2bin($user->get('user_exchange_password')), 'AES-128-CBC', getenv('EXCHANGE_SALT'), OPENSSL_RAW_DATA);

        $this->user = $user;

        $this->_ews = new EwsConnection($hostname, $username, $password);
    }

    /**
     * Creates a new EWS item.
     *
     * @param  DataType $item
     * @return ResponseMessageType
     */
    public function create(DataType $item)
    {
        return $this->_ews->CreateItem($item);
    }

    /**
     * Update an existing EWS item
     *
     * @param DataType $item
     * @return ResponseMessageType
     */
    public function update(DataType $item)
    {
        return $this->_ews->UpdateItem($item);
    }

    /**
     * Deletes an existing EWS item.
     *
     * @param  DataType $item
     * @return ResponseMessageType
     */
    public function delete(DataType $item)
    {
        return $this->_ews->DeleteItem($item);
    }

    /**
     * Fetch synchronization changes from the exchange server
     *
     * The syncFolderItems operation cannot return elements
     * such as the Body or Attachments. If we need to add this,
     * we'll need to set the value of the BaseShape element
     * to IdOnly when calling syncFolderItems and then use the
     * GetItem operation to get the extra properties.
     * @link https://msdn.microsoft.com/en-us/library/office/dn440953(v=exchg.150).aspx
     *
     * @param string|null $syncState
     * @return \PhpEws\DataType\SyncFolderItemsResponseMessageType
     */
    public function syncFolderItems($syncState = null)
    {
        $request = new SyncFolderItemsType;
        $request->SyncState = $syncState;
        $request->MaxChangesReturned = 512;
        $request->ItemShape = new ItemResponseShapeType;
        $request->ItemShape->BaseShape = DefaultShapeNamesType::ALL_PROPERTIES;

        $request->SyncFolderId = new NonEmptyArrayOfBaseFolderIdsType;
        $request->SyncFolderId->DistinguishedFolderId = new DistinguishedFolderIdType;
        $request->SyncFolderId->DistinguishedFolderId->Id = DistinguishedFolderIdNameType::CALENDAR;

        $response = $this->_ews->SyncFolderItems($request);

        return $response->ResponseMessages->SyncFolderItemsResponseMessage;
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
        $end = $end ?: Carbon::now()->endOfYear();

        // Set the encompassing time period
        $request->CalendarView->StartDate = $start->toW3cString();
        $request->CalendarView->EndDate = $end->toW3cString();

        // Only look in the "calendars folder"
        $request->ParentFolderIds = new NonEmptyArrayOfBaseFolderIdsType;
        $request->ParentFolderIds->DistinguishedFolderId = new DistinguishedFolderIdType;
        $request->ParentFolderIds->DistinguishedFolderId->Id = DistinguishedFolderIdNameType::CALENDAR;

        // Send request
        $response = $this->_ews->FindItem($request);

        return $response;
    }

    /**
     * Queries the current month's calendar events.
     *
     * @see    Calendar::getItems()
     * @return ResponseMessageType
     */
    public function getCurrentMonthsItems()
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        return $this->getItems($start, $end);
    }

    /**
     * Returns the current month's calendar events.
     *
     * @see    Calendar::getCurrentMonthsItems()
     * @return CalendarItemType[]
     */
    public static function month()
    {
        $hostname = getenv('EXCHANGE_HOSTNAME');
        $username = getenv('EXCHANGE_USERNAME');
        $password = getenv('EXCHANGE_PASSWORD');

        return (new self($hostname, $username, $password))->getCurrentMonthsItems()
            ->ResponseMessages
            ->FindItemResponseMessage
            ->RootFolder
            ->Items
            ->CalendarItem;
    }

    public function getItem($request)
    {
        return $this->_ews->GetItem($request);
    }

    public function findItem($request)
    {
        return $this->_ews->FindItem($request);
    }

    public function getEventBody($id, $changeKey)
    {
        $calendar = new Calendar($this->user);

        $request = new GetItemType();

        $request->ItemShape = new ItemResponseShapeType();
        $request->ItemShape->BaseShape = DefaultShapeNamesType::ID_ONLY;

        $request->ItemShape->AdditionalProperties = new NonEmptyArrayOfPathsToElementType();
        $entry = new DataType\PathToUnindexedFieldType();
        $entry->FieldURI = 'item:Body';
        $request->ItemShape->AdditionalProperties->FieldURI[] = $entry;

        $request->ItemIds = new NonEmptyArrayOfBaseItemIdsType();
        $request->ItemIds->ItemId = new ItemIdType();
        $request->ItemIds->ItemId->Id = $id;
        $request->ItemIds->ItemId->ChangeKey = $changeKey;

        $response = $calendar->getItem($request);
        $eventBody = $response->ResponseMessages->GetItemResponseMessage->Items->CalendarItem->Body->_;

        return $eventBody;
    }

    public function checkIfSurveyAppointment($id, $changeKey)
    {
        $calendar = new Calendar($this->user);

        $request = new GetItemType();

        $request->ItemShape = new ItemResponseShapeType();
        $request->ItemShape->BaseShape = DefaultShapeNamesType::ALL_PROPERTIES;
        $request->ItemShape->AdditionalProperties = new NonEmptyArrayOfPathsToElementType();

        $extendedProperty = new PathToExtendedFieldType();
        $extendedProperty->PropertyName = 'isSurveyAppointment';
        $extendedProperty->PropertyType = MapiPropertyTypeType::BOOLEAN;
        $extendedProperty->DistinguishedPropertySetId = DistinguishedPropertySetIdType::PUBLIC_STRINGS;
        $request->ItemShape->AdditionalProperties->ExtendedFieldURI = array($extendedProperty);

        $request->ItemIds = new NonEmptyArrayOfBaseItemIdsType();
        $request->ItemIds->ItemId = new ItemIdType();
        $request->ItemIds->ItemId->Id = $id;
        $request->ItemIds->ItemId->ChangeKey = $changeKey;
        $response = $calendar->getItem($request);

        return $response->ResponseMessages->GetItemResponseMessage->Items->CalendarItem->ExtendedProperty->Value;
    }
}
