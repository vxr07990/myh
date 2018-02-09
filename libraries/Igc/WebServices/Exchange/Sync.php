<?php

namespace Igc\WebServices\Exchange;

use PhpEws\DataType;
use PhpEws\EwsConnection;
use stdClass;
use Symfony\Component\Config\Definition\Exception\Exception;

class Sync
{
    /** @var bool This object contains the last item for the sync */
    public $includesLastItemInRange;

    /** @var string|null Last sync state */
    public $state;

    /** @var stdClass|null Queue of event items to sync */
    public $queue;

    /**
     * Sync constructor.
     *
     * @param Client $client
     * @param string|null   $state
     */
    public function __construct(Client $client, $state = null)
    {
        $this->state = $state;
        $request = Sync\Request::factory($state);
        $response = $client->SyncFolderItems($request);
        $changes = $response->Changes;
        $this->includesLastItemInRange = $response->IncludesLastItemInRange;
        $this->state = $response->SyncState;
#dump($changes);
#dump(Map::events($changes->Create));
        $this->queue = (object) [
            'create' => [],
            'update' => [],
            'delete' => []
        ];

        // Created events
        if (property_exists($changes, 'Create')) {
            $this->queue->create = Map::events($changes->Create);
        }

        // Updated events
        if (property_exists($changes, 'Update')) {
            $this->queue->update = Map::events($changes->Update);
        }

        // Deleted events
        if (property_exists($changes, 'Delete')) {
            $this->queue->delete = Map::events($changes->Delete);
        }
    }
}
