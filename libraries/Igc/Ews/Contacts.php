<?php

namespace Igc\Ews;

use Carbon\Carbon;
use PhpEws\DataType;
use PhpEws\DataType\ContactsViewType;
use PhpEws\DataType\DistinguishedFolderIdType;
use PhpEws\DataType\DistinguishedFolderIdNameType;
use PhpEws\DataType\DefaultShapeNamesType;
use PhpEws\DataType\FindItemType;
use PhpEws\DataType\ItemQueryTraversalType;
use PhpEws\DataType\ItemResponseShapeType;
use PhpEws\DataType\NonEmptyArrayOfBaseFolderIdsType;
use PhpEws\EwsConnection;
use SplString;

class Contacts
{
    /** @var EwsConnection [description] */
    protected $_ews;

    /**
     * [__construct description]
     * @param [type] $hostname [description]
     * @param [type] $username [description]
     * @param [type] $password [description]
     */
    public function __construct($hostname, $username, $password)
    {
        if (extension_loaded('SPL_Types')) {
            $hostname = new SplString($hostname);
            $username = new SplString($username);
            $password = new SplString($password);
        }

        $this->_ews = new EwsConnection($hostname, $username, $password);
    }

    /**
     * [getItems description]
     * @return [type]             [description]
     */
    public function getItems()
    {
        $request = new FindItemType;

        // Use this to search only the items in the parent directory in question or use ::SOFT_DELETED
        // to identify "soft deleted" items, i.e. not visible and not in the trash can.
        $request->Traversal = ItemQueryTraversalType::SHALLOW;

        // This identifies the set of properties to return in an item or folder response
        $request->ItemShape = new ItemResponseShapeType;

        // Alternately you can use `DefaultShapeNamesType::ALL_PROPERTIES;`
        $request->ItemShape->BaseShape = DefaultShapeNamesType::DEFAULT_PROPERTIES;

        // Define the time frame to load calendar items
        $request->ContactsView = new ContactsViewType;
        $request->ContactsView->InitialName = 'a';
        $request->ContactsView->FinalName = 'z';

        // Only look in the "calendars folder"
        $request->ParentFolderIds = new NonEmptyArrayOfBaseFolderIdsType;
        $request->ParentFolderIds->DistinguishedFolderId = new DistinguishedFolderIdType;
        $request->ParentFolderIds->DistinguishedFolderId->Id = DistinguishedFolderIdNameType::CONTACTS;

        // Send request
        $response = $this->_ews->FindItem($request);

        return $response;
    }
}
