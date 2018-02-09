<?php

namespace Vtiger\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $activity_id
 * @property int $reminder_time
 * @property int $reminder_sent
 * @property int $recurringid
 */
class ActivityReminder extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vtiger_activity_reminder';

    /**
     * Does this table utilize the default Laravel timestamp columns?
     *
     * @var boolean
     */
    public $timestamps = false;
}
