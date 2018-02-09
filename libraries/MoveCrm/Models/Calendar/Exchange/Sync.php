<?php

namespace MoveCrm\Models\Calendar\Exchange;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $id
 * @property int    $user_id
 * @property string $state
 * @property string $last_sync_time TODO: Replace with `updated_at`
 * @property string $created_at
 * @property string $updated_at
 */
class Sync extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'calendar_exchange_sync';

    /**
     * Does this table utilize the default Laravel timestamp columns?
     *
     * @var boolean
     */
    public $timestamps = false;
}
