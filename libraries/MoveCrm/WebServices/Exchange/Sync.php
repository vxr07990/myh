<?php

namespace MoveCrm\WebServices\Exchange;

use Carbon\Carbon;
use Users_Record_Model;
use MoveCrm\Models\Calendar\Exchange\Sync as ExchangeSync;

class Sync
{
    /** @var Users_Record_Model|null */
    public $user;

    /** @var ExchangeSync|null */
    protected $_sync;

    /**
     * Sync constructor.
     */
    public function __construct($userId)
    {
        $this->user  = Users_Record_Model::getInstanceById($userId, 'Users');
        $this->_sync = ExchangeSync::where('user_id', $userId)->first();
    }

    /**
     * Is this the user's first sync?
     *
     * @return bool
     */
    public function isFirst()
    {
        return is_null($this->_sync);
    }

    /**
     * @return null|Carbon
     */
    public function lastOccurred()
    {
        if (!$this->_sync) {
            return null;
        }

        // return $this->_sync->last_sync_time;
        return Carbon::createFromFormat('Y-m-d H:i:s', $this->_sync->last_sync_time);
    }
}
